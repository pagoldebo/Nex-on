<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CommercialInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'type',
        'invoice_date',
        'due_date',
        'client_name',
        'company_name',
        'client_gstin',
        'client_pan',
        'client_email',
        'client_phone',
        'billing_address',
        'shipping_address',
        'place_of_supply',
        'currency',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'cgst_amount',
        'sgst_amount',
        'igst_amount',
        'total_amount',
        'amount_paid',
        'status',
        'notes',
        'terms',
        'created_by',
    ];

    protected $casts = [
        'invoice_date'    => 'date',
        'due_date'        => 'date',
        'subtotal'        => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount'      => 'decimal:2',
        'cgst_amount'     => 'decimal:2',
        'sgst_amount'     => 'decimal:2',
        'igst_amount'     => 'decimal:2',
        'total_amount'    => 'decimal:2',
        'amount_paid'     => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (CommercialInvoice $invoice) {
            if (empty($invoice->invoice_number)) {
                $invoice->invoice_number = self::generateInvoiceNumber($invoice->type ?? 'tax_invoice');
            }
        });
    }

    public static function generateInvoiceNumber(string $type = 'tax_invoice'): string
    {
        $prefix = ($type === 'credit_note' ? 'CN-' : ($type === 'proforma' ? 'PI-' : 'INV-')) . date('Ym') . '-';
        $latest = self::where('invoice_number', 'LIKE', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->first();

        if ($latest && preg_match('/(?:INV|CN|PI)-\d{6}-(\d{4})/', $latest->invoice_number, $matches)) {
            $nextSeq = intval($matches[1]) + 1;
        } else {
            $nextSeq = 1;
        }

        return $prefix . str_pad((string) $nextSeq, 4, '0', STR_PAD_LEFT);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CommercialInvoiceItem::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getBalanceDueAttribute(): float
    {
        return max(0, (float) $this->total_amount - (float) $this->amount_paid);
    }

    public function getFormattedTotalAttribute(): string
    {
        return '₹' . number_format($this->total_amount, 2);
    }

    public function getFormattedSubtotalAttribute(): string
    {
        return '₹' . number_format($this->subtotal, 2);
    }

    public function getFormattedTaxAttribute(): string
    {
        return '₹' . number_format($this->tax_amount, 2);
    }

    public function getFormattedPaidAttribute(): string
    {
        return '₹' . number_format($this->amount_paid, 2);
    }

    public function getFormattedBalanceAttribute(): string
    {
        return '₹' . number_format($this->balance_due, 2);
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'paid'      => ['bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'label' => 'Paid in Full'],
            'partial'   => ['bg' => 'bg-amber-50 text-amber-700 border-amber-200',     'label' => 'Partially Paid'],
            'issued'    => ['bg' => 'bg-blue-50 text-blue-700 border-blue-200',         'label' => 'Issued / Sent'],
            'cancelled' => ['bg' => 'bg-rose-50 text-rose-700 border-rose-200',         'label' => 'Cancelled'],
            default     => ['bg' => 'bg-slate-50 text-slate-700 border-slate-200',     'label' => 'Draft'],
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'credit_note' => 'Credit Note',
            'proforma'    => 'Proforma Invoice',
            default       => 'Tax Invoice',
        };
    }
}

