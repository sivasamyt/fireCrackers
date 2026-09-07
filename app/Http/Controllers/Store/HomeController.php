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
        $categories = Category::query()->where('is_active', true)->orderBy('name')->get();

        $products = Product::query()
            ->with('category')
            ->where('is_active', true)
            ->when($request->filled('category'), function ($query) use ($request) {
                $query->whereHas('category', fn ($q) => $q->where('slug', $request->string('category')));
            })
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = '%'.$request->string('q').'%';
                $query->where(function ($inner) use ($q) {
                    $inner->where('name', 'like', $q)->orWhere('description', 'like', $q);
                });
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('store.home', [
            'products' => $products,
            'categories' => $categories,
            'cartCount' => $cart->count(),
            'activeCategory' => $request->string('category')->toString(),
            'search' => $request->string('q')->toString(),
        ]);
    }
}
