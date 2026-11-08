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
}
