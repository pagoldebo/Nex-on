<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
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
        $actor = Auth::user();

        $validated = $request->validate([
            'name'              => ['required', 'string', 'max:255'],
            'email'             => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone'             => ['nullable', 'string', 'max:50'],
            'role_title'        => ['required', 'string', 'max:100'],
            'department_name'   => ['required', 'string', 'max:100'],
            'employment_status' => ['required', 'in:active,on_leave,inactive'],
            'sales'             => ['nullable', 'numeric', 'min:0'],
            'password'          => ['nullable', 'string', Password::defaults()],
        ]);

        // Resolve role and enforce privilege containment guards BEFORE creation
        $roleSlug = Str::slug($validated['role_title']);
        $role = Role::where('slug', $roleSlug)->orWhere('name', $validated['role_title'])->first();

        if ($role) {
            // Privilege escalation guard: Only super-admin can assign the super-admin role
            if ($role->slug === 'super-admin' && ! $actor?->isSuperAdmin()) {
                abort(403, 'Forbidden. Only a Super Administrator can assign the super-admin role.');
            }

            // Privilege escalation guard: Only super-admin can assign system roles
            if ($role->is_system && ! $actor?->isSuperAdmin()) {
                abort(403, 'Forbidden. Only a Super Administrator can assign system roles.');
            }

            // Privilege escalation guard: Cannot assign a role containing permissions the actor does not hold
            if (! $actor?->isSuperAdmin()) {
                $actorPermIds = $actor ? $actor->roles()->with('permissions')->get()->pluck('permissions')->flatten()->pluck('id')->unique()->toArray() : [];
                $rolePermIds = $role->permissions()->pluck('permissions.id')->toArray();
                if (! empty(array_diff($rolePermIds, $actorPermIds))) {
                    abort(403, 'Forbidden. You cannot assign a role containing permissions you do not possess.');
                }
            }
        }

        $password = !empty($validated['password']) ? $validated['password'] : Str::random(12);

        $employee = User::forceCreate([
            'name'              => $validated['name'],
            'email'             => $validated['email'],
            'phone'             => $validated['phone'] ?? null,
            'role'              => 'staff',
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

        // Associate matching role if defined in RBAC roles catalog
        if ($role) {
            $employee->roles()->syncWithoutDetaching([$role->id]);
        }

        \Illuminate\Support\Facades\Log::info("Admin ID " . ($actor?->id ?? 'system') . " created staff record for {$employee->email} with role " . ($role?->name ?? 'None') . " from IP " . $request->ip());

        return redirect()->route('admin.staff.index')
            ->with('success', "Employee '{$employee->name}' added successfully.");
    }

    /**
     * Return detailed data for an employee (JSON for View modal).
     */
    public function show(User $user): JsonResponse
    {
        if (! $user->is_staff) {
            abort(404, 'Employee record not found.');
        }

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
        if (! $user->is_staff) {
            abort(404, 'Employee record not found.');
        }

        $actor = Auth::user();

        // BOLA / Privilege Escalation Guard: Only super-admin can modify a super-admin account
        if ($user->isSuperAdmin() && ! $actor?->isSuperAdmin()) {
            abort(403, 'Unauthorized. Only super-administrators can modify super-admin accounts.');
        }

        // BOLA Guard: Non-super-admins cannot modify other admin accounts unless super-admin
        if ($user->isAdmin() && $user->id !== $actor?->id && ! $actor?->isSuperAdmin()) {
            abort(403, 'Unauthorized. Only super-administrators can modify administrator staff accounts.');
        }

        $validated = $request->validate([
            'name'              => ['required', 'string', 'max:255'],
            'email'             => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'phone'             => ['nullable', 'string', 'max:50'],
            'role_title'        => ['required', 'string', 'max:100'],
            'department_name'   => ['required', 'string', 'max:100'],
            'employment_status' => ['required', 'in:active,on_leave,inactive'],
            'sales'             => ['nullable', 'numeric', 'min:0'],
        ]);

        // Guard against email-based account takeover: non-super-admin cannot change another staff member's email (M-03)
        $emailChanged = strtolower($validated['email']) !== strtolower($user->email);
        if ($emailChanged && $user->id !== $actor?->id && ! $actor?->isSuperAdmin()) {
            abort(403, 'Unauthorized. Changing staff email addresses is restricted to super-administrators.');
        }

        $oldEmail = $user->email;

        $user->update([
            'name'              => $validated['name'],
            'email'             => $validated['email'],
            'phone'             => $validated['phone'] ?? null,
            'designation'       => $validated['role_title'],
            'department_name'   => $validated['department_name'],
            'employment_status' => $validated['employment_status'],
            'staff_sales'       => (float) ($validated['sales'] ?? $user->staff_sales),
        ]);

        // If email was modified, immediately purge any pending password reset tokens and sessions to prevent hijacking
        if ($emailChanged) {
            DB::table('password_reset_tokens')->whereIn('email', [$oldEmail, $validated['email']])->delete();
            DB::table('sessions')->where('user_id', $user->id)->delete();
        }

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

        // Sync matching role if defined in RBAC roles catalog
        $roleSlug = Str::slug($validated['role_title']);
        $role = Role::where('slug', $roleSlug)->orWhere('name', $validated['role_title'])->first();
        if ($role) {
            // Prevent assigning super-admin role unless actor is super-admin
            if ($role->slug === 'super-admin' && ! $actor?->isSuperAdmin()) {
                abort(403, 'Unauthorized. Only super-administrators can assign the super-admin role.');
            }

            // Prevent assigning system roles unless actor is super-admin
            if ($role->is_system && ! $actor?->isSuperAdmin()) {
                abort(403, 'Unauthorized. Only super-administrators can assign system roles.');
            }

            // Prevent granting permissions the actor lacks
            if (! $actor?->isSuperAdmin()) {
                $actorPermIds = $actor ? $actor->roles()->with('permissions')->get()->pluck('permissions')->flatten()->pluck('id')->unique()->toArray() : [];
                $rolePermIds = $role->permissions()->pluck('permissions.id')->toArray();
                if (! empty(array_diff($rolePermIds, $actorPermIds))) {
                    abort(403, 'Unauthorized. You cannot assign a role containing permissions you do not possess.');
                }
            }

            $user->roles()->sync([$role->id]);
        }

        \Illuminate\Support\Facades\Log::info("Admin ID " . ($actor?->id ?? 'system') . " updated staff record for User ID {$user->id} ({$user->email}) from IP " . $request->ip());

        return redirect()->route('admin.staff.index')
            ->with('success', "Employee '{$user->name}' updated successfully.");
    }

    /**
     * Remove or deactivate an employee.
     */
    public function destroy(User $user): RedirectResponse
    {
        if (! $user->is_staff) {
            abort(404, 'Employee record not found.');
        }

        $actor = Auth::user();

        // Prevent self-deactivation or super-admin deactivation
        if ($user->id === $actor?->id) {
            abort(403, 'Forbidden. You cannot deactivate your own administrative account.');
        }
        if ($user->isSuperAdmin()) {
            abort(403, 'Forbidden. Super-administrators cannot be deactivated.');
        }
        if ($user->isAdmin() && ! $actor?->isSuperAdmin()) {
            abort(403, 'Unauthorized. Only super-administrators can deactivate administrator staff accounts.');
        }

        $name = $user->name;
        $user->forceFill(['employment_status' => 'inactive', 'is_staff' => false])->save();

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

        $actor = Auth::user();
        $targetQuery = User::whereIn('id', $validated['employee_ids'])
            ->where('is_staff', true)
            ->where('id', '!=', Auth::id());

        // Protect super-admins from bulk alteration by non-super-admins
        if (! $actor?->isSuperAdmin()) {
            $targetQuery->where('role', '!=', 'admin');
        }

        $targets = $targetQuery->get();
        $ids = $targets->pluck('id')->all();
        $action = $validated['action'];

        if (empty($ids)) {
            return redirect()->route('admin.staff.index')->with('error', 'No eligible staff accounts found for this action.');
        }

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
