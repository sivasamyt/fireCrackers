@php
    $qty = (int) ($quantity ?? 0);
    $stock = (int) $stock;
    $maxQty = min(50, max(0, $stock));
    $outOfStock = $stock < 1;
@endphp
<div
    class="cart-qty-inline"
    data-cart-control
    data-cart-inline
    data-product-id="{{ $productId }}"
    data-stock="{{ $stock }}"
    data-quantity="{{ $qty }}"
>
    @if($outOfStock)
        <span class="badge text-bg-secondary">Out of stock</span>
    @else
        <button type="button" class="cart-qty-inline-btn" data-cart-minus aria-label="Decrease quantity" @disabled($qty <= 0)>−</button>
        <input
            type="number"
            class="cart-qty-inline-input"
            data-cart-input
            value="{{ $qty ?: '' }}"
            placeholder="Qty"
            min="0"
            max="{{ $maxQty }}"
            inputmode="numeric"
            aria-label="Quantity"
        >
        <button type="button" class="cart-qty-inline-btn" data-cart-plus aria-label="Increase quantity" @disabled($qty >= $maxQty)>+</button>
    @endif
</div>
