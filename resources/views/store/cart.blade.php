@extends('layouts.store')

@section('title', 'Cart — FireCrackers')

@section('content')
<div class="container py-5">
    <h1 class="brand-font display-5 text-warning mb-4">Your Cart</h1>

    @if($items->isEmpty())
        <div class="panel">Cart is empty. <a href="{{ route('home') }}">Continue shopping</a></div>
    @else
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="panel">
                    @foreach($items as $item)
                        <div class="d-flex flex-column flex-md-row gap-3 align-items-md-center border-bottom border-secondary border-opacity-25 py-3">
                            <img src="{{ $item['product']->image_url }}" alt="" style="width:90px;height:90px;object-fit:cover;border-radius:.5rem;">
                            <div class="flex-grow-1">
                                <div class="fw-semibold">{{ $item['product']->name }}</div>
                                <div class="small text-secondary">₹{{ number_format($item['unit_price'], 2) }} each</div>
                            </div>
                            <form method="POST" action="{{ route('cart.update') }}" class="d-flex gap-2">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="product_id" value="{{ $item['product']->id }}">
                                <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="0" max="50" class="form-control" style="width:80px">
                                <button class="btn btn-outline-gold btn-sm">Update</button>
                            </form>
                            <div class="fw-bold">₹{{ number_format($item['line_total'], 2) }}</div>
                            <form method="POST" action="{{ route('cart.destroy') }}">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="product_id" value="{{ $item['product']->id }}">
                                <button class="btn btn-sm btn-outline-danger">Remove</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="col-lg-4">
                <div class="panel">
                    <div class="d-flex justify-content-between"><span>Subtotal</span><span>₹{{ number_format($subtotal, 2) }}</span></div>
                    <div class="d-flex justify-content-between"><span>Discount</span><span>- ₹{{ number_format($discountTotal, 2) }}</span></div>
                    <hr class="border-secondary">
                    <div class="d-flex justify-content-between fs-5 fw-bold"><span>Total</span><span class="text-warning">₹{{ number_format($grandTotal, 2) }}</span></div>
                    <a href="{{ route('checkout.create') }}" class="btn btn-gold w-100 mt-3">Checkout</a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
