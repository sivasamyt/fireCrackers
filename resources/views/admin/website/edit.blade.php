@extends('layouts.admin')

@section('title', 'Website')

@section('content')
<style>
    .theme-option { cursor: pointer; display: block; }
    .theme-option input { position: absolute; opacity: 0; pointer-events: none; }
    .theme-option .theme-card {
        border: 2px solid #e6e0d4;
        border-radius: .75rem;
        padding: .75rem;
        transition: border-color .15s ease, box-shadow .15s ease;
    }
    .theme-option input:checked + .theme-card {
        border-color: #f5c451;
        box-shadow: 0 0 0 .2rem rgba(245,196,81,.25);
    }
    .theme-preview {
        height: 110px;
        border-radius: .5rem;
        padding: .6rem;
        display: flex;
        flex-direction: column;
        gap: .4rem;
    }
    .theme-preview .bar { height: 12px; border-radius: 4px; }
    .theme-preview .tile { flex: 1; border-radius: 6px; }
    .theme-preview-dark { background: linear-gradient(180deg, #05070f 0%, #10182b 100%); }
    .theme-preview-dark .bar { background: rgba(245,196,81,.6); width: 45%; }
    .theme-preview-dark .tile { background: rgba(16,24,43,.9); border: 1px solid rgba(245,196,81,.25); }
    .theme-preview-light { background: linear-gradient(180deg, #fffaf0 0%, #f7f1e3 100%); border: 1px solid #ece4d2; }
    .theme-preview-light .bar { background: #c98a00; width: 45%; }
    .theme-preview-light .tile { background: #fff; border: 1px solid rgba(196,140,20,.3); }
</style>

<div class="card card-stat p-4" style="max-width:720px">
    <h2 class="h5 mb-1">Theme</h2>
    <p class="text-muted small mb-4">Choose how the storefront looks for every visitor. The admin panel is not affected.</p>

    <form method="POST" action="{{ route('admin.website.update') }}" enctype="multipart/form-data">
        @csrf @method('PUT')

        <div class="row g-3 mb-4">
            @foreach(['dark' => 'Dark', 'light' => 'Light'] as $value => $label)
                <div class="col-sm-6">
                    <label class="theme-option">
                        <input type="radio" name="theme" value="{{ $value }}" @checked(old('theme', $theme) === $value)>
                        <div class="theme-card">
                            <div class="theme-preview theme-preview-{{ $value }}">
                                <div class="bar"></div>
                                <div class="d-flex gap-2 flex-grow-1">
                                    <div class="tile"></div>
                                    <div class="tile"></div>
                                    <div class="tile"></div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <span class="fw-semibold">{{ $label }}</span>
                                @if($theme === $value)
                                    <span class="badge text-bg-warning">Current</span>
                                @endif
                            </div>
                        </div>
                    </label>
                </div>
            @endforeach
        </div>

        @error('theme')
            <div class="text-danger small mb-3">{{ $message }}</div>
        @enderror

        <h2 class="h5 mb-1 mt-2">Logo</h2>
        <p class="text-muted small mb-3">Shown in the store header and on the "Show All" category tile.</p>
        @if($logoUrl)
            <div class="d-flex align-items-center gap-3 mb-2">
                <img src="{{ $logoUrl }}" alt="Logo" class="rounded border" style="width:72px;height:72px;object-fit:contain">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remove_logo" value="1" id="remove_logo">
                    <label class="form-check-label" for="remove_logo">Remove logo</label>
                </div>
            </div>
        @endif
        <input type="file" name="logo" accept="image/*" class="form-control mb-1 @error('logo') is-invalid @enderror">
        @error('logo')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <div class="mb-4"></div>

        <button class="btn btn-dark">Save</button>
        <a href="{{ route('home') }}" target="_blank" class="btn btn-link">View Store</a>
    </form>
</div>
@endsection
