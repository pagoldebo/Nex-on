<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductColorImage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductColorCoverAndImageArrangementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'email' => 'admin.colorimages@nexon.com',
            'role'  => 'admin',
        ]);

        $this->category = Category::create([
            'name'      => 'Silk Sarees',
            'slug'      => 'silk-sarees',
            'is_active' => true,
        ]);

        Storage::fake('public');
    }

    public function test_product_create_page_renders_cover_image_and_reorder_gallery_sections(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.products.create'));

        $response->assertStatus(200);
        $response->assertSee('Cover Image for this color');
        $response->assertSee('Main Display Thumbnail');
        $response->assertSee('cover_image');
        $response->assertSee('Images for this color');
        $response->assertSee('gallery-preview-grid');
    }

    public function test_admin_can_create_product_with_separate_cover_image_and_gallery_images(): void
    {
        $coverFile = UploadedFile::fake()->image('midnight_black_cover.jpg', 600, 800);
        $galleryFile1 = UploadedFile::fake()->image('detail_1.jpg', 600, 800);
        $galleryFile2 = UploadedFile::fake()->image('detail_2.jpg', 600, 800);

        $payload = [
            'name'        => 'Kanjeevaram Silk Royale',
            'category_id' => $this->category->id,
            'is_active'   => 1,
            'colors'      => [
                0 => [
                    'name'        => 'Midnight Black',
                    'hex'         => '#18181B',
                    'cover_image' => $coverFile,
                    'images'      => [$galleryFile1, $galleryFile2],
                    'sizes'       => [
                        0 => [
                            'size'  => 'Free Size',
                            'price' => 4999,
                            'stock' => 15,
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.products.store'), $payload);

        $response->assertRedirect(route('admin.products.index'));

        $product = Product::where('name', 'Kanjeevaram Silk Royale')->first();
        $this->assertNotNull($product);

        $color = $product->colors()->first();
        $this->assertNotNull($color);
        $this->assertEquals('Midnight Black', $color->name);

        // Verify cover image was saved
        $rawCover = $color->getRawOriginal('cover_image');
        $this->assertNotEmpty($rawCover);
        Storage::disk('public')->assertExists($rawCover);

        // Verify cover image accessor
        $this->assertStringContainsString('images/', $color->cover_image);
        $this->assertEquals($color->cover_image, $product->cover_image);

        // Verify gallery images were saved
        $this->assertEquals(2, $color->images()->count());

        // Verify toPickerArray includes cover image first
        $picker = $color->toPickerArray();
        $this->assertEquals($color->cover_image, $picker['images'][0]);
        $this->assertCount(3, $picker['images']); // cover + 2 gallery images
    }

    public function test_product_edit_page_renders_current_cover_preview_and_reorderable_gallery(): void
    {
        $product = Product::create([
            'name'        => 'Banarasi Heirloom Gold',
            'category_id' => $this->category->id,
            'is_active'   => true,
        ]);

        $color = ProductColor::create([
            'product_id'  => $product->id,
            'name'        => 'Crimson Zari',
            'hex'         => '#DC2626',
            'cover_image' => 'products/silk-sarees/crimson_cover.webp',
            'sort_order'  => 0,
        ]);

        $img1 = ProductColorImage::create([
            'product_color_id' => $color->id,
            'image_path'       => 'products/silk-sarees/crimson_shot1.webp',
            'sort_order'       => 0,
        ]);

        $img2 = ProductColorImage::create([
            'product_color_id' => $color->id,
            'image_path'       => 'products/silk-sarees/crimson_shot2.webp',
            'sort_order'       => 1,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.products.edit', $product));

        $response->assertStatus(200);
        $response->assertSee('Cover Image for this color');
        $response->assertSee('crimson_cover.webp');
        $response->assertSee('existing-images-grid');
        $response->assertSee('existing_images_order');
        $response->assertSee('crimson_shot1.webp');
        $response->assertSee('crimson_shot2.webp');
    }

    public function test_admin_can_update_cover_image_and_reorder_existing_gallery_images(): void
    {
        $product = Product::create([
            'name'        => 'Mysore Royal Silk',
            'category_id' => $this->category->id,
            'is_active'   => true,
        ]);

        $color = ProductColor::create([
            'product_id'  => $product->id,
            'name'        => 'Royal Emerald',
            'hex'         => '#059669',
            'cover_image' => 'products/silk-sarees/old_emerald_cover.webp',
            'sort_order'  => 0,
        ]);
        Storage::disk('public')->put('products/silk-sarees/old_emerald_cover.webp', 'old-bytes');

        $imgA = ProductColorImage::create([
            'product_color_id' => $color->id,
            'image_path'       => 'products/silk-sarees/imgA.webp',
            'sort_order'       => 0,
        ]);
        $imgB = ProductColorImage::create([
            'product_color_id' => $color->id,
            'image_path'       => 'products/silk-sarees/imgB.webp',
            'sort_order'       => 1,
        ]);
        $imgC = ProductColorImage::create([
            'product_color_id' => $color->id,
            'image_path'       => 'products/silk-sarees/imgC.webp',
            'sort_order'       => 2,
        ]);

        $newCoverFile = UploadedFile::fake()->image('new_emerald_cover.jpg', 600, 800);

        // Submit rearranged order: imgC first (#0), then imgA (#1), then imgB (#2)
        $payload = [
            'name'        => 'Mysore Royal Silk',
            'category_id' => $this->category->id,
            'is_active'   => 1,
            'colors'      => [
                0 => [
                    'id'                    => $color->id,
                    'name'                  => 'Royal Emerald',
                    'hex'                   => '#059669',
                    'cover_image'           => $newCoverFile,
                    'existing_images_order' => [$imgC->id, $imgA->id, $imgB->id],
                    'sizes'                 => [
                        0 => [
                            'size'  => 'Standard',
                            'price' => 3999,
                            'stock' => 10,
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->actingAs($this->admin)->put(route('admin.products.update', $product), $payload);

        $response->assertRedirect(route('admin.products.index'));

        // Old cover file removed
        Storage::disk('public')->assertMissing('products/silk-sarees/old_emerald_cover.webp');

        // New cover file stored
        $color->refresh();
        $this->assertNotEquals('products/silk-sarees/old_emerald_cover.webp', $color->getRawOriginal('cover_image'));
        Storage::disk('public')->assertExists($color->getRawOriginal('cover_image'));

        // Verify rearranged gallery images order in database
        $this->assertEquals(0, $imgC->fresh()->sort_order);
        $this->assertEquals(1, $imgA->fresh()->sort_order);
        $this->assertEquals(2, $imgB->fresh()->sort_order);
    }

    public function test_admin_can_remove_cover_image(): void
    {
        $product = Product::create([
            'name'        => 'Tussar Handloom',
            'category_id' => $this->category->id,
            'is_active'   => true,
        ]);

        $color = ProductColor::create([
            'product_id'  => $product->id,
            'name'        => 'Mustard Ochre',
            'hex'         => '#D97706',
            'cover_image' => 'products/silk-sarees/mustard_cover.webp',
            'sort_order'  => 0,
        ]);
        Storage::disk('public')->put('products/silk-sarees/mustard_cover.webp', 'mustard-bytes');

        $payload = [
            'name'        => 'Tussar Handloom',
            'category_id' => $this->category->id,
            'is_active'   => 1,
            'colors'      => [
                0 => [
                    'id'                 => $color->id,
                    'name'               => 'Mustard Ochre',
                    'hex'                => '#D97706',
                    'remove_cover_image' => 1,
                    'sizes'              => [
                        0 => [
                            'size'  => 'Standard',
                            'price' => 2999,
                            'stock' => 5,
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->actingAs($this->admin)->put(route('admin.products.update', $product), $payload);

        $response->assertRedirect(route('admin.products.index'));

        Storage::disk('public')->assertMissing('products/silk-sarees/mustard_cover.webp');
        $this->assertNull($color->fresh()->getRawOriginal('cover_image'));
    }

    public function test_product_duplication_preserves_cover_image_on_cloned_colors(): void
    {
        $product = Product::create([
            'name'        => 'Chanderi Zari Drape',
            'category_id' => $this->category->id,
            'sku'         => 'LS-CHN-001',
            'is_active'   => true,
        ]);

        $color = ProductColor::create([
            'product_id'  => $product->id,
            'name'        => 'Dusty Rose',
            'hex'         => '#F43F5E',
            'cover_image' => 'products/silk-sarees/dusty_rose_cover.webp',
            'sort_order'  => 0,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.products.duplicate', $product));

        $clone = Product::where('name', 'Chanderi Zari Drape (Copy)')->first();
        $this->assertNotNull($clone);

        $clonedColor = $clone->colors()->first();
        $this->assertNotNull($clonedColor);
        $this->assertEquals('Dusty Rose', $clonedColor->name);
        $this->assertEquals('products/silk-sarees/dusty_rose_cover.webp', $clonedColor->getRawOriginal('cover_image'));
    }
}

