<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\StaffAttendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminAttendanceController extends Controller
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
     * Job 25: Staff Attendance & Digital Clock-In Console.
     */
    public function index(Request $request)
    {
        $selectedDate = $request->input('date', date('Y-m-d'));

        $query = StaffAttendance::with(['user', 'department', 'adjuster'])->latest('clock_in');

        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $attendances = $query->paginate(20)->withQueryString();

        // Daily KPI Metrics for selected date
        $totalStaff = User::whereIn('role', ['admin', 'vendor'])->orWhere('role', '!=', 'customer')->count() ?: 1;
        $presentToday = StaffAttendance::whereDate('date', $selectedDate)->whereIn('status', ['present', 'late', 'half_day'])->count();
        $lateToday = StaffAttendance::whereDate('date', $selectedDate)->where('status', 'late')->count();
        $totalHoursToday = (float) StaffAttendance::whereDate('date', $selectedDate)->sum('total_hours');
        $attendanceRate = round(($presentToday / max(1, $totalStaff)) * 100);

        $departments = Department::where('is_active', true)->get();
        $staffMembers = $this->getStaffMembers();

        return view('admin.staff.attendance.index', compact(
            'attendances',
            'selectedDate',
            'presentToday',
            'lateToday',
            'totalHoursToday',
            'attendanceRate',
            'departments',
            'staffMembers'
        ));
    }

    /**
     * Digital Time Punch Clock (Clock In / Clock Out).
     */
    public function punch(Request $request)
    {
        $validated = $request->validate([
            'user_id'       => 'required|exists:users,id',
            'action'        => 'required|in:clock_in,clock_out',
            'department_id' => 'nullable|exists:departments,id',
            'location_name' => 'nullable|string|max:150',
            'device_name'   => 'nullable|string|max:100',
        ]);

        $today = Carbon::today()->toDateString();
        $now = Carbon::now();

        $attendance = StaffAttendance::where('user_id', $validated['user_id'])
            ->whereDate('date', $today)
            ->first();

        if (!$attendance) {
            $attendance = new StaffAttendance([
                'user_id' => $validated['user_id'],
                'date'    => $today,
            ]);
        }

        if ($validated['action'] === 'clock_in') {
            if ($attendance->clock_in) {
                return redirect()->back()->with('error', 'Staff member has already clocked in for today.');
            }

            $attendance->clock_in = $now;
            $attendance->department_id = $validated['department_id'] ?? $attendance->department_id;
            $attendance->ip_address = $request->ip();
            $attendance->device_name = $validated['device_name'] ?? 'POS Terminal 1';
            $attendance->location_name = $validated['location_name'] ?? 'Boutique Flagship Store';

            // Check if punch is after 09:30 AM => flag as Late
            $attendance->status = $now->format('H:i') > '09:30' ? 'late' : 'present';
            $attendance->verification_status = 'verified';
            $attendance->save();

            return redirect()->back()->with('success', "Clock In recorded at {$now->format('h:i A')} for {$attendance->user->name}.");
        } else {
            // Clock Out
            if (!$attendance->clock_in) {
                return redirect()->back()->with('error', 'Cannot clock out before clocking in.');
            }

            $attendance->clock_out = $now;
            $attendance->computeHours();
            $attendance->save();

            return redirect()->back()->with('success', "Clock Out recorded at {$now->format('h:i A')}. Total time: {$attendance->total_hours} hrs.");
        }
    }

    /**
     * Manual Manager Adjustment Override.
     */
    public function adjust(Request $request, StaffAttendance $attendance)
    {
        $validated = $request->validate([
            'clock_in'     => 'required|date_format:Y-m-d\TH:i',
            'clock_out'    => 'nullable|date_format:Y-m-d\TH:i|after_or_equal:clock_in',
            'status'       => 'required|in:present,late,half_day,absent,on_leave',
            'manager_note' => 'required|string|max:500',
        ]);

        $attendance->clock_in = Carbon::parse($validated['clock_in']);
        $attendance->clock_out = !empty($validated['clock_out']) ? Carbon::parse($validated['clock_out']) : null;
        $attendance->status = $validated['status'];
        $attendance->manager_note = $validated['manager_note'];
        $attendance->adjusted_by = Auth::id();
        $attendance->verification_status = 'manual_adjustment';

        if ($attendance->clock_out) {
            $attendance->computeHours();
        }

        $attendance->save();

        return redirect()->back()->with('success', 'Attendance record adjusted successfully with manager audit note.');
    }

    /**
     * Export Timesheet CSV for 1-Click Payroll Ingestion.
     */
    public function export(Request $request): StreamedResponse
    {
        $month = (string) $request->input('month', date('Y-m'));
        if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
            $month = date('Y-m');
        }

        $attendances = StaffAttendance::with(['user', 'department'])
            ->where('date', 'LIKE', "{$month}%")
            ->orderBy('date')
            ->get();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"staff_timesheet_{$month}.csv\"",
        ];

        return response()->stream(function () use ($attendances, $month) {
            $handle = fopen('php://output', 'w');

            // Header row
            \App\Services\CsvExporter::writeRow($handle, [
                'Employee Name',
                'Email',
                'Department',
                'Date',
                'Clock In',
                'Clock Out',
                'Total Hours',
                'Overtime Hours',
                'Status',
                'Verification',
                'IP Address',
            ]);

            foreach ($attendances as $row) {
                \App\Services\CsvExporter::writeRow($handle, [
                    $row->user?->name ?? 'Staff',
                    $row->user?->email ?? '',
                    $row->department?->name ?? 'Store Operations',
                    $row->date?->toDateString(),
                    $row->clock_in ? $row->clock_in->format('H:i:s') : '-',
                    $row->clock_out ? $row->clock_out->format('H:i:s') : '-',
                    $row->total_hours,
                    $row->overtime_hours,
                    strtoupper($row->status),
                    $row->verification_status,
                    $row->ip_address,
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }
}
