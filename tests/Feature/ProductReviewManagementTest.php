<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\Template;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductReviewManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
        ]);

        Template::create(['key' => 'organic', 'name' => 'Organic', 'is_active' => true]);
        Template::create(['key' => 'buttercup', 'name' => 'Buttercup', 'is_active' => false]);
        Template::create(['key' => 'saaya', 'name' => 'Saaya', 'is_active' => false]);
        Template::create(['key' => 'lumiere', 'name' => 'Lumière', 'is_active' => false]);

        $category = Category::create([
            'name'       => 'Fresh Greens',
            'slug'       => 'fresh-greens',
            'sort_order' => 1,
        ]);

        $this->product = Product::create([
            'category_id' => $category->id,
            'name'        => 'Organic Baby Spinach',
            'slug'        => 'organic-baby-spinach',
            'sku'         => 'ORG-SPN-001',
            'description' => 'Crisp organic spinach harvest',
            'price'       => 120.00,
            'is_active'   => true,
        ]);
    }

    public function test_admin_can_view_reviews_index(): void
    {
        ProductReview::create([
            'product_id'    => $this->product->id,
            'customer_name' => 'Sunita Kulkarni',
            'city'          => 'Mumbai',
            'rating'        => 5,
            'title'         => 'Farm Fresh Taste',
            'comment'       => 'Incredible freshness and taste.',
            'is_approved'   => true,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.reviews.index'));
        $response->assertStatus(200);
        $response->assertSee('Product Reviews');
        $response->assertSee('Sunita Kulkarni');
        $response->assertSee('Mumbai');
    }

    public function test_admin_can_store_new_customer_review(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.reviews.store'), [
            'product_id'    => $this->product->id,
            'customer_name' => 'Dr. Rajesh Menon',
            'city'          => 'Pune',
            'rating'        => 5,
            'title'         => 'Superb Quality',
            'comment'       => 'Outstanding taste and pure aroma.',
            'is_approved'   => 1,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('product_reviews', [
            'customer_name' => 'Dr. Rajesh Menon',
            'city'          => 'Pune',
            'rating'        => 5,
            'is_approved'   => true,
        ]);
    }

    public function test_storefront_home_page_displays_database_reviews_across_themes(): void
    {
        ProductReview::create([
            'product_id'    => $this->product->id,
            'customer_name' => 'Ananya Patel',
            'city'          => 'Bengaluru',
            'rating'        => 5,
            'title'         => 'Eco-Friendly Packing',
            'comment'       => 'Delivery was very punctual and 100% plastic-free.',
            'is_approved'   => true,
        ]);

        foreach (['organic', 'buttercup', 'saaya', 'lumiere'] as $themeKey) {
            Template::query()->update(['is_active' => false]);
            Template::where('key', $themeKey)->update(['is_active' => true]);

            $response = $this->get(route('home'));
            $response->assertStatus(200);
            $response->assertSee('Ananya Patel');
            $response->assertSee('Bengaluru');
            $response->assertSee('Delivery was very punctual and 100% plastic-free.');
        }
    }
}
