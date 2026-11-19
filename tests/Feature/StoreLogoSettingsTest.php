<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\Template;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StoreLogoSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
        ]);

        Template::create(['key' => 'buttercup', 'name' => 'Buttercup', 'is_active' => true]);
        Template::create(['key' => 'saaya',     'name' => 'Saaya',     'is_active' => false]);
        Template::create(['key' => 'vyra',      'name' => 'Vyra',      'is_active' => false]);
        Template::create(['key' => 'lumiere',   'name' => 'Lumière',   'is_active' => false]);
    }

    public function test_settings_screen_loads_with_logo_configuration_section(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.settings.index'));

        $response->assertStatus(200);
        $response->assertSee('Project Logo & Brand Identity', false);
        $response->assertSee('Upload New Project Logo');
    }

    public function test_admin_can_upload_project_logo_and_apply_to_all_themes(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('brand-logo.png', 50, 'image/png');

        $response = $this->actingAs($this->admin)->post(route('admin.settings.update'), [
            'store_name' => 'Nexon Atelier',
            'site_logo'  => $file,
        ]);

        $response->assertRedirect(route('admin.settings.index'));
        $response->assertSessionHas('success');

        $savedLogo = Setting::get('site_logo');
        $this->assertNotEmpty($savedLogo);
        $this->assertEquals('branding/logo.png', $savedLogo);
        Storage::disk('public')->assertExists($savedLogo);

        // Test that all 4 themes render the custom logo
        foreach (['buttercup', 'saaya', 'vyra', 'lumiere'] as $themeKey) {
            Template::query()->update(['is_active' => false]);
            Template::where('key', $themeKey)->update(['is_active' => true]);
            \Illuminate\Support\Facades\Cache::forget('active_template');

            $storefront = $this->get(route('home'));
            $storefront->assertStatus(200);
            $storefront->assertSee(asset('images/' . $savedLogo));
            $storefront->assertSee('Nexon Atelier');
        }
    }

    public function test_admin_can_remove_project_logo(): void
    {
        Storage::fake('public');

        // Set a logo first
        $file = UploadedFile::fake()->create('old-logo.png', 50, 'image/png');
        $path = $file->store('branding', 'public');
        Setting::set('site_logo', $path);

        $this->assertTrue(Storage::disk('public')->exists($path));

        // Submit removal request
        $response = $this->actingAs($this->admin)->post(route('admin.settings.update'), [
            'remove_logo' => '1',
            'store_name'  => 'Nexon Default',
        ]);

        $response->assertRedirect(route('admin.settings.index'));

        $this->assertEmpty(Setting::get('site_logo'));
        $this->assertFalse(Storage::disk('public')->exists($path));
    }

    public function test_admin_can_update_footer_bio_and_badges_across_all_themes(): void
    {
        Template::create(['key' => 'organic', 'name' => 'Organic', 'is_active' => false]);

        $response = $this->actingAs($this->admin)->get(route('admin.settings.index'));
        $response->assertStatus(200);
        $response->assertSee('footer_bio');
        $response->assertSee('footer_badge1');
        $response->assertSee('footer_badge2');

        $updateResp = $this->actingAs($this->admin)->post(route('admin.settings.update'), [
            'store_name'    => 'Khokan\'s Organic & Fine Lingerie',
            'footer_bio'    => 'Khokan\'s is your premier bespoke boutique delivering pure handcrafted excellence directly to your door.',
            'footer_badge1' => '100% Certified Organic',
            'footer_badge2' => 'Handcrafted Excellence',
        ]);
        $updateResp->assertRedirect(route('admin.settings.index'));

        Setting::clearCache();

        foreach (['buttercup', 'saaya', 'vyra', 'lumiere', 'organic'] as $themeKey) {
            Template::query()->update(['is_active' => false]);
            Template::where('key', $themeKey)->update(['is_active' => true]);
            Template::clearActiveCache();

            $storefront = $this->get(route('home'));
            $storefront->assertStatus(200);
            $storefront->assertSee('Khokan\'s is your premier bespoke boutique delivering pure handcrafted excellence directly to your door.');
            $storefront->assertSee('100% Certified Organic');
            $storefront->assertSee('Handcrafted Excellence');
        }
    }

    public function test_admin_can_upload_separate_footer_logo(): void
    {
        Storage::fake('public');

        $siteLogoFile = UploadedFile::fake()->create('primary-logo.png', 50, 'image/png');
        $footerLogoFile = UploadedFile::fake()->create('white-footer-logo.png', 50, 'image/png');

        $response = $this->actingAs($this->admin)->post(route('admin.settings.update'), [
            'store_name'  => 'Nexon Luxury',
            'site_logo'   => $siteLogoFile,
            'footer_logo' => $footerLogoFile,
        ]);

        $response->assertRedirect(route('admin.settings.index'));
        $response->assertSessionHas('success');

        $this->assertEquals('branding/logo.png', Setting::get('site_logo'));
        $this->assertEquals('branding/footer_logo.png', Setting::get('footer_logo'));
        Storage::disk('public')->assertExists('branding/footer_logo.png');

        Setting::clearCache();

        // Verify footer logo is rendered in footers
        foreach (['buttercup', 'saaya', 'vyra', 'lumiere'] as $themeKey) {
            Template::query()->update(['is_active' => false]);
            Template::where('key', $themeKey)->update(['is_active' => true]);
            Template::clearActiveCache();

            $storefront = $this->get(route('home'));
            $storefront->assertStatus(200);
            $storefront->assertSee(asset('images/branding/footer_logo.png'));
        }
    }

    public function test_footer_logo_falls_back_to_site_logo_when_not_set(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('brand-header.png', 50, 'image/png');
        $this->actingAs($this->admin)->post(route('admin.settings.update'), [
            'store_name' => 'Nexon Fallback',
            'site_logo'  => $file,
        ]);

        Setting::clearCache();
        $this->assertEmpty(Setting::get('footer_logo'));

        // Setting::footerLogoUrl() must resolve the primary site logo
        $this->assertEquals(Setting::siteLogoUrl(), Setting::footerLogoUrl());

        Template::query()->update(['is_active' => false]);
        Template::where('key', 'saaya')->update(['is_active' => true]);
        Template::clearActiveCache();

        $storefront = $this->get(route('home'));
        $storefront->assertStatus(200);
        $storefront->assertSee(asset('images/branding/logo.png'));
    }

    public function test_admin_can_remove_footer_logo_separately(): void
    {
        Storage::fake('public');

        Setting::set('site_logo', 'branding/logo.png');
        Setting::set('footer_logo', 'branding/footer_logo.png');
        Storage::disk('public')->put('branding/logo.png', 'test-data-logo');
        Storage::disk('public')->put('branding/footer_logo.png', 'test-data-footer');

        $response = $this->actingAs($this->admin)->post(route('admin.settings.update'), [
            'remove_footer_logo' => '1',
            'store_name'         => 'Nexon Keep Primary',
        ]);

        $response->assertRedirect(route('admin.settings.index'));

        // Footer logo must be removed, but primary logo must be preserved
        $this->assertEmpty(Setting::get('footer_logo'));
        $this->assertEquals('branding/logo.png', Setting::get('site_logo'));
        $this->assertFalse(Storage::disk('public')->exists('branding/footer_logo.png'));
        $this->assertTrue(Storage::disk('public')->exists('branding/logo.png'));
    }
}
