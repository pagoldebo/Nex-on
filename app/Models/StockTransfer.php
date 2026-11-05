<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class StockTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'transfer_no',
        'from_outlet_id',
        'to_outlet_id',
        'status',
        'notes',
        'dispatched_by',
        'dispatched_at',
        'received_by',
        'received_at',
    ];

    protected $casts = [
        'dispatched_at' => 'datetime',
        'received_at'   => 'datetime',
    ];

    public static function generateTransferNo(): string
    {
        $year = date('Y');
        $count = static::whereYear('created_at', $year)->count() + 1;
        return 'TRF-' . $year . '-' . str_pad((string) $count, 4, '0', STR_PAD_LEFT);
    }

    public function fromOutlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class, 'from_outlet_id');
    }

    public function toOutlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class, 'to_outlet_id');
    }

    public function dispatchedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dispatched_by');
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(StockTransferItem::class);
    }

    public function totalSentQty(): int
    {
        return (int) $this->items->sum('qty_sent');
    }

    public function totalReceivedQty(): int
    {
        return (int) $this->items->sum('qty_received');
    }
}
