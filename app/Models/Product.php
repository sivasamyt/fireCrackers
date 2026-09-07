<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'discount_percent',
        'image_path',
        'stock',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'discount_percent' => 'integer',
            'stock' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Product $product): void {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name).'-'.Str::random(5);
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function getDiscountedPriceAttribute(): float
    {
        $price = (float) $this->price;
        $discount = (int) $this->discount_percent;

        if ($discount <= 0) {
            return $price;
        }

        return round($price - ($price * $discount / 100), 2);
    }

    public function getImageUrlAttribute(): string
    {
        if ($this->image_path) {
            return asset('storage/'.$this->image_path);
        }

        return 'https://placehold.co/600x600/0b1220/f5c451?text='.urlencode($this->name);
    }
}
