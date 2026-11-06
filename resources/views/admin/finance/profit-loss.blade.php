@extends('admin.layout')

@section('content')
<div class="space-y-6">
    <!-- Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-indigo-600 uppercase tracking-wider mb-1">
                <span>Executive Reports</span>
                <span>•</span>
                <span class="text-slate-500 font-medium">Income Statement</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Profit &amp; Loss (P&amp;L) Statement</h1>
            <p class="text-sm text-slate-500 mt-0.5">Automated financial ledger computing Revenue, COGS, Gross Profit, Operating Overhead, and Net Margin.</p>
        </div>
        <div class="flex items-center gap-2.5 flex-wrap">
            <a href="{{ route('admin.finance.pl.export', ['period' => $period, 'from_date' => request('from_date'), 'to_date' => request('to_date')]) }}" 
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200/80 transition-colors shadow-2xs">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Export CSV
            </a>
            <a href="{{ route('admin.finance.pl.print', ['period' => $period, 'from_date' => request('from_date'), 'to_date' => request('to_date')]) }}" target="_blank"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-white bg-slate-900 hover:bg-slate-800 shadow-sm transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print Formal P&amp;L
            </a>
        </div>
    </div>

    <!-- Reporting Period Filter Selector -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex flex-wrap items-center justify-between gap-3">
        <form method="GET" action="{{ route('admin.finance.pl') }}" class="flex flex-wrap items-center gap-2">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider mr-1">Period:</span>
            @foreach(['this_month' => 'This Month', 'last_month' => 'Last Month', 'current_quarter' => 'Current Quarter', 'financial_year' => 'FY 2026-27 (YTD)', 'all' => 'All Time'] as $key => $lbl)
                <button type="submit" name="period" value="{{ $key }}"
                        class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all {{ $period === $key ? 'bg-indigo-600 text-white shadow-2xs' : 'bg-slate-50 text-slate-600 hover:bg-slate-100 border border-slate-200/70' }}">
                    {{ $lbl }}
                </button>
            @endforeach
        </form>

        <div class="text-xs font-medium text-slate-500">
            Statement Date Range: <span class="font-bold text-slate-800">{{ $startDate->format('d M Y') }}</span> — <span class="font-bold text-slate-800">{{ $endDate->format('d M Y') }}</span>
        </div>
    </div>

    <!-- Executive KPI Row -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Net Operating Revenue -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Net Operating Revenue</span>
            <div class="mt-2 text-2xl font-black text-slate-900 font-mono">₹{{ number_format($netRevenue, 2) }}</div>
            <div class="text-xs text-slate-500 mt-1">Total revenue after coupons &amp; returns</div>
        </div>

        <!-- Gross Profit & Margin -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Gross Profit</span>
                <span class="px-2 py-0.5 rounded-md text-[11px] font-bold bg-emerald-50 text-emerald-700">
                    {{ number_format($grossMarginPercent, 1) }}% Margin
                </span>
            </div>
            <div class="mt-2 text-2xl font-black text-emerald-700 font-mono">₹{{ number_format($grossProfit, 2) }}</div>
            <div class="text-xs text-slate-500 mt-1">Revenue minus inventory COGS</div>
        </div>

        <!-- Operating Income (EBITDA) -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Operating Profit (EBITDA)</span>
                <span class="px-2 py-0.5 rounded-md text-[11px] font-bold {{ $ebitda >= 0 ? 'bg-indigo-50 text-indigo-700' : 'bg-rose-50 text-rose-700' }}">
                    {{ number_format($operatingMarginPercent, 1) }}%
                </span>
            </div>
            <div class="mt-2 text-2xl font-black {{ $ebitda >= 0 ? 'text-indigo-600' : 'text-rose-600' }} font-mono">
                ₹{{ number_format($ebitda, 2) }}
            </div>
            <div class="text-xs text-slate-500 mt-1">Gross profit minus OPEX</div>
        </div>

        <!-- Net Profit After Tax -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Net Profit (PAT)</span>
                <span class="px-2 py-0.5 rounded-md text-[11px] font-bold {{ $netProfit >= 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                    {{ number_format($netMarginPercent, 1) }}% Net
                </span>
            </div>
            <div class="mt-2 text-2xl font-black {{ $netProfit >= 0 ? 'text-emerald-700' : 'text-rose-600' }} font-mono">
                ₹{{ number_format($netProfit, 2) }}
            </div>
            <div class="text-xs text-slate-500 mt-1">After tax provision &amp; all liabilities</div>
        </div>
    </div>

    <!-- The Structured P&L Statement -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Consolidated Income Statement</h2>
                <p class="text-xs text-slate-500">Compliant with Indian Accounting Standards (Ind AS) for retail merchandising.</p>
            </div>
            <span class="px-3 py-1 rounded-lg text-xs font-semibold bg-slate-100 text-slate-700">Currency: INR (₹)</span>
        </div>

        <div class="divide-y divide-slate-100 text-xs">
            <!-- SECTION 1: OPERATING REVENUE -->
            <div class="p-6 space-y-3 bg-slate-50/40">
                <div class="flex items-center justify-between text-xs font-bold uppercase tracking-wider text-slate-500">
                    <span>1. Operating Revenue &amp; Turnover</span>
                    <span>Amount (₹)</span>
                </div>
                <div class="space-y-2 pl-3">
                    <div class="flex items-center justify-between text-slate-700">
                        <span>Gross Merchandise Value (POS + Web Storefront)</span>
                        <span class="font-mono font-medium">₹{{ number_format($grossSales, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between text-slate-700">
                        <span>Delivery, Express Courier &amp; COD Fees Collected</span>
                        <span class="font-mono font-medium">₹{{ number_format($shippingCollected, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between text-rose-600">
                        <span>Less: Promotional Coupon Discounts &amp; Markdowns</span>
                        <span class="font-mono font-medium">-₹{{ number_format($discountsGiven, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between text-rose-600">
                        <span>Less: Customer Returns &amp; Order Cancellations</span>
                        <span class="font-mono font-medium">-₹{{ number_format($refundsGiven, 2) }}</span>
                    </div>
                </div>
                <div class="pt-2 border-t border-slate-200 flex items-center justify-between font-bold text-sm text-slate-900 bg-white p-3 rounded-xl border">
                    <span>NET OPERATING REVENUE (A)</span>
                    <span class="font-mono text-base font-black">₹{{ number_format($netRevenue, 2) }}</span>
                </div>
            </div>

            <!-- SECTION 2: COST OF GOODS SOLD -->
            <div class="p-6 space-y-3">
                <div class="flex items-center justify-between text-xs font-bold uppercase tracking-wider text-slate-500">
                    <span>2. Cost of Goods Sold (COGS)</span>
                    <span>Amount (₹)</span>
                </div>
                <div class="space-y-2 pl-3">
                    <div class="flex items-center justify-between text-slate-700">
                        <span>Direct Apparel &amp; Inventory Manufacturing / Supplier Cost</span>
                        <span class="font-mono font-medium">₹{{ number_format($cogsProducts, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between text-slate-700">
                        <span>Supplier Inbound Freight &amp; Port Clearing Allocation</span>
                        <span class="font-mono font-medium">₹{{ number_format($cogsFreight, 2) }}</span>
                    </div>
                </div>
                <div class="pt-2 border-t border-slate-200 flex items-center justify-between font-bold text-sm text-slate-900 bg-slate-50 p-3 rounded-xl border border-slate-200/80">
                    <span>TOTAL COST OF GOODS SOLD (B)</span>
                    <span class="font-mono text-base font-bold text-slate-900">₹{{ number_format($totalCogs, 2) }}</span>
                </div>
            </div>

            <!-- SECTION 3: GROSS PROFIT CALLOUT -->
            <div class="p-6 bg-emerald-50/60 border-y border-emerald-200/80 flex items-center justify-between">
                <div>
                    <div class="text-sm font-bold text-emerald-950 uppercase tracking-wider">3. Gross Profit (A - B)</div>
                    <div class="text-xs text-emerald-700 mt-0.5">Retained profit after direct inventory manufacturing &amp; procurement costs</div>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-black text-emerald-800 font-mono">₹{{ number_format($grossProfit, 2) }}</div>
                    <div class="text-xs font-bold text-emerald-700">{{ number_format($grossMarginPercent, 1) }}% Gross Margin</div>
                </div>
            </div>

            <!-- SECTION 4: OPERATING EXPENSES -->
            <div class="p-6 space-y-3 bg-slate-50/40">
                <div class="flex items-center justify-between text-xs font-bold uppercase tracking-wider text-slate-500">
                    <span>4. Operating Overhead &amp; Administrative Expenses (OPEX)</span>
                    <span>Amount (₹)</span>
                </div>
                <div class="space-y-2 pl-3">
                    @forelse($opexByCategory as $cat)
                        <div class="flex items-center justify-between text-slate-700">
                            <span class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full" style="background-color: {{ $cat['color'] }}"></span>
                                {{ $cat['name'] }}
                            </span>
                            <span class="font-mono font-medium">₹{{ number_format($cat['amount'], 2) }}</span>
                        </div>
                    @empty
                        <div class="text-slate-400 italic py-1">No operating expenses recorded for this period.</div>
                    @endforelse
                </div>
                <div class="pt-2 border-t border-slate-200 flex items-center justify-between font-bold text-sm text-slate-900 bg-white p-3 rounded-xl border">
                    <span>TOTAL OPERATING OVERHEAD (C)</span>
                    <span class="font-mono text-base font-bold text-rose-700">₹{{ number_format($totalOpex, 2) }}</span>
                </div>
            </div>

            <!-- SECTION 5: OPERATING PROFIT / EBITDA -->
            <div class="p-6 {{ $ebitda >= 0 ? 'bg-indigo-50/60 border-y border-indigo-200/80' : 'bg-rose-50/60 border-y border-rose-200/80' }} flex items-center justify-between">
                <div>
                    <div class="text-sm font-bold {{ $ebitda >= 0 ? 'text-indigo-950' : 'text-rose-950' }} uppercase tracking-wider">
                        5. Operating Income / EBITDA (Gross Profit - OPEX)
                    </div>
                    <div class="text-xs {{ $ebitda >= 0 ? 'text-indigo-700' : 'text-rose-700' }} mt-0.5">
                        Earnings Before Interest, Taxes, Depreciation, and Amortization
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-black {{ $ebitda >= 0 ? 'text-indigo-700' : 'text-rose-700' }} font-mono">
                        {{ $ebitda >= 0 ? '+' : '' }}₹{{ number_format($ebitda, 2) }}
                    </div>
                    <div class="text-xs font-bold {{ $ebitda >= 0 ? 'text-indigo-600' : 'text-rose-600' }}">
                        {{ number_format($operatingMarginPercent, 1) }}% Operating Margin
                    </div>
                </div>
            </div>

            <!-- SECTION 6: TAXES & NET PROFIT -->
            <div class="p-6 space-y-3">
                <div class="flex items-center justify-between text-xs font-bold uppercase tracking-wider text-slate-500">
                    <span>6. Taxes &amp; Statutory Adjustments</span>
                    <span>Amount (₹)</span>
                </div>
                <div class="space-y-2 pl-3">
                    <div class="flex items-center justify-between text-slate-700">
                        <span>GST Output Tax Collected (Estimated 18% Base)</span>
                        <span class="font-mono font-medium">₹{{ number_format($gstOutput, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between text-emerald-700">
                        <span>Less: GST Input Tax Credit (ITC from Expenses &amp; Purchases)</span>
                        <span class="font-mono font-medium">-₹{{ number_format($gstInputCredit, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between text-slate-700">
                        <span>Net GST Cash Payable to Authorities</span>
                        <span class="font-mono font-medium">₹{{ number_format($netGstLiability, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between text-slate-700">
                        <span>Corporate Income Tax Provision (Estimated 25% on EBITDA)</span>
                        <span class="font-mono font-medium">₹{{ number_format($taxProvision, 2) }}</span>
                    </div>
                </div>

                <!-- FINAL NET PROFIT CALLOUT -->
                <div class="pt-4 mt-4 border-t-2 border-slate-900 flex items-center justify-between p-4 rounded-xl {{ $netProfit >= 0 ? 'bg-slate-900 text-white' : 'bg-rose-900 text-white' }}">
                    <div>
                        <div class="text-base font-black tracking-wide uppercase">NET PROFIT AFTER TAX (PAT)</div>
                        <div class="text-xs text-slate-300 mt-0.5">Bottom line retained earnings for business expansion</div>
                    </div>
                    <div class="text-right">
                        <div class="text-3xl font-black font-mono">
                            {{ $netProfit >= 0 ? '+' : '' }}₹{{ number_format($netProfit, 2) }}
                        </div>
                        <div class="text-xs font-semibold text-emerald-400 mt-0.5">
                            {{ number_format($netMarginPercent, 1) }}% Net Profit Margin
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

