<?php

namespace Tests\Feature;

use App\Models\CustomerAddress;
use App\Models\Order;
use App\Models\Product;
use App\Models\SupportTicket;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RowLevelSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $alice;
    protected User $bob;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name'     => 'Root Administrator',
            'email'    => 'admin@rls-test.test',
            'password' => bcrypt('StrongPass123!'),
            'role'     => 'admin',
            'is_staff' => false,
        ]);

        $this->alice = User::create([
            'name'     => 'Alice Patron',
            'email'    => 'alice@rls-test.test',
            'password' => bcrypt('StrongPass123!'),
            'role'     => 'customer',
            'is_staff' => false,
        ]);

        $this->bob = User::create([
            'name'     => 'Bob Patron',
            'email'    => 'bob@rls-test.test',
            'password' => bcrypt('StrongPass123!'),
            'role'     => 'customer',
            'is_staff' => false,
        ]);
    }

    public function test_customer_address_rls_restricts_queries_to_owner(): void
    {
        $aliceAddress = CustomerAddress::create([
            'user_id'    => $this->alice->id,
            'name'       => 'Alice Home',
            'phone'      => '9123456780',
            'address1'   => '123 Alice St',
            'city'       => 'Mumbai',
            'state'      => 'Maharashtra',
            'pincode'    => '400001',
            'type'       => 'home',
            'is_default' => true,
        ]);

        $bobAddress = CustomerAddress::create([
            'user_id'    => $this->bob->id,
            'name'       => 'Bob Office',
            'phone'      => '9123456781',
            'address1'   => '456 Bob Ave',
            'city'       => 'Bengaluru',
            'state'      => 'Karnataka',
            'pincode'    => '560001',
            'type'       => 'office',
            'is_default' => true,
        ]);

        // When Bob is authenticated, queries to CustomerAddress MUST NOT expose Alice's address
        $this->actingAs($this->bob);

        $bobResults = CustomerAddress::all();
        $this->assertTrue($bobResults->contains('id', $bobAddress->id));
        $this->assertFalse($bobResults->contains('id', $aliceAddress->id));
        $this->assertNull(CustomerAddress::find($aliceAddress->id));

        // When Alice is authenticated, queries MUST NOT expose Bob's address
        $this->actingAs($this->alice);

        $aliceResults = CustomerAddress::all();
        $this->assertTrue($aliceResults->contains('id', $aliceAddress->id));
        $this->assertFalse($aliceResults->contains('id', $bobAddress->id));
        $this->assertNull(CustomerAddress::find($bobAddress->id));
    }

    public function test_unauthenticated_guest_cannot_query_customer_addresses_via_rls(): void
    {
        CustomerAddress::create([
            'user_id'    => $this->alice->id,
            'name'       => 'Alice Secret Address',
            'phone'      => '9123456780',
            'address1'   => '123 Private St',
            'city'       => 'Mumbai',
            'state'      => 'Maharashtra',
            'pincode'    => '400001',
            'type'       => 'home',
            'is_default' => true,
        ]);

        // Unauthenticated guest query yields zero rows
        $this->assertEmpty(CustomerAddress::all());
    }

    public function test_order_rls_restricts_queries_to_order_owner(): void
    {
        $aliceOrder = Order::create([
            'user_id'         => $this->alice->id,
            'reference'       => 'ORD-ALICE-001',
            'channel'         => 'online',
            'status'          => Order::STATUS_PENDING,
            'subtotal'        => 1000,
            'shipping_charge' => 0,
            'cod_charge'      => 0,
            'discount'        => 0,
            'total'           => 1000,
            'payment_method'  => 'cod',
            'payment_status'  => 'pending',
            'shipping_name'   => 'Alice',
            'shipping_phone'  => '9123456780',
            'shipping_email'  => $this->alice->email,
            'shipping_address1' => '123 Alice St',
            'shipping_city'   => 'Mumbai',
            'shipping_state'  => 'Maharashtra',
            'shipping_pincode'=> '400001',
        ]);

        $bobOrder = Order::create([
            'user_id'         => $this->bob->id,
            'reference'       => 'ORD-BOB-001',
            'channel'         => 'online',
            'status'          => Order::STATUS_PENDING,
            'subtotal'        => 2000,
            'shipping_charge' => 0,
            'cod_charge'      => 0,
            'discount'        => 0,
            'total'           => 2000,
            'payment_method'  => 'cod',
            'payment_status'  => 'pending',
            'shipping_name'   => 'Bob',
            'shipping_phone'  => '9123456781',
            'shipping_email'  => $this->bob->email,
            'shipping_address1' => '456 Bob Ave',
            'shipping_city'   => 'Bengaluru',
            'shipping_state'  => 'Karnataka',
            'shipping_pincode'=> '560001',
        ]);

        // Alice cannot query or find Bob's order
        $this->actingAs($this->alice);
        $this->assertNull(Order::find($bobOrder->id));
        $this->assertTrue(Order::all()->contains('id', $aliceOrder->id));
        $this->assertFalse(Order::all()->contains('id', $bobOrder->id));

        // Bob cannot query or find Alice's order
        $this->actingAs($this->bob);
        $this->assertNull(Order::find($aliceOrder->id));
        $this->assertTrue(Order::all()->contains('id', $bobOrder->id));
        $this->assertFalse(Order::all()->contains('id', $aliceOrder->id));
    }

    public function test_support_ticket_rls_restricts_queries_to_ticket_creator(): void
    {
        $aliceTicket = SupportTicket::create([
            'user_id'       => $this->alice->id,
            'subject'       => 'Help with Alice Order',
            'category'      => 'order_inquiry',
            'priority'      => 'medium',
            'status'        => 'open',
        ]);

        $bobTicket = SupportTicket::create([
            'user_id'       => $this->bob->id,
            'subject'       => 'Help with Bob Order',
            'category'      => 'billing',
            'priority'      => 'high',
            'status'        => 'open',
        ]);

        // Bob cannot find or see Alice's support ticket
        $this->actingAs($this->bob);
        $this->assertNull(SupportTicket::find($aliceTicket->id));
        $this->assertTrue(SupportTicket::all()->contains('id', $bobTicket->id));
        $this->assertFalse(SupportTicket::all()->contains('id', $aliceTicket->id));
    }

    public function test_wishlist_rls_restricts_queries_to_owner(): void
    {
        $category = \App\Models\Category::create([
            'name'       => 'Ethnic Wear',
            'slug'       => 'ethnic-wear-' . uniqid(),
            'is_active'  => true,
            'sort_order' => 1,
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name'        => 'Silk Kurta',
            'slug'        => 'silk-kurta-' . uniqid(),
            'sku'         => 'SKU-' . uniqid(),
            'price'       => 1500,
            'is_active'   => true,
            'description' => 'Fine silk kurta',
        ]);

        $aliceWish = Wishlist::create([
            'user_id'    => $this->alice->id,
            'product_id' => $product->id,
        ]);

        $this->actingAs($this->bob);
        $this->assertEmpty(Wishlist::all());
        $this->assertNull(Wishlist::find($aliceWish->id));

        $this->actingAs($this->alice);
        $this->assertTrue(Wishlist::all()->contains('id', $aliceWish->id));
    }

    public function test_super_admin_bypasses_rls_and_queries_all_records(): void
    {
        $aliceAddress = CustomerAddress::create([
            'user_id'    => $this->alice->id,
            'name'       => 'Alice Home',
            'phone'      => '9123456780',
            'address1'   => '123 Alice St',
            'city'       => 'Mumbai',
            'state'      => 'Maharashtra',
            'pincode'    => '400001',
            'type'       => 'home',
            'is_default' => true,
        ]);

        $bobAddress = CustomerAddress::create([
            'user_id'    => $this->bob->id,
            'name'       => 'Bob Office',
            'phone'      => '9123456781',
            'address1'   => '456 Bob Ave',
            'city'       => 'Bengaluru',
            'state'      => 'Karnataka',
            'pincode'    => '560001',
            'type'       => 'office',
            'is_default' => true,
        ]);

        $this->actingAs($this->admin);

        $adminResults = CustomerAddress::all();
        $this->assertTrue($adminResults->contains('id', $aliceAddress->id));
        $this->assertTrue($adminResults->contains('id', $bobAddress->id));
        $this->assertNotNull(CustomerAddress::find($aliceAddress->id));
        $this->assertNotNull(CustomerAddress::find($bobAddress->id));
    }

    public function test_explicit_scope_bypass_without_row_level_security(): void
    {
        $aliceAddress = CustomerAddress::create([
            'user_id'    => $this->alice->id,
            'name'       => 'Alice Home',
            'phone'      => '9123456780',
            'address1'   => '123 Alice St',
            'city'       => 'Mumbai',
            'state'      => 'Maharashtra',
            'pincode'    => '400001',
            'type'       => 'home',
            'is_default' => true,
        ]);

        $this->actingAs($this->bob);

        // Standard query is blocked by RLS
        $this->assertNull(CustomerAddress::find($aliceAddress->id));

        // Trusted explicit bypass query succeeds
        $bypassed = CustomerAddress::withoutRowLevelSecurity()->find($aliceAddress->id);
        $this->assertNotNull($bypassed);
        $this->assertEquals($aliceAddress->id, $bypassed->id);
    }

    public function test_model_creation_automatically_attaches_authenticated_user_id(): void
    {
        $this->actingAs($this->alice);

        // Address created without passing user_id explicitly
        $address = CustomerAddress::create([
            'name'       => 'Auto User Address',
            'phone'      => '9123456789',
            'address1'   => '789 Auto Lane',
            'city'       => 'Pune',
            'state'      => 'Maharashtra',
            'pincode'    => '411001',
            'type'       => 'home',
            'is_default' => true,
        ]);

        $this->assertEquals($this->alice->id, $address->user_id);
    }
}
