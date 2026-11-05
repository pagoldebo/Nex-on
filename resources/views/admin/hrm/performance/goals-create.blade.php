@extends('admin.layout')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-indigo-600 uppercase tracking-wider mb-1">
                <span>Human Resources</span>
                <span>•</span>
                <span class="text-slate-500 font-medium">Domain 6: OKR Studio</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">New Goal &amp; Milestone Studio</h1>
            <p class="text-sm text-slate-500 mt-0.5">Define measurable objectives, metric milestone thresholds, and appraisal weightages.</p>
        </div>
        <a href="{{ route('admin.hrm.performance.goals') }}" 
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition-colors">
            ← Back to Goals Tracker
        </a>
    </div>

    <!-- Form -->
    <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/80 shadow-xs" x-data="{
        metricType: 'percentage',
        unit: '%',
        targetValue: 100,
        syncUnit() {
            if (this.metricType === 'percentage') {
                this.unit = '%';
                this.targetValue = 100;
            } else if (this.metricType === 'revenue') {
                this.unit = '₹';
                this.targetValue = 250000;
            } else if (this.metricType === 'numeric') {
                this.unit = 'Units';
                this.targetValue = 50;
            } else if (this.metricType === 'boolean') {
                this.unit = 'Done';
                this.targetValue = 1;
            }
        }
    }">
        <form action="{{ route('admin.hrm.performance.goals.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Staff Assignment -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Assignee (Employee)</label>
                    <select name="user_id" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-900 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                        <option value="">Select Staff Member</option>
                        @foreach($staffMembers as $u)
                            <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->role ?? 'Staff' }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Category -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Goal Category</label>
                    <input type="text" name="category" required placeholder="e.g. Retail Sales, Customer Care, Inventory Accuracy"
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-900 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                </div>
            </div>

            <!-- Goal Title -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Goal Objective / Milestone Title</label>
                <input type="text" name="title" required placeholder="e.g. Achieve ₹15,00,000 in Retail POS Boutique Collections"
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-900 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
            </div>

            <!-- Description / Key Results -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Key Results &amp; Success Criteria</label>
                <textarea name="description" rows="3" placeholder="Outline specific deliverables, milestones, and measurement standards..."
                          class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-900 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"></textarea>
            </div>

            <!-- Metric Type Selection -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Target Metric Type</label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <label class="flex items-center gap-2.5 p-3 rounded-xl border cursor-pointer transition"
                           :class="metricType === 'percentage' ? 'bg-indigo-50 border-indigo-400 text-indigo-900 font-bold' : 'bg-slate-50 border-slate-200 text-slate-700'">
                        <input type="radio" name="metric_type" value="percentage" x-model="metricType" @change="syncUnit()" class="sr-only">
                        <span>📊 Percentage (%)</span>
                    </label>

                    <label class="flex items-center gap-2.5 p-3 rounded-xl border cursor-pointer transition"
                           :class="metricType === 'revenue' ? 'bg-indigo-50 border-indigo-400 text-indigo-900 font-bold' : 'bg-slate-50 border-slate-200 text-slate-700'">
                        <input type="radio" name="metric_type" value="revenue" x-model="metricType" @change="syncUnit()" class="sr-only">
                        <span>💰 Revenue (₹)</span>
                    </label>

                    <label class="flex items-center gap-2.5 p-3 rounded-xl border cursor-pointer transition"
                           :class="metricType === 'numeric' ? 'bg-indigo-50 border-indigo-400 text-indigo-900 font-bold' : 'bg-slate-50 border-slate-200 text-slate-700'">
                        <input type="radio" name="metric_type" value="numeric" x-model="metricType" @change="syncUnit()" class="sr-only">
                        <span>🔢 Numeric Units</span>
                    </label>

                    <label class="flex items-center gap-2.5 p-3 rounded-xl border cursor-pointer transition"
                           :class="metricType === 'boolean' ? 'bg-indigo-50 border-indigo-400 text-indigo-900 font-bold' : 'bg-slate-50 border-slate-200 text-slate-700'">
                        <input type="radio" name="metric_type" value="boolean" x-model="metricType" @change="syncUnit()" class="sr-only">
                        <span>✔️ Yes/No Completion</span>
                    </label>
                </div>
            </div>

            <!-- Target Values & Weightage -->
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Target Threshold</label>
                    <input type="number" name="target_value" step="any" min="0.01" x-model="targetValue" required
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-900 font-mono">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Starting Value</label>
                    <input type="number" name="current_value" step="any" min="0" value="0"
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-900 font-mono">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Metric Unit</label>
                    <input type="text" name="unit" x-model="unit" required
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-900">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Weightage Score (%)</label>
                    <input type="number" name="weightage" min="1" max="100" value="25" required
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-indigo-700 font-mono">
                </div>
            </div>

            <!-- Target Deadline -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Target Completion Cutoff</label>
                <input type="date" name="due_date" value="{{ now()->addMonths(3)->toDateString() }}" required
                       class="w-full sm:w-64 px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-900">
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('admin.hrm.performance.goals') }}" class="px-5 py-2.5 bg-slate-100 text-slate-700 rounded-xl text-xs font-semibold hover:bg-slate-200 transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white rounded-xl text-xs font-bold hover:bg-indigo-700 shadow-sm transition">
                    ✓ Save &amp; Activate Goal
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

