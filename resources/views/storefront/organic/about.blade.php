{{--
    ======================================================================
    THEME: VERDIA — Botanical Living & Organic Produce Marketplace
    PAGE: ABOUT US & BRAND STORY
    ======================================================================
    File: resources/views/storefront/organic/about.blade.php
    Controller: App\Http\Controllers\StorefrontController@about
    Route Context: GET /about
    ViewModel Injections:
      - $theme: Active Template model ('organic')
      - $title: Page SEO title
    Responsibility: Brand manifesto, artisan heritage, craftsmanship, and sustainability
    Features:
      - Atmospheric hero imagery and founder's note
      - Artisan handcrafting timeline and ethical sourcing commitments
      - Brand milestone metrics and curated editorial lookbook links
    Operations & Maintenance:
      - Text content editable via admin settings or custom template language
--}}
@php
  $heroBadge = \App\Models\Setting::get('organic_about_badge', '🌱 Our Farming Manifesto');
  $heroTitle = \App\Models\Setting::get('organic_about_title', 'Bringing Honest, Pure & Chemical-Free Produce to Indian Homes');
  $heroSubtitle = \App\Models\Setting::get('organic_about_subtitle', 'Nexon Organic was born out of a profound commitment to human wellness and planetary health. We believe food should nourish, heal, and bring us closer to the rhythms of nature.');

  $storyBadge = \App\Models\Setting::get('organic_story_badge', 'The Farm-to-Fork Journey');
  $storyTitle = \App\Models\Setting::get('organic_story_title', 'Why We Refuse to Compromise on Soil Health');
  $storyP1 = \App\Models\Setting::get('organic_story_p1', 'Modern industrial farming relies on synthetic pesticides, chemical sprays, and artificial ripening agents that strip produce of essential micronutrients while poisoning aquifers.');
  $storyP2 = \App\Models\Setting::get('organic_story_p2', 'At Nexon Organic, we cultivate our fields using regenerative multi-cropping, biodiverse cow-based compost (Jeevamrutha), and solar pest traps. Our harvest is packed in breathable eco-crates and dispatched overnight so you receive farm-fresh vitality within 24 hours of cutting.');
  $storyImg = \App\Models\Setting::get('organic_story_image');

  $stat1Num = \App\Models\Setting::get('organic_stat1_num', '100%');
  $stat1Lbl = \App\Models\Setting::get('organic_stat1_label', 'Pesticide & Urea Free');
  $stat2Num = \App\Models\Setting::get('organic_stat2_num', '350+');
  $stat2Lbl = \App\Models\Setting::get('organic_stat2_label', 'Partner Organic Farmers');

  $pillar1Title = \App\Models\Setting::get('organic_pillar1_title', 'Living Soil First');
  $pillar1Desc  = \App\Models\Setting::get('organic_pillar1_desc', 'We nourish the living microorganisms in our soil using organic cow manure and compost, creating mineral-dense produce.');
  $pillar2Title = \App\Models\Setting::get('organic_pillar2_title', 'Indigenous Heirloom Seeds');
  $pillar2Desc  = \App\Models\Setting::get('organic_pillar2_desc', 'We preserve native Indian open-pollinated seed varieties that have naturally adapted over centuries to resist pests.');
  $pillar3Title = \App\Models\Setting::get('organic_pillar3_title', 'Fair Price to Farmers');
  $pillar3Desc  = \App\Models\Setting::get('organic_pillar3_desc', 'We eliminate predatory middlemen and pay our farmers a premium above mandi market rates directly to their bank accounts.');
  $pillar4Title = \App\Models\Setting::get('organic_pillar4_title', 'Zero Plastic Waste');
  $pillar4Desc  = \App\Models\Setting::get('organic_pillar4_desc', 'Deliveries arrive in 100% compostable cassava starch bags and unbleached corrugated kraft boxes.');
@endphp
<x-storefront-layout :theme="$theme" title="About Our Farm &amp; Organic Philosophy">

  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-16">
    
    {{-- Breadcrumbs --}}
    <nav class="text-xs text-stone-500 font-medium flex items-center gap-2 mb-8">
      <a href="{{ route('home') }}" class="hover:text-organic-600 transition">Home</a>
      <span>/</span>
      <span class="text-stone-900 font-bold">About Our Farm</span>
    </nav>

    {{-- Hero Section --}}
    <div class="text-center max-w-3xl mx-auto mb-16 reveal">
      <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold tracking-wider uppercase bg-organic-100 text-organic-700 border border-organic-300 mb-3">
        {{ $heroBadge }}
      </span>
      <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-stone-900 font-display leading-tight">
        {!! nl2br(e($heroTitle)) !!}
      </h1>
      <p class="mt-4 text-xs sm:text-sm text-stone-600 font-light leading-relaxed">
        {{ $heroSubtitle }}
      </p>
    </div>

    {{-- Story Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-center mb-20">
      <div class="space-y-5 reveal">
        <span class="text-xs font-bold uppercase tracking-wider text-organic-600 block">{{ $storyBadge }}</span>
        <h2 class="text-2xl sm:text-3xl font-black text-stone-900 font-display leading-snug">
          {!! nl2br(e($storyTitle)) !!}
        </h2>
        <p class="text-xs sm:text-sm text-stone-600 leading-relaxed font-light">
          {{ $storyP1 }}
        </p>
        <p class="text-xs sm:text-sm text-stone-600 leading-relaxed font-light">
          {{ $storyP2 }}
        </p>

        <div class="pt-4 border-t border-stone-200 grid grid-cols-2 gap-4">
          <div>
            <p class="text-2xl font-black text-organic-800 font-display">{{ $stat1Num }}</p>
            <p class="text-xs text-stone-500 font-semibold mt-0.5">{{ $stat1Lbl }}</p>
          </div>
          <div>
            <p class="text-2xl font-black text-organic-800 font-display">{{ $stat2Num }}</p>
            <p class="text-xs text-stone-500 font-semibold mt-0.5">{{ $stat2Lbl }}</p>
          </div>
        </div>
      </div>

      <div class="reveal">
        <div class="rounded-3xl overflow-hidden shadow-2xl border-4 border-white aspect-[4/3] bg-stone-100">
          @if($storyImg)
            <img src="{{ asset('storage/' . $storyImg) }}" alt="{{ $storyTitle }}" class="w-full h-full object-cover">
          @else
            <img src="{{ asset('organic/images/sample-image-2.jpg') }}" alt="Organic Indian farmers in fields" class="w-full h-full object-cover">
          @endif
        </div>
      </div>
    </div>

    {{-- 4 Core Pillars --}}
    <div class="mb-20">
      <div class="text-center max-w-xl mx-auto mb-12">
        <span class="text-xs font-bold uppercase tracking-wider text-organic-600 block mb-1">Our Commitments</span>
        <h2 class="text-2xl sm:text-3xl font-black text-stone-900 font-display">The Four Pillars of Nexon Organic</h2>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <div class="bg-white rounded-3xl p-6 border border-stone-200/90 shadow-xs space-y-3">
          <div class="w-12 h-12 rounded-2xl bg-organic-50 text-organic-600 flex items-center justify-center font-black text-xl">
            🌿
          </div>
          <h3 class="text-sm font-extrabold text-stone-900 font-display">{{ $pillar1Title }}</h3>
          <p class="text-xs text-stone-500 leading-relaxed font-light">
            {{ $pillar1Desc }}
          </p>
        </div>

        <div class="bg-white rounded-3xl p-6 border border-stone-200/90 shadow-xs space-y-3">
          <div class="w-12 h-12 rounded-2xl bg-organic-50 text-organic-600 flex items-center justify-center font-black text-xl">
            🌾
          </div>
          <h3 class="text-sm font-extrabold text-stone-900 font-display">{{ $pillar2Title }}</h3>
          <p class="text-xs text-stone-500 leading-relaxed font-light">
            {{ $pillar2Desc }}
          </p>
        </div>

        <div class="bg-white rounded-3xl p-6 border border-stone-200/90 shadow-xs space-y-3">
          <div class="w-12 h-12 rounded-2xl bg-organic-50 text-organic-600 flex items-center justify-center font-black text-xl">
            🤝
          </div>
          <h3 class="text-sm font-extrabold text-stone-900 font-display">{{ $pillar3Title }}</h3>
          <p class="text-xs text-stone-500 leading-relaxed font-light">
            {{ $pillar3Desc }}
          </p>
        </div>

        <div class="bg-white rounded-3xl p-6 border border-stone-200/90 shadow-xs space-y-3">
          <div class="w-12 h-12 rounded-2xl bg-organic-50 text-organic-600 flex items-center justify-center font-black text-xl">
            📦
          </div>
          <h3 class="text-sm font-extrabold text-stone-900 font-display">{{ $pillar4Title }}</h3>
          <p class="text-xs text-stone-500 leading-relaxed font-light">
            {{ $pillar4Desc }}
          </p>
        </div>

      </div>
    </div>

    {{-- CTA --}}
    <div class="rounded-3xl bg-organic-900 text-white p-8 sm:p-12 text-center max-w-4xl mx-auto shadow-xl">
      <h2 class="text-2xl sm:text-3xl font-black font-display mb-3 text-white">Taste the Difference of Real Organic Food</h2>
      <p class="text-xs sm:text-sm text-organic-200 font-light max-w-xl mx-auto mb-6">
        Switch your weekly pantry basket to clean, chemical-free nutrition. Delivered fresh to your doorstep across India.
      </p>
      <a href="{{ route('home') }}#products" class="btn-organic inline-block px-8 py-3.5 rounded-full text-xs font-bold uppercase tracking-wider shadow-lg">
        Start Your Fresh Basket
      </a>
    </div>

  </div>

</x-storefront-layout>

