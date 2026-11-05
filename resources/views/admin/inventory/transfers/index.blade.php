@extends('admin.layout')

@section('title', 'Inter-Store Stock Transfers')

@section('content')
<div class="space-y-6 pb-12">

    <!-- Header & Action Bar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.outlets.index') }}" class="text-xs font-bold text-indigo-600 hover:underline flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    <span>Outlets &amp; Warehouses</span>
                </a>
                <span class="text-slate-300">&bull;</span>
                <span class="px-2 py-0.5 text-[10px] font-bold rounded-md bg-indigo-50 text-indigo-600 border border-indigo-200/50 uppercase font-mono tracking-wider">Logistics Flow</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 mt-1">Inter-Store Stock Transfers</h1>
            <p class="text-xs text-slate-500 mt-0.5">Transfer inventory stock securely between regional fulfillment centers, flagships, and retail boutiques.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.transfers.create') }}" 
               class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-md shadow-indigo-600/20 flex items-center gap-2 transition">
                <svg class="w-4 h-4 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Initiate Transfer</span>
            </a>
        </div>
    </div>

    <!-- Transfers Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50/75 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Transfer #</th>
                        <th class="px-6 py-4">Origin &bull; Destination</th>
                        <th class="px-6 py-4">Items &bull; Sent Qty</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Dispatched By</th>
                        <th class="px-6 py-4">Date</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($transfers as $t)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="px-6 py-4 font-mono font-bold text-indigo-600">
                                <a href="{{ route('admin.transfers.show', $t) }}" class="hover:underline">
                                    {{ $t->transfer_no }}
                                </a>
                            </td>

                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900">{{ $t->fromOutlet->name ?? 'N/A' }}</div>
                                <div class="text-[11px] text-slate-400 flex items-center gap-1">
                                    <span>&rarr;</span>
                                    <span>{{ $t->toOutlet->name ?? 'N/A' }}</span>
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <span class="font-bold text-slate-900">{{ $t->items->count() }} SKUs</span>
                                <div class="text-[10px] text-slate-400">Total: {{ $t->totalSentQty() }} units</div>
                            </td>

                            <td class="px-6 py-4">
                                @if($t->status === 'received')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Received &amp; Stocked
                                    </span>
                                @elseif($t->status === 'dispatched')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                        In-Transit
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        Draft
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-slate-700">
                                {{ $t->dispatchedBy->name ?? 'System' }}
                            </td>

                            <td class="px-6 py-4 text-slate-500 font-mono text-[11px]">
                                {{ $t->created_at->format('d M Y, H:i') }}
                            </td>

                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.transfers.show', $t) }}" 
                                   class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[11px] transition">
                                    View Slip &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                No stock transfers recorded yet. Click "Initiate Transfer" to move stock between outlets.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transfers->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $transfers->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
