{{--
    ======================================================================
    THEME: SAAYA — HEADER COMPONENT
    ======================================================================
    File: resources/views/storefront/saaya/components/header.blade.php
    Features:
      - Deep maroon luxury promo marquee with gold text
      - Ivory sticky bar with Cormorant Garamond serif branding
      - Gold-underline luxury mega-menu with hierarchical categories
      - Interactive cart count badge, search flyout, and login triggers
--}}

@php
    $saayaAnnouncement = \App\Models\Setting::get('announcement_bar', 'Free delivery over ₹1,499 | Use code WELCOME10 for 10% off');
@endphp
{{-- Promo marquee --}}
<div class="bg-maroon overflow-hidden whitespace-nowrap py-2 relative z-[100]">
  <div class="marquee-track text-gold text-[9px] uppercase font-body tracking-[0.3em] select-none">
    <span>{{ $saayaAnnouncement }} &nbsp;•&nbsp; Handcrafted Elegance &nbsp;•&nbsp; {{ $saayaAnnouncement }} &nbsp;•&nbsp; &nbsp;</span>
    <span>{{ $saayaAnnouncement }} &nbsp;•&nbsp; Handcrafted Elegance &nbsp;•&nbsp; {{ $saayaAnnouncement }} &nbsp;•&nbsp; &nbsp;</span>
  </div>
</div>

<header id="siteHeader" class="sticky top-0 z-50 bg-ivory/95 backdrop-blur border-b border-gold/30 transition-shadow">
  <div class="max-w-7xl mx-auto px-6 lg:px-10">
    <div class="flex items-center justify-between h-20">

      {{-- Logo --}}
      @php
          $siteLogo = $siteLogo ?? \App\Models\Setting::get('site_logo');
          $storeName = $storeName ?? \App\Models\Setting::get('store_name', 'Saaya');
      @endphp
      <a href="{{ route('home') }}" class="select-none flex items-center py-1">
        @if(!empty($siteLogo))
          <img src="{{ \App\Models\Setting::imageUrl($siteLogo) }}" alt="{{ $storeName }}" class="h-10 md:h-12 w-auto max-w-[200px] object-contain">
        @else
          <span class="font-display text-3xl tracking-wide text-maroon">
            Sa<span class="text-gold">a</span>ya
          </span>
        @endif
      </a>

      {{-- Mobile search icon --}}
      <div class="lg:hidden flex items-center gap-2">
        <button id="mobileSearchBtn" class="p-2 -mr-2 text-maroon" aria-label="Search">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="M21 21l-4.3-4.3"/></svg>
        </button>
      </div>

      {{-- Desktop nav --}}
      <nav class="hidden lg:flex items-center gap-10 font-body text-sm tracking-wide uppercase" aria-label="Main">

        <a href="{{ route('home') }}" class="underline-grow text-maroon {{ request()->routeIs('home') ? 'after:w-full' : '' }}">Home</a>

        {{-- Mega menu: categories --}}
        <div class="menu-wrap relative">
          <button class="underline-grow text-maroon uppercase flex items-center gap-1" aria-haspopup="true">
            Categories
            <svg class="w-3 h-3 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
          </button>
          <div class="menu-panel absolute left-1/2 -translate-x-1/2 top-full pt-4 w-[680px]">
            <div class="bg-ivory border border-gold/40 shadow-2xl shadow-maroon/10 grid grid-cols-3 gap-8 p-8">
              @foreach ($categories->take(6)->chunk(2) as $chunk)
                <div>
                  @foreach ($chunk as $cat)
                    <p class="font-display text-lg text-gold mb-3 italic">{{ $cat->name }}</p>
                    <ul class="space-y-2 text-maroon/80 text-xs normal-case mb-4">
                      @foreach ($cat->children ?? collect() as $sub)
                        <li><a href="{{ route('category.show', $sub->slug) }}" class="hover:text-gold transition-colors">{{ $sub->name }}</a></li>
                      @endforeach
                      @if (($cat->children ?? collect())->isEmpty())
                        <li><a href="{{ route('category.show', $cat->slug) }}" class="hover:text-gold transition-colors">View all {{ $cat->name }}</a></li>
                      @endif
                    </ul>
                  @endforeach
                </div>
              @endforeach
              <div class="col-span-3 pt-4 border-t border-gold/20 flex items-center justify-between">
                <span class="text-xs italic font-display text-maroon/60">Bridal Trousseau Edit — handpicked sets for the big day</span>
                <div class="flex items-center gap-2">
                  <a href="{{ route('featured.products') }}" class="gold-btn text-[10px] px-3 py-1.5 tracking-widest font-body">FEATURED</a>
                  <a href="{{ route('collections.sale') }}" class="bg-maroon text-gold border border-gold/40 text-[10px] px-3 py-1.5 tracking-widest font-body hover:bg-gold hover:text-maroon transition-colors">SALE</a>
                </div>
              </div>
            </div>
          </div>
        </div>

        <a href="{{ route('featured.products') }}" class="underline-grow text-maroon {{ request()->routeIs('featured.products') ? 'after:w-full' : '' }}">Featured</a>
        <a href="{{ route('new.arrivals') }}" class="underline-grow text-maroon {{ request()->routeIs('new.arrivals') ? 'after:w-full' : '' }}">New In</a>
        <a href="{{ route('collections.sale') }}" class="underline-grow text-red-700 font-semibold {{ request()->routeIs('collections.sale') ? 'after:w-full' : '' }}">Sale</a>
        <a href="{{ route('about') }}" class="underline-grow text-maroon {{ request()->routeIs('about') ? 'after:w-full' : '' }}">About Us</a>
        <a href="{{ route('contact') }}" class="underline-grow text-maroon">Contact</a>
      </nav>

      {{-- Desktop icons --}}
      <div class="hidden lg:flex items-center gap-5">
        <button id="desktopSearchBtn" aria-label="Search" class="text-maroon hover:text-gold transition-colors">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="M21 21l-4.3-4.3"/></svg>
        </button>
        <a href="{{ route('wishlist') }}" aria-label="Wishlist" class="text-maroon hover:text-gold transition-colors p-1">
          <i class="fa-regular fa-heart text-lg"></i>
        </a>
        @auth
          <a href="{{ route('account') }}" aria-label="My account" class="text-maroon hover:text-gold transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><circle cx="12" cy="8" r="3.2"/><path stroke-linecap="round" d="M5 20c1.2-3.6 4-5.4 7-5.4s5.8 1.8 7 5.4"/></svg>
          </a>
        @else
          <button id="desktopLoginBtn" aria-label="Sign in" class="text-maroon hover:text-gold transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><circle cx="12" cy="8" r="3.2"/><path stroke-linecap="round" d="M5 20c1.2-3.6 4-5.4 7-5.4s5.8 1.8 7 5.4"/></svg>
          </button>
        @endauth
        <button id="desktopCartBtn" aria-label="Cart" class="relative text-maroon hover:text-gold transition-colors">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 7h15l-1.5 9h-12z"/><path stroke-linecap="round" d="M6 7L4 3H2"/><circle cx="9" cy="20" r="1"/><circle cx="17" cy="20" r="1"/></svg>
          <span id="cartCount" class="absolute -top-2 -right-2 bg-gold text-maroon text-[9px] w-[18px] h-[18px] rounded-full items-center justify-center font-semibold hidden">0</span>
        </button>
      </div>

    </div>
  </div>
</header>

<script>
  // Sticky shadow
  document.addEventListener('scroll', () => {
    document.getElementById('siteHeader').classList.toggle('shadow-md', window.scrollY > 40);
  }, { passive: true });

  // Wire mobile + desktop search to the shared overlay
  ['mobileSearchBtn','desktopSearchBtn'].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.addEventListener('click', () => {
      const ov = document.getElementById('searchOverlay');
      if (ov) { ov.classList.remove('hidden'); document.getElementById('searchInput')?.focus(); }
    });
  });

  // Wire desktop cart to drawer
  const dcb = document.getElementById('desktopCartBtn');
  if (dcb) dcb.addEventListener('click', () => {
    const d = document.getElementById('cartDrawer');
    if (d) d.classList.remove('translate-x-full');
  });

  // Wire desktop login to modal
  const dlb = document.getElementById('desktopLoginBtn');
  if (dlb) dlb.addEventListener('click', () => {
    const m = document.getElementById('loginModal');
    if (m) m.classList.remove('hidden');
  });
</script>
