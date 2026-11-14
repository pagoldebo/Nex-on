<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Services\RbacService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminRoleController extends Controller
{
    protected RbacService $rbacService;

    public function __construct(RbacService $rbacService)
    {
        $this->rbacService = $rbacService;
    }

    /**
     * Display roles directory, permission matrix, and staff users.
     */
    public function index(): View
    {
        // Auto-seed system defaults if empty
        if (Role::count() === 0) {
            $this->rbacService->syncDefaults();
        }

        $roles = Role::with(['permissions', 'users'])->get();
        $permissionsByGroup = Permission::all()->groupBy('group_name');
        $staffUsers = User::where('role', 'admin')
            ->orWhereHas('roles')
            ->with('roles')
            ->get();

        return view('admin.roles.index', compact('roles', 'permissionsByGroup', 'staffUsers'));
    }

    /**
     * Store a new custom role.
     */
    public function store(Request $request): RedirectResponse
    {
        $actor = Auth::user();

        $validated = $request->validate([
            'name'        => 'required|string|max:50',
            'description' => 'nullable|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        // Privilege Escalation Guard: Non-super-admins cannot grant permissions they do not possess
        if (! $actor?->isSuperAdmin()) {
            $actorPermIds = $actor->roles()->with('permissions')->get()->pluck('permissions')->flatten()->pluck('id')->unique()->toArray();
            $requestedPermIds = array_map('intval', $validated['permissions'] ?? []);
            if (! empty(array_diff($requestedPermIds, $actorPermIds))) {
                abort(403, 'Forbidden. You cannot grant permissions that you do not hold.');
            }
        }

        $slug = Str::slug($validated['name']);
        if (Role::where('slug', $slug)->exists()) {
            $slug .= '-' . uniqid();
        }

        $role = Role::create([
            'name'        => $validated['name'],
            'slug'        => $slug,
            'description' => $validated['description'] ?? null,
            'is_system'   => false,
        ]);

        if (!empty($validated['permissions'])) {
            $role->permissions()->sync($validated['permissions']);
        }

        Log::info("Admin ID " . Auth::id() . " created custom role '{$role->name}' [ID: {$role->id}] with " . count($validated['permissions'] ?? []) . " permissions.");

        return redirect()->route('admin.roles.index')
            ->with('success', "Role '{$role->name}' created successfully!");
    }

    /**
     * Update role permissions and metadata.
     */
    public function update(Request $request, Role $role): RedirectResponse
    {
        $actor = Auth::user();

        // Privilege escalation guard: Only super-admin can modify super-admin role
        if ($role->slug === 'super-admin' && ! $actor?->isSuperAdmin()) {
            abort(403, 'Forbidden. Only a Super Administrator can modify the super-admin role.');
        }

        // Privilege escalation guard: Non-super-admins cannot edit system roles
        if ($role->is_system && ! $actor?->isSuperAdmin()) {
            abort(403, 'Forbidden. System roles can only be updated by a Super Administrator.');
        }

        // Privilege escalation guard: Actor cannot modify permissions of a role they are assigned to
        if (! $actor?->isSuperAdmin() && $role->users()->where('users.id', $actor?->id)->exists()) {
            abort(403, 'Forbidden. You cannot modify permissions of a role currently assigned to yourself.');
        }

        $validated = $request->validate([
            'name'        => 'required|string|max:50',
            'description' => 'nullable|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        // Privilege escalation guard: Non-super-admins cannot grant permissions they do not possess
        if (! $actor?->isSuperAdmin()) {
            $actorPermIds = $actor->roles()->with('permissions')->get()->pluck('permissions')->flatten()->pluck('id')->unique()->toArray();
            $requestedPermIds = array_map('intval', $validated['permissions'] ?? []);
            if (! empty(array_diff($requestedPermIds, $actorPermIds))) {
                abort(403, 'Forbidden. You cannot grant permissions that you do not hold.');
            }
        }

        if (!$role->is_system) {
            $role->update([
                'name'        => $validated['name'],
                'description' => $validated['description'] ?? null,
            ]);
        }

        $role->permissions()->sync($validated['permissions'] ?? []);

        Log::info("Admin ID " . Auth::id() . " updated role '{$role->name}' [ID: {$role->id}] permissions.");

        return redirect()->route('admin.roles.index')
            ->with('success', "Role '{$role->name}' updated successfully!");
    }

    /**
     * Assign a role to a staff user.
     */
    public function assignUser(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role_id' => 'required|exists:roles,id',
        ]);

        $actor = Auth::user();
        $user = User::findOrFail($validated['user_id']);
        $role = Role::findOrFail($validated['role_id']);

        // Privilege escalation guard: prevent self-assignment
        if ($actor?->id === $user->id) {
            abort(403, 'Forbidden. Self-assignment of roles is not permitted.');
        }

        // Privilege escalation guard: only a super-admin can assign the super-admin role
        if ($role->slug === 'super-admin' && ! $actor?->isSuperAdmin()) {
            abort(403, 'Forbidden. Only a Super Administrator can assign the super-admin role.');
        }

        // Privilege escalation guard: cannot assign a role that contains permissions the actor does not hold
        if (! $actor?->isSuperAdmin()) {
            $actorPermIds = $actor->roles()->with('permissions')->get()->pluck('permissions')->flatten()->pluck('id')->unique()->toArray();
            $rolePermIds = $role->permissions()->pluck('permissions.id')->toArray();
            if (! empty(array_diff($rolePermIds, $actorPermIds))) {
                abort(403, 'Forbidden. You cannot assign a role containing permissions you do not possess.');
            }
        }

        $user->roles()->syncWithoutDetaching([$role->id]);

        Log::info("Admin ID " . Auth::id() . " assigned role '{$role->name}' to User ID {$user->id}.");

        return back()->with('success', "Assigned role '{$role->name}' to user '{$user->name}'.");
    }

    /**
     * Remove custom role.
     */
    public function destroy(Role $role): RedirectResponse
    {
        if ($role->is_system) {
            return back()->with('error', "System role '{$role->name}' is protected and cannot be deleted.");
        }

        $role->delete();

        return redirect()->route('admin.roles.index')
            ->with('success', "Role deleted successfully.");
    }
}
