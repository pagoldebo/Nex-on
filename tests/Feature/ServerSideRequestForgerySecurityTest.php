<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Setting;
use App\Models\User;
use App\Services\SsrfProtectionService;
use App\Services\WebhookService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ServerSideRequestForgerySecurityTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name'     => 'SSRF Security Admin',
            'email'    => 'admin@ssrf-security.test',
            'phone'    => '9777788888',
            'password' => bcrypt('password123'),
            'role'     => 'admin',
        ]);
    }

    protected function createOrder(): Order
    {
        return Order::create([
            'reference'         => 'SAA-SSRF-001',
            'channel'           => 'online',
            'status'            => Order::STATUS_CONFIRMED,
            'subtotal'          => 2000,
            'shipping_charge'   => 0,
            'cod_charge'        => 0,
            'discount'          => 0,
            'total'             => 2000,
            'payment_method'    => 'razorpay',
            'payment_status'    => Order::PAYMENT_PAID,
            'shipping_name'     => 'Security Auditor',
            'shipping_phone'    => '9876543210',
            'shipping_email'    => 'auditor@example.com',
            'shipping_address1' => 'MG Road',
            'shipping_city'     => 'Bengaluru',
            'shipping_state'    => 'Karnataka',
            'shipping_pincode'  => '560001',
        ]);
    }

    /**
     * Test admin settings reject cloud metadata endpoint in order_webhook_urls.
     */
    public function test_settings_rejects_cloud_metadata_in_webhook_urls(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.settings.update'), [
            'store_name'         => 'Nexon Secure Store',
            'order_webhook_urls' => 'http://169.254.169.254/latest/meta-data/',
        ]);

        $response->assertSessionHasErrors(['order_webhook_urls']);
        $this->assertNotEquals('http://169.254.169.254/latest/meta-data/', Setting::get('order_webhook_urls'));
    }

    /**
     * Test admin settings reject loopback addresses in order_webhook_urls.
     */
    public function test_settings_rejects_loopback_address_in_webhook_urls(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.settings.update'), [
            'store_name'         => 'Nexon Secure Store',
            'order_webhook_urls' => 'http://127.0.0.1:8000/internal-api',
        ]);

        $response->assertSessionHasErrors(['order_webhook_urls']);
        $this->assertNotEquals('http://127.0.0.1:8000/internal-api', Setting::get('order_webhook_urls'));
    }

    /**
     * Test admin settings accept legitimate public webhook URLs.
     */
    public function test_settings_accepts_valid_public_webhook_urls(): void
    {
        $safeUrl = 'https://discord.com/api/webhooks/12345/abcdef';

        $response = $this->actingAs($this->admin)->post(route('admin.settings.update'), [
            'store_name'         => 'Nexon Secure Store',
            'order_webhook_urls' => $safeUrl,
        ]);

        $response->assertRedirect(route('admin.settings.index'));
        $response->assertSessionHas('success');

        $this->assertEquals($safeUrl, Setting::get('order_webhook_urls'));
    }

    /**
     * Test WebhookService automatically excludes unsafe URLs stored in settings/env.
     */
    public function test_webhook_service_filters_unsafe_stored_urls(): void
    {
        Setting::set('order_webhook_urls', 'http://127.0.0.1:6379,https://discord.com/api/webhooks/test,http://169.254.169.254/latest');

        $urls = WebhookService::getWebhookUrls();

        $this->assertCount(1, $urls);
        $this->assertEquals(['https://discord.com/api/webhooks/test'], $urls);
    }

    /**
     * Test WebhookService dispatches to safe endpoints and ignores blocked endpoints.
     */
    public function test_webhook_dispatch_blocks_ssrf_and_sends_to_safe(): void
    {
        Http::fake([
            'https://discord.com/*' => Http::response(['success' => true], 200),
        ]);

        Setting::set('order_webhook_urls', 'http://127.0.0.1:9000/probe,https://discord.com/api/webhooks/order-alert');

        $order = $this->createOrder();

        WebhookService::sendOrderNotification($order);

        // Safe endpoint should receive request
        Http::assertSent(function ($request) {
            return str_starts_with($request->url(), 'https://discord.com/api/webhooks/order-alert') &&
                   $request->method() === 'POST' &&
                   $request['order_id'] !== null;
        });

        // Unsafe endpoint must never be called
        Http::assertNotSent(function ($request) {
            return str_contains($request->url(), '127.0.0.1');
        });
    }

    /**
     * Test safe client configuration disables automatic redirection.
     */
    public function test_safe_client_has_redirects_disabled(): void
    {
        $client = SsrfProtectionService::createSafeClient(5);

        $reflection = new \ReflectionClass($client);
        $optionsProp = $reflection->getProperty('options');
        $options = $optionsProp->getValue($client);

        $this->assertArrayHasKey('allow_redirects', $options);
        $this->assertFalse($options['allow_redirects'], 'Redirects must be disabled to prevent 30x SSRF pivots.');
    }
}

