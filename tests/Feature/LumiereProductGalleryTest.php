<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductColorImage;
use App\Models\Template;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LumiereProductGalleryTest extends TestCase
{
    use RefreshDatabase;

    public function test_lumiere_product_page_renders_all_images_thumbnails_and_auto_slider()
    {
        Template::create(['key' => 'saaya',   'name' => 'Saaya',   'is_active' => false]);
        Template::create(['key' => 'lumiere', 'name' => 'Lumière', 'is_active' => true]);
        Template::clearActiveCache();

        $category = Category::create([
            'name'       => 'Silk Sarees',
            'slug'       => 'silk-sarees',
            'is_active'  => true,
            'sort_order' => 1,
        ]);

        $product = Product::create([
            'category_id'      => $category->id,
            'name'             => 'Lumière Silk Saree',
            'slug'             => 'lumiere-silk-saree',
            'description'      => 'A French silk haute couture saree.',
            'long_description' => '<p>Pure silk fabric details.</p>',
            'badge'            => 'EXCLUSIVE',
            'is_active'        => true,
        ]);

        $color = ProductColor::create([
            'product_id'  => $product->id,
            'name'        => 'Rose Gold',
            'hex'         => '#B76E79',
            'cover_image' => 'products/lumiere-rose-cover.jpg',
        ]);

        for ($i = 1; $i <= 5; $i++) {
            ProductColorImage::create([
                'product_color_id' => $color->id,
                'image_path'       => "products/lumiere-rose-angle-{$i}.jpg",
                'sort_order'       => $i,
            ]);
        }

        $response = $this->get(route('product.show', $product->slug));
        $response->assertStatus(200);

        // Verify Gallery Container
        $response->assertSee('id="lumiereGalleryWrap"', false);

        // Verify Controls & Badges
        $response->assertSee('id="lumiereSlideBadge"', false);
        $response->assertSee('id="lumierePrevBtn"', false);
        $response->assertSee('id="lumiereNextBtn"', false);
        $response->assertSee('1 / 6', false);

        // Verify all 6 images are rendered in uncropped thumbnails
        $response->assertSee('products/lumiere-rose-cover.jpg');
        for ($i = 1; $i <= 5; $i++) {
            $response->assertSee("products/lumiere-rose-angle-{$i}.jpg");
        }

        // Verify JavaScript auto-sliding and thumbnail functions
        $response->assertSee('AUTO_SLIDE_DELAY = 3500', false);
        $response->assertSee('startAutoSlide()', false);
        $response->assertSee('pdRenderThumbs()', false);
        $response->assertSee('lumiereGoToSlide(', false);
    }

    public function test_lumiere_quick_view_modal_renders_html_description_container_and_inner_html_script()
    {
        Template::create(['key' => 'lumiere', 'name' => 'Lumière', 'is_active' => true]);
        Template::clearActiveCache();

        $response = $this->get(route('home'));
        $response->assertStatus(200);

        // Verify Quick View modal uses a div container with product-rich-text
        $response->assertSee('<div id="qvDescription" class="text-xs text-gray-500 leading-relaxed mt-1.5 line-clamp-2 product-rich-text [&_p]:inline [&_p]:m-0"></div>', false);

        // Verify Quick View modal uses innerHTML to render rich formatting instead of textContent
        $response->assertSee('descEl.innerHTML = product.description || product.shortDescription || \'\';', false);
        $response->assertDontSee('descEl.textContent = product.description', false);
    }
}
