<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Services\RbacService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolePermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function createAdmin(): User
    {
        return User::create([
            'name'     => 'Super Admin User',
            'email'    => 'admin@rbac.test',
            'phone'    => '9999999999',
            'password' => bcrypt('password'),
            'role'     => 'admin',
        ]);
    }

    public function test_roles_index_loads_and_seeds_defaults()
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get(route('admin.roles.index'));
        $response->assertStatus(200);
        $response->assertSee('Role &amp; Permission Management', false);
        $response->assertSee('Super Admin');
        $response->assertSee('Store Manager');
        $response->assertSee('eCommerce Manager');
        $response->assertSee('POS Cashier');
        $response->assertSee('Inventory Clerk');
        $response->assertSee('Customer Support');
    }

    public function test_create_custom_role_with_permissions()
    {
        $admin = $this->createAdmin();
        app(RbacService::class)->syncDefaults();

        $permIds = Permission::whereIn('slug', ['inventory.view', 'pos.access'])->pluck('id')->toArray();

        $response = $this->actingAs($admin)->post(route('admin.roles.store'), [
            'name'        => 'Store Supervisor',
            'description' => 'Supervises retail POS and view-only inventory',
            'permissions' => $permIds,
        ]);

        $response->assertRedirect(route('admin.roles.index'));

        $this->assertDatabaseHas('roles', [
            'name'      => 'Store Supervisor',
            'slug'      => 'store-supervisor',
            'is_system' => false,
        ]);

        $role = Role::where('slug', 'store-supervisor')->first();
        $this->assertTrue($role->hasPermission('inventory.view'));
        $this->assertTrue($role->hasPermission('pos.access'));
        $this->assertFalse($role->hasPermission('settings.manage'));
    }

    public function test_assign_role_to_staff_user_and_verify_permissions()
    {
        $admin = $this->createAdmin();
        app(RbacService::class)->syncDefaults();

        $staff = User::create([
            'name'     => 'Kunal Cashier',
            'email'    => 'kunal@store.test',
            'phone'    => '9888877777',
            'password' => bcrypt('password'),
            'role'     => 'customer',
        ]);

        $posRole = Role::where('slug', 'pos-cashier')->first();

        $response = $this->actingAs($admin)->post(route('admin.roles.assignUser'), [
            'user_id' => $staff->id,
            'role_id' => $posRole->id,
        ]);

        $response->assertRedirect();
        $this->assertTrue($staff->fresh()->hasRole('pos-cashier'));
        $this->assertTrue($staff->fresh()->hasPermission('pos.access'));
        $this->assertTrue($staff->fresh()->hasPermission('pos.checkout'));
        $this->assertFalse($staff->fresh()->hasPermission('inventory.manage'));
    }

    public function test_cannot_delete_system_roles()
    {
        $admin = $this->createAdmin();
        app(RbacService::class)->syncDefaults();

        $superAdminRole = Role::where('slug', 'super-admin')->first();

        $response = $this->actingAs($admin)->delete(route('admin.roles.destroy', $superAdminRole));
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('roles', [
            'slug' => 'super-admin',
        ]);
    }
}
