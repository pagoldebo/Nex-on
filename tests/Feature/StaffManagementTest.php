<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\StaffReferenceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaffManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->seed(StaffReferenceSeeder::class);
    }

    public function test_staff_management_index_renders_with_seeded_employees(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.staff.index'));

        $response->assertStatus(200);
        $response->assertSee('Staff Management');
        $response->assertSee('employees');
        $response->assertSee('Manage Roles');
        $response->assertSee('Add Employee');

        // Check reference employees from image
        $response->assertSee('Robert Hayes');
        $response->assertSee('Warehouse Manager');
        $response->assertSee('r.hayes@store.com');
        $response->assertSee('+1 555-1001');

        $response->assertSee('Tyler Brooks');
        $response->assertSee('28,400');

        $response->assertSee('Alicia Gomez');
        $response->assertSee('ON LEAVE');
        $response->assertSee('ACTIVE');
    }

    public function test_admin_can_add_new_employee(): void
    {
        $payload = [
            'name'              => 'Marcus Vance',
            'email'             => 'm.vance@store.com',
            'phone'             => '+1 555-1009',
            'role_title'        => 'Logistics Dispatcher',
            'department_name'   => 'Operations',
            'employment_status' => 'active',
            'sales'             => 5200,
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.staff.store'), $payload);

        $response->assertRedirect(route('admin.staff.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'name'              => 'Marcus Vance',
            'email'             => 'm.vance@store.com',
            'phone'             => '+1 555-1009',
            'designation'       => 'Logistics Dispatcher',
            'department_name'   => 'Operations',
            'employment_status' => 'active',
            'is_staff'          => true,
        ]);
    }

    public function test_admin_can_fetch_employee_details_json(): void
    {
        $employee = User::where('email', 'r.hayes@store.com')->first();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.staff.show', $employee));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'name'    => 'Robert Hayes',
            'email'   => 'r.hayes@store.com',
            'role'    => 'Warehouse Manager',
        ]);
    }

    public function test_admin_can_update_employee(): void
    {
        $employee = User::where('email', 'r.hayes@store.com')->first();

        $payload = [
            'name'              => 'Robert Hayes Updated',
            'email'             => 'r.hayes@store.com',
            'phone'             => '+1 555-9999',
            'role_title'        => 'Senior Warehouse Director',
            'department_name'   => 'Operations',
            'employment_status' => 'active',
            'sales'             => 12000,
        ];

        $response = $this->actingAs($this->admin)
            ->put(route('admin.staff.update', $employee), $payload);

        $response->assertRedirect(route('admin.staff.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id'          => $employee->id,
            'name'        => 'Robert Hayes Updated',
            'phone'       => '+1 555-9999',
            'designation' => 'Senior Warehouse Director',
        ]);
    }

    public function test_admin_can_perform_bulk_actions_on_employees(): void
    {
        $ids = User::where('is_staff', true)->pluck('id')->take(2)->toArray();

        $response = $this->actingAs($this->admin)
            ->post(route('admin.staff.bulk'), [
                'employee_ids' => $ids,
                'action'       => 'on_leave',
            ]);

        $response->assertRedirect(route('admin.staff.index'));
        $response->assertSessionHas('success');

        foreach ($ids as $id) {
            $this->assertDatabaseHas('users', [
                'id'                => $id,
                'employment_status' => 'on_leave',
            ]);
        }
    }

    public function test_admin_can_deactivate_employee(): void
    {
        $employee = User::where('email', 'a.gomez@store.com')->first();

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.staff.destroy', $employee));

        $response->assertRedirect(route('admin.staff.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id'                => $employee->id,
            'employment_status' => 'inactive',
            'is_staff'          => false,
        ]);
    }
}

