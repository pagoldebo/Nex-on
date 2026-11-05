<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('channel', 20)->default('online')->index()->after('reference');
            $table->unsignedInteger('tendered_amount')->nullable()->after('total');
            $table->unsignedInteger('change_amount')->nullable()->after('tendered_amount');
            $table->foreignId('pos_cashier_id')->nullable()->after('user_id')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['pos_cashier_id']);
            $table->dropColumn(['channel', 'tendered_amount', 'change_amount', 'pos_cashier_id']);
        });
    }
};
