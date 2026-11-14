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
     * Check if the coupon is currently valid for a given order subtotal and customer.
     */
    public function isValidForAmount(float $subtotal, ?int $userId = null, ?string $email = null): array
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

        // Per-customer redemption limit (M-09): single-use or per-customer coupons cannot be reused
        if ($this->max_uses === 1 && ($userId || !empty($email)) && $this->isUsedByUser($userId, $email)) {
            return ['valid' => false, 'message' => 'You have already redeemed this single-use coupon.'];
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
     * Determine if this coupon has already been redeemed by the user or email in a non-cancelled order.
     */
    public function isUsedByUser(?int $userId, ?string $email): bool
    {
        if (!$userId && empty($email)) {
            return false;
        }

        $query = Order::where('coupon_code', $this->code)
            ->where('status', '!=', Order::STATUS_CANCELLED);

        return $query->where(function ($q) use ($userId, $email) {
            if ($userId) {
                $q->where('user_id', $userId);
            }
            if (!empty($email)) {
                $cleanEmail = strtolower(trim($email));
                if ($userId) {
                    $q->orWhere('shipping_email', $cleanEmail);
                } else {
                    $q->where('shipping_email', $cleanEmail);
                }
            }
        })->exists();
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
