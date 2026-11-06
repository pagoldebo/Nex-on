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
        $categoriesData = [
            ['name' => 'Bralettes', 'slug' => 'bralettes', 'sort_order' => 1],
            ['name' => 'Lingerie Sets', 'slug' => 'lingerie-sets', 'sort_order' => 2],
            ['name' => 'Bodysuits', 'slug' => 'bodysuits', 'sort_order' => 3],
            ['name' => 'Nightwear & Robes', 'slug' => 'nightwear-robes', 'sort_order' => 4],
            ['name' => 'Bridal Edit', 'slug' => 'bridal-edit', 'sort_order' => 5],
            ['name' => 'Everyday Essentials', 'slug' => 'everyday-essentials', 'sort_order' => 6],
        ];

        $categories = [];
        foreach ($categoriesData as $cData) {
            $categories[$cData['slug']] = Category::updateOrCreate(
                ['slug' => $cData['slug']],
                ['name' => $cData['name'], 'sort_order' => $cData['sort_order']]
            );
        }

        $productsData = [
            [
                'name' => 'Zari Bloom Embroidered Bralette',
                'slug' => 'zari-bloom-embroidered-bralette',
                'category_slug' => 'bralettes',
                'description' => 'Delicate floral embroidery on sheer mesh with gold lurex threading and scallop trims.',
                'badge' => 'NEW',
                'is_featured' => true,
                'is_bestseller' => true,
                'colors' => [
                    [
                        'name' => 'Crimson Gold',
                        'hex' => '#9B1D20',
                        'images' => ['products/zari-bloom-crimson.jpg'],
                        'sizes' => [
                            ['size' => 'S', 'price' => 1899, 'compare_at' => 2499, 'stock' => 15],
                            ['size' => 'M', 'price' => 1899, 'compare_at' => 2499, 'stock' => 20],
                            ['size' => 'L', 'price' => 1999, 'compare_at' => 2499, 'stock' => 10],
                        ],
                    ],
                    [
                        'name' => 'Noir Onyx',
                        'hex' => '#1A1A1A',
                        'images' => ['products/zari-bloom-noir.jpg'],
                        'sizes' => [
                            ['size' => 'S', 'price' => 1899, 'compare_at' => 2499, 'stock' => 8],
                            ['size' => 'M', 'price' => 1899, 'compare_at' => 2499, 'stock' => 12],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Blush Velvet Chantilly Lace Set',
                'slug' => 'blush-velvet-chantilly-lace-set',
                'category_slug' => 'lingerie-sets',
                'description' => 'Plush stretch velvet paired with French chantilly lace, featuring rose gold hardware.',
                'badge' => 'BESTSELLER',
                'is_featured' => true,
                'is_bestseller' => true,
                'colors' => [
                    [
                        'name' => 'Blush Rose',
                        'hex' => '#D8A47F',
                        'images' => ['products/blush-velvet-lace.jpg'],
                        'sizes' => [
                            ['size' => 'S', 'price' => 2899, 'compare_at' => 3499, 'stock' => 14],
                            ['size' => 'M', 'price' => 2899, 'compare_at' => 3499, 'stock' => 18],
                            ['size' => 'L', 'price' => 2999, 'compare_at' => 3499, 'stock' => 6],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Noir Underwire Contour Bodysuit',
                'slug' => 'noir-underwire-contour-bodysuit',
                'category_slug' => 'bodysuits',
                'description' => 'Sculpting sheer mesh bodysuit with architectural underwire boning and thong back.',
                'badge' => 'EXCLUSIVE',
                'is_featured' => true,
                'is_bestseller' => false,
                'colors' => [
                    [
                        'name' => 'Midnight Black',
                        'hex' => '#111111',
                        'images' => ['products/noir-contour-bodysuit.jpg'],
                        'sizes' => [
                            ['size' => 'XS', 'price' => 3199, 'compare_at' => 3999, 'stock' => 5],
                            ['size' => 'S', 'price' => 3199, 'compare_at' => 3999, 'stock' => 10],
                            ['size' => 'M', 'price' => 3199, 'compare_at' => 3999, 'stock' => 8],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Champagne Silk Kimono Robe',
                'slug' => 'champagne-silk-kimono-robe',
                'category_slug' => 'nightwear-robes',
                'description' => '100% Mulberry silk lounge robe featuring draped kimono sleeves and matching tie belt.',
                'badge' => 'LUXE',
                'is_featured' => true,
                'is_bestseller' => true,
                'colors' => [
                    [
                        'name' => 'Champagne Gold',
                        'hex' => '#E5D3B3',
                        'images' => ['products/champagne-silk-robe.jpg'],
                        'sizes' => [
                            ['size' => 'Free Size', 'price' => 4499, 'compare_at' => 5499, 'stock' => 25],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Ivory Pearl Bridal Bustier',
                'slug' => 'ivory-pearl-bridal-bustier',
                'category_slug' => 'bridal-edit',
                'description' => 'Heirloom quality bridal corset handcrafted with freshwater seed pearls and silk satin.',
                'badge' => 'BRIDAL',
                'is_featured' => true,
                'is_bestseller' => true,
                'colors' => [
                    [
                        'name' => 'Ivory Pearl',
                        'hex' => '#F8F6F0',
                        'images' => ['products/ivory-pearl-bustier.jpg'],
                        'sizes' => [
                            ['size' => 'S', 'price' => 4999, 'compare_at' => 5999, 'stock' => 6],
                            ['size' => 'M', 'price' => 4999, 'compare_at' => 5999, 'stock' => 8],
                            ['size' => 'L', 'price' => 5199, 'compare_at' => 5999, 'stock' => 4],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Petal Soft Seamless Modal Bralette',
                'slug' => 'petal-soft-seamless-modal-bralette',
                'category_slug' => 'everyday-essentials',
                'description' => 'Second-skin ultra-breathable micro modal with wireless support and clean-cut edges.',
                'badge' => 'DAILY',
                'is_featured' => true,
                'is_bestseller' => true,
                'colors' => [
                    [
                        'name' => 'Dusty Rose',
                        'hex' => '#C48B9F',
                        'images' => ['products/petal-soft-modal.jpg'],
                        'sizes' => [
                            ['size' => 'S', 'price' => 999, 'compare_at' => 1299, 'stock' => 30],
                            ['size' => 'M', 'price' => 999, 'compare_at' => 1299, 'stock' => 45],
                            ['size' => 'L', 'price' => 999, 'compare_at' => 1299, 'stock' => 20],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Scarlet Underwire Satin Corset Top',
                'slug' => 'scarlet-underwire-satin-corset-top',
                'category_slug' => 'bralettes',
                'description' => 'Zara-inspired high-drama sculpted corset top in lustrous scarlet duchess satin with structured underwire cups.',
                'badge' => 'TRENDING',
                'is_featured' => true,
                'is_bestseller' => true,
                'colors' => [
                    [
                        'name' => 'Scarlet Red',
                        'hex' => '#B91C1C',
                        'images' => ['products/scarlet-corset-top.jpg'],
                        'sizes' => [
                            ['size' => 'XS', 'price' => 2499, 'compare_at' => 2999, 'stock' => 12],
                            ['size' => 'S', 'price' => 2499, 'compare_at' => 2999, 'stock' => 16],
                            ['size' => 'M', 'price' => 2499, 'compare_at' => 2999, 'stock' => 14],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Emerald Silk Bias Slip Chemise',
                'slug' => 'emerald-silk-bias-slip-chemise',
                'category_slug' => 'nightwear-robes',
                'description' => 'Lulu & Sky runway favorite in liquid emerald silk charmeuse, cut on the bias for an effortless silhouette.',
                'badge' => 'HOT',
                'is_featured' => true,
                'is_bestseller' => false,
                'colors' => [
                    [
                        'name' => 'Emerald Green',
                        'hex' => '#065F46',
                        'images' => ['products/emerald-satin-chemise.jpg'],
                        'sizes' => [
                            ['size' => 'S', 'price' => 3299, 'compare_at' => 3999, 'stock' => 10],
                            ['size' => 'M', 'price' => 3299, 'compare_at' => 3999, 'stock' => 15],
                            ['size' => 'L', 'price' => 3499, 'compare_at' => 3999, 'stock' => 8],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Lavender Pure Silk Lounge Pajama Set',
                'slug' => 'lavender-pure-silk-lounge-pajama-set',
                'category_slug' => 'nightwear-robes',
                'description' => 'Two-piece tailored lounge set in calming pastel lavender mulberry silk with contrasting white piping.',
                'badge' => 'PREMIUM',
                'is_featured' => false,
                'is_bestseller' => true,
                'colors' => [
                    [
                        'name' => 'Pastel Lavender',
                        'hex' => '#C084FC',
                        'images' => ['products/lavender-silk-pajama.jpg'],
                        'sizes' => [
                            ['size' => 'S', 'price' => 3899, 'compare_at' => 4599, 'stock' => 12],
                            ['size' => 'M', 'price' => 3899, 'compare_at' => 4599, 'stock' => 18],
                            ['size' => 'L', 'price' => 3899, 'compare_at' => 4599, 'stock' => 9],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Midnight Chantilly Lace Babydoll',
                'slug' => 'midnight-chantilly-lace-babydoll',
                'category_slug' => 'lingerie-sets',
                'description' => 'Sensual floating babydoll in sheer midnight navy French lace with matching silk satin briefs.',
                'badge' => 'LUXE',
                'is_featured' => true,
                'is_bestseller' => false,
                'colors' => [
                    [
                        'name' => 'Midnight Navy',
                        'hex' => '#1E1B4B',
                        'images' => ['products/midnight-lace-babydoll.jpg'],
                        'sizes' => [
                            ['size' => 'S', 'price' => 2799, 'compare_at' => 3299, 'stock' => 14],
                            ['size' => 'M', 'price' => 2799, 'compare_at' => 3299, 'stock' => 11],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Pearl White Silk Slip Evening Dress',
                'slug' => 'pearl-white-silk-slip-evening-dress',
                'category_slug' => 'bridal-edit',
                'description' => 'Minimalist modern bridal slip dress in heavyweight mulberry silk with delicate cowl neckline and low back.',
                'badge' => 'BRIDAL',
                'is_featured' => true,
                'is_bestseller' => true,
                'colors' => [
                    [
                        'name' => 'Pearl White',
                        'hex' => '#FDFBF7',
                        'images' => ['products/pearl-white-slip-dress.jpg'],
                        'sizes' => [
                            ['size' => 'XS', 'price' => 5499, 'compare_at' => 6499, 'stock' => 7],
                            ['size' => 'S', 'price' => 5499, 'compare_at' => 6499, 'stock' => 12],
                            ['size' => 'M', 'price' => 5499, 'compare_at' => 6499, 'stock' => 9],
                        ],
                    ],
                ],
            ],
        ];

        foreach ($productsData as $pData) {
            $cat = $categories[$pData['category_slug']] ?? Category::first();
            $product = Product::updateOrCreate(
                ['slug' => $pData['slug']],
                [
                    'category_id'   => $cat->id,
                    'name'          => $pData['name'],
                    'description'   => $pData['description'],
                    'badge'         => $pData['badge'],
                    'is_active'     => true,
                    'is_featured'   => $pData['is_featured'],
                    'is_bestseller' => $pData['is_bestseller'],
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
