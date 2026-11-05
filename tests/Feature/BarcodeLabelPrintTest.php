<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Template;
use App\Models\User;
use App\Services\BarcodeGeneratorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BarcodeLabelPrintTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'email' => 'admin.barcode@nexon.luxury',
            'role'  => 'admin',
        ]);

        Template::create(['key' => 'buttercup', 'name' => 'Buttercup', 'is_active' => true]);

        $category = Category::create([
            'name'      => 'Fine Jewelry',
            'slug'      => 'fine-jewelry',
            'is_active' => true,
        ]);

        $this->product = Product::create([
            'category_id' => $category->id,
            'name'        => 'Diamond Solitaire Pendant',
            'slug'        => 'diamond-solitaire-pendant',
            'sku'         => 'JWL-DIA-999',
            'is_active'   => true,
        ]);
    }

    public function test_barcode_studio_loads_successfully()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.barcode.index'));

        $response->assertStatus(200);
        $response->assertSee('Barcode &amp; Thermal Label Generator', false);
        $response->assertSee('Diamond Solitaire Pendant');
        $response->assertSee('JWL-DIA-999');
    }

    public function test_barcode_preview_svg_endpoint()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.barcode.preview', ['code' => 'TEST-SKU-123']));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/svg+xml');
        $response->assertSee('<svg', false);
        $response->assertSee('TEST-SKU-123');
    }

    public function test_barcode_print_renders_thermal_view_with_specified_copies()
    {
        $response = $this->actingAs($this->admin)->post(route('admin.barcode.print'), [
            'size'            => '50x25',
            'store_name'      => 'NEXON TEST STORE',
            'show_store_name' => 1,
            'show_price'      => 1,
            'show_sku'        => 1,
            'items'           => [
                [
                    'id'     => $this->product->id,
                    'sku'    => $this->product->sku,
                    'name'   => $this->product->name,
                    'price'  => 12999.00,
                    'copies' => 3,
                ],
            ],
        ]);

        $response->assertStatus(200);
        $response->assertSee('Thermal Barcode Labels (3 pcs)', false);
        $response->assertSee('NEXON TEST STORE');
        $response->assertSee('JWL-DIA-999');
        $response->assertSee('12,999.00');
        $response->assertSee('window.print()', false);
    }

    public function test_barcode_service_pure_php_code128_svg_generation()
    {
        $service = new BarcodeGeneratorService();
        $svg = $service->generateSvg('CODE128-TEST', 2, 40, true);

        $this->assertStringStartsWith('<svg', $svg);
        $this->assertStringEndsWith('</svg>', $svg);
        $this->assertStringContainsString('CODE128-TEST', $svg);
        $this->assertStringContainsString('rect', $svg);
    }
}

