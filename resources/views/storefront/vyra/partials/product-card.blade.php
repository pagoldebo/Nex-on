{{-- resources/views/storefront/vyra/partials/product-card.blade.php
     Expects: $product (Eloquent Product, with colors.images and colors.variants eager-loaded)
--}}
@php
    $cardData = $product->toCardArray();
@endphp

<article class="reveal tilt-card bg-ink-light product-card"
         data-product='@json($cardData)'
         data-swatch-active-class="border-lime scale-110"
         data-swatch-inactive-class="border-off/20"
         data-pill-base-class="size-pill text-[10px] w-7 h-7 border border-off/20 text-off/60"
         data-pill-active-class="bg-lime text-ink border-lime"
         data-add-to-bag-label="ADD TO BAG"
         data-select-size-label="SELECT A SIZE">

    <a href="{{ route('product.show', $product->slug) }}" class="block relative aspect-[3/4] bg-coral/10 overflow-hidden group cursor-pointer">
        @if($product->badge)
            <span class="absolute top-3 left-3 z-10 bg-lime text-ink text-[10px] font-bold px-2 py-1 uppercase">
                {{ strtoupper($product->badge) }}
            </span>
        @endif

        <img class="product-card-img card-img-zoom w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
             src="{{ $cardData['coverImage'] }}"
             alt="{{ $product->name }}">
    </a>

    <div class="p-4">
        <p class="text-[11px] font-bold text-lime uppercase tracking-wide">{{ $product->category->name }}</p>
        <h3 class="font-body font-semibold text-off mt-1">
            <a href="{{ route('product.show', $product->slug) }}" class="hover:text-lime transition-colors">
                {{ $product->name }}
            </a>
        </h3>

        <p class="product-card-price text-off font-bold mt-2">
            <span class="price-current">₹{{ number_format($cardData['startingPrice'], 0) }}</span>
            @if($cardData['startingCompareAt'])
                <span class="price-compare text-off/30 line-through text-sm ml-1">₹{{ number_format($cardData['startingCompareAt'], 0) }}</span>
            @endif
        </p>

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
