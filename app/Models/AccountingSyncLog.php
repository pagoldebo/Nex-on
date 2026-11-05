<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountingSyncLog extends Model
{
    protected $fillable = [
        'sync_type',
        'order_id',
        'voucher_number',
        'status',
        'direction',
        'payload_xml',
        'response_message',
        'synced_by',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function syncedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'synced_by');
    }

    public function getFormattedTypeAttribute(): string
    {
        return match ($this->sync_type) {
            'sales_voucher'   => 'Sales Voucher (XML)',
            'receipt_voucher' => 'Receipt Voucher (XML)',
            'stock_journal'   => 'Stock Journal (XML)',
            default           => ucfirst(str_replace('_', ' ', $this->sync_type)),
        };
    }
}
