<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminStaffController extends Controller
{
    /**
     * Display the Staff Management Directory (Staff List).
     */
    public function index(Request $request): View
    {
        $query = User::query()->where('is_staff', true);

        // Fallback if no specific staff records are flagged
        if ((clone $query)->count() === 0) {
            $query = User::query()->where(function ($q) {
                $q->whereIn('role', ['admin', 'vendor'])
                  ->orWhereHas('roles')
                  ->orWhereHas('departments');
            });
        }

        // Search query
        if ($request->filled('q')) {
            $search = trim($request->q);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('designation', 'like', "%{$search}%")
                  ->orWhere('department_name', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('employment_status', $request->status);
        }

        // Department filter
        if ($request->filled('department')) {
            $query->where('department_name', $request->department);
        }

        // Keep order consistent (seeded reference staff first, or newest)
        $employees = $query->with(['departments', 'roles'])->orderBy('id', 'asc')->get();
        $totalEmployees = $employees->count();

        $departments = Department::where('is_active', true)->get();
        $roles = Role::all();

        return view('admin.staff.index', compact(
            'employees',
            'totalEmployees',
            'departments',
            'roles'
        ));
    }

    /**
     * Store a newly created employee.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'              => ['required', 'string', 'max:255'],
            'email'             => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone'             => ['nullable', 'string', 'max:50'],
            'role_title'        => ['required', 'string', 'max:100'],
            'department_name'   => ['required', 'string', 'max:100'],
            'employment_status' => ['required', 'in:active,on_leave,inactive'],
            'sales'             => ['nullable', 'numeric', 'min:0'],
            'password'          => ['nullable', 'string', 'min:6'],
        ]);

        $password = !empty($validated['password']) ? $validated['password'] : Str::random(12);

        $employee = User::create([
            'name'              => $validated['name'],
            'email'             => $validated['email'],
            'phone'             => $validated['phone'] ?? null,
            'role'              => 'admin',
            'designation'       => $validated['role_title'],
            'department_name'   => $validated['department_name'],
            'employment_status' => $validated['employment_status'],
            'staff_sales'       => (float) ($validated['sales'] ?? 0),
            'is_staff'          => true,
            'password'          => Hash::make($password),
        ]);

        // Link department if available
        $dept = Department::where('name', $validated['department_name'])->first();
        if ($dept) {
            $employee->departments()->attach($dept->id, [
                'role_title' => $validated['role_title'],
                'is_primary' => true,
            ]);
        }

        return redirect()->route('admin.staff.index')
            ->with('success', "Employee '{$employee->name}' added successfully.");
    }

    /**
     * Return detailed data for an employee (JSON for View modal).
     */
    public function show(User $user): JsonResponse
    {
        $user->load(['departments', 'roles']);

        return response()->json([
            'success'           => true,
            'id'                => $user->id,
            'name'              => $user->name,
            'initials'          => $user->initials,
            'email'             => $user->email,
            'phone'             => $user->phone ?? '—',
            'role'              => $user->effective_role_name,
            'department'        => $user->effective_department_name,
            'employment_status' => $user->employment_status ?? 'active',
            'sales'             => number_format($user->total_sales_amount, 0),
            'joined'            => $user->joined_date_formatted,
            'created_at'        => $user->created_at ? $user->created_at->format('M d, Y') : 'N/A',
        ]);
    }

    /**
     * Update an existing employee.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name'              => ['required', 'string', 'max:255'],
            'email'             => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'phone'             => ['nullable', 'string', 'max:50'],
            'role_title'        => ['required', 'string', 'max:100'],
            'department_name'   => ['required', 'string', 'max:100'],
            'employment_status' => ['required', 'in:active,on_leave,inactive'],
            'sales'             => ['nullable', 'numeric', 'min:0'],
        ]);

        $user->update([
            'name'              => $validated['name'],
            'email'             => $validated['email'],
            'phone'             => $validated['phone'] ?? null,
            'designation'       => $validated['role_title'],
            'department_name'   => $validated['department_name'],
            'employment_status' => $validated['employment_status'],
            'staff_sales'       => (float) ($validated['sales'] ?? $user->staff_sales),
        ]);

        // Sync department
        $dept = Department::where('name', $validated['department_name'])->first();
        if ($dept) {
            $user->departments()->sync([
                $dept->id => [
                    'role_title' => $validated['role_title'],
                    'is_primary' => true,
                ],
            ]);
        }

        return redirect()->route('admin.staff.index')
            ->with('success', "Employee '{$user->name}' updated successfully.");
    }

    /**
     * Remove or deactivate an employee.
     */
    public function destroy(User $user): RedirectResponse
    {
        $name = $user->name;
        $user->update(['employment_status' => 'inactive', 'is_staff' => false]);

        return redirect()->route('admin.staff.index')
            ->with('success', "Employee '{$name}' deactivated successfully.");
    }

    /**
     * Handle bulk actions for selected employees.
     */
    public function bulkAction(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_ids' => ['required', 'array'],
            'employee_ids.*' => ['exists:users,id'],
            'action' => ['required', 'in:active,on_leave,inactive,delete'],
        ]);

        $ids = $validated['employee_ids'];
        $action = $validated['action'];

        if ($action === 'delete') {
            User::whereIn('id', $ids)->update(['employment_status' => 'inactive', 'is_staff' => false]);
            $msg = count($ids) . ' employees deactivated.';
        } else {
            User::whereIn('id', $ids)->update(['employment_status' => $action]);
            $msg = count($ids) . ' employees updated to ' . strtoupper(str_replace('_', ' ', $action)) . '.';
        }

        return redirect()->route('admin.staff.index')->with('success', $msg);
    }
}
