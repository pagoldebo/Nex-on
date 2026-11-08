<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Class Template
 *
 * Represents an interchangeable storefront brand theme in the Nex-on platform.
 * Governs dynamic view resolution (storefront.{key}.{page}) and active theme caching.
 *
 * @package App\Models
 * @property int $id
 * @property string $key           Unique directory slug (e.g. 'saaya', 'buttercup', 'vyra', 'lumiere', 'organic')
 * @property string $name          User-facing brand theme name (e.g. 'Verdia', 'Lumière')
 * @property string|null $label    Extended brand subtitle or palette description
 * @property string|null $preview_image Preview thumbnail path in storage
 * @property bool $is_active       Flag indicating whether this theme is currently active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Template extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['key', 'name', 'label', 'preview_image', 'is_active'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = ['is_active' => 'boolean'];

    /**
     * Flush the active template from cache.
     *
     * @return void
     */
    public static function clearActiveCache(): void
    {
        Cache::forget('active_template');
    }

    /**
     * The "booted" method of the model.
     * Automatically invalidates template cache upon saving or deleting records.
     *
     * @return void
     */
    protected static function booted(): void
    {
        static::saved(function () {
            self::clearActiveCache();
        });

        static::deleted(function () {
            self::clearActiveCache();
        });
    }

    /**
     * Resolve the currently active storefront template.
     * Retrieves from cache (1-hour TTL) or falls back safely to 'saaya' if unmigrated.
     *
     * @return self
     */
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
