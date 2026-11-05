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
        if (!Schema::hasTable('delivery_personnel')) {
            Schema::create('delivery_personnel', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('phone')->unique();
                $table->string('email')->nullable();
                $table->string('vehicle_number')->nullable();
                $table->string('vehicle_type')->default('bike'); // bike, van, car, scooter
                $table->string('status')->default('active'); // active, on_trip, inactive
                $table->timestamps();
            });
        }

        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'delivery_personnel_id')) {
                $table->foreignId('delivery_personnel_id')->nullable()->constrained('delivery_personnel')->nullOnDelete();
            }
            if (!Schema::hasColumn('orders', 'delivery_status')) {
                $table->string('delivery_status')->default('unassigned'); // unassigned, assigned, out_for_delivery, delivered, failed
            }
            if (!Schema::hasColumn('orders', 'proof_signature')) {
                $table->text('proof_signature')->nullable();
            }
            if (!Schema::hasColumn('orders', 'proof_notes')) {
                $table->text('proof_notes')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'delivery_personnel_id')) {
                $table->dropForeign(['delivery_personnel_id']);
                $table->dropColumn(['delivery_personnel_id']);
            }
            if (Schema::hasColumn('orders', 'delivery_status')) {
                $table->dropColumn(['delivery_status']);
            }
            if (Schema::hasColumn('orders', 'proof_signature')) {
                $table->dropColumn(['proof_signature']);
            }
            if (Schema::hasColumn('orders', 'proof_notes')) {
                $table->dropColumn(['proof_notes']);
            }
        });

        Schema::dropIfExists('delivery_personnel');
    }
};
