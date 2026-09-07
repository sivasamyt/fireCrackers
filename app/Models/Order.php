<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'user_id',
        'guest_name',
        'guest_email',
        'guest_phone',
        'status',
        'payment_method',
        'payment_status',
        'subtotal',
        'discount_total',
        'grand_total',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'pincode',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'discount_total' => 'decimal:2',
            'grand_total' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function customerName(): string
    {
        return $this->user?->name ?? $this->guest_name ?? 'Guest';
    }

    public function customerEmail(): ?string
    {
        return $this->user?->email ?? $this->guest_email;
    }

    public function customerPhone(): ?string
    {
        return $this->guest_phone;
    }

    public function fullAddress(): string
    {
        return collect([
            $this->address_line1,
            $this->address_line2,
            $this->city,
            $this->state,
            $this->pincode,
        ])->filter()->implode(', ');
    }
}
