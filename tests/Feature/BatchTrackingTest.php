<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\ProductColor;
use App\Models\ProductVariant;
use App\Models\User;
use App\Services\BatchInventoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BatchTrackingTest extends TestCase
{
    use RefreshDatabase;

    protected function setupVariant(): array
    {
        $admin = User::create([
            'name'     => 'Batch Admin',
            'email'    => 'admin@batch.test',
            'phone'    => '9999999999',
            'password' => bcrypt('password'),
            'role'     => 'admin',
        ]);

        $category = Category::create([
            'name'       => 'Luxury Lingerie',
            'slug'       => 'luxury-lingerie',
            'is_active'  => true,
            'sort_order' => 1,
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name'        => 'Velvet Corset Bodysuit',
            'slug'        => 'velvet-corset-bodysuit',
            'price'       => 3499,
            'is_active'   => true,
        ]);

        $color = ProductColor::create([
            'product_id' => $product->id,
            'name'       => 'Midnight Black',
            'color_hex'  => '#000000',
            'color_slug' => 'midnight-black',
        ]);

        $variant = ProductVariant::create([
            'product_id'        => $product->id,
            'product_color_id'  => $color->id,
            'size'              => 'M',
            'sku'               => 'VCB-M-BLK',
            'price'             => 3499,
            'cost_price'        => 1500,
            'stock'             => 0,
            'reorder_threshold' => 5,
        ]);

        return [$admin, $variant, $product, $color];
    }

    public function test_batches_index_loads()
    {
        [$admin, $variant] = $this->setupVariant();

        ProductBatch::create([
            'product_variant_id' => $variant->id,
            'batch_number'       => 'B-2026-001',
            'expiry_date'        => now()->addMonths(6),
            'initial_qty'        => 50,
            'remaining_qty'      => 50,
            'status'             => 'active',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.inventory.batches.index'));
        $response->assertStatus(200);
        $response->assertSee('Batch, Lot &amp; Expiry Tracking', false);
        $response->assertSee('B-2026-001');
        $response->assertSee('VCB-M-BLK');
    }

    public function test_store_new_batch()
    {
        [$admin, $variant] = $this->setupVariant();

        $response = $this->actingAs($admin)->post(route('admin.inventory.batches.store'), [
            'product_variant_id' => $variant->id,
            'batch_number'       => 'BATCH-TEST-99',
            'lot_number'         => 'LOT-01',
            'mfg_date'           => now()->subMonth()->toDateString(),
            'expiry_date'        => now()->addYear()->toDateString(),
            'initial_qty'        => 30,
            'cost_price'         => 1450,
            'sync_variant_stock' => '1',
            'notes'              => 'First production run',
        ]);

        $response->assertRedirect(route('admin.inventory.batches.index'));
        $this->assertDatabaseHas('product_batches', [
            'batch_number'  => 'BATCH-TEST-99',
            'remaining_qty' => 30,
            'status'        => 'active',
        ]);

        // Stock should have incremented to 30
        $this->assertEquals(30, $variant->fresh()->stock);
    }

    public function test_fifo_picking_allocation_logic()
    {
        [$admin, $variant, $product, $color] = $this->setupVariant();

        // Create 2 batches: Batch 1 expires sooner (20 days), Batch 2 expires later (180 days)
        $batch1 = ProductBatch::create([
            'product_variant_id' => $variant->id,
            'batch_number'       => 'BATCH-EARLY',
            'expiry_date'        => now()->addDays(20),
            'initial_qty'        => 5,
            'remaining_qty'      => 5,
            'status'             => 'active',
        ]);

        $batch2 = ProductBatch::create([
            'product_variant_id' => $variant->id,
            'batch_number'       => 'BATCH-LATER',
            'expiry_date'        => now()->addDays(180),
            'initial_qty'        => 10,
            'remaining_qty'      => 10,
            'status'             => 'active',
        ]);

        // Order item demanding 7 units (should pick 5 from batch1 and 2 from batch2)
        $order = Order::create([
            'reference'         => 'ORD-BATCH-01',
            'channel'           => 'online',
            'status'            => Order::STATUS_CONFIRMED,
            'subtotal'          => 3499 * 7,
            'shipping_charge'   => 0,
            'cod_charge'        => 0,
            'discount'          => 0,
            'total'             => 3499 * 7,
            'payment_method'    => 'razorpay',
            'payment_status'    => Order::PAYMENT_PAID,
            'shipping_name'     => 'Devi Sen',
            'shipping_phone'    => '9876543210',
            'shipping_email'    => 'devi@example.com',
            'shipping_address1' => 'Park Circus',
            'shipping_city'     => 'Kolkata',
            'shipping_state'    => 'West Bengal',
            'shipping_pincode'  => '700017',
        ]);

        $orderItem = OrderItem::create([
            'order_id'           => $order->id,
            'product_id'         => $product->id,
            'product_color_id'   => $color->id,
            'product_variant_id' => $variant->id,
            'product_name'       => $product->name,
            'product_slug'       => $product->slug,
            'color_name'         => $color->name,
            'color_hex'          => $color->color_hex,
            'size'               => $variant->size,
            'price'              => 3499,
            'qty'                => 7,
            'line_total'         => 3499 * 7,
        ]);

        $batchService = app(BatchInventoryService::class);
        $allocations = $batchService->allocateFifo($orderItem);

        $this->assertCount(2, $allocations);

        // Batch 1 fully depleted
        $this->assertEquals(0, $batch1->fresh()->remaining_qty);
        $this->assertEquals('depleted', $batch1->fresh()->status);

        // Batch 2 decremented by 2
        $this->assertEquals(8, $batch2->fresh()->remaining_qty);
        $this->assertEquals('active', $batch2->fresh()->status);

        // Assert allocation records in DB
        $this->assertDatabaseHas('order_item_batches', [
            'order_item_id'    => $orderItem->id,
            'product_batch_id' => $batch1->id,
            'qty_allocated'    => 5,
        ]);
        $this->assertDatabaseHas('order_item_batches', [
            'order_item_id'    => $orderItem->id,
            'product_batch_id' => $batch2->id,
            'qty_allocated'    => 2,
        ]);
    }

    public function test_batch_recall_traceability_screen()
    {
        [$admin, $variant, $product, $color] = $this->setupVariant();

        $batch = ProductBatch::create([
            'product_variant_id' => $variant->id,
            'batch_number'       => 'BATCH-RECALL-01',
            'expiry_date'        => now()->addMonths(3),
            'initial_qty'        => 20,
            'remaining_qty'      => 15,
            'status'             => 'active',
        ]);

        $order = Order::create([
            'reference'         => 'ORD-RECALL-TR01',
            'channel'           => 'online',
            'status'            => Order::STATUS_DELIVERED,
            'subtotal'          => 3499 * 5,
            'shipping_charge'   => 0,
            'cod_charge'        => 0,
            'discount'          => 0,
            'total'             => 3499 * 5,
            'payment_method'    => 'razorpay',
            'payment_status'    => Order::PAYMENT_PAID,
            'shipping_name'     => 'Meera Kapoor',
            'shipping_phone'    => '9811122233',
            'shipping_email'    => 'meera@example.com',
            'shipping_address1' => 'Connaught Place',
            'shipping_city'     => 'New Delhi',
            'shipping_state'    => 'Delhi',
            'shipping_pincode'  => '110001',
        ]);

        $orderItem = OrderItem::create([
            'order_id'           => $order->id,
            'product_id'         => $product->id,
            'product_color_id'   => $color->id,
            'product_variant_id' => $variant->id,
            'product_name'       => $product->name,
            'product_slug'       => $product->slug,
            'color_name'         => $color->name,
            'color_hex'          => $color->color_hex,
            'size'               => $variant->size,
            'price'              => 3499,
            'qty'                => 5,
            'line_total'         => 3499 * 5,
        ]);

        $batchService = app(BatchInventoryService::class);
        $batchService->allocateFifo($orderItem);

        $response = $this->actingAs($admin)->get(route('admin.inventory.batches.trace', $batch));
        $response->assertStatus(200);
        $response->assertSee('BATCH-RECALL-01');
        $response->assertSee('Meera Kapoor');
        $response->assertSee('9811122233');
        $response->assertSee('ORD-RECALL-TR01');
    }
}
