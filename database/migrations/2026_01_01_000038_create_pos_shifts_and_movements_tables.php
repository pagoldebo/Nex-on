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
        Schema::create('pos_shifts', function (Blueprint $table) {
            $table->id();
            $table->string('shift_code', 50)->unique();
            $table->foreignId('cashier_id')->constrained('users')->cascadeOnDelete();
            $table->dateTime('opened_at');
            $table->dateTime('closed_at')->nullable();
            $table->enum('status', ['open', 'closed'])->default('open')->index();
            $table->decimal('opening_float', 10, 2)->default(0);
            $table->decimal('expected_cash', 10, 2)->default(0);
            $table->decimal('counted_cash', 10, 2)->nullable();
            $table->decimal('cash_difference', 10, 2)->default(0);
            $table->decimal('cash_sales', 10, 2)->default(0);
            $table->decimal('card_sales', 10, 2)->default(0);
            $table->decimal('upi_sales', 10, 2)->default(0);
            $table->decimal('total_sales', 10, 2)->default(0);
            $table->unsignedInteger('total_orders_count')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('pos_shift_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pos_shift_id')->constrained('pos_shifts')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('type', ['cash_in', 'cash_out']);
            $table->decimal('amount', 10, 2);
            $table->string('reason', 150);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_shift_movements');
        Schema::dropIfExists('pos_shifts');
    }
};

