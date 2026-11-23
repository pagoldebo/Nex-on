<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductVariant;
use App\Models\Template;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorefrontQuickViewTest extends TestCase
{
    use RefreshDatabase;

    protected Category $category;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $templates = ['saaya', 'buttercup', 'vyra', 'lumiere', 'organic'];
        foreach ($templates as $key) {
            Template::create(['key' => $key, 'name' => ucfirst($key), 'is_active' => ($key === 'buttercup')]);
        }

        $this->category = Category::create(['name' => 'Lingerie Sets', 'slug' => 'lingerie-sets']);

        $this->product = Product::create([
            'category_id'  => $this->category->id,
            'name'         => 'Velvet Lace Bralette',
            'slug'         => 'velvet-lace-bralette',
            'description'  => 'Luxurious soft velvet bralette with delicate eyelash lace.',
            'badge'        => 'NEW',
            'is_featured'  => true,
            'is_bestseller'=> true,
            'is_active'    => true,
        ]);

        $color = ProductColor::create([
            'product_id' => $this->product->id,
            'name'       => 'Blush Rose',
            'hex'        => '#e0a899',
        ]);

        ProductVariant::create([
            'product_id'       => $this->product->id,
            'product_color_id' => $color->id,
            'sku'              => 'VEL-BRAL-001',
            'size'             => 'S',
            'price'            => 1499,
            'compare_at_price' => 1999,
            'stock'            => 15,
        ]);
    }

    public function test_quick_view_modal_renders_on_home_across_all_themes(): void
    {
        $themes = ['saaya', 'buttercup', 'vyra', 'lumiere', 'organic'];

        foreach ($themes as $themeKey) {
            Template::where('key', $themeKey)->update(['is_active' => true]);
            Template::where('key', '!=', $themeKey)->update(['is_active' => false]);
            Template::clearActiveCache();

            $response = $this->get(route('home'));

            $response->assertOk();
            $response->assertSee('id="quickViewModal"', false);
            $response->assertSee('openQuickView', false);
            $response->assertSee('id="qvName"', false);
            $response->assertSee('id="qvImage"', false);
            $response->assertSee('id="qvPrice"', false);
            $response->assertSee('id="qvAddBtn"', false);
        }
    }

    public function test_quick_view_modal_renders_on_category_across_all_themes(): void
    {
        $themes = ['saaya', 'buttercup', 'vyra', 'lumiere', 'organic'];

        foreach ($themes as $themeKey) {
            Template::where('key', $themeKey)->update(['is_active' => true]);
            Template::where('key', '!=', $themeKey)->update(['is_active' => false]);
            Template::clearActiveCache();

            $response = $this->get(route('category.show', $this->category->slug));

            $response->assertOk();
            $response->assertSee('id="quickViewModal"', false);
            $response->assertSee('openQuickView', false);
        }
    }

    public function test_quick_view_modal_renders_on_search_across_all_themes(): void
    {
        $themes = ['saaya', 'buttercup', 'vyra', 'lumiere', 'organic'];

        foreach ($themes as $themeKey) {
            Template::where('key', $themeKey)->update(['is_active' => true]);
            Template::where('key', '!=', $themeKey)->update(['is_active' => false]);
            Template::clearActiveCache();

            $response = $this->get(route('search', ['q' => 'Velvet']));

            $response->assertOk();
            $response->assertSee('id="quickViewModal"', false);
            $response->assertSee('openQuickView', false);
        }
    }

    public function test_quick_view_contains_view_details_link_and_color_options(): void
    {
        Template::where('key', 'buttercup')->update(['is_active' => true]);
        Template::clearActiveCache();

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('id="qvViewDetails"', false);
        $response->assertSee('id="colorOptions"', false);
        $response->assertSee('id="sizeOptions"', false);
        $response->assertSee('Quick View');
    }

    public function test_quick_view_active_size_pill_contrast_styling_exists(): void
    {
        $saayaCss = file_get_contents(public_path('css/themes/saaya.css'));
        $this->assertStringContainsString('.qv-size.active', $saayaCss);
        $this->assertStringContainsString('#F7F1E6', $saayaCss);
        $this->assertStringContainsString('#3D0C11', $saayaCss);

        foreach (['buttercup', 'lumiere', 'organic', 'vyra'] as $theme) {
            $css = file_get_contents(public_path("css/themes/{$theme}.css"));
            $this->assertStringContainsString('.qv-size.active', $css);
        }

        // Verify Saaya quick-view partial includes dynamic contrast toggling
        $saayaQuickView = file_get_contents(resource_path('views/storefront/saaya/partials/quick-view.blade.php'));
        $this->assertStringContainsString("b.classList.toggle('text-maroon', !on)", $saayaQuickView);
        $this->assertStringContainsString("b.style.setProperty('color', '#F7F1E6', 'important')", $saayaQuickView);
    }
}
