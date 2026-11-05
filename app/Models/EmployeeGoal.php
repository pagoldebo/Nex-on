<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeGoal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'creator_id',
        'title',
        'description',
        'metric_type',
        'target_value',
        'current_value',
        'unit',
        'progress_percent',
        'status',
        'due_date',
        'category',
        'weightage',
    ];

    protected $casts = [
        'target_value'     => 'decimal:2',
        'current_value'    => 'decimal:2',
        'progress_percent' => 'integer',
        'due_date'         => 'date',
        'weightage'        => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'completed'   => ['bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'label' => 'Completed'],
            'on_track'    => ['bg' => 'bg-blue-50 text-blue-700 border-blue-200',         'label' => 'On Track'],
            'at_risk'     => ['bg' => 'bg-rose-50 text-rose-700 border-rose-200',         'label' => 'At Risk'],
            default       => ['bg' => 'bg-slate-100 text-slate-700 border-slate-200',     'label' => 'Not Started'],
        };
    }
}

