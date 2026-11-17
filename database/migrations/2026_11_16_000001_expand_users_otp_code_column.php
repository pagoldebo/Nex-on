<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN otp_code VARCHAR(100) NULL");
        } else {
            Schema::table('users', function (Blueprint $table) {
                $table->string('otp_code', 100)->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN otp_code VARCHAR(4) NULL");
        } else {
            Schema::table('users', function (Blueprint $table) {
                $table->string('otp_code', 4)->nullable()->change();
            });
        }
    }
};
