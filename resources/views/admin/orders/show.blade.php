@extends('layouts.admin')

@section('title', 'Order '.$order->order_number)

@section('content')
<div class="row g-3">
    <div class="col-lg-8">
        <div class="card card-stat p-3 mb-3">
            <h2 class="h5">Items</h2>
            <table class="table">
                <thead><tr><th>Product</th><th>Qty</th><th>Discount</th><th>Line total</th></tr></thead>
                <tbody>
                @foreach($order->items as $item)
                    <tr>
                        <td>{{ $item->product_name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ $item->discount_percent }}%</td>
                        <td>₹{{ number_format($item->line_total, 2) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="fw-bold">Grand total: ₹{{ number_format($order->grand_total, 2) }}</div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card card-stat p-3 mb-3">
            <h2 class="h5">Customer</h2>
            <p class="mb-1">{{ $order->customerName() }}</p>
            <p class="mb-1">{{ $order->customerEmail() }}</p>
            <p class="mb-1">{{ $order->customerPhone() }}</p>
            <p class="mb-0">{{ $order->fullAddress() }}</p>
        </div>
        <div class="card card-stat p-3">
            <h2 class="h5">Update status</h2>
            <p>Payment: <span class="text-uppercase">{{ $order->payment_method }}</span> / {{ $order->payment_status }}</p>
            <form method="POST" action="{{ route('admin.orders.status', $order) }}">
                @csrf @method('PATCH')
                <select name="status" class="form-select mb-3">
                    @foreach(['pending','confirmed','shipped','delivered','cancelled'] as $status)
                        <option value="{{ $status }}" @selected($order->status === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
                <button class="btn btn-dark w-100">Save status</button>
            </form>
        </div>
    </div>
</div>
@endsection
