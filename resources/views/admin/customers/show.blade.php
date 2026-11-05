@extends('admin.layout', [
    'title' => 'Customer: ' . $customer->name,
    'breadcrumb' => 'Customer Details',
])

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <!-- Back Button & Header -->
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.customers.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 hover:text-slate-800 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            <span>Back to Customers</span>
        </a>

        <div class="flex items-center gap-2">
            <!-- Reset Password Button -->
            <button type="button" onclick="document.getElementById('passwordModal').classList.remove('hidden')" 
                    class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 shadow-xs transition">
                Reset Password
            </button>

            <!-- Toggle Status Form -->
            <form method="POST" action="{{ route('admin.customers.toggle', $customer) }}" class="inline">
                @csrf
                @method('PATCH')
                <button type="submit" 
                        onclick="return confirm('{{ $customer->isDeactivated() ? 'Activate customer account?' : 'Deactivate customer account? Customer will be logged out and cannot log in.' }}')"
                        class="px-3 py-1.5 rounded-xl text-xs font-bold shadow-xs transition {{ $customer->isDeactivated() ? 'bg-emerald-600 hover:bg-emerald-700 text-white' : 'bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200' }}">
                    {{ $customer->isDeactivated() ? 'Activate Account' : 'Deactivate Account' }}
                </button>
            </form>
        </div>
    </div>

    <!-- Customer Overview Card -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-6 sm:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6 pb-6 border-b border-slate-100">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl {{ $customer->isDeactivated() ? 'bg-rose-600' : 'bg-indigo-600' }} text-white flex items-center justify-center font-extrabold text-2xl shadow-md">
                    {{ strtoupper(substr($customer->name, 0, 1)) }}
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900">{{ $customer->name }}</h1>
                        @if($customer->isDeactivated())
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-700">Deactivated</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">Active</span>
                        @endif
                    </div>
                    <p class="text-sm text-slate-500 font-mono">{{ $customer->email }}</p>
                    <p class="text-xs text-slate-400 mt-1">Customer since {{ $customer->created_at->format('F d, Y') }}</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 sm:border-l sm:border-slate-100 sm:pl-6">
                <div>
                    <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Orders</span>
                    <span class="text-2xl font-black text-slate-900">{{ $customer->orders_count }}</span>
                </div>
                <div>
                    <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Lifetime Spend</span>
                    <span class="text-2xl font-black text-slate-900 font-mono">₹{{ number_format($customer->orders_sum_total ?? 0, 0) }}</span>
                </div>
            </div>
        </div>

        <!-- Saved Addresses -->
        <div class="pt-6">
            <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Saved Delivery Addresses ({{ $addresses->count() }})</h2>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($addresses as $addr)
                    <div class="p-4 rounded-xl border {{ $addr->is_default ? 'border-indigo-200 bg-indigo-50/20' : 'border-slate-200 bg-slate-50/40' }} relative">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-slate-800 uppercase">{{ $addr->type }}</span>
                            @if($addr->is_default)
                                <span class="text-[10px] uppercase font-bold text-indigo-600 bg-indigo-100 px-2 py-0.5 rounded-full">Default</span>
                            @endif
                        </div>
                        <p class="text-sm font-semibold text-slate-900">{{ $addr->name }}</p>
                        <p class="text-xs text-slate-500 mt-0.5">{{ $addr->phone }}</p>
                        <p class="text-xs text-slate-600 mt-2 leading-relaxed">
                            {{ $addr->address1 }}{{ $addr->address2 ? ', ' . $addr->address2 : '' }}<br>
                            {{ $addr->city }}, {{ $addr->state }} — {{ $addr->pincode }}
                        </p>
                    </div>
                @empty
                    <div class="col-span-full py-4 text-xs text-slate-400">
                        No saved address records found for this customer.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Order History -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-base font-bold text-slate-900">Order History ({{ $orders->total() }})</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50/75 border-b border-slate-200 text-xs font-bold text-slate-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-3.5">Reference</th>
                        <th class="px-6 py-3.5">Date</th>
                        <th class="px-6 py-3.5">Items</th>
                        <th class="px-6 py-3.5">Total</th>
                        <th class="px-6 py-3.5">Payment</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($orders as $order)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.orders.show', $order) }}" class="font-mono font-bold text-indigo-600 hover:text-indigo-800 text-xs">
                                    {{ $order->reference }}
                                </a>
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-500">
                                {{ $order->created_at->format('M d, Y H:i') }}
                            </td>
                            <td class="px-6 py-4 text-xs">
                                {{ $order->items->count() }} items ({{ $order->items->sum('qty') }} pcs)
                            </td>
                            <td class="px-6 py-4 font-bold text-slate-900 font-mono text-xs">
                                ₹{{ number_format($order->total, 0) }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-xs uppercase font-semibold text-slate-700 bg-slate-100 px-2 py-0.5 rounded">
                                    {{ $order->payment_method }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $order->status_color }}">
                                    {{ $order->status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.orders.show', $order) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition inline-block">
                                    View Order
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-slate-400">
                                No orders placed yet by this customer.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

</div>

<!-- Reset Password Modal -->
<div id="passwordModal" class="hidden fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-900">Reset Customer Password</h3>
            <button type="button" onclick="document.getElementById('passwordModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
        </div>
        <p class="text-xs text-slate-500">Set a new password for <strong>{{ $customer->name }}</strong> ({{ $customer->email }}).</p>
        <form method="POST" action="{{ route('admin.customers.password', $customer) }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">New Password</label>
                <input type="password" name="password" required minlength="8" placeholder="Minimum 8 characters"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Confirm New Password</label>
                <input type="password" name="password_confirmation" required minlength="8" placeholder="Repeat password"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none">
            </div>
            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('passwordModal').classList.add('hidden')"
                        class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm transition">
                    Update Password
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
