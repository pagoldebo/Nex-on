{{-- resources/views/admin/products/create.blade.php --}}
@extends('admin.layout')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Add New Product</h1>
            <p class="text-xs text-slate-500 mt-1">Configure product details, inventory base SKU, colors, images, and size-specific SKUs.</p>
        </div>
        <a href="{{ route('admin.products.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 transition">
            ← Back to Catalogue
        </a>
    </div>

    <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="space-y-8">
        @csrf

        {{-- ===== Basic product info ===== --}}
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-5">
            <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-3">Core Information & Inventory SKU</h2>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="sm:col-span-1">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Product Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" required placeholder="e.g. Silk Lace Balconette Bra" class="w-full border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                </div>
                <div class="sm:col-span-1">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Base SKU <span class="text-slate-400 font-normal">(optional)</span></label>
                    <input type="text" name="sku" placeholder="e.g. LS-SILK-001" class="w-full border border-slate-200 rounded-xl px-3.5 py-2 text-sm font-mono focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none uppercase transition">
                </div>
                <div class="sm:col-span-1">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Category <span class="text-rose-500">*</span></label>
                    <select name="category_id" required class="w-full border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition bg-white">
                        @foreach (\App\Models\Category::getFlatTree() as $item)
                            <option value="{{ data_get($item, 'id') }}">{!! data_get($item, 'indent_name') !!}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm:col-span-1">
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Brand</label>
                        <a href="{{ route('admin.addons.index', ['tab' => 'brands']) }}" target="_blank" class="text-[10px] text-indigo-600 hover:underline font-semibold">+ Manage</a>
                    </div>
                    <select name="brand_id" class="w-full border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition bg-white">
                        <option value="">— No Brand / In-House —</option>
                        @foreach ($brands as $brand)
                            <option value="{{ $brand->id }}" @selected(old('brand_id') == $brand->id)>{{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Short Description</label>
                    <span class="text-[11px] text-slate-400">Brief summary displayed near price &amp; catalog cards (supports bold, bullets, links)</span>
                </div>
                <x-rich-editor name="description" :value="old('description')" placeholder="Brief 1-2 sentence overview of the product..." minHeight="90px" :rows="3" />
            </div>

            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Long Description</label>
                    <span class="text-[11px] text-slate-400">Full detailed description, spec charts, bullet points &amp; sizing tables</span>
                </div>
                <x-rich-editor name="long_description" :value="old('long_description')" placeholder="Enter comprehensive product story, materials, spec chart table, bullets, and sizing..." minHeight="220px" :rows="8" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-center pt-2 border-t border-slate-100">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Badge (optional)</label>
                    <input type="text" name="badge" placeholder="NEW, BESTSELLER, BRIDAL, LIMITED" class="w-full border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none uppercase transition">
                </div>
                <div class="flex items-center gap-6 pt-5">
                    <label class="flex items-center gap-2 text-xs font-semibold text-slate-700 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" checked class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"> 
                        <span>Active / Published</span>
                    </label>
                    <label class="flex items-center gap-2 text-xs font-semibold text-slate-700 cursor-pointer">
                        <input type="checkbox" name="is_featured" value="1" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"> 
                        <span>Featured</span>
                    </label>
                    <label class="flex items-center gap-2 text-xs font-semibold text-slate-700 cursor-pointer">
                        <input type="checkbox" name="is_bestseller" value="1" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"> 
                        <span>Bestseller</span>
                    </label>
                </div>
            </div>
        </div>

        {{-- ===== Available Add-ons ===== --}}
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-slate-100 pb-3">
                <div>
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        <span>Available Add-ons</span>
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">Select the packaging, bespoke finishes, or gifting add-ons offered for this product at checkout.</p>
                </div>
                <div class="flex items-center gap-3">
                    <button type="button" onclick="document.querySelectorAll('.addon-checkbox').forEach(cb => cb.checked = true)" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">Select All</button>
                    <span class="text-slate-300">|</span>
                    <button type="button" onclick="document.querySelectorAll('.addon-checkbox').forEach(cb => cb.checked = false)" class="text-xs font-semibold text-slate-500 hover:text-slate-700">Deselect All</button>
                    <span class="text-slate-300">|</span>
                    <a href="{{ route('admin.addons.index', ['tab' => 'addons']) }}" target="_blank" class="text-xs font-semibold text-rose-600 hover:underline">+ New Add-on</a>
                </div>
            </div>

            @if($addons->count())
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3.5 pt-1">
                    @foreach($addons as $addon)
                        <label class="relative flex items-start gap-3 p-3.5 rounded-xl border border-slate-200 hover:border-indigo-300 hover:bg-indigo-50/20 cursor-pointer transition group">
                            <input type="checkbox" name="addon_ids[]" value="{{ $addon->id }}" 
                                   @checked(in_array($addon->id, old('addon_ids', [])))
                                   class="addon-checkbox mt-0.5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-xs font-bold text-slate-800 group-hover:text-indigo-600 truncate">{{ $addon->name }}</span>
                                    <span class="text-[11px] font-mono font-bold text-slate-900 bg-slate-100 px-2 py-0.5 rounded-full shrink-0">₹{{ number_format($addon->price, 0) }}</span>
                                </div>
                                @if($addon->description)
                                    <p class="text-[11px] text-slate-500 mt-1 line-clamp-2">{{ $addon->description }}</p>
                                @endif
                            </div>
                        </label>
                    @endforeach
                </div>
            @else
                <div class="text-center py-6 border border-dashed border-slate-200 rounded-xl">
                    <p class="text-xs text-slate-500 mb-2">No active add-ons found in catalogue.</p>
                    <a href="{{ route('admin.addons.index', ['tab' => 'addons']) }}" class="text-xs font-bold text-indigo-600 hover:underline">+ Create your first Add-on</a>
                </div>
            @endif
        </div>

        {{-- ===== Colors (each with image gallery + size/price/stock rows) ===== --}}
        <div id="colorsContainer" class="space-y-6">
            {{-- color blocks are injected here by JS, starting with one empty block --}}
        </div>

        <div>
            <button type="button" onclick="addColorBlock()" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold rounded-xl transition">
                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Add Another Color</span>
            </button>
        </div>

        <div class="pt-4 flex items-center gap-4">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3 rounded-xl text-xs font-bold uppercase tracking-wider shadow-sm transition">
                Save &amp; Publish Product
            </button>
            <a href="{{ route('admin.products.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 transition">
                Cancel
            </a>
        </div>
    </form>
</div>

{{-- ===== Templates for repeatable blocks ===== --}}
<template id="colorBlockTemplate">
    <div class="color-block bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-5" data-color-index="__COLOR_INDEX__">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Color Variation</h3>
            <button type="button" onclick="this.closest('.color-block').remove()" class="text-rose-500 text-xs font-bold hover:underline">
                Remove Color
            </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Color Name <span class="text-rose-500">*</span></label>
                <input type="text" name="colors[__COLOR_INDEX__][name]" required placeholder="e.g. Midnight Black" class="w-full border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Color Swatch</label>
                <div class="flex items-center gap-3">
                    <input type="color" name="colors[__COLOR_INDEX__][hex]" value="#18181B" class="w-12 h-10 border border-slate-200 rounded-xl p-1 cursor-pointer">
                    <span class="text-xs text-slate-500">Pick the swatch color shown on product cards</span>
                </div>
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                Images for this color <span class="text-slate-400 font-normal">(up to 8 images — automatically switched on color click)</span>
            </label>
            <input type="file" name="colors[__COLOR_INDEX__][images][]" accept="image/*" multiple
                   onchange="enforceImageLimit(this, 8)"
                   class="w-full border border-slate-200 rounded-xl px-3.5 py-2 text-xs text-slate-600 focus:outline-none file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
        </div>
                   onchange="enforceImageLimit(this, 8)"
                   class="w-full border border-slate-200 rounded-xl px-3.5 py-2 text-xs text-slate-600 focus:outline-none file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
        </div>

        {{-- ===== Size rows: each has its OWN SKU + price + compare-at + stock ===== --}}
        <div class="pt-2 border-t border-slate-100 space-y-3">
            <div class="flex items-center justify-between">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                    Sizes, Variant SKUs &amp; Inventory Pricing
                </label>
                <span class="text-[11px] text-slate-400">Variant SKU auto-generates if left blank</span>
            </div>
            
            <div class="size-rows-container space-y-2">
                {{-- size rows injected here --}}
            </div>

            <button type="button" onclick="addSizeRow(this)" class="inline-flex items-center gap-1.5 text-xs font-bold text-indigo-600 hover:text-indigo-700">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Add Another Size</span>
            </button>
        </div>
    </div>
</template>

<template id="sizeRowTemplate">
    <div class="size-row grid grid-cols-12 gap-2.5 items-end bg-slate-50/60 p-2.5 rounded-xl border border-slate-100">
        <div class="col-span-2">
            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Size *</label>
            <input type="text" name="colors[__COLOR_INDEX__][sizes][__SIZE_INDEX__][size]" required placeholder="S, 32B, M..." class="w-full border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none uppercase bg-white">
        </div>
        <div class="col-span-3">
            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Variant SKU</label>
            <input type="text" name="colors[__COLOR_INDEX__][sizes][__SIZE_INDEX__][sku]" placeholder="Auto-generated" class="w-full border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs font-mono uppercase focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none bg-white">
        </div>
        <div class="col-span-2">
            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Price (₹) *</label>
            <input type="number" step="0.01" name="colors[__COLOR_INDEX__][sizes][__SIZE_INDEX__][price]" required placeholder="1299" class="w-full border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none bg-white">
        </div>
        <div class="col-span-2">
            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">MRP / Compare (₹)</label>
            <input type="number" step="0.01" name="colors[__COLOR_INDEX__][sizes][__SIZE_INDEX__][compare_at_price]" placeholder="1899" class="w-full border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none bg-white">
        </div>
        <div class="col-span-2">
            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Stock *</label>
            <input type="number" name="colors[__COLOR_INDEX__][sizes][__SIZE_INDEX__][stock]" required value="10" min="0" class="w-full border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none bg-white">
        </div>
        <div class="col-span-1 text-center">
            <button type="button" onclick="this.closest('.size-row').remove()" class="text-rose-500 hover:text-rose-700 text-xs font-bold p-1 hover:bg-rose-50 rounded" title="Remove Size">
                ✕
            </button>
        </div>
    </div>
</template>

<script>
    let colorIndex = 0;

    function addColorBlock() {
        const tpl = document.getElementById('colorBlockTemplate').innerHTML;
        const html = tpl.replaceAll('__COLOR_INDEX__', colorIndex);
        const wrapper = document.createElement('div');
        wrapper.innerHTML = html;
        document.getElementById('colorsContainer').appendChild(wrapper.firstElementChild);
        colorIndex++;
        
        // every new color starts with one size row so the form isn't empty
        const blocks = document.querySelectorAll('.color-block');
        addSizeRow(blocks[blocks.length - 1].querySelector('button[onclick^="addSizeRow"]'));
    }

    function addSizeRow(triggerBtn) {
        const colorBlock = triggerBtn.closest('.color-block');
        const colorIdx = colorBlock.dataset.colorIndex;
        const sizeContainer = colorBlock.querySelector('.size-rows-container');
        const existingRows = sizeContainer.querySelectorAll('.size-row').length;

        const tpl = document.getElementById('sizeRowTemplate').innerHTML;
        const html = tpl
            .replaceAll('__COLOR_INDEX__', colorIdx)
            .replaceAll('__SIZE_INDEX__', existingRows);

        const wrapper = document.createElement('div');
        wrapper.innerHTML = html;
        sizeContainer.appendChild(wrapper.firstElementChild);
    }

    function enforceImageLimit(input, max) {
        if (input.files.length > max) {
            alert(`You can upload up to ${max} images per color.`);
            input.value = '';
        }
    }

    // Start with one color block pre-added so the form isn't empty on load
    document.addEventListener('DOMContentLoaded', () => addColorBlock());
</script>
@endsection
