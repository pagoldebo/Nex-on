<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_variant_id')->constrained('product_variants')->cascadeOnDelete();
            $table->string('batch_number', 50)->index();
            $table->string('lot_number', 50)->nullable()->index();
            $table->date('mfg_date')->nullable();
            $table->date('expiry_date')->index();
            $table->unsignedInteger('initial_qty');
            $table->unsignedInteger('remaining_qty')->default(0)->index();
            $table->decimal('cost_price', 10, 2)->nullable();
            $table->string('status', 20)->default('active')->index(); // active, depleted, expired, recalled
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('order_item_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_item_id')->constrained('order_items')->cascadeOnDelete();
            $table->foreignId('product_batch_id')->constrained('product_batches')->cascadeOnDelete();
            $table->unsignedInteger('qty_allocated');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_item_batches');
        Schema::dropIfExists('product_batches');
    }
};
