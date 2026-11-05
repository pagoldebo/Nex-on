<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id', 'product_color_id', 'supplier_id', 'size', 'sku',
        'price', 'compare_at_price', 'cost_price', 'stock',
        'reorder_threshold', 'reorder_qty',
    ];

    protected $casts = [
        'price'             => 'decimal:2',
        'compare_at_price'  => 'decimal:2',
        'cost_price'        => 'decimal:2',
        'reorder_threshold' => 'integer',
        'reorder_qty'       => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function color(): BelongsTo
    {
        return $this->belongsTo(ProductColor::class, 'product_color_id');
    }

    public function productColor(): BelongsTo
    {
        return $this->belongsTo(ProductColor::class, 'product_color_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function inventoryLogs(): HasMany
    {
        return $this->hasMany(InventoryLog::class, 'product_variant_id')->latest();
    }

    public function batches(): HasMany
    {
        return $this->hasMany(ProductBatch::class, 'product_variant_id')->orderBy('expiry_date', 'asc');
    }

    public function getInStockAttribute(): bool
    {
        return $this->stock > 0;
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->stock > 0 && $this->stock <= ($this->reorder_threshold ?? 5);
    }

    public function getIsBreachingThresholdAttribute(): bool
    {
        return $this->stock <= ($this->reorder_threshold ?? 5);
    }

    /**
     * Adjust stock by delta (+ or -) and log the movement.
     */
    public function adjustStock(int $changeQty, string $type = 'manual_adjustment', ?string $notes = null, ?int $userId = null): void
    {
        $this->stock = max(0, $this->stock + $changeQty);
        $this->save();

        InventoryLog::create([
            'product_variant_id' => $this->id,
            'sku'                => $this->sku ?? 'N/A',
            'change_qty'         => $changeQty,
            'new_stock'          => $this->stock,
            'type'               => $type,
            'notes'              => $notes,
            'created_by'         => $userId ?? Auth::id(),
        ]);
    }

    /**
     * Set stock to an exact count and log the adjustment.
     */
    public function setStock(int $newStock, string $type = 'manual_adjustment', ?string $notes = null, ?int $userId = null): void
    {
        $newStock = max(0, $newStock);
        $diff = $newStock - $this->stock;

        $this->stock = $newStock;
        $this->save();

        InventoryLog::create([
            'product_variant_id' => $this->id,
            'sku'                => $this->sku ?? 'N/A',
            'change_qty'         => $diff,
            'new_stock'          => $this->stock,
            'type'               => $type,
            'notes'              => $notes,
            'created_by'         => $userId ?? Auth::id(),
        ]);
    }
}
