<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupportTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'user_id',
        'order_id',
        'assigned_to',
        'subject',
        'category',
        'priority',
        'status',
        'sla_due_at',
        'resolved_at',
    ];

    protected $casts = [
        'sla_due_at'  => 'datetime',
        'resolved_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (SupportTicket $ticket) {
            if (empty($ticket->ticket_number)) {
                $ticket->ticket_number = self::generateTicketNumber();
            }
            if (empty($ticket->sla_due_at)) {
                // SLA Hours: Urgent = 4 hours, High = 12 hours, Medium = 24 hours, Low = 48 hours
                $hours = match ($ticket->priority) {
                    'urgent' => 4,
                    'high'   => 12,
                    'low'    => 48,
                    default  => 24,
                };
                $ticket->sla_due_at = Carbon::now()->addHours($hours);
            }
        });
    }

    public static function generateTicketNumber(): string
    {
        $prefix = 'TCK-' . date('Ym') . '-';
        $latest = self::where('ticket_number', 'LIKE', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->first();

        if ($latest && preg_match('/TCK-\d{6}-(\d{4})/', $latest->ticket_number, $matches)) {
            $nextSeq = intval($matches[1]) + 1;
        } else {
            $nextSeq = 1;
        }

        return $prefix . str_pad((string) $nextSeq, 4, '0', STR_PAD_LEFT);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(SupportTicketMessage::class, 'ticket_id')->oldest();
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'order_inquiry'   => 'Order Inquiry',
            'refund_return'   => 'Refund & Return',
            'delivery_delay'  => 'Delivery Delay',
            'product_defect'  => 'Product Defect',
            'billing'         => 'Billing & Payment',
            default           => 'Other / General',
        };
    }

    public function getIsOverdueAttribute(): bool
    {
        if (in_array($this->status, ['resolved', 'closed'])) {
            return false;
        }

        return $this->sla_due_at ? $this->sla_due_at->isPast() : false;
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'resolved'         => ['bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'label' => 'Resolved'],
            'closed'           => ['bg' => 'bg-slate-100 text-slate-700 border-slate-200',     'label' => 'Closed'],
            'in_progress'      => ['bg' => 'bg-blue-50 text-blue-700 border-blue-200',         'label' => 'In Progress'],
            'waiting_customer' => ['bg' => 'bg-purple-50 text-purple-700 border-purple-200',   'label' => 'Waiting on Customer'],
            default            => ['bg' => 'bg-amber-50 text-amber-700 border-amber-200',       'label' => 'Open'],
        };
    }

    public function getPriorityBadgeAttribute(): array
    {
        return match ($this->priority) {
            'urgent' => ['bg' => 'bg-rose-100 text-rose-800 border-rose-300 font-bold', 'label' => 'Urgent (SLA 4h)', 'dot' => 'bg-rose-500'],
            'high'   => ['bg' => 'bg-amber-100 text-amber-800 border-amber-300 font-bold', 'label' => 'High (SLA 12h)', 'dot' => 'bg-amber-500'],
            'low'    => ['bg' => 'bg-slate-100 text-slate-700 border-slate-200',           'label' => 'Low',             'dot' => 'bg-slate-400'],
            default  => ['bg' => 'bg-blue-50 text-blue-700 border-blue-200',               'label' => 'Medium (SLA 24h)','dot' => 'bg-blue-500'],
        };
    }
}
