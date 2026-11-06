<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Setting;
use App\Models\SupplierInvoice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminProfitLossController extends Controller
{
    /**
     * Display automated Profit & Loss (P&L) statement.
     */
    public function index(Request $request): View
    {
        $pl = $this->calculateProfitLoss($request);

        return view('admin.finance.profit-loss', $pl);
    }

    /**
     * Print formal executive P&L statement.
     */
    public function print(Request $request): View
    {
        $pl = $this->calculateProfitLoss($request);
        $storeName = Setting::get('store_name', 'Nex-on');
        $gstin = Setting::get('gstin', '27AABCS1429B1ZB');
        $storeAddress = Setting::get('store_address', '101 Fashion Boulevard, Mumbai');

        return view('admin.finance.profit-loss-print', array_merge($pl, compact('storeName', 'gstin', 'storeAddress')));
    }

    /**
     * Export P&L report to CSV.
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $pl = $this->calculateProfitLoss($request);
        $fileName = 'profit_and_loss_' . now()->format('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($pl) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['NexStore Business Suite - Profit & Loss Statement (P&L)']);
            fputcsv($handle, ['Reporting Period:', $pl['startDate']->format('d M Y') . ' to ' . $pl['endDate']->format('d M Y')]);
            fputcsv($handle, ['Generated At:', now()->toDayDateTimeString()]);
            fputcsv($handle, []);

            // 1. REVENUE
            fputcsv($handle, ['1. OPERATING REVENUE']);
            fputcsv($handle, ['Gross Merchandise Sales', number_format($pl['grossSales'], 2)]);
            fputcsv($handle, ['Shipping & Packaging Fees Collected', number_format($pl['shippingCollected'], 2)]);
            fputcsv($handle, ['Less: Promotional Discounts & Coupons', '-' . number_format($pl['discountsGiven'], 2)]);
            fputcsv($handle, ['Less: Customer Refunds & Returns', '-' . number_format($pl['refundsGiven'], 2)]);
            fputcsv($handle, ['NET OPERATING REVENUE (A)', number_format($pl['netRevenue'], 2)]);
            fputcsv($handle, []);

            // 2. COGS
            fputcsv($handle, ['2. COST OF GOODS SOLD (COGS)']);
            fputcsv($handle, ['Product Inventory Direct Cost', number_format($pl['cogsProducts'], 2)]);
            fputcsv($handle, ['Supplier Inbound Freight & Processing', number_format($pl['cogsFreight'], 2)]);
            fputcsv($handle, ['TOTAL COST OF GOODS SOLD (B)', number_format($pl['totalCogs'], 2)]);
            fputcsv($handle, []);

            // 3. GROSS PROFIT
            fputcsv($handle, ['3. GROSS PROFIT (A - B)', number_format($pl['grossProfit'], 2)]);
            fputcsv($handle, ['Gross Margin %', number_format($pl['grossMarginPercent'], 1) . '%']);
            fputcsv($handle, []);

            // 4. OPEX
            fputcsv($handle, ['4. OPERATING EXPENSES (OPEX)']);
            foreach ($pl['opexByCategory'] as $cat) {
                fputcsv($handle, [$cat['name'], number_format($cat['amount'], 2)]);
            }
            fputcsv($handle, ['TOTAL OPERATING EXPENSES (C)', number_format($pl['totalOpex'], 2)]);
            fputcsv($handle, []);

            // 5. EBITDA
            fputcsv($handle, ['5. OPERATING INCOME / EBITDA (Gross Profit - OPEX)', number_format($pl['ebitda'], 2)]);
            fputcsv($handle, ['Operating Margin %', number_format($pl['operatingMarginPercent'], 1) . '%']);
            fputcsv($handle, []);

            // 6. TAX & NET PROFIT
            fputcsv($handle, ['6. TAXES & NET PROFIT']);
            fputcsv($handle, ['Output GST Collected (Estimated)', number_format($pl['gstOutput'], 2)]);
            fputcsv($handle, ['Input GST Credit (ITC)', number_format($pl['gstInputCredit'], 2)]);
            fputcsv($handle, ['Net GST Liability', number_format($pl['netGstLiability'], 2)]);
            fputcsv($handle, ['Corporate Income Tax Provision (25%)', number_format($pl['taxProvision'], 2)]);
            fputcsv($handle, ['NET PROFIT AFTER TAX (PAT)', number_format($pl['netProfit'], 2)]);
            fputcsv($handle, ['Net Margin %', number_format($pl['netMarginPercent'], 1) . '%']);

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ]);
    }

    /**
     * Compute comprehensive financial metrics for the requested period.
     */
    protected function calculateProfitLoss(Request $request): array
    {
        $period = $request->input('period', 'this_month');

        [$startDate, $endDate] = match ($period) {
            'last_month'       => [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()],
            'current_quarter'  => [now()->firstOfQuarter()->startOfDay(), now()->lastOfQuarter()->endOfDay()],
            'financial_year'   => [Carbon::create(now()->month < 4 ? now()->year - 1 : now()->year, 4, 1)->startOfDay(), now()->endOfDay()],
            'all'              => [Carbon::parse('2020-01-01'), now()->endOfDay()],
            default            => [now()->startOfMonth(), now()->endOfMonth()], // 'this_month'
        };

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $startDate = Carbon::parse($request->input('from_date'))->startOfDay();
            $endDate = Carbon::parse($request->input('to_date'))->endOfDay();
            $period = 'custom';
        }

        // ==================== 1. REVENUE ====================
        $eligibleOrders = Order::whereNotIn('status', ['cancelled'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $grossSales = (float) $eligibleOrders->sum('subtotal');
        $shippingCollected = (float) $eligibleOrders->sum(fn($o) => (float)$o->shipping_charge + (float)$o->cod_charge);
        $discountsGiven = (float) $eligibleOrders->sum('discount');
        
        $refundsGiven = (float) Order::where('status', 'refunded')
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->sum('total');

        $netRevenue = max(0, ($grossSales + $shippingCollected) - ($discountsGiven + $refundsGiven));

        // ==================== 2. COGS (Cost of Goods Sold) ====================
        $orderIds = $eligibleOrders->pluck('id');
        
        $orderItems = OrderItem::with('variant')
            ->whereIn('order_id', $orderIds)
            ->get();

        $cogsProducts = 0.0;
        foreach ($orderItems as $item) {
            $cost = $item->variant && $item->variant->cost_price > 0
                ? (float) $item->variant->cost_price
                : (float) $item->price * 0.50; // Standard 50% fashion COGS benchmark if cost price unpopulated
            $cogsProducts += ($cost * (float) $item->qty);
        }

        // Inbound freight / processing from supplier purchases
        $cogsFreight = (float) SupplierInvoice::whereBetween('invoice_date', [$startDate, $endDate])
            ->sum('tax_amount') * 0.10; // Nominal inbound logistics attribution

        $totalCogs = $cogsProducts + $cogsFreight;

        // ==================== 3. GROSS PROFIT ====================
        $grossProfit = $netRevenue - $totalCogs;
        $grossMarginPercent = $netRevenue > 0 ? ($grossProfit / $netRevenue) * 100 : 0;

        // ==================== 4. OPERATING EXPENSES (OPEX) ====================
        $expenses = Expense::with('category')
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->get();

        $totalOpex = (float) $expenses->sum('amount');

        // Group by category for detailed statement rows
        $opexByCategory = $expenses->groupBy('expense_category_id')->map(function ($group) {
            $cat = $group->first()->category;
            return [
                'name'   => $cat ? $cat->name : 'General & Administrative',
                'color'  => $cat ? $cat->color : '#6366f1',
                'amount' => (float) $group->sum('amount'),
            ];
        })->values()->sortByDesc('amount')->values()->all();

        // ==================== 5. OPERATING PROFIT / EBITDA ====================
        $ebitda = $grossProfit - $totalOpex;
        $operatingMarginPercent = $netRevenue > 0 ? ($ebitda / $netRevenue) * 100 : 0;

        // ==================== 6. TAXES & NET PROFIT ====================
        $gstOutput = $netRevenue > 0 ? ($netRevenue * 0.18 / 1.18) : 0; // Estimated 18% inclusive GST
        $gstInputCredit = (float) $expenses->sum('tax_amount') + (float) SupplierInvoice::whereBetween('invoice_date', [$startDate, $endDate])->sum('tax_amount');
        $netGstLiability = max(0, $gstOutput - $gstInputCredit);

        // Corporate Income Tax Provision (25% standard for corporate if profitable)
        $taxProvision = $ebitda > 0 ? ($ebitda * 0.25) : 0;
        $netProfit = $ebitda - $taxProvision;
        $netMarginPercent = $netRevenue > 0 ? ($netProfit / $netRevenue) * 100 : 0;

        return compact(
            'period',
            'startDate',
            'endDate',
            'grossSales',
            'shippingCollected',
            'discountsGiven',
            'refundsGiven',
            'netRevenue',
            'cogsProducts',
            'cogsFreight',
            'totalCogs',
            'grossProfit',
            'grossMarginPercent',
            'totalOpex',
            'opexByCategory',
            'ebitda',
            'operatingMarginPercent',
            'gstOutput',
            'gstInputCredit',
            'netGstLiability',
            'taxProvision',
            'netProfit',
            'netMarginPercent'
        );
    }
}

