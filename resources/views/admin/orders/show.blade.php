@extends('layouts.admin')

@section('title', 'Order '.$order->order_number)

@section('content')
<div class="row g-3">
    <div class="col-lg-8">
        <div class="card card-stat p-3 mb-3">
            <h2 class="h5">Items</h2>
            <table class="table">
                <thead><tr><th>Product</th><th>Qty</th><th>Discount</th><th>Line total</th></tr></thead>
                <tbody>
                @foreach($order->items as $item)
                    <tr>
                        <td>{{ $item->product_name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ $item->discount_percent }}%</td>
                        <td>₹{{ number_format($item->line_total, 2) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="fw-bold">Grand total: ₹{{ number_format($order->grand_total, 2) }}</div>
        </div>

        @if($order->status !== 'cancelled')
            <div class="card card-stat p-3 mb-3">
                <h2 class="h5">Add product</h2>
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form method="POST" action="{{ route('admin.orders.items.store', $order) }}" class="row g-3 align-items-end">
                    @csrf
                    <div class="col-md-7">
                        <label class="form-label" for="product_id">Product</label>
                        <select name="product_id" id="product_id" class="form-select" required>
                            <option value="">Select product…</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" @selected(old('product_id') == $product->id) @disabled($product->stock < 1)>
                                    {{ $product->name }} (stock: {{ $product->stock }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="quantity">Qty</label>
                        <input type="number" name="quantity" id="quantity" class="form-control" value="{{ old('quantity', 1) }}" min="1" max="50" required>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-dark w-100">Add</button>
                    </div>
                </form>
            </div>
        @endif
    </div>
    <div class="col-lg-4">
        <div class="card card-stat p-3 mb-3">
            <h2 class="h5">Customer</h2>
            <p class="mb-1">{{ $order->customerName() }}</p>
            <p class="mb-1">{{ $order->customerEmail() ?? '—' }}</p>
            <p class="mb-1">{{ $order->customerPhone() }}</p>
            <p class="mb-0">{{ $order->fullAddress() }}</p>
        </div>
        <div class="card card-stat p-3 mb-3">
            <h2 class="h5">Payment status</h2>
            <p class="mb-2 text-uppercase small text-muted">Method: {{ $order->payment_method }}</p>
            <form method="POST" action="{{ route('admin.orders.payment-status', $order) }}">
                @csrf @method('PATCH')
                <select name="payment_status" class="form-select mb-3">
                    @foreach(['pending', 'paid', 'failed'] as $paymentStatus)
                        <option value="{{ $paymentStatus }}" @selected($order->payment_status === $paymentStatus)>{{ ucfirst($paymentStatus) }}</option>
                    @endforeach
                </select>
                <button class="btn btn-dark w-100">Save payment status</button>
            </form>
        </div>
        <div class="card card-stat p-3">
            <h2 class="h5">Update status</h2>
            <form method="POST" action="{{ route('admin.orders.status', $order) }}">
                @csrf @method('PATCH')
                <select name="status" class="form-select mb-3">
                    @foreach(['pending','confirmed','shipped','delivered','cancelled'] as $status)
                        <option value="{{ $status }}" @selected($order->status === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
                <button class="btn btn-dark w-100">Save status</button>
            </form>
        </div>
    </div>
</div>
@endsection
