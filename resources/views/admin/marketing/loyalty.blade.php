@extends('admin.layout')

@section('content')
<div x-data="{ adjustModal: false, selectedAccount: null, selectedUserName: '', editTierModal: false, activeTier: null }" class="space-y-6">
    <!-- Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-indigo-600 uppercase tracking-wider mb-1">
                <span>Growth &amp; Retention</span>
                <span>•</span>
                <span class="text-slate-500 font-medium">VIP Loyalty Program</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">VIP Loyalty Rewards &amp; Points Program</h1>
            <p class="text-sm text-slate-500 mt-0.5">Award reward points per rupee spent, manage tiered VIP status, and enable instant checkout redemption.</p>
        </div>
        <div class="flex items-center gap-2.5 flex-wrap">
            <a href="{{ route('admin.marketing.dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition-colors">
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
        <!-- Total Members -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Enrolled VIP Members</span>
                <span class="p-2 rounded-xl bg-indigo-50 text-indigo-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </span>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-black text-slate-900 font-mono">{{ number_format($totalMembers) }} patrons</div>
                <div class="text-xs text-slate-500 mt-1">Active customer reward accounts</div>
            </div>
        </div>

        <!-- Points Circulation -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Points In Circulation</span>
                <span class="p-2 rounded-xl bg-amber-50 text-amber-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                </span>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-black text-amber-700 font-mono">{{ number_format($totalPointsCirculation) }} pts</div>
                <div class="text-xs text-slate-500 mt-1">Unredeemed customer balance</div>
            </div>
        </div>

        <!-- Cash Value Liability -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Redemption Liability</span>
                <span class="p-2 rounded-xl bg-rose-50 text-rose-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-black text-rose-700 font-mono">₹{{ number_format($totalPointsLiabilityRupees, 2) }}</div>
                <div class="text-xs text-slate-500 mt-1">Valued at 1 pt = ₹0.50 discount</div>
            </div>
        </div>

        <!-- Total Redeemed -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Lifetime Redeemed</span>
                <span class="p-2 rounded-xl bg-emerald-50 text-emerald-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-black text-emerald-700 font-mono">{{ number_format($totalPointsRedeemed) }} pts</div>
                <div class="text-xs text-emerald-600 mt-1 font-medium">Successfully applied at checkout</div>
            </div>
        </div>
    </div>

    <!-- VIP Tiers Grid Cards -->
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">VIP Tiers &amp; Earning Rules</h2>
            <span class="text-xs text-slate-500">Auto-calculated based on customer lifetime spend</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($tiers as $tier)
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between space-y-4 relative overflow-hidden">
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold border"
                                  style="background-color: {{ $tier->badge_color }}15; color: {{ $tier->badge_color }}; border-color: {{ $tier->badge_color }}30;">
                                {{ $tier->name }}
                            </span>
                            <span class="text-xs font-mono font-bold text-slate-700 bg-slate-100 px-2 py-0.5 rounded">
                                {{ $tier->points_multiplier }}x Pts
                            </span>
                        </div>
                        <div>
                            <div class="text-xs text-slate-500">Min Lifetime Spend:</div>
                            <div class="text-lg font-black text-slate-900 font-mono">
                                {{ $tier->min_spend > 0 ? '₹' . number_format($tier->min_spend, 0) : 'Free Enrollment' }}
                            </div>
                        </div>
                        <p class="text-xs text-slate-600 line-clamp-2">{{ $tier->perks }}</p>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                        <span class="text-slate-500 font-medium">{{ $tier->accounts_count }} Members</span>
                        <button type="button" @click="activeTier = {{ json_encode($tier) }}; editTierModal = true" class="text-indigo-600 hover:underline font-bold">
                            Edit Rules
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Members Directory Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h3 class="text-base font-bold text-slate-900">VIP Members Directory</h3>
                <p class="text-xs text-slate-500">Browse customer reward balances and manually grant goodwill points.</p>
            </div>

            <form method="GET" action="{{ route('admin.marketing.loyalty') }}" class="flex items-center gap-2">
                <select name="tier_id" onchange="this.form.submit()" class="text-xs font-semibold px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-700">
                    <option value="">All Tiers</option>
                    @foreach($tiers as $t)
                        <option value="{{ $t->id }}" {{ $tierId == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                    @endforeach
                </select>

                <div class="relative min-w-[200px]">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search patron name, email..." 
                           class="w-full text-xs px-3.5 py-1.5 pl-8 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-indigo-500">
                    <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <button type="submit" class="px-3 py-1.5 rounded-xl text-xs font-semibold text-white bg-slate-800 hover:bg-slate-900 transition-colors">Filter</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200/80 text-slate-600 font-semibold uppercase tracking-wider text-[10px]">
                        <th class="py-3 px-4">Patron Name &amp; Contact</th>
                        <th class="py-3 px-4">VIP Tier</th>
                        <th class="py-3 px-4 text-right">Lifetime Spend</th>
                        <th class="py-3 px-4 text-right">Points Balance</th>
                        <th class="py-3 px-4 text-right">Cash Value (₹)</th>
                        <th class="py-3 px-4 text-center">Earned / Redeemed</th>
                        <th class="py-3 px-4 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($accounts as $account)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-900">{{ $account->user->name ?? 'Guest User' }}</div>
                                <div class="text-[11px] text-slate-500">{{ $account->user->email ?? '—' }}</div>
                                @if($account->user->phone)
                                    <div class="text-[10px] text-indigo-600 font-mono">{{ $account->user->phone }}</div>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold border"
                                      style="background-color: {{ $account->tier->badge_color }}15; color: {{ $account->tier->badge_color }}; border-color: {{ $account->tier->badge_color }}30;">
                                    {{ $account->tier->name }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono font-semibold text-slate-900">
                                ₹{{ number_format($account->lifetime_spend, 2) }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono font-black text-amber-700 text-sm">
                                {{ number_format($account->points_balance) }} pts
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono font-bold text-emerald-700">
                                ₹{{ number_format($account->points_rupee_value, 2) }}
                            </td>
                            <td class="py-3.5 px-4 text-center font-mono text-[11px] text-slate-500">
                                <span class="text-emerald-700 font-semibold">+{{ number_format($account->points_earned_lifetime) }}</span>
                                <span class="text-slate-300">/</span>
                                <span class="text-rose-600 font-semibold">-{{ number_format($account->points_redeemed_lifetime) }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <button type="button" 
                                        @click="selectedAccount = {{ $account->id }}; selectedUserName = '{{ addslashes($account->user->name ?? 'User') }}'; adjustModal = true" 
                                        class="px-3 py-1 rounded-xl text-xs font-bold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 transition-colors">
                                    Adjust Points
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                No customer loyalty accounts found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($accounts->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $accounts->links() }}
            </div>
        @endif
    </div>

    <!-- Modal: Adjust Member Points -->
    <div x-show="adjustModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="adjustModal" @click="adjustModal = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div x-show="adjustModal" class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
                <form method="POST" :action="`{{ url('admin/marketing/loyalty/accounts') }}/${selectedAccount}/adjust`" class="p-6 space-y-4">
                    @csrf
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div>
                            <h3 class="text-base font-bold text-slate-900">Adjust Member Points</h3>
                            <p class="text-xs text-slate-500" x-text="`For patron: ${selectedUserName}`"></p>
                        </div>
                        <button type="button" @click="adjustModal = false" class="text-slate-400 hover:text-slate-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Points Amount *</label>
                        <input type="number" name="points" required placeholder="e.g. 500 for credit, -200 for debit" 
                               class="w-full text-xs font-mono font-bold px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:ring-2 focus:ring-indigo-500">
                        <p class="text-[10px] text-slate-400 mt-1">Use a positive integer to credit points, or a negative value to deduct points.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Reason / Description *</label>
                        <input type="text" name="description" required placeholder="e.g. Birthday Celebration Bonus" 
                               class="w-full text-xs px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                        <button type="button" @click="adjustModal = false" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition-colors">Cancel</button>
                        <button type="submit" class="px-5 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-xs transition-colors">Save Adjustment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal: Edit VIP Tier -->
    <div x-show="editTierModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="editTierModal" @click="editTierModal = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div x-show="editTierModal" class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
                <form method="POST" :action="`{{ url('admin/marketing/loyalty/tiers') }}/${activeTier ? activeTier.id : ''}`" class="p-6 space-y-4">
                    @csrf
                    @method('PUT')
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <h3 class="text-base font-bold text-slate-900">Edit VIP Tier Rules</h3>
                        <button type="button" @click="editTierModal = false" class="text-slate-400 hover:text-slate-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Tier Name *</label>
                        <input type="text" name="name" :value="activeTier ? activeTier.name : ''" required 
                               class="w-full text-xs px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Min Spend (₹) *</label>
                            <input type="number" step="0.01" name="min_spend" :value="activeTier ? activeTier.min_spend : 0" required 
                                   class="w-full text-xs font-mono px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Multiplier (x) *</label>
                            <input type="number" step="0.05" name="points_multiplier" :value="activeTier ? activeTier.points_multiplier : 1.0" required 
                                   class="w-full text-xs font-mono px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Perks &amp; Benefits</label>
                        <textarea name="perks" rows="2" :value="activeTier ? activeTier.perks : ''" 
                                  class="w-full text-xs px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:ring-2 focus:ring-indigo-500"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                        <button type="button" @click="editTierModal = false" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition-colors">Cancel</button>
                        <button type="submit" class="px-5 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-xs transition-colors">Update Tier</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

