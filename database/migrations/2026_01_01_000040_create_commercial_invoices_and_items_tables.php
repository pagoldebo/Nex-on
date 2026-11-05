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
        Schema::create('commercial_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 50)->unique(); // INV-YYYYMM-XXXX or CN-YYYYMM-XXXX
            $table->enum('type', ['tax_invoice', 'credit_note', 'proforma'])->default('tax_invoice');
            $table->date('invoice_date');
            $table->date('due_date')->nullable();
            
            // Client & Entity Details
            $table->string('client_name', 150);
            $table->string('company_name', 150)->nullable();
            $table->string('client_gstin', 50)->nullable();
            $table->string('client_pan', 50)->nullable();
            $table->string('client_email', 150)->nullable();
            $table->string('client_phone', 50)->nullable();
            $table->text('billing_address')->nullable();
            $table->text('shipping_address')->nullable();
            $table->string('place_of_supply', 100)->nullable()->default('Maharashtra');

            // Financial Calculations
            $table->string('currency', 10)->default('INR');
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('cgst_amount', 12, 2)->default(0);
            $table->decimal('sgst_amount', 12, 2)->default(0);
            $table->decimal('igst_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->decimal('amount_paid', 12, 2)->default(0);
            
            // Status & Notes
            $table->enum('status', ['draft', 'issued', 'paid', 'partial', 'cancelled'])->default('draft');
            $table->text('notes')->nullable();
            $table->text('terms')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['invoice_date', 'status']);
            $table->index('client_gstin');
        });

        Schema::create('commercial_invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commercial_invoice_id')->constrained('commercial_invoices')->cascadeOnDelete();
            $table->foreignId('product_variant_id')->nullable()->constrained('product_variants')->nullOnDelete();
            $table->string('description', 255);
            $table->string('hsn_code', 50)->nullable();
            $table->decimal('quantity', 10, 2)->default(1);
            $table->string('unit', 20)->default('pcs'); // pcs, mtr, set, kg, box
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('tax_rate', 5, 2)->default(18.00); // 0, 5, 12, 18, 28
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commercial_invoice_items');
        Schema::dropIfExists('commercial_invoices');
    }
};

