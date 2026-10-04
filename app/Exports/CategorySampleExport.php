<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CategorySampleExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return ['name', 'sort_order', 'is_active'];
    }

    public function array(): array
    {
        return [
            ['Sparklers', 1, 1],
            ['Rockets', 2, 1],
        ];
    }
}
