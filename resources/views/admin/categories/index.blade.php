@extends('admin.layout')

@section('content')
@php
    $allRootIds = $flatTree->where('depth', 0)->pluck('id')->values()->all();
    $firstRootId = $flatTree->where('depth', 0)->first()?->id ?? null;
@endphp
<div class="space-y-8" x-data="{ 
    editModalOpen: false, 
    activeCategory: { id: '', name: '', slug: '', sort_order: '', parent_id: '', image_url: '' },
    newImagePreview: null,
    editImagePreview: null,

    // Category Hierarchy Tree Auto-Collapse & Accordion State
    tree: {
        sectionCollapsed: false,
        autoCollapse: true,
        openRoots: (function() {
            const firstId = {{ json_encode($firstRootId) }};
            const map = {};
            if (firstId) {
                map[firstId] = true;
            }
            return map;
        })(),
        collapsedSubs: {},

        toggleRoot(rootId) {
            const currentlyOpen = !!this.openRoots[rootId];
            if (this.autoCollapse) {
                // Like admin sidebar menu: close other root categories when expanding one
                this.openRoots = {};
                if (!currentlyOpen) {
                    this.openRoots[rootId] = true;
                }
            } else {
                this.openRoots[rootId] = !currentlyOpen;
            }
        },

        toggleSub(subId) {
            this.collapsedSubs[subId] = !this.collapsedSubs[subId];
        },

        isRootOpen(rootId) {
            return !!this.openRoots[rootId];
        },

        isSubOpen(subId) {
            return !this.collapsedSubs[subId];
        },

        isRowVisible(depth, rootId, ancestorIds) {
            if (depth === 0) return true;
            if (!this.openRoots[rootId]) return false;
            for (let i = 1; i < ancestorIds.length; i++) {
                if (this.collapsedSubs[ancestorIds[i]]) {
                    return false;
                }
            }
            return true;
        },

        expandAll(allRootIds) {
            allRootIds.forEach(id => {
                this.openRoots[id] = true;
            });
            this.collapsedSubs = {};
        },

        collapseAll() {
            this.openRoots = {};
            this.collapsedSubs = {};
        },

        toggleAutoCollapse() {
            this.autoCollapse = !this.autoCollapse;
            if (this.autoCollapse) {
                const openKeys = Object.keys(this.openRoots).filter(k => this.openRoots[k]);
                if (openKeys.length > 1) {
                    this.openRoots = { [openKeys[0]]: true };
                }
            }
        }
    },

    openEdit(cat) {
        this.activeCategory = { 
            id: cat.id, 
            name: cat.name, 
            slug: cat.slug, 
            sort_order: cat.sort_order, 
            parent_id: cat.parent_id !== null ? cat.parent_id : '',
            image_url: cat.image_url || ''
        };
        this.editImagePreview = null;
        if (this.$refs.editFileInput) {
            this.$refs.editFileInput.value = '';
        }
        this.editModalOpen = true;
    },
    handleNewImage(e) {
        const file = e.target.files[0];
        if (file) {
            this.newImagePreview = URL.createObjectURL(file);
        } else {
            this.newImagePreview = null;
        }
    },
    handleEditImage(e) {
        const file = e.target.files[0];
        if (file) {
            this.editImagePreview = URL.createObjectURL(file);
        } else {
            this.editImagePreview = null;
        }
    }
}">
    <!-- Header -->
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Product Categories &amp; Subcategories</h1>
            <p class="mt-1 text-sm text-slate-500">Organize multi-level collections (e.g. Men → Shirt → Formal, Women → Bra → Padded) across storefront menus and filters.</p>
        </div>
        <div class="mt-4 sm:mt-0 flex items-center gap-3">
            <div class="text-xs font-semibold text-slate-500 bg-white border border-slate-200 px-3.5 py-1.5 rounded-xl shadow-xs">
                Total Categories: <span class="text-indigo-600 font-bold">{{ count($flatTree) }}</span>
            </div>
            <div class="text-xs font-semibold text-slate-500 bg-white border border-slate-200 px-3.5 py-1.5 rounded-xl shadow-xs">
                Root Categories: <span class="text-emerald-600 font-bold">{{ $categories->whereNull('parent_id')->count() }}</span>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-center gap-3">
            <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm">
            <div class="font-bold mb-1 flex items-center gap-2">
                <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>Please fix the following:</span>
            </div>
            <ul class="list-disc list-inside text-xs space-y-1 ml-6">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Main Grid: Add Category Form (1/3) + Category Table (2/3) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
        <!-- Add Category Card -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs sticky top-20">
            <div class="flex items-center gap-2.5 mb-4">
                <span class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-sm">+</span>
                <h2 class="text-base font-bold text-slate-900">Add Category / Subcategory</h2>
            </div>
            <p class="text-xs text-slate-400 mb-5">Create a root category (e.g. Men, Women) or select a parent to create subcategories &amp; nested sub-subcategories.</p>

            <form method="POST" action="{{ route('admin.categories.store') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div>
                    <label for="new_parent_id" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Parent Category</label>
                    <select id="new_parent_id" name="parent_id" 
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition bg-white">
                        <option value="">None (Top-Level / Root Category)</option>
                        @foreach($flatTree as $item)
                            <option value="{{ data_get($item, 'id') }}">{!! data_get($item, 'indent_name') !!}</option>
                        @endforeach
                    </select>
                    <p class="text-[11px] text-slate-400 mt-1">Leave empty for main categories like "Men" or "Women". Select a category to create subcategories (e.g. Shirt, Pants) or sub-subcategories.</p>
                </div>

                <div>
                    <label for="new_name" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Category Name *</label>
                    <input type="text" id="new_name" name="name" required placeholder="e.g. Shirt, Bra, Saree, Dhuti" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                </div>

                <div>
                    <label for="new_slug" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Custom Slug (optional)</label>
                    <input type="text" id="new_slug" name="slug" placeholder="e.g. men-shirt (auto-generated if empty)" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-mono focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                </div>

                <div>
                    <label for="new_sort_order" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Display Sort Order</label>
                    <input type="number" id="new_sort_order" name="sort_order" value="{{ count($flatTree) + 1 }}" min="0" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                    <p class="text-[11px] text-slate-400 mt-1">Lower numbers display first on navigation menus.</p>
                </div>

                <!-- Category Image Upload -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                        Category Image (Optional)
                    </label>
                    <p class="text-[11px] text-slate-400 mb-2">Recommended for Parent Categories so this image appears in storefront "Browse Shop by Category".</p>
                    <div class="flex items-center gap-3">
                        <template x-if="newImagePreview">
                            <div class="relative w-16 h-16 rounded-xl overflow-hidden border border-slate-200 shrink-0 shadow-xs">
                                <img :src="newImagePreview" class="w-full h-full object-cover" alt="Preview">
                                <button type="button" @click="newImagePreview = null; $refs.newFileInput.value = ''" 
                                        class="absolute top-1 right-1 bg-rose-600 hover:bg-rose-700 text-white rounded-full w-4 h-4 flex items-center justify-center text-[10px] font-bold leading-none shadow-xs">✕</button>
                            </div>
                        </template>
                        <template x-if="!newImagePreview">
                            <div class="w-16 h-16 rounded-xl border border-dashed border-slate-300 bg-slate-50 flex flex-col items-center justify-center text-slate-400 shrink-0">
                                <svg class="w-6 h-6 stroke-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                        </template>
                        <div class="flex-1 min-w-0">
                            <input type="file" id="new_image" name="image" accept="image/jpeg,image/png,image/webp,image/svg+xml,image/gif" x-ref="newFileInput" @change="handleNewImage"
                                   class="block w-full text-xs text-slate-500 file:mr-2.5 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition cursor-pointer">
                            <p class="text-[11px] text-slate-400 mt-1">PNG, JPG, WEBP, SVG up to 5MB.</p>
                        </div>
                    </div>
                </div>

                <button type="submit" 
                        class="w-full py-2.5 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold tracking-wide transition shadow-sm flex items-center justify-center gap-1.5 mt-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Save Category</span>
                </button>
            </form>
        </div>

        <!-- Categories Table (2 cols) -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex flex-wrap items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2.5">
                        <h2 class="text-base font-bold text-slate-900">Category Hierarchy &amp; Tree</h2>
                        <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full transition"
                              :class="tree.autoCollapse ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-slate-100 text-slate-600'">
                            <span x-text="tree.autoCollapse ? 'Accordion: Auto-Collapse ON' : 'Accordion: Multi-Open'"></span>
                        </span>
                    </div>
                    <p class="text-xs text-slate-400 mt-0.5">Click any root category row to expand or collapse its subcategories.</p>
                </div>

                <!-- Tree Controls Toolbar -->
                <div class="flex items-center gap-2">
                    <!-- Toggle Auto-Collapse Button -->
                    <button type="button" 
                            @click="tree.toggleAutoCollapse()"
                            class="px-2.5 py-1.5 text-xs font-semibold rounded-xl border transition flex items-center gap-1.5"
                            :class="tree.autoCollapse ? 'bg-indigo-600 text-white border-indigo-600 shadow-xs' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50'"
                            :title="tree.autoCollapse ? 'Auto-Collapse is ON: Opening one category closes others (like admin menu). Click to toggle.' : 'Auto-Collapse is OFF: Multiple categories can stay open. Click to toggle.'">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
                        <span x-text="tree.autoCollapse ? 'Auto-Collapse: ON' : 'Auto-Collapse: OFF'"></span>
                    </button>

                    <!-- Expand All Button -->
                    <button type="button" 
                            @click="tree.expandAll({{ json_encode($allRootIds) }})"
                            class="px-2.5 py-1.5 text-xs font-semibold rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition"
                            title="Expand all root and nested categories">
                        Expand All
                    </button>

                    <!-- Collapse All Button -->
                    <button type="button" 
                            @click="tree.collapseAll()"
                            class="px-2.5 py-1.5 text-xs font-semibold rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition"
                            title="Collapse all categories to show only root items">
                        Collapse All
                    </button>

                    <!-- Section Collapse Toggle -->
                    <button type="button"
                            @click="tree.sectionCollapsed = !tree.sectionCollapsed"
                            class="p-1.5 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition"
                            :title="tree.sectionCollapsed ? 'Expand Tree Section' : 'Collapse Tree Section'">
                        <svg class="w-4 h-4 transition-transform duration-200" :class="tree.sectionCollapsed ? '-rotate-90' : 'rotate-0'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Collapsed Section Message (if card minimized) -->
            <div x-show="tree.sectionCollapsed" class="p-6 text-center text-slate-400 text-xs" x-cloak>
                <p>Category Hierarchy &amp; Tree is collapsed. <button type="button" @click="tree.sectionCollapsed = false" class="text-indigo-600 font-semibold hover:underline">Click here to expand</button>.</p>
            </div>

            <div x-show="!tree.sectionCollapsed" class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-left text-sm">
                    <thead class="bg-slate-50/70 text-slate-500 uppercase text-[11px] font-bold tracking-wider">
                        <tr>
                            <th class="px-5 py-3">Order</th>
                            <th class="px-5 py-3">Category / Subcategory Name</th>
                            <th class="px-5 py-3">Slug</th>
                            <th class="px-5 py-3 text-center">Products</th>
                            <th class="px-5 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($flatTree as $item)
                            @php
                                $cat = data_get($item, 'category');
                                $depth = (int) data_get($item, 'depth', 0);
                                $catId = (int) data_get($item, 'id');
                                $rootId = (int) data_get($item, 'root_id');
                                $ancestorIds = data_get($item, 'ancestor_ids', []);
                                $hasChildren = (bool) data_get($item, 'has_children', false);
                            @endphp
                            <tr x-show="tree.isRowVisible({{ $depth }}, {{ $rootId }}, {{ json_encode($ancestorIds) }})"
                                class="hover:bg-slate-50/80 transition {{ $depth === 0 ? 'bg-slate-50/40 cursor-pointer select-none font-medium' : '' }}"
                                @if($depth === 0 && $hasChildren)
                                    @click="if (! $event.target.closest('a, button, form, input')) { tree.toggleRoot({{ $catId }}) }"
                                @endif
                            >
                                <td class="px-5 py-3.5 font-mono font-bold text-xs text-slate-400">
                                    #{{ $cat->sort_order }}
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-3" style="padding-left: {{ $depth * 20 }}px">
                                        {{-- Root Category Expand/Collapse Chevron Button --}}
                                        @if($depth === 0)
                                            @if($hasChildren)
                                                <button type="button" 
                                                        @click.stop="tree.toggleRoot({{ $catId }})"
                                                        class="w-6 h-6 rounded-md hover:bg-slate-200/70 flex items-center justify-center transition-colors shrink-0 text-slate-400 hover:text-indigo-600"
                                                        :title="tree.isRootOpen({{ $catId }}) ? 'Collapse subcategories' : 'Expand subcategories'">
                                                    <svg class="w-4 h-4 transition-transform duration-200" 
                                                         :class="tree.isRootOpen({{ $catId }}) ? 'rotate-90 text-indigo-600' : 'rotate-0 text-slate-400'" 
                                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                                                    </svg>
                                                </button>
                                            @else
                                                <span class="w-6 h-6 shrink-0"></span>
                                            @endif
                                        @endif

                                        {{-- Hierarchy Connector Arrow for Subcategories --}}
                                        @if($depth === 1)
                                            <span class="text-slate-300 font-mono text-sm shrink-0">↳</span>
                                            @if($hasChildren)
                                                <button type="button" 
                                                        @click.stop="tree.toggleSub({{ $catId }})"
                                                        class="w-5 h-5 rounded hover:bg-slate-200/70 flex items-center justify-center transition-colors shrink-0 text-slate-400 hover:text-indigo-600"
                                                        :title="tree.isSubOpen({{ $catId }}) ? 'Collapse child categories' : 'Expand child categories'">
                                                    <svg class="w-3.5 h-3.5 transition-transform duration-200" 
                                                         :class="tree.isSubOpen({{ $catId }}) ? 'rotate-90 text-indigo-600' : 'rotate-0 text-slate-400'" 
                                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                                                    </svg>
                                                </button>
                                            @endif
                                        @elseif($depth >= 2)
                                            <span class="text-slate-300 font-mono text-sm shrink-0">↳ ↳</span>
                                            @if($hasChildren)
                                                <button type="button" 
                                                        @click.stop="tree.toggleSub({{ $catId }})"
                                                        class="w-5 h-5 rounded hover:bg-slate-200/70 flex items-center justify-center transition-colors shrink-0 text-slate-400 hover:text-indigo-600"
                                                        :title="tree.isSubOpen({{ $catId }}) ? 'Collapse child categories' : 'Expand child categories'">
                                                    <svg class="w-3.5 h-3.5 transition-transform duration-200" 
                                                         :class="tree.isSubOpen({{ $catId }}) ? 'rotate-90 text-indigo-600' : 'rotate-0 text-slate-400'" 
                                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                                                    </svg>
                                                </button>
                                            @endif
                                        @endif

                                        {{-- Category Image or Fallback Icon --}}
                                        @if($cat->image_url)
                                            <div class="relative group/thumb shrink-0">
                                                <img src="{{ $cat->image_url }}" alt="{{ $cat->name }}" class="{{ $depth === 0 ? 'w-9 h-9 rounded-xl' : ($depth === 1 ? 'w-8 h-8 rounded-lg' : 'w-7 h-7 rounded-lg') }} object-cover border border-slate-200 shadow-xs bg-white">
                                            </div>
                                        @elseif($depth === 0)
                                            <span class="w-9 h-9 rounded-xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-sm font-bold shrink-0" title="No image uploaded">📁</span>
                                        @elseif($depth === 1)
                                            <span class="w-8 h-8 rounded-lg bg-amber-50 border border-amber-100 text-amber-600 flex items-center justify-center text-xs font-bold shrink-0">📂</span>
                                        @else
                                            <span class="w-7 h-7 rounded-lg bg-purple-50 border border-purple-100 text-purple-600 flex items-center justify-center text-[10px] font-bold shrink-0">📄</span>
                                        @endif

                                        <div>
                                            <div class="flex items-center gap-2">
                                                <span class="{{ $depth === 0 ? 'font-extrabold text-slate-900 text-sm' : ($depth === 1 ? 'font-semibold text-slate-800 text-sm' : 'font-medium text-slate-700 text-sm') }}">
                                                    {{ $cat->name }}
                                                </span>
                                                @if($depth === 0)
                                                    <span class="text-[10px] bg-indigo-50 text-indigo-700 font-semibold px-2 py-0.5 rounded-full border border-indigo-100">Parent</span>
                                                    @if($cat->image_url)
                                                        <span class="text-[10px] bg-emerald-50 text-emerald-700 font-semibold px-1.5 py-0.5 rounded-md border border-emerald-100 flex items-center gap-1">
                                                            <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                            <span>Home banner</span>
                                                        </span>
                                                    @else
                                                        <span class="text-[10px] bg-amber-50 text-amber-700 font-medium px-1.5 py-0.5 rounded-md border border-amber-100">No image</span>
                                                    @endif

                                                    @if($hasChildren)
                                                        <span x-show="!tree.isRootOpen({{ $catId }})" class="text-[11px] text-slate-400 italic font-normal">
                                                            (collapsed &bull; click to expand)
                                                        </span>
                                                    @endif
                                                @elseif($depth === 1)
                                                    <span class="text-[10px] bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded font-medium">Sub of {{ $cat->parent?->name }}</span>
                                                @else
                                                    <span class="text-[10px] bg-purple-50 text-purple-600 px-1.5 py-0.5 rounded font-mono">{{ $cat->hierarchy_name }}</span>
                                                @endif

                                                @if($cat->children->count() > 0)
                                                    <button type="button"
                                                            @if($depth === 0)
                                                                @click.stop="tree.toggleRoot({{ $catId }})"
                                                                :class="tree.isRootOpen({{ $catId }}) ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-100 text-slate-600 border-slate-200 hover:bg-indigo-50 hover:text-indigo-700 hover:border-indigo-200'"
                                                            @else
                                                                @click.stop="tree.toggleSub({{ $catId }})"
                                                                :class="tree.isSubOpen({{ $catId }}) ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-100 text-slate-600 border-slate-200 hover:bg-indigo-50 hover:text-indigo-700 hover:border-indigo-200'"
                                                            @endif
                                                            class="text-[10px] font-semibold px-2 py-0.5 rounded-full border transition inline-flex items-center gap-1 cursor-pointer">
                                                        <span>{{ $cat->children->count() }} sub</span>
                                                        <span x-show="{{ $depth === 0 ? '!tree.isRootOpen('.$catId.')' : '!tree.isSubOpen('.$catId.')' }}" class="text-[9px]">▸</span>
                                                        <span x-show="{{ $depth === 0 ? 'tree.isRootOpen('.$catId.')' : 'tree.isSubOpen('.$catId.')' }}" class="text-[9px]">▾</span>
                                                    </button>
                                                @endif
                                            </div>
                                            <div class="mt-0.5">
                                                <a href="{{ route('category.show', $cat->slug) }}" target="_blank" class="text-[11px] text-indigo-600 hover:underline inline-flex items-center gap-1">
                                                    <span>View storefront page</span>
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 font-mono text-xs text-slate-500">
                                    {{ $cat->slug }}
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $cat->products_count > 0 ? 'bg-indigo-50 text-indigo-700 border border-indigo-100' : 'bg-slate-100 text-slate-400' }}">
                                        {{ $cat->products_count }} {{ Str::plural('piece', $cat->products_count) }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <!-- Inline Edit Trigger -->
                                        <button type="button" 
                                                @click="openEdit({{ json_encode($cat) }})"
                                                class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 transition">
                                            Edit
                                        </button>

                                        <!-- Delete Form -->
                                        <form method="POST" action="{{ route('admin.categories.destroy', $cat) }}" onsubmit="return confirm('Are you sure you want to delete \'{{ addslashes($cat->name) }}\'?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="px-2.5 py-1 text-xs font-semibold rounded-lg text-rose-600 hover:bg-rose-50 transition">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-slate-400 text-xs">
                                    No categories created yet. Use the form on the left to add your first collection.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div x-show="editModalOpen" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         x-cloak>
        <div class="min-h-screen px-4 text-center flex items-center justify-center">
            <!-- Modal Backdrop -->
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" 
                 @click="editModalOpen = false"></div>

            <!-- Modal Panel -->
            <div class="relative bg-white rounded-2xl max-w-md w-full p-6 text-left shadow-2xl border border-slate-200 z-10">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-5">
                    <h3 class="text-base font-bold text-slate-900">Edit Category / Subcategory</h3>
                    <button @click="editModalOpen = false" class="text-slate-400 hover:text-slate-600 font-bold p-1">✕</button>
                </div>

                <form :action="'{{ url('admin/categories') }}/' + activeCategory.id" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Parent Category</label>
                        <select name="parent_id" x-model="activeCategory.parent_id"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition bg-white">
                            <option value="">None (Top-Level / Root Category)</option>
                            @foreach($flatTree as $item)
                                <option value="{{ data_get($item, 'id') }}" :disabled="activeCategory.id == {{ data_get($item, 'id') }}">{!! data_get($item, 'indent_name') !!}</option>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-slate-400 mt-1">Change parent category to nest under a different collection.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Category Name *</label>
                        <input type="text" name="name" x-model="activeCategory.name" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">URL Slug *</label>
                        <input type="text" name="slug" x-model="activeCategory.slug" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-mono focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Sort Order</label>
                        <input type="number" name="sort_order" x-model="activeCategory.sort_order" min="0"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                    </div>

                    <!-- Category Image Field -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                            Category Image
                        </label>
                        <p class="text-[11px] text-slate-400 mb-2">Displayed in storefront "Browse Shop by Category" section for parent categories.</p>

                        <!-- Existing Image (if category currently has one) -->
                        <div x-show="activeCategory.image_url" class="mb-3 p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <img :src="activeCategory.image_url" alt="Category Image" class="w-12 h-12 rounded-lg object-cover border border-slate-200 shadow-xs bg-white">
                                <div>
                                    <span class="text-xs font-bold text-slate-700 block">Current Image</span>
                                    <span class="text-[11px] text-emerald-600 font-medium">Active on storefront</span>
                                </div>
                            </div>
                            <label class="inline-flex items-center gap-1.5 cursor-pointer text-xs font-semibold text-rose-600 hover:text-rose-700 bg-white border border-rose-200 px-2.5 py-1.5 rounded-lg shadow-xs hover:bg-rose-50 transition">
                                <input type="checkbox" name="remove_image" value="1" class="rounded text-rose-600 focus:ring-rose-500">
                                <span>Remove</span>
                            </label>
                        </div>

                        <!-- Image File Picker + Preview -->
                        <div class="flex items-center gap-3">
                            <template x-if="editImagePreview">
                                <div class="relative w-12 h-12 rounded-lg overflow-hidden border border-slate-200 shrink-0 shadow-xs">
                                    <img :src="editImagePreview" class="w-full h-full object-cover" alt="New Image Preview">
                                    <button type="button" @click="editImagePreview = null; $refs.editFileInput.value = ''" 
                                            class="absolute top-0.5 right-0.5 bg-rose-600 text-white rounded-full w-3.5 h-3.5 flex items-center justify-center text-[9px] font-bold leading-none hover:bg-rose-700">✕</button>
                                </div>
                            </template>
                            <div class="flex-1 min-w-0">
                                <input type="file" name="image" accept="image/jpeg,image/png,image/webp,image/svg+xml,image/gif" x-ref="editFileInput" @change="handleEditImage"
                                       class="block w-full text-xs text-slate-500 file:mr-2.5 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition cursor-pointer">
                                <p class="text-[11px] text-slate-400 mt-1">Upload a new image to replace current (PNG, JPG, WEBP, SVG up to 5MB).</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3">
                        <button type="button" @click="editModalOpen = false" 
                                class="px-4 py-2 text-xs font-semibold rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-5 py-2 text-xs font-bold rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white transition shadow-sm">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
