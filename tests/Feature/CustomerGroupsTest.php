<?php

namespace Tests\Feature;

use App\Models\CustomerGroup;
use App\Models\Template;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerGroupsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'email' => 'admin.groups@nexon.com',
            'role'  => 'admin',
        ]);

        $this->customer = User::factory()->create([
            'name'  => 'Pooja Hegde',
            'email' => 'pooja@example.com',
            'role'  => 'customer',
        ]);

        Template::create(['key' => 'buttercup', 'name' => 'Buttercup', 'is_active' => true]);
    }

    public function test_customer_groups_index_loads_successfully()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.customer-groups.index'));

        $response->assertStatus(200);
        $response->assertSee('Customer Groups &amp; Wholesale Tiers', false);
    }

    public function test_admin_can_create_customer_group()
    {
        $response = $this->actingAs($this->admin)->post(route('admin.customer-groups.store'), [
            'name'           => 'VIP Platinum Tier',
            'code'           => 'vip-platinum',
            'description'    => 'Exclusive 20% discount on entire catalog',
            'discount_type'  => 'percentage',
            'discount_value' => 20,
            'min_spend'      => 5000,
            'is_active'      => 1,
        ]);

        $response->assertRedirect(route('admin.customer-groups.index'));
        $this->assertDatabaseHas('customer_groups', [
            'name'           => 'VIP Platinum Tier',
            'code'           => 'vip-platinum',
            'discount_type'  => 'percentage',
            'discount_value' => 20.00,
            'min_spend'      => 5000.00,
            'is_active'      => 1,
        ]);
    }

    public function test_admin_can_update_customer_group()
    {
        $group = CustomerGroup::create([
            'name'           => 'B2B Wholesale',
            'code'           => 'b2b-wholesale',
            'discount_type'  => 'fixed',
            'discount_value' => 1000,
            'min_spend'      => 10000,
            'is_active'      => true,
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.customer-groups.update', $group), [
            'name'           => 'B2B Wholesale Elite',
            'code'           => 'b2b-wholesale-elite',
            'discount_type'  => 'fixed',
            'discount_value' => 1500,
            'min_spend'      => 12000,
            'is_active'      => 1,
        ]);

        $response->assertRedirect(route('admin.customer-groups.index'));
        $group->refresh();
        $this->assertEquals('B2B Wholesale Elite', $group->name);
        $this->assertEquals(1500.00, $group->discount_value);
    }

    public function test_admin_can_assign_and_remove_customers()
    {
        $group = CustomerGroup::create([
            'name'           => 'Gold Members',
            'code'           => 'gold-members',
            'discount_type'  => 'percentage',
            'discount_value' => 10,
        ]);

        // Assign
        $response = $this->actingAs($this->admin)->post(route('admin.customer-groups.assign', $group), [
            'user_ids' => [$this->customer->id],
        ]);

        $response->assertRedirect(route('admin.customer-groups.index'));
        $this->assertDatabaseHas('customer_group_user', [
            'customer_group_id' => $group->id,
            'user_id'           => $this->customer->id,
        ]);

        // Remove
        $removeResponse = $this->actingAs($this->admin)->delete(route('admin.customer-groups.remove-customer', [$group, $this->customer]));
        $removeResponse->assertRedirect(route('admin.customer-groups.index'));
        $this->assertDatabaseMissing('customer_group_user', [
            'customer_group_id' => $group->id,
            'user_id'           => $this->customer->id,
        ]);
    }

    public function test_customer_group_discount_calculation_percentage()
    {
        $group = CustomerGroup::create([
            'name'           => 'VIP 15%',
            'code'           => 'vip-15',
            'discount_type'  => 'percentage',
            'discount_value' => 15,
            'min_spend'      => 2000,
            'is_active'      => true,
        ]);

        // Subtotal below min_spend
        $this->assertEquals(0, $group->calculateDiscount(1500));

        // Subtotal meets min_spend: 15% of 3000 = 450
        $this->assertEquals(450.0, $group->calculateDiscount(3000));
    }

    public function test_customer_group_discount_calculation_fixed()
    {
        $group = CustomerGroup::create([
            'name'           => 'Flat 500 Off',
            'code'           => 'flat-500',
            'discount_type'  => 'fixed',
            'discount_value' => 500,
            'min_spend'      => 2500,
            'is_active'      => true,
        ]);

        $this->assertEquals(0, $group->calculateDiscount(2000));
        $this->assertEquals(500.0, $group->calculateDiscount(3000));
    }

    public function test_api_check_customer_group_endpoint()
    {
        $group = CustomerGroup::create([
            'name'           => 'Diamond Tier',
            'code'           => 'diamond-tier',
            'discount_type'  => 'percentage',
            'discount_value' => 25,
            'min_spend'      => 4000,
            'is_active'      => true,
        ]);

        $this->customer->customerGroups()->attach($group->id);

        $response = $this->actingAs($this->customer)->postJson(route('api.customer-group.check'), [
            'subtotal' => 6000,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'has_group'      => true,
            'group_name'     => 'Diamond Tier',
            'discount_type'  => 'percentage',
            'discount_value' => 25,
            'discount'       => 1500, // 25% of 6000
            'qualifies'      => true,
        ]);
    }
}

