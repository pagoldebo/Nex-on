@extends('admin.layout')

@section('content')
<div x-data="{ paymentModal: false, statusModal: false }" class="space-y-6">
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-indigo-600 uppercase tracking-wider mb-1">
                <a href="{{ route('admin.invoices.index') }}" class="hover:underline">B2B Commercial Invoices</a>
                <span>•</span>
                <span class="text-slate-500 font-medium">Invoice Inspector</span>
            </div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight font-mono">{{ $invoice->invoice_number }}</h1>
                @php $badge = $invoice->status_badge; @endphp
                <span class="px-2.5 py-1 rounded-full text-xs font-bold border {{ $badge['bg'] }}">
                    {{ $badge['label'] }}
                </span>
                <span class="px-2.5 py-1 rounded-md text-xs font-bold uppercase tracking-wider bg-slate-100 text-slate-700">
                    {{ $invoice->type_label }}
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-1">Generated on {{ $invoice->invoice_date->format('d M Y') }} • Due: {{ $invoice->due_date ? $invoice->due_date->format('d M Y') : 'On Receipt' }}</p>
        </div>
        <div class="flex items-center gap-2.5 flex-wrap">
            <button @click="paymentModal = true" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                Record Payment
            </button>
            <button @click="statusModal = true" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition-colors">
                Change Status
            </button>
            <a href="{{ route('admin.invoices.print', $invoice) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-white bg-slate-900 hover:bg-slate-800 shadow-sm transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print Formal Slip
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

    <!-- 4 Financial Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Invoiced Amount</span>
            <div class="mt-2 text-2xl font-black text-slate-900 font-mono">₹{{ number_format($invoice->total_amount, 2) }}</div>
            <div class="text-xs text-slate-500 mt-1">Includes tax &amp; line discounts</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Amount Paid</span>
            <div class="mt-2 text-2xl font-black text-emerald-700 font-mono">₹{{ number_format($invoice->amount_paid, 2) }}</div>
            <div class="text-xs text-emerald-600 mt-1 font-medium">Realized to date</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Balance Due</span>
            <div class="mt-2 text-2xl font-black {{ $invoice->balance_due > 0 ? 'text-amber-600' : 'text-slate-400' }} font-mono">
                ₹{{ number_format($invoice->balance_due, 2) }}
            </div>
            <div class="text-xs text-slate-500 mt-1">{{ $invoice->balance_due > 0 ? 'Pending remittance' : 'Settled in full' }}</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Tax (GST)</span>
            <div class="mt-2 text-2xl font-black text-indigo-600 font-mono">₹{{ number_format($invoice->tax_amount, 2) }}</div>
            <div class="text-xs text-slate-500 mt-1">
                @if($invoice->cgst_amount > 0)
                    CGST: ₹{{ number_format($invoice->cgst_amount, 2) }} | SGST: ₹{{ number_format($invoice->sgst_amount, 2) }}
                @else
                    IGST: ₹{{ number_format($invoice->igst_amount, 2) }}
                @endif
            </div>
        </div>
    </div>

    <!-- Client & Billing Information Card -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
        <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-2 mb-4">Client Entity Details</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 text-xs">
            <div>
                <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px] block mb-1">Billed To (Client / Entity)</span>
                <div class="font-bold text-sm text-slate-900">{{ $invoice->client_name }}</div>
                @if($invoice->company_name)
                    <div class="font-semibold text-slate-700 mt-0.5">{{ $invoice->company_name }}</div>
                @endif
                @if($invoice->client_email)
                    <div class="text-slate-500 mt-1">{{ $invoice->client_email }}</div>
                @endif
                @if($invoice->client_phone)
                    <div class="text-slate-500">{{ $invoice->client_phone }}</div>
                @endif
            </div>

            <div>
                <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px] block mb-1">Tax Registration Details</span>
                <div class="space-y-1">
                    <div>
                        <span class="text-slate-500">GSTIN:</span>
                        <span class="font-mono font-bold text-indigo-700">{{ $invoice->client_gstin ?? 'Unregistered' }}</span>
                    </div>
                    @if($invoice->client_pan)
                        <div>
                            <span class="text-slate-500">PAN:</span>
                            <span class="font-mono font-semibold text-slate-800">{{ $invoice->client_pan }}</span>
                        </div>
                    @endif
                    <div>
                        <span class="text-slate-500">Place of Supply:</span>
                        <span class="font-semibold text-slate-800">{{ $invoice->place_of_supply ?? 'Maharashtra' }}</span>
                    </div>
                </div>
            </div>

            <div>
                <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px] block mb-1">Addresses</span>
                <div class="space-y-2">
                    <div>
                        <span class="text-slate-400 block text-[10px]">Billing Address:</span>
                        <div class="text-slate-700">{{ $invoice->billing_address ?? 'Not provided' }}</div>
                    </div>
                    @if($invoice->shipping_address)
                        <div>
                            <span class="text-slate-400 block text-[10px]">Shipping Address:</span>
                            <div class="text-slate-700">{{ $invoice->shipping_address }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Line Items Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Itemized Breakdown</h3>
            <span class="text-xs text-slate-500">{{ $invoice->items->count() }} items listed</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200/80 text-slate-600 font-semibold uppercase tracking-wider text-[10px]">
                        <th class="py-3 px-4">#</th>
                        <th class="py-3 px-4">Description</th>
                        <th class="py-3 px-4">HSN Code</th>
                        <th class="py-3 px-4 text-center">Qty / Unit</th>
                        <th class="py-3 px-4 text-right">Unit Rate (₹)</th>
                        <th class="py-3 px-4 text-right">Discount (₹)</th>
                        <th class="py-3 px-4 text-center">GST %</th>
                        <th class="py-3 px-4 text-right">Tax (₹)</th>
                        <th class="py-3 px-4 text-right">Total (₹)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-mono">
                    @foreach($invoice->items as $idx => $item)
                        <tr class="hover:bg-slate-50/60">
                            <td class="py-3 px-4 text-slate-400">{{ $idx + 1 }}</td>
                            <td class="py-3 px-4 font-sans font-semibold text-slate-900">{{ $item->description }}</td>
                            <td class="py-3 px-4 text-slate-600">{{ $item->hsn_code ?? '—' }}</td>
                            <td class="py-3 px-4 text-center text-slate-800">{{ number_format($item->quantity, 0) }} {{ $item->unit }}</td>
                            <td class="py-3 px-4 text-right text-slate-800">₹{{ number_format($item->unit_price, 2) }}</td>
                            <td class="py-3 px-4 text-right text-rose-600">{{ $item->discount_amount > 0 ? '-₹' . number_format($item->discount_amount, 2) : '—' }}</td>
                            <td class="py-3 px-4 text-center text-slate-600">{{ number_format($item->tax_rate, 0) }}%</td>
                            <td class="py-3 px-4 text-right text-slate-700">₹{{ number_format($item->tax_amount, 2) }}</td>
                            <td class="py-3 px-4 text-right font-bold text-slate-900 text-sm">₹{{ number_format($item->total_amount, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Totals summary block -->
        <div class="p-6 bg-slate-50/60 border-t border-slate-100 flex flex-col sm:flex-row justify-between items-start gap-4">
            <div class="text-xs text-slate-500 max-w-md space-y-2">
                @if($invoice->terms)
                    <div>
                        <span class="font-bold text-slate-700 block">Terms &amp; Conditions:</span>
                        <div class="italic">{{ $invoice->terms }}</div>
                    </div>
                @endif
                @if($invoice->notes)
                    <div>
                        <span class="font-bold text-slate-700 block">Customer Notes:</span>
                        <div class="italic">{{ $invoice->notes }}</div>
                    </div>
                @endif
            </div>

            <div class="w-full sm:w-80 space-y-2 text-xs">
                <div class="flex items-center justify-between text-slate-600">
                    <span>Subtotal:</span>
                    <span class="font-mono font-semibold text-slate-900">₹{{ number_format($invoice->subtotal, 2) }}</span>
                </div>
                <div class="flex items-center justify-between text-rose-600">
                    <span>Discount:</span>
                    <span class="font-mono font-semibold">-₹{{ number_format($invoice->discount_amount, 2) }}</span>
                </div>
                @if($invoice->cgst_amount > 0)
                    <div class="flex items-center justify-between text-slate-500 text-[11px]">
                        <span>CGST:</span>
                        <span class="font-mono font-semibold text-slate-800">₹{{ number_format($invoice->cgst_amount, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between text-slate-500 text-[11px]">
                        <span>SGST:</span>
                        <span class="font-mono font-semibold text-slate-800">₹{{ number_format($invoice->sgst_amount, 2) }}</span>
                    </div>
                @else
                    <div class="flex items-center justify-between text-slate-500 text-[11px]">
                        <span>IGST:</span>
                        <span class="font-mono font-semibold text-slate-800">₹{{ number_format($invoice->igst_amount, 2) }}</span>
                    </div>
                @endif
                <div class="flex items-center justify-between text-sm font-bold text-slate-900 pt-2 border-t border-slate-200">
                    <span>Total Amount (INR):</span>
                    <span class="font-mono text-base font-black text-indigo-700">₹{{ number_format($invoice->total_amount, 2) }}</span>
                </div>
                <div class="flex items-center justify-between text-xs text-emerald-700 font-semibold">
                    <span>Amount Paid:</span>
                    <span class="font-mono">₹{{ number_format($invoice->amount_paid, 2) }}</span>
                </div>
                <div class="flex items-center justify-between text-xs font-bold {{ $invoice->balance_due > 0 ? 'text-amber-700' : 'text-slate-400' }}">
                    <span>Balance Due:</span>
                    <span class="font-mono">₹{{ number_format($invoice->balance_due, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Record Payment -->
    <div x-show="paymentModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="paymentModal" @click="paymentModal = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div x-show="paymentModal" class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
                <form method="POST" action="{{ route('admin.invoices.status', $invoice) }}" class="p-6 space-y-4">
                    @csrf
                    @method('PATCH')
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <h3 class="text-base font-bold text-slate-900">Record Payment</h3>
                        <button type="button" @click="paymentModal = false" class="text-slate-400 hover:text-slate-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="p-3 bg-slate-50 rounded-xl space-y-1 text-xs">
                        <div class="flex justify-between text-slate-600">
                            <span>Total Invoice Value:</span>
                            <span class="font-mono font-bold text-slate-900">₹{{ number_format($invoice->total_amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-slate-600">
                            <span>Already Paid:</span>
                            <span class="font-mono text-emerald-700 font-semibold">₹{{ number_format($invoice->amount_paid, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-slate-600 pt-1 border-t border-slate-200">
                            <span>Balance Due:</span>
                            <span class="font-mono font-bold text-amber-600">₹{{ number_format($invoice->balance_due, 2) }}</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Payment Amount to Add (₹) *</label>
                        <input type="number" step="0.01" name="record_payment" value="{{ $invoice->balance_due }}" required min="0.01" max="{{ $invoice->balance_due }}"
                               class="w-full text-xs font-mono font-bold px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                        <button type="button" @click="paymentModal = false" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition-colors">Cancel</button>
                        <button type="submit" class="px-5 py-2 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-xs transition-colors">Confirm Payment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal: Change Status -->
    <div x-show="statusModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="statusModal" @click="statusModal = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div x-show="statusModal" class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
                <form method="POST" action="{{ route('admin.invoices.status', $invoice) }}" class="p-6 space-y-4">
                    @csrf
                    @method('PATCH')
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <h3 class="text-base font-bold text-slate-900">Change Document Status</h3>
                        <button type="button" @click="statusModal = false" class="text-slate-400 hover:text-slate-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Select Status *</label>
                        <select name="status" class="w-full text-xs px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 font-semibold focus:ring-2 focus:ring-indigo-500">
                            <option value="draft" {{ $invoice->status === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="issued" {{ $invoice->status === 'issued' ? 'selected' : '' }}>Issued / Sent</option>
                            <option value="paid" {{ $invoice->status === 'paid' ? 'selected' : '' }}>Paid in Full</option>
                            <option value="cancelled" {{ $invoice->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                        <button type="button" @click="statusModal = false" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition-colors">Cancel</button>
                        <button type="submit" class="px-5 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-xs transition-colors">Update Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

