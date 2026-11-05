<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommercialInvoice;
use App\Models\CommercialInvoiceItem;
use App\Models\ProductVariant;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminCommercialInvoiceController extends Controller
{
    /**
     * Display B2B commercial invoices, credit notes directory, and KPI metrics.
     */
    public function index(Request $request): View
    {
        $type = $request->input('type');
        $status = $request->input('status');
        $search = $request->input('search');

        $query = CommercialInvoice::with(['items', 'creator']);

        if ($type && $type !== 'all') {
            $query->where('type', $type);
        }

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('client_name', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhere('client_gstin', 'like', "%{$search}%");
            });
        }

        $invoices = $query->orderBy('invoice_date', 'desc')->orderBy('id', 'desc')->paginate(15)->withQueryString();

        // High-level summary metrics
        $allInvoices = CommercialInvoice::all();
        $totalInvoiced = $allInvoices->where('type', 'tax_invoice')->sum('total_amount');
        $totalCollected = $allInvoices->sum('amount_paid');
        $totalOutstanding = $allInvoices->where('type', 'tax_invoice')->whereIn('status', ['draft', 'issued', 'partial'])->sum(fn($i) => $i->balance_due);
        $totalCreditNotes = $allInvoices->where('type', 'credit_note')->sum('total_amount');

        return view('admin.invoices.index', compact(
            'invoices',
            'type',
            'status',
            'search',
            'totalInvoiced',
            'totalCollected',
            'totalOutstanding',
            'totalCreditNotes'
        ));
    }

    /**
     * Show the invoice builder studio.
     */
    public function create(): View
    {
        $clients = User::where('role', 'customer')->orderBy('name')->get();
        $variants = ProductVariant::with(['product', 'color'])->whereHas('product', fn($q) => $q->where('is_active', true))->orderBy('sku')->get();
        $nextTaxNumber = CommercialInvoice::generateInvoiceNumber('tax_invoice');
        $nextCnNumber = CommercialInvoice::generateInvoiceNumber('credit_note');

        return view('admin.invoices.create', compact('clients', 'variants', 'nextTaxNumber', 'nextCnNumber'));
    }

    /**
     * Store newly created Commercial Invoice or Credit Note.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type'                    => 'required|in:tax_invoice,credit_note,proforma',
            'invoice_date'            => 'required|date',
            'due_date'                => 'nullable|date',
            'client_name'             => 'required|string|max:150',
            'company_name'            => 'nullable|string|max:150',
            'client_gstin'            => 'nullable|string|max:50',
            'client_pan'              => 'nullable|string|max:50',
            'client_email'            => 'nullable|email|max:150',
            'client_phone'            => 'nullable|string|max:50',
            'billing_address'         => 'nullable|string|max:500',
            'shipping_address'        => 'nullable|string|max:500',
            'place_of_supply'         => 'nullable|string|max:100',
            'notes'                   => 'nullable|string|max:1000',
            'terms'                   => 'nullable|string|max:1000',
            'items'                   => 'required|array|min:1',
            'items.*.description'     => 'required|string|max:255',
            'items.*.hsn_code'        => 'nullable|string|max:50',
            'items.*.quantity'        => 'required|numeric|min:0.01',
            'items.*.unit'            => 'nullable|string|max:20',
            'items.*.unit_price'      => 'required|numeric|min:0',
            'items.*.discount_amount' => 'nullable|numeric|min:0',
            'items.*.tax_rate'        => 'required|numeric|min:0|max:100',
            'items.*.product_variant_id' => 'nullable|exists:product_variants,id',
        ]);

        $subtotal = 0.0;
        $totalDiscount = 0.0;
        $totalTax = 0.0;
        $grandTotal = 0.0;
        $processedItems = [];

        foreach ($validated['items'] as $rawItem) {
            $qty = (float) $rawItem['quantity'];
            $price = (float) $rawItem['unit_price'];
            $discount = (float) ($rawItem['discount_amount'] ?? 0);
            $taxRate = (float) $rawItem['tax_rate'];

            $lineNet = max(0, ($qty * $price) - $discount);
            $lineTax = $lineNet * ($taxRate / 100);
            $lineTotal = $lineNet + $lineTax;

            $subtotal += ($qty * $price);
            $totalDiscount += $discount;
            $totalTax += $lineTax;
            $grandTotal += $lineTotal;

            $processedItems[] = [
                'product_variant_id' => $rawItem['product_variant_id'] ?? null,
                'description'        => $rawItem['description'],
                'hsn_code'           => $rawItem['hsn_code'] ?? '621210',
                'quantity'           => $qty,
                'unit'               => $rawItem['unit'] ?? 'pcs',
                'unit_price'         => $price,
                'discount_amount'    => $discount,
                'tax_rate'           => $taxRate,
                'tax_amount'         => $lineTax,
                'total_amount'       => $lineTotal,
            ];
        }

        // Intra-state (Maharashtra) vs Inter-state (IGST)
        $placeOfSupply = trim($validated['place_of_supply'] ?? 'Maharashtra');
        $isLocalState = strcasecmp($placeOfSupply, 'Maharashtra') === 0;

        $cgst = $isLocalState ? round($totalTax / 2, 2) : 0.0;
        $sgst = $isLocalState ? round($totalTax / 2, 2) : 0.0;
        $igst = $isLocalState ? 0.0 : round($totalTax, 2);

        $invoice = CommercialInvoice::create([
            'type'             => $validated['type'],
            'invoice_number'   => CommercialInvoice::generateInvoiceNumber($validated['type']),
            'invoice_date'     => $validated['invoice_date'],
            'due_date'         => $validated['due_date'] ?? null,
            'client_name'      => $validated['client_name'],
            'company_name'     => $validated['company_name'] ?? null,
            'client_gstin'     => $validated['client_gstin'] ?? null,
            'client_pan'       => $validated['client_pan'] ?? null,
            'client_email'     => $validated['client_email'] ?? null,
            'client_phone'     => $validated['client_phone'] ?? null,
            'billing_address'  => $validated['billing_address'] ?? null,
            'shipping_address' => $validated['shipping_address'] ?? null,
            'place_of_supply'  => $placeOfSupply,
            'currency'         => 'INR',
            'subtotal'         => $subtotal,
            'discount_amount'  => $totalDiscount,
            'tax_amount'       => $totalTax,
            'cgst_amount'      => $cgst,
            'sgst_amount'      => $sgst,
            'igst_amount'      => $igst,
            'total_amount'     => $grandTotal,
            'amount_paid'      => 0,
            'status'           => 'issued',
            'notes'            => $validated['notes'] ?? null,
            'terms'            => $validated['terms'] ?? 'Payment due within 15 days of invoice date. Bank transfers are subject to realization.',
            'created_by'       => auth()->id(),
        ]);

        foreach ($processedItems as $item) {
            $invoice->items()->create($item);
        }

        return redirect()->route('admin.invoices.show', $invoice)
            ->with('success', "{$invoice->type_label} {$invoice->invoice_number} created successfully.");
    }

    /**
     * Display commercial invoice inspector.
     */
    public function show(CommercialInvoice $invoice): View
    {
        $invoice->load(['items.variant', 'creator']);

        return view('admin.invoices.show', compact('invoice'));
    }

    /**
     * Update invoice payment or status.
     */
    public function updateStatus(Request $request, CommercialInvoice $invoice): RedirectResponse
    {
        $validated = $request->validate([
            'status'          => 'nullable|in:draft,issued,paid,partial,cancelled',
            'record_payment'  => 'nullable|numeric|min:0.01',
        ]);

        if ($request->filled('record_payment')) {
            $newPaid = (float) $invoice->amount_paid + (float) $validated['record_payment'];
            $invoice->amount_paid = min((float) $invoice->total_amount, $newPaid);
            
            if ($invoice->amount_paid >= (float) $invoice->total_amount) {
                $invoice->status = 'paid';
            } else {
                $invoice->status = 'partial';
            }
            $invoice->save();

            return redirect()->route('admin.invoices.show', $invoice)
                ->with('success', 'Payment of ₹' . number_format($validated['record_payment'], 2) . ' recorded.');
        }

        if ($request->filled('status')) {
            $invoice->status = $validated['status'];
            if ($validated['status'] === 'paid') {
                $invoice->amount_paid = $invoice->total_amount;
            }
            $invoice->save();

            return redirect()->route('admin.invoices.show', $invoice)
                ->with('success', 'Invoice status updated to ' . ucfirst($invoice->status) . '.');
        }

        return redirect()->route('admin.invoices.show', $invoice);
    }

    /**
     * Formal printable B2B Tax Invoice & Credit Note view.
     */
    public function print(CommercialInvoice $invoice): View
    {
        $invoice->load(['items.variant', 'creator']);

        $storeName = Setting::appName();
        $gstin = Setting::gstin();
        $storeAddress = Setting::storeAddress();
        $supportPhone = Setting::supportPhone();
        $supportEmail = Setting::supportEmail();

        return view('admin.invoices.print', compact(
            'invoice',
            'storeName',
            'gstin',
            'storeAddress',
            'supportPhone',
            'supportEmail'
        ));
    }
}

