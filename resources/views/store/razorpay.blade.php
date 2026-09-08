@extends('layouts.store')

@section('title', 'Pay Online — FireCrackers')

@section('content')
<div class="container py-5">
    <div class="panel mx-auto" style="max-width:520px">
        <h1 class="brand-font text-warning">Complete Payment</h1>
        <p>Order <strong>{{ $order->order_number }}</strong> — ₹{{ number_format($order->grand_total, 2) }}</p>
        <button id="rzp-button" class="btn btn-gold btn-lg w-100">Pay with Razorpay</button>
        <form id="verify-form" method="POST" action="{{ route('checkout.verify') }}" class="d-none">
            @csrf
            <input type="hidden" name="order_id" value="{{ $order->id }}">
            <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
            <input type="hidden" name="razorpay_order_id" id="razorpay_order_id">
            <input type="hidden" name="razorpay_signature" id="razorpay_signature">
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    const options = {
        key: @json($razorpayKey),
        amount: {{ (int) round($order->grand_total * 100) }},
        currency: 'INR',
        name: 'FireCrackers',
        description: @json($order->order_number),
        order_id: @json($payment->razorpay_order_id),
        handler: function (response) {
            document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
            document.getElementById('razorpay_order_id').value = response.razorpay_order_id;
            document.getElementById('razorpay_signature').value = response.razorpay_signature;
            document.getElementById('verify-form').submit();
        },
        prefill: {
            name: @json($order->guest_name),
            email: @json($order->customerEmail() ?? ''),
            contact: @json($order->guest_phone),
        },
        theme: { color: '#f5c451' }
    };
    document.getElementById('rzp-button').onclick = function (e) {
        e.preventDefault();
        new Razorpay(options).open();
    };
</script>
@endpush
