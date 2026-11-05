@extends('admin.layout')

@section('content')
<div class="space-y-6">
    <!-- Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-indigo-600 uppercase tracking-wider mb-1">
                <span>Finance &amp; Invoicing</span>
                <span>•</span>
                <span class="text-slate-500 font-medium">Commercial Billing</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">B2B Tax Invoices &amp; Credit Notes</h1>
            <p class="text-sm text-slate-500 mt-0.5">Generate formal GST-compliant commercial invoices, proforma bills, and credit notes for wholesale corporate clients.</p>
        </div>
        <div class="flex items-center gap-2.5 flex-wrap">
            <a href="{{ route('admin.invoices.create') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Create New Invoice
            </a>
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

    <!-- 4 High-Impact KPI Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Invoiced -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total B2B Invoiced</span>
                <span class="p-2 rounded-xl bg-indigo-50 text-indigo-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </span>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-black text-slate-900 font-mono">₹{{ number_format($totalInvoiced, 2) }}</div>
                <div class="text-xs text-slate-500 mt-1">Total commercial billings generated</div>
            </div>
        </div>

        <!-- Total Collected -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Collected</span>
                <span class="p-2 rounded-xl bg-emerald-50 text-emerald-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-black text-emerald-700 font-mono">₹{{ number_format($totalCollected, 2) }}</div>
                <div class="text-xs text-emerald-600 mt-1 font-medium">Realized bank remittances</div>
            </div>
        </div>

        <!-- Outstanding Balance -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Outstanding Due</span>
                <span class="p-2 rounded-xl bg-amber-50 text-amber-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-black text-amber-700 font-mono">₹{{ number_format($totalOutstanding, 2) }}</div>
                <div class="text-xs text-slate-500 mt-1">Pending client receivables</div>
            </div>
        </div>

        <!-- Credit Notes Issued -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Credit Notes Issued</span>
                <span class="p-2 rounded-xl bg-rose-50 text-rose-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-black text-rose-700 font-mono">₹{{ number_format($totalCreditNotes, 2) }}</div>
                <div class="text-xs text-slate-500 mt-1">Total credit adjustments granted</div>
            </div>
        </div>
    </div>

    <!-- Filter Toolbar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <form method="GET" action="{{ route('admin.invoices.index') }}" class="flex flex-wrap items-center gap-2.5 flex-1">
            <!-- Type Selector -->
            <select name="type" onchange="this.form.submit()" class="text-xs font-semibold px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:ring-2 focus:ring-indigo-500">
                <option value="all">All Document Types</option>
                <option value="tax_invoice" {{ $type === 'tax_invoice' ? 'selected' : '' }}>Tax Invoice</option>
                <option value="credit_note" {{ $type === 'credit_note' ? 'selected' : '' }}>Credit Note</option>
                <option value="proforma" {{ $type === 'proforma' ? 'selected' : '' }}>Proforma Invoice</option>
            </select>

            <!-- Status Selector -->
            <select name="status" onchange="this.form.submit()" class="text-xs font-semibold px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:ring-2 focus:ring-indigo-500">
                <option value="all">All Statuses</option>
                <option value="issued" {{ $status === 'issued' ? 'selected' : '' }}>Issued / Sent</option>
                <option value="paid" {{ $status === 'paid' ? 'selected' : '' }}>Paid in Full</option>
                <option value="partial" {{ $status === 'partial' ? 'selected' : '' }}>Partially Paid</option>
                <option value="draft" {{ $status === 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>

            <!-- Search input -->
            <div class="relative flex-1 min-w-[220px]">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search invoice#, client, company, GSTIN..." 
                       class="w-full text-xs px-3.5 py-2 pl-9 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-indigo-500">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            <button type="submit" class="px-3.5 py-2 rounded-xl text-xs font-semibold text-white bg-slate-800 hover:bg-slate-900 transition-colors">
                Filter
            </button>
            @if($search || ($type && $type !== 'all') || ($status && $status !== 'all'))
                <a href="{{ route('admin.invoices.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 px-2 py-2">Reset</a>
            @endif
        </form>
    </div>

    <!-- Invoices Directory Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200/80 text-slate-600 font-semibold uppercase tracking-wider text-[10px]">
                        <th class="py-3 px-4">Invoice / Note #</th>
                        <th class="py-3 px-4">Date &amp; Due</th>
                        <th class="py-3 px-4">Client / Company &amp; GSTIN</th>
                        <th class="py-3 px-4 text-center">Items</th>
                        <th class="py-3 px-4 text-right">Tax (GST)</th>
                        <th class="py-3 px-4 text-right">Total Amount</th>
                        <th class="py-3 px-4 text-right">Balance Due</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($invoices as $invoice)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-3.5 px-4">
                                <a href="{{ route('admin.invoices.show', $invoice) }}" class="font-bold font-mono text-indigo-600 hover:text-indigo-800 hover:underline">
                                    {{ $invoice->invoice_number }}
                                </a>
                                <div class="mt-0.5">
                                    <span class="inline-block px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider {{ $invoice->type === 'credit_note' ? 'bg-rose-50 text-rose-700' : ($invoice->type === 'proforma' ? 'bg-purple-50 text-purple-700' : 'bg-slate-100 text-slate-700') }}">
                                        {{ $invoice->type_label }}
                                    </span>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-[11px]">
                                <div class="font-medium text-slate-900">{{ $invoice->invoice_date->format('d M Y') }}</div>
                                <div class="text-slate-400">Due: {{ $invoice->due_date ? $invoice->due_date->format('d M Y') : 'On Receipt' }}</div>
                            </td>
                            <td class="py-3.5 px-4 max-w-xs">
                                <div class="font-semibold text-slate-900">{{ $invoice->client_name }}</div>
                                @if($invoice->company_name)
                                    <div class="text-[11px] text-slate-600">{{ $invoice->company_name }}</div>
                                @endif
                                @if($invoice->client_gstin)
                                    <div class="text-[10px] text-indigo-600 font-mono">{{ $invoice->client_gstin }}</div>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center font-mono font-medium text-slate-600">
                                {{ $invoice->items->count() }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono text-slate-600 font-medium">
                                ₹{{ number_format($invoice->tax_amount, 2) }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono font-bold text-slate-900 text-sm">
                                ₹{{ number_format($invoice->total_amount, 2) }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono font-bold text-xs {{ $invoice->balance_due > 0 ? 'text-amber-600' : 'text-emerald-600' }}">
                                ₹{{ number_format($invoice->balance_due, 2) }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @php $badge = $invoice->status_badge; @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $badge['bg'] }}">
                                    {{ $badge['label'] }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('admin.invoices.show', $invoice) }}" class="p-1.5 text-slate-500 hover:text-indigo-600 hover:bg-slate-100 rounded-lg transition-colors" title="View Inspector">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    <a href="{{ route('admin.invoices.print', $invoice) }}" target="_blank" class="p-1.5 text-slate-500 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition-colors" title="Print Formal Slip">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-12 text-center text-slate-400">
                                <svg class="w-12 h-12 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <p class="text-sm font-medium text-slate-600">No commercial invoices or credit notes found.</p>
                                <a href="{{ route('admin.invoices.create') }}" class="inline-block mt-3 text-xs font-semibold text-indigo-600 hover:underline">Click here to generate your first B2B invoice</a>
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
</div>
@endsection

