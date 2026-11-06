{{-- resources/views/storefront/lumiere/account.blade.php
     Data from StorefrontController@account: $user, $orders, $addresses, $theme --}}
<x-storefront-layout :theme="$theme" title="My Account">

<div class="max-w-6xl mx-auto px-4 sm:px-6 md:px-12 py-6 sm:py-10">
  <div class="mb-10">
    <h1 class="font-display italic text-3xl md:text-4xl text-gray-900 mb-1">My Account</h1>
    <p class="text-sm text-gray-400">Welcome back, {{ $user->name }}</p>
  </div>

  @if(session('profile_success'))
    <div class="mb-8 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-xs font-semibold text-emerald-800">
      ✓ {{ session('profile_success') }}
    </div>
  @endif

  <div class="grid md:grid-cols-[260px_1fr] gap-8 items-start">

    {{-- Sidebar Navigation --}}
    <aside class="space-y-4">
      <div class="bg-white rounded-3xl border border-pink-100 p-6 text-center shadow-sm shadow-pink-50">
        <div class="w-16 h-16 rounded-full bg-pink-50 text-pink-600 font-display italic text-2xl flex items-center justify-center mx-auto mb-3">
          {{ strtoupper(mb_substr($user->name, 0, 1)) }}
        </div>
        <p class="font-semibold text-gray-900">{{ $user->name }}</p>
        <p class="text-xs text-gray-400 mt-0.5 break-all">{{ $user->email }}</p>
      </div>

      <nav class="bg-white rounded-3xl border border-pink-100 p-3 shadow-sm shadow-pink-50 space-y-1">
        <button type="button" onclick="showTab('orders')" id="tabBtn-orders"
                class="acc-tab-btn w-full flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-bold uppercase tracking-wider text-left transition-all bg-pink-50 text-pink-600">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
          My Orders
        </button>
        <button type="button" onclick="showTab('addresses')" id="tabBtn-addresses"
                class="acc-tab-btn w-full flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-bold uppercase tracking-wider text-left transition-all text-gray-500 hover:bg-pink-50/60 hover:text-pink-600">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
          Addresses
        </button>
        <button type="button" onclick="showTab('profile')" id="tabBtn-profile"
                class="acc-tab-btn w-full flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-bold uppercase tracking-wider text-left transition-all text-gray-500 hover:bg-pink-50/60 hover:text-pink-600">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
          Profile
        </button>
        <a href="{{ route('wishlist') }}"
           class="flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-bold uppercase tracking-wider text-gray-500 hover:bg-pink-50/60 hover:text-pink-600 transition-colors">
          <i class="fa-regular fa-heart w-4 text-center text-sm"></i>
          Wishlist
        </a>
        <a href="{{ route('cart') }}"
           class="flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-bold uppercase tracking-wider text-gray-500 hover:bg-pink-50/60 hover:text-pink-600 transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4zM3 6h18M16 10a4 4 0 01-8 0"/></svg>
          My Bag
        </a>
        <div class="pt-2 mt-2 border-t border-pink-50">
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 rounded-2xl text-left text-xs font-bold uppercase tracking-wider text-gray-400 hover:bg-pink-50/60 hover:text-pink-600 transition-colors">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
              Sign Out
            </button>
          </form>
        </div>
      </nav>
    </aside>

    {{-- Main Content Panels --}}
    <div>

      {{-- 1. ORDERS TAB --}}
      <section id="tab-orders" class="acc-panel space-y-4">
        <h2 class="font-display italic text-2xl text-gray-900 mb-4">Recent Orders</h2>
        @forelse ($orders as $order)
          <article class="bg-white rounded-3xl border border-pink-100 p-6 shadow-sm shadow-pink-50">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4 pb-4 border-b border-pink-50">
              <div>
                <div class="flex items-center gap-2">
                  <p class="font-semibold text-gray-900">Order #{{ $order->reference ?? $order->id }}</p>
                  <a href="{{ route('order.confirmation', $order->id) }}" class="text-xs text-pink-600 font-bold hover:underline">
                    View Details &rarr;
                  </a>
                </div>
                <p class="text-xs text-gray-400 mt-0.5">
                  {{ $order->created_at->format('d M Y, h:i A') }} · {{ $order->items->count() }} item(s)
                </p>
              </div>
              <span class="text-[10px] font-black uppercase tracking-widest px-3 py-1.5 rounded-full
                {{ match($order->status) {
                  'delivered' => 'bg-emerald-50 text-emerald-700',
                  'shipped'   => 'bg-blue-50 text-blue-700',
                  'cancelled' => 'bg-rose-50 text-rose-700',
                  default     => 'bg-pink-50 text-pink-600'
                } }}">
                {{ ucfirst($order->status) }}
              </span>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-4">
              <div>
                <p class="text-xs text-gray-400">Total Price</p>
                <p class="text-2xl font-bold text-pink-600">₹{{ number_format($order->total, 0) }}</p>
              </div>

              <div class="flex items-center gap-3 text-xs">
                @if($order->isCancellable())
                  <button type="button" onclick="submitCancel({{ $order->id }})"
                          class="px-4 py-2 rounded-2xl border border-rose-200 text-rose-600 font-bold uppercase tracking-wider hover:bg-rose-50 transition-colors">
                    Cancel Order
                  </button>
                @elseif($order->status === 'delivered')
                  <button type="button" onclick="submitReturn({{ $order->id }})"
                          class="px-4 py-2 rounded-2xl border border-amber-200 text-amber-700 font-bold uppercase tracking-wider hover:bg-amber-50 transition-colors">
                    Return / Exchange
                  </button>
                @endif
                <a href="{{ route('account.orders.invoice', $order->id) }}" target="_blank"
                   class="px-4 py-2 rounded-2xl border border-gray-200 text-gray-700 font-bold uppercase tracking-wider hover:border-pink-400 hover:text-pink-600 transition-colors">
                  Tax Invoice 🖨️
                </a>
              </div>
            </div>
          </article>
        @empty
          <div class="bg-white rounded-3xl border border-pink-100 p-12 text-center shadow-sm shadow-pink-50">
            <p class="font-display italic text-2xl text-gray-900 mb-1">No orders yet</p>
            <p class="text-sm text-gray-400 mb-6">Discover our French silk lingerie collections.</p>
            <a href="{{ route('home') }}" class="inline-block py-3 px-8 rounded-2xl text-xs font-bold uppercase tracking-widest bg-gray-900 text-white hover:bg-pink-600 transition-colors">
              Explore Collections &rarr;
            </a>
          </div>
        @endforelse
      </section>

      {{-- 2. ADDRESSES TAB --}}
      <section id="tab-addresses" class="acc-panel hidden space-y-6">
        <h2 class="font-display italic text-2xl text-gray-900">Saved Addresses</h2>

        @if(isset($addresses) && $addresses->isNotEmpty())
          <div class="grid sm:grid-cols-2 gap-4">
            @foreach($addresses as $addr)
              <div class="bg-white rounded-3xl border p-5 shadow-sm shadow-pink-50 relative {{ $addr->is_default ? 'border-pink-300' : 'border-pink-100' }}">
                <div class="flex items-center justify-between mb-2">
                  <span class="text-[10px] font-bold uppercase tracking-widest px-2.5 py-0.5 rounded-full bg-pink-50 text-pink-600">{{ $addr->type }}</span>
                  <div class="flex items-center gap-2">
                    @if($addr->is_default)
                      <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full uppercase">Default</span>
                    @endif
                    <button type="button" onclick="removeAddress({{ $addr->id }})" class="text-rose-500 hover:text-rose-700 text-xs font-bold">
                      &times; Delete
                    </button>
                  </div>
                </div>
                <p class="font-semibold text-gray-900 text-base">{{ $addr->name }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $addr->phone }}</p>
                <p class="text-xs text-gray-600 mt-2 leading-relaxed">
                  {{ $addr->address1 }}{{ $addr->address2 ? ', ' . $addr->address2 : '' }}<br>
                  {{ $addr->city }}, {{ $addr->state }} — {{ $addr->pincode }}
                </p>
              </div>
            @endforeach
          </div>
        @endif

        {{-- Add Address Form --}}
        <div class="bg-white rounded-3xl border border-pink-100 p-6 md:p-8 max-w-xl shadow-sm shadow-pink-50">
          <h3 class="font-display italic text-xl text-gray-900 mb-4">Add New Address</h3>
          <form id="addAddressForm" onsubmit="event.preventDefault(); saveAddress();" class="space-y-4">
            @csrf
            <div>
              <label class="block text-[10px] uppercase tracking-wider font-bold text-gray-500 mb-1">Full Name *</label>
              <input type="text" name="name" id="newAddrName" required value="{{ $user->name ?? '' }}" class="w-full rounded-2xl border border-gray-200 px-4 py-2.5 text-xs text-gray-800 outline-none focus:border-pink-500">
            </div>
            <div>
              <label class="block text-[10px] uppercase tracking-wider font-bold text-gray-500 mb-1">Phone Number *</label>
              <input type="tel" name="phone" id="newAddrPhone" required pattern="[6-9][0-9]{9}" maxlength="10" placeholder="10-digit mobile" class="w-full rounded-2xl border border-gray-200 px-4 py-2.5 text-xs text-gray-800 outline-none focus:border-pink-500">
            </div>
            <div>
              <label class="block text-[10px] uppercase tracking-wider font-bold text-gray-500 mb-1">Address Line 1 *</label>
              <input type="text" name="address1" id="newAddr1" required placeholder="Flat, Street, Area" class="w-full rounded-2xl border border-gray-200 px-4 py-2.5 text-xs text-gray-800 outline-none focus:border-pink-500">
            </div>
            <div>
              <label class="block text-[10px] uppercase tracking-wider font-bold text-gray-500 mb-1">Address Line 2 (Optional)</label>
              <input type="text" name="address2" id="newAddr2" placeholder="Landmark" class="w-full rounded-2xl border border-gray-200 px-4 py-2.5 text-xs text-gray-800 outline-none focus:border-pink-500">
            </div>
            <div class="grid grid-cols-3 gap-3">
              <div>
                <label class="block text-[10px] uppercase tracking-wider font-bold text-gray-500 mb-1">City *</label>
                <input type="text" name="city" id="newAddrCity" required class="w-full rounded-2xl border border-gray-200 px-3 py-2.5 text-xs text-gray-800 outline-none focus:border-pink-500">
              </div>
              <div>
                <label class="block text-[10px] uppercase tracking-wider font-bold text-gray-500 mb-1">State *</label>
                <input type="text" name="state" id="newAddrState" required class="w-full rounded-2xl border border-gray-200 px-3 py-2.5 text-xs text-gray-800 outline-none focus:border-pink-500">
              </div>
              <div>
                <label class="block text-[10px] uppercase tracking-wider font-bold text-gray-500 mb-1">PIN *</label>
                <input type="text" name="pincode" id="newAddrPin" required maxlength="6" pattern="[0-9]{6}" class="w-full rounded-2xl border border-gray-200 px-3 py-2.5 text-xs text-gray-800 outline-none focus:border-pink-500">
              </div>
            </div>
            <div class="flex items-center gap-2 pt-2">
              <input type="checkbox" name="is_default" id="newAddrDefault" value="1" class="rounded accent-pink-600">
              <label for="newAddrDefault" class="text-xs text-gray-500 cursor-pointer">Set as default delivery address</label>
            </div>
            <button type="submit" id="saveAddrBtn" class="py-3 px-8 rounded-2xl text-xs font-bold uppercase tracking-widest text-white bg-gray-900 hover:bg-pink-600 transition-colors">
              Save Address
            </button>
          </form>
        </div>
      </section>

      {{-- 3. PROFILE TAB --}}
      <section id="tab-profile" class="acc-panel hidden space-y-6">
        <h2 class="font-display italic text-2xl text-gray-900">Personal Information</h2>
        <div class="bg-white rounded-3xl border border-pink-100 p-6 md:p-8 max-w-xl shadow-sm shadow-pink-50">
          <form method="POST" action="{{ route('account.update') }}" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
              <label class="block text-[10px] uppercase tracking-wider font-bold text-gray-500 mb-1">Full Name</label>
              <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full rounded-2xl border border-gray-200 px-4 py-2.5 text-xs text-gray-800 outline-none focus:border-pink-500">
            </div>
            <div>
              <label class="block text-[10px] uppercase tracking-wider font-bold text-gray-500 mb-1">Email Address</label>
              <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full rounded-2xl border border-gray-200 px-4 py-2.5 text-xs text-gray-800 outline-none focus:border-pink-500">
            </div>
            <div class="border-t border-pink-50 pt-4">
              <label class="block text-[10px] uppercase tracking-wider font-bold text-gray-500 mb-1">New Password (leave empty to retain)</label>
              <input type="password" name="password" autocomplete="new-password" class="w-full rounded-2xl border border-gray-200 px-4 py-2.5 text-xs text-gray-800 outline-none focus:border-pink-500">
            </div>
            <div>
              <label class="block text-[10px] uppercase tracking-wider font-bold text-gray-500 mb-1">Confirm Password</label>
              <input type="password" name="password_confirmation" autocomplete="new-password" class="w-full rounded-2xl border border-gray-200 px-4 py-2.5 text-xs text-gray-800 outline-none focus:border-pink-500">
            </div>
            <button type="submit" class="py-3 px-8 rounded-2xl text-xs font-bold uppercase tracking-widest text-white bg-gray-900 hover:bg-pink-600 transition-colors mt-2">
              Update Details
            </button>
          </form>
        </div>
      </section>

    </div>
  </div>
</div>

@push('scripts')
<script>
function showTab(tab) {
  document.querySelectorAll('.acc-panel').forEach(p => p.classList.add('hidden'));
  document.getElementById('tab-' + tab)?.classList.remove('hidden');

  document.querySelectorAll('.acc-tab-btn').forEach(b => {
    b.classList.remove('bg-pink-50', 'text-pink-600');
    b.classList.add('text-gray-500');
  });

  const activeBtn = document.getElementById('tabBtn-' + tab);
  if (activeBtn) {
    activeBtn.classList.add('bg-pink-50', 'text-pink-600');
    activeBtn.classList.remove('text-gray-500');
  }
}

async function submitCancel(orderId) {
  const reason = prompt('Please specify a reason for cancellation:');
  if (!reason) return;
  try {
    const res = await fetch(`/orders/${orderId}/request`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
      body: JSON.stringify({ type: 'cancel', reason: reason })
    });
    const data = await res.json();
    alert(data.message || 'Cancellation request processed.');
    window.location.reload();
  } catch {
    alert('Error submitting cancellation request.');
  }
}

async function submitReturn(orderId) {
  const reason = prompt('Please specify reason for return / exchange:');
  if (!reason) return;
  try {
    const res = await fetch(`/orders/${orderId}/request`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
      body: JSON.stringify({ type: 'return', reason: reason })
    });
    const data = await res.json();
    alert(data.message || 'Return request submitted.');
    window.location.reload();
  } catch {
    alert('Error submitting return request.');
  }
}

async function saveAddress() {
  const btn = document.getElementById('saveAddrBtn');
  btn.disabled = true;
  btn.textContent = 'Saving...';
  const payload = {
    name: document.getElementById('newAddrName')?.value,
    phone: document.getElementById('newAddrPhone')?.value,
    address1: document.getElementById('newAddr1')?.value,
    address2: document.getElementById('newAddr2')?.value || null,
    city: document.getElementById('newAddrCity')?.value,
    state: document.getElementById('newAddrState')?.value,
    pincode: document.getElementById('newAddrPin')?.value,
    is_default: document.getElementById('newAddrDefault')?.checked ? 1 : 0,
  };
  try {
    const res = await fetch('{{ route('account.addresses.store') }}', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
      body: JSON.stringify(payload)
    });
    const data = await res.json();
    if (data.success) {
      alert('Address saved successfully.');
      window.location.reload();
    } else {
      alert(data.message || 'Error saving address.');
    }
  } catch {
    alert('Network error saving address.');
  } finally {
    btn.disabled = false;
    btn.textContent = 'Save Address';
  }
}

async function removeAddress(id) {
  if (!confirm('Are you sure you want to remove this saved address?')) return;
  try {
    const res = await fetch(`/account/addresses/${id}`, {
      method: 'DELETE',
      headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    });
    const data = await res.json();
    if (data.success) {
      window.location.reload();
    } else {
      alert(data.message || 'Error deleting address.');
    }
  } catch {
    alert('Error deleting address.');
  }
}
</script>
@endpush
</x-storefront-layout>
