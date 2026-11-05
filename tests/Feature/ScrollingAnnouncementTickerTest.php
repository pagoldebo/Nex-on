<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Setting;
use App\Models\Template;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScrollingAnnouncementTickerTest extends TestCase
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
            'description' => 'Dark luxury lingerie theme',
        ]);

        Category::create([
            'name'       => 'Intimates',
            'slug'       => 'intimates',
            'is_active'  => true,
            'sort_order' => 1,
        ]);
    }

    public function test_storefront_home_displays_default_scrolling_ticker_items(): void
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee('Free Delivery on orders over', false);
        $response->assertSee('Upto 500/- off for selected styles.', false);
        $response->assertSee('WELCOME5', false);
        $response->assertSee('Accepting UPI payments', false);
        $response->assertSee('Call us:', false);
    }

    public function test_admin_settings_screen_renders_ticker_configuration_section(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.settings.index'));

        $response->assertStatus(200);
        $response->assertSee('ticker-settings');
        $response->assertSee('ticker_enabled');
        $response->assertSee('ticker_item_1');
        $response->assertSee('ticker_item_2');
        $response->assertSee('ticker_item_3');
        $response->assertSee('ticker_item_4');
        $response->assertSee('ticker_item_5');
    }

    public function test_admin_can_update_ticker_items_and_view_on_storefront(): void
    {
        $payload = [
            'ticker_enabled' => '1',
            'ticker_item_1'  => 'Express Priority Shipping on orders over ₹1999',
            'ticker_item_2'  => 'Festive Special: Flat ₹750 off on silk nightwear',
            'ticker_item_3'  => 'Use coupon code <strong>FESTIVE10</strong> for 10% instant savings',
            'ticker_item_4'  => 'Instant UPI & 0% EMI Available',
            'ticker_item_5'  => 'VIP Styling Concierge: <strong>+91 99999 88888</strong>',
        ];

        $postResp = $this->actingAs($this->admin)->post(route('admin.settings.update'), $payload);
        $postResp->assertRedirect(route('admin.settings.index'));

        Setting::clearCache();

        $homeResp = $this->get(route('home'));
        $homeResp->assertStatus(200);
        $homeResp->assertSee('Express Priority Shipping on orders over ₹1999', false);
        $homeResp->assertSee('Festive Special: Flat ₹750 off on silk nightwear', false);
        $homeResp->assertSee('FESTIVE10', false);
        $homeResp->assertSee('Instant UPI & 0% EMI Available', false);
        $homeResp->assertSee('+91 99999 88888', false);
    }

    public function test_admin_can_disable_scrolling_ticker(): void
    {
        Setting::set('ticker_enabled', '0');
        Setting::clearCache();

        $response = $this->get(route('home'));
        $response->assertStatus(200);
        $response->assertDontSee('sld-ticker-container');
    }
}
