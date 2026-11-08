<?php

namespace Tests\Feature;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RateLimitingSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Clear rate limiter cache between tests to ensure clean state
        RateLimiter::clear('login');
        RateLimiter::clear('register');
        RateLimiter::clear('otp_verify');
        RateLimiter::clear('otp_resend');
        RateLimiter::clear('password_reset');
        RateLimiter::clear('sms_send');
        RateLimiter::clear('sms_verify');
        RateLimiter::clear('coupon_apply');
        RateLimiter::clear('newsletter');
        RateLimiter::clear('contact');
        RateLimiter::clear('checkout');
        RateLimiter::clear('search');
        RateLimiter::clear('order_request');
        RateLimiter::clear('payment_verify');
        RateLimiter::clear('webhooks');
    }

    public function test_login_rate_limiting_throttles_excessive_attempts(): void
    {
        $email = 'brute-force-target@example.com';

        for ($i = 0; $i < 5; $i++) {
            $response = $this->postJson('/login', [
                'email'    => $email,
                'password' => 'wrong-password-' . $i,
            ]);
            $this->assertNotEquals(429, $response->getStatusCode(), "Attempt {$i} should not be throttled yet");
        }

        // 6th attempt must be throttled with HTTP 429
        $response = $this->postJson('/login', [
            'email'    => $email,
            'password' => 'wrong-password-6',
        ]);

        $this->assertEquals(429, $response->getStatusCode());
    }

    public function test_otp_verification_rate_limiting_and_brute_force_invalidation(): void
    {
        $user = User::create([
            'name'     => 'OTP Target User',
            'email'    => 'otp-target@example.com',
            'password' => bcrypt('secret123'),
            'role'     => 'customer',
        ]);

        $user->otp_code = '4829';
        $user->otp_expires_at = now()->addMinutes(10);
        $user->save();

        // 5 consecutive wrong OTP attempts
        for ($i = 0; $i < 5; $i++) {
            $response = $this->withSession(['pending_2fa_user_id' => $user->id])
                ->postJson('/verify-otp', ['otp' => '000' . $i]);

            // Attempt 5 triggers brute force invalidation (either 429 or 422 with invalidation)
            if ($response->getStatusCode() === 429) {
                break;
            }
        }

        // 6th attempt must be strictly blocked (429 or redirected with invalidated message)
        $finalResponse = $this->withSession(['pending_2fa_user_id' => $user->id])
            ->postJson('/verify-otp', ['otp' => '4829']);

        $this->assertTrue(
            in_array($finalResponse->getStatusCode(), [422, 429]),
            "Expected 422 or 429, got {$finalResponse->getStatusCode()}"
        );

        // Security assertion: OTP code in database must be invalidated to protect patron
        $this->assertNull($user->fresh()->otp_code, 'Target user OTP must be wiped from database upon brute force attempt');
    }

    public function test_otp_resend_rate_limiting_blocks_email_bombing(): void
    {
        Notification::fake();

        $user = User::create([
            'name'     => 'Resend Target User',
            'email'    => 'resend-target@example.com',
            'password' => bcrypt('secret123'),
            'role'     => 'customer',
        ]);

        // First 3 resend attempts allowed
        for ($i = 0; $i < 3; $i++) {
            $response = $this->withSession(['pending_2fa_user_id' => $user->id])
                ->postJson('/resend-otp');
            $this->assertNotEquals(429, $response->getStatusCode(), "Resend {$i} should not be throttled yet");
        }

        // 4th attempt must be throttled with HTTP 429
        $response = $this->withSession(['pending_2fa_user_id' => $user->id])
            ->postJson('/resend-otp');

        $this->assertEquals(429, $response->getStatusCode());
    }

    public function test_registration_rate_limiting_blocks_spam(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $response = $this->postJson('/register', [
                'name'                  => "Spam User {$i}",
                'email'                 => "spam-user-{$i}@testdomain.com",
                'password'              => 'StrongPassword123!',
                'password_confirmation' => 'StrongPassword123!',
            ]);
            $this->assertNotEquals(429, $response->getStatusCode(), "Registration {$i} should not be throttled yet");
        }

        // 6th registration attempt must be throttled with HTTP 429
        $response = $this->postJson('/register', [
            'name'                  => 'Spam User 6',
            'email'                 => 'spam-user-6@testdomain.com',
            'password'              => 'StrongPassword123!',
            'password_confirmation' => 'StrongPassword123!',
        ]);

        $this->assertEquals(429, $response->getStatusCode());
    }

    public function test_password_reset_rate_limiting_blocks_enumeration(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $response = $this->postJson('/forgot-password', [
                'email' => 'victim@example.com',
            ]);
            $this->assertNotEquals(429, $response->getStatusCode());
        }

        // 6th request must be throttled
        $response = $this->postJson('/forgot-password', [
            'email' => 'victim@example.com',
        ]);

        $this->assertEquals(429, $response->getStatusCode());
    }

    public function test_sms_send_otp_rate_limiting_blocks_financial_drain(): void
    {
        $phone = '9876543210';

        for ($i = 0; $i < 5; $i++) {
            $response = $this->postJson('/api/sms/send-otp', ['phone' => $phone]);
            $this->assertNotEquals(429, $response->getStatusCode(), "SMS send attempt {$i} should succeed");
        }

        // 6th SMS request must return HTTP 429
        $response = $this->postJson('/api/sms/send-otp', ['phone' => $phone]);
        $this->assertEquals(429, $response->getStatusCode());
    }

    public function test_sms_verify_otp_rate_limiting_blocks_brute_force(): void
    {
        $phone = '9876543210';

        for ($i = 0; $i < 5; $i++) {
            $response = $this->postJson('/api/sms/verify-otp', [
                'phone' => $phone,
                'otp'   => '00000' . $i,
            ]);
            $this->assertNotEquals(429, $response->getStatusCode(), "SMS verify attempt {$i} should not be 429 yet");
        }

        // 6th verify attempt must return HTTP 429
        $response = $this->postJson('/api/sms/verify-otp', [
            'phone' => $phone,
            'otp'   => '999999',
        ]);
        $this->assertEquals(429, $response->getStatusCode());
    }

    public function test_coupon_apply_rate_limiting_blocks_dictionary_attacks(): void
    {
        for ($i = 0; $i < 10; $i++) {
            $response = $this->postJson('/coupon/apply', [
                'code'     => 'GUESS' . $i,
                'subtotal' => 1000,
            ]);
            $this->assertNotEquals(429, $response->getStatusCode(), "Coupon guess {$i} should not be throttled yet");
        }

        // 11th invalid attempt must return HTTP 429
        $response = $this->postJson('/coupon/apply', [
            'code'     => 'GUESS11',
            'subtotal' => 1000,
        ]);

        $this->assertEquals(429, $response->getStatusCode());
    }

    public function test_newsletter_subscribe_rate_limiting_blocks_flood(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $response = $this->postJson('/newsletter/subscribe', [
                'email' => "subscriber-{$i}@testmail.com",
            ]);
            $this->assertNotEquals(429, $response->getStatusCode());
        }

        // 6th attempt must return HTTP 429
        $response = $this->postJson('/newsletter/subscribe', [
            'email' => 'subscriber-6@testmail.com',
        ]);

        $this->assertEquals(429, $response->getStatusCode());
    }

    public function test_contact_form_rate_limiting_blocks_flood(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $response = $this->postJson('/contact', [
                'name'      => "Contact Sender {$i}",
                'email'     => "contact{$i}@testdomain.com",
                'message'   => "Hello from test message {$i}",
                'form_time' => (string) (time() * 1000 - 5000), // Avoid bot fast-submit detector
            ]);
            $this->assertNotEquals(429, $response->getStatusCode());
        }

        // 6th attempt must return HTTP 429
        $response = $this->postJson('/contact', [
            'name'      => 'Contact Sender 6',
            'email'     => 'contact6@testdomain.com',
            'message'   => 'Hello from test message 6',
            'form_time' => (string) (time() * 1000 - 5000),
        ]);

        $this->assertEquals(429, $response->getStatusCode());
    }

    public function test_critical_routes_have_throttle_middleware_registered(): void
    {
        $routesToCheck = [
            'checkout.placeOrder'     => 'throttle:checkout',
            'search'                  => 'throttle:search',
            'orders.request'          => 'throttle:order_request',
            'api.payment.razorpay.verify' => 'throttle:payment_verify',
            'webhook.razorpay'        => 'throttle:webhooks',
            'webhook.stripe'          => 'throttle:webhooks',
            'webhook.phonepe'         => 'throttle:webhooks',
            'pincode.check'           => 'throttle:pincode_check',
            'products.review'         => 'throttle:reviews',
            'driver.order.status'     => 'throttle:driver_status',
            'api.push.subscribe'      => 'throttle:push_subscription',
        ];

        foreach ($routesToCheck as $routeName => $expectedMiddleware) {
            $route = Route::getRoutes()->getByName($routeName);
            $this->assertNotNull($route, "Route {$routeName} must exist");
            $middleware = $route->gatherMiddleware();
            $this->assertTrue(
                in_array($expectedMiddleware, $middleware),
                "Route {$routeName} must contain {$expectedMiddleware} middleware. Found: " . implode(', ', $middleware)
            );
        }
    }
}

