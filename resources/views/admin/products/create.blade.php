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

        {{-- ===== 1. Dedicated Cover Image for this color (Updatable separately anytime) ===== --}}
        <div class="p-4 bg-slate-50/80 rounded-xl border border-slate-200/90 space-y-3">
            <div class="flex items-center justify-between">
                <div>
                    <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider">
                        Cover Image for this color <span class="text-indigo-600 font-semibold">(Main Display Thumbnail)</span>
                    </label>
                    <p class="text-[11px] text-slate-500 mt-0.5">
                        The primary image displayed on catalog cards, collections, and initial swatch selection. Can be changed independently anytime.
                    </p>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                {{-- Preview Container --}}
                <div class="cover-preview-wrapper relative w-24 h-28 rounded-xl border-2 border-dashed border-slate-300 bg-white flex items-center justify-center overflow-hidden shrink-0 shadow-xs">
                    <img src="" class="cover-preview-img hidden w-full h-full object-cover" alt="Cover preview">
                    <div class="cover-placeholder-icon flex flex-col items-center justify-center text-slate-400 p-2 text-center">
                        <svg class="w-6 h-6 mb-1 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span class="text-[10px] font-semibold text-slate-400">No cover</span>
                    </div>
                    <span class="cover-badge hidden absolute top-1 left-1 bg-indigo-600 text-white text-[9px] font-bold px-1.5 py-0.5 rounded shadow">Cover</span>
                </div>

                {{-- Upload & Controls --}}
                <div class="flex-1 w-full space-y-2">
                    <input type="file" name="colors[__COLOR_INDEX__][cover_image]" accept="image/*"
                           onchange="handleCoverImageSelect(this)"
                           class="cover-file-input w-full border border-slate-200 rounded-xl px-3.5 py-2 text-xs text-slate-600 focus:outline-none file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 bg-white">
                    <div class="flex items-center gap-3">
                        <button type="button" onclick="clearCoverImage(this)" class="cover-clear-btn hidden text-[11px] font-semibold text-rose-600 hover:underline">
                            ✕ Remove Cover Image
                        </button>
                        <span class="text-[11px] text-slate-400">Accepted formats: WEBP, JPG, PNG, GIF (Max 4MB)</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== 2. Gallery Images with Interactive Rearrange / Drag-and-Drop ===== --}}
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <div>
                    <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider">
                        Images for this color <span class="text-slate-400 font-normal">(up to 8 images — automatically switched on color click)</span>
                    </label>
                    <p class="text-[11px] text-slate-500 mt-0.5">
                        Upload detail views &amp; angles. Drag cards or click ◀ / ▶ arrow buttons below to rearrange their order.
                    </p>
                </div>
                <span class="gallery-count-badge text-xs font-bold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-lg">0 / 8 images</span>
            </div>

            <input type="file" name="colors[__COLOR_INDEX__][images][]" accept="image/*" multiple
                   onchange="handleGalleryImagesSelect(this)"
                   class="gallery-file-input w-full border border-slate-200 rounded-xl px-3.5 py-2 text-xs text-slate-600 focus:outline-none file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 bg-white">

            {{-- Interactive Reorder Grid --}}
            <div class="gallery-preview-grid grid grid-cols-2 sm:grid-cols-4 md:grid-cols-8 gap-3 min-h-[50px] p-2.5 bg-slate-50/50 rounded-xl border border-dashed border-slate-200 empty:hidden">
            </div>
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
        const newColorBlock = wrapper.firstElementChild;
        newColorBlock._galleryFiles = [];
        document.getElementById('colorsContainer').appendChild(newColorBlock);
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

    // ===== Cover Image Handling =====
    function handleCoverImageSelect(input) {
        const block = input.closest('.color-block');
        const img = block.querySelector('.cover-preview-img');
        const icon = block.querySelector('.cover-placeholder-icon');
        const badge = block.querySelector('.cover-badge');
        const clearBtn = block.querySelector('.cover-clear-btn');

        if (input.files && input.files[0]) {
            const file = input.files[0];
            img.src = URL.createObjectURL(file);
            img.classList.remove('hidden');
            icon.classList.add('hidden');
            badge.classList.remove('hidden');
            clearBtn.classList.remove('hidden');
        } else {
            clearCoverImage(input);
        }
    }

    function clearCoverImage(btnOrInput) {
        const block = btnOrInput.closest('.color-block');
        const input = block.querySelector('.cover-file-input');
        const img = block.querySelector('.cover-preview-img');
        const icon = block.querySelector('.cover-placeholder-icon');
        const badge = block.querySelector('.cover-badge');
        const clearBtn = block.querySelector('.cover-clear-btn');

        input.value = '';
        img.src = '';
        img.classList.add('hidden');
        icon.classList.remove('hidden');
        badge.classList.add('hidden');
        clearBtn.classList.add('hidden');
    }

    // ===== Gallery Images Handling & Rearranging =====
    function handleGalleryImagesSelect(input) {
        const block = input.closest('.color-block');
        if (!block._galleryFiles) {
            block._galleryFiles = [];
        }

        const newFiles = Array.from(input.files);
        const maxAllowed = 8;
        const availableSlots = maxAllowed - block._galleryFiles.length;

        if (availableSlots <= 0) {
            alert(`You have already selected the maximum of ${maxAllowed} images for this color.`);
            syncGalleryFilesToInput(block);
            return;
        }

        if (newFiles.length > availableSlots) {
            alert(`You can only add ${availableSlots} more image(s). Only the first ${availableSlots} will be added.`);
        }

        const filesToAdd = newFiles.slice(0, availableSlots);
        block._galleryFiles = block._galleryFiles.concat(filesToAdd);

        renderGalleryPreviews(block);
        syncGalleryFilesToInput(block);
    }

    function syncGalleryFilesToInput(block) {
        const input = block.querySelector('.gallery-file-input');
        const dt = new DataTransfer();
        (block._galleryFiles || []).forEach(file => dt.items.add(file));
        input.files = dt.files;

        const badge = block.querySelector('.gallery-count-badge');
        if (badge) {
            badge.textContent = `${(block._galleryFiles || []).length} / 8 images`;
        }
    }

    function renderGalleryPreviews(block) {
        const grid = block.querySelector('.gallery-preview-grid');
        grid.innerHTML = '';
        const files = block._galleryFiles || [];

        if (files.length === 0) {
            grid.classList.add('empty:hidden');
            return;
        }
        grid.classList.remove('empty:hidden');

        files.forEach((file, index) => {
            const card = document.createElement('div');
            card.className = 'gallery-card relative group rounded-xl overflow-hidden border border-slate-200 bg-white shadow-xs select-none aspect-3/4 flex flex-col justify-between cursor-grab active:cursor-grabbing transition transform hover:scale-[1.02]';
            card.draggable = true;
            card.dataset.index = index;

            const url = URL.createObjectURL(file);

            card.innerHTML = `
                <div class="p-1.5 flex items-center justify-between z-10 pointer-events-none">
                    <span class="bg-slate-900/80 backdrop-blur text-white text-[10px] font-bold px-1.5 py-0.5 rounded shadow">
                        #${index + 1}
                    </span>
                </div>
                <img src="${url}" class="absolute inset-0 w-full h-full object-cover pointer-events-none" alt="Gallery preview">
                <div class="relative z-10 p-1 bg-gradient-to-t from-black/85 via-black/50 to-transparent flex items-center justify-between gap-1 opacity-95 sm:opacity-0 group-hover:opacity-100 transition">
                    <button type="button" onclick="moveGalleryCard(this, -1)" class="w-6 h-6 rounded bg-white/90 hover:bg-white text-slate-800 flex items-center justify-center text-[10px] font-bold shadow ${index === 0 ? 'opacity-30 pointer-events-none' : ''}" title="Move Left">
                        ◀
                    </button>
                    <button type="button" onclick="removeGalleryCard(this)" class="w-6 h-6 rounded bg-rose-600 hover:bg-rose-700 text-white flex items-center justify-center text-[11px] font-bold shadow" title="Remove image">
                        ✕
                    </button>
                    <button type="button" onclick="moveGalleryCard(this, 1)" class="w-6 h-6 rounded bg-white/90 hover:bg-white text-slate-800 flex items-center justify-center text-[10px] font-bold shadow ${index === files.length - 1 ? 'opacity-30 pointer-events-none' : ''}" title="Move Right">
                        ▶
                    </button>
                </div>
            `;

            // HTML5 Drag-and-drop events
            card.addEventListener('dragstart', (e) => {
                e.dataTransfer.setData('text/plain', index);
                card.classList.add('opacity-40');
                window._draggedGalleryCard = { block, index };
            });
            card.addEventListener('dragend', () => {
                card.classList.remove('opacity-40');
                grid.querySelectorAll('.gallery-card').forEach(c => c.classList.remove('ring-2', 'ring-indigo-500'));
                window._draggedGalleryCard = null;
            });
            card.addEventListener('dragover', (e) => {
                e.preventDefault();
                card.classList.add('ring-2', 'ring-indigo-500');
            });
            card.addEventListener('dragleave', () => {
                card.classList.remove('ring-2', 'ring-indigo-500');
            });
            card.addEventListener('drop', (e) => {
                e.preventDefault();
                card.classList.remove('ring-2', 'ring-indigo-500');
                if (!window._draggedGalleryCard || window._draggedGalleryCard.block !== block) return;

                const fromIndex = window._draggedGalleryCard.index;
                const toIndex = index;
                if (fromIndex !== toIndex) {
                    const movedFile = block._galleryFiles.splice(fromIndex, 1)[0];
                    block._galleryFiles.splice(toIndex, 0, movedFile);
                    renderGalleryPreviews(block);
                    syncGalleryFilesToInput(block);
                }
            });

            grid.appendChild(card);
        });
    }

    function moveGalleryCard(btn, direction) {
        const card = btn.closest('.gallery-card');
        const block = card.closest('.color-block');
        const index = parseInt(card.dataset.index, 10);
        const newIndex = index + direction;
        const files = block._galleryFiles || [];

        if (newIndex >= 0 && newIndex < files.length) {
            const moved = files.splice(index, 1)[0];
            files.splice(newIndex, 0, moved);
            renderGalleryPreviews(block);
            syncGalleryFilesToInput(block);
        }
    }

    function removeGalleryCard(btn) {
        const card = btn.closest('.gallery-card');
        const block = card.closest('.color-block');
        const index = parseInt(card.dataset.index, 10);
        const files = block._galleryFiles || [];

        if (index >= 0 && index < files.length) {
            files.splice(index, 1);
            renderGalleryPreviews(block);
            syncGalleryFilesToInput(block);
        }
    }

    // Start with one color block pre-added so the form isn't empty on load
    document.addEventListener('DOMContentLoaded', () => addColorBlock());
</script>
@endsection
