<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrderItem extends Model
{
    protected $fillable = [
        'purchase_order_id',
        'product_variant_id',
        'ordered_qty',
        'received_qty',
        'unit_cost',
        'line_total',
    ];

    protected $casts = [
        'ordered_qty'  => 'integer',
        'received_qty' => 'integer',
        'unit_cost'    => 'integer',
        'line_total'   => 'integer',
    ];

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function getPendingQtyAttribute(): int
    {
        return max(0, $this->ordered_qty - $this->received_qty);
    }
}
