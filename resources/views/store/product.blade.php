@extends('layouts.store')

@php
    $productCategoryName = $product->category?->name;
    $productSeoSummary = $product->name
        .($productCategoryName ? ' ('.\Illuminate\Support\Str::title(\Illuminate\Support\Str::lower($productCategoryName)).')' : '')
        .' at Royal Crackers - ₹'.number_format((float) $product->discounted_price, 2)
        .($product->discount_percent > 0 ? ', '.$product->discount_percent.'% off' : '')
        .'.';
    $productDescriptionText = $product->description
        ? trim(preg_replace('/\s+/', ' ', strip_tags($product->description)))
        : '';
@endphp
@section('title', $product->name.($productCategoryName ? ' - '.\Illuminate\Support\Str::title(\Illuminate\Support\Str::lower($productCategoryName)) : '').' | Royal Crackers')
@section('meta_description', \Illuminate\Support\Str::limit(trim($productSeoSummary.' '.$productDescriptionText), 160))
@section('og_type', 'product')
@if($product->image_path)
    @section('og_image', $product->image_url)
@endif

@push('structured-data')
@php
    $productSchema = array_filter([
        '@context' => 'https://schema.org',
        '@type' => 'Product',
        'name' => $product->name,
        'description' => $product->description
            ? trim(preg_replace('/\s+/', ' ', strip_tags($product->description)))
            : null,
        'image' => $product->image_path ? [$product->image_url] : null,
        'category' => $product->category?->name,
        'url' => route('products.show', $product->slug),
        'offers' => [
            '@type' => 'Offer',
            'url' => route('products.show', $product->slug),
            'priceCurrency' => 'INR',
            'price' => number_format((float) $product->discounted_price, 2, '.', ''),
            'availability' => $product->stock > 0
                ? 'https://schema.org/InStock'
                : 'https://schema.org/OutOfStock',
            'itemCondition' => 'https://schema.org/NewCondition',
        ],
    ]);
@endphp
<script type="application/ld+json">
{!! json_encode($productSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_PRETTY_PRINT) !!}
</script>
@endpush

@push('head')
<style>
    .product-media {
        position: relative;
        border-radius: .75rem;
        overflow: hidden;
        border: 1px solid var(--fc-border);
        background: var(--fc-img-bg);
    }
    .product-media-image,
    .product-media-video {
        display: block;
        width: 100%;
        max-height: 520px;
        object-fit: cover;
    }
    .product-media-video {
        background: var(--fc-img-bg);
        object-fit: contain;
    }
    .product-media-play {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 4.25rem;
        height: 4.25rem;
        border: 0;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--fc-gold), var(--fc-amber));
        color: var(--fc-on-accent);
        font-size: 1.35rem;
        line-height: 1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding-left: .2rem;
        box-shadow: 0 8px 28px rgba(0, 0, 0, .45);
        cursor: pointer;
        transition: transform .15s ease, filter .15s ease;
        z-index: 2;
    }
    .product-media-play:hover {
        transform: translate(-50%, -50%) scale(1.06);
        filter: brightness(1.05);
    }
    .product-media.is-playing .product-media-image,
    .product-media.is-playing .product-media-play {
        display: none;
    }
</style>
@endpush

@section('content')
<div class="container py-5">
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="product-media" id="product-media" @if($product->video_url) data-has-video="1" @endif>
                <img
                    src="{{ $product->image_url }}"
                    alt="{{ $product->name }}"
                    class="product-media-image"
                >
                @if($product->video_url)
                    <button type="button" class="product-media-play" id="product-media-play" aria-label="Play video">▶</button>
                    <video
                        class="product-media-video d-none"
                        id="product-media-video"
                        src="{{ $product->video_url }}"
                        controls
                        playsinline
                        preload="metadata"
                    ></video>
                @endif
            </div>
        </div>
        <div class="col-lg-6">
            <div class="panel h-100">
                <div class="text-secondary">
                    @if($product->category && $product->category->is_active && ! $product->isCombo())
                        <a href="{{ route('products.index', ['category' => $product->category->slug]) }}" class="text-secondary">{{ $product->category->name }}</a>
                    @else
                        {{ $product->category?->name }}
                    @endif
                </div>
                <h1 class="brand-font display-4 text-warning">{{ $product->name }}</h1>
                @php
                    $displayQty = ((int) ($cartQuantity ?? 0)) > 0 ? (int) $cartQuantity : 1;
                @endphp
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
                                    @if($component->is_active)
                                        <a href="{{ route('products.show', $component->slug) }}">{{ $component->name }}</a>
                                    @else
                                        {{ $component->name }}
                                    @endif
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

@push('scripts')
<script>
(() => {
    const media = document.getElementById('product-media');
    const playBtn = document.getElementById('product-media-play');
    const video = document.getElementById('product-media-video');
    if (!media || !playBtn || !video || media.dataset.hasVideo !== '1') return;

    const startPlayback = () => {
        media.classList.add('is-playing');
        video.classList.remove('d-none');
        video.play().catch(() => {});
    };

    playBtn.addEventListener('click', startPlayback);
})();
</script>
@endpush
