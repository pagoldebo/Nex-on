@extends('admin.layout', [
    'title' => 'Coupons & Promo Codes',
    'breadcrumb' => 'Coupons',
])

@section('content')
<div class="max-w-7xl mx-auto space-y-6" x-data="{
    modalOpen: false,
    isEdit: false,
    editAction: '',
    form: {
        id: null,
        code: '',
        type: 'percentage',
        value: 10,
        min_order_amount: 999,
        max_uses: '',
        expires_at: '',
        is_active: true
    },
    openCreate() {
        this.isEdit = false;
        this.form = { id: null, code: '', type: 'percentage', value: 10, min_order_amount: 999, max_uses: '', expires_at: '', is_active: true };
        this.editAction = '{{ route('admin.coupons.store') }}';
        this.modalOpen = true;
    },
    openEdit(c) {
        this.isEdit = true;
        this.form = {
            id: c.id,
            code: c.code,
            type: c.type,
            value: c.value,
            min_order_amount: c.min_order_amount,
            max_uses: c.max_uses ?? '',
            expires_at: c.expires_at ? c.expires_at.substring(0, 10) : '',
            is_active: Boolean(c.is_active)
        };
        this.editAction = '/admin/coupons/' + c.id;
        this.modalOpen = true;
    }
}">

    <!-- Top Header & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Coupons & Promotional Vouchers</h1>
            <p class="text-sm text-slate-500 mt-1">Manage marketing discount codes, percentage vouchers, and minimum cart constraints.</p>
        </div>
        <button @click="openCreate()" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold shadow-md shadow-indigo-600/20 transition flex items-center gap-2 self-start sm:self-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Create Coupon</span>
        </button>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Search & Filter Bar -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-4">
        <form method="GET" action="{{ route('admin.coupons.index') }}" class="flex items-center gap-3">
            <div class="relative flex-1">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="q" value="{{ $search }}" placeholder="Search coupon code..." 
                       class="w-full pl-10 pr-4 py-2 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none">
            </div>
            <button type="submit" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold transition">
                Search
            </button>
            @if($search)
                <a href="{{ route('admin.coupons.index') }}" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-500 text-sm hover:bg-slate-50 transition">
                    Clear
                </a>
            @endif
        </form>
    </div>

    <!-- Coupons Table -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50/75 border-b border-slate-200 text-xs font-bold text-slate-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Coupon Code</th>
                        <th class="px-6 py-4">Discount</th>
                        <th class="px-6 py-4">Min Order</th>
                        <th class="px-6 py-4">Usage</th>
                        <th class="px-6 py-4">Expires</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($coupons as $coupon)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="px-6 py-4">
                                <span class="font-mono font-bold text-indigo-700 bg-indigo-50 border border-indigo-200/60 px-2.5 py-1 rounded-lg text-xs uppercase tracking-wider">
                                    {{ $coupon->code }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-semibold text-slate-900">
                                @if($coupon->type === 'percentage')
                                    <span class="text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded text-xs font-bold border border-emerald-200/60">
                                        {{ (int)$coupon->value }}% OFF
                                    </span>
                                @else
                                    <span class="text-blue-700 bg-blue-50 px-2 py-0.5 rounded text-xs font-bold border border-blue-200/60">
                                        ₹{{ number_format($coupon->value, 0) }} FLAT
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-slate-600 font-mono text-xs">
                                ₹{{ number_format($coupon->min_order_amount, 0) }}
                            </td>
                            <td class="px-6 py-4 text-xs font-medium text-slate-700">
                                <span class="font-bold text-slate-900">{{ $coupon->used_count }}</span>
                                <span class="text-slate-400">/ {{ $coupon->max_uses ?? '∞' }}</span>
                            </td>
                            <td class="px-6 py-4 text-xs">
                                @if($coupon->expires_at)
                                    <span class="{{ $coupon->expires_at->isPast() ? 'text-rose-600 font-bold' : 'text-slate-600' }}">
                                        {{ $coupon->expires_at->format('M d, Y') }}
                                        @if($coupon->expires_at->isPast())
                                            <span class="text-[10px] uppercase font-bold text-rose-500 bg-rose-50 px-1.5 py-0.5 rounded ml-1">Expired</span>
                                        @endif
                                    </span>
                                @else
                                    <span class="text-slate-400">Never</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <form method="POST" action="{{ route('admin.coupons.toggle', $coupon) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $coupon->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200/60' : 'bg-slate-100 text-slate-500' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $coupon->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                        {{ $coupon->is_active ? 'Active' : 'Disabled' }}
                                    </button>
                                </form>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button @click='openEdit(@json($coupon))' class="p-1.5 rounded-lg text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 transition" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    </button>
                                    <form method="POST" action="{{ route('admin.coupons.destroy', $coupon) }}" onsubmit="return confirm('Delete coupon {{ $coupon->code }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition" title="Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                No coupons found. Click "Create Coupon" to add a promotion.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($coupons->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $coupons->links() }}
            </div>
        @endif
    </div>

    <!-- Create / Edit Modal -->
    <div x-show="modalOpen" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="min-h-screen px-4 flex items-center justify-center">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" @click="modalOpen = false"></div>

            <div class="bg-white rounded-2xl max-w-lg w-full p-6 sm:p-8 shadow-2xl relative z-10 space-y-6">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <h3 class="text-lg font-bold text-slate-900" x-text="isEdit ? 'Edit Coupon' : 'Create New Coupon'"></h3>
                    <button @click="modalOpen = false" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form :action="editAction" method="POST" class="space-y-4">
                    @csrf
                    <template x-if="isEdit">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Coupon Code *</label>
                        <input type="text" name="code" x-model="form.code" placeholder="e.g. FESTIVE20" required 
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-mono uppercase focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Discount Type</label>
                            <select name="type" x-model="form.type" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 outline-none">
                                <option value="percentage">Percentage (%)</option>
                                <option value="fixed">Flat Amount (₹)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Discount Value *</label>
                            <input type="number" step="0.01" name="value" x-model="form.value" required
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 outline-none">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Min Order (₹)</label>
                            <input type="number" name="min_order_amount" x-model="form.min_order_amount" 
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Max Uses (optional)</label>
                            <input type="number" name="max_uses" x-model="form.max_uses" placeholder="Unlimited"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Expiration Date</label>
                        <input type="date" name="expires_at" x-model="form.expires_at"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 outline-none">
                    </div>

                    <div class="pt-2">
                        <label class="flex items-center gap-2.5 text-sm font-semibold text-slate-700 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" x-model="form.is_active" class="w-4 h-4 text-indigo-600 rounded border-slate-300">
                            <span>Enable this coupon immediately</span>
                        </label>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <button type="button" @click="modalOpen = false" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-sm font-semibold hover:bg-slate-50 transition">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold shadow-md shadow-indigo-600/20 transition">
                            <span x-text="isEdit ? 'Save Changes' : 'Create Coupon'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
