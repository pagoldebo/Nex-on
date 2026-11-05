<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupplierInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_order_id',
        'supplier_id',
        'invoice_number',
        'invoice_date',
        'due_date',
        'subtotal',
        'tax_amount',
        'total_amount',
        'amount_paid',
        'payment_status',
        'matching_status',
        'po_variance',
        'notes',
        'attachment',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date'     => 'date',
        'subtotal'     => 'decimal:2',
        'tax_amount'   => 'decimal:2',
        'total_amount' => 'decimal:2',
        'amount_paid'  => 'decimal:2',
        'po_variance'  => 'decimal:2',
    ];

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(SupplierInvoicePayment::class)->latest();
    }

    /**
     * Record a payment and update status.
     */
    public function recordPayment(float $amount, string $method = 'bank_transfer', ?string $ref = null, ?string $paidAt = null, ?string $notes = null): SupplierInvoicePayment
    {
        $payment = $this->payments()->create([
            'amount'                => $amount,
            'payment_method'        => $method,
            'transaction_reference' => $ref,
            'paid_at'               => $paidAt ?? now()->toDateString(),
            'notes'                 => $notes,
        ]);

        $newPaid = (float) $this->amount_paid + $amount;
        $total = (float) $this->total_amount;

        $newStatus = 'unpaid';
        if ($newPaid >= $total) {
            $newStatus = 'paid';
        } elseif ($newPaid > 0) {
            $newStatus = 'partial';
        }

        $this->update([
            'amount_paid'    => $newPaid,
            'payment_status' => $newStatus,
        ]);

        return $payment;
    }
}

