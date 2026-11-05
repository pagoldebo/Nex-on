<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductImageService
{
    /**
     * Store an uploaded product image inside the product's main category directory,
     * converting it to WebP format and naming it {product_name}-{base_sku}-{6_random_alnum}.webp.
     */
    public static function store(UploadedFile $file, Product $product): string
    {
        $categoryFolder = static::getMainCategoryFolder($product);
        $fileName = static::generateWebpFilename($product, $categoryFolder);
        $targetPath = "products/{$categoryFolder}/{$fileName}";

        static::saveAsWebp($file, $targetPath);

        return $targetPath;
    }

    /**
     * Resolve the main (top-level root) category folder name for a product.
     */
    public static function getMainCategoryFolder(Product $product): string
    {
        $category = $product->category ?: ($product->category_id ? Category::find($product->category_id) : null);
        if (!$category) {
            return 'general';
        }

        $breadcrumbs = $category->getBreadcrumbs();
        $mainCategory = !empty($breadcrumbs) ? $breadcrumbs[0] : $category;

        $folder = Str::slug($mainCategory->name) ?: ($mainCategory->slug ?: 'general');

        return !empty($folder) ? $folder : 'general';
    }

    /**
     * Generate a unique WebP filename: {product_name}-{base_sku}-{6_digit_random}.webp.
     */
    public static function generateWebpFilename(Product $product, string $categoryFolder): string
    {
        $productSlug = Str::slug($product->name) ?: 'product';
        $skuSlug = Str::slug($product->sku ?: 'sku') ?: 'sku';

        do {
            $random6 = Str::lower(Str::random(6));
            $fileName = "{$productSlug}-{$skuSlug}-{$random6}.webp";
            $relativePath = "products/{$categoryFolder}/{$fileName}";
        } while (Storage::disk('public')->exists($relativePath));

        return $fileName;
    }

    /**
     * Save an uploaded file as a WebP image to the specified disk path.
     */
    public static function saveAsWebp(UploadedFile $file, string $targetPath): void
    {
        $raw = file_get_contents($file->getRealPath());
        if ($raw === false) {
            return;
        }

        $mime = $file->getMimeType();
        $ext = strtolower($file->getClientOriginalExtension());
        $isAlreadyWebp = ($mime === 'image/webp' || $ext === 'webp');

        static::saveRawAsWebp($raw, $targetPath, $isAlreadyWebp);
    }

    /**
     * Save raw image bytes as WebP to the specified disk path.
     */
    public static function saveRawAsWebp(string $rawContent, string $targetPath, bool $isAlreadyWebp = false): void
    {
        if ($isAlreadyWebp) {
            Storage::disk('public')->put($targetPath, $rawContent);
            return;
        }

        if (function_exists('imagecreatefromstring') && function_exists('imagewebp')) {
            $image = @imagecreatefromstring($rawContent);
            if ($image !== false) {
                imagepalettetotruecolor($image);
                imagealphablending($image, true);
                imagesavealpha($image, true);

                ob_start();
                imagewebp($image, null, 85);
                $webpBytes = ob_get_clean();

                if ($webpBytes !== false && strlen($webpBytes) > 0) {
                    Storage::disk('public')->put($targetPath, $webpBytes);
                    return;
                }
            }
        }

        Storage::disk('public')->put($targetPath, $rawContent);
    }
}

