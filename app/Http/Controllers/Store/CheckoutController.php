<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Mail\AdminNewOrderMail;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\CartService;
use App\Services\RazorpayService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class CheckoutController extends Controller
{
    public function create(CartService $cart, RazorpayService $razorpay): View|RedirectResponse
    {
        $totals = $cart->totals();

        if ($totals['items']->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        return view('store.checkout', [
            'items' => $totals['items'],
            'subtotal' => $totals['subtotal'],
            'discountTotal' => $totals['discount_total'],
            'grandTotal' => $totals['grand_total'],
            'cartCount' => $cart->count(),
            'razorpayEnabled' => $razorpay->isConfigured(),
            'user' => auth()->user(),
        ]);
    }

    public function store(Request $request, CartService $cart, RazorpayService $razorpay): RedirectResponse|View
    {
        $totals = $cart->totals();

        if ($totals['items']->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $rules = [
            'guest_name' => [Rule::requiredIf(! auth()->check()), 'nullable', 'string', 'max:255'],
            'guest_email' => [Rule::requiredIf(! auth()->check()), 'nullable', 'email', 'max:255'],
            'guest_phone' => ['required', 'string', 'max:20'],
            'address_line1' => ['required', 'string', 'max:255'],
            'address_line2' => ['nullable', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:100'],
            'pincode' => ['required', 'string', 'max:12'],
            'payment_method' => ['required', Rule::in(['cod', 'razorpay'])],
        ];

        $data = $request->validate($rules);

        if ($data['payment_method'] === 'razorpay' && ! $razorpay->isConfigured()) {
            return back()->withInput()->with('error', 'Online payment is not configured. Choose Cash on Delivery.');
        }

        try {
            $order = DB::transaction(function () use ($data, $totals) {
                $order = Order::query()->create([
                    'order_number' => 'FC-'.strtoupper(Str::random(8)),
                    'user_id' => auth()->id(),
                    'guest_name' => auth()->check() ? auth()->user()->name : $data['guest_name'],
                    'guest_email' => auth()->check() ? auth()->user()->email : $data['guest_email'],
                    'guest_phone' => $data['guest_phone'],
                    'status' => 'pending',
                    'payment_method' => $data['payment_method'],
                    'payment_status' => 'pending',
                    'subtotal' => $totals['subtotal'],
                    'discount_total' => $totals['discount_total'],
                    'grand_total' => $totals['grand_total'],
                    'address_line1' => $data['address_line1'],
                    'address_line2' => $data['address_line2'] ?? null,
                    'city' => $data['city'],
                    'state' => $data['state'],
                    'pincode' => $data['pincode'],
                ]);

                foreach ($totals['items'] as $item) {
                    OrderItem::query()->create([
                        'order_id' => $order->id,
                        'product_id' => $item['product']->id,
                        'product_name' => $item['product']->name,
                        'unit_price' => $item['original_price'],
                        'discount_percent' => $item['discount_percent'],
                        'quantity' => $item['quantity'],
                        'line_total' => $item['line_total'],
                    ]);

                    $item['product']->decrement('stock', min($item['quantity'], max(0, $item['product']->stock)));
                }

                return $order;
            });
        } catch (Throwable $e) {
            report($e);

            return back()->withInput()->with('error', 'Could not place order. Please try again.');
        }

        if ($data['payment_method'] === 'cod') {
            $cart->clear();
            session()->put('guest_order_ids', array_unique(array_merge(session('guest_order_ids', []), [$order->id])));
            $this->notifyAdmin($order);

            return redirect()->route('orders.show', $order)->with('success', 'Order placed with Cash on Delivery.');
        }

        try {
            $payment = $razorpay->createOrderPayment($order);
        } catch (Throwable $e) {
            report($e);
            $order->update(['status' => 'cancelled', 'payment_status' => 'failed']);

            return redirect()->route('checkout.create')->with('error', 'Unable to start Razorpay payment. Try COD or check Razorpay keys.');
        }

        return view('store.razorpay', [
            'order' => $order,
            'payment' => $payment,
            'razorpayKey' => config('services.razorpay.key'),
            'cartCount' => $cart->count(),
        ]);
    }

    public function verify(Request $request, CartService $cart, RazorpayService $razorpay): RedirectResponse
    {
        $data = $request->validate([
            'order_id' => ['required', 'exists:orders,id'],
            'razorpay_payment_id' => ['required', 'string'],
            'razorpay_order_id' => ['required', 'string'],
            'razorpay_signature' => ['required', 'string'],
        ]);

        $order = Order::query()->findOrFail($data['order_id']);

        try {
            $razorpay->verifyAndMarkPaid(
                $order,
                $data['razorpay_payment_id'],
                $data['razorpay_order_id'],
                $data['razorpay_signature']
            );
        } catch (Throwable $e) {
            report($e);
            $order->update(['payment_status' => 'failed']);

            return redirect()->route('checkout.create')->with('error', 'Payment verification failed.');
        }

        $cart->clear();
        session()->put('guest_order_ids', array_unique(array_merge(session('guest_order_ids', []), [$order->id])));
        $this->notifyAdmin($order);

        return redirect()->route('orders.show', $order)->with('success', 'Payment successful. Order confirmed.');
    }

    public function show(Order $order, CartService $cart): View
    {
        $this->authorizeOrderView($order);
        $order->load('items');

        return view('store.order-confirmation', [
            'order' => $order,
            'cartCount' => $cart->count(),
        ]);
    }

    private function authorizeOrderView(Order $order): void
    {
        if (auth()->check() && (auth()->user()->isAdmin() || $order->user_id === auth()->id())) {
            return;
        }

        $guestIds = session('guest_order_ids', []);

        if (in_array($order->id, $guestIds, true)) {
            return;
        }

        abort(403);
    }

    private function notifyAdmin(Order $order): void
    {
        try {
            $order->loadMissing('items');

            Mail::to(config('mail.admin_address'))->send(new AdminNewOrderMail($order));
        } catch (Throwable $e) {
            report($e);
        }
    }
}
