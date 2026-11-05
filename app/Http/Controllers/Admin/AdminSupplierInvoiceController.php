<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\SupplierInvoice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminSupplierInvoiceController extends Controller
{
    /**
     * Display listing of supplier invoices and 3-way matching ledger.
     */
    public function index(Request $request): View
    {
        $query = SupplierInvoice::with(['supplier', 'purchaseOrder.items', 'payments']);

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->input('supplier_id'));
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->input('payment_status'));
        }

        if ($request->filled('matching_status')) {
            $query->where('matching_status', $request->input('matching_status'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('supplier', fn ($sq) => $sq->where('name', 'like', "%{$search}%"));
            });
        }

        $invoices = $query->latest('invoice_date')->paginate(15)->withQueryString();

        // Financial KPIs
        $totalBilled = (float) SupplierInvoice::sum('total_amount');
        $totalPaid = (float) SupplierInvoice::sum('amount_paid');
        $totalOutstanding = max(0, $totalBilled - $totalPaid);
        $varianceCount = SupplierInvoice::where('matching_status', 'variance_detected')->count();

        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        $purchaseOrders = PurchaseOrder::with('supplier')->latest()->get();

        return view('admin.purchases.supplier-invoices', compact(
            'invoices',
            'suppliers',
            'purchaseOrders',
            'totalBilled',
            'totalPaid',
            'totalOutstanding',
            'varianceCount'
        ));
    }

    /**
     * Store a new supplier invoice and perform automated 3-way matching against PO.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'purchase_order_id' => 'required|exists:purchase_orders,id',
            'supplier_id'       => 'required|exists:suppliers,id',
            'invoice_number'    => 'required|string|max:100',
            'invoice_date'      => 'required|date',
            'due_date'          => 'required|date|after_or_equal:invoice_date',
            'subtotal'          => 'required|numeric|min:0',
            'tax_amount'        => 'nullable|numeric|min:0',
            'total_amount'      => 'required|numeric|min:0',
            'notes'             => 'nullable|string|max:500',
        ]);

        $po = PurchaseOrder::with('items')->findOrFail($validated['purchase_order_id']);

        // Check if invoice_number already exists for this supplier
        if (SupplierInvoice::where('supplier_id', $validated['supplier_id'])->where('invoice_number', $validated['invoice_number'])->exists()) {
            return back()->withErrors(['invoice_number' => 'An invoice with this number already exists for this supplier.'])->withInput();
        }

        // Automated 3-Way Matching Check:
        // PO Total vs Invoiced Total
        $poTotal = (float) $po->total_amount;
        $invoicedTotal = (float) $validated['total_amount'];
        $variance = round($invoicedTotal - $poTotal, 2);

        $matchingStatus = 'matched';
        $varianceNotes = null;

        if (abs($variance) > 0.50) {
            $matchingStatus = 'variance_detected';
            $varianceNotes = "Price variance detected: Invoiced ₹{$invoicedTotal} vs Purchase Order ₹{$poTotal} (Diff: ₹{$variance})";
        }

        $invoice = SupplierInvoice::create([
            'purchase_order_id' => $validated['purchase_order_id'],
            'supplier_id'       => $validated['supplier_id'],
            'invoice_number'    => $validated['invoice_number'],
            'invoice_date'      => $validated['invoice_date'],
            'due_date'          => $validated['due_date'],
            'subtotal'          => $validated['subtotal'],
            'tax_amount'        => $validated['tax_amount'] ?? 0,
            'total_amount'      => $validated['total_amount'],
            'amount_paid'       => 0,
            'payment_status'    => 'unpaid',
            'matching_status'   => $matchingStatus,
            'po_variance'       => $variance,
            'notes'             => $varianceNotes ? ($validated['notes'] ? $validated['notes'] . " | " . $varianceNotes : $varianceNotes) : ($validated['notes'] ?? null),
        ]);

        return redirect()->route('admin.supplier-invoices.index')
            ->with('success', "Supplier invoice #{$invoice->invoice_number} recorded. 3-Way matching status: " . strtoupper(str_replace('_', ' ', $matchingStatus)));
    }

    /**
     * Record a payment against a supplier invoice.
     */
    public function recordPayment(Request $request, SupplierInvoice $supplierInvoice): RedirectResponse
    {
        $validated = $request->validate([
            'amount'                => 'required|numeric|min:0.01',
            'payment_method'        => 'required|string|in:bank_transfer,cheque,cash,upi',
            'transaction_reference' => 'nullable|string|max:100',
            'paid_at'               => 'required|date',
            'notes'                 => 'nullable|string|max:255',
        ]);

        $supplierInvoice->recordPayment(
            (float) $validated['amount'],
            $validated['payment_method'],
            $validated['transaction_reference'] ?? null,
            $validated['paid_at'],
            $validated['notes'] ?? null
        );

        return redirect()->route('admin.supplier-invoices.index')
            ->with('success', 'Payment of ₹' . number_format($validated['amount'], 2) . ' recorded successfully.');
    }

    /**
     * Delete a supplier invoice.
     */
    public function destroy(SupplierInvoice $supplierInvoice): RedirectResponse
    {
        $supplierInvoice->delete();

        return redirect()->route('admin.supplier-invoices.index')
            ->with('success', 'Supplier invoice deleted.');
    }
}

