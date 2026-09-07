@extends('layouts.admin')

@section('title', 'Categories')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <p class="mb-0 text-muted">Manage firecracker categories</p>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-dark">Add Category</a>
</div>

<div class="card card-stat p-3 mb-3">
    <div class="d-flex flex-wrap align-items-end gap-3 justify-content-between">
        <div>
            <h2 class="h6 mb-1">Import from Excel</h2>
            <p class="text-muted small mb-0">Columns: <code>name</code>, <code>is_active</code> (optional)</p>
        </div>
        <a href="{{ route('admin.categories.sample') }}" class="btn btn-outline-dark">Download sample</a>
    </div>
    <form method="POST" action="{{ route('admin.categories.import') }}" enctype="multipart/form-data" class="row g-2 align-items-end mt-2">
        @csrf
        <div class="col-md-8">
            <label class="form-label" for="category-import-file">Excel file</label>
            <input id="category-import-file" type="file" name="file" class="form-control @error('file') is-invalid @enderror" accept=".xlsx,.xls,.csv" required>
            @error('file')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-md-4">
            <button type="submit" class="btn btn-dark w-100">Upload</button>
        </div>
    </form>
</div>

<div class="card card-stat p-3">
    <table class="table mb-0">
        <thead><tr><th>Name</th><th>Slug</th><th>Products</th><th>Status</th><th></th></tr></thead>
        <tbody>
        @foreach($categories as $category)
            <tr>
                <td>{{ $category->name }}</td>
                <td>{{ $category->slug }}</td>
                <td>{{ $category->products_count }}</td>
                <td>{{ $category->is_active ? 'Active' : 'Hidden' }}</td>
                <td class="text-end">
                    <a href="{{ route('admin.categories.edit', $category) }}">Edit</a>
                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete category?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-link text-danger p-0 ms-2">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="mt-3">{{ $categories->links() }}</div>
</div>
@endsection
