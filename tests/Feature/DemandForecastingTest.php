<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductVariant;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemandForecastingTest extends TestCase
{
    use RefreshDatabase;

    protected function createTestData(): array
    {
        $admin = User::create([
            'name'     => 'Forecasting Admin',
            'email'    => 'admin@forecast.test',
            'phone'    => '9999999999',
            'password' => bcrypt('password'),
            'role'     => 'admin',
        ]);

        $supplier = Supplier::create([
            'name'           => 'Luxe Fabrics Supplier',
            'code'           => 'SUP-LUX-01',
            'contact_name'   => 'Sanjay Mehta',
            'email'          => 'sanjay@luxefabrics.test',
            'phone'          => '9876543210',
            'lead_time_days' => 10,
            'is_active'      => true,
        ]);

        $category = Category::create([
            'name'       => 'Silk Chemises',
            'slug'       => 'silk-chemises',
            'is_active'  => true,
            'sort_order' => 1,
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name'        => 'Crimson Satin Chemise',
            'slug'        => 'crimson-satin-chemise',
            'price'       => 2499,
            'is_active'   => true,
        ]);

        $color = ProductColor::create([
            'product_id' => $product->id,
            'name'       => 'Crimson Red',
            'color_hex'  => '#990000',
            'color_slug' => 'crimson-red',
        ]);

        // High velocity variant (stock = 10, high sales => critical days left)
        $fastVariant = ProductVariant::create([
            'product_id'        => $product->id,
            'product_color_id'  => $color->id,
            'supplier_id'       => $supplier->id,
            'size'              => 'S',
            'sku'               => 'CSC-RED-S',
            'price'             => 2499,
            'cost_price'        => 1200,
            'stock'             => 10,
            'reorder_threshold' => 5,
        ]);

        // Dead stock variant (stock = 40, zero sales)
        $deadVariant = ProductVariant::create([
            'product_id'        => $product->id,
            'product_color_id'  => $color->id,
            'supplier_id'       => $supplier->id,
            'size'              => 'XL',
            'sku'               => 'CSC-RED-XL',
            'price'             => 2499,
            'cost_price'        => 1200,
            'stock'             => 40,
            'reorder_threshold' => 5,
        ]);

        // Create orders for fast variant in last 7 days (e.g. 14 units sold => 2 units/day velocity)
        $order = Order::create([
            'reference'         => 'ORD-FC-01',
            'channel'           => 'online',
            'status'            => Order::STATUS_DELIVERED,
            'subtotal'          => 2499 * 14,
            'shipping_charge'   => 0,
            'cod_charge'        => 0,
            'discount'          => 0,
            'total'             => 2499 * 14,
            'payment_method'    => 'razorpay',
            'payment_status'    => Order::PAYMENT_PAID,
            'shipping_name'     => 'Aarav Gupta',
            'shipping_phone'    => '9876543210',
            'shipping_email'    => 'aarav@example.com',
            'shipping_address1' => 'Park Street',
            'shipping_city'     => 'Kolkata',
            'shipping_state'    => 'West Bengal',
            'shipping_pincode'  => '700016',
            'created_at'        => now()->subDays(2),
        ]);

        OrderItem::create([
            'order_id'           => $order->id,
            'product_id'         => $product->id,
            'product_color_id'   => $color->id,
            'product_variant_id' => $fastVariant->id,
            'product_name'       => $product->name,
            'product_slug'       => $product->slug,
            'color_name'         => $color->name,
            'color_hex'          => $color->color_hex,
            'size'               => $fastVariant->size,
            'price'              => 2499,
            'qty'                => 14,
            'line_total'         => 2499 * 14,
            'created_at'         => now()->subDays(2),
        ]);

        return [$admin, $fastVariant, $deadVariant];
    }

    public function test_forecasting_dashboard_loads()
    {
        [$admin] = $this->createTestData();

        $response = $this->actingAs($admin)->get(route('admin.inventory.forecasting'));
        $response->assertStatus(200);
        $response->assertSee('Demand Forecasting &amp; Velocity Engine', false);
        $response->assertSee('Stockout Risk');
        $response->assertSee('Dead Stock Capital');
        $response->assertSee('Sales Velocity (Run Rate)');
        $response->assertSee('Stock Turnover Ratio');
        $response->assertSee('CSC-RED-S');
        $response->assertSee('CSC-RED-XL');
    }

    public function test_filter_by_dead_stock()
    {
        [$admin] = $this->createTestData();

        $response = $this->actingAs($admin)->get(route('admin.inventory.forecasting', ['risk' => 'dead_stock']));
        $response->assertStatus(200);
        $response->assertSee('CSC-RED-XL');
        $response->assertDontSee('CSC-RED-S');
    }

    public function test_forecasting_csv_export()
    {
        [$admin] = $this->createTestData();

        $response = $this->actingAs($admin)->get(route('admin.inventory.forecasting.export'));
        $response->assertStatus(200);
        $this->assertStringContainsString('text/csv', $response->headers->get('Content-Type'));
    }
}
