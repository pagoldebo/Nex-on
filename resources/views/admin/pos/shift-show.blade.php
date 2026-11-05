@extends('admin.layout')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="sm:flex sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.pos.shifts.index') }}" class="p-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Shift Audit: <span class="font-mono text-indigo-600">{{ $shift->shift_code }}</span></h1>
                <p class="mt-1 text-sm text-slate-500">Cashier: <strong>{{ $shift->cashier?->name }}</strong> &bull; Opened: {{ $shift->opened_at->format('M d, Y h:i A') }}</p>
            </div>
        </div>
        <div class="mt-4 sm:mt-0 flex items-center gap-3">
            <a href="{{ route('admin.pos.shifts.print', ['shift' => $shift, 'type' => $shift->status === 'open' ? 'x' : 'z']) }}" target="_blank"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-900 text-xs font-bold text-white hover:bg-slate-800 shadow-sm transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print {{ $shift->status === 'open' ? 'X-Report (Mid-Shift)' : 'Z-Report (Closing Slip)' }}
            </a>
        </div>
    </div>

    <!-- Tender Breakdown Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-5">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Cash Tender</p>
            <p class="text-2xl font-black text-emerald-600 mt-1">₹{{ number_format((float)$shift->cash_sales, 2) }}</p>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Card Payments</p>
            <p class="text-2xl font-black text-blue-600 mt-1">₹{{ number_format((float)$shift->card_sales, 2) }}</p>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">UPI / QR Code</p>
            <p class="text-2xl font-black text-purple-600 mt-1">₹{{ number_format((float)$shift->upi_sales, 2) }}</p>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Shift Revenue</p>
            <p class="text-2xl font-black text-slate-900 mt-1">₹{{ number_format((float)$shift->total_sales, 2) }}</p>
            <p class="text-[10px] text-slate-400 mt-0.5">{{ $shift->total_orders_count }} Completed Orders</p>
        </div>
    </div>

    <!-- Drawer Balancing Card -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
        <h2 class="text-xs font-bold text-slate-800 uppercase tracking-wider mb-4">Cash Drawer Balancing Formula</h2>
        
        <div class="grid grid-cols-2 md:grid-cols-6 gap-4 text-center">
            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/70">
                <span class="text-[10px] text-slate-500 font-bold uppercase">Opening Float</span>
                <div class="text-base font-black text-slate-800 mt-1">₹{{ number_format((float)$shift->opening_float, 2) }}</div>
            </div>
            <div class="p-3 bg-emerald-50 rounded-xl border border-emerald-200/70">
                <span class="text-[10px] text-emerald-700 font-bold uppercase">+ Cash Sales</span>
                <div class="text-base font-black text-emerald-800 mt-1">₹{{ number_format((float)$shift->cash_sales, 2) }}</div>
            </div>
            <div class="p-3 bg-blue-50 rounded-xl border border-blue-200/70">
                <span class="text-[10px] text-blue-700 font-bold uppercase">+ Cash In</span>
                <div class="text-base font-black text-blue-800 mt-1">₹{{ number_format((float)$shift->movements->where('type', 'cash_in')->sum('amount'), 2) }}</div>
            </div>
            <div class="p-3 bg-rose-50 rounded-xl border border-rose-200/70">
                <span class="text-[10px] text-rose-700 font-bold uppercase">- Cash Out</span>
                <div class="text-base font-black text-rose-800 mt-1">₹{{ number_format((float)$shift->movements->where('type', 'cash_out')->sum('amount'), 2) }}</div>
            </div>
            <div class="p-3 bg-indigo-50 rounded-xl border border-indigo-200/70">
                <span class="text-[10px] text-indigo-700 font-bold uppercase">= Expected Cash</span>
                <div class="text-base font-black text-indigo-900 mt-1">₹{{ number_format((float)$shift->expected_cash, 2) }}</div>
            </div>
            <div class="p-3 {{ $shift->status === 'closed' ? ($shift->cash_difference == 0 ? 'bg-slate-50' : ($shift->cash_difference > 0 ? 'bg-emerald-100' : 'bg-rose-100')) : 'bg-slate-50' }} rounded-xl border border-slate-200">
                <span class="text-[10px] text-slate-500 font-bold uppercase">Actual Counted</span>
                <div class="text-base font-black text-slate-900 mt-1">
                    {{ $shift->counted_cash !== null ? '₹' . number_format((float)$shift->counted_cash, 2) : 'Open' }}
                </div>
                @if($shift->status === 'closed')
                    <div class="text-[9px] font-extrabold {{ $shift->cash_difference >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                        Diff: {{ $shift->cash_difference > 0 ? '+₹' : ($shift->cash_difference < 0 ? '-₹' : '₹') }}{{ abs((float)$shift->cash_difference) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Cash Movements Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Drawer Pay-In / Pay-Out Movements</h3>
            <span class="text-xs bg-slate-200 text-slate-700 font-bold px-2 py-0.5 rounded-full">{{ $shift->movements->count() }} records</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50/75 border-b border-slate-200/70 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                    <tr>
                        <th class="py-3 px-4">Time</th>
                        <th class="py-3 px-4">Type</th>
                        <th class="py-3 px-4">Amount</th>
                        <th class="py-3 px-4">Reason / Notes</th>
                        <th class="py-3 px-4 text-right">Logged By</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($shift->movements as $m)
                        <tr class="hover:bg-slate-50/60">
                            <td class="py-3 px-4 font-mono text-slate-500">{{ $m->created_at->format('h:i A') }}</td>
                            <td class="py-3 px-4">
                                @if($m->type === 'cash_in')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Cash In (Pay-In)</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">Cash Out (Pay-Out)</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 font-black {{ $m->type === 'cash_in' ? 'text-emerald-700' : 'text-rose-700' }}">
                                {{ $m->type === 'cash_in' ? '+₹' : '-₹' }}{{ number_format((float)$m->amount, 2) }}
                            </td>
                            <td class="py-3 px-4 font-medium text-slate-800">{{ $m->reason }}</td>
                            <td class="py-3 px-4 text-right text-slate-500">{{ $m->user?->name ?? 'Staff' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center text-slate-400">No cash in/out movements recorded during this shift.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Orders Placed in this Shift Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">POS Orders Completed in Shift</h3>
            <span class="text-xs text-slate-500">Showing <span class="font-bold text-slate-800">{{ $orders->total() }}</span> orders</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50/75 border-b border-slate-200/70 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                    <tr>
                        <th class="py-3 px-4">Order Reference</th>
                        <th class="py-3 px-4">Time</th>
                        <th class="py-3 px-4">Customer</th>
                        <th class="py-3 px-4">Tender Method</th>
                        <th class="py-3 px-4 text-right">Order Total</th>
                        <th class="py-3 px-4 text-right">Receipt</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($orders as $o)
                        <tr class="hover:bg-slate-50/60">
                            <td class="py-3 px-4 font-mono font-bold text-indigo-600">
                                <a href="{{ route('admin.orders.show', $o->id) }}" class="hover:underline">{{ $o->reference }}</a>
                            </td>
                            <td class="py-3 px-4 font-mono text-slate-500">{{ $o->created_at->format('h:i A') }}</td>
                            <td class="py-3 px-4 font-medium text-slate-800">{{ $o->shipping_name }}</td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase border bg-slate-50 text-slate-700">
                                    {{ $o->payment_method }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right font-black text-slate-900">
                                ₹{{ number_format((float)$o->total, 2) }}
                            </td>
                            <td class="py-3 px-4 text-right">
                                <a href="{{ route('admin.pos.receipt', $o->id) }}" target="_blank" class="p-1 text-slate-400 hover:text-indigo-600 inline-block" title="Print Receipt">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">No POS orders completed during this shift.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($orders->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

