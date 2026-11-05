<?php

namespace Tests\Feature;

use App\Models\Addon;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Template;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductBrandAndAddonsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Category $category;
    protected Brand $brand;
    protected Addon $addon1;
    protected Addon $addon2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'email' => 'admin.brandaddons@nexon.luxury',
            'role'  => 'admin',
        ]);

        Template::create(['key' => 'saaya', 'name' => 'Saaya', 'is_active' => true]);

        $this->category = Category::create([
            'name'      => 'Artisanal Sarees',
            'slug'      => 'artisanal-sarees',
            'is_active' => true,
        ]);

        $this->brand = Brand::create([
            'name'        => 'Royal Heritage Atelier',
            'slug'        => 'royal-heritage-atelier',
            'description' => 'Fine handlooms & heirloom zari weaves.',
            'is_active'   => true,
        ]);

        $this->addon1 = Addon::create([
            'name'        => 'Velvet Gift Pouch',
            'description' => 'Luxury embossed satin lined velvet pouch',
            'price'       => 149.00,
            'is_active'   => true,
        ]);

        $this->addon2 = Addon::create([
            'name'        => 'Fall & Picot Finishing',
            'description' => 'Hand-stitched cotton fall and picot edge work',
            'price'       => 299.00,
            'is_active'   => true,
        ]);
    }

    public function test_product_create_page_renders_brand_and_addons_selectors()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.products.create'));

        $response->assertOk();
        $response->assertSee('Brand');
        $response->assertSee('Royal Heritage Atelier');
        $response->assertSee('Available Add-ons');
        $response->assertSee('Velvet Gift Pouch');
        $response->assertSee('Fall & Picot Finishing');
    }

    public function test_admin_can_create_product_with_brand_and_addons()
    {
        $postData = [
            'name'        => 'Banarasi Zari Saree',
            'sku'         => 'BNR-ZARI-001',
            'category_id' => $this->category->id,
            'brand_id'    => $this->brand->id,
            'addon_ids'   => [$this->addon1->id, $this->addon2->id],
            'description' => 'Handwoven Banarasi silk with pure silver zari work.',
            'is_active'   => 1,
            'colors'      => [
                0 => [
                    'name'  => 'Crimson Red',
                    'hex'   => '#990000',
                    'sizes' => [
                        0 => [
                            'size'  => 'Free Size',
                            'sku'   => 'BNR-ZARI-RED',
                            'price' => 14999.00,
                            'stock' => 5,
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.products.store'), $postData);

        $response->assertRedirect(route('admin.products.index'));

        $product = Product::where('sku', 'BNR-ZARI-001')->first();
        $this->assertNotNull($product);
        $this->assertEquals($this->brand->id, $product->brand_id);
        $this->assertCount(2, $product->addons);
        $this->assertTrue($product->addons->contains($this->addon1));
        $this->assertTrue($product->addons->contains($this->addon2));
    }

    public function test_product_edit_page_renders_preselected_brand_and_addons()
    {
        $product = Product::create([
            'category_id' => $this->category->id,
            'brand_id'    => $this->brand->id,
            'name'        => 'Tussar Handloom Saree',
            'slug'        => 'tussar-handloom-saree',
            'sku'         => 'TSR-001',
            'is_active'   => true,
        ]);
        $product->addons()->attach([$this->addon1->id]);

        $response = $this->actingAs($this->admin)->get(route('admin.products.edit', $product));

        $response->assertOk();
        $response->assertSee('Royal Heritage Atelier');
        $response->assertSee('Velvet Gift Pouch');
        $response->assertSee('checked', false);
    }

    public function test_admin_can_update_product_brand_and_addons()
    {
        $product = Product::create([
            'category_id' => $this->category->id,
            'name'        => 'Linen Saree',
            'slug'        => 'linen-saree',
            'sku'         => 'LNN-001',
            'is_active'   => true,
        ]);

        $updateData = [
            'name'        => 'Linen Saree Edited',
            'category_id' => $this->category->id,
            'brand_id'    => $this->brand->id,
            'addon_ids'   => [$this->addon2->id],
            'is_active'   => 1,
            'colors'      => [
                0 => [
                    'name'  => 'Indigo',
                    'hex'   => '#000080',
                    'sizes' => [
                        0 => [
                            'size'  => 'Standard',
                            'sku'   => 'LNN-IND-01',
                            'price' => 2999.00,
                            'stock' => 10,
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->actingAs($this->admin)->put(route('admin.products.update', $product), $updateData);

        $response->assertRedirect(route('admin.products.index'));

        $product->refresh();
        $this->assertEquals($this->brand->id, $product->brand_id);
        $this->assertCount(1, $product->addons);
        $this->assertTrue($product->addons->contains($this->addon2));
        $this->assertFalse($product->addons->contains($this->addon1));
    }

    public function test_brands_and_addons_hub_renders_and_allows_brand_creation()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.addons.index'));
        $response->assertOk();
        $response->assertSee('Brands &amp; Labels', false);
        $response->assertSee('Royal Heritage Atelier');

        $storeResponse = $this->actingAs($this->admin)->post(route('admin.brands.store'), [
            'name'        => 'Zari Emporium',
            'description' => 'Authentic zari craftsmanship since 1982.',
            'website'     => 'https://zariemporium.example',
            'is_active'   => 1,
        ]);

        $storeResponse->assertRedirect(route('admin.addons.index', ['tab' => 'brands']));
        $this->assertDatabaseHas('brands', ['name' => 'Zari Emporium', 'slug' => 'zari-emporium']);
    }

    public function test_storefront_renders_product_brand_and_addons()
    {
        $product = Product::create([
            'category_id' => $this->category->id,
            'brand_id'    => $this->brand->id,
            'name'        => 'Chanderi Silk Saree',
            'slug'        => 'chanderi-silk-saree',
            'sku'         => 'CND-001',
            'is_active'   => true,
        ]);
        $product->addons()->attach([$this->addon1->id]);

        $color = $product->colors()->create([
            'name' => 'Rose Pink',
            'hex'  => '#FFC0CB',
        ]);
        $color->variants()->create([
            'product_id' => $product->id,
            'size'       => 'Free Size',
            'sku'        => 'CND-ROSE',
            'price'      => 4500.00,
            'stock'      => 8,
        ]);

        foreach (['saaya', 'buttercup', 'lumiere', 'vyra', 'organic'] as $themeKey) {
            Template::query()->update(['is_active' => false]);
            Template::firstOrCreate(['key' => $themeKey], ['name' => ucfirst($themeKey), 'is_active' => true])
                ->update(['is_active' => true]);

            $response = $this->get(route('product.show', $product->slug));

            $response->assertOk();
            $response->assertSee('Velvet Gift Pouch');
            $response->assertSee('Available Add-ons');
            $response->assertSee('pdp-addon-checkbox');
            $response->assertSee('data-price="149', false);
        }
    }
}

