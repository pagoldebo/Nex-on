<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderRequest;
use App\Models\ProductVariant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminOrderRequestController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->input('status', 'all');
        $type   = $request->input('type', 'all');

        $query = OrderRequest::with(['order.items', 'user'])->latest();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($type !== 'all') {
            $query->where('type', $type);
        }

        $requests = $query->paginate(15)->withQueryString();

        $counts = [
            'all'      => OrderRequest::count(),
            'pending'  => OrderRequest::where('status', OrderRequest::STATUS_PENDING)->count(),
            'approved' => OrderRequest::where('status', OrderRequest::STATUS_APPROVED)->count(),
            'rejected' => OrderRequest::where('status', OrderRequest::STATUS_REJECTED)->count(),
            'cancel'   => OrderRequest::where('type', OrderRequest::TYPE_CANCEL)->count(),
            'return'   => OrderRequest::where('type', OrderRequest::TYPE_RETURN)->count(),
        ];

        return view('admin.order-requests.index', compact('requests', 'status', 'type', 'counts'));
    }

    public function update(Request $request, OrderRequest $orderRequest): RedirectResponse
    {
        $validated = $request->validate([
            'action'      => 'required|in:approve,reject',
            'admin_notes' => 'nullable|string|max:500',
        ]);

        $order = $orderRequest->order;

        if ($validated['action'] === 'approve') {
            $orderRequest->update([
                'status'      => OrderRequest::STATUS_APPROVED,
                'admin_notes' => $validated['admin_notes'] ?? 'Approved by administrator.',
            ]);

            if ($orderRequest->type === OrderRequest::TYPE_CANCEL) {
                $order->update(['status' => Order::STATUS_CANCELLED]);
                // Restock items
                foreach ($order->items as $item) {
                    if ($item->product_variant_id) {
                        $variant = ProductVariant::find($item->product_variant_id);
                        if ($variant) {
                            $variant->adjustStock($item->qty, 'order_cancellation_restock', "Admin approved cancellation #{$order->reference}", Auth::id());
                        }
                    }
                }
            } elseif ($orderRequest->type === OrderRequest::TYPE_RETURN) {
                $order->update(['status' => Order::STATUS_REFUND_REQ]);
            }

            return back()->with('success', 'Request approved successfully.');
        } else {
            $orderRequest->update([
                'status'      => OrderRequest::STATUS_REJECTED,
                'admin_notes' => $validated['admin_notes'] ?? 'Declined by administrator.',
            ]);

            return back()->with('info', 'Request declined.');
        }
    }
}
