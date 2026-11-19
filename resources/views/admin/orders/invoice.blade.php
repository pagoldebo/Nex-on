{{--
    ======================================================================
    ADMIN MODULE: ORDERS INVOICE
    ======================================================================
    File: resources/views/admin/orders/invoice.blade.php
    Responsibility: Back-office administration for ORDERS INVOICE
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
    <title>Invoice #{{ $order->reference }} — {{ \App\Models\Setting::appName() }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f1f5f9;
            color: #0f172a;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        @media print {
            body {
                background-color: #ffffff;
                color: #000000;
                margin: 0;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            .invoice-card {
                box-shadow: none !important;
                border: none !important;
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            @page {
                size: A4 portrait;
                margin: 12mm 15mm;
            }
        }
    </style>
</head>
<body class="min-h-screen py-8 px-4 sm:px-6">

    <!-- Top Action Bar (hidden when printing) -->
    <div class="no-print max-w-4xl mx-auto mb-6 flex flex-wrap items-center justify-between gap-3 bg-white p-4 rounded-xl shadow-sm border border-slate-200">
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.orders.show', $order) }}" 
               class="inline-flex items-center px-3 py-2 text-sm font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Order
            </a>
            <span class="text-sm text-slate-500">Order Ref: <strong class="text-slate-800">{{ $order->reference }}</strong></span>
        </div>
        <div class="flex items-center space-x-2">
            <button onclick="window.print()" 
                    class="inline-flex items-center px-4 py-2 text-sm font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-lg shadow-sm transition-all hover:shadow-md cursor-pointer">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Print / Save as PDF
            </button>
        </div>
    </div>

    <!-- Main Printable Invoice Document -->
    <div class="invoice-card max-w-4xl mx-auto bg-white rounded-2xl border border-slate-200 shadow-md p-8 sm:p-12">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start gap-6 pb-8 border-b border-slate-200">
            <div>
                @php
                    $storeName = \App\Models\Setting::appName();
                    $supportEmail = \App\Models\Setting::supportEmail();
                    $supportPhone = \App\Models\Setting::supportPhone();
                    $storeAddress = \App\Models\Setting::storeAddress();
                    $taxNumber = \App\Models\Setting::taxNumber();
                    $taxNumberLabel = \App\Models\Setting::taxNumberLabel();
                    $taxName = \App\Models\Setting::taxName();
                    $taxRate = \App\Models\Setting::taxRate();
                    $taxType = \App\Models\Setting::taxType();
                    $taxEnabled = \App\Models\Setting::taxEnabled();
                    $gstin = $taxNumber;
                    $invoiceFooter = \App\Models\Setting::invoiceFooter();
                    $siteLogo = $siteLogo ?? \App\Models\Setting::get('site_logo');
                @endphp
                @if(!empty($siteLogo))
                    <div class="mb-2">
                        <img src="{{ \App\Models\Setting::imageUrl($siteLogo) }}" alt="{{ $storeName }}" class="h-10 w-auto max-w-[200px] object-contain">
                    </div>
                @else
                    <div class="flex items-center space-x-2.5">
                        <span class="w-8 h-8 rounded-lg bg-rose-600 flex items-center justify-center text-white font-extrabold text-lg shadow-sm">
                            {{ strtoupper(substr($storeName, 0, 1)) }}
                        </span>
                        <span class="text-2xl font-black tracking-tight text-slate-900">{{ $storeName }}</span>
                    </div>
                @endif
                <p class="text-xs text-slate-500 mt-2">{{ $storeName }}</p>
                <p class="text-xs text-slate-500">{{ $storeAddress }}</p>
                @if(!empty($taxNumber))
                    <p class="text-xs text-slate-500">{{ $taxNumberLabel }}: <span class="font-mono font-semibold">{{ $taxNumber }}</span></p>
                @endif
                <p class="text-xs text-slate-500">{{ $supportEmail }} | {{ $supportPhone }}</p>
            </div>

            <div class="sm:text-right">
                <span class="inline-block px-3 py-1 bg-slate-900 text-white text-xs uppercase font-extrabold tracking-wider rounded">
                    {{ $taxEnabled ? strtoupper($taxName) . ' INVOICE' : 'TAX INVOICE' }}
                </span>
                <h1 class="text-xl font-bold text-slate-900 mt-2">INV-{{ preg_replace('/^SAA-/', '', $order->reference) }}</h1>
                <div class="text-xs text-slate-500 space-y-0.5 mt-2">
                    <p><span class="font-medium text-slate-700">Order Reference:</span> {{ $order->reference }}</p>
                    <p><span class="font-medium text-slate-700">Invoice Date:</span> {{ $order->created_at->format('M d, Y') }}</p>
                    <p><span class="font-medium text-slate-700">Time:</span> {{ $order->created_at->format('h:i A') }}</p>
                    <p>
                        <span class="font-medium text-slate-700">Payment:</span> 
                        <span class="uppercase font-semibold text-slate-900">{{ $order->payment_method }}</span>
                        ({{ ucfirst($order->payment_status) }})
                    </p>
                </div>
            </div>
        </div>

        <!-- Bill To / Ship To -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 py-6 border-b border-slate-200">
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Billed To / Customer</h3>
                <p class="font-bold text-slate-900 text-base">{{ $order->shipping_name }}</p>
                <p class="text-sm text-slate-600">{{ $order->shipping_phone }}</p>
                @if($order->shipping_email)
                    <p class="text-sm text-slate-600">{{ $order->shipping_email }}</p>
                @endif
            </div>

            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Shipping Destination</h3>
                <p class="text-sm font-semibold text-slate-800">{{ $order->shipping_address1 }}</p>
                @if($order->shipping_address2)
                    <p class="text-sm text-slate-600">{{ $order->shipping_address2 }}</p>
                @endif
                <p class="text-sm text-slate-600">
                    {{ $order->shipping_city }}, {{ $order->shipping_state }} — <span class="font-semibold text-slate-900">{{ $order->shipping_pincode }}</span>
                </p>
                @if($order->courier || $order->tracking_number)
                    <div class="mt-2 text-xs bg-slate-50 p-2 rounded border border-slate-200">
                        <span class="text-slate-500">Dispatch:</span>
                        <span class="font-bold text-slate-800">{{ $order->courier ?? 'Standard Courier' }}</span>
                        @if($order->tracking_number)
                            — AWB: <span class="font-mono text-slate-900">{{ $order->tracking_number }}</span>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <!-- Items Table -->
        <div class="py-6">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b-2 border-slate-900 text-xs uppercase font-extrabold tracking-wider text-slate-700">
                        <th class="py-3 pr-2 w-10">#</th>
                        <th class="py-3 px-4">Item & Description</th>
                        <th class="py-3 px-3 text-center">Size</th>
                        <th class="py-3 px-3 text-right">Unit Price</th>
                        <th class="py-3 px-3 text-center">Qty</th>
                        <th class="py-3 pl-3 text-right">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($order->items as $idx => $item)
                        @php
                            $itemLine = (float) $item->line_total;
                            $itemTax = ($taxEnabled && $taxRate > 0) 
                                ? ($taxType === 'inclusive' 
                                    ? round($itemLine - ($itemLine / (1 + ($taxRate / 100))), 2) 
                                    : round(($itemLine * $taxRate) / 100, 2))
                                : 0;
                        @endphp
                        <tr class="align-middle">
                            <td class="py-3.5 pr-2 text-xs text-slate-400 font-mono">{{ $idx + 1 }}</td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-900">{{ $item->product_name }}</div>
                                <div class="text-xs text-slate-500 flex items-center space-x-2 mt-0.5">
                                    @if($item->sku)
                                        <span class="font-mono font-bold text-slate-700 bg-slate-100 px-1.5 py-0.5 rounded text-[10px]">SKU: {{ $item->sku }}</span>
                                    @endif
                                    @if($item->color_hex)
                                        <span class="w-2.5 h-2.5 rounded-full inline-block border border-slate-300" style="background-color: {{ $item->color_hex }};"></span>
                                    @endif
                                    <span>Color: {{ $item->color_name ?? 'Standard' }}</span>
                                </div>
                            </td>
                            <td class="py-3.5 px-3 text-center font-bold text-slate-800">{{ $item->size ?? '—' }}</td>
                            <td class="py-3.5 px-3 text-right font-medium text-slate-700">₹{{ number_format($item->price, 2) }}</td>
                            <td class="py-3.5 px-3 text-center font-bold text-slate-900">{{ $item->qty }}</td>
                            <td class="py-3.5 pl-3 text-right font-bold text-slate-900">
                                <div>₹{{ number_format($item->line_total, 2) }}</div>
                                @if($taxEnabled && $itemTax > 0)
                                    <div class="text-[10px] text-slate-400 font-normal">
                                        {{ $taxType === 'inclusive' ? 'Incl. ' . $taxName : '+' . $taxName }}: ₹{{ number_format($itemTax, 2) }}
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-4 text-center text-slate-400 italic">No items listed for this order.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @php
            $orderDiscounted = max(0, (float)($order->subtotal - $order->discount));
            $taxableBase = ($taxEnabled && $taxRate > 0)
                ? ($taxType === 'inclusive' 
                    ? round($orderDiscounted / (1 + ($taxRate / 100)), 2) 
                    : $orderDiscounted)
                : $orderDiscounted;

            $calcTax = (float) ($order->tax_amount > 0 
                ? $order->tax_amount 
                : ($taxEnabled && $taxRate > 0 
                    ? ($taxType === 'exclusive' 
                        ? round(($orderDiscounted * $taxRate) / 100, 2)
                        : round($orderDiscounted - $taxableBase, 2))
                    : 0));

            $isGst = str_contains(strtoupper($taxName), 'GST');
            $storeState = \App\Models\Setting::get('store_state', 'Maharashtra');
            $shippingState = $order->shipping_state ?? '';
            $isInterstate = !empty($shippingState) && strcasecmp(trim($shippingState), trim($storeState)) !== 0;

            $halfRate = round($taxRate / 2, 2);
            $halfTax = round($calcTax / 2, 2);
            $remTax = round($calcTax - $halfTax, 2);
        @endphp

        <!-- Dedicated Tax Breakup Table -->
        @if($taxEnabled && $calcTax > 0)
            <div class="mb-6 rounded-xl border border-slate-200 overflow-hidden shadow-2xs">
                <div class="bg-slate-100/80 px-4 py-2 border-b border-slate-200 flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-800 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                        Tax Breakup &amp; Calculation Summary
                    </span>
                    <span class="text-[11px] text-slate-500 font-mono">
                        Mode: <strong class="text-slate-800">{{ strtoupper($taxType) }}</strong> &bull; Total Rate: <strong class="text-indigo-700">{{ $taxRate }}%</strong>
                    </span>
                </div>
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 text-[10px] uppercase font-bold text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="py-2.5 px-4">Tax Description</th>
                            <th class="py-2.5 px-4 text-right">Taxable Value</th>
                            @if($isGst)
                                @if($isInterstate)
                                    <th class="py-2.5 px-4 text-right">IGST ({{ $taxRate }}%)</th>
                                @else
                                    <th class="py-2.5 px-4 text-right">CGST ({{ $halfRate }}%)</th>
                                    <th class="py-2.5 px-4 text-right">SGST ({{ $halfRate }}%)</th>
                                @endif
                            @else
                                <th class="py-2.5 px-4 text-right">{{ $taxName }} Rate</th>
                            @endif
                            <th class="py-2.5 px-4 text-right">Total Tax Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        <tr>
                            <td class="py-2.5 px-4 font-semibold text-slate-800">
                                {{ $taxName }} ({{ $taxRate }}%) — {{ $taxType === 'inclusive' ? 'Included in Product Price' : 'Applied on Subtotal' }}
                            </td>
                            <td class="py-2.5 px-4 text-right font-mono text-slate-700">
                                ₹{{ number_format($taxableBase, 2) }}
                            </td>
                            @if($isGst)
                                @if($isInterstate)
                                    <td class="py-2.5 px-4 text-right font-mono text-slate-700">
                                        ₹{{ number_format($calcTax, 2) }}
                                    </td>
                                @else
                                    <td class="py-2.5 px-4 text-right font-mono text-slate-700">
                                        ₹{{ number_format($halfTax, 2) }}
                                    </td>
                                    <td class="py-2.5 px-4 text-right font-mono text-slate-700">
                                        ₹{{ number_format($remTax, 2) }}
                                    </td>
                                @endif
                            @else
                                <td class="py-2.5 px-4 text-right font-mono text-slate-700">
                                    {{ $taxRate }}%
                                </td>
                            @endif
                            <td class="py-2.5 px-4 text-right font-mono font-bold text-slate-900">
                                ₹{{ number_format($calcTax, 2) }}
                            </td>
                        </tr>
                    </tbody>
                    <tfoot class="bg-slate-50/75 font-bold border-t border-slate-200 text-slate-900">
                        <tr>
                            <td class="py-2.5 px-4 uppercase text-[10px] tracking-wider text-slate-600">Total Tax:</td>
                            <td class="py-2.5 px-4 text-right font-mono">₹{{ number_format($taxableBase, 2) }}</td>
                            @if($isGst)
                                @if($isInterstate)
                                    <td class="py-2.5 px-4 text-right font-mono">₹{{ number_format($calcTax, 2) }}</td>
                                @else
                                    <td class="py-2.5 px-4 text-right font-mono">₹{{ number_format($halfTax, 2) }}</td>
                                    <td class="py-2.5 px-4 text-right font-mono">₹{{ number_format($remTax, 2) }}</td>
                                @endif
                            @else
                                <td class="py-2.5 px-4 text-right font-mono">—</td>
                            @endif
                            <td class="py-2.5 px-4 text-right font-mono text-indigo-700 font-extrabold">₹{{ number_format($calcTax, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif

        <!-- Totals & Summary -->
        <div class="pt-4 pb-8 border-t border-slate-200">
            <div class="flex flex-col sm:flex-row justify-between items-start gap-6">
                <!-- Payment notes & policy -->
                <div class="sm:max-w-xs text-xs text-slate-500 space-y-1.5">
                    <p class="font-bold text-slate-700 uppercase tracking-wide">Notice & Hygiene Policy</p>
                    <p>All intimates are sanitized and sealed. Due to hygiene standards, returns are accepted within 7 days in original unworn condition with seals intact.</p>
                    <p class="text-slate-400 text-[11px] mt-2">Computer-generated invoice. No physical signature required.</p>
                </div>

                <!-- Financial Calculation -->
                <div class="w-full sm:w-80 space-y-2">
                    <div class="flex justify-between text-sm text-slate-600">
                        <span>Items Subtotal:</span>
                        <span class="font-medium text-slate-900">₹{{ number_format($order->subtotal, 2) }}</span>
                    </div>

                    @if($order->discount > 0)
                        <div class="flex justify-between text-sm text-rose-600">
                            <span>Discount Applied:</span>
                            <span class="font-medium">-₹{{ number_format($order->discount, 2) }}</span>
                        </div>
                    @endif

                    @if($taxEnabled && $calcTax > 0)
                        <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-200/80 space-y-1 my-1">
                            <div class="flex justify-between text-xs text-slate-500">
                                <span>Taxable Amount (Excl. Tax):</span>
                                <span class="font-mono text-slate-800 font-medium">₹{{ number_format($taxableBase, 2) }}</span>
                            </div>
                            @if($isGst)
                                @if($isInterstate)
                                    <div class="flex justify-between text-xs text-slate-600">
                                        <span>IGST ({{ $taxRate }}%):</span>
                                        <span class="font-mono text-slate-800">₹{{ number_format($calcTax, 2) }}</span>
                                    </div>
                                @else
                                    <div class="flex justify-between text-xs text-slate-600">
                                        <span>CGST ({{ $halfRate }}%):</span>
                                        <span class="font-mono text-slate-800">₹{{ number_format($halfTax, 2) }}</span>
                                    </div>
                                    <div class="flex justify-between text-xs text-slate-600">
                                        <span>SGST ({{ $halfRate }}%):</span>
                                        <span class="font-mono text-slate-800">₹{{ number_format($remTax, 2) }}</span>
                                    </div>
                                @endif
                            @else
                                <div class="flex justify-between text-xs text-slate-600">
                                    <span>{{ $taxName }} ({{ $taxRate }}%):</span>
                                    <span class="font-mono text-slate-800">₹{{ number_format($calcTax, 2) }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between text-xs font-bold text-indigo-700 pt-1 border-t border-slate-200">
                                <span>Tax Breakup Total ({{ $taxType === 'inclusive' ? 'incl.' : 'added' }}):</span>
                                <span class="font-mono">₹{{ number_format($calcTax, 2) }}</span>
                            </div>
                        </div>
                    @endif

                    <div class="flex justify-between text-sm text-slate-600">
                        <span>Shipping Charge:</span>
                        <span class="font-medium text-slate-900">
                            @if($order->shipping_charge > 0)
                                ₹{{ number_format($order->shipping_charge, 2) }}
                            @else
                                <span class="text-emerald-600 font-semibold uppercase text-xs">Free</span>
                            @endif
                        </span>
                    </div>

                    @if($order->cod_charge > 0)
                        <div class="flex justify-between text-sm text-slate-600">
                            <span>COD Convenience:</span>
                            <span class="font-medium text-slate-900">₹{{ number_format($order->cod_charge, 2) }}</span>
                        </div>
                    @endif

                    <div class="border-t-2 border-slate-900 pt-2.5 flex justify-between items-baseline">
                        <span class="text-base font-extrabold text-slate-900 uppercase">Grand Total:</span>
                        <span class="text-2xl font-black text-slate-900">₹{{ number_format($order->total, 2) }}</span>
                    </div>

                    @if($taxEnabled && $calcTax > 0)
                        @if($taxType === 'inclusive')
                            <p class="text-[11px] text-right text-slate-400">Inclusive of {{ $taxName }} ({{ $taxRate }}%): ₹{{ number_format($calcTax, 2) }}</p>
                        @else
                            <p class="text-[11px] text-right text-slate-400">{{ $taxName }} ({{ $taxRate }}%) applied on taxable total</p>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        <!-- Footer Bar -->
        <div class="border-t border-slate-200 pt-6 text-center text-xs text-slate-400">
            <p class="font-semibold text-slate-600">{{ $invoiceFooter }}</p>
            <p class="mt-1">Need help with your order? Reach out anytime at <a href="mailto:{{ $supportEmail }}" class="text-rose-600 underline">{{ $supportEmail }}</a> or WhatsApp {{ $supportPhone }}</p>
        </div>
    </div>

</body>
</html>
