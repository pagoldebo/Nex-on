<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\CommercialInvoice;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductVariant;
use App\Models\Template;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommercialInvoiceTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected ProductVariant $variant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'email' => 'billing@nexon.luxury',
            'role'  => 'admin',
        ]);

        Template::create(['key' => 'buttercup', 'name' => 'Buttercup', 'is_active' => true]);

        $category = Category::create(['name' => 'Silk Sleepwear', 'slug' => 'silk-sleepwear']);
        $product = Product::create(['category_id' => $category->id, 'name' => 'Mulberry Silk Robe', 'slug' => 'mulberry-silk-robe']);
        $color = ProductColor::create(['product_id' => $product->id, 'name' => 'Champagne', 'hex' => '#fde68a']);
        $this->variant = ProductVariant::create([
            'product_id'       => $product->id,
            'product_color_id' => $color->id,
            'sku'              => 'ROBE-CHAMP-S',
            'size'             => 'S',
            'price'            => 5000,
            'cost_price'       => 2000,
            'stock'            => 100,
        ]);
    }

    public function test_commercial_invoices_index_loads_with_kpis(): void
    {
        CommercialInvoice::create([
            'type'             => 'tax_invoice',
            'invoice_date'     => now()->format('Y-m-d'),
            'client_name'      => 'Oberoi Luxury Hotels Ltd',
            'company_name'     => 'The Oberoi Mumbai',
            'client_gstin'     => '27AABCO9988D1Z4',
            'subtotal'         => 50000,
            'tax_amount'       => 9000,
            'cgst_amount'      => 4500,
            'sgst_amount'      => 4500,
            'total_amount'     => 59000,
            'amount_paid'      => 20000,
            'status'           => 'partial',
            'created_by'       => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.invoices.index'));

        $response->assertStatus(200);
        $response->assertSee('B2B Tax Invoices &amp; Credit Notes', false);
        $response->assertSee('Oberoi Luxury Hotels Ltd');
        $response->assertSee('59,000.00');
        $response->assertSee('39,000.00'); // Outstanding balance (59,000 - 20,000)
    }

    public function test_invoice_create_screen_loads(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.invoices.create'));

        $response->assertStatus(200);
        $response->assertSee('New B2B Tax Invoice');
        $response->assertSee('Save &amp; Generate Invoice', false);
    }

    public function test_admin_can_create_local_tax_invoice_with_cgst_and_sgst(): void
    {
        $payload = [
            'type'             => 'tax_invoice',
            'invoice_date'     => now()->format('Y-m-d'),
            'due_date'         => now()->addDays(15)->format('Y-m-d'),
            'client_name'      => 'Taj Vivanta Spa & Boutique',
            'company_name'     => 'Indian Hotels Company Ltd',
            'client_gstin'     => '27AAACI1234D1Z8',
            'client_email'     => 'procurement@tajhotels.com',
            'client_phone'     => '+91 22 6665 3366',
            'billing_address'  => 'Colaba, Mumbai, Maharashtra 400001',
            'place_of_supply'  => 'Maharashtra',
            'items'            => [
                [
                    'product_variant_id' => $this->variant->id,
                    'description'        => 'Mulberry Silk Robe - Champagne / S',
                    'hsn_code'           => '621210',
                    'quantity'           => 10,
                    'unit'               => 'pcs',
                    'unit_price'         => 5000, // 10 x 5000 = 50,000
                    'discount_amount'    => 5000, // Discount 5,000 => Net 45,000
                    'tax_rate'           => 18,   // 18% of 45,000 = 8,100 => Total 53,100
                ]
            ],
            'notes'            => 'Boutique opening stock order',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.invoices.store'), $payload);

        $this->assertDatabaseHas('commercial_invoices', [
            'client_name'     => 'Taj Vivanta Spa & Boutique',
            'company_name'    => 'Indian Hotels Company Ltd',
            'subtotal'        => 50000,
            'discount_amount' => 5000,
            'tax_amount'      => 8100,
            'cgst_amount'     => 4050, // 8100 / 2
            'sgst_amount'     => 4050, // 8100 / 2
            'igst_amount'     => 0,
            'total_amount'    => 53100,
            'status'          => 'issued',
        ]);

        $invoice = CommercialInvoice::where('client_name', 'Taj Vivanta Spa & Boutique')->first();
        $response->assertRedirect(route('admin.invoices.show', $invoice));

        $this->assertDatabaseHas('commercial_invoice_items', [
            'commercial_invoice_id' => $invoice->id,
            'description'           => 'Mulberry Silk Robe - Champagne / S',
            'quantity'              => 10,
            'total_amount'          => 53100,
        ]);
    }

    public function test_admin_can_create_interstate_invoice_with_igst(): void
    {
        $payload = [
            'type'             => 'tax_invoice',
            'invoice_date'     => now()->format('Y-m-d'),
            'client_name'      => 'Leela Palace Spa',
            'company_name'     => 'Hotel Leelaventure Ltd',
            'client_gstin'     => '29AAACH0001B1Z2', // Karnataka GSTIN
            'place_of_supply'  => 'Karnataka',
            'items'            => [
                [
                    'product_variant_id' => $this->variant->id,
                    'description'        => 'Mulberry Silk Robe - Champagne / S',
                    'quantity'           => 5,
                    'unit'               => 'pcs',
                    'unit_price'         => 6000, // 30,000
                    'discount_amount'    => 0,
                    'tax_rate'           => 18,   // 5400 IGST
                ]
            ],
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.invoices.store'), $payload);

        $this->assertDatabaseHas('commercial_invoices', [
            'client_name'     => 'Leela Palace Spa',
            'place_of_supply' => 'Karnataka',
            'cgst_amount'     => 0,
            'sgst_amount'     => 0,
            'igst_amount'     => 5400,
            'total_amount'    => 35400,
        ]);
    }

    public function test_admin_can_record_payment_against_invoice(): void
    {
        $invoice = CommercialInvoice::create([
            'type'             => 'tax_invoice',
            'invoice_date'     => now()->format('Y-m-d'),
            'client_name'      => 'Four Seasons Resort',
            'subtotal'         => 40000,
            'total_amount'     => 47200,
            'amount_paid'      => 0,
            'status'           => 'issued',
            'created_by'       => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)->patch(route('admin.invoices.status', $invoice), [
            'record_payment' => 47200,
        ]);

        $response->assertRedirect(route('admin.invoices.show', $invoice));

        $invoice->refresh();
        $this->assertEquals(47200, (float)$invoice->amount_paid);
        $this->assertEquals('paid', $invoice->status);
        $this->assertEquals(0, $invoice->balance_due);
    }

    public function test_printable_b2b_gst_invoice_renders_cleanly(): void
    {
        $invoice = CommercialInvoice::create([
            'type'             => 'tax_invoice',
            'invoice_date'     => now()->format('Y-m-d'),
            'client_name'      => 'ITC Grand Chola Luxury Suites',
            'company_name'     => 'ITC Limited',
            'client_gstin'     => '33AABCI5678K1ZQ',
            'billing_address'  => 'Guindy, Chennai, Tamil Nadu 600032',
            'place_of_supply'  => 'Tamil Nadu',
            'subtotal'         => 100000,
            'tax_amount'       => 18000,
            'igst_amount'      => 18000,
            'total_amount'     => 118000,
            'status'           => 'issued',
            'created_by'       => $this->admin->id,
        ]);

        $invoice->items()->create([
            'description'  => 'Silk Loungewear Suite Hampers',
            'hsn_code'     => '621210',
            'quantity'     => 20,
            'unit'         => 'set',
            'unit_price'   => 5000,
            'tax_rate'     => 18,
            'tax_amount'   => 18000,
            'total_amount' => 118000,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.invoices.print', $invoice));

        $response->assertStatus(200);
        $response->assertSee('ITC Grand Chola Luxury Suites');
        $response->assertSee('118,000.00');
        $response->assertSee('Original for Recipient');
    }
}

