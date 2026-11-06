<?php

namespace Tests\Feature;

use App\Models\Template;
use App\Models\User;
use App\Notifications\TwoFactorOTP;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class LoginEmailOtpTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure active template exists for storefront rendering
        Template::firstOrCreate(
            ['key' => 'vyra'],
            [
                'name'       => 'Vyra',
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    public function test_login_with_invalid_credentials_fails(): void
    {
        $user = User::factory()->create([
            'email'    => 'testuser@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post(route('login'), [
            'email'    => 'testuser@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
        $this->assertNull($user->fresh()->otp_code);
    }

    public function test_deactivated_user_cannot_login(): void
    {
        $user = User::factory()->create([
            'email'          => 'deactivated@example.com',
            'password'       => Hash::make('password123'),
            'deactivated_at' => now(),
        ]);

        $response = $this->post(route('login'), [
            'email'    => 'deactivated@example.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
        $this->assertNull($user->fresh()->otp_code);
    }

    public function test_login_with_valid_credentials_dispatches_email_otp_and_redirects_to_verify(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email'    => 'patron@example.com',
            'password' => Hash::make('password123'),
            'name'     => 'Lady Alistair',
        ]);

        $response = $this->post(route('login'), [
            'email'    => 'patron@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('verification.2fa.form'));
        $response->assertSessionHas('pending_2fa_user_id', $user->id);
        $this->assertGuest(); // User should not be authenticated yet until OTP is verified

        $freshUser = $user->fresh();
        $this->assertNotNull($freshUser->otp_code);
        $this->assertEquals(4, strlen($freshUser->otp_code));
        $this->assertNotNull($freshUser->otp_expires_at);

        Notification::assertSentOnDemand(
            TwoFactorOTP::class,
            function ($notification, $channels, $notifiable) use ($user, $freshUser) {
                return $notifiable->routes['mail'] === $user->email &&
                       $notification->otp === $freshUser->otp_code;
            }
        );
    }

    public function test_verify_otp_page_requires_pending_session(): void
    {
        $response = $this->get(route('verification.otp.form'));
        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
    }

    public function test_verify_otp_page_renders_successfully_with_session(): void
    {
        $user = User::factory()->create([
            'email' => 'patron@example.com',
        ]);

        $response = $this->withSession(['pending_2fa_user_id' => $user->id])
                         ->get(route('verification.otp.form'));

        $response->assertStatus(200);
        $response->assertSee('Verify Your Sign In');
        $response->assertSee('patron@example.com');
        $response->assertSee('Two-Step Verification');
    }

    public function test_submitting_incorrect_otp_fails(): void
    {
        $user = User::factory()->create([
            'email'          => 'patron@example.com',
            'otp_code'       => '1234',
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        $response = $this->withSession(['pending_2fa_user_id' => $user->id])
                         ->post(route('verification.2fa'), [
                             'otp' => '9999',
                         ]);

        $response->assertSessionHasErrors('otp');
        $this->assertGuest();
        $this->assertEquals('1234', $user->fresh()->otp_code);
    }

    public function test_submitting_expired_otp_fails(): void
    {
        $user = User::factory()->create([
            'email'          => 'patron@example.com',
            'otp_code'       => '1234',
            'otp_expires_at' => now()->subMinute(), // Expired
        ]);

        $response = $this->withSession(['pending_2fa_user_id' => $user->id])
                         ->post(route('verification.2fa'), [
                             'otp' => '1234',
                         ]);

        $response->assertSessionHasErrors('otp');
        $this->assertGuest();
    }

    public function test_submitting_valid_otp_authenticates_customer_and_clears_otp(): void
    {
        $user = User::factory()->create([
            'name'           => 'Eleanor Vance',
            'email'          => 'patron@example.com',
            'role'           => 'customer',
            'otp_code'       => '5678',
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        $response = $this->withSession(['pending_2fa_user_id' => $user->id])
                         ->post(route('verification.2fa'), [
                             'otp' => '5678',
                         ]);

        $response->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($user);

        $freshUser = $user->fresh();
        $this->assertNull($freshUser->otp_code);
        $this->assertNull($freshUser->otp_expires_at);
    }

    public function test_submitting_valid_otp_authenticates_admin_and_redirects_to_dashboard(): void
    {
        $admin = User::factory()->create([
            'name'           => 'Admin Director',
            'email'          => 'admin@nexon.app',
            'role'           => 'admin',
            'otp_code'       => '4321',
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        $response = $this->withSession(['pending_2fa_user_id' => $admin->id])
                         ->post(route('verification.2fa'), [
                             'otp' => '4321',
                         ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);

        $this->assertNull($admin->fresh()->otp_code);
    }

    public function test_resend_otp_generates_new_code_and_sends_notification(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'name'           => 'Lady Alistair',
            'email'          => 'patron@example.com',
            'otp_code'       => '1111',
            'otp_expires_at' => now()->addMinutes(2),
        ]);

        $response = $this->withSession(['pending_2fa_user_id' => $user->id])
                         ->post(route('verification.2fa.resend'));

        $response->assertSessionHas('status');

        $freshUser = $user->fresh();
        $this->assertNotNull($freshUser->otp_code);
        $this->assertNotEquals('1111', $freshUser->otp_code);

        Notification::assertSentOnDemand(
            TwoFactorOTP::class,
            function ($notification, $channels, $notifiable) use ($user, $freshUser) {
                return $notifiable->routes['mail'] === $user->email &&
                       $notification->otp === $freshUser->otp_code;
            }
        );
    }

    public function test_ajax_resend_otp_returns_json(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'name'           => 'Lady Alistair',
            'email'          => 'patron@example.com',
            'otp_code'       => '2222',
            'otp_expires_at' => now()->addMinutes(5),
        ]);

        $response = $this->withSession(['pending_2fa_user_id' => $user->id])
                         ->postJson(route('verification.2fa.resend'));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
    }

    public function test_email_otp_view_renders_template(): void
    {
        $rendered = view('emails.two-factor-otp', [
            'otp'             => '7890',
            'user'            => (object) ['name' => 'Patron Eleanor'],
            'appName'         => 'Project Nexon',
            'storeLogo'       => null,
            'supportEmail'    => 'concierge@nexon.app',
            'supportPhone'    => '+91 98765 43210',
            'storeAddress'    => '101 Fashion Blvd, Mumbai',
        ])->render();

        $this->assertStringContainsString('7890', $rendered);
        $this->assertStringContainsString('Patron Eleanor', $rendered);
        $this->assertStringContainsString('Project Nexon', $rendered);
        $this->assertStringContainsString('concierge@nexon.app', $rendered);
        $this->assertStringContainsString('#F9F8F3', $rendered);
        $this->assertStringContainsString('#000000', $rendered);
        $this->assertStringContainsString('Valid for 10 minutes', $rendered);
    }

    public function test_email_preview_route_renders_successfully(): void
    {
        $response = $this->get(route('email.preview.otp', ['otp' => '9988', 'name' => 'Sophia']));

        $response->assertStatus(200);
        $response->assertSee('9988');
        $response->assertSee('Sophia');
        $response->assertSee('Login Verification');
    }
}

