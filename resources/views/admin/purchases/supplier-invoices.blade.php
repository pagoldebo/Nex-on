{{--
    ======================================================================
    ADMIN MODULE: PURCHASES SUPPLIER INVOICES
    ======================================================================
    File: resources/views/admin/purchases/supplier-invoices.blade.php
    Responsibility: Back-office administration for PURCHASES SUPPLIER INVOICES
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
<div class="space-y-8" x-data="{
    createModalOpen: false,
    paymentModalOpen: false,
    matchModalOpen: false,
    activeInvoiceForPayment: { id: '', invoice_number: '', balance: 0 },
    activeInvoiceForMatch: null,

    openPayment(inv) {
        const total = parseFloat(inv.total_amount || 0);
        const paid = parseFloat(inv.amount_paid || 0);
        this.activeInvoiceForPayment = {
            id: inv.id,
            invoice_number: inv.invoice_number,
            balance: Math.max(0, total - paid).toFixed(2)
        };
        this.paymentModalOpen = true;
    },

    openMatch(inv) {
        this.activeInvoiceForMatch = inv;
        this.matchModalOpen = true;
    }
}">
    <!-- Header -->
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Supplier Invoices &amp; 3-Way Matching</h1>
            <p class="mt-1 text-sm text-slate-500">Audit vendor bills against purchase orders (PO) and goods receipts (GRN). Prevent double-billing, detect price variances, and manage accounts payable.</p>
        </div>
        <div class="mt-4 sm:mt-0 flex items-center gap-3">
            <button @click="createModalOpen = true" type="button" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-600 text-xs font-bold text-white hover:bg-indigo-700 shadow-sm transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Record Supplier Invoice
            </button>
        </div>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm">
            <ul class="list-disc pl-5 space-y-1">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Financial KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Invoiced</p>
                <p class="text-2xl font-black text-slate-900 mt-1">₹{{ number_format($totalBilled, 2) }}</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Amount Paid</p>
                <p class="text-2xl font-black text-emerald-600 mt-1">₹{{ number_format($totalPaid, 2) }}</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Outstanding Payable</p>
                <p class="text-2xl font-black text-amber-600 mt-1">₹{{ number_format($totalOutstanding, 2) }}</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">3-Way Variances</p>
                <p class="text-2xl font-black {{ $varianceCount > 0 ? 'text-rose-600' : 'text-slate-900' }} mt-1">{{ $varianceCount }}</p>
            </div>
            <div class="w-11 h-11 rounded-xl {{ $varianceCount > 0 ? 'bg-rose-50 text-rose-600' : 'bg-slate-100 text-slate-600' }} flex items-center justify-center font-bold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
        </div>
    </div>

    <!-- Table Directory -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <!-- Filter Toolbar -->
        <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-slate-50/50">
            <form method="GET" action="{{ route('admin.supplier-invoices.index') }}" class="flex flex-wrap items-center gap-2 flex-1">
                <div class="relative w-64">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search invoice # or supplier..."
                           class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                </div>

                <select name="supplier_id" class="px-3 py-2 text-xs rounded-xl border border-slate-200 bg-white">
                    <option value="">All Suppliers</option>
                    @foreach($suppliers as $s)
                        <option value="{{ $s->id }}" {{ request('supplier_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                </select>

                <select name="payment_status" class="px-3 py-2 text-xs rounded-xl border border-slate-200 bg-white">
                    <option value="">All Payment Statuses</option>
                    <option value="unpaid" {{ request('payment_status') === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                    <option value="partial" {{ request('payment_status') === 'partial' ? 'selected' : '' }}>Partial</option>
                    <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="overdue" {{ request('payment_status') === 'overdue' ? 'selected' : '' }}>Overdue</option>
                </select>

                <button type="submit" class="px-3.5 py-2 bg-slate-200/80 text-slate-700 text-xs font-semibold rounded-xl hover:bg-slate-300">Filter</button>
            </form>
            <div class="text-xs text-slate-500">
                Showing <span class="font-bold text-slate-800">{{ $invoices->total() }}</span> supplier bills
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50/75 border-b border-slate-200/70 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                    <tr>
                        <th class="py-3 px-4">Invoice # &amp; Date</th>
                        <th class="py-3 px-4">Supplier</th>
                        <th class="py-3 px-4">PO Reference</th>
                        <th class="py-3 px-4">Amount Billed</th>
                        <th class="py-3 px-4">Paid / Balance</th>
                        <th class="py-3 px-4 text-center">Payment Status</th>
                        <th class="py-3 px-4 text-center">3-Way Match</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($invoices as $inv)
                        @php
                            $balance = max(0, (float)$inv->total_amount - (float)$inv->amount_paid);
                            $isOverdue = $inv->payment_status !== 'paid' && $inv->due_date && $inv->due_date->isPast();
                        @endphp
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-3 px-4">
                                <div class="font-mono font-bold text-slate-900 text-[11px]">#{{ $inv->invoice_number }}</div>
                                <div class="text-[10px] text-slate-400">Date: {{ $inv->invoice_date->format('M d, Y') }} &bull; Due: {{ $inv->due_date->format('M d, Y') }}</div>
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-900">{{ $inv->supplier?->name ?? 'Unknown Supplier' }}</div>
                                <div class="text-[10px] text-slate-400 font-mono">{{ $inv->supplier?->gst_number ?? 'No GSTIN' }}</div>
                            </td>
                            <td class="py-3 px-4">
                                <a href="{{ route('admin.purchase-orders.show', $inv->purchase_order_id) }}" class="font-mono font-bold text-indigo-600 hover:underline">
                                    {{ $inv->purchaseOrder?->po_number ?? 'PO #' . $inv->purchase_order_id }}
                                </a>
                            </td>
                            <td class="py-3 px-4 font-bold text-slate-900">
                                ₹{{ number_format((float)$inv->total_amount, 2) }}
                                <div class="text-[10px] text-slate-400 font-normal">Tax: ₹{{ number_format((float)$inv->tax_amount, 2) }}</div>
                            </td>
                            <td class="py-3 px-4">
                                <div class="text-emerald-700 font-semibold">₹{{ number_format((float)$inv->amount_paid, 2) }} paid</div>
                                @if($balance > 0)
                                    <div class="text-rose-600 font-bold text-[10px]">₹{{ number_format($balance, 2) }} due</div>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($inv->payment_status === 'paid')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Paid</span>
                                @elseif($isOverdue)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 animate-pulse">Overdue</span>
                                @elseif($inv->payment_status === 'partial')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">Partial</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">Unpaid</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($inv->matching_status === 'matched')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200" title="Matches Purchase Order">
                                        <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        3-Way Matched
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200" title="Variance: ₹{{ $inv->po_variance }}">
                                        <svg class="w-3 h-3 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                        Variance: {{ $inv->po_variance > 0 ? '+₹' : '-₹' }}{{ abs((float)$inv->po_variance) }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <button type="button" @click="openMatch({{ $inv->toJson() }})" class="p-1 text-slate-500 hover:text-indigo-600 hover:bg-slate-100 rounded-lg transition-colors" title="3-Way Match Audit Details">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                    </button>
                                    @if($balance > 0)
                                        <button type="button" @click="openPayment({{ $inv->toJson() }})" class="px-2.5 py-1 text-[11px] font-bold bg-emerald-50 text-emerald-700 hover:bg-emerald-100 rounded-lg transition-colors">
                                            Pay
                                        </button>
                                    @endif
                                    <form action="{{ route('admin.supplier-invoices.destroy', $inv) }}" method="POST" onsubmit="return confirm('Delete invoice #{{ $inv->invoice_number }}?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400">
                                <svg class="w-10 h-10 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <p class="font-semibold text-slate-600">No supplier invoices recorded</p>
                                <p class="text-xs text-slate-400 mt-0.5">Click "Record Supplier Invoice" to enter and match vendor bills.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($invoices->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $invoices->links() }}
            </div>
        @endif
    </div>

    <!-- Modal: Record Supplier Invoice -->
    <div x-show="createModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="createModalOpen" @click="createModalOpen = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="createModalOpen" class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200">
                <form action="{{ route('admin.supplier-invoices.store') }}" method="POST">
                    @csrf
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-slate-900">Record Vendor Bill / Invoice</h3>
                        <p class="text-xs text-slate-500 mt-1">Links to original Purchase Order for automated 3-way variance matching.</p>
                        
                        <div class="space-y-4 mt-5">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Select Purchase Order *</label>
                                <select name="purchase_order_id" required class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-medium">
                                    <option value="">-- Choose Purchase Order --</option>
                                    @foreach($purchaseOrders as $po)
                                        <option value="{{ $po->id }}">{{ $po->po_number }} - {{ $po->supplier?->name }} (PO Total: ₹{{ number_format((float)$po->total_amount, 2) }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Supplier *</label>
                                <select name="supplier_id" required class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-medium">
                                    <option value="">-- Choose Supplier --</option>
                                    @foreach($suppliers as $sup)
                                        <option value="{{ $sup->id }}">{{ $sup->name }} ({{ $sup->gst_number ?? 'No GST' }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Invoice Number *</label>
                                    <input type="text" name="invoice_number" required placeholder="e.g. INV-9842" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-medium">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Invoice Date *</label>
                                    <input type="date" name="invoice_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-medium">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Payment Due Date *</label>
                                    <input type="date" name="due_date" value="{{ date('Y-m-d', strtotime('+30 days')) }}" required class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-medium">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Subtotal (Excl. Tax) *</label>
                                    <input type="number" step="0.01" name="subtotal" required placeholder="e.g. 10000.00" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-medium">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">GST Tax Amount (₹)</label>
                                    <input type="number" step="0.01" name="tax_amount" value="0.00" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-medium">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Total Invoiced Amount (₹) *</label>
                                    <input type="number" step="0.01" name="total_amount" required placeholder="e.g. 11800.00" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-bold">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Notes / Discrepancy Remarks</label>
                                <textarea name="notes" rows="2" placeholder="e.g. Attached vendor original tax invoice copy..." class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                        <button type="button" @click="createModalOpen = false" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-200 rounded-xl">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-xs font-bold rounded-xl hover:bg-indigo-700 shadow-sm">Record &amp; Match</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal: Record Payment -->
    <div x-show="paymentModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="paymentModalOpen" @click="paymentModalOpen = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="paymentModalOpen" class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-slate-200">
                <form :action="'{{ url('admin/supplier-invoices') }}/' + activeInvoiceForPayment.id + '/payment'" method="POST">
                    @csrf
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-slate-900">Record Vendor Payment</h3>
                        <p class="text-xs text-slate-500 mt-1">Invoice #<span class="font-mono font-bold text-slate-800" x-text="activeInvoiceForPayment.invoice_number"></span> &bull; Balance Due: ₹<span class="font-bold text-rose-600" x-text="activeInvoiceForPayment.balance"></span></p>
                        
                        <div class="space-y-4 mt-5">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Payment Amount (₹) *</label>
                                <input type="number" step="0.01" name="amount" :value="activeInvoiceForPayment.balance" required class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-bold">
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Payment Method *</label>
                                    <select name="payment_method" required class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-medium">
                                        <option value="bank_transfer">Bank Transfer (NEFT/RTGS)</option>
                                        <option value="cheque">Cheque</option>
                                        <option value="upi">UPI / Net Banking</option>
                                        <option value="cash">Cash Outflow</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Paid At Date *</label>
                                    <input type="date" name="paid_at" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-medium">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Bank Reference / UTR Number</label>
                                <input type="text" name="transaction_reference" placeholder="e.g. UTR-HDFC98234123" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-mono">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Payment Notes</label>
                                <input type="text" name="notes" placeholder="Optional notes..." class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                            </div>
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                        <button type="button" @click="paymentModalOpen = false" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-200 rounded-xl">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-xs font-bold rounded-xl hover:bg-emerald-700 shadow-sm">Confirm Payment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal: 3-Way Match Audit Inspector -->
    <div x-show="matchModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="matchModalOpen" @click="matchModalOpen = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="matchModalOpen" class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-slate-200">
                <template x-if="activeInvoiceForMatch">
                    <div>
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-bold text-slate-900">3-Way Match Verification</h3>
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold"
                                      :class="activeInvoiceForMatch.matching_status === 'matched' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800'"
                                      x-text="activeInvoiceForMatch.matching_status === 'matched' ? 'PASSED (MATCHED)' : 'VARIANCE FLAGGED'"></span>
                            </div>
                            <p class="text-xs text-slate-500 mt-1">Cross-examining Purchase Order, Physical Receipt, and Invoiced Value.</p>

                            <div class="mt-5 space-y-3 bg-slate-50 p-4 rounded-xl border border-slate-200/80">
                                <div class="flex justify-between text-xs">
                                    <span class="text-slate-500">1. Purchase Order Amount:</span>
                                    <span class="font-bold text-slate-900" x-text="'₹' + parseFloat(activeInvoiceForMatch.purchase_order ? activeInvoiceForMatch.purchase_order.total_amount : 0).toFixed(2)"></span>
                                </div>
                                <div class="flex justify-between text-xs">
                                    <span class="text-slate-500">2. Invoiced Billed Total:</span>
                                    <span class="font-bold text-slate-900" x-text="'₹' + parseFloat(activeInvoiceForMatch.total_amount).toFixed(2)"></span>
                                </div>
                                <div class="flex justify-between text-xs border-t border-slate-200 pt-2 font-bold">
                                    <span class="text-slate-700">Calculated Variance:</span>
                                    <span :class="parseFloat(activeInvoiceForMatch.po_variance) > 0 ? 'text-rose-600' : 'text-emerald-600'" x-text="'₹' + parseFloat(activeInvoiceForMatch.po_variance).toFixed(2)"></span>
                                </div>
                            </div>

                            <div class="mt-4" x-show="activeInvoiceForMatch.notes">
                                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Audit Log Remarks:</p>
                                <p class="text-xs text-slate-700 bg-white p-3 rounded-lg border border-slate-200" x-text="activeInvoiceForMatch.notes"></p>
                            </div>
                        </div>
                        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end">
                            <button type="button" @click="matchModalOpen = false" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-200 rounded-xl">Close</button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>
@endsection

