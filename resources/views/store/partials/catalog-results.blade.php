<div class="row g-4">
    @forelse($products as $product)
        @php
            $qty = (int) (($cartQuantities ?? [])[$product->id] ?? 0);
            $displayQty = $qty > 0 ? $qty : 1;
        @endphp
        <div class="col-sm-6 col-lg-3">
            <div class="product-card">
                <a href="{{ route('products.show', $product->slug) }}">
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}">
                </a>
                <div class="p-3">
                    <div class="small text-secondary">{{ $product->category?->name }}</div>
                    <h3 class="h5 mt-1"><a href="{{ route('products.show', $product->slug) }}" class="text-decoration-none" style="color:inherit">{{ $product->name }}</a></h3>
                    <div class="d-flex align-items-center gap-2 mb-3" data-price-row>
                        <span
                            class="price-now"
                            data-unit-price="{{ $product->discounted_price }}"
                            data-original-price="{{ $product->price }}"
                        >₹{{ number_format($product->discounted_price * $displayQty, 2) }}</span>
                        @if($product->discount_percent > 0)
                            <span class="price-old" data-original-price="{{ $product->price }}">₹{{ number_format($product->price * $displayQty, 2) }}</span>
                            <span class="badge badge-disc">{{ $product->discount_percent }}% off</span>
                        @endif
                    </div>
                    @include('store.partials.cart-control', [
                        'productId' => $product->id,
                        'stock' => $product->stock,
                        'quantity' => $qty,
                    ])
                </div>
            </div>
        </div>
    @empty
        <div class="col-12"><div class="panel">No firecrackers found. Check back soon.</div></div>
    @endforelse
</div>

<div class="mt-4">{{ $products->links() }}</div>
