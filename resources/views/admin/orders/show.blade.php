{{--
    ======================================================================
    ADMIN MODULE: ORDERS SHOW
    ======================================================================
    File: resources/views/admin/orders/show.blade.php
    Responsibility: Back-office administration for ORDERS SHOW
    Features:
      - Standard administration UI following the Nex-on design guidelines
      - Accessible controls, Tailwind CSS styling, and reactive Alpine.js components
    Security & Access Control:
      - Guarded by Laravel authentication and RBAC permission middleware
    Operations & Maintenance:
      - Integrates with master administration shell (@extends('admin.layout'))
--}}
@extends('admin.layout')

@section('content')
<div class="space-y-6">
    <!-- Top Bar Navigation & Actions -->
    <div class="sm:flex sm:items-center sm:justify-between pb-4 border-b border-slate-200">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.orders.index') }}" 
               class="w-9 h-9 rounded-xl bg-white border border-slate-200 text-slate-600 hover:text-slate-900 flex items-center justify-center transition shadow-2xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-xl font-extrabold text-slate-900 tracking-tight font-mono">Order #{{ $order->reference }}</h1>
                    @php
                        $statusColors = [
                            'pending'    => 'bg-amber-50 text-amber-700 border-amber-200',
                            'confirmed'  => 'bg-blue-50 text-blue-700 border-blue-200',
                            'processing' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                            'shipped'    => 'bg-purple-50 text-purple-700 border-purple-200',
                            'delivered'  => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            'cancelled'  => 'bg-slate-100 text-slate-600 border-slate-200',
                        ];
                        $badgeClass = $statusColors[$order->status] ?? 'bg-slate-100 text-slate-600 border-slate-200';
                    @endphp
                    <span class="inline-flex items-center gap-1.5 text-xs font-bold px-2.5 py-0.5 rounded-full border {{ $badgeClass }}">
                        <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
                <p class="text-xs text-slate-400 mt-0.5">
                    Placed on {{ $order->created_at->format('M d, Y \a\t h:i A') }} ({{ $order->created_at->diffForHumans() }})
                </p>
            </div>
        </div>

        <div class="mt-4 sm:mt-0 flex items-center gap-3">
            <a href="{{ route('admin.orders.invoice', $order) }}" target="_blank"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white border border-slate-200 text-slate-700 hover:text-slate-900 text-xs font-bold transition shadow-xs">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Print Official Invoice</span>
            </a>
        </div>
    </div>

    <!-- Main Content 2-Column Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
        <!-- Left: Ordered Items & Timeline (2 cols) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Ordered Items Table Card -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="text-base font-bold text-slate-900">Ordered Package Items ({{ $order->items->sum('qty') }})</h2>
                    <span class="text-xs font-semibold text-slate-400">Currency: INR (₹)</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 text-left text-sm">
                        <thead class="bg-slate-50/70 text-slate-500 uppercase text-[11px] font-bold tracking-wider">
                            <tr>
                                <th class="px-5 py-3">Item Details</th>
                                <th class="px-5 py-3">Variant Specification</th>
                                <th class="px-5 py-3 text-right">Unit Price</th>
                                <th class="px-5 py-3 text-center">Qty</th>
                                <th class="px-5 py-3 text-right">Line Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($order->items as $item)
                                <tr class="hover:bg-slate-50/60 transition">
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-12 h-14 bg-slate-100 rounded-lg overflow-hidden shrink-0 border border-slate-200 flex items-center justify-center">
                                                @if($item->cover_image)
                                                    <img src="{{ $item->cover_image }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover">
                                                @else
                                                    <span class="text-[9px] text-slate-400 font-bold uppercase">No Pic</span>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="font-bold text-slate-900 text-xs">{{ $item->product_name }}</div>
                                                <div class="text-[11px] font-mono text-slate-400 mt-0.5">{{ $item->product_slug }}</div>
                                                @if($item->sku)
                                                    <span class="inline-block mt-0.5 px-1.5 py-0.5 rounded bg-slate-100 text-slate-800 text-[10px] font-mono font-bold border border-slate-200">
                                                        SKU: {{ $item->sku }}
                                                    </span>
                                                @endif
                                                @if($item->product_id)
                                                    <div class="mt-0.5">
                                                        <a href="{{ route('product.show', $item->product_slug) }}" target="_blank" class="text-[10px] text-indigo-600 hover:underline">
                                                            View Product →
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-1.5 text-xs">
                                            @if($item->color_hex)
                                                <span class="w-3 h-3 rounded-full border border-black/20 shrink-0" style="background-color: {{ $item->color_hex }}"></span>
                                            @endif
                                            <span class="font-medium text-slate-800">{{ $item->color_name }}</span>
                                        </div>
                                        <div class="mt-1">
                                            <span class="inline-block px-2 py-0.5 rounded bg-slate-100 text-slate-700 text-[11px] font-mono font-bold">
                                                Size: {{ $item->size }}
                                            </span>
                                        </div>
                                    </td>

                                    <td class="px-5 py-4 text-right font-medium text-slate-800 text-xs">
                                        ₹{{ number_format($item->price, 0) }}
                                    </td>

                                    <td class="px-5 py-4 text-center font-bold text-slate-900 text-xs">
                                        {{ $item->qty }}
                                    </td>

                                    <td class="px-5 py-4 text-right font-extrabold text-slate-900 text-xs">
                                        ₹{{ number_format($item->line_total, 0) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Financial Totals Summary -->
                <div class="p-5 bg-slate-50/70 border-t border-slate-100 flex justify-end">
                    <div class="w-72 space-y-2 text-xs">
                        <div class="flex justify-between text-slate-600">
                            <span>Items Subtotal:</span>
                            <span class="font-semibold text-slate-900">₹{{ number_format($order->subtotal, 0) }}</span>
                        </div>
                        <div class="flex justify-between text-slate-600">
                            <span>Shipping Charge:</span>
                            <span class="font-semibold text-slate-900">
                                {{ $order->shipping_charge > 0 ? '₹' . number_format($order->shipping_charge, 0) : 'Free Shipping' }}
                            </span>
                        </div>
                        @if($order->cod_charge > 0)
                            <div class="flex justify-between text-slate-600">
                                <span>COD Handling Fee:</span>
                                <span class="font-semibold text-slate-900">₹{{ number_format($order->cod_charge, 0) }}</span>
                            </div>
                        @endif
                        @if($order->discount > 0)
                            <div class="flex justify-between text-emerald-600 font-semibold">
                                <span>Coupon / Discount:</span>
                                <span>-₹{{ number_format($order->discount, 0) }}</span>
                            </div>
                        @endif
                        <div class="pt-2 border-t border-slate-200 flex justify-between text-sm font-extrabold text-slate-900">
                            <span>Grand Total:</span>
                            <span class="text-indigo-600 text-base">₹{{ number_format($order->total, 0) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Timeline Card -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs">
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-5">Order Fulfillment Timeline</h3>
                <div class="space-y-6 relative pl-6 border-l-2 border-slate-100 ml-2">
                    <!-- Step 1: Placed -->
                    <div class="relative">
                        <span class="absolute -left-[31px] top-0.5 w-4 h-4 rounded-full bg-emerald-500 ring-4 ring-emerald-50"></span>
                        <div class="text-xs font-bold text-slate-900">Order Placed & Confirmed</div>
                        <p class="text-[11px] text-slate-400 mt-0.5">{{ $order->created_at->format('M d, Y · h:i A') }}</p>
                    </div>

                    <!-- Step 2: Payment -->
                    <div class="relative">
                        <span class="absolute -left-[31px] top-0.5 w-4 h-4 rounded-full {{ $order->payment_status === 'paid' ? 'bg-emerald-500 ring-4 ring-emerald-50' : 'bg-amber-400 ring-4 ring-amber-50' }}"></span>
                        <div class="text-xs font-bold text-slate-900">
                            Payment: {{ ucfirst($order->payment_status) }} via {{ strtoupper($order->payment_method) }}
                        </div>
                        @if($order->payment_reference)
                            <p class="text-[11px] font-mono text-slate-400 mt-0.5">Gateway Txn ID: {{ $order->payment_reference }}</p>
                        @endif
                    </div>

                    <!-- Step 3: Shipped -->
                    <div class="relative">
                        <span class="absolute -left-[31px] top-0.5 w-4 h-4 rounded-full {{ $order->shipped_at ? 'bg-emerald-500 ring-4 ring-emerald-50' : 'bg-slate-200' }}"></span>
                        <div class="text-xs font-bold text-slate-900">
                            {{ $order->shipped_at ? 'Package Dispatched' : 'Awaiting Dispatch' }}
                        </div>
                        @if($order->shipped_at)
                            <p class="text-[11px] text-slate-400 mt-0.5">{{ $order->shipped_at->format('M d, Y · h:i A') }}</p>
                            @if($order->courier && $order->tracking_number)
                                <p class="text-xs font-semibold text-purple-700 mt-1">
                                    Courier: {{ $order->courier }} (AWB: {{ $order->tracking_number }})
                                </p>
                            @endif
                        @else
                            <p class="text-[11px] text-slate-400 mt-0.5">Use the Dispatch box on the right to enter courier tracking.</p>
                        @endif
                    </div>

                    <!-- Step 4: Delivered -->
                    <div class="relative">
                        <span class="absolute -left-[31px] top-0.5 w-4 h-4 rounded-full {{ $order->delivered_at ? 'bg-emerald-500 ring-4 ring-emerald-50' : 'bg-slate-200' }}"></span>
                        <div class="text-xs font-bold text-slate-900">
                            {{ $order->delivered_at ? 'Delivered to Customer' : 'Delivery Pending' }}
                        </div>
                        @if($order->delivered_at)
                            <p class="text-[11px] text-slate-400 mt-0.5">{{ $order->delivered_at->format('M d, Y · h:i A') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Status Control, Tracking & Customer Information (1 col) -->
        <div class="space-y-6">
            <!-- Order Status Change Dropdown Card -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Update Order Status</h3>
                <form method="POST" action="{{ route('admin.orders.updateStatus', $order) }}" class="space-y-3.5">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Fulfillment Status</label>
                        <select name="status" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                            @foreach($statuses as $statusKey => $statusLabel)
                                <option value="{{ $statusKey }}" {{ $order->status === $statusKey ? 'selected' : '' }}>
                                    {{ $statusLabel }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Payment Status</label>
                        <select name="payment_status" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                            @foreach($paymentStatuses as $pKey => $pLabel)
                                <option value="{{ $pKey }}" {{ $order->payment_status === $pKey ? 'selected' : '' }}>
                                    {{ $pLabel }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Order Notes / Internal</label>
                        <textarea name="notes" rows="2" placeholder="Private internal staff notes..." 
                                  class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">{{ $order->notes }}</textarea>
                    </div>

                    <button type="submit" 
                            class="w-full py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition shadow-xs">
                        Update Status
                    </button>
                </form>
            </div>

            <!-- In-House Delivery Personnel Assignment Card -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-base">🛵</span>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">In-House Fleet Dispatch</h3>
                </div>

                @if($order->deliveryPersonnel)
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 text-xs mb-3 space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-slate-900">{{ $order->deliveryPersonnel->name }}</span>
                            <span class="font-mono text-[10px] font-bold px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-700 uppercase">
                                {{ str_replace('_', ' ', $order->delivery_status) }}
                            </span>
                        </div>
                        <div class="text-[11px] text-slate-500 font-mono">{{ $order->deliveryPersonnel->phone }} &bull; {{ ucfirst($order->deliveryPersonnel->vehicle_type) }}</div>
                        @if($order->proof_signature)
                            <div class="pt-1 text-[10px] text-emerald-600 font-semibold">
                                Proof: Signed by {{ $order->proof_signature }}
                            </div>
                        @endif
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.orders.assignDriver', $order) }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Assign Driver</label>
                        <select name="delivery_personnel_id" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                            <option value="">-- Choose Driver --</option>
                            @foreach(\App\Models\DeliveryPersonnel::active()->get() as $dp)
                                <option value="{{ $dp->id }}" {{ $order->delivery_personnel_id === $dp->id ? 'selected' : '' }}>
                                    {{ $dp->name }} ({{ ucfirst($dp->vehicle_type) }} - {{ $dp->phone }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" 
                            class="w-full py-2.5 px-4 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition shadow-xs">
                        Assign Delivery Driver
                    </button>
                </form>
            </div>

            <!-- Courier Tracking Card -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
                <div class="flex items-center gap-2 mb-3">
                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Shipping & Tracking</h3>
                </div>

                <form method="POST" action="{{ route('admin.orders.updateTracking', $order) }}" class="space-y-3">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Courier Partner *</label>
                        <input type="text" name="courier" value="{{ old('courier', $order->courier ?? 'Delhivery') }}" required placeholder="e.g. Delhivery, BlueDart, DTDC" 
                               class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">AWB / Tracking Number *</label>
                        <input type="text" name="tracking_number" value="{{ old('tracking_number', $order->tracking_number) }}" required placeholder="e.g. DEL1234567890" 
                               class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-mono focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                    </div>

                    <button type="submit" 
                            class="w-full py-2.5 px-4 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-bold transition shadow-xs">
                        Save Tracking & Mark Shipped
                    </button>
                </form>
            </div>

            <!-- Customer & Shipping Address Card -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Customer & Delivery Address</h3>

                <div class="space-y-3 text-xs">
                    <div>
                        <div class="font-bold text-slate-900 text-sm">{{ $order->shipping_name }}</div>
                        <div class="text-slate-500 mt-0.5">{{ $order->shipping_email }}</div>
                        <div class="font-mono text-slate-700 font-semibold mt-0.5">{{ $order->shipping_phone }}</div>
                    </div>

                    <div class="pt-3 border-t border-slate-100">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Shipping Destination</span>
                        <div class="text-slate-700 leading-relaxed font-medium">
                            {{ $order->shipping_address1 }}<br>
                            @if($order->shipping_address2)
                                {{ $order->shipping_address2 }}<br>
                            @endif
                            {{ $order->shipping_city }}, {{ $order->shipping_state }} — <span class="font-mono font-bold">{{ $order->shipping_pincode }}</span>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-slate-500">Payment Option:</span>
                        <span class="font-bold uppercase text-slate-800">{{ strtoupper($order->payment_method) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
