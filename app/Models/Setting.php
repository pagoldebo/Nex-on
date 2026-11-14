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
     * Keys containing sensitive third-party tokens and secrets that MUST be encrypted at rest.
     *
     * @var array<int, string>
     */
    protected static array $encryptedKeys = [
        'gateway_razorpay_key_secret',
        'gateway_razorpay_webhook_secret',
        'gateway_stripe_secret_key',
        'gateway_stripe_webhook_secret',
        'gateway_phonepe_salt_key',
        'gateway_paypal_secret',
        'sms_twilio_token',
        'sms_msg91_authkey',
        'sms_fast2sms_api_key',
    ];

    public static function isEncryptedKey(string $key): bool
    {
        return in_array($key, static::$encryptedKeys, true);
    }

    /**
     * Get setting value by key with caching and optional fallback default.
     * In local development, short TTL allows direct database changes to take effect immediately.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        if ($key === 'support_email' || $key === 'organic_support_email') {
            return static::supportEmail();
        }

        $ttl = app()->environment('local') ? 10 : 3600;
        try {
            return Cache::remember("setting_{$key}", $ttl, function () use ($key, $default) {
                try {
                    $record = static::where('key', $key)->first();
                    $raw = ($record !== null && $record->value !== null) ? $record->value : $default;
                    if ($raw !== null && static::isEncryptedKey($key) && is_string($raw) && $raw !== '') {
                        try {
                            return \Illuminate\Support\Facades\Crypt::decryptString($raw);
                        } catch (\Throwable $e) {
                            return $raw; // Fallback to raw string if not encrypted yet
                        }
                    }
                    return $raw;
                } catch (\Throwable $e) {
                    return $default;
                }
            });
        } catch (\Throwable $e) {
            return $default;
        }
    }

    /**
     * Dynamically resolve Store/App Name from Database, Settings Page, or config
     */
    public static function appName(): string
    {
        return static::get('store_name') 
            ?: config('app.name', 'Nex-on');
    }

    /**
     * Dynamically resolve Support Email from Config or Fallback (L-01).
     */
    public static function supportEmail(): string
    {
        return (string) (config('app.support_email') ?: config('mail.from.address', 'support@nexon.app'));
    }

    /**
     * Dynamically resolve Support Phone from Database, Settings, or Config.
     */
    public static function supportPhone(): string
    {
        return static::get('support_phone') 
            ?: (static::get('store_phone') 
            ?: config('app.support_phone', '+91 98765 43210'));
    }

    /**
     * Dynamically resolve Registered Store Address from Database, Settings, or Config.
     */
    public static function storeAddress(): string
    {
        return static::get('store_address') 
            ?: config('app.store_address', '101 Fashion Boulevard, Bandra West, Mumbai, Maharashtra 400050');
    }

    /**
     * Dynamically resolve GSTIN from Database, Settings, or Config.
     */
    public static function gstin(): string
    {
        return static::get('gstin') 
            ?: (static::get('store_gstin') 
            ?: config('app.store_gstin', '27AABCS1429B1ZB'));
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

        $valStr = is_bool($value) ? ($value ? '1' : '0') : (string) $value;

        if (static::isEncryptedKey($key) && $valStr !== '') {
            try {
                $valStr = \Illuminate\Support\Facades\Crypt::encryptString($valStr);
            } catch (\Throwable $e) {
                // If encryption fails (e.g., in test without app key), store raw
            }
        }

        return static::updateOrCreate(
            ['key' => $key],
            ['value' => $valStr]
        );
    }

    /**
     * Get all settings as key-value associative array.
     */
    public static function getAll(): array
    {
        return Cache::remember('all_settings', 3600, function () {
            $settings = static::pluck('value', 'key')->toArray();
            foreach (static::$encryptedKeys as $encKey) {
                if (isset($settings[$encKey]) && is_string($settings[$encKey]) && $settings[$encKey] !== '') {
                    try {
                        $settings[$encKey] = \Illuminate\Support\Facades\Crypt::decryptString($settings[$encKey]);
                    } catch (\Throwable $e) {
                        // ignore and leave raw if not encrypted yet
                    }
                }
            }
            return $settings;
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
