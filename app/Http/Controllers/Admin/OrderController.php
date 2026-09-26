<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'order_number' => ['nullable', 'string', 'max:100'],
            'customer' => ['nullable', 'string', 'max:100'],
            'payment_status' => ['nullable', Rule::in(['pending', 'paid', 'failed'])],
            'status' => ['nullable', Rule::in(['pending', 'confirmed', 'shipped', 'delivered', 'cancelled'])],
        ]);

        $orders = Order::query()
            ->with('user')
            ->when(! empty($filters['order_number']), fn ($q) => $q->where('order_number', 'like', '%'.$filters['order_number'].'%'))
            ->when(! empty($filters['customer']), function ($q) use ($filters) {
                $term = '%'.$filters['customer'].'%';
                $q->where(function ($inner) use ($term) {
                    $inner->where('guest_name', 'like', $term)
                        ->orWhereHas('user', fn ($u) => $u->where('name', 'like', $term));
                });
            })
            ->when(! empty($filters['payment_status']), fn ($q) => $q->where('payment_status', $filters['payment_status']))
            ->when(! empty($filters['status']), fn ($q) => $q->where('status', $filters['status']))
            ->latest()
            ->paginate(20)
            ->appends(collect($filters)->filter()->all());

        return view('admin.orders.index', [
            'orders' => $orders,
            'filters' => $filters,
        ]);
    }

    public function show(Order $order): View
    {
        $order->load(['items.product', 'payment', 'user']);

        $products = Product::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'price', 'discount_percent', 'stock']);

        return view('admin.orders.show', compact('order', 'products'));
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['pending', 'confirmed', 'shipped', 'delivered', 'cancelled'])],
        ]);

        $order->update(['status' => $data['status']]);

        return back()->with('success', 'Order status updated.');
    }

    public function updatePaymentStatus(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'payment_status' => ['required', Rule::in(['pending', 'paid', 'failed'])],
        ]);

        $order->update(['payment_status' => $data['payment_status']]);

        if ($order->payment) {
            $order->payment->update(['status' => $data['payment_status']]);
        }

        return back()->with('success', 'Payment status updated.');
    }

    public function addItem(Request $request, Order $order): RedirectResponse
    {
        if ($order->status === 'cancelled') {
            return back()->with('error', 'Cannot add products to a cancelled order.');
        }

        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:50'],
        ]);

        $product = Product::query()
            ->where('is_active', true)
            ->findOrFail($data['product_id']);

        $quantity = (int) $data['quantity'];

        if ($product->stock < $quantity) {
            return back()->with('error', "Only {$product->stock} in stock for {$product->name}.");
        }

        DB::transaction(function () use ($order, $product, $quantity) {
            $existing = OrderItem::query()
                ->where('order_id', $order->id)
                ->where('product_id', $product->id)
                ->first();

            if ($existing) {
                $existing->quantity += $quantity;
                $existing->refreshLineTotal();
                $existing->save();
            } else {
                OrderItem::query()->create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'unit_price' => $product->price,
                    'discount_percent' => (int) $product->discount_percent,
                    'quantity' => $quantity,
                    'line_total' => OrderItem::computeLineTotal(
                        (float) $product->price,
                        (int) $product->discount_percent,
                        $quantity
                    ),
                ]);
            }

            $product->decrement('stock', $quantity);
            $order->recalculateTotals();
        });

        return back()->with('success', 'Product added to order.');
    }
}
