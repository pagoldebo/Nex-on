{{--
    ======================================================================
    ADMIN MODULE: PURCHASE ORDERS PRINT
    ======================================================================
    File: resources/views/admin/purchase-orders/print.blade.php
    Responsibility: Back-office administration for PURCHASE ORDERS PRINT
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
    <title>Purchase Order — {{ $purchaseOrder->po_number }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 24px;
            color: #1e293b;
            font-size: 13px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 16px;
            margin-bottom: 20px;
        }
        .title {
            font-size: 24px;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: -0.5px;
        }
        .po-badge {
            font-family: monospace;
            font-size: 16px;
            font-weight: bold;
            color: #4f46e5;
            margin-top: 4px;
        }
        .store-info {
            text-align: right;
            font-size: 11px;
            line-height: 1.4;
            color: #475569;
        }
        .meta-grid {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 24px;
        }
        .meta-card {
            flex: 1;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px 16px;
        }
        .meta-card h3 {
            margin: 0 0 8px 0;
            font-size: 11px;
            text-transform: uppercase;
            color: #64748b;
            letter-spacing: 0.5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background: #0f172a;
            color: #fff;
            padding: 8px 12px;
            font-size: 11px;
            text-transform: uppercase;
            text-align: left;
        }
        td {
            padding: 10px 12px;
            border-bottom: 1px solid #e2e8f0;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-box {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 30px;
        }
        .total-table {
            width: 280px;
        }
        .total-table td {
            padding: 6px 12px;
            font-size: 13px;
        }
        .grand-total {
            font-size: 16px;
            font-weight: bold;
            color: #0f172a;
            border-top: 2px solid #0f172a;
        }
        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 60px;
            padding-top: 20px;
        }
        .sign-line {
            width: 200px;
            border-top: 1px solid #94a3b8;
            text-align: center;
            font-size: 11px;
            padding-top: 6px;
            color: #64748b;
        }
        .no-print {
            background: #0f172a;
            color: #fff;
            padding: 10px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .no-print button {
            background: #4f46e5;
            color: #fff;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
        }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <span>Purchase Order Print Preview</span>
        <button onclick="window.print()">Print Purchase Order (Ctrl+P)</button>
    </div>

    <div class="header">
        <div>
            <div class="title">PURCHASE ORDER</div>
            <div class="po-badge">{{ $purchaseOrder->po_number }}</div>
            <div style="font-size: 11px; color: #64748b; margin-top: 4px;">Date: {{ $purchaseOrder->created_at->format('d/m/Y') }}</div>
        </div>
        <div class="store-info">
            <strong style="font-size: 14px; color: #0f172a;">{{ $storeInfo['name'] }}</strong><br>
            {{ $storeInfo['address'] }}<br>
            Phone: {{ $storeInfo['phone'] }} &bull; Email: {{ $storeInfo['email'] }}<br>
            GSTIN: <strong>{{ $storeInfo['gstin'] }}</strong>
        </div>
    </div>

    <div class="meta-grid">
        <div class="meta-card">
            <h3>Vendor / Supplier Details</h3>
            <strong style="font-size: 13px;">{{ $purchaseOrder->supplier?->name }}</strong><br>
            Vendor Code: <code>{{ $purchaseOrder->supplier?->code }}</code><br>
            Contact: {{ $purchaseOrder->supplier?->contact_name ?? 'Procurement Team' }}<br>
            Phone: {{ $purchaseOrder->supplier?->phone ?? 'N/A' }} | Email: {{ $purchaseOrder->supplier?->email ?? 'N/A' }}<br>
            GSTIN: {{ $purchaseOrder->supplier?->gst_number ?? 'N/A' }}<br>
            Address: {{ $purchaseOrder->supplier?->address ?? 'N/A' }}
        </div>

        <div class="meta-card">
            <h3>Shipping / Delivery Destination</h3>
            <strong>{{ $storeInfo['name'] }} — Central Warehouse</strong><br>
            101, Central Galleria, Indiranagar<br>
            Bengaluru, Karnataka - 560038<br>
            <strong>Expected Delivery Date:</strong> {{ $purchaseOrder->expected_delivery_date ? $purchaseOrder->expected_delivery_date->format('d/m/Y') : 'Earliest' }}<br>
            <strong>Status:</strong> {{ strtoupper($purchaseOrder->status) }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 8%;">#</th>
                <th style="width: 42%;">Item Description &amp; SKU</th>
                <th style="width: 15%; text-align: center;">Ordered Qty</th>
                <th style="width: 17%; text-align: right;">Unit Cost</th>
                <th style="width: 18%; text-align: right;">Line Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($purchaseOrder->items as $idx => $item)
                <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td>
                        <strong>{{ $item->variant?->product?->name }}</strong><br>
                        <span style="font-size: 11px; color: #64748b; font-family: monospace;">SKU: {{ $item->variant?->sku }} | Size: {{ $item->variant?->size }}</span>
                    </td>
                    <td class="text-center font-bold" style="font-size: 13px;">{{ $item->ordered_qty }}</td>
                    <td class="text-right">₹{{ number_format($item->unit_cost, 0) }}</td>
                    <td class="text-right font-bold">₹{{ number_format($item->line_total, 0) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-box">
        <table class="total-table">
            <tr>
                <td>Subtotal ({{ $purchaseOrder->items->sum('ordered_qty') }} units):</td>
                <td class="text-right font-bold">₹{{ number_format($purchaseOrder->total_amount, 0) }}</td>
            </tr>
            <tr class="grand-total">
                <td>TOTAL PO AMOUNT:</td>
                <td class="text-right">₹{{ number_format($purchaseOrder->total_amount, 0) }}</td>
            </tr>
        </table>
    </div>

    @if($purchaseOrder->notes)
        <div style="background: #f1f5f9; padding: 10px 14px; border-radius: 6px; font-size: 11px; margin-bottom: 20px;">
            <strong>Procurement Instructions:</strong> {{ $purchaseOrder->notes }}
        </div>
    @endif

    <div class="signatures">
        <div class="sign-line">Authorized Purchasing Officer</div>
        <div class="sign-line">Vendor Acceptance &amp; Stamp</div>
    </div>

</body>
</html>
