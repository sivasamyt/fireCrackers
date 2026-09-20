<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class GiftBox extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'image_path',
        'price',
        'discount_percent',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'discount_percent' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (GiftBox $giftBox): void {
            if (empty($giftBox->slug)) {
                $giftBox->slug = Str::slug($giftBox->name).'-'.Str::lower(Str::random(5));
            }
        });
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'gift_box_items')
            ->withPivot('quantity')
            ->withTimestamps();
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
