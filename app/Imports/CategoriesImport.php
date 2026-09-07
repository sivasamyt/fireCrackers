<?php

namespace App\Imports;

use App\Models\Category;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CategoriesImport implements ToCollection, WithHeadingRow
{
    public int $created = 0;

    /** @var list<string> */
    public array $errors = [];

    public function collection(Collection $rows): void
    {
        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;
            $data = [
                'name' => trim((string) ($row['name'] ?? '')),
                'is_active' => $row['is_active'] ?? 1,
            ];

            if ($data['name'] === '') {
                continue;
            }

            $validator = Validator::make($data, [
                'name' => ['required', 'string', 'max:255'],
                'is_active' => ['nullable'],
            ]);

            if ($validator->fails()) {
                $this->errors[] = "Row {$rowNumber}: ".$validator->errors()->first();

                continue;
            }

            try {
                Category::query()->create([
                    'name' => $data['name'],
                    'slug' => Str::slug($data['name']).'-'.Str::lower(Str::random(4)),
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
