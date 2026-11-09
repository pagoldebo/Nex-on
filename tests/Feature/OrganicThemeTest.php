<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductVariant;
use App\Models\Template;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrganicThemeTest extends TestCase
{
    use RefreshDatabase;

    protected Category $category;
    protected Product $product;
    protected Template $organicTheme;

    protected function setUp(): void
    {
        parent::setUp();

        $this->organicTheme = Template::create([
            'key'       => 'organic',
            'name'      => 'Organic',
            'label'     => 'Organic Market',
            'is_active' => true,
        ]);

        Template::clearActiveCache();

        $this->category = Category::create([
            'name'       => 'Fresh Vegetables',
            'slug'       => 'fresh-vegetables',
            'sort_order' => 1,
        ]);

        $this->product = Product::create([
            'category_id'   => $this->category->id,
            'name'          => 'Organic Hass Avocado',
            'slug'          => 'organic-hass-avocado',
            'sku'           => 'ORG-AVO-01',
            'badge'         => 'ORGANIC',
            'is_active'     => true,
            'is_featured'   => true,
            'is_bestseller' => true,
        ]);

        $color = ProductColor::create([
            'product_id' => $this->product->id,
            'name'       => 'Standard',
            'hex'        => '#699E4B',
            'sort_order' => 1,
        ]);

        ProductVariant::create([
            'product_id'       => $this->product->id,
            'product_color_id' => $color->id,
            'size'             => '500g',
            'sku'              => 'ORG-AVO-01-500G',
            'price'            => 199.00,
            'compare_at_price' => 249.00,
            'inventory'        => 50,
            'is_active'        => true,
            'sort_order'       => 1,
        ]);
    }

    public function test_organic_home_page_renders_successfully(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Organic Hass Avocado');
        $response->assertSee('Powered by Nex-on');
    }

    public function test_organic_single_product_page_renders_successfully(): void
    {
        Product::create([
            'category_id' => $this->category->id,
            'name'        => 'Organic Heirloom Tomato',
            'sku'         => 'ORG-TOM-002',
            'slug'        => 'organic-heirloom-tomato',
            'is_active'   => true,
        ]);

        $response = $this->get('/products/' . $this->product->slug);
        $response->assertStatus(200);
        $response->assertSee('Organic Hass Avocado');
        $response->assertSee('Traceable farm origins');
        $response->assertSee('Related Products');
        $response->assertDontSee('Related Organic Picks');
    }

    public function test_organic_category_page_renders_successfully(): void
    {
        $response = $this->get('/category/' . $this->category->slug);
        $response->assertStatus(200);
        $response->assertSee('Fresh Vegetables');
        $response->assertSee('Organic Hass Avocado');
    }

    public function test_organic_featured_products_page_renders_successfully(): void
    {
        $response = $this->get('/featured-products');
        $response->assertStatus(200);
        $response->assertSee('Organic Hass Avocado');
    }

    public function test_organic_new_arrivals_page_renders_successfully(): void
    {
        $response = $this->get('/new-arrivals');
        $response->assertStatus(200);
        $response->assertSee('Organic Hass Avocado');
    }

    public function test_organic_about_page_renders_successfully(): void
    {
        $response = $this->get('/about');
        $response->assertStatus(200);
        $response->assertSee('Why We Refuse to Compromise on Soil Health');
    }

    public function test_organic_contact_page_renders_successfully(): void
    {
        $response = $this->get('/contact');
        $response->assertStatus(200);
        $response->assertSee('Get in Touch with Our Farm Team');
    }

    public function test_organic_cart_page_renders_successfully(): void
    {
        $response = $this->get('/cart');
        $response->assertStatus(200);
        $response->assertSee('Fresh Produce Basket');
    }

    public function test_organic_checkout_page_renders_successfully(): void
    {
        $response = $this->get('/checkout');
        $response->assertStatus(200);
        $response->assertSee('Farm-to-Door Checkout');
    }

    public function test_organic_login_page_renders_successfully(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Sign In to Your Account');
        $response->assertSee('Patron Circle');
    }

    public function test_organic_account_page_renders_successfully_for_authenticated_customer(): void
    {
        $user = User::factory()->create([
            'role' => 'customer',
        ]);

        $response = $this->actingAs($user)->get('/account');
        $response->assertStatus(200);
        $response->assertSee('Patron Portal');
    }

    public function test_organic_wishlist_page_renders_successfully(): void
    {
        $response = $this->get('/wishlist');
        $response->assertStatus(200);
        $response->assertSee('Your Saved Organic Wishlist');
    }

    public function test_organic_compare_page_renders_successfully(): void
    {
        $response = $this->get('/compare');
        $response->assertStatus(200);
        $response->assertSee('Side-by-Side Produce Comparison');
    }

    public function test_organic_search_page_renders_successfully(): void
    {
        $response = $this->get('/search?q=Avocado');
        $response->assertStatus(200);
        $response->assertSee('Search Organic Produce');
        $response->assertSee('Organic Hass Avocado');
    }

    public function test_organic_header_logo_and_navigation_features(): void
    {
        // 1. Create subcategory under Fresh Vegetables
        $sub = Category::create([
            'name'      => 'Root Veggies',
            'slug'      => 'root-veggies',
            'parent_id' => $this->category->id,
        ]);

        // 2. Set custom site logo and customer care phone
        \App\Models\Setting::set('site_logo', 'branding/custom_test_logo.png');
        \App\Models\Setting::set('store_name', 'Organic Roots');
        \App\Models\Setting::set('organic_customer_care', '+91 98111 22233');

        $response = $this->get('/');
        $response->assertOk();

        // Customer care phone rendered in top bar and hardcoded 1800-ORGANIC removed
        $response->assertSee('+91 98111 22233');
        $response->assertSee('tel:+919811122233', false);
        $response->assertDontSee('1800-ORGANIC');

        // Logo image rendered with store name
        $response->assertSee('images/branding/custom_test_logo.png');
        $response->assertSee('Organic Roots');

        // Main menu displays "Featured Products" and does not have duplicate "Categories"
        $response->assertSee('Featured Products');
        $response->assertDontSee('Featured Organics');
        $response->assertDontSee('<span>Categories</span>', false);

        // Subcategory is visible in the category menu and count badge (0 writeup) is removed
        $response->assertSee('Root Veggies');
        $response->assertSee(route('category.show', 'root-veggies'));
        $response->assertDontSee('px-1.5 py-0.5 rounded-full transition');

        // Search bar placeholder is simply "Search"
        $response->assertSee('placeholder="Search"', false);
        $response->assertDontSee('Search 1,200+ fresh organic vegetables');

        // Cart drawer & overlay are present for cart click
        $response->assertSee('id="cartDrawer"', false);
        $response->assertSee('id="cartOverlay"', false);
        $response->assertSee('openCartDrawer()', false);
    }

    public function test_admin_can_update_organic_customer_care_phone_in_settings(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.settings.update'), [
            'organic_customer_care' => '+91 80000 11223',
            'support_phone'         => '+91 99999 88888',
            'organic_top_notice'    => '100% Farm Fresh Heirloom Produce • Handpicked Daily',
            'organic_delivery_info' => 'Dispatched within 12 hours in solar chill-box trucks.',
            'organic_returns_info'  => 'Hassle-free replacement within 48 hours for any bruised fruit.',
        ]);

        $response->assertRedirect(route('admin.settings.index'));

        $this->assertEquals('+91 80000 11223', \App\Models\Setting::get('organic_customer_care'));
        $this->assertEquals('100% Farm Fresh Heirloom Produce • Handpicked Daily', \App\Models\Setting::get('organic_top_notice'));
        $this->assertEquals('Dispatched within 12 hours in solar chill-box trucks.', \App\Models\Setting::get('organic_delivery_info'));
        $this->assertEquals('Hassle-free replacement within 48 hours for any bruised fruit.', \App\Models\Setting::get('organic_returns_info'));

        // Verify storefront displays this updated phone number and announcement writeup
        $homeResponse = $this->get('/');
        $homeResponse->assertSee('+91 80000 11223');
        $homeResponse->assertSee('tel:+918000011223', false);
        $homeResponse->assertSee('100% Farm Fresh Heirloom Produce • Handpicked Daily');

        // Verify single product page displays updated delivery & returns accordion writeups
        $productResponse = $this->get(route('product.show', $this->product->slug));
        $productResponse->assertSee('Dispatched within 12 hours in solar chill-box trucks.');
        $productResponse->assertSee('Hassle-free replacement within 48 hours for any bruised fruit.');
    }
}
