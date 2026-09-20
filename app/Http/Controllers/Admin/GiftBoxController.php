<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GiftBox;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class GiftBoxController extends Controller
{
    public function index(): View
    {
        $giftBoxes = GiftBox::query()->withCount('products')->latest()->paginate(12);

        return view('admin.gift-boxes.index', compact('giftBoxes'));
    }

    public function create(): View
    {
        return view('admin.gift-boxes.create', $this->formData());
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = Str::slug($data['name']).'-'.Str::lower(Str::random(5));
        $data['is_active'] = $request->boolean('is_active', true);
        unset($data['product_ids'], $data['product_qty']);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('gift-boxes', 'public');
        }

        $giftBox = GiftBox::query()->create($data);
        $this->syncProducts($request, $giftBox);

        return redirect()->route('admin.gift-boxes.index')->with('success', 'Gift box created.');
    }

    public function edit(GiftBox $giftBox): View
    {
        $giftBox->load('products');

        return view('admin.gift-boxes.edit', array_merge($this->formData(), ['giftBox' => $giftBox]));
    }

    public function update(Request $request, GiftBox $giftBox): RedirectResponse
    {
        $data = $this->validated($request);
        $data['is_active'] = $request->boolean('is_active');
        unset($data['product_ids'], $data['product_qty']);

        if ($request->hasFile('image')) {
            if ($giftBox->image_path) {
                Storage::disk('public')->delete($giftBox->image_path);
            }
            $data['image_path'] = $request->file('image')->store('gift-boxes', 'public');
        }

        $giftBox->update($data);
        $this->syncProducts($request, $giftBox);

        return redirect()->route('admin.gift-boxes.index')->with('success', 'Gift box updated.');
    }

    public function destroy(GiftBox $giftBox): RedirectResponse
    {
        if ($giftBox->image_path) {
            Storage::disk('public')->delete($giftBox->image_path);
        }

        $giftBox->products()->detach();
        $giftBox->delete();

        return redirect()->route('admin.gift-boxes.index')->with('success', 'Gift box deleted.');
    }

    private function formData(): array
    {
        $availableProducts = Product::query()
            ->with('category')
            ->whereDoesntHave('category', fn ($q) => $q->where('slug', Product::COMBO_CATEGORY_SLUG))
            ->orderBy('name')
            ->get();

        return compact('availableProducts');
    }

    private function validated(Request $request): array
    {
        $allowedProductIds = Product::query()
            ->whereDoesntHave('category', fn ($q) => $q->where('slug', Product::COMBO_CATEGORY_SLUG))
            ->pluck('id')
            ->all();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'discount_percent' => ['nullable', 'integer', 'min:0', 'max:100'],
            'image' => ['nullable', 'image', 'max:4096'],
            'is_active' => ['nullable', 'boolean'],
            'product_ids' => ['required', 'array', 'min:2'],
            'product_ids.*' => ['integer', Rule::in($allowedProductIds)],
            'product_qty' => ['nullable', 'array'],
            'product_qty.*' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $data['discount_percent'] = (int) ($data['discount_percent'] ?? 0);

        return $data;
    }

    private function syncProducts(Request $request, GiftBox $giftBox): void
    {
        $ids = array_values(array_unique(array_map('intval', $request->input('product_ids', []))));
        $qtys = $request->input('product_qty', []);
        $sync = [];

        foreach ($ids as $id) {
            $sync[$id] = ['quantity' => max(1, (int) ($qtys[$id] ?? 1))];
        }

        $giftBox->products()->sync($sync);
    }
}
