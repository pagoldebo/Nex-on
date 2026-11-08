{{--
    ======================================================================
    ADMIN HRM: 360-DEGREE PEER FEEDBACK & PRAISE WALL
    ======================================================================
    File: resources/views/admin/hrm/performance/feedback.blade.php
    Controller: App\Http\Controllers\Admin\PerformanceController@feedback
    Route Context: GET /admin/hrm/performance/feedback
    Responsibility: Continuous feedback exchange and employee praise badges
    Features:
      - Peer shoutouts and company core values praise badges
      - Confidential 360-degree review responses
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
                <span class="text-slate-500 font-medium">Domain 6: Workplace Culture</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">360° Peer Feedback &amp; Praise Wall</h1>
            <p class="text-sm text-slate-500 mt-0.5">Celebrate outstanding retail performance, collaborative teamwork, and continuous peer recognition.</p>
        </div>
    </div>

    <!-- Give Praise / Feedback Form Card -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs" x-data="{
        selectedBadge: 'Customer Hero'
    }">
        <div class="flex items-center gap-2 mb-3">
            <span class="text-lg">🏆</span>
            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Award Praise Badge or Peer Kudos</h3>
        </div>

        <form action="{{ route('admin.hrm.performance.feedback.store') }}" method="POST" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Colleague Being Recognized</label>
                    <select name="to_user_id" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:bg-white">
                        <option value="">Select Team Member</option>
                        @foreach($staffMembers as $u)
                            <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->role ?? 'Staff' }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Select Praise Badge</label>
                    <input type="hidden" name="badge" :value="selectedBadge">
                    <div class="flex flex-wrap gap-2">
                        @foreach($availableBadges as $badgeName => $meta)
                            <button type="button" @click="selectedBadge = '{{ $badgeName }}'"
                                    class="px-2.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 border"
                                    :class="selectedBadge === '{{ $badgeName }}' ? '{{ $meta['color'] }} shadow-xs ring-2 ring-indigo-500/20' : 'bg-slate-50 text-slate-600 border-slate-200'">
                                <span>{{ $meta['icon'] }}</span>
                                <span>{{ $badgeName }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Appreciation Message / Recognition Note</label>
                <textarea name="message" rows="2" required placeholder="Describe what this colleague did that was exceptional (e.g. handled an urgent boutique rush, solved inventory variance)..."
                          class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:bg-white"></textarea>
            </div>

            <div class="flex justify-between items-center pt-2">
                <label class="flex items-center gap-2 text-xs text-slate-600 cursor-pointer">
                    <input type="checkbox" name="is_public" value="1" checked class="rounded text-indigo-600 focus:ring-indigo-500">
                    <span>Show on Public Company Recognition Wall</span>
                </label>

                <button type="submit" class="px-5 py-2 bg-indigo-600 text-white rounded-xl text-xs font-bold hover:bg-indigo-700 shadow-sm transition">
                    🎉 Post Recognition
                </button>
            </div>
        </form>
    </div>

    <!-- Praise Wall Feed -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Company Recognition Feed</h3>
            <span class="text-xs text-slate-400">{{ $feedbacks->total() }} Kudos Logged</span>
        </div>

        @if($feedbacks->isEmpty())
            <div class="bg-white p-12 text-center rounded-2xl border border-slate-200/80 shadow-xs">
                <div class="text-4xl mb-2">🤝</div>
                <h4 class="text-sm font-bold text-slate-800">No Kudos on the Wall Yet</h4>
                <p class="text-xs text-slate-400 mt-1">Be the first to award a praise badge to a fellow team member above!</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($feedbacks as $fb)
                    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
                        <div>
                            <div class="flex justify-between items-start mb-3">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-700 font-bold flex items-center justify-center text-xs">
                                        {{ mb_substr($fb->recipient?->name ?? 'S', 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="text-xs font-bold text-slate-900">{{ $fb->recipient?->name ?? 'Staff' }}</div>
                                        <div class="text-[10px] text-slate-400">Awarded by {{ $fb->sender?->name ?? 'Admin' }}</div>
                                    </div>
                                </div>

                                @if($fb->badge)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $fb->badge_style['bg'] }}">
                                        <span>{{ $fb->badge_style['icon'] }}</span>
                                        <span>{{ $fb->badge }}</span>
                                    </span>
                                @endif
                            </div>

                            <p class="text-xs text-slate-700 leading-relaxed bg-slate-50/70 p-3 rounded-xl border border-slate-100">
                                "{{ $fb->message }}"
                            </p>
                        </div>

                        <div class="mt-3 pt-2 text-[10px] text-slate-400 flex justify-between items-center">
                            <span>{{ $fb->created_at->diffForHumans() }}</span>
                            <span class="text-indigo-600 font-semibold">{{ $fb->is_public ? 'Public Wall' : 'Confidential' }}</span>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-4">
                {{ $feedbacks->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

