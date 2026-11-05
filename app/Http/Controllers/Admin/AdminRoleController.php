<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Services\RbacService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        $validated = $request->validate([
            'name'        => 'required|string|max:50',
            'description' => 'nullable|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

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

        return redirect()->route('admin.roles.index')
            ->with('success', "Role '{$role->name}' created successfully!");
    }

    /**
     * Update role permissions and metadata.
     */
    public function update(Request $request, Role $role): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:50',
            'description' => 'nullable|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        if (!$role->is_system) {
            $role->update([
                'name'        => $validated['name'],
                'description' => $validated['description'] ?? null,
            ]);
        }

        $role->permissions()->sync($validated['permissions'] ?? []);

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

        $user = User::findOrFail($validated['user_id']);
        $role = Role::findOrFail($validated['role_id']);

        $user->roles()->syncWithoutDetaching([$role->id]);

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
