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
        Schema::create('push_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('endpoint');
            $table->string('endpoint_hash', 64)->unique();
            $table->text('p256dh_key')->nullable();
            $table->text('auth_token')->nullable();
            $table->string('device_type', 30)->default('browser'); // desktop, mobile, tablet
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('push_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('body');
            $table->string('action_url')->nullable();
            $table->string('image_url')->nullable();
            $table->unsignedInteger('sent_count')->default(0);
            $table->string('status', 20)->default('sent');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('push_campaigns');
        Schema::dropIfExists('push_subscriptions');
    }
};
