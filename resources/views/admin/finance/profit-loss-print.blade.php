<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>P&L Statement - {{ $storeName }} ({{ $startDate->format('d M Y') }} to {{ $endDate->format('d M Y') }})</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; color: #1e293b; background: #fff; margin: 0; padding: 24px; font-size: 13px; line-height: 1.5; }
        .container { max-width: 800px; margin: 0 auto; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #0f172a; padding-bottom: 16px; margin-bottom: 20px; }
        .logo-title { font-size: 24px; font-weight: 800; color: #0f172a; text-transform: uppercase; letter-spacing: 0.5px; }
        .badge { display: inline-block; padding: 3px 8px; font-size: 11px; font-weight: 700; background: #f1f5f9; border-radius: 4px; color: #475569; margin-top: 4px; }
        .meta-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .meta-table td { padding: 6px 8px; border-bottom: 1px solid #e2e8f0; font-size: 12px; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table th { background: #f8fafc; border-top: 1px solid #cbd5e1; border-bottom: 2px solid #cbd5e1; text-align: left; padding: 8px 12px; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #475569; }
        .table td { padding: 8px 12px; border-bottom: 1px solid #f1f5f9; }
        .amount { text-align: right; font-family: "Courier New", Courier, monospace; font-weight: 600; }
        .section-header { font-weight: 700; background: #f1f5f9; color: #0f172a; text-transform: uppercase; font-size: 11px; letter-spacing: 0.5px; }
        .highlight-row { font-weight: 800; background: #f8fafc; border-top: 1px solid #0f172a; border-bottom: 1px solid #0f172a; }
        .net-profit-box { background: #0f172a; color: #fff; padding: 14px 18px; display: flex; justify-content: space-between; align-items: center; border-radius: 6px; margin-top: 20px; font-size: 14px; }
        .net-profit-box .amount { font-size: 20px; font-weight: 800; }
        .signatures { display: flex; justify-content: space-between; margin-top: 60px; padding-top: 20px; }
        .sig-block { width: 200px; text-align: center; border-top: 1px solid #64748b; padding-top: 8px; font-size: 11px; font-weight: 600; color: #475569; }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Action Toolbar -->
        <div class="no-print" style="margin-bottom: 20px; text-align: right;">
            <button onclick="window.print()" style="padding: 8px 16px; font-size: 13px; font-weight: 600; background: #4f46e5; color: #fff; border: none; border-radius: 6px; cursor: pointer;">
                Print / Save PDF
            </button>
        </div>

        <!-- Header -->
        <div class="header">
            <div>
                <div class="logo-title">{{ $storeName }}</div>
                <div style="color: #64748b; font-size: 12px; margin-top: 2px;">{{ $storeAddress }}</div>
                <div style="color: #64748b; font-size: 12px;">GSTIN: <strong>{{ $gstin }}</strong></div>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 18px; font-weight: 800; color: #0f172a;">INCOME STATEMENT</div>
                <div class="badge">PROFIT &amp; LOSS REPORT</div>
                <div style="font-size: 11px; color: #64748b; margin-top: 6px;">
                    Period: <strong>{{ $startDate->format('d M Y') }}</strong> to <strong>{{ $endDate->format('d M Y') }}</strong>
                </div>
            </div>
        </div>

        <!-- Statement Table -->
        <table class="table">
            <thead>
                <tr>
                    <th>Account Particulars</th>
                    <th style="text-align: right;">Amount (INR)</th>
                </tr>
            </thead>
            <tbody>
                <!-- 1. REVENUE -->
                <tr class="section-header">
                    <td colspan="2">1. Operating Revenue &amp; Turnover</td>
                </tr>
                <tr>
                    <td style="padding-left: 20px;">Gross Merchandise Value (POS &amp; Web Storefront)</td>
                    <td class="amount">₹{{ number_format($grossSales, 2) }}</td>
                </tr>
                <tr>
                    <td style="padding-left: 20px;">Shipping, Express Courier &amp; COD Fees Collected</td>
                    <td class="amount">₹{{ number_format($shippingCollected, 2) }}</td>
                </tr>
                <tr>
                    <td style="padding-left: 20px; color: #b91c1c;">Less: Promotional Discounts &amp; Coupons</td>
                    <td class="amount" style="color: #b91c1c;">-₹{{ number_format($discountsGiven, 2) }}</td>
                </tr>
                <tr>
                    <td style="padding-left: 20px; color: #b91c1c;">Less: Customer Returns &amp; Order Refunds</td>
                    <td class="amount" style="color: #b91c1c;">-₹{{ number_format($refundsGiven, 2) }}</td>
                </tr>
                <tr class="highlight-row">
                    <td>NET OPERATING REVENUE (A)</td>
                    <td class="amount">₹{{ number_format($netRevenue, 2) }}</td>
                </tr>

                <!-- 2. COGS -->
                <tr class="section-header">
                    <td colspan="2">2. Cost of Goods Sold (COGS)</td>
                </tr>
                <tr>
                    <td style="padding-left: 20px;">Apparel &amp; Product Direct Cost</td>
                    <td class="amount">₹{{ number_format($cogsProducts, 2) }}</td>
                </tr>
                <tr>
                    <td style="padding-left: 20px;">Supplier Inbound Logistics &amp; Inward Handling</td>
                    <td class="amount">₹{{ number_format($cogsFreight, 2) }}</td>
                </tr>
                <tr class="highlight-row">
                    <td>TOTAL COST OF GOODS SOLD (B)</td>
                    <td class="amount">₹{{ number_format($totalCogs, 2) }}</td>
                </tr>

                <!-- 3. GROSS PROFIT -->
                <tr style="background: #f0fdf4; font-weight: 800; border-top: 1px solid #16a34a; border-bottom: 1px solid #16a34a;">
                    <td>3. GROSS PROFIT (A - B) [Margin: {{ number_format($grossMarginPercent, 1) }}%]</td>
                    <td class="amount" style="color: #15803d;">₹{{ number_format($grossProfit, 2) }}</td>
                </tr>

                <!-- 4. OPEX -->
                <tr class="section-header">
                    <td colspan="2">4. Operating Expenses &amp; Administrative Overhead (OPEX)</td>
                </tr>
                @foreach($opexByCategory as $cat)
                    <tr>
                        <td style="padding-left: 20px;">{{ $cat['name'] }}</td>
                        <td class="amount">₹{{ number_format($cat['amount'], 2) }}</td>
                    </tr>
                @endforeach
                <tr class="highlight-row">
                    <td>TOTAL OPERATING OVERHEAD (C)</td>
                    <td class="amount" style="color: #be123c;">₹{{ number_format($totalOpex, 2) }}</td>
                </tr>

                <!-- 5. EBITDA -->
                <tr style="background: #eef2ff; font-weight: 800; border-top: 1px solid #4338ca; border-bottom: 1px solid #4338ca;">
                    <td>5. OPERATING INCOME / EBITDA (Gross Profit - OPEX)</td>
                    <td class="amount" style="color: #3730a3;">₹{{ number_format($ebitda, 2) }}</td>
                </tr>

                <!-- 6. TAXES -->
                <tr class="section-header">
                    <td colspan="2">6. Taxes &amp; Statutory Adjustments</td>
                </tr>
                <tr>
                    <td style="padding-left: 20px;">Output GST Collected</td>
                    <td class="amount">₹{{ number_format($gstOutput, 2) }}</td>
                </tr>
                <tr>
                    <td style="padding-left: 20px;">Less: Input Tax Credit (ITC)</td>
                    <td class="amount">-₹{{ number_format($gstInputCredit, 2) }}</td>
                </tr>
                <tr>
                    <td style="padding-left: 20px;">Net GST Cash Payable</td>
                    <td class="amount">₹{{ number_format($netGstLiability, 2) }}</td>
                </tr>
                <tr>
                    <td style="padding-left: 20px;">Corporate Income Tax Provision (25%)</td>
                    <td class="amount">₹{{ number_format($taxProvision, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Net Profit Box -->
        <div class="net-profit-box">
            <div>
                <strong>NET PROFIT AFTER TAX (PAT)</strong>
                <div style="font-size: 11px; opacity: 0.8; margin-top: 2px;">Net Retained Earnings Margin: {{ number_format($netMarginPercent, 1) }}%</div>
            </div>
            <div class="amount">₹{{ number_format($netProfit, 2) }}</div>
        </div>

        <!-- Signatures -->
        <div class="signatures">
            <div class="sig-block">Prepared by (Internal Auditor)</div>
            <div class="sig-block">Verified by (Chief Financial Officer)</div>
            <div class="sig-block">Managing Director / Partner</div>
        </div>
    </div>
</body>
</html>

