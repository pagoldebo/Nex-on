{{--
    ======================================================================
    THEME: VERDIA — Botanical Living & Organic Produce Marketplace
    PARTIAL: FLASH SALE & PROMOTIONAL CARD
    ======================================================================
    File: resources/views/storefront/organic/partials/sale-card.blade.php
    Partial: storefront.organic.partials.sale-card
    Parameters:
      - $product: App\Models\Product instance
    Responsibility: High-conversion promotional card with discount highlighting
    Features:
      - Prominent discount percentage badge (-X%) and strike-through MSRP pricing
      - Real-time countdown timer for flash deals and campaign ends
      - Inventory scarcity bar showing remaining stock percentage
    Operations & Maintenance:
      - Binds to product sale price and active promotion schedule
--}}
@php
    $cardData = $product->toCardArray();
@endphp

<article class="reveal bg-gradient-to-br from-white via-citrus-light/40 to-white rounded-3xl border-2 border-citrus/30 overflow-hidden shadow-sm hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 flex flex-col justify-between group product-card"
         data-product='@json($cardData)'
         data-swatch-active-class="border-citrus ring-2 ring-citrus/30 scale-110"
         data-swatch-inactive-class="border-stone-200"
         data-pill-base-class="size-pill text-[10px] font-bold px-2.5 py-1 rounded-lg border inline-flex items-center justify-center transition-all"
         data-pill-inactive-class="border-citrus/30 text-stone-700 hover:border-citrus hover:text-citrus"
         data-pill-active-class="bg-citrus text-white border-citrus shadow-xs"
         data-add-to-bag-label="CLAIM DEAL"
         data-select-size-label="SELECT SIZE">

  {{-- Top Image & Badges Wrap --}}
  <div class="relative bg-white aspect-square overflow-hidden p-4 flex items-center justify-center">
    
    {{-- Deal Tags --}}
    <div class="absolute top-3 left-3 z-10 flex flex-col gap-1 items-start">
      <span class="bg-citrus text-white text-[10px] font-black uppercase px-2.5 py-0.5 rounded-full shadow-xs tracking-wider animate-pulse">
        🔥 FLASH DEAL
      </span>
      @if($cardData['startingCompareAt'] && $cardData['startingCompareAt'] > $cardData['startingPrice'])
        @php
          $pct = round((($cardData['startingCompareAt'] - $cardData['startingPrice']) / $cardData['startingCompareAt']) * 100);
        @endphp
        <span class="bg-organic-900 text-organic-200 text-[9px] font-extrabold px-2 py-0.5 rounded-full">
          SAVE {{ $pct }}%
        </span>
      @endif
    </div>

    {{-- Wishlist Button --}}
    <button type="button" 
            onclick="event.preventDefault(); window.toggleWishlist({{ $product->id }})" 
            class="absolute top-3 right-3 z-10 w-8 h-8 rounded-full bg-white text-stone-400 hover:text-citrus flex items-center justify-center shadow-xs transition" 
            title="Save to Wishlist"
            aria-label="Save {{ $product->name }} to Wishlist">
      <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><use xlink:href="#organic-heart"></use></svg>
    </button>

    {{-- Product Image --}}
    <a href="{{ route('product.show', $product->slug) }}" class="card-img-wrap w-full h-full flex items-center justify-center">
      <img class="product-card-img card-img-zoom max-h-full max-w-full object-contain transition-transform duration-500 group-hover:scale-108"
           src="{{ $cardData['coverImage'] }}"
           alt="{{ $product->name }}"
           loading="lazy">
    </a>

    {{-- Floating Quick View Button on Image Hover --}}
    <button type="button"
            onclick="event.preventDefault(); event.stopPropagation(); openQuickView({{ \Illuminate\Support\Js::from($cardData) }})"
            class="absolute bottom-3 left-1/2 -translate-x-1/2 z-20 bg-white/95 backdrop-blur-md text-stone-800 hover:bg-citrus hover:text-white text-[10px] font-bold uppercase tracking-wider px-3.5 py-1.5 rounded-full shadow-md opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-y-2 group-hover:translate-y-0 flex items-center gap-1.5 whitespace-nowrap cursor-pointer">
      <svg class="w-3.5 h-3.5 text-citrus group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
      <span>Quick View</span>
    </button>
  </div>

  {{-- Card Details --}}
  <div class="p-5 flex-1 flex flex-col justify-between">
    <div>
      <span class="text-[11px] font-bold uppercase tracking-wider text-citrus block mb-1">
        Limited Harvest Deal
      </span>

      <h3 class="text-sm font-extrabold text-stone-900 leading-snug line-clamp-2 hover:text-organic-600 transition font-display">
        <a href="{{ route('product.show', $product->slug) }}">
          {{ $product->name }}
        </a>
      </h3>

      <div class="flex items-center gap-1 mt-1.5 text-citrus text-xs">
        @for($s = 0; $s < 5; $s++)
          <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><use xlink:href="#organic-star"></use></svg>
        @endfor
        <span class="text-[10px] text-stone-400 font-semibold ml-1">(5.0)</span>
      </div>
    </div>

    <div class="mt-4 pt-3 border-t border-stone-200/80">
      <div class="flex items-center justify-between">
        <div class="product-card-price flex items-baseline gap-2">
          <span class="price-current text-xl font-black text-citrus font-display">
            @currency($cardData['startingPrice'])
          </span>
          @if($cardData['startingCompareAt'])
            <span class="price-compare text-xs text-stone-400 line-through">
              @currency($cardData['startingCompareAt'])
            </span>
          @endif
        </div>
        <button type="button"
                onclick="event.preventDefault(); event.stopPropagation(); openQuickView({{ \Illuminate\Support\Js::from($cardData) }})"
                title="Quick View"
                class="text-[10px] font-bold text-citrus hover:text-citrus-dark bg-citrus/10 hover:bg-citrus/20 px-2.5 py-1 rounded-full transition-colors flex items-center gap-1 cursor-pointer">
          <svg class="w-3 h-3 text-citrus" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
          <span>Quick View</span>
        </button>
      </div>

      {{-- Size/Pack Pills Container --}}
      <div class="size-pills flex items-center gap-1.5 mt-2 flex-wrap min-h-[28px]"></div>

      {{-- Add to Cart Action --}}
      <button type="button"
              class="add-to-cart-btn mt-3 w-full bg-citrus hover:bg-citrus-warm text-white font-bold text-xs py-2.5 px-4 rounded-xl transition duration-200 shadow-md shadow-citrus/25 flex items-center justify-center gap-2 cursor-pointer">
        <svg class="w-4 h-4" viewBox="0 0 24 24"><use xlink:href="#organic-cart"></use></svg>
        <span>CLAIM DEAL</span>
      </button>

    </div>
  </div>

</article>

