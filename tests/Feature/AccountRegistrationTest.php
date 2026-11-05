<?php

namespace Tests\Feature;

use App\Models\LoyaltyAccount;
use App\Models\LoyaltyTier;
use App\Models\Subscriber;
use App\Models\Template;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountRegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure active template exists
        Template::firstOrCreate(
            ['key' => 'saaya'],
            ['name' => 'Saaya', 'is_active' => true]
        );

        // Ensure base loyalty tier exists
        LoyaltyTier::firstOrCreate(
            ['slug' => 'bronze'],
            [
                'name'      => 'Bronze',
                'min_spend' => 0,
                'rate'      => 1.0,
                'color'     => 'amber',
            ]
        );
    }

    public function test_register_page_renders_luxury_storefront_design(): void
    {
        $response = $this->get(route('register'));

        $response->assertStatus(200);
        $response->assertSee('Create Your Account');
        $response->assertSee('Maison Privilege Club');
        $response->assertSee('100 Welcome Bonus Reward Points');
        $response->assertSee('Private Atelier Runway Drops');
        $response->assertSee('White-Glove Express Delivery');
        $response->assertSee('Full Name');
        $response->assertSee('Email Address');
        $response->assertSee('Choose Password');
        $response->assertSee('Confirm Password');
        $response->assertSee('Continue with Google');
        $response->assertSee('Continue with Apple');
        $response->assertSee('Sign in here');
    }

    public function test_customer_can_register_with_valid_details_and_claim_welcome_points(): void
    {
        $payload = [
            'name'                  => 'Genevieve Dupuis',
            'email'                 => 'genevieve@example.com',
            'phone'                 => '+91 98765 43210',
            'password'              => 'CoutureSecret!2026',
            'password_confirmation' => 'CoutureSecret!2026',
            'newsletter'            => '1',
            'terms'                 => '1',
        ];

        $response = $this->post(route('register'), $payload);

        $response->assertRedirect(route('home'));
        $this->assertAuthenticated();

        // Verify database user
        $this->assertDatabaseHas('users', [
            'name'  => 'Genevieve Dupuis',
            'email' => 'genevieve@example.com',
            'phone' => '+91 98765 43210',
            'role'  => 'customer',
        ]);

        $user = User::where('email', 'genevieve@example.com')->first();
        $this->assertNotNull($user);

        // Verify 100 Welcome Loyalty Points credited
        $loyaltyAccount = LoyaltyAccount::where('user_id', $user->id)->first();
        $this->assertNotNull($loyaltyAccount);
        $this->assertEquals(100, $loyaltyAccount->points_balance);

        // Verify newsletter subscriber created
        $this->assertDatabaseHas('subscribers', [
            'email'     => 'genevieve@example.com',
            'is_active' => true,
        ]);
    }

    public function test_registration_fails_with_invalid_email_or_unconfirmed_password(): void
    {
        $payload = [
            'name'                  => 'Genevieve Dupuis',
            'email'                 => 'not-an-email',
            'password'              => 'short',
            'password_confirmation' => 'different-password',
            'terms'                 => '1',
        ];

        $response = $this->post(route('register'), $payload);

        $response->assertSessionHasErrors(['email', 'password']);
        $this->assertGuest();
    }
}
