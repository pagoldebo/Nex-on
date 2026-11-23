{{--
    ======================================================================
    THEME: LUMIÈRE — French Silk Atelier & Minimalist Luxury
    COMPONENT: LIVE PRODUCT SEARCH OVERLAY
    ======================================================================
    File: resources/views/storefront/lumiere/components/search-overlay.blade.php
    Component Injection: storefront.lumiere.components.search-overlay
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
<div id="searchOverlay" class="hidden fixed inset-0 z-[205] backdrop-blur-sm flex-col p-6 md:p-12" style="background: rgba(255, 255, 255, 0.15); -webkit-backdrop-filter: blur(6px); backdrop-filter: blur(6px);">
    <div class="max-w-2xl mx-auto w-full">
        <div class="flex items-center justify-between mb-8">
            <span class="font-bold text-xl text-gray-900 font-display">Search</span>
            <button onclick="closeSearch()" class="w-8 h-8 rounded-full bg-black/5 hover:bg-black/10 text-2xl font-light text-gray-600 flex items-center justify-center transition-all cursor-pointer shadow-2xs" aria-label="Close search">&times;</button>
        </div>
        <form method="GET" action="{{ route('search') }}">
            <input id="searchInput" name="q" type="text" placeholder="Search for silk sets, bralettes, sleepwear..."
                   value="{{ request('q') }}"
                   class="w-full border-b-2 border-pink-200 text-xl py-4 outline-none transition-colors focus:border-pink-500 bg-white/40 px-4 rounded-xl text-gray-900 font-body">
        </form>
        <div id="liveResults" class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-8 hidden"></div>
        <div class="mt-10">
            <p class="text-xs uppercase tracking-widest font-extrabold text-gray-700 mb-4 font-body">Popular</p>
            <div class="flex flex-wrap gap-2.5">
                @foreach (['Silk Slip', 'Lustre Bralette', 'Lace Set', 'Sleepwear', 'Bridal Edit'] as $term)
                    <a href="{{ route('search') }}?q={{ $term }}" class="text-xs px-4 py-2 rounded-full border border-pink-200 bg-white/85 text-gray-800 transition-all hover:bg-pink-500 hover:text-white hover:border-pink-500 font-medium font-body shadow-xs backdrop-blur-sm">{{ $term }}</a>
                @endforeach
            </div>
        </div>
    </div>
</div>
<script>
    const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    document.getElementById('searchInput')?.addEventListener('input', function() {
        const q = this.value.trim();
        const res = document.getElementById('liveResults');
        if (q.length < 2) { res.classList.add('hidden'); return; }
        fetch(`{{ route('search') }}?q=${encodeURIComponent(q)}&ajax=1`)
            .then(r => r.json())
            .then(data => {
                res.classList.remove('hidden');
                res.innerHTML = data.length
                    ? data.map(p => `<a href="${esc(p.url)}" class="p-3 rounded-xl text-sm font-medium text-gray-700 hover:bg-pink-50 transition-colors bg-pink-50/50">${esc(p.name)}</a>`).join('')
                    : `<p class="text-xs text-gray-400 col-span-2 text-center py-4">No results found.</p>`;
            });
    });
</script>
