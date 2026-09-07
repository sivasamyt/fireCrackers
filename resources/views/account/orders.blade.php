@extends('layouts.store')

@section('title', 'My Orders')

@section('content')
<div class="container py-5">
    <h1 class="brand-font display-5 text-warning mb-4">My Orders</h1>
    <div class="panel">
        @forelse($orders as $order)
            <div class="d-flex justify-content-between align-items-center border-bottom border-secondary border-opacity-25 py-3">
                <div>
                    <div class="fw-semibold">{{ $order->order_number }}</div>
                    <div class="small text-secondary">{{ $order->created_at->format('d M Y, h:i A') }} · {{ ucfirst($order->status) }}</div>
                </div>
                <div class="text-end">
                    <div class="text-warning fw-bold">₹{{ number_format($order->grand_total, 2) }}</div>
                    <a href="{{ route('account.orders.show', $order) }}">View</a>
                </div>
            </div>
        @empty
            <p class="mb-0">No orders yet. <a href="{{ route('home') }}">Start shopping</a></p>
        @endforelse
        <div class="mt-3">{{ $orders->links() }}</div>
    </div>
</div>
@endsection
