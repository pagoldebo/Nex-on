{{--
    ======================================================================
    ADMIN MODULE: INVENTORY OUTLETS INDEX
    ======================================================================
    File: resources/views/admin/inventory/outlets/index.blade.php
    Responsibility: Back-office administration for INVENTORY OUTLETS INDEX
    Features:
      - Standard administration UI following the Nex-on design guidelines
      - Accessible controls, Tailwind CSS styling, and reactive Alpine.js components
    Security & Access Control:
      - Guarded by Laravel authentication and RBAC permission middleware
    Operations & Maintenance:
      - Integrates with master administration shell (@extends('admin.layout'))
--}}
@extends('admin.layout')

@section('title', 'Multi-Outlet & Warehouse Management')

@section('content')
<div class="space-y-6 pb-12" x-data="{ showCreateModal: false }">

    <!-- Header & Action Bar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 text-[10px] font-bold rounded-md bg-emerald-50 text-emerald-600 border border-emerald-200/50 uppercase font-mono tracking-wider">Multi-Channel Hub</span>
                <span class="text-xs text-slate-400 font-medium">Phase 3 &bull; Outlets &amp; Warehousing</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 mt-1">Outlets &amp; Warehouse Network</h1>
            <p class="text-xs text-slate-500 mt-0.5">Manage physical retail boutiques, pop-up stores, regional fulfillment warehouses, and channel allocations.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.transfers.index') }}" 
               class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition flex items-center gap-2 shadow-xs">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                <span>Stock Transfers</span>
            </a>

            <button @click="showCreateModal = true"
                    class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-md shadow-indigo-600/20 flex items-center gap-2 transition">
                <svg class="w-4 h-4 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Register Outlet / WH</span>
            </button>
        </div>
    </div>

    <!-- KPI Summary Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Locations</div>
            <div class="text-2xl font-black text-slate-900 mt-1">{{ number_format($kpis['total_outlets']) }}</div>
            <div class="text-[10px] text-slate-500 mt-1">Stores &amp; Warehouses</div>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
            <div class="text-[11px] font-bold text-emerald-600 uppercase tracking-wider">Active Boutiques</div>
            <div class="text-2xl font-black text-slate-900 mt-1">{{ number_format($kpis['active_stores']) }}</div>
            <div class="text-[10px] text-emerald-600 font-semibold mt-1">Live Walk-in POS</div>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
            <div class="text-[11px] font-bold text-indigo-600 uppercase tracking-wider">Fulfillment WH</div>
            <div class="text-2xl font-black text-slate-900 mt-1">{{ number_format($kpis['warehouses']) }}</div>
            <div class="text-[10px] text-indigo-500 font-medium mt-1">Regional depots</div>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
            <div class="text-[11px] font-bold text-amber-600 uppercase tracking-wider">Network Stock</div>
            <div class="text-2xl font-black text-slate-900 mt-1">{{ number_format($kpis['total_units']) }}</div>
            <div class="text-[10px] text-slate-500 mt-1">Units distributed</div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs text-xs">
        <form method="GET" action="{{ route('admin.outlets.index') }}" class="flex flex-wrap items-center gap-2 flex-1">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, code or city..."
                   class="px-3.5 py-2 rounded-xl border border-slate-200 text-slate-800 placeholder-slate-400 focus:outline-none focus:border-indigo-500 w-64">

            <select name="type" class="px-3 py-2 rounded-xl border border-slate-200 text-slate-800 focus:outline-none focus:border-indigo-500">
                <option value="">All Types</option>
                <option value="store" {{ request('type') === 'store' ? 'selected' : '' }}>Retail Store</option>
                <option value="warehouse" {{ request('type') === 'warehouse' ? 'selected' : '' }}>Warehouse</option>
                <option value="outlet" {{ request('type') === 'outlet' ? 'selected' : '' }}>Outlet / Pop-up</option>
            </select>

            <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 text-white font-bold hover:bg-slate-800 transition">Filter</button>
            @if(request()->anyFilled(['search', 'type']))
                <a href="{{ route('admin.outlets.index') }}" class="px-3 py-2 rounded-xl text-slate-500 hover:text-slate-900 transition">Reset</a>
            @endif
        </form>
    </div>

    <!-- Outlets Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50/75 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Location &amp; Code</th>
                        <th class="px-6 py-4">Type &amp; Channel</th>
                        <th class="px-6 py-4">City / Address</th>
                        <th class="px-6 py-4">Contact</th>
                        <th class="px-6 py-4">On-Hand Stock</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($outlets as $outlet)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900 flex items-center gap-1.5">
                                    <span>{{ $outlet->name }}</span>
                                    @if($outlet->is_central)
                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-mono font-bold bg-amber-50 text-amber-700 border border-amber-200/60 uppercase">Central</span>
                                    @endif
                                </div>
                                <div class="text-[11px] font-mono text-slate-400 mt-0.5">{{ $outlet->code }}</div>
                            </td>

                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ $outlet->type === 'warehouse' ? 'bg-indigo-50 text-indigo-700 border border-indigo-200/50' : 'bg-emerald-50 text-emerald-700 border border-emerald-200/50' }}">
                                    {{ ucfirst($outlet->type) }}
                                </span>
                                <div class="text-[10px] text-slate-400 mt-1 capitalize">Channel: {{ $outlet->channel }}</div>
                            </td>

                            <td class="px-6 py-4">
                                <div class="text-slate-800">{{ $outlet->city ?: 'N/A' }}</div>
                                <div class="text-[11px] text-slate-400 truncate max-w-xs">{{ $outlet->address ?: 'No address specified' }}</div>
                            </td>

                            <td class="px-6 py-4">
                                <div>{{ $outlet->phone ?: '—' }}</div>
                                <div class="text-[11px] text-slate-400">{{ $outlet->email ?: '' }}</div>
                            </td>

                            <td class="px-6 py-4">
                                <span class="font-bold text-slate-900">{{ number_format($outlet->totalStockOnHand()) }} units</span>
                                <div class="text-[10px] text-slate-400">{{ $outlet->uniqueSkusCount() }} SKUs stocked</div>
                            </td>

                            <td class="px-6 py-4">
                                @if($outlet->is_active)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        Active
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-500">
                                        Inactive
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.outlets.show', $outlet) }}" 
                                   class="px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 font-bold transition inline-flex items-center gap-1">
                                    <span>Manage Stock</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                No outlets registered yet. Click "Register Outlet / WH" above to add your first branch.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($outlets->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $outlets->links() }}
            </div>
        @endif
    </div>

    <!-- Create Outlet Modal -->
    <div x-show="showCreateModal" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs"
         style="display: none;">
        
        <div @click.away="showCreateModal = false" class="bg-white rounded-3xl max-w-lg w-full p-6 text-slate-900 shadow-2xl space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h3 class="text-base font-black">Register Outlet or Warehouse</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Add a new physical retail store, pop-up boutique, or distribution center.</p>
                </div>
                <button @click="showCreateModal = false" type="button" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
            </div>

            <form method="POST" action="{{ route('admin.outlets.store') }}" class="space-y-4 text-xs">
                @csrf

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Outlet / Location Name *</label>
                        <input type="text" name="name" required placeholder="e.g. Bandra Flagship Boutique"
                               class="w-full px-3.5 py-2 rounded-xl border border-slate-200 focus:outline-none focus:border-indigo-500 font-medium">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Location Code *</label>
                        <input type="text" name="code" required placeholder="e.g. BOM-01"
                               class="w-full px-3.5 py-2 rounded-xl border border-slate-200 focus:outline-none focus:border-indigo-500 font-mono uppercase">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Type *</label>
                        <select name="type" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 focus:outline-none focus:border-indigo-500">
                            <option value="store">Retail Store / Boutique</option>
                            <option value="warehouse">Fulfillment Warehouse</option>
                            <option value="outlet">Outlet / Pop-up</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Channel Allocation *</label>
                        <select name="channel" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 focus:outline-none focus:border-indigo-500">
                            <option value="all">All Channels (POS &amp; Online)</option>
                            <option value="pos">POS Walk-In Only</option>
                            <option value="online">Online Fulfillment Only</option>
                            <option value="wholesale">B2B Wholesale</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">City</label>
                        <input type="text" name="city" placeholder="Mumbai"
                               class="w-full px-3.5 py-2 rounded-xl border border-slate-200 focus:outline-none focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Phone</label>
                        <input type="text" name="phone" placeholder="+91 98765 43210"
                               class="w-full px-3.5 py-2 rounded-xl border border-slate-200 focus:outline-none focus:border-indigo-500">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Full Address</label>
                    <textarea name="address" rows="2" placeholder="Street, Building, Area, Pincode"
                              class="w-full px-3.5 py-2 rounded-xl border border-slate-200 focus:outline-none focus:border-indigo-500"></textarea>
                </div>

                <div class="flex items-center gap-6 pt-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_central" value="1" class="rounded text-indigo-600 focus:ring-0">
                        <span class="font-bold text-slate-700">Central Hub / Primary Warehouse</span>
                    </label>

                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" checked class="rounded text-indigo-600 focus:ring-0">
                        <span class="font-bold text-slate-700">Active</span>
                    </label>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button @click="showCreateModal = false" type="button" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 font-bold transition">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold transition shadow-sm">Save Outlet</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
