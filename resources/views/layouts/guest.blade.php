@php($theme = \App\Models\Setting::theme())
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="{{ $theme }}" data-bs-theme="{{ $theme }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="robots" content="noindex,nofollow">

        <title>{{ config('app.name', 'Laravel') }}</title>
        @include('layouts.partials.favicon')

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <link href="{{ asset('css/theme.css') }}?v={{ @filemtime(public_path('css/theme.css')) }}" rel="stylesheet">
    </head>
    <body class="antialiased">
        <div class="auth-shell">
            <a href="{{ route('home') }}" class="auth-brand">Royal Crackers</a>

            <div class="auth-card">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
