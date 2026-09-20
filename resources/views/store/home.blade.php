@extends('layouts.store')

@section('title', 'FireCrackers — Festival Fireworks Shop')

@section('content')
<section class="hero">
    <span class="spark" style="top:18%; left:12%;"></span>
    <span class="spark" style="top:28%; left:72%; animation-delay:.6s;"></span>
    <span class="spark" style="top:62%; left:84%; animation-delay:1.2s;"></span>
    <div class="container py-5">
        <div class="brand-font hero-brand">FireCrackers</div>
        <p class="fs-5 mt-3">Premium sparklers, rockets, and festive packs delivered to your door. Bright nights. Bold celebrations.</p>
        <div class="cta-group d-flex gap-3 mt-4 flex-wrap">
            <a href="#catalog" class="btn btn-gold btn-lg px-4">Shop Now</a>
            <a href="#combos" class="btn btn-outline-gold btn-lg px-4">Combo Packs</a>
            <a href="#gift-boxes" class="btn btn-outline-gold btn-lg px-4">Gift Boxes</a>
        </div>
    </div>
</section>

<section id="catalog" class="container py-5">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4">
        <div>
            <h2 class="brand-font display-5 mb-1 text-warning">Catalog</h2>
            <p class="text-secondary mb-0">Browse firecrackers by category and add favorites to your cart.</p>
        </div>
        <form id="catalog-filter" class="d-flex gap-2 flex-wrap" method="GET" action="{{ route('home') }}">
            <input type="search" name="q" id="catalog-search" value="{{ $search }}" class="form-control" placeholder="Search name, category, price..." autocomplete="off">
            <select name="category" id="catalog-category" class="form-select">
                <option value="">All categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->slug }}" @selected($activeCategory === $category->slug)>{{ $category->name }}</option>
                @endforeach
            </select>
            <a id="catalog-clear" href="{{ route('home') }}#catalog" class="btn btn-outline-gold {{ ($search !== '' || $activeCategory !== '') ? '' : 'd-none' }}">Clear</a>
        </form>
    </div>

    <div id="catalog-results">
        @include('store.partials.catalog-results')
    </div>
</section>

@if($combos->isNotEmpty())
<section id="combos" class="container pb-5">
    <div class="mb-4">
        <h2 class="brand-font display-5 mb-1 text-warning">Combo Packs</h2>
        <p class="text-secondary mb-0">Special bundles mapped from multiple crackers — priced as one pack.</p>
    </div>
    <div class="row g-4">
        @foreach($combos as $combo)
            <div class="col-sm-6 col-lg-4">
                <div class="product-card">
                    <a href="{{ route('products.show', $combo->slug) }}">
                        <img src="{{ $combo->image_url }}" alt="{{ $combo->name }}">
                    </a>
                    <div class="p-3">
                        <div class="small text-secondary">Combo</div>
                        <h3 class="h5 mt-1"><a href="{{ route('products.show', $combo->slug) }}" class="text-decoration-none" style="color:inherit">{{ $combo->name }}</a></h3>
                        <p class="small text-secondary mb-1">Includes:</p>
                        <ul class="small text-secondary mb-2 ps-3">
                            @foreach($combo->components as $component)
                                <li>
                                    {{ $component->name }}
                                    @if($component->pivot->quantity > 1)
                                        × {{ $component->pivot->quantity }}
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="price-now">₹{{ number_format($combo->discounted_price, 2) }}</span>
                            @if($combo->discount_percent > 0)
                                <span class="price-old">₹{{ number_format($combo->price, 2) }}</span>
                                <span class="badge badge-disc">{{ $combo->discount_percent }}% off</span>
                            @endif
                        </div>
                        <form method="POST" action="{{ route('cart.store') }}">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $combo->id }}">
                            <button class="btn btn-gold w-100" @disabled($combo->stock < 1)>{{ $combo->stock < 1 ? 'Out of stock' : 'Add to Cart' }}</button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</section>
@endif

@if($giftBoxes->isNotEmpty())
<section id="gift-boxes" class="container pb-5">
    <div class="mb-4">
        <h2 class="brand-font display-5 mb-1 text-warning">Gift Boxes</h2>
        <p class="text-secondary mb-0">Curated boxes of crackers — tap a box to see everything inside.</p>
    </div>
    <div class="row g-4">
        @foreach($giftBoxes as $box)
            <div class="col-sm-6 col-lg-4">
                <div class="product-card h-100">
                    <button type="button" class="btn p-0 border-0 w-100" data-bs-toggle="modal" data-bs-target="#gift-box-{{ $box->id }}">
                        <img src="{{ $box->image_url }}" alt="{{ $box->name }}">
                    </button>
                    <div class="p-3">
                        <div class="small text-secondary">Gift Box · {{ $box->products->count() }} items</div>
                        <h3 class="h5 mt-1">{{ $box->name }}</h3>
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="price-now">₹{{ number_format($box->discounted_price, 2) }}</span>
                            @if($box->discount_percent > 0)
                                <span class="price-old">₹{{ number_format($box->price, 2) }}</span>
                                <span class="badge badge-disc">{{ $box->discount_percent }}% off</span>
                            @endif
                        </div>
                        <button type="button" class="btn btn-gold w-100" data-bs-toggle="modal" data-bs-target="#gift-box-{{ $box->id }}">
                            View Items
                        </button>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="gift-box-{{ $box->id }}" tabindex="-1" aria-labelledby="gift-box-label-{{ $box->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
                    <div class="modal-content text-dark">
                        <div class="modal-header">
                            <h5 class="modal-title" id="gift-box-label-{{ $box->id }}">{{ $box->name }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            @if($box->description)
                                <p class="text-muted small">{{ $box->description }}</p>
                            @endif
                            <ul class="list-unstyled mb-0">
                                @foreach($box->products as $item)
                                    <li class="d-flex align-items-center gap-3 py-2 border-bottom">
                                        <img src="{{ $item->image_url }}" alt="{{ $item->name }}" style="width:48px;height:48px;object-fit:cover;border-radius:.4rem;">
                                        <span class="flex-grow-1">{{ $item->name }}</span>
                                        <span class="text-muted small">× {{ $item->pivot->quantity }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="modal-footer justify-content-between">
                            <span class="fw-bold">₹{{ number_format($box->discounted_price, 2) }}</span>
                            <form method="POST" action="{{ route('cart.store') }}">
                                @csrf
                                <input type="hidden" name="gift_box_id" value="{{ $box->id }}">
                                <button class="btn btn-gold">Add gift box to cart</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</section>
@endif
@endsection

@push('scripts')
<script>
(() => {
    const form = document.getElementById('catalog-filter');
    const search = document.getElementById('catalog-search');
    const category = document.getElementById('catalog-category');
    const results = document.getElementById('catalog-results');
    const clearBtn = document.getElementById('catalog-clear');
    const catalog = document.getElementById('catalog');
    if (!form || !search || !category || !results) return;

    let timer = null;
    let requestId = 0;

    const updateClear = () => {
        if (!clearBtn) return;
        const active = search.value.trim() !== '' || category.value !== '';
        clearBtn.classList.toggle('d-none', !active);
    };

    const loadCatalog = async () => {
        const id = ++requestId;
        const params = new URLSearchParams(new FormData(form));
        params.set('partial', '1');

        try {
            const response = await fetch(`${form.action}?${params.toString()}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            });
            if (!response.ok || id !== requestId) return;

            results.innerHTML = await response.text();

            params.delete('partial');
            const qs = params.toString();
            history.replaceState(null, '', qs ? `${form.action}?${qs}` : form.action);

            updateClear();
            catalog.scrollIntoView({ behavior: 'smooth', block: 'start' });
            search.focus();
            const len = search.value.length;
            search.setSelectionRange(len, len);
        } catch (e) {
            // Keep current results on network failure
        }
    };

    form.addEventListener('submit', (e) => {
        e.preventDefault();
        clearTimeout(timer);
        loadCatalog();
    });

    search.addEventListener('input', () => {
        clearTimeout(timer);
        updateClear();
        timer = setTimeout(loadCatalog, 600);
    });

    category.addEventListener('change', () => {
        clearTimeout(timer);
        updateClear();
        loadCatalog();
    });
})();
</script>
@endpush
