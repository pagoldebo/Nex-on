{{--
    ======================================================================
    THEME: VYRA — Modern Noir & High-Contrast Streetwear
    PARTIAL: CATALOG PRODUCT CARD ITEM
    ======================================================================
    File: resources/views/storefront/vyra/partials/product-card.blade.php
    Partial: storefront.vyra.partials.product-card
    Parameters:
      - $product: App\Models\Product instance
    Responsibility: Modular grid item card for catalog, category, and search results
    Features:
      - Image hover transition and cover color presentation
      - Promotional badge overlays (Sale, Best Seller, New Harvest / Drop)
      - Customer rating star summary and review counter
      - One-click instant Add to Bag and Wishlist toggle triggers
    Operations & Maintenance:
      - Reusable across all catalog layouts within vyra
--}}
@php
    $cardData = $product->toCardArray();
@endphp

<article class="reveal tilt-card bg-ink-light product-card"
         data-product='@json($cardData)'
         data-swatch-active-class="border-lime scale-110"
         data-swatch-inactive-class="border-off/20"
         data-pill-base-class="size-pill text-[10px] w-7 h-7 border inline-flex items-center justify-center font-mono font-semibold transition-all"
         data-pill-inactive-class="border-off/20 text-off/70 hover:border-lime hover:text-lime"
         data-pill-active-class="bg-lime text-ink border-lime shadow-xs"
         data-add-to-bag-label="ADD TO BAG"
         data-select-size-label="SELECT A SIZE">

    <div class="card-img-wrap block relative aspect-[3/4] bg-coral/10 overflow-hidden group cursor-pointer">
        <a href="{{ route('product.show', $product->slug) }}" class="block w-full h-full">
            @if($product->badge)
                <span class="absolute top-3 left-3 z-10 bg-lime text-ink text-[10px] font-mono font-bold px-2 py-1 uppercase shadow-xs">
                    {{ strtoupper($product->badge) }}
                </span>
            @endif

            <img class="product-card-img card-img-zoom w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                 src="{{ $cardData['coverImage'] }}"
                 alt="{{ $product->name }}">
        </a>

        {{-- Quick View Floating Button on Hover --}}
        <button type="button"
                onclick="event.preventDefault(); event.stopPropagation(); openQuickView({{ \Illuminate\Support\Js::from($cardData) }})"
                class="absolute bottom-3 left-1/2 -translate-x-1/2 z-20 bg-ink-light/95 backdrop-blur-md text-off hover:bg-lime hover:text-ink border border-lime/30 text-[10px] font-mono font-bold uppercase tracking-wider px-3.5 py-1.5 rounded shadow-md opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-y-2 group-hover:translate-y-0 flex items-center gap-1.5 whitespace-nowrap cursor-pointer">
            <svg class="w-3.5 h-3.5 text-lime group-hover:text-ink transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            <span>Quick View</span>
        </button>
    </div>

    <div class="p-4">
        <p class="text-[11px] font-bold text-lime uppercase tracking-wide">{{ $product->category->name }}</p>
        <h3 class="font-body font-semibold text-off mt-1">
            <a href="{{ route('product.show', $product->slug) }}" class="hover:text-lime transition-colors">
                {{ $product->name }}
            </a>
        </h3>

        <div class="flex items-center justify-between mt-2">
            <p class="product-card-price text-off font-bold">
                <span class="price-current">₹{{ number_format($cardData['startingPrice'], 0) }}</span>
                @if($cardData['startingCompareAt'])
                    <span class="price-compare text-off/30 line-through text-sm ml-1">₹{{ number_format($cardData['startingCompareAt'], 0) }}</span>
                @endif
            </p>
            <button type="button"
                    onclick="event.preventDefault(); event.stopPropagation(); openQuickView({{ \Illuminate\Support\Js::from($cardData) }})"
                    title="Quick View"
                    class="text-[10px] font-mono font-bold text-lime hover:text-ink hover:bg-lime border border-lime/40 px-2 py-0.5 rounded transition-colors flex items-center gap-1 cursor-pointer">
                <svg class="w-3 h-3 text-lime hover:text-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                <span>Quick View</span>
            </button>
        </div>

        @if(count($cardData['colors']))
            <div class="color-swatches flex gap-1.5 mt-3">
                @foreach ($cardData['colors'] as $i => $color)
                    <button type="button"
                            class="color-swatch w-5 h-5 border transition-all {{ $i === 0 ? 'border-lime scale-110' : 'border-off/20' }}"
                            style="background-color: {{ $color['hex'] ?? '#ccc' }};"
                            title="{{ $color['name'] }}"
                            data-color-index="{{ $i }}"></button>
                @endforeach
            </div>

            <div class="size-pills flex gap-1.5 mt-2 flex-wrap"></div>

            <button type="button"
                    class="add-to-cart-btn mt-3 w-full btn-coral text-xs uppercase tracking-widest font-bold py-2.5 disabled:opacity-40 disabled:cursor-not-allowed"
                    disabled>
                SELECT A SIZE
            </button>
        @endif
    </div>
</article>
