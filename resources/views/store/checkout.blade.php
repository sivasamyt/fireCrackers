@extends('layouts.store')

@section('title', 'Checkout — RoyalCrackers')

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
    .upi-qr-lightbox,
    .upi-txn-modal {
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
    .upi-qr-lightbox.d-none,
    .upi-txn-modal.d-none { display: none !important; }
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
    .upi-txn-modal-inner {
        position: relative;
        width: min(92vw, 420px);
        background: rgba(12, 18, 34, .98);
        border: 1px solid rgba(245, 196, 81, .35);
        border-radius: .75rem;
        padding: 1.35rem;
        color: var(--fc-cream);
        box-shadow: 0 20px 60px rgba(0, 0, 0, .5);
    }
    .upi-txn-modal-inner .form-control {
        background: #0b1220;
        border-color: rgba(245,196,81,.25);
        color: var(--fc-cream);
    }
    .upi-claim-box {
        border: 1px solid rgba(245,196,81,.2);
        border-radius: .5rem;
        padding: .85rem 1rem;
        margin-bottom: 1rem;
    }
</style>
@endpush

@section('content')
@php
    $upiSelected = old('payment_method', 'cod') === 'upi';
    $upiClaim = old('upi_payment_claim', 'unpaid');
@endphp
<div class="container py-5">
    <h1 class="brand-font display-5 text-warning mb-4">Checkout</h1>
    <div class="row g-4">
        <div class="col-lg-7">
            <form method="POST" action="{{ route('checkout.store') }}" class="panel" id="checkout-form">
                @csrf
                <input type="hidden" name="transaction_id" id="transaction_id" value="{{ old('transaction_id') }}">

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

                <div class="upi-claim-box {{ $upiSelected ? '' : 'd-none' }}" id="upi-claim-box">
                    <div class="fw-semibold mb-2">UPI payment status</div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="upi_payment_claim" id="upi-unpaid" value="unpaid" @checked($upiClaim !== 'paid')>
                        <label class="form-check-label" for="upi-unpaid">Unpaid — I will pay later / already scanning</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="upi_payment_claim" id="upi-paid" value="paid" @checked($upiClaim === 'paid')>
                        <label class="form-check-label" for="upi-paid">Paid — I already completed UPI payment</label>
                    </div>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    </div>
                @endif

                <button type="submit" class="btn btn-gold btn-lg" id="place-order-btn">Place Order</button>
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

<div class="upi-txn-modal d-none" id="upi-txn-modal" aria-hidden="true" role="dialog" aria-labelledby="upi-txn-title">
    <div class="upi-txn-modal-inner" id="upi-txn-modal-inner">
        <h2 class="h5 text-warning mb-3" id="upi-txn-title">Enter UPI transaction ID</h2>
        <p class="small text-secondary mb-3">You selected <strong>Paid</strong>. Please provide your UPI transaction / UTR ID to place the order.</p>
        <label class="form-label" for="upi-txn-input">Transaction ID</label>
        <input type="text" id="upi-txn-input" class="form-control mb-2" maxlength="100" placeholder="e.g. 123456789012" autocomplete="off">
        <div class="small text-danger mb-3 d-none" id="upi-txn-error">Transaction ID is required.</div>
        @if(filled($adminHelperPhone ?? null))
            <p class="small text-secondary mb-3">Need help? Contact site admin: <strong class="text-warning">{{ $adminHelperPhone }}</strong></p>
        @else
            <p class="small text-secondary mb-3">Need help? Contact the site admin for UPI support.</p>
        @endif
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-gold flex-grow-1" id="upi-txn-cancel">Cancel</button>
            <button type="button" class="btn btn-gold flex-grow-1" id="upi-txn-confirm">Confirm &amp; Place Order</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(() => {
    const form = document.getElementById('checkout-form');
    const panel = document.getElementById('upi-scanner-panel');
    const claimBox = document.getElementById('upi-claim-box');
    const methodRadios = document.querySelectorAll('input[name="payment_method"]');
    const txnHidden = document.getElementById('transaction_id');
    const txnModal = document.getElementById('upi-txn-modal');
    const txnInner = document.getElementById('upi-txn-modal-inner');
    const txnInput = document.getElementById('upi-txn-input');
    const txnError = document.getElementById('upi-txn-error');
    const txnCancel = document.getElementById('upi-txn-cancel');
    const txnConfirm = document.getElementById('upi-txn-confirm');

    const thumb = document.getElementById('upi-qr-thumb');
    const lightbox = document.getElementById('upi-qr-lightbox');
    const closeBtn = document.getElementById('upi-qr-lightbox-close');
    const inner = document.getElementById('upi-qr-lightbox-inner');

    let allowPaidSubmit = false;

    const isUpi = () => document.getElementById('upi')?.checked;
    const isPaidClaim = () => document.getElementById('upi-paid')?.checked;

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
        if (txnModal?.classList.contains('d-none')) {
            document.body.style.overflow = '';
        }
    };

    const openTxnModal = () => {
        if (!txnModal) return;
        txnError?.classList.add('d-none');
        if (txnInput) {
            txnInput.value = txnHidden?.value || '';
            txnInput.focus();
        }
        txnModal.classList.remove('d-none');
        txnModal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    };

    const closeTxnModal = () => {
        if (!txnModal) return;
        txnModal.classList.add('d-none');
        txnModal.setAttribute('aria-hidden', 'true');
        if (lightbox?.classList.contains('d-none') !== false) {
            document.body.style.overflow = '';
        }
        allowPaidSubmit = false;
    };

    const syncPaymentUi = () => {
        const upi = isUpi();
        panel?.classList.toggle('d-none', !upi);
        claimBox?.classList.toggle('d-none', !upi);
        if (!upi) {
            closeLightbox();
            closeTxnModal();
            if (txnHidden) txnHidden.value = '';
        }
    };

    methodRadios.forEach((radio) => radio.addEventListener('change', syncPaymentUi));
    syncPaymentUi();

    form?.addEventListener('submit', (e) => {
        if (!isUpi() || !isPaidClaim()) {
            if (txnHidden && !isPaidClaim()) txnHidden.value = '';
            return;
        }

        if (allowPaidSubmit && txnHidden?.value.trim()) {
            return;
        }

        e.preventDefault();
        openTxnModal();
        alert('Please enter your UPI transaction ID to continue.');
    });

    txnConfirm?.addEventListener('click', () => {
        const value = (txnInput?.value || '').trim();
        if (!value) {
            txnError?.classList.remove('d-none');
            txnInput?.focus();
            return;
        }
        if (txnHidden) txnHidden.value = value;
        allowPaidSubmit = true;
        closeTxnModal();
        form?.requestSubmit();
    });

    txnCancel?.addEventListener('click', closeTxnModal);
    txnModal?.addEventListener('click', (e) => {
        if (e.target === txnModal) closeTxnModal();
    });
    txnInner?.addEventListener('click', (e) => e.stopPropagation());

    if (thumb && lightbox) {
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
    }

    document.addEventListener('keydown', (e) => {
        if (e.key !== 'Escape') return;
        if (txnModal && !txnModal.classList.contains('d-none')) {
            closeTxnModal();
            return;
        }
        if (lightbox && !lightbox.classList.contains('d-none')) {
            closeLightbox();
        }
    });
})();
</script>
@endpush
