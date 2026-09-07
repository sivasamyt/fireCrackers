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
