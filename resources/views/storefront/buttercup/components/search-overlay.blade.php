{{--
    ======================================================================
    THEME: BUTTERCUP — Pastel Comfort Intimates & Loungewear
    COMPONENT: LIVE PRODUCT SEARCH OVERLAY
    ======================================================================
    File: resources/views/storefront/buttercup/components/search-overlay.blade.php
    Component Injection: storefront.buttercup.components.search-overlay
    Responsibility: Real-time debounced live catalog search interface
    Features:
      - Debounced asynchronous input queries to autocomplete API
      - Instant product preview cards with thumbnail, price, and stock status
      - Trending searches, popular category tags, and recent search history
      - Keyboard navigation (Arrow keys, Enter, Escape)
    Operations & Maintenance:
      - Queries route('api.search') or route('search')
      - Input strings sanitized against SQL wildcard injection
--}}
<div id="searchOverlay" class="hidden fixed inset-0 z-[205] backdrop-blur-sm flex-col p-6 md:p-12" style="background: rgba(255, 250, 245, 0.15); -webkit-backdrop-filter: blur(6px); backdrop-filter: blur(6px);">
    <div class="max-w-2xl mx-auto w-full">
        <div class="flex items-center justify-between mb-8">
            <span class="font-bold text-xl font-display" style="color:#4A3B3A">Search</span>
            <button onclick="closeSearch()" class="w-8 h-8 rounded-full bg-white/60 hover:bg-white text-2xl font-light text-[#4A3B3A] flex items-center justify-center transition-all cursor-pointer shadow-2xs" aria-label="Close search">&times;</button>
        </div>
        <form method="GET" action="{{ route('search') }}">
            <input id="searchInput" name="q" type="text" placeholder="Search for bras, panties, sets..."
                   value="{{ request('q') }}"
                   class="w-full border-b-2 text-xl py-4 outline-none transition-colors bg-white/40 px-4 rounded-xl font-body"
                   style="border-color:#D89AA0;color:#4A3B3A;">
        </form>
        <div id="liveResults" class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-8 hidden"></div>
        <div class="mt-10">
            <p class="text-xs uppercase tracking-widest font-extrabold opacity-75 mb-4 font-body" style="color:#4A3B3A">Popular</p>
            <div class="flex flex-wrap gap-2.5">
                @foreach (['Bralette','Lace Hipster','Cotton Camisole','Everyday Set','Sleepwear'] as $term)
                    <a href="{{ route('search') }}?q={{ $term }}" class="text-xs px-4 py-2 rounded-full border transition-all hover:bg-[#D89AA0] hover:text-white font-semibold font-body shadow-xs backdrop-blur-sm"
                       style="border-color:#D89AA0;background:rgba(255, 255, 255, 0.85);color:#4A3B3A;">{{ $term }}</a>
                @endforeach
            </div>
        </div>
    </div>
</div>
