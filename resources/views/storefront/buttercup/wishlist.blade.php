{{-- resources/views/storefront/buttercup/wishlist.blade.php --}}
<x-storefront-layout :theme="$theme" title="Wishlist">
<div class="max-w-7xl mx-auto px-6 lg:px-10 py-10">
  <h1 class="font-display text-3xl md:text-4xl mb-2 reveal text-[#4A3B3A]">Wishlist</h1>
  <p class="font-body text-sm opacity-50 mb-10 text-[#4A3B3A]">Items you've saved for later</p>

  @if ($wishlistProducts->count())
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
      @foreach ($wishlistProducts as $product)
        <div class="relative">
          <button onclick="rmWish({{ $product->id }}, this)" class="absolute top-3 right-3 z-10 w-7 h-7 rounded-full flex items-center justify-center shadow" style="background:#FFFAF5">
            <i class="fa-solid fa-heart text-xs" style="color:#D89AA0"></i>
          </button>
          @include('storefront.buttercup.partials.product-card', ['product' => $product])
        </div>
      @endforeach
    </div>
  @else
    <div class="text-center py-24 opacity-40 text-[#4A3B3A]">
      <i class="fa-regular fa-heart text-5xl mx-auto mb-4 block" style="color:#D89AA0"></i>
      <p class="font-display text-2xl mb-4">Your wishlist is empty</p>
      <a href="{{ route('home') }}" class="inline-block px-6 py-3 text-xs font-bold uppercase tracking-widest bg-[#D89AA0] text-[#FFFAF5] hover:bg-[#B97680] rounded-full transition-colors">Discover Products</a>
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
