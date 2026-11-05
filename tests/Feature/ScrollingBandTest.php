<?php

namespace Tests\Feature;

use App\Models\Template;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScrollingBandTest extends TestCase
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

    public function test_scrolling_band_renders_across_all_active_themes(): void
    {
        $themes = ['saaya', 'buttercup', 'vyra', 'lumiere'];

        foreach ($themes as $themeKey) {
            Template::query()->update(['is_active' => false]);
            Template::where('key', $themeKey)->update(['is_active' => true]);
            \Illuminate\Support\Facades\Cache::forget('active_template');

            $response = $this->get(route('home'));
            $response->assertStatus(200);

            // Verify scrolling ticker container and items are rendered
            $response->assertSee('sld-ticker-container', false);
            $response->assertSee('Free Delivery on orders over', false);
            $response->assertSee('WELCOME5', false);
            $response->assertSee('Accepting UPI payments', false);
        }
    }
}

