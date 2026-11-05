@extends('admin.layout')

@section('content')
<div class="space-y-6">
    <!-- Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-indigo-600 uppercase tracking-wider mb-1">
                <span>Human Resources</span>
                <span>•</span>
                <span class="text-slate-500 font-medium">Domain 5: Pre-Disbursement Audit</span>
            </div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Payroll Review &amp; Anomaly Ledger</h1>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $payroll->status_badge['bg'] }}">
                    {{ $payroll->status_badge['label'] }}
                </span>
            </div>
            <p class="text-sm text-slate-500 mt-0.5">Audit {{ $payroll->payroll_code }} (Period: {{ $payroll->period_month }}) with {{ $payroll->employees_count }} staff payslips.</p>
        </div>
        <div class="flex items-center gap-2.5 flex-wrap">
            <!-- Bank File Export -->
            <a href="{{ route('admin.hrm.payroll.bank-file', $payroll) }}" 
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition-colors shadow-2xs">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export NEFT/RTGS Batch CSV
            </a>

            <!-- Status Transition Buttons -->
            @if($payroll->status === 'draft')
                <form action="{{ route('admin.hrm.payroll.status', $payroll) }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="status" value="reviewed">
                    <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-xl text-xs font-semibold hover:bg-purple-700 shadow-sm transition">
                        ✓ Mark Reviewed by HR
                    </button>
                </form>
            @elseif($payroll->status === 'reviewed')
                <form action="{{ route('admin.hrm.payroll.status', $payroll) }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="status" value="approved">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-xl text-xs font-semibold hover:bg-blue-700 shadow-sm transition">
                        ✓ Finance Approval
                    </button>
                </form>
            @elseif($payroll->status === 'approved')
                <form action="{{ route('admin.hrm.payroll.status', $payroll) }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="status" value="disbursed">
                    <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-xl text-xs font-bold hover:bg-emerald-700 shadow-sm transition">
                        ✓ Authorize Disbursal (Paid)
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Anomaly Detection Flags -->
    @if(!empty($anomalies))
        <div class="bg-rose-50 border border-rose-200 rounded-2xl p-5">
            <div class="flex items-center gap-2.5 text-rose-800 font-bold text-sm mb-2">
                <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                Audit Anomaly Warnings Detected ({{ count($anomalies) }} Issues Found)
            </div>
            <p class="text-xs text-rose-700 mb-3">Please review the flagged employee records before authorizing bank disbursement:</p>
            <ul class="space-y-1.5 text-xs text-rose-800 list-disc list-inside">
                @foreach($anomalies as $itemId => $issues)
                    @php $item = $payroll->items->firstWhere('id', $itemId); @endphp
                    <li>
                        <span class="font-bold">{{ $item?->user?->name ?? 'Staff' }}:</span>
                        {{ implode(' • ', $issues) }}
                    </li>
                @endforeach
            </ul>
        </div>
    @else
        <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-4 flex items-center gap-3 text-emerald-800 text-xs">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div>
                <span class="font-bold">No High-Risk Anomalies:</span> All bank IFSC codes and net calculations verified without critical deviations.
            </div>
        </div>
    @endif

    <!-- Batch Summary Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Gross Wages</div>
            <div class="text-2xl font-black text-slate-900 mt-1">{{ $payroll->formatted_gross }}</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">PF + ESI + TDS</div>
            <div class="text-2xl font-black text-purple-600 mt-1">₹{{ number_format($payroll->total_deductions, 2) }}</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Net Bank Disbursal</div>
            <div class="text-2xl font-black text-emerald-600 mt-1">{{ $payroll->formatted_net }}</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Approved By</div>
            <div class="text-sm font-bold text-slate-900 mt-2">
                {{ $payroll->approver ? $payroll->approver->name : 'Pending Approval' }}
            </div>
            <div class="text-[10px] text-slate-400">
                {{ $payroll->approved_at ? $payroll->approved_at->format('d M Y, h:i A') : 'Awaiting sign-off' }}
            </div>
        </div>
    </div>

    <!-- Detailed Line Item Audit Table -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-base font-bold text-slate-900">Staff Payslip Ledger Breakdown</h3>
                <p class="text-xs text-slate-400">Individual bank accounts, allowances and verified payslip serials.</p>
            </div>
            <a href="{{ route('admin.hrm.payroll.payslips', ['month' => $payroll->period_month]) }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                View All in Payslips Directory →
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200/70 text-slate-400 uppercase text-[10px] tracking-wider">
                        <th class="pb-3 font-semibold">Slip #</th>
                        <th class="pb-3 font-semibold">Staff Member</th>
                        <th class="pb-3 font-semibold">Designation &amp; Dept</th>
                        <th class="pb-3 font-semibold">Bank Details</th>
                        <th class="pb-3 font-semibold">Gross</th>
                        <th class="pb-3 font-semibold">Deductions</th>
                        <th class="pb-3 font-semibold">Net Pay</th>
                        <th class="pb-3 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                    @foreach($payroll->items as $item)
                        <tr class="hover:bg-slate-50/60 transition-colors {{ isset($anomalies[$item->id]) ? 'bg-rose-50/40' : '' }}">
                            <td class="py-3 font-bold text-slate-900 font-mono text-[11px]">{{ $item->payslip_number }}</td>
                            <td class="py-3">
                                <div class="font-bold text-slate-900">{{ $item->user?->name ?? 'Staff' }}</div>
                                <div class="text-[10px] text-slate-400">{{ $item->user?->email }}</div>
                            </td>
                            <td class="py-3">
                                <div class="font-semibold text-slate-800">{{ $item->designation }}</div>
                                <div class="text-[10px] text-indigo-600">{{ $item->department }}</div>
                            </td>
                            <td class="py-3">
                                <div class="text-slate-800 font-mono text-[11px]">{{ $item->bank_name }}: {{ $item->bank_account_no ?? 'A/C PENDING' }}</div>
                                <div class="text-[10px] text-slate-400 font-mono">IFSC: {{ $item->bank_ifsc ?? 'PENDING' }}</div>
                            </td>
                            <td class="py-3 font-mono font-bold text-slate-900">{{ $item->formatted_gross }}</td>
                            <td class="py-3 font-mono text-purple-600">{{ $item->formatted_deductions }}</td>
                            <td class="py-3 font-mono font-black text-emerald-600">{{ $item->formatted_net }}</td>
                            <td class="py-3 text-right">
                                <a href="{{ route('admin.hrm.payroll.payslip.print', $item) }}" target="_blank"
                                   class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                    Print Slip
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

