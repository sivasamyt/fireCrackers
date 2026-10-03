@extends('layouts.admin')

@section('title', 'Order '.$order->order_number)

@php
    $editable = $order->status !== 'cancelled';
    $paymentColors = [
        'pending' => 'bg-warning-subtle text-warning-emphasis',
        'paid' => 'bg-success-subtle text-success-emphasis',
        'failed' => 'bg-danger-subtle text-danger-emphasis',
    ];
    $currentPaymentStatus = old('payment_status', $order->payment_status);
    $savedItems = $order->items->map(fn ($item) => [
        'item_id' => $item->id,
        'product_id' => $item->product_id,
        'name' => $item->product_name,
        'price' => (float) $item->unit_price,
        'discount' => (int) $item->discount_percent,
        'quantity' => (int) $item->quantity,
    ])->values();
    $catalog = $products->map(fn ($product) => [
        'id' => $product->id,
        'name' => $product->name,
        'price' => (float) $product->price,
        'discount' => (int) $product->discount_percent,
        'stock' => (int) $product->stock,
    ])->values();
@endphp

@section('content')
<form method="POST" action="{{ route('admin.orders.update', $order) }}" id="orderForm">
    @csrf @method('PUT')
    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card card-stat p-3 mb-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h2 class="h5 mb-0">
                        Items
                        <span class="badge text-bg-warning ms-2 d-none" id="unsavedBadge">Unsaved changes</span>
                    </h2>
                    @if($editable)
                        <button type="button" class="btn btn-dark btn-sm" data-bs-toggle="modal" data-bs-target="#customizeItemsModal">Customize items</button>
                    @endif
                </div>
                <table class="table">
                    <thead><tr><th>Product</th><th>Qty</th><th>Discount</th><th>Line total</th></tr></thead>
                    <tbody id="itemsPreview">
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
                <div class="fw-bold">Grand total: <span id="grandTotal">₹{{ number_format($order->grand_total, 2) }}</span></div>
                <div id="itemsInputs"></div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card card-stat p-3 mb-3">
                <h2 class="h5">Customer</h2>
                <p class="mb-1">{{ $order->customerName() }}</p>
                <p class="mb-1">{{ $order->customerEmail() ?? '—' }}</p>
                <p class="mb-1">{{ $order->customerPhone() }}</p>
                <p class="mb-0">{{ $order->fullAddress() }}</p>
            </div>
            <div class="card card-stat p-3">
                <h2 class="h5">Order</h2>
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <p class="mb-2 text-uppercase small text-muted">Method: {{ $order->payment_method }}</p>
                @if($order->transaction_id)
                    <p class="mb-2 small">Transaction ID: <strong>{{ $order->transaction_id }}</strong></p>
                @endif
                <label class="form-label" for="payment_status">Payment status</label>
                <select name="payment_status" id="payment_status" class="form-select fw-semibold mb-3 {{ $paymentColors[$currentPaymentStatus] ?? '' }}" data-saved="{{ $order->payment_status }}">
                    @foreach(['pending', 'paid', 'failed'] as $paymentStatus)
                        <option value="{{ $paymentStatus }}" class="{{ $paymentColors[$paymentStatus] }}" @selected($currentPaymentStatus === $paymentStatus)>{{ ucfirst($paymentStatus) }}</option>
                    @endforeach
                </select>
                <label class="form-label" for="status">Order status</label>
                <select name="status" id="status" class="form-select mb-3" data-saved="{{ $order->status }}">
                    @foreach(['pending', 'confirmed', 'shipped', 'delivered', 'cancelled'] as $status)
                        <option value="{{ $status }}" @selected(old('status', $order->status) === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-dark flex-fill">Save</button>
                    <button type="button" class="btn btn-outline-secondary flex-fill" id="cancelChanges">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</form>

@if($editable)
    <div class="modal fade" id="customizeItemsModal" tabindex="-1" aria-labelledby="customizeItemsTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title h5" id="customizeItemsTitle">Customize items</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table align-middle">
                        <thead><tr><th>Product</th><th style="width: 110px;">Qty</th><th>Discount</th><th>Line total</th><th></th></tr></thead>
                        <tbody id="modalItems"></tbody>
                    </table>
                    <div class="row g-2 align-items-end">
                        <div class="col-md-7">
                            <label class="form-label" for="addProduct">Add product</label>
                            <select id="addProduct" class="form-select">
                                <option value="">Select product…</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" @disabled($product->stock < 1)>
                                        {{ $product->name }} (stock: {{ $product->stock }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="addQuantity">Qty</label>
                            <input type="number" id="addQuantity" class="form-control" value="1" min="1" max="50">
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-outline-dark w-100" id="addProductBtn">Add</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <div class="fw-bold">Estimated total: <span id="modalTotal"></span></div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-dark" id="applyItems">Apply</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

<script>
(() => {
    const MAX_QTY = 50;
    const editable = @json($editable);
    const savedItems = @json($savedItems);
    const catalog = @json($catalog);
    const oldItems = @json(old('items'));

    const form = document.getElementById('orderForm');
    const badge = document.getElementById('unsavedBadge');
    const paymentSelect = document.getElementById('payment_status');
    const statusSelects = [paymentSelect, document.getElementById('status')];
    const paymentColors = @json($paymentColors);

    const applyPaymentColor = () => {
        Object.values(paymentColors).forEach((classes) => paymentSelect.classList.remove(...classes.split(' ')));
        const classes = paymentColors[paymentSelect.value];
        if (classes) paymentSelect.classList.add(...classes.split(' '));
    };

    const clone = (items) => items.map((item) => ({ ...item }));
    const money = (value) => '₹' + value.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    const lineTotal = (item) => {
        const discount = Math.max(0, Math.min(100, item.discount));
        return Math.round(item.price * (1 - discount / 100) * item.quantity * 100) / 100;
    };
    const total = (items) => items.reduce((sum, item) => sum + lineTotal(item), 0);
    const escapeHtml = (text) => String(text).replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
    const clampQty = (value) => Math.max(1, Math.min(MAX_QTY, parseInt(value, 10) || 1));
    const signature = (items) => JSON.stringify(items.map((i) => [i.item_id ?? null, i.product_id ?? null, i.quantity]));

    const fromOldInput = (rows) => rows.map((row) => {
        if (row.item_id) {
            const saved = savedItems.find((i) => String(i.item_id) === String(row.item_id));
            return saved ? { ...saved, quantity: clampQty(row.quantity) } : null;
        }
        const product = catalog.find((p) => String(p.id) === String(row.product_id));
        return product ? { item_id: null, product_id: product.id, name: product.name, price: product.price, discount: product.discount, quantity: clampQty(row.quantity) } : null;
    }).filter(Boolean);

    let draft = editable && Array.isArray(oldItems) ? fromOldInput(oldItems) : clone(savedItems);
    let modalDraft = [];

    const isDirty = () => signature(draft) !== signature(savedItems)
        || statusSelects.some((select) => select.value !== select.dataset.saved);

    const refreshDirty = () => badge.classList.toggle('d-none', !isDirty());

    const renderPage = () => {
        document.getElementById('itemsPreview').innerHTML = draft.map((item) => `
            <tr>
                <td>${escapeHtml(item.name)}</td>
                <td>${item.quantity}</td>
                <td>${item.discount}%</td>
                <td>${money(lineTotal(item))}</td>
            </tr>`).join('');
        document.getElementById('grandTotal').textContent = money(total(draft));

        if (editable) {
            document.getElementById('itemsInputs').innerHTML = draft.map((item, i) => `
                ${item.item_id
                    ? `<input type="hidden" name="items[${i}][item_id]" value="${item.item_id}">`
                    : `<input type="hidden" name="items[${i}][product_id]" value="${item.product_id}">`}
                <input type="hidden" name="items[${i}][quantity]" value="${item.quantity}">`).join('');
        }

        refreshDirty();
    };

    statusSelects.forEach((select) => select.addEventListener('change', refreshDirty));
    paymentSelect.addEventListener('change', applyPaymentColor);

    document.getElementById('cancelChanges').addEventListener('click', () => {
        draft = clone(savedItems);
        statusSelects.forEach((select) => { select.value = select.dataset.saved; });
        applyPaymentColor();
        renderPage();
    });

    let submitting = false;
    form.addEventListener('submit', () => { submitting = true; });
    window.addEventListener('beforeunload', (event) => {
        if (!submitting && isDirty()) {
            event.preventDefault();
            event.returnValue = '';
        }
    });

    if (editable) {
        const modalEl = document.getElementById('customizeItemsModal');
        const modalItems = document.getElementById('modalItems');
        const addSelect = document.getElementById('addProduct');
        const addQty = document.getElementById('addQuantity');

        const renderModalTotal = () => {
            document.getElementById('modalTotal').textContent = money(total(modalDraft));
        };

        const renderModal = () => {
            const single = modalDraft.length <= 1;
            modalItems.innerHTML = modalDraft.map((item, i) => `
                <tr>
                    <td>${escapeHtml(item.name)}${item.item_id ? '' : ' <span class="badge text-bg-secondary">new</span>'}</td>
                    <td><input type="number" class="form-control form-control-sm" min="1" max="${MAX_QTY}" value="${item.quantity}" data-index="${i}" aria-label="Quantity for ${escapeHtml(item.name)}"></td>
                    <td>${item.discount}%</td>
                    <td data-line="${i}">${money(lineTotal(item))}</td>
                    <td class="text-end">
                        <button type="button" class="btn btn-sm btn-outline-danger" data-remove="${i}" ${single ? 'disabled title="An order must have at least one item"' : 'title="Remove item"'}>&times;</button>
                    </td>
                </tr>`).join('');
            renderModalTotal();
        };

        modalEl.addEventListener('show.bs.modal', () => {
            modalDraft = clone(draft);
            addSelect.value = '';
            addQty.value = 1;
            renderModal();
        });

        modalItems.addEventListener('input', (event) => {
            const index = event.target.dataset.index;
            if (index === undefined) return;
            const item = modalDraft[index];
            item.quantity = clampQty(event.target.value);
            modalItems.querySelector(`[data-line="${index}"]`).textContent = money(lineTotal(item));
            renderModalTotal();
        });

        modalItems.addEventListener('change', (event) => {
            const index = event.target.dataset.index;
            if (index !== undefined) event.target.value = modalDraft[index].quantity;
        });

        modalItems.addEventListener('click', (event) => {
            const button = event.target.closest('[data-remove]');
            if (!button || modalDraft.length <= 1) return;
            modalDraft.splice(Number(button.dataset.remove), 1);
            renderModal();
        });

        document.getElementById('addProductBtn').addEventListener('click', () => {
            const product = catalog.find((p) => String(p.id) === addSelect.value);
            if (!product) return;
            const qty = clampQty(addQty.value);
            const existing = modalDraft.find((item) => item.product_id === product.id);
            if (existing) {
                existing.quantity = Math.min(MAX_QTY, existing.quantity + qty);
            } else {
                modalDraft.push({ item_id: null, product_id: product.id, name: product.name, price: product.price, discount: product.discount, quantity: qty });
            }
            addSelect.value = '';
            addQty.value = 1;
            renderModal();
        });

        document.getElementById('applyItems').addEventListener('click', () => {
            draft = clone(modalDraft);
            renderPage();
            bootstrap.Modal.getOrCreateInstance(modalEl).hide();
        });
    }

    applyPaymentColor();
    renderPage();
})();
</script>
@endsection
