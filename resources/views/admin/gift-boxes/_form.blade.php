@php($giftBox = $giftBox ?? null)
@php($selectedProducts = old('product_ids', $giftBox?->products?->pluck('id')->all() ?? []))
@php($productQtys = old('product_qty', $giftBox?->products?->mapWithKeys(fn ($p) => [$p->id => $p->pivot->quantity])->all() ?? []))

<div class="row g-3 mb-3">
    <div class="col-md-8">
        <label class="form-label">Name</label>
        <input type="text" name="name" value="{{ old('name', $giftBox?->name) }}" class="form-control" required>
    </div>
    <div class="col-md-4 d-flex align-items-end">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', $giftBox?->is_active ?? true))>
            <label class="form-check-label" for="is_active">Active</label>
        </div>
    </div>
    <div class="col-12">
        <label class="form-label">Description</label>
        <textarea name="description" rows="4" class="form-control">{{ old('description', $giftBox?->description) }}</textarea>
    </div>
    <div class="col-md-4">
        <label class="form-label">Price (₹)</label>
        <input type="number" step="0.01" min="0" name="price" value="{{ old('price', $giftBox?->price) }}" class="form-control" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Discount %</label>
        <input type="number" min="0" max="100" name="discount_percent" value="{{ old('discount_percent', $giftBox?->discount_percent ?? 0) }}" class="form-control">
    </div>
    <div class="col-md-4">
        <label class="form-label">Image</label>
        <input type="file" name="image" accept="image/*" class="form-control">
        @if($giftBox?->image_path)
            <div class="mt-2"><img src="{{ $giftBox->image_url }}" alt="" style="height:80px;border-radius:.4rem;"></div>
        @endif
    </div>

    <div class="col-12">
        <label class="form-label fw-semibold">Gift box includes (select at least 2 products)</label>
        <div class="border rounded p-3 bg-light">
            @forelse($availableProducts as $product)
                @php($checked = in_array($product->id, $selectedProducts, false) || in_array((string) $product->id, $selectedProducts, true))
                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                    <div class="form-check mb-0 flex-grow-1">
                        <input class="form-check-input" type="checkbox"
                               name="product_ids[]" value="{{ $product->id }}"
                               id="gift_product_{{ $product->id }}"
                               @checked($checked)>
                        <label class="form-check-label" for="gift_product_{{ $product->id }}">
                            {{ $product->name }}
                            <span class="text-muted small">({{ $product->category?->name }} · ₹{{ number_format($product->price, 2) }})</span>
                        </label>
                    </div>
                    <div class="input-group input-group-sm" style="width:120px">
                        <span class="input-group-text">Qty</span>
                        <input type="number" min="1" max="50" class="form-control"
                               name="product_qty[{{ $product->id }}]"
                               value="{{ $productQtys[$product->id] ?? 1 }}">
                    </div>
                </div>
            @empty
                <p class="mb-0 text-muted">No products available. Create products first.</p>
            @endforelse
        </div>
    </div>
</div>

@if($errors->any())
    <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif
