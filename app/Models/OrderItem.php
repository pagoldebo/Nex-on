<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'product_color_id',
        'product_variant_id',
        'product_name',
        'product_slug',
        'color_name',
        'color_hex',
        'size',
        'cover_image',
        'price',
        'compare_at_price',
        'qty',
        'line_total',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function productColor(): BelongsTo
    {
        return $this->belongsTo(ProductColor::class);
    }

    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function variant(): BelongsTo
    {
        return $this->productVariant();
    }

    public function batchAllocations(): HasMany
    {
        return $this->hasMany(OrderItemBatch::class);
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    /** Formatted unit price, e.g. "₹799" */
    public function getFormattedPriceAttribute(): string
    {
        return '₹' . number_format($this->price, 0);
    }

    /** Formatted line total, e.g. "₹1,598" */
    public function getFormattedLineTotalAttribute(): string
    {
        return '₹' . number_format($this->line_total, 0);
    }

    /** Saving per unit vs compare_at_price (null if no discount) */
    public function getSavingAttribute(): ?int
    {
        if (! $this->compare_at_price || $this->compare_at_price <= $this->price) {
            return null;
        }
        return $this->compare_at_price - $this->price;
    }

    /** Whether this item was on sale at time of purchase */
    public function isOnSale(): bool
    {
        return $this->saving !== null;
    }

    /**
     * One-line description for display in order history / confirmation:
     * "Lotus Silk Bra — Blush Pink, M × 2"
     */
    public function getSummaryAttribute(): string
    {
        return "{$this->product_name} — {$this->color_name}, {$this->size} × {$this->qty}";
    }
}
