{{--
    ======================================================================
    ADMIN MODULE: POS SHIFTS
    ======================================================================
    File: resources/views/admin/pos/shifts.blade.php
    Responsibility: Back-office administration for POS SHIFTS
    Features:
      - Standard administration UI following the Nex-on design guidelines
      - Accessible controls, Tailwind CSS styling, and reactive Alpine.js components
    Security & Access Control:
      - Guarded by Laravel authentication and RBAC permission middleware
    Operations & Maintenance:
      - Integrates with master administration shell (@extends('admin.layout'))
--}}
@extends('admin.layout')

@section('content')
<div class="space-y-8" x-data="{
    openShiftModalOpen: false,
    closeShiftModalOpen: false,
    movementModalOpen: false,
    activeShiftForClose: null,
    activeShiftForMovement: null,

    openCloseModal(shift) {
        this.activeShiftForClose = shift;
        this.closeShiftModalOpen = true;
    },

    openMovementModal(shift) {
        this.activeShiftForMovement = shift;
        this.movementModalOpen = true;
    }
}">
    <!-- Header -->
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">POS Cashier Shifts &amp; Drawer Reconciliation</h1>
            <p class="mt-1 text-sm text-slate-500">Manage cashier opening floats, mid-shift X-reports, end-of-day Z-reports, and physical drawer cash counting to prevent theft and reconcile register tenders.</p>
        </div>
        <div class="mt-4 sm:mt-0 flex items-center gap-3">
            @if(!$activeCashierShift)
                <button @click="openShiftModalOpen = true" type="button" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-600 text-xs font-bold text-white hover:bg-indigo-700 shadow-sm transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Open New Cashier Shift
                </button>
            @endif
            <a href="{{ route('admin.pos.index') }}" target="_blank" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border border-slate-200 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-50 shadow-xs">
                Launch POS Terminal ↗
            </a>
        </div>
    </div>

    <!-- Active Open Shift Banner (If cashier currently has one open) -->
    @if($activeCashierShift)
        <div class="p-6 rounded-2xl bg-gradient-to-r from-emerald-500/10 via-indigo-500/10 to-transparent border border-emerald-500/30 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-emerald-500 text-white flex items-center justify-center font-black shadow-sm">
                    <svg class="w-6 h-6 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-black uppercase tracking-wider text-emerald-800 bg-emerald-100 px-2 py-0.5 rounded-full">Your Active Register Shift</span>
                        <span class="font-mono font-bold text-slate-900 text-xs">#{{ $activeCashierShift->shift_code }}</span>
                    </div>
                    <p class="text-xs text-slate-600 mt-1">
                        Opened: <strong>{{ $activeCashierShift->opened_at->format('h:i A (M d)') }}</strong> &bull;
                        Float: <strong>₹{{ number_format((float)$activeCashierShift->opening_float, 2) }}</strong> &bull;
                        Sales: <strong>₹{{ number_format((float)$activeCashierShift->total_sales, 2) }}</strong> ({{ $activeCashierShift->total_orders_count }} orders) &bull;
                        Drawer Cash: <strong class="text-emerald-700">₹{{ number_format((float)$activeCashierShift->expected_cash, 2) }}</strong>
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" @click="openMovementModal({{ $activeCashierShift->toJson() }})" class="px-3 py-2 text-xs font-bold bg-white text-slate-700 hover:bg-slate-50 border border-slate-200 rounded-xl shadow-2xs">
                    Pay In / Pay Out
                </button>
                <a href="{{ route('admin.pos.shifts.print', ['shift' => $activeCashierShift, 'type' => 'x']) }}" target="_blank" class="px-3 py-2 text-xs font-bold bg-indigo-50 text-indigo-700 hover:bg-indigo-100 border border-indigo-200 rounded-xl">
                    Mid-Shift X-Report
                </a>
                <button type="button" @click="openCloseModal({{ $activeCashierShift->toJson() }})" class="px-4 py-2 text-xs font-bold bg-rose-600 text-white hover:bg-rose-700 rounded-xl shadow-xs">
                    Close Shift (Z-Report)
                </button>
            </div>
        </div>
    @endif

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('info'))
        <div class="p-4 rounded-xl bg-blue-50 border border-blue-200 text-blue-800 text-sm">
            {{ session('info') }}
        </div>
    @endif

    <!-- Financial KPIs -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Open Register Shifts</p>
                <p class="text-2xl font-black text-slate-900 mt-1">{{ $openShiftsCount }}</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
            </div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Shift Sales Today</p>
                <p class="text-2xl font-black text-indigo-600 mt-1">₹{{ number_format($totalSalesToday, 2) }}</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            </div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Active Drawer Cash</p>
                <p class="text-2xl font-black text-slate-900 mt-1">₹{{ number_format($totalDrawerCash, 2) }}</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Net Cash Variance</p>
                <p class="text-2xl font-black {{ $cashDiscrepancies == 0 ? 'text-slate-900' : ($cashDiscrepancies > 0 ? 'text-emerald-600' : 'text-rose-600') }} mt-1">
                    {{ $cashDiscrepancies > 0 ? '+₹' : ($cashDiscrepancies < 0 ? '-₹' : '₹') }}{{ abs($cashDiscrepancies) }}
                </p>
            </div>
            <div class="w-11 h-11 rounded-xl {{ $cashDiscrepancies < 0 ? 'bg-rose-50 text-rose-600' : 'bg-slate-100 text-slate-600' }} flex items-center justify-center font-bold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
            </div>
        </div>
    </div>

    <!-- Shifts Audit Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-slate-50/50">
            <form method="GET" action="{{ route('admin.pos.shifts.index') }}" class="flex flex-wrap items-center gap-2 flex-1">
                <div class="relative w-64">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search Shift Code or Cashier..."
                           class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                </div>

                <select name="status" class="px-3 py-2 text-xs rounded-xl border border-slate-200 bg-white">
                    <option value="">All Statuses</option>
                    <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Open Registers</option>
                    <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed / Reconciled</option>
                </select>

                <select name="cashier_id" class="px-3 py-2 text-xs rounded-xl border border-slate-200 bg-white">
                    <option value="">All Cashiers</option>
                    @foreach($cashiers as $c)
                        <option value="{{ $c->id }}" {{ request('cashier_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>

                <button type="submit" class="px-3.5 py-2 bg-slate-200/80 text-slate-700 text-xs font-semibold rounded-xl hover:bg-slate-300">Filter</button>
            </form>
            <div class="text-xs text-slate-500">
                Showing <span class="font-bold text-slate-800">{{ $shifts->total() }}</span> shifts
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50/75 border-b border-slate-200/70 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                    <tr>
                        <th class="py-3 px-4">Shift Code</th>
                        <th class="py-3 px-4">Cashier</th>
                        <th class="py-3 px-4">Opened / Closed</th>
                        <th class="py-3 px-4">Opening Float</th>
                        <th class="py-3 px-4">Total Sales (Orders)</th>
                        <th class="py-3 px-4">Expected Cash</th>
                        <th class="py-3 px-4">Counted / Diff</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($shifts as $shift)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-3 px-4 font-mono font-bold text-slate-900">
                                {{ $shift->shift_code }}
                            </td>
                            <td class="py-3 px-4 font-bold text-slate-900">
                                {{ $shift->cashier?->name ?? 'Unknown Cashier' }}
                            </td>
                            <td class="py-3 px-4 text-[11px]">
                                <div><span class="text-slate-400 font-normal">Opened:</span> {{ $shift->opened_at->format('M d, h:i A') }}</div>
                                @if($shift->closed_at)
                                    <div><span class="text-slate-400 font-normal">Closed:</span> {{ $shift->closed_at->format('M d, h:i A') }}</div>
                                @else
                                    <div class="text-emerald-600 font-bold">Currently Open</div>
                                @endif
                            </td>
                            <td class="py-3 px-4 font-semibold text-slate-800">
                                ₹{{ number_format((float)$shift->opening_float, 2) }}
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-bold text-indigo-700">₹{{ number_format((float)$shift->total_sales, 2) }}</div>
                                <div class="text-[10px] text-slate-400">{{ $shift->total_orders_count }} orders &bull; Cash: ₹{{ number_format((float)$shift->cash_sales, 0) }}</div>
                            </td>
                            <td class="py-3 px-4 font-bold text-slate-900">
                                ₹{{ number_format((float)$shift->expected_cash, 2) }}
                            </td>
                            <td class="py-3 px-4">
                                @if($shift->status === 'closed')
                                    <div class="font-bold text-slate-900">₹{{ number_format((float)$shift->counted_cash, 2) }}</div>
                                    @php $d = (float)$shift->cash_difference; @endphp
                                    <div class="text-[10px] font-bold {{ $d == 0 ? 'text-slate-500' : ($d > 0 ? 'text-emerald-600' : 'text-rose-600') }}">
                                        {{ $d == 0 ? 'Balanced' : ($d > 0 ? '+₹' . number_format($d, 2) . ' Over' : '-₹' . number_format(abs($d), 2) . ' Short') }}
                                    </div>
                                @else
                                    <span class="text-slate-400 italic">Drawer In-Use</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($shift->status === 'open')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 animate-pulse">Open</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">Closed</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('admin.pos.shifts.show', $shift) }}" class="p-1.5 text-slate-500 hover:text-indigo-600 hover:bg-slate-100 rounded-lg transition-colors" title="View Audit Details">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    <a href="{{ route('admin.pos.shifts.print', ['shift' => $shift, 'type' => $shift->status === 'open' ? 'x' : 'z']) }}" target="_blank"
                                       class="px-2 py-1 text-[11px] font-bold bg-slate-100 text-slate-700 hover:bg-slate-200 rounded-lg transition-colors" title="Print X/Z Report">
                                        {{ $shift->status === 'open' ? 'X-Report' : 'Z-Report' }}
                                    </a>
                                    @if($shift->status === 'open')
                                        <button type="button" @click="openCloseModal({{ $shift->toJson() }})" class="px-2.5 py-1 text-[11px] font-bold bg-rose-50 text-rose-700 hover:bg-rose-100 rounded-lg transition-colors">
                                            Close
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-12 text-center text-slate-400">
                                <svg class="w-10 h-10 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                                <p class="font-semibold text-slate-600">No shifts recorded</p>
                                <p class="text-xs text-slate-400 mt-0.5">Click "Open New Cashier Shift" to register opening cash drawer.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($shifts->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $shifts->links() }}
            </div>
        @endif
    </div>

    <!-- Modal: Open New Shift -->
    <div x-show="openShiftModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="openShiftModalOpen" @click="openShiftModalOpen = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="openShiftModalOpen" class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-slate-200">
                <form action="{{ route('admin.pos.shifts.open') }}" method="POST">
                    @csrf
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-slate-900">Open Register Shift</h3>
                        <p class="text-xs text-slate-500 mt-1">Unlock POS cashier drawer and record opening cash float.</p>
                        
                        <div class="space-y-4 mt-5">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Opening Cash Float (₹) *</label>
                                <input type="number" step="0.01" name="opening_float" value="1000.00" required class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-bold">
                                <p class="text-[10px] text-slate-400 mt-1">Base cash in drawer for making customer change.</p>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Opening Notes / Drawer Location</label>
                                <input type="text" name="notes" placeholder="e.g. Counter #1 - Morning Shift" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                            </div>
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                        <button type="button" @click="openShiftModalOpen = false" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-200 rounded-xl">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-xs font-bold rounded-xl hover:bg-indigo-700 shadow-sm">Start Shift</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal: Close Shift (Z-Report) -->
    <div x-show="closeShiftModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="closeShiftModalOpen" @click="closeShiftModalOpen = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="closeShiftModalOpen" class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-slate-200">
                <template x-if="activeShiftForClose">
                    <form :action="'{{ url('admin/pos/shifts') }}/' + activeShiftForClose.id + '/close'" method="POST">
                        @csrf
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-slate-900">Close Shift &amp; Z-Report</h3>
                            <p class="text-xs text-slate-500 mt-1">Count all physical currency in the drawer and enter below.</p>
                            
                            <div class="mt-4 p-3 bg-slate-50 rounded-xl border border-slate-200 space-y-1.5 text-xs">
                                <div class="flex justify-between text-slate-600">
                                    <span>Opening Float:</span>
                                    <span class="font-bold text-slate-900" x-text="'₹' + parseFloat(activeShiftForClose.opening_float).toFixed(2)"></span>
                                </div>
                                <div class="flex justify-between text-slate-600">
                                    <span>Cash Sales:</span>
                                    <span class="font-bold text-slate-900" x-text="'₹' + parseFloat(activeShiftForClose.cash_sales).toFixed(2)"></span>
                                </div>
                                <div class="flex justify-between border-t border-slate-200 pt-1 font-bold text-slate-900">
                                    <span>Expected Drawer Cash:</span>
                                    <span class="text-indigo-600" x-text="'₹' + parseFloat(activeShiftForClose.expected_cash).toFixed(2)"></span>
                                </div>
                            </div>

                            <div class="space-y-4 mt-5">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Actual Counted Cash (₹) *</label>
                                    <input type="number" step="0.01" name="counted_cash" :value="activeShiftForClose.expected_cash" required class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-bold">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Closing Audit Remarks</label>
                                    <textarea name="notes" rows="2" placeholder="e.g. Verified and locked in safe by manager..." class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                            <button type="button" @click="closeShiftModalOpen = false" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-200 rounded-xl">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-rose-600 text-white text-xs font-bold rounded-xl hover:bg-rose-700 shadow-sm">Lock &amp; Reconcile Shift</button>
                        </div>
                    </form>
                </template>
            </div>
        </div>
    </div>

    <!-- Modal: Cash In / Out Movement -->
    <div x-show="movementModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="movementModalOpen" @click="movementModalOpen = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="movementModalOpen" class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-slate-200">
                <template x-if="activeShiftForMovement">
                    <form :action="'{{ url('admin/pos/shifts') }}/' + activeShiftForMovement.id + '/movement'" method="POST">
                        @csrf
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-slate-900">Record Drawer Cash Movement</h3>
                            <p class="text-xs text-slate-500 mt-1">Pay-in petty cash or pay-out store expense from physical drawer.</p>
                            
                            <div class="space-y-4 mt-5">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Movement Type *</label>
                                    <select name="type" required class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-medium">
                                        <option value="cash_in">Cash In (Pay-In / Additional Change)</option>
                                        <option value="cash_out">Cash Out (Pay-Out / Store Expense / Bank Drop)</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Amount (₹) *</label>
                                    <input type="number" step="0.01" name="amount" required placeholder="e.g. 500.00" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-bold">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Reason / Voucher Description *</label>
                                    <input type="text" name="reason" required placeholder="e.g. Store packaging tape purchase" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                                </div>
                            </div>
                        </div>
                        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                            <button type="button" @click="movementModalOpen = false" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-200 rounded-xl">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-xs font-bold rounded-xl hover:bg-indigo-700 shadow-sm">Save Movement</button>
                        </div>
                    </form>
                </template>
            </div>
        </div>
    </div>
</div>
@endsection

