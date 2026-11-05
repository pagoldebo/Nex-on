<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AppraisalCycle extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'start_date',
        'cutoff_date',
        'description',
        'status',
    ];

    protected $casts = [
        'start_date'  => 'date',
        'cutoff_date' => 'date',
    ];

    public function appraisals(): HasMany
    {
        return $this->hasMany(StaffAppraisal::class);
    }
}

