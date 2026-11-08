{{--
    ======================================================================
    ADMIN HRM: PAYSLIPS DIRECTORY & DISPATCH
    ======================================================================
    File: resources/views/admin/hrm/payroll/payslips.blade.php
    Controller: App\Http\Controllers\Admin\PayrollController@payslips
    Route Context: GET /admin/hrm/payroll/payslips
    Responsibility: Staff payslip repository, PDF downloads, and email distribution
    Features:
      - Filter payslips by month, year, department, and employee
      - Bulk email payslips to staff with password-protected PDF attachments
      - Print / view individual employee pay voucher
--}}
@extends('admin.layout')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-indigo-600 uppercase tracking-wider mb-1">
                <span>Human Resources</span>
                <span>•</span>
                <span class="text-slate-500 font-medium">Domain 5: Payslip Records</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Employee Payslips Directory</h1>
            <p class="text-sm text-slate-500 mt-0.5">Search, review, and print individual statutory wage slips across all pay runs.</p>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="{{ route('admin.hrm.payroll.run') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                New Payroll Run
            </a>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
        <form method="GET" action="{{ route('admin.hrm.payroll.payslips') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
            <!-- Search -->
            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-slate-600 mb-1">Search Employee / Slip #</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="e.g. SLIP-202610-0001 or staff name..."
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
            </div>

            <!-- Month Filter -->
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Payroll Month</label>
                <select name="month" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:bg-white">
                    <option value="">All Months</option>
                    @foreach($payrollMonths as $m)
                        <option value="{{ $m }}" {{ request('month') === $m ? 'selected' : '' }}>{{ $m }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Department Filter & Submit -->
            <div class="flex items-center gap-2">
                <div class="flex-1">
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Department</label>
                    <select name="department" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:bg-white">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept }}" {{ request('department') === $dept ? 'selected' : '' }}>{{ $dept }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="mt-5 px-4 py-2 bg-slate-800 text-white rounded-xl text-xs font-semibold hover:bg-slate-900 transition">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Payslips Table -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
        @if($payslips->isEmpty())
            <div class="text-center py-12 border-2 border-dashed border-slate-200 rounded-xl">
                <svg class="w-10 h-10 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <p class="text-sm font-semibold text-slate-600">No payslips found</p>
                <p class="text-xs text-slate-400 mt-1">Generate a monthly payroll run to create employee payslips.</p>
                <a href="{{ route('admin.hrm.payroll.run') }}" class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-xl text-xs font-semibold hover:bg-indigo-700 transition">
                    Run Payroll Now
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200/70 text-slate-400 uppercase text-[10px] tracking-wider">
                            <th class="pb-3 font-semibold">Slip Serial</th>
                            <th class="pb-3 font-semibold">Staff Member</th>
                            <th class="pb-3 font-semibold">Department</th>
                            <th class="pb-3 font-semibold">Pay Period</th>
                            <th class="pb-3 font-semibold">Gross Pay</th>
                            <th class="pb-3 font-semibold">Total Deductions</th>
                            <th class="pb-3 font-semibold">Net Disbursed</th>
                            <th class="pb-3 font-semibold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @foreach($payslips as $slip)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-3 font-mono font-bold text-slate-900">{{ $slip->payslip_number }}</td>
                                <td class="py-3">
                                    <div class="font-bold text-slate-900">{{ $slip->user?->name ?? 'Staff' }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $slip->designation }}</div>
                                </td>
                                <td class="py-3 text-slate-600">{{ $slip->department }}</td>
                                <td class="py-3 font-semibold text-indigo-600">{{ $slip->payroll?->period_month ?? '-' }}</td>
                                <td class="py-3 font-mono font-bold text-slate-900">{{ $slip->formatted_gross }}</td>
                                <td class="py-3 font-mono text-purple-600">{{ $slip->formatted_deductions }}</td>
                                <td class="py-3 font-mono font-black text-emerald-600">{{ $slip->formatted_net }}</td>
                                <td class="py-3 text-right">
                                    <a href="{{ route('admin.hrm.payroll.payslip.print', $slip) }}" target="_blank"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 rounded-xl transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                        Print Voucher
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $payslips->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

