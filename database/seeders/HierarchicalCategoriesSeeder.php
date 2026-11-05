<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class HierarchicalCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. MEN HIERARCHY
        $men = Category::firstOrCreate(
            ['slug' => 'men'],
            ['name' => 'Men', 'sort_order' => 10, 'parent_id' => null]
        );

        $menSubs = [
            'Shirt' => [
                'Formal Shirt',
                'Casual Shirt',
                'Linen Kurta Shirt',
            ],
            'Pants' => [
                'Formal Trousers',
                'Chino Pants',
                'Denim Jeans',
            ],
            'Dhuti' => [
                'Silk Dhuti',
                'Cotton Dhuti',
            ],
            'Brief' => [
                'Boxer Briefs',
                'Classic Briefs',
                'Trunks',
            ],
        ];

        $menSort = 1;
        foreach ($menSubs as $subName => $grandChildren) {
            $subSlug = 'men-' . Str::slug($subName);
            $subCat = Category::firstOrCreate(
                ['slug' => $subSlug],
                ['name' => $subName, 'sort_order' => $menSort++, 'parent_id' => $men->id]
            );

            $subSubSort = 1;
            foreach ($grandChildren as $grandName) {
                $grandSlug = $subSlug . '-' . Str::slug($grandName);
                Category::firstOrCreate(
                    ['slug' => $grandSlug],
                    ['name' => $grandName, 'sort_order' => $subSubSort++, 'parent_id' => $subCat->id]
                );
            }
        }

        // 2. WOMEN HIERARCHY
        $women = Category::firstOrCreate(
            ['slug' => 'women'],
            ['name' => 'Women', 'sort_order' => 20, 'parent_id' => null]
        );

        $womenSubs = [
            'T-shirt' => [
                'Oversized Tees',
                'Crop Tops',
            ],
            'Bra' => [
                'T-Shirt Bra',
                'Strapless Bra',
                'Push-Up Bra',
            ],
            'Panties' => [
                'Seamless Hipsters',
                'Bikini Briefs',
                'Lace Thongs',
            ],
            'Bralettes' => [
                'Triangle Lace',
                'Longline Bralette',
            ],
            'Saree' => [
                'Chiffon Saree',
                'Kanjivaram Silk',
            ],
            'Blouse' => [
                'Padded Blouse',
                'Embroidered Blouse',
            ],
        ];

        $womenSort = 1;
        foreach ($womenSubs as $subName => $grandChildren) {
            $subSlug = 'women-' . Str::slug($subName);
            $subCat = Category::firstOrCreate(
                ['slug' => $subSlug],
                ['name' => $subName, 'sort_order' => $womenSort++, 'parent_id' => $women->id]
            );

            $subSubSort = 1;
            foreach ($grandChildren as $grandName) {
                $grandSlug = $subSlug . '-' . Str::slug($grandName);
                Category::firstOrCreate(
                    ['slug' => $grandSlug],
                    ['name' => $grandName, 'sort_order' => $subSubSort++, 'parent_id' => $subCat->id]
                );
            }
        }

        // If existing top-level Bralettes exists without parent, we can leave or link to Women
        $existingBralettes = Category::where('slug', 'bralettes')->whereNull('parent_id')->first();
        if ($existingBralettes) {
            // keep existing or let it be
        }
    }
}
