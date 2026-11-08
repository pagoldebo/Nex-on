{{--
    ======================================================================
    ADMIN MODULE: INVENTORY BATCH TRACE
    ======================================================================
    File: resources/views/admin/inventory/batch-trace.blade.php
    Responsibility: Back-office administration for INVENTORY BATCH TRACE
    Features:
      - Standard administration UI following the Nex-on design guidelines
      - Accessible controls, Tailwind CSS styling, and reactive Alpine.js components
    Security & Access Control:
      - Guarded by Laravel authentication and RBAC permission middleware
    Operations & Maintenance:
      - Integrates with master administration shell (@extends('admin.layout'))
--}}
@extends('admin.layout')

@section('title', 'Batch Recall & Customer Traceability Audit')

@section('content')
<div class="space-y-6 pb-12">

    <!-- Header & Action Bar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.inventory.batches.index') }}" class="text-xs text-indigo-600 hover:underline font-bold flex items-center gap-1">
                    &larr; Back to Batches
                </a>
                <span class="text-xs text-slate-400">&bull; Quality Recall Audit</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 mt-1">Batch Recall &amp; Traceability: {{ $batch->batch_number }}</h1>
            <p class="text-xs text-slate-500 mt-0.5">Complete customer dispatch trail for QA audits, supplier claims, or emergency product recall alerts.</p>
        </div>

        <button onclick="window.print()" type="button"
                class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold shadow-xs flex items-center gap-2 transition self-start md:self-auto">
            <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            <span>Print Recall Manifest</span>
        </button>
    </div>

    <!-- Batch Metadata Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs">
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
            <div>
                <span class="text-slate-400 font-mono uppercase text-[10px]">Product &amp; SKU</span>
                <div class="font-bold text-slate-900 text-sm mt-0.5">{{ $batch->variant->product->name ?? 'N/A' }}</div>
                <div class="font-mono text-slate-500">{{ $batch->variant->sku }} &bull; {{ $batch->variant->color->name ?? '' }} {{ $batch->variant->size }}</div>
            </div>

            <div>
                <span class="text-slate-400 font-mono uppercase text-[10px]">Batch &amp; Lot Number</span>
                <div class="font-mono font-bold text-indigo-600 text-sm mt-0.5">{{ $batch->batch_number }}</div>
                <div class="font-mono text-slate-500">Lot: {{ $batch->lot_number ?: 'N/A' }}</div>
            </div>

            <div>
                <span class="text-slate-400 font-mono uppercase text-[10px]">Dates (Mfg &bull; Expiry)</span>
                <div class="font-mono text-slate-800 font-semibold mt-0.5">Mfg: {{ $batch->mfg_date ? $batch->mfg_date->format('Y-m-d') : '—' }}</div>
                <div class="font-mono text-rose-600 font-bold">Exp: {{ $batch->expiry_date->format('Y-m-d') }}</div>
            </div>

            <div>
                <span class="text-slate-400 font-mono uppercase text-[10px]">Dispatched vs In Stock</span>
                <div class="font-mono text-slate-900 font-black text-sm mt-0.5">{{ $total_dispatched }} units dispatched</div>
                <div class="font-mono text-emerald-600 font-semibold">{{ $batch->remaining_qty }} units still in warehouse</div>
            </div>
        </div>
    </div>

    <!-- Dispatched Customers Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-900">Customer Dispatch Registry</h2>
            <span class="text-xs text-slate-400 font-mono">{{ count($customer_dispatches) }} customer shipments</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-left text-xs">
                <thead class="bg-slate-50/70 text-slate-500 uppercase font-mono text-[10px]">
                    <tr>
                        <th class="px-6 py-3">Order Ref</th>
                        <th class="px-6 py-3">Order Date</th>
                        <th class="px-6 py-3">Customer Name</th>
                        <th class="px-6 py-3">Contact (Phone &bull; Email)</th>
                        <th class="px-6 py-3">Destination City / State</th>
                        <th class="px-6 py-3 text-right">Units Allocated</th>
                        <th class="px-6 py-3">Order Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($customer_dispatches as $dispatch)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-6 py-3.5 font-mono font-bold text-indigo-600">
                                <a href="{{ route('admin.orders.show', $dispatch['order_id']) }}" class="hover:underline">
                                    {{ $dispatch['order_reference'] }}
                                </a>
                            </td>
                            <td class="px-6 py-3.5 text-slate-500 font-mono text-[11px]">{{ $dispatch['order_date'] }}</td>
                            <td class="px-6 py-3.5 font-bold text-slate-900">{{ $dispatch['customer_name'] }}</td>
                            <td class="px-6 py-3.5 font-mono text-[11px] text-slate-600">
                                <div>{{ $dispatch['customer_phone'] }}</div>
                                <div class="text-[10px] text-slate-400">{{ $dispatch['customer_email'] }}</div>
                            </td>
                            <td class="px-6 py-3.5 text-slate-800">
                                {{ $dispatch['shipping_city'] }}, {{ $dispatch['shipping_state'] }}
                            </td>
                            <td class="px-6 py-3.5 text-right font-mono font-black text-rose-600 text-sm">
                                {{ $dispatch['qty_allocated'] }}
                            </td>
                            <td class="px-6 py-3.5">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-slate-100 text-slate-700">
                                    {{ $dispatch['order_status'] }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-slate-400">
                                No customer orders have been dispatched from this batch yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
