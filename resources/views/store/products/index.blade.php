@extends('layouts.store')

@section('title', 'All Products — FireCrackers')

@section('content')
<section class="container py-5">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4">
        <div>
            <h1 class="brand-font display-5 mb-1 text-warning">All Products</h1>
            <p class="text-secondary mb-0">Browse every firecracker across all categories.</p>
        </div>
        <form id="all-products-filter" class="d-flex gap-2 flex-wrap" method="GET" action="{{ route('products.index') }}">
            <input type="search" name="q" id="all-products-search" value="{{ $search }}" class="form-control" placeholder="Search by name..." autocomplete="off" style="min-width:220px">
            <a id="all-products-clear" href="{{ route('products.index') }}" class="btn btn-outline-gold {{ $search !== '' ? '' : 'd-none' }}">Clear</a>
        </form>
    </div>

    <div id="all-products-results">
        @include('store.partials.catalog-results')
    </div>
</section>
@endsection

@push('scripts')
<script>
(() => {
    const form = document.getElementById('all-products-filter');
    const search = document.getElementById('all-products-search');
    const results = document.getElementById('all-products-results');
    const clearBtn = document.getElementById('all-products-clear');
    if (!form || !search || !results) return;

    let timer = null;
    let requestId = 0;

    const updateClear = () => {
        if (!clearBtn) return;
        clearBtn.classList.toggle('d-none', search.value.trim() === '');
    };

    const loadProducts = async () => {
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
        loadProducts();
    });

    search.addEventListener('input', () => {
        clearTimeout(timer);
        updateClear();
        timer = setTimeout(loadProducts, 600);
    });
})();
</script>
@endpush
