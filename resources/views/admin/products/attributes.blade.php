{{--
    ======================================================================
    ADMIN PRODUCTS: ATTRIBUTE & VARIANT MATRIX STUDIO
    ======================================================================
    File: resources/views/admin/products/attributes.blade.php
    Controller: App\Http\Controllers\Admin\ProductAttributeController@index
    Route Context: GET /admin/products/attributes
    Responsibility: Global product attributes studio (Colors, Sizes, Materials, Fits)
    Features:
      - Attribute definition (Color swatches with hex codes, text pills, dropdowns)
      - Attribute value options creation and sorting
      - Product variant generator mapping attributes into purchasable SKUs
    Security: Guarded by 'products.manage' permission
--}}
@extends('admin.layout')

@section('content')
<div class="space-y-8" x-data="{
    groupModalOpen: false,
    editGroupModalOpen: false,
    activeGroup: { id: '', name: '', sort_order: 0 },

    attributeModalOpen: false,
    editAttributeModalOpen: false,
    activeAttribute: { id: '', group_id: '', name: '', code: '', type: 'text', options: '', is_filterable: true, is_comparable: true },

    assignModalOpen: false,

    openEditGroup(group) {
        this.activeGroup = { id: group.id, name: group.name, sort_order: group.sort_order };
        this.editGroupModalOpen = true;
    },
    openEditAttribute(attr) {
        this.activeAttribute = {
            id: attr.id,
            group_id: attr.attribute_group_id || '',
            name: attr.name,
            code: attr.code,
            type: attr.type,
            options: Array.isArray(attr.options) ? attr.options.join(', ') : (attr.options || ''),
            is_filterable: !!attr.is_filterable,
            is_comparable: !!attr.is_comparable
        };
        this.editAttributeModalOpen = true;
    }
}">
    <!-- Header -->
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Product Attributes &amp; Specifications Engine</h1>
            <p class="mt-1 text-sm text-slate-500">Configure standardized product attributes (Fabric, Pattern, Fit, Collar, Weave) for faceted filters and product detail specifications.</p>
        </div>
        <div class="mt-4 sm:mt-0 flex items-center gap-3">
            <button @click="groupModalOpen = true" type="button" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border border-slate-200 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-50 shadow-xs">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                New Group
            </button>
            <button @click="attributeModalOpen = true" type="button" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-600 text-xs font-bold text-white hover:bg-indigo-700 shadow-sm transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Create Attribute
            </button>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm">
            <ul class="list-disc pl-5 space-y-1">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Attribute Groups</p>
                <p class="text-2xl font-black text-slate-900 mt-1">{{ $groups->count() }}</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            </div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Specifications</p>
                <p class="text-2xl font-black text-slate-900 mt-1">{{ $attributes->count() }}</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Faceted Filterable</p>
                <p class="text-2xl font-black text-slate-900 mt-1">{{ $attributes->where('is_filterable', true)->count() }}</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
            </div>
        </div>
    </div>

    <!-- Main Grid: Groups & Attributes -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Sidebar: Attribute Groups -->
        <div class="lg:col-span-1 space-y-4">
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <h2 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Specification Groups</h2>
                    <span class="text-xs bg-slate-200/60 text-slate-600 px-2 py-0.5 rounded-full font-bold">{{ $groups->count() }}</span>
                </div>
                <div class="p-2 space-y-1">
                    <a href="{{ route('admin.attributes.index') }}" 
                       class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ !request('group_id') ? 'bg-indigo-50 text-indigo-600 font-bold' : 'text-slate-600 hover:bg-slate-50' }}">
                        <span>All Groups</span>
                        <span class="text-[10px] bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full">{{ $attributes->count() }}</span>
                    </a>
                    @foreach($groups as $group)
                        <div class="group flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ request('group_id') == $group->id ? 'bg-indigo-50 text-indigo-600 font-bold' : 'text-slate-600 hover:bg-slate-50' }}">
                            <a href="{{ route('admin.attributes.index', ['group_id' => $group->id]) }}" class="flex-1 truncate">
                                {{ $group->name }}
                            </a>
                            <div class="flex items-center gap-1.5 opacity-80 group-hover:opacity-100">
                                <span class="text-[10px] bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded-full">{{ $group->attributes_count }}</span>
                                <button type="button" @click="openEditGroup({{ $group->toJson() }})" class="p-1 text-slate-400 hover:text-indigo-600" title="Edit Group">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                </button>
                                <form action="{{ route('admin.attributes.groups.destroy', $group) }}" method="POST" onsubmit="return confirm('Delete this group? Attributes will become unassigned.');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1 text-slate-400 hover:text-rose-600" title="Delete Group">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Fast Product Assign Card -->
            <div class="bg-gradient-to-br from-indigo-50 to-slate-50 p-4 rounded-2xl border border-indigo-100/80 shadow-xs">
                <div class="flex items-center gap-2 mb-2">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    <h3 class="text-xs font-bold text-indigo-950">Assign Specifications</h3>
                </div>
                <p class="text-[11px] text-slate-600 mb-3 leading-relaxed">Map attributes directly to any catalog product without leaving this console.</p>
                <button type="button" @click="assignModalOpen = true" class="w-full text-center px-3 py-2 rounded-xl bg-indigo-600 text-white font-bold text-xs hover:bg-indigo-700 shadow-xs transition-all">
                    Assign to Product
                </button>
            </div>
        </div>

        <!-- Main Attributes Table -->
        <div class="lg:col-span-3">
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <!-- Search & Filters -->
                <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <form method="GET" action="{{ route('admin.attributes.index') }}" class="flex items-center gap-2 flex-1 max-w-sm">
                        @if(request('group_id'))
                            <input type="hidden" name="group_id" value="{{ request('group_id') }}">
                        @endif
                        <div class="relative w-full">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </span>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search attribute name or code..."
                                   class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                        </div>
                        <button type="submit" class="px-3 py-2 bg-slate-100 text-slate-700 text-xs font-semibold rounded-xl hover:bg-slate-200">Filter</button>
                    </form>
                    <div class="text-xs text-slate-500">
                        Showing <span class="font-bold text-slate-800">{{ $attributes->count() }}</span> attribute specifications
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600">
                        <thead class="bg-slate-50/75 border-b border-slate-200/70 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                            <tr>
                                <th class="py-3 px-4">Specification Name</th>
                                <th class="py-3 px-4">System Code</th>
                                <th class="py-3 px-4">Group</th>
                                <th class="py-3 px-4">Type</th>
                                <th class="py-3 px-4">Options</th>
                                <th class="py-3 px-4">Faceted / Compare</th>
                                <th class="py-3 px-4 text-center">Products</th>
                                <th class="py-3 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($attributes as $attr)
                                <tr class="hover:bg-slate-50/60 transition-colors">
                                    <td class="py-3 px-4 font-bold text-slate-900">
                                        {{ $attr->name }}
                                    </td>
                                    <td class="py-3 px-4 font-mono text-[11px] text-slate-500">
                                        {{ $attr->code }}
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-700">
                                            {{ $attr->group?->name ?? 'Unassigned' }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4">
                                        @php
                                            $typeBadges = [
                                                'text' => 'bg-blue-50 text-blue-700 border-blue-200',
                                                'select' => 'bg-purple-50 text-purple-700 border-purple-200',
                                                'multiselect' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                                'color' => 'bg-pink-50 text-pink-700 border-pink-200',
                                                'boolean' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            ];
                                        @endphp
                                        <span class="inline-block px-2 py-0.5 rounded-md text-[10px] font-bold border {{ $typeBadges[$attr->type] ?? 'bg-slate-50 text-slate-700' }}">
                                            {{ strtoupper($attr->type) }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 max-w-xs truncate text-slate-500 text-[11px]">
                                        @if($attr->options && is_array($attr->options))
                                            <span title="{{ implode(', ', $attr->options) }}">
                                                {{ count($attr->options) }} options ({{ implode(', ', array_slice($attr->options, 0, 3)) }}{{ count($attr->options) > 3 ? '...' : '' }})
                                            </span>
                                        @else
                                            <span class="text-slate-400 italic">—</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="flex items-center gap-1.5">
                                            @if($attr->is_filterable)
                                                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-100 text-emerald-800" title="Visible in Storefront Filter Sidebar">Filter</span>
                                            @endif
                                            @if($attr->is_comparable)
                                                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-indigo-100 text-indigo-800" title="Visible in Product Compare Matrix">Compare</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-3 px-4 text-center font-bold text-slate-800">
                                        {{ $attr->values_count }}
                                    </td>
                                    <td class="py-3 px-4 text-right">
                                        <div class="inline-flex items-center gap-2">
                                            <button type="button" @click="openEditAttribute({{ $attr->toJson() }})" class="p-1.5 text-slate-500 hover:text-indigo-600 hover:bg-slate-100 rounded-lg transition-colors" title="Edit Attribute">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                            </button>
                                            <form action="{{ route('admin.attributes.destroy', $attr) }}" method="POST" onsubmit="return confirm('Delete attribute {{ $attr->name }}? All assigned values on products will be removed.');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Delete">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-12 text-center text-slate-400">
                                        <svg class="w-10 h-10 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                        <p class="font-semibold text-slate-600">No specifications found</p>
                                        <p class="text-xs text-slate-400 mt-0.5">Click "Create Attribute" to set up your first specification.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Create Group -->
    <div x-show="groupModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="groupModalOpen" @click="groupModalOpen = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="groupModalOpen" class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-slate-200">
                <form action="{{ route('admin.attributes.groups.store') }}" method="POST">
                    @csrf
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-slate-900">Add Specification Group</h3>
                        <p class="text-xs text-slate-500 mt-1">Group related specs (e.g. Apparel Specifications, Material &amp; Care, Technical Details).</p>
                        <div class="space-y-4 mt-5">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Group Name *</label>
                                <input type="text" name="name" required placeholder="e.g. Apparel Specifications" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Sort Order</label>
                                <input type="number" name="sort_order" value="0" min="0" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                            </div>
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                        <button type="button" @click="groupModalOpen = false" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-200 rounded-xl">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-xs font-bold rounded-xl hover:bg-indigo-700 shadow-sm">Save Group</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal: Edit Group -->
    <div x-show="editGroupModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="editGroupModalOpen" @click="editGroupModalOpen = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="editGroupModalOpen" class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-slate-200">
                <form :action="'{{ url('admin/attributes/groups') }}/' + activeGroup.id" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-slate-900">Edit Specification Group</h3>
                        <div class="space-y-4 mt-5">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Group Name *</label>
                                <input type="text" name="name" x-model="activeGroup.name" required class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Sort Order</label>
                                <input type="number" name="sort_order" x-model="activeGroup.sort_order" min="0" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                            </div>
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                        <button type="button" @click="editGroupModalOpen = false" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-200 rounded-xl">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-xs font-bold rounded-xl hover:bg-indigo-700 shadow-sm">Update Group</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal: Create Attribute -->
    <div x-show="attributeModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="attributeModalOpen" @click="attributeModalOpen = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="attributeModalOpen" class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200">
                <form action="{{ route('admin.attributes.store') }}" method="POST">
                    @csrf
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-slate-900">Create Attribute Specification</h3>
                        <p class="text-xs text-slate-500 mt-1">Define an attribute and configure its data type and storefront behavior.</p>
                        
                        <div class="space-y-4 mt-5">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Specification Group</label>
                                <select name="attribute_group_id" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                                    <option value="">-- No Group (Unassigned) --</option>
                                    @foreach($groups as $grp)
                                        <option value="{{ $grp->id }}">{{ $grp->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Attribute Name *</label>
                                    <input type="text" name="name" required placeholder="e.g. Fabric Material" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Code (Optional)</label>
                                    <input type="text" name="code" placeholder="e.g. fabric_material" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Data Type *</label>
                                <select name="type" required class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                                    <option value="text">Text (e.g. 100% Organic Silk)</option>
                                    <option value="select">Dropdown Single Select</option>
                                    <option value="multiselect">Multi-Select Tags</option>
                                    <option value="color">Color Swatch Code</option>
                                    <option value="boolean">Boolean (Yes / No toggle)</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Options (For Select / Multi-Select)</label>
                                <input type="text" name="options" placeholder="e.g. Silk, Cotton, Satin, Chiffon, Linen" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                                <p class="text-[10px] text-slate-400 mt-1">Separate available values with commas.</p>
                            </div>

                            <div class="flex items-center gap-6 pt-2">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="is_filterable" value="1" checked class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300">
                                    <span class="text-xs font-semibold text-slate-700">Faceted Filterable</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="is_comparable" value="1" checked class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300">
                                    <span class="text-xs font-semibold text-slate-700">Product Comparison</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                        <button type="button" @click="attributeModalOpen = false" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-200 rounded-xl">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-xs font-bold rounded-xl hover:bg-indigo-700 shadow-sm">Create Attribute</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal: Edit Attribute -->
    <div x-show="editAttributeModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="editAttributeModalOpen" @click="editAttributeModalOpen = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="editAttributeModalOpen" class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200">
                <form :action="'{{ url('admin/attributes') }}/' + activeAttribute.id" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-slate-900">Edit Attribute Specification</h3>
                        <div class="space-y-4 mt-5">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Specification Group</label>
                                <select name="attribute_group_id" x-model="activeAttribute.group_id" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                                    <option value="">-- No Group (Unassigned) --</option>
                                    @foreach($groups as $grp)
                                        <option value="{{ $grp->id }}">{{ $grp->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Attribute Name *</label>
                                    <input type="text" name="name" x-model="activeAttribute.name" required class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Code *</label>
                                    <input type="text" name="code" x-model="activeAttribute.code" required class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Data Type *</label>
                                <select name="type" x-model="activeAttribute.type" required class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                                    <option value="text">Text (e.g. 100% Organic Silk)</option>
                                    <option value="select">Dropdown Single Select</option>
                                    <option value="multiselect">Multi-Select Tags</option>
                                    <option value="color">Color Swatch Code</option>
                                    <option value="boolean">Boolean (Yes / No toggle)</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Options (For Select / Multi-Select)</label>
                                <input type="text" name="options" x-model="activeAttribute.options" placeholder="e.g. Silk, Cotton, Satin" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                            </div>

                            <div class="flex items-center gap-6 pt-2">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="is_filterable" value="1" x-model="activeAttribute.is_filterable" class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300">
                                    <span class="text-xs font-semibold text-slate-700">Faceted Filterable</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="is_comparable" value="1" x-model="activeAttribute.is_comparable" class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300">
                                    <span class="text-xs font-semibold text-slate-700">Product Comparison</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                        <button type="button" @click="editAttributeModalOpen = false" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-200 rounded-xl">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-xs font-bold rounded-xl hover:bg-indigo-700 shadow-sm">Update Attribute</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal: Assign Specifications to Product -->
    <div x-show="assignModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" x-data="{ selectedProductId: '{{ $products->first()?->id ?? '' }}' }">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="assignModalOpen" @click="assignModalOpen = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="assignModalOpen" class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-slate-200">
                <form :action="'{{ url('admin/products') }}/' + selectedProductId + '/attributes'" method="POST">
                    @csrf
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-slate-900">Map Specifications to Product</h3>
                        <p class="text-xs text-slate-500 mt-1">Select a catalog product and enter values for all available attributes.</p>
                        
                        <div class="mt-4">
                            <label class="block text-xs font-bold text-slate-700 mb-1">Target Product *</label>
                            <select x-model="selectedProductId" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-medium">
                                @foreach($products as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->sku }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mt-6 border-t border-slate-100 pt-4 space-y-4 max-h-80 overflow-y-auto pr-2">
                            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Fill Specifications:</p>
                            @forelse($attributes as $attribute)
                                <div class="bg-slate-50 p-3 rounded-xl border border-slate-200/70">
                                    <div class="flex items-center justify-between mb-1.5">
                                        <label class="text-xs font-bold text-slate-800">{{ $attribute->name }}</label>
                                        <span class="text-[10px] font-mono text-slate-400">{{ $attribute->code }}</span>
                                    </div>
                                    @if($attribute->type === 'select' && $attribute->options)
                                        <select name="attributes[{{ $attribute->id }}]" class="w-full px-3 py-1.5 text-xs rounded-lg border border-slate-200 bg-white">
                                            <option value="">-- None / Default --</option>
                                            @foreach($attribute->options as $opt)
                                                <option value="{{ $opt }}">{{ $opt }}</option>
                                            @endforeach
                                        </select>
                                    @elseif($attribute->type === 'boolean')
                                        <select name="attributes[{{ $attribute->id }}]" class="w-full px-3 py-1.5 text-xs rounded-lg border border-slate-200 bg-white">
                                            <option value="">-- Not Specified --</option>
                                            <option value="1">Yes</option>
                                            <option value="0">No</option>
                                        </select>
                                    @else
                                        <input type="text" name="attributes[{{ $attribute->id }}]" placeholder="Enter {{ strtolower($attribute->name) }}..." class="w-full px-3 py-1.5 text-xs rounded-lg border border-slate-200 bg-white">
                                    @endif
                                </div>
                            @empty
                                <p class="text-xs text-slate-400 italic">No attributes created yet.</p>
                            @endforelse
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                        <button type="button" @click="assignModalOpen = false" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-200 rounded-xl">Close</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-xs font-bold rounded-xl hover:bg-indigo-700 shadow-sm">Save Specifications</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

