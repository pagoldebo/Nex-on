@extends('admin.layout', [
    'title' => 'Draft New Purchase Order',
    'breadcrumbs' => [
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Purchase Orders', 'url' => route('admin.purchase-orders.index')],
        ['label' => 'Draft New PO'],
    ]
])

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="poBuilder()">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Draft Supplier Purchase Order</h1>
            <p class="text-xs text-slate-500 mt-1">Specify items, cost rates &amp; order quantities for vendor replenishment</p>
        </div>
        <a href="{{ route('admin.purchase-orders.index') }}" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition">
            Back to POs
        </a>
    </div>

    <!-- PO Form -->
    <form method="POST" action="{{ route('admin.purchase-orders.store') }}" class="space-y-6">
        @csrf

        <!-- Top Vendor & Meta Card -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs space-y-4">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">1. Vendor &amp; Schedule Details</h2>
            
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">PO Reference Number *</label>
                    <input type="text" name="po_number" value="{{ $poNumber }}" required
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 font-mono font-bold text-slate-900 focus:outline-none focus:border-indigo-500">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Select Supplier *</label>
                    <select name="supplier_id" required class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white focus:outline-none focus:border-indigo-500">
                        <option value="">-- Choose Supplier --</option>
                        @foreach($suppliers as $s)
                            <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->code }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Expected Delivery Date</label>
                    <input type="date" name="expected_delivery_date" value="{{ now()->addDays(7)->format('Y-m-d') }}"
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white font-mono focus:outline-none focus:border-indigo-500">
                </div>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1 text-xs">Procurement Notes / Instructions</label>
                <input type="text" name="notes" placeholder="e.g. Standard export packaging required, invoice with GST"
                       class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-indigo-500">
            </div>
        </div>

        <!-- Low Stock Quick Add Bar -->
        @if($lowStockVariants->isNotEmpty())
            <div class="bg-amber-50/70 border border-amber-200/80 rounded-2xl p-4">
                <span class="text-xs font-bold text-amber-900 block mb-2">⚡ 1-Click Low-Stock SKU Suggestions:</span>
                <div class="flex items-center gap-2 flex-wrap">
                    @foreach($lowStockVariants as $lv)
                        <button type="button" 
                                @click="addItem({{ $lv->id }}, '{{ addslashes($lv->product->name . ' - ' . $lv->size . ' (' . ($lv->color?->name ?? 'Std') . ')') }}', '{{ $lv->sku }}', {{ $lv->reorder_qty ?? 20 }}, {{ (int) ($lv->cost_price ?? round($lv->price * 0.45)) }})"
                                class="px-2.5 py-1 bg-white hover:bg-amber-100 text-amber-900 border border-amber-200 rounded-lg text-xs font-mono transition flex items-center gap-1.5 shadow-2xs">
                            <span>+ {{ $lv->sku }}</span>
                            <span class="text-[10px] bg-rose-500 text-white px-1.5 py-0.2 rounded-full font-bold">{{ $lv->stock }} left</span>
                        </button>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Line Items Card -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs space-y-4">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">2. Order Line Items (<span x-text="items.length"></span>)</h2>
                
                <!-- Variant Selector Dropdown -->
                <div class="flex items-center gap-2">
                    <select x-ref="variantPicker" class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs bg-white focus:outline-none focus:border-indigo-500">
                        <option value="">-- Add Variant to PO --</option>
                        @foreach($allVariants as $av)
                            <option value="{{ $av->id }}" 
                                    data-name="{{ $av->product->name }} - {{ $av->size }} ({{ $av->color?->name ?? 'Std' }})"
                                    data-sku="{{ $av->sku }}"
                                    data-qty="{{ $av->reorder_qty ?? 20 }}"
                                    data-cost="{{ (int) ($av->cost_price ?? round($av->price * 0.45)) }}">
                                {{ $av->sku }} &bull; {{ $av->product->name }} ({{ $av->size }}) &bull; Current Stock: {{ $av->stock }}
                            </option>
                        @endforeach
                    </select>
                    <button type="button" @click="addFromPicker()" class="px-3 py-1.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl transition">
                        + Add Item
                    </button>
                </div>
            </div>

            <!-- Items Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full text-xs text-left">
                    <thead>
                        <tr class="text-slate-400 uppercase font-mono text-[10px] border-b border-slate-100">
                            <th class="py-2">Item Description / SKU</th>
                            <th class="py-2 w-28 text-center">Order Qty</th>
                            <th class="py-2 w-32 text-right">Unit Cost (₹)</th>
                            <th class="py-2 w-32 text-right">Line Total (₹)</th>
                            <th class="py-2 w-12 text-center"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <template x-for="(item, idx) in items" :key="idx">
                            <tr>
                                <td class="py-3">
                                    <input type="hidden" :name="'items[' + idx + '][variant_id]'" :value="item.variant_id">
                                    <div class="font-bold text-slate-900" x-text="item.name"></div>
                                    <div class="text-[10px] font-mono text-slate-400" x-text="item.sku"></div>
                                </td>
                                <td class="py-3 text-center">
                                    <input type="number" 
                                           :name="'items[' + idx + '][ordered_qty]'" 
                                           x-model.number="item.ordered_qty" 
                                           min="1" required
                                           class="w-20 px-2 py-1 rounded-lg border border-slate-200 text-center font-mono font-bold text-slate-900 focus:outline-none focus:border-indigo-500">
                                </td>
                                <td class="py-3 text-right">
                                    <input type="number" 
                                           :name="'items[' + idx + '][unit_cost]'" 
                                           x-model.number="item.unit_cost" 
                                           min="0" required
                                           class="w-24 px-2 py-1 rounded-lg border border-slate-200 text-right font-mono font-bold text-slate-900 focus:outline-none focus:border-indigo-500">
                                </td>
                                <td class="py-3 text-right font-mono font-bold text-slate-900" x-text="'₹' + (item.ordered_qty * item.unit_cost).toLocaleString()"></td>
                                <td class="py-3 text-center">
                                    <button type="button" @click="removeItem(idx)" class="text-slate-400 hover:text-rose-500 p-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </td>
                            </tr>
                        </template>

                        <tr x-show="items.length === 0">
                            <td colspan="5" class="py-8 text-center text-slate-400">
                                No items added to this PO yet. Pick from low-stock chips above or use the selector.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Grand Total Bar -->
            <div class="flex items-center justify-between pt-3 border-t border-slate-100 text-sm font-bold">
                <span class="text-slate-600">Total Purchase Order Commitment:</span>
                <span class="font-mono text-lg font-black text-indigo-600" x-text="'₹' + totalAmount.toLocaleString()"></span>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.purchase-orders.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition">
                Cancel
            </a>
            <button type="submit" 
                    :disabled="items.length === 0"
                    class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-40 text-white text-xs font-bold rounded-xl shadow-md shadow-indigo-600/30 transition">
                Create &amp; Draft Purchase Order
            </button>
        </div>
    </form>
</div>

<script>
    function poBuilder() {
        return {
            items: [],

            addItem(variantId, name, sku, qty, cost) {
                if (this.items.some(i => i.variant_id === variantId)) {
                    alert('Item is already in this PO.');
                    return;
                }
                this.items.push({
                    variant_id: variantId,
                    name: name,
                    sku: sku,
                    ordered_qty: qty || 20,
                    unit_cost: cost || 500
                });
            },

            addFromPicker() {
                const select = this.$refs.variantPicker;
                const opt = select.options[select.selectedIndex];
                if (!opt.value) return;

                this.addItem(
                    parseInt(opt.value),
                    opt.getAttribute('data-name'),
                    opt.getAttribute('data-sku'),
                    parseInt(opt.getAttribute('data-qty')),
                    parseInt(opt.getAttribute('data-cost'))
                );

                select.value = '';
            },

            removeItem(idx) {
                this.items.splice(idx, 1);
            },

            get totalAmount() {
                return this.items.reduce((acc, i) => acc + (i.ordered_qty * i.unit_cost), 0);
            }
        };
    }
</script>
@endsection
