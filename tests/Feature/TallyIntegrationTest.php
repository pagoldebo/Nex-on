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

class TallyIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setupSeedData(): array
    {
        $admin = User::create([
            'name'     => 'Admin User',
            'email'    => 'admin@tally.test',
            'phone'    => '9999999999',
            'password' => bcrypt('password'),
            'role'     => 'admin',
        ]);

        $category = Category::create([
            'name'      => 'Nightwear',
            'slug'      => 'nightwear',
            'is_active' => true,
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name'        => 'Lace Chemise',
            'slug'        => 'lace-chemise',
            'sku'         => 'CHEM-01',
            'price'       => 1999,
            'is_active'   => true,
        ]);

        $color = ProductColor::create([
            'product_id' => $product->id,
            'name'       => 'Noir Black',
            'hex'        => '#111111',
        ]);

        $variant = ProductVariant::create([
            'product_id'       => $product->id,
            'product_color_id' => $color->id,
            'size'             => 'S',
            'sku'              => 'CHEM-01-S',
            'price'            => 1999,
            'stock'            => 15,
        ]);

        $order = Order::create([
            'reference'         => 'SAA-20261016-0001',
            'channel'           => 'online',
            'status'            => Order::STATUS_DELIVERED,
            'subtotal'          => 1999,
            'shipping_charge'   => 0,
            'cod_charge'        => 0,
            'discount'          => 0,
            'total'             => 1999,
            'payment_method'    => 'upi',
            'payment_status'    => Order::PAYMENT_PAID,
            'payment_reference' => 'UPI-TXN-12345',
            'shipping_name'     => 'Pooja Hegde',
            'shipping_phone'    => '9888877777',
            'shipping_email'    => 'pooja@example.com',
            'shipping_address1' => 'Flat 402, Royal Palms',
            'shipping_city'     => 'Bengaluru',
            'shipping_state'    => 'Karnataka',
            'shipping_pincode'  => '560001',
        ]);

        $order->items()->create([
            'product_id'         => $product->id,
            'product_color_id'   => $color->id,
            'product_variant_id' => $variant->id,
            'sku'                => $variant->sku,
            'product_name'       => $product->name,
            'product_slug'       => $product->slug,
            'color_name'         => $color->name,
            'color_hex'          => $color->hex,
            'size'               => $variant->size,
            'price'              => 1999,
            'qty'                => 1,
            'line_total'         => 1999,
        ]);

        return [$admin, $order];
    }

    public function test_tally_hub_index_loads()
    {
        [$admin] = $this->setupSeedData();

        $response = $this->actingAs($admin)->get(route('admin.integrations.tally.index'));
        $response->assertStatus(200);
        $response->assertSee('Tally Prime');
        $response->assertSee('Export Sales XML');
    }

    public function test_export_sales_vouchers_xml()
    {
        [$admin, $order] = $this->setupSeedData();

        $response = $this->actingAs($admin)->get(route('admin.integrations.tally.exportSales'));
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/xml; charset=utf-8');

        $content = $response->getContent();
        $this->assertStringContainsString('<ENVELOPE>', $content);
        $this->assertStringContainsString('<VOUCHER VCHTYPE="Sales"', $content);
        $this->assertStringContainsString($order->reference, $content);
        $this->assertStringContainsString('Pooja Hegde', $content);
        $this->assertStringContainsString('Output CGST @ 9%', $content);
    }

    public function test_export_receipt_vouchers_xml()
    {
        [$admin, $order] = $this->setupSeedData();

        $response = $this->actingAs($admin)->get(route('admin.integrations.tally.exportReceipts'));
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/xml; charset=utf-8');

        $content = $response->getContent();
        $this->assertStringContainsString('<ENVELOPE>', $content);
        $this->assertStringContainsString('<VOUCHER VCHTYPE="Receipt"', $content);
        $this->assertStringContainsString('HDFC Bank Retail Current A/c (UPI)', $content);
    }

    public function test_single_order_sync_creates_accounting_sync_log()
    {
        [$admin, $order] = $this->setupSeedData();

        $response = $this->actingAs($admin)->post(route('admin.integrations.tally.syncOrder', $order));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('accounting_sync_logs', [
            'order_id'       => $order->id,
            'voucher_number' => $order->reference,
            'status'         => 'success',
        ]);
    }
}
