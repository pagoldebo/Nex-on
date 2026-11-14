<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class AdminOrderController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status', 'all');
        $search = trim((string) $request->query('q', ''));

        $query = Order::with('items')->latest();

        if ($status !== 'all' && in_array($status, [
            Order::STATUS_PENDING,
            Order::STATUS_CONFIRMED,
            Order::STATUS_PROCESSING,
            Order::STATUS_SHIPPED,
            Order::STATUS_DELIVERED,
            Order::STATUS_CANCELLED,
            Order::STATUS_REFUND_REQ,
            Order::STATUS_REFUNDED,
        ])) {
            $query->where('status', $status);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhere('shipping_name', 'like', "%{$search}%")
                  ->orWhere('shipping_email', 'like', "%{$search}%")
                  ->orWhere('shipping_phone', 'like', "%{$search}%");
            });
        }

        $orders = $query->paginate(15)->withQueryString();

        // Status counts for filter pills
        $statusCounts = [
            'all'         => Order::count(),
            'pending'     => Order::where('status', Order::STATUS_PENDING)->count(),
            'confirmed'   => Order::where('status', Order::STATUS_CONFIRMED)->count(),
            'processing'  => Order::where('status', Order::STATUS_PROCESSING)->count(),
            'shipped'     => Order::where('status', Order::STATUS_SHIPPED)->count(),
            'delivered'   => Order::where('status', Order::STATUS_DELIVERED)->count(),
            'cancelled'   => Order::where('status', Order::STATUS_CANCELLED)->count(),
        ];

        return view('admin.orders.index', compact('orders', 'status', 'search', 'statusCounts'));
    }

    public function show(Order $order): View
    {
        $order->load(['items.product', 'items.productColor', 'user']);

        $statuses = [
            Order::STATUS_PENDING    => 'Pending',
            Order::STATUS_CONFIRMED  => 'Confirmed',
            Order::STATUS_PROCESSING => 'Processing',
            Order::STATUS_SHIPPED    => 'Shipped',
            Order::STATUS_DELIVERED  => 'Delivered',
            Order::STATUS_CANCELLED  => 'Cancelled',
            Order::STATUS_REFUND_REQ => 'Refund Requested',
            Order::STATUS_REFUNDED   => 'Refunded',
        ];

        $paymentStatuses = [
            Order::PAYMENT_PENDING  => 'Pending',
            Order::PAYMENT_PAID     => 'Paid',
            Order::PAYMENT_FAILED   => 'Failed',
            Order::PAYMENT_REFUNDED => 'Refunded',
        ];

        return view('admin.orders.show', compact('order', 'statuses', 'paymentStatuses'));
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'status'         => 'required|string',
            'payment_status' => 'nullable|string',
            'notes'          => 'nullable|string',
        ]);

        $order->status = $validated['status'];

        if (!empty($validated['payment_status'])) {
            $order->payment_status = $validated['payment_status'];
        }

        if (isset($validated['notes'])) {
            $order->notes = $validated['notes'];
        }

        if ($validated['status'] === Order::STATUS_SHIPPED && empty($order->shipped_at)) {
            $order->shipped_at = now();
        }

        if ($validated['status'] === Order::STATUS_DELIVERED) {
            if (empty($order->delivered_at)) {
                $order->delivered_at = now();
            }
            if ($order->payment_method === 'cod') {
                $order->payment_status = Order::PAYMENT_PAID;
            }
        }

        $order->save();

        if (!empty($order->shipping_email)) {
            try {
                Mail::send('emails.order-status-update', ['order' => $order], function ($m) use ($order) {
                    $m->to($order->shipping_email, $order->shipping_name)
                      ->subject('Order Status Update: #' . $order->reference . ' is now ' . ucfirst($order->status));
                });
            } catch (\Exception $e) {
                Log::warning('Status update email failed: ' . $e->getMessage());
            }
        }

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', "Order #{$order->reference} status updated to " . ucfirst($order->status) . ".");
    }

    public function updateTracking(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'courier'         => 'required|string|max:100',
            'tracking_number' => 'required|string|max:100',
        ]);

        $order->courier         = $validated['courier'];
        $order->tracking_number = $validated['tracking_number'];

        // Automatically advance order status to Shipped if not yet shipped
        if (in_array($order->status, [Order::STATUS_PENDING, Order::STATUS_CONFIRMED, Order::STATUS_PROCESSING])) {
            $order->status     = Order::STATUS_SHIPPED;
            $order->shipped_at = now();
        }

        $order->save();

        if (!empty($order->shipping_email)) {
            try {
                Mail::send('emails.order-status-update', ['order' => $order], function ($m) use ($order) {
                    $m->to($order->shipping_email, $order->shipping_name)
                      ->subject('Order Shipped: #' . $order->reference . ' — Tracking AWB ' . $order->tracking_number);
                });
            } catch (\Exception $e) {
                Log::warning('Tracking update email failed: ' . $e->getMessage());
            }
        }

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', "Tracking details updated and order marked as Shipped.");
    }

    public function invoice(Order $order): View
    {
        $order->load(['items.product', 'items.productColor', 'user']);

        return view('admin.orders.invoice', compact('order'));
    }

    /**
     * Export Orders to downloadable CSV spreadsheet.
     */
    public function exportCsv(Request $request)
    {
        $status = $request->query('status', 'all');
        $range  = $request->query('range', 'all');

        $query = Order::with('items')->latest();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($range === 'today') {
            $query->whereDate('created_at', today());
        } elseif ($range === 'week') {
            $query->where('created_at', '>=', now()->subDays(7));
        } elseif ($range === 'month') {
            $query->where('created_at', '>=', now()->subDays(30));
        }

        $orders = $query->get();
        $filename = 'orders-export-' . now()->format('Y-m-d-His') . '.csv';

        return response()->streamDownload(function () use ($orders) {
            $handle = fopen('php://output', 'w');
            // Write UTF-8 BOM for Excel
            fputs($handle, "\xEF\xBB\xBF");

            // CSV Header
            \App\Services\CsvExporter::writeRow($handle, [
                'Order Reference',
                'Date',
                'Customer Name',
                'Phone',
                'Email',
                'Address 1',
                'Address 2',
                'City',
                'State',
                'Pincode',
                'Items Count',
                'Items Summary',
                'Subtotal (₹)',
                'Shipping (₹)',
                'COD Charge (₹)',
                'Discount (₹)',
                'Coupon Code',
                'Total (₹)',
                'Payment Method',
                'Payment Status',
                'Order Status',
                'Courier',
                'Tracking Number',
            ]);

            foreach ($orders as $o) {
                $itemsSummary = $o->items->map(function ($i) {
                    return "{$i->product_name} ({$i->color_name}/{$i->size}) x {$i->qty}";
                })->implode('; ');

                \App\Services\CsvExporter::writeRow($handle, [
                    $o->reference,
                    $o->created_at->format('Y-m-d H:i:s'),
                    $o->shipping_name,
                    $o->shipping_phone,
                    $o->shipping_email,
                    $o->shipping_address1,
                    $o->shipping_address2 ?? '',
                    $o->shipping_city,
                    $o->shipping_state,
                    $o->shipping_pincode,
                    $o->items->sum('qty'),
                    $itemsSummary,
                    $o->subtotal,
                    $o->shipping_charge,
                    $o->cod_charge,
                    $o->discount,
                    $o->coupon_code ?? '',
                    $o->total,
                    strtoupper($o->payment_method),
                    ucfirst($o->payment_status),
                    ucfirst($o->status),
                    $o->courier ?? '',
                    $o->tracking_number ?? '',
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}

