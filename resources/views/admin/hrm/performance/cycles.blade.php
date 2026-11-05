@extends('admin.layout')

@section('content')
<div class="space-y-6" x-data="{ showNewModal: false }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-indigo-600 uppercase tracking-wider mb-1">
                <span>Human Resources</span>
                <span>•</span>
                <span class="text-slate-500 font-medium">Domain 6: Evaluation Windows</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Appraisal Cycles &amp; Review Windows</h1>
            <p class="text-sm text-slate-500 mt-0.5">Configure quarterly, half-yearly, and annual employee evaluation timelines and submission deadlines.</p>
        </div>
        <div class="flex items-center gap-2.5">
            <button @click="showNewModal = true" type="button"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                + New Evaluation Cycle
            </button>
        </div>
    </div>

    <!-- Cycles Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($cycles as $cycle)
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex justify-between items-start mb-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider
                            {{ $cycle->status === 'active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($cycle->status === 'closed' ? 'bg-slate-100 text-slate-600' : 'bg-blue-50 text-blue-700 border border-blue-200') }}">
                            {{ $cycle->status }}
                        </span>
                        <span class="text-[11px] font-mono text-slate-400">ID: #{{ $cycle->id }}</span>
                    </div>

                    <h3 class="text-base font-bold text-slate-900 mb-1.5">{{ $cycle->title }}</h3>
                    <p class="text-xs text-slate-500 line-clamp-2 mb-4">{{ $cycle->description ?? 'General performance evaluation cycle.' }}</p>

                    <div class="space-y-2 text-xs text-slate-600 bg-slate-50 p-3 rounded-xl mb-4">
                        <div class="flex justify-between">
                            <span class="text-slate-400">Start Date:</span>
                            <span class="font-semibold text-slate-800">{{ $cycle->start_date?->format('d M Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Cutoff Deadline:</span>
                            <span class="font-bold text-rose-600 font-mono">{{ $cycle->cutoff_date?->format('d M Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Appraisals Recorded:</span>
                            <span class="font-bold text-indigo-600">{{ $cycle->appraisals_count }} Submitted</span>
                        </div>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 flex justify-between items-center">
                    <a href="{{ route('admin.hrm.performance.appraisals', ['cycle_id' => $cycle->id]) }}" 
                       class="text-xs font-bold text-indigo-600 hover:text-indigo-800">
                        View Submissions ({{ $cycle->appraisals_count }}) →
                    </a>
                </div>
            </div>
        @endforeach
    </div>

    <!-- New Cycle Modal -->
    <div x-show="showNewModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-slate-200" @click.away="showNewModal = false">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-base font-bold text-slate-900">Configure Evaluation Window</h3>
                <button @click="showNewModal = false" class="text-slate-400 hover:text-slate-600">✕</button>
            </div>

            <form action="{{ route('admin.hrm.performance.cycles.store') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Cycle Title</label>
                    <input type="text" name="title" required placeholder="e.g. Q4 2026 Comprehensive Review"
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Start Date</label>
                        <input type="date" name="start_date" value="{{ now()->toDateString() }}" required
                               class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Cutoff Date</label>
                        <input type="date" name="cutoff_date" value="{{ now()->addMonths(1)->toDateString() }}" required
                               class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Status</label>
                    <select name="status" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold">
                        <option value="active">Active (Open for Submissions)</option>
                        <option value="upcoming">Upcoming</option>
                        <option value="evaluation">Under Manager Evaluation</option>
                        <option value="closed">Closed / Archived</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Scope &amp; Description</label>
                    <textarea name="description" rows="3" placeholder="Target staff, assessment metrics, and coaching instructions..."
                              class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" @click="showNewModal = false" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-xl text-xs font-semibold">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2 bg-indigo-600 text-white rounded-xl text-xs font-bold hover:bg-indigo-700">
                        Create Review Cycle
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

