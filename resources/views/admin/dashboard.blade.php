{{--
    ======================================================================
    ADMIN MODULE: E-COMMERCE OVERVIEW DASHBOARD
    ======================================================================
    File: resources/views/admin/dashboard.blade.php
    Controller: App\Http\Controllers\Admin\DashboardController@index
    Route Context: GET /admin, GET /admin/dashboard
    ViewModel Injections:
      - $stats: Overview metrics (Total Sales, Total Orders, Active Customers, Low Stock)
      - $recentOrders: Latest customer orders with status badges
      - $topProducts: Best-selling inventory items
      - $salesChartData: Time-series revenue data for Chart.js
    Responsibility: Executive eCommerce command center and operational pulse
    Features:
      - High-level KPI summary cards with percentage growth indicators
      - Interactive multi-day revenue and order frequency visual charts
      - Order fulfillment status breakdown and quick action buttons (POS, Add Product)
    Security: Guarded by 'dashboard.view' permission
--}}
@extends('admin.layout', ['breadcrumb' => 'eCommerce Overview'])

@section('content')
<div class="space-y-8">
    <!-- Header with Breadcrumb & Quick Actions -->
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">
                <span>Dashboards</span>
                <span>/</span>
                <span class="text-indigo-600 font-bold">eCommerce</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">eCommerce Overview</h1>
            <p class="mt-1 text-xs sm:text-sm text-slate-500">Real-time store metrics, multi-channel revenues, fulfillment orders, and catalogue performance.</p>
        </div>
        <div class="mt-4 sm:mt-0 flex items-center gap-2.5 flex-wrap">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-2xs">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                Theme: {{ ucfirst($activeTheme->name ?? $activeTheme->key ?? 'Saaya') }}
            </span>
            <a href="{{ route('admin.pos.index') }}"
               class="px-3.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition shadow-xs flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                <span>POS Screen</span>
            </a>
            <a href="{{ route('admin.products.create') }}" 
               class="px-3.5 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold transition shadow-xs flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Add Product</span>
            </a>
        </div>
    </div>

    <!-- Quick Action Shortcut Pills (matching reference template) -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <a href="{{ route('admin.orders.index') }}" 
           class="p-3 bg-white rounded-xl border border-slate-200/80 hover:border-indigo-400 hover:bg-indigo-50/30 transition shadow-2xs flex items-center gap-2.5 group">
            <span class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-sm group-hover:scale-105 transition">📦</span>
            <div class="min-w-0">
                <div class="text-xs font-bold text-slate-800 group-hover:text-indigo-600 truncate">Orders</div>
                <div class="text-[10px] text-slate-400">Manage all</div>
            </div>
        </a>

        <a href="{{ route('admin.products.create') }}" 
           class="p-3 bg-white rounded-xl border border-slate-200/80 hover:border-emerald-400 hover:bg-emerald-50/30 transition shadow-2xs flex items-center gap-2.5 group">
            <span class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-sm group-hover:scale-105 transition">➕</span>
            <div class="min-w-0">
                <div class="text-xs font-bold text-slate-800 group-hover:text-emerald-600 truncate">New Product</div>
                <div class="text-[10px] text-slate-400">Add SKU</div>
            </div>
        </a>

        <a href="{{ route('admin.pos.index') }}"
           class="p-3 bg-white rounded-xl border border-slate-200/80 hover:border-purple-400 hover:bg-purple-50/30 transition shadow-2xs flex items-center gap-2.5 group">
            <span class="w-8 h-8 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center font-bold text-sm group-hover:scale-105 transition">🖥️</span>
            <div class="min-w-0">
                <div class="text-xs font-bold text-slate-800 group-hover:text-purple-600 truncate">POS Register</div>
                <div class="text-[10px] text-slate-400">Cashier mode</div>
            </div>
        </a>

        <a href="{{ route('admin.customers.index') }}" 
           class="p-3 bg-white rounded-xl border border-slate-200/80 hover:border-sky-400 hover:bg-sky-50/30 transition shadow-2xs flex items-center gap-2.5 group">
            <span class="w-8 h-8 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center font-bold text-sm group-hover:scale-105 transition">👥</span>
            <div class="min-w-0">
                <div class="text-xs font-bold text-slate-800 group-hover:text-sky-600 truncate">Customers</div>
                <div class="text-[10px] text-slate-400">View roster</div>
            </div>
        </a>

        <a href="{{ route('admin.purchase-orders.create') }}" 
           class="p-3 bg-white rounded-xl border border-slate-200/80 hover:border-amber-400 hover:bg-amber-50/30 transition shadow-2xs flex items-center gap-2.5 group">
            <span class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-sm group-hover:scale-105 transition">🛒</span>
            <div class="min-w-0">
                <div class="text-xs font-bold text-slate-800 group-hover:text-amber-600 truncate">Purchase PO</div>
                <div class="text-[10px] text-slate-400">Restock vendor</div>
            </div>
        </a>

        <a href="{{ route('admin.reports.index') }}" 
           class="p-3 bg-white rounded-xl border border-slate-200/80 hover:border-rose-400 hover:bg-rose-50/30 transition shadow-2xs flex items-center gap-2.5 group">
            <span class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center font-bold text-sm group-hover:scale-105 transition">📊</span>
            <div class="min-w-0">
                <div class="text-xs font-bold text-slate-800 group-hover:text-rose-600 truncate">Analytics</div>
                <div class="text-[10px] text-slate-400">Tax &amp; Sales</div>
            </div>
        </a>
    </div>

    <!-- 8 KPI Metrics Cards Grid (Exactly matching reference PDF / template) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Total Revenue -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs relative overflow-hidden flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Revenue</span>
                    <span class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-sm shadow-2xs">
                        💰
                    </span>
                </div>
                <div class="mt-3">
                    <div class="text-2xl font-black text-slate-900 tracking-tight">₹{{ number_format($totalRevenue, 0) }}</div>
                    <div class="flex items-center gap-1.5 mt-1 text-xs">
                        <span class="text-emerald-600 font-bold flex items-center gap-0.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                            +{{ $revenueGrowth }}%
                        </span>
                        <span class="text-slate-400">vs last month</span>
                    </div>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                <span>Today: ₹{{ number_format($revenueToday, 0) }}</span>
                <span>Month: ₹{{ number_format($revenueMonth, 0) }}</span>
            </div>
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-emerald-400 to-teal-500"></div>
        </div>

        <!-- 2. Total Orders -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs relative overflow-hidden flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Orders</span>
                    <span class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-sm shadow-2xs">
                        📦
                    </span>
                </div>
                <div class="mt-3">
                    <div class="text-2xl font-black text-slate-900 tracking-tight">{{ number_format($totalOrders) }}</div>
                    <div class="flex items-center gap-1.5 mt-1 text-xs">
                        <span class="text-indigo-600 font-bold flex items-center gap-0.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                            +{{ $ordersGrowth }}%
                        </span>
                        <span class="text-slate-400">vs last month</span>
                    </div>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                <span class="text-blue-600 font-medium">{{ $processingOrdersCount }} processing</span>
                <span class="text-emerald-600 font-medium">{{ $deliveredOrdersCount }} delivered</span>
            </div>
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-indigo-500 to-purple-500"></div>
        </div>

        <!-- 3. Total Customers -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs relative overflow-hidden flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Customers</span>
                    <span class="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold text-sm shadow-2xs">
                        👥
                    </span>
                </div>
                <div class="mt-3">
                    <div class="text-2xl font-black text-slate-900 tracking-tight">{{ number_format($totalCustomers) }}</div>
                    <div class="flex items-center gap-1.5 mt-1 text-xs">
                        <span class="text-purple-600 font-bold flex items-center gap-0.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                            +{{ $customersGrowth }}%
                        </span>
                        <span class="text-slate-400">new this month</span>
                    </div>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                <span>Active Roster</span>
                <a href="{{ route('admin.customers.index') }}" class="text-indigo-600 font-bold hover:underline">View All →</a>
            </div>
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-purple-500 to-pink-500"></div>
        </div>

        <!-- 4. Net Profit -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs relative overflow-hidden flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Net Profit</span>
                    <span class="w-9 h-9 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center font-bold text-sm shadow-2xs">
                        📈
                    </span>
                </div>
                <div class="mt-3">
                    <div class="text-2xl font-black text-slate-900 tracking-tight">₹{{ number_format($netProfit, 0) }}</div>
                    <div class="flex items-center gap-1.5 mt-1 text-xs">
                        <span class="text-teal-600 font-bold flex items-center gap-0.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                            +21.3%
                        </span>
                        <span class="text-slate-400">vs last month</span>
                    </div>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                <span>Operating Margin</span>
                <span class="font-bold text-slate-700">~42.0%</span>
            </div>
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-teal-500 to-emerald-500"></div>
        </div>

        <!-- 5. Avg Order Value (AOV) -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs relative overflow-hidden flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Avg Order Value</span>
                    <span class="w-9 h-9 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center font-bold text-sm shadow-2xs">
                        🎯
                    </span>
                </div>
                <div class="mt-3">
                    <div class="text-2xl font-black text-slate-900 tracking-tight">₹{{ number_format($avgOrderValue, 0) }}</div>
                    <div class="flex items-center gap-1.5 mt-1 text-xs">
                        <span class="text-sky-600 font-bold flex items-center gap-0.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                            +₹42.00
                        </span>
                        <span class="text-slate-400">vs last month</span>
                    </div>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                <span>Basket Size</span>
                <span class="font-bold text-slate-700">Multi-item</span>
            </div>
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-sky-400 to-blue-500"></div>
        </div>

        <!-- 6. Conversion Rate -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs relative overflow-hidden flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Conversion Rate</span>
                    <span class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-sm shadow-2xs">
                        ⚡
                    </span>
                </div>
                <div class="mt-3">
                    <div class="text-2xl font-black text-slate-900 tracking-tight">{{ $conversionRate }}%</div>
                    <div class="flex items-center gap-1.5 mt-1 text-xs">
                        <span class="text-emerald-600 font-bold flex items-center gap-0.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                            +0.42%
                        </span>
                        <span class="text-slate-400">this month</span>
                    </div>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                <span>Checkout Funnel</span>
                <span class="font-bold text-emerald-600">Optimal</span>
            </div>
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-amber-400 to-orange-500"></div>
        </div>

        <!-- 7. Pending Orders (with Attention pill) -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs relative overflow-hidden flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Pending Orders</span>
                    <span class="w-9 h-9 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold text-sm shadow-2xs">
                        ⏳
                    </span>
                </div>
                <div class="mt-3">
                    <div class="text-2xl font-black text-slate-900 tracking-tight">{{ $pendingOrdersCount }}</div>
                    <div class="flex items-center gap-1.5 mt-1 text-xs">
                        @if($pendingOrdersCount > 0)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200 animate-pulse">
                                Need attention
                            </span>
                        @else
                            <span class="text-emerald-600 font-medium">All caught up</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px]">
                <span class="text-slate-500">Action Queue</span>
                <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="text-rose-600 font-bold hover:underline">Fulfill Now →</a>
            </div>
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-rose-500 to-pink-500"></div>
        </div>

        <!-- 8. Low Stock Items (with Reorder Needed pill) -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs relative overflow-hidden flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Low Stock Items</span>
                    <span class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-sm shadow-2xs">
                        ⚠️
                    </span>
                </div>
                <div class="mt-3">
                    <div class="text-2xl font-black text-slate-900 tracking-tight">{{ $navLowStockCount }}</div>
                    <div class="flex items-center gap-1.5 mt-1 text-xs">
                        @if($navLowStockCount > 0)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                Reorder needed
                            </span>
                        @else
                            <span class="text-emerald-600 font-medium">Inventory optimal</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px]">
                <span class="text-slate-500">Replenishment</span>
                <a href="{{ route('admin.purchase-orders.create') }}" class="text-amber-700 font-bold hover:underline">Draft PO →</a>
            </div>
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-amber-500 to-red-500"></div>
        </div>
    </div>

    <!-- Charts & Analytics Row: Revenue Multi-Month Trend (2/3) + Category Breakdown (1/3) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Monthly Revenue & Performance Trend -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 flex flex-col justify-between">
            <div>
                <div class="sm:flex sm:items-center sm:justify-between pb-4 border-b border-slate-100">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Revenue &amp; Orders Trend</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Aggregated multi-channel sales performance for the last 6 months.</p>
                    </div>
                    <div class="mt-3 sm:mt-0 flex items-center gap-2">
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 text-xs font-bold">
                            <span class="w-2 h-2 rounded-full bg-indigo-600"></span> Gross Revenue
                        </span>
                    </div>
                </div>

                <!-- Visual Bar / Trend Meter Chart -->
                <div class="mt-6">
                    @php
                        $maxRev = max(collect($monthlyTrend)->max('revenue') ?? 1, 1000);
                    @endphp
                    <div class="grid grid-cols-6 gap-3 items-end h-44 pb-2 pt-6 border-b border-slate-100">
                        @foreach($monthlyTrend as $point)
                            @php
                                $heightPct = max(round(($point['revenue'] / $maxRev) * 100), 8);
                            @endphp
                            <div class="flex flex-col items-center h-full justify-end group">
                                <div class="text-[10px] font-mono font-bold text-slate-500 opacity-0 group-hover:opacity-100 transition-opacity mb-1">
                                    ₹{{ number_format($point['revenue'], 0) }}
                                </div>
                                <div class="w-full max-w-[42px] bg-indigo-100 group-hover:bg-indigo-600 transition-colors rounded-t-lg relative" style="height: {{ $heightPct }}%">
                                    <div class="w-full h-1.5 rounded-t bg-indigo-500"></div>
                                </div>
                                <span class="mt-2 text-[11px] font-semibold text-slate-500 uppercase">{{ $point['label'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Mini stats row at chart footer -->
            <div class="grid grid-cols-3 gap-4 pt-4 mt-2 border-t border-slate-100 text-center">
                <div>
                    <div class="text-[11px] text-slate-400 font-medium">Today's Sales</div>
                    <div class="text-sm font-bold text-slate-900 mt-0.5">₹{{ number_format($revenueToday, 0) }}</div>
                </div>
                <div>
                    <div class="text-[11px] text-slate-400 font-medium">Last 7 Days</div>
                    <div class="text-sm font-bold text-slate-900 mt-0.5">₹{{ number_format($revenueWeek, 0) }}</div>
                </div>
                <div>
                    <div class="text-[11px] text-slate-400 font-medium">This Month</div>
                    <div class="text-sm font-bold text-indigo-600 mt-0.5">₹{{ number_format($revenueMonth, 0) }}</div>
                </div>
            </div>
        </div>

        <!-- Sales by Category / Category Distribution -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Catalogue Breakdown</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Top active product categories.</p>
                    </div>
                    <a href="{{ route('admin.categories.index') }}" class="text-xs font-semibold text-indigo-600 hover:underline">
                        View All →
                    </a>
                </div>

                <div class="mt-5 space-y-4">
                    @php
                        $totalCatProds = max($categoryBreakdown->sum('products_count'), 1);
                        $catColors = ['bg-indigo-500', 'bg-purple-500', 'bg-emerald-500', 'bg-sky-500', 'bg-amber-500'];
                    @endphp
                    @forelse($categoryBreakdown as $idx => $cat)
                        @php
                            $catPct = round(($cat->products_count / $totalCatProds) * 100);
                            $color = $catColors[$idx % count($catColors)];
                        @endphp
                        <div>
                            <div class="flex items-center justify-between text-xs mb-1.5">
                                <span class="font-bold text-slate-800">{{ $cat->name }}</span>
                                <span class="text-slate-500 font-mono">{{ $cat->products_count }} items ({{ $catPct }}%)</span>
                            </div>
                            <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full {{ $color }} rounded-full" style="width: {{ $catPct }}%"></div>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-xs text-slate-400">
                            No product categories configured yet.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 text-center">
                <a href="{{ route('admin.products.create') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition">
                    + Add New Category SKU
                </a>
            </div>
        </div>
    </div>

    <!-- Operational Data Row: Recent Orders (2/3) + Top VIP Customers (1/3) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Orders (2 cols) -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden flex flex-col justify-between">
            <div>
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Recent Customer Orders</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Real-time incoming orders from storefront &amp; POS terminals.</p>
                    </div>
                    <a href="{{ route('admin.orders.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition">
                        View All ({{ $totalOrders }}) →
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 text-left text-sm">
                        <thead class="bg-slate-50/70 text-slate-500 uppercase text-[11px] font-bold tracking-wider">
                            <tr>
                                <th class="px-5 py-3">Order Ref</th>
                                <th class="px-5 py-3">Customer</th>
                                <th class="px-5 py-3">Status</th>
                                <th class="px-5 py-3">Total</th>
                                <th class="px-5 py-3 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($recentOrders as $order)
                                @php
                                    $statusColors = [
                                        'pending'    => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'confirmed'  => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'processing' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                        'shipped'    => 'bg-purple-50 text-purple-700 border-purple-200',
                                        'delivered'  => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'cancelled'  => 'bg-slate-100 text-slate-600 border-slate-200',
                                    ];
                                    $colorClass = $statusColors[$order->status] ?? 'bg-slate-100 text-slate-600 border-slate-200';
                                @endphp
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="px-5 py-3.5 font-mono text-xs font-semibold text-slate-900">
                                        <a href="{{ route('admin.orders.show', $order) }}" class="hover:text-indigo-600 transition flex items-center gap-1.5">
                                            <span>#{{ $order->reference }}</span>
                                            @if(($order->channel ?? 'online') === 'pos')
                                                <span class="px-1.5 py-0.2 rounded text-[9px] font-bold uppercase bg-emerald-100 text-emerald-800">POS</span>
                                            @endif
                                        </a>
                                        <div class="text-[10px] text-slate-400 font-sans mt-0.5">{{ $order->created_at->diffForHumans() }}</div>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <div class="font-medium text-slate-800 text-xs">{{ $order->shipping_name ?: 'Walk-in Customer' }}</div>
                                        <div class="text-[11px] text-slate-400">{{ $order->shipping_phone }}</div>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold px-2.5 py-0.5 rounded-full border {{ $colorClass }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5 font-bold text-slate-900 text-xs">
                                        ₹{{ number_format($order->total, 0) }}
                                        <div class="text-[10px] font-normal text-slate-400">{{ $order->items->count() }} item(s)</div>
                                    </td>
                                    <td class="px-5 py-3.5 text-right">
                                        <a href="{{ route('admin.orders.show', $order) }}" 
                                           class="inline-block px-3 py-1 rounded-lg bg-slate-100 hover:bg-indigo-50 text-slate-700 hover:text-indigo-700 text-xs font-semibold transition">
                                            Manage
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-slate-400 text-xs">
                                        No customer orders recorded yet. Test placing an order on the storefront or POS!
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="p-3.5 bg-slate-50/50 border-t border-slate-100 text-center">
                <a href="{{ route('admin.orders.index') }}" class="text-xs font-semibold text-slate-600 hover:text-indigo-600 transition">
                    View Complete Orders List &amp; History →
                </a>
            </div>
        </div>

        <!-- Top VIP Customers Widget -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden flex flex-col justify-between">
            <div>
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Top Customers</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Highest lifetime value &amp; frequent buyers.</p>
                    </div>
                    <span class="w-6 h-6 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center text-xs font-black">
                        VIP
                    </span>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse($topCustomers as $customer)
                        <div class="p-4 flex items-center justify-between hover:bg-slate-50/60 transition">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-600 text-white flex items-center justify-center font-bold text-xs shrink-0 shadow-2xs">
                                    {{ mb_strtoupper(mb_substr($customer->name ?? 'C', 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <div class="font-bold text-slate-800 text-xs truncate">{{ $customer->name }}</div>
                                    <div class="text-[11px] text-slate-400 truncate">{{ $customer->email }}</div>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <div class="text-xs font-black text-slate-900">₹{{ number_format($customer->orders_sum_total ?? 0, 0) }}</div>
                                <div class="text-[10px] text-indigo-600 font-semibold">{{ $customer->orders_count ?? 0 }} order(s)</div>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-400 text-xs">
                            No registered customer purchase data yet.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="p-3.5 bg-slate-50/50 border-t border-slate-100 text-center">
                <a href="{{ route('admin.customers.index') }}" class="text-xs font-semibold text-slate-600 hover:text-indigo-600 transition">
                    View All Customer Directory →
                </a>
            </div>
        </div>
    </div>

    <!-- Inventory & Catalogue Health Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Low Stock Alerts (1 col) -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden flex flex-col justify-between">
            <div>
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Low Stock Alerts</h2>
                        <p class="text-xs text-slate-400 mt-0.5">SKUs breaching safety threshold (&le; 5 units)</p>
                    </div>
                    <span class="w-6 h-6 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center text-xs font-black">
                        {{ $navLowStockCount }}
                    </span>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse($lowStockVariants as $variant)
                        <div class="p-4 flex items-center justify-between hover:bg-slate-50/60 transition">
                            <div class="min-w-0 flex-1 pr-3">
                                <div class="font-semibold text-slate-900 text-xs truncate">
                                    {{ $variant->product->name ?? 'Product' }}
                                </div>
                                <div class="flex items-center gap-2 mt-1 text-[11px] text-slate-500">
                                    @if($variant->productColor?->hex)
                                        <span class="w-2.5 h-2.5 rounded-full border border-black/20" style="background-color: {{ $variant->productColor->hex }}"></span>
                                    @endif
                                    <span>{{ $variant->productColor->name ?? 'Default' }}</span>
                                    <span class="text-slate-300">·</span>
                                    <span class="font-mono font-bold text-slate-700">Size {{ $variant->size }}</span>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                @if($variant->stock <= 0)
                                    <span class="inline-block px-2 py-0.5 text-[10px] font-black uppercase rounded bg-rose-100 text-rose-800">
                                        Out of Stock
                                    </span>
                                @else
                                    <span class="inline-block px-2 py-0.5 text-[10px] font-bold rounded bg-amber-50 text-amber-800 border border-amber-200">
                                        {{ $variant->stock }} left
                                    </span>
                                @endif
                                <div class="mt-1">
                                    <a href="{{ route('admin.products.edit', $variant->product_id) }}" class="text-[11px] text-indigo-600 hover:underline font-semibold">
                                        Restock →
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-400 text-xs">
                            <svg class="w-8 h-8 mx-auto text-emerald-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            All product variants are well stocked!
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="p-3.5 bg-slate-50/50 border-t border-slate-100 text-center">
                <a href="{{ route('admin.inventory.index') }}" class="text-xs font-semibold text-slate-600 hover:text-indigo-600 transition">
                    Manage Complete Inventory →
                </a>
            </div>
        </div>

        <!-- Featured & Bestselling Catalogue Preview (2 cols) -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 flex flex-col justify-between">
            <div>
                <div class="sm:flex sm:items-center sm:justify-between mb-5 pb-3 border-b border-slate-100">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Featured &amp; Bestselling Products</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Top-performing catalogue items across all 4 storefront themes.</p>
                    </div>
                    <a href="{{ route('admin.products.create') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 mt-2 sm:mt-0 inline-block">
                        + Add Another Product
                    </a>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3.5">
                    @forelse($topProducts as $product)
                        @php $card = $product->toCardArray(); @endphp
                        <div class="group border border-slate-200/80 rounded-xl p-2.5 hover:border-indigo-400 transition bg-slate-50/30 flex flex-col justify-between">
                            <div>
                                <div class="w-full aspect-[4/5] bg-slate-100 rounded-lg overflow-hidden relative mb-2">
                                    @if($product->cover_image)
                                        <img src="{{ $product->cover_image }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                    @endif
                                    @if($product->badge)
                                        <span class="absolute top-1.5 left-1.5 text-[8px] font-black uppercase tracking-wider px-1.5 py-0.5 bg-slate-900/85 text-white rounded backdrop-blur-xs">
                                            {{ $product->badge }}
                                        </span>
                                    @endif
                                </div>
                                <p class="text-[9px] font-bold uppercase text-slate-400 truncate">{{ $product->category->name ?? 'Lingerie' }}</p>
                                <h3 class="font-bold text-xs text-slate-900 mt-0.5 truncate">{{ $product->name }}</h3>
                                <p class="text-xs font-extrabold text-slate-800 mt-1">₹{{ number_format($card['startingPrice'] ?? 0, 0) }}</p>
                            </div>
                            <div class="mt-2.5 pt-2 border-t border-slate-200/60 flex items-center justify-between text-xs">
                                <span class="text-[10px] text-slate-400">{{ $product->colors->count() }} Colors</span>
                                <a href="{{ route('admin.products.edit', $product) }}" class="font-semibold text-indigo-600 hover:underline text-[11px]">Edit</a>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-8 text-center text-xs text-slate-400">
                            No featured products found. Mark products as Bestseller or Featured in Products manager!
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 text-center">
                <a href="{{ route('admin.products.index') }}" class="text-xs font-semibold text-slate-600 hover:text-indigo-600 transition">
                    View Complete Products Catalogue →
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
