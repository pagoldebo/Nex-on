@extends('admin.layout')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-indigo-600 uppercase tracking-wider mb-1">
                <span>Human Resources</span>
                <span>•</span>
                <span class="text-slate-500 font-medium">Domain 6: OKRs &amp; Goals</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Employee Goals &amp; OKR Tracker</h1>
            <p class="text-sm text-slate-500 mt-0.5">Track individual and departmental key results, milestone delivery percentages, and performance weightages.</p>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="{{ route('admin.hrm.performance.goals.create') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                New Goal Definition
            </a>
        </div>
    </div>

    <!-- 4 High-Impact KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Active OKRs</div>
            <div class="text-2xl font-black text-slate-900 mt-1">{{ $totalGoals }}</div>
            <p class="text-xs text-slate-400 mt-1">Across all departments</p>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Completed Milestones</div>
            <div class="text-2xl font-black text-emerald-600 mt-1">{{ $completedGoals }}</div>
            <p class="text-xs text-slate-400 mt-1">100% target reached</p>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">On Track</div>
            <div class="text-2xl font-black text-blue-600 mt-1">{{ $onTrackGoals }}</div>
            <p class="text-xs text-slate-400 mt-1">Healthy pacing toward deadline</p>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Average Progress</div>
            <div class="text-2xl font-black text-indigo-600 mt-1">{{ round($avgProgress, 1) }}%</div>
            <div class="w-full bg-slate-100 rounded-full h-1.5 mt-2 overflow-hidden">
                <div class="bg-indigo-600 h-1.5 rounded-full" style="width: {{ min(100, $avgProgress) }}%"></div>
            </div>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
        <form method="GET" action="{{ route('admin.hrm.performance.goals') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
            <!-- Staff Filter -->
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Employee</label>
                <select name="user_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:bg-white">
                    <option value="">All Staff Members</option>
                    @foreach($staffMembers as $u)
                        <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Status</label>
                <select name="status" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:bg-white">
                    <option value="">All Statuses</option>
                    <option value="not_started" {{ request('status') === 'not_started' ? 'selected' : '' }}>Not Started</option>
                    <option value="on_track" {{ request('status') === 'on_track' ? 'selected' : '' }}>On Track</option>
                    <option value="at_risk" {{ request('status') === 'at_risk' ? 'selected' : '' }}>At Risk</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>

            <!-- Category Filter -->
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Category</label>
                <input type="text" name="category" value="{{ request('category') }}" placeholder="e.g. Retail Sales, Operations"
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:bg-white">
            </div>

            <!-- Submit -->
            <div>
                <button type="submit" class="w-full px-4 py-2 bg-slate-800 text-white rounded-xl text-xs font-semibold hover:bg-slate-900 transition">
                    Filter OKRs
                </button>
            </div>
        </form>
    </div>

    <!-- Goals List Table -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
        @if($goals->isEmpty())
            <div class="text-center py-12 border-2 border-dashed border-slate-200 rounded-xl">
                <svg class="w-10 h-10 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                <p class="text-sm font-semibold text-slate-600">No OKRs or employee goals found</p>
                <p class="text-xs text-slate-400 mt-1">Define quarterly targets and measurable deliverables for your staff.</p>
                <a href="{{ route('admin.hrm.performance.goals.create') }}" class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-xl text-xs font-semibold hover:bg-indigo-700 transition">
                    Create First Goal
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200/70 text-slate-400 uppercase text-[10px] tracking-wider">
                            <th class="pb-3 font-semibold">Goal &amp; Milestone</th>
                            <th class="pb-3 font-semibold">Employee</th>
                            <th class="pb-3 font-semibold">Target / Unit</th>
                            <th class="pb-3 font-semibold">Weight</th>
                            <th class="pb-3 font-semibold">Progress</th>
                            <th class="pb-3 font-semibold">Status</th>
                            <th class="pb-3 font-semibold">Due Date</th>
                            <th class="pb-3 font-semibold text-right">Quick Update</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @foreach($goals as $goal)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-3">
                                    <div class="font-bold text-slate-900">{{ $goal->title }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $goal->category }}</div>
                                </td>
                                <td class="py-3 font-semibold text-slate-800">{{ $goal->user?->name ?? 'Staff' }}</td>
                                <td class="py-3">
                                    <span class="font-bold text-slate-900 font-mono">{{ number_format($goal->current_value, 0) }} / {{ number_format($goal->target_value, 0) }}</span>
                                    <span class="text-[10px] text-slate-400">{{ $goal->unit }}</span>
                                </td>
                                <td class="py-3 font-bold text-slate-700">{{ $goal->weightage }}%</td>
                                <td class="py-3 w-40">
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1 bg-slate-100 rounded-full h-2 overflow-hidden">
                                            <div class="h-2 rounded-full {{ $goal->progress_percent >= 100 ? 'bg-emerald-500' : ($goal->status === 'at_risk' ? 'bg-rose-500' : 'bg-indigo-600') }}" 
                                                 style="width: {{ $goal->progress_percent }}%"></div>
                                        </div>
                                        <span class="text-[11px] font-bold text-slate-700 font-mono">{{ $goal->progress_percent }}%</span>
                                    </div>
                                </td>
                                <td class="py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold border {{ $goal->status_badge['bg'] }}">
                                        {{ $goal->status_badge['label'] }}
                                    </span>
                                </td>
                                <td class="py-3 font-mono text-slate-600 text-[11px]">{{ $goal->due_date?->format('d M Y') }}</td>
                                <td class="py-3 text-right">
                                    <form action="{{ route('admin.hrm.performance.goals.update-progress', $goal) }}" method="POST" class="inline-flex items-center gap-1.5">
                                        @csrf
                                        <input type="number" name="current_value" value="{{ $goal->current_value }}" step="any" min="0"
                                               class="w-16 px-1.5 py-0.5 bg-slate-50 border border-slate-200 rounded text-xs font-semibold text-center">
                                        <button type="submit" class="p-1 text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 rounded" title="Save Progress">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $goals->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

