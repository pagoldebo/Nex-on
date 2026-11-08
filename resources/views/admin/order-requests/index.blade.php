{{--
    ======================================================================
    ADMIN MODULE: ORDER REQUESTS INDEX
    ======================================================================
    File: resources/views/admin/order-requests/index.blade.php
    Responsibility: Back-office administration for ORDER REQUESTS INDEX
    Features:
      - Standard administration UI following the Nex-on design guidelines
      - Accessible controls, Tailwind CSS styling, and reactive Alpine.js components
    Security & Access Control:
      - Guarded by Laravel authentication and RBAC permission middleware
    Operations & Maintenance:
      - Integrates with master administration shell (@extends('admin.layout'))
--}}
@extends('admin.layout', [
    'title' => 'Cancellation & Return Requests',
    'breadcrumb' => 'Order Requests',
])

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Order Cancellations & Returns</h1>
            <p class="text-sm text-slate-500 mt-1">Review, approve, or decline customer refund, exchange, and cancellation submissions.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('info'))
        <div class="p-4 rounded-xl bg-slate-100 border border-slate-200 text-slate-700 text-sm flex items-center gap-3">
            <svg class="w-5 h-5 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ session('info') }}</span>
        </div>
    @endif

    <!-- Status Filter Tabs -->
    <div class="flex flex-wrap items-center gap-2">
        <a href="{{ route('admin.order-requests.index', ['status' => 'all', 'type' => $type]) }}" 
           class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition {{ $status === 'all' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
            All Requests ({{ $counts['all'] }})
        </a>
        <a href="{{ route('admin.order-requests.index', ['status' => 'pending', 'type' => $type]) }}" 
           class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition {{ $status === 'pending' ? 'bg-amber-600 text-white shadow-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
            Pending Review ({{ $counts['pending'] }})
        </a>
        <a href="{{ route('admin.order-requests.index', ['status' => 'approved', 'type' => $type]) }}" 
           class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition {{ $status === 'approved' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
            Approved ({{ $counts['approved'] }})
        </a>
        <a href="{{ route('admin.order-requests.index', ['status' => 'rejected', 'type' => $type]) }}" 
           class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition {{ $status === 'rejected' ? 'bg-rose-600 text-white shadow-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
            Declined ({{ $counts['rejected'] }})
        </a>
    </div>

    <!-- Requests Table -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50/75 border-b border-slate-200 text-xs font-bold text-slate-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Order Ref</th>
                        <th class="px-6 py-4">Customer</th>
                        <th class="px-6 py-4">Request Type</th>
                        <th class="px-6 py-4">Reason & Notes</th>
                        <th class="px-6 py-4">Date</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Decision</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($requests as $req)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.orders.show', $req->order_id) }}" class="font-mono font-bold text-indigo-600 hover:text-indigo-800 text-xs">
                                    {{ $req->order->reference ?? '#' . $req->order_id }}
                                </a>
                                <p class="text-[11px] text-slate-400 mt-0.5">Order Status: {{ ucfirst($req->order->status ?? 'unknown') }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-semibold text-slate-900 text-xs">{{ $req->order->shipping_name ?? $req->user->name ?? 'Guest' }}</p>
                                <p class="text-xs text-slate-400 font-mono">{{ $req->order->shipping_phone ?? '' }}</p>
                            </td>
                            <td class="px-6 py-4">
                                @if($req->type === 'cancel')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200/60">
                                        Cancellation
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200/60">
                                        Return / Exchange
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 max-w-xs">
                                <p class="font-semibold text-slate-800 text-xs">{{ $req->reason }}</p>
                                @if($req->comments)
                                    <p class="text-xs text-slate-500 mt-1 italic line-clamp-2">"{{ $req->comments }}"</p>
                                @endif
                                @if($req->admin_notes)
                                    <p class="text-[11px] text-indigo-600 mt-1 font-medium">Admin: {{ $req->admin_notes }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-500">
                                {{ $req->created_at->format('M d, Y H:i') }}
                            </td>
                            <td class="px-6 py-4">
                                @if($req->status === 'pending')
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-amber-50 text-amber-700 border border-amber-200/60">
                                        Pending Review
                                    </span>
                                @elseif($req->status === 'approved')
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                                        Approved
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-slate-100 text-slate-500">
                                        Declined
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($req->status === 'pending')
                                    <div class="flex items-center justify-end gap-2">
                                        <form method="POST" action="{{ route('admin.order-requests.update', $req) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="action" value="approve">
                                            <button type="submit" onclick="return confirm('Approve this request? This will update the order status.')" 
                                                    class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 transition border border-emerald-200/60">
                                                Approve
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.order-requests.update', $req) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="action" value="reject">
                                            <button type="submit" onclick="return confirm('Decline this request?')" 
                                                    class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-rose-50 text-rose-700 hover:bg-rose-100 transition border border-rose-200/60">
                                                Decline
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400 font-medium">Completed</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                No cancellation or return requests found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($requests->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $requests->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
