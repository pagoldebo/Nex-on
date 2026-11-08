{{--
    ======================================================================
    VIEW TEMPLATE:  STOREFRONT LUMIERE PARTIALS BESTSELLER CARD 
    ======================================================================
    File: resources/views/storefront/lumiere/partials/bestseller-card.blade.php
    Responsibility: View template supporting the Nex-on platform
    Features:
      - Standard Blade template syntax with Tailwind CSS styling
      - Integrates with application controllers and view model data
--}}
@php
    $cardData = $product->toCardArray();
@endphp

<div class="best-scroll-item p-4 bg-cyan rounded-[50px] shadow-inner relative animate-fade group transition-transform hover:scale-[1.01]"
     onclick="openQuickView({{ \Illuminate\Support\Js::from($cardData) }})">

    <div class="flex justify-between items-center mb-6 px-2">
        <div class="flex items-center gap-2">
            <span class="text-pink font-black text-base font-display italic">Lumière</span>
            <span class="text-[7px] font-bold text-gray-400 uppercase tracking-tighter">SPOTLIGHT</span>
        </div>
    </div>

    <div class="relative bg-white rounded-[35px] overflow-hidden shadow-xl h-[240px] mb-6">
        {{-- Same hover-swap pattern as the lustre card, just inverted base/hover
             order to match the original spotlight card's visual rhythm. --}}
        <div class="absolute inset-0 bg-cover bg-center transition-opacity duration-700 group-hover:opacity-0"
             style="background-image: url('{{ $cardData['colors'][0]['images'][1] ?? $cardData['colors'][0]['images'][0] ?? $cardData['coverImage'] }}')"></div>
        <div class="absolute inset-0 bg-cover bg-center opacity-0 transition-opacity duration-700 group-hover:opacity-100 scale-105 group-hover:scale-100"
             style="background-image: url('{{ $cardData['colors'][0]['images'][0] ?? $cardData['coverImage'] }}')"></div>
    </div>

    <div class="px-2 mb-6">
        <h3 class="text-base font-bold text-gray-800 leading-tight mb-2 truncate">{{ $product->name }}</h3>
        <p class="text-[9px] text-gray-500 font-medium leading-relaxed">{{ $product->description }}</p>
    </div>

    <div class="flex flex-col gap-2.5 px-1">
        <div class="flex gap-2">
            <div class="bg-white/80 backdrop-blur-md px-2 py-2 rounded-xl flex items-center gap-2 shadow-sm border border-white">
                <span class="text-[7px] font-bold text-gray-500 uppercase">Fine Silk</span>
            </div>
            <div class="bg-white px-2 py-2 rounded-xl shadow-sm border border-white flex items-center gap-1.5 flex-1 overflow-hidden justify-center">
                @foreach ($cardData['colors'] as $color)
                    <div class="w-2.5 h-2.5 rounded-full border border-gray-100" style="background-color: {{ $color['hex'] ?? '#ccc' }}"></div>
                @endforeach
            </div>
        </div>
        <div class="flex items-center gap-2">
            <div class="bg-white px-2 py-2 rounded-xl shadow-sm border border-white flex items-center gap-1.5 flex-1 justify-center">
                @foreach ($cardData['colors'][0]['sizes'] ?? [] as $size)
                    <div class="w-5 h-5 rounded-full border {{ $size['inStock'] ? 'border-gray-100 text-gray-400' : 'border-gray-50 text-gray-200' }} flex items-center justify-center text-[7px] font-bold">
                        {{ $size['size'] }}
                    </div>
                @endforeach
            </div>
            {{-- stopPropagation so this inline buy button doesn't double-fire the card's own onclick --}}
            <button onclick="event.stopPropagation(); openQuickView({{ \Illuminate\Support\Js::from($cardData) }})"
                    class="bg-pink text-white font-bold text-[9px] uppercase py-2.5 px-4 rounded-xl shadow-lg hover:bg-pink-dark transition-colors flex items-center gap-1.5">
                @if(!empty($cardData['startingCompareAt']) && $cardData['startingCompareAt'] > $cardData['startingPrice'])
                    <span class="line-through opacity-70 text-[8px] font-normal">₹{{ number_format($cardData['startingCompareAt'], 0) }}</span>
                @endif
                <span>₹{{ number_format($cardData['startingPrice'], 0) }}</span>
            </button>
        </div>
    </div>
</div>
