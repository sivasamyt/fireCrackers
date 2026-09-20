@php
    $qty = (int) ($quantity ?? 0);
    $inCart = $qty > 0;
    $stock = (int) $stock;
    $maxQty = min(50, max(0, $stock));
    $outOfStock = $stock < 1;
@endphp
<div
    class="cart-control"
    data-cart-control
    data-product-id="{{ $productId ?? '' }}"
    data-gift-box-id="{{ $giftBoxId ?? '' }}"
    data-stock="{{ $stock }}"
    data-quantity="{{ $qty }}"
>
    @if($outOfStock)
        <button type="button" class="btn btn-gold w-100" disabled>Out of stock</button>
    @else
        <button
            type="button"
            class="btn btn-gold w-100 cart-control-add {{ $inCart ? 'd-none' : '' }}"
            data-cart-add
        >{{ $addLabel ?? 'Add to Cart' }}</button>

        <div class="cart-control-added {{ $inCart ? '' : 'd-none' }}" data-cart-added>
            <div class="cart-qty-row">
                <button type="button" class="btn btn-outline-gold cart-qty-btn" data-cart-minus aria-label="Decrease quantity">−</button>
                <span class="cart-qty-value" data-cart-qty>{{ $qty }}</span>
                <button type="button" class="btn btn-outline-gold cart-qty-btn" data-cart-plus aria-label="Increase quantity">+</button>
            </div>
            <div class="cart-added-label">Added</div>
        </div>
    @endif
</div>
