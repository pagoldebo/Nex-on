{{--
    ======================================================================
    ADMIN MODULE: CUSTOMERS INDEX
    ======================================================================
    File: resources/views/admin/customers/index.blade.php
    Responsibility: Back-office administration for CUSTOMERS INDEX
    Features:
      - Standard administration UI following the Nex-on design guidelines
      - Accessible controls, Tailwind CSS styling, and reactive Alpine.js components
    Security & Access Control:
      - Guarded by Laravel authentication and RBAC permission middleware
    Operations & Maintenance:
      - Integrates with master administration shell (@extends('admin.layout'))
--}}
@extends('admin.layout', [
    'title' => 'Customer Management',
    'breadcrumb' => 'Customers',
])

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Customer Directory & Lifetime Analytics</h1>
            <p class="text-sm text-slate-500 mt-1">Registered customer accounts, orders placed, lifetime spend, and address records.</p>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Accounts</p>
                <p class="text-2xl font-extrabold text-slate-900 mt-0.5">{{ number_format($stats['total_customers']) }}</p>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Active Customers</p>
                <p class="text-2xl font-extrabold text-emerald-600 mt-0.5">{{ number_format($stats['active_customers']) }}</p>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Deactivated</p>
                <p class="text-2xl font-extrabold text-rose-600 mt-0.5">{{ number_format($stats['deactivated_customers']) }}</p>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Lifetime Spend</p>
                <p class="text-2xl font-extrabold text-slate-900 mt-0.5">₹{{ number_format($stats['total_lifetime_value'], 0) }}</p>
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <!-- Status Tabs -->
        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 md:pb-0">
            <a href="{{ route('admin.customers.index', ['status' => 'all', 'q' => $search]) }}" 
               class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition {{ ($status ?? 'all') === 'all' ? 'bg-indigo-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                All ({{ $stats['total_customers'] }})
            </a>
            <a href="{{ route('admin.customers.index', ['status' => 'active', 'q' => $search]) }}" 
               class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition {{ ($status ?? '') === 'active' ? 'bg-emerald-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                Active ({{ $stats['active_customers'] }})
            </a>
            <a href="{{ route('admin.customers.index', ['status' => 'deactivated', 'q' => $search]) }}" 
               class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition {{ ($status ?? '') === 'deactivated' ? 'bg-rose-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                Deactivated ({{ $stats['deactivated_customers'] }})
            </a>
        </div>

        <form method="GET" action="{{ route('admin.customers.index') }}" class="flex items-center gap-3">
            <input type="hidden" name="status" value="{{ $status ?? 'all' }}">
            <div class="relative flex-1 md:w-72">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="q" value="{{ $search }}" placeholder="Search customer name or email..." 
                       class="w-full pl-10 pr-4 py-2 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none">
            </div>
            <button type="submit" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold transition">
                Search
            </button>
            @if($search)
                <a href="{{ route('admin.customers.index', ['status' => $status]) }}" class="px-3 py-2 rounded-xl border border-slate-200 text-slate-500 text-sm hover:bg-slate-50 transition">
                    Clear
                </a>
            @endif
        </form>
    </div>

    <!-- Customer Directory Table -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50/75 border-b border-slate-200 text-xs font-bold text-slate-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Customer</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Total Orders</th>
                        <th class="px-6 py-4">Lifetime Spend</th>
                        <th class="px-6 py-4">Joined Date</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($customers as $customer)
                        <tr class="hover:bg-slate-50/60 transition {{ $customer->isDeactivated() ? 'bg-rose-50/20' : '' }}">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full {{ $customer->isDeactivated() ? 'bg-rose-100 text-rose-700' : 'bg-slate-100 text-slate-700' }} flex items-center justify-center font-bold text-sm">
                                        {{ strtoupper(substr($customer->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.customers.show', $customer) }}" class="font-bold text-slate-900 hover:text-indigo-600 transition block">
                                            {{ $customer->name }}
                                        </a>
                                        <p class="text-xs text-slate-400 font-mono">{{ $customer->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($customer->isDeactivated())
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-700">
                                        Deactivated
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">
                                        Active
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-semibold text-slate-800">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $customer->orders_count > 0 ? 'bg-indigo-50 text-indigo-700' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $customer->orders_count }} {{ Str::plural('order', $customer->orders_count) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-bold text-slate-900 font-mono text-xs">
                                ₹{{ number_format($customer->orders_sum_total ?? 0, 0) }}
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-500">
                                {{ $customer->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="inline-flex items-center gap-2">
                                    <form method="POST" action="{{ route('admin.customers.toggle', $customer) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" 
                                                onclick="return confirm('{{ $customer->isDeactivated() ? 'Activate this customer account?' : 'Deactivate this customer account? They will not be able to log in.' }}')"
                                                class="px-2.5 py-1.5 rounded-lg text-xs font-bold transition {{ $customer->isDeactivated() ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' : 'bg-rose-50 text-rose-700 hover:bg-rose-100' }}">
                                            {{ $customer->isDeactivated() ? 'Activate' : 'Deactivate' }}
                                        </button>
                                    </form>

                                    <a href="{{ route('admin.customers.show', $customer) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition">
                                        <span>View</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                No registered customers found matching criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($customers->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $customers->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
