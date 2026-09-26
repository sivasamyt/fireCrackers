@extends('layouts.admin')

@section('title', 'Orders')

@section('content')
@php
    $filters = $filters ?? [];
    $hasFilters = collect($filters)->filter(fn ($v) => filled($v))->isNotEmpty();
@endphp
<div class="card card-stat p-3 mb-3">
    <form method="GET" action="{{ route('admin.orders.index') }}" id="orders-filter-form" class="row g-3 align-items-end">
        <div class="col-md-3">
            <label class="form-label" for="order_number">Order number</label>
            <input type="text" name="order_number" id="order_number" class="form-control" value="{{ $filters['order_number'] ?? '' }}" placeholder="FC-…" autocomplete="off">
        </div>
        <div class="col-md-3">
            <label class="form-label" for="customer">Customer</label>
            <input type="text" name="customer" id="customer" class="form-control" value="{{ $filters['customer'] ?? '' }}" placeholder="Name" autocomplete="off">
        </div>
        <div class="col-md-2">
            <label class="form-label" for="payment_status">Payment status</label>
            <select name="payment_status" id="payment_status" class="form-select">
                <option value="">All</option>
                @foreach(['pending', 'paid', 'failed'] as $paymentStatus)
                    <option value="{{ $paymentStatus }}" @selected(($filters['payment_status'] ?? '') === $paymentStatus)>{{ ucfirst($paymentStatus) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label" for="status">Status</label>
            <select name="status" id="status" class="form-select">
                <option value="">All</option>
                @foreach(['pending', 'confirmed', 'shipped', 'delivered', 'cancelled'] as $status)
                    <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
        </div>
        @if($hasFilters)
            <div class="col-md-2">
                <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary w-100">Clear</a>
            </div>
        @endif
    </form>
</div>

<div class="card card-stat p-3">
    <table class="table mb-0">
        <thead><tr><th>Order</th><th>Customer</th><th>Payment</th><th>Total</th><th>Status</th><th></th></tr></thead>
        <tbody>
        @forelse($orders as $order)
            @php
                $rowClass = match ($order->payment_status) {
                    'pending' => 'table-warning',
                    'failed' => 'table-danger',
                    'paid' => 'table-success',
                    default => '',
                };
            @endphp
            <tr class="{{ $rowClass }}">
                <td>{{ $order->order_number }}</td>
                <td>{{ $order->customerName() }}</td>
                <td class="text-uppercase">{{ $order->payment_method }} / {{ $order->payment_status }}</td>
                <td>₹{{ number_format($order->grand_total, 2) }}</td>
                <td>{{ ucfirst($order->status) }}</td>
                <td><a href="{{ route('admin.orders.show', $order) }}">View</a></td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-muted">No orders match these filters.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
    <div class="mt-3">{{ $orders->links() }}</div>
</div>

<script>
(() => {
    const form = document.getElementById('orders-filter-form');
    if (!form) return;

    const focusKey = 'adminOrdersFilterFocus';
    let timer = null;

    const submitForm = (fieldName) => {
        if (fieldName) {
            sessionStorage.setItem(focusKey, fieldName);
        }
        form.submit();
    };

    form.querySelectorAll('select').forEach((select) => {
        select.addEventListener('change', () => submitForm(select.name));
    });

    form.querySelectorAll('input[type="text"]').forEach((input) => {
        input.addEventListener('input', () => {
            clearTimeout(timer);
            timer = setTimeout(() => submitForm(input.name), 400);
        });
    });

    const lastFocus = sessionStorage.getItem(focusKey);
    if (lastFocus) {
        sessionStorage.removeItem(focusKey);
        const el = form.querySelector(`[name="${lastFocus}"]`);
        if (el) {
            el.focus();
            if (typeof el.setSelectionRange === 'function' && el.value) {
                const len = el.value.length;
                el.setSelectionRange(len, len);
            }
        }
    }
})();
</script>
@endsection
