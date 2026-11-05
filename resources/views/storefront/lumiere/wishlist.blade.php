{{-- resources/views/storefront/lumiere/wishlist.blade.php
     Data from StorefrontController@wishlist: $wishlistProducts (from ?ids=, plus the user's saved list if one exists) --}}
<x-storefront-layout :theme="$theme" title="Wishlist">

{{-- Guests keep their wishlist in localStorage ('lc_wishlist') but this page is rendered by the server
     from ?ids=. Runs immediately (before the page paints): if there is no ?ids= yet, redirect once with
     the saved ids. After the redirect ?ids= is present, so it cannot loop. --}}
<script>
(function () {
    try {
        if (new URLSearchParams(location.search).has('ids')) return;
        const ids = JSON.parse(localStorage.getItem('lc_wishlist') || '[]').map(Number).filter(n => n > 0);
        if (ids.length) location.replace(location.pathname + '?ids=' + ids.join(','));
    } catch (e) { /* storage unavailable: just show the page */ }
})();
</script>

<style>
  .animate-fade { animation: pageFadeIn .5s ease both; }
</style>

<div class="max-w-7xl mx-auto px-6 md:px-12 py-10">

  <div class="mb-10 reveal">
    <h1 class="font-display italic text-3xl md:text-4xl text-gray-900 mb-2">Wishlist</h1>
    <p id="wishCount" class="text-sm text-gray-400">
      {{ $wishlistProducts->count() }} saved {{ \Illuminate\Support\Str::plural('piece', $wishlistProducts->count()) }}
    </p>
  </div>

  {{-- Grid (always in the DOM so removing the last item can swap to the empty state) --}}
  <div id="wishGrid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-x-5 gap-y-10 {{ $wishlistProducts->count() ? '' : 'hidden' }}">
    @foreach ($wishlistProducts as $product)
      {{-- The remove button is a sibling of the card (not inside it), so it never triggers the card's quick-view --}}
      <div class="relative" data-wish-id="{{ $product->id }}">
        <button type="button" data-wish-remove="{{ $product->id }}"
                class="absolute top-4 right-4 z-30 w-9 h-9 rounded-full bg-white/90 backdrop-blur-md shadow flex items-center justify-center hover:scale-110 transition-transform"
                aria-label="Remove {{ $product->name }} from wishlist">
          <i class="fa-solid fa-heart text-pink-600 text-sm"></i>
        </button>
        @include('storefront.lumiere.partials.lustre-card', ['product' => $product])
      </div>
    @endforeach
  </div>

  {{-- Empty state --}}
  <div id="wishEmpty" class="text-center py-24 {{ $wishlistProducts->count() ? 'hidden' : '' }}">
    <i class="fa-regular fa-heart text-5xl mx-auto mb-5 text-pink-200 block"></i>
    <p class="font-display italic text-2xl text-gray-300 mb-2">Your wishlist is empty</p>
    <p class="text-sm text-gray-400 mb-6">Tap the heart on any piece to save it here.</p>
    <a href="{{ route('home') }}" class="inline-block px-6 py-3 rounded-2xl text-xs font-bold uppercase tracking-widest bg-gray-900 text-white hover:bg-pink-600 transition-colors">Discover Pieces</a>
  </div>
</div>

{{-- Quick-view modal for the cards --}}
@include('storefront.lumiere.partials.quick-view')

@push('scripts')
<script>
function wishStored() {
    try { return JSON.parse(localStorage.getItem('lc_wishlist') || '[]').map(Number); } catch (e) { return []; }
}

// Header count, grid and empty state follow whatever is currently on screen.
function wishRefresh() {
    const n = document.querySelectorAll('[data-wish-id]').length;
    document.getElementById('wishCount').textContent = n + ' saved ' + (n === 1 ? 'piece' : 'pieces');
    document.getElementById('wishGrid').classList.toggle('hidden', n === 0);
    document.getElementById('wishEmpty').classList.toggle('hidden', n !== 0);
}

document.getElementById('wishGrid').addEventListener('click', e => {
    const btn = e.target.closest('[data-wish-remove]');
    if (!btn) return;
    const id = Number(btn.dataset.wishRemove);

    localStorage.setItem('lc_wishlist', JSON.stringify(wishStored().filter(x => x !== id)));
    btn.closest('[data-wish-id]').remove();

    // Keep the address bar in step with what is shown (without touching anyone else's saved list).
    const shown = (new URLSearchParams(location.search).get('ids') || '').split(',').map(Number).filter(n => n && n !== id);
    history.replaceState(null, '', shown.length ? location.pathname + '?ids=' + shown.join(',') : location.pathname);

    wishRefresh();
});
</script>
@endpush

</x-storefront-layout>
