<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Template extends Model
{
    protected $fillable = ['key', 'name', 'label', 'preview_image', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public static function clearActiveCache(): void
    {
        Cache::forget('active_template');
    }

    protected static function booted(): void
    {
        static::saved(function () {
            self::clearActiveCache();
        });

        static::deleted(function () {
            self::clearActiveCache();
        });
    }

    public static function active(): self
    {
        try {
            $template = Cache::remember('active_template', 3600, function () {
                return self::where('is_active', true)->first() ?? self::first();
            });

            if ($template) {
                return $template;
            }
        } catch (\Throwable $e) {
            // Fallback gracefully if database table is not yet migrated
        }

        $fallback = new self();
        $fallback->id = 1;
        $fallback->key = 'saaya';
        $fallback->name = 'Saaya';
        $fallback->label = 'Saaya';
        $fallback->is_active = true;
        return $fallback;
    }

    public function getNameAttribute($value)
    {
        return $value ?? $this->attributes['label'] ?? ucfirst($this->key ?? 'Saaya');
    }

    public function getLabelAttribute($value)
    {
        return $value ?? $this->attributes['name'] ?? ucfirst($this->key ?? 'Saaya');
    }
}
