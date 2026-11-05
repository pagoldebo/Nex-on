<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductBatch extends Model
{
    protected $fillable = [
        'product_variant_id',
        'batch_number',
        'lot_number',
        'mfg_date',
        'expiry_date',
        'initial_qty',
        'remaining_qty',
        'cost_price',
        'status',
        'notes',
    ];

    protected $casts = [
        'mfg_date'      => 'date',
        'expiry_date'   => 'date',
        'initial_qty'   => 'integer',
        'remaining_qty' => 'integer',
        'cost_price'    => 'decimal:2',
    ];

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(OrderItemBatch::class, 'product_batch_id');
    }

    /**
     * Check if batch is expired.
     */
    public function isExpired(): bool
    {
        return $this->expiry_date->isPast();
    }

    /**
     * Days remaining until expiration.
     */
    public function getDaysUntilExpiryAttribute(): int
    {
        return (int) Carbon::now()->startOfDay()->diffInDays($this->expiry_date, false);
    }

    /**
     * Expiry status badge label.
     */
    public function getExpiryStatusAttribute(): string
    {
        $days = $this->days_until_expiry;
        if ($days < 0) return 'expired';
        if ($days <= 30) return 'expiring_30d';
        if ($days <= 60) return 'expiring_60d';
        if ($days <= 90) return 'expiring_90d';
        return 'healthy';
    }
}
