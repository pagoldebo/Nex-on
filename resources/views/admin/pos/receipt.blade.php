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
    <link rel="stylesheet" href="{{ asset('css/admin/receipt.css') }}">
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
                <div class="store-sub font-bold">{{ \App\Models\Setting::taxNumberLabel() }}: {{ $storeInfo['gstin'] }}</div>
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

        <!-- Tax breakdown note -->
        @php
            $taxEnabled = \App\Models\Setting::taxEnabled();
            $taxName = \App\Models\Setting::taxName();
            $taxRate = \App\Models\Setting::taxRate();
            $taxType = \App\Models\Setting::taxType();
            $taxable = $taxRate > 0 ? round($order->total / (1 + ($taxRate / 100)), 2) : $order->total;
            $taxVal = round($order->total - $taxable, 2);
        @endphp
        @if($taxEnabled && $taxRate > 0)
            <div style="font-size: 10px; line-height: 1.3;" class="text-center">
                [{{ $taxName }} ({{ $taxRate }}%): Taxable ₹{{ number_format($taxable, 2) }} | Tax ₹{{ number_format($taxVal, 2) }}]
            </div>
        @endif

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

    <script src="{{ asset('js/admin/print.js') }}"></script>
</body>
</html>
