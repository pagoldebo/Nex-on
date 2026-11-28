<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use App\Services\RbacService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServerSidePermissionsSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed default system roles and permissions into test database
        app(RbacService::class)->syncDefaults();
    }

    protected function createSuperAdmin(): User
    {
        return User::create([
            'name'     => 'Root Administrator',
            'email'    => 'root@nexon.test',
            'phone'    => '9999900001',
            'password' => bcrypt('StrongPass123!'),
            'role'     => 'admin',
            'is_staff' => false,
        ]);
    }

    protected function createStaffWithRole(string $roleSlug, string $name = 'Staff Member'): User
    {
        $role = Role::where('slug', $roleSlug)->firstOrFail();

        $user = User::create([
            'name'              => $name,
            'email'             => "{$roleSlug}@nexon.test",
            'phone'             => '9999900002',
            'password'          => bcrypt('StrongPass123!'),
            'role'              => 'admin',
            'designation'       => $role->name,
            'is_staff'          => true,
            'employment_status' => 'active',
        ]);

        $user->roles()->attach($role->id);

        return $user;
    }

    public function test_super_admin_has_unrestricted_server_side_access_to_all_sections(): void
    {
        $superAdmin = $this->createSuperAdmin();

        $this->actingAs($superAdmin)->get(route('admin.dashboard'))->assertStatus(200);
        $this->actingAs($superAdmin)->get(route('admin.settings.index'))->assertStatus(200);
        $this->actingAs($superAdmin)->get(route('admin.roles.index'))->assertStatus(200);
        $this->actingAs($superAdmin)->get(route('admin.reports.index'))->assertStatus(200);
        $this->actingAs($superAdmin)->get(route('admin.inventory.index'))->assertStatus(200);
        $this->actingAs($superAdmin)->get(route('admin.pos.index'))->assertStatus(200);
        $this->actingAs($superAdmin)->get(route('admin.staff.index'))->assertStatus(200);
    }

    public function test_pos_cashier_can_access_pos_terminal_but_is_forbidden_from_settings_and_roles_and_reports(): void
    {
        $cashier = $this->createStaffWithRole('pos-cashier', 'Ramesh Cashier');

        // Cashier can access POS Terminal
        $posResponse = $this->actingAs($cashier)->get(route('admin.pos.index'));
        $posResponse->assertStatus(200);

        // Cashier cannot access Store Settings (403 Forbidden)
        $settingsResponse = $this->actingAs($cashier)->get(route('admin.settings.index'));
        $settingsResponse->assertStatus(403);

        // Cashier cannot access Roles Management (403 Forbidden)
        $rolesResponse = $this->actingAs($cashier)->get(route('admin.roles.index'));
        $rolesResponse->assertStatus(403);

        // Cashier cannot access Financial & Tax Reports (403 Forbidden)
        $reportsResponse = $this->actingAs($cashier)->get(route('admin.reports.index'));
        $reportsResponse->assertStatus(403);

        // Cashier cannot access Financial Expenses (403 Forbidden)
        $expenseResponse = $this->actingAs($cashier)->get(route('admin.finance.expenses'));
        $expenseResponse->assertStatus(403);

        // Cashier cannot access Staff Directory (403 Forbidden)
        $staffResponse = $this->actingAs($cashier)->get(route('admin.staff.index'));
        $staffResponse->assertStatus(403);
    }

    public function test_store_manager_can_control_store_items_sales_and_billing_but_is_forbidden_from_settings_and_roles(): void
    {
        $manager = $this->createStaffWithRole('store-manager', 'Vikram Store Manager');

        // Store Manager can access Products (Store Items)
        $this->actingAs($manager)->get(route('admin.products.index'))->assertStatus(200);
        $this->actingAs($manager)->get(route('admin.products.create'))->assertStatus(200);

        // Store Manager can access Orders & Billing
        $this->actingAs($manager)->get(route('admin.orders.index'))->assertStatus(200);

        // Store Manager can access POS billing terminal
        $this->actingAs($manager)->get(route('admin.pos.index'))->assertStatus(200);

        // Store Manager can access Inventory
        $this->actingAs($manager)->get(route('admin.inventory.index'))->assertStatus(200);

        // Store Manager CANNOT access general store settings (403 Forbidden)
        $this->actingAs($manager)->get(route('admin.settings.index'))->assertStatus(403);

        // Store Manager CANNOT access payment gateway credentials (403 Forbidden)
        $this->actingAs($manager)->get(route('admin.settings.gateways'))->assertStatus(403);

        // Store Manager CANNOT access SMS gateway credentials (403 Forbidden)
        $this->actingAs($manager)->get(route('admin.settings.sms'))->assertStatus(403);

        // Store Manager CANNOT access Roles & Permissions (403 Forbidden)
        $this->actingAs($manager)->get(route('admin.roles.index'))->assertStatus(403);

        // Store Manager CAN access Payroll (has payroll.manage permission)
        $this->actingAs($manager)->get(route('admin.hrm.payroll.overview'))->assertStatus(200);
    }

    public function test_inventory_clerk_can_access_inventory_but_is_forbidden_from_settings_and_roles(): void
    {
        $clerk = $this->createStaffWithRole('inventory-clerk', 'Suresh Inventory');

        // Clerk can access Inventory & Stock
        $invResponse = $this->actingAs($clerk)->get(route('admin.inventory.index'));
        $invResponse->assertStatus(200);

        // Clerk can view forecasting
        $forecastResponse = $this->actingAs($clerk)->get(route('admin.inventory.forecasting'));
        $forecastResponse->assertStatus(200);

        // Clerk cannot access Settings (403 Forbidden)
        $settingsResponse = $this->actingAs($clerk)->get(route('admin.settings.index'));
        $settingsResponse->assertStatus(403);

        // Clerk cannot access Roles Management (403 Forbidden)
        $rolesResponse = $this->actingAs($clerk)->get(route('admin.roles.index'));
        $rolesResponse->assertStatus(403);

        // Clerk cannot access POS terminal (403 Forbidden)
        $posResponse = $this->actingAs($clerk)->get(route('admin.pos.index'));
        $posResponse->assertStatus(403);
    }

    public function test_customer_support_can_access_orders_but_cannot_modify_products_or_access_settings(): void
    {
        $support = $this->createStaffWithRole('customer-support', 'Priya Support');

        // Support can view Orders
        $ordersResponse = $this->actingAs($support)->get(route('admin.orders.index'));
        $ordersResponse->assertStatus(200);

        // Support cannot create Products (requires products.manage)
        $productCreateResponse = $this->actingAs($support)->get(route('admin.products.create'));
        $productCreateResponse->assertStatus(403);

        // Support cannot access Settings
        $settingsResponse = $this->actingAs($support)->get(route('admin.settings.index'));
        $settingsResponse->assertStatus(403);

        // Support cannot access Roles
        $rolesResponse = $this->actingAs($support)->get(route('admin.roles.index'));
        $rolesResponse->assertStatus(403);
    }

    public function test_staff_cannot_elevate_privileges_by_assigning_roles_without_roles_manage_permission(): void
    {
        $cashier = $this->createStaffWithRole('pos-cashier', 'Mallory Cashier');
        $superAdminRole = Role::where('slug', 'super-admin')->firstOrFail();

        // Cashier tries to assign super-admin role to herself
        $hackAttempt = $this->actingAs($cashier)->post(route('admin.roles.assignUser'), [
            'user_id' => $cashier->id,
            'role_id' => $superAdminRole->id,
        ]);

        $hackAttempt->assertStatus(403);

        // Verify the cashier's roles are unchanged in the database
        $this->assertFalse($cashier->fresh()->roles->contains('slug', 'super-admin'));
    }

    public function test_staff_creation_via_admin_staff_controller_links_rbac_role_and_enforces_is_staff_constraint(): void
    {
        $superAdmin = $this->createSuperAdmin();

        $dept = Department::firstOrCreate(
            ['name' => 'Sales Counter'],
            ['code' => 'SALES-COUNTER', 'is_active' => true]
        );

        $response = $this->actingAs($superAdmin)->post(route('admin.staff.store'), [
            'name'              => 'Anil Counter',
            'email'             => 'anil.counter@nexon.test',
            'phone'             => '9876543210',
            'role_title'        => 'POS Cashier',
            'department_name'   => $dept->name,
            'employment_status' => 'active',
            'sales'             => 0,
            'password'          => 'SecurePass123!',
        ]);

        $response->assertRedirect(route('admin.staff.index'));

        $employee = User::where('email', 'anil.counter@nexon.test')->firstOrFail();
        $this->assertTrue((bool) $employee->is_staff);
        $this->assertFalse($employee->isSuperAdmin());
        $this->assertTrue($employee->hasRole('pos-cashier'));
        $this->assertTrue($employee->hasPermission('pos.access'));
        $this->assertFalse($employee->hasPermission('settings.manage'));

        // Employee can access POS
        $this->actingAs($employee)->get(route('admin.pos.index'))->assertStatus(200);

        // Employee is blocked from settings with 403 Forbidden
        $this->actingAs($employee)->get(route('admin.settings.index'))->assertStatus(403);
    }

    public function test_unauthenticated_guest_access_to_permission_guarded_route_redirects_to_login(): void
    {
        $response = $this->get(route('admin.settings.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_pure_customer_cannot_access_any_admin_route(): void
    {
        $customer = User::create([
            'name'     => 'Regular Customer',
            'email'    => 'shopper@nexon.test',
            'phone'    => '9111122222',
            'password' => bcrypt('CustomerPass123!'),
            'role'     => 'customer',
            'is_staff' => false,
        ]);

        $response = $this->actingAs($customer)->get(route('admin.dashboard'));
        $response->assertStatus(403);

        $settingsResponse = $this->actingAs($customer)->get(route('admin.settings.index'));
        $settingsResponse->assertStatus(403);
    }

    public function test_json_requests_receive_json_403_with_clear_error_payload(): void
    {
        $cashier = $this->createStaffWithRole('pos-cashier', 'API Tester');

        $jsonResponse = $this->actingAs($cashier)->getJson(route('admin.settings.index'));
        $jsonResponse->assertStatus(403);
        $jsonResponse->assertJsonStructure([
            'message',
            'required_permissions',
        ]);
    }

    public function test_non_super_admin_cannot_escalate_via_staff_creation_with_super_admin_role(): void
    {
        // Create an HR manager who only holds roles.manage
        $hrRole = Role::create([
            'name' => 'HR Staff Manager',
            'slug' => 'hr-staff-manager',
            'is_system' => false,
        ]);
        $hrRole->permissions()->attach(
            \App\Models\Permission::where('slug', 'roles.manage')->firstOrFail()->id
        );

        $hrUser = User::create([
            'name' => 'Delegated HR Officer',
            'email' => 'hr@nexon.test',
            'password' => bcrypt('Password123!'),
            'role' => 'staff',
            'is_staff' => true,
        ]);
        $hrUser->roles()->attach($hrRole->id);

        $dept = Department::firstOrCreate(['name' => 'Operations'], ['code' => 'OPS', 'is_active' => true]);

        // Attempt C-01 exploit: HR tries to create a Super Admin user
        $response = $this->actingAs($hrUser)->post(route('admin.staff.store'), [
            'name'              => 'Attacker Super Admin',
            'email'             => 'attacker.admin@evil.test',
            'phone'             => '9999999999',
            'role_title'        => 'Super Admin',
            'department_name'   => $dept->name,
            'employment_status' => 'active',
            'password'          => 'HackedPass123!',
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('users', ['email' => 'attacker.admin@evil.test']);
    }

    public function test_staff_created_has_staff_role_and_cannot_access_arbitrary_customer_invoices(): void
    {
        $superAdmin = $this->createSuperAdmin();
        $dept = Department::firstOrCreate(['name' => 'Support Desk'], ['code' => 'SUP', 'is_active' => true]);

        // Super-admin creates a POS cashier employee
        $this->actingAs($superAdmin)->post(route('admin.staff.store'), [
            'name'              => 'Junior Cashier',
            'email'             => 'junior.cashier@nexon.test',
            'phone'             => '9888877777',
            'role_title'        => 'POS Cashier',
            'department_name'   => $dept->name,
            'employment_status' => 'active',
            'password'          => 'CashierPass123!',
        ]);

        $staffUser = User::where('email', 'junior.cashier@nexon.test')->firstOrFail();

        // Verify H-01 fix: role is 'staff' and isAdmin() is false
        $this->assertEquals('staff', $staffUser->role);
        $this->assertTrue((bool) $staffUser->is_staff);
        $this->assertFalse($staffUser->isAdmin());
        $this->assertFalse($staffUser->isSuperAdmin());

        // Create a customer order
        $customer = User::create([
            'name' => 'John Customer',
            'email' => 'john.cust@test.com',
            'password' => bcrypt('CustPass123!'),
            'role' => 'customer',
        ]);
        $order = \App\Models\Order::create([
            'user_id' => $customer->id,
            'reference' => 'SAA-20261114-9999',
            'status' => 'pending',
            'subtotal' => 1000,
            'total' => 1000,
            'payment_method' => 'cod',
            'payment_status' => 'pending',
            'shipping_name' => 'John Customer',
            'shipping_phone' => '9888888888',
            'shipping_email' => 'john.cust@test.com',
            'shipping_address1' => '123 Test St',
            'shipping_city' => 'Mumbai',
            'shipping_state' => 'Maharashtra',
            'shipping_pincode' => '400001',
        ]);

        // Junior cashier cannot access customer's order invoice (H-01 check)
        $invoiceResponse = $this->actingAs($staffUser)->get(route('account.orders.invoice', $order));
        $invoiceResponse->assertStatus(403);
    }

    public function test_non_super_admin_cannot_change_another_staff_member_email_address(): void
    {
        $hrRole = Role::create([
            'name' => 'HR Staff Officer',
            'slug' => 'hr-staff-officer',
            'is_system' => false,
        ]);
        $hrRole->permissions()->attach(
            \App\Models\Permission::where('slug', 'roles.manage')->firstOrFail()->id
        );

        $hrUser = User::create([
            'name' => 'HR Manager',
            'email' => 'hr.mgr@nexon.test',
            'password' => bcrypt('Password123!'),
            'role' => 'staff',
            'is_staff' => true,
        ]);
        $hrUser->roles()->attach($hrRole->id);

        $victimStaff = User::create([
            'name' => 'Finance Clerk',
            'email' => 'finance.clerk@nexon.test',
            'password' => bcrypt('Password123!'),
            'role' => 'staff',
            'is_staff' => true,
            'department_name' => 'Finance',
            'designation' => 'Clerk',
            'employment_status' => 'active',
        ]);

        $dept = Department::firstOrCreate(['name' => 'Finance'], ['code' => 'FIN', 'is_active' => true]);

        // HR user attempts to change finance clerk's email to takeover account (M-03 exploit)
        $response = $this->actingAs($hrUser)->put(route('admin.staff.update', $victimStaff), [
            'name'              => $victimStaff->name,
            'email'             => 'attacker.clerk@evil.test',
            'role_title'        => 'Clerk',
            'department_name'   => $dept->name,
            'employment_status' => 'active',
        ]);

        $response->assertStatus(403);
        $this->assertEquals('finance.clerk@nexon.test', $victimStaff->fresh()->email);
    }

    public function test_unauthenticated_user_cannot_access_address_book_endpoints(): void
    {
        $responsePost = $this->post(route('account.addresses.store'), [
            'name'     => 'Eve Hacker',
            'phone'    => '9876543210',
            'address1' => '123 Fake Street',
            'city'     => 'Mumbai',
            'state'    => 'Maharashtra',
            'pincode'  => '400001',
        ]);
        $responsePost->assertRedirect(route('login'));

        $responseDelete = $this->delete(route('account.addresses.destroy', 9999));
        $responseDelete->assertRedirect(route('login'));
    }

    public function test_apply_coupon_enforces_user_usage_limit_on_client_check(): void
    {
        $user = User::create([
            'name'     => 'Coupon User',
            'email'    => 'coupon.user@nexon.test',
            'phone'    => '9988776655',
            'password' => bcrypt('Password123!'),
            'role'     => 'customer',
        ]);

        $coupon = \App\Models\Coupon::create([
            'code'       => 'ONCEPERUSER',
            'type'       => 'fixed',
            'value'      => 100,
            'is_active'  => true,
            'max_uses'   => 1,
            'starts_at'  => now()->subDay(),
            'expires_at' => now()->addDay(),
        ]);

        // Prior order using this coupon by this user
        \App\Models\Order::create([
            'reference'         => 'ORD-COUPON-USED-1',
            'user_id'           => $user->id,
            'subtotal'          => 1000,
            'shipping_charge'   => 0,
            'discount'          => 100,
            'coupon_code'       => 'ONCEPERUSER',
            'total'             => 900,
            'payment_method'    => 'cod',
            'status'            => \App\Models\Order::STATUS_DELIVERED,
            'payment_status'    => \App\Models\Order::PAYMENT_PAID,
            'shipping_name'     => $user->name,
            'shipping_email'    => $user->email,
            'shipping_phone'    => '9988776655',
            'shipping_address1' => '100 Test St',
            'shipping_city'     => 'Mumbai',
            'shipping_state'    => 'Maharashtra',
            'shipping_pincode'  => '400001',
        ]);

        $response = $this->actingAs($user)->postJson(route('coupon.apply'), [
            'code'     => 'ONCEPERUSER',
            'subtotal' => 1500,
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'message' => 'You have already redeemed this single-use coupon.',
        ]);
    }

    public function test_admin_review_creation_sanitizes_html_script_and_style_tags(): void
    {
        $superAdmin = $this->createSuperAdmin();
        $cat = \App\Models\Category::firstOrCreate(['slug' => 'test-cat'], ['name' => 'Test Cat']);
        $product = \App\Models\Product::firstOrCreate(
            ['slug' => 'review-test-prod'],
            [
                'category_id'    => $cat->id,
                'name'           => 'Review Test Product',
                'sku'            => 'REV-TEST-01',
                'starting_price' => 500,
                'is_active'      => true,
            ]
        );

        $response = $this->actingAs($superAdmin)->post(route('admin.reviews.store'), [
            'product_id'    => $product->id,
            'customer_name' => '<b>Bold Customer</b>',
            'city'          => '<script>alert("city")</script>Mumbai',
            'rating'        => 5,
            'title'         => 'Great <script>alert("xss")</script>Item',
            'comment'       => 'Wonderful quality <style>body{display:none}</style><b>Loved it!</b>',
            'is_approved'   => true,
        ]);

        $response->assertRedirect();

        $review = \App\Models\ProductReview::where('product_id', $product->id)->latest('id')->firstOrFail();
        $this->assertEquals('Bold Customer', $review->customer_name);
        $this->assertEquals('Mumbai', $review->city);
        $this->assertStringNotContainsString('<script>', $review->title);
        $this->assertStringNotContainsString('<style>', $review->comment);
        $this->assertStringContainsString('Loved it!', $review->comment);
    }

    public function test_deactivated_or_inactive_staff_is_immediately_blocked_from_admin(): void
    {
        $manager = $this->createStaffWithRole('store-manager', 'Inactive Manager');
        $this->actingAs($manager)->get(route('admin.dashboard'))->assertStatus(200);

        // Deactivate via deactivated_at
        $manager->forceFill(['deactivated_at' => now()])->save();
        $this->actingAs($manager)->get(route('admin.dashboard'))->assertRedirect(route('login'));

        // Re-activate deactivated_at, but mark employment_status inactive
        $manager->forceFill(['deactivated_at' => null, 'employment_status' => 'inactive'])->save();
        $this->actingAs($manager)->getJson(route('admin.dashboard'))->assertStatus(403);
    }
}
