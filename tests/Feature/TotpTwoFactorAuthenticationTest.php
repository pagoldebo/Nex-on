<?php

namespace Tests\Feature;

use App\Models\Template;
use App\Models\User;
use App\Services\Auth\TotpService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TotpTwoFactorAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

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

    public function test_user_can_enable_two_factor_authentication()
    {
        $user = User::factory()->create([
            'password' => Hash::make('password123'),
        ]);

        $response = $this->actingAs($user)->post(route('two-factor.enable'));

        $response->assertSessionHas('status', 'two-factor-authentication-enabled');
        $user->refresh();

        $this->assertNotNull($user->two_factor_secret);
        $this->assertNotNull($user->two_factor_recovery_codes);
        $this->assertNull($user->two_factor_confirmed_at);
        $this->assertCount(8, $user->getRecoveryCodes());
    }

    public function test_user_can_confirm_two_factor_authentication_with_valid_code()
    {
        $totp = app(TotpService::class);
        $secret = $totp->generateSecretKey();

        $user = User::factory()->create([
            'two_factor_secret'         => encrypt($secret),
            'two_factor_recovery_codes' => encrypt(json_encode($totp->generateRecoveryCodes())),
            'two_factor_confirmed_at'   => null,
        ]);

        $validCode = $totp->calculateCode($secret, (int) floor(time() / 30));

        $response = $this->actingAs($user)->post(route('two-factor.confirm'), [
            'code' => $validCode,
        ]);

        $response->assertSessionHas('status', 'two-factor-authentication-confirmed');
        $user->refresh();

        $this->assertTrue($user->hasTwoFactorEnabled());
        $this->assertNotNull($user->two_factor_confirmed_at);
    }

    public function test_user_cannot_confirm_two_factor_with_invalid_code()
    {
        $totp = app(TotpService::class);
        $secret = $totp->generateSecretKey();

        $user = User::factory()->create([
            'two_factor_secret'         => encrypt($secret),
            'two_factor_recovery_codes' => encrypt(json_encode($totp->generateRecoveryCodes())),
            'two_factor_confirmed_at'   => null,
        ]);

        $response = $this->actingAs($user)->post(route('two-factor.confirm'), [
            'code' => '000000',
        ]);

        $response->assertSessionHasErrors('code');
        $this->assertFalse($user->fresh()->hasTwoFactorEnabled());
    }

    public function test_user_can_regenerate_recovery_codes()
    {
        $totp = app(TotpService::class);
        $secret = $totp->generateSecretKey();
        $initialCodes = ['code-1', 'code-2'];

        $user = User::factory()->create([
            'two_factor_secret'         => encrypt($secret),
            'two_factor_recovery_codes' => encrypt(json_encode($initialCodes)),
            'two_factor_confirmed_at'   => now(),
        ]);

        $response = $this->actingAs($user)->post(route('two-factor.regenerate-recovery-codes'));

        $response->assertSessionHas('status', 'recovery-codes-generated');
        $newCodes = $user->fresh()->getRecoveryCodes();

        $this->assertCount(8, $newCodes);
        $this->assertNotEquals($initialCodes, $newCodes);
    }

    public function test_user_can_disable_two_factor_with_correct_password()
    {
        $user = User::factory()->create([
            'password'                  => Hash::make('secret-pass'),
            'two_factor_secret'         => encrypt('dummysecret'),
            'two_factor_recovery_codes' => encrypt(json_encode(['code-1'])),
            'two_factor_confirmed_at'   => now(),
        ]);

        $response = $this->actingAs($user)->delete(route('two-factor.disable'), [
            'password' => 'secret-pass',
        ]);

        $response->assertSessionHas('status', 'two-factor-authentication-disabled');
        $user->refresh();

        $this->assertFalse($user->hasTwoFactorEnabled());
        $this->assertNull($user->two_factor_secret);
        $this->assertNull($user->two_factor_recovery_codes);
    }

    public function test_login_flow_redirects_to_totp_challenge_when_two_factor_is_active()
    {
        $totp = app(TotpService::class);
        $secret = $totp->generateSecretKey();
        $recoveryCodes = ['backup-code-1234'];

        $user = User::factory()->create([
            'email'                     => 'totpuser@example.com',
            'password'                  => Hash::make('password123'),
            'two_factor_secret'         => encrypt($secret),
            'two_factor_recovery_codes' => encrypt(json_encode($recoveryCodes)),
            'two_factor_confirmed_at'   => now(),
        ]);

        $response = $this->post(route('login'), [
            'email'    => 'totpuser@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('two-factor.login'));
        $this->assertEquals($user->id, session('login.id'));

        // Challenge screen renders
        $challengeResponse = $this->get(route('two-factor.login'));
        $challengeResponse->assertStatus(200);

        // Authenticate with valid TOTP
        $validCode = $totp->calculateCode($secret, (int) floor(time() / 30));
        $authResponse = $this->post(route('two-factor.login'), [
            'code' => $validCode,
        ]);

        $authResponse->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_authenticates_with_recovery_code_and_burns_it()
    {
        $totp = app(TotpService::class);
        $secret = $totp->generateSecretKey();
        $recoveryCodes = ['recovery-code-alpha', 'recovery-code-beta'];

        $user = User::factory()->create([
            'email'                     => 'recoveryuser@example.com',
            'password'                  => Hash::make('password123'),
            'two_factor_secret'         => encrypt($secret),
            'two_factor_recovery_codes' => encrypt(json_encode($recoveryCodes)),
            'two_factor_confirmed_at'   => now(),
        ]);

        $this->withSession(['login.id' => $user->id]);

        $response = $this->post(route('two-factor.login'), [
            'recovery_code' => 'recovery-code-alpha',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($user);

        // recovery-code-alpha must be burned, only recovery-code-beta remains
        $remaining = $user->fresh()->getRecoveryCodes();
        $this->assertCount(1, $remaining);
        $this->assertContains('recovery-code-beta', $remaining);
        $this->assertNotContains('recovery-code-alpha', $remaining);
    }
}

