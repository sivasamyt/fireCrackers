<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request, CartService $cart): View
    {
        $search = $request->string('q')->toString();

        $products = Product::query()
            ->with('category')
            ->where('is_active', true)
            ->whereDoesntHave('category', fn ($q) => $q->where('slug', Product::COMBO_CATEGORY_SLUG))
            ->when($search !== '', function ($query) use ($search) {
                $query->where('name', 'like', '%'.$search.'%');
            })
            ->latest()
            ->paginate(12)
            ->appends($request->only('q'));

        if ($request->boolean('partial')) {
            return view('store.partials.catalog-results', [
                'products' => $products,
                'search' => $search,
                'cartQuantities' => $cart->productQuantities(),
            ]);
        }

        return view('store.products.index', [
            'products' => $products,
            'search' => $search,
            'cartCount' => $cart->count(),
            'cartQuantities' => $cart->productQuantities(),
            'cartSummary' => $cart->summary(),
        ]);
    }

    public function show(string $slug, CartService $cart): View
    {
        $product = Product::query()
            ->with(['category', 'components'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $related = Product::query()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->take(4)
            ->get();

        $cartQuantities = $cart->productQuantities();

        return view('store.product', [
            'product' => $product,
            'related' => $related,
            'cartCount' => $cart->count(),
            'cartQuantities' => $cartQuantities,
            'cartQuantity' => $cartQuantities[$product->id] ?? 0,
            'cartSummary' => $cart->summary(),
        ]);
    }
}
