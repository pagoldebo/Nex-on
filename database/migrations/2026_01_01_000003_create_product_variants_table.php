<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // This is the actual sellable SKU: one specific color + one specific size.
        // Price and stock live HERE, not on the product and not on the color,
        // because "Red + S" and "Red + L" can have different prices.
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_color_id')->constrained()->cascadeOnDelete();
            $table->string('size');               // "S", "M", "L", "XL"
            $table->string('sku')->unique()->nullable(); // optional human SKU code
            $table->decimal('price', 10, 2);
            $table->decimal('compare_at_price', 10, 2)->nullable();
            $table->unsignedInteger('stock')->default(0);
            $table->timestamps();

            // Prevents creating "Red + S" twice for the same product.
            // This is what makes combinations sparse/optional — you only
            // insert the rows that actually exist.
            $table->unique(['product_color_id', 'size'], 'unique_color_size');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
