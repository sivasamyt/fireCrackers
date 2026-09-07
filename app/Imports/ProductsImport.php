<?php

namespace App\Imports;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductsImport implements ToCollection, WithHeadingRow
{
    public int $created = 0;

    /** @var list<string> */
    public array $errors = [];

    public function collection(Collection $rows): void
    {
        $categories = Category::query()
            ->get(['id', 'name'])
            ->keyBy(fn (Category $category) => strtolower($category->name));

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;
            $categoryName = trim((string) ($row['category_name'] ?? ''));
            $name = trim((string) ($row['name'] ?? ''));

            if ($categoryName === '' && $name === '') {
                continue;
            }

            $data = [
                'category_name' => $categoryName,
                'name' => $name,
                'description' => isset($row['description']) ? trim((string) $row['description']) : null,
                'price' => $row['price'] ?? null,
                'discount_percent' => $row['discount_percent'] ?? 0,
                'stock' => $row['stock'] ?? null,
                'is_active' => $row['is_active'] ?? 1,
            ];

            if ($data['description'] === '') {
                $data['description'] = null;
            }

            $validator = Validator::make($data, [
                'category_name' => ['required', 'string', 'max:255'],
                'name' => ['required', 'string', 'max:255'],
                'description' => ['nullable', 'string'],
                'price' => ['required', 'numeric', 'min:0'],
                'discount_percent' => ['nullable', 'integer', 'min:0', 'max:100'],
                'stock' => ['required', 'integer', 'min:0'],
                'is_active' => ['nullable'],
            ]);

            if ($validator->fails()) {
                $this->errors[] = "Row {$rowNumber}: ".$validator->errors()->first();

                continue;
            }

            $category = $categories->get(strtolower($categoryName));

            if (! $category) {
                $this->errors[] = "Row {$rowNumber}: Category \"{$categoryName}\" not found.";

                continue;
            }

            try {
                Product::query()->create([
                    'category_id' => $category->id,
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'price' => $data['price'],
                    'discount_percent' => (int) ($data['discount_percent'] ?? 0),
                    'stock' => (int) $data['stock'],
                    'is_active' => $this->toBoolean($data['is_active'], true),
                ]);
                $this->created++;
            } catch (\Throwable $e) {
                $this->errors[] = "Row {$rowNumber}: ".$e->getMessage();
            }
        }
    }

    private function toBoolean(mixed $value, bool $default): bool
    {
        if ($value === null || $value === '') {
            return $default;
        }

        $normalized = strtolower(trim((string) $value));

        return in_array($normalized, ['1', 'true', 'yes', 'y', 'on'], true);
    }
}
