@extends('admin.layout')

@section('content')
<div class="space-y-6" x-data="{
    showNewDeptModal: false,
    showShiftModal: false,
    showAssignModal: false,
    selectedDeptId: null,
    selectedDeptName: '',
    openAssign(deptId, deptName) {
        this.selectedDeptId = deptId;
        this.selectedDeptName = deptName;
        this.showAssignModal = true;
    }
}">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-indigo-600 uppercase tracking-wider mb-1">
                <span>Organization</span>
                <span>•</span>
                <span class="text-slate-500 font-medium">Domain 7: Staff &amp; Operations</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Internal Departments &amp; Shift Rostering</h1>
            <p class="text-sm text-slate-500 mt-0.5">Manage store departmental registries, allocate staff associates, and schedule weekly shift rotations.</p>
        </div>
        <div class="flex items-center gap-2.5 flex-wrap">
            <button @click="showNewDeptModal = true" type="button"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition-colors">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                + New Department
            </button>
            <button @click="showShiftModal = true" type="button"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                + Schedule Shift
            </button>
        </div>
    </div>

    <!-- Deficit Warnings Alert (if any active department lacks coverage) -->
    @if(!empty($deficits))
        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4">
            <div class="flex items-center gap-2 text-amber-900 font-bold text-xs mb-1">
                <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                Staffing Coverage Deficit Alert ({{ count($deficits) }} Uncovered Department Days)
            </div>
            <p class="text-[11px] text-amber-700 mb-2">The following operational days currently have 0 rostered personnel scheduled:</p>
            <div class="flex flex-wrap gap-1.5">
                @foreach(array_slice($deficits, 0, 5) as $def)
                    <span class="inline-flex items-center px-2 py-0.5 bg-white border border-amber-300 rounded text-[10px] font-semibold text-amber-800">
                        {{ $def }}
                    </span>
                @endforeach
                @if(count($deficits) > 5)
                    <span class="text-[10px] text-amber-700 font-bold self-center">+{{ count($deficits) - 5 }} more days</span>
                @endif
            </div>
        </div>
    @endif

    <!-- Departments Grid -->
    <div>
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Active Departments Directory</h3>
            <span class="text-xs text-slate-400">{{ $departments->count() }} Functional Units</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($departments as $dept)
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
                    <div>
                        <div class="flex items-start justify-between mb-2">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $dept->color_badge['bg'] }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $dept->color_badge['dot'] }}"></span>
                                {{ $dept->code }}
                            </span>
                            <span class="text-[10px] text-slate-400 font-mono">{{ $dept->staff_count }} Enrolled Staff</span>
                        </div>

                        <h4 class="text-base font-bold text-slate-900 mb-1">{{ $dept->name }}</h4>
                        <p class="text-xs text-slate-500 line-clamp-2 mb-3">{{ $dept->description ?? 'No department description provided.' }}</p>

                        <div class="text-xs text-slate-600 bg-slate-50 p-2.5 rounded-xl border border-slate-100 space-y-1">
                            <div class="flex justify-between">
                                <span class="text-slate-400">Head of Dept:</span>
                                <span class="font-bold text-slate-800">{{ $dept->head?->name ?? 'Unassigned' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-400">Weekly Shifts:</span>
                                <span class="font-semibold text-indigo-600">{{ $dept->shifts_count }} Rostered</span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex justify-between items-center mt-4">
                        <button type="button" @click="openAssign({{ $dept->id }}, '{{ addslashes($dept->name) }}')"
                                class="text-xs font-bold text-indigo-600 hover:text-indigo-800">
                            + Assign Personnel
                        </button>
                        <span class="text-[10px] font-semibold text-slate-400">
                            {{ $dept->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Weekly Shift Roster Matrix -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <h3 class="text-base font-bold text-slate-900">Weekly Shift Schedule Roster</h3>
                <p class="text-xs text-slate-400">Week of {{ $startOfWeek->format('d M Y') }} — {{ $endOfWeek->format('d M Y') }}</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.staff.departments', ['week' => $startOfWeek->copy()->subWeek()->toDateString()]) }}" 
                   class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition">
                    ← Prev Week
                </a>
                <a href="{{ route('admin.staff.departments') }}" 
                   class="px-3 py-1.5 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 rounded-xl text-xs font-bold transition">
                    Current Week
                </a>
                <a href="{{ route('admin.staff.departments', ['week' => $startOfWeek->copy()->addWeek()->toDateString()]) }}" 
                   class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition">
                    Next Week →
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="border-b border-slate-200/70 text-slate-500 uppercase text-[10px] tracking-wider">
                        <th class="pb-3 pr-4 font-bold text-slate-900 w-44">Staff Associate</th>
                        @foreach($daysOfWeek as $day)
                            <th class="pb-3 px-2 font-semibold text-center {{ $day->isToday() ? 'text-indigo-600 bg-indigo-50/50 rounded-t-lg' : '' }}">
                                <div>{{ $day->format('D') }}</div>
                                <div class="font-bold text-[11px] {{ $day->isToday() ? 'text-indigo-700' : 'text-slate-800' }}">{{ $day->format('d M') }}</div>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($staffMembers as $staff)
                        <tr class="hover:bg-slate-50/40 transition-colors">
                            <td class="py-3 pr-4 font-semibold text-slate-800">
                                <div class="font-bold text-slate-900">{{ $staff->name }}</div>
                                <div class="text-[10px] text-slate-400">{{ $staff->email }}</div>
                            </td>
                            @foreach($daysOfWeek as $day)
                                @php
                                    $dayShift = $shifts->where('user_id', $staff->id)->where('shift_date', $day->toDateString())->first();
                                @endphp
                                <td class="py-2.5 px-2 text-center align-top {{ $day->isToday() ? 'bg-indigo-50/30' : '' }}">
                                    @if($dayShift)
                                        <div class="p-1.5 rounded-lg border text-left {{ $dayShift->shift_type_badge['bg'] }} text-[10px] relative group">
                                            <div class="font-bold flex items-center justify-between">
                                                <span>{{ $dayShift->shift_type_badge['icon'] }} {{ ucfirst($dayShift->shift_type) }}</span>
                                                <form action="{{ route('admin.staff.departments.shifts.destroy', $dayShift) }}" method="POST" class="inline opacity-0 group-hover:opacity-100 transition">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-rose-500 hover:text-rose-700 font-bold" title="Cancel Shift">✕</button>
                                                </form>
                                            </div>
                                            <div class="font-mono text-[9px] mt-0.5 text-slate-600">{{ substr($dayShift->start_time, 0, 5) }} - {{ substr($dayShift->end_time, 0, 5) }}</div>
                                            <div class="text-[9px] truncate text-slate-500 font-semibold">{{ $dayShift->department?->code }}</div>
                                        </div>
                                    @else
                                        <div class="text-slate-300 font-mono text-center py-2 text-[11px]">—</div>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- 1. New Department Modal -->
    <div x-show="showNewDeptModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-slate-200" @click.away="showNewDeptModal = false">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-base font-bold text-slate-900">Register New Department</h3>
                <button @click="showNewDeptModal = false" class="text-slate-400 hover:text-slate-600">✕</button>
            </div>

            <form action="{{ route('admin.staff.departments.store') }}" method="POST" class="space-y-4">
                @csrf

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Department Name</label>
                        <input type="text" name="name" required placeholder="e.g. VIP Atelier Services"
                               class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Code (Unique)</label>
                        <input type="text" name="code" required placeholder="e.g. VIP-ATELIER"
                               class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold uppercase">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Head of Department</label>
                        <select name="head_user_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold">
                            <option value="">Select Supervisor</option>
                            @foreach($staffMembers as $u)
                                <option value="{{ $u->id }}">{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Tag Color</label>
                        <select name="color" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold">
                            <option value="indigo">Indigo</option>
                            <option value="blue">Blue</option>
                            <option value="rose">Rose</option>
                            <option value="emerald">Emerald</option>
                            <option value="purple">Purple</option>
                            <option value="amber">Amber</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Operational Description</label>
                    <textarea name="description" rows="2" placeholder="Primary store responsibilities, team deliverables..."
                              class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" @click="showNewDeptModal = false" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-xl text-xs font-semibold">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2 bg-indigo-600 text-white rounded-xl text-xs font-bold hover:bg-indigo-700">
                        Save Department
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 2. Schedule Shift Modal -->
    <div x-show="showShiftModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-slate-200" @click.away="showShiftModal = false">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-base font-bold text-slate-900">Schedule Staff Shift</h3>
                <button @click="showShiftModal = false" class="text-slate-400 hover:text-slate-600">✕</button>
            </div>

            <form action="{{ route('admin.staff.departments.shifts') }}" method="POST" class="space-y-4">
                @csrf

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Staff Member</label>
                        <select name="user_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold">
                            <option value="">Select Staff</option>
                            @foreach($staffMembers as $u)
                                <option value="{{ $u->id }}">{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Department</label>
                        <select name="department_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold">
                            @foreach($departments as $d)
                                <option value="{{ $d->id }}">{{ $d->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Shift Date</label>
                        <input type="date" name="shift_date" value="{{ now()->toDateString() }}" required
                               class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Shift Type</label>
                        <select name="shift_type" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold">
                            <option value="morning">🌅 Morning (09:00 - 17:00)</option>
                            <option value="evening">🌆 Evening (13:00 - 21:00)</option>
                            <option value="night">🌙 Night (21:00 - 06:00)</option>
                            <option value="full_day">☀️ Full Day (10:00 - 20:00)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Shift Assignment Notes</label>
                    <input type="text" name="notes" placeholder="e.g. Lead bridal room stylist, terminal cashier..."
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold">
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" @click="showShiftModal = false" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-xl text-xs font-semibold">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2 bg-indigo-600 text-white rounded-xl text-xs font-bold hover:bg-indigo-700">
                        Roster Shift
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 3. Assign Personnel Modal -->
    <div x-show="showAssignModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-slate-200" @click.away="showAssignModal = false">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Assign Associates to Department</h3>
                    <p class="text-xs text-slate-400" x-text="selectedDeptName"></p>
                </div>
                <button @click="showAssignModal = false" class="text-slate-400 hover:text-slate-600">✕</button>
            </div>

            <form :action="'{{ url('admin/staff/departments') }}/' + selectedDeptId + '/assign'" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Select Associates</label>
                    <div class="max-h-48 overflow-y-auto border border-slate-200 rounded-xl p-2.5 space-y-1.5 bg-slate-50">
                        @foreach($staffMembers as $u)
                            <label class="flex items-center gap-2 text-xs text-slate-700 p-1.5 hover:bg-white rounded cursor-pointer">
                                <input type="checkbox" name="user_ids[]" value="{{ $u->id }}" class="rounded text-indigo-600 focus:ring-indigo-500">
                                <span class="font-semibold">{{ $u->name }}</span>
                                <span class="text-[10px] text-slate-400">({{ $u->email }})</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Designation / Role Title in Dept</label>
                    <input type="text" name="role_title" value="Department Associate"
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold">
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" @click="showAssignModal = false" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-xl text-xs font-semibold">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2 bg-indigo-600 text-white rounded-xl text-xs font-bold hover:bg-indigo-700">
                        Confirm Assignment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

