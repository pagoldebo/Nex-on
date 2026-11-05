@extends('admin.layout')

@section('content')
<div class="space-y-8" x-data="{
    createModalOpen: false,
    statusModalOpen: false,
    activeReturnForStatus: { id: '', debit_note_number: '', status: 'pending' },
    items: [
        { variant_id: '', quantity: 1, unit_cost: 0, reason: '' }
    ],

    addItem() {
        this.items.push({ variant_id: '', quantity: 1, unit_cost: 0, reason: '' });
    },

    removeItem(index) {
        if (this.items.length > 1) {
            this.items.splice(index, 1);
        }
    },

    openStatus(ret) {
        this.activeReturnForStatus = { id: ret.id, debit_note_number: ret.debit_note_number, status: ret.status };
        this.statusModalOpen = true;
    }
}">
    <!-- Header -->
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Purchase Returns &amp; Debit Notes</h1>
            <p class="mt-1 text-sm text-slate-500">Safely return defective, damaged, or overstocked items to suppliers. Generates legal GST Debit Notes and decrements warehouse stock balances.</p>
        </div>
        <div class="mt-4 sm:mt-0 flex items-center gap-3">
            <button @click="createModalOpen = true" type="button" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-600 text-xs font-bold text-white hover:bg-indigo-700 shadow-sm transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Initiate Purchase Return
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

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Debit Notes</p>
                <p class="text-2xl font-black text-slate-900 mt-1">{{ $totalReturnsCount }}</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Return Value</p>
                <p class="text-2xl font-black text-rose-600 mt-1">₹{{ number_format($totalReturnsValue, 2) }}</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"/></svg>
            </div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Credited / Settled</p>
                <p class="text-2xl font-black text-emerald-600 mt-1">{{ $creditedCount }}</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
    </div>

    <!-- Returns Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-slate-50/50">
            <form method="GET" action="{{ route('admin.purchase-returns.index') }}" class="flex flex-wrap items-center gap-2 flex-1">
                <div class="relative w-64">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search Debit Note # or Supplier..."
                           class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                </div>

                <select name="supplier_id" class="px-3 py-2 text-xs rounded-xl border border-slate-200 bg-white">
                    <option value="">All Suppliers</option>
                    @foreach($suppliers as $s)
                        <option value="{{ $s->id }}" {{ request('supplier_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                </select>

                <select name="status" class="px-3 py-2 text-xs rounded-xl border border-slate-200 bg-white">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="dispatched" {{ request('status') === 'dispatched' ? 'selected' : '' }}>Dispatched</option>
                    <option value="credited" {{ request('status') === 'credited' ? 'selected' : '' }}>Credited</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>

                <button type="submit" class="px-3.5 py-2 bg-slate-200/80 text-slate-700 text-xs font-semibold rounded-xl hover:bg-slate-300">Filter</button>
            </form>
            <div class="text-xs text-slate-500">
                Showing <span class="font-bold text-slate-800">{{ $returns->total() }}</span> Debit Notes
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50/75 border-b border-slate-200/70 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                    <tr>
                        <th class="py-3 px-4">Debit Note #</th>
                        <th class="py-3 px-4">Supplier</th>
                        <th class="py-3 px-4">PO Reference</th>
                        <th class="py-3 px-4">Reason</th>
                        <th class="py-3 px-4 text-center">Items Count</th>
                        <th class="py-3 px-4">Total Amount (Incl. GST)</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($returns as $ret)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-3 px-4 font-mono font-bold text-indigo-600">
                                {{ $ret->debit_note_number }}
                                <div class="text-[10px] text-slate-400 font-sans font-normal">{{ $ret->return_date->format('M d, Y') }}</div>
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-900">{{ $ret->supplier?->name ?? 'Unknown Supplier' }}</div>
                                <div class="text-[10px] text-slate-400 font-mono">{{ $ret->supplier?->gst_number ?? 'No GSTIN' }}</div>
                            </td>
                            <td class="py-3 px-4 font-mono text-[11px]">
                                @if($ret->purchaseOrder)
                                    <a href="{{ route('admin.purchase-orders.show', $ret->purchaseOrder->id) }}" class="text-indigo-600 hover:underline font-bold">
                                        {{ $ret->purchaseOrder->po_number }}
                                    </a>
                                @else
                                    <span class="text-slate-400 italic">Direct Return</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 font-semibold text-slate-800 capitalize">
                                {{ str_replace('_', ' ', $ret->reason) }}
                            </td>
                            <td class="py-3 px-4 text-center font-bold text-slate-800">
                                {{ $ret->items->sum('quantity') }} pcs
                            </td>
                            <td class="py-3 px-4 font-black text-rose-600">
                                ₹{{ number_format((float)$ret->total_amount, 2) }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                @php
                                    $statusColors = [
                                        'pending'    => 'bg-amber-100 text-amber-800',
                                        'approved'   => 'bg-blue-100 text-blue-800',
                                        'dispatched' => 'bg-indigo-100 text-indigo-800',
                                        'credited'   => 'bg-emerald-100 text-emerald-800',
                                        'cancelled'  => 'bg-slate-100 text-slate-600',
                                    ];
                                @endphp
                                <button type="button" @click="openStatus({{ $ret->toJson() }})" class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $statusColors[$ret->status] ?? 'bg-slate-100' }}">
                                    {{ ucfirst($ret->status) }}
                                </button>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('admin.purchase-returns.print', $ret) }}" target="_blank"
                                       class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-bold bg-slate-100 text-slate-700 hover:bg-slate-200 rounded-lg transition-colors" title="Print GST Debit Note Voucher">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                        Debit Note
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400">
                                <svg class="w-10 h-10 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"/></svg>
                                <p class="font-semibold text-slate-600">No purchase returns recorded</p>
                                <p class="text-xs text-slate-400 mt-0.5">Click "Initiate Purchase Return" to generate debit notes for defective or rejected vendor stock.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($returns->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $returns->links() }}
            </div>
        @endif
    </div>

    <!-- Modal: Initiate Purchase Return -->
    <div x-show="createModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="createModalOpen" @click="createModalOpen = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="createModalOpen" class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full border border-slate-200">
                <form action="{{ route('admin.purchase-returns.store') }}" method="POST">
                    @csrf
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-slate-900">Initiate Purchase Return &amp; Issue Debit Note</h3>
                        <p class="text-xs text-slate-500 mt-1">Specify returned items and unit cost. Warehouse inventory will be adjusted automatically.</p>
                        
                        <div class="space-y-4 mt-5">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Supplier *</label>
                                    <select name="supplier_id" required class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-medium">
                                        <option value="">-- Select Supplier --</option>
                                        @foreach($suppliers as $sup)
                                            <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Purchase Order (Optional)</label>
                                    <select name="purchase_order_id" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-medium">
                                        <option value="">-- Direct Supplier Return (No PO) --</option>
                                        @foreach($purchaseOrders as $porder)
                                            <option value="{{ $porder->id }}">{{ $porder->po_number }} ({{ $porder->supplier?->name }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Return Date *</label>
                                    <input type="date" name="return_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-medium">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Primary Reason *</label>
                                    <select name="reason" required class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-medium">
                                        <option value="defective">Defective / Damaged Fabric</option>
                                        <option value="wrong_item">Wrong Item Shipped by Vendor</option>
                                        <option value="quality_rejection">QC Inspection Rejected</option>
                                        <option value="overstocked">Overstocked / Stock Return</option>
                                        <option value="expired">Expired / Shelf Life Breach</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Line items repeater -->
                            <div class="border-t border-slate-100 pt-4">
                                <div class="flex items-center justify-between mb-2">
                                    <p class="text-xs font-bold text-slate-800 uppercase tracking-wider">Returned Item Lines</p>
                                    <button type="button" @click="addItem()" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
                                        + Add Another Item
                                    </button>
                                </div>

                                <div class="space-y-3 max-h-60 overflow-y-auto pr-1">
                                    <template x-for="(item, idx) in items" :key="idx">
                                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/80 flex items-center gap-3">
                                            <div class="flex-1">
                                                <label class="block text-[10px] font-bold text-slate-500 mb-1">Select SKU Variant *</label>
                                                <select :name="'items[' + idx + '][variant_id]'" required class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 bg-white">
                                                    <option value="">-- Choose SKU Variant --</option>
                                                    @foreach($variants as $v)
                                                        <option value="{{ $v->id }}">{{ $v->product?->name }} ({{ $v->sku }} &bull; Size: {{ $v->size }} &bull; In Stock: {{ $v->stock }})</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="w-24">
                                                <label class="block text-[10px] font-bold text-slate-500 mb-1">Return Qty *</label>
                                                <input type="number" :name="'items[' + idx + '][quantity]'" x-model="item.quantity" min="1" required class="w-full px-2 py-1.5 text-xs rounded-lg border border-slate-200 bg-white font-bold text-center">
                                            </div>
                                            <div class="w-28">
                                                <label class="block text-[10px] font-bold text-slate-500 mb-1">Unit Cost (₹) *</label>
                                                <input type="number" step="0.01" :name="'items[' + idx + '][unit_cost]'" x-model="item.unit_cost" min="0" required class="w-full px-2 py-1.5 text-xs rounded-lg border border-slate-200 bg-white font-bold text-right">
                                            </div>
                                            <div class="pt-5">
                                                <button type="button" @click="removeItem(idx)" class="p-1.5 text-slate-400 hover:text-rose-600">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Debit Note Remarks</label>
                                <textarea name="notes" rows="2" placeholder="e.g. Return authorization approved via email by vendor manager..." class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                        <button type="button" @click="createModalOpen = false" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-200 rounded-xl">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-xs font-bold rounded-xl hover:bg-indigo-700 shadow-sm">Issue Debit Note</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal: Update Status -->
    <div x-show="statusModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="statusModalOpen" @click="statusModalOpen = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="statusModalOpen" class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-slate-200">
                <form :action="'{{ url('admin/purchase-returns') }}/' + activeReturnForStatus.id + '/status'" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-slate-900">Update Return Workflow Status</h3>
                        <p class="text-xs text-slate-500 mt-1">Debit Note: <span class="font-mono font-bold text-indigo-600" x-text="activeReturnForStatus.debit_note_number"></span></p>

                        <div class="mt-4">
                            <label class="block text-xs font-bold text-slate-700 mb-1">New Status</label>
                            <select name="status" x-model="activeReturnForStatus.status" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-medium">
                                <option value="pending">Pending Review</option>
                                <option value="approved">Approved</option>
                                <option value="dispatched">Dispatched to Vendor</option>
                                <option value="credited">Credited (Vendor Ledger Adjusted)</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                        <button type="button" @click="statusModalOpen = false" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-200 rounded-xl">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-xs font-bold rounded-xl hover:bg-indigo-700 shadow-sm">Save Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

