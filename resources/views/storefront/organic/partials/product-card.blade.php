{{-- resources/views/storefront/organic/partials/product-card.blade.php
     Organic Market Product Card: Fresh badge, clean product imagery, organic color/variety swatches, size/pack pills, and instant add to bag. --}}
@php
    $cardData = $product->toCardArray();
@endphp

<article class="reveal bg-white rounded-3xl border border-stone-200/90 overflow-hidden shadow-xs hover:shadow-organic hover:-translate-y-1.5 transition-all duration-300 flex flex-col justify-between group product-card"
         data-product='@json($cardData)'
         data-swatch-active-class="border-organic-600 ring-2 ring-organic-300 scale-110"
         data-swatch-inactive-class="border-stone-200"
         data-pill-base-class="size-pill text-[10px] font-bold px-2.5 py-1 rounded-lg border border-stone-200 text-stone-600 hover:border-organic-500 hover:text-organic-700 transition"
         data-pill-active-class="bg-organic-500 text-white border-organic-500"
         data-add-to-bag-label="ADD TO BASKET"
         data-select-size-label="SELECT PACK / SIZE">

  {{-- Top Image & Badges Wrap --}}
  <div class="relative bg-stone-50/80 aspect-square overflow-hidden p-4 flex items-center justify-center">
    
    {{-- Badges: Discount or Organic Tag --}}
    <div class="absolute top-3 left-3 z-10 flex flex-col gap-1 items-start">
      @if($product->badge)
        <span class="bg-citrus text-white text-[10px] font-black uppercase px-2.5 py-0.5 rounded-full shadow-xs tracking-wider">
          {{ $product->badge }}
        </span>
      @else
        <span class="bg-organic-500 text-white text-[10px] font-bold uppercase px-2.5 py-0.5 rounded-full shadow-xs tracking-wider flex items-center gap-1">
          <svg class="w-3 h-3" viewBox="0 0 24 24"><use xlink:href="#organic-leaf"></use></svg>
          100% Organic
        </span>
      @endif

      @if($cardData['startingCompareAt'] && $cardData['startingCompareAt'] > $cardData['startingPrice'])
        @php
          $pct = round((($cardData['startingCompareAt'] - $cardData['startingPrice']) / $cardData['startingCompareAt']) * 100);
        @endphp
        <span class="bg-organic-900 text-organic-200 text-[9px] font-extrabold px-2 py-0.5 rounded-full">
          {{ $pct }}% OFF
        </span>
      @endif
    </div>

    {{-- Wishlist Action Button --}}
    <button type="button" 
            onclick="event.preventDefault(); window.toggleWishlist({{ $product->id }})" 
            class="absolute top-3 right-3 z-10 w-8 h-8 rounded-full bg-white/90 hover:bg-white text-stone-400 hover:text-citrus flex items-center justify-center shadow-xs transition group/btn" 
            title="Save to Wishlist"
            aria-label="Save {{ $product->name }} to Wishlist">
      <svg class="w-4 h-4 fill-current group-hover/btn:scale-110 transition-transform" viewBox="0 0 24 24"><use xlink:href="#organic-heart"></use></svg>
    </button>

    {{-- Product Image --}}
    <a href="{{ route('product.show', $product->slug) }}" class="card-img-wrap w-full h-full flex items-center justify-center">
      <img class="product-card-img card-img-zoom max-h-full max-w-full object-contain transition-transform duration-500 group-hover:scale-108"
           src="{{ $cardData['coverImage'] }}"
           alt="{{ $product->name }}"
           loading="lazy">
    </a>
  </div>

  {{-- Card Details --}}
  <div class="p-5 flex-1 flex flex-col justify-between">
    <div>
      {{-- Department / Category --}}
      <span class="text-[11px] font-bold uppercase tracking-wider text-organic-600 block mb-1">
        {{ $product->category->name ?? 'Fresh Produce' }}
      </span>

      {{-- Product Name --}}
      <h3 class="text-sm font-extrabold text-stone-900 leading-snug line-clamp-2 hover:text-organic-600 transition font-display">
        <a href="{{ route('product.show', $product->slug) }}">
          {{ $product->name }}
        </a>
      </h3>

      {{-- Rating Stars --}}
      <div class="flex items-center gap-1 mt-1.5 text-citrus-gold text-xs">
        @for($s = 0; $s < 5; $s++)
          <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><use xlink:href="#organic-star"></use></svg>
        @endfor
        <span class="text-[10px] text-stone-400 font-semibold ml-1">(4.9)</span>
      </div>
    </div>

    <div class="mt-4 pt-3 border-t border-stone-100">
      {{-- Price Container --}}
      <div class="product-card-price flex items-baseline gap-2">
        <span class="price-current text-lg font-black text-organic-900 font-display">
          ₹{{ number_format($cardData['startingPrice'], 2) }}
        </span>
        @if($cardData['startingCompareAt'])
          <span class="price-compare text-xs text-stone-400 line-through">
            ₹{{ number_format($cardData['startingCompareAt'], 2) }}
          </span>
        @endif
      </div>

      {{-- Color / Variety Swatches (if multiple) --}}
      @if(count($cardData['colors']) > 1)
        <div class="color-swatches flex items-center gap-1.5 mt-2.5">
          @foreach ($cardData['colors'] as $i => $color)
            <button type="button"
                    class="color-swatch w-4 h-4 rounded-full border transition-all {{ $i === 0 ? 'border-organic-600 ring-2 ring-organic-300 scale-110' : 'border-stone-200' }}"
                    style="background-color: {{ $color['hex'] ?? '#699E4B' }};"
                    title="{{ $color['name'] }}"
                    data-color-index="{{ $i }}"
                    aria-label="{{ $color['name'] }}"></button>
          @endforeach
        </div>
      @endif

      {{-- Dynamic Size/Pack Pills Container --}}
      <div class="size-pills flex items-center gap-1.5 mt-2 flex-wrap min-h-[28px]"></div>

      {{-- Add to Cart Action --}}
      <button type="button"
              class="add-to-cart-btn mt-3 w-full bg-organic-500 hover:bg-organic-600 text-white font-bold text-xs py-2.5 px-4 rounded-xl transition duration-200 shadow-sm hover:shadow-organic flex items-center justify-center gap-2 cursor-pointer">
        <svg class="w-4 h-4" viewBox="0 0 24 24"><use xlink:href="#organic-cart"></use></svg>
        <span>ADD TO BASKET</span>
      </button>

    </div>
  </div>

</article>

