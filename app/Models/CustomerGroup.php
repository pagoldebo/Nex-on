<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class CustomerGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'discount_type',
        'discount_value',
        'min_spend',
        'is_active',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'min_spend'      => 'decimal:2',
        'is_active'      => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($group) {
            if (empty($group->code)) {
                $group->code = Str::slug($group->name);
            }
        });
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'customer_group_user')
            ->withPivot('assigned_at');
    }

    /**
     * Calculate discount amount for a given subtotal.
     */
    public function calculateDiscount(float $subtotal): float
    {
        if (! $this->is_active || $subtotal < (float) $this->min_spend) {
            return 0.0;
        }

        if ($this->discount_type === 'percentage') {
            return round(($subtotal * (float) $this->discount_value) / 100, 2);
        }

        // Fixed discount cannot exceed subtotal
        return min((float) $this->discount_value, $subtotal);
    }
}
