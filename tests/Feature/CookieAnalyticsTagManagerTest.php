<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\Template;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CookieAnalyticsTagManagerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Template::create([
            'key'         => 'saaya',
            'name'        => 'Saaya',
            'is_active'   => true,
            'description' => 'Test Theme',
        ]);
    }

    public function test_admin_can_view_analytics_and_cookie_settings_page()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('admin.settings.analytics'));

        $response->assertStatus(200);
        $response->assertSee('Cookies &amp; Social Analytics Tag Manager', false);
        $response->assertSee('Google Tag Manager (GTM)');
        $response->assertSee('Google Analytics 4 (GA4)');
        $response->assertSee('Meta Pixel');
    }

    public function test_admin_can_update_marketing_and_analytics_tags()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $payload = [
            'cookie_banner_enabled'      => '1',
            'analytics_gtm_id'           => 'GTM-TEST999',
            'analytics_ga4_id'           => 'G-DEMO12345',
            'analytics_meta_pixel_id'    => '987654321012345',
            'analytics_tiktok_pixel_id'  => 'TIKTOK-SAMPLE-ID',
            'analytics_pinterest_tag_id' => 'PIN-TAG-888',
            'cookie_policy_url'          => '/privacy-cookies',
        ];

        $response = $this->actingAs($admin)->post(route('admin.settings.analytics.update'), $payload);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals('1', Setting::get('cookie_banner_enabled'));
        $this->assertEquals('GTM-TEST999', Setting::get('analytics_gtm_id'));
        $this->assertEquals('G-DEMO12345', Setting::get('analytics_ga4_id'));
        $this->assertEquals('987654321012345', Setting::get('analytics_meta_pixel_id'));
        $this->assertEquals('TIKTOK-SAMPLE-ID', Setting::get('analytics_tiktok_pixel_id'));
        $this->assertEquals('PIN-TAG-888', Setting::get('analytics_pinterest_tag_id'));
        $this->assertEquals('/privacy-cookies', Setting::get('cookie_policy_url'));
    }

    public function test_storefront_renders_cookie_consent_component_when_enabled()
    {
        Setting::set('cookie_banner_enabled', '1');
        Setting::set('analytics_ga4_id', 'G-LIVEGA4TEST');
        Setting::set('analytics_gtm_id', 'GTM-STOREFRONT');
        Setting::clearCache();

        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee('cookieConsentManager', false);
        $response->assertSee('G-LIVEGA4TEST');
        $response->assertSee('GTM-STOREFRONT');
        $response->assertSee('Respecting Your Privacy');
    }

    public function test_cookie_consent_component_can_be_disabled()
    {
        Setting::set('cookie_banner_enabled', '0');
        Setting::clearCache();

        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertDontSee('cookieConsentManager');
        $response->assertDontSee('Respecting Your Privacy');
    }
}
