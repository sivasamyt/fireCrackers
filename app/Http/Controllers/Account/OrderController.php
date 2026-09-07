<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request, CartService $cart): View
    {
        $orders = Order::query()
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(10);

        return view('account.orders', [
            'orders' => $orders,
            'cartCount' => $cart->count(),
        ]);
    }

    public function show(Order $order, CartService $cart): View
    {
        abort_unless($order->user_id === auth()->id(), 403);
        $order->load('items');

        return view('store.order-confirmation', [
            'order' => $order,
            'cartCount' => $cart->count(),
        ]);
    }
}
