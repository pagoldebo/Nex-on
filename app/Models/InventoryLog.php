<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryLog extends Model
{
    protected $fillable = [
        'product_variant_id',
        'sku',
        'change_qty',
        'new_stock',
        'type',
        'notes',
        'created_by',
    ];

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'initial'                    => 'Initial Stock',
            'restock'                    => 'Stock Restock',
            'manual_adjustment'          => 'Manual Adjustment',
            'order_deduction'            => 'Order Placed (Sale)',
            'order_cancellation_restock' => 'Order Cancelled (Restock)',
            default                      => ucfirst(str_replace('_', ' ', $this->type)),
        };
    }
}
