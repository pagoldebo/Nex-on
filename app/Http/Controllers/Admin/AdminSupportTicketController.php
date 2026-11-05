<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\SupportTicket;
use App\Models\SupportTicketMessage;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminSupportTicketController extends Controller
{
    /**
     * Job 26: Customer Support Helpdesk & SLA Ticketing Desk.
     */
    public function index(Request $request)
    {
        $query = SupportTicket::with(['user', 'order', 'agent'])->latest();

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Priority filter
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Search filter (ticket number, subject, customer name/email)
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'LIKE', "%{$search}%")
                    ->orWhere('subject', 'LIKE', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'LIKE', "%{$search}%")
                            ->orWhere('email', 'LIKE', "%{$search}%");
                    });
            });
        }

        $tickets = $query->paginate(15)->withQueryString();

        // Calculate Helpdesk Metrics
        $totalTickets = SupportTicket::count();
        $openTickets = SupportTicket::whereIn('status', ['open', 'in_progress', 'waiting_customer'])->count();
        $urgentTickets = SupportTicket::where('priority', 'urgent')
            ->whereNotIn('status', ['resolved', 'closed'])
            ->count();
        $overdueTickets = SupportTicket::where('sla_due_at', '<', Carbon::now())
            ->whereNotIn('status', ['resolved', 'closed'])
            ->count();
        $resolvedToday = SupportTicket::where('status', 'resolved')
            ->whereDate('resolved_at', Carbon::today())
            ->count();

        // Agents for assignment dropdown
        $agents = User::whereIn('role', ['admin', 'vendor'])
            ->whereNull('deactivated_at')
            ->orderBy('name')
            ->get();

        if ($agents->isEmpty()) {
            $agents = User::whereNull('deactivated_at')->get();
        }

        $customers = User::orderBy('name')->take(50)->get();

        return view('admin.support.tickets.index', compact(
            'tickets',
            'totalTickets',
            'openTickets',
            'urgentTickets',
            'overdueTickets',
            'resolvedToday',
            'agents',
            'customers'
        ));
    }

    /**
     * Show ticket conversation dossier.
     */
    public function show(SupportTicket $ticket)
    {
        $ticket->load(['user', 'order.orderItems', 'agent', 'messages.user']);

        $agents = User::whereIn('role', ['admin', 'vendor'])
            ->whereNull('deactivated_at')
            ->orderBy('name')
            ->get();

        if ($agents->isEmpty()) {
            $agents = User::whereNull('deactivated_at')->get();
        }

        return view('admin.support.tickets.show', compact('ticket', 'agents'));
    }

    /**
     * Store new support ticket manually from admin desk.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'     => 'required|exists:users,id',
            'order_id'    => 'nullable|exists:orders,id',
            'category'    => 'required|in:order_inquiry,refund_return,delivery_delay,product_defect,billing,other',
            'priority'    => 'required|in:low,medium,high,urgent',
            'subject'     => 'required|string|max:255',
            'message'     => 'required|string',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $ticket = SupportTicket::create([
            'user_id'     => $validated['user_id'],
            'order_id'    => $validated['order_id'] ?? null,
            'category'    => $validated['category'],
            'priority'    => $validated['priority'],
            'subject'     => $validated['subject'],
            'status'      => 'open',
            'assigned_to' => $validated['assigned_to'] ?? Auth::id(),
        ]);

        SupportTicketMessage::create([
            'ticket_id'        => $ticket->id,
            'user_id'          => Auth::id(),
            'message'          => $validated['message'],
            'is_internal_note' => false,
        ]);

        return redirect()->route('admin.support.tickets.show', $ticket)
            ->with('success', "Ticket {$ticket->ticket_number} logged successfully with SLA dispatch.");
    }

    /**
     * Reply or post internal note to ticket.
     */
    public function reply(Request $request, SupportTicket $ticket)
    {
        $validated = $request->validate([
            'message'          => 'required|string',
            'is_internal_note' => 'nullable|boolean',
            'change_status'    => 'nullable|in:open,in_progress,waiting_customer,resolved,closed',
        ]);

        $isInternal = (bool) ($validated['is_internal_note'] ?? false);

        SupportTicketMessage::create([
            'ticket_id'        => $ticket->id,
            'user_id'          => Auth::id(),
            'message'          => $validated['message'],
            'is_internal_note' => $isInternal,
        ]);

        if (!empty($validated['change_status'])) {
            $ticket->status = $validated['change_status'];
            if ($ticket->status === 'resolved' && !$ticket->resolved_at) {
                $ticket->resolved_at = Carbon::now();
            }
            $ticket->save();
        } elseif (!$isInternal && $ticket->status === 'open') {
            $ticket->status = 'in_progress';
            $ticket->save();
        }

        $typeLabel = $isInternal ? 'Internal staff note' : 'Customer response';

        return redirect()->route('admin.support.tickets.show', $ticket)
            ->with('success', "{$typeLabel} posted successfully.");
    }

    /**
     * Update ticket assignment, priority, or status.
     */
    public function updateStatus(Request $request, SupportTicket $ticket)
    {
        $validated = $request->validate([
            'status'      => 'nullable|in:open,in_progress,waiting_customer,resolved,closed',
            'priority'    => 'nullable|in:low,medium,high,urgent',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        if (isset($validated['status'])) {
            $ticket->status = $validated['status'];
            if ($ticket->status === 'resolved') {
                $ticket->resolved_at = Carbon::now();
            } elseif ($ticket->status !== 'closed' && $ticket->resolved_at) {
                $ticket->resolved_at = null;
            }
        }

        if (isset($validated['priority'])) {
            $ticket->priority = $validated['priority'];
        }

        if (array_key_exists('assigned_to', $validated)) {
            $ticket->assigned_to = $validated['assigned_to'];
        }

        $ticket->save();

        return redirect()->route('admin.support.tickets.show', $ticket)
            ->with('success', "Ticket {$ticket->ticket_number} updated.");
    }
}
