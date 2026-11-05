@extends('admin.layout')

@section('content')
<div class="space-y-8" x-data="{
    createModalOpen: false,
    editModalOpen: false,
    assignModalOpen: false,
    activeGroup: { id: '', name: '', code: '', description: '', discount_type: 'percentage', discount_value: 0, min_spend: 0, is_active: true },
    targetGroupForAssign: { id: '', name: '' },

    openEdit(group) {
        this.activeGroup = {
            id: group.id,
            name: group.name,
            code: group.code,
            description: group.description || '',
            discount_type: group.discount_type,
            discount_value: group.discount_value,
            min_spend: group.min_spend,
            is_active: !!group.is_active
        };
        this.editModalOpen = true;
    },

    openAssign(group) {
        this.targetGroupForAssign = { id: group.id, name: group.name };
        this.assignModalOpen = true;
    }
}">
    <!-- Header -->
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Customer Groups &amp; Wholesale Tiers</h1>
            <p class="mt-1 text-sm text-slate-500">Segment shoppers into VIP tiers, B2B wholesale buyers, and exclusive loyalty tiers with automatic checkout discounts and minimum spend gates.</p>
        </div>
        <div class="mt-4 sm:mt-0 flex items-center gap-3">
            <button @click="createModalOpen = true" type="button" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-600 text-xs font-bold text-white hover:bg-indigo-700 shadow-sm transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Create Customer Group
            </button>
        </div>
    </div>

    <!-- Flash Messages -->
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
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Groups</p>
                <p class="text-2xl font-black text-slate-900 mt-1">{{ $groups->count() }}</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Active Tiers</p>
                <p class="text-2xl font-black text-emerald-600 mt-1">{{ $groups->where('is_active', true)->count() }}</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Enrolled Members</p>
                <p class="text-2xl font-black text-slate-900 mt-1">{{ $groups->sum('users_count') }}</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
        </div>
    </div>

    <!-- Groups Directory Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-slate-50/50">
            <form method="GET" action="{{ route('admin.customer-groups.index') }}" class="flex items-center gap-2 flex-1 max-w-sm">
                <div class="relative w-full">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search group name or code..."
                           class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                </div>
                <button type="submit" class="px-3 py-2 bg-slate-200/70 text-slate-700 text-xs font-semibold rounded-xl hover:bg-slate-300">Filter</button>
            </form>
            <div class="text-xs text-slate-500">
                Configured Wholesale &amp; Tiered Rates
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50/75 border-b border-slate-200/70 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                    <tr>
                        <th class="py-3 px-4">Group Name</th>
                        <th class="py-3 px-4">Tier Code</th>
                        <th class="py-3 px-4">Discount Rate</th>
                        <th class="py-3 px-4">Min. Spend Gate</th>
                        <th class="py-3 px-4 text-center">Active Members</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($groups as $group)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-900">{{ $group->name }}</div>
                                @if($group->description)
                                    <div class="text-[11px] text-slate-400 mt-0.5">{{ $group->description }}</div>
                                @endif
                            </td>
                            <td class="py-3 px-4 font-mono text-[11px] text-indigo-600 font-bold">
                                {{ $group->code }}
                            </td>
                            <td class="py-3 px-4">
                                @if($group->discount_type === 'percentage')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800">
                                        {{ (float) $group->discount_value }}% OFF
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-100 text-blue-800">
                                        ₹{{ number_format((float) $group->discount_value, 2) }} FLAT
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 font-semibold text-slate-800">
                                @if($group->min_spend > 0)
                                    ₹{{ number_format((float) $group->min_spend, 2) }}
                                @else
                                    <span class="text-slate-400">No Minimum</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="inline-flex items-center gap-1 font-bold text-slate-800 bg-slate-100 px-2 py-0.5 rounded-full text-[11px]">
                                    {{ $group->users_count }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($group->is_active)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Active</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-500">Disabled</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right">
                                <div class="inline-flex items-center gap-2">
                                    <button type="button" @click="openAssign({{ $group->toJson() }})" class="px-2.5 py-1 text-[11px] font-bold bg-indigo-50 text-indigo-600 hover:bg-indigo-100 rounded-lg transition-colors">
                                        Members
                                    </button>
                                    <button type="button" @click="openEdit({{ $group->toJson() }})" class="p-1.5 text-slate-500 hover:text-indigo-600 hover:bg-slate-100 rounded-lg transition-colors" title="Edit Group">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    </button>
                                    <form action="{{ route('admin.customer-groups.destroy', $group) }}" method="POST" onsubmit="return confirm('Delete customer group {{ $group->name }}?');" class="inline">
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
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <svg class="w-10 h-10 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                <p class="font-semibold text-slate-600">No customer groups configured</p>
                                <p class="text-xs text-slate-400 mt-0.5">Click "Create Customer Group" to set up VIP or Wholesale buyer tiers.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal: Create Customer Group -->
    <div x-show="createModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="createModalOpen" @click="createModalOpen = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="createModalOpen" class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200">
                <form action="{{ route('admin.customer-groups.store') }}" method="POST">
                    @csrf
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-slate-900">Create Customer Group / Wholesale Tier</h3>
                        <div class="space-y-4 mt-5">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Group Name *</label>
                                <input type="text" name="name" required placeholder="e.g. VIP Gold Tier, B2B Wholesalers" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-medium">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Code (Optional)</label>
                                <input type="text" name="code" placeholder="e.g. vip-gold, wholesale-b2b" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-medium">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Description</label>
                                <textarea name="description" rows="2" placeholder="Brief note explaining membership perks or criteria..." class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"></textarea>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Discount Type *</label>
                                    <select name="discount_type" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-medium">
                                        <option value="percentage">Percentage (%) OFF</option>
                                        <option value="fixed">Fixed Amount (₹) OFF</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Discount Value *</label>
                                    <input type="number" step="0.01" name="discount_value" required placeholder="e.g. 15 or 500" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-medium">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Minimum Order Spend Gate (₹)</label>
                                <input type="number" step="0.01" name="min_spend" value="0" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-medium">
                                <p class="text-[10px] text-slate-400 mt-1">Discount will only apply when checkout subtotal exceeds this value. Set 0 for no threshold.</p>
                            </div>

                            <div class="pt-2">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="is_active" value="1" checked class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300">
                                    <span class="text-xs font-semibold text-slate-700">Group Active &amp; Ready</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                        <button type="button" @click="createModalOpen = false" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-200 rounded-xl">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-xs font-bold rounded-xl hover:bg-indigo-700 shadow-sm">Save Group</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal: Edit Customer Group -->
    <div x-show="editModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="editModalOpen" @click="editModalOpen = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="editModalOpen" class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200">
                <form :action="'{{ url('admin/customer-groups') }}/' + activeGroup.id" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-slate-900">Edit Customer Group</h3>
                        <div class="space-y-4 mt-5">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Group Name *</label>
                                <input type="text" name="name" x-model="activeGroup.name" required class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-medium">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Code *</label>
                                <input type="text" name="code" x-model="activeGroup.code" required class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-medium">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Description</label>
                                <textarea name="description" x-model="activeGroup.description" rows="2" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"></textarea>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Discount Type *</label>
                                    <select name="discount_type" x-model="activeGroup.discount_type" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-medium">
                                        <option value="percentage">Percentage (%) OFF</option>
                                        <option value="fixed">Fixed Amount (₹) OFF</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Discount Value *</label>
                                    <input type="number" step="0.01" name="discount_value" x-model="activeGroup.discount_value" required class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-medium">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Minimum Order Spend Gate (₹)</label>
                                <input type="number" step="0.01" name="min_spend" x-model="activeGroup.min_spend" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-medium">
                            </div>

                            <div class="pt-2">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="is_active" value="1" x-model="activeGroup.is_active" class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300">
                                    <span class="text-xs font-semibold text-slate-700">Group Active &amp; Ready</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                        <button type="button" @click="editModalOpen = false" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-200 rounded-xl">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-xs font-bold rounded-xl hover:bg-indigo-700 shadow-sm">Update Group</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal: Assign Members -->
    <div x-show="assignModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="assignModalOpen" @click="assignModalOpen = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="assignModalOpen" class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200">
                <form :action="'{{ url('admin/customer-groups') }}/' + targetGroupForAssign.id + '/assign'" method="POST">
                    @csrf
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-slate-900">Assign Customers to <span class="text-indigo-600" x-text="targetGroupForAssign.name"></span></h3>
                        <p class="text-xs text-slate-500 mt-1">Select registered shoppers to enroll into this wholesale or VIP tier.</p>
                        
                        <div class="mt-4 border border-slate-200 rounded-xl p-3 max-h-64 overflow-y-auto space-y-2">
                            @forelse($customers as $cust)
                                <label class="flex items-center justify-between p-2 rounded-lg hover:bg-slate-50 cursor-pointer">
                                    <div class="flex items-center gap-2.5">
                                        <input type="checkbox" name="user_ids[]" value="{{ $cust->id }}" class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300">
                                        <div>
                                            <div class="text-xs font-bold text-slate-800">{{ $cust->name }}</div>
                                            <div class="text-[10px] text-slate-400">{{ $cust->email }}</div>
                                        </div>
                                    </div>
                                    <span class="text-[10px] bg-slate-100 text-slate-500 px-2 py-0.5 rounded-full font-mono">ID #{{ $cust->id }}</span>
                                </label>
                            @empty
                                <p class="text-xs text-slate-400 text-center py-4">No customers registered yet.</p>
                            @endforelse
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                        <button type="button" @click="assignModalOpen = false" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-200 rounded-xl">Close</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-xs font-bold rounded-xl hover:bg-indigo-700 shadow-sm">Assign Selected</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

