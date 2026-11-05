<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsurePlatformBranding;
use App\Models\Category;
use App\Models\Template;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlatformBrandingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Template::create(['key' => 'organic', 'name' => 'Organic', 'is_active' => true]);
        Template::create(['key' => 'buttercup', 'name' => 'Buttercup', 'is_active' => false]);
        Template::create(['key' => 'saaya', 'name' => 'Saaya', 'is_active' => false]);
        Template::create(['key' => 'lumiere', 'name' => 'Lumière', 'is_active' => false]);
        Template::create(['key' => 'vyra', 'name' => 'VYRA', 'is_active' => false]);

        Category::create([
            'name'       => 'Default Category',
            'slug'       => 'default-category',
            'sort_order' => 1,
        ]);
    }

    public function test_platform_branding_is_always_enforced_by_middleware(): void
    {
        foreach (['organic', 'buttercup', 'saaya', 'lumiere', 'vyra'] as $themeKey) {
            Template::query()->update(['is_active' => false]);
            Template::where('key', $themeKey)->update(['is_active' => true]);

            $response = $this->get(route('home'));
            $response->assertStatus(200);
            $response->assertSee('• Powered by Nex-on');
            $response->assertSee('data-nexon-platform-branding="verified"', false);
        }
    }

    public function test_middleware_shares_branding_variable_with_views(): void
    {
        $middleware = new EnsurePlatformBranding();
        $request = \Illuminate\Http\Request::create('/', 'GET');

        $middleware->handle($request, function () {
            $this->assertEquals('• Powered by Nex-on', view()->shared('platformBranding'));
            $this->assertStringContainsString('• Powered by Nex-on', view()->shared('platformBrandingHtml'));
            $this->assertEquals('Nex-on', view()->shared('platformName'));
            $this->assertEquals('E-commerce Business Suite', view()->shared('platformSuite'));

            return new \Illuminate\Http\Response('<html><body><footer></footer></body></html>');
        });
    }

    public function test_middleware_replaces_tampered_branding(): void
    {
        $middleware = new EnsurePlatformBranding();
        $request = \Illuminate\Http\Request::create('/', 'GET');

        $response = $middleware->handle($request, function () {
            return new \Illuminate\Http\Response('<html><body><footer><span data-nexon-platform-branding="verified">Tampered Branding</span></footer></body></html>');
        });

        $this->assertStringContainsString('• Powered by Nex-on', $response->getContent());
        $this->assertStringNotContainsString('Tampered Branding', $response->getContent());
    }

    public function test_middleware_injects_branding_if_missing(): void
    {
        $middleware = new EnsurePlatformBranding();
        $request = \Illuminate\Http\Request::create('/', 'GET');

        $response = $middleware->handle($request, function () {
            return new \Illuminate\Http\Response('<html><body><footer><p>No branding here</p></footer></body></html>');
        });

        $this->assertStringContainsString('• Powered by Nex-on', $response->getContent());
    }
}
