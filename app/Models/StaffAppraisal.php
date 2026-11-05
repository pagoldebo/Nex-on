<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffAppraisal extends Model
{
    use HasFactory;

    protected $fillable = [
        'appraisal_cycle_id',
        'user_id',
        'reviewer_id',
        'self_rating',
        'manager_rating',
        'peer_rating',
        'final_score',
        'competency_scores',
        'strengths',
        'areas_for_improvement',
        'recommendation',
        'hike_percentage',
        'status',
    ];

    protected $casts = [
        'self_rating'       => 'decimal:2',
        'manager_rating'    => 'decimal:2',
        'peer_rating'       => 'decimal:2',
        'final_score'       => 'decimal:2',
        'competency_scores' => 'array',
        'hike_percentage'   => 'decimal:1',
    ];

    public function cycle(): BelongsTo
    {
        return $this->belongsTo(AppraisalCycle::class, 'appraisal_cycle_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    /**
     * Compute 30-50-20 composite score.
     */
    public function computeCompositeScore(): float
    {
        $composite = ((float)$this->self_rating * 0.30)
                   + ((float)$this->manager_rating * 0.50)
                   + ((float)$this->peer_rating * 0.20);

        return round($composite, 2);
    }
}

