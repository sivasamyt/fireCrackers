@extends('layouts.admin')

@section('title', 'Edit Category')

@section('content')
<div class="card card-stat p-4" style="max-width:640px">
    <form method="POST" action="{{ route('admin.categories.update', $category) }}" enctype="multipart/form-data">
        @csrf @method('PUT')
        <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" value="{{ old('name', $category->name) }}" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Image</label>
            @if($category->image_path)
                <div class="d-flex align-items-center gap-3 mb-2">
                    <img src="{{ $category->image_url }}" alt="{{ $category->name }}" class="rounded border" style="width:72px;height:72px;object-fit:cover">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remove_image" value="1" id="remove_image">
                        <label class="form-check-label" for="remove_image">Remove image</label>
                    </div>
                </div>
            @endif
            <input type="file" name="image" accept="image/*" class="form-control @error('image') is-invalid @enderror">
            @error('image')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', $category->is_active))>
            <label class="form-check-label" for="is_active">Active</label>
        </div>
        <button class="btn btn-dark">Update</button>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-link">Cancel</a>
    </form>
</div>
@endsection
