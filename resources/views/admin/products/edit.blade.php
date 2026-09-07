@extends('layouts.admin')

@section('title', 'Edit Product')

@section('content')
<div class="card card-stat p-4" style="max-width:920px">
    <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
        @csrf @method('PUT')
        @include('admin.products._form', ['product' => $product])
        <button class="btn btn-dark">Update Product</button>
        <a href="{{ route('admin.products.index') }}" class="btn btn-link">Cancel</a>
    </form>
</div>
@endsection
