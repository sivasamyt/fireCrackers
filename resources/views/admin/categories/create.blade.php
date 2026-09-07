@extends('layouts.admin')

@section('title', 'Add Category')

@section('content')
<div class="card card-stat p-4" style="max-width:640px">
    <form method="POST" action="{{ route('admin.categories.store') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" value="{{ old('name') }}" class="form-control" required>
        </div>
        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" checked>
            <label class="form-check-label" for="is_active">Active</label>
        </div>
        <button class="btn btn-dark">Save</button>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-link">Cancel</a>
    </form>
</div>
@endsection
