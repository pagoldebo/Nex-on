{{--
    ======================================================================
    THEME: BUTTERCUP — Pastel Comfort Intimates & Loungewear
    PAGE: ABOUT US & BRAND STORY
    ======================================================================
    File: resources/views/storefront/buttercup/about.blade.php
    Controller: App\Http\Controllers\StorefrontController@about
    Route Context: GET /about
    ViewModel Injections:
      - $theme: Active Template model ('buttercup')
      - $title: Page SEO title
    Responsibility: Brand manifesto, artisan heritage, craftsmanship, and sustainability
    Features:
      - Atmospheric hero imagery and founder's note
      - Artisan handcrafting timeline and ethical sourcing commitments
      - Brand milestone metrics and curated editorial lookbook links
    Operations & Maintenance:
      - Text content editable via admin settings or custom template language
--}}
<x-storefront-layout :theme="$theme" title="About Buttercup — Comfort That Feels Like Home" metaDesc="Buttercup designs cloud-soft everyday lingerie, wire-free comfort, and breathable cotton modal sets made for real bodies.">

<div class="py-12 lg:py-20" style="background:#FFFAF5">
  <div class="max-w-7xl mx-auto px-6 lg:px-12">

    {{-- Breadcrumb --}}
    <nav class="text-xs flex items-center gap-2 mb-8 text-[#4A3B3A]/40 font-body">
      <a href="{{ route('home') }}" class="hover:text-[#B97680] transition-colors">Home</a>
      <span>/</span>
      <span class="text-[#4A3B3A] font-medium">About Us</span>
    </nav>

    {{-- Header --}}
    <div class="text-center max-w-3xl mx-auto mb-16 reveal">
      <p class="text-[#7F9272] text-xs font-bold uppercase tracking-widest mb-2 font-body">
        {{ \App\Models\Setting::get('buttercup_story_badge', 'Our Story') }}
      </p>
      <h1 class="font-display text-4xl lg:text-6xl text-[#4A3B3A] leading-tight">
        {!! nl2br(e(\App\Models\Setting::get('buttercup_story_title', "Designed for Real Bodies,\nReal Days"))) !!}
      </h1>
      <div class="flex justify-center mt-4">
        <div class="w-32 h-0.5 bg-gradient-to-r from-transparent via-[#D89AA0] to-transparent"></div>
      </div>
    </div>

    {{-- ═══════════════════════════════════════════════
         OUR STORY  —  2-Column matching home page
         ═══════════════════════════════════════════════ --}}
    <div class="grid lg:grid-cols-2 gap-12 lg:gap-20 items-center mb-20">

      {{-- Left: story image frame --}}
      <div class="reveal flex justify-center lg:justify-start">
        <div class="aspect-[3/4] w-72 lg:w-96 rounded-[32px] overflow-hidden border-2 border-[#D89AA0]/40 shadow-md relative bg-[#FFFAF5]">
          @php
            $bcStoryImg = \App\Models\Setting::get('buttercup_story_image');
          @endphp
          @if($bcStoryImg)
            <img src="{{ \App\Models\Setting::imageUrl($bcStoryImg) }}" alt="About Buttercup" class="w-full h-full object-cover">
          @else
            <div class="w-full h-full bg-gradient-to-b from-[#FBE7E4] to-[#FFFAF5] flex flex-col items-center justify-center p-8 text-center border border-[#D89AA0]/20">
              <svg viewBox="0 0 100 120" class="w-24 h-28 text-[#D89AA0]/40 mb-3" fill="currentColor">
                <ellipse cx="50" cy="25" rx="16" ry="20"/>
                <path d="M15 48 Q30 38 50 42 Q70 38 85 48 L88 105 Q70 115 50 112 Q30 115 12 105 Z"/>
              </svg>
              <p class="font-display text-base font-bold text-[#B97680]">Buttercup Studio</p>
              <p class="font-body text-xs text-[#4A3B3A]/60 mt-1">Comfort that feels like home</p>
            </div>
          @endif
        </div>
      </div>

      {{-- Right: story narrative + live stats --}}
      <div class="reveal">
        <p class="text-[#7F9272] text-xs font-bold uppercase tracking-widest mb-2 font-body">Our Promise</p>
        <h2 class="font-display text-3xl lg:text-4xl text-[#4A3B3A] mb-6 leading-tight">
          {{ \App\Models\Setting::get('buttercup_about_philosophy_heading', 'Why Should Comfort and Style Ever Be a Trade-off?') }}
        </h2>
        <p class="font-body text-[#4A3B3A]/80 leading-relaxed mb-5 text-sm lg:text-base">
          {{ \App\Models\Setting::get('buttercup_story_p1', 'Buttercup started with one simple question: why should comfort and style be a trade-off? We design every piece with breathable, skin-friendly fabric — soft enough for everyday, pretty enough to feel special.') }}
        </p>
        <p class="font-body text-[#4A3B3A]/80 leading-relaxed mb-10 text-sm lg:text-base">
          {{ \App\Models\Setting::get('buttercup_story_p2', 'Zero painful underwires, zero pinch. Just butter-soft modal, organic cotton, and gentle stretch that moves with you from dawn to dusk.') }}
        </p>

        {{-- 3 Stats --}}
        <div class="grid grid-cols-3 gap-4 border-t border-[#D89AA0]/30 pt-8">
          @php
            $stat1Num = \App\Models\Setting::get('buttercup_stat1_num', '5+');
            $stat1Lbl = \App\Models\Setting::get('buttercup_stat1_label', 'Years of Comfort');
            $stat2Num = \App\Models\Setting::get('buttercup_stat2_num', '2L+');
            $stat2Lbl = \App\Models\Setting::get('buttercup_stat2_label', 'Happy Customers');
            $stat3Num = \App\Models\Setting::get('buttercup_stat3_num', '100%');
            $stat3Lbl = \App\Models\Setting::get('buttercup_stat3_label', 'Skin-Friendly');
          @endphp
          @foreach([[$stat1Num, $stat1Lbl], [$stat2Num, $stat2Lbl], [$stat3Num, $stat3Lbl]] as [$num, $label])
            <div class="text-center">
              <p class="font-display text-3xl lg:text-4xl text-[#B97680]">{{ $num }}</p>
              <p class="font-body text-xs text-[#4A3B3A]/60 uppercase tracking-widest mt-1">{{ $label }}</p>
            </div>
          @endforeach
        </div>
      </div>

    </div>

    {{-- Floral divider --}}
    <div class="py-6 flex justify-center">
      <div class="w-48 h-0.5 bg-gradient-to-r from-transparent via-[#D89AA0]/40 to-transparent"></div>
    </div>

    {{-- Brand Pillars --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 py-12">
      <div class="p-8 bg-[#FBE7E4]/50 rounded-[28px] border border-[#D89AA0]/30 shadow-xs space-y-3 reveal text-center">
        <div class="w-12 h-12 rounded-full bg-[#D89AA0]/20 text-[#B97680] flex items-center justify-center font-display text-xl mx-auto">1</div>
        <h3 class="font-display text-xl text-[#4A3B3A]">{{ \App\Models\Setting::get('buttercup_pillar1_title', 'Cloud-Soft Modal') }}</h3>
        <p class="font-body text-xs text-[#4A3B3A]/70 leading-relaxed">{{ \App\Models\Setting::get('buttercup_pillar1_desc', 'Micro-modal knit woven from sustainably harvested beechwood, providing a feather-soft second skin feel.') }}</p>
      </div>

      <div class="p-8 bg-[#FBE7E4]/50 rounded-[28px] border border-[#D89AA0]/30 shadow-xs space-y-3 reveal text-center">
        <div class="w-12 h-12 rounded-full bg-[#D89AA0]/20 text-[#B97680] flex items-center justify-center font-display text-xl mx-auto">2</div>
        <h3 class="font-display text-xl text-[#4A3B3A]">{{ \App\Models\Setting::get('buttercup_pillar2_title', 'Wire-Free Ergonomics') }}</h3>
        <p class="font-body text-xs text-[#4A3B3A]/70 leading-relaxed">{{ \App\Models\Setting::get('buttercup_pillar2_desc', 'Sculpted support panels that gently lift without digging metal bones into your ribs. Wearable 24 hours a day.') }}</p>
      </div>

      <div class="p-8 bg-[#FBE7E4]/50 rounded-[28px] border border-[#D89AA0]/30 shadow-xs space-y-3 reveal text-center">
        <div class="w-12 h-12 rounded-full bg-[#D89AA0]/20 text-[#B97680] flex items-center justify-center font-display text-xl mx-auto">3</div>
        <h3 class="font-display text-xl text-[#4A3B3A]">{{ \App\Models\Setting::get('buttercup_pillar3_title', 'Real-Body Inclusive Sizing') }}</h3>
        <p class="font-body text-xs text-[#4A3B3A]/70 leading-relaxed">{{ \App\Models\Setting::get('buttercup_pillar3_desc', 'Engineered across all bust shapes and cup sizes so you never feel squeezed, pinched, or restricted.') }}</p>
      </div>
    </div>

    {{-- Call to action --}}
    <div class="text-center mt-12 mb-8 reveal">
      <a href="{{ route('search') }}" class="px-10 py-4 text-xs font-bold uppercase tracking-widest transition-all bg-[#D89AA0] text-[#FFFAF5] hover:bg-[#B97680] rounded-full inline-block shadow-sm">
        Explore Buttercup Edit 🌸
      </a>
    </div>

  </div>
</div>

</x-storefront-layout>
