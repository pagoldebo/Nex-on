{{--
    ======================================================================
    ADMIN MODULE: HRM PERFORMANCE APPRAISALS CREATE
    ======================================================================
    File: resources/views/admin/hrm/performance/appraisals-create.blade.php
    Responsibility: Back-office administration for HRM PERFORMANCE APPRAISALS CREATE
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
<div class="space-y-6 max-w-4xl mx-auto" x-data="{
    selfRating: 4.0,
    managerRating: 4.5,
    peerRating: 4.2,
    get composite() {
        let s = (parseFloat(this.selfRating) || 0) * 0.30;
        let m = (parseFloat(this.managerRating) || 0) * 0.50;
        let p = (parseFloat(this.peerRating) || 0) * 0.20;
        return Math.round((s + m + p) * 100) / 100;
    }
}">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-indigo-600 uppercase tracking-wider mb-1">
                <span>Human Resources</span>
                <span>•</span>
                <span class="text-slate-500 font-medium">Domain 6: Evaluation Dossier</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Conduct Staff Performance Appraisal</h1>
            <p class="text-sm text-slate-500 mt-0.5">360-degree weighted review (Self 30%, Manager 50%, Peer 20%) with core competency benchmarking.</p>
        </div>
        <a href="{{ route('admin.hrm.performance.appraisals') }}" 
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition-colors">
            ← Back to Appraisals
        </a>
    </div>

    <!-- Form -->
    <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/80 shadow-xs">
        <form action="{{ route('admin.hrm.performance.appraisals.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Cycle Selection -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Appraisal Cycle</label>
                    <select name="appraisal_cycle_id" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-900 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                        @foreach($cycles as $cycle)
                            <option value="{{ $cycle->id }}">{{ $cycle->title }} ({{ $cycle->status }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Employee Selection -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Staff Member Under Review</label>
                    <select name="user_id" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-900 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                        <option value="">Select Employee</option>
                        @foreach($staffMembers as $u)
                            <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->role ?? 'Staff' }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Weighted 30-50-20 Scoring Panel -->
            <div class="p-5 bg-indigo-50/50 border border-indigo-100 rounded-2xl">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-indigo-900">30-50-20 Weighted Score Matrix</h4>
                        <p class="text-[11px] text-indigo-700">Self: 30% • Manager: 50% • Peer: 20%</p>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] text-slate-500 font-bold uppercase">Composite Score</span>
                        <div class="text-2xl font-black text-indigo-600 font-mono" x-text="composite.toFixed(2) + ' / 5.00'"></div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Self-Rating (1.0 - 5.0)</label>
                        <input type="number" name="self_rating" step="0.1" min="1.0" max="5.0" x-model="selfRating" required
                               class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-900 font-mono">
                        <span class="text-[10px] text-slate-400">Weightage: 30%</span>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Manager Rating (1.0 - 5.0)</label>
                        <input type="number" name="manager_rating" step="0.1" min="1.0" max="5.0" x-model="managerRating" required
                               class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-900 font-mono">
                        <span class="text-[10px] text-slate-400">Weightage: 50%</span>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Peer Review Rating (1.0 - 5.0)</label>
                        <input type="number" name="peer_rating" step="0.1" min="1.0" max="5.0" x-model="peerRating" required
                               class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-900 font-mono">
                        <span class="text-[10px] text-slate-400">Weightage: 20%</span>
                    </div>
                </div>
            </div>

            <!-- Competency Assessments -->
            <div>
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 mb-3">Core Competency Assessment (1 - 5 Scale)</h4>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Punctuality &amp; Discipline</label>
                        <input type="number" name="competencies[punctuality]" value="4.5" step="0.1" min="1" max="5"
                               class="w-full px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-bold text-center">
                    </div>
                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Client Care &amp; Hospitality</label>
                        <input type="number" name="competencies[client_hospitality]" value="4.8" step="0.1" min="1" max="5"
                               class="w-full px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-bold text-center">
                    </div>
                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Technical Competence</label>
                        <input type="number" name="competencies[technical_competence]" value="4.2" step="0.1" min="1" max="5"
                               class="w-full px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-bold text-center">
                    </div>
                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Teamwork &amp; Collaboration</label>
                        <input type="number" name="competencies[teamwork]" value="4.7" step="0.1" min="1" max="5"
                               class="w-full px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-bold text-center">
                    </div>
                </div>
            </div>

            <!-- Qualitative Notes -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Key Strengths &amp; Commendations</label>
                    <textarea name="strengths" rows="3" placeholder="Exceptional customer hospitality, deep garment silhouette knowledge..."
                              class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:bg-white"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Areas for Development &amp; Coaching</label>
                    <textarea name="areas_for_improvement" rows="3" placeholder="Stock counting speed, advanced POS terminal shortcuts..."
                              class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:bg-white"></textarea>
                </div>
            </div>

            <!-- Recommendation & Hike -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Appraisal Recommendation</label>
                    <select name="recommendation" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:bg-white">
                        <option value="salary_hike">Salary Hike</option>
                        <option value="promotion">Role Promotion</option>
                        <option value="bonus">One-Time Performance Bonus</option>
                        <option value="retain">Retain Existing Package</option>
                        <option value="training_pip">Training / PIP</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Recommended Hike (%)</label>
                    <input type="number" name="hike_percentage" value="12.5" step="0.5" min="0" max="100"
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-emerald-600 font-mono">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Appraisal Status</label>
                    <select name="status" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:bg-white">
                        <option value="submitted">Submitted</option>
                        <option value="draft">Draft</option>
                        <option value="finalized">Finalized</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('admin.hrm.performance.appraisals') }}" class="px-5 py-2.5 bg-slate-100 text-slate-700 rounded-xl text-xs font-semibold hover:bg-slate-200 transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white rounded-xl text-xs font-bold hover:bg-indigo-700 shadow-sm transition">
                    ✓ Save &amp; Record Appraisal Dossier
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

