<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductColorImage;
use App\Models\Template;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ButtercupProductGalleryTest extends TestCase
{
    use RefreshDatabase;

    public function test_buttercup_product_page_renders_all_images_thumbnails_and_auto_slider()
    {
        Template::create(['key' => 'saaya',     'name' => 'Saaya',     'is_active' => false]);
        Template::create(['key' => 'buttercup', 'name' => 'Buttercup', 'is_active' => true]);
        Template::clearActiveCache();

        $category = Category::create([
            'name'       => 'Sarees',
            'slug'       => 'sarees',
            'is_active'  => true,
            'sort_order' => 1,
        ]);

        $product = Product::create([
            'category_id'      => $category->id,
            'name'             => 'Silk Heritage Saree',
            'slug'             => 'silk-heritage-saree',
            'description'      => 'A luxurious handcrafted silk saree.',
            'long_description' => '<p>Heritage silk details.</p>',
            'badge'            => 'HOT',
            'is_active'        => true,
        ]);

        $color = ProductColor::create([
            'product_id'  => $product->id,
            'name'        => 'Crimson',
            'hex'         => '#DC2626',
            'cover_image' => 'products/crimson-cover.jpg',
        ]);

        // Add 6 additional images (total 7 images including cover)
        for ($i = 1; $i <= 6; $i++) {
            ProductColorImage::create([
                'product_color_id' => $color->id,
                'image_path'       => "products/crimson-angle-{$i}.jpg",
                'sort_order'       => $i,
            ]);
        }

        $response = $this->get(route('product.show', $product->slug));
        $response->assertStatus(200);

        // Verify Gallery Container
        $response->assertSee('id="buttercupGalleryWrap"', false);

        // Verify Controls & Badges
        $response->assertSee('id="buttercupSlideBadge"', false);
        $response->assertSee('id="buttercupPrevBtn"', false);
        $response->assertSee('id="buttercupNextBtn"', false);
        $response->assertSee('1 / 7', false);

        // Verify ALL 7 images are rendered in thumbnails (no take(4) truncation!)
        $response->assertSee('products/crimson-cover.jpg');
        for ($i = 1; $i <= 6; $i++) {
            $response->assertSee("products/crimson-angle-{$i}.jpg");
        }

        // Verify JavaScript auto-sliding and thumbnail functions
        $response->assertSee('AUTO_SLIDE_DELAY = 3500', false);
        $response->assertSee('startAutoSlide()', false);
        $response->assertSee('renderThumbnails()', false);
        $response->assertSee('goToSlide(', false);
    }
}
