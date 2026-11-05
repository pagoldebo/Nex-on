<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmsLog extends Model
{
    protected $fillable = [
        'phone',
        'driver',
        'template_type',
        'order_id',
        'message',
        'status',
        'gateway_response_id',
        'error_message',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
