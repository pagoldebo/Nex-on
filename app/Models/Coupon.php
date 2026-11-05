<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'type',
        'value',
        'min_order_amount',
        'max_uses',
        'used_count',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'value'            => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'expires_at'       => 'date',
        'is_active'        => 'boolean',
    ];

    /**
     * Check if the coupon is currently valid for a given order subtotal.
     */
    public function isValidForAmount(float $subtotal): array
    {
        if (!$this->is_active) {
            return ['valid' => false, 'message' => 'This coupon is no longer active.'];
        }

        if ($this->expires_at && Carbon::now()->startOfDay()->gt($this->expires_at)) {
            return ['valid' => false, 'message' => 'This coupon has expired.'];
        }

        if ($this->max_uses !== null && $this->used_count >= $this->max_uses) {
            return ['valid' => false, 'message' => 'This coupon has reached its maximum redemptions.'];
        }

        if ($subtotal < (float) $this->min_order_amount) {
            return [
                'valid'   => false,
                'message' => 'Minimum order amount for this coupon is ₹' . number_format($this->min_order_amount, 0),
            ];
        }

        return ['valid' => true, 'message' => 'Coupon applied successfully!'];
    }

    /**
     * Calculate discount amount given the cart subtotal.
     */
    public function calculateDiscount(float $subtotal): float
    {
        if ($this->type === 'percentage') {
            $discount = ($subtotal * (float) $this->value) / 100.0;
        } else {
            $discount = (float) $this->value;
        }

        return round(min($discount, $subtotal), 2);
    }
}
