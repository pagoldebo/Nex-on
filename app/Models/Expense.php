<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'expense_category_id',
        'outlet_id',
        'expense_number',
        'title',
        'amount',
        'tax_amount',
        'payment_method',
        'expense_date',
        'vendor_name',
        'vendor_gstin',
        'reference_no',
        'receipt_path',
        'is_recurring',
        'recurring_frequency',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'amount'       => 'decimal:2',
        'tax_amount'   => 'decimal:2',
        'expense_date' => 'date',
        'is_recurring' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Expense $expense) {
            if (empty($expense->expense_number)) {
                $expense->expense_number = self::generateExpenseNumber();
            }
        });
    }

    public static function generateExpenseNumber(): string
    {
        $prefix = 'EXP-' . date('Ym') . '-';
        $latest = self::where('expense_number', 'LIKE', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->first();

        if ($latest && preg_match('/EXP-\d{6}-(\d{4})/', $latest->expense_number, $matches)) {
            $nextSeq = intval($matches[1]) + 1;
        } else {
            $nextSeq = 1;
        }

        return $prefix . str_pad((string) $nextSeq, 4, '0', STR_PAD_LEFT);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeInDateRange(Builder $query, $startDate, $endDate): Builder
    {
        return $query->whereBetween('expense_date', [
            Carbon::parse($startDate)->startOfDay(),
            Carbon::parse($endDate)->endOfDay(),
        ]);
    }

    public function getFormattedAmountAttribute(): string
    {
        return '₹' . number_format($this->amount, 2);
    }

    public function getFormattedTaxAttribute(): string
    {
        return '₹' . number_format($this->tax_amount, 2);
    }
}

