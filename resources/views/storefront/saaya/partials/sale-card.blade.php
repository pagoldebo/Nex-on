{{-- resources/views/storefront/saaya/partials/sale-card.blade.php
     Sale Section Product Card matching Reference Design
--}}
@php
    $cardData = $product->toCardArray();
    $discountPercent = 0;
    if (!empty($cardData['startingCompareAt']) && $cardData['startingCompareAt'] > $cardData['startingPrice']) {
        $discountPercent = round((($cardData['startingCompareAt'] - $cardData['startingPrice']) / $cardData['startingCompareAt']) * 100);
    }
@endphp

<article class="reveal group product-card"
         data-product='@json($cardData)'
         data-swatch-active-class="border-maroon scale-110"
         data-swatch-inactive-class="border-gold/30"
         data-pill-base-class="size-pill text-[9px] w-6 h-6 rounded-full border border-gold/40 text-maroon/70 flex items-center justify-center font-medium"
         data-pill-active-class="bg-maroon text-ivory">

    <a href="{{ route('product.show', $product->slug) }}" class="card-img-wrap block relative aspect-[3/4] overflow-hidden rounded-t-[28px] md:rounded-t-[32px] bg-blush/20 cursor-pointer shadow-xs">
        @if(!empty($product->badge))
            <span class="absolute top-3 left-3 z-10 bg-gold text-maroon text-[9px] font-bold px-2.5 py-1 rounded-full uppercase tracking-widest shadow-xs">
                {{ strtoupper($product->badge) }}
            </span>
        @elseif($discountPercent > 0)
            <span class="absolute top-3 left-3 z-10 bg-[#4A1521] text-gold text-[9px] font-bold px-2.5 py-1 rounded-full uppercase tracking-widest shadow-xs border border-gold/30">
                -{{ $discountPercent }}% OFF
            </span>
        @else
            <span class="absolute top-3 left-3 z-10 bg-gold text-maroon text-[9px] font-bold px-2.5 py-1 rounded-full uppercase tracking-widest shadow-xs">
                SALE
            </span>
        @endif

        <img class="product-card-img card-img-zoom w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
             src="{{ $cardData['coverImage'] }}"
             alt="{{ $product->name }}">
    </a>

    <div class="pt-3 pb-2 px-1">
        <p class="text-[10px] tracking-widest text-gold-dark uppercase font-bold mb-1">{{ $product->category->name ?? 'Collection' }}</p>
        <h3 class="font-display text-base md:text-lg text-maroon truncate mb-1">
            <a href="{{ route('product.show', $product->slug) }}" class="hover:text-gold transition-colors">
                {{ $product->name }}
            </a>
        </h3>

        <p class="product-card-price text-maroon font-bold text-sm mt-1 mb-2.5 flex items-center gap-1.5 flex-wrap">
            <span class="price-current">₹{{ number_format($cardData['startingPrice'], 0) }}</span>
            @if($cardData['startingCompareAt'])
                <span class="price-compare text-maroon/40 line-through text-xs font-normal">₹{{ number_format($cardData['startingCompareAt'], 0) }}</span>
            @endif
        </p>

        @if(count($cardData['colors']))
            <div class="color-swatches flex gap-1.5 mb-2">
                @foreach ($cardData['colors'] as $i => $color)
                    <button type="button"
                            class="color-swatch w-4 h-4 rounded-full border transition-all {{ $i === 0 ? 'border-maroon scale-110' : 'border-gold/30' }}"
                            style="background-color: {{ $color['hex'] ?? '#ccc' }};"
                            title="{{ $color['name'] }}"
                            data-color-index="{{ $i }}"></button>
                @endforeach
            </div>

            <div class="size-pills flex gap-1.5 flex-wrap">
                @foreach ($cardData['colors'][0]['sizes'] ?? [] as $sz)
                    <span class="size-pill text-[9px] w-6 h-6 rounded-full border border-gold/30 text-maroon/70 flex items-center justify-center font-medium {{ $sz['inStock'] ? '' : 'opacity-30' }}">
                        {{ $sz['size'] }}
                    </span>
                @endforeach
            </div>
        @endif
    </div>
</article>
