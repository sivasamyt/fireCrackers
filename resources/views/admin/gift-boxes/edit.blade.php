@extends('layouts.admin')

@section('title', 'Edit Gift Box')

@section('content')
<div class="card card-stat p-4">
    <form method="POST" action="{{ route('admin.gift-boxes.update', $giftBox) }}" enctype="multipart/form-data">
        @csrf @method('PUT')
        @include('admin.gift-boxes._form')
        <button class="btn btn-dark">Update Gift Box</button>
        <a href="{{ route('admin.gift-boxes.index') }}" class="btn btn-link">Cancel</a>
    </form>
</div>
@endsection
