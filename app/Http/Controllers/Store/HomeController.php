<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(Request $request, CartService $cart): View
    {
        $categories = Category::query()
            ->where('is_active', true)
            ->where('slug', '!=', Product::COMBO_CATEGORY_SLUG)
            ->orderBy('name')
            ->get();

        $products = Product::query()
            ->with('category')
            ->where('is_active', true)
            ->whereDoesntHave('category', fn ($q) => $q->where('slug', Product::COMBO_CATEGORY_SLUG))
            ->when($request->filled('category'), function ($query) use ($request) {
                $query->whereHas('category', fn ($q) => $q->where('slug', $request->string('category')));
            })
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%'.$request->string('q').'%';
                $query->where(function ($inner) use ($term) {
                    $inner->where('name', 'like', $term)
                        ->orWhere('description', 'like', $term)
                        ->orWhere('price', 'like', $term)
                        ->orWhereHas('category', fn ($q) => $q->where('name', 'like', $term));
                });
            })
            ->latest()
            ->paginate(12)
            ->appends($request->only(['q', 'category']));

        $search = $request->string('q')->toString();
        $activeCategory = $request->string('category')->toString();

        if ($request->boolean('partial')) {
            return view('store.partials.catalog-results', [
                'products' => $products,
                'search' => $search,
                'activeCategory' => $activeCategory,
            ]);
        }

        $combos = Product::query()
            ->with(['category', 'components'])
            ->where('is_active', true)
            ->whereHas('category', fn ($q) => $q->where('slug', Product::COMBO_CATEGORY_SLUG))
            ->latest()
            ->get();

        return view('store.home', [
            'products' => $products,
            'categories' => $categories,
            'combos' => $combos,
            'cartCount' => $cart->count(),
            'activeCategory' => $activeCategory,
            'search' => $search,
        ]);
    }
}
