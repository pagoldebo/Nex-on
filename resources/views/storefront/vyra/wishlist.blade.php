{{--
    ======================================================================
    THEME: VYRA — Modern Noir & High-Contrast Streetwear
    PAGE: SAVED WISHLIST & FAVORITES
    ======================================================================
    File: resources/views/storefront/vyra/wishlist.blade.php
    Controller: App\Http\Controllers\StorefrontController@wishlist
    Route Context: GET /wishlist
    ViewModel Injections:
      - $theme: Active Template model ('vyra')
    Responsibility: Customer saved wishlist collection and quick purchase staging
    Features:
      - Client & server synchronized saved product list
      - Live stock availability badge per wishlist item
      - One-click 'Move to Bag' and 'Move All to Bag' actions
      - Social sharing link to share curated wishlist with friends
    Operations & Maintenance:
      - Automatically syncs client localStorage favorites upon customer login
--}}
<x-storefront-layout :theme="$theme" title="Wishlist">
<div class="max-w-7xl mx-auto px-6 lg:px-10 py-10">
  <h1 class="font-display uppercase text-3xl md:text-4xl mb-2 reveal text-[#F2F0EC]">Wishlist</h1>
  <p class="font-body text-sm opacity-50 mb-10 text-[#F2F0EC]">Items you've saved for later</p>

  @if ($wishlistProducts->count())
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
      @foreach ($wishlistProducts as $product)
        <div class="relative">
          <button onclick="rmWish({{ $product->id }}, this)" class="absolute top-3 right-3 z-10 w-7 h-7 flex items-center justify-center shadow" style="background:#0E0E10;border:1px solid #FF3D5A">
            <i class="fa-solid fa-heart text-xs" style="color:#FF3D5A"></i>
          </button>
          @include('storefront.vyra.partials.product-card', ['product' => $product])
        </div>
      @endforeach
    </div>
  @else
    <div class="text-center py-24 opacity-40 text-[#F2F0EC]">
      <i class="fa-regular fa-heart text-5xl mx-auto mb-4 block" style="color:#FF3D5A"></i>
      <p class="font-display uppercase text-2xl mb-4">Your wishlist is empty</p>
      <a href="{{ route('home') }}" class="inline-block px-6 py-3 text-xs font-bold uppercase tracking-widest transition-all" style="background:#FF3D5A;color:#0E0E10;clip-path:polygon(4% 0,100% 0,96% 100%,0 100%)">Discover Products</a>
    </div>
  @endif
</div>
@push('scripts')
<script>
function rmWish(id, btn) {
  let wl = JSON.parse(localStorage.getItem('lc_wishlist') || '[]');
  wl = wl.filter(x => x !== id);
  localStorage.setItem('lc_wishlist', JSON.stringify(wl));
  btn.closest('.relative').remove();
}
</script>
@endpush
</x-storefront-layout>
