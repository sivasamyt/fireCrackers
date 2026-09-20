@extends('layouts.admin')

@section('title', 'Gift Boxes')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <p class="mb-0 text-muted">Bundle multiple products into a gift box sold as one item</p>
    <a href="{{ route('admin.gift-boxes.create') }}" class="btn btn-dark">Add Gift Box</a>
</div>

<div class="card card-stat p-3">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead><tr><th></th><th>Name</th><th>Items</th><th>Price</th><th>Discount</th><th>Status</th><th></th></tr></thead>
            <tbody>
            @foreach($giftBoxes as $giftBox)
                <tr>
                    <td><img src="{{ $giftBox->image_url }}" alt="" style="width:56px;height:56px;object-fit:cover;border-radius:.4rem;"></td>
                    <td>{{ $giftBox->name }}</td>
                    <td>{{ $giftBox->products_count }}</td>
                    <td>₹{{ number_format($giftBox->price, 2) }}</td>
                    <td>{{ $giftBox->discount_percent }}%</td>
                    <td>{{ $giftBox->is_active ? 'Active' : 'Hidden' }}</td>
                    <td class="text-end">
                        <a href="{{ route('admin.gift-boxes.edit', $giftBox) }}">Edit</a>
                        <form action="{{ route('admin.gift-boxes.destroy', $giftBox) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete gift box?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-link text-danger p-0 ms-2">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $giftBoxes->links() }}</div>
</div>
@endsection
