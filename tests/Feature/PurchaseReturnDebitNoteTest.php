<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductVariant;
use App\Models\PurchaseOrder;
use App\Models\PurchaseReturn;
use App\Models\Supplier;
use App\Models\Template;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseReturnDebitNoteTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Supplier $supplier;
    protected ProductVariant $variant;
    protected PurchaseOrder $purchaseOrder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'email' => 'admin.returns@nexon.luxury',
            'role'  => 'admin',
        ]);

        Template::create(['key' => 'buttercup', 'name' => 'Buttercup', 'is_active' => true]);

        $this->supplier = Supplier::create([
            'name'       => 'Mysore Raw Silk Mill',
            'code'       => 'MYS-SILK',
            'email'      => 'supply@mysoresilk.in',
            'gst_number' => '29AAAFM4321B1Z2',
            'is_active'  => true,
        ]);

        $category = Category::create(['name' => 'Raw Silk', 'slug' => 'raw-silk']);
        $product = Product::create(['category_id' => $category->id, 'name' => 'Mysore Crepe Silk Fabric', 'slug' => 'mysore-crepe-silk-fabric']);
        $color = ProductColor::create(['product_id' => $product->id, 'name' => 'Royal Gold', 'hex' => '#eab308']);
        $this->variant = ProductVariant::create([
            'product_id'       => $product->id,
            'product_color_id' => $color->id,
            'sku'              => 'MYS-CREP-01',
            'size'             => 'Roll 50m',
            'price'            => 45000,
            'stock'            => 25, // Starting stock
        ]);

        $this->purchaseOrder = PurchaseOrder::create([
            'po_number'    => 'PO-2026-9900',
            'supplier_id'  => $this->supplier->id,
            'created_by'   => $this->admin->id,
            'status'       => 'received',
            'total_amount' => 90000,
        ]);
    }

    public function test_purchase_returns_index_loads_successfully()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.purchase-returns.index'));

        $response->assertStatus(200);
        $response->assertSee('Purchase Returns &amp; Debit Notes', false);
        $response->assertSee('Mysore Raw Silk Mill');
    }

    public function test_admin_can_issue_purchase_return_and_debit_note_with_stock_decrement()
    {
        $this->assertEquals(25, $this->variant->stock);

        $response = $this->actingAs($this->admin)->post(route('admin.purchase-returns.store'), [
            'supplier_id'       => $this->supplier->id,
            'purchase_order_id' => $this->purchaseOrder->id,
            'return_date'       => now()->toDateString(),
            'reason'            => 'quality_rejection',
            'notes'             => 'Weaving defects noticed across 5 rolls',
            'items'             => [
                [
                    'variant_id' => $this->variant->id,
                    'quantity'   => 5,
                    'unit_cost'  => 8000.00,
                    'reason'     => 'Weft thread irregularities',
                ],
            ],
        ]);

        $response->assertRedirect(route('admin.purchase-returns.index'));

        // Subtotal = 5 * 8000 = 40000; Tax (18%) = 7200; Total = 47200
        $this->assertDatabaseHas('purchase_returns', [
            'supplier_id'       => $this->supplier->id,
            'purchase_order_id' => $this->purchaseOrder->id,
            'reason'            => 'quality_rejection',
            'subtotal'          => 40000.00,
            'tax_amount'        => 7200.00,
            'total_amount'      => 47200.00,
            'status'            => 'approved',
        ]);

        $return = PurchaseReturn::latest()->first();
        $this->assertStringStartsWith('DN-', $return->debit_note_number);
        $this->assertCount(1, $return->items);

        // Verify stock automatically decremented from 25 to 20
        $this->variant->refresh();
        $this->assertEquals(20, $this->variant->stock);
    }

    public function test_admin_can_update_return_status()
    {
        $return = PurchaseReturn::create([
            'supplier_id'       => $this->supplier->id,
            'purchase_order_id' => $this->purchaseOrder->id,
            'return_date'       => now()->toDateString(),
            'reason'            => 'defective',
            'status'            => 'pending',
            'subtotal'          => 10000,
            'tax_amount'        => 1800,
            'total_amount'      => 11800,
        ]);

        $response = $this->actingAs($this->admin)->patch(route('admin.purchase-returns.status', $return), [
            'status' => 'credited',
        ]);

        $response->assertRedirect(route('admin.purchase-returns.index'));
        $return->refresh();
        $this->assertEquals('credited', $return->status);
    }

    public function test_print_gst_debit_note_voucher_view()
    {
        $return = PurchaseReturn::create([
            'supplier_id'       => $this->supplier->id,
            'purchase_order_id' => $this->purchaseOrder->id,
            'return_date'       => now()->toDateString(),
            'reason'            => 'wrong_item',
            'status'            => 'approved',
            'subtotal'          => 20000,
            'tax_amount'        => 3600,
            'total_amount'      => 23600,
        ]);

        $return->items()->create([
            'product_variant_id' => $this->variant->id,
            'product_name'       => $this->variant->product->name,
            'sku'                => $this->variant->sku,
            'quantity'           => 2,
            'unit_cost'          => 10000,
            'line_total'         => 20000,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.purchase-returns.print', $return));

        $response->assertStatus(200);
        $response->assertSee('DEBIT NOTE');
        $response->assertSee($return->debit_note_number);
        $response->assertSee('Mysore Raw Silk Mill');
        $response->assertSee('23,600.00');
    }
}

