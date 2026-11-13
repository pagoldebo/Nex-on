<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductVariant;
use App\Models\Template;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaleSectionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Template::create(['key' => 'saaya',     'name' => 'Saaya',     'is_active' => true]);
        Template::create(['key' => 'buttercup', 'name' => 'Buttercup', 'is_active' => false]);
        Template::create(['key' => 'vyra',      'name' => 'Vyra',      'is_active' => false]);
        Template::create(['key' => 'lumiere',   'name' => 'Lumière',   'is_active' => false]);

        $category = Category::create([
            'name'       => 'Bralettes',
            'slug'       => 'bralettes',
            'sort_order' => 1,
        ]);

        $product = Product::create([
            'category_id'  => $category->id,
            'name'         => 'Scarlet Underwire Satin Corset Top',
            'slug'         => 'scarlet-underwire-satin-corset-top',
            'sku'          => 'LS-SCARLET-01',
            'badge'        => 'SALE',
            'is_active'    => true,
            'is_featured'  => true,
            'is_bestseller'=> false,
        ]);

        $color = ProductColor::create([
            'product_id' => $product->id,
            'name'       => 'Crimson',
            'hex'        => '#9B1D20',
            'sort_order' => 1,
        ]);

        ProductVariant::create([
            'product_id'       => $product->id,
            'product_color_id' => $color->id,
            'sku'              => 'LS-SCARLET-01-S',
            'size'             => 'S',
            'price'            => 2499,
            'compare_at_price' => 2999,
            'stock'            => 10,
        ]);
    }

    public function test_sale_section_renders_on_all_themes(): void
    {
        $themes = ['saaya', 'buttercup', 'vyra', 'lumiere'];

        foreach ($themes as $themeKey) {
            Template::query()->update(['is_active' => false]);
            Template::where('key', $themeKey)->update(['is_active' => true]);
            \Illuminate\Support\Facades\Cache::forget('active_template');

            $response = $this->get(route('home'));
            $response->assertStatus(200);
            $response->assertSee('id="saleSection"', false);
            $response->assertSee('id="saleFilterWrap"', false);
            $response->assertSee('Scarlet Underwire Satin Corset Top');
            $response->assertSee('2,499');
            $response->assertSee('2,999');
            $response->assertSee('data-filter="all"', false);
            $response->assertSee('data-filter="bralettes"', false);
        }
    }

    public function test_lumiere_sale_card_renders_quick_view_triggers(): void
    {
        Template::query()->update(['is_active' => false]);
        Template::where('key', 'lumiere')->update(['is_active' => true]);
        \Illuminate\Support\Facades\Cache::forget('active_template');

        $response = $this->get(route('home'));
        $response->assertStatus(200);

        // Verify Quick View modal is included on the page
        $response->assertSee('id="quickViewModal"', false);

        // Verify sale card has quick-view trigger and quick-view button
        $response->assertSee('openQuickView(', false);
        $response->assertSee('Quick View', false);
    }
}
