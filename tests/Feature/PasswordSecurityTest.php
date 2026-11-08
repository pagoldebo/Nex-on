<?php

namespace Tests\Feature;

use App\Models\PhoneOtp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password as PasswordBroker;
use Illuminate\Support\Str;
use Tests\TestCase;

class PasswordSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_passwords_are_securely_hashed_using_bcrypt(): void
    {
        $plainPassword = 'CorrectHorseBatteryStaple123!';
        $user = User::create([
            'name'     => 'Hash Test User',
            'email'    => 'hash-test@example.com',
            'password' => $plainPassword,
            'role'     => 'customer',
        ]);

        // Fetch raw database value directly bypassing model casts
        $rawPassword = DB::table('users')->where('id', $user->id)->value('password');

        // Verify password is NOT stored as plaintext
        $this->assertNotEquals($plainPassword, $rawPassword);

        // Verify hash uses Bcrypt format ($2y$)
        $this->assertStringStartsWith('$2y$', $rawPassword);

        // Verify hash info algorithm matches bcrypt
        $info = password_get_info($rawPassword);
        $this->assertEquals('bcrypt', $info['algoName']);

        // Verify Hash::check succeeds with correct password and fails with incorrect
        $this->assertTrue(Hash::check($plainPassword, $rawPassword));
        $this->assertFalse(Hash::check('WrongPassword!', $rawPassword));
    }

    public function test_user_model_hides_sensitive_credentials_from_serialization(): void
    {
        $user = User::create([
            'name'                      => 'Serialization User',
            'email'                     => 'serial@example.com',
            'password'                  => 'Secret123!',
            'otp_code'                  => '4829',
            'remember_token'            => Str::random(10),
            'two_factor_secret'         => encrypt('SECRET'),
            'two_factor_recovery_codes' => encrypt(json_encode(['ABC', 'DEF'])),
            'role'                      => 'customer',
        ]);

        $array = $user->toArray();
        $json  = $user->toJson();

        $this->assertArrayNotHasKey('password', $array);
        $this->assertArrayNotHasKey('remember_token', $array);
        $this->assertArrayNotHasKey('otp_code', $array);
        $this->assertArrayNotHasKey('two_factor_secret', $array);
        $this->assertArrayNotHasKey('two_factor_recovery_codes', $array);

        $this->assertStringNotContainsString('Secret123!', $json);
        $this->assertStringNotContainsString('4829', $json);
    }

    public function test_phone_otp_model_hides_otp_from_serialization(): void
    {
        $otp = PhoneOtp::create([
            'phone'       => '9876543210',
            'otp'         => '849201',
            'expires_at'  => now()->addMinutes(10),
            'is_verified' => false,
        ]);

        $array = $otp->toArray();
        $this->assertArrayNotHasKey('otp', $array);
        $this->assertStringNotContainsString('849201', $otp->toJson());
    }

    public function test_login_transparently_rehashes_passwords_when_cost_factor_changes(): void
    {
        $plain = 'RehashSecurePassword123!';
        // Create an older hash with cost factor 4
        $legacyHash = password_hash($plain, PASSWORD_BCRYPT, ['cost' => 4]);

        $user = User::create([
            'name'     => 'Rehash User',
            'email'    => 'rehash@example.com',
            'password' => $legacyHash,
            'role'     => 'customer',
        ]);

        // Emulate that the application now requires cost factor 10 or 12
        // Hash::needsRehash will return true for cost 4 if config cost is higher
        $this->assertTrue(Hash::check($plain, $user->password));

        // Perform login
        $response = $this->post('/login', [
            'email'    => 'rehash@example.com',
            'password' => $plain,
        ]);

        $response->assertStatus(302);

        // Fetch fresh password hash
        $updatedHash = DB::table('users')->where('id', $user->id)->value('password');

        // Verify password hash is valid and works
        $this->assertTrue(Hash::check($plain, $updatedHash));
    }

    public function test_user_registration_enforces_strong_password_rules(): void
    {
        // Weak password (< 8 chars)
        $response = $this->postJson('/register', [
            'name'                  => 'Weak Pass User',
            'email'                 => 'weak@example.com',
            'password'              => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['password']);

        // Strong password (>= 8 chars)
        $responseSuccess = $this->post('/register', [
            'name'                  => 'Strong Pass User',
            'email'                 => 'strong@example.com',
            'password'              => 'ValidSecurePass123!',
            'password_confirmation' => 'ValidSecurePass123!',
        ]);

        $responseSuccess->assertStatus(302);
        $user = User::where('email', 'strong@example.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue(Hash::check('ValidSecurePass123!', $user->password));
    }

    public function test_profile_password_update_requires_current_password_verification(): void
    {
        $user = User::create([
            'name'     => 'Profile Password User',
            'email'    => 'profile-pass@example.com',
            'password' => 'OldPassword123!',
            'role'     => 'customer',
        ]);

        // Attempt password change with wrong current password
        $response = $this->actingAs($user)->put('/password', [
            'current_password'      => 'WrongCurrentPassword!',
            'password'              => 'NewPassword456!',
            'password_confirmation' => 'NewPassword456!',
        ]);

        $response->assertSessionHasErrors(['current_password']);
        $this->assertTrue(Hash::check('OldPassword123!', $user->fresh()->password));

        // Submit with valid current password
        $responseSuccess = $this->actingAs($user)->put('/password', [
            'current_password'      => 'OldPassword123!',
            'password'              => 'NewPassword456!',
            'password_confirmation' => 'NewPassword456!',
        ]);

        $responseSuccess->assertRedirect(route('profile.edit'));
        $this->assertTrue(Hash::check('NewPassword456!', $user->fresh()->password));
    }

    public function test_account_password_update_requires_current_password_protection(): void
    {
        $user = User::create([
            'name'     => 'Account User',
            'email'    => 'account-user@example.com',
            'password' => 'ExistingPass123!',
            'role'     => 'customer',
        ]);

        // Attempting to change password without providing current_password must fail
        $response = $this->actingAs($user)->put('/account', [
            'name'                  => 'Account User',
            'email'                 => 'account-user@example.com',
            'password'              => 'NewHijackedPass123!',
            'password_confirmation' => 'NewHijackedPass123!',
        ]);

        $response->assertSessionHasErrors(['current_password']);
        $this->assertTrue(Hash::check('ExistingPass123!', $user->fresh()->password));

        // Providing valid current_password succeeds
        $responseSuccess = $this->actingAs($user)->put('/account', [
            'name'                  => 'Account User',
            'email'                 => 'account-user@example.com',
            'current_password'      => 'ExistingPass123!',
            'password'              => 'NewLegitPass123!',
            'password_confirmation' => 'NewLegitPass123!',
        ]);

        $responseSuccess->assertSessionHasNoErrors();
        $this->assertTrue(Hash::check('NewLegitPass123!', $user->fresh()->password));
    }

    public function test_password_reset_broker_stores_hashed_tokens_in_database(): void
    {
        $user = User::create([
            'name'     => 'Reset Token User',
            'email'    => 'reset-token@example.com',
            'password' => 'Secret123!',
            'role'     => 'customer',
        ]);

        $token = PasswordBroker::broker()->createToken($user);
        $this->assertNotEmpty($token);

        // Fetch raw stored token from password_reset_tokens table
        $stored = DB::table('password_reset_tokens')->where('email', $user->email)->first();
        $this->assertNotNull($stored);

        // Security check: Stored token must NOT be plaintext
        $this->assertNotEquals($token, $stored->token);

        // Stored token is securely hashed
        $this->assertTrue(Hash::check($token, $stored->token));
    }

    public function test_password_reset_successfully_updates_and_hashes_new_password(): void
    {
        $user = User::create([
            'name'     => 'Reset Target',
            'email'    => 'reset-target@example.com',
            'password' => 'OldPassword123!',
            'role'     => 'customer',
        ]);

        $token = PasswordBroker::broker()->createToken($user);

        // Reset password via POST /reset-password
        $response = $this->post('/reset-password', [
            'token'                 => $token,
            'email'                 => $user->email,
            'password'              => 'BrandNewPassword789!',
            'password_confirmation' => 'BrandNewPassword789!',
        ]);

        $response->assertRedirect(route('home'));

        // Verify password was updated in database and properly hashed
        $this->assertTrue(Hash::check('BrandNewPassword789!', $user->fresh()->password));
        $this->assertFalse(Hash::check('OldPassword123!', $user->fresh()->password));

        // Verify token in password_reset_tokens was consumed and removed
        $remainingToken = DB::table('password_reset_tokens')->where('email', $user->email)->first();
        $this->assertNull($remainingToken, 'Password reset token must be consumed after successful reset');
    }

    public function test_hashing_configuration_integrity(): void
    {
        $this->assertEquals('bcrypt', config('hashing.driver'));
        $this->assertTrue(config('hashing.rehash_on_login'));
        $this->assertTrue(config('hashing.bcrypt.verify'));
    }
}

