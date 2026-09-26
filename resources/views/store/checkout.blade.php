@extends('layouts.store')

@section('title', 'Checkout — FireCrackers')

@push('head')
<style>
    .upi-qr-thumb {
        max-width: 280px;
        width: 100%;
        background: #fff;
        padding: .5rem;
        cursor: pointer;
        transition: transform .15s ease, box-shadow .15s ease;
    }
    .upi-qr-thumb:hover {
        transform: scale(1.02);
        box-shadow: 0 0 0 2px rgba(245, 196, 81, .45);
    }
    .upi-qr-lightbox {
        position: fixed;
        inset: 0;
        z-index: 2000;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1.5rem;
        background: rgba(5, 7, 15, .88);
        backdrop-filter: blur(4px);
    }
    .upi-qr-lightbox.d-none { display: none !important; }
    .upi-qr-lightbox-inner {
        position: relative;
        max-width: min(90vw, 520px);
        max-height: 90vh;
        background: #fff;
        border-radius: .75rem;
        padding: 1rem;
        box-shadow: 0 20px 60px rgba(0, 0, 0, .5);
    }
    .upi-qr-lightbox-inner img {
        display: block;
        width: 100%;
        height: auto;
        max-height: calc(90vh - 3rem);
        object-fit: contain;
    }
    .upi-qr-lightbox-close {
        position: absolute;
        top: -.65rem;
        right: -.65rem;
        width: 2.25rem;
        height: 2.25rem;
        border: 0;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--fc-gold), var(--fc-amber));
        color: #1a1205;
        font-weight: 700;
        font-size: 1.25rem;
        line-height: 1;
        cursor: pointer;
    }
</style>
@endpush

@section('content')
@php($upiSelected = old('payment_method', 'cod') === 'upi')
<div class="container py-5">
    <h1 class="brand-font display-5 text-warning mb-4">Checkout</h1>
    <div class="row g-4">
        <div class="col-lg-7">
            <form method="POST" action="{{ route('checkout.store') }}" class="panel" id="checkout-form">
                @csrf
                @guest
                    <h2 class="h5 mb-3">Contact</h2>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Name</label>
                            <input type="text" name="guest_name" value="{{ old('guest_name') }}" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email <span class="text-secondary small">(optional)</span></label>
                            <input type="email" name="guest_email" value="{{ old('guest_email') }}" class="form-control">
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
                    <input class="form-check-input" type="radio" name="payment_method" id="cod" value="cod" @checked(! $upiSelected)>
                    <label class="form-check-label" for="cod">Cash on Delivery</label>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="radio" name="payment_method" id="upi" value="upi" @checked($upiSelected)>
                    <label class="form-check-label" for="upi">UPI</label>
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
            <div class="panel mb-3">
                <h2 class="h5 mb-3">Order Summary</h2>
                @foreach($items as $item)
                    <div class="d-flex justify-content-between mb-2">
                        <span>{{ $item['name'] }} × {{ $item['quantity'] }}</span>
                        <span>₹{{ number_format($item['line_total'], 2) }}</span>
                    </div>
                @endforeach
                <hr class="border-secondary">
                <div class="d-flex justify-content-between"><span>Subtotal</span><span>₹{{ number_format($subtotal, 2) }}</span></div>
                <div class="d-flex justify-content-between"><span>Discount</span><span>- ₹{{ number_format($discountTotal, 2) }}</span></div>
                <div class="d-flex justify-content-between fw-bold fs-5 mt-2"><span>Total</span><span class="text-warning">₹{{ number_format($grandTotal, 2) }}</span></div>
            </div>

            <div class="panel {{ $upiSelected ? '' : 'd-none' }}" id="upi-scanner-panel">
                <h2 class="h5 mb-3">Pay with UPI</h2>
                @if($upiQrUrl)
                    <img
                        src="{{ $upiQrUrl }}"
                        alt="UPI QR scanner"
                        id="upi-qr-thumb"
                        class="img-fluid rounded mb-3 upi-qr-thumb"
                        role="button"
                        tabindex="0"
                        title="View full size"
                    >
                @else
                    <div class="border border-warning border-opacity-25 rounded p-4 text-center text-secondary mb-3">
                        UPI QR not uploaded yet. Place your scanner image at <code>public/images/upi-qr.png</code>.
                    </div>
                @endif
                <p class="mb-1 fw-semibold">Amount: <span class="text-warning">₹{{ number_format($grandTotal, 2) }}</span></p>
                <p class="small text-secondary mb-0">Scan to pay, then place your order. We will confirm payment once received.</p>
            </div>
        </div>
    </div>
</div>

@if($upiQrUrl)
<div class="upi-qr-lightbox d-none" id="upi-qr-lightbox" aria-hidden="true" role="dialog" aria-label="UPI QR full size">
    <div class="upi-qr-lightbox-inner" id="upi-qr-lightbox-inner">
        <button type="button" class="upi-qr-lightbox-close" id="upi-qr-lightbox-close" aria-label="Close">&times;</button>
        <img src="{{ $upiQrUrl }}" alt="UPI QR scanner full size">
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
(() => {
    const panel = document.getElementById('upi-scanner-panel');
    const radios = document.querySelectorAll('input[name="payment_method"]');
    const thumb = document.getElementById('upi-qr-thumb');
    const lightbox = document.getElementById('upi-qr-lightbox');
    const closeBtn = document.getElementById('upi-qr-lightbox-close');
    const inner = document.getElementById('upi-qr-lightbox-inner');

    const openLightbox = () => {
        if (!lightbox) return;
        lightbox.classList.remove('d-none');
        lightbox.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    };

    const closeLightbox = () => {
        if (!lightbox) return;
        lightbox.classList.add('d-none');
        lightbox.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    };

    if (panel && radios.length) {
        const sync = () => {
            const upi = document.getElementById('upi')?.checked;
            panel.classList.toggle('d-none', !upi);
            if (!upi) closeLightbox();
        };
        radios.forEach((radio) => radio.addEventListener('change', sync));
        sync();
    }

    if (!thumb || !lightbox) return;

    thumb.addEventListener('click', openLightbox);
    thumb.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            openLightbox();
        }
    });
    closeBtn?.addEventListener('click', closeLightbox);
    lightbox.addEventListener('click', (e) => {
        if (e.target === lightbox) closeLightbox();
    });
    inner?.addEventListener('click', (e) => e.stopPropagation());
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !lightbox.classList.contains('d-none')) {
            closeLightbox();
        }
    });
})();
</script>
@endpush
