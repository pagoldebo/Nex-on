<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductColorImage;
use App\Models\Template;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductImageUploadNamingTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Category $rootCategory;
    protected Category $subCategory;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->admin = User::factory()->create([
            'email' => 'admin.images@nexon.com',
            'role'  => 'admin',
        ]);

        Template::create(['key' => 'saaya', 'name' => 'Saaya', 'is_active' => true]);

        // Setup category hierarchy: Root "Men" -> Sub "Shirt" -> Sub "Casual Shirt"
        $this->rootCategory = Category::create([
            'name'      => 'Men',
            'slug'      => 'men',
            'parent_id' => null,
        ]);

        $intermediateCategory = Category::create([
            'name'      => 'Shirt',
            'slug'      => 'men-shirt',
            'parent_id' => $this->rootCategory->id,
        ]);

        $this->subCategory = Category::create([
            'name'      => 'Casual Shirt',
            'slug'      => 'men-shirt-casual-shirt',
            'parent_id' => $intermediateCategory->id,
        ]);
    }

    public function test_product_image_stored_in_main_category_folder_with_webp_and_random_suffix(): void
    {
        $fakeImage = UploadedFile::fake()->image('test_shirt.jpg', 600, 600);

        $payload = [
            'name'        => 'Linen Casual Shirt',
            'sku'         => 'LS-LIN-001',
            'category_id' => $this->subCategory->id,
            'description' => 'Fine linen shirt',
            'colors'      => [
                [
                    'name'   => 'Ocean Blue',
                    'hex'    => '#0066CC',
                    'images' => [$fakeImage],
                    'sizes'  => [
                        [
                            'size'  => 'M',
                            'sku'   => 'LS-LIN-001-BLU-M',
                            'price' => 1499.00,
                            'stock' => 15,
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.products.store'), $payload);
        $response->assertRedirect(route('admin.products.index'));

        $product = Product::where('sku', 'LS-LIN-001')->firstOrFail();
        $this->assertCount(1, $product->colors);

        $color = $product->colors->first();
        $this->assertCount(1, $color->images);

        $image = $color->images->first();
        $path = $image->image_path;

        // Verify folder is the main category name (men)
        $this->assertStringStartsWith('products/men/', $path);
        $this->assertStringEndsWith('.webp', $path);

        // Verify filename pattern: {product-name}-{base-sku}-{6-char-random}.webp
        $this->assertMatchesRegularExpression(
            '/^products\/men\/linen-casual-shirt-ls-lin-001-[a-z0-9]{6}\.webp$/',
            $path
        );

        // Verify file exists on public storage
        Storage::disk('public')->assertExists($path);
    }

    public function test_updating_product_with_new_image_stores_in_main_category_folder(): void
    {
        $product = Product::create([
            'name'        => 'Silk Kurta',
            'sku'         => 'LS-KUR-555',
            'category_id' => $this->rootCategory->id,
            'is_active'   => true,
        ]);

        $color = ProductColor::create([
            'product_id' => $product->id,
            'name'       => 'Ivory',
            'hex'        => '#FFFFF0',
            'sort_order' => 0,
        ]);

        $newImage = UploadedFile::fake()->image('kurta_ivory.png', 500, 500);

        $payload = [
            'name'        => 'Silk Kurta',
            'sku'         => 'LS-KUR-555',
            'category_id' => $this->rootCategory->id,
            'colors'      => [
                [
                    'id'     => $color->id,
                    'name'   => 'Ivory',
                    'hex'    => '#FFFFF0',
                    'images' => [$newImage],
                    'sizes'  => [
                        [
                            'size'  => 'L',
                            'sku'   => 'LS-KUR-555-IVO-L',
                            'price' => 2999.00,
                            'stock' => 10,
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->actingAs($this->admin)->put(route('admin.products.update', $product), $payload);
        $response->assertRedirect(route('admin.products.index'));

        $freshImage = ProductColorImage::where('product_color_id', $color->id)->firstOrFail();
        $this->assertMatchesRegularExpression(
            '/^products\/men\/silk-kurta-ls-kur-555-[a-z0-9]{6}\.webp$/',
            $freshImage->image_path
        );

        Storage::disk('public')->assertExists($freshImage->image_path);
    }

    public function test_inventory_quick_add_stores_image_in_main_category_folder(): void
    {
        $fakeImage = UploadedFile::fake()->image('trouser.jpg', 600, 600);

        $payload = [
            'name'             => 'Chino Trousers',
            'sku'              => 'LS-TR-900',
            'category_id'      => $this->subCategory->id,
            'color_name'       => 'Khaki',
            'color_hex'        => '#C3B091',
            'size'             => '32',
            'price'            => 1999.00,
            'stock'            => 25,
            'image'            => $fakeImage,
            'is_active'        => 1,
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.inventory.quickStore'), $payload);
        $response->assertRedirect(route('admin.inventory.index'));

        $product = Product::where('sku', 'LS-TR-900')->firstOrFail();
        $color = $product->colors->first();
        $image = $color->images->first();

        $this->assertNotNull($image);
        $this->assertMatchesRegularExpression(
            '/^products\/men\/chino-trousers-ls-tr-900-[a-z0-9]{6}\.webp$/',
            $image->image_path
        );

        Storage::disk('public')->assertExists($image->image_path);
    }
}
