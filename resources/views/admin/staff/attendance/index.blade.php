@extends('admin.layout')

@section('content')
<div class="space-y-6" x-data="{
    showAdjustModal: false,
    adjustActionUrl: '',
    adjustStaffName: '',
    adjustClockIn: '',
    adjustClockOut: '',
    adjustStatus: 'present',
    adjustNote: '',
    openAdjust(url, name, clockIn, clockOut, status, note) {
        this.adjustActionUrl = url;
        this.adjustStaffName = name;
        this.adjustClockIn = clockIn;
        this.adjustClockOut = clockOut;
        this.adjustStatus = status;
        this.adjustNote = note || '';
        this.showAdjustModal = true;
    }
}">
    <!-- Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-indigo-600 uppercase tracking-wider mb-1">
                <span>Organization</span>
                <span>•</span>
                <span class="text-slate-500 font-medium">Domain 7: Attendance &amp; Timesheets</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Staff Attendance &amp; Digital Clock-In</h1>
            <p class="text-sm text-slate-500 mt-0.5">Real-time store employee check-in verification, hours tracking, overtime logs, and timesheet exports.</p>
        </div>
        <div class="flex items-center gap-2.5 flex-wrap">
            <a href="{{ route('admin.staff.attendance.export', ['month' => substr($selectedDate, 0, 7)]) }}" 
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition-colors shadow-2xs">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export Timesheet (CSV)
            </a>
            <a href="{{ route('admin.hrm.payroll.run') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                Run Monthly Payroll
            </a>
        </div>
    </div>

    <!-- Navigation Pills Bar (List | Roles | Attendance) -->
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.staff.index') }}"
           class="px-4 py-1.5 rounded-full text-xs font-medium text-slate-600 hover:text-slate-900 bg-white border border-slate-200 hover:bg-slate-50 transition">
            List
        </a>
        <a href="{{ route('admin.roles.index') }}"
           class="px-4 py-1.5 rounded-full text-xs font-medium text-slate-600 hover:text-slate-900 bg-white border border-slate-200 hover:bg-slate-50 transition">
            Roles
        </a>
        <a href="{{ route('admin.staff.attendance') }}"
           class="px-4 py-1.5 rounded-full text-xs font-bold bg-indigo-600 text-white shadow-xs">
            Attendance
        </a>
    </div>

    <!-- 4 High-Impact Daily KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-xs font-semibold uppercase tracking-wider">Present Today</span>
                <span class="p-2 bg-emerald-50 text-emerald-600 rounded-xl">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <div class="text-2xl font-black text-emerald-600 tracking-tight">{{ $presentToday }} Staff</div>
            <p class="text-xs text-slate-400 mt-1">Clocked in across store &amp; warehouse</p>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-xs font-semibold uppercase tracking-wider">Attendance Rate</span>
                <span class="p-2 bg-indigo-50 text-indigo-600 rounded-xl">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </span>
            </div>
            <div class="text-2xl font-black text-indigo-600 tracking-tight">{{ $attendanceRate }}%</div>
            <p class="text-xs text-slate-400 mt-1">Of active store workforce</p>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-xs font-semibold uppercase tracking-wider">Late Arrivals</span>
                <span class="p-2 bg-amber-50 text-amber-600 rounded-xl">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <div class="text-2xl font-black text-amber-600 tracking-tight">{{ $lateToday }} Associates</div>
            <p class="text-xs text-slate-400 mt-1">Clocked in post 09:30 AM</p>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-xs font-semibold uppercase tracking-wider">Total Hours Logged</span>
                <span class="p-2 bg-purple-50 text-purple-600 rounded-xl">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                </span>
            </div>
            <div class="text-2xl font-black text-slate-900 tracking-tight">{{ number_format($totalHoursToday, 1) }} hrs</div>
            <p class="text-xs text-slate-400 mt-1">Cumulative operational time</p>
        </div>
    </div>

    <!-- Digital Time Punch Terminal Widget -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs" x-data="{ punchAction: 'clock_in' }">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
                <span class="text-lg">⏱️</span>
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Quick Digital Punch Terminal</h3>
            </div>
            <span class="text-xs font-mono font-bold text-slate-500">{{ now()->format('l, d M Y — h:i A') }}</span>
        </div>

        <form action="{{ route('admin.staff.attendance.punch') }}" method="POST" class="grid grid-cols-1 sm:grid-cols-5 gap-3 items-end">
            @csrf

            <!-- Associate -->
            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-slate-600 mb-1">Staff Associate</label>
                <select name="user_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:bg-white">
                    <option value="">Select Associate</option>
                    @foreach($staffMembers as $u)
                        <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Department -->
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Assigned Department</label>
                <select name="department_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:bg-white">
                    <option value="">Auto Detect</option>
                    @foreach($departments as $d)
                        <option value="{{ $d->id }}">{{ $d->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Terminal / Device -->
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Terminal Location</label>
                <select name="location_name" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:bg-white">
                    <option value="Boutique Main Floor">Boutique Main Floor</option>
                    <option value="Central Warehouse">Central Warehouse</option>
                    <option value="Atelier Salon">Atelier Salon</option>
                    <option value="POS Cashier Terminal 1">POS Cashier Terminal 1</option>
                </select>
            </div>

            <!-- Punch Button -->
            <div class="flex items-center gap-2">
                <button type="submit" name="action" value="clock_in"
                        class="flex-1 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition shadow-xs">
                    ✓ Clock In
                </button>
                <button type="submit" name="action" value="clock_out"
                        class="flex-1 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-bold transition shadow-xs">
                    🚪 Clock Out
                </button>
            </div>
        </form>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
        <form method="GET" action="{{ route('admin.staff.attendance') }}" class="grid grid-cols-1 sm:grid-cols-5 gap-3 items-end">
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Date</label>
                <input type="date" name="date" value="{{ $selectedDate }}"
                       class="w-full px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:bg-white">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Department</label>
                <select name="department_id" class="w-full px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:bg-white">
                    <option value="">All Departments</option>
                    @foreach($departments as $d)
                        <option value="{{ $d->id }}" {{ request('department_id') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Staff Member</label>
                <select name="user_id" class="w-full px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:bg-white">
                    <option value="">All Staff</option>
                    @foreach($staffMembers as $u)
                        <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Status</label>
                <select name="status" class="w-full px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:bg-white">
                    <option value="">All Statuses</option>
                    <option value="present" {{ request('status') === 'present' ? 'selected' : '' }}>Present</option>
                    <option value="late" {{ request('status') === 'late' ? 'selected' : '' }}>Late In</option>
                    <option value="half_day" {{ request('status') === 'half_day' ? 'selected' : '' }}>Half Day</option>
                    <option value="absent" {{ request('status') === 'absent' ? 'selected' : '' }}>Absent</option>
                </select>
            </div>

            <div>
                <button type="submit" class="w-full py-1.5 bg-slate-800 text-white rounded-xl text-xs font-bold hover:bg-slate-900 transition">
                    Filter Logs
                </button>
            </div>
        </form>
    </div>

    <!-- Attendance Table -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
        @if($attendances->isEmpty())
            <div class="text-center py-12 border-2 border-dashed border-slate-200 rounded-xl">
                <svg class="w-10 h-10 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-sm font-semibold text-slate-600">No attendance records found for {{ $selectedDate }}</p>
                <p class="text-xs text-slate-400 mt-1">Staff associates can clock in via the terminal widget above or POS register.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200/70 text-slate-400 uppercase text-[10px] tracking-wider">
                            <th class="pb-3 font-semibold">Associate</th>
                            <th class="pb-3 font-semibold">Department</th>
                            <th class="pb-3 font-semibold">Date</th>
                            <th class="pb-3 font-semibold">Clock In</th>
                            <th class="pb-3 font-semibold">Clock Out</th>
                            <th class="pb-3 font-semibold">Total Hours</th>
                            <th class="pb-3 font-semibold">Status</th>
                            <th class="pb-3 font-semibold">Verification</th>
                            <th class="pb-3 font-semibold text-right">Manager Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @foreach($attendances as $row)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-3">
                                    <div class="font-bold text-slate-900">{{ $row->user?->name ?? 'Staff' }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $row->user?->email }}</div>
                                </td>
                                <td class="py-3 font-semibold text-slate-800">{{ $row->department?->name ?? 'Store Floor' }}</td>
                                <td class="py-3 font-mono text-slate-600 text-[11px]">{{ $row->date?->format('d M Y') }}</td>
                                <td class="py-3 font-mono font-bold text-slate-900">
                                    {{ $row->clock_in ? $row->clock_in->format('h:i A') : '—' }}
                                </td>
                                <td class="py-3 font-mono font-bold text-slate-900">
                                    {{ $row->clock_out ? $row->clock_out->format('h:i A') : 'Active Shift' }}
                                </td>
                                <td class="py-3 font-mono font-bold text-indigo-600">
                                    {{ $row->total_hours > 0 ? $row->total_hours . ' hrs' : 'In Progress' }}
                                    @if($row->overtime_hours > 0)
                                        <span class="text-[10px] text-emerald-600 font-semibold">(+{{ $row->overtime_hours }} OT)</span>
                                    @endif
                                </td>
                                <td class="py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $row->status_badge['bg'] }}">
                                        {{ $row->status_badge['label'] }}
                                    </span>
                                </td>
                                <td class="py-3">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-semibold border {{ $row->verification_badge['bg'] }}"
                                          title="{{ $row->location_name }} • IP: {{ $row->ip_address }}">
                                        <span>{{ $row->verification_badge['icon'] }}</span>
                                        <span>{{ $row->verification_badge['label'] }}</span>
                                    </span>
                                </td>
                                <td class="py-3 text-right">
                                    <button type="button" 
                                            @click="openAdjust(
                                                '{{ route('admin.staff.attendance.adjust', $row) }}',
                                                '{{ addslashes($row->user?->name ?? 'Staff') }}',
                                                '{{ $row->clock_in ? $row->clock_in->format('Y-m-d\TH:i') : now()->format('Y-m-d\T09:00') }}',
                                                '{{ $row->clock_out ? $row->clock_out->format('Y-m-d\TH:i') : '' }}',
                                                '{{ $row->status }}',
                                                '{{ addslashes($row->manager_note ?? '') }}'
                                            )"
                                            class="inline-flex items-center px-2.5 py-1 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition">
                                        Adjust ✏️
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $attendances->links() }}
            </div>
        @endif
    </div>

    <!-- Manager Adjustment Modal -->
    <div x-show="showAdjustModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-200" @click.away="showAdjustModal = false">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Adjust Attendance Punch</h3>
                    <p class="text-xs text-slate-400" x-text="adjustStaffName"></p>
                </div>
                <button @click="showAdjustModal = false" class="text-slate-400 hover:text-slate-600">✕</button>
            </div>

            <form :action="adjustActionUrl" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Clock In Timestamp</label>
                    <input type="datetime-local" name="clock_in" x-model="adjustClockIn" required
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Clock Out Timestamp</label>
                    <input type="datetime-local" name="clock_out" x-model="adjustClockOut"
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Attendance Status</label>
                    <select name="status" x-model="adjustStatus" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold">
                        <option value="present">Present</option>
                        <option value="late">Late In</option>
                        <option value="half_day">Half Day</option>
                        <option value="absent">Absent</option>
                        <option value="on_leave">On Leave</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Manager Audit Reason / Note</label>
                    <textarea name="manager_note" x-model="adjustNote" rows="2" required placeholder="Reason for manual adjustment (e.g. employee forgot badge punch out, approved overtime)..."
                              class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" @click="showAdjustModal = false" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-xl text-xs font-semibold">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2 bg-indigo-600 text-white rounded-xl text-xs font-bold hover:bg-indigo-700">
                        Save Adjustment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

