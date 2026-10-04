<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $urls = collect([
            ['loc' => url('/').'/', 'lastmod' => null],
            ['loc' => route('products.index'), 'lastmod' => null],
        ]);

        Product::query()
            ->where('is_active', true)
            ->orderBy('id')
            ->get(['slug', 'updated_at'])
            ->each(function (Product $product) use ($urls) {
                $urls->push([
                    'loc' => route('products.show', $product->slug),
                    'lastmod' => $product->updated_at?->toAtomString(),
                ]);
            });

        return response()
            ->view('sitemap', ['urls' => $urls])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
