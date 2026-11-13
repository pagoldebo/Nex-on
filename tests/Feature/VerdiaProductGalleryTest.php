<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductColorImage;
use App\Models\Template;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VerdiaProductGalleryTest extends TestCase
{
    use RefreshDatabase;

    public function test_verdia_product_page_renders_all_images_thumbnails_and_auto_slider()
    {
        Template::create(['key' => 'saaya',   'name' => 'Saaya',   'is_active' => false]);
        Template::create(['key' => 'organic', 'name' => 'Verdia',  'is_active' => true]);
        Template::clearActiveCache();

        $category = Category::create([
            'name'       => 'Organic Greens',
            'slug'       => 'organic-greens',
            'is_active'  => true,
            'sort_order' => 1,
        ]);

        $product = Product::create([
            'category_id'      => $category->id,
            'name'             => 'Farm Fresh Kale',
            'slug'             => 'farm-fresh-kale',
            'description'      => 'Naturally grown, pesticide-free kale.',
            'long_description' => '<p>Directly harvested from organic certified soil.</p>',
            'badge'            => 'FRESH',
            'is_active'        => true,
        ]);

        $color = ProductColor::create([
            'product_id'  => $product->id,
            'name'        => 'Standard Harvest',
            'hex'         => '#699E4B',
            'cover_image' => 'products/kale-cover.jpg',
        ]);

        // Add 5 additional gallery angle shots (total 6 images including cover)
        for ($i = 1; $i <= 5; $i++) {
            ProductColorImage::create([
                'product_color_id' => $color->id,
                'image_path'       => "products/kale-angle-{$i}.jpg",
                'sort_order'       => $i,
            ]);
        }

        $response = $this->get(route('product.show', $product->slug));
        $response->assertStatus(200);

        // Verify Gallery Container
        $response->assertSee('id="verdiaGalleryWrap"', false);

        // Verify Controls & Badges
        $response->assertSee('id="verdiaSlideBadge"', false);
        $response->assertSee('id="verdiaPrevBtn"', false);
        $response->assertSee('id="verdiaNextBtn"', false);
        $response->assertSee('1 / 6', false);

        // Verify ALL 6 images are rendered in thumbnails (including the primary cover image)
        $response->assertSee('products/kale-cover.jpg');
        for ($i = 1; $i <= 5; $i++) {
            $response->assertSee("products/kale-angle-{$i}.jpg");
        }

        // Verify JavaScript auto-sliding and thumbnail functions
        $response->assertSee('AUTO_SLIDE_DELAY = 3500', false);
        $response->assertSee('startVerdiaAutoSlide()', false);
        $response->assertSee('renderVerdiaThumbnails()', false);
        $response->assertSee('verdiaGoToSlide(', false);
    }
}
