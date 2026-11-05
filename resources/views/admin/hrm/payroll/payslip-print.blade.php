<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payslip - {{ $item->payslip_number }} - {{ $item->user?->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body { background: #fff !important; }
            .no-print { display: none !important; }
            .print-container { border: none !important; box-shadow: none !important; margin: 0 !important; width: 100% !important; max-width: 100% !important; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 font-sans p-4 sm:p-8">
    <div class="no-print max-w-3xl mx-auto mb-4 flex justify-between items-center">
        <a href="{{ route('admin.hrm.payroll.payslips') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">
            ← Back to Payslips Directory
        </a>
        <button onclick="window.print()" class="px-4 py-2 bg-indigo-600 text-white rounded-xl text-xs font-bold hover:bg-indigo-700 shadow-sm">
            🖨️ Print / Save as PDF
        </button>
    </div>

    <!-- Printable Payslip Voucher Container -->
    <div class="print-container max-w-3xl mx-auto bg-white border border-slate-200 rounded-2xl p-8 shadow-sm">
        <!-- Voucher Header -->
        <div class="flex justify-between items-start border-b border-slate-200 pb-6 mb-6">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="w-8 h-8 rounded-lg bg-indigo-600 text-white font-black flex items-center justify-center text-sm">N</span>
                    <span class="text-lg font-black tracking-tight text-slate-900">{{ config('app.name', 'Project Nexon') }}</span>
                </div>
                <p class="text-xs text-slate-500">Luxury Retail, Atelier &amp; Lifestyle Private Limited</p>
                <p class="text-xs text-slate-400">GSTIN: 29AAAAA0000A1Z5 • CIN: U74999KA2026PTC123456</p>
            </div>
            <div class="text-right">
                <span class="inline-block px-3 py-1 bg-indigo-50 text-indigo-700 text-xs font-bold rounded-lg border border-indigo-200 uppercase tracking-wider mb-1">
                    Salary Payslip
                </span>
                <div class="text-sm font-bold text-slate-900 font-mono">{{ $item->payslip_number }}</div>
                <div class="text-xs text-slate-500">Pay Period: <span class="font-bold text-slate-800">{{ $item->payroll?->period_month ?? date('Y-m') }}</span></div>
            </div>
        </div>

        <!-- Employee & Bank Details Grid -->
        <div class="grid grid-cols-2 gap-4 bg-slate-50/70 p-4 rounded-xl border border-slate-200/60 mb-6 text-xs">
            <div>
                <div class="text-slate-400 uppercase font-semibold text-[10px]">Employee Details</div>
                <div class="text-sm font-black text-slate-900 mt-0.5">{{ $item->user?->name ?? 'Staff' }}</div>
                <div class="text-slate-600 mt-0.5"><span class="font-semibold">Designation:</span> {{ $item->designation }}</div>
                <div class="text-slate-600"><span class="font-semibold">Department:</span> {{ $item->department }}</div>
                <div class="text-slate-600"><span class="font-semibold">Email:</span> {{ $item->user?->email }}</div>
            </div>
            <div>
                <div class="text-slate-400 uppercase font-semibold text-[10px]">Bank &amp; Attendance Metadata</div>
                <div class="text-slate-700 mt-0.5"><span class="font-semibold">Bank:</span> {{ $item->bank_name }}</div>
                <div class="text-slate-700 font-mono"><span class="font-semibold">A/C:</span> {{ $item->bank_account_no ?? 'A/C PENDING' }}</div>
                <div class="text-slate-700 font-mono"><span class="font-semibold">IFSC:</span> {{ $item->bank_ifsc ?? 'HDFC0000123' }}</div>
                <div class="text-slate-700 mt-1"><span class="font-semibold">Days:</span> {{ $item->present_days }} / {{ $item->working_days }} Days Worked (Leaves: {{ $item->leave_days }})</div>
            </div>
        </div>

        <!-- Earnings & Deductions Tables -->
        <div class="grid grid-cols-2 gap-6 mb-6">
            <!-- Earnings -->
            <div>
                <div class="border-b-2 border-indigo-600 pb-1 mb-2 font-bold text-xs uppercase tracking-wider text-slate-800">
                    Earnings (A)
                </div>
                <table class="w-full text-xs">
                    <tbody class="divide-y divide-slate-100">
                        <tr>
                            <td class="py-2 text-slate-600">Basic Salary</td>
                            <td class="py-2 text-right font-mono font-semibold text-slate-900">₹{{ number_format($item->basic_salary, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 text-slate-600">House Rent Allowance (HRA)</td>
                            <td class="py-2 text-right font-mono font-semibold text-slate-900">₹{{ number_format($item->hra, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 text-slate-600">Transport / Travel Allowance</td>
                            <td class="py-2 text-right font-mono font-semibold text-slate-900">₹{{ number_format($item->transport_allowance, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 text-slate-600">Performance Incentive &amp; Bonus</td>
                            <td class="py-2 text-right font-mono font-semibold text-slate-900">₹{{ number_format($item->performance_bonus, 2) }}</td>
                        </tr>
                        <tr class="font-bold text-slate-900 bg-slate-50">
                            <td class="py-2.5 px-2">Total Gross Earnings</td>
                            <td class="py-2.5 px-2 text-right font-mono text-sm">₹{{ number_format($item->gross_salary, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Deductions -->
            <div>
                <div class="border-b-2 border-purple-600 pb-1 mb-2 font-bold text-xs uppercase tracking-wider text-slate-800">
                    Statutory Deductions (B)
                </div>
                <table class="w-full text-xs">
                    <tbody class="divide-y divide-slate-100">
                        <tr>
                            <td class="py-2 text-slate-600">Provident Fund (PF - 12%)</td>
                            <td class="py-2 text-right font-mono font-semibold text-slate-900">₹{{ number_format($item->pf_deduction, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 text-slate-600">Employee State Insurance (ESI)</td>
                            <td class="py-2 text-right font-mono font-semibold text-slate-900">₹{{ number_format($item->esi_deduction, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 text-slate-600">Tax Deducted at Source (TDS)</td>
                            <td class="py-2 text-right font-mono font-semibold text-slate-900">₹{{ number_format($item->tds_tax, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 text-slate-400 italic">Professional Tax / Other</td>
                            <td class="py-2 text-right font-mono text-slate-400">₹0.00</td>
                        </tr>
                        <tr class="font-bold text-purple-900 bg-purple-50">
                            <td class="py-2.5 px-2">Total Deductions</td>
                            <td class="py-2.5 px-2 text-right font-mono text-sm">₹{{ number_format($item->total_deductions, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Net Payable Block -->
        <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 flex justify-between items-center mb-8">
            <div>
                <span class="text-xs uppercase font-bold text-emerald-800 tracking-wider">Net Take-Home Pay (A - B)</span>
                <p class="text-[11px] text-emerald-700 mt-0.5">Disbursed via Electronic Corporate Bank Transfer (NEFT)</p>
            </div>
            <div class="text-2xl font-black text-emerald-700 font-mono tracking-tight">
                {{ $item->formatted_net }}
            </div>
        </div>

        <!-- Signatures & Legal Disclaimer -->
        <div class="flex justify-between items-end border-t border-slate-200 pt-6 text-xs text-slate-500">
            <div>
                <p class="text-[10px] text-slate-400">Generated automatically by {{ config('app.name', 'Project Nexon') }} HRM Suite.</p>
                <p class="text-[10px] text-slate-400">Confidential document intended solely for the named employee.</p>
            </div>
            <div class="text-center">
                <div class="w-32 h-10 border-b border-dashed border-slate-400 mx-auto mb-1"></div>
                <div class="text-[11px] font-bold text-slate-800">Authorized Signatory</div>
                <div class="text-[10px] text-slate-400">Finance &amp; Payroll Controller</div>
            </div>
        </div>
    </div>
</body>
</html>

