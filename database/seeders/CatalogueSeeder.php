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
                'name' => 'Ratri Saree',
                'slug' => 'ratri-saree',
                'sku' => 'SARE-RATR-QBL7',
                'category_slug' => 'nightwear-robes',
                'description' => '<p><strong>Fabric:</strong> Handwoven tussar by cotton &nbsp;|&nbsp; <strong>Blouse Piece:</strong> No &nbsp;|&nbsp; <strong>Colour:</strong> Black</p><p>An ode to the magic of Nobomi night. Crafted in deep black handwoven Tussar-by-Cotton, carrying delicate embroidered motifs across its body, finished with intricate lace edging and a statement embroidered pallu.</p>',
                'long_description' => '<div class="product-highlights mb-6"><h3 style="font-family: Georgia, serif; color: #1a1a1a; font-size: 1.5rem; margin-bottom: 1rem; border-left: 4px solid #d4af37; padding-left: 12px; line-height: 1.2;">Product Highlights</h3><ul style="list-style: none; padding: 0; margin: 0;" class="space-y-2 text-sm text-gray-700"><li style="display: flex; justify-content: space-between; padding-bottom: 8px; border-bottom: 1px solid #e5e7eb;"><span style="color: #6b7280; font-weight: 500;">Works:</span><span style="font-weight: 600; color: #111827;">Embroidery</span></li><li style="display: flex; justify-content: space-between; padding-bottom: 8px; border-bottom: 1px solid #e5e7eb;"><span style="color: #6b7280; font-weight: 500;">Fabric:</span><span style="font-weight: 600; color: #111827;">Handwoven tussar by cotton</span></li><li style="display: flex; justify-content: space-between; padding-bottom: 8px; border-bottom: 1px solid #e5e7eb;"><span style="color: #6b7280; font-weight: 500;">Saree Length:</span><span style="font-weight: 600; color: #111827;">5.5 meters</span></li><li style="display: flex; justify-content: space-between; padding-bottom: 8px; border-bottom: 1px solid #e5e7eb;"><span style="color: #6b7280; font-weight: 500;">Blouse Piece:</span><span style="font-weight: 600; color: #111827;">No</span></li><li style="display: flex; justify-content: space-between; padding-bottom: 8px; border-bottom: 1px solid #e5e7eb;"><span style="color: #6b7280; font-weight: 500;">Colour:</span><span style="font-weight: 600; color: #111827;">Black</span></li></ul></div><div class="story-box mb-6" style="margin-top: 1.5rem;"><h3 style="font-family: Georgia, serif; color: #1a1a1a; font-size: 1.5rem; margin-bottom: 1rem; border-left: 4px solid #d4af37; padding-left: 12px; line-height: 1.2;">Story</h3><div style="padding-left: 4px;"><p style="font-weight: 700; color: #990000; font-size: 1.1rem; margin-bottom: 0.75rem;">RATRI | রাত্রী</p><p style="margin-bottom: 0.75rem; line-height: 1.7; color: #374151;">There’s something about <strong style="color: #990000;">Nobomi night</strong>—the lights, the quiet after the chaos, the last few hours of Pujo that feel almost magical.</p><p style="margin-bottom: 0.75rem; line-height: 1.7; color: #374151;"><strong style="color: #990000;">RATRI</strong> is an ode to that night.</p><p style="margin-bottom: 0.75rem; line-height: 1.7; color: #374151;">Crafted in a beautiful handwoven Tussar-by-Cotton, this deep black saree carries delicate embroidered <span style="color: #990000; font-weight: 600;">motifs</span> across its body, finished with intricate lace edging and a statement embroidered pallu.</p><p style="margin-bottom: 0.75rem; line-height: 1.7; color: #374151;">Understated yet dramatic, traditional yet effortlessly contemporary—<strong style="color: #990000;">RATRI</strong> is made for the woman who lets the night do the talking.</p><p style="margin-top: 1rem; font-style: italic; color: #990000; font-weight: 600;">RATRI — An ode to the night. A final chapter of Pujo.</p></div></div><div class="care-and-disclaimer" style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid #e5e7eb;"><div style="margin-bottom: 1rem;"><span style="font-size: 0.95rem; font-weight: 700; color: #111827; display: block; margin-bottom: 0.25rem;">Wash &amp; Care:</span><p style="font-size: 0.875rem; color: #4b5563; margin: 0; line-height: 1.5;">Dry clean only recommended to maintain the longevity of the fabric.</p></div><div style="margin-bottom: 1.25rem;"><span style="font-size: 0.95rem; font-weight: 700; color: #111827; display: block; margin-bottom: 0.25rem;">Production Time:</span><p style="font-size: 0.875rem; color: #4b5563; margin: 0; line-height: 1.5;">Handcrafted artisanal saree. Please allow 10-12 days for production.</p></div><div style="background-color: #fff5f5; border-radius: 8px; padding: 14px; border-left: 4px solid #e53e3e;"><span style="font-size: 0.9rem; font-weight: 700; display: block; margin-bottom: 6px; color: #c53030;">Disclaimer:</span><ul style="font-size: 0.825rem; color: #4a5568; margin: 0; padding-left: 18px; line-height: 1.6;"><li>Photos are captured in natural light to avoid discrepancies; minor shade variations may occur due to individual screen calibration.</li><li>For bespoke customizations, please contact our concierge team. Customized items cannot be returned or exchanged.</li></ul></div></div>',
                'badge' => 'HOT',
                'is_featured' => true,
                'is_bestseller' => false,
                'colors' => [
                    [
                        'name' => 'Black',
                        'hex' => '#000000',
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
                    'category_id'      => $cat->id,
                    'name'             => $pData['name'],
                    'sku'              => $pData['sku'] ?? null,
                    'description'      => $pData['description'],
                    'long_description' => $pData['long_description'] ?? null,
                    'badge'            => $pData['badge'],
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
