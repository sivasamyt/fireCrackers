<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'FireCrackers')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --fc-night: #070b16;
            --fc-navy: #10182b;
            --fc-gold: #f5c451;
            --fc-amber: #ff8a3d;
            --fc-cream: #f7f1e3;
            --fc-muted: #9aa3b8;
        }
        body {
            font-family: 'Outfit', sans-serif;
            background:
                radial-gradient(ellipse at 20% 0%, rgba(255,138,61,.18), transparent 45%),
                radial-gradient(ellipse at 80% 10%, rgba(245,196,81,.12), transparent 40%),
                linear-gradient(180deg, #05070f 0%, #0c1424 45%, #10182b 100%);
            color: var(--fc-cream);
            min-height: 100vh;
        }
        .brand-font { font-family: 'Bebas Neue', sans-serif; letter-spacing: .04em; }
        .navbar-store {
            background: rgba(7,11,22,.85);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(245,196,81,.2);
        }
        .navbar-store .nav-link, .navbar-store .navbar-brand { color: var(--fc-cream); }
        .navbar-store .nav-link:hover { color: var(--fc-gold); }
        .btn-gold {
            background: linear-gradient(135deg, var(--fc-gold), var(--fc-amber));
            border: 0;
            color: #1a1205;
            font-weight: 700;
        }
        .btn-gold:hover { filter: brightness(1.05); color: #1a1205; }
        .btn-outline-gold {
            border: 1px solid var(--fc-gold);
            color: var(--fc-gold);
        }
        .btn-outline-gold:hover { background: var(--fc-gold); color: #1a1205; }
        .hero {
            min-height: 88vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
            background:
                linear-gradient(90deg, rgba(5,7,15,.92) 0%, rgba(5,7,15,.55) 45%, rgba(5,7,15,.25) 100%),
                url('https://images.unsplash.com/photo-1481166852575-bb42b2f0a0ad?auto=format&fit=crop&w=1800&q=80') center/cover no-repeat;
        }
        .hero-brand {
            font-size: clamp(4rem, 12vw, 8rem);
            line-height: .9;
            color: var(--fc-gold);
            text-shadow: 0 0 40px rgba(245,196,81,.35);
            animation: riseIn .9s ease both;
        }
        .hero p { max-width: 34rem; color: #e8e2d4; animation: riseIn 1.1s ease both; }
        .hero .cta-group { animation: riseIn 1.3s ease both; }
        @keyframes riseIn {
            from { opacity: 0; transform: translateY(18px); }
            to { opacity: 1; transform: none; }
        }
        .spark {
            position: absolute;
            width: 6px; height: 6px; border-radius: 50%;
            background: var(--fc-gold);
            box-shadow: 0 0 12px var(--fc-gold);
            animation: twinkle 2.8s infinite ease-in-out;
        }
        @keyframes twinkle {
            0%,100% { opacity: .2; transform: scale(.6); }
            50% { opacity: 1; transform: scale(1.3); }
        }
        .product-card {
            background: rgba(16,24,43,.72);
            border: 1px solid rgba(245,196,81,.15);
            border-radius: .75rem;
            overflow: hidden;
            height: 100%;
            transition: transform .25s ease, border-color .25s ease;
        }
        .product-card:hover {
            transform: translateY(-4px);
            border-color: rgba(245,196,81,.45);
        }
        .product-card img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            background: #0b1220;
        }
        .price-old { text-decoration: line-through; color: var(--fc-muted); font-size: .9rem; }
        .price-now { color: var(--fc-gold); font-weight: 700; font-size: 1.15rem; }
        .badge-disc { background: var(--fc-amber); color: #1a1205; }
        .panel {
            background: rgba(16,24,43,.85);
            border: 1px solid rgba(245,196,81,.18);
            border-radius: 1rem;
            padding: 1.5rem;
        }
        .form-control, .form-select {
            background: #0b1220;
            border-color: rgba(245,196,81,.25);
            color: var(--fc-cream);
        }
        .form-control:focus, .form-select:focus {
            background: #0b1220;
            color: var(--fc-cream);
            border-color: var(--fc-gold);
            box-shadow: 0 0 0 .2rem rgba(245,196,81,.2);
        }
        .footer-store {
            border-top: 1px solid rgba(245,196,81,.15);
            color: var(--fc-muted);
            margin-top: 4rem;
            padding: 2rem 0;
        }
        a { color: var(--fc-gold); text-decoration: none; }
        a:hover { color: var(--fc-amber); }
        .alert { border: 0; }
        .pagination {
            --bs-pagination-padding-x: .85rem;
            --bs-pagination-padding-y: .45rem;
            --bs-pagination-font-size: .95rem;
            --bs-pagination-color: var(--fc-cream);
            --bs-pagination-bg: rgba(16,24,43,.9);
            --bs-pagination-border-width: 1px;
            --bs-pagination-border-color: rgba(245,196,81,.25);
            --bs-pagination-hover-color: var(--fc-gold);
            --bs-pagination-hover-bg: rgba(245,196,81,.12);
            --bs-pagination-hover-border-color: rgba(245,196,81,.5);
            --bs-pagination-focus-color: var(--fc-gold);
            --bs-pagination-focus-bg: rgba(245,196,81,.12);
            --bs-pagination-focus-box-shadow: 0 0 0 .2rem rgba(245,196,81,.2);
            --bs-pagination-active-color: #1a1205;
            --bs-pagination-active-bg: linear-gradient(135deg, var(--fc-gold), var(--fc-amber));
            --bs-pagination-active-border-color: var(--fc-gold);
            --bs-pagination-disabled-color: var(--fc-muted);
            --bs-pagination-disabled-bg: rgba(11,18,32,.7);
            --bs-pagination-disabled-border-color: rgba(245,196,81,.12);
            gap: .35rem;
        }
        .pagination .page-link {
            border-radius: .5rem;
            background: rgba(16,24,43,.9);
            color: var(--fc-cream);
            border-color: rgba(245,196,81,.25);
        }
        .pagination .page-link:hover {
            background: rgba(245,196,81,.12);
            color: var(--fc-gold);
            border-color: rgba(245,196,81,.5);
        }
        .pagination .page-item.active .page-link {
            background: linear-gradient(135deg, var(--fc-gold), var(--fc-amber));
            border-color: var(--fc-gold);
            color: #1a1205;
            font-weight: 700;
        }
        .pagination .page-item.disabled .page-link {
            background: rgba(11,18,32,.7);
            color: var(--fc-muted);
            border-color: rgba(245,196,81,.12);
            opacity: .7;
        }
        nav[role="navigation"] .small,
        nav[aria-label] .small,
        nav[role="navigation"] .text-muted,
        nav[aria-label] .text-muted {
            color: var(--fc-muted) !important;
        }

        /* In-card cart controls */
        .cart-control-added { text-align: center; }
        .cart-qty-row {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
        }
        .cart-qty-btn {
            width: 2.25rem;
            height: 2.25rem;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
            line-height: 1;
        }
        .cart-qty-value {
            min-width: 1.75rem;
            text-align: center;
            font-weight: 700;
            font-size: 1.05rem;
        }
        .cart-added-label {
            margin-top: .4rem;
            font-size: .8rem;
            color: var(--fc-gold);
            font-weight: 600;
            letter-spacing: .04em;
            text-transform: uppercase;
        }
        .cart-control[data-busy="1"] { opacity: .65; pointer-events: none; }

        /* Header cart dropdown */
        .nav-cart {
            position: relative;
        }
        .nav-cart-link {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
        }
        .nav-cart-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 1.35rem;
            height: 1.35rem;
            padding: 0 .35rem;
            border-radius: .4rem;
            background: linear-gradient(135deg, var(--fc-gold), var(--fc-amber));
            color: #1a1205;
            font-size: .75rem;
            font-weight: 700;
        }
        .nav-cart-dropdown {
            position: absolute;
            top: calc(100% + .35rem);
            right: 0;
            width: min(22rem, calc(100vw - 2rem));
            background: rgba(12, 18, 34, .98);
            border: 1px solid rgba(245,196,81,.28);
            border-radius: .75rem;
            box-shadow: 0 16px 40px rgba(0,0,0,.45);
            padding: .75rem;
            opacity: 0;
            visibility: hidden;
            transform: translateY(6px);
            transition: opacity .18s ease, transform .18s ease, visibility .18s;
            z-index: 1050;
        }
        .nav-cart:hover .nav-cart-dropdown,
        .nav-cart:focus-within .nav-cart-dropdown,
        .nav-cart.is-open .nav-cart-dropdown {
            opacity: 1;
            visibility: visible;
            transform: none;
        }
        .nav-cart-items {
            max-height: 16rem;
            overflow-y: auto;
        }
        .nav-cart-item {
            display: flex;
            gap: .65rem;
            align-items: center;
            padding: .55rem 0;
            border-bottom: 1px solid rgba(245,196,81,.12);
        }
        .nav-cart-item:last-child { border-bottom: 0; }
        .nav-cart-item img {
            width: 48px;
            height: 48px;
            object-fit: cover;
            border-radius: .4rem;
            flex-shrink: 0;
        }
        .nav-cart-item-meta { flex: 1; min-width: 0; }
        .nav-cart-item-name {
            font-size: .9rem;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .nav-cart-item-sub {
            font-size: .75rem;
            color: var(--fc-muted);
        }
        .nav-cart-item-total {
            font-weight: 700;
            color: var(--fc-gold);
            font-size: .9rem;
            white-space: nowrap;
        }
        .nav-cart-empty {
            color: var(--fc-muted);
            font-size: .9rem;
            padding: .75rem .25rem;
            text-align: center;
        }
        .nav-cart-footer {
            border-top: 1px solid rgba(245,196,81,.18);
            margin-top: .5rem;
            padding-top: .75rem;
        }
        .nav-cart-total {
            display: flex;
            justify-content: space-between;
            font-weight: 700;
            margin-bottom: .65rem;
        }
    </style>
    @stack('head')
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-store sticky-top">
    <div class="container">
        <a class="navbar-brand brand-font fs-3" href="{{ route('home') }}">FireCrackers</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMain">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                <li class="nav-item"><a class="nav-link" href="{{ route('home') }}#catalog">Shop</a></li>
                <li class="nav-item nav-cart" id="nav-cart">
                    <a class="nav-link nav-cart-link" href="{{ route('cart.index') }}" id="nav-cart-link">
                        Cart <span class="nav-cart-badge" id="nav-cart-count">{{ $cartCount ?? 0 }}</span>
                    </a>
                    <div class="nav-cart-dropdown" id="nav-cart-dropdown" role="menu" aria-label="Cart preview">
                        <div class="nav-cart-items" id="nav-cart-items">
                            @php($summary = $cartSummary ?? ['items' => [], 'grand_total' => 0, 'cart_count' => 0])
                            @forelse(($summary['items'] ?? []) as $item)
                                <div class="nav-cart-item">
                                    <img src="{{ $item['image_url'] }}" alt="">
                                    <div class="nav-cart-item-meta">
                                        <div class="nav-cart-item-name">{{ $item['name'] }}</div>
                                        <div class="nav-cart-item-sub">Qty {{ $item['quantity'] }} · ₹{{ number_format($item['unit_price'], 2) }}</div>
                                    </div>
                                    <div class="nav-cart-item-total">₹{{ number_format($item['line_total'], 2) }}</div>
                                </div>
                            @empty
                                <div class="nav-cart-empty">Your cart is empty</div>
                            @endforelse
                        </div>
                        <div class="nav-cart-footer {{ ($summary['cart_count'] ?? 0) > 0 ? '' : 'd-none' }}" id="nav-cart-footer">
                            <div class="nav-cart-total">
                                <span>Total</span>
                                <span class="text-warning" id="nav-cart-total">₹{{ number_format($summary['grand_total'] ?? 0, 2) }}</span>
                            </div>
                            <a href="{{ route('checkout.create') }}" class="btn btn-gold w-100 btn-sm">Checkout</a>
                            <a href="{{ route('cart.index') }}" class="btn btn-outline-gold w-100 btn-sm mt-2">View cart</a>
                        </div>
                    </div>
                </li>
                @auth
                    @if(auth()->user()->isAdmin())
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}">Admin</a></li>
                    @else
                        <li class="nav-item"><a class="nav-link" href="{{ route('account.orders') }}">My Orders</a></li>
                    @endif
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}">@csrf
                            <button class="btn btn-sm btn-outline-gold">Logout</button>
                        </form>
                    </li>
                @else
                    <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
                    <li class="nav-item"><a class="btn btn-sm btn-gold" href="{{ route('register') }}">Register</a></li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

@if(session('success'))
    <div class="container mt-3"><div class="alert alert-success">{{ session('success') }}</div></div>
@endif
@if(session('error'))
    <div class="container mt-3"><div class="alert alert-danger">{{ session('error') }}</div></div>
@endif

@yield('content')

<footer class="footer-store">
    <div class="container d-flex flex-column flex-md-row justify-content-between gap-2">
        <div class="brand-font fs-4 text-warning">FireCrackers</div>
        <div>Celebrate safely. Follow local firework regulations.</div>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
(() => {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const routes = {
        store: @json(route('cart.store')),
        update: @json(route('cart.update')),
        destroy: @json(route('cart.destroy')),
    };

    const countEl = document.getElementById('nav-cart-count');
    const itemsEl = document.getElementById('nav-cart-items');
    const footerEl = document.getElementById('nav-cart-footer');
    const totalEl = document.getElementById('nav-cart-total');
    const navCart = document.getElementById('nav-cart');
    const navCartLink = document.getElementById('nav-cart-link');

    const formatMoney = (value) =>
        '₹' + Number(value || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    const escapeHtml = (str) => String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');

    const renderDropdown = (summary) => {
        if (!itemsEl) return;

        const items = summary.items || [];
        if (countEl) countEl.textContent = String(summary.cart_count || 0);

        if (items.length === 0) {
            itemsEl.innerHTML = '<div class="nav-cart-empty">Your cart is empty</div>';
            footerEl?.classList.add('d-none');
            return;
        }

        itemsEl.innerHTML = items.map((item) => `
            <div class="nav-cart-item">
                <img src="${escapeHtml(item.image_url || '')}" alt="">
                <div class="nav-cart-item-meta">
                    <div class="nav-cart-item-name">${escapeHtml(item.name || '')}</div>
                    <div class="nav-cart-item-sub">Qty ${item.quantity} · ${formatMoney(item.unit_price)}</div>
                </div>
                <div class="nav-cart-item-total">${formatMoney(item.line_total)}</div>
            </div>
        `).join('');

        if (totalEl) totalEl.textContent = formatMoney(summary.grand_total);
        footerEl?.classList.remove('d-none');
    };

    const syncControls = (summary) => {
        const qtyByProduct = {};
        const qtyByGift = {};

        (summary.items || []).forEach((item) => {
            if (item.type === 'gift_box') {
                qtyByGift[item.id] = item.quantity;
            } else {
                qtyByProduct[item.id] = item.quantity;
            }
        });

        document.querySelectorAll('[data-cart-control]').forEach((el) => {
            const productId = el.dataset.productId;
            const giftBoxId = el.dataset.giftBoxId;
            let qty = 0;

            if (giftBoxId) {
                qty = qtyByGift[giftBoxId] || 0;
            } else if (productId) {
                qty = qtyByProduct[productId] || 0;
            }

            setControlQty(el, qty);
        });
    };

    const setControlQty = (el, qty) => {
        el.dataset.quantity = String(qty);
        const addBtn = el.querySelector('[data-cart-add]');
        const added = el.querySelector('[data-cart-added]');
        const qtyEl = el.querySelector('[data-cart-qty]');
        const stock = parseInt(el.dataset.stock || '0', 10);

        const priceRow = el.closest('[data-price-row]')
            || el.closest('.product-card, .panel, .modal-content')?.querySelector('[data-price-row]');
        if (priceRow) {
            const multiplier = qty > 0 ? qty : 1;
            const priceNow = priceRow.querySelector('.price-now');
            const priceOld = priceRow.querySelector('.price-old');
            const unit = parseFloat(priceNow?.dataset.unitPrice || '0');
            const original = parseFloat(
                priceOld?.dataset.originalPrice
                || priceNow?.dataset.originalPrice
                || '0'
            );

            if (priceNow && !Number.isNaN(unit)) {
                priceNow.textContent = formatMoney(unit * multiplier);
            }
            if (priceOld && !Number.isNaN(original)) {
                priceOld.textContent = formatMoney(original * multiplier);
            }
        }

        if (stock < 1) return;

        if (qty > 0) {
            addBtn?.classList.add('d-none');
            added?.classList.remove('d-none');
            if (qtyEl) qtyEl.textContent = String(qty);
            const plus = el.querySelector('[data-cart-plus]');
            if (plus) plus.disabled = qty >= Math.min(50, stock);
        } else {
            addBtn?.classList.remove('d-none');
            added?.classList.add('d-none');
            if (qtyEl) qtyEl.textContent = '0';
        }
    };

    const applySummary = (summary) => {
        renderDropdown(summary);
        syncControls(summary);
    };

    const cartRequest = async (url, method, body) => {
        const response = await fetch(url, {
            method,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify(body),
            credentials: 'same-origin',
        });

        if (!response.ok) {
            let message = 'Could not update cart.';
            try {
                const err = await response.json();
                message = err.message || Object.values(err.errors || {}).flat()[0] || message;
            } catch (_) {}
            throw new Error(message);
        }

        return response.json();
    };

    const linePayload = (el) => {
        const productId = el.dataset.productId;
        const giftBoxId = el.dataset.giftBoxId;
        if (giftBoxId) return { gift_box_id: parseInt(giftBoxId, 10) };
        return { product_id: parseInt(productId, 10) };
    };

    const withBusy = async (el, fn) => {
        if (el.dataset.busy === '1') return;
        el.dataset.busy = '1';
        try {
            await fn();
        } catch (e) {
            console.error(e);
        } finally {
            el.dataset.busy = '0';
        }
    };

    document.addEventListener('click', (e) => {
        const addBtn = e.target.closest('[data-cart-add]');
        if (addBtn) {
            e.preventDefault();
            const el = addBtn.closest('[data-cart-control]');
            if (!el) return;
            withBusy(el, async () => {
                const summary = await cartRequest(routes.store, 'POST', {
                    ...linePayload(el),
                    quantity: 1,
                });
                applySummary(summary);
            });
            return;
        }

        const plusBtn = e.target.closest('[data-cart-plus]');
        if (plusBtn) {
            e.preventDefault();
            const el = plusBtn.closest('[data-cart-control]');
            if (!el) return;
            const stock = parseInt(el.dataset.stock || '0', 10);
            const current = parseInt(el.dataset.quantity || '0', 10);
            const next = Math.min(50, stock, current + 1);
            if (next === current) return;
            withBusy(el, async () => {
                const summary = await cartRequest(routes.update, 'PATCH', {
                    ...linePayload(el),
                    quantity: next,
                });
                applySummary(summary);
            });
            return;
        }

        const minusBtn = e.target.closest('[data-cart-minus]');
        if (minusBtn) {
            e.preventDefault();
            const el = minusBtn.closest('[data-cart-control]');
            if (!el) return;
            const current = parseInt(el.dataset.quantity || '0', 10);
            const next = Math.max(0, current - 1);
            withBusy(el, async () => {
                const summary = await cartRequest(routes.update, 'PATCH', {
                    ...linePayload(el),
                    quantity: next,
                });
                applySummary(summary);
            });
        }
    });

    // Touch: tap cart toggles dropdown; link still navigates on second intent via View cart / badge area
    if (navCart && navCartLink && window.matchMedia('(hover: none)').matches) {
        navCartLink.addEventListener('click', (e) => {
            if (!navCart.classList.contains('is-open')) {
                e.preventDefault();
                navCart.classList.add('is-open');
            }
        });
        document.addEventListener('click', (e) => {
            if (!navCart.contains(e.target)) {
                navCart.classList.remove('is-open');
            }
        });
    }
})();
</script>
@stack('scripts')
</body>
</html>
