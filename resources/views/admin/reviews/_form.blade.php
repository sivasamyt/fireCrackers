<div class="mb-3">
    <label class="form-label" for="name">Name</label>
    <input type="text" name="name" id="name" value="{{ old('name', $review->name) }}" class="form-control @error('name') is-invalid @enderror" required>
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
<div class="mb-3">
    <label class="form-label" for="email">Email</label>
    <input type="email" name="email" id="email" value="{{ old('email', $review->email) }}" class="form-control @error('email') is-invalid @enderror" required>
    @error('email')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
<div class="mb-3">
    <label class="form-label" for="rating">Stars</label>
    <select name="rating" id="rating" class="form-select @error('rating') is-invalid @enderror" required>
        @for($i = 5; $i >= 1; $i--)
            <option value="{{ $i }}" @selected((int) old('rating', $review->rating) === $i)>{{ str_repeat('★', $i) }} ({{ $i }})</option>
        @endfor
    </select>
    @error('rating')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
<div class="mb-3">
    <label class="form-label" for="description">Description</label>
    <textarea name="description" id="description" rows="4" maxlength="2000" class="form-control @error('description') is-invalid @enderror" required>{{ old('description', $review->description) }}</textarea>
    @error('description')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
<div class="mb-3">
    <label class="form-label" for="sort_order">Display order</label>
    <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $review->sort_order) }}" min="0" max="100000" step="1" class="form-control @error('sort_order') is-invalid @enderror" placeholder="Leave blank to show newest first">
    @error('sort_order')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    <div class="form-text">Lower numbers appear first on the Home page. Blank shows after ordered reviews, newest first.</div>
</div>
<div class="form-check mb-3">
    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', $review->is_active))>
    <label class="form-check-label" for="is_active">Show on Home page</label>
</div>
