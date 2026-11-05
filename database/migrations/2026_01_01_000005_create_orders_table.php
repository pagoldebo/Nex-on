<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            // Customer — nullable so guest orders are supported
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            // Human-readable order reference (e.g. SAA-20240101-0001)
            $table->string('reference', 32)->unique();

            // --- Status ---
            // pending → confirmed → processing → shipped → delivered
            // cancelled, refund_requested, refunded
            $table->string('status', 32)->default('pending')->index();

            // --- Totals (stored in paise for precision; displayed as ₹) ---
            $table->unsignedInteger('subtotal');        // sum of item totals
            $table->unsignedInteger('shipping_charge')->default(0);
            $table->unsignedInteger('cod_charge')->default(0);
            $table->unsignedInteger('discount')->default(0);
            $table->unsignedInteger('total');           // subtotal + shipping + cod - discount

            // --- Payment ---
            $table->string('payment_method', 32);       // upi | card | cod
            $table->string('payment_status', 32)->default('pending'); // pending | paid | failed | refunded
            $table->string('payment_reference')->nullable();          // gateway txn ID

            // --- Shipping address (snapshot at order time) ---
            $table->string('shipping_name');
            $table->string('shipping_phone', 15);
            $table->string('shipping_email');
            $table->string('shipping_address1');
            $table->string('shipping_address2')->nullable();
            $table->string('shipping_city');
            $table->string('shipping_state');
            $table->string('shipping_pincode', 10);

            // --- Delivery ---
            $table->string('tracking_number')->nullable();
            $table->string('courier')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();

            // --- Internal notes ---
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
