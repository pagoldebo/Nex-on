<?php

namespace Tests\Feature;

use App\Models\CustomerAddress;
use App\Models\DeliveryPersonnel;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BrokenObjectLevelAuthorizationSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $customer1;
    protected User $customer2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name'     => 'System Administrator',
            'email'    => 'admin@bola-security.test',
            'phone'    => '9999900001',
            'password' => bcrypt('password123'),
            'role'     => 'admin',
        ]);

        $this->customer1 = User::create([
            'name'     => 'Customer Alice',
            'email'    => 'alice@bola-security.test',
            'phone'    => '9999900002',
            'password' => bcrypt('password123'),
            'role'     => 'customer',
        ]);

        $this->customer2 = User::create([
            'name'     => 'Customer Bob',
            'email'    => 'bob@bola-security.test',
            'phone'    => '9999900003',
            'password' => bcrypt('password123'),
            'role'     => 'customer',
        ]);
    }

    protected function createOrderForUser(User $user, string $ref = 'SAA-BOLA-001'): Order
    {
        return Order::create([
            'user_id'           => $user->id,
            'reference'         => $ref,
            'channel'           => 'online',
            'status'            => Order::STATUS_PENDING,
            'subtotal'          => 1999,
            'shipping_charge'   => 0,
            'cod_charge'        => 0,
            'discount'          => 0,
            'total'             => 1999,
            'payment_method'    => 'razorpay',
            'payment_status'    => Order::PAYMENT_PENDING,
            'shipping_name'     => $user->name,
            'shipping_phone'    => $user->phone,
            'shipping_email'    => $user->email,
            'shipping_address1' => 'MG Road',
            'shipping_city'     => 'Bengaluru',
            'shipping_state'    => 'Karnataka',
            'shipping_pincode'  => '560001',
        ]);
    }

    /**
     * Test unauthenticated guest cannot access admin dashboard or settings.
     */
    public function test_unauthenticated_guest_cannot_access_admin_panel(): void
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect(route('login'));

        $responseSettings = $this->get('/admin/settings');
        $responseSettings->assertRedirect(route('login'));
    }

    /**
     * Test regular customer role cannot access admin panel (receives 403 Forbidden).
     */
    public function test_customer_role_cannot_access_admin_panel(): void
    {
        $response = $this->actingAs($this->customer1)->get('/admin/dashboard');
        $response->assertStatus(403);

        $responseSettings = $this->actingAs($this->customer1)->get('/admin/settings');
        $responseSettings->assertStatus(403);
    }

    /**
     * Test admin role can access admin panel.
     */
    public function test_admin_role_can_access_admin_panel(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/dashboard');
        $response->assertStatus(200);

        $responseSettings = $this->actingAs($this->admin)->get('/admin/settings');
        $responseSettings->assertStatus(200);
    }

    /**
     * Test Customer A cannot access Customer B's invoice (BOLA prevention).
     */
    public function test_customer_cannot_access_other_users_invoice(): void
    {
        $orderOfBob = $this->createOrderForUser($this->customer2, 'SAA-BOB-INV01');

        // Alice tries to view Bob's invoice -> Blocked via RLS (404) or BOLA policy (403)
        $response = $this->actingAs($this->customer1)->get(route('account.orders.invoice', $orderOfBob->id));
        $this->assertTrue(in_array($response->getStatusCode(), [403, 404], true));
    }

    /**
     * Test Customer can access their own invoice.
     */
    public function test_customer_can_access_own_invoice(): void
    {
        $orderOfAlice = $this->createOrderForUser($this->customer1, 'SAA-ALICE-INV01');

        $response = $this->actingAs($this->customer1)->get(route('account.orders.invoice', $orderOfAlice->id));
        $response->assertStatus(200);
    }

    /**
     * Test anonymous user cannot view another customer's order confirmation PII (BOLA prevention).
     */
    public function test_anonymous_user_cannot_view_arbitrary_order_confirmation(): void
    {
        $order = $this->createOrderForUser($this->customer1, 'SAA-ALICE-CONF01');

        // Anonymous user visits /order-confirmation/{order} -> 403 Forbidden
        $response = $this->get(route('order.confirmation', $order->id));
        $response->assertStatus(403);
    }

    /**
     * Test order owner can view their order confirmation.
     */
    public function test_owner_can_view_order_confirmation(): void
    {
        $order = $this->createOrderForUser($this->customer1, 'SAA-ALICE-CONF02');

        $response = $this->actingAs($this->customer1)->get(route('order.confirmation', $order->id));
        $response->assertStatus(200);
    }

    /**
     * Test guest can view order confirmation if session token/credentials match recent order.
     */
    public function test_guest_with_session_token_can_view_own_confirmation(): void
    {
        $order = $this->createOrderForUser($this->customer1, 'SAA-GUEST-CONF01');

        $response = $this->withSession(['recent_order_id' => $order->id])
            ->get(route('order.confirmation', $order->id));

        $response->assertStatus(200);
    }

    /**
     * Test User A cannot cancel User B's order (BOLA prevention).
     */
    public function test_user_cannot_cancel_other_users_order(): void
    {
        $orderOfBob = $this->createOrderForUser($this->customer2, 'SAA-BOB-CANCEL01');

        // Alice tries to cancel Bob's order -> Blocked via RLS (404) or BOLA policy (403)
        $response = $this->actingAs($this->customer1)->postJson(route('orders.request', $orderOfBob->id), [
            'type'   => 'cancel',
            'reason' => 'Unauthorized cancellation attempt',
        ]);

        $this->assertTrue(in_array($response->getStatusCode(), [403, 404], true));
        $orderOfBob->refresh();
        $this->assertEquals(Order::STATUS_PENDING, $orderOfBob->status);
    }

    /**
     * Test anonymous user cannot cancel another user's order.
     */
    public function test_anonymous_user_cannot_cancel_order(): void
    {
        $order = $this->createOrderForUser($this->customer1, 'SAA-ALICE-CANCEL01');

        $response = $this->postJson(route('orders.request', $order->id), [
            'type'   => 'cancel',
            'reason' => 'Anonymous cancellation attempt',
        ]);

        $response->assertStatus(403);
        $order->refresh();
        $this->assertEquals(Order::STATUS_PENDING, $order->status);
    }

    /**
     * Test Customer A cannot delete Customer B's saved address.
     */
    public function test_customer_cannot_delete_other_users_address(): void
    {
        $bobsAddress = CustomerAddress::create([
            'user_id'    => $this->customer2->id,
            'name'       => 'Bob Home',
            'phone'      => '9999900003',
            'address1'   => 'Brigade Road',
            'city'       => 'Bengaluru',
            'state'      => 'Karnataka',
            'pincode'    => '560025',
            'is_default' => true,
        ]);

        // Alice tries to delete Bob's address -> Blocked via RLS (404) or BOLA policy (403)
        $response = $this->actingAs($this->customer1)->deleteJson(route('account.addresses.destroy', $bobsAddress->id));
        $this->assertTrue(in_array($response->getStatusCode(), [403, 404], true));

        $this->assertDatabaseHas('customer_addresses', ['id' => $bobsAddress->id]);
    }

    /**
     * Test Driver B cannot update Driver A's assigned order status.
     */
    public function test_driver_cannot_update_other_drivers_order_status(): void
    {
        $driver1 = DeliveryPersonnel::create([
            'name'      => 'Driver One',
            'phone'     => '9111111111',
            'is_active' => true,
        ]);

        $driver2 = DeliveryPersonnel::create([
            'name'      => 'Driver Two',
            'phone'     => '9222222222',
            'is_active' => true,
        ]);

        $order = $this->createOrderForUser($this->customer1, 'SAA-DELIVERY-001');
        $order->update([
            'delivery_personnel_id' => $driver1->id,
            'delivery_status'       => 'assigned',
        ]);

        // Driver 2 tries to update Driver 1's order -> 403 Forbidden
        $response = $this->withSession(['driver_phone' => $driver2->phone])
            ->post(route('driver.order.status', $order->id), [
                'action' => 'start',
            ]);

        $response->assertStatus(403);

        $order->refresh();
        $this->assertEquals('assigned', $order->delivery_status);

        // Driver 1 can update Driver 1's assigned order -> Success (302)
        $responseDriver1 = $this->withSession(['driver_phone' => $driver1->phone])
            ->post(route('driver.order.status', $order->id), [
                'action' => 'start',
            ]);

        $responseDriver1->assertStatus(302);
        $order->refresh();
        $this->assertEquals('out_for_delivery', $order->delivery_status);
    }
}

