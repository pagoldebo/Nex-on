{{--
    ======================================================================
    THEME: VERDIA — Botanical Living & Organic Produce Marketplace
    PAGE: CUSTOMER ACCOUNT & ORDERS DASHBOARD
    ======================================================================
    File: resources/views/storefront/organic/account.blade.php
    Controller: App\Http\Controllers\StorefrontController@account
    Route Context: GET /account (Auth Middleware Guard: customer)
    ViewModel Injections:
      - $theme: Active Template model ('organic')
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
<x-storefront-layout :theme="$theme" title="My Patron Account">

  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
    
    {{-- Header Banner --}}
    <div class="mb-8 pb-6 border-b border-stone-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <span class="text-xs font-bold uppercase tracking-wider text-organic-600 block mb-1">Patron Portal</span>
        <h1 class="text-2xl sm:text-3xl font-black text-stone-900 font-display">Welcome, {{ $user->name }}</h1>
        <p class="text-xs sm:text-sm text-stone-500 mt-0.5 font-light">{{ $user->email }} • Supporting local organic agriculture</p>
      </div>

      <div class="flex items-center gap-3">
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold border border-stone-300 hover:border-stone-400 bg-white text-stone-700 hover:text-stone-900 transition flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
            <span>Sign Out</span>
          </button>
        </form>
      </div>
    </div>

    @if(session('profile_success'))
      <div class="mb-6 p-4 rounded-2xl bg-organic-50 border border-organic-200 text-xs font-bold text-organic-800">
        ✓ {{ session('profile_success') }}
      </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start" x-data="{ tab: 'orders' }">
      
      {{-- Sidebar Nav --}}
      <nav class="lg:col-span-3 bg-white rounded-3xl border border-stone-200 p-4 shadow-xs space-y-1">
        <button type="button" 
                @click="tab = 'orders'" 
                :class="tab === 'orders' ? 'bg-organic-500 text-white font-extrabold shadow-xs' : 'text-stone-700 hover:bg-stone-50 font-semibold'"
                class="w-full flex items-center gap-3 px-4 py-3 rounded-2xl text-xs transition text-left">
          <span>📦</span>
          <span>Order History</span>
        </button>

        <button type="button" 
                @click="tab = 'addresses'" 
                :class="tab === 'addresses' ? 'bg-organic-500 text-white font-extrabold shadow-xs' : 'text-stone-700 hover:bg-stone-50 font-semibold'"
                class="w-full flex items-center gap-3 px-4 py-3 rounded-2xl text-xs transition text-left">
          <span>📍</span>
          <span>Saved Addresses</span>
        </button>

        <button type="button" 
                @click="tab = 'profile'" 
                :class="tab === 'profile' ? 'bg-organic-500 text-white font-extrabold shadow-xs' : 'text-stone-700 hover:bg-stone-50 font-semibold'"
                class="w-full flex items-center gap-3 px-4 py-3 rounded-2xl text-xs transition text-left">
          <span>👤</span>
          <span>Profile Details</span>
        </button>

        <a href="{{ route('wishlist') }}" class="w-full flex items-center gap-3 px-4 py-3 rounded-2xl text-xs text-stone-700 hover:bg-stone-50 font-semibold transition text-left">
          <span>💖</span>
          <span>Saved Wishlist</span>
        </a>

        <a href="{{ route('cart') }}" class="w-full flex items-center gap-3 px-4 py-3 rounded-2xl text-xs text-stone-700 hover:bg-stone-50 font-semibold transition text-left">
          <span>🛍️</span>
          <span>Produce Basket</span>
        </a>
      </nav>

      {{-- Tab Content --}}
      <div class="lg:col-span-9 space-y-6">
        
        {{-- 1. ORDERS TAB --}}
        <div x-show="tab === 'orders'" class="space-y-4">
          <div class="flex items-center justify-between">
            <h2 class="text-xl font-black text-stone-900 font-display">Recent Orders &amp; Dispatches</h2>
            <span class="text-xs text-stone-400 font-bold">{{ $orders->count() }} Orders</span>
          </div>

          @forelse($orders as $order)
            <div class="bg-white rounded-3xl border border-stone-200 p-6 shadow-xs space-y-4">
              <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-stone-100 gap-2">
                <div>
                  <div class="flex items-center gap-2">
                    <span class="text-base font-extrabold text-stone-900 font-display">#{{ $order->reference ?? $order->id }}</span>
                    <span class="text-[10px] font-black uppercase px-2.5 py-0.5 rounded-full {{ $order->status === 'delivered' ? 'bg-emerald-100 text-emerald-800' : 'bg-organic-100 text-organic-800' }}">
                      {{ ucfirst($order->status) }}
                    </span>
                  </div>
                  <p class="text-xs text-stone-400 mt-0.5">Placed on {{ $order->created_at->format('M d, Y • h:i A') }}</p>
                </div>
                <div class="text-left sm:text-right">
                  <span class="text-lg font-black text-organic-900 font-display">₹{{ number_format($order->total, 2) }}</span>
                  <span class="text-[10px] text-stone-400 block font-semibold uppercase">{{ $order->payment_method }}</span>
                </div>
              </div>

              {{-- Line Items --}}
              <div class="divide-y divide-stone-100 text-xs">
                @foreach($order->items as $item)
                  <div class="py-2.5 flex items-center justify-between gap-4">
                    <div class="min-w-0">
                      <p class="font-bold text-stone-800 truncate">{{ $item->product_name }}</p>
                      <p class="text-[11px] text-stone-500">{{ $item->color_name }} • {{ $item->size }} × {{ $item->qty }}</p>
                    </div>
                    <span class="font-extrabold text-stone-900 shrink-0">₹{{ number_format($item->line_total, 2) }}</span>
                  </div>
                @endforeach
              </div>

              {{-- Order Actions: Tracking & Invoice --}}
              <div class="pt-3 border-t border-stone-100 flex flex-wrap items-center justify-between gap-3 text-xs">
                <div class="text-stone-500">
                  @if($order->tracking_number)
                    <span>AWB Tracking: <strong class="text-stone-800 font-mono">{{ $order->tracking_number }}</strong> ({{ $order->courier_name ?? 'Cold-Chain Delivery' }})</span>
                  @else
                    <span>Status: <strong class="text-organic-700">Packing in Central Organic Depot</strong></span>
                  @endif
                </div>
                <div class="flex items-center gap-3">
                  <a href="{{ route('account.orders.invoice', $order->id) }}" target="_blank" class="px-3 py-1.5 rounded-xl border border-stone-200 hover:border-organic-500 text-stone-700 hover:text-organic-700 font-bold transition flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Download Invoice</span>
                  </a>
                </div>
              </div>
            </div>
          @empty
            <div class="bg-white rounded-3xl border border-stone-200 p-12 text-center text-stone-400">
              <p class="text-sm font-semibold text-stone-600">You haven't placed any orders yet.</p>
              <a href="{{ route('home') }}" class="btn-organic inline-block mt-4 px-6 py-2.5 rounded-full text-xs font-bold">
                Start First Order
              </a>
            </div>
          @endforelse
        </div>

        {{-- 2. ADDRESSES TAB --}}
        <div x-show="tab === 'addresses'" class="space-y-4">
          <div class="flex items-center justify-between">
            <h2 class="text-xl font-black text-stone-900 font-display">Saved Delivery Addresses</h2>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse($addresses as $addr)
              <div class="bg-white rounded-3xl border {{ $addr->is_default ? 'border-organic-500 ring-2 ring-organic-200/60' : 'border-stone-200' }} p-6 shadow-xs flex flex-col justify-between">
                <div>
                  <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-black uppercase tracking-wider text-stone-900">{{ $addr->name }}</span>
                    @if($addr->is_default)
                      <span class="text-[10px] font-bold bg-organic-100 text-organic-700 px-2 py-0.5 rounded-full">Default</span>
                    @endif
                  </div>
                  <p class="text-xs text-stone-600 leading-relaxed">
                    {{ $addr->address1 }}<br>
                    @if($addr->address2) {{ $addr->address2 }}<br> @endif
                    {{ $addr->city }}, {{ $addr->state }} — {{ $addr->pincode }}
                  </p>
                  <p class="text-xs text-stone-500 mt-2"><strong>Phone:</strong> {{ $addr->phone }}</p>
                </div>
              </div>
            @empty
              <div class="col-span-full bg-white rounded-3xl border border-stone-200 p-8 text-center text-stone-400 text-xs">
                No addresses saved yet. You can save your address during checkout!
              </div>
            @endforelse
          </div>
        </div>

        {{-- 3. PROFILE TAB --}}
        <div x-show="tab === 'profile'" class="bg-white rounded-3xl border border-stone-200 p-6 sm:p-8 shadow-xs max-w-xl">
          <h2 class="text-xl font-black text-stone-900 font-display mb-6">Profile Information</h2>
          <form method="POST" action="/account" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
              <label for="profName" class="block text-xs font-bold text-stone-700 uppercase mb-1">Full Name</label>
              <input type="text" id="profName" name="name" required value="{{ $user->name }}" class="w-full bg-stone-50 border border-stone-200 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-stone-800 focus:outline-none focus:border-organic-500">
            </div>
            <div>
              <label for="profEmail" class="block text-xs font-bold text-stone-700 uppercase mb-1">Email Address</label>
              <input type="email" id="profEmail" name="email" required value="{{ $user->email }}" class="w-full bg-stone-50 border border-stone-200 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-stone-800 focus:outline-none focus:border-organic-500">
            </div>
            <div>
              <label for="profPhone" class="block text-xs font-bold text-stone-700 uppercase mb-1">Mobile Phone</label>
              <input type="tel" id="profPhone" name="phone" value="{{ $user->phone ?? '' }}" class="w-full bg-stone-50 border border-stone-200 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-stone-800 focus:outline-none focus:border-organic-500">
            </div>
            <div class="pt-2">
              <button type="submit" class="btn-organic px-6 py-2.5 rounded-xl text-xs font-bold shadow-xs">
                Update Profile
              </button>
            </div>
          </form>
        </div>

      </div>

    </div>

  </div>

</x-storefront-layout>

