{{--
    ======================================================================
    ADMIN MODULE: PURCHASE ORDERS SUPPLIERS
    ======================================================================
    File: resources/views/admin/purchase-orders/suppliers.blade.php
    Responsibility: Back-office administration for PURCHASE ORDERS SUPPLIERS
    Features:
      - Standard administration UI following the Nex-on design guidelines
      - Accessible controls, Tailwind CSS styling, and reactive Alpine.js components
    Security & Access Control:
      - Guarded by Laravel authentication and RBAC permission middleware
    Operations & Maintenance:
      - Integrates with master administration shell (@extends('admin.layout'))
--}}
@extends('admin.layout', [
    'title' => 'Suppliers & Vendors Directory',
    'breadcrumbs' => [
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Purchase Orders', 'url' => route('admin.purchase-orders.index')],
        ['label' => 'Suppliers'],
    ]
])

@section('content')
<div class="space-y-6" x-data="{ addModalOpen: {{ request()->has('create') ? 'true' : 'false' }} }">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Suppliers &amp; Vendors Directory</h1>
            <p class="text-xs text-slate-500 mt-1">Manage garment mills, fabric suppliers &amp; procurement lead times</p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('admin.purchase-orders.index') }}" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition">
                &larr; Back to POs
            </a>
            <button @click="addModalOpen = true" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-md shadow-indigo-600/20 flex items-center gap-2 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Add Supplier</span>
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2 shadow-xs">
            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Suppliers Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-left text-sm">
                <thead class="bg-slate-50/70 text-slate-500 uppercase text-[11px] font-bold tracking-wider">
                    <tr>
                        <th class="px-6 py-3.5">Supplier Name &amp; Code</th>
                        <th class="px-6 py-3.5">Contact Person</th>
                        <th class="px-6 py-3.5">Phone &amp; Email</th>
                        <th class="px-6 py-3.5">Lead Time</th>
                        <th class="px-6 py-3.5">GSTIN</th>
                        <th class="px-6 py-3.5">Purchase Orders</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($suppliers as $supplier)
                        <tr class="hover:bg-slate-50/80 transition text-xs">
                            <td class="px-6 py-4 font-bold text-slate-900">
                                <div>{{ $supplier->name }}</div>
                                <div class="font-mono text-[10px] text-indigo-600">{{ $supplier->code }}</div>
                            </td>
                            <td class="px-6 py-4 text-slate-700">
                                {{ $supplier->contact_name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-mono text-slate-800">{{ $supplier->phone ?? 'N/A' }}</div>
                                <div class="text-[11px] text-slate-400">{{ $supplier->email ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 font-mono">
                                {{ $supplier->lead_time_days }} days
                            </td>
                            <td class="px-6 py-4 font-mono text-slate-700">
                                {{ $supplier->gst_number ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.purchase-orders.index', ['supplier_id' => $supplier->id]) }}" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-mono font-bold">
                                    {{ $supplier->purchase_orders_count }} POs
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-slate-400">
                                No suppliers registered yet. Click "Add Supplier" to create one.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal: Add Supplier -->
    <div x-show="addModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/70 backdrop-blur-xs p-4" x-cloak>
        <div @click.away="addModalOpen = false" class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl relative border border-slate-100">
            <h3 class="text-base font-extrabold text-slate-900 pb-3 border-b border-slate-100">Add New Supplier</h3>
            <form method="POST" action="{{ route('admin.suppliers.store') }}" class="py-4 space-y-3.5 text-xs">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Company / Supplier Name *</label>
                        <input type="text" name="name" required placeholder="e.g. Apex Mills"
                               class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Vendor Code *</label>
                        <input type="text" name="code" required placeholder="SUP-ALM01"
                               class="w-full px-3 py-2 rounded-xl border border-slate-200 font-mono focus:outline-none focus:border-indigo-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Contact Person</label>
                        <input type="text" name="contact_name" placeholder="Rajesh Singhania"
                               class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Phone Number</label>
                        <input type="text" name="phone" placeholder="+91 98200 11223"
                               class="w-full px-3 py-2 rounded-xl border border-slate-200 font-mono focus:outline-none focus:border-indigo-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Email Address</label>
                        <input type="email" name="email" placeholder="vendor@mills.com"
                               class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Lead Time (Days)</label>
                        <input type="number" name="lead_time_days" value="7" min="1"
                               class="w-full px-3 py-2 rounded-xl border border-slate-200 font-mono focus:outline-none focus:border-indigo-500">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">GSTIN (Tax ID)</label>
                    <input type="text" name="gst_number" placeholder="27AAACA1234F1Z9"
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 font-mono focus:outline-none focus:border-indigo-500">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Factory / Office Address</label>
                    <textarea name="address" rows="2" placeholder="Industrial Area, Surat, Gujarat"
                              class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:border-indigo-500"></textarea>
                </div>

                <div class="pt-2 flex items-center justify-end gap-2">
                    <button type="button" @click="addModalOpen = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold shadow-md shadow-indigo-600/20">
                        Save Supplier
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
