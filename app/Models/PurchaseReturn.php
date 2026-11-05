<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class PurchaseReturn extends Model
{
    use HasFactory;

    protected $fillable = [
        'debit_note_number',
        'purchase_order_id',
        'supplier_id',
        'created_by',
        'return_date',
        'reason',
        'status',
        'subtotal',
        'tax_amount',
        'total_amount',
        'notes',
    ];

    protected $casts = [
        'return_date'  => 'date',
        'subtotal'     => 'decimal:2',
        'tax_amount'   => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::creating(function ($return) {
            if (empty($return->debit_note_number)) {
                $count = static::whereYear('created_at', now()->year)->count() + 1;
                $return->debit_note_number = 'DN-' . now()->format('Ym') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseReturnItem::class);
    }
}

