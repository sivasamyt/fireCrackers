<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') — FireCrackers</title>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; background: #f3f0ea; }
        .brand-font { font-family: 'Bebas Neue', sans-serif; letter-spacing: .04em; }
        .sidebar {
            min-height: 100vh;
            background: #10182b;
            color: #f7f1e3;
        }
        .sidebar a { color: #d7d2c6; text-decoration: none; display: block; padding: .65rem 1rem; border-radius: .5rem; }
        .sidebar a:hover, .sidebar a.active { background: rgba(245,196,81,.15); color: #f5c451; }
        .topbar { background: #fff; border-bottom: 1px solid #e6e0d4; }
        .card-stat { border: 0; box-shadow: 0 8px 24px rgba(16,24,43,.06); }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <aside class="col-md-3 col-lg-2 sidebar p-3">
            <div class="brand-font fs-3 text-warning mb-4">FC Admin</div>
            <nav class="d-grid gap-1">
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard</a>
                <a href="{{ route('admin.categories.index') }}" class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">Categories</a>
                <a href="{{ route('admin.products.index') }}" class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}">Products</a>
                <a href="{{ route('admin.orders.index') }}" class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">Orders</a>
                <a href="{{ route('home') }}">View Store</a>
            </nav>
            <form method="POST" action="{{ route('logout') }}" class="mt-4">@csrf
                <button class="btn btn-outline-warning btn-sm w-100">Logout</button>
            </form>
        </aside>
        <main class="col-md-9 col-lg-10 p-0">
            <div class="topbar px-4 py-3 d-flex justify-content-between align-items-center">
                <h1 class="h4 mb-0">@yield('title', 'Dashboard')</h1>
                <span class="text-muted">{{ auth()->user()->name }}</span>
            </div>
            <div class="p-4">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                @yield('content')
            </div>
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
