<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoyaltyTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'loyalty_account_id',
        'order_id',
        'type',
        'points',
        'description',
    ];

    protected $casts = [
        'points' => 'integer',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(LoyaltyAccount::class, 'loyalty_account_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}

