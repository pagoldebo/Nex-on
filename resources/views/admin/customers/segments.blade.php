{{--
    ======================================================================
    ADMIN MODULE: CUSTOMERS SEGMENTS
    ======================================================================
    File: resources/views/admin/customers/segments.blade.php
    Responsibility: Back-office administration for CUSTOMERS SEGMENTS
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
<div class="space-y-8">
    <!-- Header -->
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">RFM Behavioral Customer Segmentation</h1>
            <p class="mt-1 text-sm text-slate-500">Automated Recency, Frequency, and Monetary (RFM) behavioral analysis. Target VIP champions, re-engage at-risk shoppers, and convert promising new leads.</p>
        </div>
        <div class="mt-4 sm:mt-0 flex items-center gap-3">
            <a href="{{ route('admin.customers.segments.export', ['segment' => $selectedSegment, 'search' => $search]) }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-200 bg-white text-xs font-bold text-slate-700 hover:bg-slate-50 shadow-xs transition-all">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Export Segments CSV
            </a>
        </div>
    </div>

    <!-- RFM Domain KPI Cards -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        @php
            $segCards = [
                'champions' => [
                    'label' => 'Champions',
                    'icon_bg' => 'bg-emerald-50 text-emerald-600',
                    'border' => 'border-emerald-200/80',
                    'badge' => 'Top LTV'
                ],
                'loyal' => [
                    'label' => 'Loyal Shoppers',
                    'icon_bg' => 'bg-blue-50 text-blue-600',
                    'border' => 'border-blue-200/80',
                    'badge' => 'High Freq'
                ],
                'potential' => [
                    'label' => 'Potential',
                    'icon_bg' => 'bg-indigo-50 text-indigo-600',
                    'border' => 'border-indigo-200/80',
                    'badge' => 'Recent 1-2'
                ],
                'at_risk' => [
                    'label' => 'At Risk',
                    'icon_bg' => 'bg-amber-50 text-amber-600',
                    'border' => 'border-amber-200/80',
                    'badge' => '>90d Inactive'
                ],
                'hibernating' => [
                    'label' => 'Hibernating',
                    'icon_bg' => 'bg-rose-50 text-rose-600',
                    'border' => 'border-rose-200/80',
                    'badge' => 'Lost Shopper'
                ],
                'prospect' => [
                    'label' => 'Prospects',
                    'icon_bg' => 'bg-slate-50 text-slate-600',
                    'border' => 'border-slate-200/80',
                    'badge' => '0 Orders'
                ],
            ];
        @endphp

        @foreach($segCards as $k => $c)
            @php $metric = $distribution['segments'][$k] ?? ['count' => 0, 'percentage' => 0]; @endphp
            <div class="bg-white p-4 rounded-2xl border {{ $c['border'] }} shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ $c['label'] }}</span>
                    <span class="text-[9px] font-bold px-1.5 py-0.5 rounded {{ $c['icon_bg'] }}">{{ $c['badge'] }}</span>
                </div>
                <div>
                    <div class="text-2xl font-black text-slate-900">{{ $metric['count'] }}</div>
                    <div class="text-[10px] text-slate-500 font-semibold mt-0.5">{{ $metric['percentage'] }}% of customers</div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Segment Selector Navigation Pills -->
    <div class="bg-white p-2 rounded-2xl border border-slate-200/80 shadow-xs flex flex-wrap items-center gap-1.5">
        <a href="{{ route('admin.customers.segments', ['segment' => 'all']) }}"
           class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ $selectedSegment === 'all' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
            All Customers ({{ $distribution['total_customers'] }})
        </a>
        @foreach($distribution['segments'] as $key => $data)
            <a href="{{ route('admin.customers.segments', ['segment' => $key]) }}"
               class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 {{ $selectedSegment === $key ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
                <span>{{ $data['name'] }}</span>
                <span class="text-[10px] px-1.5 py-0.2 rounded-full {{ $selectedSegment === $key ? 'bg-slate-700 text-white' : 'bg-slate-200/80 text-slate-700' }}">{{ $data['count'] }}</span>
            </a>
        @endforeach
    </div>

    <!-- Main Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-slate-50/50">
            <form method="GET" action="{{ route('admin.customers.segments') }}" class="flex items-center gap-2 flex-1 max-w-sm">
                <input type="hidden" name="segment" value="{{ $selectedSegment }}">
                <div class="relative w-full">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search customer name or email..."
                           class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                </div>
                <button type="submit" class="px-3 py-2 bg-slate-200/70 text-slate-700 text-xs font-semibold rounded-xl hover:bg-slate-300">Filter</button>
            </form>
            <div class="text-xs text-slate-500">
                Showing <span class="font-bold text-slate-800">{{ $customers->count() }}</span> analyzed profiles
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50/75 border-b border-slate-200/70 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                    <tr>
                        <th class="py-3 px-4">Customer Details</th>
                        <th class="py-3 px-4">RFM Segment</th>
                        <th class="py-3 px-4 text-center">Frequency (Orders)</th>
                        <th class="py-3 px-4">Monetary Spend</th>
                        <th class="py-3 px-4">Recency (Inactivity)</th>
                        <th class="py-3 px-4">Recommended Marketing Action</th>
                        <th class="py-3 px-4 text-right">Profile</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($customers as $customer)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-900">{{ $customer->name }}</div>
                                <div class="text-[11px] text-slate-400">{{ $customer->email }}</div>
                            </td>
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-extrabold border {{ $customer->segment['badge_color'] }}">
                                    {{ $customer->segment['name'] }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center font-bold text-slate-800">
                                {{ $customer->frequency }}
                            </td>
                            <td class="py-3 px-4 font-extrabold text-slate-900">
                                ₹{{ number_format($customer->monetary, 2) }}
                            </td>
                            <td class="py-3 px-4">
                                @if($customer->recency_days !== null)
                                    <div class="font-bold text-slate-700">{{ $customer->recency_days }} days ago</div>
                                    <div class="text-[10px] text-slate-400">{{ $customer->latest_order?->format('M d, Y') }}</div>
                                @else
                                    <span class="text-slate-400 italic">No orders yet</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <span class="text-[11px] font-medium text-indigo-700 bg-indigo-50/70 px-2 py-1 rounded-md inline-block">
                                    {{ $customer->segment['action'] }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <a href="{{ route('admin.customers.show', $customer->id) }}" class="p-1.5 text-slate-500 hover:text-indigo-600 hover:bg-slate-100 rounded-lg transition-colors inline-block" title="View Customer Profile">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <svg class="w-10 h-10 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                <p class="font-semibold text-slate-600">No customers found in this segment</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

