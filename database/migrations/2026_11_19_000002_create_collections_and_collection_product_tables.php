<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('collections')) {
            Schema::create('collections', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->string('image')->nullable();
                $table->boolean('is_active')->default(true)->index();
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('collection_product')) {
            Schema::create('collection_product', function (Blueprint $table) {
                $table->id();
                $table->foreignId('collection_id')->constrained('collections')->cascadeOnDelete();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['collection_id', 'product_id'], 'col_prod_unique');
            });
        }

        // Seed initial curated collections if table is empty
        if (DB::table('collections')->count() === 0) {
            $defaultCollections = [
                [
                    'name'        => 'Royal Bridal Trousseau',
                    'slug'        => 'royal-bridal-trousseau',
                    'description' => 'Handcrafted pure silk and antique zari embroidery heirlooms tailored for sacred wedding celebrations.',
                    'sort_order'  => 1,
                    'is_active'   => true,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ],
                [
                    'name'        => 'Festive Velvet Edit',
                    'slug'        => 'festive-velvet-edit',
                    'description' => 'Opulent crimson and emerald velvet sets tailored for celebrations and grand evenings.',
                    'sort_order'  => 2,
                    'is_active'   => true,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ],
                [
                    'name'        => 'Benarasi Heritage Archive',
                    'slug'        => 'benarasi-heritage-archive',
                    'description' => 'Timeless handloom motifs woven with pure gold and silver wire by royal artisan clusters.',
                    'sort_order'  => 3,
                    'is_active'   => true,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ],
                [
                    'name'        => 'Everyday Luxury Silks',
                    'slug'        => 'everyday-luxury-silks',
                    'description' => 'Weightless mulberry silk satin essentials crafted for effortless daily comfort and grace.',
                    'sort_order'  => 4,
                    'is_active'   => true,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ],
            ];

            DB::table('collections')->insert($defaultCollections);

            // Link existing active products to sample collections
            $collectionIds = DB::table('collections')->pluck('id')->toArray();
            $productIds = DB::table('products')->where('is_active', true)->pluck('id')->toArray();

            if (!empty($collectionIds) && !empty($productIds)) {
                $pivotData = [];
                foreach ($productIds as $pId) {
                    // Assign 1 or 2 collections to each product deterministically
                    $assignedCol = $collectionIds[$pId % count($collectionIds)];
                    $pivotData[] = [
                        'collection_id' => $assignedCol,
                        'product_id'    => $pId,
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ];

                    if ($pId % 2 === 0 && count($collectionIds) > 1) {
                        $secondCol = $collectionIds[($pId + 1) % count($collectionIds)];
                        if ($secondCol !== $assignedCol) {
                            $pivotData[] = [
                                'collection_id' => $secondCol,
                                'product_id'    => $pId,
                                'created_at'    => now(),
                                'updated_at'    => now(),
                            ];
                        }
                    }
                }

                if (!empty($pivotData)) {
                    DB::table('collection_product')->insertOrIgnore($pivotData);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collection_product');
        Schema::dropIfExists('collections');
    }
};
