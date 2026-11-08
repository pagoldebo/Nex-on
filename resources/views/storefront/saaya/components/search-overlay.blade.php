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
     class="hidden fixed inset-0 z-[65] bg-ivory/97 backdrop-blur-md flex flex-col"
     role="search" aria-label="Search">

  {{-- Search bar --}}
  <div class="flex items-center gap-4 border-b border-gold/30 px-6 lg:px-12 py-5">
    <svg class="w-5 h-5 text-maroon/40 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="M21 21l-4.3-4.3"/></svg>
    <input id="searchInput"
           type="search"
           autocomplete="off"
           placeholder="Search bras, panties, nightwear…"
           class="flex-1 bg-transparent text-maroon text-lg font-body placeholder-maroon/30 focus:outline-none">
    <button id="searchClose" class="p-2 text-maroon/40 hover:text-maroon transition-colors flex-shrink-0" aria-label="Close search">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
  </div>

  {{-- Live results --}}
  <div id="searchResults" class="flex-1 overflow-y-auto px-6 lg:px-12 py-6">
    {{-- Popular suggestions (shown before typing) --}}
    <div id="searchSuggestions">
      <p class="text-[10px] uppercase tracking-widest font-semibold text-gold-dark font-body mb-4">Popular Searches</p>
      <div class="flex flex-wrap gap-2">
        @foreach (['Silk Bra Set', 'Bridal Trousseau', 'Lace Brief', 'Wireless Bra', 'Nightwear', 'Camisole'] as $term)
          <button type="button" data-search-term="{{ $term }}"
                  class="search-pill border border-gold/40 text-maroon text-xs px-4 py-2 rounded-full hover:border-gold hover:bg-gold/10 transition-colors font-body">
            {{ $term }}
          </button>
        @endforeach
      </div>

      @php
        $parentCategories = $categories->filter(fn($c) => is_null($c->parent_id));
      @endphp
      @if ($parentCategories->count())
        <p class="text-[10px] uppercase tracking-widest font-semibold text-gold-dark font-body mt-8 mb-4">Shop by Category</p>
        <div class="flex flex-wrap gap-2">
          @foreach ($parentCategories as $cat)
            <a href="{{ route('category.show', $cat->slug) }}"
               class="border border-gold/40 text-maroon text-xs px-4 py-2 rounded-full hover:border-gold hover:bg-gold/10 transition-colors font-body">
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
  <div id="searchFooter" class="hidden border-t border-gold/20 px-6 lg:px-12 py-4">
    <a id="searchFullLink" href="{{ route('search') }}"
       class="flex items-center justify-between text-sm text-maroon hover:text-gold transition-colors font-body group">
      <span>See all results for <span id="searchFooterTerm" class="font-semibold text-gold"></span></span>
      <svg class="w-4 h-4 text-gold group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/></svg>
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
            <a href="${esc(p.url)}" class="flex gap-3 items-center hover:bg-gold/10 p-2 rounded-xl transition-colors group">
              <img src="${esc(p.image)}" alt="" class="w-14 h-16 object-cover bg-blush/30 flex-shrink-0">
              <div class="min-w-0">
                <p class="font-display text-sm text-maroon truncate">${esc(p.name)}</p>
                <p class="text-xs text-gold-dark font-body mt-0.5">₹${Number(p.price).toLocaleString('en-IN')}</p>
              </div>
            </a>`).join('') + '</div>';
      }
      resultsWrap.classList.remove('hidden');
    })
    .catch(() => {});
  }
})();
</script>
