{{-- resources/views/storefront/buttercup/components/tab-bar.blade.php --}}
<nav class="tab-bar lg:hidden fixed bottom-0 left-0 right-0 z-[55] grid grid-cols-5 border-t" style="background:#FFFAF5;border-color:#D89AA033;padding-bottom:env(safe-area-inset-bottom);">
    <a href="{{ route('home') }}" class="tab-item flex flex-col items-center justify-center gap-1 py-3 transition-opacity" data-tab="home" style="color:#4A3B3A;opacity:{{ request()->routeIs('home') ? '1' : '0.45' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 11l9-7 9 7M5 10v10h14V10"/></svg>
        <span class="text-[9px] font-medium">Home</span>
    </a>
    <button onclick="openCategorySheet()" class="tab-item flex flex-col items-center justify-center gap-1 py-3" style="color:#4A3B3A;opacity:0.45;" data-tab="categories">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
        <span class="text-[9px] font-medium">Categories</span>
    </button>
    <button onclick="openCartDrawer()" class="tab-item flex flex-col items-center justify-center gap-1 py-3" style="color:#4A3B3A;opacity:0.45;" data-tab="cart" aria-label="Cart">
        <div class="relative w-12 h-12 -mt-5 rounded-full flex items-center justify-center shadow-lg" style="background:#D89AA0">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 7h15l-1.5 9h-12z"/><path stroke-linecap="round" d="M6 7L4 3H2"/><circle cx="9" cy="20" r="1"/><circle cx="17" cy="20" r="1"/></svg>
            <span class="cart-count-badge absolute -top-1 -right-1 text-[9px] font-bold w-4 h-4 rounded-full flex items-center justify-center" style="background:#4A3B3A;color:#FFFAF5">0</span>
        </div>
    </button>
    <button onclick="openSearch()" class="tab-item flex flex-col items-center justify-center gap-1 py-3" style="color:#4A3B3A;opacity:0.45;" data-tab="search">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="M21 21l-4.3-4.3"/></svg>
        <span class="text-[9px] font-medium">Search</span>
    </button>
    @auth
    <a href="{{ route('account') }}" class="tab-item flex flex-col items-center justify-center gap-1 py-3" style="color:#4A3B3A;opacity:{{ request()->routeIs('account') ? '1' : '0.45' }};" data-tab="account">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="12" cy="8" r="3.2"/><path stroke-linecap="round" d="M5 20c1.2-3.6 4-5.4 7-5.4s5.8 1.8 7 5.4"/></svg>
        <span class="text-[9px] font-medium">Account</span>
    </a>
    @else
    <button onclick="openLoginModal()" class="tab-item flex flex-col items-center justify-center gap-1 py-3" style="color:#4A3B3A;opacity:0.45;" data-tab="account">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="12" cy="8" r="3.2"/><path stroke-linecap="round" d="M5 20c1.2-3.6 4-5.4 7-5.4s5.8 1.8 7 5.4"/></svg>
        <span class="text-[9px] font-medium">Account</span>
    </button>
    @endauth
</nav>
