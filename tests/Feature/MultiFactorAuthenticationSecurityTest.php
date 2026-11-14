<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\Auth\TotpService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class MultiFactorAuthenticationSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected TotpService $totp;

    protected function setUp(): void
    {
        parent::setUp();
        $this->totp = app(TotpService::class);
        RateLimiter::clear('2fa_challenge_attempts:1');
        RateLimiter::clear('2fa_confirm:1');
    }

    public function test_totp_service_generates_local_qr_code_without_third_party_leak(): void
    {
        $secret = 'JBSWY3DPEHPK3PXPJBSWY3DPEHPK3PXP';
        $email = 'patron@example.com';
        $company = 'Nex-on';

        $qrCodeDataUri = $this->totp->getQrCodeSvgUrl($company, $email, $secret);

        // Security assertion: Never leak user secret to third-party domains (e.g. api.qrserver.com)
        $this->assertStringNotContainsString('qrserver.com', $qrCodeDataUri);
        $this->assertStringNotContainsString('google.com/chart', $qrCodeDataUri);

        // Must be a self-contained RFC 2397 Data URI
        $this->assertStringStartsWith('data:image/svg+xml;base64,', $qrCodeDataUri);

        $base64 = str_replace('data:image/svg+xml;base64,', '', $qrCodeDataUri);
        $svg = base64_decode($base64);

        $this->assertStringContainsString('<svg', $svg);
        $this->assertStringContainsString('<rect', $svg);
    }

    public function test_user_otp_generation_uses_csprng_and_stores_expiry(): void
    {
        $user = User::create([
            'name'     => 'CSPRNG User',
            'email'    => 'csprng@example.com',
            'password' => bcrypt('password123'),
            'role'     => 'customer',
        ]);

        $otp = $user->generateOtp(10);

        $this->assertEquals(6, strlen($otp));
        $this->assertTrue(ctype_digit($otp));
        $this->assertEquals($otp, $user->fresh()->otp_code);
        $this->assertNotNull($user->fresh()->otp_expires_at);
        $this->assertTrue($user->fresh()->otp_expires_at->isFuture());
    }

    public function test_totp_authentication_flow_success(): void
    {
        $rawSecret = $this->totp->generateSecretKey();

        $user = User::forceCreate([
            'name'                    => 'MFA Patron',
            'email'                   => 'mfa-patron@example.com',
            'password'                => bcrypt('ValidPass123!'),
            'two_factor_secret'       => encrypt($rawSecret),
            'two_factor_confirmed_at' => now(),
            'role'                    => 'customer',
        ]);

        $timeSlice = (int) floor(time() / 30);
        $validCode = $this->totp->calculateCode($rawSecret, $timeSlice);

        $response = $this->withSession(['login.id' => $user->id])
            ->post('/two-factor-challenge', [
                'code' => $validCode,
            ]);

        $response->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_totp_code_cannot_be_replayed_within_validity_window(): void
    {
        $rawSecret = $this->totp->generateSecretKey();

        $user = User::forceCreate([
            'name'                    => 'Replay Target',
            'email'                   => 'replay-target@example.com',
            'password'                => bcrypt('ValidPass123!'),
            'two_factor_secret'       => encrypt($rawSecret),
            'two_factor_confirmed_at' => now(),
            'role'                    => 'customer',
        ]);

        $timeSlice = (int) floor(time() / 30);
        $validCode = $this->totp->calculateCode($rawSecret, $timeSlice);

        // 1st use succeeds
        $firstResponse = $this->withSession(['login.id' => $user->id])
            ->post('/two-factor-challenge', ['code' => $validCode]);
        $firstResponse->assertRedirect(route('home'));

        // Reset auth state to simulate a separate session/attacker attempting replay
        \Illuminate\Support\Facades\Auth::logout();
        $this->flushSession();

        // Attacker attempts to replay the same code in the same 30s window
        $replayResponse = $this->withSession(['login.id' => $user->id])
            ->post('/two-factor-challenge', ['code' => $validCode]);

        $replayResponse->assertSessionHasErrors(['code']);
        $errors = session('errors')->get('code');
        $this->assertStringContainsString('already been used', $errors[0]);
    }

    public function test_totp_challenge_blocks_unconfirmed_2fa_and_redirects(): void
    {
        $rawSecret = $this->totp->generateSecretKey();

        // User started 2FA setup but never confirmed it
        $user = User::forceCreate([
            'name'                    => 'Unconfirmed 2FA User',
            'email'                   => 'unconfirmed-2fa@example.com',
            'password'                => bcrypt('ValidPass123!'),
            'two_factor_secret'       => encrypt($rawSecret),
            'two_factor_confirmed_at' => null,
            'role'                    => 'customer',
        ]);

        $this->assertFalse($user->hasTwoFactorEnabled());

        $response = $this->withSession(['login.id' => $user->id])
            ->post('/two-factor-challenge', ['code' => '123456']);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors(['email']);
        $this->assertFalse(session()->has('login.id'));
    }

    public function test_totp_challenge_invalidates_session_after_excessive_failed_attempts(): void
    {
        $rawSecret = $this->totp->generateSecretKey();

        $user = User::forceCreate([
            'name'                    => 'Brute Target',
            'email'                   => 'brute-target@example.com',
            'password'                => bcrypt('ValidPass123!'),
            'two_factor_secret'       => encrypt($rawSecret),
            'two_factor_confirmed_at' => now(),
            'role'                    => 'customer',
        ]);

        // Submit wrong 2FA codes 5 times
        for ($i = 0; $i < 5; $i++) {
            $this->withSession(['login.id' => $user->id])
                ->post('/two-factor-challenge', ['code' => '00000' . $i]);
        }

        // 6th attempt must terminate challenge session
        $finalResponse = $this->withSession(['login.id' => $user->id])
            ->post('/two-factor-challenge', ['code' => '999999']);

        $finalResponse->assertSessionHasErrors(['code']);
        $errors = session('errors')->get('code');
        $this->assertStringContainsString('Session terminated', $errors[0]);
        $this->assertFalse(session()->has('login.id'));
    }

    public function test_two_factor_recovery_code_one_time_consumption(): void
    {
        $rawSecret = $this->totp->generateSecretKey();
        $recoveryCodes = ['code1-1234567890', 'code2-0987654321'];

        $user = User::forceCreate([
            'name'                      => 'Recovery Code User',
            'email'                     => 'recovery@example.com',
            'password'                  => bcrypt('ValidPass123!'),
            'two_factor_secret'         => encrypt($rawSecret),
            'two_factor_recovery_codes' => encrypt(json_encode($recoveryCodes)),
            'two_factor_confirmed_at'   => now(),
            'role'                      => 'customer',
        ]);

        // Use first recovery code
        $response = $this->withSession(['login.id' => $user->id])
            ->post('/two-factor-challenge', ['recovery_code' => 'code1-1234567890']);

        $response->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($user);

        // Security check: Code must be consumed and removed from database
        $remainingCodes = $user->fresh()->getRecoveryCodes();
        $this->assertCount(1, $remainingCodes);
        $this->assertFalse(in_array('code1-1234567890', $remainingCodes));

        // Reset auth state to simulate a second login attempt
        \Illuminate\Support\Facades\Auth::logout();
        $this->flushSession();

        // Attempting to reuse the consumed recovery code must fail
        $reuseResponse = $this->withSession(['login.id' => $user->id])
            ->post('/two-factor-challenge', ['recovery_code' => 'code1-1234567890']);

        $reuseResponse->assertSessionHasErrors(['recovery_code']);
    }

    public function test_two_factor_disable_requires_password_when_active(): void
    {
        $rawSecret = $this->totp->generateSecretKey();

        $user = User::forceCreate([
            'name'                    => 'Armed User',
            'email'                   => 'armed@example.com',
            'password'                => bcrypt('MySecretPassword123!'),
            'two_factor_secret'       => encrypt($rawSecret),
            'two_factor_confirmed_at' => now(),
            'role'                    => 'customer',
        ]);

        // Attempting to disable with wrong password must fail
        $failResponse = $this->actingAs($user)->delete('/user/two-factor-authentication', [
            'password' => 'WrongPassword!',
        ]);
        $failResponse->assertSessionHasErrors(['password']);
        $this->assertTrue($user->fresh()->hasTwoFactorEnabled());

        // Submitting with correct password succeeds
        $successResponse = $this->actingAs($user)->delete('/user/two-factor-authentication', [
            'password' => 'MySecretPassword123!',
        ]);
        $successResponse->assertSessionHasNoErrors();
        $this->assertFalse($user->fresh()->hasTwoFactorEnabled());
        $this->assertNull($user->fresh()->two_factor_secret);
    }

    public function test_two_factor_cancel_unconfirmed_setup_does_not_fail(): void
    {
        $rawSecret = $this->totp->generateSecretKey();

        // User is setting up 2FA, has not yet confirmed
        $user = User::forceCreate([
            'name'                    => 'Setup Canceller',
            'email'                   => 'cancel@example.com',
            'password'                => bcrypt('MySecretPassword123!'),
            'two_factor_secret'       => encrypt($rawSecret),
            'two_factor_confirmed_at' => null,
            'role'                    => 'customer',
        ]);

        // Cancelling unconfirmed setup should succeed gracefully
        $response = $this->actingAs($user)->delete('/user/two-factor-authentication');
        $response->assertSessionHasNoErrors();
        $this->assertNull($user->fresh()->two_factor_secret);
    }

    public function test_two_factor_confirm_rate_limiting(): void
    {
        $rawSecret = $this->totp->generateSecretKey();

        $user = User::forceCreate([
            'name'                    => 'Confirm Brute User',
            'email'                   => 'confirm-brute@example.com',
            'password'                => bcrypt('MySecretPassword123!'),
            'two_factor_secret'       => encrypt($rawSecret),
            'two_factor_confirmed_at' => null,
            'role'                    => 'customer',
        ]);

        for ($i = 0; $i < 5; $i++) {
            $this->actingAs($user)->post('/user/confirmed-two-factor-authentication', ['code' => '00000' . $i]);
        }

        // 6th attempt must be throttled
        $response = $this->actingAs($user)->post('/user/confirmed-two-factor-authentication', ['code' => '999999']);
        $response->assertSessionHasErrors(['code']);
        $errors = session('errors')->get('code');
        $this->assertStringContainsString('Too many failed confirmation attempts', $errors[0]);
    }
}
