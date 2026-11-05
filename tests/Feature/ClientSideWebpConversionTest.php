<?php

namespace Tests\Feature;

use App\Models\Template;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientSideWebpConversionTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'email' => 'admin@nexon.luxury',
            'name'  => 'Admin User',
            'role'  => 'admin',
        ]);

        Template::create(['key' => 'saaya',     'name' => 'Saaya',     'is_active' => true]);
        Template::create(['key' => 'buttercup', 'name' => 'Buttercup', 'is_active' => false]);
        Template::create(['key' => 'vyra',      'name' => 'Vyra',      'is_active' => false]);
        Template::create(['key' => 'lumiere',   'name' => 'Lumière',   'is_active' => false]);
        Template::create(['key' => 'organic',   'name' => 'Organic',   'is_active' => false]);
    }

    public function test_webp_converter_script_exists_and_contains_canvas_conversion_logic()
    {
        $scriptPath = public_path('js/webp-converter.js');
        $this->assertFileExists($scriptPath);

        $content = file_get_contents($scriptPath);
        $this->assertStringContainsString('canvas.toBlob', $content);
        $this->assertStringContainsString('image/webp', $content);
        $this->assertStringContainsString('findOptimalQuality', $content);
        $this->assertStringContainsString('lockFormSubmission', $content);
        $this->assertStringContainsString('DataTransfer', $content);
    }

    public function test_admin_pages_load_client_side_webp_converter_script()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.settings.index'));
        $response->assertStatus(200);
        $response->assertSee(asset('js/webp-converter.js'));

        $dashboard = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $dashboard->assertStatus(200);
        $dashboard->assertSee(asset('js/webp-converter.js'));
    }

    public function test_storefront_pages_load_client_side_webp_converter_across_themes()
    {
        foreach (['saaya', 'buttercup', 'vyra', 'lumiere', 'organic'] as $themeKey) {
            Template::query()->update(['is_active' => false]);
            Template::where('key', $themeKey)->update(['is_active' => true]);
            Template::clearActiveCache();

            $response = $this->get(route('home'));
            $response->assertStatus(200);
            $response->assertSee(asset('js/webp-converter.js'));
        }
    }
}

