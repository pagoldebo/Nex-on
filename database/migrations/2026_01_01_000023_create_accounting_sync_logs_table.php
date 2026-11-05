<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_sync_logs', function (Blueprint $table) {
            $table->id();
            $table->string('sync_type', 40)->index(); // sales_voucher, receipt_voucher, stock_journal
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->string('voucher_number', 80)->index();
            $table->string('status', 30)->default('success')->index(); // success, failed, pending
            $table->string('direction', 30)->default('export_xml'); // export_xml, api_push
            $table->longText('payload_xml');
            $table->text('response_message')->nullable();
            $table->foreignId('synced_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_sync_logs');
    }
};
