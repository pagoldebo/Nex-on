<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductVariant;
use App\Models\Template;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckDeliveryAvailabilityTest extends TestCase
{
    use RefreshDatabase;

    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        Template::create(['key' => 'buttercup', 'name' => 'Buttercup', 'is_active' => true]);
        Template::create(['key' => 'lumiere',   'name' => 'Lumière',   'is_active' => false]);
        Template::create(['key' => 'organic',   'name' => 'Organic',   'is_active' => false]);
        Template::create(['key' => 'saaya',     'name' => 'Saaya',     'is_active' => false]);
        Template::create(['key' => 'vyra',      'name' => 'VYRA',      'is_active' => false]);

        $category = Category::create([
            'name'       => 'Couture',
            'slug'       => 'couture',
            'sort_order' => 1,
        ]);

        $this->product = Product::create([
            'category_id' => $category->id,
            'name'        => 'Aura Velvet Bodysuit',
            'slug'        => 'aura-velvet-bodysuit',
            'sku'         => 'AVB-001',
            'price'       => 1899,
            'is_active'   => true,
        ]);

        $color = ProductColor::create([
            'product_id' => $this->product->id,
            'name'       => 'Black',
            'hex'        => '#000000',
            'sort_order' => 1,
        ]);

        ProductVariant::create([
            'product_id'       => $this->product->id,
            'product_color_id' => $color->id,
            'sku'              => 'AVB-001-S',
            'size'             => 'S',
            'stock'            => 10,
            'price'            => 1899,
        ]);
    }

    public function test_check_delivery_availability_section_renders_across_all_themes(): void
    {
        foreach (['buttercup', 'lumiere', 'organic', 'saaya', 'vyra'] as $themeKey) {
            Template::query()->update(['is_active' => false]);
            Template::where('key', $themeKey)->update(['is_active' => true]);
            \Illuminate\Support\Facades\Cache::forget('active_template');

            $response = $this->get('/products/' . $this->product->slug);
            $response->assertStatus(200);
            $response->assertSee('Check Delivery Availability');
        }
    }

    public function test_pincode_check_validates_input(): void
    {
        $response = $this->postJson(route('pincode.check'), ['pincode' => '123']);
        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'message' => 'Please enter a valid 6-digit Indian PIN code.',
        ]);
    }

    public function test_pincode_check_returns_delivery_details(): void
    {
        $response = $this->postJson(route('pincode.check'), ['pincode' => '110001']);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'serviceable',
                'pincode',
                'city',
                'state',
                'cod_available',
                'delivery_days',
                'estimated_days',
                'estimated_date',
                'message',
            ],
        ]);
        $response->assertJson([
            'success' => true,
            'data'    => [
                'pincode' => '110001',
            ],
        ]);
    }
}
