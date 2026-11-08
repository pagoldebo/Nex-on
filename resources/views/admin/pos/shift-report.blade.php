{{--
    ======================================================================
    ADMIN MODULE: POS SHIFT REPORT
    ======================================================================
    File: resources/views/admin/pos/shift-report.blade.php
    Responsibility: Back-office administration for POS SHIFT REPORT
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
    <title>{{ strtoupper($reportType) }}-REPORT: Shift {{ $shift->shift_code }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: "Courier New", Courier, monospace, monospace; background: #f1f5f9; color: #000; padding: 15px; }
        .receipt-card { width: 80mm; max-width: 100%; margin: 0 auto; background: #fff; border: 1px dashed #cbd5e1; padding: 12px 10px; font-size: 11px; line-height: 1.3; }
        .toolbar { width: 80mm; max-width: 100%; margin: 0 auto 10px auto; display: flex; justify-content: space-between; }
        .btn { background: #1e293b; color: #fff; padding: 6px 12px; border-radius: 4px; font-size: 11px; font-family: sans-serif; font-weight: bold; text-decoration: none; border: none; cursor: pointer; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .divider { border-top: 1px dashed #000; margin: 6px 0; }
        .divider-double { border-top: 2px dashed #000; margin: 6px 0; }
        .row { display: flex; justify-content: space-between; }
        .header-title { font-size: 14px; font-weight: bold; letter-spacing: 0.5px; }
        .report-badge { font-size: 13px; font-weight: 900; background: #000; color: #fff; padding: 2px 6px; display: inline-block; margin: 4px 0; }
        @media print {
            body { background: #fff; padding: 0; }
            .toolbar { display: none !important; }
            .receipt-card { width: 80mm !important; border: none !important; margin: 0 !important; padding: 4px !important; }
            @page { size: 80mm auto; margin: 0; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <button class="btn" onclick="window.print()">🖨️ Print Slip</button>
        <button class="btn" style="background:#64748b;" onclick="window.close()">Close</button>
    </div>

    <div class="receipt-card">
        <div class="text-center">
            <div class="header-title">{{ $storeInfo['name'] }}</div>
            <div>{{ $storeInfo['address'] }}</div>
            <div>GSTIN: {{ $storeInfo['gstin'] }} &bull; Ph: {{ $storeInfo['phone'] }}</div>
            
            <div class="divider"></div>
            <div class="report-badge">
                {{ strtoupper($reportType) === 'X' ? '*** X-REPORT (MID-SHIFT AUDIT) ***' : '*** Z-REPORT (SHIFT CLOSE RECONCILIATION) ***' }}
            </div>
            <div class="divider"></div>
        </div>

        <div class="row"><span>SHIFT CODE:</span><span class="font-bold">#{{ $shift->shift_code }}</span></div>
        <div class="row"><span>CASHIER:</span><span class="font-bold">{{ $shift->cashier?->name }}</span></div>
        <div class="row"><span>OPENED:</span><span>{{ $shift->opened_at->format('d/m/y h:i A') }}</span></div>
        <div class="row"><span>{{ $shift->closed_at ? 'CLOSED:' : 'PRINTED:' }}</span><span>{{ ($shift->closed_at ?? now())->format('d/m/y h:i A') }}</span></div>
        <div class="row"><span>STATUS:</span><span class="font-bold">{{ strtoupper($shift->status) }}</span></div>

        <div class="divider"></div>
        <div class="text-center font-bold">--- TENDER BREAKDOWN ---</div>
        <div class="divider"></div>

        <div class="row"><span>Cash Sales:</span><span class="font-bold">₹{{ number_format((float)$shift->cash_sales, 2) }}</span></div>
        <div class="row"><span>Card Sales:</span><span class="font-bold">₹{{ number_format((float)$shift->card_sales, 2) }}</span></div>
        <div class="row"><span>UPI / QR Sales:</span><span class="font-bold">₹{{ number_format((float)$shift->upi_sales, 2) }}</span></div>
        <div class="divider"></div>
        <div class="row font-bold"><span>TOTAL GROSS REVENUE:</span><span>₹{{ number_format((float)$shift->total_sales, 2) }}</span></div>
        <div class="row"><span>TRANSACTIONS COUNT:</span><span class="font-bold">{{ $shift->total_orders_count }} orders</span></div>

        <div class="divider"></div>
        <div class="text-center font-bold">--- DRAWER RECONCILIATION ---</div>
        <div class="divider"></div>

        @php
            $cashIn = (float)$shift->movements->where('type', 'cash_in')->sum('amount');
            $cashOut = (float)$shift->movements->where('type', 'cash_out')->sum('amount');
        @endphp

        <div class="row"><span>(+) Opening Float:</span><span>₹{{ number_format((float)$shift->opening_float, 2) }}</span></div>
        <div class="row"><span>(+) Cash Tender Sales:</span><span>₹{{ number_format((float)$shift->cash_sales, 2) }}</span></div>
        <div class="row"><span>(+) Cash In (Pay-In):</span><span>₹{{ number_format($cashIn, 2) }}</span></div>
        <div class="row"><span>(-) Cash Out (Pay-Out):</span><span>₹{{ number_format($cashOut, 2) }}</span></div>
        <div class="divider"></div>
        <div class="row font-bold"><span>EXPECTED IN DRAWER:</span><span>₹{{ number_format((float)$shift->expected_cash, 2) }}</span></div>

        @if($shift->status === 'closed')
            <div class="row font-bold"><span>ACTUAL COUNTED CASH:</span><span>₹{{ number_format((float)$shift->counted_cash, 2) }}</span></div>
            <div class="divider-double"></div>
            @php $diff = (float)$shift->cash_difference; @endphp
            <div class="row font-bold" style="font-size: 12px;">
                <span>VARIANCE (OVER/SHORT):</span>
                <span>{{ $diff == 0 ? '₹0.00 (PERFECT)' : ($diff > 0 ? '+₹' . number_format($diff, 2) . ' OVER' : '-₹' . number_format(abs($diff), 2) . ' SHORT') }}</span>
            </div>
            <div class="divider-double"></div>
        @else
            <div class="divider"></div>
            <div class="text-center" style="font-style: italic;">* Drawer is currently active and open. Counted cash will be verified upon final Z-Report closure.</div>
        @endif

        @if($shift->notes)
            <div style="margin-top: 8px;">
                <strong>Remarks:</strong> {{ $shift->notes }}
            </div>
        @endif

        <div style="margin-top: 24px;">
            <div class="row" style="margin-bottom: 20px;">
                <span>Cashier Sign: ____________</span>
                <span>Manager Sign: ____________</span>
            </div>
            <div class="text-center" style="font-size: 10px;">
                *** END OF {{ strtoupper($reportType) }}-REPORT ***
            </div>
        </div>
    </div>

    <script>
        window.addEventListener('load', function() {
            setTimeout(function() { window.print(); }, 400);
        });
    </script>
</body>
</html>

