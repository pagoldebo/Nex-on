{{-- resources/views/storefront/saaya/wishlist.blade.php
     Saaya wishlist page.
     Guest flow: JS reads localStorage lc_wishlist, redirects once to ?ids=…
     Controller passes: $products (Collection of Product, toCardArray-ready),
                        $categories (layout) --}}

<x-storefront-layout>

{{-- Guest redirect — fires only when ?ids is absent and localStorage has items --}}
<script>
(function () {
  if (window.location.search.includes('ids=')) return; // already redirected
  var ids;
  try { ids = JSON.parse(localStorage.getItem('lc_wishlist') || '[]'); } catch { ids = []; }
  if (ids.length) {
    window.location.replace(window.location.pathname + '?ids=' + ids.join(','));
  }
})();
</script>

<div class="bg-ivory min-h-screen">

{{-- Maroon header --}}
<div class="bg-maroon py-12 lg:py-16 relative overflow-hidden">
  <div class="absolute inset-0 opacity-[.05] pointer-events-none"
       style="background-image:radial-gradient(circle,#C9A24B 1px,transparent 1px);background-size:24px 24px;"></div>
  <div class="relative max-w-7xl mx-auto px-6 lg:px-12 text-center">
    <p class="font-display italic text-gold text-lg mb-1">Your</p>
    <h1 class="font-display text-5xl text-ivory">Wishlist</h1>
    @if($products->count())
      <p class="font-body text-ivory/50 text-sm mt-3">{{ $products->count() }} {{ Str::plural('piece', $products->count()) }} saved</p>
    @endif
  </div>
</div>

<div class="max-w-7xl mx-auto px-6 lg:px-12 py-10 lg:py-14">

  @if($products->count())

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 lg:gap-6">
      @foreach($products as $product)
        @php $cardData = $product->toCardArray(); @endphp
        <div class="reveal relative group">
          @include('storefront.saaya.partials.product-card', ['product' => $product, 'cardData' => $cardData])

          {{-- Remove from wishlist --}}
          <button type="button"
                  class="wish-remove absolute top-3 right-3 z-10 w-8 h-8 bg-ivory/90 backdrop-blur-sm flex items-center justify-center text-maroon/40 hover:text-maroon transition-colors shadow-sm"
                  data-product-id="{{ $product->id }}"
                  aria-label="Remove from wishlist">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
              <path d="M4.318 6.318a4.5 4.5 0 016.364 0L12 7.636l1.318-1.318a4.5 4.5 0 116.364 6.364L12 20.364l-7.682-7.682a4.5 4.5 0 010-6.364z"/>
            </svg>
          </button>
        </div>
      @endforeach
    </div>

    <div class="text-center mt-12 reveal">
      <a href="{{ route('search') }}" class="gold-btn py-3.5 px-10 text-xs font-body font-semibold tracking-widest uppercase inline-block">
        Continue Shopping
      </a>
    </div>

  @else

    {{-- Empty state --}}
    <div class="text-center py-24">
      <svg class="w-12 h-12 text-gold/30 mx-auto mb-4" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 016.364 0L12 7.636l1.318-1.318a4.5 4.5 0 116.364 6.364L12 20.364l-7.682-7.682a4.5 4.5 0 010-6.364z"/>
      </svg>
      <p class="font-display text-3xl text-maroon mb-2">Nothing saved yet</p>
      <p class="font-body text-sm text-maroon/50 mb-8">Tap the heart on any piece to save it here.</p>
      <a href="{{ route('search') }}" class="gold-btn py-3.5 px-10 text-xs font-body font-semibold tracking-widest uppercase inline-block">Explore Collection</a>
    </div>

  @endif

</div>
</div>

@push('scripts')
<script>
(function () {
  // Remove buttons — update localStorage + remove card from DOM
  document.querySelectorAll('.wish-remove').forEach(btn => {
    btn.addEventListener('click', () => {
      const pid = Number(btn.dataset.productId);
      let ids;
      try { ids = JSON.parse(localStorage.getItem('lc_wishlist') || '[]'); } catch { ids = []; }
      ids = ids.filter(i => Number(i) !== pid);
      try { localStorage.setItem('lc_wishlist', JSON.stringify(ids)); } catch {}

      // Remove card from DOM
      const card = btn.closest('.reveal');
      card?.classList.add('opacity-0', 'scale-95', 'transition-all', 'duration-300');
      setTimeout(() => {
        card?.remove();
        // Update URL so a refresh shows correct state
        if (ids.length) {
          history.replaceState(null, '', window.location.pathname + '?ids=' + ids.join(','));
        } else {
          history.replaceState(null, '', window.location.pathname);
          location.reload(); // show empty state
        }
      }, 320);
    });
  });

  // Reveal
  if ('IntersectionObserver' in window) {
    const obs = new IntersectionObserver(entries => {
      entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('visible'); obs.unobserve(e.target); } });
    }, { threshold: 0.1 });
    document.querySelectorAll('.reveal').forEach(el => obs.observe(el));
  } else {
    document.querySelectorAll('.reveal').forEach(el => el.classList.add('visible'));
  }
})();
</script>
@endpush

</x-storefront-layout>
