<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'head_user_id',
        'description',
        'color',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function head(): BelongsTo
    {
        return $this->belongsTo(User::class, 'head_user_id');
    }

    public function staff(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'department_user')
            ->withPivot('role_title', 'is_primary')
            ->withTimestamps();
    }

    public function shifts(): HasMany
    {
        return $this->hasMany(StaffShift::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(StaffAttendance::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getColorBadgeAttribute(): array
    {
        return match ($this->color) {
            'indigo'  => ['bg' => 'bg-indigo-50 text-indigo-700 border-indigo-200', 'dot' => 'bg-indigo-500'],
            'blue'    => ['bg' => 'bg-blue-50 text-blue-700 border-blue-200',       'dot' => 'bg-blue-500'],
            'rose'    => ['bg' => 'bg-rose-50 text-rose-700 border-rose-200',       'dot' => 'bg-rose-500'],
            'emerald' => ['bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'dot' => 'bg-emerald-500'],
            'purple'  => ['bg' => 'bg-purple-50 text-purple-700 border-purple-200',   'dot' => 'bg-purple-500'],
            'amber'   => ['bg' => 'bg-amber-50 text-amber-700 border-amber-200',     'dot' => 'bg-amber-500'],
            default   => ['bg' => 'bg-slate-100 text-slate-700 border-slate-200',     'dot' => 'bg-slate-500'],
        };
    }
}

