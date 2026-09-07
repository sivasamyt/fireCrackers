@extends('layouts.admin')

@section('title', 'Products')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <p class="mb-0 text-muted">Upload and manage firecracker products</p>
    <a href="{{ route('admin.products.create') }}" class="btn btn-dark">Add Product</a>
</div>
<div class="card card-stat p-3">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead><tr><th></th><th>Name</th><th>Category</th><th>Price</th><th>Discount</th><th>Stock</th><th></th></tr></thead>
            <tbody>
            @foreach($products as $product)
                <tr>
                    <td><img src="{{ $product->image_url }}" alt="" style="width:56px;height:56px;object-fit:cover;border-radius:.4rem;"></td>
                    <td>{{ $product->name }}</td>
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
