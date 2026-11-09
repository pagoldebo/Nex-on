<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Template;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CategoryImageTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'email' => 'admin@nexon.com',
            'name'  => 'Admin User',
            'role'  => 'admin',
        ]);

        Template::create(['key' => 'buttercup', 'name' => 'Buttercup', 'is_active' => true]);
        Template::create(['key' => 'saaya',     'name' => 'Saaya',     'is_active' => false]);
        Template::create(['key' => 'vyra',      'name' => 'Vyra',      'is_active' => false]);
        Template::create(['key' => 'lumiere',   'name' => 'Lumière',   'is_active' => false]);
    }

    public function test_admin_can_upload_image_when_creating_parent_category()
    {
        Storage::fake('public');

        $image = UploadedFile::fake()->create('men-collection.jpg', 100, 'image/jpeg');

        $response = $this->actingAs($this->admin)->post(route('admin.categories.store'), [
            'name'       => 'Men Premium',
            'slug'       => 'men-premium',
            'parent_id'  => '',
            'sort_order' => 1,
            'image'      => $image,
        ]);

        $response->assertRedirect(route('admin.categories.index'));
        $response->assertSessionHas('success');

        $category = Category::where('slug', 'men-premium')->firstOrFail();
        $this->assertNotNull($category->image);
        Storage::disk('public')->assertExists($category->image);
        $this->assertStringContainsString('images/' . $category->image, $category->image_url);
    }

    public function test_admin_can_update_and_replace_category_image()
    {
        Storage::fake('public');

        $oldImage = UploadedFile::fake()->create('old-pic.jpg', 100, 'image/jpeg');
        $oldPath = $oldImage->store('categories', 'public');

        $category = Category::create([
            'name'  => 'Women Collection',
            'slug'  => 'women-collection',
            'image' => $oldPath,
        ]);

        $newImage = UploadedFile::fake()->create('new-pic.jpg', 100, 'image/jpeg');

        $response = $this->actingAs($this->admin)->put(route('admin.categories.update', $category), [
            'name'       => 'Women Premium Collection',
            'slug'       => 'women-collection',
            'parent_id'  => '',
            'sort_order' => 2,
            'image'      => $newImage,
        ]);

        $response->assertRedirect(route('admin.categories.index'));

        $category->refresh();
        $this->assertNotEquals($oldPath, $category->image);
        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($category->image);
    }

    public function test_admin_can_remove_category_image()
    {
        Storage::fake('public');

        $image = UploadedFile::fake()->create('to-remove.jpg', 100, 'image/jpeg');
        $path = $image->store('categories', 'public');

        $category = Category::create([
            'name'  => 'Kids Collection',
            'slug'  => 'kids-collection',
            'image' => $path,
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.categories.update', $category), [
            'name'         => 'Kids Collection',
            'slug'         => 'kids-collection',
            'remove_image' => '1',
        ]);

        $response->assertRedirect(route('admin.categories.index'));

        $category->refresh();
        $this->assertNull($category->image);
        Storage::disk('public')->assertMissing($path);
    }

    public function test_browse_shop_by_category_displays_uploaded_image_across_all_themes()
    {
        Storage::fake('public');

        $image = UploadedFile::fake()->create('western-wear.jpg', 100, 'image/jpeg');
        $path = $image->store('categories', 'public');

        $parent = Category::create([
            'name'       => 'Western Wear',
            'slug'       => 'western-wear',
            'image'      => $path,
            'sort_order' => 1,
        ]);

        foreach (['buttercup', 'saaya', 'vyra', 'lumiere'] as $themeKey) {
            Template::query()->update(['is_active' => false]);
            Template::where('key', $themeKey)->update(['is_active' => true]);

            $response = $this->get(route('home'));
            $response->assertStatus(200);

            $content = $response->getContent();
            preg_match('/Shop by Category.*?<\/section>/s', $content, $m);
            $section = $m[0] ?? '';

            $this->assertStringContainsString('Western Wear', $section, "Theme {$themeKey} should display parent category title");
            $this->assertStringContainsString($parent->image_url, $section, "Theme {$themeKey} should display category image URL");
        }
    }
}
