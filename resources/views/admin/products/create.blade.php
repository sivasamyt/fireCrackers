@extends('layouts.admin')

@section('title', 'Add Product')

@section('content')
<div class="card card-stat p-4" style="max-width:760px">
    <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
        @csrf
        @include('admin.products._form')
        <button class="btn btn-dark">Save Product</button>
        <a href="{{ route('admin.products.index') }}" class="btn btn-link">Cancel</a>
    </form>
</div>
@endsection
