{{--
    ======================================================================
    ADMIN MODULE: INTEGRATIONS TALLY
    ======================================================================
    File: resources/views/admin/integrations/tally.blade.php
    Responsibility: Back-office administration for INTEGRATIONS TALLY
    Features:
      - Standard administration UI following the Nex-on design guidelines
      - Accessible controls, Tailwind CSS styling, and reactive Alpine.js components
    Security & Access Control:
      - Guarded by Laravel authentication and RBAC permission middleware
    Operations & Maintenance:
      - Integrates with master administration shell (@extends('admin.layout'))
--}}
@extends('admin.layout', [
    'title' => 'Tally Prime & ERP Accounting Sync',
    'breadcrumbs' => [
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Integrations'],
        ['label' => 'Tally Prime Sync'],
    ]
])

@section('content')
<div class="space-y-6" x-data="{ xmlModalOpen: false, activeXml: '', activeRef: '' }">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <span class="w-7 h-7 rounded-lg bg-emerald-600 text-white flex items-center justify-center font-mono font-black text-xs shadow-md">T</span>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Tally Prime &amp; ERP Accounting Sync</h1>
            </div>
            <p class="text-xs text-slate-500 mt-1">Automatic XML voucher streaming, ledger postings, GST tax splitting &amp; stock journal synchronization</p>
        </div>

        <!-- 3 Bulk Export Buttons -->
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('admin.integrations.tally.exportSales', request()->query()) }}" 
               class="px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-md shadow-indigo-600/20 flex items-center gap-1.5 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                <span>Export Sales XML</span>
            </a>

            <a href="{{ route('admin.integrations.tally.exportReceipts', request()->query()) }}" 
               class="px-3.5 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold shadow-xs flex items-center gap-1.5 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Receipts XML</span>
            </a>

            <a href="{{ route('admin.integrations.tally.exportStock') }}" 
               class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-xs flex items-center gap-1.5 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                <span>Stock Journal XML</span>
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2 shadow-xs">
            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- 4 Accounting KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Synced Vouchers</span>
                <div class="text-2xl font-black text-emerald-600 mt-1 font-mono">{{ $syncedVouchersCount }}</div>
                <span class="text-[10px] text-slate-400">Total XML vouchers logged</span>
            </div>
            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                ✓
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Pending Orders</span>
                <div class="text-2xl font-black text-amber-600 mt-1 font-mono">{{ $pendingOrdersCount }}</div>
                <span class="text-[10px] text-slate-400">Awaiting ledger export</span>
            </div>
            <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Period Turnover</span>
                <div class="text-2xl font-black text-slate-900 mt-1 font-mono">₹{{ number_format($totalSalesValue, 0) }}</div>
                <span class="text-[10px] text-slate-400">{{ $totalOrdersCount }} orders in selected range</span>
            </div>
            <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center font-bold font-mono">
                ₹
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Tally XML Format</span>
                <div class="text-lg font-black text-indigo-600 mt-1 font-mono">TallyPrime 4/5</div>
                <span class="text-[10px] text-slate-400">Standard &lt;ENVELOPE&gt; compliant</span>
            </div>
            <div class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
            </div>
        </div>
    </div>

    <!-- Date Range Filter & Ledger Configuration Info -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-xs">
        <form method="GET" action="{{ route('admin.integrations.tally') }}" class="flex flex-col sm:flex-row items-end gap-3 text-xs">
            <div class="flex-1 grid grid-cols-2 gap-3 w-full sm:w-auto">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">From Date</label>
                    <input type="date" name="date_from" value="{{ $dateFrom }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 font-mono">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">To Date</label>
                    <input type="date" name="date_to" value="{{ $dateTo }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 font-mono">
                </div>
            </div>
            <button type="submit" class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl font-bold transition">
                Filter Records
            </button>
        </form>

        <!-- Standard Ledger Posting Map -->
        <div class="mt-4 pt-4 border-t border-slate-100 grid grid-cols-1 md:grid-cols-3 gap-3 text-[11px] text-slate-600">
            <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200/60">
                <span class="font-bold text-slate-900 block mb-1">Sales &amp; Revenue Ledgers:</span>
                <div>&bull; Primary: <code>Sales - Apparel &amp; Lingerie</code></div>
                <div>&bull; Freight: <code>Courier Freight &amp; Delivery Income</code></div>
            </div>
            <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200/60">
                <span class="font-bold text-slate-900 block mb-1">Tax Ledgers (Auto-Split):</span>
                <div>&bull; Intra-State: <code>Output CGST @ 9%</code> + <code>SGST @ 9%</code></div>
                <div>&bull; Inter-State: <code>Output IGST @ 18%</code></div>
            </div>
            <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200/60">
                <span class="font-bold text-slate-900 block mb-1">Payment Gateways &rarr; Bank Accounts:</span>
                <div>&bull; Cash: <code>Cash-in-Hand</code> | UPI: <code>HDFC Bank (UPI)</code></div>
                <div>&bull; Gateways: <code>Razorpay / Stripe Settlement A/c</code></div>
            </div>
        </div>
    </div>

    <!-- Recent Store Orders for Instant Sync -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-900">Recent Store Orders (Ready for Accounting Sync)</h2>
            <span class="text-xs text-slate-400 font-mono">Showing latest {{ $recentOrders->count() }}</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-left text-xs">
                <thead class="bg-slate-50/70 text-slate-500 uppercase font-mono text-[10px]">
                    <tr>
                        <th class="px-6 py-3">Order Ref</th>
                        <th class="px-6 py-3">Customer</th>
                        <th class="px-6 py-3">Payment Method</th>
                        <th class="px-6 py-3">Total Amount</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentOrders as $order)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-6 py-3 font-mono font-bold text-slate-900">
                                #{{ $order->reference }}
                                <span class="ml-1 text-[10px] text-slate-400 font-normal">({{ $order->channel }})</span>
                            </td>
                            <td class="px-6 py-3 text-slate-700">
                                {{ $order->shipping_name }}
                            </td>
                            <td class="px-6 py-3 font-mono uppercase font-bold text-slate-800">
                                {{ $order->payment_method }}
                            </td>
                            <td class="px-6 py-3 font-mono font-bold text-slate-900">
                                {{ $order->formatted_total }}
                            </td>
                            <td class="px-6 py-3 text-right">
                                <form method="POST" action="{{ route('admin.integrations.tally.syncOrder', $order) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-lg text-xs font-semibold transition">
                                        Sync to Tally
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-400">No recent orders in selected period.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Accounting Sync Logs & Audit History -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-900">Tally XML Sync Audit Logs</h2>
            <span class="text-xs text-slate-400 font-mono">{{ $syncLogs->total() }} total entries</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-left text-xs">
                <thead class="bg-slate-50/70 text-slate-500 uppercase font-mono text-[10px]">
                    <tr>
                        <th class="px-6 py-3">Timestamp</th>
                        <th class="px-6 py-3">Voucher Ref</th>
                        <th class="px-6 py-3">Sync Type</th>
                        <th class="px-6 py-3">Direction</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Payload</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($syncLogs as $log)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-6 py-3 font-mono text-slate-500">
                                {{ $log->created_at->format('M d, Y · H:i:s') }}
                            </td>
                            <td class="px-6 py-3 font-mono font-bold text-slate-900">
                                {{ $log->voucher_number }}
                            </td>
                            <td class="px-6 py-3 font-semibold text-slate-800">
                                {{ $log->formatted_type }}
                            </td>
                            <td class="px-6 py-3 font-mono text-[11px] text-slate-500">
                                {{ strtoupper($log->direction) }}
                            </td>
                            <td class="px-6 py-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    {{ strtoupper($log->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-right">
                                <button type="button" 
                                        @click="activeRef = '{{ $log->voucher_number }}'; activeXml = @js($log->payload_xml); xmlModalOpen = true"
                                        class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-[11px] font-mono transition">
                                    View XML
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-400">No synchronization logs recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($syncLogs->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $syncLogs->links() }}
            </div>
        @endif
    </div>

    <!-- Modal: View XML Payload -->
    <div x-show="xmlModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-xs p-4" x-cloak>
        <div @click.away="xmlModalOpen = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-3xl w-full p-6 shadow-2xl relative text-white flex flex-col max-h-[85vh]">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800 shrink-0">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                    <h3 class="text-sm font-extrabold font-mono text-white">Tally XML Payload: <span x-text="activeRef" class="text-indigo-400"></span></h3>
                </div>
                <button @click="xmlModalOpen = false" class="text-slate-400 hover:text-white p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto py-4">
                <pre class="bg-slate-950 p-4 rounded-xl text-xs font-mono text-emerald-300 overflow-x-auto whitespace-pre select-all border border-slate-800" x-text="activeXml"></pre>
            </div>
            <div class="pt-3 border-t border-slate-800 flex justify-end shrink-0">
                <button @click="navigator.clipboard.writeText(activeXml); alert('Tally XML copied to clipboard!')" 
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-md transition">
                    Copy XML
                </button>
            </div>
        </div>
    </div>

</div>
@endsection
