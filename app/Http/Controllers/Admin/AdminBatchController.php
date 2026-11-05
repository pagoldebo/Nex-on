<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductBatch;
use App\Models\ProductVariant;
use App\Services\BatchInventoryService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminBatchController extends Controller
{
    protected BatchInventoryService $batchService;

    public function __construct(BatchInventoryService $batchService)
    {
        $this->batchService = $batchService;
    }

    /**
     * Display batches and expiration monitor.
     */
    public function index(Request $request): View
    {
        $filter = $request->input('status', 'all');
        $query = ProductBatch::with(['variant.product', 'variant.color'])->latest('expiry_date');

        $today = Carbon::today();

        if ($filter === 'expired') {
            $query->where('expiry_date', '<', $today);
        } elseif ($filter === 'expiring_30d') {
            $query->whereBetween('expiry_date', [$today, $today->copy()->addDays(30)]);
        } elseif ($filter === 'expiring_60d') {
            $query->whereBetween('expiry_date', [$today, $today->copy()->addDays(60)]);
        } elseif ($filter === 'expiring_90d') {
            $query->whereBetween('expiry_date', [$today, $today->copy()->addDays(90)]);
        } elseif ($filter === 'healthy') {
            $query->where('expiry_date', '>', $today->copy()->addDays(90));
        }

        $batches = $query->paginate(20)->withQueryString();

        // Calculate summary KPI stats
        $allBatches = ProductBatch::all();
        $expiredCount   = $allBatches->filter(fn($b) => $b->expiry_date->isPast())->count();
        $expiring30d    = $allBatches->filter(fn($b) => !$b->expiry_date->isPast() && $b->days_until_expiry <= 30);
        $expiring30dCount = $expiring30d->count();
        $valueAtRisk30d = $expiring30d->sum(fn($b) => $b->remaining_qty * ($b->cost_price ?: 0));
        $totalActiveBatches = $allBatches->where('status', 'active')->count();
        $totalRemainingQty = $allBatches->sum('remaining_qty');

        $variants = ProductVariant::with(['product', 'color'])->get();

        return view('admin.inventory.batches', compact(
            'batches', 'filter', 'expiredCount', 'expiring30dCount',
            'valueAtRisk30d', 'totalActiveBatches', 'totalRemainingQty', 'variants'
        ));
    }

    /**
     * Store a new product batch.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_variant_id' => 'required|exists:product_variants,id',
            'batch_number'       => 'required|string|max:50',
            'lot_number'         => 'nullable|string|max:50',
            'mfg_date'           => 'nullable|date',
            'expiry_date'        => 'required|date',
            'initial_qty'        => 'required|integer|min:1',
            'cost_price'         => 'nullable|numeric|min:0',
            'sync_variant_stock' => 'nullable|boolean',
            'notes'              => 'nullable|string|max:500',
        ]);

        $this->batchService->createBatch($validated['product_variant_id'], $validated);

        return redirect()->route('admin.inventory.batches.index')
            ->with('success', "Batch {$validated['batch_number']} registered successfully.");
    }

    /**
     * Batch Recall & Traceability Report.
     */
    public function trace(ProductBatch $batch): View
    {
        $traceData = $this->batchService->traceBatch($batch->id);

        return view('admin.inventory.batch-trace', $traceData);
    }
}
