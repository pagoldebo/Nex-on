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
        // 1. Tags table
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 120)->unique();
            $table->string('description', 255)->nullable();
            $table->string('color', 30)->nullable()->default('#4F46E5');
            $table->timestamps();
        });

        // 2. Product-Tag pivot table
        Schema::create('product_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained('tags')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['product_id', 'tag_id']);
        });

        // 3. Seed popular e-commerce tags
        $now = now();
        $initialTags = [
            ['name' => 'Bridal',           'slug' => 'bridal',           'description' => 'Royal bridal & ceremonial collection',       'color' => '#E11D48'],
            ['name' => 'Pure Silk',        'slug' => 'pure-silk',        'description' => 'Pure mulberry & handloom silk pieces',         'color' => '#7C3AED'],
            ['name' => 'Handcrafted',      'slug' => 'handcrafted',      'description' => 'Artisanal handwoven & zari embroidered craft', 'color' => '#D97706'],
            ['name' => 'Festive Edit',     'slug' => 'festive-edit',     'description' => 'Special occasion & festive celebrations',     'color' => '#DC2626'],
            ['name' => 'Bestseller',       'slug' => 'bestseller',       'description' => 'Customer favorite top selling heirloom items', 'color' => '#059669'],
            ['name' => 'New Arrival',      'slug' => 'new-arrival',      'description' => 'Freshly released contemporary couture',        'color' => '#2563EB'],
            ['name' => 'Limited Edition',  'slug' => 'limited-edition',  'description' => 'Exclusive archival small-batch pieces',        'color' => '#4338CA'],
            ['name' => 'Organic Fabric',   'slug' => 'organic-fabric',   'description' => 'Sustainably sourced botanical & natural yarns', 'color' => '#16A34A'],
        ];

        foreach ($initialTags as $tag) {
            DB::table('tags')->insert(array_merge($tag, [
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }

        // 4. Attach sample tags to existing products if any
        $products = DB::table('products')->pluck('id');
        $tagIds = DB::table('tags')->pluck('id')->toArray();
        if ($products->isNotEmpty() && !empty($tagIds)) {
            $pivotData = [];
            foreach ($products as $index => $pid) {
                // Attach 1-3 tags per product
                $assignedTagIds = array_slice($tagIds, $index % count($tagIds), 2);
                if (empty($assignedTagIds)) {
                    $assignedTagIds = [$tagIds[0]];
                }
                foreach ($assignedTagIds as $tid) {
                    $pivotData[] = [
                        'product_id' => $pid,
                        'tag_id'     => $tid,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }
            DB::table('product_tag')->insertOrIgnore($pivotData);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('product_tag');
        Schema::dropIfExists('tags');
    }
};
