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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone', 50)->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'designation')) {
                $table->string('designation', 100)->nullable()->after('role');
            }
            if (!Schema::hasColumn('users', 'department_name')) {
                $table->string('department_name', 100)->nullable()->after('designation');
            }
            if (!Schema::hasColumn('users', 'employment_status')) {
                $table->string('employment_status', 30)->default('active')->index()->after('department_name');
            }
            if (!Schema::hasColumn('users', 'staff_sales')) {
                $table->decimal('staff_sales', 12, 2)->default(0)->after('employment_status');
            }
            if (!Schema::hasColumn('users', 'is_staff')) {
                $table->boolean('is_staff')->default(false)->index()->after('staff_sales');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = [];
            foreach (['phone', 'designation', 'department_name', 'employment_status', 'staff_sales', 'is_staff'] as $col) {
                if (Schema::hasColumn('users', $col)) {
                    $columns[] = $col;
                }
            }
            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};

