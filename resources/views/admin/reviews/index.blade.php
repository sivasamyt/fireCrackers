@extends('layouts.admin')

@section('title', 'Reviews')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <p class="mb-0 text-muted">Customer reviews shown at the bottom of the Home page</p>
    <a href="{{ route('admin.reviews.create') }}" class="btn btn-dark">Add Review</a>
</div>

<div class="card card-stat p-3">
    <table class="table mb-0">
        <thead><tr><th>Order</th><th>Name</th><th>Email</th><th>Stars</th><th>Description</th><th>Status</th><th></th></tr></thead>
        <tbody>
        @forelse($reviews as $review)
            <tr>
                <td>{{ $review->sort_order ?? '-' }}</td>
                <td>{{ $review->name }}</td>
                <td>{{ $review->email }}</td>
                <td class="text-warning text-nowrap" title="{{ $review->rating }} / 5">{{ str_repeat('★', $review->rating) }}<span class="text-muted">{{ str_repeat('★', 5 - $review->rating) }}</span></td>
                <td>{{ \Illuminate\Support\Str::limit($review->description, 80) }}</td>
                <td>{{ $review->is_active ? 'Active' : 'Hidden' }}</td>
                <td class="text-end text-nowrap">
                    <a href="{{ route('admin.reviews.edit', $review) }}">Edit</a>
                    <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete review?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-link text-danger p-0 ms-2">Delete</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="7" class="text-center text-muted py-4">No reviews yet.</td></tr>
        @endforelse
        </tbody>
    </table>
    <div class="mt-3">{{ $reviews->links() }}</div>
</div>
@endsection
