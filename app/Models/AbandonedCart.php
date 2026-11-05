<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class AbandonedCart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'email',
        'phone',
        'customer_name',
        'cart_data',
        'recovery_hash',
        'total_amount',
        'items_count',
        'recovery_step',
        'last_reminded_at',
        'is_recovered',
        'recovered_at',
        'recovered_order_id',
    ];

    protected $casts = [
        'cart_data'        => 'array',
        'total_amount'     => 'decimal:2',
        'items_count'      => 'integer',
        'recovery_step'    => 'integer',
        'last_reminded_at' => 'datetime',
        'is_recovered'     => 'boolean',
        'recovered_at'     => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (AbandonedCart $cart) {
            if (empty($cart->recovery_hash)) {
                $cart->recovery_hash = Str::random(40) . time();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function recoveredOrder(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'recovered_order_id');
    }

    public function scopeUnrecovered(Builder $query): Builder
    {
        return $query->where('is_recovered', false);
    }

    public function scopeRecovered(Builder $query): Builder
    {
        return $query->where('is_recovered', true);
    }

    public function getFormattedTotalAttribute(): string
    {
        return '₹' . number_format($this->total_amount, 2);
    }

    public function getStepLabelAttribute(): string
    {
        return match ($this->recovery_step) {
            1 => '1-Hour Email Sent',
            2 => '24-Hour SMS Sent',
            3 => '72-Hour Final Notice Sent',
            default => 'Cart Abandoned (Pending)',
        };
    }

    public function getStepBadgeAttribute(): array
    {
        if ($this->is_recovered) {
            return ['bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'label' => 'Recovered ✅'];
        }

        return match ($this->recovery_step) {
            1 => ['bg' => 'bg-blue-50 text-blue-700 border-blue-200', 'label' => '1-Hr Email Sent'],
            2 => ['bg' => 'bg-purple-50 text-purple-700 border-purple-200', 'label' => '24-Hr SMS Sent'],
            3 => ['bg' => 'bg-rose-50 text-rose-700 border-rose-200', 'label' => '72-Hr Final Drip'],
            default => ['bg' => 'bg-amber-50 text-amber-700 border-amber-200', 'label' => 'Pending Drip'],
        };
    }
}

