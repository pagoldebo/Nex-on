<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_colors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('name');           // "Red", "Blue", "Maroon"
            $table->string('hex')->nullable(); // "#DC2626" — for swatch rendering
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            // A product shouldn't have the same color name twice
            $table->unique(['product_id', 'name']);
        });

        // Up to 8 images per color. Separate table (not a JSON column) so each
        // image has its own sort order and can be added/removed/reordered cleanly.
        Schema::create('product_color_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_color_id')->constrained()->cascadeOnDelete();
            $table->string('image_path');
            $table->unsignedTinyInteger('sort_order')->default(0); // 0 = main/cover image
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_color_images');
        Schema::dropIfExists('product_colors');
    }
};
