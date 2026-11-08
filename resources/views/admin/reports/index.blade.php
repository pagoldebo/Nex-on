{{--
    ======================================================================
    ADMIN MODULE: REPORTS INDEX
    ======================================================================
    File: resources/views/admin/reports/index.blade.php
    Responsibility: Back-office administration for REPORTS INDEX
    Features:
      - Standard administration UI following the Nex-on design guidelines
      - Accessible controls, Tailwind CSS styling, and reactive Alpine.js components
    Security & Access Control:
      - Guarded by Laravel authentication and RBAC permission middleware
    Operations & Maintenance:
      - Integrates with master administration shell (@extends('admin.layout'))
--}}
@extends('admin.layout')

@section('title', 'Financial Analytics & Reports Suite')

@section('content')
<div class="space-y-6 pb-12" x-data="{ customDate: '{{ $preset }}' === 'custom' }">

    <!-- Header & Action Bar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 text-[10px] font-bold rounded-md bg-indigo-50 text-indigo-600 border border-indigo-200/50 uppercase font-mono tracking-wider">Reports Suite</span>
                <span class="text-xs text-slate-400 font-medium">Phase 1 &bull; Financial &amp; Operational Analytics</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 mt-1">Analytics &amp; Executive Reports</h1>
            <p class="text-xs text-slate-500 mt-0.5">Comprehensive audit reports for revenue, product sales velocity, inventory asset valuation, and GST filing.</p>
        </div>

        <!-- Export & Print Actions -->
        <div class="flex items-center gap-2.5">
            <button onclick="window.print()" type="button"
                    class="px-3.5 py-2 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-xs flex items-center gap-2 transition">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Print Report</span>
            </button>

            @php
                $exportUrl = match($tab) {
                    'products'  => route('admin.reports.export.products', request()->all()),
                    'inventory' => route('admin.reports.export.inventory', request()->all()),
                    'gst'       => route('admin.reports.export.gst', request()->all()),
                    default     => route('admin.reports.export.sales', request()->all()),
                };
            @endphp
            <a href="{{ $exportUrl }}"
               class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-md shadow-indigo-600/20 flex items-center gap-2 transition">
                <svg class="w-4 h-4 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                <span>Export CSV</span>
            </a>
        </div>
    </div>

    <!-- Date Range & Preset Filter Bar -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-xs">
        <form method="GET" action="{{ route('admin.reports.index') }}" class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 text-xs">
            <input type="hidden" name="tab" value="{{ $tab }}">

            <!-- Presets -->
            <div class="flex items-center gap-1.5 flex-wrap">
                <span class="text-slate-400 font-semibold mr-1 font-mono uppercase text-[11px]">Period:</span>
                @php
                    $presets = [
                        'today'      => 'Today',
                        '7days'      => '7 Days',
                        '30days'     => '30 Days',
                        'this_month' => 'This Month',
                        'last_month' => 'Last Month',
                        'this_year'  => 'This Year',
                    ];
                @endphp

                @foreach($presets as $key => $label)
                    <button type="submit" name="preset" value="{{ $key }}"
                            class="px-3 py-1.5 rounded-lg font-medium transition {{ $preset === $key ? 'bg-slate-900 text-white shadow-xs font-bold' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                        {{ $label }}
                    </button>
                @endforeach

                <button type="button" @click="customDate = !customDate"
                        class="px-3 py-1.5 rounded-lg font-medium transition flex items-center gap-1.5 {{ $preset === 'custom' ? 'bg-indigo-600 text-white font-bold' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                    <span>Custom</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
            </div>

            <!-- Custom Date Inputs (collapsible) -->
            <div x-show="customDate" x-transition class="flex items-center gap-2">
                <input type="hidden" name="preset" value="custom" x-bind:disabled="!customDate">
                <input type="date" name="from_date" value="{{ $from_date }}"
                       class="px-2.5 py-1.5 rounded-lg border border-slate-300 font-mono text-slate-800 focus:outline-none focus:border-indigo-500">
                <span class="text-slate-400">to</span>
                <input type="date" name="to_date" value="{{ $to_date }}"
                       class="px-2.5 py-1.5 rounded-lg border border-slate-300 font-mono text-slate-800 focus:outline-none focus:border-indigo-500">
                <button type="submit" class="px-3 py-1.5 rounded-lg bg-indigo-600 text-white font-semibold hover:bg-indigo-700 transition">
                    Apply
                </button>
            </div>

            <div class="text-right text-[11px] text-slate-500 font-mono">
                Active Filter: <strong class="text-slate-800">{{ $from_date }}</strong> to <strong class="text-slate-800">{{ $to_date }}</strong>
            </div>
        </form>
    </div>

    <!-- Navigation Tabs -->
    <div class="border-b border-slate-200 flex items-center gap-6 text-xs font-bold">
        <a href="{{ route('admin.reports.index', array_merge(request()->all(), ['tab' => 'sales'])) }}"
           class="pb-3 border-b-2 transition flex items-center gap-2 {{ $tab === 'sales' ? 'border-indigo-600 text-indigo-600 font-extrabold' : 'border-transparent text-slate-500 hover:text-slate-900' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>Sales &amp; Revenue</span>
        </a>

        <a href="{{ route('admin.reports.index', array_merge(request()->all(), ['tab' => 'products'])) }}"
           class="pb-3 border-b-2 transition flex items-center gap-2 {{ $tab === 'products' ? 'border-indigo-600 text-indigo-600 font-extrabold' : 'border-transparent text-slate-500 hover:text-slate-900' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            <span>Product Performance</span>
        </a>

        <a href="{{ route('admin.reports.index', array_merge(request()->all(), ['tab' => 'inventory'])) }}"
           class="pb-3 border-b-2 transition flex items-center gap-2 {{ $tab === 'inventory' ? 'border-indigo-600 text-indigo-600 font-extrabold' : 'border-transparent text-slate-500 hover:text-slate-900' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            <span>Inventory Asset Valuation</span>
        </a>

        <a href="{{ route('admin.reports.index', array_merge(request()->all(), ['tab' => 'gst'])) }}"
           class="pb-3 border-b-2 transition flex items-center gap-2 {{ $tab === 'gst' ? 'border-indigo-600 text-indigo-600 font-extrabold' : 'border-transparent text-slate-500 hover:text-slate-900' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <span>GST Tax Audit (GSTR-1 &amp; 3B)</span>
        </a>
    </div>

    <!-- TAB 1: SALES & REVENUE -->
    @if($tab === 'sales' && isset($salesData))
        <div class="space-y-6">
            <!-- Metric KPI Grid -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Net Realized Revenue</span>
                    <div class="text-2xl font-black text-slate-900 mt-1 font-mono">₹{{ number_format($salesData['netSales'], 2) }}</div>
                    <div class="text-[10px] text-slate-400 mt-1">Excludes cancelled &amp; refunded</div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Gross Sales</span>
                    <div class="text-2xl font-black text-slate-700 mt-1 font-mono">₹{{ number_format($salesData['grossSales'], 2) }}</div>
                    <div class="text-[10px] text-slate-400 mt-1">{{ $salesData['totalOrders'] }} total order transactions</div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Average Order Value (AOV)</span>
                    <div class="text-2xl font-black text-indigo-600 mt-1 font-mono">₹{{ number_format($salesData['aov'], 2) }}</div>
                    <div class="text-[10px] text-slate-400 mt-1">Revenue per non-cancelled order</div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Discounts &amp; Promos</span>
                    <div class="text-2xl font-black text-rose-500 mt-1 font-mono">-₹{{ number_format($salesData['discounts'], 2) }}</div>
                    <div class="text-[10px] text-slate-400 mt-1">Coupons applied at checkout</div>
                </div>
            </div>

            <!-- Channels & Payment Methods Breakdown -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Channel Segregation (POS vs Online) -->
                <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs space-y-4">
                    <h2 class="text-sm font-bold text-slate-900">Sales Channel Breakdown</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-4 rounded-2xl bg-emerald-50/60 border border-emerald-200/60">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-emerald-800">POS Register</span>
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-emerald-100 text-emerald-700">In-Store</span>
                            </div>
                            <div class="text-xl font-black text-emerald-950 mt-2 font-mono">₹{{ number_format($salesData['posSales'], 2) }}</div>
                            <div class="text-[11px] text-emerald-700 mt-1 font-medium">{{ $salesData['posCount'] }} walk-in orders</div>
                        </div>

                        <div class="p-4 rounded-2xl bg-indigo-50/60 border border-indigo-200/60">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-indigo-800">Online Storefront</span>
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-indigo-100 text-indigo-700">Web</span>
                            </div>
                            <div class="text-xl font-black text-indigo-950 mt-2 font-mono">₹{{ number_format($salesData['onlineSales'], 2) }}</div>
                            <div class="text-[11px] text-indigo-700 mt-1 font-medium">{{ $salesData['onlineCount'] }} digital orders</div>
                        </div>
                    </div>
                </div>

                <!-- Payment Methods Revenue -->
                <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs space-y-4">
                    <h2 class="text-sm font-bold text-slate-900">Payment Gateway Distribution</h2>
                    <div class="space-y-2">
                        @forelse($salesData['paymentMethods'] as $pm)
                            <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold uppercase font-mono text-slate-800">{{ $pm->payment_method ?: 'manual' }}</span>
                                    <span class="text-slate-400 font-mono">({{ $pm->count }} orders)</span>
                                </div>
                                <span class="font-extrabold text-slate-900 font-mono">₹{{ number_format($pm->revenue, 2) }}</span>
                            </div>
                        @empty
                            <div class="text-xs text-slate-400 py-4 text-center">No payment transactions recorded for this period.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Orders Table -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="text-sm font-bold text-slate-900">Detailed Sales Ledger</h2>
                    <span class="text-xs text-slate-400 font-mono">{{ $salesData['orders']->total() }} transactions</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 text-left text-xs">
                        <thead class="bg-slate-50/70 text-slate-500 uppercase font-mono text-[10px]">
                            <tr>
                                <th class="px-6 py-3">Order Ref</th>
                                <th class="px-6 py-3">Date</th>
                                <th class="px-6 py-3">Channel</th>
                                <th class="px-6 py-3">Customer</th>
                                <th class="px-6 py-3">Payment</th>
                                <th class="px-6 py-3">Status</th>
                                <th class="px-6 py-3 text-right">Total Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($salesData['orders'] as $order)
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="px-6 py-3.5 font-mono font-bold text-indigo-600">
                                        <a href="{{ route('admin.orders.show', $order) }}" class="hover:underline">{{ $order->reference }}</a>
                                    </td>
                                    <td class="px-6 py-3.5 text-slate-500 font-mono text-[11px]">{{ $order->created_at->format('M d, Y H:i') }}</td>
                                    <td class="px-6 py-3.5">
                                        @if($order->channel === 'pos')
                                            <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[10px] font-bold">POS</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full bg-sky-50 text-sky-700 text-[10px] font-bold">ONLINE</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3.5 text-slate-800 font-medium">{{ $order->shipping_name }}</td>
                                    <td class="px-6 py-3.5 uppercase font-mono text-[11px] text-slate-600">{{ $order->payment_method }}</td>
                                    <td class="px-6 py-3.5">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase
                                            {{ $order->status === 'delivered' ? 'bg-emerald-50 text-emerald-700' : ($order->status === 'cancelled' ? 'bg-rose-50 text-rose-700' : 'bg-amber-50 text-amber-700') }}">
                                            {{ $order->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3.5 text-right font-mono font-extrabold text-slate-900">₹{{ number_format($order->total, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center text-slate-400">No transactions recorded within selected date range.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($salesData['orders']->hasPages())
                    <div class="px-6 py-3 border-t border-slate-100 bg-slate-50/50">
                        {{ $salesData['orders']->links() }}
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- TAB 2: PRODUCT PERFORMANCE -->
    @if($tab === 'products' && isset($productsData))
        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Units Sold</span>
                    <div class="text-2xl font-black text-slate-900 mt-1 font-mono">{{ number_format($productsData['totalUnitsSold']) }}</div>
                    <div class="text-[10px] text-slate-400 mt-1">Across all product lines</div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Product Gross Revenue</span>
                    <div class="text-2xl font-black text-indigo-600 mt-1 font-mono">₹{{ number_format($productsData['totalProductRevenue'], 2) }}</div>
                    <div class="text-[10px] text-slate-400 mt-1">Excludes freight and taxes</div>
                </div>
            </div>

            <!-- Category Sales Contribution -->
            <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs space-y-4">
                <h2 class="text-sm font-bold text-slate-900">Category Sales Velocity &amp; Contribution</h2>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                    @forelse($productsData['categoryBreakdown'] as $cat)
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 flex flex-col justify-between">
                            <span class="font-bold text-slate-800">{{ $cat->category_name }}</span>
                            <div class="mt-2">
                                <div class="text-base font-extrabold font-mono text-slate-900">₹{{ number_format($cat->revenue, 2) }}</div>
                                <div class="text-[10px] text-slate-500 font-mono">{{ $cat->units_sold }} units sold</div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-4 text-xs text-slate-400 text-center py-4">No category sales recorded.</div>
                    @endforelse
                </div>
            </div>

            <!-- Top Products Ranking Table -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="text-sm font-bold text-slate-900">Product Performance Ranking</h2>
                    <span class="text-xs text-slate-400 font-mono">{{ $productsData['topItems']->total() }} SKUs</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 text-left text-xs">
                        <thead class="bg-slate-50/70 text-slate-500 uppercase font-mono text-[10px]">
                            <tr>
                                <th class="px-6 py-3">Product Name</th>
                                <th class="px-6 py-3">Variant / Size</th>
                                <th class="px-6 py-3">Color</th>
                                <th class="px-6 py-3 text-right">Units Sold</th>
                                <th class="px-6 py-3 text-right">Avg Unit Price</th>
                                <th class="px-6 py-3 text-right">Total Revenue</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($productsData['topItems'] as $item)
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="px-6 py-3.5 font-bold text-slate-900">{{ $item->product_name }}</td>
                                    <td class="px-6 py-3.5 text-slate-600 font-mono">{{ $item->size ?: 'Standard' }}</td>
                                    <td class="px-6 py-3.5 text-slate-600">{{ $item->color_name ?: 'N/A' }}</td>
                                    <td class="px-6 py-3.5 text-right font-mono font-bold text-emerald-600">{{ number_format($item->total_units) }}</td>
                                    <td class="px-6 py-3.5 text-right font-mono text-slate-700">₹{{ number_format($item->avg_price, 2) }}</td>
                                    <td class="px-6 py-3.5 text-right font-mono font-black text-slate-900">₹{{ number_format($item->total_revenue, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-slate-400">No items sold within this timeframe.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($productsData['topItems']->hasPages())
                    <div class="px-6 py-3 border-t border-slate-100 bg-slate-50/50">
                        {{ $productsData['topItems']->links() }}
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- TAB 3: INVENTORY VALUATION -->
    @if($tab === 'inventory' && isset($inventoryData))
        <div class="space-y-6">
            <!-- Asset Valuation Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Retail Inventory Value</span>
                    <div class="text-2xl font-black text-slate-900 mt-1 font-mono">₹{{ number_format($inventoryData['totalRetailVal'], 2) }}</div>
                    <div class="text-[10px] text-slate-400 mt-1">{{ number_format($inventoryData['totalUnits']) }} total units on hand</div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Cost Price Asset Value (COGS)</span>
                    <div class="text-2xl font-black text-slate-700 mt-1 font-mono">₹{{ number_format($inventoryData['totalCostValue'], 2) }}</div>
                    <div class="text-[10px] text-slate-400 mt-1">Capital invested in physical stock</div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Potential Gross Margin</span>
                    <div class="text-2xl font-black text-emerald-600 mt-1 font-mono">₹{{ number_format($inventoryData['marginSpread'], 2) }}</div>
                    <div class="text-[10px] text-emerald-600 mt-1 font-semibold">{{ $inventoryData['marginPct'] }}% expected margin</div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Low Stock SKUs</span>
                    <div class="text-2xl font-black text-rose-500 mt-1 font-mono">{{ $inventoryData['lowStockCount'] }}</div>
                    <div class="text-[10px] text-slate-400 mt-1">Below safety reorder threshold</div>
                </div>
            </div>

            <!-- Inventory SKU Asset Table -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="text-sm font-bold text-slate-900">Inventory Valuation by SKU</h2>
                    <span class="text-xs text-slate-400 font-mono">{{ $inventoryData['paginatedVariants']->total() }} SKUs tracked</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 text-left text-xs">
                        <thead class="bg-slate-50/70 text-slate-500 uppercase font-mono text-[10px]">
                            <tr>
                                <th class="px-6 py-3">SKU</th>
                                <th class="px-6 py-3">Product Name</th>
                                <th class="px-6 py-3">Variant</th>
                                <th class="px-6 py-3 text-right">Stock</th>
                                <th class="px-6 py-3 text-right">Cost (₹)</th>
                                <th class="px-6 py-3 text-right">Retail (₹)</th>
                                <th class="px-6 py-3 text-right">Cost Asset Value</th>
                                <th class="px-6 py-3 text-right">Retail Asset Value</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($inventoryData['paginatedVariants'] as $v)
                                @php
                                    $cost = (float) ($v->cost_price ?: ($v->price * 0.5));
                                    $retail = (float) $v->price;
                                    $stock = max(0, (int) $v->stock);
                                @endphp
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="px-6 py-3.5 font-mono font-bold text-slate-900">{{ $v->sku }}</td>
                                    <td class="px-6 py-3.5 text-slate-800 font-medium">{{ $v->product->name ?? 'N/A' }}</td>
                                    <td class="px-6 py-3.5 text-slate-600 font-mono">{{ $v->color->name ?? '' }} / {{ $v->size }}</td>
                                    <td class="px-6 py-3.5 text-right font-mono font-bold {{ $stock <= ($v->reorder_threshold ?? 5) ? 'text-rose-600' : 'text-slate-900' }}">
                                        {{ $stock }}
                                    </td>
                                    <td class="px-6 py-3.5 text-right font-mono text-slate-600">₹{{ number_format($cost, 2) }}</td>
                                    <td class="px-6 py-3.5 text-right font-mono text-slate-800">₹{{ number_format($retail, 2) }}</td>
                                    <td class="px-6 py-3.5 text-right font-mono text-slate-700 font-semibold">₹{{ number_format($cost * $stock, 2) }}</td>
                                    <td class="px-6 py-3.5 text-right font-mono font-black text-indigo-600">₹{{ number_format($retail * $stock, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-8 text-center text-slate-400">No inventory SKU variants found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($inventoryData['paginatedVariants']->hasPages())
                    <div class="px-6 py-3 border-t border-slate-100 bg-slate-50/50">
                        {{ $inventoryData['paginatedVariants']->links() }}
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- TAB 4: GST TAX AUDIT (GSTR-1 & 3B) -->
    @if($tab === 'gst' && isset($gstData))
        <div class="space-y-6">
            <!-- GST Summary Metrics -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Taxable Turnover</span>
                    <div class="text-2xl font-black text-slate-900 mt-1 font-mono">₹{{ number_format($gstData['totalTaxableValue'], 2) }}</div>
                    <div class="text-[10px] text-slate-400 mt-1">Base turnover subject to GST</div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total GST Collected</span>
                    <div class="text-2xl font-black text-indigo-600 mt-1 font-mono">₹{{ number_format($gstData['totalTax'], 2) }}</div>
                    <div class="text-[10px] text-slate-400 mt-1">18% standard rate inclusive</div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">CGST + SGST (Intra-state)</span>
                    <div class="text-2xl font-black text-emerald-600 mt-1 font-mono">₹{{ number_format($gstData['totalCgst'] + $gstData['totalSgst'], 2) }}</div>
                    <div class="text-[10px] text-slate-400 mt-1">CGST: ₹{{ number_format($gstData['totalCgst'], 2) }} | SGST: ₹{{ number_format($gstData['totalSgst'], 2) }}</div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">IGST (Inter-state)</span>
                    <div class="text-2xl font-black text-amber-500 mt-1 font-mono">₹{{ number_format($gstData['totalIgst'], 2) }}</div>
                    <div class="text-[10px] text-slate-400 mt-1">Dispatched outside {{ $gstData['storeState'] }}</div>
                </div>
            </div>

            <!-- Tax Invoice Register Table -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">B2C Outward Tax Supply Register (GSTR-1)</h2>
                        <span class="text-[11px] text-slate-500">Store Origin State: <strong class="text-slate-800">{{ $gstData['storeState'] }}</strong></span>
                    </div>
                    <span class="text-xs text-slate-400 font-mono">{{ $gstData['orders']->total() }} tax invoices</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 text-left text-xs">
                        <thead class="bg-slate-50/70 text-slate-500 uppercase font-mono text-[10px]">
                            <tr>
                                <th class="px-6 py-3">Invoice Ref</th>
                                <th class="px-6 py-3">Date</th>
                                <th class="px-6 py-3">Place of Supply</th>
                                <th class="px-6 py-3 text-right">Invoice Value</th>
                                <th class="px-6 py-3 text-right">Taxable Value</th>
                                <th class="px-6 py-3 text-right">CGST (9%)</th>
                                <th class="px-6 py-3 text-right">SGST (9%)</th>
                                <th class="px-6 py-3 text-right">IGST (18%)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($gstData['orders'] as $ord)
                                @php
                                    $invTotal = (float) $ord->total;
                                    $taxable  = round($invTotal / 1.18, 2);
                                    $tax      = round($invTotal - $taxable, 2);
                                    $isInterstate = strcasecmp(trim($ord->shipping_state ?? ''), trim($gstData['storeState'])) !== 0;
                                    $cgst = $isInterstate ? 0 : round($tax / 2, 2);
                                    $sgst = $isInterstate ? 0 : round($tax - $cgst, 2);
                                    $igst = $isInterstate ? $tax : 0;
                                @endphp
                                <tr class="hover:bg-slate-50/50 transition font-mono">
                                    <td class="px-6 py-3.5 font-bold text-indigo-600">{{ $ord->reference }}</td>
                                    <td class="px-6 py-3.5 text-slate-500 text-[11px]">{{ $ord->created_at->format('Y-m-d') }}</td>
                                    <td class="px-6 py-3.5 font-sans font-medium text-slate-800">
                                        {{ $ord->shipping_state ?: 'Unknown' }}
                                        <span class="ml-1 text-[10px] font-mono font-bold {{ $isInterstate ? 'text-amber-600' : 'text-emerald-600' }}">
                                            ({{ $isInterstate ? 'INTER' : 'INTRA' }})
                                        </span>
                                    </td>
                                    <td class="px-6 py-3.5 text-right font-bold text-slate-900">₹{{ number_format($invTotal, 2) }}</td>
                                    <td class="px-6 py-3.5 text-right text-slate-600">₹{{ number_format($taxable, 2) }}</td>
                                    <td class="px-6 py-3.5 text-right text-slate-600">₹{{ number_format($cgst, 2) }}</td>
                                    <td class="px-6 py-3.5 text-right text-slate-600">₹{{ number_format($sgst, 2) }}</td>
                                    <td class="px-6 py-3.5 text-right text-slate-600 font-bold {{ $igst > 0 ? 'text-amber-600' : '' }}">₹{{ number_format($igst, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-8 text-center text-slate-400">No tax invoices recorded within date range.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($gstData['orders']->hasPages())
                    <div class="px-6 py-3 border-t border-slate-100 bg-slate-50/50">
                        {{ $gstData['orders']->links() }}
                    </div>
                @endif
            </div>
        </div>
    @endif

</div>
@endsection
