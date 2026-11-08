{{--
    ======================================================================
    ADMIN MODULE: INVENTORY BATCHES
    ======================================================================
    File: resources/views/admin/inventory/batches.blade.php
    Responsibility: Back-office administration for INVENTORY BATCHES
    Features:
      - Standard administration UI following the Nex-on design guidelines
      - Accessible controls, Tailwind CSS styling, and reactive Alpine.js components
    Security & Access Control:
      - Guarded by Laravel authentication and RBAC permission middleware
    Operations & Maintenance:
      - Integrates with master administration shell (@extends('admin.layout'))
--}}
@extends('admin.layout')

@section('title', 'Batch, Lot & Expiry Tracking')

@section('content')
<div class="space-y-6 pb-12" x-data="{ showModal: false }">

    <!-- Header & Action Bar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 text-[10px] font-bold rounded-md bg-indigo-50 text-indigo-600 border border-indigo-200/50 uppercase font-mono tracking-wider">FIFO Inventory</span>
                <span class="text-xs text-slate-400 font-medium">Phase 2 &bull; Quality Assurance &amp; Expiry Monitor</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 mt-1">Batch, Lot &amp; Expiry Tracking</h1>
            <p class="text-xs text-slate-500 mt-0.5">Maintain audit traceability, FIFO stock picking, near-expiry alerts, and instant customer recall reports.</p>
        </div>

        <button @click="showModal = true" type="button"
                class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-md shadow-indigo-600/20 flex items-center gap-2 transition self-start md:self-auto">
            <svg class="w-4 h-4 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Register New Batch</span>
        </button>
    </div>

    <!-- KPI Metrics Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Expired Batches -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Expired Batches</span>
                <div class="text-2xl font-black text-rose-600 mt-1 font-mono">{{ $expiredCount }}</div>
                <div class="text-[10px] text-rose-500 mt-1 font-medium">Past expiry date &bull; quarantined</div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold">
                ✕
            </div>
        </div>

        <!-- Expiring in 30 Days -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Expiring in 30 Days</span>
                <div class="text-2xl font-black text-amber-500 mt-1 font-mono">{{ $expiring30dCount }}</div>
                <div class="text-[10px] text-slate-400 mt-1">Value at risk: ₹{{ number_format($valueAtRisk30d, 2) }}</div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                ⚠️
            </div>
        </div>

        <!-- Active Batches -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Active Batches</span>
                <div class="text-2xl font-black text-indigo-600 mt-1 font-mono">{{ $totalActiveBatches }}</div>
                <div class="text-[10px] text-slate-400 mt-1">FIFO active in store</div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            </div>
        </div>

        <!-- Total Batched Units -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Batched Stock Units</span>
                <div class="text-2xl font-black text-slate-900 mt-1 font-mono">{{ number_format($totalRemainingQty) }}</div>
                <div class="text-[10px] text-slate-400 mt-1">Available for order allocation</div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                ✓
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-xs">
        <div class="flex items-center gap-1.5 flex-wrap text-xs">
            <span class="text-slate-400 font-semibold mr-1 font-mono uppercase text-[11px]">Filter Expiry:</span>
            @php
                $statusPills = [
                    'all'          => 'All Batches',
                    'expired'      => 'Expired (' . $expiredCount . ')',
                    'expiring_30d' => 'Expiring in 30 Days (' . $expiring30dCount . ')',
                    'expiring_60d' => 'Expiring in 60 Days',
                    'expiring_90d' => 'Expiring in 90 Days',
                    'healthy'      => 'Healthy (>90 Days)',
                ];
            @endphp

            @foreach($statusPills as $key => $label)
                <a href="{{ route('admin.inventory.batches.index', ['status' => $key]) }}"
                   class="px-3 py-1.5 rounded-lg font-medium transition {{ $filter === $key ? 'bg-slate-900 text-white shadow-xs font-bold' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </div>

    <!-- Batches Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-900">Registered Product Batches</h2>
            <span class="text-xs text-slate-400 font-mono">{{ $batches->total() }} batches</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-left text-xs">
                <thead class="bg-slate-50/70 text-slate-500 uppercase font-mono text-[10px]">
                    <tr>
                        <th class="px-6 py-3">Batch Number</th>
                        <th class="px-6 py-3">Product &amp; SKU</th>
                        <th class="px-6 py-3">Mfg Date</th>
                        <th class="px-6 py-3">Expiry Date</th>
                        <th class="px-6 py-3 text-right">Remaining / Initial</th>
                        <th class="px-6 py-3 text-right">Cost (₹)</th>
                        <th class="px-6 py-3">Expiry Status</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($batches as $batch)
                        @php
                            $days = $batch->days_until_expiry;
                        @endphp
                        <tr class="hover:bg-slate-50/50 transition">
                            <!-- Batch & Lot -->
                            <td class="px-6 py-3.5">
                                <div class="font-mono font-bold text-indigo-600">{{ $batch->batch_number }}</div>
                                @if($batch->lot_number)
                                    <div class="text-[10px] text-slate-400 font-mono">Lot: {{ $batch->lot_number }}</div>
                                @endif
                            </td>

                            <!-- Product -->
                            <td class="px-6 py-3.5">
                                <div class="font-bold text-slate-900">{{ $batch->variant->product->name ?? 'N/A' }}</div>
                                <div class="text-[11px] text-slate-500 font-mono">
                                    {{ $batch->variant->sku }} &bull; {{ $batch->variant->color->name ?? '' }} {{ $batch->variant->size }}
                                </div>
                            </td>

                            <!-- Mfg Date -->
                            <td class="px-6 py-3.5 text-slate-500 font-mono text-[11px]">
                                {{ $batch->mfg_date ? $batch->mfg_date->format('Y-m-d') : '—' }}
                            </td>

                            <!-- Expiry Date -->
                            <td class="px-6 py-3.5 font-mono text-[11px]">
                                <div class="font-bold {{ $days < 0 ? 'text-rose-600' : ($days <= 30 ? 'text-amber-600 font-black' : 'text-slate-800') }}">
                                    {{ $batch->expiry_date->format('Y-m-d') }}
                                </div>
                                <div class="text-[10px] text-slate-400">
                                    @if($days < 0)
                                        Expired {{ abs($days) }}d ago
                                    @else
                                        {{ $days }} days left
                                    @endif
                                </div>
                            </td>

                            <!-- Remaining / Initial -->
                            <td class="px-6 py-3.5 text-right font-mono">
                                <span class="font-bold text-slate-900 text-sm">{{ $batch->remaining_qty }}</span>
                                <span class="text-slate-400">/ {{ $batch->initial_qty }}</span>
                            </td>

                            <!-- Cost -->
                            <td class="px-6 py-3.5 text-right font-mono text-slate-700">
                                ₹{{ number_format($batch->cost_price ?: 0, 2) }}
                            </td>

                            <!-- Expiry Pill -->
                            <td class="px-6 py-3.5">
                                @if($days < 0)
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-rose-100 text-rose-800">Expired</span>
                                @elseif($days <= 30)
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-amber-50 text-amber-700 border border-amber-200 animate-pulse">Expiring ({{ $days }}d)</span>
                                @elseif($days <= 60)
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-yellow-50 text-yellow-700 border border-yellow-200">Expiring ({{ $days }}d)</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-emerald-50 text-emerald-700 border border-emerald-200">Healthy</span>
                                @endif
                            </td>

                            <!-- Action -->
                            <td class="px-6 py-3.5 text-right">
                                <a href="{{ route('admin.inventory.batches.trace', $batch) }}"
                                   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition"
                                   title="Trace all customer dispatches for recall">
                                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                    <span>Recall Trace</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-center text-slate-400">
                                No product batches found matching selected filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($batches->hasPages())
            <div class="px-6 py-3 border-t border-slate-100 bg-slate-50/50">
                {{ $batches->links() }}
            </div>
        @endif
    </div>

    <!-- Register Batch Modal -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="showModal = false" class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-base font-bold text-slate-900">Register New Product Batch</h3>
                <button @click="showModal = false" type="button" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
            </div>

            <form method="POST" action="{{ route('admin.inventory.batches.store') }}" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Select Product SKU *</label>
                    <select name="product_variant_id" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-slate-800 focus:outline-none focus:border-indigo-500">
                        <option value="">-- Choose SKU Variant --</option>
                        @foreach($variants as $v)
                            <option value="{{ $v->id }}">{{ $v->sku }} — {{ $v->product->name ?? 'N/A' }} ({{ $v->color->name ?? '' }} {{ $v->size }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Batch Number *</label>
                        <input type="text" name="batch_number" required placeholder="e.g. BATCH-2026-001"
                               class="w-full px-3.5 py-2 rounded-xl border border-slate-200 font-mono text-slate-900 focus:outline-none focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Lot Number (Optional)</label>
                        <input type="text" name="lot_number" placeholder="e.g. LOT-A01"
                               class="w-full px-3.5 py-2 rounded-xl border border-slate-200 font-mono text-slate-900 focus:outline-none focus:border-indigo-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Manufacturing Date</label>
                        <input type="date" name="mfg_date"
                               class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-slate-900 focus:outline-none focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Expiry Date *</label>
                        <input type="date" name="expiry_date" required
                               class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-slate-900 focus:outline-none focus:border-indigo-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Initial Quantity *</label>
                        <input type="number" name="initial_qty" min="1" required placeholder="Units"
                               class="w-full px-3.5 py-2 rounded-xl border border-slate-200 font-mono text-slate-900 focus:outline-none focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Cost Price / Unit (₹)</label>
                        <input type="number" step="0.01" name="cost_price" placeholder="Cost"
                               class="w-full px-3.5 py-2 rounded-xl border border-slate-200 font-mono text-slate-900 focus:outline-none focus:border-indigo-500">
                    </div>
                </div>

                <div class="flex items-center gap-2 pt-1">
                    <input type="checkbox" name="sync_variant_stock" id="sync_variant_stock" value="1" checked class="rounded text-indigo-600">
                    <label for="sync_variant_stock" class="text-slate-600 font-medium">Increment total SKU variant on-hand stock by this batch quantity</label>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                    <button @click="showModal = false" type="button" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 font-semibold hover:bg-slate-200 transition">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 text-white font-bold hover:bg-indigo-700 transition">Save Batch</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
