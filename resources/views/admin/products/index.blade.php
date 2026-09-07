@extends('layouts.admin')

@section('title', 'Products')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <p class="mb-0 text-muted">Upload and manage firecracker products</p>
    <a href="{{ route('admin.products.create') }}" class="btn btn-dark">Add Product</a>
</div>

<div class="card card-stat p-3 mb-3">
    <div class="d-flex flex-wrap align-items-end gap-3 justify-content-between">
        <div>
            <h2 class="h6 mb-1">Import from Excel</h2>
            <p class="text-muted small mb-0">
                Columns: <code>category_name</code>, <code>name</code>, <code>description</code>,
                <code>price</code>, <code>discount_percent</code>, <code>stock</code>, <code>is_active</code>
            </p>
            <p class="text-muted small mb-0">Category name must already exist. Images and combo items are set after import.</p>
        </div>
        <a href="{{ route('admin.products.sample') }}" class="btn btn-outline-dark">Download sample</a>
    </div>
    <form method="POST" action="{{ route('admin.products.import') }}" enctype="multipart/form-data" class="row g-2 align-items-end mt-2">
        @csrf
        <div class="col-md-8">
            <label class="form-label" for="product-import-file">Excel file</label>
            <input id="product-import-file" type="file" name="file" class="form-control @error('file') is-invalid @enderror" accept=".xlsx,.xls,.csv" required>
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
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead><tr><th></th><th>Name</th><th>Category</th><th>Price</th><th>Discount</th><th>Stock</th><th></th></tr></thead>
            <tbody>
            @foreach($products as $product)
                <tr>
                    <td><img src="{{ $product->image_url }}" alt="" style="width:56px;height:56px;object-fit:cover;border-radius:.4rem;"></td>
                    <td>
                        {{ $product->name }}
                        @if($product->category?->slug === \App\Models\Product::COMBO_CATEGORY_SLUG)
                            <span class="badge text-bg-warning">Combo</span>
                        @endif
                    </td>
                    <td>{{ $product->category?->name }}</td>
                    <td>₹{{ number_format($product->price, 2) }}</td>
                    <td>{{ $product->discount_percent }}%</td>
                    <td>{{ $product->stock }}</td>
                    <td class="text-end">
                        <a href="{{ route('admin.products.edit', $product) }}">Edit</a>
                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete product?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-link text-danger p-0 ms-2">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $products->links() }}</div>
</div>
@endsection
