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
            'store_name' => 'Nexon Luxury Atelier',
            'site_logo'  => $file,
        ]);

        $response->assertRedirect(route('admin.settings.index'));
        $response->assertSessionHas('success');

        $savedLogo = Setting::get('site_logo');
        $this->assertNotEmpty($savedLogo);
        Storage::disk('public')->assertExists($savedLogo);

        // Test that all 4 themes render the custom logo
        foreach (['buttercup', 'saaya', 'vyra', 'lumiere'] as $themeKey) {
            Template::query()->update(['is_active' => false]);
            Template::where('key', $themeKey)->update(['is_active' => true]);
            \Illuminate\Support\Facades\Cache::forget('active_template');

            $storefront = $this->get(route('home'));
            $storefront->assertStatus(200);
            $storefront->assertSee(asset('storage/' . $savedLogo));
            $storefront->assertSee('Nexon Luxury Atelier');
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
}
