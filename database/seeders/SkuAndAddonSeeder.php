<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Addon;

class SkuAndAddonSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::whereNull('sku')->orWhere('sku', '')->get();
        foreach ($products as $p) {
            $code = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $p->name), 0, 3));
            $p->sku = 'LS-' . ($code ?: 'PRD') . '-' . str_pad($p->id, 3, '0', STR_PAD_LEFT);
            $p->save();

            foreach ($p->variants()->with('color')->get() as $v) {
                if (empty($v->sku)) {
                    $cName = $v->color ? strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $v->color->name), 0, 3)) : 'STD';
                    $v->sku = $p->sku . '-' . $cName . '-' . strtoupper($v->size);
                    $v->save();
                }
            }
        }

        if (Addon::count() === 0) {
            Addon::create([
                'name'        => 'Discreet Luxury Box',
                'description' => 'Plain exterior shipping box with silk tissue wrapping inside for total privacy.',
                'price'       => 49.00,
                'is_active'   => true,
            ]);
            Addon::create([
                'name'        => 'Signature Satin Gift Bag',
                'description' => 'Reusable champagne gold satin pouch with woven ribbon.',
                'price'       => 129.00,
                'is_active'   => true,
            ]);
            Addon::create([
                'name'        => 'Handwritten Love Note',
                'description' => 'Custom handwritten greeting card with your personalized message.',
                'price'       => 29.00,
                'is_active'   => true,
            ]);
        }
    }
}
