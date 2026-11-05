@extends('admin.layout')

@section('title', "Transfer Manifest #{$transfer->transfer_no}")

@section('content')
<div class="max-w-4xl mx-auto space-y-6 pb-12" x-data="{ showReceiveModal: false }">

    <!-- Header & Action Bar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 print:hidden">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.transfers.index') }}" class="text-xs font-bold text-indigo-600 hover:underline flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    <span>All Transfers</span>
                </a>
                <span class="text-slate-300">&bull;</span>
                <span class="px-2 py-0.5 text-[10px] font-bold rounded-md bg-slate-100 text-slate-700 font-mono">{{ $transfer->transfer_no }}</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 mt-1">Transfer Manifest #{{ $transfer->transfer_no }}</h1>
            <p class="text-xs text-slate-500 mt-0.5">Created on {{ $transfer->created_at->format('d M Y, H:i') }}</p>
        </div>

        <div class="flex items-center gap-3">
            <button onclick="window.print()" type="button"
                    class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition flex items-center gap-2 shadow-xs">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Print Slip</span>
            </button>

            @if($transfer->status === 'draft')
                <form method="POST" action="{{ route('admin.transfers.dispatch', $transfer) }}" onsubmit="return confirm('Deduct stock from origin outlet and mark as dispatched?')">
                    @csrf
                    <button type="submit"
                            class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-md shadow-indigo-600/20 flex items-center gap-2 transition">
                        <svg class="w-4 h-4 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                        <span>Dispatch Transfer</span>
                    </button>
                </form>
            @elseif($transfer->status === 'dispatched')
                <button @click="showReceiveModal = true" type="button"
                        class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-600/20 flex items-center gap-2 transition">
                    <svg class="w-4 h-4 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Receive &amp; Stock Inbound</span>
                </button>
            @endif
        </div>
    </div>

    <!-- Printable Slip Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 p-8 shadow-xs space-y-6 print:border-none print:shadow-none print:p-0">

        <!-- Slip Header -->
        <div class="flex items-start justify-between border-b border-slate-100 pb-6">
            <div>
                <div class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Inter-Store Stock Movement Manifest</div>
                <div class="text-xl font-black text-slate-900 mt-1 font-mono">{{ $transfer->transfer_no }}</div>
                <div class="text-xs text-slate-500 mt-0.5">Date: {{ $transfer->created_at->format('F d, Y') }}</div>
            </div>

            <div class="text-right">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $transfer->status === 'received' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($transfer->status === 'dispatched' ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-amber-50 text-amber-700 border border-amber-200') }}">
                    Status: {{ ucfirst($transfer->status) }}
                </span>
            </div>
        </div>

        <!-- Route Nodes -->
        <div class="grid grid-cols-2 gap-6 bg-slate-50/80 p-5 rounded-2xl border border-slate-100 text-xs">
            <div>
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Origin Location (Source)</div>
                <div class="font-bold text-slate-900 text-sm mt-1">{{ $transfer->fromOutlet->name }}</div>
                <div class="font-mono text-[11px] text-slate-500">{{ $transfer->fromOutlet->code }} &bull; {{ $transfer->fromOutlet->city }}</div>
                <div class="text-slate-500 mt-0.5">{{ $transfer->fromOutlet->address }}</div>
            </div>

            <div>
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Destination Location (Target)</div>
                <div class="font-bold text-slate-900 text-sm mt-1">{{ $transfer->toOutlet->name }}</div>
                <div class="font-mono text-[11px] text-slate-500">{{ $transfer->toOutlet->code }} &bull; {{ $transfer->toOutlet->city }}</div>
                <div class="text-slate-500 mt-0.5">{{ $transfer->toOutlet->address }}</div>
            </div>
        </div>

        @if($transfer->notes)
            <div class="p-4 rounded-xl bg-indigo-50/60 border border-indigo-100 text-xs">
                <span class="font-bold text-indigo-900">Logistics Notes:</span>
                <span class="text-indigo-700 ml-1">{{ $transfer->notes }}</span>
            </div>
        @endif

        <!-- Items Table -->
        <div class="overflow-hidden border border-slate-200/80 rounded-2xl">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-3">#</th>
                        <th class="px-5 py-3">Product Name &amp; Size</th>
                        <th class="px-5 py-3">SKU Barcode</th>
                        <th class="px-5 py-3 text-right">Qty Sent</th>
                        <th class="px-5 py-3 text-right">Qty Received</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @foreach($transfer->items as $idx => $item)
                        <tr>
                            <td class="px-5 py-3 text-slate-400 font-mono">{{ $idx + 1 }}</td>
                            <td class="px-5 py-3">
                                <div class="font-bold text-slate-900">{{ $item->variant->product->name ?? 'Product' }}</div>
                                <div class="text-[11px] text-slate-400">Size: {{ $item->variant->size ?? 'Std' }}</div>
                            </td>
                            <td class="px-5 py-3 font-mono font-bold text-slate-700">
                                {{ $item->variant->sku }}
                            </td>
                            <td class="px-5 py-3 text-right font-bold text-slate-900">
                                {{ $item->qty_sent }}
                            </td>
                            <td class="px-5 py-3 text-right font-bold {{ $item->qty_received === $item->qty_sent ? 'text-emerald-600' : 'text-slate-400' }}">
                                {{ $transfer->status === 'received' ? $item->qty_received : '—' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-slate-50/50 border-t border-slate-200/80 font-bold text-slate-900">
                    <tr>
                        <td colspan="3" class="px-5 py-3 text-right uppercase text-[10px] tracking-wider text-slate-500">Total Units</td>
                        <td class="px-5 py-3 text-right font-black text-sm">{{ $transfer->totalSentQty() }}</td>
                        <td class="px-5 py-3 text-right font-black text-sm text-emerald-600">
                            {{ $transfer->status === 'received' ? $transfer->totalReceivedQty() : '—' }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Audit & Sign-off Footer -->
        <div class="grid grid-cols-2 gap-8 pt-8 border-t border-slate-100 text-xs">
            <div>
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Dispatched By</div>
                <div class="font-bold text-slate-900 mt-1">{{ $transfer->dispatchedBy->name ?? 'Not dispatched' }}</div>
                <div class="text-[11px] text-slate-400">{{ $transfer->dispatched_at ? $transfer->dispatched_at->format('d M Y, H:i') : 'Pending dispatch' }}</div>
                <div class="mt-8 border-b border-dashed border-slate-300 w-48"></div>
                <div class="text-[10px] text-slate-400 mt-1">Origin Warehouse Manager Signature</div>
            </div>

            <div>
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Received &amp; Stocked By</div>
                <div class="font-bold text-slate-900 mt-1">{{ $transfer->receivedBy->name ?? 'Awaiting delivery' }}</div>
                <div class="text-[11px] text-slate-400">{{ $transfer->received_at ? $transfer->received_at->format('d M Y, H:i') : 'Pending verification' }}</div>
                <div class="mt-8 border-b border-dashed border-slate-300 w-48"></div>
                <div class="text-[10px] text-slate-400 mt-1">Destination Receiving Clerk Signature</div>
            </div>
        </div>

    </div>

    <!-- Receive Inbound Modal -->
    <div x-show="showReceiveModal" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs"
         style="display: none;">
        
        <div @click.away="showReceiveModal = false" class="bg-white rounded-3xl max-w-lg w-full p-6 text-slate-900 shadow-2xl space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h3 class="text-base font-black">Receive Transfer Manifest</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Verify received unit counts to update {{ $transfer->toOutlet->name }} inventory.</p>
                </div>
                <button @click="showReceiveModal = false" type="button" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
            </div>

            <form method="POST" action="{{ route('admin.transfers.receive', $transfer) }}" class="space-y-4 text-xs">
                @csrf

                <div class="space-y-3 max-h-64 overflow-y-auto pr-1">
                    @foreach($transfer->items as $item)
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/60 flex items-center justify-between">
                            <div>
                                <div class="font-bold text-slate-900">{{ $item->variant->product->name ?? 'SKU' }}</div>
                                <div class="font-mono text-[10px] text-slate-400">{{ $item->variant->sku }} &bull; Sent: {{ $item->qty_sent }}</div>
                            </div>

                            <div class="w-24">
                                <label class="block text-[9px] font-bold text-slate-400 uppercase">Received Qty</label>
                                <input type="number" name="received_qty[{{ $item->id }}]" value="{{ $item->qty_sent }}" min="0" required
                                       class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 font-mono text-center font-bold text-slate-900 focus:outline-none focus:border-indigo-500">
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button @click="showReceiveModal = false" type="button" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 font-bold transition">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold transition shadow-sm">Confirm &amp; Stock Inbound</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
