<?php

namespace Tests\Feature;

use App\Http\Middleware\SetLocale;
use App\Models\Product;
use App\Models\Template;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocalizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Template::create([
            'key'         => 'saaya',
            'name'        => 'Saaya Luxury',
            'is_active'   => true,
            'description' => 'Test Theme',
        ]);
    }

    public function test_default_locale_is_english()
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $this->assertEquals('en', app()->getLocale());
        $response->assertSee('lang="en"', false);
        $response->assertSee('dir="ltr"', false);
    }

    public function test_can_switch_locale_via_route_and_persist_in_session()
    {
        $response = $this->get(route('locale.switch', ['locale' => 'hi']));

        $response->assertRedirect();
        $response->assertSessionHas('locale', 'hi');
        $response->assertCookie('locale', 'hi');
    }

    public function test_can_switch_locale_via_json_request()
    {
        $response = $this->postJson(route('locale.switch', ['locale' => 'es']));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'locale'  => 'es',
        ]);
        $response->assertSessionHas('locale', 'es');
    }

    public function test_middleware_applies_session_locale_to_subsequent_requests()
    {
        $response = $this->withSession(['locale' => 'hi'])->get(route('home'));

        $response->assertStatus(200);
        $this->assertEquals('hi', app()->getLocale());
        $response->assertSee('lang="hi"', false);
    }

    public function test_arabic_locale_sets_rtl_text_direction()
    {
        $response = $this->withSession(['locale' => 'ar'])->get(route('home'));

        $response->assertStatus(200);
        $this->assertEquals('ar', app()->getLocale());
        $response->assertSee('lang="ar"', false);
        $response->assertSee('dir="rtl"', false);
    }

    public function test_unsupported_locale_falls_back_to_default()
    {
        $response = $this->get(route('locale.switch', ['locale' => 'invalid_lang_code']));

        $response->assertRedirect();
        $response->assertSessionHas('locale', config('app.fallback_locale', 'en'));
    }

    public function test_all_supported_locales_have_json_translation_files()
    {
        foreach (SetLocale::SUPPORTED_LOCALES as $code => $meta) {
            $path = base_path("lang/{$code}.json");
            $this->assertFileExists($path, "Translation file missing for locale: {$code}");
            $data = json_decode(file_get_contents($path), true);
            $this->assertIsArray($data);
            $this->assertArrayHasKey('Home', $data);
            $this->assertArrayHasKey('Cart', $data);
            $this->assertArrayHasKey('Checkout', $data);
        }
    }

    public function test_product_localized_name_and_description_helper()
    {
        $product = new Product([
            'name'        => 'Home',
            'description' => 'Luxury Silk Robe',
        ]);

        app()->setLocale('hi');
        $this->assertEquals('होम', $product->getLocalizedName());

        app()->setLocale('es');
        $this->assertEquals('Inicio', $product->getLocalizedName());

        app()->setLocale('en');
        $this->assertEquals('Home', $product->getLocalizedName());
    }
}
