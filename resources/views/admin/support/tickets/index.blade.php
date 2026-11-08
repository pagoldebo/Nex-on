{{--
    ======================================================================
    ADMIN MODULE: SUPPORT TICKETS INDEX
    ======================================================================
    File: resources/views/admin/support/tickets/index.blade.php
    Responsibility: Back-office administration for SUPPORT TICKETS INDEX
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
    showNewTicketModal: false,
    newCustomerId: '',
    newCategory: 'Shipping & Delivery',
    newPriority: 'medium',
    newSubject: '',
    newMessage: ''
}">
    <!-- Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-rose-600 uppercase tracking-wider mb-1">
                <span>Organization</span>
                <span>•</span>
                <span class="text-slate-500 font-medium">Domain 7: Customer Support &amp; SLA Desk</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Customer Support &amp; Helpdesk Desk</h1>
            <p class="text-sm text-slate-500 mt-0.5">SLA-monitored customer ticket dispatch, priority queues, and customer communication ledger.</p>
        </div>
        <div class="flex items-center gap-2.5 flex-wrap">
            <a href="{{ route('admin.support.kb.index') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition shadow-2xs">
                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                Knowledge Base
            </a>
            <button @click="showNewTicketModal = true" 
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700 shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Log New Ticket
            </button>
        </div>
    </div>

    <!-- 4 High-Impact SLA & Desk KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-xs font-medium uppercase tracking-wider">Total Tickets</span>
                <span class="p-2 bg-slate-50 text-slate-600 rounded-xl">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                </span>
            </div>
            <div class="text-2xl font-bold text-slate-900">{{ number_format($totalTickets) }}</div>
            <p class="text-xs text-slate-500 mt-1">All tickets in system</p>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-xs font-medium uppercase tracking-wider">Active Queue</span>
                <span class="p-2 bg-amber-50 text-amber-600 rounded-xl">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <div class="text-2xl font-bold text-amber-600">{{ number_format($openTickets) }}</div>
            <p class="text-xs text-slate-500 mt-1">Awaiting resolution</p>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-xs font-medium uppercase tracking-wider">Urgent Escalations</span>
                <span class="p-2 bg-rose-50 text-rose-600 rounded-xl">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </span>
            </div>
            <div class="text-2xl font-bold text-rose-600">{{ number_format($urgentTickets) }}</div>
            <p class="text-xs text-rose-500 mt-1">4-hour priority SLA</p>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-xs font-medium uppercase tracking-wider">Overdue SLA</span>
                <span class="p-2 bg-purple-50 text-purple-600 rounded-xl">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <div class="text-2xl font-bold {{ $overdueTickets > 0 ? 'text-purple-600' : 'text-slate-900' }}">
                {{ number_format($overdueTickets) }}
            </div>
            <p class="text-xs text-slate-500 mt-1">Target dispatch breached</p>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
        <form method="GET" action="{{ route('admin.support.tickets.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5 items-end">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Search Tickets</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Ticket #, subject, customer..." 
                           class="w-full text-xs rounded-xl border-slate-200 pl-8 focus:border-rose-500 focus:ring-rose-500">
                    <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Ticket Status</label>
                <select name="status" class="w-full text-xs rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500">
                    <option value="">All Statuses</option>
                    <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Open</option>
                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="waiting_customer" {{ request('status') === 'waiting_customer' ? 'selected' : '' }}>Waiting on Customer</option>
                    <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                    <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Priority / SLA</label>
                <select name="priority" class="w-full text-xs rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500">
                    <option value="">All Priorities</option>
                    <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgent (4h SLA)</option>
                    <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High (12h SLA)</option>
                    <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Medium (24h SLA)</option>
                    <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low (48h SLA)</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Category</label>
                <select name="category" class="w-full text-xs rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500">
                    <option value="">All Categories</option>
                    <option value="order_inquiry" {{ request('category') === 'order_inquiry' ? 'selected' : '' }}>Order Inquiry</option>
                    <option value="refund_return" {{ request('category') === 'refund_return' ? 'selected' : '' }}>Refund &amp; Return</option>
                    <option value="delivery_delay" {{ request('category') === 'delivery_delay' ? 'selected' : '' }}>Delivery Delay</option>
                    <option value="product_defect" {{ request('category') === 'product_defect' ? 'selected' : '' }}>Product Defect</option>
                    <option value="billing" {{ request('category') === 'billing' ? 'selected' : '' }}>Billing &amp; Payment</option>
                    <option value="other" {{ request('category') === 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 py-2 px-3 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold rounded-xl transition">
                    Filter
                </button>
                <a href="{{ route('admin.support.tickets.index') }}" class="py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold rounded-xl transition text-center">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Tickets Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50/75 border-b border-slate-200 text-slate-600 font-semibold uppercase tracking-wider">
                        <th class="py-3.5 px-4">Ticket</th>
                        <th class="py-3.5 px-4">Customer</th>
                        <th class="py-3.5 px-4">Category</th>
                        <th class="py-3.5 px-4">Priority / SLA</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4">Assigned Agent</th>
                        <th class="py-3.5 px-4">Logged / Target</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($tickets as $ticket)
                        @php
                            $pBadge = $ticket->priority_badge;
                            $sBadge = $ticket->status_badge;
                            $isOverdue = $ticket->is_overdue;
                        @endphp
                        <tr class="hover:bg-slate-50/60 transition-colors {{ $isOverdue ? 'bg-rose-50/20' : '' }}">
                            <td class="py-3.5 px-4">
                                <a href="{{ route('admin.support.tickets.show', $ticket) }}" class="font-bold text-rose-600 hover:text-rose-700">
                                    {{ $ticket->ticket_number }}
                                </a>
                                <p class="text-slate-800 font-medium truncate max-w-xs mt-0.5" title="{{ $ticket->subject }}">
                                    {{ $ticket->subject }}
                                </p>
                            </td>
                            <td class="py-3.5 px-4">
                                @if($ticket->user)
                                    <div class="font-semibold text-slate-900">{{ $ticket->user->name }}</div>
                                    <div class="text-[11px] text-slate-400">{{ $ticket->user->email }}</div>
                                @else
                                    <span class="text-slate-400">Guest Customer</span>
                                @endif
                                @if($ticket->order)
                                    <span class="inline-block mt-0.5 text-[10px] font-mono font-semibold text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded">
                                        Order #{{ $ticket->order->order_number ?? $ticket->order->id }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-medium bg-slate-100 text-slate-700">
                                    {{ $ticket->category_label }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] border {{ $pBadge['bg'] }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $pBadge['dot'] }}"></span>
                                    <span>{{ $pBadge['label'] }}</span>
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-medium border {{ $sBadge['bg'] }}">
                                    {{ $sBadge['label'] }}
                                </span>
                                @if($isOverdue)
                                    <span class="block mt-1 text-[10px] font-bold text-rose-600 uppercase tracking-wider">
                                        ⚠ SLA Breached
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                @if($ticket->agent)
                                    <div class="flex items-center gap-1.5">
                                        <div class="w-5 h-5 rounded-full bg-slate-200 text-slate-700 font-bold flex items-center justify-center text-[10px]">
                                            {{ substr($ticket->agent->name, 0, 1) }}
                                        </div>
                                        <span class="text-slate-800 font-medium">{{ $ticket->agent->name }}</span>
                                    </div>
                                @else
                                    <span class="text-slate-400 italic">Unassigned</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="text-slate-600">{{ $ticket->created_at->format('M d, H:i') }}</div>
                                <div class="text-[11px] text-slate-400">
                                    SLA: {{ $ticket->sla_due_at ? $ticket->sla_due_at->diffForHumans() : '—' }}
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <a href="{{ route('admin.support.tickets.show', $ticket) }}" 
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-rose-50 text-slate-700 hover:text-rose-600 font-semibold transition">
                                    <span>Manage</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400">
                                <div class="max-w-xs mx-auto space-y-2">
                                    <svg class="w-8 h-8 text-slate-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                    <p class="font-semibold text-slate-600 text-sm">No support tickets match filters</p>
                                    <p class="text-xs text-slate-400">All customer inquiries are resolved or no tickets have been dispatched yet.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($tickets->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $tickets->links() }}
            </div>
        @endif
    </div>

    <!-- Log New Ticket Modal -->
    <div x-show="showNewTicketModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs transition-opacity" @click="showNewTicketModal = false"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-xl bg-white rounded-3xl p-6 shadow-2xl border border-slate-200">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">Log Support Ticket</h3>
                        <p class="text-xs text-slate-500">Record a phone, WhatsApp, or concierge inquiry into SLA tracker.</p>
                    </div>
                    <button @click="showNewTicketModal = false" class="text-slate-400 hover:text-slate-600">✕</button>
                </div>

                <form method="POST" action="{{ route('admin.support.tickets.store') }}" class="mt-4 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Customer <span class="text-rose-500">*</span></label>
                        <select name="user_id" required class="w-full text-xs rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500">
                            <option value="">Select Customer Account</option>
                            @foreach($customers as $cust)
                                <option value="{{ $cust->id }}">{{ $cust->name }} ({{ $cust->email }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Category <span class="text-rose-500">*</span></label>
                            <select name="category" required class="w-full text-xs rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500">
                                <option value="order_inquiry">Order Inquiry</option>
                                <option value="refund_return">Refund &amp; Return</option>
                                <option value="delivery_delay">Delivery Delay</option>
                                <option value="product_defect">Product Defect</option>
                                <option value="billing">Billing &amp; Payment</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Priority (SLA) <span class="text-rose-500">*</span></label>
                            <select name="priority" required class="w-full text-xs rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500">
                                <option value="low">Low (48 Hours)</option>
                                <option value="medium" selected>Medium (24 Hours)</option>
                                <option value="high">High (12 Hours)</option>
                                <option value="urgent">Urgent (4 Hours)</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Assign Concierge Agent</label>
                        <select name="assigned_to" class="w-full text-xs rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500">
                            <option value="">Auto-Assign to Self</option>
                            @foreach($agents as $agent)
                                <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Subject <span class="text-rose-500">*</span></label>
                        <input type="text" name="subject" required placeholder="Brief summary of inquiry..." 
                               class="w-full text-xs rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Inquiry Details <span class="text-rose-500">*</span></label>
                        <textarea name="message" rows="4" required placeholder="Full notes, customer statement, or request details..." 
                                  class="w-full text-xs rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-slate-100">
                        <button type="button" @click="showNewTicketModal = false" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-800">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl shadow-sm transition">
                            Create Ticket
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
