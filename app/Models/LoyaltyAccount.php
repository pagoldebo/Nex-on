<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LoyaltyAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'loyalty_tier_id',
        'points_balance',
        'points_earned_lifetime',
        'points_redeemed_lifetime',
        'lifetime_spend',
    ];

    protected $casts = [
        'points_balance'           => 'integer',
        'points_earned_lifetime'   => 'integer',
        'points_redeemed_lifetime' => 'integer',
        'lifetime_spend'           => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tier(): BelongsTo
    {
        return $this->belongsTo(LoyaltyTier::class, 'loyalty_tier_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(LoyaltyTransaction::class)->orderBy('id', 'desc');
    }

    /**
     * Credit points to the customer's balance.
     */
    public function creditPoints(int $points, string $description, ?int $orderId = null): LoyaltyTransaction
    {
        $this->increment('points_balance', $points);
        $this->increment('points_earned_lifetime', $points);

        return $this->transactions()->create([
            'order_id'    => $orderId,
            'type'        => 'earned',
            'points'      => $points,
            'description' => $description,
        ]);
    }

    /**
     * Debit / Redeem points.
     */
    public function redeemPoints(int $points, string $description, ?int $orderId = null): LoyaltyTransaction
    {
        $pointsToDeduct = min($this->points_balance, abs($points));
        $this->decrement('points_balance', $pointsToDeduct);
        $this->increment('points_redeemed_lifetime', $pointsToDeduct);

        return $this->transactions()->create([
            'order_id'    => $orderId,
            'type'        => 'redeemed',
            'points'      => -$pointsToDeduct,
            'description' => $description,
        ]);
    }

    /**
     * Adjust points balance manually with reason.
     */
    public function adjustPoints(int $points, string $description): LoyaltyTransaction
    {
        if ($points >= 0) {
            $this->increment('points_balance', $points);
            $this->increment('points_earned_lifetime', $points);
            $type = 'adjusted';
        } else {
            $deduct = min($this->points_balance, abs($points));
            $this->decrement('points_balance', $deduct);
            $type = 'adjusted';
            $points = -$deduct;
        }

        return $this->transactions()->create([
            'type'        => $type,
            'points'      => $points,
            'description' => $description,
        ]);
    }

    /**
     * Automatically update tier according to lifetime spend.
     */
    public function refreshTier(): void
    {
        $appropriateTier = LoyaltyTier::where('is_active', true)
            ->where('min_spend', '<=', $this->lifetime_spend)
            ->orderBy('min_spend', 'desc')
            ->first();

        if ($appropriateTier && $appropriateTier->id !== $this->loyalty_tier_id) {
            $this->loyalty_tier_id = $appropriateTier->id;
            $this->save();
        }
    }

    /**
     * Cash equivalent value (1 point = ₹0.50).
     */
    public function getPointsRupeeValueAttribute(): float
    {
        return (float) ($this->points_balance * 0.50);
    }
}

