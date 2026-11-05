@extends('admin.layout')

@section('content')
<div class="space-y-6">
    <!-- Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-indigo-600 uppercase tracking-wider mb-1">
                <span>Finance & Treasury</span>
                <span>•</span>
                <span class="text-slate-500 font-medium">Liquidity Statement</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Real-Time Daily Cash Flow</h1>
            <p class="text-sm text-slate-500 mt-0.5">Consolidated cash inflows from POS &amp; online sales vs outflows for supplier purchases and overhead.</p>
        </div>
        <div class="flex items-center gap-2.5 flex-wrap">
            <a href="{{ route('admin.finance.cash-flow.export', ['period' => $period, 'from_date' => request('from_date'), 'to_date' => request('to_date')]) }}" 
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200/80 transition-colors shadow-2xs">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Export Statement (CSV)
            </a>
            <a href="{{ route('admin.finance.pl') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                View P&amp;L Report
            </a>
        </div>
    </div>

    <!-- Period Filter Selector -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex flex-wrap items-center justify-between gap-3">
        <form method="GET" action="{{ route('admin.finance.cash-flow') }}" class="flex flex-wrap items-center gap-2">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider mr-1">Period:</span>
            @foreach(['today' => 'Today', 'this_week' => 'This Week', 'this_month' => 'This Month', 'last_month' => 'Last Month', 'this_quarter' => 'This Quarter', 'this_year' => 'This Year'] as $key => $lbl)
                <button type="submit" name="period" value="{{ $key }}"
                        class="px-3 py-1.5 rounded-xl text-xs font-semibold transition-all {{ $period === $key ? 'bg-indigo-600 text-white shadow-2xs' : 'bg-slate-50 text-slate-600 hover:bg-slate-100 border border-slate-200/70' }}">
                    {{ $lbl }}
                </button>
            @endforeach
        </form>

        <div class="text-xs font-medium text-slate-500">
            Reporting Window: <span class="font-bold text-slate-800">{{ $startDate->format('d M Y') }}</span> — <span class="font-bold text-slate-800">{{ $endDate->format('d M Y') }}</span>
        </div>
    </div>

    <!-- 4 High-Impact Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Inflows -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Cash Inflows</span>
                <span class="p-2 rounded-xl bg-emerald-50 text-emerald-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
                </span>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-black text-emerald-700">₹{{ number_format($totalInflows, 2) }}</div>
                <div class="text-xs text-slate-500 mt-1">POS, online orders &amp; invoices</div>
            </div>
        </div>

        <!-- Total Outflows -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Cash Outflows</span>
                <span class="p-2 rounded-xl bg-rose-50 text-rose-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>
                </span>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-black text-rose-700">₹{{ number_format($totalOutflows, 2) }}</div>
                <div class="text-xs text-slate-500 mt-1">Expenses, PO bills &amp; payouts</div>
            </div>
        </div>

        <!-- Net Cash Flow -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Net Cash Flow</span>
                <span class="p-2 rounded-xl {{ $netCashFlow >= 0 ? 'bg-indigo-50 text-indigo-600' : 'bg-amber-50 text-amber-600' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-black {{ $netCashFlow >= 0 ? 'text-indigo-600' : 'text-rose-600' }}">
                    {{ $netCashFlow >= 0 ? '+' : '' }}₹{{ number_format($netCashFlow, 2) }}
                </div>
                <div class="text-xs {{ $netCashFlow >= 0 ? 'text-emerald-600 font-semibold' : 'text-rose-600 font-semibold' }} mt-1">
                    {{ $netCashFlow >= 0 ? 'Net Cash Surplus' : 'Net Cash Deficit' }}
                </div>
            </div>
        </div>

        <!-- Estimated Liquidity Balance -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Active Liquidity</span>
                <span class="p-2 rounded-xl bg-slate-100 text-slate-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                </span>
            </div>
            <div class="mt-3 space-y-1">
                <div class="flex items-center justify-between text-xs">
                    <span class="text-slate-500">Drawer Cash:</span>
                    <span class="font-bold font-mono text-slate-900">₹{{ number_format(max(0, $cashDrawerLiquidity), 2) }}</span>
                </div>
                <div class="flex items-center justify-between text-xs">
                    <span class="text-slate-500">Bank / Online:</span>
                    <span class="font-bold font-mono text-slate-900">₹{{ number_format($bankAccountLiquidity, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Inflow vs Outflow Detailed Breakdown Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Inflow Sources Card -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Operational Inflows Breakdown</h2>
                </div>
                <span class="text-sm font-black font-mono text-emerald-700">₹{{ number_format($totalInflows, 2) }}</span>
            </div>

            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50">
                    <div>
                        <div class="text-xs font-bold text-slate-800">POS Cash Register Sales</div>
                        <div class="text-[11px] text-slate-500">Walk-in customer orders paid with physical cash</div>
                    </div>
                    <div class="text-xs font-bold font-mono text-slate-900">₹{{ number_format($posCashSales, 2) }}</div>
                </div>

                <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50">
                    <div>
                        <div class="text-xs font-bold text-slate-800">Online &amp; Card Gateway Settlements</div>
                        <div class="text-[11px] text-slate-500">Razorpay, Stripe, UPI QR, and online web orders</div>
                    </div>
                    <div class="text-xs font-bold font-mono text-slate-900">₹{{ number_format($onlinePaidSales, 2) }}</div>
                </div>

                <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50">
                    <div>
                        <div class="text-xs font-bold text-slate-800">POS Shift Cash Floats &amp; Pay-Ins</div>
                        <div class="text-[11px] text-slate-500">Cash drawer injections recorded in register shifts</div>
                    </div>
                    <div class="text-xs font-bold font-mono text-slate-900">₹{{ number_format($posPayIns, 2) }}</div>
                </div>

                <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50">
                    <div>
                        <div class="text-xs font-bold text-slate-800">B2B Commercial Invoice Receipts</div>
                        <div class="text-[11px] text-slate-500">Direct wholesale invoice payments collected</div>
                    </div>
                    <div class="text-xs font-bold font-mono text-slate-900">₹{{ number_format($b2bCollected, 2) }}</div>
                </div>
            </div>
        </div>

        <!-- Outflow Destinations Card -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-rose-500"></span>
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Operational Outflows Breakdown</h2>
                </div>
                <span class="text-sm font-black font-mono text-rose-700">₹{{ number_format($totalOutflows, 2) }}</span>
            </div>

            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50">
                    <div>
                        <div class="text-xs font-bold text-slate-800">Operating Expenses (OPEX)</div>
                        <div class="text-[11px] text-slate-500">Rent, staff salaries, packaging, marketing, utilities</div>
                    </div>
                    <div class="text-xs font-bold font-mono text-slate-900">₹{{ number_format($expensesOutflow, 2) }}</div>
                </div>

                <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50">
                    <div>
                        <div class="text-xs font-bold text-slate-800">Supplier Purchase Invoice Payments</div>
                        <div class="text-[11px] text-slate-500">Vendor disbursements against purchase orders</div>
                    </div>
                    <div class="text-xs font-bold font-mono text-slate-900">₹{{ number_format($supplierPayments, 2) }}</div>
                </div>

                <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50">
                    <div>
                        <div class="text-xs font-bold text-slate-800">POS Drawer Petty Cash Pay-Outs</div>
                        <div class="text-[11px] text-slate-500">Store cash withdrawn during cashier shifts</div>
                    </div>
                    <div class="text-xs font-bold font-mono text-slate-900">₹{{ number_format($posPayOuts, 2) }}</div>
                </div>

                <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50">
                    <div>
                        <div class="text-xs font-bold text-slate-800">Customer Returns &amp; Order Refunds</div>
                        <div class="text-[11px] text-slate-500">Store credits and refunds issued to customers</div>
                    </div>
                    <div class="text-xs font-bold font-mono text-slate-900">₹{{ number_format($refundsOutflow, 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Daily Cash Flow Statement Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900">Daily Cash Movement Ledger</h3>
                <p class="text-xs text-slate-500">Chronological daily statement with cumulative liquidity progression.</p>
            </div>
            <span class="text-xs font-medium text-slate-500">Showing {{ count($dailyBreakdown) }} days in window</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200/80 text-slate-600 font-semibold uppercase tracking-wider text-[10px]">
                        <th class="py-3 px-4">Date</th>
                        <th class="py-3 px-4">Day</th>
                        <th class="py-3 px-4 text-right">Inflows (₹)</th>
                        <th class="py-3 px-4 text-right">Outflows (₹)</th>
                        <th class="py-3 px-4 text-right">Net Change (₹)</th>
                        <th class="py-3 px-4 text-right">Running Net (₹)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-mono">
                    @forelse($dailyBreakdown as $row)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-3 px-4 font-bold text-slate-900 font-sans">{{ $row['date'] }}</td>
                            <td class="py-3 px-4 text-slate-500 font-sans">{{ $row['day'] }}</td>
                            <td class="py-3 px-4 text-right font-medium text-emerald-700">
                                {{ $row['inflows'] > 0 ? '+' . number_format($row['inflows'], 2) : '—' }}
                            </td>
                            <td class="py-3 px-4 text-right font-medium text-rose-700">
                                {{ $row['outflows'] > 0 ? '-' . number_format($row['outflows'], 2) : '—' }}
                            </td>
                            <td class="py-3 px-4 text-right font-bold {{ $row['net'] >= 0 ? 'text-indigo-600' : 'text-rose-600' }}">
                                {{ $row['net'] >= 0 ? '+' : '' }}{{ number_format($row['net'], 2) }}
                            </td>
                            <td class="py-3 px-4 text-right font-bold text-slate-900">
                                ₹{{ number_format($row['cumulative'], 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400 font-sans">
                                No activity recorded in this period.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

