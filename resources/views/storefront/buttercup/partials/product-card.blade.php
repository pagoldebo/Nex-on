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
         data-pill-base-class="size-pill text-[10px] w-7 h-7 rounded-full border border-rose/40 text-ink/60"
         data-pill-active-class="bg-rose text-cream"
         data-add-to-bag-label="Add to Cart"
         data-select-size-label="Select a Size">

    <a href="{{ route('product.show', $product->slug) }}" class="block relative aspect-[3/4] bg-blush-deep/60 overflow-hidden group cursor-pointer">
        @if($product->badge)
            <span class="absolute top-3 left-3 z-10 bg-sage text-cream text-[10px] font-bold px-2 py-1 rounded-full">
                {{ strtoupper($product->badge) }}
            </span>
        @endif

        <img class="product-card-img card-img-zoom w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
             src="{{ $cardData['coverImage'] }}"
             alt="{{ $product->name }}">
    </a>

    <div class="p-4">
        <p class="text-[11px] font-bold text-sage-dark uppercase tracking-wide">{{ $product->category->name }}</p>
        <h3 class="font-display text-lg text-ink mt-1">
            <a href="{{ route('product.show', $product->slug) }}" class="hover:text-[#B97680] transition-colors">
                {{ $product->name }}
            </a>
        </h3>

        <p class="product-card-price text-rose-dark font-bold mt-2">
            <span class="price-current">₹{{ number_format($cardData['startingPrice'], 0) }}</span>
            @if($cardData['startingCompareAt'])
                <span class="price-compare text-ink/30 line-through text-sm ml-1">₹{{ number_format($cardData['startingCompareAt'], 0) }}</span>
            @endif
        </p>

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
