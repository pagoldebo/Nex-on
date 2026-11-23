<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductColorImage;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CatalogueSeeder extends Seeder
{
    public function run(): void
    {
        $categoriesData = [];

        $categories = [];
        foreach ($categoriesData as $cData) {
            $categories[$cData['slug']] = Category::updateOrCreate(
                ['slug' => $cData['slug']],
                ['name' => $cData['name'], 'sort_order' => $cData['sort_order']]
            );
        }

        $productsData = [];

        foreach ($productsData as $pData) {
            $cat = $categories[$pData['category_slug']] ?? Category::first();
            $product = Product::updateOrCreate(
                ['slug' => $pData['slug']],
                [
                    'category_id'      => $cat->id,
                    'name'             => $pData['name'],
                    'sku'              => $pData['sku'] ?? null,
                    'description'      => $pData['description'],
                    'long_description' => $pData['long_description'] ?? null,
                    'badge'            => $pData['badge'],
                    'is_active'        => true,
                    'is_featured'      => $pData['is_featured'],
                    'is_bestseller'    => $pData['is_bestseller'],
                ]
            );

            foreach ($pData['colors'] as $cIndex => $colorData) {
                $color = ProductColor::updateOrCreate(
                    ['product_id' => $product->id, 'name' => $colorData['name']],
                    ['hex' => $colorData['hex'], 'sort_order' => $cIndex]
                );

                // Clear old external URLs if any
                $color->images()->delete();

                foreach ($colorData['images'] as $imgIndex => $imgPath) {
                    ProductColorImage::create([
                        'product_color_id' => $color->id,
                        'image_path'       => $imgPath,
                        'sort_order'       => $imgIndex,
                    ]);
                }

                foreach ($colorData['sizes'] as $sizeData) {
                    $cCode = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $color->name), 0, 3));
                    $sku = $product->sku . '-' . $cCode . '-' . strtoupper($sizeData['size']);

                    ProductVariant::updateOrCreate(
                        ['product_color_id' => $color->id, 'size' => $sizeData['size']],
                        [
                            'product_id'       => $product->id,
                            'sku'              => $sku,
                            'price'            => $sizeData['price'],
                            'compare_at_price' => $sizeData['compare_at'],
                            'stock'            => $sizeData['stock'],
                        ]
                    );
                }
            }
        }

        $this->command->info('Catalogue seeded: ' . count($productsData) . ' multi-variant products with local storage images.');
    }
}
