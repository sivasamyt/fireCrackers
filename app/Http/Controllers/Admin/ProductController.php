<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ProductSampleExport;
use App\Http\Controllers\Controller;
use App\Imports\ProductsImport;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProductController extends Controller
{
    public function index(): View
    {
        $products = Product::query()->with('category')->latest()->paginate(12);

        return view('admin.products.index', compact('products'));
    }

    public function downloadSample(): BinaryFileResponse
    {
        return Excel::download(new ProductSampleExport, 'products-sample.xlsx');
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
        ]);

        $import = new ProductsImport;

        try {
            Excel::import($import, $request->file('file'));
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', 'Could not import products. Check the file format and try again.');
        }

        $message = "{$import->created} product".($import->created === 1 ? '' : 's').' imported.';

        if ($import->errors !== []) {
            $preview = implode(' ', array_slice($import->errors, 0, 5));

            return back()->with('error', $message.' Some rows failed: '.$preview);
        }

        return back()->with('success', $message);
    }

    public function create(): View
    {
        return view('admin.products.create', $this->formData());
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = Str::slug($data['name']).'-'.Str::lower(Str::random(5));
        $data['is_active'] = $request->boolean('is_active', true);
        unset($data['component_ids'], $data['component_qty']);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::query()->create($data);
        $this->syncComponents($request, $product->load('category'));

        return redirect()->route('admin.products.index')->with('success', 'Product created.');
    }

    public function edit(Product $product): View
    {
        $product->load('components');

        return view('admin.products.edit', array_merge($this->formData($product), compact('product')));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $this->validated($request, $product);
        $data['is_active'] = $request->boolean('is_active');
        unset($data['component_ids'], $data['component_qty']);

        if ($request->hasFile('image')) {
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
            $data['image_path'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);
        $this->syncComponents($request, $product->fresh('category'));

        return redirect()->route('admin.products.index')->with('success', 'Product updated.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }

        $product->components()->detach();
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Product deleted.');
    }

    private function formData(?Product $product = null): array
    {
        Category::query()->firstOrCreate(
            ['slug' => Product::COMBO_CATEGORY_SLUG],
            ['name' => 'Combo', 'is_active' => true]
        );

        $categories = Category::query()->orderBy('name')->get();

        $componentProducts = Product::query()
            ->with('category')
            ->whereDoesntHave('category', fn ($q) => $q->where('slug', Product::COMBO_CATEGORY_SLUG))
            ->when($product, fn ($q) => $q->where('id', '!=', $product->id))
            ->orderBy('name')
            ->get();

        $comboCategoryId = $categories->firstWhere('slug', Product::COMBO_CATEGORY_SLUG)?->id;

        return compact('categories', 'componentProducts', 'comboCategoryId');
    }

    private function validated(Request $request, ?Product $product = null): array
    {
        $comboCategoryId = Category::query()
            ->where('slug', Product::COMBO_CATEGORY_SLUG)
            ->value('id');

        $isCombo = (int) $request->input('category_id') === (int) $comboCategoryId;

        $allowedComponentIds = Product::query()
            ->whereDoesntHave('category', fn ($q) => $q->where('slug', Product::COMBO_CATEGORY_SLUG))
            ->when($product, fn ($q) => $q->where('id', '!=', $product->id))
            ->pluck('id')
            ->all();

        $data = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'discount_percent' => ['nullable', 'integer', 'min:0', 'max:100'],
            'stock' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'max:4096'],
            'is_active' => ['nullable', 'boolean'],
            'component_ids' => [$isCombo ? 'required' : 'nullable', 'array', 'min:'.($isCombo ? 2 : 0)],
            'component_ids.*' => ['integer', Rule::in($allowedComponentIds)],
            'component_qty' => ['nullable', 'array'],
            'component_qty.*' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $data['discount_percent'] = (int) ($data['discount_percent'] ?? 0);

        if ($isCombo) {
            $ids = array_values(array_unique(array_map('intval', $data['component_ids'] ?? [])));
            if (count($ids) < 2) {
                throw ValidationException::withMessages([
                    'component_ids' => 'A combo product must include at least 2 products.',
                ]);
            }
            $data['component_ids'] = $ids;
        }

        return $data;
    }

    private function syncComponents(Request $request, Product $product): void
    {
        if (! $product->isCombo()) {
            $product->components()->detach();

            return;
        }

        $ids = array_map('intval', $request->input('component_ids', []));
        $qtys = $request->input('component_qty', []);
        $sync = [];

        foreach ($ids as $id) {
            $sync[$id] = ['quantity' => max(1, (int) ($qtys[$id] ?? 1))];
        }

        $product->components()->sync($sync);
    }
}
