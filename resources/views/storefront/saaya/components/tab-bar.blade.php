{{-- resources/views/storefront/saaya/components/tab-bar.blade.php
     4-tab mobile bar (lg:hidden): Home, Categories, Cart (gold badge), Account.
     Active tab is determined by the current route name. --}}

@php
  $route = request()->route()?->getName() ?? '';
  $active = match(true) {
    str_starts_with($route, 'home')     => 'home',
    str_starts_with($route, 'category') => 'categories',
    str_starts_with($route, 'cart')     => 'cart',
    str_starts_with($route, 'account')
      || str_starts_with($route, 'wishlist') => 'account',
    default => '',
  };
@endphp

<nav class="tab-bar lg:hidden grid grid-cols-4" aria-label="Mobile navigation">

  {{-- Home --}}
  <a href="{{ route('home') }}"
     class="tab-item {{ $active === 'home' ? 'active' : '' }}">
    <span class="tab-dot"></span>
    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 11l9-7 9 7M5 10v10h14V10"/></svg>
    <span class="text-[10px] font-body">Home</span>
  </a>

  {{-- Categories — opens the bottom sheet --}}
  <button id="tabCategoriesBtn"
          class="tab-item {{ $active === 'categories' ? 'active' : '' }}"
          aria-label="Browse categories"
          aria-expanded="false">
    <span class="tab-dot"></span>
    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
    <span class="text-[10px] font-body">Shop</span>
  </button>

  {{-- Cart —opens drawer --}}
  <button id="tabCartBtn"
          class="tab-item {{ $active === 'cart' ? 'active' : '' }}"
          aria-label="Open cart">
    <span class="tab-dot"></span>
    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 7h15l-1.5 9h-12z"/><path stroke-linecap="round" d="M6 7L4 3H2"/><circle cx="9" cy="20" r="1"/><circle cx="17" cy="20" r="1"/></svg>
    <span id="tabCartBadge" class="tab-badge hidden">0</span>
    <span class="text-[10px] font-body">Cart</span>
  </button>

  {{-- Account --}}
  @auth
    <a href="{{ route('account') }}"
       class="tab-item {{ $active === 'account' ? 'active' : '' }}">
      <span class="tab-dot"></span>
      <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="12" cy="8" r="3.2"/><path stroke-linecap="round" d="M5 20c1.2-3.6 4-5.4 7-5.4s5.8 1.8 7 5.4"/></svg>
      <span class="text-[10px] font-body">Account</span>
    </a>
  @else
    <button id="tabLoginBtn"
            class="tab-item {{ $active === 'account' ? 'active' : '' }}"
            aria-label="Sign in">
      <span class="tab-dot"></span>
      <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="12" cy="8" r="3.2"/><path stroke-linecap="round" d="M5 20c1.2-3.6 4-5.4 7-5.4s5.8 1.8 7 5.4"/></svg>
      <span class="text-[10px] font-body">Account</span>
    </button>
  @endauth

</nav>

<script>
  // Cart tab → drawer
  document.getElementById('tabCartBtn')?.addEventListener('click', () => {
    const d = document.getElementById('cartDrawer');
    if (d) d.classList.remove('translate-x-full');
  });

  // Categories tab → bottom sheet
  document.getElementById('tabCategoriesBtn')?.addEventListener('click', () => {
    const o = document.getElementById('sheetOverlay');
    if (o) { o.classList.add('open'); document.getElementById('tabCategoriesBtn').setAttribute('aria-expanded','true'); }
  });

  // Login tab → modal
  document.getElementById('tabLoginBtn')?.addEventListener('click', () => {
    const m = document.getElementById('loginModal');
    if (m) m.classList.remove('hidden');
  });

  // Keep tab-bar cart badge in sync
  document.addEventListener('cartUpdated', () => {
    const badge = document.getElementById('tabCartBadge');
    if (!badge) return;
    const n = (typeof cart !== 'undefined' ? cart : []).reduce((s, i) => s + i.qty, 0);
    badge.textContent = n;
    badge.classList.toggle('hidden', n === 0);
  });
</script>
