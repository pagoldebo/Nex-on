<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class StaffShift extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_id',
        'user_id',
        'shift_date',
        'shift_type',
        'start_time',
        'end_time',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'shift_date' => 'date',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function attendance(): HasOne
    {
        return $this->hasOne(StaffAttendance::class, 'shift_id');
    }

    public function getShiftTypeBadgeAttribute(): array
    {
        return match ($this->shift_type) {
            'morning'  => ['bg' => 'bg-amber-50 text-amber-800 border-amber-200',   'label' => 'Morning (09:00 - 17:00)', 'icon' => '🌅'],
            'evening'  => ['bg' => 'bg-indigo-50 text-indigo-800 border-indigo-200', 'label' => 'Evening (13:00 - 21:00)', 'icon' => '🌆'],
            'night'    => ['bg' => 'bg-purple-50 text-purple-800 border-purple-200', 'label' => 'Night (21:00 - 06:00)',   'icon' => '🌙'],
            'full_day' => ['bg' => 'bg-emerald-50 text-emerald-800 border-emerald-200', 'label' => 'Full Day (10:00 - 20:00)', 'icon' => '☀️'],
            default    => ['bg' => 'bg-slate-100 text-slate-700 border-slate-200',   'label' => ucfirst($this->shift_type), 'icon' => '⏱️'],
        };
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'completed' => ['bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'label' => 'Completed'],
            'swapped'   => ['bg' => 'bg-blue-50 text-blue-700 border-blue-200',         'label' => 'Swapped'],
            'cancelled' => ['bg' => 'bg-rose-50 text-rose-700 border-rose-200',         'label' => 'Cancelled'],
            default     => ['bg' => 'bg-amber-50 text-amber-700 border-amber-200',       'label' => 'Scheduled'],
        };
    }
}

