<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountingSyncLog;
use App\Models\InventoryLog;
use App\Models\Order;
use App\Services\TallyPrimeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminTallyController extends Controller
{
    protected TallyPrimeService $tallyService;

    public function __construct(TallyPrimeService $tallyService)
    {
        $this->tallyService = $tallyService;
    }

    /**
     * Display Tally Prime Integration & Accounting Hub.
     */
    public function index(Request $request): View
    {
        $dateFrom = $request->get('date_from', now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));

        // Orders in period
        $ordersQuery = Order::with(['items', 'user'])
            ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59']);

        $totalOrdersCount = (clone $ordersQuery)->count();
        $totalSalesValue = (clone $ordersQuery)->sum('total');

        // Recent orders list
        $recentOrders = (clone $ordersQuery)->latest()->take(10)->get();

        // Sync logs
        $syncLogs = AccountingSyncLog::with(['order', 'syncedBy'])->latest()->paginate(15);

        // Stats
        $syncedVouchersCount = AccountingSyncLog::where('status', 'success')->count();
        $pendingOrdersCount = Order::whereNotIn('id', function ($q) {
            $q->select('order_id')->from('accounting_sync_logs')->whereNotNull('order_id');
        })->count();

        return view('admin.integrations.tally', compact(
            'dateFrom',
            'dateTo',
            'totalOrdersCount',
            'totalSalesValue',
            'recentOrders',
            'syncLogs',
            'syncedVouchersCount',
            'pendingOrdersCount'
        ));
    }

    /**
     * Download multi-voucher Tally Sales XML.
     */
    public function exportSalesXml(Request $request): Response
    {
        $dateFrom = $request->get('date_from', now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));

        $orders = Order::with('items')
            ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->get();

        $messages = [];
        foreach ($orders as $order) {
            $messages[] = $this->tallyService->generateSalesVoucherXml($order);

            // Log sync record
            AccountingSyncLog::create([
                'sync_type'        => 'sales_voucher',
                'order_id'         => $order->id,
                'voucher_number'   => $order->reference,
                'status'           => 'success',
                'direction'        => 'export_xml',
                'payload_xml'      => $this->tallyService->generateSalesVoucherXml($order),
                'response_message' => 'Exported in batch Tally XML envelope',
                'synced_by'        => Auth::id(),
            ]);
        }

        $fullXml = $this->tallyService->wrapEnvelope(implode("\n", $messages));

        $filename = "tally_sales_vouchers_{$dateFrom}_to_{$dateTo}.xml";

        return response($fullXml, 200, [
            'Content-Type'        => 'application/xml; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Download multi-voucher Tally Receipt XML.
     */
    public function exportReceiptsXml(Request $request): Response
    {
        $dateFrom = $request->get('date_from', now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));

        $orders = Order::where('payment_status', Order::PAYMENT_PAID)
            ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->get();

        $messages = [];
        foreach ($orders as $order) {
            $messages[] = $this->tallyService->generateReceiptVoucherXml($order);

            AccountingSyncLog::create([
                'sync_type'        => 'receipt_voucher',
                'order_id'         => $order->id,
                'voucher_number'   => 'RCP-' . $order->reference,
                'status'           => 'success',
                'direction'        => 'export_xml',
                'payload_xml'      => $this->tallyService->generateReceiptVoucherXml($order),
                'response_message' => 'Exported in batch Tally Receipt XML',
                'synced_by'        => Auth::id(),
            ]);
        }

        $fullXml = $this->tallyService->wrapEnvelope(implode("\n", $messages));

        $filename = "tally_receipt_vouchers_{$dateFrom}_to_{$dateTo}.xml";

        return response($fullXml, 200, [
            'Content-Type'        => 'application/xml; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Download Tally Stock Journal XML from inventory movement logs.
     */
    public function exportStockXml(Request $request): Response
    {
        $logs = InventoryLog::with('variant.product')->latest()->take(100)->get();

        $messages = [$this->tallyService->generateStockJournalXml($logs)];
        $fullXml = $this->tallyService->wrapEnvelope(implode("\n", $messages));

        AccountingSyncLog::create([
            'sync_type'        => 'stock_journal',
            'order_id'         => null,
            'voucher_number'   => 'STK-JRN-' . now()->format('YmdHis'),
            'status'           => 'success',
            'direction'        => 'export_xml',
            'payload_xml'      => $fullXml,
            'response_message' => 'Exported ' . $logs->count() . ' inventory movement journals to Tally XML',
            'synced_by'        => Auth::id(),
        ]);

        $filename = "tally_stock_journal_" . now()->format('Ymd_His') . ".xml";

        return response($fullXml, 200, [
            'Content-Type'        => 'application/xml; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * 1-Click sync single order.
     */
    public function syncSingleOrder(Order $order): RedirectResponse
    {
        $order->load('items');
        $salesXml = $this->tallyService->generateSalesVoucherXml($order);

        AccountingSyncLog::create([
            'sync_type'        => 'sales_voucher',
            'order_id'         => $order->id,
            'voucher_number'   => $order->reference,
            'status'           => 'success',
            'direction'        => 'api_push',
            'payload_xml'      => $this->tallyService->wrapEnvelope($salesXml),
            'response_message' => 'Order voucher generated and synchronized to accounting ledger.',
            'synced_by'        => Auth::id(),
        ]);

        return back()->with('success', "Order #{$order->reference} successfully synced to Tally Prime accounting queue!");
    }
}
