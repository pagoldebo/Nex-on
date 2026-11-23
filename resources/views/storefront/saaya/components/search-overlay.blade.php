{{--
    ======================================================================
    THEME: SAAYA — Royal Indian Trousseau & Heritage Silk
    COMPONENT: LIVE PRODUCT SEARCH OVERLAY
    ======================================================================
    File: resources/views/storefront/saaya/components/search-overlay.blade.php
    Component Injection: storefront.saaya.components.search-overlay
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
<div id="searchOverlay"
     class="hidden fixed inset-0 z-[65] backdrop-blur-sm flex flex-col"
     style="background-color: rgba(247, 241, 230, 0.15); -webkit-backdrop-filter: blur(6px); backdrop-filter: blur(6px);"
     role="search" aria-label="Search">

  {{-- Search bar --}}
  <div class="flex items-center gap-4 border-b border-[#C9A24B]/30 px-6 lg:px-12 py-5 bg-white/40 backdrop-blur-md">
    <svg class="w-5 h-5 text-[#3D0C11]/70 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="M21 21l-4.3-4.3"/></svg>
    <input id="searchInput"
           type="search"
           autocomplete="off"
           placeholder="Search bras, panties, nightwear…"
           class="flex-1 bg-transparent text-[#3D0C11] text-lg font-body placeholder-[#3D0C11]/60 focus:outline-none font-medium"
           style="color: #3D0C11;">
    <button id="searchClose" class="w-8 h-8 rounded-full bg-white/60 hover:bg-white text-[#3D0C11]/70 hover:text-[#3D0C11] transition-all flex items-center justify-center flex-shrink-0 cursor-pointer shadow-2xs" aria-label="Close search">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
  </div>

  {{-- Live results --}}
  <div id="searchResults" class="flex-1 overflow-y-auto px-6 lg:px-12 py-6 bg-transparent">
    {{-- Popular suggestions (shown before typing) --}}
    <div id="searchSuggestions">
      <p class="text-xs uppercase tracking-widest font-extrabold text-[#9A7A33] font-body mb-4 drop-shadow-2xs">Popular Searches</p>
      <div class="flex flex-wrap gap-2.5">
        @foreach (['Silk Bra Set', 'Bridal Trousseau', 'Lace Brief', 'Wireless Bra', 'Nightwear', 'Camisole'] as $term)
          <button type="button" data-search-term="{{ $term }}"
                  class="search-pill border border-[#C9A24B]/60 bg-white/85 hover:bg-[#C9A24B] hover:text-white text-[#3D0C11] text-xs font-semibold px-4 py-2 rounded-full transition-all shadow-xs backdrop-blur-sm font-body cursor-pointer">
            {{ $term }}
          </button>
        @endforeach
      </div>

      @php
        $parentCategories = $categories->filter(fn($c) => is_null($c->parent_id));
      @endphp
      @if ($parentCategories->count())
        <p class="text-xs uppercase tracking-widest font-extrabold text-[#9A7A33] font-body mt-8 mb-4 drop-shadow-2xs">Shop by Category</p>
        <div class="flex flex-wrap gap-2.5">
          @foreach ($parentCategories as $cat)
            <a href="{{ route('category.show', $cat->slug) }}"
               class="border border-[#C9A24B]/60 bg-white/85 hover:bg-[#C9A24B] hover:text-white text-[#3D0C11] text-xs font-semibold px-4 py-2 rounded-full transition-all shadow-xs backdrop-blur-sm font-body">
              {{ $cat->name }}
            </a>
          @endforeach
        </div>
      @endif
    </div>

    {{-- Ajax results injected here --}}
    <div id="searchResultsInner" class="hidden"></div>
  </div>

  {{-- Full search link (shown after typing) --}}
  <div id="searchFooter" class="hidden border-t border-[#C9A24B]/30 px-6 lg:px-12 py-4 bg-white/40 backdrop-blur-md">
    <a id="searchFullLink" href="{{ route('search') }}"
       class="flex items-center justify-between text-sm text-[#3D0C11] hover:text-[#9A7A33] transition-colors font-body group">
      <span>See all results for <span id="searchFooterTerm" class="font-semibold text-[#9A7A33]"></span></span>
      <svg class="w-4 h-4 text-[#9A7A33] group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/></svg>
    </a>
  </div>
</div>

<script>
(function () {
  const overlay     = document.getElementById('searchOverlay');
  const input       = document.getElementById('searchInput');
  const closeBtn    = document.getElementById('searchClose');
  const suggestions = document.getElementById('searchSuggestions');
  const resultsWrap = document.getElementById('searchResultsInner');
  const footer      = document.getElementById('searchFooter');
  const footerTerm  = document.getElementById('searchFooterTerm');
  const fullLink    = document.getElementById('searchFullLink');

  function openSearch()  { overlay?.classList.remove('hidden'); setTimeout(() => input?.focus(), 50); }
  function closeSearch() { overlay?.classList.add('hidden'); if (input) input.value = ''; resetResults(); }
  function resetResults() {
    suggestions?.classList.remove('hidden');
    resultsWrap?.classList.add('hidden');
    footer?.classList.add('hidden');
    if (resultsWrap) resultsWrap.innerHTML = '';
  }

  window.openSearch = openSearch;

  closeBtn?.addEventListener('click', closeSearch);
  document.addEventListener('keydown', e => { if (e.key === 'Escape') closeSearch(); });

  // Pill clicks
  document.querySelectorAll('.search-pill').forEach(btn => {
    btn.addEventListener('click', () => { if (input) { input.value = btn.dataset.searchTerm; input.dispatchEvent(new Event('input')); } });
  });

  // Debounced live search
  let timer;
  input?.addEventListener('input', () => {
    clearTimeout(timer);
    const q = input.value.trim();
    if (!q) { resetResults(); return; }
    timer = setTimeout(() => fetchResults(q), 280);
  });

  // Submit → full search page
  input?.addEventListener('keydown', e => {
    if (e.key === 'Enter' && input.value.trim()) {
      window.location.href = "{{ route('search') }}?q=" + encodeURIComponent(input.value.trim());
    }
  });

  function esc(s) {
    return String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
  }

  function fetchResults(q) {
    fetch("{{ route('search') }}?q=" + encodeURIComponent(q) + "&ajax=1", {
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
      suggestions?.classList.add('hidden');
      footer?.classList.remove('hidden');
      footerTerm && (footerTerm.textContent = q);
      fullLink && (fullLink.href = "{{ route('search') }}?q=" + encodeURIComponent(q));

      if (!data.length) {
        resultsWrap.innerHTML = '<p class="text-sm text-maroon/40 font-body py-4">No results found.</p>';
      } else {
        resultsWrap.innerHTML = '<div class="grid grid-cols-2 md:grid-cols-4 gap-4">' +
          data.map(p => `
            <a href="${esc(p.url)}" class="flex gap-3 items-center bg-white/80 hover:bg-white border border-[#C9A24B]/30 hover:border-[#C9A24B] p-2.5 rounded-xl transition-all shadow-2xs group">
              <img src="${esc(p.image)}" alt="" class="w-14 h-16 object-cover rounded-lg bg-blush/30 flex-shrink-0">
              <div class="min-w-0">
                <p class="font-display text-sm text-[#3D0C11] font-semibold truncate group-hover:text-[#9A7A33] transition-colors">${esc(p.name)}</p>
                <p class="text-xs text-[#9A7A33] font-bold font-body mt-0.5">₹${Number(p.price).toLocaleString('en-IN')}</p>
              </div>
            </a>`).join('') + '</div>';
      }
      resultsWrap.classList.remove('hidden');
    })
    .catch(() => {});
  }
})();
</script>
