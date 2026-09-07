<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;

class CartService
{
    public const SESSION_KEY = 'cart';

    public function items(): array
    {
        return Session::get(self::SESSION_KEY, []);
    }

    public function count(): int
    {
        return collect($this->items())->sum('quantity');
    }

    public function add(Product $product, int $quantity = 1): void
    {
        $cart = $this->items();
        $id = (string) $product->id;

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] += $quantity;
        } else {
            $cart[$id] = [
                'product_id' => $product->id,
                'quantity' => $quantity,
            ];
        }

        Session::put(self::SESSION_KEY, $cart);
    }

    public function update(int $productId, int $quantity): void
    {
        $cart = $this->items();
        $id = (string) $productId;

        if (! isset($cart[$id])) {
            return;
        }

        if ($quantity <= 0) {
            unset($cart[$id]);
        } else {
            $cart[$id]['quantity'] = $quantity;
        }

        Session::put(self::SESSION_KEY, $cart);
    }

    public function remove(int $productId): void
    {
        $cart = $this->items();
        unset($cart[(string) $productId]);
        Session::put(self::SESSION_KEY, $cart);
    }

    public function clear(): void
    {
        Session::forget(self::SESSION_KEY);
    }

    public function detailedItems(): Collection
    {
        $cart = $this->items();

        if ($cart === []) {
            return collect();
        }

        $products = Product::query()
            ->with('category')
            ->whereIn('id', array_column($cart, 'product_id'))
            ->where('is_active', true)
            ->get()
            ->keyBy('id');

        return collect($cart)->map(function (array $row) use ($products) {
            $product = $products->get($row['product_id']);

            if (! $product) {
                return null;
            }

            $qty = (int) $row['quantity'];
            $unit = $product->discounted_price;
            $original = (float) $product->price;

            return [
                'product' => $product,
                'quantity' => $qty,
                'unit_price' => $unit,
                'original_price' => $original,
                'discount_percent' => (int) $product->discount_percent,
                'line_total' => round($unit * $qty, 2),
                'line_discount' => round(($original - $unit) * $qty, 2),
            ];
        })->filter()->values();
    }

    public function totals(): array
    {
        $items = $this->detailedItems();
        $subtotal = round($items->sum(fn ($i) => $i['original_price'] * $i['quantity']), 2);
        $discountTotal = round($items->sum('line_discount'), 2);
        $grandTotal = round($items->sum('line_total'), 2);

        return [
            'subtotal' => $subtotal,
            'discount_total' => $discountTotal,
            'grand_total' => $grandTotal,
            'items' => $items,
        ];
    }

    public function merge(array $incoming): void
    {
        $cart = $this->items();

        foreach ($incoming as $row) {
            $id = (string) $row['product_id'];
            $qty = (int) $row['quantity'];

            if (isset($cart[$id])) {
                $cart[$id]['quantity'] += $qty;
            } else {
                $cart[$id] = [
                    'product_id' => (int) $row['product_id'],
                    'quantity' => $qty,
                ];
            }
        }

        Session::put(self::SESSION_KEY, $cart);
    }
}
