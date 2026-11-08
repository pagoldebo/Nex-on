{{--
    ======================================================================
    ADMIN MODULE: PURCHASE ORDERS INDEX
    ======================================================================
    File: resources/views/admin/purchase-orders/index.blade.php
    Responsibility: Back-office administration for PURCHASE ORDERS INDEX
    Features:
      - Standard administration UI following the Nex-on design guidelines
      - Accessible controls, Tailwind CSS styling, and reactive Alpine.js components
    Security & Access Control:
      - Guarded by Laravel authentication and RBAC permission middleware
    Operations & Maintenance:
      - Integrates with master administration shell (@extends('admin.layout'))
--}}
@extends('admin.layout', [
    'title' => 'Purchase Orders & Replenishment',
    'breadcrumbs' => [
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Purchase Orders'],
    ]
])

@section('content')
<div class="space-y-6">

    <!-- Top Action Bar & KPIs -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Supplier Purchase Orders (PO)</h1>
            <p class="text-xs text-slate-500 mt-1">Automated reordering, supplier directory &amp; Goods Received Note (GRN) receipts</p>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
            <form method="POST" action="{{ route('admin.purchase-orders.autoGenerate') }}" onsubmit="return confirm('Scan low-stock thresholds and draft Purchase Orders for depleted SKUs?');">
                @csrf
                <button type="submit" class="px-4 py-2 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white text-xs font-bold rounded-xl shadow-md shadow-amber-500/20 flex items-center gap-2 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    <span>1-Click Auto-Generate POs</span>
                </button>
            </form>

            <a href="{{ route('admin.suppliers.index') }}" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition border border-slate-200">
                Suppliers Directory
            </a>

            <a href="{{ route('admin.purchase-orders.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-md shadow-indigo-600/20 flex items-center gap-2 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Draft New PO</span>
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif
    @if(session('info'))
        <div class="p-4 rounded-2xl bg-blue-50 border border-blue-200 text-blue-800 text-xs font-semibold flex items-center gap-2 shadow-xs">
            <svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ session('info') }}</span>
        </div>
    @endif

    <!-- 4 Procurement Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Low Stock Breached</span>
                <div class="text-2xl font-black text-rose-600 mt-1 font-mono">{{ $lowStockVariantsCount }} SKUs</div>
                <span class="text-[10px] text-slate-400">Units &le; safety threshold</span>
            </div>
            <div class="w-11 h-11 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Open / Active POs</span>
                <div class="text-2xl font-black text-indigo-600 mt-1 font-mono">{{ $openPosCount }}</div>
                <span class="text-[10px] text-slate-400">Draft, sent &amp; in-transit</span>
            </div>
            <div class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Fulfilled This Month</span>
                <div class="text-2xl font-black text-emerald-600 mt-1 font-mono">{{ $receivedThisMonth }} POs</div>
                <span class="text-[10px] text-slate-400">Stock safely received</span>
            </div>
            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Procurement Committed</span>
                <div class="text-2xl font-black text-slate-900 mt-1 font-mono">₹{{ number_format($totalSpend, 0) }}</div>
                <span class="text-[10px] text-slate-400">Active purchase order value</span>
            </div>
            <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
        </div>
    </div>

    <!-- Status Tabs -->
    <div class="flex items-center gap-2 border-b border-slate-200 overflow-x-auto pb-1 text-xs">
        @php
            $tabList = [
                'all'       => 'All Orders',
                'draft'     => 'Draft',
                'sent'      => 'Sent to Supplier',
                'partial'   => 'Partially Received',
                'received'  => 'Received (GRN Done)',
                'cancelled' => 'Cancelled',
            ];
        @endphp
        @foreach($tabList as $key => $tabLabel)
            <a href="{{ route('admin.purchase-orders.index', array_merge(request()->query(), ['status' => $key])) }}"
               class="px-3.5 py-2 font-semibold whitespace-nowrap transition border-b-2 {{ $status === $key ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                {{ $tabLabel }}
            </a>
        @endforeach
    </div>

    <!-- Search & Supplier Filters -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-xs">
        <form method="GET" action="{{ route('admin.purchase-orders.index') }}" class="flex flex-col sm:flex-row gap-3">
            <input type="hidden" name="status" value="{{ $status }}">
            <div class="flex-1 relative">
                <input type="text" name="q" value="{{ $search }}"
                       placeholder="Search by PO Number (#PO-...), Supplier Name or Code..."
                       class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition font-mono">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="m21 21-4.35-4.35"/></svg>
            </div>
            <select name="supplier_id" class="px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-indigo-500 bg-white">
                <option value="">All Suppliers</option>
                @foreach($suppliers as $s)
                    <option value="{{ $s->id }}" {{ $supplierId == $s->id ? 'selected' : '' }}>{{ $s->name }} ({{ $s->code }})</option>
                @endforeach
            </select>
            <button type="submit" class="px-5 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition">
                Filter
            </button>
            @if(!empty($search) || !empty($supplierId))
                <a href="{{ route('admin.purchase-orders.index', ['status' => $status]) }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-semibold transition">
                    Clear
                </a>
            @endif
        </form>
    </div>

    <!-- Purchase Orders Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-left text-sm">
                <thead class="bg-slate-50/70 text-slate-500 uppercase text-[11px] font-bold tracking-wider">
                    <tr>
                        <th class="px-6 py-3.5">PO Number</th>
                        <th class="px-6 py-3.5">Supplier</th>
                        <th class="px-6 py-3.5">SKU Items</th>
                        <th class="px-6 py-3.5">Expected Date</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5">Total Amount</th>
                        <th class="px-6 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($purchaseOrders as $po)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-6 py-4 font-mono font-bold text-xs text-slate-900">
                                <a href="{{ route('admin.purchase-orders.show', $po) }}" class="text-indigo-600 hover:text-indigo-700">
                                    {{ $po->po_number }}
                                </a>
                                <div class="font-sans text-[11px] text-slate-400 font-normal mt-0.5">
                                    Drafted {{ $po->created_at->format('M d, Y') }}
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900 text-xs">{{ $po->supplier?->name ?? 'N/A' }}</div>
                                <div class="text-[11px] text-slate-400 font-mono mt-0.5">{{ $po->supplier?->code }} &bull; {{ $po->supplier?->phone }}</div>
                            </td>

                            <td class="px-6 py-4">
                                <div class="text-xs font-semibold text-slate-800">
                                    {{ $po->items->count() }} {{ Str::plural('line', $po->items->count()) }} &bull; {{ $po->items->sum('ordered_qty') }} units
                                </div>
                                <div class="text-[11px] text-slate-400 font-mono">
                                    Received: {{ $po->items->sum('received_qty') }} / {{ $po->items->sum('ordered_qty') }}
                                </div>
                            </td>

                            <td class="px-6 py-4 font-mono text-xs text-slate-600">
                                {{ $po->expected_delivery_date ? $po->expected_delivery_date->format('M d, Y') : 'Immediate' }}
                            </td>

                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold border uppercase tracking-wider {{ $po->status_badge }}">
                                    {{ $po->status }}
                                </span>
                            </td>

                            <td class="px-6 py-4 font-mono font-bold text-xs text-slate-900">
                                {{ $po->formatted_total }}
                            </td>

                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.purchase-orders.show', $po) }}" 
                                       class="px-2.5 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-lg text-xs font-semibold transition" title="GRN Stock Receipt">
                                        Fulfill / GRN
                                    </a>
                                    <a href="{{ route('admin.purchase-orders.print', $po) }}" 
                                       target="_blank"
                                       class="p-1 text-slate-400 hover:text-slate-700 rounded" title="Print Supplier Slip">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                <svg class="w-10 h-10 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                <p class="text-sm font-semibold">No purchase orders found.</p>
                                <p class="text-xs text-slate-500 mt-0.5">Click "1-Click Auto-Generate POs" or draft one manually.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($purchaseOrders->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $purchaseOrders->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
