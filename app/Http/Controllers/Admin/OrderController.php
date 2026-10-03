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
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class OrderController extends Controller
{
    private const STATUSES = ['pending', 'confirmed', 'shipped', 'delivered', 'cancelled'];

    private const PAYMENT_STATUSES = ['pending', 'paid', 'failed'];

    private const MAX_ITEM_QUANTITY = 50;

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'order_number' => ['nullable', 'string', 'max:100'],
            'customer' => ['nullable', 'string', 'max:100'],
            'payment_status' => ['nullable', Rule::in(self::PAYMENT_STATUSES)],
            'status' => ['nullable', Rule::in(self::STATUSES)],
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

    public function update(Request $request, Order $order): RedirectResponse
    {
        $editItems = $order->status !== 'cancelled';

        $rules = [
            'status' => ['required', Rule::in(self::STATUSES)],
            'payment_status' => ['required', Rule::in(self::PAYMENT_STATUSES)],
        ];

        if ($editItems) {
            $rules += [
                'items' => ['required', 'array', 'min:1'],
                'items.*.item_id' => [
                    'nullable', 'integer', 'required_without:items.*.product_id',
                    Rule::exists('order_items', 'id')->where('order_id', $order->id),
                ],
                'items.*.product_id' => [
                    'nullable', 'integer', 'required_without:items.*.item_id',
                    Rule::exists('products', 'id')->where('is_active', true),
                ],
                'items.*.quantity' => ['required', 'integer', 'min:1', 'max:'.self::MAX_ITEM_QUANTITY],
            ];
        }

        $data = $request->validate($rules, [
            'items.required' => 'An order must have at least one item.',
            'items.min' => 'An order must have at least one item.',
        ]);

        DB::transaction(function () use ($order, $data, $editItems) {
            if ($editItems) {
                $this->syncItems($order, $data['items']);
            }

            $order->update([
                'status' => $data['status'],
                'payment_status' => $data['payment_status'],
            ]);

            $order->payment?->update(['status' => $data['payment_status']]);
        });

        return back()->with('success', 'Order updated.');
    }

    /**
     * @param  array<int, array{item_id?: int|null, product_id?: int|null, quantity: int}>  $rows
     */
    private function syncItems(Order $order, array $rows): void
    {
        $existing = $order->items()->get()->keyBy('id');

        $keepQty = [];
        $newQty = [];

        foreach ($rows as $row) {
            $qty = (int) $row['quantity'];

            if (! empty($row['item_id'])) {
                $id = (int) $row['item_id'];
                $keepQty[$id] = ($keepQty[$id] ?? 0) + $qty;
            } else {
                $productId = (int) $row['product_id'];
                $newQty[$productId] = ($newQty[$productId] ?? 0) + $qty;
            }
        }

        foreach ($newQty as $productId => $qty) {
            $line = $existing->first(fn (OrderItem $item) => (int) $item->product_id === $productId);

            if ($line) {
                $keepQty[$line->id] = ($keepQty[$line->id] ?? 0) + $qty;
                unset($newQty[$productId]);
            }
        }

        foreach (array_merge($keepQty, $newQty) as $qty) {
            if ($qty > self::MAX_ITEM_QUANTITY) {
                throw ValidationException::withMessages([
                    'items' => 'Quantity per product cannot exceed '.self::MAX_ITEM_QUANTITY.'.',
                ]);
            }
        }

        $stockDelta = [];

        foreach ($existing as $id => $item) {
            if ($item->product_id) {
                $stockDelta[$item->product_id] = ($stockDelta[$item->product_id] ?? 0)
                    + ($keepQty[$id] ?? 0) - $item->quantity;
            }
        }

        foreach ($newQty as $productId => $qty) {
            $stockDelta[$productId] = ($stockDelta[$productId] ?? 0) + $qty;
        }

        $products = Product::query()
            ->whereIn('id', array_keys($stockDelta))
            ->lockForUpdate()
            ->get()
            ->keyBy('id');

        foreach ($stockDelta as $productId => $delta) {
            $product = $products->get($productId);

            if ($delta > 0 && $product && $product->stock < $delta) {
                throw ValidationException::withMessages([
                    'items' => "Only {$product->stock} more in stock for {$product->name}.",
                ]);
            }
        }

        foreach ($existing as $id => $item) {
            if (! isset($keepQty[$id])) {
                $item->delete();

                continue;
            }

            if ($item->quantity !== $keepQty[$id]) {
                $item->quantity = $keepQty[$id];
                $item->refreshLineTotal();
                $item->save();
            }
        }

        foreach ($newQty as $productId => $qty) {
            $product = $products->get($productId);

            OrderItem::query()->create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'unit_price' => $product->price,
                'discount_percent' => (int) $product->discount_percent,
                'quantity' => $qty,
                'line_total' => OrderItem::computeLineTotal(
                    (float) $product->price,
                    (int) $product->discount_percent,
                    $qty
                ),
            ]);
        }

        foreach ($stockDelta as $productId => $delta) {
            $product = $products->get($productId);

            if (! $product || $delta === 0) {
                continue;
            }

            $delta > 0
                ? $product->decrement('stock', $delta)
                : $product->increment('stock', -$delta);
        }

        $order->recalculateTotals();
    }
}
