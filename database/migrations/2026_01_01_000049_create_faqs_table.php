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
        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->string('question', 255);
            $table->text('answer');
            $table->string('category', 50)->default('General');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['category', 'is_active', 'sort_order']);
        });

        // Seed initial storefront FAQs
        DB::table('faqs')->insert([
            [
                'question'   => 'How long does domestic shipping usually take?',
                'answer'     => 'Domestic courier dispatches take between 2 to 4 business days for major metro cities (Mumbai, Delhi, Bengaluru, Hyderabad). Custom bespoke alterations require an additional 48 hours in our atelier.',
                'category'   => 'Shipping & Delivery',
                'sort_order' => 1,
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question'   => 'What is the return and exchange window?',
                'answer'     => 'We provide a 7-day complimentary return or size exchange window from the date of physical delivery for all unworn garments with original designer tags intact.',
                'category'   => 'Returns & Exchanges',
                'sort_order' => 2,
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question'   => 'Can I request custom silhouette alterations before dispatch?',
                'answer'     => 'Yes, during checkout or immediately after placing your order, you can contact our concierge with your bust, waist, and length measurements for complimentary basic tailoring.',
                'category'   => 'Atelier & Customization',
                'sort_order' => 3,
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question'   => 'Are B2B GST tax invoices available for wholesale or corporate purchases?',
                'answer'     => 'Yes. Simply enter your company name and GSTIN during the checkout step or contact our finance desk to receive an official GST tax invoice with input credit claims.',
                'category'   => 'Payments & Billing',
                'sort_order' => 4,
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faqs');
    }
};

