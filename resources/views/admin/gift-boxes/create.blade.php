@extends('layouts.admin')

@section('title', 'Add Gift Box')

@section('content')
<div class="card card-stat p-4">
    <form method="POST" action="{{ route('admin.gift-boxes.store') }}" enctype="multipart/form-data">
        @csrf
        @include('admin.gift-boxes._form')
        <button class="btn btn-dark">Save Gift Box</button>
        <a href="{{ route('admin.gift-boxes.index') }}" class="btn btn-link">Cancel</a>
    </form>
</div>
@endsection
