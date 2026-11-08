{{--
    ======================================================================
    THEME: LUMIÈRE — French Silk Atelier & Minimalist Luxury
    PARTIAL: FLASH SALE & PROMOTIONAL CARD
    ======================================================================
    File: resources/views/storefront/lumiere/partials/sale-card.blade.php
    Partial: storefront.lumiere.partials.sale-card
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
    $discountPercent = 0;
    if (!empty($cardData['startingCompareAt']) && $cardData['startingCompareAt'] > $cardData['startingPrice']) {
        $discountPercent = round((($cardData['startingCompareAt'] - $cardData['startingPrice']) / $cardData['startingCompareAt']) * 100);
    }
@endphp

<article class="reveal group product-card"
         data-product='@json($cardData)'
         data-swatch-active-class="border-pink-600 scale-110"
         data-swatch-inactive-class="border-gray-200"
         data-pill-base-class="size-pill text-[9px] w-6 h-6 rounded-full border border-pink-200 text-gray-700 flex items-center justify-center font-medium"
         data-pill-active-class="bg-pink-600 text-white">

    <a href="{{ route('product.show', $product->slug) }}" class="card-img-wrap block relative aspect-[3/4] bg-pink-50/50 overflow-hidden rounded-t-[30px] md:rounded-t-[36px] cursor-pointer shadow-xs border border-pink-100/40">
        @if(!empty($product->badge))
            <span class="absolute top-3 left-3 z-10 bg-white/95 backdrop-blur-md text-pink-600 text-[9px] font-black px-2.5 py-1 rounded-full uppercase tracking-widest shadow-xs">
                {{ strtoupper($product->badge) }}
            </span>
        @elseif($discountPercent > 0)
            <span class="absolute top-3 left-3 z-10 bg-pink-600 text-white text-[9px] font-black px-2.5 py-1 rounded-full uppercase tracking-widest shadow-xs">
                -{{ $discountPercent }}% OFF
            </span>
        @else
            <span class="absolute top-3 left-3 z-10 bg-white/95 backdrop-blur-md text-pink-600 text-[9px] font-black px-2.5 py-1 rounded-full uppercase tracking-widest shadow-xs">
                SALE
            </span>
        @endif

        <img class="product-card-img card-img-zoom w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
             src="{{ $cardData['coverImage'] }}"
             alt="{{ $product->name }}">
    </a>

    <div class="pt-3 pb-2 px-1">
        <p class="text-[10px] font-black text-pink-600 uppercase tracking-widest mb-1">{{ $product->category->name ?? 'Collection' }}</p>
        <h3 class="font-display italic text-base md:text-lg text-gray-900 truncate mb-1">
            <a href="{{ route('product.show', $product->slug) }}" class="hover:text-pink-600 transition-colors">
                {{ $product->name }}
            </a>
        </h3>

        <p class="product-card-price text-pink-600 font-bold text-sm mt-1 mb-2.5 flex items-center gap-1.5 flex-wrap">
            <span class="price-current">₹{{ number_format($cardData['startingPrice'], 0) }}</span>
            @if($cardData['startingCompareAt'])
                <span class="price-compare text-gray-400 line-through text-xs font-normal">₹{{ number_format($cardData['startingCompareAt'], 0) }}</span>
            @endif
        </p>

        @if(count($cardData['colors']))
            <div class="color-swatches flex gap-1.5 mb-2">
                @foreach ($cardData['colors'] as $i => $color)
                    <button type="button"
                            class="color-swatch w-4 h-4 rounded-full border transition-all {{ $i === 0 ? 'border-pink-600 scale-110' : 'border-gray-200' }}"
                            style="background-color: {{ $color['hex'] ?? '#ccc' }};"
                            title="{{ $color['name'] }}"
                            data-color-index="{{ $i }}"></button>
                @endforeach
            </div>

            <div class="size-pills flex gap-1.5 flex-wrap">
                @foreach ($cardData['colors'][0]['sizes'] ?? [] as $sz)
                    <span class="size-pill text-[9px] w-6 h-6 rounded-full border border-pink-200 text-gray-600 flex items-center justify-center font-medium {{ $sz['inStock'] ? '' : 'opacity-30' }}">
                        {{ $sz['size'] }}
                    </span>
                @endforeach
            </div>
        @endif
    </div>
</article>
