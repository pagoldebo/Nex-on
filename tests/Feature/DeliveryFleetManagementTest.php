<?php

namespace Tests\Feature;

use App\Models\DeliveryPersonnel;
use App\Models\Order;
use App\Models\Template;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeliveryFleetManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);

        Template::create([
            'key'         => 'saaya',
            'name'        => 'Saaya',
            'is_active'   => true,
            'description' => 'Default Theme',
        ]);
    }

    public function test_admin_can_view_fleet_management_screen()
    {
        $driver = DeliveryPersonnel::create([
            'name'           => 'Ravi Kumar',
            'phone'          => '9876543210',
            'email'          => 'ravi@example.com',
            'vehicle_type'   => 'bike',
            'vehicle_number' => 'KA 01 AB 1234',
            'status'         => 'active',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.fleet.index'));

        $response->assertStatus(200);
        $response->assertSee('Delivery Personnel &amp; Fleet Management', false);
        $response->assertSee('Ravi Kumar');
        $response->assertSee('KA 01 AB 1234');
    }

    public function test_admin_can_register_new_delivery_driver()
    {
        $payload = [
            'name'           => 'Mohan Das',
            'phone'          => '9988776655',
            'email'          => 'mohan@fleetexpress.in',
            'vehicle_type'   => 'van',
            'vehicle_number' => 'MH 12 CD 5678',
            'status'         => 'active',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.fleet.store'), $payload);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('delivery_personnel', [
            'name'           => 'Mohan Das',
            'phone'          => '9988776655',
            'vehicle_type'   => 'van',
            'vehicle_number' => 'MH 12 CD 5678',
            'status'         => 'active',
        ]);
    }

    public function test_admin_can_update_delivery_driver_profile()
    {
        $driver = DeliveryPersonnel::create([
            'name'           => 'Arjun Reddy',
            'phone'          => '9123456780',
            'vehicle_type'   => 'scooter',
            'vehicle_number' => 'TS 09 EE 4321',
            'status'         => 'active',
        ]);

        $updatePayload = [
            'name'           => 'Arjun Reddy K',
            'phone'          => '9123456780',
            'email'          => 'arjun.reddy@express.com',
            'vehicle_type'   => 'scooter',
            'vehicle_number' => 'TS 09 EE 9999',
            'status'         => 'inactive',
        ];

        $response = $this->actingAs($this->admin)->put(route('admin.fleet.update', $driver), $updatePayload);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $driver->refresh();
        $this->assertEquals('Arjun Reddy K', $driver->name);
        $this->assertEquals('TS 09 EE 9999', $driver->vehicle_number);
        $this->assertEquals('inactive', $driver->status);
    }

    public function test_admin_can_assign_order_to_driver()
    {
        $driver = DeliveryPersonnel::create([
            'name'           => 'Sunil Shetty',
            'phone'          => '9000011111',
            'vehicle_type'   => 'bike',
            'status'         => 'active',
        ]);

        $order = Order::create([
            'reference'         => 'ORD-FLEET-1001',
            'channel'           => 'online',
            'status'            => Order::STATUS_PROCESSING,
            'subtotal'          => 2499,
            'shipping_charge'   => 0,
            'cod_charge'        => 0,
            'discount'          => 0,
            'total'             => 2499,
            'payment_method'    => 'prepaid',
            'payment_status'    => Order::PAYMENT_PAID,
            'shipping_name'     => 'Ananya Panday',
            'shipping_phone'    => '9111122222',
            'shipping_email'    => 'ananya@example.com',
            'shipping_address1' => 'Bandra West',
            'shipping_city'     => 'Mumbai',
            'shipping_state'    => 'Maharashtra',
            'shipping_pincode'  => '400050',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.orders.assignDriver', $order), [
            'delivery_personnel_id' => $driver->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $order->refresh();
        $this->assertEquals($driver->id, $order->delivery_personnel_id);
        $this->assertEquals('assigned', $order->delivery_status);
    }

    public function test_driver_can_access_mobile_portal_and_view_assigned_deliveries()
    {
        $driver = DeliveryPersonnel::create([
            'name'           => 'Kiran Rao',
            'phone'          => '9777788888',
            'vehicle_type'   => 'bike',
            'status'         => 'active',
        ]);

        $order = Order::create([
            'reference'             => 'ORD-FLEET-2002',
            'channel'               => 'online',
            'status'                => Order::STATUS_PROCESSING,
            'subtotal'              => 3999,
            'shipping_charge'       => 0,
            'cod_charge'            => 0,
            'discount'              => 0,
            'total'                 => 3999,
            'payment_method'        => 'cod',
            'payment_status'        => Order::PAYMENT_PENDING,
            'shipping_name'         => 'Deepika S',
            'shipping_phone'        => '9444455555',
            'shipping_email'        => 'deepika@example.com',
            'shipping_address1'     => 'Indiranagar 100ft Rd',
            'shipping_city'         => 'Bengaluru',
            'shipping_state'        => 'Karnataka',
            'shipping_pincode'      => '560038',
            'delivery_personnel_id' => $driver->id,
            'delivery_status'       => 'assigned',
        ]);

        $response = $this->get(route('driver.portal', ['phone' => '9777788888']));

        $response->assertStatus(200);
        $response->assertSee('Kiran Rao');
        $response->assertSee('ORD-FLEET-2002');
        $response->assertSee('Deepika S');
        $response->assertSee('Cash on Delivery (COD)');
    }

    public function test_driver_can_transition_order_to_out_for_delivery_and_complete_with_proof()
    {
        $driver = DeliveryPersonnel::create([
            'name'           => 'Vikram Batra',
            'phone'          => '9555566666',
            'vehicle_type'   => 'scooter',
            'status'         => 'active',
        ]);

        $order = Order::create([
            'reference'             => 'ORD-FLEET-3003',
            'channel'               => 'online',
            'status'                => Order::STATUS_PROCESSING,
            'subtotal'              => 5500,
            'shipping_charge'       => 0,
            'cod_charge'            => 0,
            'discount'              => 0,
            'total'                 => 5500,
            'payment_method'        => 'razorpay',
            'payment_status'        => Order::PAYMENT_PAID,
            'shipping_name'         => 'Shreya Ghoshal',
            'shipping_phone'        => '9666677777',
            'shipping_email'        => 'shreya@example.com',
            'shipping_address1'     => 'Juhu Tara Rd',
            'shipping_city'         => 'Mumbai',
            'shipping_state'        => 'Maharashtra',
            'shipping_pincode'      => '400049',
            'delivery_personnel_id' => $driver->id,
            'delivery_status'       => 'assigned',
        ]);

        // Step 1: Driver marks out for delivery
        $startResponse = $this->withSession(['driver_phone' => $driver->phone])->post(route('driver.order.status', $order), [
            'action' => 'start',
        ]);

        $startResponse->assertRedirect();
        $startResponse->assertSessionHas('success');

        $order->refresh();
        $this->assertEquals('out_for_delivery', $order->delivery_status);
        $this->assertEquals(Order::STATUS_SHIPPED, $order->status);
        $this->assertNotNull($order->shipped_at);

        // Step 2: Driver completes delivery with proof
        $completeResponse = $this->withSession(['driver_phone' => $driver->phone])->post(route('driver.order.status', $order), [
            'action'          => 'complete',
            'proof_signature' => 'Shreya Ghoshal (Self)',
            'proof_notes'     => 'Received with security seal intact.',
        ]);

        $completeResponse->assertRedirect();
        $completeResponse->assertSessionHas('success');

        $order->refresh();
        $this->assertEquals('delivered', $order->delivery_status);
        $this->assertEquals(Order::STATUS_DELIVERED, $order->status);
        $this->assertNotNull($order->delivered_at);
        $this->assertEquals('Shreya Ghoshal (Self)', $order->proof_signature);
        $this->assertEquals('Received with security seal intact.', $order->proof_notes);
    }
}
