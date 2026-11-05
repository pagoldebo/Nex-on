<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')->constrained()->cascadeOnDelete();

            // --- Live references (nullable so records survive catalogue changes) ---
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('product_color_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('product_variant_id')->nullable()->constrained()->nullOnDelete();

            // --- Snapshot at order time (always populated, never changes) ---
            $table->string('product_name');             // e.g. "Lotus Silk Bra Set"
            $table->string('product_slug');             // for order history deep-links
            $table->string('color_name');               // e.g. "Blush Pink"
            $table->string('color_hex', 7)->nullable(); // e.g. "#E8C4C0"
            $table->string('size');                     // e.g. "M" or "34B"
            $table->string('cover_image')->nullable();  // URL of product image at purchase time

            // --- Pricing (in paise) ---
            $table->unsignedInteger('price');           // unit selling price
            $table->unsignedInteger('compare_at_price')->nullable(); // original price if on sale
            $table->unsignedSmallInteger('qty');
            $table->unsignedInteger('line_total');      // price × qty

            $table->timestamps();

            // Prevent duplicate line items for the same variant in one order
            $table->unique(['order_id', 'product_variant_id'], 'uniq_order_variant');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
