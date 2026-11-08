{{--
    ======================================================================
    THEME: BUTTERCUP — Pastel Comfort Intimates & Loungewear
    PAGE: CUSTOMER ACCOUNT & ORDERS DASHBOARD
    ======================================================================
    File: resources/views/storefront/buttercup/account.blade.php
    Controller: App\Http\Controllers\StorefrontController@account
    Route Context: GET /account (Auth Middleware Guard: customer)
    ViewModel Injections:
      - $theme: Active Template model ('buttercup')
      - $orders: Customer's order collection with shipment status
    Responsibility: Patron self-service dashboard for orders, addresses, and settings
    Features:
      - Order history listing with fulfillment status and invoice download links
      - Shipping & billing address book manager
      - Customer security controls (Password update, 2FA OTP status)
      - Loyalty rewards points balance and redemption history
    Operations & Maintenance:
      - Strict BOLA/RLS enforcement: customers can only view their own records
--}}
<x-storefront-layout :theme="$theme" title="My Account">
<div class="max-w-6xl mx-auto px-6 lg:px-10 py-10">
  <div class="mb-10">
    <h1 class="font-display text-3xl md:text-4xl mb-1 text-[#4A3B3A]">My Account</h1>
    <p class="font-body opacity-60 text-sm text-[#4A3B3A]">Welcome back, {{ $user->name }} 👋</p>
  </div>

  @if(session('profile_success'))
    <div class="mb-8 p-4 rounded-2xl bg-[#A8B89A22] border border-[#A8B89A] text-xs font-semibold text-[#7F9272]">
      ✓ {{ session('profile_success') }}
    </div>
  @endif

  <div class="grid md:grid-cols-[240px_1fr] gap-8 items-start">

    {{-- Sidebar Navigation --}}
    <nav class="bg-[#FFFAF5] rounded-[28px] border p-5 shadow-sm space-y-1" style="border-color:#D89AA022">
      <button type="button" onclick="showTab('orders')" id="tabBtn-orders"
              class="acc-tab-btn flex items-center gap-3 w-full px-4 py-3 rounded-2xl text-xs font-bold uppercase tracking-wider text-left transition-all bg-[#D89AA0] text-[#FFFAF5]">
        📦 My Orders
      </button>
      <button type="button" onclick="showTab('addresses')" id="tabBtn-addresses"
              class="acc-tab-btn flex items-center gap-3 w-full px-4 py-3 rounded-2xl text-xs font-bold uppercase tracking-wider text-left transition-all text-[#4A3B3A] opacity-70 hover:opacity-100 hover:bg-[#D89AA015]">
        📍 Addresses
      </button>
      <button type="button" onclick="showTab('profile')" id="tabBtn-profile"
              class="acc-tab-btn flex items-center gap-3 w-full px-4 py-3 rounded-2xl text-xs font-bold uppercase tracking-wider text-left transition-all text-[#4A3B3A] opacity-70 hover:opacity-100 hover:bg-[#D89AA015]">
        👤 Profile
      </button>
      <a href="{{ route('wishlist') }}"
         class="flex items-center gap-3 w-full px-4 py-3 rounded-2xl text-xs font-bold uppercase tracking-wider text-left transition-all text-[#4A3B3A] opacity-70 hover:opacity-100 hover:bg-[#D89AA015]">
        💖 Wishlist
      </a>
      <a href="{{ route('cart') }}"
         class="flex items-center gap-3 w-full px-4 py-3 rounded-2xl text-xs font-bold uppercase tracking-wider text-left transition-all text-[#4A3B3A] opacity-70 hover:opacity-100 hover:bg-[#D89AA015]">
        🛍️ My Bag
      </a>
      <div class="pt-3 border-t mt-2" style="border-color:#D89AA022">
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="flex items-center gap-3 px-4 py-2.5 w-full text-left text-xs font-bold uppercase tracking-wider opacity-50 hover:opacity-90 text-[#4A3B3A] rounded-2xl">
            🚪 Sign Out
          </button>
        </form>
      </div>
    </nav>

    {{-- Main Content Area --}}
    <div>

      {{-- 1. ORDERS TAB --}}
      <div id="tab-orders" class="acc-panel space-y-4">
        <h2 class="font-display text-2xl text-[#4A3B3A] mb-4">Recent Orders</h2>
        @forelse ($orders as $order)
          <div class="bg-[#FFFAF5] rounded-[28px] border p-6 shadow-sm transition-all hover:shadow-md" style="border-color:#D89AA022">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4 pb-4 border-b" style="border-color:#D89AA015">
              <div>
                <div class="flex items-center gap-2">
                  <p class="font-display font-bold text-lg text-[#4A3B3A]">#{{ $order->reference ?? $order->id }}</p>
                  <a href="{{ route('order.confirmation', $order->id) }}" class="text-xs font-bold underline" style="color:#D89AA0">
                    View Details &rarr;
                  </a>
                </div>
                <p class="font-body text-xs opacity-50 text-[#4A3B3A] mt-0.5">
                  Placed on {{ $order->created_at->format('d M Y, h:i A') }} · {{ $order->items->count() }} item(s)
                </p>
              </div>
              <div class="flex items-center gap-3">
                <span class="text-[10px] font-bold uppercase tracking-widest px-3 py-1 rounded-full
                  {{ match($order->status) {
                    'delivered' => 'bg-emerald-100 text-emerald-800',
                    'shipped'   => 'bg-blue-100 text-blue-800',
                    'cancelled' => 'bg-rose-100 text-rose-800',
                    default     => 'bg-[#D89AA022] text-[#B97680]'
                  } }}">
                  {{ ucfirst($order->status) }}
                </span>
              </div>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-4">
              <div>
                <p class="text-xs opacity-50 text-[#4A3B3A]">Total Amount</p>
                <p class="font-display text-2xl font-bold" style="color:#B97680">₹{{ number_format($order->total, 0) }}</p>
              </div>

              <div class="flex items-center gap-3 text-xs">
                @if($order->isCancellable())
                  <button type="button" onclick="submitCancel({{ $order->id }})"
                          class="px-4 py-2 rounded-full border border-rose-300 text-rose-600 font-bold uppercase tracking-wider hover:bg-rose-50 transition-all">
                    Cancel Order
                  </button>
                @elseif($order->status === 'delivered')
                  <button type="button" onclick="submitReturn({{ $order->id }})"
                          class="px-4 py-2 rounded-full border border-amber-300 text-amber-700 font-bold uppercase tracking-wider hover:bg-amber-50 transition-all">
                    Return / Exchange
                  </button>
                @endif
                <a href="{{ route('account.orders.invoice', $order->id) }}" target="_blank"
                   class="px-4 py-2 rounded-full border border-[#D89AA0] text-[#D89AA0] font-bold uppercase tracking-wider hover:bg-[#D89AA0] hover:text-white transition-all">
                  Invoice 🖨️
                </a>
              </div>
            </div>
          </div>
        @empty
          <div class="bg-[#FFFAF5] rounded-[28px] border p-12 text-center" style="border-color:#D89AA022">
            <p class="font-display text-xl text-[#4A3B3A] mb-2">No orders placed yet</p>
            <p class="font-body text-xs opacity-60 text-[#4A3B3A] mb-6">Explore our curated collections of soft lingerie.</p>
            <a href="{{ route('home') }}" class="py-3 px-8 rounded-full text-xs font-bold uppercase tracking-widest text-white inline-block transition-all" style="background:#D89AA0">
              Start Shopping &rarr;
            </a>
          </div>
        @endforelse
      </div>

      {{-- 2. ADDRESSES TAB --}}
      <div id="tab-addresses" class="acc-panel hidden space-y-6">
        <h2 class="font-display text-2xl text-[#4A3B3A]">Saved Delivery Addresses</h2>

        @if(isset($addresses) && $addresses->isNotEmpty())
          <div class="grid sm:grid-cols-2 gap-4">
            @foreach($addresses as $addr)
              <div class="bg-[#FFFAF5] rounded-[28px] border p-5 relative {{ $addr->is_default ? 'border-[#D89AA0] shadow-sm' : '' }}" style="border-color:{{ $addr->is_default ? '#D89AA0' : '#D89AA033' }}">
                <div class="flex items-center justify-between mb-2">
                  <span class="text-[10px] font-bold uppercase tracking-widest px-2.5 py-0.5 rounded-full bg-[#D89AA022] text-[#B97680]">{{ $addr->type }}</span>
                  <div class="flex items-center gap-2">
                    @if($addr->is_default)
                      <span class="text-[10px] font-bold text-[#7F9272] bg-[#A8B89A33] px-2 py-0.5 rounded-full uppercase">Default</span>
                    @endif
                    <button type="button" onclick="removeAddress({{ $addr->id }})" class="text-rose-500 hover:text-rose-700 text-xs font-bold">
                      &times; Delete
                    </button>
                  </div>
                </div>
                <p class="font-display font-bold text-base text-[#4A3B3A]">{{ $addr->name }}</p>
                <p class="font-body text-xs opacity-60 text-[#4A3B3A]">{{ $addr->phone }}</p>
                <p class="font-body text-xs opacity-80 text-[#4A3B3A] mt-2 leading-relaxed">
                  {{ $addr->address1 }}{{ $addr->address2 ? ', ' . $addr->address2 : '' }}<br>
                  {{ $addr->city }}, {{ $addr->state }} — {{ $addr->pincode }}
                </p>
              </div>
            @endforeach
          </div>
        @endif

        {{-- Add Address Form --}}
        <div class="bg-[#FFFAF5] rounded-[28px] border p-6 md:p-8 max-w-xl" style="border-color:#D89AA033">
          <h3 class="font-display text-xl mb-4 text-[#4A3B3A]">Add New Address</h3>
          <form id="addAddressForm" onsubmit="event.preventDefault(); saveAddress();" class="space-y-4">
            @csrf
            <div>
              <label class="block text-xs font-bold uppercase tracking-wider opacity-60 text-[#4A3B3A] mb-1">Full Name *</label>
              <input type="text" name="name" id="newAddrName" required value="{{ $user->name ?? '' }}" class="w-full bg-white border rounded-2xl px-4 py-2.5 text-xs text-[#4A3B3A] outline-none" style="border-color:#D89AA044">
            </div>
            <div>
              <label class="block text-xs font-bold uppercase tracking-wider opacity-60 text-[#4A3B3A] mb-1">Phone Number *</label>
              <input type="tel" name="phone" id="newAddrPhone" required pattern="[6-9][0-9]{9}" maxlength="10" placeholder="10-digit mobile" class="w-full bg-white border rounded-2xl px-4 py-2.5 text-xs text-[#4A3B3A] outline-none" style="border-color:#D89AA044">
            </div>
            <div>
              <label class="block text-xs font-bold uppercase tracking-wider opacity-60 text-[#4A3B3A] mb-1">Address Line 1 *</label>
              <input type="text" name="address1" id="newAddr1" required placeholder="Flat, Street, Area" class="w-full bg-white border rounded-2xl px-4 py-2.5 text-xs text-[#4A3B3A] outline-none" style="border-color:#D89AA044">
            </div>
            <div>
              <label class="block text-xs font-bold uppercase tracking-wider opacity-60 text-[#4A3B3A] mb-1">Address Line 2 (Optional)</label>
              <input type="text" name="address2" id="newAddr2" placeholder="Landmark" class="w-full bg-white border rounded-2xl px-4 py-2.5 text-xs text-[#4A3B3A] outline-none" style="border-color:#D89AA044">
            </div>
            <div class="grid grid-cols-3 gap-3">
              <div>
                <label class="block text-xs font-bold uppercase tracking-wider opacity-60 text-[#4A3B3A] mb-1">City *</label>
                <input type="text" name="city" id="newAddrCity" required class="w-full bg-white border rounded-2xl px-3 py-2.5 text-xs text-[#4A3B3A] outline-none" style="border-color:#D89AA044">
              </div>
              <div>
                <label class="block text-xs font-bold uppercase tracking-wider opacity-60 text-[#4A3B3A] mb-1">State *</label>
                <input type="text" name="state" id="newAddrState" required class="w-full bg-white border rounded-2xl px-3 py-2.5 text-xs text-[#4A3B3A] outline-none" style="border-color:#D89AA044">
              </div>
              <div>
                <label class="block text-xs font-bold uppercase tracking-wider opacity-60 text-[#4A3B3A] mb-1">PIN Code *</label>
                <input type="text" name="pincode" id="newAddrPin" required maxlength="6" pattern="[0-9]{6}" class="w-full bg-white border rounded-2xl px-3 py-2.5 text-xs text-[#4A3B3A] outline-none" style="border-color:#D89AA044">
              </div>
            </div>
            <div class="flex items-center gap-2 pt-2">
              <input type="checkbox" name="is_default" id="newAddrDefault" value="1" class="rounded accent-[#D89AA0]">
              <label for="newAddrDefault" class="text-xs opacity-70 text-[#4A3B3A] cursor-pointer">Set as default delivery address</label>
            </div>
            <button type="submit" id="saveAddrBtn" class="py-3 px-8 rounded-full text-xs font-bold uppercase tracking-widest text-white transition-all" style="background:#D89AA0">
              Save Address
            </button>
          </form>
        </div>
      </div>

      {{-- 3. PROFILE TAB --}}
      <div id="tab-profile" class="acc-panel hidden space-y-6">
        <h2 class="font-display text-2xl text-[#4A3B3A]">Profile Settings</h2>
        <div class="bg-[#FFFAF5] rounded-[28px] border p-6 md:p-8 max-w-xl" style="border-color:#D89AA033">
          <form method="POST" action="{{ route('account.update') }}" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
              <label class="block text-xs font-bold uppercase tracking-wider opacity-60 text-[#4A3B3A] mb-1">Full Name</label>
              <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full bg-white border rounded-2xl px-4 py-2.5 text-xs text-[#4A3B3A] outline-none" style="border-color:#D89AA044">
            </div>
            <div>
              <label class="block text-xs font-bold uppercase tracking-wider opacity-60 text-[#4A3B3A] mb-1">Email Address</label>
              <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full bg-white border rounded-2xl px-4 py-2.5 text-xs text-[#4A3B3A] outline-none" style="border-color:#D89AA044">
            </div>
            <div class="border-t pt-4" style="border-color:#D89AA022">
              <label class="block text-xs font-bold uppercase tracking-wider opacity-60 text-[#4A3B3A] mb-1">New Password (leave blank to keep current)</label>
              <input type="password" name="password" autocomplete="new-password" class="w-full bg-white border rounded-2xl px-4 py-2.5 text-xs text-[#4A3B3A] outline-none" style="border-color:#D89AA044">
            </div>
            <div>
              <label class="block text-xs font-bold uppercase tracking-wider opacity-60 text-[#4A3B3A] mb-1">Confirm New Password</label>
              <input type="password" name="password_confirmation" autocomplete="new-password" class="w-full bg-white border rounded-2xl px-4 py-2.5 text-xs text-[#4A3B3A] outline-none" style="border-color:#D89AA044">
            </div>
            <button type="submit" class="py-3 px-8 rounded-full text-xs font-bold uppercase tracking-widest text-white transition-all mt-2" style="background:#D89AA0">
              Save Profile Changes
            </button>
          </form>
        </div>
      </div>

    </div>
  </div>
</div>

@push('scripts')
<script>
function showTab(tab) {
  document.querySelectorAll('.acc-panel').forEach(p => p.classList.add('hidden'));
  document.getElementById('tab-' + tab)?.classList.remove('hidden');

  document.querySelectorAll('.acc-tab-btn').forEach(b => {
    b.classList.remove('bg-[#D89AA0]', 'text-[#FFFAF5]');
    b.classList.add('text-[#4A3B3A]', 'opacity-70');
  });

  const activeBtn = document.getElementById('tabBtn-' + tab);
  if (activeBtn) {
    activeBtn.classList.add('bg-[#D89AA0]', 'text-[#FFFAF5]');
    activeBtn.classList.remove('text-[#4A3B3A]', 'opacity-70');
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
  const reason = prompt('Please specify reason for return / exchange (e.g. Size fit issue, fabric preference, damaged):');
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
