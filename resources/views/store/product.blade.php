@extends('layouts.store')

@section('title', $product->name.' — FireCrackers')

@section('content')
<div class="container py-5">
    <div class="row g-4">
        <div class="col-lg-6">
            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-100 rounded-3 border border-warning border-opacity-25" style="max-height:520px;object-fit:cover;">
        </div>
        <div class="col-lg-6">
            <div class="panel h-100">
                <div class="text-secondary">{{ $product->category?->name }}</div>
                <h1 class="brand-font display-4 text-warning">{{ $product->name }}</h1>
                @php($displayQty = ((int) ($cartQuantity ?? 0)) > 0 ? (int) $cartQuantity : 1)
                <div class="d-flex align-items-center gap-2 my-3" data-price-row>
                    <span
                        class="price-now fs-3"
                        data-unit-price="{{ $product->discounted_price }}"
                        data-original-price="{{ $product->price }}"
                    >₹{{ number_format($product->discounted_price * $displayQty, 2) }}</span>
                    @if($product->discount_percent > 0)
                        <span class="price-old" data-original-price="{{ $product->price }}">₹{{ number_format($product->price * $displayQty, 2) }}</span>
                        <span class="badge badge-disc">{{ $product->discount_percent }}% off</span>
                    @endif
                </div>
                <p class="text-secondary">{{ $product->description }}</p>
                @if($product->isCombo() && $product->components->isNotEmpty())
                    <div class="mb-3">
                        <div class="fw-semibold mb-2">This combo includes</div>
                        <ul class="mb-0">
                            @foreach($product->components as $component)
                                <li>
                                    <a href="{{ route('products.show', $component->slug) }}">{{ $component->name }}</a>
                                    @if($component->pivot->quantity > 1)
                                        × {{ $component->pivot->quantity }}
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <p class="small">Stock: {{ $product->stock }}</p>
                <div style="max-width:280px">
                    @include('store.partials.cart-control', [
                        'productId' => $product->id,
                        'stock' => $product->stock,
                        'quantity' => (int) ($cartQuantity ?? 0),
                    ])
                </div>
            </div>
        </div>
    </div>

    @if($related->isNotEmpty())
        <h2 class="brand-font display-6 text-warning mt-5 mb-3">Related</h2>
        <div class="row g-4">
            @foreach($related as $item)
                <div class="col-md-3">
                    <div class="product-card">
                        <a href="{{ route('products.show', $item->slug) }}"><img src="{{ $item->image_url }}" alt="{{ $item->name }}"></a>
                        <div class="p-3">
                            <h3 class="h6">{{ $item->name }}</h3>
                            <div class="price-now">₹{{ number_format($item->discounted_price, 2) }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
