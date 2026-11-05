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
        Schema::create('loyalty_tiers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->string('slug', 50)->unique();
            $table->decimal('min_spend', 12, 2)->default(0);
            $table->decimal('points_multiplier', 3, 2)->default(1.00); // 1.00x, 1.25x, 1.50x, 2.00x
            $table->text('perks')->nullable();
            $table->string('badge_color', 20)->default('#94a3b8');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('loyalty_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->foreignId('loyalty_tier_id')->constrained('loyalty_tiers')->cascadeOnDelete();
            $table->integer('points_balance')->default(0);
            $table->integer('points_earned_lifetime')->default(0);
            $table->integer('points_redeemed_lifetime')->default(0);
            $table->decimal('lifetime_spend', 12, 2)->default(0);
            $table->timestamps();

            $table->index(['points_balance', 'loyalty_tier_id']);
        });

        Schema::create('loyalty_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loyalty_account_id')->constrained('loyalty_accounts')->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->enum('type', ['earned', 'redeemed', 'adjusted', 'expired'])->default('earned');
            $table->integer('points'); // positive for earned/credit, negative for redeemed/debit
            $table->string('description', 255);
            $table->timestamps();

            $table->index(['loyalty_account_id', 'created_at']);
        });

        // Seed default foundation tiers
        $now = now();
        DB::table('loyalty_tiers')->insert([
            [
                'name'              => 'Silver Member',
                'slug'              => 'silver',
                'min_spend'         => 0,
                'points_multiplier' => 1.00,
                'perks'             => '1 Point per ₹50 spent. Standard checkout redemption. Seasonal member newsletters.',
                'badge_color'       => '#94a3b8',
                'is_active'         => true,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'name'              => 'Gold VIP',
                'slug'              => 'gold',
                'min_spend'         => 15000,
                'points_multiplier' => 1.25,
                'perks'             => '1.25x point multiplier. Free express courier delivery on all orders. ₹500 Birthday reward coupon.',
                'badge_color'       => '#f59e0b',
                'is_active'         => true,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'name'              => 'Platinum Elite',
                'slug'              => 'platinum',
                'min_spend'         => 40000,
                'points_multiplier' => 1.50,
                'perks'             => '1.50x point multiplier. Priority same-day atelier dispatch. Private WhatsApp concierge styling.',
                'badge_color'       => '#8b5cf6',
                'is_active'         => true,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'name'              => 'Diamond Patron',
                'slug'              => 'diamond',
                'min_spend'         => 100000,
                'points_multiplier' => 2.00,
                'perks'             => '2.0x points multiplier. Complimentary satin packaging with every shipment. Exclusive runway previews.',
                'badge_color'       => '#06b6d4',
                'is_active'         => true,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loyalty_transactions');
        Schema::dropIfExists('loyalty_accounts');
        Schema::dropIfExists('loyalty_tiers');
    }
};

