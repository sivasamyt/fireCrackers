<div class="table-responsive catalog-table-wrap d-none d-md-block">
    <table class="table catalog-table align-middle mb-0">
        <thead>
            <tr>
                <th class="text-center">S.No</th>
                <th class="text-center">Image</th>
                <th>Product Name</th>
                <th class="text-center">Actual Price</th>
                <th class="text-center">Offer Price</th>
                <th class="text-center">Amount</th>
                <th class="text-center">Quantity</th>
                <th class="text-center">Total</th>
            </tr>
        </thead>
        <tbody>
        @forelse($products as $product)
            @php($qty = (int) (($cartQuantities ?? [])[$product->id] ?? 0))
            <tr data-catalog-row>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td class="text-center">
                    <a href="{{ route('products.show', $product->slug) }}">
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="catalog-thumb" loading="lazy">
                    </a>
                </td>
                <td class="catalog-name-cell">
                    <div class="catalog-name-row">
                        <a href="{{ route('products.show', $product->slug) }}" class="catalog-name">{{ $product->name }}</a>
                        @if($product->discount_percent > 0)
                            <span class="badge badge-disc">{{ $product->discount_percent }}% off</span>
                        @endif
                    </div>
                    @if($product->description)
                        <div class="catalog-desc">{{ \Illuminate\Support\Str::limit(strip_tags($product->description), 90) }}</div>
                    @endif
                </td>
                <td class="text-center">₹{{ number_format($product->price, 2) }}</td>
                <td class="text-center">
                    @if($product->discount_percent > 0)
                        <span class="catalog-old-price">₹{{ number_format($product->price, 2) }}</span>
                    @else
                        <span class="text-secondary">—</span>
                    @endif
                </td>
                <td class="text-center fw-bold">₹{{ number_format($product->discounted_price, 2) }}</td>
                <td class="text-center">
                    @include('store.partials.cart-qty-input', [
                        'productId' => $product->id,
                        'stock' => $product->stock,
                        'quantity' => $qty,
                    ])
                </td>
                <td class="text-center catalog-total" data-row-total data-unit-price="{{ $product->discounted_price }}">₹{{ number_format($product->discounted_price * $qty, 2) }}</td>
            </tr>
        @empty
            <tr><td colspan="8" class="text-center py-4 text-secondary">No firecrackers found. Check back soon.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

<div class="catalog-card-list d-md-none">
    @forelse($products as $product)
        @php($qty = (int) (($cartQuantities ?? [])[$product->id] ?? 0))
        <div class="catalog-card" data-catalog-row>
            <a href="{{ route('products.show', $product->slug) }}" class="catalog-card-img">
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" loading="lazy">
            </a>
            <div class="catalog-card-body">
                <div class="catalog-name-row">
                    <a href="{{ route('products.show', $product->slug) }}" class="catalog-name">{{ $product->name }}</a>
                    @if($product->discount_percent > 0)
                        <span class="badge badge-disc">{{ $product->discount_percent }}% off</span>
                    @endif
                </div>
                <div class="catalog-card-meta">Code: {{ $loop->iteration }}</div>
                @if($product->description)
                    <div class="catalog-card-meta">{{ \Illuminate\Support\Str::limit(strip_tags($product->description), 40) }}</div>
                @endif
                <div class="catalog-card-prices">
                    @if($product->discount_percent > 0)
                        <span class="catalog-card-old">₹{{ number_format($product->price, 2) }}</span>
                    @endif
                    <span class="catalog-card-amount">₹{{ number_format($product->discounted_price, 2) }}</span>
                </div>
            </div>
            <div class="catalog-card-actions">
                @include('store.partials.cart-qty-input', [
                    'productId' => $product->id,
                    'stock' => $product->stock,
                    'quantity' => $qty,
                ])
                <div class="catalog-card-total">
                    Total: <span data-row-total data-unit-price="{{ $product->discounted_price }}">₹{{ number_format($product->discounted_price * $qty, 2) }}</span>
                </div>
            </div>
        </div>
    @empty
        <div class="catalog-card-empty">No firecrackers found. Check back soon.</div>
    @endforelse
</div>
