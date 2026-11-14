<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentGatewaysTest extends TestCase
{
    use RefreshDatabase;

    protected function createAdminAndOrder(): array
    {
        $admin = User::create([
            'name'     => 'Admin User',
            'email'    => 'admin@gateways.test',
            'phone'    => '9999999999',
            'password' => bcrypt('password'),
            'role'     => 'admin',
        ]);

        $order = Order::create([
            'reference'         => 'SAA-20261016-GW01',
            'channel'           => 'online',
            'status'            => Order::STATUS_PENDING,
            'subtotal'          => 2499,
            'shipping_charge'   => 0,
            'cod_charge'        => 0,
            'discount'          => 0,
            'total'             => 2499,
            'payment_method'    => 'razorpay',
            'payment_status'    => Order::PAYMENT_PENDING,
            'shipping_name'     => 'Test Customer',
            'shipping_phone'    => '9876543210',
            'shipping_email'    => 'test@example.com',
            'shipping_address1' => 'MG Road',
            'shipping_city'     => 'Bengaluru',
            'shipping_state'    => 'Karnataka',
            'shipping_pincode'  => '560001',
        ]);

        return [$admin, $order];
    }

    public function test_payment_gateways_settings_screen_loads()
    {
        [$admin] = $this->createAdminAndOrder();

        $response = $this->actingAs($admin)->get(route('admin.settings.gateways'));
        $response->assertStatus(200);
        $response->assertSee('Razorpay');
        $response->assertSee('Stripe');
        $response->assertSee('PhonePe');
        $response->assertSee('PayPal');
    }

    public function test_update_gateway_credentials()
    {
        [$admin] = $this->createAdminAndOrder();

        $response = $this->actingAs($admin)->post(route('admin.settings.gateways.update'), [
            'gateway_razorpay_enabled'        => '1',
            'gateway_razorpay_mode'           => 'live',
            'gateway_razorpay_key_id'         => 'rzp_live_newKey123',
            'gateway_razorpay_key_secret'     => 'secret_newSecret456',
            'gateway_razorpay_webhook_secret' => 'whsec_newWebhookSecret',
            'enable_cod'                      => '1',
            'cod_charge'                      => 50,
        ]);

        $response->assertSessionHas('success');

        $this->assertEquals('1', Setting::get('gateway_razorpay_enabled'));
        $this->assertEquals('live', Setting::get('gateway_razorpay_mode'));
        $this->assertEquals('rzp_live_newKey123', Setting::get('gateway_razorpay_key_id'));
        $this->assertEquals('50', Setting::get('cod_charge'));
    }

    public function test_razorpay_webhook_marks_order_paid()
    {
        [$admin, $order] = $this->createAdminAndOrder();
        $this->assertEquals(Order::PAYMENT_PENDING, $order->payment_status);

        $secret = 'whsec_testSecret123';
        Setting::set('gateway_razorpay_webhook_secret', $secret);

        $payload = [
            'event'   => 'payment.captured',
            'payload' => [
                'payment' => [
                    'entity' => [
                        'id'          => 'pay_RZPTestPayment999',
                        'amount'      => 249900,
                        'description' => $order->reference,
                        'notes'       => [
                            'order_reference' => $order->reference,
                        ],
                    ],
                ],
            ],
        ];

        $jsonPayload = json_encode($payload);
        $signature = hash_hmac('sha256', $jsonPayload, $secret);

        $response = $this->call(
            'POST',
            route('webhook.razorpay'),
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X-Razorpay-Signature' => $signature,
            ],
            $jsonPayload
        );
        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);

        $order->refresh();
        $this->assertEquals(Order::PAYMENT_PAID, $order->payment_status);
        $this->assertEquals(Order::STATUS_CONFIRMED, $order->status);
        $this->assertEquals('pay_RZPTestPayment999', $order->payment_reference);
    }

    public function test_razorpay_webhook_rejects_missing_signature()
    {
        Setting::set('gateway_razorpay_webhook_secret', 'secret');
        $response = $this->postJson(route('webhook.razorpay'), ['event' => 'payment.captured']);
        $response->assertStatus(400);
    }

    public function test_stripe_webhook_marks_order_paid()
    {
        [$admin, $order] = $this->createAdminAndOrder();

        $secret = 'whsec_stripeSecret456';
        Setting::set('gateway_stripe_webhook_secret', $secret);

        $payload = [
            'type' => 'checkout.session.completed',
            'data' => [
                'object' => [
                    'id'       => 'cs_test_stripeSession123',
                    'metadata' => [
                        'order_reference' => $order->reference,
                    ],
                ],
            ],
        ];

        $jsonPayload = json_encode($payload);
        $timestamp = time();
        $signature = hash_hmac('sha256', $timestamp . '.' . $jsonPayload, $secret);

        $response = $this->call(
            'POST',
            route('webhook.stripe'),
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_Stripe-Signature' => "t={$timestamp},v1={$signature}",
            ],
            $jsonPayload
        );
        $response->assertStatus(200);

        $order->refresh();
        $this->assertEquals(Order::PAYMENT_PAID, $order->payment_status);
        $this->assertEquals(Order::STATUS_CONFIRMED, $order->status);
    }

    public function test_stripe_webhook_rejects_invalid_signature()
    {
        Setting::set('gateway_stripe_webhook_secret', 'secret');
        $response = $this->postJson(route('webhook.stripe'), ['type' => 'checkout.session.completed']);
        $response->assertStatus(400);
    }

    public function test_active_gateways_api_returns_json()
    {
        $response = $this->getJson(route('api.payment.gateways'));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'razorpay' => ['enabled', 'key_id'],
            'stripe'   => ['enabled', 'publishable_key'],
            'phonepe'  => ['enabled'],
            'cod'      => ['enabled', 'charge'],
            'upi_qr'   => ['enabled', 'vpa'],
        ]);
    }
}
