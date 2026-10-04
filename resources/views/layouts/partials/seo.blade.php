@php
    $seoTitle = $__env->yieldContent('title', config('seo.default_title'));
    $seoDescription = $__env->yieldContent('meta_description', config('seo.default_description'));
    $seoRobots = $__env->yieldContent('robots', 'index,follow');
    $seoCanonical = $__env->yieldContent('canonical', request()->is('/') ? url('/').'/' : url()->current());
    $seoType = $__env->yieldContent('og_type', 'website');
    $seoImage = $__env->yieldContent('og_image', \App\Models\Setting::brandImageUrl());
@endphp
<title>{!! $seoTitle !!}</title>
<meta name="description" content="{!! $seoDescription !!}">
<meta name="robots" content="{!! $seoRobots !!}">
@unless(str_contains($seoRobots, 'noindex'))
<link rel="canonical" href="{!! $seoCanonical !!}">
@endunless
<meta property="og:site_name" content="{{ config('seo.site_name') }}">
<meta property="og:title" content="{!! $seoTitle !!}">
<meta property="og:description" content="{!! $seoDescription !!}">
<meta property="og:url" content="{!! $seoCanonical !!}">
<meta property="og:type" content="{!! $seoType !!}">
<meta property="og:image" content="{!! $seoImage !!}">
<meta name="twitter:card" content="summary">
<meta name="twitter:title" content="{!! $seoTitle !!}">
<meta name="twitter:description" content="{!! $seoDescription !!}">
<meta name="twitter:image" content="{!! $seoImage !!}">
@stack('structured-data')
