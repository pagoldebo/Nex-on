<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductVariant;
use App\Models\Setting;
use App\Models\Template;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PolicyAndSizeGuideSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);

        Template::create([
            'key'         => 'vyra',
            'name'        => 'Vyra Noir',
            'is_active'   => true,
            'description' => 'Dark lingerie theme',
        ]);

        Category::create([
            'name'       => 'Intimates',
            'slug'       => 'intimates',
            'is_active'  => true,
            'sort_order' => 1,
        ]);
    }

    public function test_storefront_policy_and_size_guide_pages_load_with_defaults(): void
    {
        // 1. Size Guide
        $response = $this->get(route('sizeGuide'));
        $response->assertStatus(200);
        $response->assertSee('Atelier Fit Perfection', false);
        $response->assertSee('The Precision Size Guide', false);
        $response->assertSee('Sister Sizing Matrix', false);

        // 2. Shipping Policy
        $response = $this->get(route('shippingPolicy'));
        $response->assertStatus(200);
        $response->assertSee('Logistics &amp; Privacy', false);
        $response->assertSee('Shipping &amp; Discreet Delivery', false);
        $response->assertSee('100% Confidential Outer Packaging', false);

        // 3. Return Policy
        $response = $this->get(route('returnPolicy'));
        $response->assertStatus(200);
        $response->assertSee('Customer Guarantee', false);
        $response->assertSee('Returns &amp; Hygiene Standards', false);
        $response->assertSee('Intimate Hygiene Regulations', false);
    }

    public function test_admin_settings_screen_renders_policy_and_size_guide_inputs(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.settings.index'));

        $response->assertStatus(200);
        $response->assertSee('policy-settings');
        $response->assertSee('sizeguide_title');
        $response->assertSee('sizeguide_calc_title');
        $response->assertSee('shipping_policy_title');
        $response->assertSee('shipping_banner_title');
        $response->assertSee('return_policy_title');
        $response->assertSee('return_banner_title');
    }

    public function test_admin_can_update_policies_and_size_guide_and_view_on_storefront(): void
    {
        $payload = [
            // Size guide updates
            'sizeguide_badge'          => 'Custom Atelier Measurements',
            'sizeguide_title'          => 'Bespoke Fit Experience',
            'sizeguide_subtitle'       => 'Find your custom cup and contour dimensions in seconds.',
            'sizeguide_calc_title'     => 'Smart Size Finder 2.0',
            'sizeguide_calc_desc'      => 'Follow our professional bra fit consultant guidelines.',
            'sizeguide_underbust_hint' => 'Tape parallel around ribcage snugly.',
            'sizeguide_bust_hint'      => 'Tape across fullest bust curve loosely.',
            'sizeguide_sister_title'   => 'Sister Fit Alternates',
            'sizeguide_sister_desc'    => 'Horizontal matrix for equivalent cup volumes.',
            'sizeguide_bottoms_title'  => 'Silk Bottoms & Robes',
            'sizeguide_bottoms_desc'   => 'Waist and hip coordinates for luxurious comfort.',
            'sizeguide_additional_info' => 'Contact our fit concierge for private sizing appointments.',

            // Shipping policy updates
            'shipping_policy_badge'    => 'Fast & Tracked Dispatch',
            'shipping_policy_title'    => 'VIP Doorstep Shipping',
            'shipping_policy_subtitle' => 'Express air cargo in unmarked protective premium packaging.',
            'shipping_banner_badge'    => 'Discreet Security',
            'shipping_banner_title'    => '100% Plain Exterior Parcel Promise',
            'shipping_banner_desc'     => 'Zero branding on carton with discreet sender name.',
            'shipping_sec1_title'      => '1. Same-Day Dispatch Guarantee',
            'shipping_sec1_desc'       => 'Orders placed before 2 PM IST dispatch on the exact same business day.',
            'shipping_sec2_title'      => '2. Rapid Air Transit Times',
            'shipping_sec2_desc'       => 'All metro hubs receive delivery within 24 to 36 hours.',
            'shipping_sec3_title'      => '3. Free Delivery Tier',
            'shipping_sec3_desc'       => 'All orders over ₹999 qualify for free air courier dispatch.',
            'shipping_sec4_title'      => '4. COD Terms',
            'shipping_sec4_desc'       => 'Cash on delivery enabled across 18,000+ Indian pincodes.',
            'shipping_sec5_title'      => '5. Approved Couriers',
            'shipping_sec5_desc'       => 'Bluedart Apex, Delhivery Express, and DHL Express.',
            'shipping_custom_content'  => '<p id="custom-ship-clause">Complimentary tracking SMS sent upon gate scan.</p>',

            // Return policy updates
            'return_policy_badge'      => 'Satisfaction & Comfort Guarantee',
            'return_policy_title'      => '14-Day Seamless Exchange Policy',
            'return_policy_subtitle'   => 'Effortless exchanges with complimentary reverse pickup from your doorstep.',
            'return_banner_title'      => 'Hygiene Seals & Trial Standards',
            'return_banner_desc'       => 'Protective seals must remain intact on intimate bottoms.',
            'return_sec1_title'      => '1. Accepted Return Categories',
            'return_sec1_desc'       => 'Unworn bras and sleepwear with tags attached within 14 days.',
            'return_sec2_title'      => '2. Final Archive Items',
            'return_sec2_desc'       => 'Customized bridal embroidery and hygiene-opened bottoms.',
            'return_sec3_title'      => '3. Hassle-Free Pickup Process',
            'return_sec3_desc'       => 'Tap exchange in your patron portal to generate an instant OTP pickup.',
            'return_sec4_title'      => '4. Instant Store Credit or Refund',
            'return_sec4_desc'       => 'Refunds triggered immediately upon warehouse inbound scan.',
            'return_custom_content'  => '<p id="custom-return-clause">Patron VIPs enjoy extended 30-day exchange privileges.</p>',
        ];

        $postResp = $this->actingAs($this->admin)->post(route('admin.settings.update'), $payload);
        $postResp->assertRedirect(route('admin.settings.index'));

        // Clear settings cache to simulate real subsequent request
        Setting::clearCache();

        // 1. Verify Size Guide storefront
        $sgResp = $this->get(route('sizeGuide'));
        $sgResp->assertStatus(200);
        $sgResp->assertSee('Custom Atelier Measurements', false);
        $sgResp->assertSee('Bespoke Fit Experience', false);
        $sgResp->assertSee('Smart Size Finder 2.0', false);
        $sgResp->assertSee('Sister Fit Alternates', false);
        $sgResp->assertSee('Silk Bottoms &amp; Robes', false);
        $sgResp->assertSee('Contact our fit concierge for private sizing appointments.', false);

        // 2. Verify Shipping Policy storefront
        $shipResp = $this->get(route('shippingPolicy'));
        $shipResp->assertStatus(200);
        $shipResp->assertSee('Fast &amp; Tracked Dispatch', false);
        $shipResp->assertSee('VIP Doorstep Shipping', false);
        $shipResp->assertSee('100% Plain Exterior Parcel Promise', false);
        $shipResp->assertSee('1. Same-Day Dispatch Guarantee', false);
        $shipResp->assertSee('Orders placed before 2 PM IST dispatch on the exact same business day.', false);
        $shipResp->assertSee('custom-ship-clause', false);

        // 3. Verify Return Policy storefront
        $retResp = $this->get(route('returnPolicy'));
        $retResp->assertStatus(200);
        $retResp->assertSee('Satisfaction &amp; Comfort Guarantee', false);
        $retResp->assertSee('14-Day Seamless Exchange Policy', false);
        $retResp->assertSee('Hygiene Seals &amp; Trial Standards', false);
        $retResp->assertSee('1. Accepted Return Categories', false);
        $retResp->assertSee('Unworn bras and sleepwear with tags attached within 14 days.', false);
        $retResp->assertSee('custom-return-clause', false);
    }

    public function test_universal_product_delivery_and_returns_accordion_settings_render_across_themes(): void
    {
        // 1. Verify settings screen renders universal accordion settings card
        $settingsResp = $this->actingAs($this->admin)->get(route('admin.settings.index'));
        $settingsResp->assertStatus(200);
        $settingsResp->assertSee('product-page-accordion');
        $settingsResp->assertSee('product_delivery_info');
        $settingsResp->assertSee('product_returns_info');
        $settingsResp->assertSee('Product Page Delivery &amp; Returns Accordion', false);

        // 2. Admin updates the universal delivery & returns settings
        $postResp = $this->actingAs($this->admin)->post(route('admin.settings.update'), [
            'product_delivery_info' => 'Universal VIP Courier: Dispatched within 24 hours in discreet insulated packaging.',
            'product_returns_info'  => 'Universal 30-Day Guarantee: Complimentary doorstep reverse pickup on all unworn items.',
        ]);
        $postResp->assertRedirect(route('admin.settings.index'));

        Setting::clearCache();

        $this->assertEquals(
            'Universal VIP Courier: Dispatched within 24 hours in discreet insulated packaging.',
            Setting::get('product_delivery_info')
        );
        $this->assertEquals(
            'Universal 30-Day Guarantee: Complimentary doorstep reverse pickup on all unworn items.',
            Setting::get('product_returns_info')
        );

        // 3. Create a test product
        $category = Category::first();
        $product = Product::create([
            'category_id'   => $category->id,
            'name'          => 'Atelier Silk Chemise',
            'slug'          => 'atelier-silk-chemise',
            'sku'           => 'ATEL-001',
            'is_active'     => true,
        ]);
        $color = ProductColor::create([
            'product_id' => $product->id,
            'name'       => 'Noir',
            'hex'        => '#000000',
            'sort_order' => 1,
        ]);
        ProductVariant::create([
            'product_id'       => $product->id,
            'product_color_id' => $color->id,
            'size'             => 'M',
            'sku'              => 'ATEL-001-M',
            'price'            => 1499.00,
            'inventory'        => 10,
            'is_active'        => true,
            'sort_order'       => 1,
        ]);

        // 4. Verify across themes
        $themeKeys = ['vyra', 'buttercup', 'lumiere', 'saaya', 'organic'];
        foreach ($themeKeys as $themeKey) {
            Template::query()->update(['is_active' => false]);
            Template::updateOrCreate(
                ['key' => $themeKey],
                ['name' => ucfirst($themeKey), 'is_active' => true]
            );
            Template::clearActiveCache();

            $pdpResp = $this->get(route('product.show', $product->slug));
            $pdpResp->assertStatus(200);
            $pdpResp->assertSee('Universal VIP Courier: Dispatched within 24 hours in discreet insulated packaging.');
            $pdpResp->assertSee('Universal 30-Day Guarantee: Complimentary doorstep reverse pickup on all unworn items.');
        }
    }
}
