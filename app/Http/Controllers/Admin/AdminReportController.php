<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminReportController extends Controller
{
    /**
     * Resolve date range from request parameters.
     */
    protected function resolveDateRange(Request $request): array
    {
        $preset = $request->input('preset', '30days');
        $now = Carbon::now();

        switch ($preset) {
            case 'today':
                $start = $now->copy()->startOfDay();
                $end   = $now->copy()->endOfDay();
                break;
            case 'yesterday':
                $start = $now->copy()->subDay()->startOfDay();
                $end   = $now->copy()->subDay()->endOfDay();
                break;
            case '7days':
                $start = $now->copy()->subDays(6)->startOfDay();
                $end   = $now->copy()->endOfDay();
                break;
            case 'this_month':
                $start = $now->copy()->startOfMonth();
                $end   = $now->copy()->endOfDay();
                break;
            case 'last_month':
                $start = $now->copy()->subMonth()->startOfMonth();
                $end   = $now->copy()->subMonth()->endOfMonth();
                break;
            case 'this_year':
                $start = $now->copy()->startOfYear();
                $end   = $now->copy()->endOfDay();
                break;
            case 'custom':
                $start = $request->filled('from_date') 
                    ? Carbon::parse($request->input('from_date'))->startOfDay() 
                    : $now->copy()->subDays(29)->startOfDay();
                $end   = $request->filled('to_date') 
                    ? Carbon::parse($request->input('to_date'))->endOfDay() 
                    : $now->copy()->endOfDay();
                break;
            case '30days':
            default:
                $preset = '30days';
                $start = $now->copy()->subDays(29)->startOfDay();
                $end   = $now->copy()->endOfDay();
                break;
        }

        return [$start, $end, $preset];
    }

    /**
     * Main Reports Dashboard.
     */
    public function index(Request $request): View
    {
        [$start, $end, $preset] = $this->resolveDateRange($request);
        $tab = $request->input('tab', 'sales');

        $data = [
            'tab'       => $tab,
            'preset'    => $preset,
            'from_date' => $start->toDateString(),
            'to_date'   => $end->toDateString(),
        ];

        // Gather relevant data depending on active tab
        if ($tab === 'sales') {
            $data['salesData'] = $this->getSalesData($start, $end);
        } elseif ($tab === 'products') {
            $data['productsData'] = $this->getProductsData($start, $end);
        } elseif ($tab === 'inventory') {
            $data['inventoryData'] = $this->getInventoryData();
        } elseif ($tab === 'gst') {
            $data['gstData'] = $this->getGstData($start, $end);
        }

        return view('admin.reports.index', $data);
    }

    /**
     * Calculate Sales & Revenue Metrics.
     */
    protected function getSalesData(Carbon $start, Carbon $end): array
    {
        $baseQuery = Order::whereBetween('created_at', [$start, $end]);

        // Exclude cancelled for revenue
        $activeOrders = (clone $baseQuery)->where('status', '!=', Order::STATUS_CANCELLED);

        $grossSales      = (float) $activeOrders->sum('subtotal');
        $netSales        = (float) $activeOrders->where('status', '!=', Order::STATUS_REFUNDED)->sum('total');
        $discounts       = (float) $activeOrders->sum('discount');
        $shippingCharges = (float) $activeOrders->sum('shipping_charge');
        $codCharges      = (float) $activeOrders->sum('cod_charge');
        $refundTotal     = (float) (clone $baseQuery)->where('status', Order::STATUS_REFUNDED)->sum('total');
        $totalOrders     = $activeOrders->count();
        $aov             = $totalOrders > 0 ? ($netSales / $totalOrders) : 0;

        // Channel breakdown
        $posSales    = (float) (clone $activeOrders)->where('channel', 'pos')->sum('total');
        $posCount    = (clone $activeOrders)->where('channel', 'pos')->count();
        $onlineSales = (float) (clone $activeOrders)->where(function($q) {
            $q->whereNull('channel')->orWhere('channel', '!=', 'pos');
        })->sum('total');
        $onlineCount = (clone $activeOrders)->where(function($q) {
            $q->whereNull('channel')->orWhere('channel', '!=', 'pos');
        })->count();

        // Payment method breakdown
        $paymentMethods = (clone $activeOrders)
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(total) as revenue'))
            ->groupBy('payment_method')
            ->get();

        // Orders list
        $orders = (clone $baseQuery)->latest()->paginate(15)->withQueryString();

        return compact(
            'grossSales', 'netSales', 'discounts', 'shippingCharges', 'codCharges',
            'refundTotal', 'totalOrders', 'aov', 'posSales', 'posCount', 'onlineSales',
            'onlineCount', 'paymentMethods', 'orders'
        );
    }

    /**
     * Calculate Product Performance Metrics.
     */
    protected function getProductsData(Carbon $start, Carbon $end): array
    {
        $orderIds = Order::whereBetween('created_at', [$start, $end])
            ->where('status', '!=', Order::STATUS_CANCELLED)
            ->pluck('id');

        $topItems = OrderItem::whereIn('order_id', $orderIds)
            ->select(
                'product_id',
                'product_name',
                'size',
                'color_name',
                DB::raw('SUM(qty) as total_units'),
                DB::raw('SUM(line_total) as total_revenue'),
                DB::raw('COUNT(DISTINCT order_id) as order_count'),
                DB::raw('AVG(price) as avg_price')
            )
            ->groupBy('product_id', 'product_name', 'size', 'color_name')
            ->orderByDesc('total_revenue')
            ->paginate(15)
            ->withQueryString();

        $totalUnitsSold = (int) OrderItem::whereIn('order_id', $orderIds)->sum('qty');
        $totalProductRevenue = (float) OrderItem::whereIn('order_id', $orderIds)->sum('line_total');

        // Category breakdown
        $categoryBreakdown = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->whereIn('order_items.order_id', $orderIds)
            ->select(
                DB::raw("COALESCE(categories.name, 'Uncategorized') as category_name"),
                DB::raw('SUM(order_items.qty) as units_sold'),
                DB::raw('SUM(order_items.line_total) as revenue')
            )
            ->groupBy('categories.name')
            ->orderByDesc('revenue')
            ->get();

        return compact('topItems', 'totalUnitsSold', 'totalProductRevenue', 'categoryBreakdown');
    }

    /**
     * Calculate Inventory Asset Valuation.
     */
    protected function getInventoryData(): array
    {
        $totalSkus      = ProductVariant::count();
        $totalUnits     = (int) ProductVariant::sum('stock');
        $totalCostValue = 0.0;
        $totalRetailVal = 0.0;
        $lowStockValue  = 0.0;
        $lowStockCount  = 0;

        ProductVariant::select('cost_price', 'price', 'stock', 'reorder_threshold')
            ->lazy(200)
            ->each(function ($v) use (&$totalCostValue, &$totalRetailVal, &$lowStockValue, &$lowStockCount) {
                $cost = (float) ($v->cost_price ?: ($v->price * 0.5)); // Default fallback 50% COGS
                $retail = (float) $v->price;
                $stock = max(0, (int) $v->stock);

                $totalCostValue += ($cost * $stock);
                $totalRetailVal += ($retail * $stock);

                $threshold = (int) ($v->reorder_threshold ?? 5);
                if ($stock <= $threshold) {
                    $lowStockCount++;
                    $lowStockValue += ($retail * $stock);
                }
            });

        $marginSpread = $totalRetailVal - $totalCostValue;
        $marginPct    = $totalRetailVal > 0 ? round(($marginSpread / $totalRetailVal) * 100, 1) : 0;

        $paginatedVariants = ProductVariant::with(['product.category', 'color'])
            ->orderBy('stock', 'asc')
            ->paginate(20)
            ->withQueryString();

        return compact(
            'totalSkus', 'totalUnits', 'totalCostValue', 'totalRetailVal',
            'marginSpread', 'marginPct', 'lowStockCount', 'lowStockValue',
            'paginatedVariants'
        );
    }

    /**
     * Calculate GST Tax Audit Metrics (GSTR-1 & GSTR-3B).
     */
    protected function getGstData(Carbon $start, Carbon $end): array
    {
        $storeState = Setting::get('store_state', 'Maharashtra');

        $orders = Order::whereBetween('created_at', [$start, $end])
            ->where('status', '!=', Order::STATUS_CANCELLED)
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $totalInvoiceValue = 0.0;
        $totalTaxableValue = 0.0;
        $totalCgst         = 0.0;
        $totalSgst         = 0.0;
        $totalIgst         = 0.0;
        $totalTax          = 0.0;

        $gstRows = [];

        $taxPercentage = (float) Setting::get('tax_percentage', 18);
        $divisor = 1 + ($taxPercentage / 100);

        Order::whereBetween('created_at', [$start, $end])
            ->where('status', '!=', Order::STATUS_CANCELLED)
            ->lazy(100)
            ->each(function ($ord) use (&$totalInvoiceValue, &$totalTaxableValue, &$totalCgst, &$totalSgst, &$totalIgst, &$totalTax, &$gstRows, $storeState, $taxPercentage, $divisor) {
                $invTotal = (float) $ord->total;
                $taxable = round($invTotal / $divisor, 2);
                $tax = round($invTotal - $taxable, 2);

                $isInterstate = strcasecmp(trim($ord->shipping_state ?? ''), trim($storeState)) !== 0;

                if ($isInterstate) {
                    $cgst = 0.0;
                    $sgst = 0.0;
                    $igst = $tax;
                } else {
                    $cgst = round($tax / 2, 2);
                    $sgst = round($tax - $cgst, 2);
                    $igst = 0.0;
                }

                $totalInvoiceValue += $invTotal;
                $totalTaxableValue += $taxable;
                $totalCgst         += $cgst;
                $totalSgst         += $sgst;
                $totalIgst         += $igst;
                $totalTax          += $tax;

                $gstRows[] = [
                    'order'       => $ord,
                    'invoice_ref' => $ord->reference,
                    'date'        => $ord->created_at->format('d M Y'),
                    'customer'    => $ord->shipping_name,
                    'state'       => $ord->shipping_state ?? 'N/A',
                    'type'        => $isInterstate ? 'Inter-State (IGST)' : 'Intra-State (CGST+SGST)',
                    'inv_value'   => $invTotal,
                    'taxable'     => $taxable,
                    'cgst'        => $cgst,
                    'sgst'        => $sgst,
                    'igst'        => $igst,
                    'total_tax'   => $tax,
                ];
            });

        return compact(
            'storeState', 'orders', 'totalInvoiceValue', 'totalTaxableValue',
            'totalCgst', 'totalSgst', 'totalIgst', 'totalTax'
        );
    }

    /**
     * CSV Export: Sales & Revenue.
     */
    public function exportSalesCsv(Request $request): StreamedResponse
    {
        [$start, $end] = $this->resolveDateRange($request);

        $filename = "sales_report_{$start->format('Ymd')}_{$end->format('Ymd')}.csv";

        return response()->streamDownload(function () use ($start, $end) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF"); // UTF-8 BOM

            \App\Services\CsvExporter::writeRow($handle, [
                'Order Reference', 'Date', 'Channel', 'Customer Name', 'Phone',
                'Subtotal', 'Discount', 'Shipping', 'COD Fee', 'Total',
                'Payment Method', 'Payment Status', 'Order Status'
            ]);

            Order::whereBetween('created_at', [$start, $end])
                ->latest()
                ->lazy(100)
                ->each(function ($order) use ($handle) {
                    \App\Services\CsvExporter::writeRow($handle, [
                        $order->reference,
                        $order->created_at->format('Y-m-d H:i:s'),
                        strtoupper($order->channel ?? 'ONLINE'),
                        $order->shipping_name,
                        $order->shipping_phone,
                        $order->subtotal,
                        $order->discount,
                        $order->shipping_charge,
                        $order->cod_charge,
                        $order->total,
                        strtoupper($order->payment_method),
                        strtoupper($order->payment_status),
                        strtoupper($order->status),
                    ]);
                });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * CSV Export: Product Performance.
     */
    public function exportProductsCsv(Request $request): StreamedResponse
    {
        [$start, $end] = $this->resolveDateRange($request);

        $orderIds = Order::whereBetween('created_at', [$start, $end])
            ->where('status', '!=', Order::STATUS_CANCELLED)
            ->pluck('id');

        $filename = "products_report_{$start->format('Ymd')}_{$end->format('Ymd')}.csv";

        return response()->streamDownload(function () use ($orderIds) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");

            \App\Services\CsvExporter::writeRow($handle, ['Product Title', 'Size', 'Color', 'Units Sold', 'Avg Price', 'Gross Revenue']);

            OrderItem::whereIn('order_id', $orderIds)
                ->select(
                    'product_name',
                    'size',
                    'color_name',
                    DB::raw('SUM(qty) as units_sold'),
                    DB::raw('SUM(line_total) as total_revenue'),
                    DB::raw('AVG(price) as avg_price')
                )
                ->groupBy('product_name', 'size', 'color_name')
                ->orderByDesc('total_revenue')
                ->lazy(100)
                ->each(function ($item) use ($handle) {
                    \App\Services\CsvExporter::writeRow($handle, [
                        $item->product_name,
                        $item->size,
                        $item->color_name,
                        $item->units_sold,
                        round($item->avg_price, 2),
                        round($item->total_revenue, 2),
                    ]);
                });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * CSV Export: Inventory Asset Valuation.
     */
    public function exportInventoryCsv(): StreamedResponse
    {
        $filename = "inventory_valuation_" . date('Ymd_His') . ".csv";

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");

            \App\Services\CsvExporter::writeRow($handle, [
                'SKU', 'Product Name', 'Color', 'Size', 'Supplier',
                'Stock Qty', 'Cost Price (₹)', 'Retail Price (₹)',
                'Cost Valuation (₹)', 'Retail Valuation (₹)', 'Potential Margin (₹)'
            ]);

            ProductVariant::with(['product', 'color', 'supplier'])
                ->lazy(100)
                ->each(function ($v) use ($handle) {
                    $cost = (float) ($v->cost_price ?: ($v->price * 0.5));
                    $retail = (float) $v->price;
                    $stock = max(0, (int) $v->stock);

                    \App\Services\CsvExporter::writeRow($handle, [
                        $v->sku,
                        $v->product->name ?? 'N/A',
                        $v->color->name ?? 'N/A',
                        $v->size,
                        $v->supplier->name ?? 'Default Supplier',
                        $stock,
                        $cost,
                        $retail,
                        round($cost * $stock, 2),
                        round($retail * $stock, 2),
                        round(($retail - $cost) * $stock, 2),
                    ]);
                });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * CSV Export: GST Tax Audit (GSTR-1 & GSTR-3B).
     */
    public function exportGstCsv(Request $request): StreamedResponse
    {
        [$start, $end] = $this->resolveDateRange($request);
        $storeState = Setting::get('store_state', 'Maharashtra');

        $filename = "gst_tax_report_{$start->format('Ymd')}_{$end->format('Ymd')}.csv";

        return response()->streamDownload(function () use ($start, $end, $storeState) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");

            \App\Services\CsvExporter::writeRow($handle, [
                'Invoice Ref', 'Order Date', 'Customer Name', 'Customer State',
                'Supply Type', 'Invoice Value (₹)', 'Taxable Amount (₹)',
                'CGST 9% (₹)', 'SGST 9% (₹)', 'IGST 18% (₹)', 'Total Tax (₹)'
            ]);

            Order::whereBetween('created_at', [$start, $end])
                ->where('status', '!=', Order::STATUS_CANCELLED)
                ->latest()
                ->lazy(100)
                ->each(function ($ord) use ($handle, $storeState) {
                    $invTotal = (float) $ord->total;
                    $taxable  = round($invTotal / 1.18, 2);
                    $tax      = round($invTotal - $taxable, 2);

                    $isInterstate = strcasecmp(trim($ord->shipping_state ?? ''), trim($storeState)) !== 0;

                    if ($isInterstate) {
                        $supplyType = 'Inter-State';
                        $cgst = 0.0;
                        $sgst = 0.0;
                        $igst = $tax;
                    } else {
                        $supplyType = 'Intra-State';
                        $cgst = round($tax / 2, 2);
                        $sgst = round($tax - $cgst, 2);
                        $igst = 0.0;
                    }

                    \App\Services\CsvExporter::writeRow($handle, [
                        $ord->reference,
                        $ord->created_at->format('Y-m-d'),
                        $ord->shipping_name,
                        $ord->shipping_state ?: 'Unknown',
                        $supplyType,
                        $invTotal,
                        $taxable,
                        $cgst,
                        $sgst,
                        $igst,
                        $tax,
                    ]);
                });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
