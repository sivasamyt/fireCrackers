@php($product = $product ?? null)
@php($selectedComponents = old('component_ids', $product?->components?->pluck('id')->all() ?? []))
@php($componentQtys = old('component_qty', $product?->components?->mapWithKeys(fn ($c) => [$c->id => $c->pivot->quantity])->all() ?? []))

<div class="row g-3 mb-3">
    <div class="col-md-8">
        <label class="form-label">Name</label>
        <input type="text" name="name" value="{{ old('name', $product?->name) }}" class="form-control" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Category</label>
        <select name="category_id" id="category_id" class="form-select" required>
            <option value="">Select</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}"
                    data-combo="{{ $category->slug === \App\Models\Product::COMBO_CATEGORY_SLUG ? '1' : '0' }}"
                    @selected(old('category_id', $product?->category_id) == $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-12">
        <label class="form-label">Description</label>
        <textarea name="description" rows="4" class="form-control">{{ old('description', $product?->description) }}</textarea>
    </div>
    <div class="col-md-4">
        <label class="form-label">Price (₹)</label>
        <input type="number" step="0.01" min="0" name="price" value="{{ old('price', $product?->price) }}" class="form-control" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Discount %</label>
        <input type="number" min="0" max="100" name="discount_percent" value="{{ old('discount_percent', $product?->discount_percent ?? 0) }}" class="form-control">
    </div>
    <div class="col-md-4">
        <label class="form-label">Stock</label>
        <input type="number" min="0" name="stock" value="{{ old('stock', $product?->stock ?? 0) }}" class="form-control" required>
    </div>
    <div class="col-md-8">
        <label class="form-label">Image</label>
        <input type="file" name="image" accept="image/*" class="form-control">
        @if($product?->image_path)
            <div class="mt-2"><img src="{{ $product->image_url }}" alt="" style="height:80px;border-radius:.4rem;"></div>
        @endif
    </div>
    <div class="col-md-4 d-flex align-items-end">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', $product?->is_active ?? true))>
            <label class="form-check-label" for="is_active">Active</label>
        </div>
    </div>

    <div class="col-12" id="combo-components" style="display:none;">
        <label class="form-label fw-semibold">Combo includes (select at least 2 products)</label>
        <div class="border rounded p-3 bg-light">
            @forelse($componentProducts as $component)
                @php($checked = in_array($component->id, $selectedComponents, false) || in_array((string) $component->id, $selectedComponents, true))
                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                    <div class="form-check mb-0 flex-grow-1">
                        <input class="form-check-input component-check" type="checkbox"
                               name="component_ids[]" value="{{ $component->id }}"
                               id="component_{{ $component->id }}"
                               @checked($checked)>
                        <label class="form-check-label" for="component_{{ $component->id }}">
                            {{ $component->name }}
                            <span class="text-muted small">({{ $component->category?->name }} · ₹{{ number_format($component->price, 2) }})</span>
                        </label>
                    </div>
                    <div class="input-group input-group-sm" style="width:120px">
                        <span class="input-group-text">Qty</span>
                        <input type="number" min="1" max="50" class="form-control"
                               name="component_qty[{{ $component->id }}]"
                               value="{{ $componentQtys[$component->id] ?? 1 }}">
                    </div>
                </div>
            @empty
                <p class="mb-0 text-muted">No regular products available to map. Create non-combo products first.</p>
            @endforelse
        </div>
    </div>
</div>
@if($errors->any())
    <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif

<script>
(function () {
    const select = document.getElementById('category_id');
    const panel = document.getElementById('combo-components');
    if (!select || !panel) return;

    function toggleCombo() {
        const option = select.options[select.selectedIndex];
        panel.style.display = option && option.dataset.combo === '1' ? 'block' : 'none';
    }

    select.addEventListener('change', toggleCombo);
    toggleCombo();
})();
</script>
