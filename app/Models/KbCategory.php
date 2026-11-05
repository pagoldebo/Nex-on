<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class KbCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active'  => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (KbCategory $cat) {
            if (empty($cat->slug)) {
                $cat->slug = Str::slug($cat->name);
            }
        });
    }

    public function articles(): HasMany
    {
        return $this->hasMany(KbArticle::class, 'category_id')->where('is_published', true);
    }

    public function allArticles(): HasMany
    {
        return $this->hasMany(KbArticle::class, 'category_id');
    }
}

