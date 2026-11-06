@extends('admin.layout', [
    'title' => 'Brands & Add-ons',
    'breadcrumb' => 'Brands & Add-ons',
])

@section('content')
<div class="max-w-7xl mx-auto space-y-6" x-data="{
    activeTab: '{{ request('tab', $tab ?? 'brands') }}',

    // Addon modal state
    addonModalOpen: false,
    isAddonEdit: false,
    addonEditAction: '',
    addonForm: {
        id: null,
        name: '',
        description: '',
        price: 49,
        is_active: true
    },
    openCreateAddon() {
        this.isAddonEdit = false;
        this.addonForm = { id: null, name: '', description: '', price: 49, is_active: true };
        this.addonEditAction = '{{ route('admin.addons.store') }}';
        this.addonModalOpen = true;
    },
    openEditAddon(a) {
        this.isAddonEdit = true;
        this.addonForm = {
            id: a.id,
            name: a.name,
            description: a.description || '',
            price: a.price,
            is_active: Boolean(a.is_active)
        };
        this.addonEditAction = '/admin/addons/' + a.id;
        this.addonModalOpen = true;
    },

    // Brand modal state
    brandModalOpen: false,
    isBrandEdit: false,
    brandEditAction: '',
    brandForm: {
        id: null,
        name: '',
        description: '',
        website: '',
        is_active: true
    },
    openCreateBrand() {
        this.isBrandEdit = false;
        this.brandForm = { id: null, name: '', description: '', website: '', is_active: true };
        this.brandEditAction = '{{ route('admin.brands.store') }}';
        this.brandModalOpen = true;
    },
    openEditBrand(b) {
        this.isBrandEdit = true;
        this.brandForm = {
            id: b.id,
            name: b.name,
            description: b.description || '',
            website: b.website || '',
            is_active: Boolean(b.is_active)
        };
        this.brandEditAction = '/admin/brands/' + b.id;
        this.brandModalOpen = true;
    }
}">

    <!-- Top Header & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Brands &amp; Add-ons</h1>
            <p class="text-sm text-slate-500 mt-1">Configure brand houses, designer labels, and customer packaging &amp; gifting add-ons for your products.</p>
        </div>
        <div class="flex items-center gap-2.5">
            <template x-if="activeTab === 'brands'">
                <button @click="openCreateBrand()" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold shadow-md shadow-indigo-600/20 transition flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Create Brand</span>
                </button>
            </template>
            <template x-if="activeTab === 'addons'">
                <button @click="openCreateAddon()" class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-sm font-semibold shadow-md shadow-rose-600/20 transition flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Create Add-on</span>
                </button>
            </template>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex items-center gap-2 border-b border-slate-200">
        <button type="button" @click="activeTab = 'brands'"
                :class="activeTab === 'brands' ? 'border-indigo-600 text-indigo-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-800'"
                class="px-5 py-3 border-b-2 text-sm transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
            <span>Brands &amp; Labels</span>
            <span class="px-2 py-0.5 rounded-full text-xs" :class="activeTab === 'brands' ? 'bg-indigo-100 text-indigo-700 font-bold' : 'bg-slate-100 text-slate-600'">{{ count($brands) }}</span>
        </button>
        <button type="button" @click="activeTab = 'addons'"
                :class="activeTab === 'addons' ? 'border-rose-600 text-rose-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-800'"
                class="px-5 py-3 border-b-2 text-sm transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            <span>Add-ons &amp; Packaging</span>
            <span class="px-2 py-0.5 rounded-full text-xs" :class="activeTab === 'addons' ? 'bg-rose-100 text-rose-700 font-bold' : 'bg-slate-100 text-slate-600'">{{ $addons->total() }}</span>
        </button>
    </div>

    <!-- Search Bar -->
    <div class="flex items-center justify-between">
        <form method="GET" action="{{ route('admin.addons.index') }}" class="relative max-w-sm w-full">
            <input type="hidden" name="tab" :value="activeTab">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search brand or add-on..."
                   class="w-full pl-10 pr-4 py-2 text-sm bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition shadow-xs">
            <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </form>
    </div>

    <!-- TAB 1: BRANDS -->
    <div x-show="activeTab === 'brands'" class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50/75 border-b border-slate-200/80 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    <tr>
                        <th class="py-3.5 px-4">Brand Name</th>
                        <th class="py-3.5 px-4">Slug</th>
                        <th class="py-3.5 px-4">Description</th>
                        <th class="py-3.5 px-4 text-center">Products</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($brands as $brand)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-3.5 px-4 font-bold text-slate-900 flex items-center gap-2.5">
                                <span class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 font-extrabold flex items-center justify-center text-xs shrink-0 border border-indigo-100">
                                    {{ strtoupper(substr($brand->name, 0, 1)) }}
                                </span>
                                <div>
                                    <span>{{ $brand->name }}</span>
                                    @if($brand->website)
                                        <a href="{{ $brand->website }}" target="_blank" class="block text-[10px] text-slate-400 hover:text-indigo-600 truncate max-w-[150px]">{{ $brand->website }}</a>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3.5 px-4 font-mono text-xs text-slate-500">
                                {{ $brand->slug }}
                            </td>
                            <td class="py-3.5 px-4 text-slate-500 max-w-sm truncate text-xs">
                                {{ $brand->description ?? '—' }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                                    {{ $brand->products_count }} {{ Str::plural('item', $brand->products_count) }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $brand->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $brand->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="inline-flex items-center gap-2">
                                    <button @click="openEditBrand({{ json_encode($brand) }})" class="p-1.5 text-slate-500 hover:text-indigo-600 hover:bg-slate-100 rounded-lg transition" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <form method="POST" action="{{ route('admin.brands.destroy', $brand) }}" onsubmit="return confirm('Delete this brand? Products linked to this brand will become unbranded.')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-500 hover:text-red-600 hover:bg-slate-100 rounded-lg transition" title="Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">
                                No brands added yet. Click &ldquo;Create Brand&rdquo; to add your first brand.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- TAB 2: ADD-ONS & PACKAGING -->
    <div x-show="activeTab === 'addons'" class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50/75 border-b border-slate-200/80 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    <tr>
                        <th class="py-3.5 px-4">Add-on Item</th>
                        <th class="py-3.5 px-4">Description</th>
                        <th class="py-3.5 px-4 text-right">Fee (₹)</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($addons as $addon)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-3.5 px-4 font-semibold text-slate-900">
                                {{ $addon->name }}
                            </td>
                            <td class="py-3.5 px-4 text-slate-500 max-w-md truncate">
                                {{ $addon->description ?? '—' }}
                            </td>
                            <td class="py-3.5 px-4 font-mono font-bold text-slate-800 text-right">
                                ₹{{ number_format($addon->price, 2) }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $addon->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $addon->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="inline-flex items-center gap-2">
                                    <button @click="openEditAddon({{ json_encode($addon) }})" class="p-1.5 text-slate-500 hover:text-rose-600 hover:bg-slate-100 rounded-lg transition" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <form method="POST" action="{{ route('admin.addons.destroy', $addon) }}" onsubmit="return confirm('Delete this add-on item?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-500 hover:text-red-600 hover:bg-slate-100 rounded-lg transition" title="Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-400">
                                No add-on options configured yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($addons, 'hasPages') && $addons->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $addons->links() }}
            </div>
        @endif
    </div>

    <!-- Create/Edit Addon Modal -->
    <div x-show="addonModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="addonModalOpen" x-transition.opacity class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" @click="addonModalOpen = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="addonModalOpen" x-transition.scale.95 class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200">
                <form :action="addonEditAction" method="POST">
                    @csrf
                    <template x-if="isAddonEdit">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <div class="p-6 space-y-4">
                        <h3 class="text-lg font-bold text-slate-900" x-text="isAddonEdit ? 'Edit Add-on' : 'Create New Add-on'"></h3>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Add-on Name *</label>
                            <input type="text" name="name" x-model="addonForm.name" required placeholder="e.g. Discreet Gift Box"
                                   class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Description</label>
                            <textarea name="description" x-model="addonForm.description" rows="2" placeholder="Brief note explaining what is included..."
                                      class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500"></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Price (₹) *</label>
                            <input type="number" step="0.01" min="0" name="price" x-model="addonForm.price" required
                                   class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 font-mono">
                        </div>

                        <div class="flex items-center gap-2 pt-2">
                            <input type="checkbox" id="addonActive" name="is_active" value="1" x-model="addonForm.is_active"
                                   class="w-4 h-4 text-rose-600 rounded border-slate-300 focus:ring-rose-500">
                            <label for="addonActive" class="text-sm font-medium text-slate-700">Active and available for products</label>
                        </div>
                    </div>

                    <div class="bg-slate-50 px-6 py-3 border-t border-slate-100 flex items-center justify-end gap-3">
                        <button type="button" @click="addonModalOpen = false" class="px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-800">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2 text-sm font-semibold text-white bg-rose-600 hover:bg-rose-700 rounded-xl shadow-md shadow-rose-600/20 transition">
                            Save Add-on
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Create/Edit Brand Modal -->
    <div x-show="brandModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="brand-modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="brandModalOpen" x-transition.opacity class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" @click="brandModalOpen = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="brandModalOpen" x-transition.scale.95 class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200">
                <form :action="brandEditAction" method="POST">
                    @csrf
                    <template x-if="isBrandEdit">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <div class="p-6 space-y-4">
                        <h3 class="text-lg font-bold text-slate-900" x-text="isBrandEdit ? 'Edit Brand' : 'Create New Brand'"></h3>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Brand Name *</label>
                            <input type="text" name="name" x-model="brandForm.name" required placeholder="e.g. Sabyasachi Atelier"
                                   class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Description / Tagline</label>
                            <textarea name="description" x-model="brandForm.description" rows="2" placeholder="Brief note about the designer label, heritage, or aesthetic..."
                                      class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Official Website (optional)</label>
                            <input type="url" name="website" x-model="brandForm.website" placeholder="https://example.com"
                                   class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                        </div>

                        <div class="flex items-center gap-2 pt-2">
                            <input type="checkbox" id="brandActive" name="is_active" value="1" x-model="brandForm.is_active"
                                   class="w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                            <label for="brandActive" class="text-sm font-medium text-slate-700">Active and available for product selection</label>
                        </div>
                    </div>

                    <div class="bg-slate-50 px-6 py-3 border-t border-slate-100 flex items-center justify-end gap-3">
                        <button type="button" @click="brandModalOpen = false" class="px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-800">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-md shadow-indigo-600/20 transition">
                            Save Brand
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
