<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\StaffAttendance;
use App\Models\StaffShift;
use App\Models\Template;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrganizationStaffTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $staff;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'email' => 'admin.org@nexon.luxury',
            'role'  => 'admin',
        ]);

        $this->staff = User::factory()->create([
            'name'  => 'Kavita Verma',
            'email' => 'kavita.verma@nexon.luxury',
            'role'  => 'vendor',
        ]);

        Template::create(['key' => 'buttercup', 'name' => 'Buttercup', 'is_active' => true]);
    }

    public function test_departments_directory_and_weekly_roster_renders(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.staff.departments'));

        $response->assertStatus(200);
        $response->assertSee('Internal Departments &amp; Shift Rostering', false);
        $response->assertSee('Retail Store Sales');
        $response->assertSee('Weekly Shift Schedule Roster');
    }

    public function test_admin_can_create_new_department(): void
    {
        $payload = [
            'name'        => 'VIP Bespoke Atelier',
            'code'        => 'VIP-ATELIER',
            'head_user_id'=> $this->admin->id,
            'description' => 'Luxury couture customizations.',
            'color'       => 'rose',
            'is_active'   => 1,
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.staff.departments.store'), $payload);

        $response->assertRedirect(route('admin.staff.departments'));
        $this->assertDatabaseHas('departments', [
            'name' => 'VIP Bespoke Atelier',
            'code' => 'VIP-ATELIER',
        ]);
    }

    public function test_admin_can_assign_staff_to_department(): void
    {
        $dept = Department::create([
            'name'      => 'Warehouse & Logistics',
            'code'      => 'WH-LOG',
            'color'     => 'blue',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.staff.departments.assign', $dept), [
            'user_ids'   => [$this->staff->id],
            'role_title' => 'Senior Picker',
        ]);

        $response->assertRedirect();
        $this->assertTrue($dept->staff->contains($this->staff->id));
        $this->assertEquals('Senior Picker', $dept->staff->first()->pivot->role_title);
    }

    public function test_admin_can_schedule_and_delete_weekly_shift(): void
    {
        $dept = Department::create([
            'name'      => 'Retail Sales',
            'code'      => 'RETAIL',
            'color'     => 'indigo',
            'is_active' => true,
        ]);

        $shiftDate = Carbon::today()->toDateString();

        // 1. Schedule Shift
        $scheduleResponse = $this->actingAs($this->admin)->post(route('admin.staff.departments.shifts'), [
            'department_id' => $dept->id,
            'user_id'       => $this->staff->id,
            'shift_date'    => $shiftDate,
            'shift_type'    => 'morning',
            'notes'         => 'Lead cashier terminal 1',
        ]);

        $scheduleResponse->assertSessionHasNoErrors();
        $scheduleResponse->assertRedirect();
        $shift = StaffShift::where('user_id', $this->staff->id)->first();
        $this->assertNotNull($shift);
        $this->assertEquals('morning', $shift->shift_type);
        $this->assertEquals('09:00', substr($shift->start_time, 0, 5));
        $this->assertEquals('17:00', substr($shift->end_time, 0, 5));

        // 2. Delete Shift
        $deleteResponse = $this->actingAs($this->admin)->delete(route('admin.staff.departments.shifts.destroy', $shift));
        $deleteResponse->assertRedirect();
        $this->assertDatabaseMissing('staff_shifts', ['id' => $shift->id]);
    }

    public function test_attendance_console_renders_with_daily_kpis(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.staff.attendance'));

        $response->assertStatus(200);
        $response->assertSee('Staff Attendance &amp; Digital Clock-In', false);
        $response->assertSee('Quick Digital Punch Terminal');
        $response->assertSee('Present Today');
    }

    public function test_digital_punch_clock_in_and_clock_out_calculates_hours_and_overtime(): void
    {
        // 1. Clock In
        $clockInResponse = $this->actingAs($this->admin)->post(route('admin.staff.attendance.punch'), [
            'user_id'       => $this->staff->id,
            'action'        => 'clock_in',
            'location_name' => 'Boutique Flagship Floor',
            'device_name'   => 'POS Terminal 1',
        ]);

        $clockInResponse->assertRedirect();
        $attendance = StaffAttendance::where('user_id', $this->staff->id)->whereDate('date', Carbon::today())->first();
        $this->assertNotNull($attendance);
        $this->assertNotNull($attendance->clock_in);
        $this->assertNull($attendance->clock_out);

        // Manually adjust clock_in to 9 hours ago to test clock_out calculation
        $attendance->clock_in = Carbon::now()->subHours(9);
        $attendance->save();

        // 2. Clock Out
        $clockOutResponse = $this->actingAs($this->admin)->post(route('admin.staff.attendance.punch'), [
            'user_id' => $this->staff->id,
            'action'  => 'clock_out',
        ]);
        $clockOutResponse->assertRedirect();
        $fresh = $attendance->fresh();
        $this->assertNotNull($fresh->clock_out);
        $this->assertGreaterThanOrEqual(8.9, (float) $fresh->total_hours);
        $this->assertGreaterThan(0, (float) $fresh->overtime_hours);
    }

    public function test_manager_can_adjust_attendance_with_audit_note(): void
    {
        $attendance = StaffAttendance::create([
            'user_id'             => $this->staff->id,
            'date'                => Carbon::today()->toDateString(),
            'clock_in'            => Carbon::today()->setTime(9, 0),
            'status'              => 'present',
            'verification_status' => 'verified',
        ]);

        $adjustPayload = [
            'clock_in'     => Carbon::today()->setTime(9, 0)->format('Y-m-d\TH:i'),
            'clock_out'    => Carbon::today()->setTime(18, 0)->format('Y-m-d\TH:i'),
            'status'       => 'present',
            'manager_note' => 'Staff forgot to punch out before closing gate.',
        ];

        $response = $this->actingAs($this->admin)->put(route('admin.staff.attendance.adjust', $attendance), $adjustPayload);

        $response->assertRedirect();
        $fresh = $attendance->fresh();
        $this->assertEquals(9.00, (float) $fresh->total_hours);
        $this->assertEquals(1.00, (float) $fresh->overtime_hours);
        $this->assertEquals('manual_adjustment', $fresh->verification_status);
        $this->assertEquals($this->admin->id, $fresh->adjusted_by);
        $this->assertEquals('Staff forgot to punch out before closing gate.', $fresh->manager_note);
    }

    public function test_export_timesheet_csv(): void
    {
        StaffAttendance::create([
            'user_id'             => $this->staff->id,
            'date'                => Carbon::today()->toDateString(),
            'clock_in'            => Carbon::today()->setTime(9, 0),
            'clock_out'           => Carbon::today()->setTime(17, 30),
            'total_hours'         => 8.5,
            'overtime_hours'      => 0.5,
            'status'              => 'present',
            'verification_status' => 'verified',
        ]);

        $month = Carbon::today()->format('Y-m');
        $response = $this->actingAs($this->admin)->get(route('admin.staff.attendance.export', ['month' => $month]));

        $response->assertStatus(200);
        $this->assertStringContainsString('text/csv', $response->headers->get('content-type'));
        $this->assertStringContainsString("staff_timesheet_{$month}.csv", $response->headers->get('content-disposition'));
    }
}
