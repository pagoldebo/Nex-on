<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\CustomerSegmentationService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminCustomerSegmentController extends Controller
{
    public function __construct(
        protected CustomerSegmentationService $segmentationService
    ) {}

    /**
     * Display RFM Customer Segmentation Dashboard.
     */
    public function index(Request $request): View
    {
        $selectedSegment = $request->input('segment', 'all');
        $search = $request->input('search');

        $distribution = $this->segmentationService->getSegmentDistribution();
        $customers = $this->segmentationService->getCustomerMetrics($selectedSegment, $search);

        return view('admin.customers.segments', [
            'distribution'    => $distribution,
            'customers'       => $customers,
            'selectedSegment' => $selectedSegment,
            'search'          => $search,
        ]);
    }

    /**
     * Export segment customers as CSV.
     */
    public function export(Request $request): StreamedResponse
    {
        $selectedSegment = $request->input('segment', 'all');
        $search = $request->input('search');
        $customers = $this->segmentationService->getCustomerMetrics($selectedSegment, $search);

        $filename = 'rfm-customer-segments-' . ($selectedSegment ?: 'all') . '-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($customers) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Customer ID',
                'Name',
                'Email',
                'Segment',
                'Orders Count (Frequency)',
                'Total Spend (Monetary INR)',
                'Days Since Last Order (Recency)',
                'Latest Order Date',
                'Recommended Marketing Action',
            ]);

            foreach ($customers as $row) {
                fputcsv($handle, [
                    $row->id,
                    $row->name,
                    $row->email,
                    $row->segment['name'],
                    $row->frequency,
                    number_format($row->monetary, 2, '.', ''),
                    $row->recency_days ?? 'N/A (No orders)',
                    $row->latest_order ? $row->latest_order->format('Y-m-d H:i') : 'Never',
                    $row->segment['action'],
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}

