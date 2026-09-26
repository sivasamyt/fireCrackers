<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'unit_price',
        'discount_percent',
        'quantity',
        'line_total',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'discount_percent' => 'integer',
            'quantity' => 'integer',
            'line_total' => 'decimal:2',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public static function computeLineTotal(float $unitPrice, int $discountPercent, int $quantity): float
    {
        $discountedUnit = $unitPrice * (1 - max(0, min(100, $discountPercent)) / 100);

        return round($discountedUnit * $quantity, 2);
    }

    public function refreshLineTotal(): void
    {
        $this->line_total = self::computeLineTotal(
            (float) $this->unit_price,
            (int) $this->discount_percent,
            (int) $this->quantity
        );
    }
}
