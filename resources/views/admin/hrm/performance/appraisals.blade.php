@extends('admin.layout')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-indigo-600 uppercase tracking-wider mb-1">
                <span>Human Resources</span>
                <span>•</span>
                <span class="text-slate-500 font-medium">Domain 6: Appraisals Matrix</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Staff Appraisal Directory &amp; Scoring Matrix</h1>
            <p class="text-sm text-slate-500 mt-0.5">Composite 30-50-20 performance evaluations, competency ratings, and salary increment recommendations.</p>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="{{ route('admin.hrm.performance.cycles') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition-colors">
                Evaluation Windows
            </a>
            <a href="{{ route('admin.hrm.performance.appraisals.create') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                New Staff Appraisal
            </a>
        </div>
    </div>

    <!-- Filter by Cycle -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
        <form method="GET" action="{{ route('admin.hrm.performance.appraisals') }}" class="flex flex-wrap items-center gap-3">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Evaluation Cycle:</span>
            <select name="cycle_id" class="px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:bg-white">
                <option value="">All Review Cycles</option>
                @foreach($cycles as $c)
                    <option value="{{ $c->id }}" {{ request('cycle_id') == $c->id ? 'selected' : '' }}>{{ $c->title }}</option>
                @endforeach
            </select>

            <select name="status" class="px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:bg-white">
                <option value="">All Statuses</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="submitted" {{ request('status') === 'submitted' ? 'selected' : '' }}>Submitted</option>
                <option value="reviewed" {{ request('status') === 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                <option value="finalized" {{ request('status') === 'finalized' ? 'selected' : '' }}>Finalized</option>
            </select>

            <button type="submit" class="px-4 py-1.5 bg-slate-800 text-white rounded-xl text-xs font-semibold hover:bg-slate-900 transition">
                Filter
            </button>
        </form>
    </div>

    <!-- Appraisals Matrix Table -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
        @if($appraisals->isEmpty())
            <div class="text-center py-12 border-2 border-dashed border-slate-200 rounded-xl">
                <svg class="w-10 h-10 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                <p class="text-sm font-semibold text-slate-600">No staff appraisals recorded yet</p>
                <p class="text-xs text-slate-400 mt-1">Conduct 30-50-20 evaluations for your retail staff and managers.</p>
                <a href="{{ route('admin.hrm.performance.appraisals.create') }}" class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-xl text-xs font-semibold hover:bg-indigo-700 transition">
                    Conduct Appraisal
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200/70 text-slate-400 uppercase text-[10px] tracking-wider">
                            <th class="pb-3 font-semibold">Employee</th>
                            <th class="pb-3 font-semibold">Review Cycle</th>
                            <th class="pb-3 font-semibold">Self (30%)</th>
                            <th class="pb-3 font-semibold">Manager (50%)</th>
                            <th class="pb-3 font-semibold">Peer (20%)</th>
                            <th class="pb-3 font-semibold">Composite Score</th>
                            <th class="pb-3 font-semibold">Recommendation</th>
                            <th class="pb-3 font-semibold">Hike</th>
                            <th class="pb-3 font-semibold text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @foreach($appraisals as $appraisal)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-3">
                                    <div class="font-bold text-slate-900">{{ $appraisal->user?->name ?? 'Staff' }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $appraisal->user?->email }}</div>
                                </td>
                                <td class="py-3 font-semibold text-indigo-600">{{ $appraisal->cycle?->title ?? 'Annual Review' }}</td>
                                <td class="py-3 font-mono font-bold text-slate-700">{{ number_format($appraisal->self_rating, 1) }} / 5.0</td>
                                <td class="py-3 font-mono font-bold text-slate-700">{{ number_format($appraisal->manager_rating, 1) }} / 5.0</td>
                                <td class="py-3 font-mono font-bold text-slate-700">{{ number_format($appraisal->peer_rating, 1) }} / 5.0</td>
                                <td class="py-3">
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-sm font-black font-mono {{ $appraisal->final_score >= 4.0 ? 'text-emerald-600' : ($appraisal->final_score >= 3.0 ? 'text-indigo-600' : 'text-amber-600') }}">
                                            {{ number_format($appraisal->final_score, 2) }}
                                        </span>
                                        <span class="text-[10px] text-slate-400">/ 5.00</span>
                                    </div>
                                </td>
                                <td class="py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider
                                        {{ $appraisal->recommendation === 'promotion' ? 'bg-purple-100 text-purple-800' : ($appraisal->recommendation === 'salary_hike' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700') }}">
                                        {{ str_replace('_', ' ', $appraisal->recommendation) }}
                                    </span>
                                </td>
                                <td class="py-3 font-mono font-bold text-emerald-600">
                                    {{ $appraisal->hike_percentage > 0 ? '+' . $appraisal->hike_percentage . '%' : '-' }}
                                </td>
                                <td class="py-3 text-right">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase
                                        {{ $appraisal->status === 'finalized' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                        {{ $appraisal->status }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $appraisals->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

