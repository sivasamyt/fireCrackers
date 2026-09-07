@php($product = $product ?? null)
<div class="row g-3 mb-3">
    <div class="col-md-8">
        <label class="form-label">Name</label>
        <input type="text" name="name" value="{{ old('name', $product?->name) }}" class="form-control" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Category</label>
        <select name="category_id" class="form-select" required>
            <option value="">Select</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" @selected(old('category_id', $product?->category_id) == $category->id)>{{ $category->name }}</option>
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
</div>
@if($errors->any())
    <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif
