<?php

namespace Tests\Feature;

use App\Models\Template;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorefrontLoginModalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Template::create(['key' => 'saaya',     'name' => 'Saaya',     'is_active' => true]);
        Template::create(['key' => 'buttercup', 'name' => 'Buttercup', 'is_active' => false]);
        Template::create(['key' => 'vyra',      'name' => 'Vyra',      'is_active' => false]);
        Template::create(['key' => 'lumiere',   'name' => 'Lumière',   'is_active' => false]);
        Template::create(['key' => 'organic',   'name' => 'Organic',   'is_active' => false]);
    }

    public function test_login_modal_renders_centered_across_all_themes(): void
    {
        foreach (['saaya', 'buttercup', 'vyra', 'lumiere', 'organic'] as $themeKey) {
            Template::query()->update(['is_active' => false]);
            Template::where('key', $themeKey)->update(['is_active' => true]);
            Template::clearActiveCache();

            $response = $this->get(route('home'));
            $response->assertStatus(200);
            $response->assertSee('id="loginModal"', false);
            $response->assertSee('openLoginModal', false);
            $response->assertSee('closeLoginModal', false);
            $response->assertSee('m-auto', false);
        }
    }
}
