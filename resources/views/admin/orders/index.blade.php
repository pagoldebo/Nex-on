{{--
    ======================================================================
    ADMIN MODULE: ORDERS INDEX
    ======================================================================
    File: resources/views/admin/orders/index.blade.php
    Responsibility: Back-office administration for ORDERS INDEX
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
<div class="space-y-6">
    <!-- Header -->
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Customer Orders</h1>
            <p class="mt-1 text-sm text-slate-500">Track shipments, fulfill packages, update dispatch tracking, and manage invoices.</p>
        </div>
        <div class="mt-4 sm:mt-0 flex items-center gap-3">
            <a href="{{ route('admin.orders.export', request()->query()) }}" 
               class="inline-flex items-center gap-2 px-3.5 py-1.5 text-xs font-semibold rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 transition border border-emerald-200/60 shadow-xs">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Export CSV</span>
            </a>
            <span class="text-xs font-semibold text-slate-500 bg-white border border-slate-200 px-3 py-1.5 rounded-lg shadow-xs">
                Total Orders: <span class="text-indigo-600 font-bold">{{ $statusCounts['all'] ?? 0 }}</span>
            </span>
        </div>
    </div>

    <!-- Status Tabs -->
    <div class="flex items-center gap-2 overflow-x-auto pb-1 border-b border-slate-200">
        @php
            $tabs = [
                'all'        => 'All Orders',
                'pending'    => 'Pending',
                'confirmed'  => 'Confirmed',
                'processing' => 'Processing',
                'shipped'    => 'Shipped',
                'delivered'  => 'Delivered',
                'cancelled'  => 'Cancelled',
            ];
        @endphp

        @foreach($tabs as $tabKey => $tabLabel)
            @php
                $isActive = ($status ?? 'all') === $tabKey;
                $count = $statusCounts[$tabKey] ?? 0;
            @endphp
            <a href="{{ route('admin.orders.index', array_merge(request()->query(), ['status' => $tabKey])) }}"
               class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 shrink-0 {{ $isActive ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                <span>{{ $tabLabel }}</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $isActive ? 'bg-slate-700 text-slate-200' : 'bg-slate-200 text-slate-600' }}">
                    {{ $count }}
                </span>
            </a>
        @endforeach
    </div>

    <!-- Search & Search Filters -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-xs">
        <form method="GET" action="{{ route('admin.orders.index') }}" class="flex flex-col sm:flex-row gap-3">
            <input type="hidden" name="status" value="{{ $status ?? 'all' }}">
            <div class="flex-1 relative">
                <input type="text" name="q" value="{{ $search ?? '' }}" 
                       placeholder="Search by order reference (#SAA-...), customer name, phone, or email..." 
                       class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="m21 21-4.35-4.35"/></svg>
            </div>
            <button type="submit" class="px-5 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition">
                Search
            </button>
            @if(!empty($search))
                <a href="{{ route('admin.orders.index', ['status' => $status ?? 'all']) }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-semibold transition" title="Clear search">
                    Clear
                </a>
            @endif
        </form>
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-left text-sm">
                <thead class="bg-slate-50/70 text-slate-500 uppercase text-[11px] font-bold tracking-wider">
                    <tr>
                        <th class="px-6 py-3.5">Order Ref</th>
                        <th class="px-6 py-3.5">Customer & Destination</th>
                        <th class="px-6 py-3.5">Items</th>
                        <th class="px-6 py-3.5">Payment</th>
                        <th class="px-6 py-3.5">Order Status</th>
                        <th class="px-6 py-3.5">Grand Total</th>
                        <th class="px-6 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($orders as $order)
                        @php
                            $statusColors = [
                                'pending'    => 'bg-amber-50 text-amber-700 border-amber-200',
                                'confirmed'  => 'bg-blue-50 text-blue-700 border-blue-200',
                                'processing' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                'shipped'    => 'bg-purple-50 text-purple-700 border-purple-200',
                                'delivered'  => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                'cancelled'  => 'bg-slate-100 text-slate-600 border-slate-200',
                            ];
                            $badgeClass = $statusColors[$order->status] ?? 'bg-slate-100 text-slate-600 border-slate-200';
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-6 py-4 font-mono font-bold text-xs text-slate-900">
                                <div class="flex items-center gap-1.5">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="hover:text-indigo-600 transition">
                                        <span>#{{ $order->reference }}</span>
                                    </a>
                                    @if($order->channel === 'pos')
                                        <span class="px-1.5 py-0.2 rounded text-[10px] font-mono font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">POS</span>
                                    @else
                                        <span class="px-1.5 py-0.2 rounded text-[10px] font-mono font-bold bg-blue-50 text-blue-700 border border-blue-200">ONLINE</span>
                                    @endif
                                </div>
                                <div class="font-sans text-[11px] text-slate-400 font-normal mt-0.5">
                                    {{ $order->created_at->format('M d, Y · h:i A') }}
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900 text-xs">{{ $order->shipping_name }}</div>
                                <div class="text-[11px] text-slate-400 flex items-center gap-1.5 mt-0.5">
                                    <span>{{ $order->shipping_city }}, {{ $order->shipping_state }}</span>
                                    <span>·</span>
                                    <span>{{ $order->shipping_phone }}</span>
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <div class="text-xs font-semibold text-slate-800">
                                    {{ $order->items->sum('qty') }} {{ Str::plural('item', $order->items->sum('qty')) }}
                                </div>
                                <div class="text-[11px] text-slate-400 truncate max-w-[180px]">
                                    {{ $order->items->pluck('product_name')->join(', ') }}
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex items-center gap-1.5">
                                    <span class="text-[10px] font-black uppercase tracking-wider px-2 py-0.5 bg-slate-100 text-slate-700 rounded border border-slate-200">
                                        {{ strtoupper($order->payment_method) }}
                                    </span>
                                    <span class="text-[11px] font-semibold {{ $order->payment_status === 'paid' ? 'text-emerald-600' : 'text-amber-600' }}">
                                        {{ ucfirst($order->payment_status) }}
                                    </span>
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-0.5 rounded-full border {{ $badgeClass }}">
                                    <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>

                            <td class="px-6 py-4 font-extrabold text-slate-900 text-sm">
                                ₹{{ number_format($order->total, 0) }}
                            </td>

                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.orders.show', $order) }}" 
                                       class="px-3 py-1 rounded-lg bg-indigo-50 text-indigo-700 hover:bg-indigo-100 text-xs font-bold transition">
                                        Manage
                                    </a>
                                    <a href="{{ route('admin.orders.invoice', $order) }}" target="_blank"
                                       class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-medium transition" title="Print Invoice">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400 text-xs">
                                No orders found in this status category.
                            </td>
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
