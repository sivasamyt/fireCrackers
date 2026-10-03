<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request, CartService $cart): View
    {
        $search = $request->string('q')->toString();
        $categorySlug = $request->string('category')->toString();

        $products = Product::query()
            ->with('category')
            ->where('is_active', true)
            ->whereHas('category', fn ($q) => $q
                ->where('is_active', true)
                ->where('slug', '!=', Product::COMBO_CATEGORY_SLUG))
            ->when($categorySlug !== '', function ($query) use ($categorySlug) {
                $query->whereHas('category', fn ($q) => $q->where('slug', $categorySlug));
            })
            ->when($search !== '', function ($query) use ($search) {
                $query->where('name', 'like', '%'.$search.'%');
            })
            ->orderBy('name')
            ->get();

        if ($request->boolean('partial')) {
            return view('store.partials.catalog-table', [
                'products' => $products,
                'cartQuantities' => $cart->productQuantities(),
            ]);
        }

        $categories = Category::query()
            ->where('is_active', true)
            ->where('slug', '!=', Product::COMBO_CATEGORY_SLUG)
            ->withCount(['products' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('name')
            ->get();

        return view('store.products.index', [
            'products' => $products,
            'categories' => $categories,
            'totalCount' => $categories->sum('products_count'),
            'activeCategory' => $categorySlug,
            'siteLogo' => Setting::logoUrl(),
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
