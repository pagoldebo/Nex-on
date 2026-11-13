{{--
    ======================================================================
    THEME: LUMIÈRE — HOMEPAGE TEMPLATE
    ======================================================================
    File: resources/views/storefront/lumiere/index.blade.php
    Controller: App\Http\Controllers\StorefrontController@index
    ViewModel Injections:
      - $theme:            Active Template model ('lumiere')
      - $categories:       Hierarchical luxury categories
      - $featured:         Curated silk picks (Lustre Edit)
      - $bestsellers:      High-demand silk slips & bralettes
      - $newArrivals:      Festive new silk silhouettes
    Section Architecture:
      1. HERO:            Warm butter gold canvas with silk teaser illustration
      2. SCROLLING BAND:  Global announcement marquee (storefront.common.scrolling-band)
      3. SHOP BY CAT:     Centered carousel with flanking left & right navigation buttons
      4. CURATED PICKS:   Horizontal Lustre Edit rail with quick-view modal triggers
      5. BEST SELLERS:    Tab-filtered grid with hover image transitions
      6. SILK ATELIER:    Mulberry silk quality, fair-wage handcraft & keepsakes
      7. CLIENT VOICES:   Editorial reviews & verified testimonials
      8. CONCIERGE:       Private styling consultation form
--}}
<x-storefront-layout :theme="$theme" title="Home">

<style>
  .animate-fade { animation: pageFadeIn .5s ease both; }
  #curatedPicks .scroll-item, #bsGrid .scroll-item { flex: initial; width: 100%; }
</style>

{{-- ===== 1. HERO (Full Width Banner) ===== --}}
@php
  $lumiereHeroRaw = \App\Models\Setting::get('lumiere_hero_image') ?: \App\Models\Setting::get('hero_banner_image');
  $lumiereHeroImg = $lumiereHeroRaw ? \App\Models\Setting::imageUrl($lumiereHeroRaw) : null;
  $lumiereBadge   = \App\Models\Setting::get('lumiere_hero_badge', 'The Festive Edit');
  $lumiereTitle   = \App\Models\Setting::get('lumiere_hero_title', "Silk that\nfeels like light.");
  $lumiereSub     = \App\Models\Setting::get('lumiere_hero_subtitle', 'Sustainably made silk lingerie, finished by hand in India. Everyday elegance, no compromise.');
@endphp

<section class="relative bg-[#FFE6A5] overflow-hidden min-h-[580px] sm:min-h-[660px] lg:min-h-[760px] flex items-center select-none w-full">
  
  {{-- Full Width Hero Banner Image / Background --}}
  @if($lumiereHeroImg)
    <div class="absolute inset-0 w-full h-full overflow-hidden">
      <img src="{{ $lumiereHeroImg }}" alt="Lumière Hero Banner" class="w-full h-full object-cover object-center">
    </div>
  @else
    {{-- Fallback: Haute Couture Silk Artwork & Sunlight Canvas across Full Width --}}
    <div class="absolute inset-0 w-full h-full bg-gradient-to-br from-[#FFE6A5] via-[#FFF0F3] to-[#FFD7BE] flex items-center justify-end pr-10 lg:pr-32 opacity-40 pointer-events-none">
      <svg viewBox="0 0 400 480" class="w-80 h-96 lg:w-[460px] lg:h-[540px]">
        <ellipse cx="200" cy="110" rx="95" ry="75" fill="#DB2777" opacity="0.45"/>
        <path d="M120 130 Q200 190 280 130 L275 180 Q200 235 125 180 Z" fill="#DB2777" opacity="0.65"/>
        <path d="M135 250 Q200 290 265 250 L260 410 Q200 450 140 410 Z" fill="#DB2777" opacity="0.3"/>
      </svg>
    </div>
  @endif

  {{-- Silk Gradient Scrim / Overlay for Crystal-Clear Text Contrast --}}
  <div class="absolute inset-0 z-10 pointer-events-none bg-gradient-to-r from-[#FFFAF5]/95 via-[#FFFAF5]/85 to-[#FFFAF5]/40 md:to-transparent"></div>
  <div class="absolute inset-0 z-10 pointer-events-none bg-gradient-to-t from-[#FFFAF5]/60 via-transparent to-transparent"></div>

  {{-- Hero Content (Badge, Editorial Title, Subtitle & Buttons) --}}
  <div class="relative z-20 max-w-7xl mx-auto px-6 md:px-12 py-16 sm:py-24 w-full">
    <div class="max-w-xl reveal">
      @if($lumiereBadge)
        <p class="text-pink-600 text-xs font-black uppercase tracking-widest mb-4 flex items-center gap-2 drop-shadow-xs">
          <span class="w-2 h-2 rounded-full bg-pink-600 inline-block animate-pulse"></span>
          <span>{{ $lumiereBadge }}</span>
        </p>
      @endif

      <h1 class="font-display italic text-4xl sm:text-5xl md:text-6xl lg:text-7xl text-gray-900 leading-[1.08] mb-6 drop-shadow-xs">
        {!! nl2br(e($lumiereTitle)) !!}
      </h1>

      <p class="text-gray-800 text-sm sm:text-base mb-8 leading-relaxed font-normal max-w-lg drop-shadow-2xs">
        {{ $lumiereSub }}
      </p>

      <div class="flex flex-wrap gap-4 items-center">
        <a href="#curatedPicks" class="px-8 py-3.5 rounded-2xl text-xs font-bold uppercase tracking-widest bg-[#405549] text-white hover:bg-pink-600 transition-all shadow-md hover:shadow-lg hover:-translate-y-0.5">
          Shop the Edit
        </a>
        <a href="{{ route('search') }}" class="px-8 py-3.5 rounded-2xl text-xs font-bold uppercase tracking-widest border-2 border-[#405549] text-[#405549] hover:bg-[#405549] hover:text-white transition-all shadow-sm">
          Explore All
        </a>
      </div>
    </div>
  </div>

</section>

{{-- Scrolling Announcement Band --}}
@include('storefront.common.scrolling-band')

{{-- Delicate Silk Divider --}}
<div class="bg-[#FFF0F3] py-3 flex justify-center">
  <div class="w-48 h-0.5 bg-gradient-to-r from-transparent via-pink-300 to-transparent"></div>
</div>

{{-- ===== 2. SHOP BY CATEGORY ===== --}}
<section class="py-12 lg:py-16 px-6 md:px-12 bg-[#FFF0F3]"
         x-data="{
           canScrollLeft: false,
           canScrollRight: false,
           checkScroll() {
             const el = this.$refs.catTrack;
             if (!el) return;
             this.canScrollLeft = el.scrollLeft > 5;
             this.canScrollRight = el.scrollLeft < (el.scrollWidth - el.clientWidth - 5);
           },
           scrollTrack(direction) {
             const el = this.$refs.catTrack;
             if (!el) return;
             const items = el.querySelectorAll('.category-item');
             if (!items.length) return;

             const firstItem = items[0];
             const itemWidth = firstItem.getBoundingClientRect().width;
             const gap = parseFloat(window.getComputedStyle(el).gap) || 20;
             const step = itemWidth + gap;

             if (step <= 0) return;

             // Calculate how many items fit in the visible viewport
             const visibleItems = Math.max(1, Math.floor((el.clientWidth + gap) / step));
             const scrollCount = visibleItems > 1 ? Math.min(visibleItems - 1, 4) : 1;

             // Compute exact snap target to avoid halfway cropping
             const currentSnapIndex = Math.round(el.scrollLeft / step);
             const targetSnapIndex = Math.max(0, currentSnapIndex + (direction * scrollCount));
             const targetLeft = targetSnapIndex * step;

             el.scrollTo({
               left: targetLeft,
               behavior: 'smooth'
             });

             setTimeout(() => this.checkScroll(), 400);
           }
         }"
         x-init="$nextTick(() => { checkScroll(); }); window.addEventListener('resize', () => checkScroll());">
  <div class="max-w-7xl mx-auto">
    {{-- Header: Centered Category Title --}}
    <div class="text-center mb-8 sm:mb-10">
      <p class="text-pink-600 text-[10px] font-black uppercase tracking-widest mb-1.5">Browse</p>
      <h2 class="font-display italic text-2xl sm:text-3xl md:text-4xl text-gray-900">Shop by Category</h2>
    </div>

    {{-- Carousel: Left Arrow + Items Track + Right Arrow --}}
    <div class="relative flex items-center gap-2 sm:gap-4">
      {{-- Left Arrow (Left Side) --}}
      <button type="button" 
              @click="scrollTrack(-1)" 
              class="shrink-0 w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-pink-600 hover:bg-pink-700 text-white flex items-center justify-center transition text-xs font-bold shadow-2xs focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed z-10" 
              :disabled="!canScrollLeft"
              aria-label="Previous categories">
        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
      </button>

      {{-- Category Items Track --}}
      <div x-ref="catTrack" 
           @scroll.passive="checkScroll()"
           class="flex-1 flex items-start gap-4 sm:gap-6 lg:gap-8 overflow-x-auto scroll-smooth scrollbar-none pb-2 pt-1"
           :class="(canScrollLeft || canScrollRight) ? 'justify-start' : 'justify-center'"
           style="scrollbar-width: none; -ms-overflow-style: none; scroll-snap-type: x mandatory;">
        @php
          $parentCats = $categories->filter(fn($c) => is_null($c->parent_id));
        @endphp
        @forelse($parentCats as $cat)
          <a href="{{ route('category.show', $cat->slug) }}" 
             class="category-item group shrink-0 min-w-[96px] sm:min-w-[110px] max-w-[140px] flex flex-col items-center text-center transition"
             style="scroll-snap-align: start; scroll-snap-stop: normal;">
            <div class="w-20 h-20 sm:w-24 sm:h-24 md:w-28 md:h-28 rounded-full overflow-hidden mb-3 bg-pink-50 border-2 border-pink-200 group-hover:border-pink-500 shadow-2xs group-hover:scale-105 group-hover:shadow-md transition-all duration-300 flex items-center justify-center">
              @if($cat->image_url)
                <img src="{{ $cat->image_url }}" 
                     alt="{{ $cat->name }}" 
                     class="w-full h-full object-cover rounded-full" 
                     loading="lazy">
              @else
                <svg viewBox="0 0 60 60" class="w-9 h-9"><circle cx="30" cy="30" r="20" fill="#DB2777" opacity="0.35"/></svg>
              @endif
            </div>
            <h3 class="font-bold text-xs sm:text-sm text-gray-800 group-hover:text-pink-600 transition-colors leading-snug px-1 line-clamp-2 max-w-[125px]">
              {{ $cat->name }}
            </h3>
          </a>
        @empty
          @foreach(['Silk Robes', 'Lace Bralettes', 'Bodysuits', 'Nightwear', 'Bridal Edit', 'Mulberry Sets'] as $fallbackName)
            <a href="{{ route('featured.products') }}" 
               class="category-item group shrink-0 min-w-[96px] sm:min-w-[110px] max-w-[140px] flex flex-col items-center text-center transition"
               style="scroll-snap-align: start; scroll-snap-stop: normal;">
              <div class="w-20 h-20 sm:w-24 sm:h-24 md:w-28 md:h-28 rounded-full overflow-hidden mb-3 bg-pink-50 border-2 border-pink-200 group-hover:border-pink-500 shadow-2xs group-hover:scale-105 group-hover:shadow-md transition-all duration-300 flex items-center justify-center">
                <svg viewBox="0 0 60 60" class="w-9 h-9"><circle cx="30" cy="30" r="20" fill="#DB2777" opacity="0.35"/></svg>
              </div>
              <h3 class="font-bold text-xs sm:text-sm text-gray-800 group-hover:text-pink-600 transition-colors leading-snug px-1 line-clamp-2 max-w-[125px]">
                {{ $fallbackName }}
              </h3>
            </a>
          @endforeach
        @endforelse
      </div>

      {{-- Right Arrow (Right Side) --}}
      <button type="button" 
              @click="scrollTrack(1)" 
              class="shrink-0 w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-pink-600 hover:bg-pink-700 text-white flex items-center justify-center transition text-xs font-bold shadow-2xs focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed z-10" 
              :disabled="!canScrollRight"
              aria-label="Next categories">
        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
      </button>
    </div>
  </div>
</section>

{{-- Delicate Silk Divider --}}
<div class="bg-white py-3 flex justify-center">
  <div class="w-48 h-0.5 bg-gradient-to-r from-transparent via-pink-300 to-transparent"></div>
</div>

{{-- ===== 3. CURATED FEATURED PICKS ===== --}}
@php
  $curatedProducts = ($featured && $featured->count()) ? $featured : $featuredProducts;
@endphp
@if($curatedProducts && $curatedProducts->count())
<section id="curatedPicks" class="py-16 lg:py-24 px-6 md:px-12 bg-white">
  <div class="max-w-7xl mx-auto">
    <div class="text-center mb-12 reveal">
      <p class="text-pink-600 text-[10px] font-black uppercase tracking-widest mb-2">
        {{ \App\Models\Setting::get('lumiere_curated_badge', 'Curated') }}
      </p>
      <h2 class="font-display italic text-3xl md:text-5xl text-gray-900">
        {{ \App\Models\Setting::get('lumiere_curated_title', 'Curated Featured Picks') }}
      </h2>
      <p class="text-gray-500 text-xs md:text-sm max-w-xl mx-auto mt-2 leading-relaxed">
        {{ \App\Models\Setting::get('lumiere_curated_subtitle', 'Hand-finished pure mulberry silk, delicate French lace, and heirloom silhouettes curated for effortless elegance.') }}
      </p>
      <div class="flex justify-center mt-3">
        <div class="w-24 h-0.5 bg-gradient-to-r from-transparent via-pink-300 to-transparent"></div>
      </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-x-5 gap-y-10">
      @foreach($curatedProducts->take(8) as $product)
        <div class="reveal">
          @include('storefront.lumiere.partials.lustre-card', ['product' => $product])
        </div>
      @endforeach
    </div>

    <div class="text-center mt-12 reveal">
      <a href="{{ route('featured.products') }}" class="px-8 py-3.5 rounded-2xl text-xs font-bold uppercase tracking-widest bg-[#405549] text-white hover:bg-pink-600 transition-colors shadow-sm inline-block">
        Explore Curated Edit ✨
      </a>
    </div>
  </div>
</section>
@endif

{{-- Delicate Silk Divider --}}
<div class="bg-[#FFF0F3] py-3 flex justify-center">
  <div class="w-48 h-0.5 bg-gradient-to-r from-transparent via-pink-300 to-transparent"></div>
</div>

{{-- ═══════════════════════════════════════════════
     FLASH SALE / SPECIAL OFFERS SECTION (REF IMAGE DESIGN)
═══════════════════════════════════════════════ --}}
@if(isset($saleProducts) && $saleProducts->count())
<section class="py-16 lg:py-24 px-6 md:px-12 bg-white" id="saleSection">
  <div class="max-w-7xl mx-auto">
    <div class="text-center mb-10 reveal">
      <p class="text-pink-600 text-[10px] font-black uppercase tracking-widest mb-2">{{ \App\Models\Setting::get('lumiere_sale_kicker', 'Limited Time Deals') }}</p>
      <h2 class="font-display italic text-3xl md:text-5xl text-gray-900">{{ \App\Models\Setting::get('lumiere_sale_title', 'Special Sale Offers') }}</h2>
      <p class="text-gray-500 text-xs md:text-sm max-w-xl mx-auto mt-2 leading-relaxed">{{ \App\Models\Setting::get('lumiere_sale_subtitle', 'Hand-finished French lace, fine silk, and fine nightwear at celebratory promotional prices.') }}</p>
    </div>

    {{-- Category filter tabs matching Reference Design --}}
    <div class="flex flex-wrap justify-center gap-2 mb-10 reveal" id="saleFilterWrap">
      <button class="sale-tab active text-xs font-bold uppercase tracking-widest px-5 py-2.5 rounded-2xl border border-pink-500 text-pink-600 bg-pink-50 transition-all shadow-sm" data-filter="all">All</button>
      @foreach($categories->take(6) as $cat)
        <button class="sale-tab text-xs font-bold uppercase tracking-widest px-5 py-2.5 rounded-2xl border border-pink-200 text-gray-600 hover:border-pink-500 hover:text-pink-600 transition-all"
                data-filter="{{ $cat->slug }}">{{ $cat->name }}</button>
      @endforeach
    </div>

    {{-- Products grid --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-x-5 gap-y-10" id="saleGrid">
      @foreach($saleProducts->take(8) as $product)
        <div class="sale-card reveal" data-category="{{ $product->category_slugs_string }}">
          @include('storefront.lumiere.partials.sale-card', ['product' => $product])
        </div>
      @endforeach
    </div>

    <div class="text-center mt-12 reveal">
      <a href="{{ route('collections.sale') }}" class="px-8 py-3.5 rounded-2xl text-xs font-bold uppercase tracking-widest border border-pink-300 text-gray-800 hover:bg-pink-600 hover:text-white transition-all inline-block shadow-sm">
        Explore All Deals ✨
      </a>
    </div>
  </div>
</section>

{{-- Delicate Silk Divider --}}
<div class="bg-[#FFF0F3] py-3 flex justify-center">
  <div class="w-48 h-0.5 bg-gradient-to-r from-transparent via-pink-300 to-transparent"></div>
</div>
@endif

{{-- ===== 4. BEST SELLERS (WITH CATEGORY FILTER TABS) ===== --}}
@php
  $bestSellerItems = ($bestsellers && $bestsellers->count()) ? $bestsellers : $featuredProducts;
@endphp
@if($bestSellerItems && $bestSellerItems->count())
<section class="py-16 lg:py-24 px-6 md:px-12 bg-[#FFF0F3]" id="bestSellers">
  <div class="max-w-7xl mx-auto">
    <div class="text-center mb-10 reveal">
      <p class="text-pink-600 text-[10px] font-black uppercase tracking-widest mb-2">Most Loved</p>
      <h2 class="font-display italic text-3xl md:text-5xl text-gray-900">Best Sellers</h2>
    </div>

    {{-- Category filter tabs --}}
    <div class="flex flex-wrap justify-center gap-2 mb-10 reveal" id="bsFilterWrap">
      <button class="bs-tab active text-xs font-bold uppercase tracking-widest px-5 py-2.5 rounded-2xl bg-[#405549] text-white transition-all shadow-sm" data-filter="all">All</button>
      @foreach($categories->take(5) as $cat)
        <button class="bs-tab text-xs font-bold uppercase tracking-widest px-5 py-2.5 rounded-2xl border border-pink-200 text-gray-600 hover:border-pink-500 hover:text-pink-600 transition-all"
                data-filter="{{ $cat->slug }}">{{ $cat->name }}</button>
      @endforeach
    </div>

    {{-- Products grid --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-x-5 gap-y-10" id="bsGrid">
      @foreach($bestSellerItems as $product)
        <div class="bs-card reveal" data-category="{{ $product->category_slugs_string }}">
          @include('storefront.lumiere.partials.lustre-card', ['product' => $product])
        </div>
      @endforeach
    </div>

    <div class="text-center mt-12 reveal">
      <a href="{{ route('search') }}" class="px-8 py-3.5 rounded-2xl text-xs font-bold uppercase tracking-widest border border-pink-300 text-gray-800 hover:bg-pink-600 hover:text-white transition-all inline-block">
        View Full Collection
      </a>
    </div>
  </div>
</section>
@endif

{{-- Delicate Silk Divider --}}
<div class="bg-white py-3 flex justify-center">
  <div class="w-48 h-0.5 bg-gradient-to-r from-transparent via-pink-300 to-transparent"></div>
</div>

{{-- ===== 5. ABOUT / OUR STORY (2-COLUMN MATCHING SAAYA) ===== --}}
<section id="about" class="py-16 lg:py-24 px-6 md:px-12 bg-white">
  <div class="max-w-7xl mx-auto grid lg:grid-cols-2 gap-12 lg:gap-20 items-center">
    
    {{-- Left: Story Image Container --}}
    <div class="reveal flex justify-center lg:justify-start">
      <div class="aspect-[3/4] w-72 lg:w-96 rounded-[40px] overflow-hidden border-2 border-pink-200 shadow-xl relative bg-[#FFF0F3]">
        @php
          $lumiereStoryImg = \App\Models\Setting::get('lumiere_story_image');
        @endphp
        @if($lumiereStoryImg)
          <img src="{{ \App\Models\Setting::imageUrl($lumiereStoryImg) }}" alt="Our Story" class="w-full h-full object-cover">
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

    {{-- Right: Text + Stats --}}
    <div class="reveal">
      <p class="text-pink-600 text-[10px] font-black uppercase tracking-widest mb-3">
        {{ \App\Models\Setting::get('lumiere_story_badge', 'Our Story') }}
      </p>
      <h2 class="font-display italic text-3xl md:text-5xl text-gray-900 mb-6 leading-tight">
        {!! nl2br(e(\App\Models\Setting::get('lumiere_story_title', "Everyday elegance,\nmade responsibly"))) !!}
      </h2>
      <p class="text-gray-600 leading-relaxed mb-4">
        {{ \App\Models\Setting::get('lumiere_story_p1', 'Lumière began with a single loom and a simple belief: the things closest to your skin should be the best you own. Every piece is cut from sustainably sourced silk and finished by artisans who have done this for generations.') }}
      </p>
      <p class="text-gray-600 leading-relaxed mb-10">
        {{ \App\Models\Setting::get('lumiere_story_p2', 'We work directly with master craftspeople in heritage clusters across India, guaranteeing fair wages and keeping age-old textile traditions alive in contemporary silhouettes.') }}
      </p>

      {{-- Stats --}}
      <div class="grid grid-cols-3 gap-4 border-t border-pink-100 pt-8">
        @php
          $lumStat1Num = \App\Models\Setting::get('lumiere_stat1_num', '100%');
          $lumStat1Lbl = \App\Models\Setting::get('lumiere_stat1_label', 'Pure Silk');
          $lumStat2Num = \App\Models\Setting::get('lumiere_stat2_num', '300+');
          $lumStat2Lbl = \App\Models\Setting::get('lumiere_stat2_label', 'Artisans');
          $lumStat3Num = \App\Models\Setting::get('lumiere_stat3_num', '12');
          $lumStat3Lbl = \App\Models\Setting::get('lumiere_stat3_label', 'Cities');
        @endphp
        @foreach([[$lumStat1Num, $lumStat1Lbl], [$lumStat2Num, $lumStat2Lbl], [$lumStat3Num, $lumStat3Lbl]] as [$num, $label])
          <div class="text-center">
            <p class="font-display italic text-3xl lg:text-4xl text-pink-600">{{ $num }}</p>
            <p class="text-[10px] uppercase tracking-widest text-gray-400 mt-1">{{ $label }}</p>
          </div>
        @endforeach
      </div>
    </div>

  </div>
</section>

{{-- ===== 5b. CUSTOMER REVIEWS ===== --}}
@php
  $displayReviews = (isset($reviews) && $reviews->count()) 
      ? $reviews 
      : \App\Models\ProductReview::where('is_approved', true)->with(['product', 'user.addresses'])->latest()->take(3)->get();
@endphp
@if($displayReviews && $displayReviews->count())
<section class="py-12 lg:py-16 px-6 md:px-12 bg-white border-t border-pink-100">
  <div class="max-w-7xl mx-auto">
    <div class="text-center mb-10 reveal">
      <p class="text-pink-600 text-[10px] font-black uppercase tracking-widest mb-1">Client Accolades</p>
      <h2 class="font-display italic text-3xl md:text-4xl text-gray-900">What Our Clients Say</h2>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      @foreach($displayReviews->take(3) as $rev)
        @php
          $initials = collect(explode(' ', $rev->customer_name ?: 'Verified Customer'))
            ->filter()
            ->map(fn($part) => mb_substr($part, 0, 1))
            ->take(2)
            ->join('');
          $location = $rev->city ?: ($rev->user?->addresses?->first()?->city);
        @endphp
        <div class="bg-[#FFF0F3]/40 rounded-3xl p-6 sm:p-8 border border-pink-100 shadow-xs flex flex-col justify-between hover:border-pink-300 transition">
          <div>
            <div class="flex items-center gap-1 text-pink-500 mb-3 text-sm">
              @for($i = 1; $i <= 5; $i++)
                <span>{{ $i <= ($rev->rating ?? 5) ? '★' : '☆' }}</span>
              @endfor
            </div>
            @if($rev->title)
              <h3 class="font-bold text-gray-900 text-sm mb-1.5">{{ $rev->title }}</h3>
            @endif
            <p class="text-xs text-gray-600 italic leading-relaxed">
              "{{ $rev->comment }}"
            </p>
          </div>
          <div class="mt-6 pt-4 border-t border-pink-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-pink-100 text-pink-700 font-bold flex items-center justify-center text-xs shrink-0">
              {{ strtoupper($initials ?: 'VB') }}
            </div>
            <div>
              <h4 class="font-bold text-xs text-gray-900">{{ $rev->customer_name }}</h4>
              <span class="text-[10px] text-pink-600 font-semibold">
                Verified Client @if($location) • {{ $location }} @endif
              </span>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</section>

{{-- Delicate Silk Divider --}}
<div class="bg-[#FFFAF5] py-2 flex justify-center">
  <div class="w-48 h-0.5 bg-gradient-to-r from-transparent via-pink-300 to-transparent"></div>
</div>
@endif

{{-- ===== 6. CONTACT ===== --}}
<section id="contact" class="py-16 lg:py-24 px-6 md:px-12 bg-[#FFFAF5]">
  <div class="max-w-xl mx-auto text-center reveal">
    <p class="text-pink-600 text-[10px] font-black uppercase tracking-widest mb-2">Get in Touch</p>
    <h2 class="font-display italic text-3xl md:text-4xl text-gray-900 mb-4">We'd love to hear from you</h2>
    <p class="text-gray-500 text-sm mb-10">We reply within one business day.</p>
    
    <form action="{{ route('contact.submit') }}" method="POST" class="grid sm:grid-cols-2 gap-4 text-left">
      @csrf
      <input type="text"  name="name"  placeholder="Full Name"     required class="rounded-2xl border border-pink-100 px-4 py-3.5 text-sm outline-none w-full bg-white focus:border-pink-400">
      <input type="email" name="email" placeholder="Email Address" required class="rounded-2xl border border-pink-100 px-4 py-3.5 text-sm outline-none w-full bg-white focus:border-pink-400">
      <textarea name="message" placeholder="Your Message…" rows="4" required class="sm:col-span-2 rounded-2xl border border-pink-100 px-4 py-3.5 text-sm outline-none w-full bg-white focus:border-pink-400 resize-none"></textarea>
      <button type="submit" class="sm:col-span-2 py-3.5 rounded-2xl text-xs font-bold uppercase tracking-widest bg-[#405549] text-white hover:bg-pink-600 transition-colors shadow-sm">Send Message ✨</button>
    </form>
  </div>
</section>

{{-- Quick-view modal used by card clicks --}}
@include('storefront.lumiere.partials.quick-view')

@push('scripts')
<script src="{{ asset('js/product-card.js') }}"></script>
<script>
(function () {
  /* ── Sale Section tab filter ── */
  const saleTabs  = document.querySelectorAll('#saleFilterWrap .sale-tab');
  const saleCards = document.querySelectorAll('#saleGrid .sale-card');
  const saleGrid  = document.getElementById('saleGrid');

  saleTabs.forEach(tab => {
    tab.addEventListener('click', () => {
      saleTabs.forEach(t => {
        t.classList.remove('active', 'border-pink-500', 'text-pink-600', 'bg-pink-50');
        t.classList.add('border-pink-200', 'text-gray-600');
      });
      tab.classList.add('active', 'border-pink-500', 'text-pink-600', 'bg-pink-50');
      tab.classList.remove('border-pink-200', 'text-gray-600');

      const filter = tab.dataset.filter;
      let visibleCount = 0;
      saleCards.forEach(card => {
        const cats = (card.dataset.category || '').split(/\s+/).filter(Boolean);
        const match = filter === 'all' || cats.includes(filter);
        card.style.display = match ? '' : 'none';
        if (match) visibleCount++;
      });

      let emptyMsg = document.getElementById('saleEmptyMsg');
      if (visibleCount === 0) {
        if (!emptyMsg && saleGrid) {
          emptyMsg = document.createElement('div');
          emptyMsg.id = 'saleEmptyMsg';
          emptyMsg.className = 'col-span-full py-16 text-center text-gray-400 font-body';
          emptyMsg.innerHTML = '<p class="text-base font-semibold text-gray-700 uppercase tracking-wider mb-1">No items found</p><p class="text-xs text-gray-400">No promotional drops currently available in this collection.</p>';
          saleGrid.appendChild(emptyMsg);
        } else if (emptyMsg) {
          emptyMsg.style.display = '';
        }
      } else if (emptyMsg) {
        emptyMsg.style.display = 'none';
      }
    });
  });

  /* ── Best Sellers tab filter ── */
  const tabs   = document.querySelectorAll('.bs-tab');
  const cards  = document.querySelectorAll('.bs-card');
  const bsGrid = document.getElementById('bsGrid');

  tabs.forEach(tab => {
    tab.addEventListener('click', () => {
      tabs.forEach(t => {
        t.classList.remove('active', 'bg-[#405549]', 'text-white');
        t.classList.add('border', 'border-pink-200', 'text-gray-600');
      });
      tab.classList.add('active', 'bg-[#405549]', 'text-white');
      tab.classList.remove('border', 'border-pink-200', 'text-gray-600');

      const filter = tab.dataset.filter;
      let visibleCount = 0;
      cards.forEach(card => {
        const cats = (card.dataset.category || '').split(/\s+/).filter(Boolean);
        const match = filter === 'all' || cats.includes(filter);
        card.style.display = match ? '' : 'none';
        if (match) visibleCount++;
      });

      let emptyMsg = document.getElementById('bsEmptyMsg');
      if (visibleCount === 0) {
        if (!emptyMsg && bsGrid) {
          emptyMsg = document.createElement('div');
          emptyMsg.id = 'bsEmptyMsg';
          emptyMsg.className = 'col-span-full py-16 text-center text-gray-400 font-body';
          emptyMsg.innerHTML = '<p class="text-base font-semibold text-gray-700 uppercase tracking-wider mb-1">No items found</p><p class="text-xs text-gray-400">No best-selling items currently available in this collection.</p>';
          bsGrid.appendChild(emptyMsg);
        } else if (emptyMsg) {
          emptyMsg.style.display = '';
        }
      } else if (emptyMsg) {
        emptyMsg.style.display = 'none';
      }
    });
  });

  document.querySelector('.bs-tab.active')?.classList.add('bg-[#405549]', 'text-white');
})();
</script>
@endpush

</x-storefront-layout>
