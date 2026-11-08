{{--
    ======================================================================
    ADMIN MODULE: HRM PAYROLL OVERVIEW
    ======================================================================
    File: resources/views/admin/hrm/payroll/overview.blade.php
    Responsibility: Back-office administration for HRM PAYROLL OVERVIEW
    Features:
      - Standard administration UI following the Nex-on design guidelines
      - Accessible controls, Tailwind CSS styling, and reactive Alpine.js components
    Security & Access Control:
      - Guarded by Laravel authentication and RBAC permission middleware
    Operations & Maintenance:
      - Integrates with master administration shell (@extends('admin.layout'))
--}}
@extends('admin.layout')

@section('content')
<div class="space-y-6">
    <!-- Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-indigo-600 uppercase tracking-wider mb-1">
                <span>Human Resources</span>
                <span>•</span>
                <span class="text-slate-500 font-medium">Domain 5: Compensation Console</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Payroll Overview & Expenditure</h1>
            <p class="text-sm text-slate-500 mt-0.5">High-level salary obligations, statutory deductions (PF, ESI, TDS), and department wage analytics.</p>
        </div>
        <div class="flex items-center gap-2.5 flex-wrap">
            <a href="{{ route('admin.hrm.payroll.payslips') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200/80 transition-colors shadow-2xs">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Browse Payslips
            </a>
            <a href="{{ route('admin.hrm.payroll.run') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Run Monthly Payroll
            </a>
        </div>
    </div>

    <!-- 4 High-Impact KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Monthly Gross Salary Obligation -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-xs font-semibold uppercase tracking-wider">Latest Gross Payroll</span>
                <span class="p-2 bg-indigo-50 text-indigo-600 rounded-xl">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </span>
            </div>
            <div class="text-2xl font-black text-slate-900 tracking-tight">
                {{ $latestPayroll ? $latestPayroll->formatted_gross : '₹0.00' }}
            </div>
            <p class="text-xs text-slate-400 mt-1">Period: <span class="font-semibold text-slate-600">{{ $latestPayroll ? $latestPayroll->period_month : date('Y-m') }}</span></p>
        </div>

        <!-- Net Payable Outflow -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-xs font-semibold uppercase tracking-wider">Net Bank Disbursal</span>
                <span class="p-2 bg-emerald-50 text-emerald-600 rounded-xl">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <div class="text-2xl font-black text-emerald-600 tracking-tight">
                {{ $latestPayroll ? $latestPayroll->formatted_net : '₹0.00' }}
            </div>
            <p class="text-xs text-slate-400 mt-1">Post all tax &amp; statutory deductions</p>
        </div>

        <!-- Statutory Deductions (PF / ESI / TDS) -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-xs font-semibold uppercase tracking-wider">Statutory Deductions</span>
                <span class="p-2 bg-purple-50 text-purple-600 rounded-xl">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </span>
            </div>
            <div class="text-2xl font-black text-purple-600 tracking-tight">
                ₹{{ number_format($latestPayroll ? $latestPayroll->total_deductions : 0, 2) }}
            </div>
            <p class="text-xs text-slate-400 mt-1">PF 12% + ESI 0.75% + TDS withholding</p>
        </div>

        <!-- Staff Headcount Processed -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-xs font-semibold uppercase tracking-wider">Active Staff Processed</span>
                <span class="p-2 bg-blue-50 text-blue-600 rounded-xl">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </span>
            </div>
            <div class="text-2xl font-black text-slate-900 tracking-tight">
                {{ $latestPayroll ? $latestPayroll->employees_count : $totalEmployeesProcessed }} Staff
            </div>
            <p class="text-xs text-slate-400 mt-1">Across Store, Warehouse &amp; Atelier</p>
        </div>
    </div>

    <!-- Department-wise Wage Breakdown & Monthly Trends -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Department Wage Distribution -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs lg:col-span-1">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-slate-900 tracking-tight">Department Wage Share</h3>
                <span class="text-[10px] font-semibold uppercase px-2 py-0.5 bg-slate-100 text-slate-600 rounded-lg">Expenditure</span>
            </div>
            <div class="space-y-4">
                @foreach($departmentBreakdown as $dept)
                    <div>
                        <div class="flex justify-between items-center text-xs mb-1.5">
                            <span class="font-semibold text-slate-700">{{ $dept->department }}</span>
                            <span class="font-bold text-slate-900">₹{{ number_format($dept->total_gross, 2) }}</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                            @php
                                $percent = $latestPayroll && $latestPayroll->total_gross > 0 ? min(100, round(($dept->total_gross / $latestPayroll->total_gross) * 100)) : 25;
                            @endphp
                            <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $percent }}%"></div>
                        </div>
                        <div class="text-[10px] text-slate-400 mt-0.5">{{ $dept->staff_count ?? 1 }} staff enrolled ({{ $percent }}% of total)</div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Historical Payroll Runs Table -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs lg:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 tracking-tight">Payroll Run Audit History</h3>
                    <p class="text-xs text-slate-400">Monthly compensation batches and lifecycle status.</p>
                </div>
                <a href="{{ route('admin.hrm.payroll.run') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                    + New Run
                </a>
            </div>

            @if($historicalRuns->isEmpty())
                <div class="text-center py-12 border-2 border-dashed border-slate-200 rounded-xl">
                    <svg class="w-10 h-10 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <p class="text-sm font-semibold text-slate-600">No payroll runs recorded yet</p>
                    <p class="text-xs text-slate-400 mt-1">Start by executing your first monthly payroll calculation.</p>
                    <a href="{{ route('admin.hrm.payroll.run') }}" class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-xl text-xs font-semibold hover:bg-indigo-700 transition">
                        Run Payroll Wizard
                    </a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-slate-200/70 text-slate-400 uppercase text-[10px] tracking-wider">
                                <th class="pb-3 font-semibold">Batch Code</th>
                                <th class="pb-3 font-semibold">Month</th>
                                <th class="pb-3 font-semibold">Employees</th>
                                <th class="pb-3 font-semibold">Total Net Outflow</th>
                                <th class="pb-3 font-semibold">Status</th>
                                <th class="pb-3 font-semibold text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                            @foreach($historicalRuns as $run)
                                <tr class="hover:bg-slate-50/60 transition-colors">
                                    <td class="py-3 font-bold text-slate-900 font-mono">{{ $run->payroll_code }}</td>
                                    <td class="py-3">{{ $run->period_month }}</td>
                                    <td class="py-3">{{ $run->employees_count }} staff</td>
                                    <td class="py-3 font-bold text-emerald-600">{{ $run->formatted_net }}</td>
                                    <td class="py-3">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold border {{ $run->status_badge['bg'] }}">
                                            {{ $run->status_badge['label'] }}
                                        </span>
                                    </td>
                                    <td class="py-3 text-right">
                                        <a href="{{ route('admin.hrm.payroll.review', $run) }}" 
                                           class="inline-flex items-center px-2.5 py-1 text-xs font-semibold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition">
                                            Audit Ledger →
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

