{{--
    ======================================================================
    ADMIN MODULE: HRM PAYROLL RUN
    ======================================================================
    File: resources/views/admin/hrm/payroll/run.blade.php
    Responsibility: Back-office administration for HRM PAYROLL RUN
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
<div class="space-y-6" x-data="{
    step: 1,
    periodMonth: '{{ $selectedMonth }}',
    items: [
        @foreach($staffMembers as $index => $staff)
        {
            user_id: {{ $staff->id }},
            name: '{{ addslashes($staff->name) }}',
            email: '{{ addslashes($staff->email) }}',
            role: '{{ $staff->role ?? "Staff" }}',
            designation: '{{ $staff->role === "admin" ? "Store General Manager" : ($staff->role === "delivery" ? "Logistics Specialist" : "Senior Retail Associate") }}',
            department: '{{ $staff->role === "admin" ? "Store Management & Admin" : ($staff->role === "delivery" ? "Warehouse & Logistics" : "Retail Store Sales") }}',
            working_days: 30,
            present_days: 30,
            basic_salary: {{ $staff->role === "admin" ? 65000 : ($staff->role === "delivery" ? 22000 : 28000) }},
            hra: {{ $staff->role === "admin" ? 18000 : ($staff->role === "delivery" ? 5000 : 7000) }},
            transport_allowance: 2500,
            performance_bonus: 0,
            tds_tax: {{ $staff->role === "admin" ? 4500 : 0 }},
            bank_name: 'HDFC Bank',
            bank_account_no: '50100{{ rand(1000000, 9999999) }}',
            bank_ifsc: 'HDFC0000123'
        },
        @endforeach
    ],
    get gross(i) {
        let it = this.items[i];
        return (parseFloat(it.basic_salary) || 0) + (parseFloat(it.hra) || 0) + (parseFloat(it.transport_allowance) || 0) + (parseFloat(it.performance_bonus) || 0);
    },
    get pf(i) {
        let it = this.items[i];
        return Math.round((parseFloat(it.basic_salary) || 0) * 0.12 * 100) / 100;
    },
    get esi(i) {
        let g = this.gross(i);
        return (g <= 21000 && g > 0) ? Math.round(g * 0.0075 * 100) / 100 : 0;
    },
    get totalDeductions(i) {
        let it = this.items[i];
        return this.pf(i) + this.esi(i) + (parseFloat(it.tds_tax) || 0);
    },
    get net(i) {
        return Math.max(0, this.gross(i) - this.totalDeductions(i));
    },
    get totalNetAll() {
        return this.items.reduce((acc, _, idx) => acc + this.net(idx), 0);
    },
    get totalGrossAll() {
        return this.items.reduce((acc, _, idx) => acc + this.gross(idx), 0);
    },
    get totalDeductionsAll() {
        return this.items.reduce((acc, _, idx) => acc + this.totalDeductions(idx), 0);
    }
}">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-indigo-600 uppercase tracking-wider mb-1">
                <span>Human Resources</span>
                <span>•</span>
                <span class="text-slate-500 font-medium">Domain 5: Payroll Engine</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Run Monthly Payroll Wizard</h1>
            <p class="text-sm text-slate-500 mt-0.5">Automated 3-step calculation wizard computing gross pay, allowances, PF, ESI &amp; net bank transfers.</p>
        </div>
        <a href="{{ route('admin.hrm.payroll.overview') }}" 
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition-colors">
            ← Back to Overview
        </a>
    </div>

    <!-- Wizard Stepper Navigation -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
        <div class="flex items-center justify-between max-w-2xl mx-auto">
            <!-- Step 1 Indicator -->
            <button type="button" @click="step = 1" class="flex items-center gap-3 text-left focus:outline-none">
                <span class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all"
                      :class="step >= 1 ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-100 text-slate-400'">1</span>
                <div>
                    <div class="text-xs font-bold" :class="step >= 1 ? 'text-slate-900' : 'text-slate-400'">Attendance &amp; Cycle</div>
                    <div class="text-[10px] text-slate-400">Working days &amp; staff</div>
                </div>
            </button>
            <div class="w-12 h-0.5" :class="step >= 2 ? 'bg-indigo-600' : 'bg-slate-200'"></div>

            <!-- Step 2 Indicator -->
            <button type="button" @click="step = 2" class="flex items-center gap-3 text-left focus:outline-none">
                <span class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all"
                      :class="step >= 2 ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-100 text-slate-400'">2</span>
                <div>
                    <div class="text-xs font-bold" :class="step >= 2 ? 'text-slate-900' : 'text-slate-400'">Wages &amp; Allowances</div>
                    <div class="text-[10px] text-slate-400">Earnings &amp; deductions</div>
                </div>
            </button>
            <div class="w-12 h-0.5" :class="step >= 3 ? 'bg-indigo-600' : 'bg-slate-200'"></div>

            <!-- Step 3 Indicator -->
            <button type="button" @click="step = 3" class="flex items-center gap-3 text-left focus:outline-none">
                <span class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all"
                      :class="step >= 3 ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-100 text-slate-400'">3</span>
                <div>
                    <div class="text-xs font-bold" :class="step >= 3 ? 'text-slate-900' : 'text-slate-400'">Summary &amp; Lock</div>
                    <div class="text-[10px] text-slate-400">Pre-disbursal preview</div>
                </div>
            </button>
        </div>
    </div>

    <!-- Main Wizard Form -->
    <form action="{{ route('admin.hrm.payroll.run.process') }}" method="POST">
        @csrf

        <!-- STEP 1: Attendance & Month Configuration -->
        <div x-show="step === 1" class="space-y-6">
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
                <h3 class="text-base font-bold text-slate-900 mb-4">Step 1: Select Payroll Month &amp; Working Days</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-w-2xl mb-6">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Payroll Period Month (YYYY-MM)</label>
                        <input type="month" name="period_month" x-model="periodMonth" required
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-900 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Batch Run Title</label>
                        <input type="text" name="title" :value="'Staff Monthly Payroll - ' + periodMonth" required
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-900 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-slate-200/70 text-slate-400 uppercase text-[10px] tracking-wider">
                                <th class="pb-3 font-semibold">Employee</th>
                                <th class="pb-3 font-semibold">Role &amp; Department</th>
                                <th class="pb-3 font-semibold">Working Days</th>
                                <th class="pb-3 font-semibold">Days Present</th>
                                <th class="pb-3 font-semibold">Unpaid Leaves</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                            <template x-for="(item, index) in items" :key="item.user_id">
                                <tr>
                                    <td class="py-3">
                                        <div class="font-bold text-slate-900" x-text="item.name"></div>
                                        <div class="text-[10px] text-slate-400" x-text="item.email"></div>
                                    </td>
                                    <td class="py-3">
                                        <div class="font-semibold text-slate-800" x-text="item.designation"></div>
                                        <div class="text-[10px] text-indigo-600" x-text="item.department"></div>
                                    </td>
                                    <td class="py-3">
                                        <input type="number" :name="'items[' + index + '][working_days]'" x-model.number="item.working_days" min="1" max="31"
                                               class="w-20 px-2 py-1 bg-slate-50 border border-slate-200 rounded-lg text-xs font-bold text-center">
                                    </td>
                                    <td class="py-3">
                                        <input type="number" :name="'items[' + index + '][present_days]'" x-model.number="item.present_days" min="0" max="31" step="0.5"
                                               class="w-20 px-2 py-1 bg-slate-50 border border-slate-200 rounded-lg text-xs font-bold text-center text-emerald-600">
                                    </td>
                                    <td class="py-3 font-semibold text-rose-500" x-text="Math.max(0, item.working_days - item.present_days) + ' days'"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-end mt-6">
                    <button type="button" @click="step = 2" class="px-6 py-2.5 bg-indigo-600 text-white rounded-xl text-xs font-semibold hover:bg-indigo-700 shadow-sm transition">
                        Next: Earnings &amp; Deductions →
                    </button>
                </div>
            </div>
        </div>

        <!-- STEP 2: Wage Structure, Allowances & Deductions Engine -->
        <div x-show="step === 2" class="space-y-6">
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Step 2: Salary Earnings &amp; Statutory Deductions</h3>
                        <p class="text-xs text-slate-400">Basic salary, HRA, travel allowance, and statutory calculations (PF 12%, ESI 0.75%, TDS).</p>
                    </div>
                    <div class="text-right">
                        <span class="text-xs text-slate-400">Total Run Net: </span>
                        <span class="text-base font-black text-emerald-600 font-mono" x-text="'₹' + totalNetAll.toLocaleString('en-IN', {minimumFractionDigits: 2})"></span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-slate-200/70 text-slate-400 uppercase text-[10px] tracking-wider">
                                <th class="pb-3 font-semibold">Employee</th>
                                <th class="pb-3 font-semibold">Basic Pay</th>
                                <th class="pb-3 font-semibold">HRA</th>
                                <th class="pb-3 font-semibold">Travel</th>
                                <th class="pb-3 font-semibold">Bonus</th>
                                <th class="pb-3 font-semibold">Gross</th>
                                <th class="pb-3 font-semibold">PF (12%)</th>
                                <th class="pb-3 font-semibold">ESI (0.75%)</th>
                                <th class="pb-3 font-semibold">TDS</th>
                                <th class="pb-3 font-semibold text-right">Net Payable</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                            <template x-for="(item, index) in items" :key="item.user_id">
                                <tr>
                                    <td class="py-3">
                                        <div class="font-bold text-slate-900" x-text="item.name"></div>
                                        <input type="hidden" :name="'items[' + index + '][user_id]'" :value="item.user_id">
                                        <input type="hidden" :name="'items[' + index + '][designation]'" :value="item.designation">
                                        <input type="hidden" :name="'items[' + index + '][department]'" :value="item.department">
                                        <input type="hidden" :name="'items[' + index + '][bank_name]'" :value="item.bank_name">
                                        <input type="hidden" :name="'items[' + index + '][bank_account_no]'" :value="item.bank_account_no">
                                        <input type="hidden" :name="'items[' + index + '][bank_ifsc]'" :value="item.bank_ifsc">
                                    </td>
                                    <td class="py-3">
                                        <input type="number" :name="'items[' + index + '][basic_salary]'" x-model.number="item.basic_salary"
                                               class="w-24 px-2 py-1 bg-slate-50 border border-slate-200 rounded-lg text-xs font-semibold">
                                    </td>
                                    <td class="py-3">
                                        <input type="number" :name="'items[' + index + '][hra]'" x-model.number="item.hra"
                                               class="w-20 px-2 py-1 bg-slate-50 border border-slate-200 rounded-lg text-xs font-semibold">
                                    </td>
                                    <td class="py-3">
                                        <input type="number" :name="'items[' + index + '][transport_allowance]'" x-model.number="item.transport_allowance"
                                               class="w-20 px-2 py-1 bg-slate-50 border border-slate-200 rounded-lg text-xs font-semibold">
                                    </td>
                                    <td class="py-3">
                                        <input type="number" :name="'items[' + index + '][performance_bonus]'" x-model.number="item.performance_bonus"
                                               class="w-20 px-2 py-1 bg-slate-50 border border-slate-200 rounded-lg text-xs font-semibold">
                                    </td>
                                    <td class="py-3 font-bold text-slate-900 font-mono" x-text="'₹' + gross(index).toFixed(2)"></td>
                                    <td class="py-3 text-purple-600 font-mono text-xs" x-text="'₹' + pf(index).toFixed(2)"></td>
                                    <td class="py-3 text-purple-600 font-mono text-xs" x-text="'₹' + esi(index).toFixed(2)"></td>
                                    <td class="py-3">
                                        <input type="number" :name="'items[' + index + '][tds_tax]'" x-model.number="item.tds_tax"
                                               class="w-20 px-2 py-1 bg-slate-50 border border-slate-200 rounded-lg text-xs font-semibold text-rose-600">
                                    </td>
                                    <td class="py-3 text-right font-black text-emerald-600 font-mono text-sm" x-text="'₹' + net(index).toFixed(2)"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-between items-center mt-6">
                    <button type="button" @click="step = 1" class="px-5 py-2.5 bg-slate-100 text-slate-700 rounded-xl text-xs font-semibold hover:bg-slate-200 transition">
                        ← Back to Attendance
                    </button>
                    <button type="button" @click="step = 3" class="px-6 py-2.5 bg-indigo-600 text-white rounded-xl text-xs font-semibold hover:bg-indigo-700 shadow-sm transition">
                        Next: Final Review &amp; Lock →
                    </button>
                </div>
            </div>
        </div>

        <!-- STEP 3: Summary, Confirmation & Lock Draft -->
        <div x-show="step === 3" class="space-y-6">
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
                <h3 class="text-base font-bold text-slate-900 mb-2">Step 3: Confirm Payroll Draft Generation</h3>
                <p class="text-xs text-slate-500 mb-6">Review total wage commitments before saving this calculation batch. You will review and approve in the audit ledger next.</p>

                <!-- 3 Consolidated Metric Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-200">
                        <div class="text-xs font-bold text-slate-500 uppercase tracking-wider">Gross Payroll Commitment</div>
                        <div class="text-xl font-black text-slate-900 mt-1 font-mono" x-text="'₹' + totalGrossAll.toLocaleString('en-IN', {minimumFractionDigits: 2})"></div>
                    </div>
                    <div class="p-4 bg-purple-50 rounded-xl border border-purple-200">
                        <div class="text-xs font-bold text-purple-700 uppercase tracking-wider">Statutory Deductions (PF/ESI/TDS)</div>
                        <div class="text-xl font-black text-purple-800 mt-1 font-mono" x-text="'₹' + totalDeductionsAll.toLocaleString('en-IN', {minimumFractionDigits: 2})"></div>
                    </div>
                    <div class="p-4 bg-emerald-50 rounded-xl border border-emerald-200">
                        <div class="text-xs font-bold text-emerald-700 uppercase tracking-wider">Final Net Bank Outflow</div>
                        <div class="text-xl font-black text-emerald-800 mt-1 font-mono" x-text="'₹' + totalNetAll.toLocaleString('en-IN', {minimumFractionDigits: 2})"></div>
                    </div>
                </div>

                <div class="p-4 bg-amber-50 rounded-xl border border-amber-200 text-amber-800 text-xs flex items-start gap-3 mb-6">
                    <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div>
                        <div class="font-bold">Ready to generate draft batch:</div>
                        Submitting will create official draft payslips for <span class="font-bold" x-text="items.length"></span> staff members. Store managers will be able to audit individual line items and generate bank NEFT files prior to disbursement.
                    </div>
                </div>

                <div class="flex justify-between items-center">
                    <button type="button" @click="step = 2" class="px-5 py-2.5 bg-slate-100 text-slate-700 rounded-xl text-xs font-semibold hover:bg-slate-200 transition">
                        ← Back to Wages
                    </button>
                    <button type="submit" class="px-6 py-2.5 bg-emerald-600 text-white rounded-xl text-xs font-bold hover:bg-emerald-700 shadow-sm transition">
                        ✓ Save &amp; Generate Payroll Batch
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

