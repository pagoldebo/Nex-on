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
        Schema::create('attribute_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attribute_group_id')->nullable()->constrained('attribute_groups')->nullOnDelete();
            $table->string('name');
            $table->string('code')->unique();
            $table->enum('type', ['text', 'select', 'multiselect', 'color', 'boolean'])->default('text');
            $table->json('options')->nullable(); // For select / multiselect options
            $table->boolean('is_filterable')->default(true);
            $table->boolean('is_comparable')->default(true);
            $table->timestamps();
        });

        Schema::create('product_attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('attribute_id')->constrained('attributes')->cascadeOnDelete();
            $table->text('value_text')->nullable();
            $table->decimal('value_number', 12, 4)->nullable();
            $table->json('value_json')->nullable();
            $table->timestamps();

            $table->index(['product_id', 'attribute_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_attribute_values');
        Schema::dropIfExists('attributes');
        Schema::dropIfExists('attribute_groups');
    }
};

