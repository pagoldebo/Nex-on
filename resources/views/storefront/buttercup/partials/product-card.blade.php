{{--
    ======================================================================
    THEME: BUTTERCUP — Pastel Comfort Intimates & Loungewear
    PARTIAL: CATALOG PRODUCT CARD ITEM
    ======================================================================
    File: resources/views/storefront/buttercup/partials/product-card.blade.php
    Partial: storefront.buttercup.partials.product-card
    Parameters:
      - $product: App\Models\Product instance
    Responsibility: Modular grid item card for catalog, category, and search results
    Features:
      - Image hover transition and cover color presentation
      - Promotional badge overlays (Sale, Best Seller, New Harvest / Drop)
      - Customer rating star summary and review counter
      - One-click instant Add to Bag and Wishlist toggle triggers
    Operations & Maintenance:
      - Reusable across all catalog layouts within buttercup
--}}
@php
    $cardData = $product->toCardArray();
@endphp

<article class="reveal ribbon-tilt rounded-4xl bg-cream overflow-hidden product-card"
         data-product='@json($cardData)'
         data-swatch-active-class="border-rose-dark scale-110"
         data-swatch-inactive-class="border-rose/30"
         data-pill-base-class="size-pill text-[10px] w-7 h-7 rounded-full border inline-flex items-center justify-center font-semibold transition-all"
         data-pill-inactive-class="border-rose/40 text-ink/70 hover:border-rose hover:text-rose"
         data-pill-active-class="bg-rose text-cream border-rose shadow-xs"
         data-add-to-bag-label="Add to Cart"
         data-select-size-label="Select a Size">

    <div class="card-img-wrap block relative aspect-[3/4] bg-blush-deep/60 overflow-hidden group cursor-pointer">
        <a href="{{ route('product.show', $product->slug) }}" class="block w-full h-full">
            @if($product->badge)
                <span class="absolute top-3 left-3 z-10 bg-sage text-cream text-[10px] font-bold px-2 py-1 rounded-full shadow-xs">
                    {{ strtoupper($product->badge) }}
                </span>
            @endif

            <img class="product-card-img card-img-zoom w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                 src="{{ $cardData['coverImage'] }}"
                 alt="{{ $product->name }}">
        </a>

        {{-- Floating Quick View Button on Image Hover --}}
        <button type="button"
                onclick="event.preventDefault(); event.stopPropagation(); openQuickView({{ \Illuminate\Support\Js::from($cardData) }})"
                class="absolute bottom-3 left-1/2 -translate-x-1/2 z-20 bg-cream/95 backdrop-blur-md text-ink hover:bg-[#B97680] hover:text-white text-[10px] font-bold uppercase tracking-widest px-3.5 py-1.5 rounded-full shadow-md opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-y-2 group-hover:translate-y-0 flex items-center gap-1.5 whitespace-nowrap cursor-pointer">
            <svg class="w-3.5 h-3.5 text-[#B97680] group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            <span>Quick View</span>
        </button>
    </div>

    <div class="p-4">
        <p class="text-[11px] font-bold text-sage-dark uppercase tracking-wide">{{ $product->category->name }}</p>
        <h3 class="font-display text-lg text-ink mt-1">
            <a href="{{ route('product.show', $product->slug) }}" class="hover:text-[#B97680] transition-colors">
                {{ $product->name }}
            </a>
        </h3>

        <div class="flex items-center justify-between mt-2">
            <p class="product-card-price text-rose-dark font-bold">
                <span class="price-current">₹{{ number_format($cardData['startingPrice'], 0) }}</span>
                @if($cardData['startingCompareAt'])
                    <span class="price-compare text-ink/30 line-through text-sm ml-1">₹{{ number_format($cardData['startingCompareAt'], 0) }}</span>
                @endif
            </p>
            <button type="button"
                    onclick="event.preventDefault(); event.stopPropagation(); openQuickView({{ \Illuminate\Support\Js::from($cardData) }})"
                    title="Quick View"
                    class="text-[10px] font-bold text-[#B97680] hover:text-ink bg-rose/10 hover:bg-rose/20 px-2.5 py-1 rounded-full transition-colors flex items-center gap-1 cursor-pointer">
                <svg class="w-3 h-3 text-[#B97680]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                <span>Quick View</span>
            </button>
        </div>

        @if(count($cardData['colors']))
            <div class="color-swatches flex gap-1.5 mt-3">
                @foreach ($cardData['colors'] as $i => $color)
                    <button type="button"
                            class="color-swatch w-5 h-5 rounded-full border-2 transition-all {{ $i === 0 ? 'border-rose-dark scale-110' : 'border-rose/30' }}"
                            style="background-color: {{ $color['hex'] ?? '#ccc' }};"
                            title="{{ $color['name'] }}"
                            data-color-index="{{ $i }}"></button>
                @endforeach
            </div>

            <div class="size-pills flex gap-1.5 mt-2 flex-wrap"></div>

            <button type="button"
                    class="add-to-cart-btn mt-3 w-full btn-rose text-xs py-2.5 font-semibold disabled:opacity-40 disabled:cursor-not-allowed"
                    disabled>
                SELECT A SIZE
            </button>
        @endif
    </div>
</article>
