<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Outlet;
use App\Models\OutletStock;
use App\Models\ProductVariant;
use App\Models\StockTransfer;
use App\Services\OutletInventoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminOutletController extends Controller
{
    public function __construct(
        protected OutletInventoryService $inventoryService
    ) {}

    /**
     * Display all physical outlets and warehouses.
     */
    public function index(Request $request): View
    {
        $outlets = Outlet::withCount('stocks')
            ->when($request->query('type'), fn($q, $type) => $q->where('type', $type))
            ->when($request->query('search'), fn($q, $term) => $q->where(function ($sub) use ($term) {
                $sub->where('name', 'like', "%{$term}%")
                    ->orWhere('code', 'like', "%{$term}%")
                    ->orWhere('city', 'like', "%{$term}%");
            }))
            ->latest()
            ->paginate(15);

        $kpis = [
            'total_outlets' => Outlet::count(),
            'active_stores' => Outlet::where('type', 'store')->where('is_active', true)->count(),
            'warehouses'    => Outlet::where('type', 'warehouse')->count(),
            'total_units'   => (int) OutletStock::sum('stock'),
        ];

        return view('admin.inventory.outlets.index', compact('outlets', 'kpis'));
    }

    /**
     * Store new outlet or warehouse.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:150',
            'code'       => 'required|string|max:50|unique:outlets,code',
            'type'       => 'required|in:store,warehouse,outlet',
            'address'    => 'nullable|string|max:255',
            'city'       => 'nullable|string|max:100',
            'phone'      => 'nullable|string|max:30',
            'email'      => 'nullable|email|max:150',
            'channel'    => 'required|in:all,online,pos,wholesale',
            'is_central' => 'nullable|boolean',
            'is_active'  => 'nullable|boolean',
        ]);

        $validated['is_central'] = $request->has('is_central');
        $validated['is_active']  = $request->has('is_active');

        Outlet::create($validated);

        return back()->with('success', "Branch/Outlet {$validated['name']} registered successfully!");
    }

    /**
     * Show inventory stock details for an outlet.
     */
    public function show(Request $request, Outlet $outlet): View
    {
        $stocks = OutletStock::with('variant.product')
            ->where('outlet_id', $outlet->id)
            ->when($request->query('search'), function ($q, $term) {
                $q->whereHas('variant', function ($sub) use ($term) {
                    $sub->where('sku', 'like', "%{$term}%")
                        ->orWhereHas('product', fn($p) => $p->where('name', 'like', "%{$term}%"));
                });
            })
            ->paginate(20);

        $variants = ProductVariant::with('product')
            ->whereHas('product', fn($q) => $q->where('is_active', true))
            ->orderBy('sku')
            ->take(100)
            ->get();

        return view('admin.inventory.outlets.show', compact('outlet', 'stocks', 'variants'));
    }

    /**
     * Adjust stock level for a product variant at an outlet.
     */
    public function adjustStock(Request $request, Outlet $outlet): RedirectResponse
    {
        $request->validate([
            'product_variant_id' => 'required|exists:product_variants,id',
            'stock_delta'        => 'required|integer',
            'shelf_bin_location' => 'nullable|string|max:100',
        ]);

        $variant = ProductVariant::findOrFail($request->input('product_variant_id'));
        $this->inventoryService->adjustStock(
            $outlet,
            $variant,
            (int) $request->input('stock_delta'),
            $request->input('shelf_bin_location')
        );

        return back()->with('success', "Stock updated for SKU: {$variant->sku} at {$outlet->name}");
    }

    /**
     * List all inter-outlet stock transfers.
     */
    public function transfersIndex(Request $request): View
    {
        $transfers = StockTransfer::with(['fromOutlet', 'toOutlet', 'dispatchedBy', 'items'])
            ->when($request->query('status'), fn($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(15);

        $outlets = Outlet::active()->get();

        return view('admin.inventory.transfers.index', compact('transfers', 'outlets'));
    }

    /**
     * Show create transfer screen.
     */
    public function transfersCreate(): View
    {
        $outlets = Outlet::active()->orderBy('name')->get();
        $variants = ProductVariant::with('product')
            ->whereHas('product', fn($q) => $q->where('is_active', true))
            ->orderBy('sku')
            ->get();

        return view('admin.inventory.transfers.create', compact('outlets', 'variants'));
    }

    /**
     * Store inter-store stock transfer.
     */
    public function transfersStore(Request $request): RedirectResponse
    {
        $request->validate([
            'from_outlet_id' => 'required|exists:outlets,id',
            'to_outlet_id'   => 'required|exists:outlets,id|different:from_outlet_id',
            'notes'          => 'nullable|string|max:1000',
            'items'          => 'required|array|min:1',
            'items.*.variant_id' => 'required|exists:product_variants,id',
            'items.*.qty'        => 'required|integer|min:1',
        ]);

        $from = Outlet::findOrFail($request->input('from_outlet_id'));
        $to   = Outlet::findOrFail($request->input('to_outlet_id'));

        $transfer = $this->inventoryService->createTransfer(
            $from,
            $to,
            $request->input('items'),
            $request->input('notes'),
            $request->user()
        );

        return redirect()->route('admin.transfers.show', $transfer)
            ->with('success', "Draft Transfer #{$transfer->transfer_no} initiated.");
    }

    /**
     * Display transfer details and printable manifest slip.
     */
    public function transfersShow(StockTransfer $transfer): View
    {
        $transfer->load(['fromOutlet', 'toOutlet', 'dispatchedBy', 'receivedBy', 'items.variant.product']);
        return view('admin.inventory.transfers.show', compact('transfer'));
    }

    /**
     * Dispatch stock transfer.
     */
    public function transfersDispatch(Request $request, StockTransfer $transfer): RedirectResponse
    {
        try {
            $this->inventoryService->dispatchTransfer($transfer, $request->user());
            return back()->with('success', "Transfer #{$transfer->transfer_no} marked as DISPATCHED. Stock deducted from {$transfer->fromOutlet->name}.");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Receive stock transfer and stock into destination warehouse.
     */
    public function transfersReceive(Request $request, StockTransfer $transfer): RedirectResponse
    {
        $receivedMap = $request->input('received_qty', []);

        try {
            $this->inventoryService->receiveTransfer($transfer, $receivedMap, $request->user());
            return back()->with('success', "Transfer #{$transfer->transfer_no} successfully RECEIVED and stocked into {$transfer->toOutlet->name}.");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
