<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductSampleExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return [
            'category_name',
            'name',
            'description',
            'price',
            'discount_percent',
            'stock',
            'is_active',
        ];
    }

    public function array(): array
    {
        return [
            ['Sparklers', 'Golden Sparkler 10cm', 'Classic handheld sparkler', 49.00, 0, 100, 1],
            ['Sparklers', 'Color Sparkler Pack', 'Assorted color sparklers', 99.00, 10, 50, 1],
        ];
    }
}
