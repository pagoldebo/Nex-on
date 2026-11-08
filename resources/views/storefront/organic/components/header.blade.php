{{--
    ======================================================================
    THEME: VERDIA (ORGANIC) — HEADER COMPONENT
    ======================================================================
    File: resources/views/storefront/organic/components/header.blade.php
    Features:
      - Deep forest olive announcement bar with certification badges
      - Clean botanical brand logo with integrated live search input
      - Customer care helpline & quick account / cart actions
      - All Departments mega-dropdown with category tree navigation
--}}
<header class="w-full bg-white border-b border-stone-200 sticky top-0 z-40 shadow-xs" x-data="{ mobileMenuOpen: false, categoryOpen: false }">

  {{-- 1. Top Announcement Bar --}}
  <div class="bg-organic-900 text-organic-100 text-xs py-2 px-4 sm:px-6">
    <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-1 text-[11px] sm:text-xs">
      <div class="flex items-center gap-2">
        <span class="inline-flex items-center justify-center bg-organic-500 text-white rounded-full w-4 h-4 text-[9px] font-bold">✓</span>
        <span class="font-medium">{{ $organicTopNotice ?? \App\Models\Setting::get('organic_top_notice', '100% Certified Organic • Direct from Local Farmers & Sustainable Harvests') }}</span>
      </div>
      <div class="flex items-center gap-4 text-organic-200">
        <span>{{ $organicTopShippingNotice ?? \App\Models\Setting::get('organic_top_shipping_notice', 'Delivery to All Major Indian Pincodes') }}</span>
        <span class="hidden md:inline">•</span>
        @php
          $carePhone = $organicCustomerCare ?? \App\Models\Setting::get('organic_customer_care', \App\Models\Setting::supportPhone());
          $telHref = preg_replace('/[^0-9+]/', '', $carePhone);
        @endphp
        <a href="tel:{{ $telHref }}" class="hover:text-white transition flex items-center gap-1 font-semibold">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
          <span>{{ $carePhone }}</span>
        </a>
      </div>
    </div>
  </div>

  {{-- 2. Main Branding & Search Header --}}
  <div class="max-w-7xl mx-auto px-4 sm:px-6 py-3 sm:py-4">
    <div class="flex items-center justify-between gap-4 lg:gap-8">

      {{-- Mobile Menu Trigger --}}
      <button type="button" @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden p-2 text-stone-700 hover:text-organic-600 focus:outline-none" aria-label="Toggle navigation menu">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
      </button>

      {{-- Brand Logo --}}
      <a href="{{ route('home') }}" class="flex items-center gap-2.5 shrink-0 group">
        @if(!empty($siteLogo))
          <img src="{{ asset('storage/' . $siteLogo) }}" alt="{{ $storeName }}" class="h-10 md:h-12 w-auto max-w-[200px] object-contain">
        @else
          <div class="w-10 h-10 rounded-xl bg-organic-500 text-white flex items-center justify-center shadow-md group-hover:scale-105 transition-transform duration-300">
            <svg class="w-6 h-6" viewBox="0 0 24 24"><use xlink:href="#organic-leaf"></use></svg>
          </div>
          <div class="flex flex-col">
            <span class="text-2xl font-black tracking-tight text-organic-900 group-hover:text-organic-600 transition font-display">
              {{ $storeName }}<span class="text-organic-500">.</span>
            </span>
            <span class="text-[9px] uppercase tracking-widest text-organic-700 font-bold -mt-1">Fresh Farm Groceries</span>
          </div>
        @endif
      </a>

      {{-- Desktop Live Search --}}
      <div class="hidden md:flex flex-1 max-w-xl mx-2">
        <form action="{{ route('search') }}" method="GET" class="w-full relative flex items-center">
          <div class="relative w-full">
            <input type="text" 
                   name="q" 
                   placeholder="Search" 
                   class="w-full bg-stone-50 border border-stone-200 focus:border-organic-500 focus:bg-white text-xs sm:text-sm rounded-full py-2.5 pl-4 pr-12 text-stone-800 placeholder-stone-400 focus:outline-none focus:ring-2 focus:ring-organic-200 transition">
            <button type="submit" class="absolute right-1 top-1 bottom-1 w-9 h-9 rounded-full bg-organic-500 hover:bg-organic-600 text-white flex items-center justify-center transition shadow-xs" aria-label="Search">
              <svg class="w-4 h-4" viewBox="0 0 24 24"><use xlink:href="#organic-search"></use></svg>
            </button>
          </div>
        </form>
      </div>

      {{-- User Actions --}}
      <div class="flex items-center gap-2 sm:gap-4">

        {{-- Mobile Search Trigger --}}
        <button type="button" onclick="openSearchOverlay()" class="md:hidden p-2 text-stone-700 hover:text-organic-600" aria-label="Search products">
          <svg class="w-5 h-5" viewBox="0 0 24 24"><use xlink:href="#organic-search"></use></svg>
        </button>

        {{-- Account --}}
        @auth
          <a href="{{ route('account') }}" class="flex items-center gap-2 px-3 py-1.5 rounded-full hover:bg-stone-100 text-stone-700 transition" title="My Patron Account">
            <div class="w-8 h-8 rounded-full bg-organic-100 text-organic-700 flex items-center justify-center font-bold text-xs">
              {{ strtoupper(substr(Auth::user()->name ?? 'P', 0, 1)) }}
            </div>
            <div class="hidden xl:flex flex-col text-left">
              <span class="text-[10px] text-stone-400 uppercase font-semibold leading-none">Patron</span>
              <span class="text-xs font-bold text-stone-800 truncate max-w-[100px] leading-tight">{{ Auth::user()->name }}</span>
            </div>
          </a>
        @else
          <a href="{{ route('login') }}" class="flex items-center gap-1.5 px-3 py-1.5 rounded-full border border-stone-200 hover:border-organic-500 text-stone-700 hover:text-organic-600 text-xs font-semibold transition">
            <svg class="w-4 h-4 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            <span class="hidden sm:inline">Sign In</span>
          </a>
        @endauth

        {{-- Wishlist --}}
        <a href="{{ route('wishlist') }}" class="relative p-2 text-stone-700 hover:text-organic-600 hover:bg-organic-50 rounded-full transition" title="Saved Wishlist">
          <svg class="w-6 h-6" viewBox="0 0 24 24"><use xlink:href="#organic-heart"></use></svg>
          <span class="wishlist-count-badge absolute -top-0.5 -right-0.5 bg-citrus text-white text-[10px] font-bold rounded-full w-4 h-4 flex items-center justify-center shadow-xs">0</span>
        </a>

        {{-- Cart Drawer Trigger --}}
        <button type="button" onclick="openCartDrawer()" class="flex items-center gap-2.5 bg-organic-50 hover:bg-organic-100 text-organic-900 border border-organic-200/80 px-3.5 py-2 rounded-full transition shadow-2xs group" aria-label="View Shopping Cart">
          <div class="relative">
            <svg class="w-5 h-5 text-organic-600 group-hover:scale-110 transition-transform" viewBox="0 0 24 24"><use xlink:href="#organic-cart"></use></svg>
            <span class="cart-count absolute -top-2 -right-2 bg-organic-600 text-white text-[10px] font-bold rounded-full w-4 h-4 flex items-center justify-center shadow-xs">0</span>
          </div>
          <div class="hidden sm:flex flex-col text-left">
            <span class="text-[9px] uppercase tracking-wider text-organic-700 font-bold leading-none">Cart</span>
            <span id="cartTotal" class="text-xs font-black text-organic-900 leading-tight">₹0.00</span>
          </div>
        </button>

      </div>
    </div>
  </div>

  {{-- 3. Category Departments Bar (Desktop) --}}
  <div class="hidden lg:block bg-stone-50 border-t border-stone-200/80 text-xs font-semibold text-stone-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 flex items-center justify-between">

      {{-- Departments Dropdown Button --}}
      <div class="relative" x-data="{ open: false }" @click.outside="open = false">
        <button type="button" @click="open = !open" class="flex items-center gap-2.5 bg-organic-500 hover:bg-organic-600 text-white font-bold px-5 py-3 transition tracking-wide text-xs cursor-pointer">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h7"/></svg>
          <span>ALL DEPARTMENTS</span>
          <svg class="w-3.5 h-3.5 ml-1 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
        </button>

        {{-- Dropdown Mega Menu with Subcategories --}}
        <div x-show="open" 
             style="display: none;"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-1"
             class="absolute left-0 top-full w-[680px] max-w-[90vw] bg-white border border-stone-200 shadow-2xl rounded-b-2xl p-6 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 max-h-[75vh] overflow-y-auto z-50">
          @forelse($categories as $cat)
            <div class="space-y-2">
              <a href="{{ route('category.show', $cat->slug) }}" class="flex items-center gap-2 font-bold text-stone-900 hover:text-organic-600 text-xs transition group">
                <span class="w-2 h-2 rounded-full bg-organic-500 group-hover:scale-125 transition-transform"></span>
                <span>{{ $cat->name }}</span>
              </a>

              @if($cat->children && $cat->children->count() > 0)
                <ul class="space-y-1.5 pl-4 border-l-2 border-stone-100 text-[11px] text-stone-600">
                  @foreach($cat->children as $sub)
                    <li>
                      <a href="{{ route('category.show', $sub->slug) }}" class="hover:text-organic-600 transition flex items-center justify-between group/sub py-0.5">
                        <span class="truncate">{{ $sub->name }}</span>
                        <svg class="w-3 h-3 text-stone-300 group-hover/sub:text-organic-500 group-hover/sub:translate-x-0.5 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                      </a>
                    </li>
                  @endforeach
                </ul>
              @endif
              
              <a href="{{ route('category.show', $cat->slug) }}" class="inline-block text-[10px] font-bold text-organic-600 hover:text-organic-800 hover:underline pl-4 pt-0.5">
                View all {{ $cat->name }} →
              </a>
            </div>
          @empty
            <span class="block px-4 py-2 text-stone-400 text-xs">No categories found</span>
          @endforelse
        </div>
      </div>

      {{-- Navigation Links --}}
      <nav class="flex items-center gap-6 py-2.5 relative z-10">
        <a href="{{ route('home') }}" class="cursor-pointer hover:text-organic-600 transition {{ request()->routeIs('home') ? 'text-organic-600 font-bold' : '' }}">Home</a>
        <a href="{{ route('featured.products') }}" class="cursor-pointer hover:text-organic-600 transition {{ request()->routeIs('featured.products') ? 'text-organic-600 font-bold' : '' }}">Featured Products</a>
        <a href="{{ route('new.arrivals') }}" class="cursor-pointer hover:text-organic-600 transition {{ request()->routeIs('new.arrivals') ? 'text-organic-600 font-bold' : '' }}">New Arrivals</a>
        <a href="{{ route('sale.products') }}" class="cursor-pointer hover:text-citrus transition flex items-center gap-1 {{ request()->routeIs('sale.products') ? 'text-citrus font-bold' : '' }}">
          <span>Seasonal Deals</span>
          <span class="bg-citrus text-white text-[9px] font-black px-1.5 py-0.2 rounded-full uppercase">Hot</span>
        </a>
        <a href="{{ route('about') }}" class="cursor-pointer hover:text-organic-600 transition {{ request()->routeIs('about') ? 'text-organic-600 font-bold' : '' }}">About Farm</a>
        <a href="{{ route('contact') }}" class="cursor-pointer hover:text-organic-600 transition {{ request()->routeIs('contact') ? 'text-organic-600 font-bold' : '' }}">Contact Us</a>
        <a href="{{ route('faq') }}" class="cursor-pointer hover:text-organic-600 transition {{ request()->routeIs('faq') ? 'text-organic-600 font-bold' : '' }}">Help &amp; FAQs</a>
      </nav>

      {{-- Fresh Hotline --}}
      <div class="hidden xl:flex items-center gap-2 text-organic-700">
        <span class="text-[11px] font-bold">Free Shipping on ₹1,499+</span>
      </div>

    </div>
  </div>

  {{-- 4. Mobile Offcanvas Navigation Menu --}}
  <div x-show="mobileMenuOpen" 
       x-transition:enter="transition-opacity ease-linear duration-200"
       x-transition:enter-start="opacity-0"
       x-transition:enter-end="opacity-100"
       x-transition:leave="transition-opacity ease-linear duration-150"
       x-transition:leave-start="opacity-100"
       x-transition:leave-end="opacity-0"
       class="fixed inset-0 bg-stone-900/60 backdrop-blur-xs z-50 lg:hidden"
       @click="mobileMenuOpen = false">

    <div class="fixed inset-y-0 left-0 max-w-xs w-full bg-white shadow-2xl p-6 flex flex-col justify-between overflow-y-auto" @click.stop>
      <div>
        <div class="flex items-center justify-between pb-4 border-b border-stone-200">
          <div class="flex items-center gap-2">
            @if(!empty($siteLogo))
              <img src="{{ asset('storage/' . $siteLogo) }}" alt="{{ $storeName }}" class="h-8 w-auto max-w-[140px] object-contain">
            @else
              <div class="w-8 h-8 rounded-lg bg-organic-500 text-white flex items-center justify-center">
                <svg class="w-5 h-5" viewBox="0 0 24 24"><use xlink:href="#organic-leaf"></use></svg>
              </div>
              <span class="text-lg font-black text-organic-900 font-display">{{ $storeName }}<span class="text-organic-500">.</span></span>
            @endif
          </div>
          <button type="button" @click="mobileMenuOpen = false" class="p-1.5 text-stone-400 hover:text-stone-700" aria-label="Close menu">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>

        <nav class="mt-6 space-y-1 text-sm font-semibold text-stone-800">
          <a href="{{ route('home') }}" class="block px-3 py-2.5 rounded-lg hover:bg-organic-50 hover:text-organic-600 transition">Home</a>
          <a href="{{ route('featured.products') }}" class="block px-3 py-2.5 rounded-lg hover:bg-organic-50 hover:text-organic-600 transition">Featured Products</a>
          <a href="{{ route('new.arrivals') }}" class="block px-3 py-2.5 rounded-lg hover:bg-organic-50 hover:text-organic-600 transition">New Arrivals</a>
          <a href="{{ route('sale.products') }}" class="block px-3 py-2.5 rounded-lg hover:bg-organic-50 text-citrus transition">Special Deals</a>
          <a href="{{ route('about') }}" class="block px-3 py-2.5 rounded-lg hover:bg-organic-50 hover:text-organic-600 transition">About Us</a>
          <a href="{{ route('contact') }}" class="block px-3 py-2.5 rounded-lg hover:bg-organic-50 hover:text-organic-600 transition">Contact Us</a>
          <a href="{{ route('faq') }}" class="block px-3 py-2.5 rounded-lg hover:bg-organic-50 hover:text-organic-600 transition">FAQs &amp; Help</a>
        </nav>

        <div class="mt-6 pt-6 border-t border-stone-200">
          <h4 class="text-xs uppercase tracking-wider font-bold text-stone-400 mb-3 px-3">Departments &amp; Categories</h4>
          <div class="space-y-2">
            @foreach($categories as $cat)
              <div class="rounded-xl border border-stone-100 bg-stone-50/70 p-2.5">
                <a href="{{ route('category.show', $cat->slug) }}" class="flex items-center gap-2 text-xs font-bold text-stone-800 hover:text-organic-600">
                  <span class="w-1.5 h-1.5 rounded-full bg-organic-500"></span>
                  <span>{{ $cat->name }}</span>
                </a>
                @if($cat->children && $cat->children->count() > 0)
                  <div class="mt-2 pl-3 pt-2 border-t border-stone-200/60 space-y-1.5">
                    @foreach($cat->children as $sub)
                      <a href="{{ route('category.show', $sub->slug) }}" class="block text-[11px] text-stone-600 hover:text-organic-600 transition pl-1">
                        • {{ $sub->name }}
                      </a>
                    @endforeach
                  </div>
                @endif
              </div>
            @endforeach
          </div>
        </div>
      </div>

      <div class="mt-6 pt-4 border-t border-stone-200 text-xs text-stone-500">
        <p class="font-bold text-stone-800">Farm Fresh Helpline:</p>
        <a href="tel:{{ $telHref }}" class="text-organic-600 font-bold mt-0.5 block hover:underline">{{ $carePhone }}</a>
      </div>

    </div>
  </div>

</header>

