{{-- resources/views/storefront/organic/components/search-overlay.blade.php
     Accessible full-screen search overlay for quick product lookup. --}}
<div id="searchOverlay" class="hidden fixed inset-0 z-50 transition-opacity duration-200 opacity-0 bg-stone-900/70 backdrop-blur-sm items-start justify-center pt-16 sm:pt-24 px-4">
  
  <div id="searchOverlayCard" class="w-full max-w-2xl bg-white rounded-3xl shadow-2xl p-6 sm:p-8 transform -translate-y-4 transition-transform duration-200 pointer-events-auto">
    
    <div class="flex items-center justify-between pb-4 border-b border-stone-100">
      <div class="flex items-center gap-2">
        <svg class="w-5 h-5 text-organic-500" viewBox="0 0 24 24"><use xlink:href="#organic-search"></use></svg>
        <h3 class="text-base font-extrabold text-stone-900 font-display">Search Organic Produce</h3>
      </div>
      <button type="button" onclick="closeSearchOverlay()" class="p-1.5 text-stone-400 hover:text-stone-700 rounded-full" aria-label="Close search overlay">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>

    <form action="{{ route('search') }}" method="GET" class="mt-4">
      <div class="relative">
        <input type="text" 
               id="overlaySearchInput"
               name="q" 
               placeholder="Search" 
               class="w-full bg-stone-50 border-2 border-stone-200 focus:border-organic-500 focus:bg-white text-sm sm:text-base rounded-2xl py-3.5 pl-5 pr-12 text-stone-800 placeholder-stone-400 focus:outline-none transition">
        <button type="submit" class="absolute right-2 top-2 bottom-2 px-4 rounded-xl bg-organic-500 hover:bg-organic-600 text-white font-bold text-xs flex items-center justify-center transition shadow-xs">
          Search
        </button>
      </div>
    </form>

    {{-- Popular organic searches --}}
    <div class="mt-6 pt-4 border-t border-stone-100">
      <span class="text-[11px] font-bold text-stone-400 uppercase tracking-wider block mb-2">Popular Searches:</span>
      <div class="flex flex-wrap gap-2">
        <a href="{{ route('search', ['q' => 'Organic Spinach']) }}" class="px-3 py-1.5 rounded-full bg-organic-50 hover:bg-organic-100 text-organic-700 text-xs font-semibold transition">🥬 Organic Spinach</a>
        <a href="{{ route('search', ['q' => 'Cold Pressed Mustard Oil']) }}" class="px-3 py-1.5 rounded-full bg-organic-50 hover:bg-organic-100 text-organic-700 text-xs font-semibold transition">🌻 Cold Pressed Oil</a>
        <a href="{{ route('search', ['q' => 'Alphonso Mangoes']) }}" class="px-3 py-1.5 rounded-full bg-organic-50 hover:bg-organic-100 text-organic-700 text-xs font-semibold transition">🥭 Alphonso Mangoes</a>
        <a href="{{ route('search', ['q' => 'Wild Forest Honey']) }}" class="px-3 py-1.5 rounded-full bg-organic-50 hover:bg-organic-100 text-organic-700 text-xs font-semibold transition">🍯 Raw Forest Honey</a>
        <a href="{{ route('search', ['q' => 'A2 Cow Ghee']) }}" class="px-3 py-1.5 rounded-full bg-organic-50 hover:bg-organic-100 text-organic-700 text-xs font-semibold transition">🧈 A2 Desi Cow Ghee</a>
      </div>
    </div>

  </div>
</div>

<script>
  function openSearchOverlay() {
    const overlay = document.getElementById('searchOverlay');
    const card = document.getElementById('searchOverlayCard');
    const input = document.getElementById('overlaySearchInput');
    if (!overlay || !card) return;
    overlay.classList.remove('hidden');
    overlay.classList.add('flex');
    requestAnimationFrame(() => {
      overlay.classList.remove('opacity-0');
      overlay.classList.add('opacity-100');
      card.classList.remove('-translate-y-4');
      setTimeout(() => { if (input) input.focus(); }, 100);
    });
  }

  function closeSearchOverlay() {
    const overlay = document.getElementById('searchOverlay');
    const card = document.getElementById('searchOverlayCard');
    if (!overlay || !card) return;
    card.classList.add('-translate-y-4');
    overlay.classList.remove('opacity-100');
    overlay.classList.add('opacity-0');
    setTimeout(() => {
      overlay.classList.remove('flex');
      overlay.classList.add('hidden');
    }, 200);
  }

  // Alias for global storefront search triggers
  window.openSearch = openSearchOverlay;
  window.closeSearch = closeSearchOverlay;

  document.getElementById('searchOverlay')?.addEventListener('click', function(e) {
    if (e.target === this) closeSearchOverlay();
  });
</script>
