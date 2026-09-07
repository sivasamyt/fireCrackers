@extends('layouts.admin')

@section('title', 'Orders')

@section('content')
<div class="card card-stat p-3">
    <table class="table mb-0">
        <thead><tr><th>Order</th><th>Customer</th><th>Payment</th><th>Total</th><th>Status</th><th></th></tr></thead>
        <tbody>
        @foreach($orders as $order)
            <tr>
                <td>{{ $order->order_number }}</td>
                <td>{{ $order->customerName() }}</td>
                <td class="text-uppercase">{{ $order->payment_method }} / {{ $order->payment_status }}</td>
                <td>₹{{ number_format($order->grand_total, 2) }}</td>
                <td>{{ ucfirst($order->status) }}</td>
                <td><a href="{{ route('admin.orders.show', $order) }}">View</a></td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="mt-3">{{ $orders->links() }}</div>
</div>
@endsection
