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
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'currency')) {
                $table->string('currency', 10)->default('INR')->after('total');
            }
            if (!Schema::hasColumn('orders', 'exchange_rate')) {
                $table->decimal('exchange_rate', 12, 6)->default(1.000000)->after('currency');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'exchange_rate')) {
                $table->dropColumn('exchange_rate');
            }
            if (Schema::hasColumn('orders', 'currency')) {
                $table->dropColumn('currency');
            }
        });
    }
};
