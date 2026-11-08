{{--
    ======================================================================
    ADMIN MODULE: MARKETING DASHBOARD
    ======================================================================
    File: resources/views/admin/marketing/dashboard.blade.php
    Responsibility: Back-office administration for MARKETING DASHBOARD
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
    <!-- Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-indigo-600 uppercase tracking-wider mb-1">
                <span>Growth &amp; Acquisition</span>
                <span>•</span>
                <span class="text-slate-500 font-medium">Performance Console</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Marketing &amp; ROI Analytics</h1>
            <p class="text-sm text-slate-500 mt-0.5">Unified performance dashboard monitoring coupon redemptions, newsletter reach, push alerts, and recovery funnels.</p>
        </div>
        <div class="flex items-center gap-2.5 flex-wrap">
            <a href="{{ route('admin.coupons.index') }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition-colors">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                Manage Coupons
            </a>
            <a href="{{ route('admin.marketing.abandoned-carts') }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                Abandoned Carts
            </a>
            <a href="{{ route('admin.marketing.loyalty') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                VIP Loyalty Program
            </a>
        </div>
    </div>

    <!-- 4 High-Impact KPI Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Coupon Driven Revenue -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Coupon Driven Sales</span>
                <span class="p-2 rounded-xl bg-emerald-50 text-emerald-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-black text-emerald-700 font-mono">₹{{ number_format($couponDrivenRevenue, 2) }}</div>
                <div class="text-xs text-slate-500 mt-1">{{ $totalCouponsUsed }} orders generated via promo codes</div>
            </div>
        </div>

        <!-- Total Discounts Granted -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Discount Granted</span>
                <span class="p-2 rounded-xl bg-indigo-50 text-indigo-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                </span>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-black text-indigo-700 font-mono">₹{{ number_format($totalDiscountGranted, 2) }}</div>
                <div class="text-xs text-slate-500 mt-1">Direct promotional customer savings</div>
            </div>
        </div>

        <!-- Abandoned Cart Recovery Rate -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Cart Recovery Rate</span>
                <span class="p-2 rounded-xl bg-purple-50 text-purple-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </span>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-black text-purple-700 font-mono">{{ $recoveryRate }}%</div>
                <div class="text-xs text-slate-500 mt-1">₹{{ number_format($recoveredRevenue, 0) }} recovered from {{ $totalRecovered }} carts</div>
            </div>
        </div>

        <!-- VIP Loyalty Points Circulation -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Loyalty Points in Hand</span>
                <span class="p-2 rounded-xl bg-amber-50 text-amber-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                </span>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-black text-amber-700 font-mono">{{ number_format($totalPointsBalance) }} pts</div>
                <div class="text-xs text-slate-500 mt-1">Across {{ $loyaltyMembersCount }} enrolled VIP patrons</div>
            </div>
        </div>
    </div>

    <!-- 3 Acquisition & Channel Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Newsletter Audience -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-3">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Email Subscribers</h3>
                </div>
                <a href="{{ route('admin.newsletter.index') }}" class="text-[11px] font-semibold text-indigo-600 hover:underline">Manage</a>
            </div>
            <div>
                <div class="text-2xl font-bold text-slate-900 font-mono">{{ number_format($totalSubscribers) }}</div>
                <div class="text-xs text-slate-500 mt-0.5">+{{ $newSubscribersThisMonth }} organic signups this month</div>
            </div>
            <div class="p-3 bg-slate-50 rounded-xl text-xs text-slate-600">
                Direct email newsletter engagement drives regular Friday editorial open rates.
            </div>
        </div>

        <!-- Web Push Audience -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-3">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Web Push Alerts</h3>
                </div>
                <a href="{{ route('admin.push.index') }}" class="text-[11px] font-semibold text-indigo-600 hover:underline">Campaigns</a>
            </div>
            <div>
                <div class="text-2xl font-bold text-slate-900 font-mono">{{ number_format($totalPushSubscribers) }}</div>
                <div class="text-xs text-slate-500 mt-0.5">{{ $pushCampaignsSent }} broadcast alerts dispatched</div>
            </div>
            <div class="p-3 bg-slate-50 rounded-xl text-xs text-slate-600">
                Direct browser push alerts deliver immediate flash sale and drop reminders.
            </div>
        </div>

        <!-- Omnichannel Share -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-3">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Omnichannel Split</h3>
                </div>
                <span class="text-[11px] text-slate-400 font-medium">Orders</span>
            </div>
            <div class="space-y-2">
                <div class="flex items-center justify-between text-xs">
                    <span class="text-slate-600 font-medium">Web Storefront:</span>
                    <span class="font-bold text-slate-900 font-mono">{{ $webOrdersCount }} ({{ $webShare }}%)</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden flex">
                    <div class="bg-indigo-600 h-2" style="width: {{ $webShare }}%"></div>
                    <div class="bg-emerald-500 h-2" style="width: {{ $posShare }}%"></div>
                </div>
                <div class="flex items-center justify-between text-xs">
                    <span class="text-slate-600 font-medium">In-Store Cashier POS:</span>
                    <span class="font-bold text-slate-900 font-mono">{{ $posOrdersCount }} ({{ $posShare }}%)</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Performing Coupons Leaderboard -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900">Promo Code &amp; Coupon Leaderboard</h3>
                <p class="text-xs text-slate-500">Highest grossing discount codes ranked by redemptions and incremental revenue.</p>
            </div>
            <a href="{{ route('admin.coupons.index') }}" class="text-xs font-semibold text-indigo-600 hover:underline">View All Coupons &rarr;</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200/80 text-slate-600 font-semibold uppercase tracking-wider text-[10px]">
                        <th class="py-3 px-4">Coupon Code</th>
                        <th class="py-3 px-4">Discount Type</th>
                        <th class="py-3 px-4 text-center">Times Used</th>
                        <th class="py-3 px-4 text-right">Discount Granted</th>
                        <th class="py-3 px-4 text-right">Gross Sales Driven</th>
                        <th class="py-3 px-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($coupons as $coupon)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-1 rounded-lg bg-indigo-50 border border-indigo-200 font-mono font-bold text-indigo-700 text-xs">
                                    {{ $coupon['code'] }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 capitalize text-slate-700">
                                {{ $coupon['type'] }} ({{ $coupon['type'] === 'percentage' ? $coupon['value'] . '%' : '₹' . $coupon['value'] }})
                            </td>
                            <td class="py-3.5 px-4 text-center font-bold font-mono text-slate-900">
                                {{ $coupon['uses_count'] }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono text-rose-600 font-medium">
                                ₹{{ number_format($coupon['discount_given'], 2) }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono font-bold text-emerald-700 text-sm">
                                ₹{{ number_format($coupon['revenue_driven'], 2) }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $coupon['is_active'] ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $coupon['is_active'] ? 'Active' : 'Disabled' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">
                                No promotional coupons configured.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

