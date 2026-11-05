@extends('admin.layout')

@section('content')
<div class="space-y-6">
    <!-- Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-indigo-600 uppercase tracking-wider mb-1">
                <span>Growth &amp; Retention</span>
                <span>•</span>
                <span class="text-slate-500 font-medium">Checkout Recovery</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Abandoned Cart Automated Recovery</h1>
            <p class="text-sm text-slate-500 mt-0.5">Detect dropped shopping checkouts and recover lost revenue via automated 3-stage drip sequences.</p>
        </div>
        <div class="flex items-center gap-2.5 flex-wrap">
            <form method="POST" action="{{ route('admin.marketing.abandoned-carts.simulate') }}">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition-colors">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                    Simulate Abandoned Cart
                </button>
            </form>
            <a href="{{ route('admin.marketing.dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm transition-all">
                Marketing Console
            </a>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <!-- 4 High-Impact KPI Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Abandoned -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Abandoned</span>
                <span class="p-2 rounded-xl bg-rose-50 text-rose-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </span>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-black text-slate-900 font-mono">{{ $totalAbandoned }} carts</div>
                <div class="text-xs text-slate-500 mt-1">Detected dropped sessions</div>
            </div>
        </div>

        <!-- Recovered Count & Rate -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Recovery Rate</span>
                <span class="p-2 rounded-xl bg-emerald-50 text-emerald-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-black text-emerald-700 font-mono">{{ $recoveryRate }}%</div>
                <div class="text-xs text-emerald-600 mt-1 font-medium">{{ $totalRecovered }} converted checkouts</div>
            </div>
        </div>

        <!-- Pending Pipeline Value -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Recoverable Pipeline</span>
                <span class="p-2 rounded-xl bg-amber-50 text-amber-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-black text-amber-700 font-mono">₹{{ number_format($unrecoveredPipeline, 2) }}</div>
                <div class="text-xs text-slate-500 mt-1">Pending dropped cart value</div>
            </div>
        </div>

        <!-- Recovered Revenue -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Recovered Revenue</span>
                <span class="p-2 rounded-xl bg-indigo-50 text-indigo-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </span>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-black text-indigo-700 font-mono">₹{{ number_format($recoveredRevenue, 2) }}</div>
                <div class="text-xs text-slate-500 mt-1">Saved from cart drop-offs</div>
            </div>
        </div>
    </div>

    <!-- 3-Stage Drip Funnel Visual Info -->
    <div class="bg-gradient-to-r from-indigo-50 via-purple-50 to-pink-50 p-6 rounded-2xl border border-indigo-100 flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="p-3 bg-white rounded-xl shadow-xs text-indigo-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
            <div>
                <h3 class="text-sm font-bold text-slate-900">Automated 3-Stage Smart Drip Strategy</h3>
                <p class="text-xs text-slate-600">Escalates automatically from gentle reminder to exclusive coupon discount.</p>
            </div>
        </div>
        <div class="flex items-center gap-2 text-xs flex-wrap">
            <div class="flex items-center gap-1.5 px-3 py-1.5 bg-white rounded-xl shadow-2xs font-medium text-slate-700">
                <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                <span>Stage 1: 1-Hr Gentle Email</span>
            </div>
            <span class="text-slate-400">&rarr;</span>
            <div class="flex items-center gap-1.5 px-3 py-1.5 bg-white rounded-xl shadow-2xs font-medium text-slate-700">
                <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                <span>Stage 2: 24-Hr SMS + 10% Off</span>
            </div>
            <span class="text-slate-400">&rarr;</span>
            <div class="flex items-center gap-1.5 px-3 py-1.5 bg-white rounded-xl shadow-2xs font-medium text-slate-700">
                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                <span>Stage 3: 72-Hr Final Urgency</span>
            </div>
        </div>
    </div>

    <!-- Filter Toolbar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.marketing.abandoned-carts', ['status' => 'unrecovered']) }}" 
               class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all {{ $status === 'unrecovered' ? 'bg-indigo-600 text-white shadow-2xs' : 'bg-slate-50 text-slate-600 hover:bg-slate-100 border border-slate-200/70' }}">
                Unrecovered (Pending)
            </a>
            <a href="{{ route('admin.marketing.abandoned-carts', ['status' => 'recovered']) }}" 
               class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all {{ $status === 'recovered' ? 'bg-indigo-600 text-white shadow-2xs' : 'bg-slate-50 text-slate-600 hover:bg-slate-100 border border-slate-200/70' }}">
                Recovered ({{ $totalRecovered }})
            </a>
            <a href="{{ route('admin.marketing.abandoned-carts', ['status' => 'all']) }}" 
               class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all {{ $status === 'all' ? 'bg-indigo-600 text-white shadow-2xs' : 'bg-slate-50 text-slate-600 hover:bg-slate-100 border border-slate-200/70' }}">
                All Carts ({{ $totalAbandoned }})
            </a>
        </div>

        <form method="GET" action="{{ route('admin.marketing.abandoned-carts') }}" class="flex items-center gap-2">
            <input type="hidden" name="status" value="{{ $status }}">
            <div class="relative min-w-[220px]">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search customer, email, phone..." 
                       class="w-full text-xs px-3.5 py-2 pl-9 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-indigo-500">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <button type="submit" class="px-3.5 py-2 rounded-xl text-xs font-semibold text-white bg-slate-800 hover:bg-slate-900 transition-colors">Search</button>
        </form>
    </div>

    <!-- Abandoned Carts Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200/80 text-slate-600 font-semibold uppercase tracking-wider text-[10px]">
                        <th class="py-3 px-4">Shopper / Contact</th>
                        <th class="py-3 px-4">Bag Contents</th>
                        <th class="py-3 px-4 text-right">Value (₹)</th>
                        <th class="py-3 px-4">Dropped When</th>
                        <th class="py-3 px-4 text-center">Drip Stage</th>
                        <th class="py-3 px-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($carts as $cart)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-900">{{ $cart->customer_name ?? 'Guest Shopper' }}</div>
                                @if($cart->email)
                                    <div class="text-[11px] text-slate-500">{{ $cart->email }}</div>
                                @endif
                                @if($cart->phone)
                                    <div class="text-[10px] text-indigo-600 font-mono">{{ $cart->phone }}</div>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 max-w-sm">
                                <div class="space-y-1">
                                    @if(is_array($cart->cart_data))
                                        @foreach(array_slice($cart->cart_data, 0, 2) as $item)
                                            <div class="text-[11px] text-slate-800 flex items-center gap-1.5">
                                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                                <span class="font-medium truncate">{{ $item['name'] ?? 'Item' }}</span>
                                                <span class="text-slate-400 font-mono">({{ $item['size'] ?? '' }} / {{ $item['color'] ?? '' }}) × {{ $item['qty'] ?? 1 }}</span>
                                            </div>
                                        @endforeach
                                        @if(count($cart->cart_data) > 2)
                                            <div class="text-[10px] text-slate-400 italic">+ {{ count($cart->cart_data) - 2 }} more items</div>
                                        @endif
                                    @else
                                        <span class="text-slate-400 italic">Cart details unavailable</span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono font-bold text-slate-900 text-sm">
                                ₹{{ number_format($cart->total_amount, 2) }}
                            </td>
                            <td class="py-3.5 px-4 text-[11px] text-slate-500">
                                <div>{{ $cart->created_at->format('d M Y, h:i A') }}</div>
                                <div class="text-[10px] text-slate-400">{{ $cart->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @php $badge = $cart->step_badge; @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $badge['bg'] }}">
                                    {{ $badge['label'] }}
                                </span>
                                @if($cart->last_reminded_at)
                                    <div class="text-[9px] text-slate-400 mt-0.5">Last: {{ $cart->last_reminded_at->diffForHumans() }}</div>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    @if(!$cart->is_recovered)
                                        <!-- Trigger Next Drip -->
                                        <form method="POST" action="{{ route('admin.marketing.abandoned-carts.drip', $cart) }}">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1 rounded-lg text-[11px] font-bold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 transition-colors" title="Advance to Next Drip">
                                                Send Drip {{ $cart->recovery_step + 1 }}
                                            </button>
                                        </form>

                                        <!-- Mark Recovered -->
                                        <form method="POST" action="{{ route('admin.marketing.abandoned-carts.recovered', $cart) }}">
                                            @csrf
                                            <button type="submit" class="px-2 py-1 rounded-lg text-[11px] font-medium text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 transition-colors" title="Mark as Recovered">
                                                ✓ Recovered
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-emerald-600 font-bold text-[11px]">Converted</span>
                                    @endif

                                    <!-- Delete -->
                                    <form method="POST" action="{{ route('admin.marketing.abandoned-carts.destroy', $cart) }}" onsubmit="return confirm('Delete this record?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1 text-slate-400 hover:text-rose-600 rounded">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                <svg class="w-12 h-12 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                <p class="text-sm font-medium text-slate-600">No abandoned shopping carts in this view.</p>
                                <p class="text-xs text-slate-400 mt-1">Use the "Simulate Abandoned Cart" button above to test the recovery funnel.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($carts->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $carts->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

