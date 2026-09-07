@extends('layouts.admin')

@section('title', 'Categories')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <p class="mb-0 text-muted">Manage firecracker categories</p>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-dark">Add Category</a>
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
