<?php

namespace Tests\Feature;

use App\Models\LoyaltyAccount;
use App\Models\LoyaltyTier;
use App\Models\Template;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoyaltyRewardsProgramTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $customer;
    protected LoyaltyTier $tier;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'email' => 'loyalty.lead@nexon.luxury',
            'role'  => 'admin',
        ]);

        $this->customer = User::factory()->create([
            'name'  => 'Kiara Advani',
            'email' => 'kiara@luxury.in',
            'role'  => 'customer',
        ]);

        Template::create(['key' => 'buttercup', 'name' => 'Buttercup', 'is_active' => true]);

        $this->tier = LoyaltyTier::where('slug', 'gold')->first() ?? LoyaltyTier::create([
            'name'              => 'Gold VIP',
            'slug'              => 'gold',
            'min_spend'         => 15000,
            'points_multiplier' => 1.25,
            'perks'             => 'Free express delivery',
            'is_active'         => true,
        ]);
    }

    public function test_loyalty_dashboard_loads_and_lists_members(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.marketing.loyalty'));

        $response->assertStatus(200);
        $response->assertSee('VIP Loyalty Rewards &amp; Points Program', false);
        $response->assertSee('Kiara Advani');
    }

    public function test_admin_can_update_loyalty_tier_rules(): void
    {
        $payload = [
            'name'              => 'Gold VIP Royal',
            'min_spend'         => 20000,
            'points_multiplier' => 1.35,
            'perks'             => 'Exclusive previews + Dedicated stylist',
        ];

        $response = $this->actingAs($this->admin)->put(route('admin.marketing.loyalty.tier.update', $this->tier), $payload);

        $response->assertRedirect();
        $this->assertDatabaseHas('loyalty_tiers', [
            'id'                => $this->tier->id,
            'name'              => 'Gold VIP Royal',
            'min_spend'         => 20000,
            'points_multiplier' => 1.35,
        ]);
    }

    public function test_admin_can_adjust_member_points(): void
    {
        $account = LoyaltyAccount::where('user_id', $this->customer->id)->first() ?? LoyaltyAccount::create([
            'user_id'         => $this->customer->id,
            'loyalty_tier_id' => $this->tier->id,
            'points_balance'  => 100,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.marketing.loyalty.account.adjust', $account), [
            'points'      => 500,
            'description' => 'Festive Goodwill Bonus',
        ]);

        $response->assertRedirect();
        $account->refresh();
        $this->assertEquals(600, $account->points_balance);

        $this->assertDatabaseHas('loyalty_transactions', [
            'loyalty_account_id' => $account->id,
            'type'               => 'adjusted',
            'points'             => 500,
            'description'        => 'Festive Goodwill Bonus',
        ]);
    }
}

