<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class CsrfProtectionSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure HTTP Kernel resolves so middleware definitions in bootstrap/app.php are booted
        $this->app->make(\Illuminate\Contracts\Http\Kernel::class);
    }

    protected function createAdmin(): User
    {
        return User::create([
            'name'     => 'Security Admin',
            'email'    => 'admin@csrf-test.com',
            'phone'    => '9123456789',
            'password' => bcrypt('password123'),
            'role'     => 'admin',
        ]);
    }

    protected function createOrder(string $ref = 'SAA-CSRF-001'): Order
    {
        return Order::create([
            'reference'         => $ref,
            'channel'           => 'online',
            'status'            => Order::STATUS_PENDING,
            'subtotal'          => 1500,
            'shipping_charge'   => 0,
            'cod_charge'        => 0,
            'discount'          => 0,
            'total'             => 1500,
            'payment_method'    => 'razorpay',
            'payment_status'    => Order::PAYMENT_PENDING,
            'shipping_name'     => 'John Doe',
            'shipping_phone'    => '9876543210',
            'shipping_email'    => 'john@example.com',
            'shipping_address1' => 'Brigade Road',
            'shipping_city'     => 'Bengaluru',
            'shipping_state'    => 'Karnataka',
            'shipping_pincode'  => '560025',
        ]);
    }

    /**
     * Create an instance of ValidateCsrfToken configured to enforce verification during tests.
     */
    protected function getActiveCsrfMiddleware(): ValidateCsrfToken
    {
        return new class($this->app, $this->app['encrypter']) extends ValidateCsrfToken {
            protected function runningUnitTests(): bool
            {
                return false;
            }
        };
    }

    /**
     * Test admin optimize/clear route cannot be triggered via GET request (preventing CSRF via img/link).
     */
    public function test_optimize_clear_route_rejects_get_requests(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get('/admin/optimize/clear');
        $response->assertStatus(405); // Method Not Allowed
    }

    /**
     * Test admin optimize/clear route accepts POST request.
     */
    public function test_optimize_clear_route_accepts_post_requests(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->post('/admin/optimize/clear');
        $response->assertStatus(302);
        $response->assertSessionHas('success');
    }

    /**
     * Test CSRF exclusion list: webhooks are exempted, but admin routes are strictly guarded.
     */
    public function test_csrf_exemptions_configuration(): void
    {
        $middleware = new ValidateCsrfToken($this->app, $this->app['encrypter']);
        $excludedPaths = $middleware->getExcludedPaths();

        $this->assertContains('webhook/*', $excludedPaths, 'Webhooks should be exempted from CSRF.');
        $this->assertNotContains('admin/themes/admin-toggle', $excludedPaths, 'Admin theme toggle must NOT be exempted from CSRF.');
        $this->assertNotContains('admin/optimize/clear', $excludedPaths, 'Admin optimize clear must NOT be exempted from CSRF.');
    }

    /**
     * Test ValidateCsrfToken rejects POST requests without token on non-exempted routes.
     */
    public function test_csrf_middleware_enforces_token_on_admin_routes(): void
    {
        $middleware = $this->getActiveCsrfMiddleware();

        $session = $this->app['session']->driver();
        $session->setId('test-session-id');
        $session->start();
        $session->put('_token', 'valid-server-session-token');

        // Request without CSRF token
        $request = Request::create('/admin/themes/admin-toggle', 'POST', ['theme' => 'dark']);
        $request->setLaravelSession($session);

        $this->expectException(TokenMismatchException::class);
        $middleware->handle($request, function () {
            return new Response('OK');
        });
    }

    /**
     * Test ValidateCsrfToken allows POST requests with valid token on admin routes.
     */
    public function test_csrf_middleware_accepts_valid_token_on_admin_routes(): void
    {
        $middleware = $this->getActiveCsrfMiddleware();

        $session = $this->app['session']->driver();
        $session->setId('test-session-id');
        $session->start();
        $session->put('_token', 'valid-server-session-token');

        // Request with valid CSRF token header
        $request = Request::create('/admin/themes/admin-toggle', 'POST', ['theme' => 'dark']);
        $request->headers->set('X-CSRF-TOKEN', 'valid-server-session-token');
        $request->setLaravelSession($session);

        $response = $middleware->handle($request, function () {
            return new Response('OK');
        });

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * Test ValidateCsrfToken allows webhook POST requests without CSRF token due to exemption.
     */
    public function test_csrf_middleware_allows_webhook_routes_without_token(): void
    {
        $middleware = $this->getActiveCsrfMiddleware();

        $session = $this->app['session']->driver();
        $session->setId('test-session-id');
        $session->start();
        $session->put('_token', 'valid-server-session-token');

        // Webhook request without CSRF token
        $request = Request::create('/webhook/razorpay', 'POST', ['event' => 'ping']);
        $request->setLaravelSession($session);

        $response = $middleware->handle($request, function () {
            return new Response('WEBHOOK_PROCESSED');
        });

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('WEBHOOK_PROCESSED', $response->getContent());
    }

    /**
     * Test Razorpay webhook signature validation rejects missing/invalid signatures when secret configured.
     */
    public function test_razorpay_webhook_enforces_cryptographic_signature_when_configured(): void
    {
        $order = $this->createOrder('SAA-CSRF-RZP02');
        $secret = 'whsec_secure_key_123';
        Setting::set('gateway_razorpay_webhook_secret', $secret);

        $payload = json_encode([
            'event'   => 'payment.captured',
            'payload' => [
                'payment' => [
                    'entity' => [
                        'id'          => 'pay_RZPValidSig123',
                        'amount'      => 150000,
                        'description' => $order->reference,
                        'notes'       => [
                            'order_reference' => $order->reference,
                        ],
                    ],
                ],
            ],
        ]);

        // 1. Missing signature header -> 400
        $responseMissing = $this->call(
            'POST',
            '/webhook/razorpay',
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $payload
        );
        $responseMissing->assertStatus(400);

        // 2. Invalid signature header -> 400
        $responseInvalid = $this->call(
            'POST',
            '/webhook/razorpay',
            [],
            [],
            [],
            [
                'CONTENT_TYPE'          => 'application/json',
                'HTTP_X_RAZORPAY_SIGNATURE' => 'fake_invalid_signature',
            ],
            $payload
        );
        $responseInvalid->assertStatus(400);

        // 3. Valid HMAC SHA-256 signature -> 200
        $validSignature = hash_hmac('sha256', $payload, $secret);
        $responseValid = $this->call(
            'POST',
            '/webhook/razorpay',
            [],
            [],
            [],
            [
                'CONTENT_TYPE'          => 'application/json',
                'HTTP_X_RAZORPAY_SIGNATURE' => $validSignature,
            ],
            $payload
        );
        $responseValid->assertStatus(200);

        $order->refresh();
        $this->assertEquals(Order::PAYMENT_PAID, $order->payment_status);
    }

    /**
     * Test Stripe webhook signature validation rejects forged payloads when secret configured.
     */
    public function test_stripe_webhook_enforces_signature_when_configured(): void
    {
        $order = $this->createOrder('SAA-CSRF-STRIPE01');
        $secret = 'whsec_stripe_secret_test_456';
        Setting::set('gateway_stripe_webhook_secret', $secret);

        $payload = json_encode([
            'type' => 'checkout.session.completed',
            'data' => [
                'object' => [
                    'id'       => 'cs_test_stripeSigned123',
                    'metadata' => [
                        'order_reference' => $order->reference,
                    ],
                ],
            ],
        ]);

        // Missing signature -> 400
        $responseMissing = $this->call('POST', '/webhook/stripe', [], [], [], ['CONTENT_TYPE' => 'application/json'], $payload);
        $responseMissing->assertStatus(400);

        // Invalid signature -> 400
        $responseInvalid = $this->call(
            'POST',
            '/webhook/stripe',
            [],
            [],
            [],
            [
                'CONTENT_TYPE'           => 'application/json',
                'HTTP_STRIPE_SIGNATURE' => 't=' . time() . ',v1=fake_sig',
            ],
            $payload
        );
        $responseInvalid->assertStatus(400);

        // Valid signature -> 200
        $time = time();
        $signedPayload = $time . '.' . $payload;
        $validSig = hash_hmac('sha256', $signedPayload, $secret);

        $responseValid = $this->call(
            'POST',
            '/webhook/stripe',
            [],
            [],
            [],
            [
                'CONTENT_TYPE'           => 'application/json',
                'HTTP_STRIPE_SIGNATURE' => "t={$time},v1={$validSig}",
            ],
            $payload
        );
        $responseValid->assertStatus(200);

        $order->refresh();
        $this->assertEquals(Order::PAYMENT_PAID, $order->payment_status);
    }

    /**
     * Test PhonePe webhook enforces checksum verification when salt key is configured.
     */
    public function test_phonepe_webhook_enforces_checksum_when_configured(): void
    {
        $order = $this->createOrder('SAA-CSRF-PHONEPE01');
        $saltKey = 'd3b07384d113edec49eaa6238ad5ff00';
        $saltIndex = '1';
        Setting::set('gateway_phonepe_salt_key', $saltKey);
        Setting::set('gateway_phonepe_salt_index', $saltIndex);

        $payloadData = [
            'code' => 'PAYMENT_SUCCESS',
            'data' => [
                'merchantTransactionId' => $order->reference,
            ],
        ];
        $responseBase64 = base64_encode(json_encode($payloadData));

        // Missing X-VERIFY -> 400
        $responseMissing = $this->postJson('/webhook/phonepe', [
            'response' => $responseBase64,
        ]);
        $responseMissing->assertStatus(400);

        // Invalid X-VERIFY -> 400
        $responseInvalid = $this->withHeader('X-VERIFY', 'invalid_checksum###1')
            ->postJson('/webhook/phonepe', [
                'response' => $responseBase64,
            ]);
        $responseInvalid->assertStatus(400);

        // Valid X-VERIFY -> 200
        $validChecksum = hash('sha256', $responseBase64 . $saltKey) . '###' . $saltIndex;
        $responseValid = $this->withHeader('X-VERIFY', $validChecksum)
            ->postJson('/webhook/phonepe', [
                'response' => $responseBase64,
            ]);
        $responseValid->assertStatus(200);

        $order->refresh();
        $this->assertEquals(Order::PAYMENT_PAID, $order->payment_status);
    }

    /**
     * Test client Razorpay payment verification enforces secret HMAC when configured.
     */
    public function test_client_razorpay_verification_enforces_signature(): void
    {
        $order = $this->createOrder('SAA-CSRF-CLIENT01');
        $keySecret = 'sec_razorpay_client_key_999';
        Setting::set('gateway_razorpay_key_secret', $keySecret);

        $orderId = 'order_RZP123456';
        $paymentId = 'pay_RZPPayment789';

        // Invalid signature -> 400
        $responseBad = $this->postJson(route('api.payment.razorpay.verify'), [
            'order_reference'     => $order->reference,
            'razorpay_payment_id' => $paymentId,
            'razorpay_order_id'   => $orderId,
            'razorpay_signature'  => 'tampered_signature',
        ]);
        $responseBad->assertStatus(400);

        // Valid signature -> 200
        $validSignature = hash_hmac('sha256', $orderId . '|' . $paymentId, $keySecret);
        $responseGood = $this->postJson(route('api.payment.razorpay.verify'), [
            'order_reference'     => $order->reference,
            'razorpay_payment_id' => $paymentId,
            'razorpay_order_id'   => $orderId,
            'razorpay_signature'  => $validSignature,
        ]);
        $responseGood->assertStatus(200);

        $order->refresh();
        $this->assertEquals(Order::PAYMENT_PAID, $order->payment_status);
    }
}
