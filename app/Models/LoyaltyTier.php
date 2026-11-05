<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class LoyaltyTier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'min_spend',
        'points_multiplier',
        'perks',
        'badge_color',
        'is_active',
    ];

    protected $casts = [
        'min_spend'         => 'decimal:2',
        'points_multiplier' => 'decimal:2',
        'is_active'         => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (LoyaltyTier $tier) {
            if (empty($tier->slug)) {
                $tier->slug = Str::slug($tier->name);
            }
        });
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(LoyaltyAccount::class);
    }
}

