<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('cart.index') }}">Cart ({{ $cartCount ?? 0 }})</a>
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
@stack('scripts')
</body>
</html>
