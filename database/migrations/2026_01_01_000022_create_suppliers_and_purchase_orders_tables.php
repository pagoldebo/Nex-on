<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Suppliers Table
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('code', 50)->unique();
            $table->string('contact_name', 120)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('phone', 30)->nullable();
            $table->unsignedInteger('lead_time_days')->default(7);
            $table->string('gst_number', 30)->nullable();
            $table->text('address')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Add Reorder thresholds & Supplier FK to product_variants
        Schema::table('product_variants', function (Blueprint $table) {
            $table->foreignId('supplier_id')->nullable()->after('product_color_id')->constrained('suppliers')->nullOnDelete();
            $table->unsignedInteger('reorder_threshold')->default(5)->after('stock');
            $table->unsignedInteger('reorder_qty')->default(20)->after('reorder_threshold');
            $table->decimal('cost_price', 10, 2)->nullable()->after('compare_at_price');
        });

        // 3. Purchase Orders Table
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('po_number', 50)->unique();
            $table->foreignId('supplier_id')->constrained('suppliers')->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 30)->default('draft')->index(); // draft, sent, partial, received, cancelled
            $table->unsignedInteger('total_amount')->default(0);
            $table->date('expected_delivery_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 4. Purchase Order Items Table
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained('purchase_orders')->cascadeOnDelete();
            $table->foreignId('product_variant_id')->constrained('product_variants')->cascadeOnDelete();
            $table->unsignedInteger('ordered_qty');
            $table->unsignedInteger('received_qty')->default(0);
            $table->unsignedInteger('unit_cost')->default(0);
            $table->unsignedInteger('line_total')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_order_items');
        Schema::dropIfExists('purchase_orders');

        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropForeign(['supplier_id']);
            $table->dropColumn(['supplier_id', 'reorder_threshold', 'reorder_qty', 'cost_price']);
        });

        Schema::dropIfExists('suppliers');
    }
};
