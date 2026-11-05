<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductVariant;
use App\Models\Template;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CollectionRoutesTest extends TestCase
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
            'name'       => 'Silk & Satin',
            'slug'       => 'silk-satin',
            'sort_order' => 1,
        ]);

        $productFeatured = Product::create([
            'category_id'   => $category->id,
            'name'          => 'Royal Gold Embroidered Silk Chemise',
            'slug'          => 'royal-gold-embroidered-silk-chemise',
            'sku'           => 'LS-SILK-01',
            'badge'         => 'FEATURED',
            'is_active'     => true,
            'is_featured'   => true,
            'is_bestseller' => false,
        ]);

        $color1 = ProductColor::create([
            'product_id' => $productFeatured->id,
            'name'       => 'Gold',
            'hex'        => '#C9A24B',
            'sort_order' => 1,
        ]);

        ProductVariant::create([
            'product_id'       => $productFeatured->id,
            'product_color_id' => $color1->id,
            'sku'              => 'LS-SILK-01-M',
            'size'             => 'M',
            'price'            => 3999,
            'compare_at_price' => 4999,
            'stock'            => 10,
        ]);

        $productSale = Product::create([
            'category_id'   => $category->id,
            'name'          => 'Velvet Midnight Balconette Bra',
            'slug'          => 'velvet-midnight-balconette-bra',
            'sku'           => 'LS-VELVET-02',
            'badge'         => 'SALE 30% OFF',
            'is_active'     => true,
            'is_featured'   => false,
            'is_bestseller' => true,
        ]);

        $color2 = ProductColor::create([
            'product_id' => $productSale->id,
            'name'       => 'Midnight Blue',
            'hex'        => '#1A1A2E',
            'sort_order' => 1,
        ]);

        ProductVariant::create([
            'product_id'       => $productSale->id,
            'product_color_id' => $color2->id,
            'sku'              => 'LS-VELVET-02-34B',
            'size'             => '34B',
            'price'            => 1899,
            'compare_at_price' => 2699,
            'stock'            => 15,
        ]);
    }

    public function test_featured_products_route_renders_successfully(): void
    {
        $response = $this->get('/featured-products');
        $response->assertStatus(200);
        $response->assertSee('Featured Products');
        $response->assertSee('Royal Gold Embroidered Silk Chemise');

        $aliasResponse = $this->get('/collections/featured-products');
        $aliasResponse->assertStatus(200);
        $aliasResponse->assertSee('Featured Products');
    }

    public function test_sale_products_route_renders_successfully(): void
    {
        $response = $this->get('/collections/sale');
        $response->assertStatus(200);
        $response->assertSee('Sale & Special Offers');
        $response->assertSee('Velvet Midnight Balconette Bra');

        $aliasResponse = $this->get('/sale');
        $aliasResponse->assertStatus(200);
        $aliasResponse->assertSee('Sale & Special Offers');
    }

    public function test_new_arrivals_route_renders_successfully(): void
    {
        $response = $this->get('/new-arrivals');
        $response->assertStatus(200);
        $response->assertSee('New Arrivals & Launches');

        $alias1 = $this->get('/collections/new-arrivals');
        $alias1->assertStatus(200);

        $alias2 = $this->get('/new-launches');
        $alias2->assertStatus(200);
    }

    public function test_collections_all_and_category_slug_resolver(): void
    {
        $responseAll = $this->get('/collections/all');
        $responseAll->assertStatus(200);
        $responseAll->assertSee('All Products');

        $responseCat = $this->get('/collections/silk-satin');
        $responseCat->assertStatus(200);
        $responseCat->assertSee('Silk & Satin');
    }

    public function test_collection_routes_render_across_all_themes(): void
    {
        $themes = ['saaya', 'buttercup', 'vyra', 'lumiere'];

        foreach ($themes as $themeKey) {
            Template::query()->update(['is_active' => false]);
            Template::where('key', $themeKey)->update(['is_active' => true]);
            \Illuminate\Support\Facades\Cache::forget('active_template');

            $featured = $this->get('/featured-products');
            $featured->assertStatus(200);

            $sale = $this->get('/collections/sale');
            $sale->assertStatus(200);

            $newArrivals = $this->get('/new-arrivals');
            $newArrivals->assertStatus(200);

            // Test sort query string
            $sorted = $this->get('/featured-products?sort=price_asc');
            $sorted->assertStatus(200);
        }
    }
}
