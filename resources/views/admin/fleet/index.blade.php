@extends('admin.layout')

@section('title', 'Logistics Fleet & Delivery Personnel')

@section('content')
<div class="space-y-6 pb-12" x-data="{ showCreateModal: false }">

    <!-- Header & Action Bar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 text-[10px] font-bold rounded-md bg-blue-50 text-blue-600 border border-blue-200/50 uppercase font-mono tracking-wider">Fleet Logistics</span>
                <span class="text-xs text-slate-400 font-medium">Phase 3 &bull; In-House Delivery Operations</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 mt-1">Delivery Personnel &amp; Fleet Management</h1>
            <p class="text-xs text-slate-500 mt-0.5">Manage drivers, assigned delivery drop-offs, vehicle fleets, and digital proof-of-delivery.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('driver.portal') }}" target="_blank"
               class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition flex items-center gap-2 shadow-xs">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                <span>Driver Mobile Portal</span>
            </a>

            <button @click="showCreateModal = true"
                    class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-md shadow-indigo-600/20 flex items-center gap-2 transition">
                <svg class="w-4 h-4 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Add Delivery Driver</span>
            </button>
        </div>
    </div>

    <!-- KPI Cards Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Drivers</div>
            <div class="text-2xl font-black text-slate-900 mt-1">{{ number_format($kpis['total_drivers']) }}</div>
            <div class="text-[10px] text-slate-500 mt-1">Registered drivers</div>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
            <div class="text-[11px] font-bold text-emerald-600 uppercase tracking-wider">Active Fleet</div>
            <div class="text-2xl font-black text-slate-900 mt-1">{{ number_format($kpis['active_drivers']) }}</div>
            <div class="text-[10px] text-emerald-600 font-semibold mt-1">Available on duty</div>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
            <div class="text-[11px] font-bold text-indigo-600 uppercase tracking-wider">Out for Delivery</div>
            <div class="text-2xl font-black text-slate-900 mt-1">{{ number_format($kpis['out_for_delivery']) }}</div>
            <div class="text-[10px] text-indigo-500 font-medium mt-1">Live shipments on road</div>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
            <div class="text-[11px] font-bold text-purple-600 uppercase tracking-wider">Completed Drops</div>
            <div class="text-2xl font-black text-slate-900 mt-1">{{ number_format($kpis['total_delivered']) }}</div>
            <div class="text-[10px] text-slate-500 mt-1">Successfully fulfilled</div>
        </div>
    </div>

    <!-- Fleet Directory Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50/75 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Driver Name</th>
                        <th class="px-6 py-4">Phone / Contact</th>
                        <th class="px-6 py-4">Vehicle &amp; Plate</th>
                        <th class="px-6 py-4">Active Drops</th>
                        <th class="px-6 py-4">Delivered</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Portal Link</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($fleet as $driver)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900 text-sm">{{ $driver->name }}</div>
                                <div class="text-[11px] text-slate-400">{{ $driver->email ?: 'No email' }}</div>
                            </td>

                            <td class="px-6 py-4">
                                <div class="font-mono font-bold text-slate-800">{{ $driver->phone }}</div>
                            </td>

                            <td class="px-6 py-4">
                                <div class="capitalize text-slate-800 flex items-center gap-1.5 font-bold">
                                    <span>
                                        @if($driver->vehicle_type === 'bike') 🏍️
                                        @elseif($driver->vehicle_type === 'van') 🚐
                                        @elseif($driver->vehicle_type === 'car') 🚗
                                        @else 🛵
                                        @endif
                                    </span>
                                    <span>{{ ucfirst($driver->vehicle_type) }}</span>
                                </div>
                                <div class="font-mono text-[10px] text-slate-400 mt-0.5">{{ $driver->vehicle_number ?: 'No plate assigned' }}</div>
                            </td>

                            <td class="px-6 py-4">
                                @if($driver->active_orders_count > 0)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                        {{ $driver->active_orders_count }} in progress
                                    </span>
                                @else
                                    <span class="text-slate-400 text-[11px]">Idle / Ready</span>
                                @endif
                            </td>

                            <td class="px-6 py-4">
                                <span class="font-bold text-slate-900">{{ number_format($driver->completed_orders_count) }}</span>
                                <span class="text-[10px] text-slate-400">drops</span>
                            </td>

                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold {{ $driver->status === 'active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $driver->status === 'active' ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                    {{ ucfirst($driver->status) }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('driver.portal', ['phone' => $driver->phone]) }}" target="_blank"
                                   class="px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 font-bold transition inline-flex items-center gap-1">
                                    <span>Open Web App</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                No delivery personnel registered yet. Click "Add Delivery Driver" to enroll your fleet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($fleet->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $fleet->links() }}
            </div>
        @endif
    </div>

    <!-- Add Driver Modal -->
    <div x-show="showCreateModal" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs"
         style="display: none;">
        
        <div @click.away="showCreateModal = false" class="bg-white rounded-3xl max-w-md w-full p-6 text-slate-900 shadow-2xl space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h3 class="text-base font-black">Register Delivery Personnel</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Add a new courier driver to dispatch orders.</p>
                </div>
                <button @click="showCreateModal = false" type="button" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
            </div>

            <form method="POST" action="{{ route('admin.fleet.store') }}" class="space-y-4 text-xs">
                @csrf

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Full Name *</label>
                    <input type="text" name="name" required placeholder="e.g. Rahul Sharma"
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-200 focus:outline-none focus:border-indigo-500 font-medium">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Mobile Phone *</label>
                        <input type="text" name="phone" required placeholder="+91 98765 00000"
                               class="w-full px-3.5 py-2 rounded-xl border border-slate-200 focus:outline-none focus:border-indigo-500 font-mono">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Email</label>
                        <input type="email" name="email" placeholder="rahul@delivery.in"
                               class="w-full px-3.5 py-2 rounded-xl border border-slate-200 focus:outline-none focus:border-indigo-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Vehicle Type *</label>
                        <select name="vehicle_type" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 focus:outline-none focus:border-indigo-500">
                            <option value="bike">Motorbike</option>
                            <option value="scooter">Electric Scooter</option>
                            <option value="van">Delivery Van</option>
                            <option value="car">Car / Sedan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Vehicle License Plate</label>
                        <input type="text" name="vehicle_number" placeholder="MH 02 AB 1234"
                               class="w-full px-3.5 py-2 rounded-xl border border-slate-200 focus:outline-none focus:border-indigo-500 font-mono uppercase">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Status</label>
                    <select name="status" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 focus:outline-none focus:border-indigo-500">
                        <option value="active">Active &amp; Ready</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button @click="showCreateModal = false" type="button" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 font-bold transition">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold transition shadow-sm">Save Driver</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
