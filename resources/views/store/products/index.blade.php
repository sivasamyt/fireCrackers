@extends('layouts.store')

@section('title', 'All Products | Royal Crackers')
@section('meta_description', 'Browse all Royal Crackers products by category and add them to your cart.')
@if($search !== '')
    @section('robots', 'noindex,follow')
@endif

@section('content')
<section class="container py-3 py-md-5 products-page">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-3 mb-md-4">
        <div class="d-none d-md-block">
            <h1 class="brand-font display-5 mb-1 text-warning">All Products</h1>
            <p class="text-secondary mb-0">Pick a category and set quantities — items are added to your cart instantly.</p>
        </div>
        <form id="all-products-filter" class="products-search-form d-flex gap-2" method="GET" action="{{ route('products.index') }}">
            <input type="hidden" name="category" id="all-products-category" value="{{ $activeCategory }}">
            <div class="products-search">
                <svg class="products-search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true">
                    <circle cx="11" cy="11" r="7"></circle>
                    <line x1="21" y1="21" x2="16.5" y2="16.5"></line>
                </svg>
                <input type="search" name="q" id="all-products-search" value="{{ $search }}" class="form-control" placeholder="Search products by name..." autocomplete="off">
            </div>
            <a id="all-products-clear" href="{{ route('products.index') }}" class="btn btn-outline-gold {{ $search !== '' ? '' : 'd-none' }}">Clear</a>
        </form>
    </div>

    <div class="cat-tiles-wrap mb-4">
        <button type="button" class="cat-scroll-btn cat-scroll-prev d-none" id="cat-scroll-prev" aria-label="Scroll categories left">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="15 18 9 12 15 6"></polyline></svg>
        </button>
        <div class="cat-tiles" id="category-tiles">
            <a href="{{ route('products.index') }}" class="cat-tile cat-tile-all {{ $activeCategory === '' ? 'active' : '' }}" data-category-tile="">
                <span class="cat-tile-count">{{ $totalCount }}</span>
                <span class="cat-tile-img">
                    @if($siteLogo)
                        <img src="{{ $siteLogo }}" alt="Show All">
                    @else
                        <span class="cat-tile-all-icon">★</span>
                    @endif
                </span>
                <span class="cat-tile-name">Show All</span>
            </a>
            @foreach($categories as $category)
                <a href="{{ route('products.index', ['category' => $category->slug]) }}" class="cat-tile {{ $activeCategory === $category->slug ? 'active' : '' }}" data-category-tile="{{ $category->slug }}">
                    <span class="cat-tile-count">{{ $category->products_count }}</span>
                    <span class="cat-tile-img"><img src="{{ $category->image_url }}" alt="{{ $category->name }}" loading="lazy"></span>
                    <span class="cat-tile-name">{{ $category->name }}</span>
                </a>
            @endforeach
        </div>
        <button type="button" class="cat-scroll-btn cat-scroll-next d-none" id="cat-scroll-next" aria-label="Scroll categories right">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 18 15 12 9 6"></polyline></svg>
        </button>
    </div>

    <div id="all-products-results">
        @include('store.partials.catalog-table')
    </div>
</section>
@endsection

@push('scripts')
<script>
(() => {
    const form = document.getElementById('all-products-filter');
    const search = document.getElementById('all-products-search');
    const categoryInput = document.getElementById('all-products-category');
    const results = document.getElementById('all-products-results');
    const clearBtn = document.getElementById('all-products-clear');
    const tiles = document.getElementById('category-tiles');
    const prevBtn = document.getElementById('cat-scroll-prev');
    const nextBtn = document.getElementById('cat-scroll-next');

    if (tiles && prevBtn && nextBtn) {
        const updateArrows = () => {
            const maxScroll = tiles.scrollWidth - tiles.clientWidth;
            const overflowing = maxScroll > 1;
            prevBtn.classList.toggle('d-none', !overflowing);
            nextBtn.classList.toggle('d-none', !overflowing);
            prevBtn.disabled = tiles.scrollLeft <= 1;
            nextBtn.disabled = tiles.scrollLeft >= maxScroll - 1;
        };
        const scrollTiles = (direction) => {
            tiles.scrollBy({ left: direction * tiles.clientWidth * 0.8, behavior: 'smooth' });
        };

        prevBtn.addEventListener('click', () => scrollTiles(-1));
        nextBtn.addEventListener('click', () => scrollTiles(1));
        tiles.addEventListener('scroll', updateArrows, { passive: true });
        window.addEventListener('resize', updateArrows);
        window.addEventListener('load', updateArrows);

        const activeTile = tiles.querySelector('.cat-tile.active:not(.cat-tile-all)');
        if (activeTile) {
            tiles.scrollLeft = activeTile.offsetLeft - (tiles.clientWidth - activeTile.offsetWidth) / 2;
        }
        updateArrows();
    }

    if (!form || !search || !results) return;

    let timer = null;
    let requestId = 0;

    const updateClear = () => {
        if (!clearBtn) return;
        clearBtn.classList.toggle('d-none', search.value.trim() === '');
    };

    const buildParams = () => {
        const params = new URLSearchParams();
        if (categoryInput.value) params.set('category', categoryInput.value);
        if (search.value.trim() !== '') params.set('q', search.value.trim());
        return params;
    };

    const loadProducts = async ({ focusSearch = true } = {}) => {
        const id = ++requestId;
        const params = buildParams();
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
            if (focusSearch) {
                search.focus();
                const len = search.value.length;
                search.setSelectionRange(len, len);
            }
        } catch (e) {
            // Keep current results on network failure
        }
    };

    form.addEventListener('submit', (e) => {
        e.preventDefault();
        clearTimeout(timer);
        loadProducts();
    });

    search.addEventListener('input', () => {
        clearTimeout(timer);
        updateClear();
        timer = setTimeout(loadProducts, 600);
    });

    clearBtn?.addEventListener('click', (e) => {
        e.preventDefault();
        search.value = '';
        clearTimeout(timer);
        loadProducts();
    });

    tiles?.addEventListener('click', (e) => {
        const tile = e.target.closest('[data-category-tile]');
        if (!tile) return;
        e.preventDefault();
        tiles.querySelectorAll('.cat-tile').forEach((t) => t.classList.toggle('active', t === tile));
        categoryInput.value = tile.dataset.categoryTile;
        clearTimeout(timer);
        loadProducts({ focusSearch: false });
    });
})();
</script>
@endpush
