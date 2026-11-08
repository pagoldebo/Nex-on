{{--
    ======================================================================
    ADMIN PRODUCTS: PRODUCT EDITOR STUDIO
    ======================================================================
    File: resources/views/admin/products/edit.blade.php
    Controller: App\Http\Controllers\Admin\ProductController@edit, update
    Route Context: GET /admin/products/{product}/edit, PUT /admin/products/{product}
    ViewModel Injections:
      - $product: App\Models\Product instance with variants & images
      - $categories: Category hierarchy
    Responsibility: Full product lifecycle modifier and gallery manager
    Features:
      - Image gallery drag-and-drop reordering and cover image assignment
      - Variant color cover mapping and attribute value matrix
      - Inventory stock ledger adjustments and low-stock threshold triggers
    Security: Guarded by 'products.manage' permission
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

                    {{-- ===== 1. Dedicated Cover Image for this color ===== --}}
                    <div class="p-4 bg-slate-50/80 rounded-xl border border-slate-200/90 space-y-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider">
                                    Cover Image for this color <span class="text-indigo-600 font-semibold">(Main Display Thumbnail)</span>
                                </label>
                                <p class="text-[11px] text-slate-500 mt-0.5">
                                    Primary image displayed on catalog cards, collections, and initial swatch selection. Can be replaced or changed independently anytime.
                                </p>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                            {{-- Preview Container --}}
                            @php
                                $rawCover = $color->getRawOriginal('cover_image');
                                $currentCoverUrl = $rawCover 
                                    ? (\Illuminate\Support\Str::startsWith($rawCover, ['http://', 'https://']) ? $rawCover : asset('storage/' . ltrim($rawCover, '/')))
                                    : ($color->images->first() ? asset('storage/'.$color->images->first()->image_path) : null);
                            @endphp
                            <div class="cover-preview-wrapper relative w-24 h-28 rounded-xl border-2 border-dashed border-slate-300 bg-white flex items-center justify-center overflow-hidden shrink-0 shadow-xs">
                                <img src="{{ $currentCoverUrl ?? '' }}" class="cover-preview-img {{ $currentCoverUrl ? '' : 'hidden' }} w-full h-full object-cover" alt="Cover preview">
                                <div class="cover-placeholder-icon {{ $currentCoverUrl ? 'hidden' : 'flex' }} flex-col items-center justify-center text-slate-400 p-2 text-center">
                                    <svg class="w-6 h-6 mb-1 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <span class="text-[10px] font-semibold text-slate-400">No cover</span>
                                </div>
                                <span class="cover-badge {{ $currentCoverUrl ? '' : 'hidden' }} absolute top-1 left-1 bg-indigo-600 text-white text-[9px] font-bold px-1.5 py-0.5 rounded shadow">
                                    Cover
                                </span>
                            </div>

                            {{-- Upload & Controls --}}
                            <div class="flex-1 w-full space-y-2">
                                <input type="file" name="colors[{{ $color->id }}][cover_image]" accept="image/*"
                                       onchange="handleCoverImageSelect(this)"
                                       class="cover-file-input w-full border border-slate-200 rounded-xl px-3.5 py-2 text-xs text-slate-600 focus:outline-none file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 bg-white">
                                <div class="flex flex-wrap items-center gap-4">
                                    @if ($rawCover)
                                        <label class="inline-flex items-center gap-1.5 cursor-pointer text-xs font-semibold text-rose-600 hover:text-rose-700">
                                            <input type="checkbox" name="colors[{{ $color->id }}][remove_cover_image]" value="1" class="rounded text-rose-600 focus:ring-rose-500" onchange="toggleRemoveCoverStyle(this)">
                                            <span>Remove Saved Cover</span>
                                        </label>
                                    @endif
                                    <button type="button" onclick="clearCoverImage(this)" class="cover-clear-btn hidden text-[11px] font-semibold text-slate-500 hover:underline">
                                        ✕ Reset Selected File
                                    </button>
                                    <span class="text-[11px] text-slate-400">Accepted formats: WEBP, JPG, PNG, GIF (Max 4MB)</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ===== 2. Gallery Images: Current Images (with drag-and-drop & arrow reordering) ===== --}}
                    @if ($color->images->count())
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <div>
                                    <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider">
                                        Current Gallery Images <span class="text-slate-400 font-normal">({{ $color->images->count() }}/8 — rearrange order by dragging or using ◀ / ▶)</span>
                                    </label>
                                    <p class="text-[11px] text-slate-500">
                                        Drag to reorder display sequence or click arrow buttons. Check "Remove" to delete on save.
                                    </p>
                                </div>
                            </div>

                            <div class="existing-images-grid grid grid-cols-2 sm:grid-cols-4 md:grid-cols-8 gap-3 p-2.5 bg-slate-50/50 rounded-xl border border-slate-200">
                                @foreach ($color->images as $imgIdx => $image)
                                    <div class="existing-image-card relative group rounded-xl overflow-hidden border border-slate-200 bg-white shadow-xs select-none aspect-3/4 flex flex-col justify-between cursor-grab active:cursor-grabbing transition transform hover:scale-[1.02]"
                                         draggable="true" data-image-id="{{ $image->id }}">
                                        <input type="hidden" name="colors[{{ $color->id }}][existing_images_order][]" value="{{ $image->id }}" class="order-input">

                                        {{-- Top badge: Order number --}}
                                        <div class="p-1.5 flex items-center justify-between z-10 pointer-events-none">
                                            <span class="order-badge bg-slate-900/80 backdrop-blur text-white text-[10px] font-bold px-1.5 py-0.5 rounded shadow">
                                                #{{ $imgIdx + 1 }}
                                            </span>
                                        </div>

                                        <img src="{{ asset('storage/'.$image->image_path) }}" class="absolute inset-0 w-full h-full object-cover pointer-events-none" alt="Product image">

                                        {{-- Bottom Controls Overlay --}}
                                        <div class="relative z-10 p-1 bg-gradient-to-t from-black/85 via-black/50 to-transparent flex items-center justify-between gap-1 opacity-95 sm:opacity-0 group-hover:opacity-100 transition">
                                            <button type="button" onclick="moveExistingImageCard(this, -1)" class="btn-move-left w-6 h-6 rounded bg-white/90 hover:bg-white text-slate-800 flex items-center justify-center text-[10px] font-bold shadow {{ $loop->first ? 'opacity-30 pointer-events-none' : '' }}" title="Move Left">
                                                ◀
                                            </button>
                                            
                                            <label class="px-1.5 py-0.5 rounded bg-rose-600/90 hover:bg-rose-700 text-white text-[10px] font-bold flex items-center gap-1 cursor-pointer transition shadow" title="Mark for removal">
                                                <input type="checkbox" name="colors[{{ $color->id }}][remove_image_ids][]" value="{{ $image->id }}"
                                                       class="remove-image-checkbox sr-only" onchange="toggleImageRemoveStyle(this)">
                                                <span class="remove-label-text">Remove</span>
                                            </label>

                                            <button type="button" onclick="moveExistingImageCard(this, 1)" class="btn-move-right w-6 h-6 rounded bg-white/90 hover:bg-white text-slate-800 flex items-center justify-center text-[10px] font-bold shadow {{ $loop->last ? 'opacity-30 pointer-events-none' : '' }}" title="Move Right">
                                                ▶
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider">
                                    Add More Images <span class="text-slate-400 font-normal">(up to {{ 8 - $color->images->count() }} more)</span>
                                </label>
                                <p class="text-[11px] text-slate-500 mt-0.5">
                                    Upload additional images. You can drag and drop or use arrow buttons to rearrange them before saving.
                                </p>
                            </div>
                            <span class="gallery-count-badge text-xs font-bold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-lg">0 / {{ 8 - $color->images->count() }} new</span>
                        </div>

                        <input type="file" name="colors[{{ $color->id }}][images][]" accept="image/*" multiple
                               data-max-allowed="{{ 8 - $color->images->count() }}"
                               onchange="handleGalleryImagesSelect(this)"
                               class="gallery-file-input w-full border border-slate-200 rounded-xl px-3.5 py-2 text-xs text-slate-600 focus:outline-none file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 bg-white">

                        <div class="gallery-preview-grid grid grid-cols-2 sm:grid-cols-4 md:grid-cols-8 gap-3 min-h-[50px] p-2.5 bg-slate-50/50 rounded-xl border border-dashed border-slate-200 empty:hidden">
                        </div>
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

        {{-- ===== 1. Dedicated Cover Image for this color ===== --}}
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
                        <svg class="w-6 h-6 mb-1 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
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
                   data-max-allowed="8"
                   onchange="handleGalleryImagesSelect(this)"
                   class="gallery-file-input w-full border border-slate-200 rounded-xl px-3.5 py-2 text-xs text-slate-600 focus:outline-none file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 bg-white">

            {{-- Interactive Reorder Grid --}}
            <div class="gallery-preview-grid grid grid-cols-2 sm:grid-cols-4 md:grid-cols-8 gap-3 min-h-[50px] p-2.5 bg-slate-50/50 rounded-xl border border-dashed border-slate-200 empty:hidden">
            </div>
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
        const newBlock = wrapper.firstElementChild;
        newBlock._galleryFiles = [];
        document.getElementById('colorsContainer').appendChild(newBlock);

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
            if (icon) icon.classList.add('hidden');
            if (badge) badge.classList.remove('hidden');
            if (clearBtn) clearBtn.classList.remove('hidden');
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
        if (img.dataset.origSrc) {
            img.src = img.dataset.origSrc;
            img.classList.remove('hidden');
            if (badge) badge.classList.remove('hidden');
            if (icon) icon.classList.add('hidden');
        } else {
            img.src = '';
            img.classList.add('hidden');
            if (icon) icon.classList.remove('hidden');
            if (badge) badge.classList.add('hidden');
        }
        if (clearBtn) clearBtn.classList.add('hidden');
    }

    function toggleRemoveCoverStyle(checkbox) {
        const block = checkbox.closest('.color-block');
        const wrapper = block.querySelector('.cover-preview-wrapper');
        if (wrapper) {
            wrapper.classList.toggle('ring-4', checkbox.checked);
            wrapper.classList.toggle('ring-rose-500', checkbox.checked);
            wrapper.classList.toggle('opacity-30', checkbox.checked);
        }
    }

    // ===== Existing Images Reordering & Removal =====
    function toggleImageRemoveStyle(checkbox) {
        const card = checkbox.closest('.existing-image-card');
        const label = card.querySelector('.remove-label-text');
        if (checkbox.checked) {
            card.classList.add('ring-4', 'ring-rose-500', 'opacity-40');
            if (label) label.textContent = 'Removed';
        } else {
            card.classList.remove('ring-4', 'ring-rose-500', 'opacity-40');
            if (label) label.textContent = 'Remove';
        }
    }

    function moveExistingImageCard(btn, direction) {
        const card = btn.closest('.existing-image-card');
        const grid = card.closest('.existing-images-grid');
        if (direction === -1 && card.previousElementSibling) {
            grid.insertBefore(card, card.previousElementSibling);
        } else if (direction === 1 && card.nextElementSibling) {
            grid.insertBefore(card.nextElementSibling, card);
        }
        updateExistingGridOrder(grid);
    }

    function updateExistingGridOrder(grid) {
        const cards = Array.from(grid.querySelectorAll('.existing-image-card'));
        cards.forEach((card, idx) => {
            const badge = card.querySelector('.order-badge');
            if (badge) badge.textContent = `#${idx + 1}`;

            const leftBtn = card.querySelector('.btn-move-left');
            const rightBtn = card.querySelector('.btn-move-right');
            if (leftBtn) {
                leftBtn.classList.toggle('opacity-30', idx === 0);
                leftBtn.classList.toggle('pointer-events-none', idx === 0);
            }
            if (rightBtn) {
                rightBtn.classList.toggle('opacity-30', idx === cards.length - 1);
                rightBtn.classList.toggle('pointer-events-none', idx === cards.length - 1);
            }
        });
    }

    function setupExistingImageDragAndDrop(grid) {
        grid.addEventListener('dragstart', (e) => {
            const card = e.target.closest('.existing-image-card');
            if (!card) return;
            card.classList.add('opacity-40');
            e.dataTransfer.setData('text/plain', card.dataset.imageId);
            window._draggedExistingCard = card;
        });

        grid.addEventListener('dragend', (e) => {
            const card = e.target.closest('.existing-image-card');
            if (card) card.classList.remove('opacity-40');
            grid.querySelectorAll('.existing-image-card').forEach(c => c.classList.remove('ring-2', 'ring-indigo-500'));
            window._draggedExistingCard = null;
        });

        grid.addEventListener('dragover', (e) => {
            e.preventDefault();
            const targetCard = e.target.closest('.existing-image-card');
            if (targetCard && targetCard !== window._draggedExistingCard) {
                targetCard.classList.add('ring-2', 'ring-indigo-500');
            }
        });

        grid.addEventListener('dragleave', (e) => {
            const targetCard = e.target.closest('.existing-image-card');
            if (targetCard) targetCard.classList.remove('ring-2', 'ring-indigo-500');
        });

        grid.addEventListener('drop', (e) => {
            e.preventDefault();
            const targetCard = e.target.closest('.existing-image-card');
            grid.querySelectorAll('.existing-image-card').forEach(c => c.classList.remove('ring-2', 'ring-indigo-500'));

            if (targetCard && window._draggedExistingCard && targetCard !== window._draggedExistingCard) {
                const cards = Array.from(grid.querySelectorAll('.existing-image-card'));
                const draggedIdx = cards.indexOf(window._draggedExistingCard);
                const targetIdx = cards.indexOf(targetCard);
                if (draggedIdx < targetIdx) {
                    grid.insertBefore(window._draggedExistingCard, targetCard.nextSibling);
                } else {
                    grid.insertBefore(window._draggedExistingCard, targetCard);
                }
                updateExistingGridOrder(grid);
            }
        });
    }

    // ===== New Gallery Images Handling & Rearranging (for edit page) =====
    function handleGalleryImagesSelect(input) {
        const block = input.closest('.color-block');
        if (!block._galleryFiles) {
            block._galleryFiles = [];
        }

        const maxAllowed = parseInt(input.dataset.maxAllowed || '8', 10);
        const availableSlots = maxAllowed - block._galleryFiles.length;

        if (availableSlots <= 0) {
            alert(`You have reached the maximum allowed images for this color.`);
            syncGalleryFilesToInput(block, maxAllowed);
            return;
        }

        const newFiles = Array.from(input.files);
        if (newFiles.length > availableSlots) {
            alert(`You can only add ${availableSlots} more image(s). Only the first ${availableSlots} will be added.`);
        }

        const filesToAdd = newFiles.slice(0, availableSlots);
        block._galleryFiles = block._galleryFiles.concat(filesToAdd);

        renderGalleryPreviews(block, maxAllowed);
        syncGalleryFilesToInput(block, maxAllowed);
    }

    function syncGalleryFilesToInput(block, maxAllowed) {
        const input = block.querySelector('.gallery-file-input');
        const dt = new DataTransfer();
        (block._galleryFiles || []).forEach(file => dt.items.add(file));
        input.files = dt.files;

        const badge = block.querySelector('.gallery-count-badge');
        if (badge) {
            const count = (block._galleryFiles || []).length;
            const max = maxAllowed !== undefined ? maxAllowed : parseInt(input.dataset.maxAllowed || '8', 10);
            badge.textContent = `${count} / ${max} new`;
        }
    }

    function renderGalleryPreviews(block, maxAllowed) {
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
                    <span class="bg-indigo-600/90 backdrop-blur text-white text-[10px] font-bold px-1.5 py-0.5 rounded shadow">
                        New #${index + 1}
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
                window._draggedGalleryCard = { block, index, maxAllowed };
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
                    renderGalleryPreviews(block, maxAllowed);
                    syncGalleryFilesToInput(block, maxAllowed);
                }
            });

            grid.appendChild(card);
        });
    }

    function moveGalleryCard(btn, direction) {
        const card = btn.closest('.gallery-card');
        const block = card.closest('.color-block');
        const input = block.querySelector('.gallery-file-input');
        const maxAllowed = parseInt(input.dataset.maxAllowed || '8', 10);
        const index = parseInt(card.dataset.index, 10);
        const newIndex = index + direction;
        const files = block._galleryFiles || [];

        if (newIndex >= 0 && newIndex < files.length) {
            const moved = files.splice(index, 1)[0];
            files.splice(newIndex, 0, moved);
            renderGalleryPreviews(block, maxAllowed);
            syncGalleryFilesToInput(block, maxAllowed);
        }
    }

    function removeGalleryCard(btn) {
        const card = btn.closest('.gallery-card');
        const block = card.closest('.color-block');
        const input = block.querySelector('.gallery-file-input');
        const maxAllowed = parseInt(input.dataset.maxAllowed || '8', 10);
        const index = parseInt(card.dataset.index, 10);
        const files = block._galleryFiles || [];

        if (index >= 0 && index < files.length) {
            files.splice(index, 1);
            renderGalleryPreviews(block, maxAllowed);
            syncGalleryFilesToInput(block, maxAllowed);
        }
    }

    // Initialize drag-and-drop on all existing image grids on page load
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.existing-images-grid').forEach(grid => {
            setupExistingImageDragAndDrop(grid);
        });
        document.querySelectorAll('.cover-preview-img').forEach(img => {
            if (img.getAttribute('src')) {
                img.dataset.origSrc = img.getAttribute('src');
            }
        });
    });
</script>
@endsection
