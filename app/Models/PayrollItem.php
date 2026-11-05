<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'payroll_id',
        'user_id',
        'payslip_number',
        'designation',
        'department',
        'bank_name',
        'bank_account_no',
        'bank_ifsc',
        'pan_number',
        'uan_number',
        'working_days',
        'present_days',
        'leave_days',
        'basic_salary',
        'hra',
        'transport_allowance',
        'performance_bonus',
        'gross_salary',
        'pf_deduction',
        'esi_deduction',
        'tds_tax',
        'total_deductions',
        'net_salary',
        'status',
        'payment_method',
        'payment_reference',
    ];

    protected $casts = [
        'working_days'        => 'integer',
        'present_days'        => 'decimal:1',
        'leave_days'          => 'decimal:1',
        'basic_salary'        => 'decimal:2',
        'hra'                 => 'decimal:2',
        'transport_allowance' => 'decimal:2',
        'performance_bonus'   => 'decimal:2',
        'gross_salary'        => 'decimal:2',
        'pf_deduction'        => 'decimal:2',
        'esi_deduction'       => 'decimal:2',
        'tds_tax'             => 'decimal:2',
        'total_deductions'    => 'decimal:2',
        'net_salary'          => 'decimal:2',
    ];

    public function payroll(): BelongsTo
    {
        return $this->belongsTo(Payroll::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function generatePayslipNumber(string $periodMonth): string
    {
        $prefix = 'SLIP-' . str_replace('-', '', $periodMonth) . '-';
        $latest = self::where('payslip_number', 'LIKE', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->first();

        if ($latest && preg_match('/SLIP-\d{6}-(\d{4})/', $latest->payslip_number, $matches)) {
            $nextSeq = intval($matches[1]) + 1;
        } else {
            $nextSeq = 1;
        }

        return $prefix . str_pad((string) $nextSeq, 4, '0', STR_PAD_LEFT);
    }

    public function getFormattedNetAttribute(): string
    {
        return '₹' . number_format($this->net_salary, 2);
    }

    public function getFormattedGrossAttribute(): string
    {
        return '₹' . number_format($this->gross_salary, 2);
    }

    public function getFormattedDeductionsAttribute(): string
    {
        return '₹' . number_format($this->total_deductions, 2);
    }
}

