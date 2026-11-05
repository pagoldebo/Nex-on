<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $invoice->type_label }} - {{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
            color: #1e293b;
            background: #fff;
            margin: 0;
            padding: 30px;
            font-size: 12px;
            line-height: 1.4;
        }
        .container {
            max-width: 820px;
            margin: 0 auto;
            border: 1px solid #cbd5e1;
            padding: 24px;
        }
        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }
        .store-name {
            font-size: 22px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .store-sub {
            color: #64748b;
            font-size: 11px;
            margin-top: 2px;
        }
        .doc-title {
            text-align: right;
        }
        .doc-type-badge {
            font-size: 18px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .doc-subtitle {
            font-size: 10px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 16px;
        }
        .entity-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 10px 12px;
            font-size: 11px;
        }
        .entity-title {
            font-weight: 700;
            text-transform: uppercase;
            font-size: 10px;
            color: #475569;
            margin-bottom: 4px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 2px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }
        .table th {
            background: #f1f5f9;
            border-top: 1px solid #94a3b8;
            border-bottom: 1px solid #94a3b8;
            padding: 6px 8px;
            font-size: 10px;
            text-transform: uppercase;
            font-weight: 700;
            color: #334155;
            text-align: left;
        }
        .table td {
            padding: 6px 8px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 11px;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-mono { font-family: "Courier New", Courier, monospace; }
        .summary-container {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 16px;
        }
        .bank-box {
            flex: 1;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 10px 12px;
            font-size: 11px;
            background: #f8fafc;
        }
        .totals-box {
            width: 280px;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            padding: 8px 12px;
            font-size: 11px;
        }
        .totals-row {
            display: flex;
            justify-content: space-between;
            padding: 3px 0;
        }
        .totals-row.grand {
            font-size: 13px;
            font-weight: 800;
            border-top: 2px solid #0f172a;
            margin-top: 4px;
            padding-top: 6px;
            color: #0f172a;
        }
        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
            padding-top: 16px;
        }
        .sig-box {
            width: 220px;
            text-align: center;
            border-top: 1px solid #94a3b8;
            padding-top: 6px;
            font-size: 10px;
            font-weight: 600;
            color: #475569;
        }
        @media print {
            body { padding: 0; }
            .container { border: none; padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Print Trigger -->
        <div class="no-print" style="margin-bottom: 16px; text-align: right;">
            <button onclick="window.print()" style="padding: 7px 16px; font-size: 12px; font-weight: 600; background: #4f46e5; color: #fff; border: none; border-radius: 6px; cursor: pointer;">
                Print Invoice / Save PDF
            </button>
        </div>

        <!-- Top Header -->
        <div class="header-top">
            <div>
                <div class="store-name">{{ $storeName }}</div>
                <div class="store-sub">{{ $storeAddress }}</div>
                <div class="store-sub">GSTIN: <strong>{{ $gstin }}</strong> • State: Maharashtra (Code: 27)</div>
                <div class="store-sub">Email: {{ $supportEmail }} • Phone: {{ $supportPhone }}</div>
            </div>
            <div class="doc-title">
                <div class="doc-type-badge">{{ $invoice->type_label }}</div>
                <div class="doc-subtitle">Original for Recipient</div>
                <div style="font-size: 14px; font-weight: 800; font-family: monospace; color: #0f172a; margin-top: 4px;">
                    {{ $invoice->invoice_number }}
                </div>
                <div style="font-size: 11px; color: #64748b; margin-top: 2px;">
                    Date: <strong>{{ $invoice->invoice_date->format('d/m/Y') }}</strong>
                </div>
            </div>
        </div>

        <!-- Details Grid -->
        <div class="grid-2">
            <!-- Billed To -->
            <div class="entity-box">
                <div class="entity-title">Details of Receiver (Billed To)</div>
                <div style="font-weight: 700; font-size: 12px; color: #0f172a;">{{ $invoice->client_name }}</div>
                @if($invoice->company_name)
                    <div style="font-weight: 600;">{{ $invoice->company_name }}</div>
                @endif
                <div>{{ $invoice->billing_address ?? 'Address on file' }}</div>
                <div style="margin-top: 4px;">GSTIN / UIN: <strong>{{ $invoice->client_gstin ?? 'Unregistered' }}</strong></div>
                @if($invoice->client_pan)
                    <div>PAN: <strong>{{ $invoice->client_pan }}</strong></div>
                @endif
                <div>Place of Supply: <strong>{{ $invoice->place_of_supply ?? 'Maharashtra' }}</strong></div>
            </div>

            <!-- Invoice & Supply Meta -->
            <div class="entity-box">
                <div class="entity-title">Invoice &amp; Transportation Details</div>
                <div class="totals-row">
                    <span style="color: #64748b;">Payment Due Date:</span>
                    <strong>{{ $invoice->due_date ? $invoice->due_date->format('d/m/Y') : 'Due on receipt' }}</strong>
                </div>
                <div class="totals-row">
                    <span style="color: #64748b;">Reverse Charge Applicable:</span>
                    <strong>NO</strong>
                </div>
                <div class="totals-row">
                    <span style="color: #64748b;">Shipping Address:</span>
                    <span>{{ $invoice->shipping_address ?? $invoice->billing_address ?? 'Same as billing' }}</span>
                </div>
                @if($invoice->client_phone)
                    <div class="totals-row">
                        <span style="color: #64748b;">Contact:</span>
                        <span>{{ $invoice->client_phone }}</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Line Items Table -->
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 24px;">#</th>
                    <th>Description of Goods</th>
                    <th style="width: 60px;">HSN/SAC</th>
                    <th class="text-center" style="width: 50px;">Qty</th>
                    <th class="text-right" style="width: 70px;">Rate (₹)</th>
                    <th class="text-right" style="width: 55px;">Disc (₹)</th>
                    <th class="text-right" style="width: 75px;">Taxable (₹)</th>
                    <th class="text-center" style="width: 50px;">GST %</th>
                    <th class="text-right" style="width: 80px;">Total (₹)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $idx => $item)
                    @php
                        $taxableLine = max(0, ($item->quantity * $item->unit_price) - $item->discount_amount);
                    @endphp
                    <tr>
                        <td class="text-center font-mono" style="color: #64748b;">{{ $idx + 1 }}</td>
                        <td>
                            <strong>{{ $item->description }}</strong>
                        </td>
                        <td class="font-mono">{{ $item->hsn_code ?? '621210' }}</td>
                        <td class="text-center font-mono">{{ number_format($item->quantity, 0) }} {{ $item->unit }}</td>
                        <td class="text-right font-mono">{{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-right font-mono">{{ $item->discount_amount > 0 ? number_format($item->discount_amount, 2) : '—' }}</td>
                        <td class="text-right font-mono">{{ number_format($taxableLine, 2) }}</td>
                        <td class="text-center font-mono">{{ number_format($item->tax_rate, 0) }}%</td>
                        <td class="text-right font-mono font-bold">{{ number_format($item->total_amount, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Summary & Banking Box -->
        <div class="summary-container">
            <!-- Bank & Terms -->
            <div class="bank-box">
                <div class="entity-title">Bank Remittance Instructions</div>
                <div>Bank Name: <strong>HDFC Bank Ltd</strong></div>
                <div>Account Name: <strong>Project Nexon Retail Operations</strong></div>
                <div>Account Number: <strong>50200088991234</strong></div>
                <div>IFSC Code: <strong>HDFC0000123</strong></div>
                <div>Branch: <strong>Bandra West, Mumbai 400050</strong></div>
                <div style="margin-top: 8px; font-size: 10px; color: #64748b; border-top: 1px solid #e2e8f0; padding-top: 4px;">
                    <strong>Terms:</strong> {{ $invoice->terms ?? 'Subject to Mumbai jurisdiction. Cheques/remittances subject to realization.' }}
                </div>
            </div>

            <!-- Totals -->
            <div class="totals-box">
                <div class="totals-row">
                    <span>Taxable Subtotal:</span>
                    <span class="font-mono">₹{{ number_format($invoice->subtotal - $invoice->discount_amount, 2) }}</span>
                </div>
                @if($invoice->discount_amount > 0)
                    <div class="totals-row" style="color: #b91c1c;">
                        <span>Total Discount:</span>
                        <span class="font-mono">-₹{{ number_format($invoice->discount_amount, 2) }}</span>
                    </div>
                @endif
                @if($invoice->cgst_amount > 0)
                    <div class="totals-row">
                        <span>CGST (Central Tax):</span>
                        <span class="font-mono">₹{{ number_format($invoice->cgst_amount, 2) }}</span>
                    </div>
                    <div class="totals-row">
                        <span>SGST (State Tax):</span>
                        <span class="font-mono">₹{{ number_format($invoice->sgst_amount, 2) }}</span>
                    </div>
                @else
                    <div class="totals-row">
                        <span>IGST (Integrated Tax):</span>
                        <span class="font-mono">₹{{ number_format($invoice->igst_amount, 2) }}</span>
                    </div>
                @endif
                <div class="totals-row grand">
                    <span>Total Amount (INR):</span>
                    <span class="font-mono">₹{{ number_format($invoice->total_amount, 2) }}</span>
                </div>
                <div class="totals-row" style="color: #15803d; font-weight: 600; margin-top: 4px;">
                    <span>Amount Paid:</span>
                    <span class="font-mono">₹{{ number_format($invoice->amount_paid, 2) }}</span>
                </div>
                <div class="totals-row" style="font-weight: 700; color: {{ $invoice->balance_due > 0 ? '#b45309' : '#64748b' }};">
                    <span>Balance Due:</span>
                    <span class="font-mono">₹{{ number_format($invoice->balance_due, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Authorized Signature -->
        <div class="signatures">
            <div class="sig-box">
                Receiver's Signature / Company Stamp
            </div>
            <div class="sig-box">
                For <strong>{{ $storeName }}</strong><br><br>
                Authorized Signatory
            </div>
        </div>
    </div>
</body>
</html>

