<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductVariant;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseOrderTest extends TestCase
{
    use RefreshDatabase;

    protected function setupSeedData(): array
    {
        $admin = User::create([
            'name'     => 'Admin User',
            'email'    => 'admin@po.test',
            'phone'    => '9999999999',
            'password' => bcrypt('password'),
            'role'     => 'admin',
        ]);

        $supplier = Supplier::create([
            'name'           => 'Test Supplier Co',
            'code'           => 'SUP-TEST-01',
            'contact_name'   => 'Contact Person',
            'phone'          => '9876543210',
            'lead_time_days' => 5,
            'is_active'      => true,
        ]);

        $category = Category::create([
            'name'      => 'Sleepwear',
            'slug'      => 'sleepwear',
            'is_active' => true,
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name'        => 'Silk Robe',
            'slug'        => 'silk-robe',
            'sku'         => 'ROBE-01',
            'price'       => 2499,
            'is_active'   => true,
        ]);

        $color = ProductColor::create([
            'product_id' => $product->id,
            'name'       => 'Emerald Green',
            'hex'        => '#059669',
        ]);

        $variant = ProductVariant::create([
            'product_id'        => $product->id,
            'product_color_id'  => $color->id,
            'supplier_id'       => $supplier->id,
            'size'              => 'L',
            'sku'               => 'ROBE-01-L',
            'price'             => 2499,
            'cost_price'        => 950,
            'stock'             => 3, // Low stock <= threshold (5)
            'reorder_threshold' => 5,
            'reorder_qty'       => 25,
        ]);

        return [$admin, $supplier, $variant];
    }

    public function test_purchase_orders_index_loads()
    {
        [$admin] = $this->setupSeedData();

        $response = $this->actingAs($admin)->get(route('admin.purchase-orders.index'));
        $response->assertStatus(200);
        $response->assertSee('Purchase Orders');
    }

    public function test_draft_purchase_order_creation()
    {
        [$admin, $supplier, $variant] = $this->setupSeedData();

        $response = $this->actingAs($admin)->post(route('admin.purchase-orders.store'), [
            'po_number'              => 'PO-20261016-001',
            'supplier_id'            => $supplier->id,
            'expected_delivery_date' => now()->addDays(7)->format('Y-m-d'),
            'notes'                  => 'Urgent stock replenishment',
            'items'                  => [
                [
                    'variant_id'  => $variant->id,
                    'ordered_qty' => 30,
                    'unit_cost'   => 950,
                ],
            ],
        ]);

        $po = PurchaseOrder::where('po_number', 'PO-20261016-001')->first();
        $this->assertNotNull($po);
        $this->assertEquals(30 * 950, $po->total_amount);
        $this->assertEquals(PurchaseOrder::STATUS_DRAFT, $po->status);
        $this->assertEquals(1, $po->items()->count());
    }

    public function test_grn_goods_received_note_workflow_updates_stock_and_logs()
    {
        [$admin, $supplier, $variant] = $this->setupSeedData();
        $initialStock = $variant->stock; // 3

        $po = PurchaseOrder::create([
            'po_number'    => 'PO-20261016-002',
            'supplier_id'  => $supplier->id,
            'created_by'   => $admin->id,
            'status'       => PurchaseOrder::STATUS_SENT,
            'total_amount' => 10000,
        ]);

        $item = $po->items()->create([
            'product_variant_id' => $variant->id,
            'ordered_qty'        => 20,
            'received_qty'       => 0,
            'unit_cost'          => 500,
            'line_total'         => 10000,
        ]);

        // Receive partial shipment of 12 units
        $response = $this->actingAs($admin)->post(route('admin.purchase-orders.receiveGoods', $po), [
            'received'  => [$item->id => 12],
            'grn_notes' => 'Received Batch A at loading dock',
        ]);

        $response->assertSessionHas('success');

        $variant->refresh();
        $this->assertEquals($initialStock + 12, $variant->stock);

        $po->refresh();
        $this->assertEquals(PurchaseOrder::STATUS_PARTIAL, $po->status);

        // Receive remaining 8 units -> status should become received
        $this->actingAs($admin)->post(route('admin.purchase-orders.receiveGoods', $po), [
            'received' => [$item->id => 8],
        ]);

        $variant->refresh();
        $this->assertEquals($initialStock + 20, $variant->stock);

        $po->refresh();
        $this->assertEquals(PurchaseOrder::STATUS_RECEIVED, $po->status);
    }

    public function test_auto_generate_pos_from_low_stock_thresholds()
    {
        [$admin, $supplier, $variant] = $this->setupSeedData();
        $this->assertTrue($variant->stock <= $variant->reorder_threshold);

        $response = $this->actingAs($admin)->post(route('admin.purchase-orders.autoGenerate'));
        $response->assertRedirect(route('admin.purchase-orders.index'));
        $response->assertSessionHas('success');

        $this->assertTrue(PurchaseOrder::where('supplier_id', $supplier->id)->exists());
    }

    public function test_inventory_check_reorder_artisan_command()
    {
        $this->setupSeedData();

        $this->artisan('inventory:check-reorder --auto-po')
            ->assertSuccessful();
    }
}
