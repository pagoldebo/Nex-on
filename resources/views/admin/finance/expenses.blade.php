{{--
    ======================================================================
    ADMIN MODULE: FINANCE EXPENSES
    ======================================================================
    File: resources/views/admin/finance/expenses.blade.php
    Responsibility: Back-office administration for FINANCE EXPENSES
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
<div x-data="{ addExpenseModal: false, categoryModal: false }" class="space-y-6">
    <!-- Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-indigo-600 uppercase tracking-wider mb-1">
                <span>Finance & Treasury</span>
                <span>•</span>
                <span class="text-slate-500 font-medium">Operating Overhead</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Operating Expenses &amp; Outflows</h1>
            <p class="text-sm text-slate-500 mt-0.5">Track commercial leases, packaging, staff wages, logistics freight, and claim GST input tax credit (ITC).</p>
        </div>
        <div class="flex items-center gap-2.5 flex-wrap">
            <button @click="categoryModal = true" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200/80 transition-colors">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                Manage Categories
            </button>
            <button @click="addExpenseModal = true" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm hover:shadow transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Log New Expense
            </button>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <!-- KPI Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Spend -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total OPEX (Period)</span>
                <span class="p-2 rounded-xl bg-rose-50 text-rose-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </span>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-black text-slate-900">₹{{ number_format($totalSpend, 2) }}</div>
                <div class="text-xs text-slate-500 mt-1 flex items-center gap-1.5">
                    <span>From {{ $startDate->format('d M') }} to {{ $endDate->format('d M Y') }}</span>
                </div>
            </div>
        </div>

        <!-- GST Input Tax Credit (ITC) -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">GST Input Credit (ITC)</span>
                <span class="p-2 rounded-xl bg-emerald-50 text-emerald-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-black text-emerald-700">₹{{ number_format($totalGstItc, 2) }}</div>
                <div class="text-xs text-emerald-600 mt-1 font-medium">Eligible for GST tax deduction</div>
            </div>
        </div>

        <!-- Monthly Recurring Commitment -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Monthly Recurring</span>
                <span class="p-2 rounded-xl bg-indigo-50 text-indigo-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </span>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-black text-slate-900">₹{{ number_format($recurringMonthly, 2) }}</div>
                <div class="text-xs text-slate-500 mt-1">Automatic recurring run-rate/mo</div>
            </div>
        </div>

        <!-- Top Category -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Top Spend Category</span>
                <span class="p-2 rounded-xl bg-amber-50 text-amber-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/></svg>
                </span>
            </div>
            <div class="mt-3">
                <div class="text-xl font-black text-slate-900 truncate">
                    {{ $topCategory ? $topCategory->name : 'No expenses' }}
                </div>
                <div class="text-xs text-slate-500 mt-1">
                    {{ $topCategory ? '₹' . number_format($topCategory->total_amount, 2) . ' (' . $topCategory->expense_count . ' bills)' : 'None logged' }}
                </div>
            </div>
        </div>
    </div>

    <!-- Category Distribution Pills -->
    @if($categoryBreakdown->isNotEmpty())
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3 overflow-x-auto pb-3">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider shrink-0">Categories:</span>
            @foreach($categoryBreakdown as $cat)
                <a href="{{ route('admin.finance.expenses', ['category_id' => $cat->id, 'period' => $period]) }}" 
                   class="shrink-0 inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs font-medium border transition-all {{ $categoryId == $cat->id ? 'bg-indigo-50 border-indigo-300 text-indigo-700 font-bold' : 'bg-slate-50 border-slate-200/80 text-slate-700 hover:bg-slate-100' }}">
                    <span class="w-2.5 h-2.5 rounded-full" style="background-color: {{ $cat->color }}"></span>
                    <span>{{ $cat->name }}</span>
                    <span class="font-bold text-slate-900">₹{{ number_format($cat->total_amount, 0) }}</span>
                </a>
            @endforeach
            @if($categoryId)
                <a href="{{ route('admin.finance.expenses', ['period' => $period]) }}" class="shrink-0 text-xs text-rose-600 hover:underline font-semibold ml-2">Clear Filter</a>
            @endif
        </div>
    @endif

    <!-- Search & Period Filter Toolbar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <form method="GET" action="{{ route('admin.finance.expenses') }}" class="flex flex-wrap items-center gap-2.5 flex-1">
            <!-- Period Selector -->
            <select name="period" onchange="this.form.submit()" class="text-xs font-semibold px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:ring-2 focus:ring-indigo-500">
                <option value="this_month" {{ $period === 'this_month' ? 'selected' : '' }}>This Month</option>
                <option value="today" {{ $period === 'today' ? 'selected' : '' }}>Today</option>
                <option value="this_week" {{ $period === 'this_week' ? 'selected' : '' }}>This Week</option>
                <option value="last_month" {{ $period === 'last_month' ? 'selected' : '' }}>Last Month</option>
                <option value="this_year" {{ $period === 'this_year' ? 'selected' : '' }}>This Year</option>
                <option value="all" {{ $period === 'all' ? 'selected' : '' }}>All Time</option>
            </select>

            <!-- Payment Method Filter -->
            <select name="payment_method" onchange="this.form.submit()" class="text-xs font-semibold px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:ring-2 focus:ring-indigo-500">
                <option value="">All Payment Modes</option>
                <option value="bank_transfer" {{ $paymentMethod === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer / NEFT</option>
                <option value="upi" {{ $paymentMethod === 'upi' ? 'selected' : '' }}>UPI / QR</option>
                <option value="cash" {{ $paymentMethod === 'cash' ? 'selected' : '' }}>Cash</option>
                <option value="card" {{ $paymentMethod === 'card' ? 'selected' : '' }}>Debit/Credit Card</option>
                <option value="cheque" {{ $paymentMethod === 'cheque' ? 'selected' : '' }}>Cheque</option>
            </select>

            <!-- Search input -->
            <div class="relative flex-1 min-w-[200px]">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search title, vendor, GSTIN, ref#..." 
                       class="w-full text-xs px-3.5 py-2 pl-9 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-indigo-500">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            <button type="submit" class="px-3.5 py-2 rounded-xl text-xs font-semibold text-white bg-slate-800 hover:bg-slate-900 transition-colors">
                Filter
            </button>
            @if($search || $categoryId || $paymentMethod)
                <a href="{{ route('admin.finance.expenses') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 px-2 py-2">Reset</a>
            @endif
        </form>
    </div>

    <!-- Expenses Ledger Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200/80 text-slate-600 font-semibold uppercase tracking-wider text-[10px]">
                        <th class="py-3 px-4">Expense No &amp; Date</th>
                        <th class="py-3 px-4">Category</th>
                        <th class="py-3 px-4">Title / Particulars</th>
                        <th class="py-3 px-4">Vendor &amp; GSTIN</th>
                        <th class="py-3 px-4">Payment Mode</th>
                        <th class="py-3 px-4">Receipt</th>
                        <th class="py-3 px-4 text-right">Tax (ITC)</th>
                        <th class="py-3 px-4 text-right">Total Amount</th>
                        <th class="py-3 px-4 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($expenses as $expense)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-3.5 px-4">
                                <div class="font-bold font-mono text-slate-900">{{ $expense->expense_number }}</div>
                                <div class="text-[11px] text-slate-500">{{ $expense->expense_date->format('d M Y') }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-semibold border"
                                      style="background-color: {{ $expense->category->color }}15; color: {{ $expense->category->color }}; border-color: {{ $expense->category->color }}30;">
                                    <span class="w-1.5 h-1.5 rounded-full" style="background-color: {{ $expense->category->color }}"></span>
                                    {{ $expense->category->name }}
                                </span>
                                @if($expense->is_recurring)
                                    <span class="block mt-1 text-[9px] font-bold uppercase text-indigo-600 bg-indigo-50 px-1.5 py-0.5 rounded w-max">
                                        Recurring ({{ $expense->recurring_frequency }})
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 max-w-xs">
                                <div class="font-semibold text-slate-900">{{ $expense->title }}</div>
                                @if($expense->reference_no)
                                    <div class="text-[11px] text-slate-500 font-mono">Ref: {{ $expense->reference_no }}</div>
                                @endif
                                @if($expense->notes)
                                    <div class="text-[11px] text-slate-400 italic truncate">{{ $expense->notes }}</div>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-medium text-slate-800">{{ $expense->vendor_name ?? '—' }}</div>
                                @if($expense->vendor_gstin)
                                    <div class="text-[10px] text-indigo-600 font-mono">{{ $expense->vendor_gstin }}</div>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium bg-slate-100 text-slate-700 capitalize">
                                    {{ str_replace('_', ' ', $expense->payment_method) }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                @if($expense->receipt_path)
                                    <a href="{{ Storage::disk('public')->url($expense->receipt_path) }}" target="_blank" 
                                       class="inline-flex items-center gap-1 text-[11px] font-semibold text-indigo-600 hover:text-indigo-800 hover:underline">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                        View Bill
                                    </a>
                                @else
                                    <span class="text-slate-400 text-[11px]">None</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono font-medium text-emerald-700">
                                ₹{{ number_format($expense->tax_amount, 2) }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono font-bold text-slate-900 text-sm">
                                ₹{{ number_format($expense->amount, 2) }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <form method="POST" action="{{ route('admin.finance.expenses.destroy', $expense) }}" onsubmit="return confirm('Delete this expense record?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Delete Expense">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-12 text-center text-slate-400">
                                <svg class="w-12 h-12 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <p class="text-sm font-medium text-slate-600">No operating expenses recorded for this period.</p>
                                <button @click="addExpenseModal = true" class="mt-3 text-xs font-semibold text-indigo-600 hover:underline">Click here to log your first expense</button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($expenses->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $expenses->links() }}
            </div>
        @endif
    </div>

    <!-- Modal: Log New Expense -->
    <div x-show="addExpenseModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="addExpenseModal" @click="addExpenseModal = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div x-show="addExpenseModal" class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full">
                <form method="POST" action="{{ route('admin.finance.expenses.store') }}" enctype="multipart/form-data" class="p-6 space-y-4">
                    @csrf
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div>
                            <h3 class="text-lg font-bold text-slate-900">Log Operating Expense</h3>
                            <p class="text-xs text-slate-500">Record a new business expenditure, rent, packaging, or bill.</p>
                        </div>
                        <button type="button" @click="addExpenseModal = false" class="text-slate-400 hover:text-slate-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Expense Category *</label>
                            <select name="expense_category_id" required class="w-full text-xs px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:ring-2 focus:ring-indigo-500">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Expense Date *</label>
                            <input type="date" name="expense_date" value="{{ date('Y-m-d') }}" required 
                                   class="w-full text-xs px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Title / Description *</label>
                        <input type="text" name="title" required placeholder="e.g. Bandra Showroom Rent - November" 
                               class="w-full text-xs px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Total Amount (₹) *</label>
                            <input type="number" step="0.01" name="amount" required placeholder="0.00" 
                                   class="w-full text-xs px-3.5 py-2 font-mono font-bold bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:ring-2 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">GST Input Tax (ITC ₹)</label>
                            <input type="number" step="0.01" name="tax_amount" placeholder="0.00" 
                                   class="w-full text-xs px-3.5 py-2 font-mono bg-slate-50 border border-slate-200 rounded-xl text-emerald-700 focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Payment Method *</label>
                            <select name="payment_method" required class="w-full text-xs px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:ring-2 focus:ring-indigo-500">
                                <option value="bank_transfer">Bank Transfer / NEFT / IMPS</option>
                                <option value="upi">UPI / QR Payment</option>
                                <option value="cash">Petty Cash</option>
                                <option value="card">Company Debit / Credit Card</option>
                                <option value="cheque">Cheque</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Reference / Bill / UTR No.</label>
                            <input type="text" name="reference_no" placeholder="e.g. UTR-982341209" 
                                   class="w-full text-xs px-3.5 py-2 font-mono bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Vendor / Payee Name</label>
                            <input type="text" name="vendor_name" placeholder="e.g. Oberoi Realty Ltd" 
                                   class="w-full text-xs px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:ring-2 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Vendor GSTIN</label>
                            <input type="text" name="vendor_gstin" placeholder="e.g. 27AABCO1234F1Z5" 
                                   class="w-full text-xs px-3.5 py-2 font-mono bg-slate-50 border border-slate-200 rounded-xl text-slate-800 uppercase focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>

                    <!-- Receipt Attachment Upload -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Upload Receipt / Bill (PDF or Image)</label>
                        <input type="file" name="receipt" accept="image/*,.pdf" 
                               class="w-full text-xs px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-600 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    </div>

                    <!-- Recurring Checkbox -->
                    <div x-data="{ recurring: false }" class="p-3 bg-slate-50 rounded-xl border border-slate-200/80">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_recurring" value="1" x-model="recurring" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="text-xs font-bold text-slate-700">This is an automatic recurring expense</span>
                        </label>
                        <div x-show="recurring" class="mt-2.5">
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">Frequency</label>
                            <select name="recurring_frequency" class="w-full text-xs px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-slate-700">
                                <option value="monthly">Monthly (Rent, Wages, Utilities)</option>
                                <option value="quarterly">Quarterly</option>
                                <option value="yearly">Annual Subscription / Insurance</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Audit Notes / Comments</label>
                        <textarea name="notes" rows="2" placeholder="Optional notes for accounting ledger..." 
                                  class="w-full text-xs px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:ring-2 focus:ring-indigo-500"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                        <button type="button" @click="addExpenseModal = false" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-xs transition-colors">
                            Save Expense Record
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal: Manage Custom Categories -->
    <div x-show="categoryModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="categoryModal" @click="categoryModal = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div x-show="categoryModal" class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
                <form method="POST" action="{{ route('admin.finance.expenses.categories.store') }}" class="p-6 space-y-4">
                    @csrf
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <h3 class="text-base font-bold text-slate-900">Add Expense Category</h3>
                        <button type="button" @click="categoryModal = false" class="text-slate-400 hover:text-slate-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Category Name *</label>
                        <input type="text" name="name" required placeholder="e.g. Legal & Compliance" 
                               class="w-full text-xs px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Badge Color Hex</label>
                        <div class="flex items-center gap-2">
                            <input type="color" name="color" value="#6366f1" class="h-9 w-12 rounded border border-slate-200 p-0.5 cursor-pointer">
                            <span class="text-xs text-slate-500">Used for charts and category tags</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Description</label>
                        <textarea name="description" rows="2" placeholder="Brief explanation of items belonging to this category..." 
                                  class="w-full text-xs px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:ring-2 focus:ring-indigo-500"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                        <button type="button" @click="categoryModal = false" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition-colors">Cancel</button>
                        <button type="submit" class="px-5 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-xs transition-colors">Create Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

