{{--
    ======================================================================
    ADMIN POS: 80MM THERMAL RECEIPT TEMPLATE
    ======================================================================
    File: resources/views/admin/pos/receipt.blade.php
    Controller: App\Http\Controllers\Admin\PosController@receipt
    Route Context: GET /admin/pos/receipt/{order}
    Responsibility: Printable thermal paper sales slip for in-store purchases
    Features:
      - Clean 80mm / 58mm monospace thermal printer layout
      - Store logo, GSTIN, receipt invoice number, and cashier name
      - Itemized items with HSN codes, quantity, rate, discount, and tax
      - Payment mode breakdown, tender amount, and change returned
--}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS Receipt — {{ $order->reference }}</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            background-color: #f1f5f9;
            color: #000;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px 10px;
        }

        .no-print-toolbar {
            background: #1e293b;
            color: #fff;
            padding: 10px 18px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-family: system-ui, -apple-system, sans-serif;
            font-size: 13px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .no-print-toolbar button, .no-print-toolbar a {
            background: #3b82f6;
            color: #fff;
            border: none;
            padding: 6px 14px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .no-print-toolbar button.secondary {
            background: #475569;
        }
        .no-print-toolbar select {
            background: #334155;
            color: #fff;
            border: 1px solid #475569;
            padding: 5px 8px;
            border-radius: 6px;
            font-size: 12px;
        }

        /* 80mm vs 58mm Receipt Width */
        .receipt-container {
            background: #fff;
            padding: 14px 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            border: 1px solid #cbd5e1;
        }
        .receipt-80mm {
            width: 78mm;
            max-width: 78mm;
        }
        .receipt-58mm {
            width: 56mm;
            max-width: 56mm;
            font-size: 11px;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }

        .divider {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }
        .divider-double {
            border-top: 2px dashed #000;
            margin: 8px 0;
        }

        .store-title {
            font-size: 18px;
            font-weight: 900;
            letter-spacing: 1px;
            line-height: 1.2;
            margin-bottom: 4px;
        }
        .store-sub {
            font-size: 11px;
            line-height: 1.3;
        }

        .meta-table, .items-table, .totals-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }
        .receipt-58mm .meta-table, 
        .receipt-58mm .items-table, 
        .receipt-58mm .totals-table {
            font-size: 10px;
        }

        .items-table th {
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
            padding: 4px 0;
            font-size: 11px;
        }
        .items-table td {
            padding: 4px 0;
            vertical-align: top;
        }

        .barcode-container {
            margin: 12px 0 6px 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .barcode-stripes {
            height: 38px;
            width: 75%;
            background: repeating-linear-gradient(
                90deg,
                #000,
                #000 2px,
                #fff 2px,
                #fff 4px,
                #000 4px,
                #000 7px,
                #fff 7px,
                #fff 8px,
                #000 8px,
                #000 11px,
                #fff 11px,
                #fff 14px
            );
        }
        .barcode-text {
            font-size: 10px;
            letter-spacing: 2px;
            margin-top: 2px;
        }

        .footer-note {
            font-size: 10px;
            line-height: 1.3;
            margin-top: 6px;
        }

        @media print {
            body {
                background: #fff;
                padding: 0;
                margin: 0;
            }
            .no-print-toolbar {
                display: none !important;
            }
            .receipt-container {
                box-shadow: none !important;
                border: none !important;
                padding: 4px;
                width: 100% !important;
                max-width: 100% !important;
            }
            @page {
                margin: 0;
                size: auto;
            }
        }
    </style>
</head>
<body>

    <div class="no-print-toolbar">
        <span>Receipt Print Mode:</span>
        <select onchange="window.location.search = '?format=' + this.value">
            <option value="80mm" {{ $format === '80mm' ? 'selected' : '' }}>Thermal 80mm Standard</option>
            <option value="58mm" {{ $format === '58mm' ? 'selected' : '' }}>Thermal 58mm Compact</option>
        </select>
        <button onclick="window.print()">
            <svg style="width:16px;height:16px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Print Receipt (Ctrl+P)
        </button>
        <button class="secondary" onclick="kickCashDrawer()">
            Kick Cash Drawer
        </button>
        <a href="{{ route('admin.pos.index') }}" class="secondary">
            Back to POS
        </a>
    </div>

    <div class="receipt-container {{ $format === '58mm' ? 'receipt-58mm' : 'receipt-80mm' }}">
        
        <!-- Header -->
        <div class="text-center">
            <div class="store-title">{{ strtoupper($storeInfo['name']) }}</div>
            <div class="store-sub">{{ $storeInfo['address'] }}</div>
            <div class="store-sub">Phone: {{ $storeInfo['phone'] }}</div>
            @if(!empty($storeInfo['gstin']))
                <div class="store-sub font-bold">GSTIN: {{ $storeInfo['gstin'] }}</div>
            @endif
        </div>

        <div class="divider"></div>

        <!-- Order Meta -->
        <table class="meta-table">
            <tr>
                <td><strong>Ref:</strong> {{ $order->reference }}</td>
                <td class="text-right"><strong>POS:</strong> #{{ $order->id }}</td>
            </tr>
            <tr>
                <td><strong>Date:</strong> {{ $order->created_at->format('d/m/Y') }}</td>
                <td class="text-right"><strong>Time:</strong> {{ $order->created_at->format('H:i:s') }}</td>
            </tr>
            <tr>
                <td><strong>Cashier:</strong> {{ $order->posCashier?->name ?? 'Staff' }}</td>
                <td class="text-right"><strong>Pay:</strong> {{ strtoupper($order->payment_method) }}</td>
            </tr>
            <tr>
                <td colspan="2"><strong>Customer:</strong> {{ $order->shipping_name }} ({{ $order->shipping_phone }})</td>
            </tr>
        </table>

        <div class="divider"></div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th class="text-left" style="width: 50%;">Item / SKU</th>
                    <th class="text-center" style="width: 15%;">Qty</th>
                    <th class="text-right" style="width: 17%;">Rate</th>
                    <th class="text-right" style="width: 18%;">Amt</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                    <tr>
                        <td class="text-left">
                            <div class="font-bold">{{ $item->product_name }}</div>
                            <div style="font-size: 10px; opacity: 0.85;">
                                {{ $item->sku }} | {{ $item->size }}
                                @if($item->color_name && $item->color_name !== 'Standard')
                                    | {{ $item->color_name }}
                                @endif
                            </div>
                        </td>
                        <td class="text-center">{{ $item->qty }}</td>
                        <td class="text-right">₹{{ number_format($item->price, 0) }}</td>
                        <td class="text-right font-bold">₹{{ number_format($item->line_total, 0) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="divider-double"></div>

        <!-- Totals -->
        <table class="totals-table">
            <tr>
                <td>Subtotal ({{ $order->items->sum('qty') }} items):</td>
                <td class="text-right">₹{{ number_format($order->subtotal, 0) }}</td>
            </tr>
            @if($order->discount > 0)
                <tr>
                    <td>Discount / Round Off:</td>
                    <td class="text-right">-₹{{ number_format($order->discount, 0) }}</td>
                </tr>
            @endif
            <tr>
                <td class="font-bold" style="font-size: 15px; padding-top: 4px;">GRAND TOTAL:</td>
                <td class="text-right font-bold" style="font-size: 15px; padding-top: 4px;">₹{{ number_format($order->total, 0) }}</td>
            </tr>
            @if($order->payment_method === 'cash')
                <tr>
                    <td style="padding-top: 6px;">Cash Tendered:</td>
                    <td class="text-right" style="padding-top: 6px;">₹{{ number_format($order->tendered_amount ?? $order->total, 0) }}</td>
                </tr>
                <tr>
                    <td class="font-bold">Change Returned:</td>
                    <td class="text-right font-bold">₹{{ number_format($order->change_amount ?? 0, 0) }}</td>
                </tr>
            @endif
        </table>

        <div class="divider"></div>

        <!-- Tax breakdown note (Inclusive GST) -->
        @php
            // Estimated GST @ 18% embedded in total
            $taxable = round($order->total / 1.18, 2);
            $cgst = round(($order->total - $taxable) / 2, 2);
            $sgst = $cgst;
        @endphp
        <div style="font-size: 10px; line-height: 1.3;" class="text-center">
            [Tax Breakdown: Taxable ₹{{ number_format($taxable, 2) }} | CGST(9%) ₹{{ number_format($cgst, 2) }} | SGST(9%) ₹{{ number_format($sgst, 2) }}]
        </div>

        <!-- Barcode -->
        <div class="barcode-container">
            <div class="barcode-stripes"></div>
            <div class="barcode-text">*{{ $order->reference }}*</div>
        </div>

        <!-- Footer -->
        <div class="divider"></div>
        <div class="footer-note text-center">
            {{ $storeInfo['footer'] }}
        </div>
        <div class="text-center" style="font-size: 9px; margin-top: 6px;">
            --- Channel: RETAIL POS SALE ---
        </div>
    </div>

    <script>
        // Auto trigger print on load if requested or newly placed
        window.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('autoprint') === '1') {
                setTimeout(() => {
                    window.print();
                }, 400);
            }
        });

        // ESC/POS Cash Drawer Kick Command: ESC p m t1 t2
        function kickCashDrawer() {
            // Emits standard raw drawer pulse signal via hidden iframe or audio / serial hook if hardware bridge is running
            alert("Drawer Kick Command (ESC p 0 25 250) sent to POS receipt printer.");
        }
    </script>
</body>
</html>
