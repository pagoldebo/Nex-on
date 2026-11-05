<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\StaffShift;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminDepartmentController extends Controller
{
    /**
     * Get list of staff users.
     */
    protected function getStaffMembers()
    {
        $staff = User::query()
            ->where(function ($q) {
                $q->whereIn('role', ['admin', 'vendor'])
                    ->orWhere('role', '!=', 'customer');
            })
            ->whereNull('deactivated_at')
            ->orderBy('name')
            ->get();

        if ($staff->isEmpty()) {
            $staff = User::whereNull('deactivated_at')->get();
        }

        return $staff;
    }

    /**
     * Job 24: Internal Departments & Shift Rostering Dashboard.
     */
    public function index(Request $request)
    {
        // 1. Departments Directory
        $departments = Department::with(['head', 'staff'])->withCount(['staff', 'shifts'])->get();

        // 2. Weekly Shift Roster
        $startOfWeek = $request->filled('week')
            ? Carbon::parse($request->week)->startOfWeek(Carbon::MONDAY)
            : Carbon::now()->startOfWeek(Carbon::MONDAY);

        $endOfWeek = $startOfWeek->copy()->endOfWeek(Carbon::SUNDAY);

        $daysOfWeek = [];
        for ($i = 0; $i < 7; $i++) {
            $daysOfWeek[] = $startOfWeek->copy()->addDays($i);
        }

        $shifts = StaffShift::with(['department', 'user'])
            ->whereBetween('shift_date', [$startOfWeek->toDateString(), $endOfWeek->toDateString()])
            ->get();

        // Detect Staffing Deficits (Days with zero scheduled staff in active departments)
        $deficits = [];
        foreach ($departments->where('is_active', true) as $dept) {
            foreach ($daysOfWeek as $day) {
                $count = $shifts->where('department_id', $dept->id)->where('shift_date', $day->toDateString())->count();
                if ($count === 0) {
                    $deficits[] = "{$dept->name} has 0 staff scheduled on {$day->format('l (d M)')}";
                }
            }
        }

        $staffMembers = $this->getStaffMembers();

        return view('admin.staff.departments.index', compact(
            'departments',
            'shifts',
            'startOfWeek',
            'endOfWeek',
            'daysOfWeek',
            'deficits',
            'staffMembers'
        ));
    }

    /**
     * Store new Department.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:100',
            'code'         => 'required|string|max:30|unique:departments,code',
            'head_user_id' => 'nullable|exists:users,id',
            'description'  => 'nullable|string',
            'color'        => 'required|string|max:30',
            'is_active'    => 'nullable|boolean',
        ]);

        $validated['is_active'] = (bool) ($request->input('is_active', 1));

        Department::create($validated);

        return redirect()->route('admin.staff.departments')
            ->with('success', "Department '{$validated['name']}' registered successfully.");
    }

    /**
     * Update Department.
     */
    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:100',
            'code'         => 'required|string|max:30|unique:departments,code,' . $department->id,
            'head_user_id' => 'nullable|exists:users,id',
            'description'  => 'nullable|string',
            'color'        => 'required|string|max:30',
            'is_active'    => 'nullable|boolean',
        ]);

        $validated['is_active'] = (bool) ($request->input('is_active', 1));

        $department->update($validated);

        return redirect()->route('admin.staff.departments')
            ->with('success', "Department '{$department->name}' updated successfully.");
    }

    /**
     * Assign Staff to Department.
     */
    public function assignStaff(Request $request, Department $department)
    {
        $validated = $request->validate([
            'user_ids'   => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'role_title' => 'nullable|string|max:100',
        ]);

        foreach ($validated['user_ids'] as $userId) {
            $department->staff()->syncWithoutDetaching([
                $userId => ['role_title' => $validated['role_title'] ?? 'Staff Associate'],
            ]);
        }

        return redirect()->back()->with('success', 'Staff members assigned to ' . $department->name . '.');
    }

    /**
     * Schedule a Staff Shift on Roster.
     */
    public function scheduleShift(Request $request)
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'user_id'       => 'required|exists:users,id',
            'shift_date'    => 'required|date',
            'shift_type'    => 'required|in:morning,evening,night,full_day',
            'start_time'    => 'nullable|date_format:H:i',
            'end_time'      => 'nullable|date_format:H:i',
            'notes'         => 'nullable|string|max:255',
        ]);

        // Default shift timings based on shift type
        $defaultTimings = [
            'morning'  => ['start' => '09:00', 'end' => '17:00'],
            'evening'  => ['start' => '13:00', 'end' => '21:00'],
            'night'    => ['start' => '21:00', 'end' => '06:00'],
            'full_day' => ['start' => '10:00', 'end' => '20:00'],
        ];

        $startTime = $validated['start_time'] ?? $defaultTimings[$validated['shift_type']]['start'];
        $endTime = $validated['end_time'] ?? $defaultTimings[$validated['shift_type']]['end'];

        // Overlapping Shift Conflict Detection
        $conflict = StaffShift::where('user_id', $validated['user_id'])
            ->whereDate('shift_date', $validated['shift_date'])
            ->where('status', 'scheduled')
            ->first();

        $warning = null;
        if ($conflict) {
            $warning = "Note: Staff member already had a {$conflict->shift_type} shift on this date.";
        }

        $shift = StaffShift::where('user_id', $validated['user_id'])
            ->whereDate('shift_date', $validated['shift_date'])
            ->first();

        if (!$shift) {
            $shift = new StaffShift([
                'user_id'    => $validated['user_id'],
                'shift_date' => $validated['shift_date'],
            ]);
        }

        $shift->fill([
            'department_id' => $validated['department_id'],
            'shift_type'    => $validated['shift_type'],
            'start_time'    => $startTime,
            'end_time'      => $endTime,
            'status'        => 'scheduled',
            'notes'         => $validated['notes'] ?? null,
            'created_by'    => Auth::id(),
        ]);
        $shift->save();

        $msg = 'Shift successfully rostered.';
        if ($warning) {
            $msg .= ' (' . $warning . ')';
        }

        return redirect()->back()->with('success', $msg);
    }

    /**
     * Delete / Cancel Shift.
     */
    public function deleteShift(StaffShift $shift)
    {
        $shift->delete();

        return redirect()->back()->with('success', 'Shift removed from weekly roster.');
    }
}
