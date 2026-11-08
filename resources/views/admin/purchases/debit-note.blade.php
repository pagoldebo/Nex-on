{{--
    ======================================================================
    ADMIN MODULE: PURCHASES DEBIT NOTE
    ======================================================================
    File: resources/views/admin/purchases/debit-note.blade.php
    Responsibility: Back-office administration for PURCHASES DEBIT NOTE
    Features:
      - Standard administration UI following the Nex-on design guidelines
      - Accessible controls, Tailwind CSS styling, and reactive Alpine.js components
    Security & Access Control:
      - Guarded by Laravel authentication and RBAC permission middleware
    Operations & Maintenance:
      - Integrates with master administration shell (@extends('admin.layout'))
--}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GST Debit Note #{{ $purchaseReturn->debit_note_number }} - {{ $storeInfo['name'] }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif; background: #f8fafc; color: #1e293b; padding: 20px; }
        .voucher-card { max-width: 820px; margin: 0 auto; background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 36px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        .toolbar { max-width: 820px; margin: 0 auto 16px auto; display: flex; justify-content: space-between; align-items: center; }
        .btn { background: #4f46e5; color: #fff; padding: 8px 16px; border-radius: 6px; font-size: 13px; font-weight: 600; text-decoration: none; border: none; cursor: pointer; }
        .btn:hover { background: #4338ca; }
        .header { display: flex; justify-content: space-between; border-bottom: 2px solid #0f172a; padding-bottom: 20px; margin-bottom: 24px; }
        .title { font-size: 20px; font-weight: 900; text-transform: uppercase; color: #0f172a; letter-spacing: 0.5px; }
        .badge { display: inline-block; padding: 4px 10px; border-radius: 4px; font-size: 11px; font-weight: 800; text-transform: uppercase; margin-top: 6px; background: #fee2e2; color: #991b1b; }
        .parties { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px; }
        .party-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 14px; font-size: 12px; }
        .party-box h4 { font-size: 11px; text-transform: uppercase; color: #64748b; margin-bottom: 6px; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 24px; font-size: 12px; }
        .table th { background: #f1f5f9; padding: 10px 12px; text-align: left; font-weight: 700; border-bottom: 1px solid #cbd5e1; }
        .table td { padding: 10px 12px; border-bottom: 1px solid #f1f5f9; }
        .totals { margin-left: auto; width: 300px; font-size: 12px; }
        .totals-row { display: flex; justify-content: space-between; padding: 4px 0; }
        .totals-row.grand { border-top: 2px solid #0f172a; padding-top: 8px; font-weight: 900; font-size: 14px; color: #b91c1c; }
        .signatory { display: flex; justify-content: space-between; margin-top: 48px; padding-top: 24px; font-size: 11px; }
        .sign-box { width: 220px; border-top: 1px solid #94a3b8; text-align: center; padding-top: 6px; }
        @media print {
            body { background: #fff; padding: 0; }
            .toolbar { display: none; }
            .voucher-card { border: none; box-shadow: none; padding: 0; }
            @page { margin: 15mm; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <div><strong>Legal Tax Document: GST Debit Note</strong></div>
        <div style="display: flex; gap: 8px;">
            <button class="btn" onclick="window.print()">🖨️ Print Voucher</button>
            <button class="btn" style="background:#64748b;" onclick="window.close()">Close</button>
        </div>
    </div>

    <div class="voucher-card">
        <div class="header">
            <div>
                <h1 style="font-size: 22px; font-weight: 900; color: #0f172a;">{{ $storeInfo['name'] }}</h1>
                <p style="font-size: 12px; color: #64748b; margin-top: 4px;">{{ $storeInfo['address'] }}</p>
                <p style="font-size: 12px; color: #64748b;">GSTIN: <strong>{{ $storeInfo['gstin'] }}</strong> &bull; Phone: {{ $storeInfo['phone'] }}</p>
            </div>
            <div style="text-align: right;">
                <div class="title">DEBIT NOTE</div>
                <div class="badge">Original For Supplier</div>
                <p style="font-size: 12px; font-weight: 700; margin-top: 8px; font-family: monospace;">#{{ $purchaseReturn->debit_note_number }}</p>
                <p style="font-size: 11px; color: #64748b;">Date: {{ $purchaseReturn->return_date->format('d/m/Y') }}</p>
            </div>
        </div>

        <div class="parties">
            <div class="party-box">
                <h4>Issued To (Supplier):</h4>
                <div style="font-weight: 700; font-size: 13px;">{{ $purchaseReturn->supplier?->name }}</div>
                <div>GSTIN: <strong>{{ $purchaseReturn->supplier?->gst_number ?? 'UNREGISTERED' }}</strong></div>
                <div>Address: {{ $purchaseReturn->supplier?->address ?? 'On Record' }}</div>
                <div>Phone: {{ $purchaseReturn->supplier?->phone ?? 'N/A' }}</div>
            </div>
            <div class="party-box">
                <h4>Original Order Reference:</h4>
                <div>Original PO Number: <strong>{{ $purchaseReturn->purchaseOrder?->po_number ?? 'DIRECT SUPPLIER RETURN' }}</strong></div>
                <div>Return Nature: <strong style="text-transform: capitalize;">{{ str_replace('_', ' ', $purchaseReturn->reason) }}</strong></div>
                <div>Status: <strong style="text-transform: uppercase;">{{ $purchaseReturn->status }}</strong></div>
                <div>Generated By: {{ $purchaseReturn->creator?->name ?? 'Admin Staff' }}</div>
            </div>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Item Description &amp; SKU</th>
                    <th>Defect / Return Reason</th>
                    <th style="text-align: center;">Qty</th>
                    <th style="text-align: right;">Unit Rate</th>
                    <th style="text-align: right;">Total Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchaseReturn->items as $idx => $item)
                    <tr>
                        <td>{{ $idx + 1 }}</td>
                        <td>
                            <strong>{{ $item->product_name }}</strong>
                            <div style="font-size: 10px; color: #64748b; font-family: monospace;">SKU: {{ $item->sku }}</div>
                        </td>
                        <td style="color: #64748b;">{{ $item->reason ?: str_replace('_', ' ', $purchaseReturn->reason) }}</td>
                        <td style="text-align: center; font-weight: 700;">{{ $item->quantity }} pcs</td>
                        <td style="text-align: right;">₹{{ number_format((float)$item->unit_cost, 2) }}</td>
                        <td style="text-align: right; font-weight: 700;">₹{{ number_format((float)$item->line_total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <div class="totals-row">
                <span>Taxable Value (Subtotal):</span>
                <span>₹{{ number_format((float)$purchaseReturn->subtotal, 2) }}</span>
            </div>
            <div class="totals-row">
                <span>Input GST Adjustment (18%):</span>
                <span>₹{{ number_format((float)$purchaseReturn->tax_amount, 2) }}</span>
            </div>
            <div class="totals-row grand">
                <span>TOTAL DEBIT AMOUNT:</span>
                <span>₹{{ number_format((float)$purchaseReturn->total_amount, 2) }}</span>
            </div>
        </div>

        @if($purchaseReturn->notes)
            <div style="margin-top: 20px; font-size: 11px; background: #f8fafc; border: 1px dashed #cbd5e1; padding: 10px; border-radius: 6px;">
                <strong>Notes / Instructions:</strong> {{ $purchaseReturn->notes }}
            </div>
        @endif

        <div class="signatory">
            <div class="sign-box">
                Received &amp; Accepted by Supplier
            </div>
            <div class="sign-box">
                Authorized Signatory for {{ $storeInfo['name'] }}
            </div>
        </div>
    </div>
</body>
</html>

