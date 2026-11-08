{{--
    ======================================================================
    ADMIN MODULE: MARKETING PUSH NOTIFICATIONS
    ======================================================================
    File: resources/views/admin/marketing/push-notifications.blade.php
    Responsibility: Back-office administration for MARKETING PUSH NOTIFICATIONS
    Features:
      - Standard administration UI following the Nex-on design guidelines
      - Accessible controls, Tailwind CSS styling, and reactive Alpine.js components
    Security & Access Control:
      - Guarded by Laravel authentication and RBAC permission middleware
    Operations & Maintenance:
      - Integrates with master administration shell (@extends('admin.layout'))
--}}
@extends('admin.layout')

@section('title', 'Web & Mobile Push Notifications')

@section('content')
<div class="space-y-6 pb-12" x-data="{
    title: 'Flash Sale: 30% Off Silk Robes',
    body: 'Exclusive weekend offer for our VIP members. Use code LUXE30 at checkout.',
    actionUrl: '{{ url('/category/silk-lingerie') }}',
    imageUrl: ''
}">

    <!-- Header & Action Bar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 text-[10px] font-bold rounded-md bg-indigo-50 text-indigo-600 border border-indigo-200/50 uppercase font-mono tracking-wider">Marketing Automation</span>
                <span class="text-xs text-slate-400 font-medium">Phase 2 &bull; Customer Device Re-engagement</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 mt-1">Web &amp; Mobile Push Notifications</h1>
            <p class="text-xs text-slate-500 mt-0.5">Broadcast instant marketing alerts, flash discounts, and back-in-stock notifications directly to subscriber devices.</p>
        </div>
    </div>

    <!-- Metrics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Active Subscribers</span>
                <div class="text-2xl font-black text-indigo-600 mt-1 font-mono">{{ number_format($totalSubscribers) }}</div>
                <div class="text-[10px] text-slate-400 mt-1">Opted-in browser endpoints</div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                📱
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Notifications Sent</span>
                <div class="text-2xl font-black text-slate-900 mt-1 font-mono">{{ number_format($totalSent) }}</div>
                <div class="text-[10px] text-slate-400 mt-1">Lifetime device dispatches</div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                ✓
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Service Worker</span>
                <div class="text-base font-black text-emerald-600 mt-1 font-mono flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    ACTIVE
                </div>
                <div class="text-[10px] text-slate-400 mt-1">/firebase-messaging-sw.js</div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center font-bold">
                ⚙️
            </div>
        </div>
    </div>

    <!-- Main Grid: Composer & Device Mockup Preview -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Left: Composer Form -->
        <div class="lg:col-span-7 bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs space-y-4">
            <h2 class="text-sm font-bold text-slate-900">Broadcast Push Campaign</h2>
            <p class="text-xs text-slate-500">Draft an announcement to send to all {{ $totalSubscribers }} subscribed devices.</p>

            <form method="POST" action="{{ route('admin.push.send') }}" class="space-y-4 text-xs">
                @csrf

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Campaign Title *</label>
                    <input type="text" name="title" x-model="title" required maxlength="100"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-slate-900 focus:outline-none focus:border-indigo-500">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Message Body *</label>
                    <textarea name="body" x-model="body" required rows="3" maxlength="255"
                              class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-slate-900 focus:outline-none focus:border-indigo-500"></textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Click Action URL</label>
                        <input type="url" name="action_url" x-model="actionUrl" placeholder="https://..."
                               class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-slate-900 focus:outline-none focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Banner Image URL (Optional)</label>
                        <input type="url" name="image_url" x-model="imageUrl" placeholder="https://.../banner.jpg"
                               class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-slate-900 focus:outline-none focus:border-indigo-500">
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-[11px] text-slate-400">Targeting <strong>{{ $totalSubscribers }}</strong> subscribers</span>
                    <button type="submit"
                            class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition flex items-center gap-2 shadow-md shadow-indigo-600/20">
                        <svg class="w-4 h-4 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span>Send Push Broadcast Now</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Right: Real-time Device Mockup Preview -->
        <div class="lg:col-span-5 space-y-4">
            <div class="bg-slate-900 rounded-3xl p-6 shadow-xl text-white space-y-4">
                <div class="flex items-center justify-between text-xs text-slate-400 border-b border-slate-800 pb-3">
                    <span class="font-bold text-slate-300 uppercase tracking-wider font-mono text-[10px]">Real-time Notification Preview</span>
                    <span class="text-[10px]">Lock Screen &bull; Chrome / Safari</span>
                </div>

                <!-- Notification Card Simulation -->
                <div class="bg-slate-800/90 rounded-2xl p-4 border border-slate-700/80 shadow-lg space-y-2.5">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-5 h-5 rounded-full bg-indigo-600 flex items-center justify-center text-[10px] font-bold text-white">
                                V
                            </div>
                            <span class="text-[11px] font-bold text-slate-300 font-mono">{{ config('app.name', 'NEXON BOUTIQUE') }}</span>
                        </div>
                        <span class="text-[10px] text-slate-400 font-mono">now</span>
                    </div>

                    <div>
                        <div class="font-extrabold text-sm text-white" x-text="title || 'Notification Title'"></div>
                        <p class="text-xs text-slate-300 mt-0.5 line-clamp-2" x-text="body || 'Notification message description...'"></p>
                    </div>

                    <template x-if="imageUrl">
                        <div class="mt-2 rounded-xl overflow-hidden max-h-32 bg-slate-950">
                            <img :src="imageUrl" alt="Preview Image" class="w-full h-full object-cover">
                        </div>
                    </template>

                    <div class="pt-2 border-t border-slate-700/50 flex items-center justify-between text-[10px] text-indigo-400 font-mono">
                        <span>Tap to open &rarr;</span>
                        <span class="text-slate-400 truncate max-w-[180px]" x-text="actionUrl"></span>
                    </div>
                </div>

                <div class="text-[11px] text-slate-400 text-center">
                    Delivered via browser Push API using Web Push VAPID protocol.
                </div>
            </div>
        </div>

    </div>

    <!-- Broadcast Campaign History Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-900">Push Campaign Broadcast History</h2>
            <span class="text-xs text-slate-400 font-mono">{{ $campaigns->total() }} campaigns</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-left text-xs">
                <thead class="bg-slate-50/70 text-slate-500 uppercase font-mono text-[10px]">
                    <tr>
                        <th class="px-6 py-3">Dispatched Date</th>
                        <th class="px-6 py-3">Campaign Title</th>
                        <th class="px-6 py-3">Message Body</th>
                        <th class="px-6 py-3">Target URL</th>
                        <th class="px-6 py-3 text-right">Recipients Sent</th>
                        <th class="px-6 py-3 text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($campaigns as $camp)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-6 py-3.5 text-slate-500 font-mono text-[11px]">
                                {{ $camp->created_at->format('M d, Y H:i') }}
                            </td>
                            <td class="px-6 py-3.5 font-bold text-slate-900">{{ $camp->title }}</td>
                            <td class="px-6 py-3.5 text-slate-600 max-w-xs truncate">{{ $camp->body }}</td>
                            <td class="px-6 py-3.5 font-mono text-[11px] text-indigo-600">
                                <a href="{{ $camp->action_url }}" target="_blank" class="hover:underline">
                                    {{ Str::limit($camp->action_url, 30) }}
                                </a>
                            </td>
                            <td class="px-6 py-3.5 text-right font-mono font-bold text-slate-900">
                                {{ number_format($camp->sent_count) }}
                            </td>
                            <td class="px-6 py-3.5 text-right">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-emerald-50 text-emerald-700">
                                    Delivered
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-slate-400">
                                No push campaigns broadcasted yet. Use the composer above.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($campaigns->hasPages())
            <div class="px-6 py-3 border-t border-slate-100 bg-slate-50/50">
                {{ $campaigns->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
