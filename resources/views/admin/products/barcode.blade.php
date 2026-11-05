@extends('admin.layout')

@section('content')
<div class="space-y-8" x-data="{
    selectedSize: '{{ $selectedSize }}',
    storeName: 'NEXON LUXE',
    showStoreName: true,
    showPrice: true,
    showSku: true,
    selectedItems: {},
    selectAll: false,
    
    toggleSelectAll(products) {
        if (this.selectAll) {
            products.forEach(p => {
                this.selectedItems[p.id] = {
                    id: p.id,
                    sku: p.sku,
                    name: p.name,
                    price: p.starting_price || 0,
                    copies: 1
                };
            });
        } else {
            this.selectedItems = {};
        }
    },

    toggleItem(p) {
        if (this.selectedItems[p.id]) {
            delete this.selectedItems[p.id];
        } else {
            this.selectedItems[p.id] = {
                id: p.id,
                sku: p.sku,
                name: p.name,
                price: p.starting_price || 0,
                copies: 1
            };
        }
    },

    selectedCount() {
        return Object.keys(this.selectedItems).length;
    },

    totalCopies() {
        return Object.values(this.selectedItems).reduce((acc, curr) => acc + parseInt(curr.copies || 1), 0);
    }
}">
    <!-- Header -->
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Barcode &amp; Thermal Label Generator</h1>
            <p class="mt-1 text-sm text-slate-500">Generate high-density Code-128 vector barcodes and batch print standard thermal adhesive rolls for jewelry, apparel tags, and outer cartons.</p>
        </div>
        <div class="mt-4 sm:mt-0 flex items-center gap-3">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-indigo-50 border border-indigo-200 text-indigo-700 text-xs font-bold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Pure Code-128 Engine
            </span>
        </div>
    </div>

    @if(session('error'))
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Configuration & Live Preview Studio -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-5">
                <h2 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Thermal Label Preset
                </h2>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Roll Dimensions</label>
                    <select x-model="selectedSize" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-medium">
                        @foreach($labelSizes as $key => $size)
                            <option value="{{ $key }}">{{ $size['name'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Header Store Name</label>
                    <input type="text" x-model="storeName" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-medium">
                </div>

                <div class="space-y-2 pt-2 border-t border-slate-100">
                    <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Display Fields</p>
                    <label class="flex items-center gap-2.5 cursor-pointer">
                        <input type="checkbox" x-model="showStoreName" class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300">
                        <span class="text-xs font-semibold text-slate-700">Include Store Header</span>
                    </label>
                    <label class="flex items-center gap-2.5 cursor-pointer">
                        <input type="checkbox" x-model="showPrice" class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300">
                        <span class="text-xs font-semibold text-slate-700">Include MRP / Selling Price</span>
                    </label>
                    <label class="flex items-center gap-2.5 cursor-pointer">
                        <input type="checkbox" x-model="showSku" class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300">
                        <span class="text-xs font-semibold text-slate-700">Include SKU Text String</span>
                    </label>
                </div>
            </div>

            <!-- Live Barcode Preview Box -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-3">
                <div class="flex items-center justify-between">
                    <h2 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Thermal Tag Preview</h2>
                    <span class="text-[10px] font-mono text-indigo-600 font-bold" x-text="selectedSize"></span>
                </div>
                
                <div class="p-4 bg-slate-50 rounded-xl border border-dashed border-slate-300 flex flex-col items-center justify-center text-center">
                    <div class="bg-white p-3 rounded-lg border border-slate-200 shadow-xs w-full max-w-[240px] text-center space-y-1">
                        <div x-show="showStoreName" class="text-[10px] font-black tracking-widest text-slate-800 uppercase" x-text="storeName"></div>
                        <div class="text-[11px] font-bold text-slate-900 truncate">Sample Luxury Silk Robe</div>
                        <div class="py-1 flex justify-center">
                            {!! $previewSvg !!}
                        </div>
                        <div x-show="showSku" class="text-[9px] font-mono text-slate-600 font-bold">{{ $sampleSku }}</div>
                        <div x-show="showPrice" class="text-[11px] font-black text-slate-900 pt-0.5">MRP: ₹2,499.00</div>
                    </div>
                </div>
                <p class="text-[10px] text-slate-400 text-center">Outputs vector SVGs pixel-perfect on Zebra, TSC, Honeywell, and Xprinter thermal rolls.</p>
            </div>
        </div>

        <!-- Product Selector & Batch Printing Queue -->
        <div class="lg:col-span-2 space-y-4">
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <!-- Search & Actions Bar -->
                <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-slate-50/50">
                    <form method="GET" action="{{ route('admin.barcode.index') }}" class="flex items-center gap-2 flex-1 max-w-sm">
                        <input type="hidden" name="size" :value="selectedSize">
                        <div class="relative w-full">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </span>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by product name or SKU..."
                                   class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                        </div>
                        <button type="submit" class="px-3 py-2 bg-slate-200/70 text-slate-700 text-xs font-semibold rounded-xl hover:bg-slate-300">Search</button>
                    </form>

                    <!-- Batch Trigger Form -->
                    <form action="{{ route('admin.barcode.print') }}" method="POST" target="_blank" class="inline">
                        @csrf
                        <input type="hidden" name="size" :value="selectedSize">
                        <input type="hidden" name="store_name" :value="storeName">
                        <input type="hidden" name="show_store_name" :value="showStoreName ? '1' : '0'">
                        <input type="hidden" name="show_price" :value="showPrice ? '1' : '0'">
                        <input type="hidden" name="show_sku" :value="showSku ? '1' : '0'">

                        <template x-for="(item, key, index) in selectedItems" :key="key">
                            <div>
                                <input type="hidden" :name="'items[' + index + '][id]'" :value="item.id">
                                <input type="hidden" :name="'items[' + index + '][sku]'" :value="item.sku">
                                <input type="hidden" :name="'items[' + index + '][name]'" :value="item.name">
                                <input type="hidden" :name="'items[' + index + '][price]'" :value="item.price">
                                <input type="hidden" :name="'items[' + index + '][copies]'" :value="item.copies">
                            </div>
                        </template>

                        <button type="submit" :disabled="selectedCount() === 0"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-white transition-all shadow-sm"
                                :class="selectedCount() > 0 ? 'bg-indigo-600 hover:bg-indigo-700' : 'bg-slate-300 cursor-not-allowed'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            Print Selected (<span x-text="totalCopies()"></span> labels)
                        </button>
                    </form>
                </div>

                <!-- Products Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600">
                        <thead class="bg-slate-50/75 border-b border-slate-200/70 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                            <tr>
                                <th class="py-3 px-4 w-10 text-center">
                                    <input type="checkbox" x-model="selectAll" @change="toggleSelectAll({{ $products->toJson() }})" class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300">
                                </th>
                                <th class="py-3 px-4">Product &amp; Category</th>
                                <th class="py-3 px-4">SKU Code</th>
                                <th class="py-3 px-4">Price</th>
                                <th class="py-3 px-4">Stock</th>
                                <th class="py-3 px-4 w-24">Copies</th>
                                <th class="py-3 px-4 text-right">Quick Print</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($products as $product)
                                <tr class="hover:bg-slate-50/60 transition-colors" :class="selectedItems['{{ $product->id }}'] ? 'bg-indigo-50/30' : ''">
                                    <td class="py-3 px-4 text-center">
                                        <input type="checkbox" :checked="!!selectedItems['{{ $product->id }}']" @change="toggleItem({ id: {{ $product->id }}, sku: '{{ $product->sku }}', name: '{{ addslashes($product->name) }}', starting_price: {{ (float) $product->starting_price }} })" class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300">
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="font-bold text-slate-900">{{ $product->name }}</div>
                                        <div class="text-[10px] text-slate-400">{{ $product->category?->name ?? 'Uncategorized' }}</div>
                                    </td>
                                    <td class="py-3 px-4 font-mono font-bold text-indigo-600 text-[11px]">
                                        {{ $product->sku }}
                                    </td>
                                    <td class="py-3 px-4 font-semibold text-slate-800">
                                        ₹{{ number_format((float) $product->starting_price, 2) }}
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold {{ $product->total_stock > 5 ? 'bg-emerald-100 text-emerald-800' : ($product->total_stock > 0 ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                                            {{ $product->total_stock }} units
                                        </span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <input type="number" min="1" max="100" value="1"
                                               @input="if(selectedItems['{{ $product->id }}']) selectedItems['{{ $product->id }}'].copies = $event.target.value"
                                               class="w-16 px-2 py-1 text-xs border border-slate-200 rounded-lg text-center focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-bold">
                                    </td>
                                    <td class="py-3 px-4 text-right">
                                        <form action="{{ route('admin.barcode.print') }}" method="POST" target="_blank" class="inline">
                                            @csrf
                                            <input type="hidden" name="size" :value="selectedSize">
                                            <input type="hidden" name="store_name" :value="storeName">
                                            <input type="hidden" name="show_store_name" :value="showStoreName ? '1' : '0'">
                                            <input type="hidden" name="show_price" :value="showPrice ? '1' : '0'">
                                            <input type="hidden" name="show_sku" :value="showSku ? '1' : '0'">
                                            <input type="hidden" name="items[0][id]" value="{{ $product->id }}">
                                            <input type="hidden" name="items[0][sku]" value="{{ $product->sku }}">
                                            <input type="hidden" name="items[0][name]" value="{{ $product->name }}">
                                            <input type="hidden" name="items[0][price]" value="{{ (float) $product->starting_price }}">
                                            <input type="hidden" name="items[0][copies]" value="1">
                                            <button type="submit" class="p-1.5 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors" title="Print 1 Label">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-12 text-center text-slate-400">
                                        <p class="font-semibold text-slate-600">No active products found</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($products->hasPages())
                    <div class="p-4 border-t border-slate-100">
                        {{ $products->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

