<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Mail\AdminNewOrderMail;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\CartService;
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
    public function create(CartService $cart): View|RedirectResponse
    {
        $totals = $cart->totals();

        if ($totals['items']->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $qrRelative = ltrim((string) config('services.upi.qr_image', '/images/upi-qr.png'), '/');
        $qrExists = is_file(public_path($qrRelative));

        return view('store.checkout', [
            'items' => $totals['items'],
            'subtotal' => $totals['subtotal'],
            'discountTotal' => $totals['discount_total'],
            'grandTotal' => $totals['grand_total'],
            'cartCount' => $cart->count(),
            'upiQrUrl' => $qrExists ? asset($qrRelative) : null,
            'user' => auth()->user(),
        ]);
    }

    public function store(Request $request, CartService $cart): RedirectResponse
    {
        $totals = $cart->totals();

        if ($totals['items']->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $data = $request->validate([
            'guest_name' => [Rule::requiredIf(! auth()->check()), 'nullable', 'string', 'max:255'],
            'guest_email' => ['nullable', 'email', 'max:255'],
            'guest_phone' => ['required', 'string', 'max:20'],
            'address_line1' => ['required', 'string', 'max:255'],
            'address_line2' => ['nullable', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:100'],
            'pincode' => ['required', 'string', 'max:12'],
            'payment_method' => ['required', Rule::in(['cod', 'upi'])],
        ]);

        try {
            $order = DB::transaction(function () use ($data, $totals) {
                $order = Order::query()->create([
                    'order_number' => 'FC-'.strtoupper(Str::random(8)),
                    'user_id' => auth()->id(),
                    'guest_name' => auth()->check() ? auth()->user()->name : $data['guest_name'],
                    'guest_email' => auth()->check() ? auth()->user()->email : ($data['guest_email'] ?? null),
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
                        'product_id' => $item['product']?->id,
                        'product_name' => $item['name'],
                        'unit_price' => $item['original_price'],
                        'discount_percent' => $item['discount_percent'],
                        'quantity' => $item['quantity'],
                        'line_total' => $item['line_total'],
                    ]);

                    if ($item['product']) {
                        $item['product']->decrement('stock', min($item['quantity'], max(0, $item['product']->stock)));

                        continue;
                    }

                    foreach ($item['gift_box']->products as $boxProduct) {
                        $needed = $item['quantity'] * (int) $boxProduct->pivot->quantity;
                        $boxProduct->decrement('stock', min($needed, max(0, $boxProduct->stock)));
                    }
                }

                return $order;
            });
        } catch (Throwable $e) {
            report($e);

            return back()->withInput()->with('error', 'Could not place order. Please try again.');
        }

        $cart->clear();
        session()->put('guest_order_ids', array_unique(array_merge(session('guest_order_ids', []), [$order->id])));
        $this->notifyAdmin($order);

        $message = $data['payment_method'] === 'upi'
            ? 'Order placed. Complete UPI payment if you have not already — we will confirm once received.'
            : 'Order placed with Cash on Delivery.';

        return redirect()->route('orders.show', $order)->with('success', $message);
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
