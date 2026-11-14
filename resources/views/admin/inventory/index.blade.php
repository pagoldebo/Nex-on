{{--
    ======================================================================
    ADMIN MODULE: INVENTORY INDEX
    ======================================================================
    File: resources/views/admin/inventory/index.blade.php
    Responsibility: Back-office administration for INVENTORY INDEX
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
<div class="space-y-6" x-data="inventoryManager()">
    <!-- Header -->
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Inventory &amp; SKU Control Center</h1>
            <p class="mt-1 text-sm text-slate-500">Track real-time stock levels by SKU, make instant warehouse adjustments, and manage live inventory valuation.</p>
        </div>
        <div class="mt-4 sm:mt-0 flex items-center gap-3">
            <a href="{{ route('admin.inventory.export') }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold rounded-xl transition">
                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Export CSV</span>
            </a>

            <button type="button" @click="openQuickAddModal()"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Add Product via SKU</span>
            </button>
        </div>
    </div>

    <!-- 5 KPI Summary Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- Total SKUs -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs">
            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Tracked SKUs</div>
            <div class="text-2xl font-black text-slate-900 mt-1 font-mono">{{ number_format($totalSkus) }}</div>
            <div class="text-[11px] text-slate-500 mt-1">Unique size/color variations</div>
        </div>

        <!-- Total Units -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs">
            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Units in Stock</div>
            <div class="text-2xl font-black text-indigo-600 mt-1 font-mono">{{ number_format($totalUnits) }}</div>
            <div class="text-[11px] text-slate-500 mt-1">Physical pieces across atelier</div>
        </div>

        <!-- Total Inventory Valuation -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs">
            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Stock Value</div>
            <div class="text-2xl font-black text-slate-900 mt-1">₹{{ number_format($totalValuation, 0) }}</div>
            <div class="text-[11px] text-slate-500 mt-1">Retail price inventory asset</div>
        </div>

        <!-- Low Stock Alerts -->
        <a href="{{ route('admin.inventory.index', ['status' => 'low_stock']) }}" 
           class="bg-white rounded-2xl p-4 border transition {{ $status === 'low_stock' ? 'border-amber-400 ring-2 ring-amber-400/20 bg-amber-50/20' : 'border-slate-200/80 hover:border-amber-300' }} shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-amber-600">Low Stock</span>
                <span class="w-2 h-2 rounded-full bg-amber-500 {{ $lowStockCount > 0 ? 'animate-ping' : '' }}"></span>
            </div>
            <div class="text-2xl font-black text-amber-700 mt-1 font-mono">{{ $lowStockCount }}</div>
            <div class="text-[11px] text-slate-500 mt-1">&le; 5 units remaining</div>
        </a>

        <!-- Out of Stock -->
        <a href="{{ route('admin.inventory.index', ['status' => 'out_of_stock']) }}" 
           class="bg-white rounded-2xl p-4 border transition {{ $status === 'out_of_stock' ? 'border-rose-400 ring-2 ring-rose-400/20 bg-rose-50/20' : 'border-slate-200/80 hover:border-rose-300' }} shadow-xs col-span-2 lg:col-span-1">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-rose-600">Out of Stock</span>
                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
            </div>
            <div class="text-2xl font-black text-rose-700 mt-1 font-mono">{{ $outOfStockCount }}</div>
            <div class="text-[11px] text-slate-500 mt-1">0 units remaining (hidden)</div>
        </a>
    </div>

    <!-- Filters & Search Bar -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-xs">
        <form method="GET" action="{{ route('admin.inventory.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-center">
            <!-- Search by SKU / Name -->
            <div class="lg:col-span-5 relative">
                <input type="text" name="q" value="{{ $search ?? '' }}" placeholder="Search by SKU (e.g. LS-SILK-001...), product name, or size..." 
                       class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="m21 21-4.35-4.35"/></svg>
            </div>

            <!-- Filter by Category -->
            <div class="lg:col-span-3">
                <select name="category_id" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-700 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition bg-white">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ ($categoryId ?? '') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter by Stock Status -->
            <div class="lg:col-span-2">
                <select name="status" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-700 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition bg-white">
                    <option value="all" {{ ($status ?? '') === 'all' ? 'selected' : '' }}>All Statuses</option>
                    <option value="in_stock" {{ ($status ?? '') === 'in_stock' ? 'selected' : '' }}>In Stock (&gt; 5)</option>
                    <option value="low_stock" {{ ($status ?? '') === 'low_stock' ? 'selected' : '' }}>Low Stock (&le; 5)</option>
                    <option value="out_of_stock" {{ ($status ?? '') === 'out_of_stock' ? 'selected' : '' }}>Out of Stock (0)</option>
                </select>
            </div>

            <!-- Submit & Reset Buttons -->
            <div class="lg:col-span-2 flex items-center gap-2">
                <button type="submit" class="flex-1 py-2 px-3 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition">
                    Filter
                </button>
                @if(!empty($search) || !empty($categoryId) || (!empty($status) && $status !== 'all'))
                    <a href="{{ route('admin.inventory.index') }}" class="py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-semibold transition" title="Reset Filters">
                        ✕
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Inventory SKU Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-left text-sm">
                <thead class="bg-slate-50/70 text-slate-500 uppercase text-[11px] font-bold tracking-wider">
                    <tr>
                        <th class="px-6 py-3.5">Variant SKU</th>
                        <th class="px-6 py-3.5">Product &amp; Category</th>
                        <th class="px-6 py-3.5">Color &amp; Size</th>
                        <th class="px-6 py-3.5">Price (₹)</th>
                        <th class="px-6 py-3.5 text-center">Stock Status</th>
                        <th class="px-6 py-3.5">Quick Stock Adjustment</th>
                        <th class="px-6 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($variants as $variant)
                        @php
                            $prod = $variant->product;
                            $color = $variant->color;
                            $coverImg = $prod->cover_image;
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition" id="row-variant-{{ $variant->id }}">
                            <!-- Variant SKU -->
                            <td class="px-6 py-4">
                                <div class="font-mono text-xs font-bold text-slate-900 bg-slate-100 px-2.5 py-1 rounded-lg border border-slate-200/80 inline-block">
                                    {{ $variant->sku ?? 'NO-SKU' }}
                                </div>
                                <div class="text-[10px] text-slate-400 font-mono mt-1">
                                    Base: <span class="font-semibold text-slate-600">{{ $prod->sku ?? 'N/A' }}</span>
                                </div>
                            </td>

                            <!-- Product & Category -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-12 bg-slate-100 rounded-lg overflow-hidden shrink-0 border border-slate-200 flex items-center justify-center">
                                        @if($coverImg)
                                            <img src="{{ $coverImg }}" alt="{{ $prod->name }}" class="w-full h-full object-cover">
                                        @else
                                            <span class="text-[9px] text-slate-400 font-bold uppercase">No Img</span>
                                        @endif
                                    </div>
                                    <div class="min-w-0 max-w-xs">
                                        <div class="font-bold text-slate-900 text-xs truncate">{{ $prod->name }}</div>
                                        <div class="text-[11px] text-slate-400 flex items-center gap-1.5 mt-0.5">
                                            <span class="font-semibold text-indigo-600">{{ $prod->category->name ?? 'Uncategorized' }}</span>
                                            <span>·</span>
                                            <span class="inline-flex items-center gap-1 text-[10px] {{ $prod->is_active ? 'text-emerald-600 font-bold' : 'text-slate-400' }}">
                                                <span class="w-1.5 h-1.5 rounded-full {{ $prod->is_active ? 'bg-emerald-500' : 'bg-slate-300' }}"></span>
                                                {{ $prod->is_active ? 'Live on Site' : 'Draft' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Color & Size -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    @if($color && $color->hex)
                                        <span class="w-3.5 h-3.5 rounded-full border border-black/20 shrink-0" style="background-color: {{ $color->hex }}"></span>
                                    @endif
                                    <span class="text-xs font-semibold text-slate-800">{{ $color->name ?? 'Default' }}</span>
                                </div>
                                <div class="mt-1">
                                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-slate-200/70 text-slate-700">
                                        Size {{ $variant->size }}
                                    </span>
                                </div>
                            </td>

                            <!-- Price -->
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900 text-xs">
                                    ₹{{ number_format($variant->price, 0) }}
                                </div>
                                @if($variant->compare_at_price && $variant->compare_at_price > $variant->price)
                                    <div class="text-[10px] text-slate-400 line-through">
                                        ₹{{ number_format($variant->compare_at_price, 0) }}
                                    </div>
                                @endif
                            </td>

                            <!-- Stock Status -->
                            <td class="px-6 py-4 text-center">
                                @if($variant->stock === 0)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        <span>Out of Stock (0)</span>
                                    </span>
                                @elseif($variant->stock <= 5)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        <span>Low Stock ({{ $variant->stock }})</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        <span>In Stock ({{ $variant->stock }})</span>
                                    </span>
                                @endif
                            </td>

                            <!-- Quick Stock Adjustment -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="flex items-center border border-slate-200 rounded-lg overflow-hidden bg-white">
                                        <button type="button" @click="stepStock({{ $variant->id }}, -1)" class="px-2 py-1 bg-slate-50 hover:bg-slate-100 text-slate-600 text-xs font-bold border-r border-slate-200 transition">
                                            –
                                        </button>
                                        <input type="number" id="stock-input-{{ $variant->id }}" value="{{ $variant->stock }}" min="0" 
                                               class="w-14 py-1 text-center text-xs font-mono font-bold text-slate-800 outline-none">
                                        <button type="button" @click="stepStock({{ $variant->id }}, 1)" class="px-2 py-1 bg-slate-50 hover:bg-slate-100 text-slate-600 text-xs font-bold border-l border-slate-200 transition">
                                            +
                                        </button>
                                    </div>
                                    <button type="button" @click="saveStock({{ $variant->id }})" 
                                            class="px-2.5 py-1 bg-slate-900 hover:bg-indigo-600 text-white rounded-lg text-xs font-bold transition shadow-xs" title="Save updated stock">
                                        Save
                                    </button>
                                </div>
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <!-- Stock Movement History Logs -->
                                    <button type="button" @click="viewLogs({{ $variant->id }})" 
                                            class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 transition" title="View SKU Stock Movement History">
                                        Logs
                                    </button>

                                    <!-- Edit Full Product Details -->
                                    <a href="{{ route('admin.products.edit', $prod->id) }}" 
                                       class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition" title="Edit in Catalogue">
                                        Edit
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400 text-xs">
                                No inventory items found matching your filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($variants->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $variants->links() }}
            </div>
        @endif
    </div>

    <!-- ========================================== -->
    <!-- MODAL 1: Quick Add Product via SKU         -->
    <!-- ========================================== -->
    <div x-show="quickAddModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="quickAddModalOpen" x-transition.opacity class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" @click="quickAddModalOpen = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="quickAddModalOpen" x-transition.scale.95 class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-slate-200">
                <form action="{{ route('admin.inventory.quickStore') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <h2 class="text-base font-bold text-slate-900">Add New Product to Inventory</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Adding here registers the SKU in inventory AND adds the product to your live storefront.</p>
                        </div>
                        <button type="button" @click="quickAddModalOpen = false" class="text-slate-400 hover:text-slate-600 text-sm font-bold">✕</button>
                    </div>

                    <div class="p-6 space-y-4 text-xs max-h-[75vh] overflow-y-auto">
                        <!-- Product Name & Base SKU -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Product Name <span class="text-rose-500">*</span></label>
                                <input type="text" name="name" required placeholder="e.g. Mulberry Silk Chemise" class="w-full border border-slate-200 rounded-xl px-3.5 py-2 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Base Product SKU <span class="text-slate-400 font-normal">(auto if blank)</span></label>
                                <input type="text" name="sku" placeholder="e.g. LS-SILK-009" class="w-full border border-slate-200 rounded-xl px-3.5 py-2 font-mono uppercase focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none">
                            </div>
                        </div>

                        <!-- Category -->
                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Category <span class="text-rose-500">*</span></label>
                            <select name="category_id" required class="w-full border border-slate-200 rounded-xl px-3.5 py-2 bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Color & Swatch -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Primary Color Name</label>
                                <input type="text" name="color_name" value="Midnight Black" class="w-full border border-slate-200 rounded-xl px-3.5 py-2 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Color Swatch</label>
                                <div class="flex items-center gap-2">
                                    <input type="color" name="color_hex" value="#18181B" class="w-10 h-9 border border-slate-200 rounded-lg p-1 cursor-pointer">
                                    <span class="text-slate-400 text-[11px]">Swatch color</span>
                                </div>
                            </div>
                        </div>

                        <!-- Initial Size, Variant SKU & Stock -->
                        <div class="p-3.5 bg-slate-50/80 rounded-2xl border border-slate-200/60 space-y-3">
                            <span class="block font-bold uppercase tracking-wider text-slate-700 text-[10px]">Initial Variant &amp; Starting Stock</span>
                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <label class="block font-bold text-slate-600 text-[10px] uppercase mb-1">Size *</label>
                                    <input type="text" name="size" value="34B" required class="w-full border border-slate-200 rounded-lg px-2.5 py-1.5 bg-white uppercase font-bold text-slate-800">
                                </div>
                                <div>
                                    <label class="block font-bold text-slate-600 text-[10px] uppercase mb-1">Variant SKU</label>
                                    <input type="text" name="variant_sku" placeholder="Auto-generated" class="w-full border border-slate-200 rounded-lg px-2.5 py-1.5 bg-white font-mono uppercase text-slate-800">
                                </div>
                                <div>
                                    <label class="block font-bold text-slate-600 text-[10px] uppercase mb-1">Initial Stock *</label>
                                    <input type="number" name="stock" value="25" min="0" required class="w-full border border-slate-200 rounded-lg px-2.5 py-1.5 bg-white font-bold text-indigo-700">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3 pt-1">
                                <div>
                                    <label class="block font-bold text-slate-600 text-[10px] uppercase mb-1">Selling Price (₹) *</label>
                                    <input type="number" step="0.01" name="price" value="1499" required class="w-full border border-slate-200 rounded-lg px-2.5 py-1.5 bg-white font-bold">
                                </div>
                                <div>
                                    <label class="block font-bold text-slate-600 text-[10px] uppercase mb-1">MRP / Retail (₹)</label>
                                    <input type="number" step="0.01" name="compare_at_price" value="1999" class="w-full border border-slate-200 rounded-lg px-2.5 py-1.5 bg-white">
                                </div>
                            </div>
                        </div>

                        <!-- Product Cover Image -->
                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Product Photo <span class="text-slate-400 font-normal">(optional, can upload more later)</span></label>
                            <input type="file" name="image" accept="image/*" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-slate-600 file:mr-3 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700">
                        </div>

                        <!-- Storefront Live Status -->
                        <div class="pt-2 flex items-center justify-between border-t border-slate-100">
                            <div>
                                <label class="font-bold text-slate-800 cursor-pointer flex items-center gap-2">
                                    <input type="checkbox" name="is_active" value="1" checked class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                    <span>Publish immediately to storefront website</span>
                                </label>
                                <p class="text-[11px] text-slate-400 ml-6">Uncheck to save as a private draft in inventory.</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 bg-slate-50/50 border-t border-slate-100 flex items-center justify-end gap-3">
                        <button type="button" @click="quickAddModalOpen = false" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-800">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition shadow-sm">
                            Add to Inventory &amp; Publish
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MODAL 2: SKU Stock Movement Logs           -->
    <!-- ========================================== -->
    <div x-show="logsModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="logsModalOpen" x-transition.opacity class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" @click="logsModalOpen = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="logsModalOpen" x-transition.scale.95 class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-slate-200">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Stock Movement Audit Trail</h2>
                        <p class="text-xs text-slate-500 mt-0.5 font-mono">SKU: <strong x-text="activeSku"></strong> (<span x-text="activeProduct"></span> - Size <span x-text="activeSize"></span>)</p>
                    </div>
                    <button type="button" @click="logsModalOpen = false" class="text-slate-400 hover:text-slate-600 text-sm font-bold">✕</button>
                </div>

                <div class="p-6 max-h-[60vh] overflow-y-auto">
                    <template x-if="loadingLogs">
                        <div class="py-12 text-center text-xs text-slate-400">Loading audit trail...</div>
                    </template>

                    <template x-if="!loadingLogs && logs.length === 0">
                        <div class="py-12 text-center text-xs text-slate-400">No stock movement logs recorded yet for this SKU.</div>
                    </template>

                    <template x-if="!loadingLogs && logs.length > 0">
                        <div class="space-y-3">
                            <template x-for="log in logs" :key="log.id">
                                <div class="p-3.5 rounded-xl border border-slate-100 bg-slate-50/60 flex items-center justify-between text-xs">
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-2">
                                            <span class="font-bold text-slate-900" x-text="log.type_label"></span>
                                            <span class="text-[11px] text-slate-400" x-text="log.date"></span>
                                        </div>
                                        <p class="text-[11px] text-slate-500" x-text="log.notes || 'No extra notes.'"></p>
                                        <p class="text-[10px] text-slate-400">By: <span x-text="log.user"></span></p>
                                    </div>
                                    <div class="text-right">
                                        <span class="font-bold font-mono text-sm" 
                                              :class="log.change_qty.startsWith('+') ? 'text-emerald-600' : 'text-rose-600'" 
                                              x-text="log.change_qty"></span>
                                        <div class="text-[10px] text-slate-400 mt-0.5">Balance: <strong class="text-slate-700" x-text="log.new_stock"></strong></div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>

                <div class="p-4 bg-slate-50/50 border-t border-slate-100 text-right">
                    <button type="button" @click="logsModalOpen = false" class="px-4 py-2 bg-slate-900 text-white text-xs font-bold rounded-xl">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function inventoryManager() {
    return {
        quickAddModalOpen: false,
        logsModalOpen: false,
        loadingLogs: false,
        activeSku: '',
        activeProduct: '',
        activeSize: '',
        logs: [],

        openQuickAddModal() {
            this.quickAddModalOpen = true;
        },

        stepStock(variantId, delta) {
            const input = document.getElementById(`stock-input-${variantId}`);
            let val = parseInt(input.value || 0, 10);
            val = Math.max(0, val + delta);
            input.value = val;
        },

        async saveStock(variantId) {
            const input = document.getElementById(`stock-input-${variantId}`);
            const newStock = parseInt(input.value || 0, 10);

            try {
                const res = await fetch(`/admin/inventory/variants/${variantId}`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        stock: newStock,
                        type: 'manual_adjustment',
                        notes: 'Quick inline adjustment from Inventory Center'
                    })
                });

                const data = await res.json();
                if (data.success) {
                    // Flash success border
                    input.classList.add('ring-2', 'ring-emerald-500');
                    setTimeout(() => input.classList.remove('ring-2', 'ring-emerald-500'), 1500);
                } else {
                    alert('Error updating stock: ' + (data.message || 'Unknown error'));
                }
            } catch (e) {
                alert('Network error while saving stock.');
            }
        },

        async viewLogs(variantId) {
            this.logsModalOpen = true;
            this.loadingLogs = true;
            this.logs = [];

            try {
                const res = await fetch(`/admin/inventory/variants/${variantId}/logs`, {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();
                if (data.success) {
                    this.activeSku = data.sku;
                    this.activeProduct = data.product;
                    this.activeSize = data.size;
                    this.logs = data.logs;
                }
            } catch (e) {
                console.error(e);
            } finally {
                this.loadingLogs = false;
            }
        }
    };
}
</script>
@endsection
