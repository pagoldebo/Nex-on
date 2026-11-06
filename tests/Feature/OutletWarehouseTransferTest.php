<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Outlet;
use App\Models\OutletStock;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductVariant;
use App\Models\StockTransfer;
use App\Models\User;
use App\Services\OutletInventoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OutletWarehouseTransferTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Outlet $origin;
    protected Outlet $destination;
    protected ProductVariant $variant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);

        $category = Category::create([
            'name'       => 'Pure Silk',
            'slug'       => 'pure-silk',
            'is_active'  => true,
            'sort_order' => 1,
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name'        => 'Silk Chemise Nightdress',
            'slug'        => 'silk-chemise-nightdress',
            'sku'         => 'SCN-001',
            'is_active'   => true,
        ]);

        $color = ProductColor::create([
            'product_id' => $product->id,
            'name'       => 'Champagne Gold',
            'hex'        => '#F5E6C8',
            'is_active'  => true,
        ]);

        $this->variant = ProductVariant::create([
            'product_id'       => $product->id,
            'product_color_id' => $color->id,
            'size'             => 'M',
            'sku'              => 'SCN-001-GOLD-M',
            'price'            => 4999.00,
            'stock'            => 100,
            'is_active'        => true,
        ]);

        $this->origin = Outlet::create([
            'name'       => 'Central Distribution Hub',
            'code'       => 'WH-MUM-01',
            'type'       => 'warehouse',
            'city'       => 'Mumbai',
            'is_central' => true,
            'is_active'  => true,
            'channel'    => 'all',
        ]);

        $this->destination = Outlet::create([
            'name'       => 'Bandra Boutique Store',
            'code'       => 'STR-BOM-02',
            'type'       => 'store',
            'city'       => 'Mumbai',
            'is_central' => false,
            'is_active'  => true,
            'channel'    => 'pos',
        ]);
    }

    public function test_admin_can_view_outlets_list_and_register_new_outlet()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.outlets.index'));

        $response->assertStatus(200);
        $response->assertSee('Outlets &amp; Warehouse Network', false);
        $response->assertSee('Central Distribution Hub');
        $response->assertSee('Bandra Boutique Store');

        $payload = [
            'name'       => 'Delhi Flagship Showroom',
            'code'       => 'STR-DEL-01',
            'type'       => 'store',
            'city'       => 'New Delhi',
            'channel'    => 'pos',
            'is_active'  => '1',
        ];

        $postResp = $this->actingAs($this->admin)->post(route('admin.outlets.store'), $payload);
        $postResp->assertRedirect();
        $this->assertDatabaseHas('outlets', ['code' => 'STR-DEL-01']);
    }

    public function test_admin_can_adjust_stock_at_specific_outlet()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.outlets.show', $this->origin));
        $response->assertStatus(200);
        $response->assertSee('Stock Adjustment');

        $adjustPayload = [
            'product_variant_id' => $this->variant->id,
            'stock_delta'        => 50,
            'shelf_bin_location' => 'Rack A - Bin 3',
        ];

        $postResp = $this->actingAs($this->admin)->post(route('admin.outlets.adjustStock', $this->origin), $adjustPayload);
        $postResp->assertRedirect();

        $this->assertDatabaseHas('outlet_stocks', [
            'outlet_id'          => $this->origin->id,
            'product_variant_id' => $this->variant->id,
            'stock'              => 50,
            'shelf_bin_location' => 'Rack A - Bin 3',
        ]);
    }

    public function test_inter_store_transfer_lifecycle_draft_dispatch_receive()
    {
        $service = app(OutletInventoryService::class);

        // 1. Initial stock at origin
        $service->adjustStock($this->origin, $this->variant, 60);
        $this->assertEquals(60, $service->getStock($this->origin, $this->variant));
        $this->assertEquals(0, $service->getStock($this->destination, $this->variant));

        // 2. Initiate Transfer
        $transferPayload = [
            'from_outlet_id' => $this->origin->id,
            'to_outlet_id'   => $this->destination->id,
            'notes'          => 'Stock balancing for weekend high traffic',
            'items'          => [
                [
                    'variant_id' => $this->variant->id,
                    'qty'        => 20,
                ],
            ],
        ];

        $initResp = $this->actingAs($this->admin)->post(route('admin.transfers.store'), $transferPayload);
        $initResp->assertRedirect();

        $transfer = StockTransfer::where('from_outlet_id', $this->origin->id)->first();
        $this->assertNotNull($transfer);
        $this->assertEquals('draft', $transfer->status);
        $this->assertEquals(20, $transfer->totalSentQty());

        // 3. Dispatch Transfer
        $dispatchResp = $this->actingAs($this->admin)->post(route('admin.transfers.dispatch', $transfer));
        $dispatchResp->assertRedirect();

        $transfer->refresh();
        $this->assertEquals('dispatched', $transfer->status);
        // Origin stock should be reduced from 60 to 40
        $this->assertEquals(40, $service->getStock($this->origin, $this->variant));
        // Destination stock still 0 (in-transit)
        $this->assertEquals(0, $service->getStock($this->destination, $this->variant));

        // 4. Receive Transfer
        $receivePayload = [
            'received_qty' => [
                $transfer->items->first()->id => 20,
            ],
        ];

        $receiveResp = $this->actingAs($this->admin)->post(route('admin.transfers.receive', $transfer), $receivePayload);
        $receiveResp->assertRedirect();

        $transfer->refresh();
        $this->assertEquals('received', $transfer->status);
        // Destination stock should now be 20
        $this->assertEquals(20, $service->getStock($this->destination, $this->variant));
    }

    public function test_cannot_transfer_to_same_outlet()
    {
        $transferPayload = [
            'from_outlet_id' => $this->origin->id,
            'to_outlet_id'   => $this->origin->id, // identical
            'items'          => [
                [
                    'variant_id' => $this->variant->id,
                    'qty'        => 5,
                ],
            ],
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.transfers.store'), $transferPayload);
        $response->assertSessionHasErrors(['to_outlet_id']);
    }

    public function test_cannot_dispatch_with_insufficient_stock()
    {
        $service = app(OutletInventoryService::class);
        $service->adjustStock($this->origin, $this->variant, 5); // only 5 units in stock

        $transfer = $service->createTransfer($this->origin, $this->destination, [
            ['variant_id' => $this->variant->id, 'qty' => 50], // demands 50
        ]);

        $dispatchResp = $this->actingAs($this->admin)->post(route('admin.transfers.dispatch', $transfer));
        $dispatchResp->assertRedirect();
        $dispatchResp->assertSessionHas('error');

        $transfer->refresh();
        $this->assertEquals('draft', $transfer->status); // Remains draft
    }
}
