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

    <a href="{{ route('product.show', $product->slug) }}" class="card-img-wrap block relative aspect-[3/4] overflow-hidden bg-blush/30 group cursor-pointer">
        @if($product->badge)
            <span class="absolute top-3 left-3 z-10 bg-gold text-maroon text-[10px] px-2 py-1 tracking-widest">
                {{ strtoupper($product->badge) }}
            </span>
        @endif

        {{-- Cover image = first image of the default (first) color.
             Swapping colors below changes this via JS, no reload. --}}
        <img class="product-card-img card-img-zoom w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
             src="{{ $cardData['coverImage'] }}"
             alt="{{ $product->name }}">
    </a>

    <div class="p-4">
        <p class="text-[11px] tracking-widest text-gold-dark uppercase">{{ $product->category->name }}</p>
        <h3 class="font-display text-lg text-maroon mt-1">
            <a href="{{ route('product.show', $product->slug) }}" class="hover:text-gold transition-colors">
                {{ $product->name }}
            </a>
        </h3>

        {{-- Price updates live as color+size selection changes --}}
        <p class="product-card-price text-maroon font-medium mt-2">
            <span class="price-current">₹{{ number_format($cardData['startingPrice'], 0) }}</span>
            @if($cardData['startingCompareAt'])
                <span class="price-compare text-maroon/40 line-through text-sm ml-1">₹{{ number_format($cardData['startingCompareAt'], 0) }}</span>
            @endif
        </p>

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
