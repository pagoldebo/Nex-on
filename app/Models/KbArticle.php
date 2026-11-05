<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class KbArticle extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'content',
        'summary',
        'views_count',
        'is_published',
        'is_featured',
        'author_id',
    ];

    protected $casts = [
        'views_count'  => 'integer',
        'is_published' => 'boolean',
        'is_featured'  => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (KbArticle $article) {
            if (empty($article->slug)) {
                $base = Str::slug($article->title);
                $article->slug = $base . '-' . Str::random(4);
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(KbCategory::class, 'category_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}

