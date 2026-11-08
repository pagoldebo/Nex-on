{{--
    ======================================================================
    VIEW TEMPLATE:  STOREFRONT LUMIERE PARTIALS LUSTRE CARD 
    ======================================================================
    File: resources/views/storefront/lumiere/partials/lustre-card.blade.php
    Responsibility: View template supporting the Nex-on platform
    Features:
      - Standard Blade template syntax with Tailwind CSS styling
      - Integrates with application controllers and view model data
--}}
@php
    $cardData = $product->toCardArray();
    $hasVariety = count($cardData['colors']) > 1 || count($cardData['colors'][0]['sizes'] ?? []) > 1;
@endphp

<div class="scroll-item group cursor-pointer relative animate-fade"
     onclick="openQuickView({{ \Illuminate\Support\Js::from($cardData) }})">
    <div class="aspect-[4/5] rounded-[30px] overflow-hidden relative shadow-sm mb-4">
        <div class="absolute top-4 left-4 z-20">
            @if($cardData['totalStock'] > 0 && $cardData['totalStock'] <= 3)
                <span class="bg-white/90 backdrop-blur-md text-[8px] font-black text-red-400 px-3 py-1.5 rounded-full uppercase tracking-widest flex items-center gap-2 animate-pulse">
                    Only {{ $cardData['totalStock'] }} Left
                </span>
            @else
                <span class="bg-white/90 backdrop-blur-md text-[8px] font-black text-pink px-3 py-1.5 rounded-full uppercase tracking-widest">
                    {{ $product->badge ?? 'Limited' }}
                </span>
            @endif
        </div>

        {{-- Base image = first image of the first color. Hover image = that
             color's second image if it exists, else repeats the first —
             mirrors the hover-swap micro-interaction in the static demo. --}}
        <div class="absolute inset-0 bg-cover bg-center transition-opacity duration-700 group-hover:opacity-0"
             style="background-image: url('{{ $cardData['colors'][0]['images'][0] ?? $cardData['coverImage'] }}')"></div>
        <div class="absolute inset-0 bg-cover bg-center opacity-0 transition-opacity duration-700 group-hover:opacity-100 scale-105 group-hover:scale-100"
             style="background-image: url('{{ $cardData['colors'][0]['images'][1] ?? $cardData['colors'][0]['images'][0] ?? $cardData['coverImage'] }}')"></div>
    </div>

    <div class="px-2">
        <h3 class="text-sm font-bold text-gray-800 mb-1 truncate">{{ $product->name }}</h3>
        <div class="flex items-center justify-between mb-3">
            <p class="text-pink font-bold text-sm flex items-center gap-1.5 flex-wrap">
                <span>₹{{ number_format($cardData['startingPrice'], 0) }}</span>
                @if(!empty($cardData['startingCompareAt']) && $cardData['startingCompareAt'] > $cardData['startingPrice'])
                    <span class="text-[11px] font-normal text-gray-400 line-through">₹{{ number_format($cardData['startingCompareAt'], 0) }}</span>
                @endif
                @if($hasVariety)
                    <span class="text-[8px] text-gray-400 font-normal">onwards</span>
                @endif
            </p>
            {{-- One dot per color, so the card hints at variety before opening quick-view --}}
            <div class="flex gap-1">
                @foreach ($cardData['colors'] as $color)
                    <div class="w-2.5 h-2.5 rounded-full border border-gray-100" style="background-color: {{ $color['hex'] ?? '#ccc' }}"></div>
                @endforeach
            </div>
        </div>

        {{-- Size hints from the first color only — the quick-view modal is
             where the real per-color size list and live price take over. --}}
        @if(!empty($cardData['colors'][0]['sizes']))
            <div class="flex gap-1.5">
                @foreach ($cardData['colors'][0]['sizes'] as $size)
                    <div class="w-4 h-4 rounded-full border {{ $size['inStock'] ? 'border-gray-200 text-gray-400' : 'border-gray-100 text-gray-200' }} flex items-center justify-center text-[7px] font-bold">
                        {{ $size['size'] }}
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
