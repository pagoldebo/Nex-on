{{-- resources/views/admin/products/edit.blade.php
     $product is loaded with: colors.images, colors.variants (see ProductController::edit)
--}}
@extends('admin.layout')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Edit Product — {{ $product->name }}</h1>
            <p class="text-xs text-slate-500 mt-1">Manage product details, base SKU ({{ $product->sku ?? 'Unassigned' }}), colors, and variant-level SKUs.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('product.show', $product->slug) }}" target="_blank" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition">
                View on Storefront ↗
            </a>
            <span class="text-slate-300">|</span>
            <a href="{{ route('admin.products.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 transition">
                ← Back to Catalogue
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data" class="space-y-8">
        @csrf
        @method('PUT')

        {{-- ===== Basic product info, pre-filled ===== --}}
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-5">
            <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-3">Core Information &amp; Base SKU</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="sm:col-span-1">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Product Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" required value="{{ old('name', $product->name) }}" class="w-full border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                </div>
                <div class="sm:col-span-1">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Base SKU</label>
                    <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" placeholder="e.g. LS-SILK-001" class="w-full border border-slate-200 rounded-xl px-3.5 py-2 text-sm font-mono focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none uppercase transition">
                </div>
                <div class="sm:col-span-1">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Category <span class="text-rose-500">*</span></label>
                    <select name="category_id" required class="w-full border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition bg-white">
                        @foreach (\App\Models\Category::getFlatTree() as $item)
                            <option value="{{ data_get($item, 'id') }}" @selected(data_get($item, 'id') === $product->category_id)>{!! data_get($item, 'indent_name') !!}</option>
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
                            <option value="{{ $brand->id }}" @selected(old('brand_id', $product->brand_id) == $brand->id)>{{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Short Description</label>
                    <span class="text-[11px] text-slate-400">Brief summary displayed near price, add to cart &amp; catalog rails (supports bold, bullets, links)</span>
                </div>
                <x-rich-editor name="description" :value="old('description', $product->description)" placeholder="Brief 1-2 sentence overview of the product..." minHeight="90px" :rows="3" />
            </div>

            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Long Description</label>
                    <span class="text-[11px] text-slate-400">Full detailed description, spec charts, bullet points &amp; sizing tables</span>
                </div>
                <x-rich-editor name="long_description" :value="old('long_description', $product->long_description)" placeholder="Enter comprehensive product story, materials, spec chart table, bullets, and sizing..." minHeight="220px" :rows="8" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-center pt-2 border-t border-slate-100">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Badge (optional)</label>
                    <input type="text" name="badge" value="{{ old('badge', $product->badge) }}" placeholder="NEW, BESTSELLER, BRIDAL" class="w-full border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none uppercase transition">
                </div>
                <div class="flex items-center gap-6 pt-5">
                    <label class="flex items-center gap-2 text-xs font-semibold text-slate-700 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" @checked($product->is_active) class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"> 
                        <span>Active / Published</span>
                    </label>
                    <label class="flex items-center gap-2 text-xs font-semibold text-slate-700 cursor-pointer">
                        <input type="checkbox" name="is_featured" value="1" @checked($product->is_featured) class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"> 
                        <span>Featured</span>
                    </label>
                    <label class="flex items-center gap-2 text-xs font-semibold text-slate-700 cursor-pointer">
                        <input type="checkbox" name="is_bestseller" value="1" @checked($product->is_bestseller) class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"> 
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
                                   @checked(in_array($addon->id, old('addon_ids', $product->addons->pluck('id')->toArray())))
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

        {{-- ===== Existing colors, pre-rendered with real data ===== --}}
        <div id="colorsContainer" class="space-y-6">
            @foreach ($product->colors as $color)
                <div class="color-block bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-5" data-color-index="{{ $color->id }}">
                    <input type="hidden" name="colors[{{ $color->id }}][id]" value="{{ $color->id }}">

                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Color: {{ $color->name }}</h3>
                        <button type="button" onclick="removeColorBlock(this)" class="text-rose-500 text-xs font-bold hover:underline">
                            Remove Color
                        </button>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Color Name <span class="text-rose-500">*</span></label>
                            <input type="text" name="colors[{{ $color->id }}][name]" required value="{{ $color->name }}" class="w-full border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Color Swatch</label>
                            <div class="flex items-center gap-3">
                                <input type="color" name="colors[{{ $color->id }}][hex]" value="{{ $color->hex ?? '#18181B' }}" class="w-12 h-10 border border-slate-200 rounded-xl p-1 cursor-pointer">
                                <span class="text-xs text-slate-500">Swatch color on product page</span>
                            </div>
                        </div>
                    </div>

                    {{-- Existing images --}}
                    @if ($color->images->count())
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Current Images ({{ $color->images->count() }}/8)</label>
                            <div class="grid grid-cols-4 sm:grid-cols-8 gap-3">
                                @foreach ($color->images as $image)
                                    <div class="relative border rounded-xl overflow-hidden group aspect-3/4">
                                        <img src="{{ asset('storage/'.$image->image_path) }}" class="w-full h-full object-cover">
                                        <label class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition flex items-center justify-center cursor-pointer">
                                            <input type="checkbox" name="colors[{{ $color->id }}][remove_image_ids][]" value="{{ $image->id }}"
                                                   class="remove-image-checkbox sr-only" onchange="toggleImageRemoveStyle(this)">
                                            <span class="text-white text-[10px] font-bold bg-rose-600 px-2 py-1 rounded">Remove</span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Add More Images <span class="text-slate-400 font-normal">(up to {{ 8 - $color->images->count() }} more)</span>
                        </label>
                        <input type="file" name="colors[{{ $color->id }}][images][]" accept="image/*" multiple
                               onchange="enforceImageLimit(this, {{ 8 - $color->images->count() }})"
                               class="w-full border border-slate-200 rounded-xl px-3.5 py-2 text-xs text-slate-600 focus:outline-none file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    </div>

                    {{-- Existing size variants --}}
                    <div class="pt-2 border-t border-slate-100 space-y-3">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Sizes, Variant SKUs &amp; Stock
                            </label>
                            <span class="text-[11px] text-slate-400">Variant SKU is used for individual barcode/inventory</span>
                        </div>

                        <div class="size-rows-container space-y-2">
                            @foreach ($color->variants as $variant)
                                <div class="size-row grid grid-cols-12 gap-2.5 items-end bg-slate-50/60 p-2.5 rounded-xl border border-slate-100">
                                    <input type="hidden" name="colors[{{ $color->id }}][sizes][{{ $variant->id }}][id]" value="{{ $variant->id }}">
                                    <div class="col-span-2">
                                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Size *</label>
                                        <input type="text" name="colors[{{ $color->id }}][sizes][{{ $variant->id }}][size]" required value="{{ $variant->size }}" class="w-full border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs uppercase bg-white">
                                    </div>
                                    <div class="col-span-3">
                                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Variant SKU</label>
                                        <input type="text" name="colors[{{ $color->id }}][sizes][{{ $variant->id }}][sku]" value="{{ $variant->sku }}" placeholder="Auto-generated" class="w-full border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs font-mono uppercase bg-white">
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Price (₹) *</label>
                                        <input type="number" step="0.01" name="colors[{{ $color->id }}][sizes][{{ $variant->id }}][price]" required value="{{ $variant->price }}" class="w-full border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs bg-white">
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">MRP / Compare (₹)</label>
                                        <input type="number" step="0.01" name="colors[{{ $color->id }}][sizes][{{ $variant->id }}][compare_at_price]" value="{{ $variant->compare_at_price }}" class="w-full border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs bg-white">
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Stock *</label>
                                        <input type="number" name="colors[{{ $color->id }}][sizes][{{ $variant->id }}][stock]" required value="{{ $variant->stock }}" min="0" class="w-full border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs bg-white">
                                    </div>
                                    <div class="col-span-1 text-center">
                                        <button type="button" onclick="this.closest('.size-row').remove()" class="text-rose-500 hover:text-rose-700 text-xs font-bold p-1 hover:bg-rose-50 rounded" title="Remove Size">
                                            ✕
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <button type="button" onclick="addSizeRow(this, {{ $color->id }})" class="inline-flex items-center gap-1.5 text-xs font-bold text-indigo-600 hover:text-indigo-700">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <span>Add Another Size</span>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        <div>
            <button type="button" onclick="addColorBlock()" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold rounded-xl transition">
                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Add Another Color</span>
            </button>
        </div>

        <div class="pt-4 flex items-center gap-4">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3 rounded-xl text-xs font-bold uppercase tracking-wider shadow-sm transition">
                Save Changes &amp; Sync SKUs
            </button>
            <a href="{{ route('admin.products.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 transition">
                Cancel
            </a>
        </div>
    </form>
</div>

{{-- ===== Templates for NEW color/size blocks added during this edit session ===== --}}
<template id="colorBlockTemplate">
    <div class="color-block bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-5" data-color-index="__COLOR_INDEX__">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Color Variation <span class="text-xs text-indigo-600 font-normal">(new)</span></h3>
            <button type="button" onclick="this.closest('.color-block').remove()" class="text-rose-500 text-xs font-bold hover:underline">
                Remove Color
            </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Color Name <span class="text-rose-500">*</span></label>
                <input type="text" name="colors[__COLOR_INDEX__][name]" required placeholder="e.g. Royal Ruby" class="w-full border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Color Swatch</label>
                <div class="flex items-center gap-3">
                    <input type="color" name="colors[__COLOR_INDEX__][hex]" value="#DC2626" class="w-12 h-10 border border-slate-200 rounded-xl p-1 cursor-pointer">
                    <span class="text-xs text-slate-500">Pick swatch color</span>
                </div>
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                Images for this color <span class="text-slate-400 font-normal">(up to 8)</span>
            </label>
            <input type="file" name="colors[__COLOR_INDEX__][images][]" accept="image/*" multiple
                   onchange="enforceImageLimit(this, 8)"
                   class="w-full border border-slate-200 rounded-xl px-3.5 py-2 text-xs text-slate-600 focus:outline-none file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
        </div>

        <div class="pt-2 border-t border-slate-100 space-y-3">
            <div class="flex items-center justify-between">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                    Sizes, Variant SKUs &amp; Inventory Pricing
                </label>
                <span class="text-[11px] text-slate-400">Variant SKU auto-generates if left blank</span>
            </div>

            <div class="size-rows-container space-y-2"></div>

            <button type="button" onclick="addSizeRow(this, '__COLOR_INDEX__')" class="inline-flex items-center gap-1.5 text-xs font-bold text-indigo-600 hover:text-indigo-700">
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
            <input type="text" name="colors[__COLOR_INDEX__][sizes][__SIZE_INDEX__][size]" required placeholder="S, 32B..." class="w-full border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs uppercase bg-white">
        </div>
        <div class="col-span-3">
            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Variant SKU</label>
            <input type="text" name="colors[__COLOR_INDEX__][sizes][__SIZE_INDEX__][sku]" placeholder="Auto-generated" class="w-full border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs font-mono uppercase bg-white">
        </div>
        <div class="col-span-2">
            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Price (₹) *</label>
            <input type="number" step="0.01" name="colors[__COLOR_INDEX__][sizes][__SIZE_INDEX__][price]" required placeholder="1299" class="w-full border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs bg-white">
        </div>
        <div class="col-span-2">
            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">MRP / Compare (₹)</label>
            <input type="number" step="0.01" name="colors[__COLOR_INDEX__][sizes][__SIZE_INDEX__][compare_at_price]" placeholder="1899" class="w-full border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs bg-white">
        </div>
        <div class="col-span-2">
            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Stock *</label>
            <input type="number" name="colors[__COLOR_INDEX__][sizes][__SIZE_INDEX__][stock]" required value="10" min="0" class="w-full border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs bg-white">
        </div>
        <div class="col-span-1 text-center">
            <button type="button" onclick="this.closest('.size-row').remove()" class="text-rose-500 hover:text-rose-700 text-xs font-bold p-1 hover:bg-rose-50 rounded" title="Remove Size">
                ✕
            </button>
        </div>
    </div>
</template>

<script>
    let newColorCounter = 0;
    let newSizeCounters = {};

    function addColorBlock() {
        const colorKey = `new${newColorCounter}`;
        newColorCounter++;
        newSizeCounters[colorKey] = 0;

        const tpl = document.getElementById('colorBlockTemplate').innerHTML;
        const html = tpl.replaceAll('__COLOR_INDEX__', colorKey);
        const wrapper = document.createElement('div');
        wrapper.innerHTML = html;
        document.getElementById('colorsContainer').appendChild(wrapper.firstElementChild);

        const newBlock = document.querySelectorAll('.color-block')[document.querySelectorAll('.color-block').length - 1];
        addSizeRow(newBlock.querySelector('button[onclick^="addSizeRow"]'), colorKey);
    }

    function addSizeRow(triggerBtnOrColorBlock, colorKey) {
        const colorBlock = triggerBtnOrColorBlock.closest ? triggerBtnOrColorBlock.closest('.color-block') : triggerBtnOrColorBlock;
        const sizeContainer = colorBlock.querySelector('.size-rows-container');

        if (newSizeCounters[colorKey] === undefined) {
            newSizeCounters[colorKey] = 0;
        }
        const sizeKey = `new${newSizeCounters[colorKey]}`;
        newSizeCounters[colorKey]++;

        const tpl = document.getElementById('sizeRowTemplate').innerHTML;
        const html = tpl
            .replaceAll('__COLOR_INDEX__', colorKey)
            .replaceAll('__SIZE_INDEX__', sizeKey);

        const wrapper = document.createElement('div');
        wrapper.innerHTML = html;
        sizeContainer.appendChild(wrapper.firstElementChild);
    }

    function removeColorBlock(btn) {
        const block = btn.closest('.color-block');
        block.remove();
    }

    function enforceImageLimit(input, max) {
        if (input.files.length > max) {
            alert(`You can upload up to ${max} more image(s) for this color.`);
            input.value = '';
        }
    }

    function toggleImageRemoveStyle(checkbox) {
        const wrapper = checkbox.closest('.relative');
        wrapper.classList.toggle('ring-4', checkbox.checked);
        wrapper.classList.toggle('ring-rose-500', checkbox.checked);
        wrapper.classList.toggle('opacity-40', checkbox.checked);
    }
</script>
@endsection
