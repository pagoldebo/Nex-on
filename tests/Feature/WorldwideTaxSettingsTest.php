<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductVariant;
use App\Models\Setting;
use App\Models\Supplier;
use App\Models\Template;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorldwideTaxSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $customer;
    protected ProductVariant $variant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'email' => 'admin.tax@nexon.com',
            'role'  => 'admin',
        ]);

        $this->customer = User::factory()->create([
            'email' => 'customer.tax@example.com',
            'role'  => 'customer',
        ]);

        Template::create(['key' => 'buttercup', 'name' => 'Buttercup', 'is_active' => true]);

        $category = Category::create(['name' => 'Test Category', 'slug' => 'test-cat']);
        $product = Product::create([
            'category_id' => $category->id,
            'name'        => 'Luxury Silk Shirt',
            'slug'        => 'luxury-silk-shirt',
            'is_active'   => true,
        ]);
        $color = ProductColor::create([
            'product_id' => $product->id,
            'name'       => 'Midnight Blue',
            'hex'        => '#001f3f',
        ]);
        $this->variant = ProductVariant::create([
            'product_id'       => $product->id,
            'product_color_id' => $color->id,
            'sku'              => 'SILK-BLU-M',
            'size'             => 'M',
            'price'            => 1000,
            'stock'            => 50,
            'is_active'        => true,
        ]);
    }

    public function test_admin_settings_page_displays_worldwide_tax_section(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.settings.index'));

        $response->assertOk();
        $response->assertSee('Worldwide Tax &amp; Invoicing Details', false);
        $response->assertSee('International Tax Presets');
        $response->assertSee('USA (Sales Tax 8.25%)');
        $response->assertSee('UK (VAT 20%)');
        $response->assertSee('EU (VAT 21%)');
        $response->assertSee('Canada (HST 13%)');
        $response->assertSee('Australia (GST 10%)');
        $response->assertSee('UAE (VAT 5%)');
        $response->assertSee('India (GST 18%)');
        $response->assertSee('Pricing Calculation Mode');
        $response->assertSee('Live Invoice Preview &amp; Calculation Simulation', false);
    }

    public function test_admin_can_configure_uk_vat_settings(): void
    {
        $payload = [
            'tax_section_submitted' => '1',
            'tax_enabled'           => '1',
            'tax_name'              => 'VAT',
            'tax_percentage'        => '20.00',
            'tax_number_label'      => 'VAT Reg No',
            'tax_number'            => 'GB999999973',
            'tax_type'              => 'inclusive',
            'store_state'           => 'Greater London',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.settings.update'), $payload);

        $response->assertRedirect(route('admin.settings.index'));

        $this->assertTrue(Setting::taxEnabled());
        $this->assertSame('VAT', Setting::taxName());
        $this->assertEquals(20.0, Setting::taxRate());
        $this->assertSame('VAT Reg No', Setting::taxNumberLabel());
        $this->assertSame('GB999999973', Setting::taxNumber());
        $this->assertSame('inclusive', Setting::taxType());
        $this->assertSame('GB999999973', Setting::gstin()); // backward compatibility
    }

    public function test_admin_can_configure_us_exclusive_sales_tax(): void
    {
        $payload = [
            'tax_section_submitted' => '1',
            'tax_enabled'           => '1',
            'tax_name'              => 'Sales Tax',
            'tax_percentage'        => '8.25',
            'tax_number_label'      => 'Tax ID / EIN',
            'tax_number'            => 'US-12345678',
            'tax_type'              => 'exclusive',
            'store_state'           => 'California',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.settings.update'), $payload);

        $response->assertRedirect(route('admin.settings.index'));

        $this->assertTrue(Setting::taxEnabled());
        $this->assertSame('Sales Tax', Setting::taxName());
        $this->assertEquals(8.25, Setting::taxRate());
        $this->assertSame('Tax ID / EIN', Setting::taxNumberLabel());
        $this->assertSame('exclusive', Setting::taxType());
    }

    public function test_admin_can_disable_tax_calculation(): void
    {
        $payload = [
            'tax_section_submitted' => '1',
            // tax_enabled checkbox unchecked (omitted from request)
            'tax_name'              => 'VAT',
            'tax_percentage'        => '20.00',
            'tax_number'            => 'GB999999973',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.settings.update'), $payload);

        $response->assertRedirect(route('admin.settings.index'));
        $this->assertFalse(Setting::taxEnabled());
    }

    public function test_order_checkout_computes_tax_based_on_calculation_mode(): void
    {
        // 1. Test Exclusive Tax (e.g. US Sales Tax 10%)
        Setting::set('tax_enabled', '1');
        Setting::set('tax_name', 'Sales Tax');
        Setting::set('tax_percentage', '10.00');
        Setting::set('tax_type', 'exclusive');
        Setting::set('free_shipping_threshold', '50000');
        Setting::set('shipping_cost', '0');
        Setting::set('cod_charge', '0');

        $checkoutData = [
            'name'           => 'John Doe',
            'email'          => 'john@example.com',
            'phone'          => '9876543210',
            'address'        => '123 Main St',
            'city'           => 'Los Angeles',
            'state'          => 'California',
            'pincode'        => '90001',
            'payment_method' => 'cod',
            'items'          => [
                [
                    'product_variant_id' => $this->variant->id,
                    'qty'                => 1, // Subtotal = 1000
                ],
            ],
        ];

        $orderExclusive = Order::createFromCheckout($checkoutData, $this->customer->id);

        // Subtotal = 1000, 10% exclusive tax = 100, Total = 1100
        $this->assertEquals(1000, $orderExclusive->subtotal);
        $this->assertEquals(100, $orderExclusive->tax_amount);
        $this->assertEquals(1100, $orderExclusive->total);

        // 2. Test Inclusive Tax (e.g. UK VAT 20%)
        Setting::set('tax_enabled', '1');
        Setting::set('tax_name', 'VAT');
        Setting::set('tax_percentage', '20.00');
        Setting::set('tax_type', 'inclusive');

        $orderInclusive = Order::createFromCheckout($checkoutData, $this->customer->id);

        // Subtotal = 1000, Total = 1000, Embedded VAT = round(1000 * 20 / 120) = 167
        $this->assertEquals(1000, $orderInclusive->subtotal);
        $this->assertEquals(167, $orderInclusive->tax_amount);
        $this->assertEquals(1000, $orderInclusive->total);

        // 3. Test Disabled Tax
        Setting::set('tax_enabled', '0');

        $orderDisabled = Order::createFromCheckout($checkoutData, $this->customer->id);
        $this->assertEquals(0, $orderDisabled->tax_amount);
        $this->assertEquals(1000, $orderDisabled->total);
    }

    public function test_customer_invoice_renders_configured_tax_name_and_registration(): void
    {
        Setting::set('tax_enabled', '1');
        Setting::set('tax_name', 'VAT');
        Setting::set('tax_percentage', '20.00');
        Setting::set('tax_number_label', 'VAT Reg No');
        Setting::set('tax_number', 'GB999999973');
        Setting::set('tax_type', 'inclusive');

        $order = Order::create([
            'user_id'         => $this->customer->id,
            'reference'       => 'INV-UK-1001',
            'subtotal'        => 1000,
            'total'           => 1000,
            'tax_amount'      => 167,
            'status'          => Order::STATUS_DELIVERED,
            'shipping_name'     => 'Jane Doe',
            'shipping_email'    => 'jane@example.com',
            'shipping_phone'    => '9876543210',
            'shipping_address1' => '456 Oxford St',
            'shipping_city'     => 'London',
            'shipping_state'  => 'Greater London',
            'shipping_pincode'=> 'W1D 1BS',
            'shipping_country'=> 'United Kingdom',
            'payment_method'  => 'stripe',
            'payment_status'  => Order::PAYMENT_PAID,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.orders.invoice', $order));

        $response->assertOk();
        $response->assertSee('VAT INVOICE');
        $response->assertSee('VAT Reg No: <span class="font-mono font-semibold">GB999999973</span>', false);
        $response->assertSee('Inclusive of VAT (20%)', false);
    }

    public function test_supplier_invoices_view_reflects_store_tax_configuration(): void
    {
        Setting::set('tax_enabled', '1');
        Setting::set('tax_name', 'HST');
        Setting::set('tax_percentage', '13.00');
        Setting::set('tax_number_label', 'Business Number (BN)');

        Supplier::create([
            'name'       => 'Montreal Textile Co',
            'code'       => 'MTL-TEXTILES',
            'email'      => 'orders@montrealtextiles.ca',
            'gst_number' => '123456789RT0001',
            'is_active'  => true,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.supplier-invoices.index'));

        $response->assertOk();
        $response->assertSee('HST Tax (13%)', false);
        $response->assertSee('Auto-calc');
    }
}
