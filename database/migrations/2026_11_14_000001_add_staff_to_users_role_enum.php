<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role enum('customer','vendor','admin','staff') NOT NULL DEFAULT 'customer'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::table('users')->where('role', 'staff')->update(['role' => 'admin', 'is_staff' => 1]);
            DB::statement("ALTER TABLE users MODIFY COLUMN role enum('customer','vendor','admin') NOT NULL DEFAULT 'customer'");
        }
    }
};
