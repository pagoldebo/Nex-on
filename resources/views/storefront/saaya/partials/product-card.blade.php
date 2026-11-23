{{--
    ======================================================================
    THEME: SAAYA — Royal Indian Trousseau & Heritage Silk
    PARTIAL: CATALOG PRODUCT CARD ITEM
    ======================================================================
    File: resources/views/storefront/saaya/partials/product-card.blade.php
    Partial: storefront.saaya.partials.product-card
    Parameters:
      - $product: App\Models\Product instance
    Responsibility: Modular grid item card for catalog, category, and search results
    Features:
      - Image hover transition and cover color presentation
      - Promotional badge overlays (Sale, Best Seller, New Harvest / Drop)
      - Customer rating star summary and review counter
      - One-click instant Add to Bag and Wishlist toggle triggers
    Operations & Maintenance:
      - Reusable across all catalog layouts within saaya
--}}
@php
    $cardData = $product->toCardArray();
@endphp

<article class="reveal bg-ivory group product-card"
         data-product='@json($cardData)'
         data-swatch-active-class="border-maroon scale-110"
         data-swatch-inactive-class="border-gold/30"
         data-pill-base-class="size-pill text-[10px] w-7 h-7 rounded-full border inline-flex items-center justify-center font-semibold transition-all"
         data-pill-inactive-class="border-gold/40 text-maroon/80 hover:border-maroon hover:text-maroon"
         data-pill-active-class="bg-maroon text-ivory border-maroon shadow-xs"
         data-add-to-bag-label="ADD TO BAG"
         data-select-size-label="SELECT A SIZE">

    <div class="card-img-wrap block relative aspect-[3/4] overflow-hidden bg-blush/30 group cursor-pointer">
        <a href="{{ route('product.show', $product->slug) }}" class="block w-full h-full">
            @if($product->badge)
                <span class="absolute top-3 left-3 z-10 bg-gold text-maroon text-[10px] px-2 py-1 tracking-widest font-semibold">
                    {{ strtoupper($product->badge) }}
                </span>
            @endif

            {{-- Cover image = first image of the default (first) color.
                 Swapping colors below changes this via JS, no reload. --}}
            <img class="product-card-img card-img-zoom w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                 src="{{ $cardData['coverImage'] }}"
                 alt="{{ $product->name }}">
        </a>

        {{-- Quick View Floating Button on Hover --}}
        <button type="button"
                onclick="event.preventDefault(); event.stopPropagation(); openQuickView({{ \Illuminate\Support\Js::from($cardData) }})"
                class="absolute bottom-3 left-1/2 -translate-x-1/2 z-20 bg-ivory/95 backdrop-blur-md text-maroon hover:bg-maroon hover:text-gold border border-gold/40 text-[10px] font-bold uppercase tracking-widest px-3.5 py-1.5 rounded-full shadow-md opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-y-2 group-hover:translate-y-0 flex items-center gap-1.5 whitespace-nowrap cursor-pointer">
            <svg class="w-3.5 h-3.5 text-gold group-hover:text-gold transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            <span>Quick View</span>
        </button>
    </div>

    <div class="p-4">
        <p class="text-[11px] tracking-widest text-gold-dark uppercase">{{ $product->category->name }}</p>
        <h3 class="font-display text-lg text-maroon mt-1">
            <a href="{{ route('product.show', $product->slug) }}" class="hover:text-gold transition-colors">
                {{ $product->name }}
            </a>
        </h3>

        {{-- Price updates live as color+size selection changes --}}
        <div class="flex items-center justify-between mt-2">
            <p class="product-card-price text-maroon font-medium">
                <span class="price-current">@currency($cardData['startingPrice'])</span>
                @if($cardData['startingCompareAt'])
                    <span class="price-compare text-maroon/40 line-through text-sm ml-1">@currency($cardData['startingCompareAt'])</span>
                @endif
            </p>
            <button type="button"
                    onclick="event.preventDefault(); event.stopPropagation(); openQuickView({{ \Illuminate\Support\Js::from($cardData) }})"
                    title="Quick View"
                    class="text-[10px] font-bold text-gold hover:text-maroon transition-colors flex items-center gap-1 cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                <span>Quick View</span>
            </button>
        </div>

        {{-- Color swatches --}}
        @if(count($cardData['colors']))
            <div class="color-swatches flex gap-1.5 mt-3">
                @foreach ($cardData['colors'] as $i => $color)
                    <button type="button"
                            class="color-swatch w-5 h-5 rounded-full border transition-all {{ $i === 0 ? 'border-maroon scale-110' : 'border-gold/30' }}"
                            style="background-color: {{ $color['hex'] ?? '#ccc' }};"
                            title="{{ $color['name'] }}"
                            data-color-index="{{ $i }}"></button>
                @endforeach
            </div>

            {{-- Size pills — populated/replaced by JS based on the selected color,
                 since sizes are sparse and differ per color --}}
            <div class="size-pills flex gap-1.5 mt-2 flex-wrap"></div>

            <button type="button"
                    class="add-to-cart-btn mt-3 w-full gold-btn text-xs py-2 tracking-widest font-medium disabled:opacity-40 disabled:cursor-not-allowed"
                    disabled>
                SELECT A SIZE
            </button>
        @endif
    </div>
</article>
