{{--
    ======================================================================
    ADMIN MODULE: INVENTORY TRANSFERS CREATE
    ======================================================================
    File: resources/views/admin/inventory/transfers/create.blade.php
    Responsibility: Back-office administration for INVENTORY TRANSFERS CREATE
    Features:
      - Standard administration UI following the Nex-on design guidelines
      - Accessible controls, Tailwind CSS styling, and reactive Alpine.js components
    Security & Access Control:
      - Guarded by Laravel authentication and RBAC permission middleware
    Operations & Maintenance:
      - Integrates with master administration shell (@extends('admin.layout'))
--}}
@extends('admin.layout')

@section('title', 'Initiate Stock Transfer')

@section('content')
<div class="max-w-4xl mx-auto space-y-6 pb-12" x-data="{
    rows: [
        { variant_id: '', qty: 1 }
    ],
    addRow() {
        this.rows.push({ variant_id: '', qty: 1 });
    },
    removeRow(index) {
        if (this.rows.length > 1) {
            this.rows.splice(index, 1);
        }
    }
}">

    <!-- Header & Action Bar -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.transfers.index') }}" class="text-xs font-bold text-indigo-600 hover:underline flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    <span>Back to Transfers</span>
                </a>
                <span class="text-slate-300">&bull;</span>
                <span class="text-xs text-slate-500 font-medium">New Stock Movement</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 mt-1">Initiate Inter-Store Transfer</h1>
        </div>
    </div>

    <!-- Transfer Form -->
    <form method="POST" action="{{ route('admin.transfers.store') }}" class="space-y-6 text-xs">
        @csrf

        <!-- Routing Card -->
        <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs space-y-4">
            <h2 class="text-sm font-bold text-slate-900">Transfer Routing &amp; Origin</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Origin Source Outlet / Warehouse *</label>
                    <select name="from_outlet_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-slate-900 font-bold focus:outline-none focus:border-indigo-500">
                        <option value="">-- Select Source Location --</option>
                        @foreach($outlets as $o)
                            <option value="{{ $o->id }}">{{ $o->name }} ({{ $o->code }})</option>
                        @endforeach
                    </select>
                    <span class="text-[10px] text-slate-400">Stock will be verified and deducted from this branch.</span>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Destination Target Outlet / Warehouse *</label>
                    <select name="to_outlet_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-slate-900 font-bold focus:outline-none focus:border-indigo-500">
                        <option value="">-- Select Destination Location --</option>
                        @foreach($outlets as $o)
                            <option value="{{ $o->id }}">{{ $o->name }} ({{ $o->code }})</option>
                        @endforeach
                    </select>
                    <span class="text-[10px] text-slate-400">Stock will be incremented at this branch upon receipt.</span>
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Logistics / Manifest Notes (Optional)</label>
                <textarea name="notes" rows="2" placeholder="e.g. Courier: BlueDart, AWB: 98127391823, Packed in 2 sealed boxes"
                          class="w-full px-3.5 py-2 rounded-xl border border-slate-200 focus:outline-none focus:border-indigo-500"></textarea>
            </div>
        </div>

        <!-- Line Items Card -->
        <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Transfer Manifest Line Items</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Select the SKU variants and quantities to transfer.</p>
                </div>

                <button @click="addRow()" type="button"
                        class="px-3.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs transition flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Add Item</span>
                </button>
            </div>

            <div class="space-y-3">
                <template x-for="(row, idx) in rows" :key="idx">
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/70 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-white border border-slate-200 text-slate-500 font-bold flex items-center justify-center text-xs shrink-0"
                             x-text="idx + 1"></div>

                        <div class="flex-1">
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Product Variant / SKU *</label>
                            <select :name="'items[' + idx + '][variant_id]'" x-model="row.variant_id" required
                                    class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white font-mono text-slate-900 focus:outline-none focus:border-indigo-500">
                                <option value="">-- Choose Variant --</option>
                                @foreach($variants as $v)
                                    <option value="{{ $v->id }}">{{ $v->sku }} — {{ $v->product->name ?? '' }} (Size: {{ $v->size }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="w-32">
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Transfer Qty *</label>
                            <input type="number" :name="'items[' + idx + '][qty]'" x-model="row.qty" min="1" required
                                   class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white font-mono text-slate-900 font-bold focus:outline-none focus:border-indigo-500">
                        </div>

                        <button @click="removeRow(idx)" type="button" 
                                class="self-end mb-1 p-2 rounded-xl hover:bg-rose-50 text-slate-400 hover:text-rose-600 transition"
                                :disabled="rows.length <= 1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </template>
            </div>
        </div>

        <!-- Submit Bar -->
        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('admin.transfers.index') }}" class="px-5 py-2.5 rounded-xl text-slate-600 hover:bg-slate-100 font-bold transition">Cancel</a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold transition shadow-md shadow-indigo-600/20 flex items-center gap-2">
                <svg class="w-4 h-4 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>Save &amp; Generate Transfer Manifest</span>
            </button>
        </div>
    </form>

</div>
@endsection
