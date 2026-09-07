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
        <div class="cta-group d-flex gap-3 mt-4">
            <a href="#catalog" class="btn btn-gold btn-lg px-4">Shop Now</a>
            <a href="{{ route('cart.index') }}" class="btn btn-outline-gold btn-lg px-4">View Cart</a>
        </div>
    </div>
</section>

<section id="catalog" class="container py-5">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4">
        <div>
            <h2 class="brand-font display-5 mb-1 text-warning">Catalog</h2>
            <p class="text-secondary mb-0">Browse firecrackers by category and add favorites to your cart.</p>
        </div>
        <form class="d-flex gap-2" method="GET" action="{{ route('home') }}">
            <input type="search" name="q" value="{{ $search }}" class="form-control" placeholder="Search...">
            <select name="category" class="form-select">
                <option value="">All categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->slug }}" @selected($activeCategory === $category->slug)>{{ $category->name }}</option>
                @endforeach
            </select>
            <button class="btn btn-gold">Filter</button>
        </form>
    </div>

    <div class="row g-4">
        @forelse($products as $product)
            <div class="col-sm-6 col-lg-3">
                <div class="product-card">
                    <a href="{{ route('products.show', $product->slug) }}">
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}">
                    </a>
                    <div class="p-3">
                        <div class="small text-secondary">{{ $product->category?->name }}</div>
                        <h3 class="h5 mt-1"><a href="{{ route('products.show', $product->slug) }}" class="text-decoration-none" style="color:inherit">{{ $product->name }}</a></h3>
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="price-now">₹{{ number_format($product->discounted_price, 2) }}</span>
                            @if($product->discount_percent > 0)
                                <span class="price-old">₹{{ number_format($product->price, 2) }}</span>
                                <span class="badge badge-disc">{{ $product->discount_percent }}% off</span>
                            @endif
                        </div>
                        <form method="POST" action="{{ route('cart.store') }}">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <button class="btn btn-gold w-100" @disabled($product->stock < 1)>{{ $product->stock < 1 ? 'Out of stock' : 'Add to Cart' }}</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12"><div class="panel">No firecrackers found. Check back soon.</div></div>
        @endforelse
    </div>

    <div class="mt-4">{{ $products->links() }}</div>
</section>
@endsection
