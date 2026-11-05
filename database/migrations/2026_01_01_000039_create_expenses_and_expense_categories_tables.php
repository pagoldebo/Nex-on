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
        Schema::create('expense_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->string('color', 20)->default('#6366f1');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expense_category_id')->constrained('expense_categories')->cascadeOnDelete();
            $table->foreignId('outlet_id')->nullable()->constrained('outlets')->nullOnDelete();
            $table->string('expense_number', 50)->unique();
            $table->string('title', 255);
            $table->decimal('amount', 12, 2);
            $table->decimal('tax_amount', 12, 2)->default(0); // GST Input Tax Credit
            $table->string('payment_method', 50)->default('bank_transfer'); // cash, bank_transfer, upi, card, cheque
            $table->date('expense_date');
            $table->string('vendor_name', 150)->nullable();
            $table->string('vendor_gstin', 50)->nullable();
            $table->string('reference_no', 100)->nullable();
            $table->string('receipt_path', 255)->nullable();
            $table->boolean('is_recurring')->default(false);
            $table->string('recurring_frequency', 50)->nullable(); // monthly, quarterly, yearly
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['expense_date', 'expense_category_id']);
        });

        // Seed default foundational expense categories
        $now = now();
        DB::table('expense_categories')->insert([
            ['name' => 'Rent & Store Leases',        'slug' => 'rent-leases',          'color' => '#3b82f6', 'description' => 'Commercial showroom, warehouse, and office rent', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Packaging & Bags',           'slug' => 'packaging-bags',       'color' => '#10b981', 'description' => 'Boxes, tissue wrap, shopping bags, and branded tags', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Courier & Freight Outward',   'slug' => 'courier-freight',      'color' => '#f59e0b', 'description' => 'BlueDart, Delhivery, and express logistics shipping charges', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Salaries, Wages & Stipends', 'slug' => 'salaries-wages',       'color' => '#8b5cf6', 'description' => 'Staff payroll, store cashier wages, and commissions', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Marketing & Paid Ads',       'slug' => 'marketing-ads',        'color' => '#ec4899', 'description' => 'Meta ads, Google Ads, influencers, and creative campaigns', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Electricity & Utilities',    'slug' => 'utilities',            'color' => '#06b6d4', 'description' => 'Store electricity, water, internet, and phone bills', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Maintenance & Fixtures',     'slug' => 'maintenance-fixtures', 'color' => '#64748b', 'description' => 'HVAC repairs, lighting, plumbing, and showroom upkeep', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Software & Cloud SaaS',      'slug' => 'software-saas',        'color' => '#6366f1', 'description' => 'Hosting, POS licenses, Shopify/ERP subscriptions', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('expense_categories');
    }
};

