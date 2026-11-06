<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\Template;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PwaInstallPromptTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Template::create(['key' => 'saaya',     'name' => 'Saaya',     'is_active' => true]);
        Template::create(['key' => 'buttercup', 'name' => 'Buttercup', 'is_active' => false]);
        Template::create(['key' => 'vyra',      'name' => 'Vyra',      'is_active' => false]);
        Template::create(['key' => 'lumiere',   'name' => 'Lumière',   'is_active' => false]);
    }

    public function test_manifest_json_returns_valid_pwa_configuration(): void
    {
        Setting::set('store_name', 'Nex-on');

        $response = $this->get('/manifest.json');
        $response->assertStatus(200);
        $response->assertJson([
            'short_name'  => 'Nex-on',
            'display'     => 'standalone',
            'theme_color' => '#7f1d1d',
        ]);
        $response->assertJsonFragment([
            'src' => '/icons/icon-192x192.png',
        ]);
    }

    public function test_pwa_install_popup_renders_across_all_themes(): void
    {
        Setting::set('store_name', 'Nex-on');
        Setting::set('pwa_prompt_enabled', '1');
        Setting::set('pwa_prompt_title', 'Install Nex-on App');
        Setting::set('pwa_prompt_description', 'Get quick access to our collection! Install our app for a faster, better shopping experience on your device.');

        $themes = ['saaya', 'buttercup', 'vyra', 'lumiere'];

        foreach ($themes as $themeKey) {
            Template::query()->update(['is_active' => false]);
            Template::where('key', $themeKey)->update(['is_active' => true]);
            \Illuminate\Support\Facades\Cache::forget('active_template');

            $response = $this->get(route('home'));
            $response->assertStatus(200);

            // Manifest & apple touch tags in head
            $response->assertSee('rel="manifest"', false);
            $response->assertSee('name="theme-color" content="#7f1d1d"', false);
            $response->assertSee('name="apple-mobile-web-app-capable"', false);

            // PWA Popup elements matching reference image
            $response->assertSee('id="pwaInstallPopup"', false);
            $response->assertSee('id="pwaCloseBtn"', false);
            $response->assertSee('id="pwaInstallBtn"', false);
            $response->assertSee('id="pwaMaybeLaterBtn"', false);
            $response->assertSee('Install Nex-on App');
            $response->assertSee('Install App');
            $response->assertSee('Maybe Later');

            // Service worker and script
            $response->assertSee('/sw.js');
            $response->assertSee('js/pwa-install.js');
        }
    }

    public function test_pwa_install_popup_can_be_disabled_via_settings(): void
    {
        Setting::set('pwa_prompt_enabled', '0');

        $response = $this->get(route('home'));
        $response->assertStatus(200);
        $response->assertDontSee('id="pwaInstallPopup"', false);
    }

    public function test_admin_can_update_pwa_settings(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('admin.settings.update'), [
            'pwa_prompt_enabled'     => '1',
            'pwa_prompt_title'       => 'Install Nex-on VIP App',
            'pwa_prompt_description' => 'Install our mobile app for member discounts.',
        ]);

        $response->assertRedirect(route('admin.settings.index'));

        $this->assertEquals('1', Setting::get('pwa_prompt_enabled'));
        $this->assertEquals('Install Nex-on VIP App', Setting::get('pwa_prompt_title'));
        $this->assertEquals('Install our mobile app for member discounts.', Setting::get('pwa_prompt_description'));
    }

    public function test_pwa_install_popup_dynamically_inherits_app_name_from_config(): void
    {
        config(['app.name' => 'Nex-on']);
        Setting::set('pwa_prompt_enabled', '1');
        \App\Models\Setting::where('key', 'pwa_prompt_title')->delete();
        \Illuminate\Support\Facades\Cache::forget('setting_pwa_prompt_title');

        $response = $this->get(route('home'));
        $response->assertStatus(200);
        $response->assertSee('Install Nex-on App');

        config(['app.name' => 'Nexus Retail']);
        $response2 = $this->get(route('home'));
        $response2->assertStatus(200);
        $response2->assertSee('Install Nexus Retail App');
    }

    public function test_admin_panel_displays_app_name_and_initial_from_config(): void
    {
        config(['app.name' => 'Nex-on']);
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Nex-on');
        $response->assertSee('>N<', false);
    }
}
