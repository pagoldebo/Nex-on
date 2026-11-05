<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'department_id',
        'shift_id',
        'date',
        'clock_in',
        'clock_out',
        'ip_address',
        'device_name',
        'location_name',
        'total_hours',
        'overtime_hours',
        'status',
        'verification_status',
        'manager_note',
        'adjusted_by',
    ];

    protected $casts = [
        'date'           => 'date',
        'clock_in'       => 'datetime',
        'clock_out'      => 'datetime',
        'total_hours'    => 'decimal:2',
        'overtime_hours' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(StaffShift::class, 'shift_id');
    }

    public function adjuster(): BelongsTo
    {
        return $this->belongsTo(User::class, 'adjusted_by');
    }

    /**
     * Compute total hours worked and overtime.
     */
    public function computeHours(): void
    {
        if ($this->clock_in && $this->clock_out) {
            $diffMinutes = $this->clock_in->diffInMinutes($this->clock_out);
            $hours = round($diffMinutes / 60, 2);
            $this->total_hours = $hours;
            $this->overtime_hours = max(0, round($hours - 8.0, 2));

            if ($hours >= 7.5) {
                $this->status = 'present';
            } elseif ($hours >= 4.0) {
                $this->status = 'half_day';
            }
        }
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'present'  => ['bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'label' => 'Present'],
            'late'     => ['bg' => 'bg-amber-50 text-amber-700 border-amber-200',       'label' => 'Late In'],
            'half_day' => ['bg' => 'bg-blue-50 text-blue-700 border-blue-200',         'label' => 'Half Day'],
            'on_leave' => ['bg' => 'bg-purple-50 text-purple-700 border-purple-200',   'label' => 'On Leave'],
            default    => ['bg' => 'bg-rose-50 text-rose-700 border-rose-200',         'label' => 'Absent'],
        };
    }

    public function getVerificationBadgeAttribute(): array
    {
        return match ($this->verification_status) {
            'verified'          => ['bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'label' => 'Store Wi-Fi Verified', 'icon' => '✓'],
            'flagged_remote'    => ['bg' => 'bg-rose-50 text-rose-700 border-rose-200',         'label' => 'Remote / IP Flagged',   'icon' => '⚠️'],
            'manual_adjustment' => ['bg' => 'bg-purple-50 text-purple-700 border-purple-200',   'label' => 'Manager Override',      'icon' => '✏️'],
            default             => ['bg' => 'bg-slate-100 text-slate-700 border-slate-200',     'label' => 'Standard',              'icon' => '•'],
        };
    }
}

