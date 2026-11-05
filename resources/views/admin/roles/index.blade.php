@extends('admin.layout')

@section('title', 'Role & Permission Management (Dynamic RBAC)')

@section('content')
<div class="space-y-6 pb-12" x-data="{ showCreateModal: false, editingRole: null }">

    <!-- Header & Action Bar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 text-[10px] font-bold rounded-md bg-indigo-50 text-indigo-600 border border-indigo-200/50 uppercase font-mono tracking-wider">Access Control</span>
                <span class="text-xs text-slate-400 font-medium">Phase 2 &bull; Dynamic Staff Authorization Matrix</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 mt-1">Role &amp; Permission Management</h1>
            <p class="text-xs text-slate-500 mt-0.5">Control granular staff access rights across POS cashier registers, inventory adjustments, financial reports, and settings.</p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('admin.staff.index') }}" 
               class="px-4 py-2 bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 text-xs font-semibold rounded-xl shadow-xs transition">
                Staff List
            </a>
            <button @click="showCreateModal = true" type="button"
                    class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-md shadow-indigo-600/20 flex items-center gap-2 transition self-start md:self-auto">
                <svg class="w-4 h-4 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Create Custom Role</span>
            </button>
        </div>
    </div>

    <!-- Navigation Pills Bar (List | Roles | Attendance) -->
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.staff.index') }}"
           class="px-4 py-1.5 rounded-full text-xs font-medium text-slate-600 hover:text-slate-900 bg-white border border-slate-200 hover:bg-slate-50 transition">
            List
        </a>
        <a href="{{ route('admin.roles.index') }}"
           class="px-4 py-1.5 rounded-full text-xs font-bold bg-indigo-600 text-white shadow-xs">
            Roles
        </a>
        <a href="{{ route('admin.staff.attendance') }}"
           class="px-4 py-1.5 rounded-full text-xs font-medium text-slate-600 hover:text-slate-900 bg-white border border-slate-200 hover:bg-slate-50 transition">
            Attendance
        </a>
    </div>

    <!-- Predefined & Custom Roles Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($roles as $role)
            <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs flex flex-col justify-between space-y-4">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-base font-bold text-slate-900">{{ $role->name }}</span>
                        @if($role->is_system)
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-slate-100 text-slate-600 uppercase font-mono">System</span>
                        @else
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-indigo-50 text-indigo-600 uppercase font-mono">Custom</span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-500 mt-1">{{ $role->description ?: 'No description provided.' }}</p>

                    <!-- Permissions tags -->
                    <div class="mt-4 pt-3 border-t border-slate-100">
                        <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2 font-mono">
                            Permissions ({{ $role->permissions->count() }})
                        </div>
                        <div class="flex flex-wrap gap-1 max-h-32 overflow-y-auto pr-1">
                            @forelse($role->permissions as $perm)
                                <span class="px-2 py-0.5 rounded-md bg-slate-50 border border-slate-200 text-slate-700 text-[10px] font-mono">
                                    {{ $perm->slug }}
                                </span>
                            @empty
                                <span class="text-xs text-slate-400 italic">No permissions assigned.</span>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Footer with Assigned Users & Actions -->
                <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="text-slate-400 font-mono text-[11px]">
                        <strong>{{ $role->users->count() }}</strong> staff assigned
                    </span>

                    <div class="flex items-center gap-2">
                        <button @click="editingRole = {{ json_encode([
                            'id' => $role->id,
                            'name' => $role->name,
                            'description' => $role->description,
                            'is_system' => $role->is_system,
                            'permissions' => $role->permissions->pluck('id')->toArray()
                        ]) }}"
                                type="button"
                                class="px-3 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold transition">
                            Edit Permissions
                        </button>

                        @if(!$role->is_system)
                            <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" onsubmit="return confirm('Delete this role?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-2.5 py-1 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 font-semibold transition">
                                    &times;
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Staff Assignment Console -->
    <div id="staff-assignment" class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs space-y-4">
        <h2 class="text-sm font-bold text-slate-900">Assign Role to Staff Member</h2>
        <p class="text-xs text-slate-500">Quickly attach an administrative role to any existing team user account.</p>

        <form method="POST" action="{{ route('admin.roles.assignUser') }}" class="flex flex-col sm:flex-row gap-3 text-xs max-w-2xl">
            @csrf
            <select name="user_id" required class="flex-1 px-3.5 py-2 rounded-xl border border-slate-200 text-slate-800 focus:outline-none focus:border-indigo-500">
                <option value="">-- Select Staff User --</option>
                @foreach($staffUsers as $usr)
                    <option value="{{ $usr->id }}">
                        {{ $usr->name }} ({{ $usr->email }}) &bull; Current: {{ $usr->roles->pluck('name')->implode(', ') ?: 'Admin' }}
                    </option>
                @endforeach
            </select>

            <select name="role_id" required class="w-full sm:w-56 px-3.5 py-2 rounded-xl border border-slate-200 text-slate-800 focus:outline-none focus:border-indigo-500">
                <option value="">-- Choose Role --</option>
                @foreach($roles as $r)
                    <option value="{{ $r->id }}">{{ $r->name }}</option>
                @endforeach
            </select>

            <button type="submit" class="px-5 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl font-bold transition whitespace-nowrap">
                Assign Role
            </button>
        </form>
    </div>

    <!-- Create Custom Role Modal -->
    <div x-show="showCreateModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="showCreateModal = false" class="bg-white rounded-3xl max-w-2xl w-full p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-base font-bold text-slate-900">Create New Custom Staff Role</h3>
                <button @click="showCreateModal = false" type="button" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
            </div>

            <form method="POST" action="{{ route('admin.roles.store') }}" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Role Name *</label>
                    <input type="text" name="name" required placeholder="e.g. Regional Merchandiser"
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-slate-900 focus:outline-none focus:border-indigo-500">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Description</label>
                    <input type="text" name="description" placeholder="Short description of role responsibility"
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-slate-900 focus:outline-none focus:border-indigo-500">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-2">Granted Permissions</label>
                    <div class="space-y-4 border border-slate-200 rounded-2xl p-4 bg-slate-50/50 max-h-72 overflow-y-auto">
                        @foreach($permissionsByGroup as $group => $perms)
                            <div>
                                <span class="font-mono font-bold uppercase text-[10px] text-indigo-600 tracking-wider">{{ $group }}</span>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-1.5">
                                    @foreach($perms as $p)
                                        <label class="flex items-center gap-2 p-2 rounded-lg bg-white border border-slate-200/80 cursor-pointer hover:bg-slate-50">
                                            <input type="checkbox" name="permissions[]" value="{{ $p->id }}" class="rounded text-indigo-600">
                                            <div>
                                                <div class="font-bold text-slate-800 text-[11px]">{{ $p->name }}</div>
                                                <div class="text-[9px] font-mono text-slate-400">{{ $p->slug }}</div>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                    <button @click="showCreateModal = false" type="button" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 font-semibold hover:bg-slate-200 transition">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 text-white font-bold hover:bg-indigo-700 transition">Create Role</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Role Modal -->
    <template x-if="editingRole">
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
            <div @click.away="editingRole = null" class="bg-white rounded-3xl max-w-2xl w-full p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-base font-bold text-slate-900">Edit Permissions: <span x-text="editingRole.name"></span></h3>
                    <button @click="editingRole = null" type="button" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
                </div>

                <form :action="'{{ url('admin/roles') }}/' + editingRole.id" method="POST" class="space-y-4 text-xs">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Role Name</label>
                        <input type="text" name="name" x-model="editingRole.name" :disabled="editingRole.is_system" required
                               class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-slate-900 focus:outline-none focus:border-indigo-500 disabled:bg-slate-100 disabled:text-slate-500">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Description</label>
                        <input type="text" name="description" x-model="editingRole.description" :disabled="editingRole.is_system"
                               class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-slate-900 focus:outline-none focus:border-indigo-500 disabled:bg-slate-100 disabled:text-slate-500">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-2">Granted Permissions</label>
                        <div class="space-y-4 border border-slate-200 rounded-2xl p-4 bg-slate-50/50 max-h-72 overflow-y-auto">
                            @foreach($permissionsByGroup as $group => $perms)
                                <div>
                                    <span class="font-mono font-bold uppercase text-[10px] text-indigo-600 tracking-wider">{{ $group }}</span>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-1.5">
                                        @foreach($perms as $p)
                                            <label class="flex items-center gap-2 p-2 rounded-lg bg-white border border-slate-200/80 cursor-pointer hover:bg-slate-50">
                                                <input type="checkbox" name="permissions[]" value="{{ $p->id }}"
                                                       :checked="editingRole.permissions.includes({{ $p->id }})"
                                                       class="rounded text-indigo-600">
                                                <div>
                                                    <div class="font-bold text-slate-800 text-[11px]">{{ $p->name }}</div>
                                                    <div class="text-[9px] font-mono text-slate-400">{{ $p->slug }}</div>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                        <button @click="editingRole = null" type="button" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 font-semibold hover:bg-slate-200 transition">Cancel</button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 text-white font-bold hover:bg-indigo-700 transition">Update Permissions</button>
                    </div>
                </form>
            </div>
        </div>
    </template>

</div>
@endsection
