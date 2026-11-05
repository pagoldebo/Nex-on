<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportsSuiteTest extends TestCase
{
    use RefreshDatabase;

    protected function setupSampleData(): array
    {
        $admin = User::create([
            'name'     => 'Reports Admin',
            'email'    => 'admin@reports.test',
            'phone'    => '9999999999',
            'password' => bcrypt('password'),
            'role'     => 'admin',
        ]);

        $category = Category::create([
            'name'        => 'Luxury Bras',
            'slug'        => 'luxury-bras',
            'is_active'   => true,
            'sort_order'  => 1,
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name'        => 'Silk Lace Bra',
            'slug'        => 'silk-lace-bra',
            'price'       => 1999,
            'is_active'   => true,
        ]);

        $color = ProductColor::create([
            'product_id' => $product->id,
            'name'       => 'Blush Pink',
            'color_hex'  => '#FFD1DC',
            'color_slug' => 'blush-pink',
        ]);

        $variant = ProductVariant::create([
            'product_id'        => $product->id,
            'product_color_id'  => $color->id,
            'size'              => '34B',
            'sku'               => 'SLB-34B-PNK',
            'price'             => 1999,
            'cost_price'        => 999,
            'stock'             => 25,
            'reorder_threshold' => 5,
        ]);

        // Create sample orders (1 Online, 1 POS)
        $onlineOrder = Order::create([
            'reference'         => 'SAA-REP-ON01',
            'channel'           => 'online',
            'status'            => Order::STATUS_DELIVERED,
            'subtotal'          => 1999,
            'shipping_charge'   => 0,
            'cod_charge'        => 0,
            'discount'          => 0,
            'total'             => 1999,
            'payment_method'    => 'razorpay',
            'payment_status'    => Order::PAYMENT_PAID,
            'shipping_name'     => 'Ananya Sharma',
            'shipping_phone'    => '9876543210',
            'shipping_email'    => 'ananya@example.com',
            'shipping_address1' => 'Marine Drive',
            'shipping_city'     => 'Mumbai',
            'shipping_state'    => 'Maharashtra',
            'shipping_pincode'  => '400020',
        ]);

        OrderItem::create([
            'order_id'           => $onlineOrder->id,
            'product_id'         => $product->id,
            'product_color_id'   => $color->id,
            'product_variant_id' => $variant->id,
            'product_name'       => $product->name,
            'product_slug'       => $product->slug,
            'color_name'         => $color->name,
            'color_hex'          => $color->color_hex,
            'size'               => $variant->size,
            'price'              => 1999,
            'qty'                => 1,
            'line_total'         => 1999,
        ]);

        $posOrder = Order::create([
            'reference'         => 'POS-REP-001',
            'channel'           => 'pos',
            'status'            => Order::STATUS_DELIVERED,
            'subtotal'          => 3998,
            'shipping_charge'   => 0,
            'cod_charge'        => 0,
            'discount'          => 200,
            'total'             => 3798,
            'tendered_amount'   => 4000,
            'change_amount'     => 202,
            'payment_method'    => 'cash',
            'payment_status'    => Order::PAYMENT_PAID,
            'shipping_name'     => 'Walk-in Boutique Client',
            'shipping_phone'    => '9888888888',
            'shipping_email'    => 'pos@store.test',
            'shipping_address1' => 'Boutique Storefront POS',
            'shipping_city'     => 'Mumbai',
            'shipping_state'    => 'Maharashtra',
            'shipping_pincode'  => '400001',
        ]);

        OrderItem::create([
            'order_id'           => $posOrder->id,
            'product_id'         => $product->id,
            'product_color_id'   => $color->id,
            'product_variant_id' => $variant->id,
            'product_name'       => $product->name,
            'product_slug'       => $product->slug,
            'color_name'         => $color->name,
            'color_hex'          => $color->color_hex,
            'size'               => $variant->size,
            'price'              => 1999,
            'qty'                => 2,
            'line_total'         => 3998,
        ]);

        return [$admin, $product, $variant, $onlineOrder, $posOrder];
    }

    public function test_reports_screen_loads_default_sales_tab()
    {
        [$admin] = $this->setupSampleData();

        $response = $this->actingAs($admin)->get(route('admin.reports.index'));
        $response->assertStatus(200);
        $response->assertSee('Analytics &amp; Executive Reports', false);
        $response->assertSee('Net Realized Revenue');
        $response->assertSee('Sales Channel Breakdown');
        $response->assertSee('POS Register');
        $response->assertSee('Online Storefront');
        $response->assertSee('Detailed Sales Ledger');
    }

    public function test_reports_product_performance_tab_loads()
    {
        [$admin] = $this->setupSampleData();

        $response = $this->actingAs($admin)->get(route('admin.reports.index', ['tab' => 'products']));
        $response->assertStatus(200);
        $response->assertSee('Product Performance Ranking');
        $response->assertSee('Total Units Sold');
        $response->assertSee('Category Sales Velocity');
        $response->assertSee('Silk Lace Bra');
    }

    public function test_reports_inventory_asset_valuation_tab_loads()
    {
        [$admin] = $this->setupSampleData();

        $response = $this->actingAs($admin)->get(route('admin.reports.index', ['tab' => 'inventory']));
        $response->assertStatus(200);
        $response->assertSee('Retail Inventory Value');
        $response->assertSee('Cost Price Asset Value (COGS)');
        $response->assertSee('Potential Gross Margin');
        $response->assertSee('SLB-34B-PNK');
    }

    public function test_reports_gst_tax_audit_tab_loads()
    {
        [$admin] = $this->setupSampleData();

        $response = $this->actingAs($admin)->get(route('admin.reports.index', ['tab' => 'gst']));
        $response->assertStatus(200);
        $response->assertSee('B2C Outward Tax Supply Register (GSTR-1)');
        $response->assertSee('Total Taxable Turnover');
        $response->assertSee('Total GST Collected');
        $response->assertSee('CGST + SGST (Intra-state)');
    }

    public function test_all_reports_csv_exports_stream_successfully()
    {
        [$admin] = $this->setupSampleData();

        // 1. Sales CSV Export
        $salesRes = $this->actingAs($admin)->get(route('admin.reports.export.sales'));
        $salesRes->assertStatus(200);
        $this->assertStringContainsString('text/csv', $salesRes->headers->get('Content-Type'));

        // 2. Products CSV Export
        $prodRes = $this->actingAs($admin)->get(route('admin.reports.export.products'));
        $prodRes->assertStatus(200);
        $this->assertStringContainsString('text/csv', $prodRes->headers->get('Content-Type'));

        // 3. Inventory CSV Export
        $invRes = $this->actingAs($admin)->get(route('admin.reports.export.inventory'));
        $invRes->assertStatus(200);
        $this->assertStringContainsString('text/csv', $invRes->headers->get('Content-Type'));

        // 4. GST CSV Export
        $gstRes = $this->actingAs($admin)->get(route('admin.reports.export.gst'));
        $gstRes->assertStatus(200);
        $this->assertStringContainsString('text/csv', $gstRes->headers->get('Content-Type'));
    }
}
