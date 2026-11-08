{{--
    ======================================================================
    ADMIN MODULE: INVENTORY OUTLETS SHOW
    ======================================================================
    File: resources/views/admin/inventory/outlets/show.blade.php
    Responsibility: Back-office administration for INVENTORY OUTLETS SHOW
    Features:
      - Standard administration UI following the Nex-on design guidelines
      - Accessible controls, Tailwind CSS styling, and reactive Alpine.js components
    Security & Access Control:
      - Guarded by Laravel authentication and RBAC permission middleware
    Operations & Maintenance:
      - Integrates with master administration shell (@extends('admin.layout'))
--}}
@extends('admin.layout')

@section('title', "{$outlet->name} — Stock Inventory")

@section('content')
<div class="space-y-6 pb-12" x-data="{ showAdjustModal: false, selectedVariantId: '', selectedSku: '' }">

    <!-- Header & Action Bar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.outlets.index') }}" class="text-xs font-bold text-indigo-600 hover:underline flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    <span>Back to Outlets</span>
                </a>
                <span class="text-slate-300">&bull;</span>
                <span class="px-2 py-0.5 text-[10px] font-bold rounded-md bg-slate-100 text-slate-700 font-mono">{{ $outlet->code }}</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 mt-1">{{ $outlet->name }}</h1>
            <p class="text-xs text-slate-500 mt-0.5">
                {{ ucfirst($outlet->type) }} &bull; {{ $outlet->city ?: 'No city' }} &bull; Channel: {{ ucfirst($outlet->channel) }} &bull; Total On-Hand: <span class="font-bold text-slate-900">{{ number_format($outlet->totalStockOnHand()) }} units</span>
            </p>
        </div>

        <div class="flex items-center gap-3">
            <button @click="showAdjustModal = true; selectedVariantId = ''; selectedSku = '';"
                    class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-md shadow-indigo-600/20 flex items-center gap-2 transition">
                <svg class="w-4 h-4 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Stock / Adjust SKU</span>
            </button>
        </div>
    </div>

    <!-- Stock Search -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs text-xs">
        <form method="GET" action="{{ route('admin.outlets.show', $outlet) }}" class="flex items-center gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search SKU or product in this outlet..."
                   class="px-3.5 py-2 rounded-xl border border-slate-200 text-slate-800 placeholder-slate-400 focus:outline-none focus:border-indigo-500 w-80">
            <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 text-white font-bold hover:bg-slate-800 transition">Search</button>
            @if(request()->filled('search'))
                <a href="{{ route('admin.outlets.show', $outlet) }}" class="px-3 py-2 rounded-xl text-slate-500 hover:text-slate-900 transition">Reset</a>
            @endif
        </form>
    </div>

    <!-- Stocks Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50/75 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Product &amp; Variant</th>
                        <th class="px-6 py-4">SKU</th>
                        <th class="px-6 py-4">Shelf / Bin Location</th>
                        <th class="px-6 py-4">Available Units</th>
                        <th class="px-6 py-4 text-right">Quick Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($stocks as $stock)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900">{{ $stock->variant->product->name ?? 'Unknown Product' }}</div>
                                <div class="text-[11px] text-slate-400">Size: {{ $stock->variant->size ?? 'Standard' }}</div>
                            </td>

                            <td class="px-6 py-4">
                                <span class="font-mono text-slate-700 bg-slate-100 px-2 py-0.5 rounded font-bold">{{ $stock->variant->sku }}</span>
                            </td>

                            <td class="px-6 py-4">
                                <span class="text-slate-700">{{ $stock->shelf_bin_location ?: '—' }}</span>
                            </td>

                            <td class="px-6 py-4">
                                <span class="text-sm font-black {{ $stock->stock > 5 ? 'text-slate-900' : ($stock->stock > 0 ? 'text-amber-600' : 'text-rose-600') }}">
                                    {{ $stock->stock }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-right">
                                <button @click="showAdjustModal = true; selectedVariantId = '{{ $stock->product_variant_id }}'; selectedSku = '{{ $stock->variant->sku }}';"
                                        class="px-2.5 py-1 rounded-lg border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold text-[11px] transition">
                                    Adjust
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                No inventory records mapped to this location yet. Click "Stock / Adjust SKU" above to assign stock.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($stocks->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $stocks->links() }}
            </div>
        @endif
    </div>

    <!-- Adjust Stock Modal -->
    <div x-show="showAdjustModal" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs"
         style="display: none;">
        
        <div @click.away="showAdjustModal = false" class="bg-white rounded-3xl max-w-md w-full p-6 text-slate-900 shadow-2xl space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h3 class="text-base font-black">Stock Adjustment</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Location: <span class="font-bold text-slate-800">{{ $outlet->name }}</span></p>
                </div>
                <button @click="showAdjustModal = false" type="button" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
            </div>

            <form method="POST" action="{{ route('admin.outlets.adjustStock', $outlet) }}" class="space-y-4 text-xs">
                @csrf

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Select Product Variant / SKU *</label>
                    <select name="product_variant_id" x-model="selectedVariantId" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 focus:outline-none focus:border-indigo-500 font-mono">
                        <option value="">-- Choose SKU --</option>
                        @foreach($variants as $v)
                            <option value="{{ $v->id }}">{{ $v->sku }} — {{ $v->product->name ?? '' }} ({{ $v->size }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Adjustment Delta (Positive or Negative) *</label>
                    <input type="number" name="stock_delta" required placeholder="e.g. +20 or -5"
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-200 focus:outline-none focus:border-indigo-500 font-mono text-slate-900 font-bold">
                    <span class="text-[10px] text-slate-400">Enter positive integer to add stock, negative to decrement.</span>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Shelf / Bin Location (Optional)</label>
                    <input type="text" name="shelf_bin_location" placeholder="e.g. Aisle 3 - Bin 14"
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-200 focus:outline-none focus:border-indigo-500">
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button @click="showAdjustModal = false" type="button" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 font-bold transition">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold transition shadow-sm">Apply Adjustment</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
