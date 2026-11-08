{{--
    ======================================================================
    THEME: VYRA — Modern Noir & High-Contrast Streetwear
    PAGE: ABOUT US & BRAND STORY
    ======================================================================
    File: resources/views/storefront/vyra/about.blade.php
    Controller: App\Http\Controllers\StorefrontController@about
    Route Context: GET /about
    ViewModel Injections:
      - $theme: Active Template model ('vyra')
      - $title: Page SEO title
    Responsibility: Brand manifesto, artisan heritage, craftsmanship, and sustainability
    Features:
      - Atmospheric hero imagery and founder's note
      - Artisan handcrafting timeline and ethical sourcing commitments
      - Brand milestone metrics and curated editorial lookbook links
    Operations & Maintenance:
      - Text content editable via admin settings or custom template language
--}}
<x-storefront-layout :theme="$theme" title="About Vyra — The Philosophy" metaDesc="Vyra was created for those who treat style as armour. Architectural seams, sheer mesh, and unyielding contours crafted for the fearless.">

<div class="py-12 lg:py-20" style="background:#0E0E10">
  <div class="max-w-7xl mx-auto px-6 lg:px-12">

    {{-- Breadcrumb --}}
    <nav class="text-xs flex items-center gap-2 mb-8 text-[#F2F0EC]/40 font-body">
      <a href="{{ route('home') }}" class="hover:text-[#D4FF3D] transition-colors">Home</a>
      <span>/</span>
      <span class="text-[#F2F0EC] font-medium">About Vyra</span>
    </nav>

    {{-- Header --}}
    <div class="text-center max-w-3xl mx-auto mb-16 reveal">
      <p class="text-[#D4FF3D] text-xs font-bold uppercase tracking-widest mb-2">
        {{ \App\Models\Setting::get('vyra_story_badge', 'The Brand') }}
      </p>
      <h1 class="font-display uppercase text-4xl lg:text-6xl text-[#F2F0EC] leading-tight">
        {!! nl2br(e(\App\Models\Setting::get('vyra_story_title', "Not Made to Blend In.\nMade to Rule."))) !!}
      </h1>
      <div class="flex justify-center mt-4">
        <div class="w-32 h-0.5 bg-gradient-to-r from-transparent via-[#FF3D5A] to-transparent"></div>
      </div>
    </div>

    {{-- ═══════════════════════════════════════════════
         OUR STORY  —  2-Column matching home page
         ═══════════════════════════════════════════════ --}}
    <div class="grid lg:grid-cols-2 gap-12 lg:gap-20 items-center mb-20">

      {{-- Left: story image frame --}}
      <div class="reveal flex justify-center lg:justify-start">
        <div class="aspect-[3/4] w-72 lg:w-96 border-2 border-[#FF3D5A] shadow-[0_0_25px_rgba(255,61,90,0.2)] relative overflow-hidden bg-[#1A1A1D]">
          @php
            $vyraStoryImg = \App\Models\Setting::get('vyra_story_image');
          @endphp
          @if($vyraStoryImg)
            <img src="{{ asset('storage/' . $vyraStoryImg) }}" alt="About Vyra" class="w-full h-full object-cover">
          @else
            <div class="w-full h-full bg-gradient-to-b from-[#1A1A1D] to-[#0E0E10] flex flex-col items-center justify-center p-8 text-center">
              <svg viewBox="0 0 100 120" class="w-24 h-28 text-[#FF3D5A]/40 mb-3" fill="currentColor">
                <ellipse cx="50" cy="25" rx="16" ry="20"/>
                <path d="M15 48 Q30 38 50 42 Q70 38 85 48 L88 105 Q70 115 50 112 Q30 115 12 105 Z"/>
              </svg>
              <p class="font-display uppercase text-sm font-bold text-[#F2F0EC]">Vyra Atelier</p>
              <p class="font-body text-xs text-[#D4FF3D] mt-1 font-mono">Wear Your Power</p>
            </div>
          @endif
        </div>
      </div>

      {{-- Right: story narrative + live stats --}}
      <div class="reveal">
        <p class="text-[#D4FF3D] text-xs font-bold uppercase tracking-widest mb-2 font-mono">{{ \App\Models\Setting::get('vyra_about_philosophy_badge', 'Philosophy 01') }}</p>
        <h2 class="font-display uppercase text-3xl lg:text-4xl text-[#F2F0EC] mb-6 leading-tight">
          {!! nl2br(e(\App\Models\Setting::get('vyra_about_philosophy_heading', 'Raw Edge. Zero Compromise.'))) !!}
        </h2>
        <p class="font-body text-[#F2F0EC]/80 leading-relaxed mb-5 text-sm lg:text-base">
          {{ \App\Models\Setting::get('vyra_story_p1', 'Vyra was created for those who treat style as armour. Born in the intersection of high fashion and street energy, every stitch is calibrated for raw confidence.') }}
        </p>
        <p class="font-body text-[#F2F0EC]/80 leading-relaxed mb-10 text-sm lg:text-base">
          {{ \App\Models\Setting::get('vyra_story_p2', 'Architectural seams, breathable sheer mesh, and unyielding contours. Designed without apologies, crafted for the fearless.') }}
        </p>

        {{-- 3 Stats --}}
        <div class="grid grid-cols-3 gap-4 border-t border-[#FF3D5A]/30 pt-8">
          @php
            $stat1Num = \App\Models\Setting::get('vyra_stat1_num', '0%');
            $stat1Lbl = \App\Models\Setting::get('vyra_stat1_label', 'Compromise');
            $stat2Num = \App\Models\Setting::get('vyra_stat2_num', '50K+');
            $stat2Lbl = \App\Models\Setting::get('vyra_stat2_label', 'Rebels Styled');
            $stat3Num = \App\Models\Setting::get('vyra_stat3_num', '100%');
            $stat3Lbl = \App\Models\Setting::get('vyra_stat3_label', 'Raw Edge');
          @endphp
          @foreach([[$stat1Num, $stat1Lbl], [$stat2Num, $stat2Lbl], [$stat3Num, $stat3Lbl]] as [$num, $label])
            <div class="text-center">
              <p class="font-display uppercase text-3xl lg:text-4xl text-[#D4FF3D]">{{ $num }}</p>
              <p class="font-body text-xs text-[#F2F0EC]/60 uppercase tracking-widest mt-1">{{ $label }}</p>
            </div>
          @endforeach
        </div>
      </div>

    </div>

    {{-- Neon divider --}}
    <div class="py-6 flex justify-center">
      <div class="w-48 h-0.5 bg-gradient-to-r from-transparent via-[#FF3D5A]/40 to-transparent"></div>
    </div>

    {{-- Brand Pillars --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 py-12">
      <div class="p-8 bg-[#1A1A1D] border border-[#FF3D5A]/20 shadow-xs space-y-3 reveal text-center">
        <div class="w-12 h-12 border border-[#D4FF3D] text-[#D4FF3D] flex items-center justify-center font-display text-xl mx-auto">01</div>
        <h3 class="font-display uppercase text-lg text-[#F2F0EC]">{{ \App\Models\Setting::get('vyra_pillar1_title', 'Architectural Seams') }}</h3>
        <p class="font-body text-xs text-[#F2F0EC]/60 leading-relaxed">{{ \App\Models\Setting::get('vyra_pillar1_desc', 'Precision-engineered sheer mesh cut lines that sculpt your silhouette with zero bulk and maximum comfort.') }}</p>
      </div>

      <div class="p-8 bg-[#1A1A1D] border border-[#FF3D5A]/20 shadow-xs space-y-3 reveal text-center">
        <div class="w-12 h-12 border border-[#FF3D5A] text-[#FF3D5A] flex items-center justify-center font-display text-xl mx-auto">02</div>
        <h3 class="font-display uppercase text-lg text-[#F2F0EC]">{{ \App\Models\Setting::get('vyra_pillar2_title', 'Midnight Noir Palette') }}</h3>
        <p class="font-body text-xs text-[#F2F0EC]/60 leading-relaxed">{{ \App\Models\Setting::get('vyra_pillar2_desc', 'Deep carbon blacks, gunmetal clasps, and electric accents built for nocturnal presence and unfiltered attitude.') }}</p>
      </div>

      <div class="p-8 bg-[#1A1A1D] border border-[#D4FF3D] text-[#D4FF3D] shadow-xs space-y-3 reveal text-center bg-transparent">
        <div class="w-12 h-12 border border-[#D4FF3D] text-[#D4FF3D] flex items-center justify-center font-display text-xl mx-auto">03</div>
        <h3 class="font-display uppercase text-lg text-[#F2F0EC]">{{ \App\Models\Setting::get('vyra_pillar3_title', 'Stealth Packaging') }}</h3>
        <p class="font-body text-xs text-[#F2F0EC]/60 leading-relaxed">{{ \App\Models\Setting::get('vyra_pillar3_desc', 'All deliveries arrive in sealed, blacked-out matte boxes with zero exterior brand graphics. Your privacy is non-negotiable.') }}</p>
      </div>
    </div>

    {{-- Call to action --}}
    <div class="text-center mt-12 mb-8 reveal">
      <a href="{{ route('search') }}" class="py-4 px-12 text-xs font-bold uppercase tracking-widest transition-all bg-[#FF3D5A] text-[#0E0E10] hover:bg-[#D4FF3D] inline-block">
        Explore Vyra Edit &rarr;
      </a>
    </div>

  </div>
</div>

</x-storefront-layout>
