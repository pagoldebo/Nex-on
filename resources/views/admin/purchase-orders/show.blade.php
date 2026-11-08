{{--
    ======================================================================
    ADMIN MODULE: PURCHASE ORDERS SHOW
    ======================================================================
    File: resources/views/admin/purchase-orders/show.blade.php
    Responsibility: Back-office administration for PURCHASE ORDERS SHOW
    Features:
      - Standard administration UI following the Nex-on design guidelines
      - Accessible controls, Tailwind CSS styling, and reactive Alpine.js components
    Security & Access Control:
      - Guarded by Laravel authentication and RBAC permission middleware
    Operations & Maintenance:
      - Integrates with master administration shell (@extends('admin.layout'))
--}}
@extends('admin.layout', [
    'title' => 'Purchase Order #' . $purchaseOrder->po_number,
    'breadcrumbs' => [
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Purchase Orders', 'url' => route('admin.purchase-orders.index')],
        ['label' => '#' . $purchaseOrder->po_number],
    ]
])

@section('content')
<div class="space-y-6">

    <!-- Top Action Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-black text-slate-900 font-mono tracking-tight">{{ $purchaseOrder->po_number }}</h1>
                <span class="px-3 py-1 rounded-full text-xs font-bold border uppercase tracking-wider {{ $purchaseOrder->status_badge }}">
                    {{ $purchaseOrder->status }}
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-1">
                Drafted on {{ $purchaseOrder->created_at->format('M d, Y · h:i A') }} &bull; Supplier: <strong class="text-slate-800">{{ $purchaseOrder->supplier?->name }}</strong>
            </p>
        </div>

        <div class="flex items-center gap-2.5">
            <!-- Status Change Form -->
            <form method="POST" action="{{ route('admin.purchase-orders.updateStatus', $purchaseOrder) }}" class="flex items-center gap-1.5">
                @csrf
                @method('PATCH')
                <select name="status" onchange="this.form.submit()" class="px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold bg-white focus:outline-none">
                    <option value="draft" {{ $purchaseOrder->status === 'draft' ? 'selected' : '' }}>Mark as Draft</option>
                    <option value="sent" {{ $purchaseOrder->status === 'sent' ? 'selected' : '' }}>Mark as Sent to Vendor</option>
                    <option value="cancelled" {{ $purchaseOrder->status === 'cancelled' ? 'selected' : '' }}>Cancel Order</option>
                </select>
            </form>

            <a href="{{ route('admin.purchase-orders.print', $purchaseOrder) }}" target="_blank"
               class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold flex items-center gap-2 transition shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Print PO Slip</span>
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2 shadow-xs">
            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- 2 Cards: Vendor Details & Procurement Summary -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Vendor Card -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Supplier Information</h2>
            <div class="space-y-1.5 text-xs text-slate-600">
                <div class="text-sm font-bold text-slate-900">{{ $purchaseOrder->supplier?->name }}</div>
                <div>Code: <strong class="font-mono text-slate-800">{{ $purchaseOrder->supplier?->code }}</strong></div>
                <div>Contact Person: {{ $purchaseOrder->supplier?->contact_name ?? 'N/A' }}</div>
                <div>Email: <a href="mailto:{{ $purchaseOrder->supplier?->email }}" class="text-indigo-600">{{ $purchaseOrder->supplier?->email ?? 'N/A' }}</a></div>
                <div>Phone: {{ $purchaseOrder->supplier?->phone ?? 'N/A' }}</div>
                @if($purchaseOrder->supplier?->gst_number)
                    <div>GSTIN: <strong class="font-mono text-slate-800">{{ $purchaseOrder->supplier?->gst_number }}</strong></div>
                @endif
                <div>Address: {{ $purchaseOrder->supplier?->address ?? 'N/A' }}</div>
            </div>
        </div>

        <!-- Procurement Schedule Card -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Schedule &amp; Totals</h2>
            <div class="space-y-2.5 text-xs text-slate-600">
                <div class="flex items-center justify-between">
                    <span>Expected Delivery:</span>
                    <strong class="font-mono text-slate-900">{{ $purchaseOrder->expected_delivery_date ? $purchaseOrder->expected_delivery_date->format('M d, Y') : 'Immediate' }}</strong>
                </div>
                <div class="flex items-center justify-between">
                    <span>Created By:</span>
                    <strong class="text-slate-900">{{ $purchaseOrder->creator?->name ?? 'System Admin' }}</strong>
                </div>
                <div class="flex items-center justify-between">
                    <span>Total Ordered Units:</span>
                    <strong class="font-mono text-slate-900">{{ $purchaseOrder->items->sum('ordered_qty') }} units</strong>
                </div>
                <div class="flex items-center justify-between">
                    <span>Total Received to Date:</span>
                    <strong class="font-mono text-emerald-600">{{ $purchaseOrder->items->sum('received_qty') }} units</strong>
                </div>
                <div class="flex items-center justify-between pt-2 border-t border-slate-100 text-sm font-bold">
                    <span class="text-slate-800">Total PO Value:</span>
                    <span class="font-mono text-indigo-600">{{ $purchaseOrder->formatted_total }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Goods Received Note (GRN) Receiving Console -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-slate-100">
            <div>
                <h2 class="text-base font-bold text-slate-900">Goods Received Note (GRN) &amp; Stock Synchronization</h2>
                <p class="text-xs text-slate-500 mt-0.5">Enter delivered quantities to immediately update on-hand stock and generate audit inventory logs</p>
            </div>
            @php
                $allCompleted = $purchaseOrder->items->every(fn($i) => $i->received_qty >= $i->ordered_qty);
            @endphp
            @if($allCompleted)
                <span class="px-3 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full text-xs font-bold flex items-center gap-1.5 self-start sm:self-auto">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>All Items Fully Received</span>
                </span>
            @endif
        </div>

        <form method="POST" action="{{ route('admin.purchase-orders.receiveGoods', $purchaseOrder) }}" class="space-y-4">
            @csrf

            <div class="overflow-x-auto">
                <table class="min-w-full text-xs text-left">
                    <thead>
                        <tr class="text-slate-400 uppercase font-mono text-[10px] border-b border-slate-100">
                            <th class="py-2.5">Item / SKU</th>
                            <th class="py-2.5 text-center">Ordered</th>
                            <th class="py-2.5 text-center">Received to Date</th>
                            <th class="py-2.5 text-center">Pending Due</th>
                            <th class="py-2.5 text-right">Unit Rate</th>
                            <th class="py-2.5 text-right">Line Total</th>
                            <th class="py-2.5 w-32 text-center bg-indigo-50/50 rounded-t-lg text-indigo-900 font-bold">Receive Now</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($purchaseOrder->items as $item)
                            <tr>
                                <td class="py-3">
                                    <div class="font-bold text-slate-900">{{ $item->variant?->product?->name }}</div>
                                    <div class="text-[10px] text-slate-400 font-mono mt-0.5">
                                        {{ $item->variant?->sku }} &bull; Size: {{ $item->variant?->size }} &bull; Current On-Hand: <span class="font-bold text-slate-700">{{ $item->variant?->stock }}</span>
                                    </div>
                                </td>

                                <td class="py-3 text-center font-mono font-bold text-slate-900">
                                    {{ $item->ordered_qty }}
                                </td>

                                <td class="py-3 text-center font-mono font-bold text-emerald-600">
                                    {{ $item->received_qty }}
                                </td>

                                <td class="py-3 text-center font-mono font-bold {{ $item->pending_qty > 0 ? 'text-amber-600' : 'text-slate-400' }}">
                                    {{ $item->pending_qty }}
                                </td>

                                <td class="py-3 text-right font-mono text-slate-700">
                                    ₹{{ number_format($item->unit_cost, 0) }}
                                </td>

                                <td class="py-3 text-right font-mono font-bold text-slate-900">
                                    ₹{{ number_format($item->line_total, 0) }}
                                </td>

                                <td class="py-3 text-center bg-indigo-50/20">
                                    <input type="number" 
                                           name="received[{{ $item->id }}]" 
                                           min="0" 
                                           max="{{ $item->pending_qty }}"
                                           placeholder="{{ $item->pending_qty }}"
                                           value="{{ $item->pending_qty > 0 ? $item->pending_qty : 0 }}"
                                           {{ $item->pending_qty <= 0 ? 'disabled' : '' }}
                                           class="w-20 px-2 py-1.5 rounded-lg border border-indigo-200 text-center font-mono font-bold text-slate-900 focus:outline-none focus:border-indigo-500 disabled:bg-slate-100 disabled:opacity-50">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if(! $allCompleted)
                <div class="pt-3 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <input type="text" name="grn_notes" placeholder="GRN Invoice No. or Delivery Challan note (e.g. DC-7842 received at main gate)..."
                           class="flex-1 px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-indigo-500">
                    <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-md shadow-emerald-600/20 flex items-center justify-center gap-2 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Process GRN &amp; Restock Inventory</span>
                    </button>
                </div>
            @endif
        </form>
    </div>

</div>
@endsection
