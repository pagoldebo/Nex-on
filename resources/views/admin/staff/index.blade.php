{{--
    ======================================================================
    ADMIN MODULE: STAFF INDEX
    ======================================================================
    File: resources/views/admin/staff/index.blade.php
    Responsibility: Back-office administration for STAFF INDEX
    Features:
      - Standard administration UI following the Nex-on design guidelines
      - Accessible controls, Tailwind CSS styling, and reactive Alpine.js components
    Security & Access Control:
      - Guarded by Laravel authentication and RBAC permission middleware
    Operations & Maintenance:
      - Integrates with master administration shell (@extends('admin.layout'))
--}}
@extends('admin.layout')

@section('title', 'Staff Management')

@section('content')
<div class="space-y-6 pb-12" 
     x-data="{
        addModalOpen: false,
        editModalOpen: false,
        viewModalOpen: false,
        selected: [],
        allIds: {{ json_encode($employees->pluck('id')) }},
        currentEdit: {
            id: null,
            name: '',
            email: '',
            phone: '',
            role_title: '',
            department_name: '',
            sales: 0,
            employment_status: 'active'
        },
        currentView: {
            id: null,
            name: '',
            initials: '',
            email: '',
            phone: '',
            role: '',
            department: '',
            sales: '0',
            joined: '',
            status: 'ACTIVE'
        },
        toggleSelectAll(event) {
            if (event.target.checked) {
                this.selected = [...this.allIds];
            } else {
                this.selected = [];
            }
        },
        openEditModal(employee) {
            this.currentEdit = Object.assign({}, employee);
            this.editModalOpen = true;
        },
        openViewModal(employee) {
            this.currentView = Object.assign({}, employee);
            this.viewModalOpen = true;
        }
     }">

    <!-- Page Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Staff Management</h1>
            <p class="text-xs text-slate-500 mt-1 font-medium">{{ $totalEmployees }} employees</p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('admin.roles.index') }}" 
               class="px-4 py-2 bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 text-xs font-semibold rounded-xl shadow-xs transition">
                Manage Roles
            </a>
            <button type="button" 
                    @click="addModalOpen = true"
                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-md shadow-indigo-600/20 transition flex items-center gap-1.5">
                <svg class="w-4 h-4 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Add Employee</span>
            </button>
        </div>
    </div>

    <!-- Navigation Pills Bar (List | Roles | Attendance) -->
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.staff.index') }}"
           class="px-4 py-1.5 rounded-full text-xs font-bold bg-indigo-600 text-white shadow-xs">
            List
        </a>
        <a href="{{ route('admin.roles.index') }}"
           class="px-4 py-1.5 rounded-full text-xs font-medium text-slate-600 hover:text-slate-900 bg-white border border-slate-200 hover:bg-slate-50 transition">
            Roles
        </a>
        <a href="{{ route('admin.staff.attendance') }}"
           class="px-4 py-1.5 rounded-full text-xs font-medium text-slate-600 hover:text-slate-900 bg-white border border-slate-200 hover:bg-slate-50 transition">
            Attendance
        </a>
    </div>

    <!-- Flash Notifications -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold space-y-1 shadow-xs">
            @foreach($errors->all() as $err)
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    <span>{{ $err }}</span>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Floating Bulk Actions Toolbar -->
    <div x-show="selected.length > 0" 
         x-cloak
         x-transition
         class="bg-slate-900 text-white rounded-2xl p-3 px-5 shadow-xl flex items-center justify-between text-xs font-medium">
        <div class="flex items-center gap-2">
            <span class="w-6 h-6 rounded-full bg-indigo-600 text-white text-[11px] font-bold flex items-center justify-center" x-text="selected.length"></span>
            <span>employees selected</span>
        </div>
        <form method="POST" action="{{ route('admin.staff.bulk') }}" class="flex items-center gap-2">
            @csrf
            <template x-for="id in selected" :key="id">
                <input type="hidden" name="employee_ids[]" :value="id">
            </template>
            <button type="submit" name="action" value="active" class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold transition text-[11px]">
                Mark Active
            </button>
            <button type="submit" name="action" value="on_leave" class="px-3 py-1.5 rounded-lg bg-amber-600 hover:bg-amber-700 text-white font-bold transition text-[11px]">
                Mark On Leave
            </button>
            <button type="submit" name="action" value="inactive" class="px-3 py-1.5 rounded-lg bg-slate-700 hover:bg-slate-600 text-white font-bold transition text-[11px]">
                Deactivate
            </button>
            <button type="button" @click="selected = []" class="px-2.5 py-1.5 text-slate-400 hover:text-white transition text-[11px]">
                Cancel
            </button>
        </form>
    </div>

    <!-- Staff Directory Data Table Container -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto" tabindex="0">
            <table class="min-w-full divide-y divide-slate-100 text-left text-xs">
                <thead>
                    <tr class="bg-white text-slate-400 font-bold uppercase text-[10px] tracking-wider border-b border-slate-100">
                        <th scope="col" class="w-10 px-4 py-4">
                            <input type="checkbox" 
                                   @change="toggleSelectAll($event)" 
                                   :checked="selected.length === {{ $employees->count() }} && {{ $employees->count() }} > 0"
                                   class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                        </th>
                        <th scope="col" class="px-4 py-4">EMPLOYEE</th>
                        <th scope="col" class="px-4 py-4">ROLE</th>
                        <th scope="col" class="px-4 py-4">DEPARTMENT</th>
                        <th scope="col" class="px-4 py-4">EMAIL</th>
                        <th scope="col" class="px-4 py-4">PHONE</th>
                        <th scope="col" class="px-4 py-4">SALES</th>
                        <th scope="col" class="px-4 py-4">JOINED</th>
                        <th scope="col" class="px-4 py-4">STATUS</th>
                        <th scope="col" class="px-4 py-4 text-right">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($employees as $emp)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <!-- Checkbox -->
                            <td class="w-10 px-4 py-3.5">
                                <input type="checkbox" 
                                       :value="{{ $emp->id }}" 
                                       x-model="selected"
                                       class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                            </td>

                            <!-- Employee (Avatar with Initials + Name) -->
                            <td class="px-4 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-indigo-600 text-white font-bold text-xs flex items-center justify-center shrink-0 shadow-xs uppercase tracking-tight select-none">
                                        {{ $emp->initials }}
                                    </div>
                                    <span class="font-semibold text-slate-900 text-xs whitespace-nowrap">
                                        {{ $emp->name }}
                                    </span>
                                </div>
                            </td>

                            <!-- Role -->
                            <td class="px-4 py-3.5 text-slate-700 text-xs whitespace-nowrap font-normal">
                                {{ $emp->effective_role_name }}
                            </td>

                            <!-- Department -->
                            <td class="px-4 py-3.5 text-slate-600 text-xs whitespace-nowrap font-normal">
                                {{ $emp->effective_department_name }}
                            </td>

                            <!-- Email -->
                            <td class="px-4 py-3.5 text-slate-600 text-xs whitespace-nowrap font-normal">
                                {{ $emp->email }}
                            </td>

                            <!-- Phone -->
                            <td class="px-4 py-3.5 text-slate-600 text-xs whitespace-nowrap font-normal">
                                {{ $emp->phone ?? '—' }}
                            </td>

                            <!-- Sales -->
                            <td class="px-4 py-3.5 font-bold text-slate-900 text-xs whitespace-nowrap font-mono">
                                ${{ number_format($emp->total_sales_amount, 0) }}
                            </td>

                            <!-- Joined -->
                            <td class="px-4 py-3.5 text-slate-500 text-xs whitespace-nowrap font-normal">
                                {{ $emp->joined_date_formatted }}
                            </td>

                            <!-- Status Badge -->
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                @php
                                    $st = strtolower($emp->employment_status ?? 'active');
                                @endphp
                                @if($st === 'active')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold tracking-wider uppercase bg-emerald-50 text-emerald-600 border border-emerald-200/60">
                                        ACTIVE
                                    </span>
                                @elseif($st === 'on_leave')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold tracking-wider uppercase bg-amber-50 text-amber-700 border border-amber-200/60">
                                        ON LEAVE
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold tracking-wider uppercase bg-slate-100 text-slate-600 border border-slate-200/60">
                                        INACTIVE
                                    </span>
                                @endif
                            </td>

                            <!-- Actions (View, Edit) -->
                            <td class="px-4 py-3.5 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <button type="button" 
                                            @click="openViewModal({
                                                id: {{ $emp->id }},
                                                name: '{{ addslashes($emp->name) }}',
                                                initials: '{{ $emp->initials }}',
                                                email: '{{ addslashes($emp->email) }}',
                                                phone: '{{ addslashes($emp->phone ?? '—') }}',
                                                role: '{{ addslashes($emp->effective_role_name) }}',
                                                department: '{{ addslashes($emp->effective_department_name) }}',
                                                sales: '{{ number_format($emp->total_sales_amount, 0) }}',
                                                joined: '{{ $emp->joined_date_formatted }}',
                                                status: '{{ strtoupper(str_replace('_', ' ', $emp->employment_status ?? 'active')) }}'
                                            })"
                                            class="px-2.5 py-1 rounded-lg border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-medium transition shadow-2xs">
                                        View
                                    </button>

                                    <button type="button" 
                                            @click="openEditModal({
                                                id: {{ $emp->id }},
                                                name: '{{ addslashes($emp->name) }}',
                                                email: '{{ addslashes($emp->email) }}',
                                                phone: '{{ addslashes($emp->phone ?? '') }}',
                                                role_title: '{{ addslashes($emp->effective_role_name) }}',
                                                department_name: '{{ addslashes($emp->effective_department_name) }}',
                                                sales: {{ (float) $emp->total_sales_amount }},
                                                employment_status: '{{ $emp->employment_status ?? 'active' }}'
                                            })"
                                            class="px-2.5 py-1 rounded-lg border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-medium transition shadow-2xs">
                                        Edit
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-12 text-center text-slate-500">
                                <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                </div>
                                <h3 class="text-sm font-bold text-slate-800">No employees found</h3>
                                <p class="text-xs text-slate-400 mt-1">Get started by clicking the "Add Employee" button above.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ==================== ADD EMPLOYEE MODAL ==================== -->
    <div x-show="addModalOpen" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="addModalOpen = false" 
             x-show="addModalOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl space-y-4">
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Add New Employee</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Register staff member into the store directory</p>
                </div>
                <button type="button" @click="addModalOpen = false" class="text-slate-400 hover:text-slate-600 text-xl font-light leading-none">&times;</button>
            </div>

            <form method="POST" action="{{ route('admin.staff.store') }}" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Full Name *</label>
                    <input type="text" name="name" required placeholder="e.g. Robert Hayes"
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-slate-900 focus:outline-none focus:border-indigo-500">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Email Address *</label>
                        <input type="email" name="email" required placeholder="e.g. r.hayes@store.com"
                               class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-slate-900 focus:outline-none focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Phone Number</label>
                        <input type="text" name="phone" placeholder="e.g. +1 555-1001"
                               class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-slate-900 focus:outline-none focus:border-indigo-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Role Title *</label>
                        <input type="text" name="role_title" required placeholder="e.g. Warehouse Manager" list="roles-list"
                               class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-slate-900 focus:outline-none focus:border-indigo-500">
                        <datalist id="roles-list">
                            @foreach($roles as $r)
                                <option value="{{ $r->name }}"></option>
                            @endforeach
                            <option value="Warehouse Manager"></option>
                            <option value="Store Manager"></option>
                            <option value="eCommerce Manager"></option>
                            <option value="Sales Associate"></option>
                            <option value="Cashier"></option>
                            <option value="Inventory Analyst"></option>
                            <option value="Customer Support"></option>
                        </datalist>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Department *</label>
                        <select name="department_name" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-slate-900 focus:outline-none focus:border-indigo-500">
                            @foreach($departments as $d)
                                <option value="{{ $d->name }}">{{ $d->name }}</option>
                            @endforeach
                            <option value="Operations">Operations</option>
                            <option value="Management">Management</option>
                            <option value="Sales">Sales</option>
                            <option value="POS">POS</option>
                            <option value="Support">Support</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Sales Volume ($)</label>
                        <input type="number" step="0.01" name="sales" value="0" placeholder="0"
                               class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-slate-900 focus:outline-none focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Employment Status *</label>
                        <select name="employment_status" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-slate-900 focus:outline-none focus:border-indigo-500">
                            <option value="active">Active</option>
                            <option value="on_leave">On Leave</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Temporary Password</label>
                    <input type="password" name="password" placeholder="Leave blank to generate automatically"
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-slate-900 focus:outline-none focus:border-indigo-500">
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                    <button type="button" @click="addModalOpen = false" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold transition">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold shadow-md shadow-indigo-600/20 transition">
                        Add Employee
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ==================== EDIT EMPLOYEE MODAL ==================== -->
    <div x-show="editModalOpen" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="editModalOpen = false" 
             x-show="editModalOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl space-y-4">
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Edit Employee</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Modify employee profile &amp; department assignments</p>
                </div>
                <button type="button" @click="editModalOpen = false" class="text-slate-400 hover:text-slate-600 text-xl font-light leading-none">&times;</button>
            </div>

            <form method="POST" :action="'{{ url('admin/staff') }}/' + currentEdit.id" class="space-y-4 text-xs">
                @csrf
                @method('PUT')
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Full Name *</label>
                    <input type="text" name="name" x-model="currentEdit.name" required
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-slate-900 focus:outline-none focus:border-indigo-500">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Email Address *</label>
                        <input type="email" name="email" x-model="currentEdit.email" required
                               class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-slate-900 focus:outline-none focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Phone Number</label>
                        <input type="text" name="phone" x-model="currentEdit.phone"
                               class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-slate-900 focus:outline-none focus:border-indigo-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Role Title *</label>
                        <input type="text" name="role_title" x-model="currentEdit.role_title" required
                               class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-slate-900 focus:outline-none focus:border-indigo-500">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Department *</label>
                        <input type="text" name="department_name" x-model="currentEdit.department_name" required
                               class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-slate-900 focus:outline-none focus:border-indigo-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Sales Volume ($)</label>
                        <input type="number" step="0.01" name="sales" x-model="currentEdit.sales"
                               class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-slate-900 focus:outline-none focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Employment Status *</label>
                        <select name="employment_status" x-model="currentEdit.employment_status" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-slate-900 focus:outline-none focus:border-indigo-500">
                            <option value="active">Active</option>
                            <option value="on_leave">On Leave</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                    <button type="button" @click="editModalOpen = false" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold transition">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold shadow-md shadow-indigo-600/20 transition">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ==================== VIEW EMPLOYEE PROFILE MODAL ==================== -->
    <div x-show="viewModalOpen" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="viewModalOpen = false" 
             x-show="viewModalOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-5 text-left">
            
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-full bg-indigo-600 text-white font-extrabold text-base flex items-center justify-center shadow-md shadow-indigo-600/20 select-none"
                         x-text="currentView.initials">
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900" x-text="currentView.name"></h3>
                        <p class="text-xs text-slate-500 font-medium" x-text="currentView.role"></p>
                    </div>
                </div>
                <button type="button" @click="viewModalOpen = false" class="text-slate-400 hover:text-slate-600 text-xl font-light leading-none">&times;</button>
            </div>

            <!-- Details List -->
            <div class="space-y-3 bg-slate-50/80 rounded-2xl p-4 border border-slate-100 text-xs">
                <div class="flex items-center justify-between">
                    <span class="text-slate-400 font-medium">Department</span>
                    <span class="font-bold text-slate-800" x-text="currentView.department"></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-400 font-medium">Email</span>
                    <span class="font-mono text-slate-700" x-text="currentView.email"></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-400 font-medium">Phone</span>
                    <span class="font-mono text-slate-700" x-text="currentView.phone"></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-400 font-medium">Total Sales</span>
                    <span class="font-bold text-slate-900 font-mono" x-text="'$' + currentView.sales"></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-400 font-medium">Joined Date</span>
                    <span class="text-slate-700" x-text="currentView.joined"></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-400 font-medium">Current Status</span>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold tracking-wider uppercase bg-emerald-50 text-emerald-600 border border-emerald-200"
                          x-text="currentView.status"></span>
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" @click="viewModalOpen = false" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition">
                    Close
                </button>
            </div>
        </div>
    </div>

</div>
@endsection

