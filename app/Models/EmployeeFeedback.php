<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeFeedback extends Model
{
    use HasFactory;

    protected $table = 'employee_feedbacks';

    protected $fillable = [
        'from_user_id',
        'to_user_id',
        'type',
        'badge',
        'message',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    public function getBadgeStyleAttribute(): array
    {
        return match ($this->badge) {
            'Customer Hero'       => ['bg' => 'bg-amber-50 text-amber-800 border-amber-200',   'icon' => '⭐'],
            'Inventory Wizard'    => ['bg' => 'bg-blue-50 text-blue-800 border-blue-200',     'icon' => '📦'],
            'Problem Solver'      => ['bg' => 'bg-purple-50 text-purple-800 border-purple-200', 'icon' => '💡'],
            'Team Player'         => ['bg' => 'bg-emerald-50 text-emerald-800 border-emerald-200', 'icon' => '🤝'],
            'Atelier Excellence'  => ['bg' => 'bg-rose-50 text-rose-800 border-rose-200',       'icon' => '✨'],
            default               => ['bg' => 'bg-indigo-50 text-indigo-800 border-indigo-200', 'icon' => '🎖️'],
        };
    }
}

