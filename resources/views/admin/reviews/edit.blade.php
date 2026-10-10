@extends('layouts.admin')

@section('title', 'Edit Review')

@section('content')
<div class="card card-stat p-4" style="max-width:640px">
    <form method="POST" action="{{ route('admin.reviews.update', $review) }}">
        @csrf @method('PUT')
        @include('admin.reviews._form')
        <button class="btn btn-dark">Update</button>
        <a href="{{ route('admin.reviews.index') }}" class="btn btn-link">Cancel</a>
    </form>
</div>
@endsection
