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
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->string('payroll_code', 50)->unique(); // PAY-YYYYMM
            $table->string('period_month', 7); // YYYY-MM
            $table->string('title', 150);
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('total_gross', 12, 2)->default(0);
            $table->decimal('total_allowances', 12, 2)->default(0);
            $table->decimal('total_deductions', 12, 2)->default(0);
            $table->decimal('total_net', 12, 2)->default(0);
            $table->unsignedInteger('employees_count')->default(0);
            $table->enum('status', ['draft', 'reviewed', 'approved', 'disbursed'])->default('draft');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('disbursed_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['period_month', 'status']);
        });

        Schema::create('payroll_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_id')->constrained('payrolls')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('payslip_number', 50)->unique(); // SLIP-YYYYMM-XXXX
            $table->string('designation', 100)->default('Retail Associate');
            $table->string('department', 100)->default('Retail Store Sales');
            
            // Bank & Statutory Details
            $table->string('bank_name', 100)->nullable()->default('HDFC Bank');
            $table->string('bank_account_no', 50)->nullable();
            $table->string('bank_ifsc', 20)->nullable()->default('HDFC0000123');
            $table->string('pan_number', 20)->nullable();
            $table->string('uan_number', 30)->nullable(); // PF Universal Account Number

            // Attendance & Days
            $table->unsignedSmallInteger('working_days')->default(30);
            $table->decimal('present_days', 4, 1)->default(30);
            $table->decimal('leave_days', 4, 1)->default(0);

            // Earnings
            $table->decimal('basic_salary', 10, 2)->default(0);
            $table->decimal('hra', 10, 2)->default(0);
            $table->decimal('transport_allowance', 10, 2)->default(0);
            $table->decimal('performance_bonus', 10, 2)->default(0);
            $table->decimal('gross_salary', 10, 2)->default(0);

            // Deductions
            $table->decimal('pf_deduction', 10, 2)->default(0); // Provident Fund
            $table->decimal('esi_deduction', 10, 2)->default(0); // Employee State Insurance
            $table->decimal('tds_tax', 10, 2)->default(0); // TDS Income Tax
            $table->decimal('total_deductions', 10, 2)->default(0);

            // Final Net Salary
            $table->decimal('net_salary', 10, 2)->default(0);
            $table->enum('status', ['draft', 'approved', 'paid'])->default('draft');
            $table->string('payment_method', 50)->default('bank_transfer');
            $table->string('payment_reference', 100)->nullable();
            $table->timestamps();

            $table->index(['payroll_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_items');
        Schema::dropIfExists('payrolls');
    }
};

