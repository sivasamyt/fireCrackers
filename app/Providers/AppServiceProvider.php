<?php

namespace App\Providers;

use App\Services\CartService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useBootstrapFive();

        View::composer('layouts.store', function ($view) {
            $cart = app(CartService::class);
            $data = $view->getData();

            if (! array_key_exists('cartCount', $data)) {
                $view->with('cartCount', $cart->count());
            }

            if (! array_key_exists('cartSummary', $data)) {
                $view->with('cartSummary', $cart->summary());
            }
        });
    }
}
