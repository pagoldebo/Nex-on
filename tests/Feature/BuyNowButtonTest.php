<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductVariant;
use App\Models\Template;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BuyNowButtonTest extends TestCase
{
    use RefreshDatabase;

    private function createSampleProduct(): Product
    {
        $category = Category::create([
            'name'       => 'Test Category',
            'slug'       => 'test-category',
            'is_active'  => true,
            'sort_order' => 1,
        ]);

        $product = Product::create([
            'category_id'      => $category->id,
            'name'             => 'Test Signature Piece',
            'slug'             => 'test-signature-piece',
            'description'      => 'A luxury test product.',
            'long_description' => '<p>Details here.</p>',
            'badge'            => 'NEW',
            'is_active'        => true,
        ]);

        $color = ProductColor::create([
            'product_id'  => $product->id,
            'name'        => 'Onyx Black',
            'hex'         => '#000000',
            'cover_image' => 'products/test-cover.jpg',
        ]);

        ProductVariant::create([
            'product_id'       => $product->id,
            'product_color_id' => $color->id,
            'sku'              => 'TEST-M',
            'size'             => 'M',
            'price'            => 1999.00,
            'stock'            => 10,
        ]);

        return $product;
    }

    public function test_buttercup_replaces_compare_with_buy_now_button()
    {
        Template::create(['key' => 'saaya',     'name' => 'Saaya',     'is_active' => false]);
        Template::create(['key' => 'buttercup', 'name' => 'Buttercup', 'is_active' => true]);
        Template::clearActiveCache();

        $product = $this->createSampleProduct();

        $response = $this->get(route('product.show', $product->slug));
        $response->assertStatus(200);

        // Verify Buy Now button exists
        $response->assertSee('id="buttercupBuyNowBtn"', false);
        $response->assertSee('handleBuyNow()', false);
        $response->assertSee('Buy Now');

        // Verify Compare button is gone from product page actions
        $response->assertDontSee('id="buttercupCompareBtn"', false);
        $response->assertDontSee('?ids=' . $product->id, false);
    }

    public function test_lumiere_replaces_compare_with_buy_now_button()
    {
        Template::create(['key' => 'saaya',   'name' => 'Saaya',   'is_active' => false]);
        Template::create(['key' => 'lumiere', 'name' => 'Lumière', 'is_active' => true]);
        Template::clearActiveCache();

        $product = $this->createSampleProduct();

        $response = $this->get(route('product.show', $product->slug));
        $response->assertStatus(200);

        // Verify Buy Now button exists
        $response->assertSee('id="lumiereBuyNowBtn"', false);
        $response->assertSee('pdBuyNow()', false);
        $response->assertSee('Buy Now');

        // Verify old Compare button is gone
        $response->assertDontSee('pdAddToCompare()', false);
    }

    public function test_vyra_replaces_compare_with_buy_now_button()
    {
        Template::create(['key' => 'saaya', 'name' => 'Saaya', 'is_active' => false]);
        Template::create(['key' => 'vyra',  'name' => 'Vyra',  'is_active' => true]);
        Template::clearActiveCache();

        $product = $this->createSampleProduct();

        $response = $this->get(route('product.show', $product->slug));
        $response->assertStatus(200);

        // Verify Buy Now button exists
        $response->assertSee('id="vyraBuyNowBtn"', false);
        $response->assertSee('handleBuyNow()', false);
        $response->assertSee('Buy Now');

        // Verify Compare button is gone from product page actions
        $response->assertDontSee('?ids=' . $product->id, false);
    }

    public function test_saaya_has_buy_now_button()
    {
        Template::create(['key' => 'saaya', 'name' => 'Saaya', 'is_active' => true]);
        Template::clearActiveCache();

        $product = $this->createSampleProduct();

        $response = $this->get(route('product.show', $product->slug));
        $response->assertStatus(200);

        // Verify Buy Now button exists
        $response->assertSee('id="pdpBuyNowBtn"', false);
        $response->assertSee('Buy Now');
    }

    public function test_organic_has_buy_now_button()
    {
        Template::create(['key' => 'saaya',   'name' => 'Saaya',   'is_active' => false]);
        Template::create(['key' => 'organic', 'name' => 'Verdia',  'is_active' => true]);
        Template::clearActiveCache();

        $product = $this->createSampleProduct();

        $response = $this->get(route('product.show', $product->slug));
        $response->assertStatus(200);

        // Verify Buy Now button exists
        $response->assertSee('id="buyNowPrimaryBtn"', false);
        $response->assertSee('triggerDirectBuyNow()', false);
        $response->assertSee('Buy Now');
    }
}
