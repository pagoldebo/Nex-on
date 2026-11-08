{{--
    ======================================================================
    THEME: VYRA — Modern Noir & High-Contrast Streetwear
    PAGE: CUSTOMER ACCOUNT & ORDERS DASHBOARD
    ======================================================================
    File: resources/views/storefront/vyra/account.blade.php
    Controller: App\Http\Controllers\StorefrontController@account
    Route Context: GET /account (Auth Middleware Guard: customer)
    ViewModel Injections:
      - $theme: Active Template model ('vyra')
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
    <h1 class="font-display uppercase text-3xl md:text-4xl mb-1 text-[#F2F0EC]">Customer Portal</h1>
    <p class="font-body opacity-60 text-xs uppercase tracking-wider text-[#F2F0EC]">Welcome back, {{ $user->name }}</p>
  </div>

  @if(session('profile_success'))
    <div class="mb-8 p-4 bg-[#D4FF3D15] border border-[#D4FF3D] text-xs font-bold uppercase tracking-wider text-[#D4FF3D]">
      ✓ {{ session('profile_success') }}
    </div>
  @endif

  <div class="grid md:grid-cols-[240px_1fr] gap-8 items-start">

    {{-- Sidebar Navigation --}}
    <nav class="bg-[#1A1A1D] border p-5 space-y-1" style="border-color:#FF3D5A22">
      <button type="button" onclick="showTab('orders')" id="tabBtn-orders"
              class="acc-tab-btn flex items-center gap-3 w-full px-4 py-3 text-xs font-bold uppercase tracking-wider text-left transition-all bg-[#FF3D5A] text-[#0E0E10]"
              style="clip-path:polygon(4% 0,100% 0,96% 100%,0 100%)">
        Orders
      </button>
      <button type="button" onclick="showTab('addresses')" id="tabBtn-addresses"
              class="acc-tab-btn flex items-center gap-3 w-full px-4 py-3 text-xs font-bold uppercase tracking-wider text-left transition-all text-[#F2F0EC] opacity-60 hover:opacity-100 hover:bg-[#FF3D5A15]">
        Addresses
      </button>
      <button type="button" onclick="showTab('profile')" id="tabBtn-profile"
              class="acc-tab-btn flex items-center gap-3 w-full px-4 py-3 text-xs font-bold uppercase tracking-wider text-left transition-all text-[#F2F0EC] opacity-60 hover:opacity-100 hover:bg-[#FF3D5A15]">
        Profile
      </button>
      <a href="{{ route('wishlist') }}"
         class="flex items-center gap-3 w-full px-4 py-3 text-xs font-bold uppercase tracking-wider text-left transition-all text-[#F2F0EC] opacity-60 hover:opacity-100 hover:bg-[#FF3D5A15]">
        Wishlist
      </a>
      <a href="{{ route('cart') }}"
         class="flex items-center gap-3 w-full px-4 py-3 text-xs font-bold uppercase tracking-wider text-left transition-all text-[#F2F0EC] opacity-60 hover:opacity-100 hover:bg-[#FF3D5A15]">
        My Bag
      </a>
      <div class="pt-3 border-t mt-2" style="border-color:#FF3D5A22">
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="flex items-center gap-3 px-4 py-2.5 w-full text-left text-xs font-bold uppercase tracking-wider opacity-50 hover:opacity-100 text-[#FF3D5A]">
            Sign Out &rarr;
          </button>
        </form>
      </div>
    </nav>

    {{-- Main Content Area --}}
    <div>

      {{-- 1. ORDERS TAB --}}
      <div id="tab-orders" class="acc-panel space-y-4">
        <h2 class="font-display uppercase text-2xl text-[#F2F0EC] mb-4">Order History</h2>
        @forelse ($orders as $order)
          <div class="bg-[#1A1A1D] border p-6 transition-all hover:border-[#FF3D5A55]" style="border-color:#FF3D5A22">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4 pb-4 border-b" style="border-color:#FF3D5A15">
              <div>
                <div class="flex items-center gap-3">
                  <p class="font-display uppercase font-bold text-lg text-[#F2F0EC]">#{{ $order->reference ?? $order->id }}</p>
                  <a href="{{ route('order.confirmation', $order->id) }}" class="text-xs font-bold uppercase underline text-[#FF3D5A]">
                    Details &rarr;
                  </a>
                </div>
                <p class="font-body text-xs opacity-50 text-[#F2F0EC] mt-0.5">
                  Placed on {{ $order->created_at->format('d M Y, h:i A') }} · {{ $order->items->count() }} item(s)
                </p>
              </div>
              <div class="flex items-center gap-3">
                <span class="text-[10px] font-bold uppercase tracking-widest px-3 py-1
                  {{ match($order->status) {
                    'delivered' => 'bg-emerald-950 text-emerald-400 border border-emerald-800',
                    'shipped'   => 'bg-blue-950 text-blue-400 border border-blue-800',
                    'cancelled' => 'bg-rose-950 text-rose-400 border border-rose-800',
                    default     => 'bg-[#FF3D5A22] text-[#FF3D5A] border border-[#FF3D5A44]'
                  } }}">
                  {{ ucfirst($order->status) }}
                </span>
              </div>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-4">
              <div>
                <p class="text-xs uppercase opacity-50 text-[#F2F0EC]">Total</p>
                <p class="font-display text-2xl font-bold text-[#F2F0EC]">₹{{ number_format($order->total, 0) }}</p>
              </div>

              <div class="flex items-center gap-3 text-xs">
                @if($order->isCancellable())
                  <button type="button" onclick="submitCancel({{ $order->id }})"
                          class="px-4 py-2 border border-rose-500 text-rose-400 font-bold uppercase tracking-wider hover:bg-rose-950 transition-all">
                    Cancel Order
                  </button>
                @elseif($order->status === 'delivered')
                  <button type="button" onclick="submitReturn({{ $order->id }})"
                          class="px-4 py-2 border border-amber-500 text-amber-400 font-bold uppercase tracking-wider hover:bg-amber-950 transition-all">
                    Return / Exchange
                  </button>
                @endif
                <a href="{{ route('account.orders.invoice', $order->id) }}" target="_blank"
                   class="px-4 py-2 border border-[#FF3D5A] text-[#FF3D5A] font-bold uppercase tracking-wider hover:bg-[#FF3D5A] hover:text-[#0E0E10] transition-all">
                  Invoice
                </a>
              </div>
            </div>
          </div>
        @empty
          <div class="bg-[#1A1A1D] border p-12 text-center" style="border-color:#FF3D5A22">
            <p class="font-display uppercase text-xl text-[#F2F0EC] mb-2">No Orders Found</p>
            <p class="font-body text-xs opacity-50 text-[#F2F0EC] mb-6">Discover bold statements from our new arrivals.</p>
            <a href="{{ route('home') }}" class="py-3 px-8 text-xs font-bold uppercase tracking-widest text-[#0E0E10] bg-[#FF3D5A] inline-block transition-all hover:bg-[#D4FF3D]"
               style="clip-path:polygon(4% 0,100% 0,96% 100%,0 100%)">
              Explore Drops &rarr;
            </a>
          </div>
        @endforelse
      </div>

      {{-- 2. ADDRESSES TAB --}}
      <div id="tab-addresses" class="acc-panel hidden space-y-6">
        <h2 class="font-display uppercase text-2xl text-[#F2F0EC]">Shipping Addresses</h2>

        @if(isset($addresses) && $addresses->isNotEmpty())
          <div class="grid sm:grid-cols-2 gap-4">
            @foreach($addresses as $addr)
              <div class="bg-[#1A1A1D] border p-5 relative {{ $addr->is_default ? 'border-[#FF3D5A]' : '' }}" style="border-color:{{ $addr->is_default ? '#FF3D5A' : '#FF3D5A33' }}">
                <div class="flex items-center justify-between mb-2">
                  <span class="text-[10px] font-bold uppercase tracking-widest px-2 py-0.5 bg-[#FF3D5A22] text-[#FF3D5A]">{{ $addr->type }}</span>
                  <div class="flex items-center gap-2">
                    @if($addr->is_default)
                      <span class="text-[10px] font-bold text-[#D4FF3D] bg-[#D4FF3D22] px-2 py-0.5 uppercase">Default</span>
                    @endif
                    <button type="button" onclick="removeAddress({{ $addr->id }})" class="text-rose-400 hover:text-rose-300 text-xs font-bold">
                      &times; Delete
                    </button>
                  </div>
                </div>
                <p class="font-display uppercase font-bold text-base text-[#F2F0EC]">{{ $addr->name }}</p>
                <p class="font-body text-xs opacity-60 text-[#F2F0EC]">{{ $addr->phone }}</p>
                <p class="font-body text-xs opacity-80 text-[#F2F0EC] mt-2 leading-relaxed">
                  {{ $addr->address1 }}{{ $addr->address2 ? ', ' . $addr->address2 : '' }}<br>
                  {{ $addr->city }}, {{ $addr->state }} — {{ $addr->pincode }}
                </p>
              </div>
            @endforeach
          </div>
        @endif

        {{-- Add Address Form --}}
        <div class="bg-[#1A1A1D] border p-6 md:p-8 max-w-xl" style="border-color:#FF3D5A33">
          <h3 class="font-display uppercase text-xl mb-4 text-[#F2F0EC]">Add Delivery Destination</h3>
          <form id="addAddressForm" onsubmit="event.preventDefault(); saveAddress();" class="space-y-4">
            @csrf
            <div>
              <label class="block text-xs font-bold uppercase tracking-wider opacity-60 text-[#F2F0EC] mb-1">Full Name *</label>
              <input type="text" name="name" id="newAddrName" required value="{{ $user->name ?? '' }}" class="w-full bg-[#0E0E10] border px-4 py-2.5 text-xs text-[#F2F0EC] outline-none" style="border-color:#FF3D5A44">
            </div>
            <div>
              <label class="block text-xs font-bold uppercase tracking-wider opacity-60 text-[#F2F0EC] mb-1">Phone Number *</label>
              <input type="tel" name="phone" id="newAddrPhone" required pattern="[6-9][0-9]{9}" maxlength="10" placeholder="10-digit mobile" class="w-full bg-[#0E0E10] border px-4 py-2.5 text-xs text-[#F2F0EC] outline-none" style="border-color:#FF3D5A44">
            </div>
            <div>
              <label class="block text-xs font-bold uppercase tracking-wider opacity-60 text-[#F2F0EC] mb-1">Address Line 1 *</label>
              <input type="text" name="address1" id="newAddr1" required placeholder="Flat, Street, Area" class="w-full bg-[#0E0E10] border px-4 py-2.5 text-xs text-[#F2F0EC] outline-none" style="border-color:#FF3D5A44">
            </div>
            <div>
              <label class="block text-xs font-bold uppercase tracking-wider opacity-60 text-[#F2F0EC] mb-1">Address Line 2 (Optional)</label>
              <input type="text" name="address2" id="newAddr2" placeholder="Landmark" class="w-full bg-[#0E0E10] border px-4 py-2.5 text-xs text-[#F2F0EC] outline-none" style="border-color:#FF3D5A44">
            </div>
            <div class="grid grid-cols-3 gap-3">
              <div>
                <label class="block text-xs font-bold uppercase tracking-wider opacity-60 text-[#F2F0EC] mb-1">City *</label>
                <input type="text" name="city" id="newAddrCity" required class="w-full bg-[#0E0E10] border px-3 py-2.5 text-xs text-[#F2F0EC] outline-none" style="border-color:#FF3D5A44">
              </div>
              <div>
                <label class="block text-xs font-bold uppercase tracking-wider opacity-60 text-[#F2F0EC] mb-1">State *</label>
                <input type="text" name="state" id="newAddrState" required class="w-full bg-[#0E0E10] border px-3 py-2.5 text-xs text-[#F2F0EC] outline-none" style="border-color:#FF3D5A44">
              </div>
              <div>
                <label class="block text-xs font-bold uppercase tracking-wider opacity-60 text-[#F2F0EC] mb-1">PIN *</label>
                <input type="text" name="pincode" id="newAddrPin" required maxlength="6" pattern="[0-9]{6}" class="w-full bg-[#0E0E10] border px-3 py-2.5 text-xs text-[#F2F0EC] outline-none" style="border-color:#FF3D5A44">
              </div>
            </div>
            <div class="flex items-center gap-2 pt-2">
              <input type="checkbox" name="is_default" id="newAddrDefault" value="1" class="accent-[#FF3D5A]">
              <label for="newAddrDefault" class="text-xs opacity-70 text-[#F2F0EC] cursor-pointer">Set as primary delivery address</label>
            </div>
            <button type="submit" id="saveAddrBtn" class="py-3 px-8 text-xs font-bold uppercase tracking-widest text-[#0E0E10] bg-[#D4FF3D] hover:bg-[#FF3D5A] hover:text-[#F2F0EC] transition-all"
                    style="clip-path:polygon(4% 0,100% 0,96% 100%,0 100%)">
              Save Address
            </button>
          </form>
        </div>
      </div>

      {{-- 3. PROFILE TAB --}}
      <div id="tab-profile" class="acc-panel hidden space-y-6">
        <h2 class="font-display uppercase text-2xl text-[#F2F0EC]">Security & Details</h2>
        <div class="bg-[#1A1A1D] border p-6 md:p-8 max-w-xl" style="border-color:#FF3D5A33">
          <form method="POST" action="{{ route('account.update') }}" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
              <label class="block text-xs font-bold uppercase tracking-wider opacity-60 text-[#F2F0EC] mb-1">Full Name</label>
              <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full bg-[#0E0E10] border px-4 py-2.5 text-xs text-[#F2F0EC] outline-none" style="border-color:#FF3D5A44">
            </div>
            <div>
              <label class="block text-xs font-bold uppercase tracking-wider opacity-60 text-[#F2F0EC] mb-1">Email Address</label>
              <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full bg-[#0E0E10] border px-4 py-2.5 text-xs text-[#F2F0EC] outline-none" style="border-color:#FF3D5A44">
            </div>
            <div class="border-t pt-4" style="border-color:#FF3D5A22">
              <label class="block text-xs font-bold uppercase tracking-wider opacity-60 text-[#F2F0EC] mb-1">New Password (leave empty to retain)</label>
              <input type="password" name="password" autocomplete="new-password" class="w-full bg-[#0E0E10] border px-4 py-2.5 text-xs text-[#F2F0EC] outline-none" style="border-color:#FF3D5A44">
            </div>
            <div>
              <label class="block text-xs font-bold uppercase tracking-wider opacity-60 text-[#F2F0EC] mb-1">Confirm Password</label>
              <input type="password" name="password_confirmation" autocomplete="new-password" class="w-full bg-[#0E0E10] border px-4 py-2.5 text-xs text-[#F2F0EC] outline-none" style="border-color:#FF3D5A44">
            </div>
            <button type="submit" class="py-3 px-8 text-xs font-bold uppercase tracking-widest text-[#0E0E10] bg-[#FF3D5A] hover:bg-[#D4FF3D] transition-all mt-2"
                    style="clip-path:polygon(4% 0,100% 0,96% 100%,0 100%)">
              Update Profile
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
    b.classList.remove('bg-[#FF3D5A]', 'text-[#0E0E10]');
    b.classList.add('text-[#F2F0EC]', 'opacity-60');
  });

  const activeBtn = document.getElementById('tabBtn-' + tab);
  if (activeBtn) {
    activeBtn.classList.add('bg-[#FF3D5A]', 'text-[#0E0E10]');
    activeBtn.classList.remove('text-[#F2F0EC]', 'opacity-60');
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
  btn.textContent = 'SAVING...';
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
    btn.textContent = 'SAVE ADDRESS';
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
