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
        Schema::create('abandoned_carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('email', 191)->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('customer_name', 150)->nullable();
            $table->json('cart_data');
            $table->string('recovery_hash', 64)->unique();
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->unsignedInteger('items_count')->default(1);
            $table->unsignedTinyInteger('recovery_step')->default(0); // 0: detected, 1: 1-hr email, 2: 24-hr SMS, 3: 72-hr push
            $table->timestamp('last_reminded_at')->nullable();
            $table->boolean('is_recovered')->default(false);
            $table->timestamp('recovered_at')->nullable();
            $table->foreignId('recovered_order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->timestamps();

            $table->index(['is_recovered', 'created_at']);
            $table->index('email');
            $table->index('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('abandoned_carts');
    }
};

