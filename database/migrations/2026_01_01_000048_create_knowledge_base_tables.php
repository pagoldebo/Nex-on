<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Knowledge Base Categories
        Schema::create('kb_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 120)->unique();
            $table->text('description')->nullable();
            $table->string('icon', 50)->default('📚');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Knowledge Base Articles
        Schema::create('kb_articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('kb_categories')->cascadeOnDelete();
            $table->string('title', 255);
            $table->string('slug', 255)->unique();
            $table->mediumText('content');
            $table->string('summary', 255)->nullable();
            $table->unsignedInteger('views_count')->default(0);
            $table->boolean('is_published')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['category_id', 'is_published']);
        });

        // Seed initial categories
        DB::table('kb_categories')->insert([
            [
                'name'        => 'Shipping & Delivery',
                'slug'        => 'shipping-delivery',
                'description' => 'Delivery timelines, domestic courier tracking, and white-glove doorstep delivery.',
                'icon'        => '📦',
                'sort_order'  => 1,
                'is_active'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => 'Returns & Exchanges',
                'slug'        => 'returns-exchanges',
                'description' => '7-day hassle-free returns policy, exchange process, and refund settlement schedules.',
                'icon'        => '🔄',
                'sort_order'  => 2,
                'is_active'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => 'Bespoke Sizing & Fit',
                'slug'        => 'sizing-fit',
                'description' => 'How to take measurements, international sizing conversions, and custom alterations.',
                'icon'        => '📏',
                'sort_order'  => 3,
                'is_active'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => 'Garment Care & Fabric Maintenance',
                'slug'        => 'garment-care',
                'description' => 'Dry cleaning recommendations, silk and velvet preservation, and embroidery care.',
                'icon'        => '✨',
                'sort_order'  => 4,
                'is_active'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => 'Payments & Security',
                'slug'        => 'payments-security',
                'description' => 'Credit card safety, UPI, EMI installment options, and B2B GST tax invoices.',
                'icon'        => '💳',
                'sort_order'  => 5,
                'is_active'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kb_articles');
        Schema::dropIfExists('kb_categories');
    }
};

