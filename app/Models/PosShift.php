<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class PosShift extends Model
{
    use HasFactory;

    protected $fillable = [
        'shift_code',
        'cashier_id',
        'opened_at',
        'closed_at',
        'status',
        'opening_float',
        'expected_cash',
        'counted_cash',
        'cash_difference',
        'cash_sales',
        'card_sales',
        'upi_sales',
        'total_sales',
        'total_orders_count',
        'notes',
    ];

    protected $casts = [
        'opened_at'          => 'datetime',
        'closed_at'          => 'datetime',
        'opening_float'      => 'decimal:2',
        'expected_cash'      => 'decimal:2',
        'counted_cash'       => 'decimal:2',
        'cash_difference'    => 'decimal:2',
        'cash_sales'         => 'decimal:2',
        'card_sales'         => 'decimal:2',
        'upi_sales'          => 'decimal:2',
        'total_sales'        => 'decimal:2',
        'total_orders_count' => 'integer',
    ];

    protected static function booted()
    {
        static::creating(function ($shift) {
            if (empty($shift->shift_code)) {
                $count = static::whereDate('created_at', today())->count() + 1;
                $shift->shift_code = 'SHIFT-' . now()->format('Ymd') . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
            }
            if (empty($shift->opened_at)) {
                $shift->opened_at = now();
            }
            if (empty($shift->expected_cash)) {
                $shift->expected_cash = $shift->opening_float ?? 0;
            }
        });
    }

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function movements(): HasMany
    {
        return $this->hasMany(PosShiftMovement::class);
    }

    /**
     * Orders placed during this shift window.
     */
    public function getOrdersQuery()
    {
        $query = Order::where('channel', 'pos')
            ->where('pos_cashier_id', $this->cashier_id)
            ->where('created_at', '>=', $this->opened_at);

        if ($this->closed_at) {
            $query->where('created_at', '<=', $this->closed_at);
        }

        return $query;
    }

    /**
     * Dynamically compute current sales totals and expected drawer cash.
     */
    public function recomputeLiveMetrics(): self
    {
        $orders = $this->getOrdersQuery()->get();

        $cashSales = (float) $orders->where('payment_method', 'cash')->sum('total');
        $cardSales = (float) $orders->where('payment_method', 'card')->sum('total');
        $upiSales  = (float) $orders->where('payment_method', 'upi')->sum('total');
        $totalSales = $cashSales + $cardSales + $upiSales;
        $orderCount = $orders->count();

        $cashIn  = (float) $this->movements()->where('type', 'cash_in')->sum('amount');
        $cashOut = (float) $this->movements()->where('type', 'cash_out')->sum('amount');

        $opening = (float) $this->opening_float;
        $expectedCash = $opening + $cashSales + $cashIn - $cashOut;

        $difference = 0.0;
        if ($this->counted_cash !== null) {
            $difference = round((float) $this->counted_cash - $expectedCash, 2);
        }

        $this->update([
            'cash_sales'         => $cashSales,
            'card_sales'         => $cardSales,
            'upi_sales'          => $upiSales,
            'total_sales'        => $totalSales,
            'total_orders_count' => $orderCount,
            'expected_cash'      => $expectedCash,
            'cash_difference'    => $difference,
        ]);

        return $this;
    }

    /**
     * Close shift with final drawer count (Z-Report).
     */
    public function closeShift(float $countedCash, ?string $notes = null): self
    {
        $this->counted_cash = $countedCash;
        $this->closed_at = now();
        $this->status = 'closed';
        if ($notes) {
            $this->notes = $notes;
        }
        $this->save();

        return $this->recomputeLiveMetrics();
    }
}

