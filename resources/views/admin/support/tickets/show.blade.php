{{--
    ======================================================================
    ADMIN MODULE: SUPPORT TICKETS SHOW
    ======================================================================
    File: resources/views/admin/support/tickets/show.blade.php
    Responsibility: Back-office administration for SUPPORT TICKETS SHOW
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
<div class="space-y-6" x-data="{
    isInternalNote: false
}">
    <!-- Header & Breadcrumbs -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-rose-600 uppercase tracking-wider mb-1">
                <a href="{{ route('admin.support.tickets.index') }}" class="hover:underline">Support Helpdesk</a>
                <span>•</span>
                <span class="text-slate-500 font-medium">Ticket {{ $ticket->ticket_number }}</span>
            </div>
            <div class="flex items-center gap-3 flex-wrap">
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">{{ $ticket->subject }}</h1>
                @php
                    $pBadge = $ticket->priority_badge;
                    $sBadge = $ticket->status_badge;
                @endphp
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs border {{ $pBadge['bg'] }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $pBadge['dot'] }}"></span>
                    <span>{{ $pBadge['label'] }}</span>
                </span>
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border {{ $sBadge['bg'] }}">
                    {{ $sBadge['label'] }}
                </span>
            </div>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="{{ route('admin.support.tickets.index') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition shadow-2xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to Desk
            </a>
        </div>
    </div>

    <!-- Main 2-Column Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Conversation Feed (Left 2 cols) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Messages Stream -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 space-y-6">
                <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-3 flex items-center justify-between">
                    <span>Message History &amp; Internal Notes</span>
                    <span class="text-xs font-normal text-slate-400">{{ $ticket->messages->count() }} messages</span>
                </h2>

                <div class="space-y-5">
                    @forelse($ticket->messages as $msg)
                        @php
                            $isInternal = $msg->is_internal_note;
                            $isAgent = $msg->user && in_array($msg->user->role, ['admin', 'vendor']);
                        @endphp
                        <div class="p-4 rounded-2xl border transition {{ $isInternal ? 'bg-amber-50/60 border-amber-200/80' : ($isAgent ? 'bg-indigo-50/30 border-indigo-100' : 'bg-slate-50 border-slate-200/70') }}">
                            <div class="flex items-center justify-between mb-2 pb-2 border-b {{ $isInternal ? 'border-amber-200/60' : 'border-slate-100' }}">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-full font-bold flex items-center justify-center text-xs {{ $isInternal ? 'bg-amber-200 text-amber-800' : ($isAgent ? 'bg-indigo-600 text-white' : 'bg-slate-300 text-slate-700') }}">
                                        {{ substr($msg->user->name ?? 'U', 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="font-bold text-xs text-slate-900">{{ $msg->user->name ?? 'System' }}</span>
                                            @if($isInternal)
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-200 text-amber-900 inline-flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                                    Private Staff Note
                                                </span>
                                            @elseif($isAgent)
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-indigo-100 text-indigo-700">Support Concierge</span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-200 text-slate-700">Customer</span>
                                            @endif
                                        </div>
                                        <div class="text-[11px] text-slate-400">{{ $msg->user->email ?? '' }}</div>
                                    </div>
                                </div>
                                <div class="text-[11px] text-slate-400">
                                    {{ $msg->created_at->format('M d, Y · H:i') }}
                                </div>
                            </div>
                            <div class="text-xs text-slate-800 leading-relaxed whitespace-pre-line font-normal">
                                {{ $msg->message }}
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-slate-400 text-xs">
                            No messages recorded yet.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Reply Console -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 space-y-4">
                <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">
                    Post Response or Internal Note
                </h2>
                
                <form method="POST" action="{{ route('admin.support.tickets.reply', $ticket) }}" class="space-y-4">
                    @csrf
                    <div>
                        <textarea name="message" rows="4" required 
                                  placeholder="Type your customer reply or internal troubleshooting note here..." 
                                  :class="isInternalNote ? 'border-amber-300 focus:border-amber-500 focus:ring-amber-500 bg-amber-50/20' : 'border-slate-200 focus:border-rose-500 focus:ring-rose-500'"
                                  class="w-full text-xs rounded-xl p-3"></textarea>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-2">
                        <div class="flex items-center gap-6">
                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="is_internal_note" value="1" x-model="isInternalNote" 
                                       class="rounded border-slate-300 text-amber-600 focus:ring-amber-500">
                                <span class="text-xs font-semibold text-slate-700 flex items-center gap-1">
                                    <span>🔒 Private Internal Note</span>
                                    <span class="text-[10px] text-slate-400 font-normal">(Visible only to agents)</span>
                                </span>
                            </label>

                            <div class="flex items-center gap-2">
                                <span class="text-xs text-slate-500">Update Status:</span>
                                <select name="change_status" class="text-xs rounded-xl border-slate-200 py-1 pl-2 pr-6">
                                    <option value="">Keep ({{ ucfirst(str_replace('_', ' ', $ticket->status)) }})</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="waiting_customer">Waiting on Customer</option>
                                    <option value="resolved">Mark Resolved</option>
                                    <option value="closed">Close Ticket</option>
                                </select>
                            </div>
                        </div>

                        <button type="submit" 
                                :class="isInternalNote ? 'bg-amber-600 hover:bg-amber-700' : 'bg-rose-600 hover:bg-rose-700'"
                                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold text-white shadow-sm transition">
                            <span x-text="isInternalNote ? 'Save Private Note' : 'Send Customer Reply'"></span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sidebar Details & Assignment (Right col) -->
        <div class="space-y-6">
            <!-- Ticket Controls Card -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 space-y-4">
                <h2 class="text-xs font-bold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-3">
                    Ticket Controls &amp; Status
                </h2>

                <form method="POST" action="{{ route('admin.support.tickets.update-status', $ticket) }}" class="space-y-3.5">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Status</label>
                        <select name="status" class="w-full text-xs rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500">
                            <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>Open</option>
                            <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="waiting_customer" {{ $ticket->status === 'waiting_customer' ? 'selected' : '' }}>Waiting on Customer</option>
                            <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                            <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Priority / SLA Window</label>
                        <select name="priority" class="w-full text-xs rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500">
                            <option value="low" {{ $ticket->priority === 'low' ? 'selected' : '' }}>Low (48 Hours)</option>
                            <option value="medium" {{ $ticket->priority === 'medium' ? 'selected' : '' }}>Medium (24 Hours)</option>
                            <option value="high" {{ $ticket->priority === 'high' ? 'selected' : '' }}>High (12 Hours)</option>
                            <option value="urgent" {{ $ticket->priority === 'urgent' ? 'selected' : '' }}>Urgent (4 Hours)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Assigned Support Agent</label>
                        <select name="assigned_to" class="w-full text-xs rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500">
                            <option value="">Unassigned</option>
                            @foreach($agents as $agent)
                                <option value="{{ $agent->id }}" {{ $ticket->assigned_to == $agent->id ? 'selected' : '' }}>
                                    {{ $agent->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="w-full py-2 bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs rounded-xl transition shadow-xs">
                        Update Ticket Dossier
                    </button>
                </form>
            </div>

            <!-- SLA Compliance Card -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 space-y-3">
                <h2 class="text-xs font-bold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-3 flex items-center justify-between">
                    <span>SLA Compliance</span>
                    <span class="w-2 h-2 rounded-full {{ $ticket->is_overdue ? 'bg-rose-500 animate-ping' : 'bg-emerald-500' }}"></span>
                </h2>

                <div class="space-y-2 text-xs">
                    <div class="flex items-center justify-between py-1">
                        <span class="text-slate-500">Logged Time:</span>
                        <span class="font-semibold text-slate-800">{{ $ticket->created_at->format('M d, Y · H:i') }}</span>
                    </div>
                    <div class="flex items-center justify-between py-1">
                        <span class="text-slate-500">Target SLA Dispatch:</span>
                        <span class="font-semibold text-slate-800">
                            {{ $ticket->sla_due_at ? $ticket->sla_due_at->format('M d, Y · H:i') : 'N/A' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between py-1">
                        <span class="text-slate-500">SLA Status:</span>
                        @if($ticket->is_overdue)
                            <span class="font-bold text-rose-600">Breached ({{ $ticket->sla_due_at->diffForHumans() }})</span>
                        @elseif($ticket->resolved_at)
                            <span class="font-bold text-emerald-600">Resolved at {{ $ticket->resolved_at->format('M d, H:i') }}</span>
                        @else
                            <span class="font-semibold text-emerald-600">In SLA ({{ $ticket->sla_due_at ? $ticket->sla_due_at->diffForHumans() : 'Active' }})</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Customer Dossier -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 space-y-3">
                <h2 class="text-xs font-bold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-3">
                    Customer Information
                </h2>

                @if($ticket->user)
                    <div class="space-y-2 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Name:</span>
                            <span class="font-bold text-slate-900">{{ $ticket->user->name }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Email:</span>
                            <span class="text-slate-700">{{ $ticket->user->email }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Role:</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] bg-slate-100 text-slate-700 capitalize">{{ $ticket->user->role }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Member Since:</span>
                            <span class="text-slate-700">{{ $ticket->user->created_at->format('M Y') }}</span>
                        </div>
                    </div>
                @else
                    <p class="text-xs text-slate-400 italic">No associated user account.</p>
                @endif

                @if($ticket->order)
                    <div class="pt-3 border-t border-slate-100">
                        <div class="text-xs font-bold text-slate-800 mb-2">Linked Order</div>
                        <a href="{{ route('admin.orders.show', $ticket->order) }}" 
                           class="block p-3 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-200/70 transition">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-bold text-rose-600">#{{ $ticket->order->order_number ?? $ticket->order->id }}</span>
                                <span class="font-semibold text-slate-800">₹{{ number_format($ticket->order->total_amount ?? 0, 2) }}</span>
                            </div>
                            <div class="text-[11px] text-slate-500 mt-1 capitalize">
                                Status: {{ $ticket->order->status }}
                            </div>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

