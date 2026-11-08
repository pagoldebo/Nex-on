{{--
    ======================================================================
    ADMIN MODULE: INVENTORY FORECASTING
    ======================================================================
    File: resources/views/admin/inventory/forecasting.blade.php
    Responsibility: Back-office administration for INVENTORY FORECASTING
    Features:
      - Standard administration UI following the Nex-on design guidelines
      - Accessible controls, Tailwind CSS styling, and reactive Alpine.js components
    Security & Access Control:
      - Guarded by Laravel authentication and RBAC permission middleware
    Operations & Maintenance:
      - Integrates with master administration shell (@extends('admin.layout'))
--}}
@extends('admin.layout')

@section('title', 'Demand Forecasting & Sales Velocity Engine')

@section('content')
<div class="space-y-6 pb-12">

    <!-- Header & Action Bar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 text-[10px] font-bold rounded-md bg-indigo-50 text-indigo-600 border border-indigo-200/50 uppercase font-mono tracking-wider">Phase 2 Engine</span>
                <span class="text-xs text-slate-400 font-medium">Predictive Analytics &bull; Runout Forecasts</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 mt-1">Demand Forecasting &amp; Velocity Engine</h1>
            <p class="text-xs text-slate-500 mt-0.5">Algorithmically compute daily run rates, days to stockout, dead-stock capital, and automated replenishment orders.</p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('admin.purchase-orders.create') }}"
               class="px-3.5 py-2 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-xs flex items-center gap-2 transition">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Draft Purchase Order</span>
            </a>

            <a href="{{ route('admin.inventory.forecasting.export', request()->all()) }}"
               class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-md shadow-indigo-600/20 flex items-center gap-2 transition">
                <svg class="w-4 h-4 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                <span>Export Velocity CSV</span>
            </a>
        </div>
    </div>

    <!-- Executive KPI Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Stockout Risk Critical -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Stockout Risk (≤7 Days)</span>
                <div class="text-2xl font-black text-rose-600 mt-1 font-mono">{{ $summary['critical_count'] }}</div>
                <div class="text-[10px] text-rose-500 mt-1 font-medium">Immediate reorder required</div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold">
                ⚠️
            </div>
        </div>

        <!-- Dead Stock Capital -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Dead Stock Capital</span>
                <div class="text-2xl font-black text-amber-600 mt-1 font-mono">₹{{ number_format($summary['dead_stock_capital'], 2) }}</div>
                <div class="text-[10px] text-slate-400 mt-1">{{ $summary['dead_stock_count'] }} SKUs with 0 sales in 90d</div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

        <!-- System Daily Velocity -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Sales Velocity (Run Rate)</span>
                <div class="text-2xl font-black text-indigo-600 mt-1 font-mono">{{ $summary['total_daily_velocity'] }} <span class="text-xs text-slate-400">units/day</span></div>
                <div class="text-[10px] text-slate-400 mt-1">Weighted 7d/30d/90d consumption</div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
        </div>

        <!-- Stock Turnover Ratio -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Stock Turnover Ratio</span>
                <div class="text-2xl font-black text-slate-900 mt-1 font-mono">{{ $summary['turnover_ratio'] }}x</div>
                <div class="text-[10px] text-slate-400 mt-1">Total tied up: ₹{{ number_format($summary['total_tied_up'], 2) }}</div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                ↻
            </div>
        </div>
    </div>

    <!-- Filter & Sort Bar -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-xs">
        <form method="GET" action="{{ route('admin.inventory.forecasting') }}" class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 text-xs">
            <!-- Risk Filter Pills -->
            <div class="flex items-center gap-1.5 flex-wrap">
                <span class="text-slate-400 font-semibold mr-1 font-mono uppercase text-[11px]">Filter:</span>
                @php
                    $riskTabs = [
                        'all'          => 'All (' . $summary['total_skus'] . ')',
                        'critical'     => 'Critical (' . $summary['critical_count'] . ')',
                        'warning'      => 'Warning (' . $summary['warning_count'] . ')',
                        'dead_stock'   => 'Dead Stock (' . $summary['dead_stock_count'] . ')',
                        'overstocked'  => 'Overstocked (' . $summary['overstock_count'] . ')',
                        'healthy'      => 'Healthy (' . $summary['healthy_count'] . ')',
                    ];
                @endphp

                @foreach($riskTabs as $key => $label)
                    <button type="submit" name="risk" value="{{ $key }}"
                            class="px-3 py-1.5 rounded-lg font-medium transition {{ ($filters['risk'] ?? 'all') === $key ? 'bg-slate-900 text-white shadow-xs font-bold' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>

            <!-- Dropdown Filters & Sorting -->
            <div class="flex items-center gap-2 flex-wrap">
                <select name="category_id" onchange="this.form.submit()"
                        class="px-2.5 py-1.5 rounded-lg border border-slate-300 text-slate-800 focus:outline-none focus:border-indigo-500">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ ($filters['category_id'] ?? '') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>

                <select name="supplier_id" onchange="this.form.submit()"
                        class="px-2.5 py-1.5 rounded-lg border border-slate-300 text-slate-800 focus:outline-none focus:border-indigo-500">
                    <option value="">All Suppliers</option>
                    @foreach($suppliers as $sup)
                        <option value="{{ $sup->id }}" {{ ($filters['supplier_id'] ?? '') == $sup->id ? 'selected' : '' }}>{{ $sup->name }}</option>
                    @endforeach
                </select>

                <select name="sort" onchange="this.form.submit()"
                        class="px-2.5 py-1.5 rounded-lg border border-slate-300 text-slate-800 focus:outline-none focus:border-indigo-500">
                    <option value="risk_asc" {{ ($filters['sort'] ?? '') === 'risk_asc' ? 'selected' : '' }}>Sort: Risk Priority</option>
                    <option value="velocity_desc" {{ ($filters['sort'] ?? '') === 'velocity_desc' ? 'selected' : '' }}>Sort: Highest Velocity</option>
                    <option value="days_asc" {{ ($filters['sort'] ?? '') === 'days_asc' ? 'selected' : '' }}>Sort: Days Left (Lowest)</option>
                    <option value="stock_asc" {{ ($filters['sort'] ?? '') === 'stock_asc' ? 'selected' : '' }}>Sort: Stock (Lowest)</option>
                    <option value="capital_desc" {{ ($filters['sort'] ?? '') === 'capital_desc' ? 'selected' : '' }}>Sort: Capital Tied Up</option>
                    <option value="reorder_desc" {{ ($filters['sort'] ?? '') === 'reorder_desc' ? 'selected' : '' }}>Sort: Reorder Recommended</option>
                </select>
            </div>
        </form>
    </div>

    <!-- Forecasting & Velocity Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-900">Inventory Velocity &amp; Stockout Forecasts</h2>
            <span class="text-xs text-slate-400 font-mono">{{ count($items) }} SKUs in view</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-left text-xs">
                <thead class="bg-slate-50/70 text-slate-500 uppercase font-mono text-[10px]">
                    <tr>
                        <th class="px-6 py-3">SKU &amp; Product</th>
                        <th class="px-6 py-3 text-right">Current Stock</th>
                        <th class="px-6 py-3 text-right">7d / 30d / 90d Sold</th>
                        <th class="px-6 py-3 text-right">Velocity (Units/Day)</th>
                        <th class="px-6 py-3 text-right">Est. Days Left</th>
                        <th class="px-6 py-3">Risk Assessment</th>
                        <th class="px-6 py-3 text-right">Capital Tied Up</th>
                        <th class="px-6 py-3 text-right">Recommended Reorder</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($items as $item)
                        <tr class="hover:bg-slate-50/50 transition">
                            <!-- SKU & Product -->
                            <td class="px-6 py-3.5">
                                <div class="font-mono font-bold text-slate-900">{{ $item['sku'] }}</div>
                                <div class="text-slate-800 font-medium">{{ $item['product_name'] }}</div>
                                <div class="text-[11px] text-slate-400">{{ $item['category_name'] }} &bull; {{ $item['color_name'] }} {{ $item['size'] }}</div>
                            </td>

                            <!-- Stock -->
                            <td class="px-6 py-3.5 text-right font-mono font-bold text-slate-900 text-sm">
                                {{ $item['stock'] }}
                            </td>

                            <!-- Sales history -->
                            <td class="px-6 py-3.5 text-right font-mono text-slate-600">
                                <span class="font-bold text-slate-900">{{ $item['units_sold_7d'] }}</span> /
                                <span>{{ $item['units_sold_30d'] }}</span> /
                                <span class="text-slate-400">{{ $item['units_sold_90d'] }}</span>
                            </td>

                            <!-- Velocity -->
                            <td class="px-6 py-3.5 text-right font-mono">
                                <span class="font-bold {{ $item['velocity_daily'] > 0.5 ? 'text-indigo-600' : 'text-slate-600' }}">
                                    {{ $item['velocity_daily'] }}
                                </span>
                            </td>

                            <!-- Days Left -->
                            <td class="px-6 py-3.5 text-right font-mono font-bold">
                                @if($item['days_left'] === 999)
                                    <span class="text-slate-400">&infin; (No Sales)</span>
                                @elseif($item['days_left'] <= 7)
                                    <span class="text-rose-600 text-sm animate-pulse">{{ $item['days_left'] }} days</span>
                                @elseif($item['days_left'] <= 21)
                                    <span class="text-amber-600">{{ $item['days_left'] }} days</span>
                                @else
                                    <span class="text-emerald-600">{{ $item['days_left'] }} days</span>
                                @endif
                            </td>

                            <!-- Risk Pill -->
                            <td class="px-6 py-3.5">
                                @if($item['risk'] === 'out_of_stock')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-rose-100 text-rose-800">Out of Stock</span>
                                @elseif($item['risk'] === 'critical')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-rose-50 text-rose-700 border border-rose-200">Critical (≤7d)</span>
                                @elseif($item['risk'] === 'warning')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-amber-50 text-amber-700 border border-amber-200">Warning (≤21d)</span>
                                @elseif($item['risk'] === 'dead_stock')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-slate-100 text-slate-700 border border-slate-300">Dead Stock</span>
                                @elseif($item['risk'] === 'overstocked')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-sky-50 text-sky-700 border border-sky-200">Overstocked</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-emerald-50 text-emerald-700 border border-emerald-200">Healthy</span>
                                @endif
                            </td>

                            <!-- Capital Tied Up -->
                            <td class="px-6 py-3.5 text-right font-mono text-slate-800">
                                ₹{{ number_format($item['capital_tied_up'], 2) }}
                            </td>

                            <!-- Recommended Reorder -->
                            <td class="px-6 py-3.5 text-right font-mono">
                                @if($item['recommended_reorder'] > 0)
                                    <span class="font-extrabold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md">
                                        +{{ $item['recommended_reorder'] }} units
                                    </span>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-center text-slate-400">
                                No SKUs matched the selected filter criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
