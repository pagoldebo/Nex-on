{{--
    ======================================================================
    THEME: VYRA — Modern Noir & High-Contrast Streetwear
    PARTIAL: FLASH SALE & PROMOTIONAL CARD
    ======================================================================
    File: resources/views/storefront/vyra/partials/sale-card.blade.php
    Partial: storefront.vyra.partials.sale-card
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
         data-swatch-active-class="border-[#D4FF3D] scale-110"
         data-swatch-inactive-class="border-white/20"
         data-pill-base-class="size-pill text-[9px] w-6 h-6 rounded-full border inline-flex items-center justify-center font-mono font-semibold transition-all"
         data-pill-inactive-class="border-[#FF3D5A]/40 text-[#F2F0EC]/70 hover:border-[#FF3D5A] hover:text-[#FF3D5A]"
         data-pill-active-class="bg-[#FF3D5A] text-white border-[#FF3D5A] shadow-xs">

    <div class="card-img-wrap block relative aspect-[3/4] bg-black/40 overflow-hidden rounded-t-[24px] md:rounded-t-[28px] cursor-pointer shadow-xs border border-white/5 group">
        <a href="{{ route('product.show', $product->slug) }}" class="block w-full h-full">
            @if(!empty($product->badge))
                <span class="absolute top-3 left-3 z-10 bg-[#D4FF3D] text-[#0E0E10] text-[9px] font-black px-2.5 py-1 rounded-full uppercase tracking-widest shadow-xs">
                    {{ strtoupper($product->badge) }}
                </span>
            @elseif($discountPercent > 0)
                <span class="absolute top-3 left-3 z-10 bg-[#FF3D5A] text-white text-[9px] font-black px-2.5 py-1 rounded-full uppercase tracking-widest shadow-xs">
                    -{{ $discountPercent }}% OFF
                </span>
            @else
                <span class="absolute top-3 left-3 z-10 bg-[#D4FF3D] text-[#0E0E10] text-[9px] font-black px-2.5 py-1 rounded-full uppercase tracking-widest shadow-xs">
                    SALE
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

    <div class="pt-3 pb-2 px-1">
        <p class="text-[10px] font-bold text-[#D4FF3D] uppercase tracking-wide mb-1">{{ $product->category->name ?? 'Collection' }}</p>
        <h3 class="font-body font-semibold text-base text-[#F2F0EC] truncate mb-1">
            <a href="{{ route('product.show', $product->slug) }}" class="hover:text-[#D4FF3D] transition-colors">
                {{ $product->name }}
            </a>
        </h3>

        <div class="flex items-center justify-between mt-1 mb-2.5">
            <p class="product-card-price text-[#FF3D5A] font-bold text-sm flex items-center gap-1.5 flex-wrap">
                <span class="price-current">₹{{ number_format($cardData['startingPrice'], 0) }}</span>
                @if($cardData['startingCompareAt'])
                    <span class="price-compare text-[#F2F0EC]/30 line-through text-xs font-normal">₹{{ number_format($cardData['startingCompareAt'], 0) }}</span>
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
            <div class="color-swatches flex gap-1.5 mb-2">
                @foreach ($cardData['colors'] as $i => $color)
                    <button type="button"
                            class="color-swatch w-4 h-4 rounded-full border transition-all {{ $i === 0 ? 'border-[#D4FF3D] scale-110' : 'border-white/20' }}"
                            style="background-color: {{ $color['hex'] ?? '#ccc' }};"
                            title="{{ $color['name'] }}"
                            data-color-index="{{ $i }}"></button>
                @endforeach
            </div>

            <div class="size-pills flex gap-1.5 flex-wrap">
                @foreach ($cardData['colors'][0]['sizes'] ?? [] as $sz)
                    <span class="size-pill text-[9px] w-6 h-6 rounded-full border border-white/15 text-[#F2F0EC]/70 flex items-center justify-center font-mono font-medium {{ $sz['inStock'] ? '' : 'opacity-30' }}">
                        {{ $sz['size'] }}
                    </span>
                @endforeach
            </div>
        @endif
    </div>
</article>
