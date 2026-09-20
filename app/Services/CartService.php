<?php

namespace App\Services;

use App\Models\GiftBox;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;

class CartService
{
    public const SESSION_KEY = 'cart';

    public const GIFT_BOX_SESSION_KEY = 'cart_gift_boxes';

    public function items(): array
    {
        return Session::get(self::SESSION_KEY, []);
    }

    public function giftBoxItems(): array
    {
        return Session::get(self::GIFT_BOX_SESSION_KEY, []);
    }

    public function count(): int
    {
        return collect($this->items())->sum('quantity')
            + collect($this->giftBoxItems())->sum('quantity');
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

    public function addGiftBox(GiftBox $giftBox, int $quantity = 1): void
    {
        $cart = $this->giftBoxItems();
        $id = (string) $giftBox->id;

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] += $quantity;
        } else {
            $cart[$id] = [
                'gift_box_id' => $giftBox->id,
                'quantity' => $quantity,
            ];
        }

        Session::put(self::GIFT_BOX_SESSION_KEY, $cart);
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

    public function updateGiftBox(int $giftBoxId, int $quantity): void
    {
        $cart = $this->giftBoxItems();
        $id = (string) $giftBoxId;

        if (! isset($cart[$id])) {
            return;
        }

        if ($quantity <= 0) {
            unset($cart[$id]);
        } else {
            $cart[$id]['quantity'] = $quantity;
        }

        Session::put(self::GIFT_BOX_SESSION_KEY, $cart);
    }

    public function remove(int $productId): void
    {
        $cart = $this->items();
        unset($cart[(string) $productId]);
        Session::put(self::SESSION_KEY, $cart);
    }

    public function removeGiftBox(int $giftBoxId): void
    {
        $cart = $this->giftBoxItems();
        unset($cart[(string) $giftBoxId]);
        Session::put(self::GIFT_BOX_SESSION_KEY, $cart);
    }

    public function clear(): void
    {
        Session::forget(self::SESSION_KEY);
        Session::forget(self::GIFT_BOX_SESSION_KEY);
    }

    public function detailedItems(): Collection
    {
        return $this->detailedProductItems()->concat($this->detailedGiftBoxItems())->values();
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

    private function detailedProductItems(): Collection
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
                'type' => 'product',
                'id' => $product->id,
                'name' => $product->name,
                'image_url' => $product->image_url,
                'product' => $product,
                'gift_box' => null,
                'quantity' => $qty,
                'unit_price' => $unit,
                'original_price' => $original,
                'discount_percent' => (int) $product->discount_percent,
                'line_total' => round($unit * $qty, 2),
                'line_discount' => round(($original - $unit) * $qty, 2),
            ];
        })->filter()->values();
    }

    private function detailedGiftBoxItems(): Collection
    {
        $cart = $this->giftBoxItems();

        if ($cart === []) {
            return collect();
        }

        $giftBoxes = GiftBox::query()
            ->with('products')
            ->whereIn('id', array_column($cart, 'gift_box_id'))
            ->where('is_active', true)
            ->get()
            ->keyBy('id');

        return collect($cart)->map(function (array $row) use ($giftBoxes) {
            $giftBox = $giftBoxes->get($row['gift_box_id']);

            if (! $giftBox) {
                return null;
            }

            $qty = (int) $row['quantity'];
            $unit = $giftBox->discounted_price;
            $original = (float) $giftBox->price;

            return [
                'type' => 'gift_box',
                'id' => $giftBox->id,
                'name' => $giftBox->name,
                'image_url' => $giftBox->image_url,
                'product' => null,
                'gift_box' => $giftBox,
                'quantity' => $qty,
                'unit_price' => $unit,
                'original_price' => $original,
                'discount_percent' => (int) $giftBox->discount_percent,
                'line_total' => round($unit * $qty, 2),
                'line_discount' => round(($original - $unit) * $qty, 2),
            ];
        })->filter()->values();
    }
}
