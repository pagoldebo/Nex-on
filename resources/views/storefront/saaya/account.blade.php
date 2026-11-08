{{--
    ======================================================================
    THEME: SAAYA — Royal Indian Trousseau & Heritage Silk
    PAGE: CUSTOMER ACCOUNT & ORDERS DASHBOARD
    ======================================================================
    File: resources/views/storefront/saaya/account.blade.php
    Controller: App\Http\Controllers\StorefrontController@account
    Route Context: GET /account (Auth Middleware Guard: customer)
    ViewModel Injections:
      - $theme: Active Template model ('saaya')
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
<x-storefront-layout>

<div class="bg-ivory min-h-screen">

{{-- Maroon header --}}
<div class="bg-maroon py-12 lg:py-16 relative overflow-hidden">
  <div class="absolute inset-0 opacity-[.05] pointer-events-none"
       style="background-image:radial-gradient(circle,#C9A24B 1px,transparent 1px);background-size:24px 24px;"></div>
  <div class="relative max-w-7xl mx-auto px-6 lg:px-12">
    <p class="font-display italic text-gold text-lg mb-1">Welcome Back</p>
    <h1 class="font-display text-4xl lg:text-5xl text-ivory">{{ Str::before($user->name, ' ') }}'s Account</h1>
  </div>
</div>


<div class="max-w-7xl mx-auto px-6 lg:px-12 py-10 lg:py-14">
  <div class="grid lg:grid-cols-[220px_1fr] gap-8 lg:gap-12 items-start">

    {{-- ── Sidebar nav ── --}}
    <nav class="lg:sticky lg:top-28 space-y-1" aria-label="Account navigation">
      @foreach([
        ['orders',  'My Orders',   'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
        ['wishlist','My Wishlist', 'M4.318 6.318a4.5 4.5 0 016.364 0L12 7.636l1.318-1.318a4.5 4.5 0 116.364 6.364L12 20.364l-7.682-7.682a4.5 4.5 0 010-6.364z'],
        ['profile', 'Profile',     'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
        ['address', 'Addresses',   'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z'],
      ] as [$id, $label, $iconPath])
        <button type="button"
                class="acc-nav-btn w-full flex items-center gap-3 px-4 py-3 text-sm font-body text-maroon/70 hover:text-maroon hover:bg-gold/8 rounded-xl transition-colors group"
                data-tab="{{ $id }}">
          <svg class="w-4 h-4 flex-shrink-0 text-gold/60 group-hover:text-gold transition-colors" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $iconPath }}"/>
          </svg>
          {{ $label }}
        </button>
      @endforeach

      <div class="border-t border-gold/15 pt-2 mt-2">
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit"
                  class="w-full flex items-center gap-3 px-4 py-3 text-sm font-body text-maroon/50 hover:text-maroon hover:bg-blush/30 rounded-xl transition-colors">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
            Sign Out
          </button>
        </form>
      </div>
    </nav>

    {{-- ── Tab content ── --}}
    <div>

      {{-- ── ORDERS ── --}}
      <div id="tab-orders" class="acc-tab">
        <h2 class="font-display text-3xl text-maroon mb-6">My Orders</h2>

        @if(isset($orders) && $orders->count())
          <div class="divide-y divide-gold/15">
            @foreach($orders as $order)
              <div class="py-5 grid sm:grid-cols-[1fr_auto] gap-3 items-center">
                <div>
                  <div class="flex items-center gap-3">
                    <p class="font-body text-sm font-semibold text-maroon">Order #{{ $order->reference }}</p>
                    <a href="{{ route('order.confirmation', $order->id) }}" class="text-xs text-gold-dark hover:underline font-semibold">
                      View Details &rarr;
                    </a>
                    <a href="{{ route('account.orders.invoice', $order->id) }}" target="_blank" class="text-xs text-maroon/60 hover:text-maroon underline font-semibold">
                      Tax Invoice 🖨️
                    </a>
                  </div>
                  <p class="font-body text-xs text-maroon/50 mt-1">
                    {{ $order->created_at->format('d M Y') }}
                    · {{ $order->items->count() }} item{{ $order->items->count() !== 1 ? 's' : '' }}
                    · ₹{{ number_format($order->total, 0) }}
                  </p>
                  @if($order->isCancellable())
                    <button type="button" onclick="submitCancel({{ $order->id }})" class="text-[11px] font-semibold text-rose-600 hover:underline mt-1.5 inline-block">
                      Cancel This Order
                    </button>
                  @elseif($order->status === 'delivered')
                    <button type="button" onclick="submitReturn({{ $order->id }})" class="text-[11px] font-semibold text-amber-700 hover:underline mt-1.5 inline-block">
                      Request Return / Exchange
                    </button>
                  @endif
                </div>
                <span class="inline-flex items-center text-[10px] font-body font-semibold uppercase tracking-widest px-3 py-1
                  {{ match($order->status ?? 'pending') {
                       'delivered' => 'bg-green-50 text-green-700',
                       'shipped'   => 'bg-blue-50 text-blue-700',
                       'cancelled' => 'bg-red-50 text-red-600',
                       default     => 'bg-gold/10 text-gold-dark'
                     } }}">
                  {{ ucfirst($order->status ?? 'Pending') }}
                </span>
              </div>
            @endforeach
          </div>
        @else
          <div class="text-center py-16">
            <svg class="w-10 h-10 text-gold/30 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <p class="font-display text-2xl text-maroon mb-1">No orders yet</p>
            <p class="font-body text-sm text-maroon/50 mb-6">Your orders will appear here once you shop.</p>
            <a href="{{ route('search') }}" class="gold-btn py-3 px-8 text-xs font-body font-semibold tracking-widest uppercase inline-block">Start Shopping</a>
          </div>
        @endif
      </div>

      {{-- ── WISHLIST ── --}}
      <div id="tab-wishlist" class="acc-tab hidden">
        <h2 class="font-display text-3xl text-maroon mb-6">My Wishlist</h2>
        <div id="wishlistGrid" class="grid grid-cols-2 md:grid-cols-3 gap-4">
          <p class="col-span-full text-sm font-body text-maroon/40 py-10 text-center">Loading your wishlist…</p>
        </div>
      </div>

      {{-- ── PROFILE ── --}}
      <div id="tab-profile" class="acc-tab hidden">
        <h2 class="font-display text-3xl text-maroon mb-6">Profile</h2>

        @if(session('profile_success'))
          <div class="mb-6 text-xs font-body text-green-700 bg-green-50 border border-green-200 px-4 py-3">
            {{ session('profile_success') }}
          </div>
        @endif

        <form method="POST" action="{{ route('account.update') }}" class="space-y-4 max-w-md">
          @csrf
          @method('PUT')
          <div>
            <label class="block text-xs font-body font-semibold uppercase tracking-widest text-maroon/60 mb-1.5">Full Name</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                   class="w-full border border-gold/30 bg-transparent px-4 py-3 text-sm text-maroon placeholder-maroon/30 focus:border-gold focus:outline-none transition-colors font-body @error('name') border-red-400 @enderror">
            @error('name')<p class="text-xs text-red-500 mt-1 font-body">{{ $message }}</p>@enderror
          </div>
          <div>
            <label class="block text-xs font-body font-semibold uppercase tracking-widest text-maroon/60 mb-1.5">Email Address</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                   class="w-full border border-gold/30 bg-transparent px-4 py-3 text-sm text-maroon placeholder-maroon/30 focus:border-gold focus:outline-none transition-colors font-body @error('email') border-red-400 @enderror">
            @error('email')<p class="text-xs text-red-500 mt-1 font-body">{{ $message }}</p>@enderror
          </div>
          <div class="border-t border-gold/15 pt-4">
            <label class="block text-xs font-body font-semibold uppercase tracking-widest text-maroon/60 mb-1.5">New Password <span class="font-normal normal-case">(leave blank to keep current)</span></label>
            <input type="password" name="password" autocomplete="new-password"
                   class="w-full border border-gold/30 bg-transparent px-4 py-3 text-sm text-maroon placeholder-maroon/30 focus:border-gold focus:outline-none transition-colors font-body @error('password') border-red-400 @enderror">
            @error('password')<p class="text-xs text-red-500 mt-1 font-body">{{ $message }}</p>@enderror
          </div>
          <div>
            <label class="block text-xs font-body font-semibold uppercase tracking-widest text-maroon/60 mb-1.5">Confirm New Password</label>
            <input type="password" name="password_confirmation" autocomplete="new-password"
                   class="w-full border border-gold/30 bg-transparent px-4 py-3 text-sm text-maroon placeholder-maroon/30 focus:border-gold focus:outline-none transition-colors font-body">
          </div>
          <button type="submit" class="gold-btn py-3.5 px-8 text-xs font-body font-semibold tracking-widest uppercase mt-2">Save Changes</button>
        </form>
      </div>

      {{-- ── ADDRESSES ── --}}
      <div id="tab-address" class="acc-tab hidden">
        <h2 class="font-display text-3xl text-maroon mb-6">Saved Addresses</h2>

        @if(isset($addresses) && $addresses->isNotEmpty())
          <div class="grid sm:grid-cols-2 gap-4 mb-8">
            @foreach($addresses as $addr)
              <div class="p-5 rounded-2xl border {{ $addr->is_default ? 'border-gold bg-gold/5' : 'border-gold/20 bg-ivory' }} relative">
                <div class="flex items-center justify-between mb-2">
                  <span class="text-xs font-bold uppercase tracking-wider text-maroon">{{ $addr->type }}</span>
                  <div class="flex items-center gap-2">
                    @if($addr->is_default)
                      <span class="text-[10px] uppercase font-bold text-gold-dark bg-gold/15 px-2 py-0.5 rounded-full">Default</span>
                    @endif
                    <button type="button" onclick="removeAddress({{ $addr->id }})" class="text-rose-500 hover:text-rose-700 text-xs font-semibold" title="Delete">
                      &times; Delete
                    </button>
                  </div>
                </div>
                <p class="font-display text-base text-maroon">{{ $addr->name }}</p>
                <p class="font-body text-xs text-maroon/60 mt-0.5">{{ $addr->phone }}</p>
                <p class="font-body text-xs text-maroon/80 mt-2 leading-relaxed">
                  {{ $addr->address1 }}{{ $addr->address2 ? ', ' . $addr->address2 : '' }}<br>
                  {{ $addr->city }}, {{ $addr->state }} — {{ $addr->pincode }}
                </p>
              </div>
            @endforeach
          </div>
        @endif

        <div class="max-w-md p-6 rounded-2xl border border-gold/30 bg-gold/5 space-y-4">
          <h3 class="font-display text-xl text-maroon">Add New Address</h3>
          <form id="addAddressForm" onsubmit="event.preventDefault(); saveAddress();" class="space-y-4">
            @csrf
            <div>
              <label class="block text-xs font-body font-semibold uppercase tracking-widest text-maroon/60 mb-1.5">Full Name *</label>
              <input type="text" name="name" id="newAddrName" required value="{{ $user->name ?? '' }}" class="w-full border border-gold/30 bg-white px-4 py-2.5 text-sm text-maroon focus:border-gold outline-none font-body">
            </div>
            <div>
              <label class="block text-xs font-body font-semibold uppercase tracking-widest text-maroon/60 mb-1.5">Mobile Phone *</label>
              <input type="tel" name="phone" id="newAddrPhone" required pattern="[6-9][0-9]{9}" maxlength="10" placeholder="10 Digits" class="w-full border border-gold/30 bg-white px-4 py-2.5 text-sm text-maroon focus:border-gold outline-none font-body">
            </div>
            <div>
              <label class="block text-xs font-body font-semibold uppercase tracking-widest text-maroon/60 mb-1.5">Address Line 1 *</label>
              <input type="text" name="address1" id="newAddr1" required placeholder="Flat, Street, Area" class="w-full border border-gold/30 bg-white px-4 py-2.5 text-sm text-maroon focus:border-gold outline-none font-body">
            </div>
            <div>
              <label class="block text-xs font-body font-semibold uppercase tracking-widest text-maroon/60 mb-1.5">Address Line 2 (Optional)</label>
              <input type="text" name="address2" id="newAddr2" placeholder="Landmark" class="w-full border border-gold/30 bg-white px-4 py-2.5 text-sm text-maroon focus:border-gold outline-none font-body">
            </div>
            <div class="grid grid-cols-3 gap-3">
              <div>
                <label class="block text-xs font-body font-semibold uppercase tracking-widest text-maroon/60 mb-1.5">City *</label>
                <input type="text" name="city" id="newAddrCity" required class="w-full border border-gold/30 bg-white px-3 py-2.5 text-sm text-maroon focus:border-gold outline-none font-body">
              </div>
              <div>
                <label class="block text-xs font-body font-semibold uppercase tracking-widest text-maroon/60 mb-1.5">State *</label>
                <input type="text" name="state" id="newAddrState" required class="w-full border border-gold/30 bg-white px-3 py-2.5 text-sm text-maroon focus:border-gold outline-none font-body">
              </div>
              <div>
                <label class="block text-xs font-body font-semibold uppercase tracking-widest text-maroon/60 mb-1.5">PIN Code *</label>
                <input type="text" name="pincode" id="newAddrPin" required maxlength="6" pattern="[0-9]{6}" class="w-full border border-gold/30 bg-white px-3 py-2.5 text-sm text-maroon focus:border-gold outline-none font-body">
              </div>
            </div>
            <div class="flex items-center gap-2 pt-2">
              <input type="checkbox" name="is_default" id="newAddrDefault" value="1" class="accent-gold">
              <label for="newAddrDefault" class="text-xs font-body text-maroon/70 cursor-pointer">Set as default delivery address</label>
            </div>
            <button type="submit" id="saveAddrBtn" class="gold-btn py-3 px-6 text-xs font-body font-semibold tracking-widest uppercase mt-2">
              Save Address
            </button>
          </form>
        </div>
      </div>

    </div>{{-- /tab-content --}}
  </div>
</div>

</div>

@push('scripts')
<script>
async function submitCancel(orderId) {
  const reason = prompt('Please select or specify a reason for cancellation (e.g. Ordered by mistake, found better price, change of address):');
  if (!reason) return;
  try {
    const res = await fetch(`/orders/${orderId}/request`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
      body: JSON.stringify({ type: 'cancel', reason: reason })
    });
    const data = await res.json();
    alert(data.message || 'Cancellation processed.');
    window.location.reload();
  } catch {
    alert('Error submitting cancellation request.');
  }
}

async function submitReturn(orderId) {
  const reason = prompt('Please describe why you are requesting a return/exchange (e.g. Size fit issue, defective):');
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
  btn.textContent = 'Saving…';
  const payload = {
    name: document.getElementById('newAddrName').value,
    phone: document.getElementById('newAddrPhone').value,
    address1: document.getElementById('newAddr1').value,
    address2: document.getElementById('newAddr2').value,
    city: document.getElementById('newAddrCity').value,
    state: document.getElementById('newAddrState').value,
    pincode: document.getElementById('newAddrPin').value,
    is_default: document.getElementById('newAddrDefault').checked,
  };
  try {
    const res = await fetch("{{ route('account.addresses.store') }}", {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
      body: JSON.stringify(payload)
    });
    const data = await res.json();
    if (data.success) {
      alert('Address saved!');
      window.location.reload();
    } else {
      alert(data.message || 'Error saving address.');
      btn.disabled = false;
      btn.textContent = 'Save Address';
    }
  } catch {
    alert('Network error.');
    btn.disabled = false;
    btn.textContent = 'Save Address';
  }
}

async function removeAddress(id) {
  if (!confirm('Remove this address?')) return;
  try {
    const res = await fetch(`/account/addresses/${id}`, {
      method: 'DELETE',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    });
    const data = await res.json();
    if (data.success) window.location.reload();
  } catch {
    alert('Error deleting address.');
  }
}

(function () {

  /* ── Tab switching ── */
  const tabs    = document.querySelectorAll('.acc-tab');
  const navBtns = document.querySelectorAll('.acc-nav-btn');

  function showTab(id) {
    tabs.forEach(t => t.classList.toggle('hidden', t.id !== 'tab-' + id));
    navBtns.forEach(b => {
      const on = b.dataset.tab === id;
      b.classList.toggle('bg-gold/10',  on);
      b.classList.toggle('text-maroon', on);
      b.classList.toggle('font-semibold', on);
    });
    if (id === 'wishlist') loadWishlist();
    try { sessionStorage.setItem('saa_acc_tab', id); } catch {}
  }

  navBtns.forEach(btn => btn.addEventListener('click', () => showTab(btn.dataset.tab)));

  // Restore last tab
  const saved = (() => { try { return sessionStorage.getItem('saa_acc_tab'); } catch { return null; } })();
  showTab(saved || 'orders');

  /* ── Wishlist loader ── */
  function loadWishlist() {
    const grid = document.getElementById('wishlistGrid');
    if (!grid) return;
    let ids;
    try { ids = JSON.parse(localStorage.getItem('lc_wishlist') || '[]'); } catch { ids = []; }
    if (!ids.length) {
      grid.innerHTML = '<p class="col-span-full text-sm font-body text-maroon/40 py-10 text-center">Your wishlist is empty.</p>';
      return;
    }
    fetch("{{ route('wishlist') }}?ids=" + ids.join(','), {
      headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(r => r.ok ? r.json() : [])
    .then(products => {
      if (!products.length) {
        grid.innerHTML = '<p class="col-span-full text-sm font-body text-maroon/40 py-10 text-center">No matching items found.</p>';
        return;
      }
      grid.innerHTML = products.map(p => `
        <a href="${esc(p.url)}" class="group block">
          <div class="aspect-[3/4] overflow-hidden bg-blush/30">
            <img src="${esc(p.image)}" alt="${esc(p.name)}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
          </div>
          <p class="font-display text-base text-maroon mt-2 truncate">${esc(p.name)}</p>
          <p class="font-body text-xs text-gold-dark mt-0.5">₹${Number(p.price).toLocaleString('en-IN')}</p>
        </a>`).join('');
    })
    .catch(() => {
      grid.innerHTML = '<p class="col-span-full text-sm font-body text-maroon/40 py-10 text-center">Could not load wishlist.</p>';
    });
  }

  function esc(s) {
    return String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
  }

})();
</script>
@endpush

</x-storefront-layout>
