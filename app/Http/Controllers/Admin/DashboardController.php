<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'productCount' => Product::query()->count(),
            'categoryCount' => Category::query()->count(),
            'orderCount' => Order::query()->count(),
            'pendingOrders' => Order::query()->where('status', 'pending')->count(),
            'recentOrders' => Order::query()->latest()->take(5)->get(),
        ]);
    }
}
