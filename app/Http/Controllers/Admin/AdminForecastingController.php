<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Supplier;
use App\Services\DemandForecastingService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminForecastingController extends Controller
{
    protected DemandForecastingService $forecastingService;

    public function __construct(DemandForecastingService $forecastingService)
    {
        $this->forecastingService = $forecastingService;
    }

    /**
     * Display Demand Forecasting & Inventory Velocity Dashboard.
     */
    public function index(Request $request): View
    {
        $filters = [
            'category_id' => $request->input('category_id'),
            'supplier_id' => $request->input('supplier_id'),
            'risk'        => $request->input('risk', 'all'),
            'sort'        => $request->input('sort', 'risk_asc'),
        ];

        $analysis = $this->forecastingService->analyze($filters);

        $categories = Category::orderBy('name')->get();
        $suppliers  = Supplier::where('is_active', true)->orderBy('name')->get();

        return view('admin.inventory.forecasting', [
            'items'      => $analysis['items'],
            'summary'    => $analysis['summary'],
            'filters'    => $filters,
            'categories' => $categories,
            'suppliers'  => $suppliers,
        ]);
    }

    /**
     * Export Demand Forecasting data to CSV.
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $filters = [
            'category_id' => $request->input('category_id'),
            'supplier_id' => $request->input('supplier_id'),
            'risk'        => $request->input('risk', 'all'),
            'sort'        => $request->input('sort', 'risk_asc'),
        ];

        $analysis = $this->forecastingService->analyze($filters);
        $filename = "inventory_forecasting_" . date('Ymd_His') . ".csv";

        return response()->streamDownload(function () use ($analysis) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF"); // UTF-8 BOM

            \App\Services\CsvExporter::writeRow($handle, [
                'SKU', 'Product Title', 'Category', 'Variant / Color',
                'Current Stock', 'Cost Price (₹)', 'Tied Up Capital (₹)',
                '7d Sold', '30d Sold', '90d Sold',
                'Daily Velocity (units/day)', 'Days Until Stockout',
                'Risk Category', 'Supplier', 'Lead Time (Days)', 'Recommended Reorder Qty'
            ]);

            foreach ($analysis['items'] as $item) {
                \App\Services\CsvExporter::writeRow($handle, [
                    $item['sku'],
                    $item['product_name'],
                    $item['category_name'],
                    trim("{$item['color_name']} {$item['size']}"),
                    $item['stock'],
                    $item['cost_price'],
                    round($item['capital_tied_up'], 2),
                    $item['units_sold_7d'],
                    $item['units_sold_30d'],
                    $item['units_sold_90d'],
                    $item['velocity_daily'],
                    $item['days_left'] === 999 ? 'No Depletion' : $item['days_left'],
                    strtoupper(str_replace('_', ' ', $item['risk'])),
                    $item['supplier_name'],
                    $item['lead_time_days'],
                    $item['recommended_reorder'],
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
