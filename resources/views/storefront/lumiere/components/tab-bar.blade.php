{{-- resources/views/storefront/lumiere/components/tab-bar.blade.php --}}
<nav class="tab-bar lg:hidden fixed bottom-0 left-0 right-0 z-[55] grid grid-cols-5 border-t bg-white" style="border-color:#FBCFE8;padding-bottom:env(safe-area-inset-bottom);">
    <a href="{{ route('home') }}" class="tab-item flex flex-col items-center justify-center gap-1 py-3 transition-opacity" data-tab="home" style="color:#1f2937;opacity:{{ request()->routeIs('home') ? '1' : '0.4' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 11l9-7 9 7M5 10v10h14V10"/></svg>
        <span class="text-[9px] font-semibold">Home</span>
    </a>
    <button onclick="openCategorySheet()" class="tab-item flex flex-col items-center justify-center gap-1 py-3" style="color:#1f2937;opacity:0.4;" data-tab="categories">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="2"/><rect x="14" y="3" width="7" height="7" rx="2"/><rect x="3" y="14" width="7" height="7" rx="2"/><rect x="14" y="14" width="7" height="7" rx="2"/></svg>
        <span class="text-[9px] font-semibold">Categories</span>
    </button>
    <button onclick="openCartDrawer()" class="tab-item flex flex-col items-center justify-center gap-1 py-3" style="color:#1f2937;opacity:0.4;" data-tab="cart" aria-label="Cart">
        <div class="relative w-12 h-12 -mt-5 rounded-full flex items-center justify-center shadow-lg bg-pink-600">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
            <span class="cart-count-badge absolute -top-1 -right-1 text-[9px] font-bold w-4 h-4 rounded-full flex items-center justify-center bg-gray-900 text-white">0</span>
        </div>
    </button>
    <button onclick="openSearch()" class="tab-item flex flex-col items-center justify-center gap-1 py-3" style="color:#1f2937;opacity:0.4;" data-tab="search">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="M21 21l-4.3-4.3"/></svg>
        <span class="text-[9px] font-semibold">Search</span>
    </button>
    @auth
    <a href="{{ route('account') }}" class="tab-item flex flex-col items-center justify-center gap-1 py-3" style="color:#1f2937;opacity:{{ request()->routeIs('account') ? '1' : '0.4' }};" data-tab="account">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><circle cx="12" cy="8" r="3.2"/><path stroke-linecap="round" d="M5 20c1.2-3.6 4-5.4 7-5.4s5.8 1.8 7 5.4"/></svg>
        <span class="text-[9px] font-semibold">Account</span>
    </a>
    @else
    <button onclick="openLoginModal()" class="tab-item flex flex-col items-center justify-center gap-1 py-3" style="color:#1f2937;opacity:0.4;" data-tab="account">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><circle cx="12" cy="8" r="3.2"/><path stroke-linecap="round" d="M5 20c1.2-3.6 4-5.4 7-5.4s5.8 1.8 7 5.4"/></svg>
        <span class="text-[9px] font-semibold">Account</span>
    </button>
    @endauth
</nav>
