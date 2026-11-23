<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductVariant;
use App\Models\Setting;
use App\Models\Template;
use App\Models\User;
use App\Services\CurrencyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MultiCurrencyTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected CurrencyService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->service = app(CurrencyService::class);

        // Ensure template is active
        Template::query()->update(['is_active' => false]);
        Template::create([
            'name'      => 'Saaya',
            'key'       => 'saaya',
            'is_active' => true,
        ]);
        Template::clearActiveCache();
    }

    public function test_default_currency_configuration(): void
    {
        $this->assertTrue($this->service->isMultiCurrencyEnabled());
        $this->assertEquals('INR', $this->service->getBaseCurrency());
        $this->assertEquals('INR', $this->service->getUserCurrency());

        $enabled = $this->service->getEnabledCurrencies();
        $this->assertArrayHasKey('INR', $enabled);
        $this->assertArrayHasKey('USD', $enabled);
        $this->assertArrayHasKey('EUR', $enabled);
        $this->assertArrayHasKey('GBP', $enabled);

        $rates = $this->service->getRates();
        $this->assertEquals(1.0, $rates['INR']);
        $this->assertGreaterThan(0, $rates['USD']);
    }

    public function test_currency_conversion_and_formatting(): void
    {
        // 1000 INR at rate 0.0116 = 11.60 USD
        $convertedUsd = $this->service->convert(1000, 'USD', 'INR');
        $this->assertEquals(11.60, $convertedUsd);

        $formattedUsd = $this->service->format($convertedUsd, 'USD');
        $this->assertEquals('$11.60', $formattedUsd);

        $formattedInr = $this->service->format(1499, 'INR');
        $this->assertEquals('₹1,499', $formattedInr);

        // JPY formatting (0 decimals)
        $convertedJpy = $this->service->convert(1000, 'JPY', 'INR');
        $this->assertEquals(1780.0, $convertedJpy);
        $formattedJpy = $this->service->format($convertedJpy, 'JPY');
        $this->assertEquals('¥1,780', $formattedJpy);
    }

    public function test_currency_switching_via_post_and_get(): void
    {
        // Test AJAX POST
        $response = $this->postJson(route('currency.switch'), [
            'currency' => 'USD',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success'  => true,
            'currency' => 'USD',
        ]);
        $response->assertCookie('currency', 'USD');
        $this->assertEquals('USD', session('currency'));

        // Test Invalid Currency POST
        $invalid = $this->postJson(route('currency.switch'), [
            'currency' => 'XYZ',
        ]);
        $invalid->assertStatus(422);

        // Test GET route
        $getRes = $this->get(route('currency.switch.get', ['code' => 'EUR']));
        $getRes->assertStatus(302);
        $getRes->assertCookie('currency', 'EUR');
        $this->assertEquals('EUR', session('currency'));
    }

    public function test_order_checkout_snapshots_currency_and_rate(): void
    {
        $category = \App\Models\Category::create([
            'name' => 'Robes',
            'slug' => 'robes',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name'        => 'Multi Currency Silk Robe',
            'slug'        => 'multi-currency-silk-robe',
            'is_active'   => true,
        ]);

        $color = ProductColor::create([
            'product_id' => $product->id,
            'name'       => 'Emerald',
            'hex'        => '#059669',
        ]);

        $variant = ProductVariant::create([
            'product_id'       => $product->id,
            'product_color_id' => $color->id,
            'size'             => 'M',
            'price'            => 2000,
            'stock'            => 10,
            'sku'              => 'ROBE-EM-M',
        ]);

        // Place order in USD
        $order = Order::createFromCheckout([
            'customer_name'    => 'John Doe',
            'customer_email'   => 'john@example.com',
            'customer_phone'   => '9876543210',
            'shipping_address' => '123 Market St',
            'shipping_city'    => 'New York',
            'shipping_state'   => 'NY',
            'shipping_pincode' => '10001',
            'payment_method'   => 'card',
            'currency'         => 'USD',
            'items'            => [
                [
                    'productId' => $product->id,
                    'variantId' => $variant->id,
                    'name'      => $product->name,
                    'size'      => 'M',
                    'qty'       => 1,
                ],
            ],
        ]);

        $this->assertEquals('USD', $order->currency);
        $this->assertEquals($this->service->getRate('USD'), (float) $order->exchange_rate);
        $this->assertEquals('$', $order->currency_symbol);

        // Subtotal = 2000 * 0.0116 = 23.20 USD
        $this->assertEquals('$23.20', $order->formatted_subtotal);
        $this->assertEquals('$23.20', $order->formatted_total);

        // Check OrderItem formatting
        $item = $order->items->first();
        $this->assertNotNull($item);
        $this->assertEquals('$23.20', $item->formatted_price);
        $this->assertEquals('$23.20', $item->formatted_line_total);
    }

    public function test_admin_can_update_multi_currency_settings(): void
    {
        $this->actingAs($this->admin)->post(route('admin.settings.update'), [
            'multi_currency_section_submitted' => '1',
            'multi_currency_enabled'           => '1',
            'base_currency'                    => 'USD',
            'enabled_currencies'               => ['USD', 'EUR', 'GBP'],
            'currency_rates'                   => [
                'USD' => 1.0,
                'EUR' => 0.92,
                'GBP' => 0.78,
            ],
        ]);

        Setting::clearCache();

        $this->assertTrue(Setting::isMultiCurrencyEnabled());
        $this->assertEquals('USD', Setting::baseCurrency());
        $this->assertEquals(['USD', 'EUR', 'GBP'], Setting::enabledCurrencies());

        $rates = Setting::currencyRates();
        $this->assertEquals(0.92, $rates['EUR']);
        $this->assertEquals(0.78, $rates['GBP']);
    }

    public function test_storefront_renders_currency_config_and_switcher(): void
    {
        $response = $this->get(route('home'));
        $response->assertStatus(200);
        $response->assertSee('window.currencyConfig', false);
        $response->assertSee('window.formatPrice', false);
        $response->assertSee('Switch Currency');
    }

    public function test_home_page_and_quick_view_render_selected_currency_and_format_price(): void
    {
        $category = \App\Models\Category::create([
            'name' => 'Bridal Edit',
            'slug' => 'bridal-edit',
        ]);

        $product = Product::create([
            'category_id'  => $category->id,
            'name'         => 'Heritage Maroon Silk Lehenga',
            'slug'         => 'heritage-maroon-silk-lehenga',
            'is_active'    => true,
            'is_featured'  => true,
            'is_bestseller'=> true,
        ]);

        $color = ProductColor::create([
            'product_id' => $product->id,
            'name'       => 'Maroon',
            'hex'        => '#800000',
        ]);

        ProductVariant::create([
            'product_id'       => $product->id,
            'product_color_id' => $color->id,
            'size'             => 'L',
            'price'            => 10000,
            'stock'            => 5,
            'sku'              => 'LEH-MAR-L',
        ]);

        // Request Home Page with USD currency in session
        $response = $this->withSession(['currency' => 'USD'])->get(route('home'));
        $response->assertStatus(200);

        // 1. window.currencyConfig must reflect USD
        $response->assertSee('"code":"USD"', false);
        $response->assertSee('"symbol":"$"', false);

        // 2. window.formatPrice function must be defined in head
        $response->assertSee('window.formatPrice = function', false);

        // 3. Product card starting price rendered with @currency must be in USD ($116.00)
        // 10000 * 0.0116 = 116.00
        $response->assertSee('$116.00');

        // 4. Quick View partial must reference window.formatPrice
        $response->assertSee('window.formatPrice(price)', false);
    }
}
