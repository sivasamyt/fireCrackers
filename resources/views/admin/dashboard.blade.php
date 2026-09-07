@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="card card-stat p-3"><div class="text-muted">Products</div><div class="fs-3 fw-bold">{{ $productCount }}</div></div></div>
    <div class="col-md-3"><div class="card card-stat p-3"><div class="text-muted">Categories</div><div class="fs-3 fw-bold">{{ $categoryCount }}</div></div></div>
    <div class="col-md-3"><div class="card card-stat p-3"><div class="text-muted">Orders</div><div class="fs-3 fw-bold">{{ $orderCount }}</div></div></div>
    <div class="col-md-3"><div class="card card-stat p-3"><div class="text-muted">Pending</div><div class="fs-3 fw-bold">{{ $pendingOrders }}</div></div></div>
</div>

<div class="card card-stat p-3">
    <h2 class="h5">Recent orders</h2>
    <table class="table mb-0">
        <thead><tr><th>#</th><th>Customer</th><th>Total</th><th>Status</th><th></th></tr></thead>
        <tbody>
        @forelse($recentOrders as $order)
            <tr>
                <td>{{ $order->order_number }}</td>
                <td>{{ $order->customerName() }}</td>
                <td>₹{{ number_format($order->grand_total, 2) }}</td>
                <td>{{ ucfirst($order->status) }}</td>
                <td><a href="{{ route('admin.orders.show', $order) }}">View</a></td>
            </tr>
        @empty
            <tr><td colspan="5">No orders yet.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
