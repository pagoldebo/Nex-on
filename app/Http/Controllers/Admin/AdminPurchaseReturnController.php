<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use App\Models\PurchaseOrder;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnItem;
use App\Models\Setting;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminPurchaseReturnController extends Controller
{
    /**
     * Display listing of purchase returns & debit notes.
     */
    public function index(Request $request): View
    {
        $query = PurchaseReturn::with(['supplier', 'purchaseOrder', 'creator', 'items']);

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->input('supplier_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('debit_note_number', 'like', "%{$search}%")
                  ->orWhereHas('supplier', fn ($sq) => $sq->where('name', 'like', "%{$search}%"));
            });
        }

        $returns = $query->latest('return_date')->paginate(15)->withQueryString();

        $totalReturnsValue = (float) PurchaseReturn::where('status', '!=', 'cancelled')->sum('total_amount');
        $totalReturnsCount = PurchaseReturn::count();
        $creditedCount = PurchaseReturn::where('status', 'credited')->count();

        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        $variants = ProductVariant::with('product')->orderBy('sku')->get();
        $purchaseOrders = PurchaseOrder::with('supplier')->latest()->get();

        return view('admin.purchases.returns', compact(
            'returns',
            'suppliers',
            'variants',
            'purchaseOrders',
            'totalReturnsValue',
            'totalReturnsCount',
            'creditedCount'
        ));
    }

    /**
     * Store a newly created purchase return & generate GST debit note.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'supplier_id'       => 'required|exists:suppliers,id',
            'purchase_order_id' => 'nullable|exists:purchase_orders,id',
            'return_date'       => 'required|date',
            'reason'            => 'required|string|max:150',
            'notes'             => 'nullable|string|max:500',
            'items'             => 'required|array|min:1',
            'items.*.variant_id'=> 'required|exists:product_variants,id',
            'items.*.quantity'  => 'required|integer|min:1',
            'items.*.unit_cost' => 'required|numeric|min:0',
            'items.*.reason'    => 'nullable|string|max:150',
        ]);

        return DB::transaction(function () use ($validated) {
            $subtotal = 0;
            $itemsData = [];

            foreach ($validated['items'] as $itemInput) {
                $variant = ProductVariant::with('product')->findOrFail($itemInput['variant_id']);
                $qty = (int) $itemInput['quantity'];
                $unitCost = (float) $itemInput['unit_cost'];
                $lineTotal = round($qty * $unitCost, 2);
                $subtotal += $lineTotal;

                $itemsData[] = [
                    'product_variant_id' => $variant->id,
                    'product_name'       => $variant->product->name,
                    'sku'                => $variant->sku,
                    'quantity'           => $qty,
                    'unit_cost'          => $unitCost,
                    'line_total'         => $lineTotal,
                    'reason'             => $itemInput['reason'] ?? $validated['reason'],
                ];
            }

            // Assume standard 18% GST or 0 based on store tax
            $taxAmount = round($subtotal * 0.18, 2);
            $totalAmount = $subtotal + $taxAmount;

            $purchaseReturn = PurchaseReturn::create([
                'purchase_order_id' => $validated['purchase_order_id'] ?? null,
                'supplier_id'       => $validated['supplier_id'],
                'created_by'        => Auth::id(),
                'return_date'       => $validated['return_date'],
                'reason'            => $validated['reason'],
                'status'            => 'approved',
                'subtotal'          => $subtotal,
                'tax_amount'        => $taxAmount,
                'total_amount'      => $totalAmount,
                'notes'             => $validated['notes'] ?? null,
            ]);

            foreach ($itemsData as $itemRow) {
                $purchaseReturn->items()->create($itemRow);

                // Adjust stock decrement for returned inventory
                /** @var ProductVariant $variant */
                $variant = ProductVariant::find($itemRow['product_variant_id']);
                if ($variant) {
                    $variant->adjustStock(
                        -$itemRow['quantity'],
                        'purchase_return',
                        "Purchase Return Debit Note #{$purchaseReturn->debit_note_number} to supplier",
                        Auth::id()
                    );
                }
            }

            return redirect()->route('admin.purchase-returns.index')
                ->with('success', "Debit Note #{$purchaseReturn->debit_note_number} created and inventory deducted successfully.");
        });
    }

    /**
     * Printable GST Debit Note Voucher.
     */
    public function printDebitNote(PurchaseReturn $purchaseReturn): View
    {
        $purchaseReturn->load(['supplier', 'purchaseOrder', 'creator', 'items.variant.product']);

        $storeInfo = [
            'name'    => Setting::get('store_name', config('app.name', 'NEXON LUXURY RETAIL PVT LTD')),
            'address' => Setting::get('store_address', '101, Luxury Galleria, Indiranagar, Bengaluru'),
            'gstin'   => Setting::get('store_gstin', '29AAAAA0000A1Z5'),
            'phone'   => Setting::get('store_phone', '+91 98765 43210'),
            'email'   => Setting::get('store_email', 'accounts@nexon.luxury'),
        ];

        return view('admin.purchases.debit-note', compact('purchaseReturn', 'storeInfo'));
    }

    /**
     * Update return workflow status.
     */
    public function updateStatus(Request $request, PurchaseReturn $purchaseReturn): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,approved,dispatched,credited,cancelled',
        ]);

        $purchaseReturn->update(['status' => $validated['status']]);

        return redirect()->route('admin.purchase-returns.index')
            ->with('success', "Debit Note #{$purchaseReturn->debit_note_number} status updated to " . strtoupper($validated['status']) . '.');
    }
}

