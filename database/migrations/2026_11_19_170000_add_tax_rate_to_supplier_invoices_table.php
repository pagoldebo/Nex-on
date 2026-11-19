<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('supplier_invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('supplier_invoices', 'tax_rate')) {
                $table->decimal('tax_rate', 5, 2)->nullable()->default(18.00)->after('subtotal');
            }
        });
    }

    public function down(): void
    {
        Schema::table('supplier_invoices', function (Blueprint $table) {
            if (Schema::hasColumn('supplier_invoices', 'tax_rate')) {
                $table->dropColumn('tax_rate');
            }
        });
    }
};
