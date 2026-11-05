<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pincode_zones', function (Blueprint $table) {
            $table->id();
            $table->string('pincode', 6)->unique();
            $table->string('city');
            $table->string('state');
            $table->boolean('cod_available')->default(true);
            $table->unsignedSmallInteger('delivery_days')->default(3);
            $table->boolean('is_serviceable')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pincode_zones');
    }
};
