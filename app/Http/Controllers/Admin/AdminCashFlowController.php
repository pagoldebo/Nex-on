<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommercialInvoice;
use App\Models\Expense;
use App\Models\Order;
use App\Models\PosShiftMovement;
use App\Models\SupplierInvoicePayment;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminCashFlowController extends Controller
{
    /**
     * Display real-time cash flow dashboard, liquidity metrics, and daily statement.
     */
    public function index(Request $request): View
    {
        $data = $this->calculateCashFlowData($request);

        return view('admin.finance.cash-flow', $data);
    }

    /**
     * Export daily cash flow statement to CSV.
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $data = $this->calculateCashFlowData($request);
        $dailyRows = $data['dailyBreakdown'];

        $fileName = 'cash_flow_statement_' . now()->format('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($dailyRows, $data) {
            $handle = fopen('php://output', 'w');
            
            // File Header
            \App\Services\CsvExporter::writeRow($handle, ['NexStore Business Suite - Daily Cash Flow Statement']);
            \App\Services\CsvExporter::writeRow($handle, ['Period:', $data['startDate']->format('d M Y') . ' to ' . $data['endDate']->format('d M Y')]);
            \App\Services\CsvExporter::writeRow($handle, ['Total Inflows (INR):', number_format($data['totalInflows'], 2)]);
            \App\Services\CsvExporter::writeRow($handle, ['Total Outflows (INR):', number_format($data['totalOutflows'], 2)]);
            \App\Services\CsvExporter::writeRow($handle, ['Net Cash Flow (INR):', number_format($data['netCashFlow'], 2)]);
            \App\Services\CsvExporter::writeRow($handle, []);

            // Table Columns
            \App\Services\CsvExporter::writeRow($handle, ['Date', 'Day', 'Operational Inflows (₹)', 'Operational Outflows (₹)', 'Net Change (₹)', 'Running Balance (₹)']);

            foreach ($dailyRows as $row) {
                \App\Services\CsvExporter::writeRow($handle, [
                    $row['date'],
                    $row['day'],
                    number_format($row['inflows'], 2),
                    number_format($row['outflows'], 2),
                    number_format($row['net'], 2),
                    number_format($row['cumulative'], 2),
                ]);
            }

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ]);
    }

    /**
     * Core business calculation engine for cash inflows and outflows.
     */
    protected function calculateCashFlowData(Request $request): array
    {
        $period = $request->input('period', 'this_month');

        [$startDate, $endDate] = match ($period) {
            'today'          => [now()->startOfDay(), now()->endOfDay()],
            'this_week'      => [now()->startOfWeek(), now()->endOfWeek()],
            'last_month'     => [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()],
            'this_quarter'   => [now()->firstOfQuarter()->startOfDay(), now()->lastOfQuarter()->endOfDay()],
            'this_year'      => [now()->startOfYear(), now()->endOfYear()],
            default          => [now()->startOfMonth(), now()->endOfMonth()], // 'this_month'
        };

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $startDate = Carbon::parse($request->input('from_date'))->startOfDay();
            $endDate = Carbon::parse($request->input('to_date'))->endOfDay();
            $period = 'custom';
        }

        // ==================== INFLOWS ====================
        // 1. POS Cash Sales
        $posCashSales = (float) Order::where('channel', 'pos')
            ->where('payment_method', 'cash')
            ->whereNotIn('status', ['cancelled', 'refunded'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total');

        // 2. Online & Non-cash Sales (Razorpay, Stripe, Card, UPI)
        $onlinePaidSales = (float) Order::where('payment_status', 'paid')
            ->where(function ($q) {
                $q->where('channel', '!=', 'pos')
                  ->orWhere('payment_method', '!=', 'cash');
            })
            ->whereNotIn('status', ['cancelled', 'refunded'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total');

        // 3. POS Drawer Pay-Ins (Cash Floats & Manual Cash-in)
        $posPayIns = (float) PosShiftMovement::where('type', 'cash_in')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');

        // 4. B2B Commercial Invoices Collected
        $b2bCollected = (float) CommercialInvoice::whereIn('status', ['paid', 'partial'])
            ->whereBetween('invoice_date', [$startDate, $endDate])
            ->sum('amount_paid');

        $totalInflows = $posCashSales + $onlinePaidSales + $posPayIns + $b2bCollected;

        // ==================== OUTFLOWS ====================
        // 1. Operating Expenses (OPEX)
        $expensesOutflow = (float) Expense::whereBetween('expense_date', [$startDate, $endDate])
            ->sum('amount');

        // 2. Supplier Invoice Payments (COGS / Vendor Bills)
        $supplierPayments = (float) SupplierInvoicePayment::whereBetween('paid_at', [$startDate, $endDate])
            ->sum('amount');

        // 3. POS Drawer Pay-Outs (Petty Cash Cash-Outs)
        $posPayOuts = (float) PosShiftMovement::where('type', 'cash_out')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');

        // 4. Customer Order Refunds
        $refundsOutflow = (float) Order::where('status', 'refunded')
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->sum('total');

        $totalOutflows = $expensesOutflow + $supplierPayments + $posPayOuts + $refundsOutflow;

        $netCashFlow = $totalInflows - $totalOutflows;

        // Liquidity splits
        $cashDrawerLiquidity = ($posCashSales + $posPayIns) - $posPayOuts;
        $bankAccountLiquidity = ($onlinePaidSales + $b2bCollected) - ($expensesOutflow + $supplierPayments + $refundsOutflow);

        // Daily breakdown timeline
        $dailyBreakdown = [];
        $runningCumulative = 0;

        // Limit iteration to max 62 days for rendering performance if custom range is too broad
        $clampedStart = $startDate->diffInDays($endDate) > 62 ? (clone $endDate)->subDays(62)->startOfDay() : $startDate;
        $dateRange = CarbonPeriod::create($clampedStart, '1 day', $endDate);

        foreach ($dateRange as $day) {
            $dayStart = (clone $day)->startOfDay();
            $dayEnd = (clone $day)->endOfDay();

            $dayIn = (float) Order::where('payment_status', 'paid')
                ->whereNotIn('status', ['cancelled', 'refunded'])
                ->whereBetween('created_at', [$dayStart, $dayEnd])
                ->sum('total')
                + (float) PosShiftMovement::where('type', 'cash_in')->whereBetween('created_at', [$dayStart, $dayEnd])->sum('amount')
                + (float) CommercialInvoice::whereIn('status', ['paid', 'partial'])->whereBetween('invoice_date', [$dayStart, $dayEnd])->sum('amount_paid');

            $dayOut = (float) Expense::whereBetween('expense_date', [$dayStart, $dayEnd])->sum('amount')
                + (float) SupplierInvoicePayment::whereBetween('paid_at', [$dayStart, $dayEnd])->sum('amount')
                + (float) PosShiftMovement::where('type', 'cash_out')->whereBetween('created_at', [$dayStart, $dayEnd])->sum('amount')
                + (float) Order::where('status', 'refunded')->whereBetween('updated_at', [$dayStart, $dayEnd])->sum('total');

            $dayNet = $dayIn - $dayOut;
            $runningCumulative += $dayNet;

            $dailyBreakdown[] = [
                'date'       => $day->format('Y-m-d'),
                'day'        => $day->format('D, d M'),
                'inflows'    => $dayIn,
                'outflows'   => $dayOut,
                'net'        => $dayNet,
                'cumulative' => $runningCumulative,
            ];
        }

        // Reverse to show latest first in table
        $dailyBreakdownTable = array_reverse($dailyBreakdown);

        return [
            'period'               => $period,
            'startDate'            => $startDate,
            'endDate'              => $endDate,
            'totalInflows'         => $totalInflows,
            'totalOutflows'        => $totalOutflows,
            'netCashFlow'          => $netCashFlow,
            'posCashSales'         => $posCashSales,
            'onlinePaidSales'      => $onlinePaidSales,
            'posPayIns'            => $posPayIns,
            'b2bCollected'         => $b2bCollected,
            'expensesOutflow'      => $expensesOutflow,
            'supplierPayments'     => $supplierPayments,
            'posPayOuts'           => $posPayOuts,
            'refundsOutflow'       => $refundsOutflow,
            'cashDrawerLiquidity'  => $cashDrawerLiquidity,
            'bankAccountLiquidity' => $bankAccountLiquidity,
            'dailyBreakdown'       => $dailyBreakdownTable,
            'chartPoints'          => $dailyBreakdown, // chronological for charts
        ];
    }
}

