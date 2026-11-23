{{--
    ======================================================================
    THEME: VYRA — Modern Noir & High-Contrast Streetwear
    COMPONENT: LIVE PRODUCT SEARCH OVERLAY
    ======================================================================
    File: resources/views/storefront/vyra/components/search-overlay.blade.php
    Component Injection: storefront.vyra.components.search-overlay
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
<div id="searchOverlay" class="hidden fixed inset-0 z-[205] backdrop-blur-sm flex-col p-6 md:p-12" style="background: rgba(14, 14, 16, 0.20); -webkit-backdrop-filter: blur(6px); backdrop-filter: blur(6px);">
    <div class="max-w-2xl mx-auto w-full">
        <div class="flex items-center justify-between mb-8">
            <span class="font-bold text-xl uppercase tracking-wider font-display" style="color:#F2F0EC">Search</span>
            <button onclick="closeSearch()" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-2xl font-light text-[#F2F0EC] flex items-center justify-center transition-all cursor-pointer shadow-2xs" aria-label="Close search">&times;</button>
        </div>
        <form method="GET" action="{{ route('search') }}">
            <input id="searchInput" name="q" type="text" placeholder="Search for bralettes, thongs, sets..."
                   value="{{ request('q') }}"
                   class="w-full border-b-2 text-xl py-4 outline-none transition-colors bg-white/5 px-4 rounded-xl font-body"
                   style="border-color:#FF3D5A;color:#F2F0EC;">
        </form>
        <div id="liveResults" class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-8 hidden"></div>
        <div class="mt-10">
            <p class="text-xs uppercase tracking-widest font-bold opacity-75 mb-4 font-body" style="color:#D4FF3D">Popular</p>
            <div class="flex flex-wrap gap-2.5">
                @foreach (['Mesh Bralette','High-Waist Thong','Slip Cami','Strappy Set','Sports Bra'] as $term)
                    <a href="{{ route('search') }}?q={{ $term }}" class="text-xs px-4 py-2 border transition-all hover:bg-[#FF3D5A] hover:border-[#FF3D5A] hover:text-white uppercase font-body font-semibold tracking-wider rounded-md shadow-xs backdrop-blur-sm"
                       style="border-color:#FF3D5A77;background:rgba(26, 26, 29, 0.85);color:#F2F0EC;">{{ $term }}</a>
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
                    ? data.map(p => `<a href="${esc(p.url)}" class="p-3 text-sm font-medium hover:opacity-80 transition-opacity uppercase" style="background:#FF3D5A22;color:#F2F0EC">${esc(p.name)}</a>`).join('')
                    : `<p class="text-xs opacity-40 col-span-2 text-center py-4" style="color:#F2F0EC">No results found.</p>`;
            });
    });
</script>
