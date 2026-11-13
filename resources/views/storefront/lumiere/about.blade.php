{{--
    ======================================================================
    THEME: LUMIÈRE — French Silk Atelier & Minimalist Luxury
    PAGE: ABOUT US & BRAND STORY
    ======================================================================
    File: resources/views/storefront/lumiere/about.blade.php
    Controller: App\Http\Controllers\StorefrontController@about
    Route Context: GET /about
    ViewModel Injections:
      - $theme: Active Template model ('lumiere')
      - $title: Page SEO title
    Responsibility: Brand manifesto, artisan heritage, craftsmanship, and sustainability
    Features:
      - Atmospheric hero imagery and founder's note
      - Artisan handcrafting timeline and ethical sourcing commitments
      - Brand milestone metrics and curated editorial lookbook links
    Operations & Maintenance:
      - Text content editable via admin settings or custom template language
--}}
<x-storefront-layout :theme="$theme" title="Our Story — Lumière Atelier" metaDesc="Lumière crafts sustainably made silk lingerie, finished by hand by master artisans in India. Everyday elegance, made responsibly.">

<div class="py-12 lg:py-20 bg-white">
  <div class="max-w-7xl mx-auto px-6 md:px-12">

    {{-- Breadcrumb --}}
    <nav class="text-xs flex items-center gap-2 mb-8 text-gray-400">
      <a href="{{ route('home') }}" class="hover:text-pink-600 transition-colors">Home</a>
      <span>/</span>
      <span class="text-gray-900 font-medium">Our Story</span>
    </nav>

    {{-- Header --}}
    <div class="text-center max-w-3xl mx-auto mb-16 reveal">
      <p class="text-pink-600 text-[10px] font-black uppercase tracking-widest mb-2">
        {{ \App\Models\Setting::get('lumiere_story_badge', 'Our Story') }}
      </p>
      <h1 class="font-display italic text-4xl lg:text-6xl text-gray-900 leading-tight">
        {!! nl2br(e(\App\Models\Setting::get('lumiere_story_title', "Everyday elegance,\nmade responsibly"))) !!}
      </h1>
      <div class="flex justify-center mt-4">
        <div class="w-32 h-0.5 bg-gradient-to-r from-transparent via-pink-300 to-transparent"></div>
      </div>
    </div>

    {{-- ═══════════════════════════════════════════════
         OUR STORY  —  2-Column matching home page
         ═══════════════════════════════════════════════ --}}
    <div class="grid lg:grid-cols-2 gap-12 lg:gap-20 items-center mb-20">

      {{-- Left: story image frame --}}
      <div class="reveal flex justify-center lg:justify-start">
        <div class="aspect-[3/4] w-72 lg:w-96 rounded-[40px] overflow-hidden border-2 border-pink-200 shadow-xl relative bg-[#FFF0F3]">
          @php
            $lumStoryImg = \App\Models\Setting::get('lumiere_story_image');
          @endphp
          @if($lumStoryImg)
            <img src="{{ \App\Models\Setting::imageUrl($lumStoryImg) }}" alt="About Lumière" class="w-full h-full object-cover">
          @else
            <div class="w-full h-full bg-gradient-to-b from-[#FFF0F3] to-white flex flex-col items-center justify-center p-8 text-center border border-pink-100">
              <svg viewBox="0 0 100 120" class="w-24 h-28 text-pink-400 opacity-60 mb-3" fill="currentColor">
                <ellipse cx="50" cy="25" rx="16" ry="20"/>
                <path d="M15 48 Q30 38 50 42 Q70 38 85 48 L88 105 Q70 115 50 112 Q30 115 12 105 Z"/>
              </svg>
              <p class="font-display italic text-lg font-bold text-gray-800">Lumière Atelier</p>
              <p class="text-xs text-gray-500 mt-1">Silk that feels like light</p>
            </div>
          @endif
        </div>
      </div>

      {{-- Right: story narrative + live stats --}}
      <div class="reveal">
        <p class="text-pink-600 text-[10px] font-black uppercase tracking-widest mb-2">{{ \App\Models\Setting::get('lumiere_about_philosophy_badge', 'Artisanal Roots') }}</p>
        <h2 class="font-display italic text-3xl lg:text-4xl text-gray-900 mb-6 leading-tight">
          {{ \App\Models\Setting::get('lumiere_about_philosophy_heading', 'Hand-Finished Pure Mulberry Silk') }}
        </h2>
        <p class="text-gray-600 leading-relaxed mb-5 text-sm lg:text-base">
          {{ \App\Models\Setting::get('lumiere_story_p1', 'Lumière began with a single loom and a simple belief: the things closest to your skin should be the best you own. Every piece is cut from sustainably sourced silk and finished by artisans who have done this for generations.') }}
        </p>
        <p class="text-gray-600 leading-relaxed mb-10 text-sm lg:text-base">
          {{ \App\Models\Setting::get('lumiere_story_p2', 'We work directly with master craftspeople in heritage clusters across India, guaranteeing fair wages and keeping age-old textile traditions alive in contemporary silhouettes.') }}
        </p>

        {{-- 3 Stats --}}
        <div class="grid grid-cols-3 gap-4 border-t border-pink-100 pt-8">
          @php
            $stat1Num = \App\Models\Setting::get('lumiere_stat1_num', '100%');
            $stat1Lbl = \App\Models\Setting::get('lumiere_stat1_label', 'Pure Silk');
            $stat2Num = \App\Models\Setting::get('lumiere_stat2_num', '300+');
            $stat2Lbl = \App\Models\Setting::get('lumiere_stat2_label', 'Artisans');
            $stat3Num = \App\Models\Setting::get('lumiere_stat3_num', '12');
            $stat3Lbl = \App\Models\Setting::get('lumiere_stat3_label', 'Cities');
          @endphp
          @foreach([[$stat1Num, $stat1Lbl], [$stat2Num, $stat2Lbl], [$stat3Num, $stat3Lbl]] as [$num, $label])
            <div class="text-center">
              <p class="font-display italic text-3xl lg:text-4xl text-pink-600">{{ $num }}</p>
              <p class="text-[10px] uppercase tracking-widest text-gray-400 mt-1">{{ $label }}</p>
            </div>
          @endforeach
        </div>
      </div>

    </div>

    {{-- Silk divider --}}
    <div class="py-6 flex justify-center">
      <div class="w-48 h-0.5 bg-gradient-to-r from-transparent via-pink-300 to-transparent"></div>
    </div>

    {{-- Brand Pillars --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 py-12">
      <div class="p-8 bg-[#FFF0F3] rounded-[32px] border border-pink-100 shadow-xs space-y-3 reveal text-center">
        <div class="w-12 h-12 rounded-full bg-white text-pink-600 flex items-center justify-center font-display italic text-xl mx-auto shadow-xs">1</div>
        <h3 class="font-display italic text-xl text-gray-900">{{ \App\Models\Setting::get('lumiere_pillar1_title', 'Mulberry Silk Quality') }}</h3>
        <p class="text-gray-600 text-xs leading-relaxed">{{ \App\Models\Setting::get('lumiere_pillar1_desc', 'Featherweight, breathable Grade 6A pure mulberry silk naturally hypoallergenic and temperature-regulating.') }}</p>
      </div>

      <div class="p-8 bg-[#FFF0F3] rounded-[32px] border border-pink-100 shadow-xs space-y-3 reveal text-center">
        <div class="w-12 h-12 rounded-full bg-white text-pink-600 flex items-center justify-center font-display italic text-xl mx-auto shadow-xs">2</div>
        <h3 class="font-display italic text-xl text-gray-900">{{ \App\Models\Setting::get('lumiere_pillar2_title', 'Fair-Wage Handcraft') }}</h3>
        <p class="text-gray-600 text-xs leading-relaxed">{{ \App\Models\Setting::get('lumiere_pillar2_desc', 'Honouring craftspeople in historic artisan clusters with fair wages, safe ateliers, and sustainable working standards.') }}</p>
      </div>

      <div class="p-8 bg-[#FFF0F3] rounded-[32px] border border-pink-100 shadow-xs space-y-3 reveal text-center">
        <div class="w-12 h-12 rounded-full bg-white text-pink-600 flex items-center justify-center font-display italic text-xl mx-auto shadow-xs">3</div>
        <h3 class="font-display italic text-xl text-gray-900">{{ \App\Models\Setting::get('lumiere_pillar3_title', 'Zero-Plastic Keepsakes') }}</h3>
        <p class="text-gray-600 text-xs leading-relaxed">{{ \App\Models\Setting::get('lumiere_pillar3_desc', 'Delivered in unmarked, satin-tied reusable presentation boxes. Minimal environmental footprint, absolute discretion.') }}</p>
      </div>
    </div>

    {{-- Call to action --}}
    <div class="text-center mt-12 mb-8 reveal">
      <a href="{{ route('search') }}" class="px-10 py-4 rounded-2xl text-xs font-bold uppercase tracking-widest bg-[#405549] text-white hover:bg-pink-600 transition-colors shadow-sm inline-block">
        Explore Curated Edit ✨
      </a>
    </div>

  </div>
</div>

</x-storefront-layout>
