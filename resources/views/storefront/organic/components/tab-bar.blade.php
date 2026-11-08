{{--
    ======================================================================
    THEME: VERDIA — Botanical Living & Organic Produce Marketplace
    COMPONENT: MOBILE NAVIGATION TAB BAR
    ======================================================================
    File: resources/views/storefront/organic/components/tab-bar.blade.php
    Component Injection: storefront.organic.components.tab-bar
    Responsibility: Fixed bottom dock navigation for mobile and tablet viewports
    Features:
      - Quick touch navigation (Home, Categories, Search, Wishlist, Cart)
      - Reactive cart & wishlist badge item counters
      - Auto-hides on large screens via Tailwind 'md:hidden'
    Operations & Maintenance:
      - Cart quantity binds to Alpine/sessionStorage state 'cartCount'
      - Category button triggers the slide-up category drawer
--}}
<nav class="lg:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-stone-200 shadow-lg px-2 py-1.5 flex items-center justify-around text-center text-[10px] font-semibold text-stone-600">
  
  {{-- Home --}}
  <a href="{{ route('home') }}" class="flex-1 py-1 flex flex-col items-center gap-0.5 {{ request()->routeIs('home') ? 'text-organic-600 font-bold' : 'hover:text-organic-600' }}">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
    <span>Home</span>
  </a>

  {{-- Categories --}}
  <button type="button" onclick="openCategorySheet()" class="flex-1 py-1 flex flex-col items-center gap-0.5 hover:text-organic-600">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
    <span>Catalog</span>
  </button>

  {{-- Wishlist --}}
  <a href="{{ route('wishlist') }}" class="flex-1 py-1 flex flex-col items-center gap-0.5 relative {{ request()->routeIs('wishlist') ? 'text-organic-600 font-bold' : 'hover:text-organic-600' }}">
    <div class="relative">
      <svg class="w-5 h-5" viewBox="0 0 24 24"><use xlink:href="#organic-heart"></use></svg>
      <span class="wishlist-count-badge absolute -top-1.5 -right-2 bg-citrus text-white text-[9px] font-bold rounded-full w-3.5 h-3.5 flex items-center justify-center">0</span>
    </div>
    <span>Saved</span>
  </a>

  {{-- Cart / Bag --}}
  <button type="button" onclick="openCartDrawer()" class="flex-1 py-1 flex flex-col items-center gap-0.5 relative hover:text-organic-600">
    <div class="relative">
      <svg class="w-5 h-5" viewBox="0 0 24 24"><use xlink:href="#organic-cart"></use></svg>
      <span class="cart-count absolute -top-1.5 -right-2 bg-organic-600 text-white text-[9px] font-bold rounded-full w-3.5 h-3.5 flex items-center justify-center">0</span>
    </div>
    <span>Basket</span>
  </button>

  {{-- Account --}}
  <a href="{{ route('account') }}" class="flex-1 py-1 flex flex-col items-center gap-0.5 {{ request()->routeIs('account') ? 'text-organic-600 font-bold' : 'hover:text-organic-600' }}">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
    <span>Account</span>
  </a>

</nav>

