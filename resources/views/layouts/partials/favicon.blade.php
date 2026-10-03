@php($faviconVersion = @filemtime(public_path('favicon.ico')))
<link rel="icon" href="{{ asset('favicon.ico') }}?v={{ $faviconVersion }}" sizes="16x16 32x32 48x48">
<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16.png') }}?v={{ $faviconVersion }}">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32.png') }}?v={{ $faviconVersion }}">
<link rel="icon" type="image/png" sizes="48x48" href="{{ asset('images/favicon-48.png') }}?v={{ $faviconVersion }}">
<link rel="icon" type="image/png" sizes="512x512" href="{{ asset('images/favicon-512.png') }}?v={{ $faviconVersion }}">
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/favicon-180.png') }}?v={{ $faviconVersion }}">
