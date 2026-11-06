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
        // 1. Departments Table
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('code', 30)->unique();
            $table->foreignId('head_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('description')->nullable();
            $table->string('color', 30)->default('indigo');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Department User Pivot Table
        Schema::create('department_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained('departments')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('role_title', 100)->nullable();
            $table->boolean('is_primary')->default(true);
            $table->timestamps();

            $table->unique(['department_id', 'user_id']);
        });

        // 3. Staff Shifts Table
        Schema::create('staff_shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained('departments')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('shift_date');
            $table->enum('shift_type', ['morning', 'evening', 'night', 'full_day'])->default('morning');
            $table->time('start_time');
            $table->time('end_time');
            $table->enum('status', ['scheduled', 'completed', 'swapped', 'cancelled'])->default('scheduled');
            $table->string('notes', 255)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['shift_date', 'department_id']);
            $table->index(['user_id', 'shift_date']);
        });

        // Seed core retail departments
        DB::table('departments')->insert([
            [
                'name'        => 'Retail Store Sales',
                'code'        => 'SALES',
                'description' => 'Boutique storefront customer styling, floor service, and POS billing.',
                'color'       => 'indigo',
                'is_active'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => 'Warehouse & Fulfillment',
                'code'        => 'WH',
                'description' => 'Inventory picking, packing, dispatch, and inter-store stock transfers.',
                'color'       => 'blue',
                'is_active'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => 'Atelier & Bespoke Tailoring',
                'code'        => 'ATELIER',
                'description' => 'Garment alterations, custom silhouettes, and bespoke embroidery.',
                'color'       => 'rose',
                'is_active'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => 'Customer Care & Concierge',
                'code'        => 'CARE',
                'description' => 'VIP concierge service, order support tickets, and post-purchase follow-up.',
                'color'       => 'emerald',
                'is_active'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => 'Store Management & Admin',
                'code'        => 'MGMT',
                'description' => 'General store administration, cash drawer audits, and staff supervision.',
                'color'       => 'purple',
                'is_active'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_shifts');
        Schema::dropIfExists('department_user');
        Schema::dropIfExists('departments');
    }
};

