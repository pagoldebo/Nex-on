<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Class Setting
 *
 * Provides a dynamic, high-performance key-value configuration store for Nex-on.
 * Automatically handles environment-aware caching (10s local, 1h production)
 * and resolves fallback values from config or environment variables.
 *
 * @package App\Models
 * @property int $id
 * @property string $key     Unique setting identifier
 * @property string|null $value   Serialized or string setting content
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Setting extends Model
{
    /**
     * Mass assignable attributes.
     *
     * @var array<int, string>
     */
    protected $fillable = ['key', 'value'];

    /**
     * Get setting value by key with caching and optional fallback default.
     * In local development, short TTL allows direct database changes to take effect immediately.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $ttl = app()->environment('local') ? 10 : 3600;
        try {
            return Cache::remember("setting_{$key}", $ttl, function () use ($key, $default) {
                try {
                    $record = static::where('key', $key)->first();
                    $val = $record !== null && $record->value !== null ? $record->value : $default;
                    if ($key === 'support_email' && ($val === null || in_array(strtolower(trim((string)$val)), ['support@lingerie.store', 'returns@lingeriestore.in', 'concierge@lingeriestore.in'], true))) {
                        return env('SUPPORT_EMAIL') ?: (config('mail.from.address') ?: (env('MAIL_FROM_ADDRESS') ?: ($default ?: 'support@nexon.app')));
                    }
                    return $val;
                } catch (\Throwable $e) {
                    return $default;
                }
            });
        } catch (\Throwable $e) {
            return $default;
        }
    }

    /**
     * Dynamically resolve Store/App Name from Database, Settings Page, or .env APP_NAME
     */
    public static function appName(): string
    {
        return static::get('store_name') 
            ?: (config('app.name') ?: env('APP_NAME', 'Nex-on'));
    }

    /**
     * Dynamically resolve Support Email from .env SUPPORT_EMAIL / MAIL_FROM_ADDRESS, or Settings Page
     */
    public static function supportEmail(): string
    {
        $email = static::get('support_email') 
            ?: static::get('store_email') 
            ?: static::get('mail_from_address');

        if ($email && !in_array(strtolower(trim($email)), [
            'support@lingerie.store',
            'returns@lingeriestore.in',
            'concierge@lingeriestore.in',
        ], true)) {
            return $email;
        }

        return env('SUPPORT_EMAIL') 
            ?: (config('mail.from.address') 
            ?: (env('MAIL_FROM_ADDRESS') ?: 'support@nexon.app'));
    }

    /**
     * Dynamically resolve Support Phone from Database, Settings Page, or .env SUPPORT_PHONE
     */
    public static function supportPhone(): string
    {
        return static::get('support_phone') 
            ?: static::get('store_phone') 
            ?: (env('SUPPORT_PHONE') ?: '+91 98765 43210');
    }

    /**
     * Dynamically resolve Registered Store Address from Database, Settings Page, or .env STORE_ADDRESS
     */
    public static function storeAddress(): string
    {
        return static::get('store_address') 
            ?: (env('STORE_ADDRESS') ?: '101 Fashion Boulevard, Bandra West, Mumbai, Maharashtra 400050');
    }

    /**
     * Dynamically resolve GSTIN from Database, Settings Page, or .env STORE_GSTIN
     */
    public static function gstin(): string
    {
        return static::get('gstin') 
            ?: static::get('store_gstin') 
            ?: (env('STORE_GSTIN') ?: '27AABCS1429B1ZB');
    }

    /**
     * Dynamically resolve Invoice Thank-You Note from Database or dynamic fallback
     */
    public static function invoiceFooter(): string
    {
        return static::get('invoice_footer') 
            ?: ('Thank you for choosing ' . static::appName() . '!');
    }

    /**
     * Set setting value by key and refresh caches.
     */
    public static function set(string $key, mixed $value): static
    {
        Cache::forget("setting_{$key}");
        Cache::forget('all_settings');

        return static::updateOrCreate(
            ['key' => $key],
            ['value' => is_bool($value) ? ($value ? '1' : '0') : (string) $value]
        );
    }

    /**
     * Get all settings as key-value associative array.
     */
    public static function getAll(): array
    {
        return Cache::remember('all_settings', 3600, function () {
            return static::pluck('value', 'key')->toArray();
        });
    }

    /**
     * Resolve a public image URL for any path stored in settings or catalog.
     * Supports external URLs, paths prefixed with 'images/', and direct subdirectories
     * (e.g. 'banners/...', 'products/...', 'branding/...', 'categories/...', 'slider/...').
     */
    public static function imageUrl(?string $path, ?string $fallback = null): ?string
    {
        if (empty($path)) {
            return $fallback;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '//')) {
            return $path;
        }

        $clean = ltrim($path, '/');
        if (str_starts_with($clean, 'images/')) {
            return asset($clean);
        }

        return asset('images/' . $clean);
    }

    /**
     * Get theme image URL with fallback to default asset or placeholder.
     */
    public static function themeImage(string $themeKey, string $section, ?string $fallback = null): ?string
    {
        $path = static::get("theme_{$themeKey}_{$section}") 
             ?: static::get("{$themeKey}_{$section}") 
             ?: ($section === 'hero_image' ? static::get('hero_banner_image') : null);

        return static::imageUrl($path, $fallback);
    }

    /**
     * Get website logo URL with fallback.
     */
    public static function siteLogoUrl(?string $fallback = null): ?string
    {
        $path = static::get('site_logo');

        if (!empty($path)) {
            return static::imageUrl($path, $fallback);
        }

        if (file_exists(public_path('images/branding/logo.png'))) {
            return asset('images/branding/logo.png');
        }

        return $fallback;
    }

    /**
     * Clear all setting caches.
     */
    public static function clearCache(): void
    {
        Cache::forget('all_settings');
        foreach (static::all() as $setting) {
            Cache::forget("setting_{$setting->key}");
        }
    }
}
