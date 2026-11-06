{{-- resources/views/storefront/saaya/about.blade.php --}}
<x-storefront-layout :theme="$theme" title="About Saaya — The Atelier Story" metaDesc="Discover the heritage of Saaya intimate atelier, handcrafted with pure mulberry silk, zari embroidery, and heirloom silhouettes.">

<div class="bg-ivory py-10 lg:py-16">
  <div class="max-w-7xl mx-auto px-6 lg:px-12">

    {{-- Breadcrumb --}}
    <nav class="text-xs flex items-center gap-2 mb-8 text-maroon/50 font-body">
      <a href="{{ route('home') }}" class="hover:text-gold transition-colors">Home</a>
      <span>/</span>
      <span class="text-maroon font-medium">About Saaya</span>
    </nav>

    {{-- Hero Heading --}}
    <div class="text-center max-w-3xl mx-auto mb-16 reveal">
      <p class="font-display italic text-gold text-lg lg:text-xl mb-2">
        {{ \App\Models\Setting::get('saaya_story_badge', 'Our Story') }}
      </p>
      <h1 class="font-display text-4xl lg:text-6xl text-maroon leading-tight">
        {!! nl2br(e(\App\Models\Setting::get('saaya_story_title', "A Love Letter\nto Indian Femininity"))) !!}
      </h1>
      <div class="flex justify-center mt-4">
        <div class="petal-divider w-32 opacity-60"></div>
      </div>
    </div>

    {{-- ═══════════════════════════════════════════════
         OUR STORY  —  2-Column matching home page
         ═══════════════════════════════════════════════ --}}
    <div class="grid lg:grid-cols-2 gap-12 lg:gap-20 items-center mb-20">

      {{-- Left: foil-frame story image --}}
      <div class="reveal flex justify-center lg:justify-start">
        <div class="foil-frame w-72 h-96 lg:w-96 lg:h-[480px] relative overflow-hidden shadow-2xl">
          <svg class="frame-line" viewBox="0 0 100 100" preserveAspectRatio="none">
            <rect x="2" y="2" width="96" height="96" vector-effect="non-scaling-stroke"/>
          </svg>
          @php
            $saayaStoryImg = \App\Models\Setting::get('saaya_story_image');
          @endphp
          @if($saayaStoryImg)
            <img src="{{ asset('storage/' . $saayaStoryImg) }}" alt="Our Story" class="w-full h-full object-cover">
          @else
            <div class="w-full h-full bg-gradient-to-b from-blush/30 to-maroon/20 flex flex-col items-center justify-center p-8 text-center">
              <svg class="w-24 h-32 text-maroon/25 mb-4" fill="currentColor" viewBox="0 0 80 110">
                <ellipse cx="40" cy="22" rx="14" ry="18"/>
                <path d="M10 45 Q25 35 40 38 Q55 35 70 45 L72 95 Q55 105 40 102 Q25 105 8 95 Z"/>
              </svg>
              <p class="font-display text-base font-bold text-maroon">Saaya Atelier</p>
              <p class="font-body text-xs text-maroon/60 mt-1">Draped in Gold &amp; Grace</p>
            </div>
          @endif
        </div>
      </div>

      {{-- Right: story narrative + live stats --}}
      <div class="reveal">
        <p class="font-display italic text-gold text-lg mb-2">{{ \App\Models\Setting::get('saaya_about_philosophy_badge', 'Heritage & Mastery') }}</p>
        <h2 class="font-display text-3xl lg:text-4xl text-maroon mb-6 leading-tight">
          {{ \App\Models\Setting::get('saaya_about_philosophy_heading', 'Where Ancient Zari Craft Meets Modern Contour') }}
        </h2>
        <p class="font-body text-maroon/80 leading-relaxed mb-5 text-sm lg:text-base">
          {{ \App\Models\Setting::get('saaya_story_p1', 'Saaya was born from a belief that every Indian woman deserves lingerie that honours her form and her heritage. We weave together age-old motifs — jaali embroidery, zari threadwork, lotus prints — with modern fits engineered for real bodies.') }}
        </p>
        <p class="font-body text-maroon/80 leading-relaxed mb-10 text-sm lg:text-base">
          {{ \App\Models\Setting::get('saaya_story_p2', 'Every piece leaves our atelier only after passing through the hands of our master karigars, who carry forward skills passed down through generations.') }}
        </p>

        {{-- 3 Stats --}}
        <div class="grid grid-cols-3 gap-4 border-t border-gold/30 pt-8">
          @php
            $stat1Num = \App\Models\Setting::get('saaya_stat1_num', '12+');
            $stat1Lbl = \App\Models\Setting::get('saaya_stat1_label', 'Years of Craft');
            $stat2Num = \App\Models\Setting::get('saaya_stat2_num', '80K+');
            $stat2Lbl = \App\Models\Setting::get('saaya_stat2_label', 'Brides Adorned');
            $stat3Num = \App\Models\Setting::get('saaya_stat3_num', '450+');
            $stat3Lbl = \App\Models\Setting::get('saaya_stat3_label', 'Karigars');
          @endphp
          @foreach([[$stat1Num, $stat1Lbl], [$stat2Num, $stat2Lbl], [$stat3Num, $stat3Lbl]] as [$num, $label])
            <div class="text-center">
              <p class="font-display text-3xl lg:text-4xl text-gold">{{ $num }}</p>
              <p class="font-body text-xs text-maroon/70 mt-1 leading-snug">{{ $label }}</p>
            </div>
          @endforeach
        </div>
      </div>

    </div>

    {{-- Petal divider --}}
    <div class="py-6 flex justify-center">
      <div class="petal-divider w-48 opacity-50"></div>
    </div>

    {{-- Brand Pillars --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 py-12">
      <div class="p-8 bg-white border border-gold/20 shadow-xs space-y-3 reveal text-center">
        <div class="w-12 h-12 rounded-full bg-gold/10 text-gold flex items-center justify-center font-display text-xl mx-auto">1</div>
        <h3 class="font-display text-xl text-maroon">{{ \App\Models\Setting::get('saaya_pillar1_title', 'Pure Mulberry Silk') }}</h3>
        <p class="font-body text-xs text-maroon/70 leading-relaxed">{{ \App\Models\Setting::get('saaya_pillar1_desc', 'Sustainably sourced Grade 6A mulberry silk that breathes against the skin, delivering regal drape and featherlight comfort.') }}</p>
      </div>

      <div class="p-8 bg-white border border-gold/20 shadow-xs space-y-3 reveal text-center">
        <div class="w-12 h-12 rounded-full bg-gold/10 text-gold flex items-center justify-center font-display text-xl mx-auto">2</div>
        <h3 class="font-display text-xl text-maroon">{{ \App\Models\Setting::get('saaya_pillar2_title', 'Master Karigar Craft') }}</h3>
        <p class="font-body text-xs text-maroon/70 leading-relaxed">{{ \App\Models\Setting::get('saaya_pillar2_desc', 'Each seam is hand-finished by master craftspeople across Rajasthan and Bengal, preserving centuries-old artisanal zardozi techniques.') }}</p>
      </div>

      <div class="p-8 bg-white border border-gold/20 shadow-xs space-y-3 reveal text-center">
        <div class="w-12 h-12 rounded-full bg-gold/10 text-gold flex items-center justify-center font-display text-xl mx-auto">3</div>
        <h3 class="font-display text-xl text-maroon">{{ \App\Models\Setting::get('saaya_pillar3_title', 'Discreet Heirloom Packaging') }}</h3>
        <p class="font-body text-xs text-maroon/70 leading-relaxed">{{ \App\Models\Setting::get('saaya_pillar3_desc', 'Your intimacy is sacred. Every order is encased in unmarked, gold-embossed matte keepsakes with zero external brand indicators.') }}</p>
      </div>
    </div>

    {{-- Call to action --}}
    <div class="text-center mt-12 mb-8 reveal">
      <a href="{{ route('search') }}" class="gold-btn py-4 px-12 text-xs font-body font-semibold tracking-widest uppercase inline-block shadow-md">
        Explore Curated Edit &rarr;
      </a>
    </div>

  </div>
</div>

</x-storefront-layout>
