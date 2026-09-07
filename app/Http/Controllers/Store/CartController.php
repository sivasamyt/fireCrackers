<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(CartService $cart): View
    {
        $totals = $cart->totals();

        return view('store.cart', [
            'items' => $totals['items'],
            'subtotal' => $totals['subtotal'],
            'discountTotal' => $totals['discount_total'],
            'grandTotal' => $totals['grand_total'],
            'cartCount' => $cart->count(),
        ]);
    }

    public function store(Request $request, CartService $cart): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $product = Product::query()->where('is_active', true)->findOrFail($data['product_id']);
        $cart->add($product, (int) ($data['quantity'] ?? 1));

        return redirect()->route('cart.index')->with('success', 'Added to cart.');
    }

    public function update(Request $request, CartService $cart): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:0', 'max:50'],
        ]);

        $cart->update((int) $data['product_id'], (int) $data['quantity']);

        return back()->with('success', 'Cart updated.');
    }

    public function destroy(Request $request, CartService $cart): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
        ]);

        $cart->remove((int) $data['product_id']);

        return back()->with('success', 'Item removed.');
    }
}
