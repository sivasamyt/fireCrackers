<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function show(string $slug, CartService $cart): View
    {
        $product = Product::query()
            ->with('category')
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $related = Product::query()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->take(4)
            ->get();

        return view('store.product', [
            'product' => $product,
            'related' => $related,
            'cartCount' => $cart->count(),
        ]);
    }
}
