<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Brands table
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('logo')->nullable();
            $table->string('website')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // 2. Add brand_id to products
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('brand_id')->nullable()->after('category_id')->constrained('brands')->nullOnDelete();
        });

        // 3. Addon-Product pivot table
        Schema::create('addon_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('addon_id')->constrained('addons')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['product_id', 'addon_id']);
        });

        // 4. Seed initial curated artisan brands
        $now = now();
        $initialBrands = [
            ['name' => 'Nex-on Signature', 'slug' => 'nexon-signature', 'description' => 'Flagship couture & signature collection.', 'is_active' => true, 'sort_order' => 1],
            ['name' => 'Sabyasachi Atelier', 'slug' => 'sabyasachi-atelier', 'description' => 'Opulent royal bridal lehengas, sarees, and heirloom ensembles.', 'is_active' => true, 'sort_order' => 2],
            ['name' => 'Raw Mango', 'slug' => 'raw-mango', 'description' => 'Contemporary handwoven Chanderi, Banarasi, and Mashru silks.', 'is_active' => true, 'sort_order' => 3],
            ['name' => 'Bespoke Heritage', 'slug' => 'bespoke-heritage', 'description' => 'Handcrafted pure tussar, linen, and artisanal handloom fabrics.', 'is_active' => true, 'sort_order' => 4],
            ['name' => 'Vedic Organics', 'slug' => 'vedic-organics', 'description' => 'Pure organic produce, botanicals, and farm-fresh natural wellness.', 'is_active' => true, 'sort_order' => 5],
        ];

        foreach ($initialBrands as $b) {
            DB::table('brands')->insert(array_merge($b, [
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('addon_product');

        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['brand_id']);
            $table->dropColumn('brand_id');
        });

        Schema::dropIfExists('brands');
    }
};
