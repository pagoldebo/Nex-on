<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CustomPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content_blocks',
        'meta_title',
        'meta_description',
        'is_published',
    ];

    protected $casts = [
        'content_blocks' => 'array',
        'is_published'   => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($page) {
            if (empty($page->slug)) {
                $page->slug = Str::slug($page->title) . '-' . Str::random(5);
            }
        });
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function blocksCount(): int
    {
        return is_array($this->content_blocks) ? count($this->content_blocks) : 0;
    }
}
