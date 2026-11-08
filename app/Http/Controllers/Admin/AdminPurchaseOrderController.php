<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryLog;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Setting;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminPurchaseOrderController extends Controller
{
    /**
     * Display list of Purchase Orders and procurement KPIs.
     */
    public function index(Request $request): View
    {
        $status = $request->get('status', 'all');
        $supplierId = $request->get('supplier_id');
        $search = trim($request->get('q', ''));

        $query = PurchaseOrder::with(['supplier', 'creator', 'items.variant.product'])->latest();

        if ($status !== 'all' && in_array($status, [
            PurchaseOrder::STATUS_DRAFT,
            PurchaseOrder::STATUS_SENT,
            PurchaseOrder::STATUS_PARTIAL,
            PurchaseOrder::STATUS_RECEIVED,
            PurchaseOrder::STATUS_CANCELLED,
        ])) {
            $query->where('status', $status);
        }

        if (!empty($supplierId)) {
            $query->where('supplier_id', $supplierId);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('po_number', 'LIKE', "%{$search}%")
                  ->orWhereHas('supplier', function ($sq) use ($search) {
                      $sq->where('name', 'LIKE', "%{$search}%")
                         ->orWhere('code', 'LIKE', "%{$search}%");
                  });
            });
        }

        $purchaseOrders = $query->paginate(15)->withQueryString();

        // Procurement KPIs
        $lowStockVariantsCount = ProductVariant::lowStock()->count();
        $openPosCount = PurchaseOrder::whereIn('status', [PurchaseOrder::STATUS_DRAFT, PurchaseOrder::STATUS_SENT, PurchaseOrder::STATUS_PARTIAL])->count();
        $receivedThisMonth = PurchaseOrder::where('status', PurchaseOrder::STATUS_RECEIVED)->whereMonth('updated_at', now()->month)->count();
        $totalSpend = PurchaseOrder::whereIn('status', [PurchaseOrder::STATUS_SENT, PurchaseOrder::STATUS_PARTIAL, PurchaseOrder::STATUS_RECEIVED])->sum('total_amount');

        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();

        return view('admin.purchase-orders.index', compact(
            'purchaseOrders',
            'suppliers',
            'status',
            'search',
            'supplierId',
            'lowStockVariantsCount',
            'openPosCount',
            'receivedThisMonth',
            'totalSpend'
        ));
    }

    /**
     * Show form to manually draft a new Purchase Order.
     */
    public function create(): View
    {
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        
        // Variants that are low or zero stock for fast selection
        $lowStockVariants = ProductVariant::with(['product', 'color', 'supplier'])
            ->lowStock()
            ->orderBy('stock')
            ->take(30)
            ->get();

        $allVariants = ProductVariant::with(['product', 'color'])
            ->latest()
            ->take(100)
            ->get();

        $poNumber = PurchaseOrder::generatePoNumber();

        return view('admin.purchase-orders.create', compact('suppliers', 'lowStockVariants', 'allVariants', 'poNumber'));
    }

    /**
     * Store drafted Purchase Order.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'supplier_id'            => 'required|exists:suppliers,id',
            'po_number'              => 'required|string|unique:purchase_orders,po_number',
            'expected_delivery_date' => 'nullable|date',
            'notes'                  => 'nullable|string|max:500',
            'items'                  => 'required|array|min:1',
            'items.*.variant_id'     => 'required|exists:product_variants,id',
            'items.*.ordered_qty'    => 'required|integer|min:1',
            'items.*.unit_cost'      => 'required|numeric|min:0',
        ]);

        return DB::transaction(function () use ($validated) {
            $totalAmount = 0;
            $itemsData = [];

            foreach ($validated['items'] as $item) {
                $qty = (int) $item['ordered_qty'];
                $cost = (int) $item['unit_cost'];
                $lineTotal = $qty * $cost;
                $totalAmount += $lineTotal;

                $itemsData[] = [
                    'product_variant_id' => $item['variant_id'],
                    'ordered_qty'        => $qty,
                    'received_qty'       => 0,
                    'unit_cost'          => $cost,
                    'line_total'         => $lineTotal,
                ];
            }

            $po = PurchaseOrder::create([
                'po_number'              => $validated['po_number'],
                'supplier_id'            => $validated['supplier_id'],
                'created_by'             => Auth::id(),
                'status'                 => PurchaseOrder::STATUS_DRAFT,
                'total_amount'           => $totalAmount,
                'expected_delivery_date' => $validated['expected_delivery_date'] ?? now()->addDays(7),
                'notes'                  => $validated['notes'] ?? null,
            ]);

            foreach ($itemsData as $item) {
                $po->items()->create($item);
            }

            return redirect()->route('admin.purchase-orders.show', $po)
                ->with('success', "Purchase Order #{$po->po_number} drafted successfully.");
        });
    }

    /**
     * Show PO details, item fulfillment status, and GRN receipt console.
     */
    public function show(PurchaseOrder $purchaseOrder): View
    {
        $purchaseOrder->load(['supplier', 'creator', 'items.variant.product', 'items.variant.color']);

        return view('admin.purchase-orders.show', compact('purchaseOrder'));
    }

    /**
     * Goods Received Note (GRN) workflow.
     * Receiving items increments product_variants.stock and creates audit inventory logs.
     */
    public function receiveGoods(PurchaseOrder $purchaseOrder, Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'received'   => 'required|array',
            'received.*' => 'nullable|integer|min:0',
            'grn_notes'  => 'nullable|string|max:255',
        ]);

        return DB::transaction(function () use ($purchaseOrder, $validated) {
            $totalOrdered = 0;
            $totalReceived = 0;

            foreach ($purchaseOrder->items as $item) {
                $itemId = $item->id;
                $newReceiptQty = (int) ($validated['received'][$itemId] ?? 0);

                if ($newReceiptQty > 0) {
                    // Update item received count
                    $item->received_qty += $newReceiptQty;
                    $item->save();

                    // Adjust physical inventory & log movement
                    $variant = $item->variant;
                    if ($variant) {
                        $variant->adjustStock(
                            $newReceiptQty,
                            'purchase_order_receipt',
                            "GRN PO #{$purchaseOrder->po_number}: " . ($validated['grn_notes'] ?? 'Stock batch receipt'),
                            Auth::id()
                        );
                    }
                }

                $totalOrdered += $item->ordered_qty;
                $totalReceived += $item->received_qty;
            }

            // Update status based on overall completion
            if ($totalReceived >= $totalOrdered) {
                $purchaseOrder->status = PurchaseOrder::STATUS_RECEIVED;
            } elseif ($totalReceived > 0) {
                $purchaseOrder->status = PurchaseOrder::STATUS_PARTIAL;
            }
            $purchaseOrder->save();

            return redirect()->route('admin.purchase-orders.show', $purchaseOrder)
                ->with('success', "Goods Received Note recorded! Inventory stock updated and logged successfully.");
        });
    }

    /**
     * Update PO status (e.g. Sent, Cancelled, Draft).
     */
    public function updateStatus(PurchaseOrder $purchaseOrder, Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:draft,sent,partial,received,cancelled',
        ]);

        $purchaseOrder->status = $validated['status'];
        $purchaseOrder->save();

        return back()->with('success', "PO #{$purchaseOrder->po_number} status updated to " . ucfirst($purchaseOrder->status));
    }

    /**
     * 1-Click Auto-Generate Purchase Orders from Low-Stock Thresholds.
     */
    public function autoGenerate(): RedirectResponse
    {
        // 1. Ensure at least one default supplier exists
        $defaultSupplier = Supplier::firstOrCreate(
            ['code' => 'SUP-DEFAULT'],
            [
                'name'           => 'Apex Textiles & Garments Ltd',
                'contact_name'   => 'Rajesh Singhania',
                'email'          => 'procurement@apextextiles.com',
                'phone'          => '+91 98200 11223',
                'lead_time_days' => 5,
                'gst_number'     => '27AAACA1234F1Z9',
                'address'        => 'Plot 45, Textile SEZ, Surat, Gujarat - 395001',
                'is_active'      => true,
            ]
        );

        // 2. Fetch variants breaching safety thresholds
        $breachedVariants = ProductVariant::with(['product', 'supplier'])
            ->lowStock()
            ->get();

        if ($breachedVariants->isEmpty()) {
            return redirect()->route('admin.purchase-orders.index')
                ->with('info', 'All SKUs are currently well-stocked above their safety thresholds. No POs required.');
        }

        // Group by supplier
        $grouped = $breachedVariants->groupBy(function ($v) use ($defaultSupplier) {
            return $v->supplier_id ?? $defaultSupplier->id;
        });

        $poCount = 0;
        $itemsCount = 0;

        DB::transaction(function () use ($grouped, &$poCount, &$itemsCount) {
            foreach ($grouped as $supplierId => $variants) {
                $supplier = Supplier::find($supplierId);
                $poNumber = PurchaseOrder::generatePoNumber() . '-' . rand(10, 99);

                $po = PurchaseOrder::create([
                    'po_number'              => $poNumber,
                    'supplier_id'            => $supplierId,
                    'created_by'             => Auth::id(),
                    'status'                 => PurchaseOrder::STATUS_DRAFT,
                    'total_amount'           => 0,
                    'expected_delivery_date' => now()->addDays($supplier->lead_time_days ?? 7),
                    'notes'                  => 'Automated replenishment draft generated from low safety stock thresholds.',
                ]);

                $totalAmount = 0;
                foreach ($variants as $variant) {
                    $orderQty = $variant->reorder_qty > 0 ? $variant->reorder_qty : 20;
                    $unitCost = (int) ($variant->cost_price ?? round($variant->price * 0.45));
                    $lineTotal = $orderQty * $unitCost;
                    $totalAmount += $lineTotal;

                    $po->items()->create([
                        'product_variant_id' => $variant->id,
                        'ordered_qty'        => $orderQty,
                        'received_qty'       => 0,
                        'unit_cost'          => $unitCost,
                        'line_total'         => $lineTotal,
                    ]);

                    $itemsCount++;
                }

                $po->total_amount = $totalAmount;
                $po->save();
                $poCount++;
            }
        });

        return redirect()->route('admin.purchase-orders.index')
            ->with('success', "Auto-Replenishment Success: Generated {$poCount} drafted Purchase Order(s) covering {$itemsCount} depleted SKU items!");
    }

    /**
     * Printable Purchase Order Slip for suppliers.
     */
    public function printSlip(PurchaseOrder $purchaseOrder): View
    {
        $purchaseOrder->load(['supplier', 'creator', 'items.variant.product', 'items.variant.color']);

        $storeInfo = [
            'name'    => Setting::get('store_name', config('app.name', 'NEXON CMS STORE')),
            'phone'   => Setting::get('store_phone', '+91 98765 43210'),
            'address' => Setting::get('store_address', '101, Central Galleria, Indiranagar, Bengaluru, KA - 560038'),
            'gstin'   => Setting::get('store_gstin', '29AAAAA0000A1Z5'),
            'email'   => Setting::get('store_email', 'procurement@nexon.com'),
        ];

        return view('admin.purchase-orders.print', compact('purchaseOrder', 'storeInfo'));
    }

    /**
     * Suppliers directory view.
     */
    public function suppliersIndex(): View
    {
        $suppliers = Supplier::withCount(['variants', 'purchaseOrders'])->latest()->paginate(15);
        return view('admin.purchase-orders.suppliers', compact('suppliers'));
    }

    /**
     * Store new supplier.
     */
    public function storeSupplier(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:150',
            'code'           => 'required|string|max:50|unique:suppliers,code',
            'contact_name'   => 'nullable|string|max:120',
            'email'          => 'nullable|email|max:150',
            'phone'          => 'nullable|string|max:30',
            'lead_time_days' => 'nullable|integer|min:1',
            'gst_number'     => 'nullable|string|max:30',
            'address'        => 'nullable|string|max:500',
        ]);

        Supplier::create($validated);

        return back()->with('success', 'Supplier created successfully.');
    }
}
