<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Attribute extends Model
{
    use HasFactory;

    protected $fillable = [
        'attribute_group_id',
        'name',
        'code',
        'type',
        'options',
        'is_filterable',
        'is_comparable',
    ];

    protected $casts = [
        'options'       => 'array',
        'is_filterable' => 'boolean',
        'is_comparable' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($attribute) {
            if (empty($attribute->code)) {
                $attribute->code = Str::slug($attribute->name, '_');
            }
        });
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(AttributeGroup::class, 'attribute_group_id');
    }

    public function values(): HasMany
    {
        return $this->hasMany(ProductAttributeValue::class);
    }
}

