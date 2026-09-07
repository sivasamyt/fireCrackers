@extends('layouts.store')

@section('title', 'Checkout — FireCrackers')

@section('content')
<div class="container py-5">
    <h1 class="brand-font display-5 text-warning mb-4">Checkout</h1>
    <div class="row g-4">
        <div class="col-lg-7">
            <form method="POST" action="{{ route('checkout.store') }}" class="panel">
                @csrf
                @guest
                    <h2 class="h5 mb-3">Contact</h2>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Name</label>
                            <input type="text" name="guest_name" value="{{ old('guest_name') }}" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="guest_email" value="{{ old('guest_email') }}" class="form-control" required>
                        </div>
                    </div>
                    <p class="small text-secondary">Optional: <a href="{{ route('register') }}">create an account</a> to track orders later.</p>
                @else
                    <div class="mb-3">Ordering as <strong>{{ $user->name }}</strong> ({{ $user->email }})</div>
                @endguest

                <h2 class="h5 mb-3 mt-4">Delivery Address</h2>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Phone</label>
                        <input type="text" name="guest_phone" value="{{ old('guest_phone') }}" class="form-control" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Address line 1</label>
                        <input type="text" name="address_line1" value="{{ old('address_line1') }}" class="form-control" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Address line 2</label>
                        <input type="text" name="address_line2" value="{{ old('address_line2') }}" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">City</label>
                        <input type="text" name="city" value="{{ old('city') }}" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">State</label>
                        <input type="text" name="state" value="{{ old('state') }}" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Pincode</label>
                        <input type="text" name="pincode" value="{{ old('pincode') }}" class="form-control" required>
                    </div>
                </div>

                <h2 class="h5 mb-3 mt-4">Payment</h2>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="radio" name="payment_method" id="cod" value="cod" @checked(old('payment_method', 'cod') === 'cod')>
                    <label class="form-check-label" for="cod">Cash on Delivery</label>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="radio" name="payment_method" id="razorpay" value="razorpay" @checked(old('payment_method') === 'razorpay') @disabled(! $razorpayEnabled)>
                    <label class="form-check-label" for="razorpay">
                        Pay Online (Razorpay)
                        @unless($razorpayEnabled)
                            <span class="text-secondary small">— add RAZORPAY_KEY / RAZORPAY_SECRET in .env</span>
                        @endunless
                    </label>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    </div>
                @endif

                <button class="btn btn-gold btn-lg">Place Order</button>
            </form>
        </div>
        <div class="col-lg-5">
            <div class="panel">
                <h2 class="h5 mb-3">Order Summary</h2>
                @foreach($items as $item)
                    <div class="d-flex justify-content-between mb-2">
                        <span>{{ $item['product']->name }} × {{ $item['quantity'] }}</span>
                        <span>₹{{ number_format($item['line_total'], 2) }}</span>
                    </div>
                @endforeach
                <hr class="border-secondary">
                <div class="d-flex justify-content-between"><span>Subtotal</span><span>₹{{ number_format($subtotal, 2) }}</span></div>
                <div class="d-flex justify-content-between"><span>Discount</span><span>- ₹{{ number_format($discountTotal, 2) }}</span></div>
                <div class="d-flex justify-content-between fw-bold fs-5 mt-2"><span>Total</span><span class="text-warning">₹{{ number_format($grandTotal, 2) }}</span></div>
            </div>
        </div>
    </div>
</div>
@endsection
