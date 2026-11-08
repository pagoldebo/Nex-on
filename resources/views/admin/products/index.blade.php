@extends('admin.layout')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Products Catalogue</h1>
            <p class="mt-1 text-sm text-slate-500">Manage all products, base SKUs, inventory variants, color galleries, and pricing.</p>
        </div>
        <div class="mt-4 sm:mt-0 flex items-center gap-3">
            <a href="{{ route('admin.products.create') }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Add New Product</span>
            </a>
        </div>
    </div>

    <!-- Filters & Search Bar -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-xs">
        <form method="GET" action="{{ route('admin.products.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-center">
            <!-- Search by Name / Slug / SKU -->
            <div class="lg:col-span-5 relative">
                <input type="text" name="q" value="{{ $search ?? '' }}" placeholder="Search by name, SKU (e.g. LS-...), badge, slug..." 
                       class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="m21 21-4.35-4.35"/></svg>
            </div>

            <!-- Filter by Category -->
            <div class="lg:col-span-3">
                <select name="category_id" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-700 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition bg-white">
                    <option value="">All Categories</option>
                    @foreach(\App\Models\Category::getFlatTree() as $item)
                        <option value="{{ data_get($item, 'id') }}" {{ ($selectedCategory ?? '') == data_get($item, 'id') ? 'selected' : '' }}>
                            {!! data_get($item, 'indent_name') !!}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter by Status -->
            <div class="lg:col-span-2">
                <select name="status" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-700 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition bg-white">
                    <option value="">All Statuses</option>
                    <option value="active" {{ ($selectedStatus ?? '') === 'active' ? 'selected' : '' }}>Active Only</option>
                    <option value="draft" {{ ($selectedStatus ?? '') === 'draft' ? 'selected' : '' }}>Drafts Only</option>
                </select>
            </div>

            <!-- Submit & Reset Buttons -->
            <div class="lg:col-span-2 flex items-center gap-2">
                <button type="submit" class="flex-1 py-2 px-3 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition">
                    Filter
                </button>
                @if(!empty($search) || !empty($selectedCategory) || !empty($selectedStatus))
                    <a href="{{ route('admin.products.index') }}" class="py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-semibold transition" title="Reset Filters">
                        ✕
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Bulk Action Bar (Hidden until rows are selected) -->
    <form id="bulkForm" method="POST" action="{{ route('admin.products.bulkDelete') }}" onsubmit="return confirm('Are you sure you want to permanently delete all selected products?')">
        @csrf
        <div id="bulkBar" class="hidden mb-4 p-3 bg-indigo-50 border border-indigo-200 rounded-xl flex items-center justify-between text-xs animate-fade-in">
            <div class="flex items-center gap-2 text-indigo-900 font-semibold">
                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span><span id="selectedCount">0</span> product(s) selected</span>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white rounded-lg font-bold shadow-xs transition">
                    Delete Selected
                </button>
            </div>
        </div>

        <!-- Products Table -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-left text-sm">
                    <thead class="bg-slate-50/70 text-slate-500 uppercase text-[11px] font-bold tracking-wider">
                        <tr>
                            <th class="pl-6 pr-2 py-3.5 w-8">
                                <input type="checkbox" id="selectAllCheckbox" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            </th>
                            <th class="px-4 py-3.5">Product & Base SKU</th>
                            <th class="px-6 py-3.5">Colors & Variant SKUs</th>
                            <th class="px-6 py-3.5">Price Range</th>
                            <th class="px-6 py-3.5 text-center">Status</th>
                            <th class="px-6 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($products as $product)
                            @php
                                $allVariants = $product->colors->flatMap->variants;
                                $minPrice = $allVariants->min('price');
                                $maxPrice = $allVariants->max('price');
                                $totalStock = $allVariants->sum('stock');
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="pl-6 pr-2 py-4">
                                    <input type="checkbox" name="ids[]" value="{{ $product->id }}" class="product-row-check rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-3.5">
                                        <div class="w-12 h-14 bg-slate-100 rounded-xl overflow-hidden shrink-0 border border-slate-200 flex items-center justify-center relative">
                                            @if($product->cover_image)
                                                <img src="{{ $product->cover_image }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                            @else
                                                <span class="text-[10px] text-slate-400 font-bold uppercase">No Img</span>
                                            @endif
                                        </div>
                                        <div class="min-w-0 max-w-xs">
                                            <div class="font-bold text-slate-900 text-sm truncate">{{ $product->name }}</div>
                                            <div class="text-xs text-slate-400 flex items-center gap-2 mt-0.5">
                                                <span class="font-semibold text-indigo-600">{{ $product->category->name ?? 'Uncategorized' }}</span>
                                                @if($product->brand)
                                                    <span>·</span>
                                                    <span class="font-semibold text-purple-600">{{ $product->brand->name }}</span>
                                                @endif
                                                <span>·</span>
                                                <span class="font-mono text-[11px]">{{ $product->slug }}</span>
                                            </div>
                                            <div class="mt-1 flex items-center gap-1.5 flex-wrap">
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-mono font-bold bg-slate-100 text-slate-800 border border-slate-200" title="Product Base SKU">
                                                    SKU: {{ $product->sku ?? 'LS-AUTO' }}
                                                </span>
                                                @if($product->brand)
                                                    <span class="text-[9px] font-bold uppercase tracking-wider bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded-full border border-indigo-200">
                                                        {{ $product->brand->name }}
                                                    </span>
                                                @endif
                                                @if($product->addons->count())
                                                    <span class="text-[9px] font-bold uppercase tracking-wider bg-rose-50 text-rose-700 px-2 py-0.5 rounded-full border border-rose-200" title="{{ $product->addons->pluck('name')->join(', ') }}">
                                                        {{ $product->addons->count() }} Add-on{{ $product->addons->count() > 1 ? 's' : '' }}
                                                    </span>
                                                @endif
                                                @if($product->badge)
                                                    <span class="text-[9px] font-black uppercase tracking-wider bg-amber-50 text-amber-700 px-2 py-0.5 rounded-full border border-amber-200">
                                                        {{ $product->badge }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        @foreach($product->colors as $c)
                                            <span class="inline-flex items-center gap-1 text-[11px] bg-slate-100 px-2.5 py-1 rounded-full border border-slate-200/80" title="{{ $c->name }} ({{ $c->variants->count() }} sizes)">
                                                @if($c->hex)
                                                    <span class="w-2.5 h-2.5 rounded-full border border-black/20 shrink-0" style="background-color: {{ $c->hex }}"></span>
                                                @endif
                                                <span class="font-medium text-slate-700">{{ $c->name }}</span>
                                                <span class="text-[10px] text-slate-400 font-mono">({{ $c->variants->count() }})</span>
                                            </span>
                                        @endforeach
                                    </div>
                                    <div class="text-[11px] text-slate-500 mt-1.5 flex items-center gap-2">
                                        <span>Total Stock: <strong class="{{ $totalStock <= 5 ? 'text-rose-600 font-bold' : 'text-slate-800 font-bold' }}">{{ $totalStock }} pcs</strong></span>
                                        @php
                                            $sampleSkus = $allVariants->pluck('sku')->filter()->take(2);
                                        @endphp
                                        @if($sampleSkus->count())
                                            <span class="text-[10px] font-mono text-slate-400 hidden sm:inline" title="{{ $allVariants->pluck('sku')->filter()->implode(', ') }}">
                                                ({{ $sampleSkus->first() }}...)
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    @if($minPrice)
                                        <div class="font-extrabold text-slate-900 text-sm">
                                            ₹{{ number_format($minPrice, 0) }}
                                            @if($maxPrice && $maxPrice > $minPrice)
                                                <span class="text-xs font-normal text-slate-400"> – ₹{{ number_format($maxPrice, 0) }}</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-slate-400 text-xs italic">No variants yet</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <!-- Publish Toggle Button -->
                                    <button type="button" 
                                            onclick="toggleProductStatus({{ $product->id }})"
                                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold transition shadow-2xs {{ $product->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100' : 'bg-slate-100 text-slate-500 border border-slate-200 hover:bg-slate-200' }}"
                                            title="Click to toggle publish status">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $product->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                        <span>{{ $product->is_active ? 'Active' : 'Draft' }}</span>
                                    </button>
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <!-- Edit -->
                                        <a href="{{ route('admin.products.edit', $product) }}" 
                                           class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition" title="Edit Product">
                                            Edit
                                        </a>

                                        <!-- Duplicate / Clone -->
                                        <button type="button" 
                                                onclick="duplicateProduct({{ $product->id }}, @js($product->name))"
                                                class="px-2 py-1 text-xs font-medium text-amber-600 hover:text-amber-800 hover:bg-amber-50 rounded-lg transition" 
                                                title="Clone / Duplicate Product with all variants & SKUs">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/></svg>
                                        </button>

                                        <!-- Preview on Storefront -->
                                        <a href="{{ route('product.show', $product->slug) }}" target="_blank" 
                                           class="px-2 py-1 text-xs font-medium text-slate-400 hover:text-slate-700 transition" title="Preview on storefront">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        </a>

                                        <!-- Delete Single -->
                                        <button type="button" 
                                                onclick="deleteSingleProduct({{ $product->id }}, @js($product->name))"
                                                class="px-2 py-1 text-xs text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded transition" title="Delete Product">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-400 text-xs">
                                    No products found matching your search or filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($products->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </form>
</div>

<!-- Standalone form for single actions -->
<form id="singleActionForm" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="_method" id="singleActionMethod" value="POST">
</form>

<script>
    // Bulk select handling
    const selectAll = document.getElementById('selectAllCheckbox');
    const checkboxes = document.querySelectorAll('.product-row-check');
    const bulkBar = document.getElementById('bulkBar');
    const selectedCount = document.getElementById('selectedCount');

    function updateBulkBar() {
        const checked = document.querySelectorAll('.product-row-check:checked');
        const count = checked.length;
        if (count > 0) {
            bulkBar.classList.remove('hidden');
            selectedCount.textContent = count;
        } else {
            bulkBar.classList.add('hidden');
        }
    }

    if (selectAll) {
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            updateBulkBar();
        });
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateBulkBar);
    });

    // Single item actions
    function toggleProductStatus(id) {
        const form = document.getElementById('singleActionForm');
        form.action = `/admin/products/${id}/toggle`;
        document.getElementById('singleActionMethod').value = 'PATCH';
        form.submit();
    }

    function duplicateProduct(id, name) {
        if (confirm(`Duplicate product "${name}" with all its colors, sizes, and SKUs?`)) {
            const form = document.getElementById('singleActionForm');
            form.action = `/admin/products/${id}/duplicate`;
            document.getElementById('singleActionMethod').value = 'POST';
            form.submit();
        }
    }

    function deleteSingleProduct(id, name) {
        if (confirm(`Delete product "${name}" and all associated variants?`)) {
            const form = document.getElementById('singleActionForm');
            form.action = `/admin/products/${id}`;
            document.getElementById('singleActionMethod').value = 'DELETE';
            form.submit();
        }
    }
</script>
@endsection
