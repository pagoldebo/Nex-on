<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'payroll_code',
        'period_month',
        'title',
        'start_date',
        'end_date',
        'total_gross',
        'total_allowances',
        'total_deductions',
        'total_net',
        'employees_count',
        'status',
        'approved_by',
        'approved_at',
        'disbursed_at',
        'created_by',
    ];

    protected $casts = [
        'start_date'       => 'date',
        'end_date'         => 'date',
        'total_gross'      => 'decimal:2',
        'total_allowances' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'total_net'        => 'decimal:2',
        'employees_count'  => 'integer',
        'approved_at'      => 'datetime',
        'disbursed_at'     => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Payroll $payroll) {
            if (empty($payroll->payroll_code)) {
                $payroll->payroll_code = 'PAY-' . str_replace('-', '', $payroll->period_month ?? date('Y-m'));
            }
        });
    }

    public function items(): HasMany
    {
        return $this->hasMany(PayrollItem::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function recalculateTotals(): void
    {
        $this->loadMissing('items');
        $this->total_gross = (float) $this->items->sum('gross_salary');
        $this->total_allowances = (float) $this->items->sum(fn($i) => (float)$i->hra + (float)$i->transport_allowance + (float)$i->performance_bonus);
        $this->total_deductions = (float) $this->items->sum('total_deductions');
        $this->total_net = (float) $this->items->sum('net_salary');
        $this->employees_count = $this->items->count();
        $this->save();
    }

    public function getFormattedNetAttribute(): string
    {
        return '₹' . number_format($this->total_net, 2);
    }

    public function getFormattedGrossAttribute(): string
    {
        return '₹' . number_format($this->total_gross, 2);
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'disbursed' => ['bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'label' => 'Disbursed / Paid'],
            'approved'  => ['bg' => 'bg-blue-50 text-blue-700 border-blue-200',         'label' => 'Approved by Finance'],
            'reviewed'  => ['bg' => 'bg-purple-50 text-purple-700 border-purple-200',     'label' => 'HR Reviewed'],
            default     => ['bg' => 'bg-amber-50 text-amber-700 border-amber-200',       'label' => 'Draft Calculation'],
        };
    }
}
