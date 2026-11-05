<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrder extends Model
{
    const STATUS_DRAFT     = 'draft';
    const STATUS_SENT      = 'sent';
    const STATUS_PARTIAL   = 'partial';
    const STATUS_RECEIVED  = 'received';
    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'po_number',
        'supplier_id',
        'created_by',
        'status',
        'total_amount',
        'expected_delivery_date',
        'notes',
    ];

    protected $casts = [
        'expected_delivery_date' => 'date',
        'total_amount'           => 'integer',
    ];

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
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function getFormattedTotalAttribute(): string
    {
        return '₹' . number_format($this->total_amount, 0);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT     => 'bg-slate-100 text-slate-700 border-slate-300',
            self::STATUS_SENT      => 'bg-blue-50 text-blue-700 border-blue-200',
            self::STATUS_PARTIAL   => 'bg-amber-50 text-amber-700 border-amber-200',
            self::STATUS_RECEIVED  => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            self::STATUS_CANCELLED => 'bg-rose-50 text-rose-700 border-rose-200',
            default                => 'bg-slate-100 text-slate-700 border-slate-300',
        };
    }

    public static function generatePoNumber(): string
    {
        $date = now()->format('Ymd');
        $count = static::whereDate('created_at', today())->count() + 1;
        return 'PO-' . $date . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
    }
}
