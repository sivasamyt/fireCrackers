@extends('layouts.store')

@section('title', 'Order '.$order->order_number.' | Royal Crackers')
@section('robots', 'noindex,nofollow')

@section('content')
<div class="container py-5">
    <div class="panel">
        <h1 class="brand-font text-warning display-6">Order Confirmed</h1>
        <p class="mb-1">Order number: <strong>{{ $order->order_number }}</strong></p>
        <p class="mb-1">Status: <span class="text-capitalize">{{ $order->status }}</span></p>
        <p class="mb-1">Payment: <span class="text-uppercase">{{ $order->payment_method }}</span> — {{ $order->payment_status }}</p>
        @if($order->transaction_id)
            <p class="mb-1">UPI Transaction ID: <strong>{{ $order->transaction_id }}</strong></p>
        @endif
        <p class="mb-4">Deliver to: {{ $order->fullAddress() }}</p>

        <table class="table table-borderless align-middle">
            <thead>
            <tr><th>Item</th><th>Qty</th><th>Total</th></tr>
            </thead>
            <tbody>
            @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->product_name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>₹{{ number_format($item->line_total, 2) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="fs-5 fw-bold">Grand total: <span class="text-warning">₹{{ number_format($order->grand_total, 2) }}</span></div>
        <a href="{{ route('home') }}" class="btn btn-gold mt-3">Continue Shopping</a>
    </div>
</div>
@endsection
