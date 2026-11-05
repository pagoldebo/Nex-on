<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. SMS Logs Table
        Schema::create('sms_logs', function (Blueprint $table) {
            $table->id();
            $table->string('phone', 30)->index();
            $table->string('driver', 30)->default('log'); // twilio, msg91, fast2sms, log
            $table->string('template_type', 40)->default('custom'); // order_confirmed, order_dispatched, otp_login, custom
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->text('message');
            $table->string('status', 20)->default('sent'); // sent, failed, delivered
            $table->string('gateway_response_id')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });

        // 2. Phone OTPs Table
        Schema::create('phone_otps', function (Blueprint $table) {
            $table->id();
            $table->string('phone', 30)->index();
            $table->string('otp', 8);
            $table->timestamp('expires_at');
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('phone_otps');
        Schema::dropIfExists('sms_logs');
    }
};
