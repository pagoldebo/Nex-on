<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Log;

class PwaManifestService
{
    /**
     * Get dynamic Web App Manifest data.
     */
    public static function getManifestData(): array
    {
        $storeName = Setting::get('store_name');
        if (empty($storeName) || $storeName === 'Nexon') {
            $storeName = config('app.name', 'Nex-on');
        }

        $tagline = Setting::get('store_tagline');
        $desc = Setting::get('store_meta_description');
        $description = $tagline ?: ($desc ?: 'Shop fashion, apparel, and signature collections at ' . $storeName);

        return [
            'name' => $storeName . ' – Online Store',
            'short_name' => $storeName,
            'description' => $description,
            'start_url' => '/',
            'scope' => '/',
            'display' => 'standalone',
            'background_color' => '#fdfbf7',
            'theme_color' => '#7f1d1d',
            'orientation' => 'portrait-primary',
            'icons' => [
                [
                    'src' => '/icons/icon-192x192.png',
                    'sizes' => '192x192',
                    'type' => 'image/png',
                    'purpose' => 'any maskable',
                ],
                [
                    'src' => '/icons/icon-512x512.png',
                    'sizes' => '512x512',
                    'type' => 'image/png',
                    'purpose' => 'any maskable',
                ],
                [
                    'src' => 'images/branding/logo.png',
                    'sizes' => '192x192',
                    'type' => 'image/png',
                    'purpose' => 'any maskable',
                ],
                [
                    'src' => 'images/branding/logo.png',
                    'sizes' => '512x512',
                    'type' => 'image/png',
                    'purpose' => 'any maskable',
                ],
            ],
            'categories' => ['shopping', 'lifestyle'],
            'prefer_related_applications' => false,
        ];
    }

    /**
     * Generate square PWA icons (192x192 and 512x512) from the uploaded admin logo.
     */
    public static function generateIcons(?string $sourcePath = null): bool
    {
        if (!function_exists('imagecreatefromstring')) {
            return false;
        }

        $logoPath = null;
        if ($sourcePath && file_exists($sourcePath)) {
            $logoPath = $sourcePath;
        } else {
            $siteLogo = Setting::get('site_logo');
            if ($siteLogo) {
                $candidates = [
                    public_path('images/' . ltrim($siteLogo, '/')),
                    public_path(ltrim($siteLogo, '/')),
                    storage_path('app/public/' . ltrim($siteLogo, '/')),
                ];
                foreach ($candidates as $cand) {
                    if (file_exists($cand) && !is_dir($cand)) {
                        $logoPath = $cand;
                        break;
                    }
                }
            }
            if (!$logoPath && file_exists(public_path('images/branding/logo.png'))) {
                $logoPath = public_path('images/branding/logo.png');
            }
        }

        if (!$logoPath || !file_exists($logoPath)) {
            return false;
        }

        $raw = @file_get_contents($logoPath);
        if (!$raw) {
            return false;
        }

        $src = @imagecreatefromstring($raw);
        if (!$src) {
            return false;
        }

        $srcW = imagesx($src);
        $srcH = imagesy($src);
        if ($srcW <= 0 || $srcH <= 0) {
            imagedestroy($src);
            return false;
        }

        $iconsDir = public_path('icons');
        if (!is_dir($iconsDir)) {
            @mkdir($iconsDir, 0755, true);
        }

        foreach ([192, 512] as $size) {
            $dest = imagecreatetruecolor($size, $size);
            imagealphablending($dest, false);
            imagesavealpha($dest, true);
            $transparent = imagecolorallocatealpha($dest, 0, 0, 0, 127);
            imagefilledrectangle($dest, 0, 0, $size, $size, $transparent);
            imagealphablending($dest, true);

            // 8% inner padding so icon borders don't touch round app mask corners
            $pad = (int)($size * 0.08);
            $maxW = $size - ($pad * 2);
            $maxH = $size - ($pad * 2);
            $ratio = min($maxW / $srcW, $maxH / $srcH);
            $newW = (int)($srcW * $ratio);
            $newH = (int)($srcH * $ratio);
            $dstX = (int)(($size - $newW) / 2);
            $dstY = (int)(($size - $newH) / 2);

            imagecopyresampled($dest, $src, $dstX, $dstY, 0, 0, $newW, $newH, $srcW, $srcH);

            $outPath = $iconsDir . "/icon-{$size}x{$size}.png";
            imagepng($dest, $outPath, 9);
            if (PHP_VERSION_ID < 80500) {
                @imagedestroy($dest);
            }
        }

        if (PHP_VERSION_ID < 80500) {
            @imagedestroy($src);
        }
        return true;
    }

    /**
     * Synchronize public/manifest.json file and PWA icons.
     */
    public static function sync(?string $sourceLogoPath = null): void
    {
        try {
            self::generateIcons($sourceLogoPath);

            $manifestData = self::getManifestData();
            $manifestPath = public_path('manifest.json');
            @file_put_contents(
                $manifestPath,
                json_encode($manifestData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
            );
        } catch (\Throwable $e) {
            Log::warning('Failed to sync PWA manifest: ' . $e->getMessage());
        }
    }
}
