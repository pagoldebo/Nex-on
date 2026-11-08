{{--
    ======================================================================
    THEME: VERDIA — Botanical Living & Organic Produce Marketplace
    PAGE: SAVED WISHLIST & FAVORITES
    ======================================================================
    File: resources/views/storefront/organic/wishlist.blade.php
    Controller: App\Http\Controllers\StorefrontController@wishlist
    Route Context: GET /wishlist
    ViewModel Injections:
      - $theme: Active Template model ('organic')
    Responsibility: Customer saved wishlist collection and quick purchase staging
    Features:
      - Client & server synchronized saved product list
      - Live stock availability badge per wishlist item
      - One-click 'Move to Bag' and 'Move All to Bag' actions
      - Social sharing link to share curated wishlist with friends
    Operations & Maintenance:
      - Automatically syncs client localStorage favorites upon customer login
--}}
<x-storefront-layout :theme="$theme" title="Saved Produce &amp; Wishlist">

  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
    
    {{-- Breadcrumbs --}}
    <nav class="text-xs text-stone-500 font-medium flex items-center gap-2 mb-6">
      <a href="{{ route('home') }}" class="hover:text-organic-600 transition">Home</a>
      <span>/</span>
      <span class="text-stone-900 font-bold">Saved Produce</span>
    </nav>

    <div class="flex items-center justify-between pb-6 border-b border-stone-200 mb-8">
      <div>
        <h1 class="text-2xl sm:text-3xl font-black text-stone-900 font-display">Your Saved Organic Wishlist</h1>
        <p class="text-xs sm:text-sm text-stone-500 mt-1 font-light">Fresh seasonal items you're saving for your next delivery.</p>
      </div>
      <span class="text-xs font-bold text-stone-700 bg-organic-50 px-3.5 py-1.5 rounded-full border border-organic-200">
        {{ $wishlistProducts->count() }} Saved Items
      </span>
    </div>

    @if($wishlistProducts->count())
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @foreach($wishlistProducts as $product)
          <div class="relative group/wl">
            @include('storefront.organic.partials.product-card', ['product' => $product])
          </div>
        @endforeach
      </div>
    @else
      <div class="text-center py-24 bg-white rounded-3xl border border-stone-200 p-8 max-w-md mx-auto">
        <div class="w-16 h-16 rounded-full bg-organic-50 text-citrus flex items-center justify-center mx-auto mb-4">
          <svg class="w-8 h-8 fill-current" viewBox="0 0 24 24"><use xlink:href="#organic-heart"></use></svg>
        </div>
        <h3 class="text-lg font-black text-stone-900 font-display">Your Wishlist is Empty</h3>
        <p class="text-xs text-stone-500 mt-1 mb-6 font-light">Tap the heart icon on any produce card to save it for your next delivery.</p>
        <a href="{{ route('home') }}" class="btn-organic inline-block px-6 py-2.5 rounded-full text-xs font-bold">
          Explore Fresh Produce
        </a>
      </div>
    @endif

  </div>

</x-storefront-layout>

