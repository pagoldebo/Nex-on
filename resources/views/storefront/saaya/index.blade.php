{{--
    ======================================================================
    THEME: SAAYA — HOMEPAGE TEMPLATE
    ======================================================================
    File: resources/views/storefront/saaya/index.blade.php
    Controller: App\Http\Controllers\StorefrontController@index
    ViewModel Injections:
      - $theme:            Active Template model ('saaya')
      - $categories:       Hierarchical royal trousseau categories
      - $featured:         Handcrafted bridal & zari silk spotlight
      - $bestsellers:      Royal heirloom bestsellers
      - $newArrivals:      Festive new collections
    Section Architecture:
      1. HERO:            Deep maroon velvet canvas with antique gold foil-frame
      2. SCROLLING BAND:  Heritage Sanskrit/English luxury motto ribbon
      3. SHOP BY CAT:     Regal arched cards with gold border filigree
      4. SPOTLIGHT EDIT:  Archival trousseau showcase with silk swatches
      5. BEST SELLERS:    Foil-framed cards with dynamic size & color selectors
      6. ATELIER STORY:   Royal artisan clusters, handloom weaving heritage
      7. TESTIMONIALS:    Patron bride reviews & luxury packaging highlights
      8. CONCIERGE:       Bespoke bridal trousseau consultation booking
--}}
<x-storefront-layout :theme="$theme" title="Home">

{{-- ═══════════════════════════════════════════════
     HERO  —  Full Width Multiple Image Auto Slider
═══════════════════════════════════════════════ --}}
@php
  $saayaSlides = [];
  $saayaDefaultSlides = [
    1 => [
      'badge' => 'Crafted for Her',
      'title' => "Draped in\nGold & Grace",
      'desc'  => 'Handcrafted Indian lingerie rooted in tradition, elevated by craft. Each piece is an heirloom waiting to be worn.',
      'btn'   => 'Explore Collection',
      'url'   => route('search'),
    ],
    2 => [
      'badge' => 'Royal Trousseau Edit',
      'title' => "Zari Silk &\nVelvet Intimates",
      'desc'  => 'Intricate gold wire embroidery and pure mulberry silk, tailored for sacred celebrations and timeless grandeur.',
      'btn'   => 'Shop Bridal Silk',
      'url'   => route('search'),
    ],
    3 => [
      'badge' => 'Artisanal Heritage',
      'title' => "The Benarasi\nArchive",
      'desc'  => 'Handwoven motifs passed through generations of master ateliers. Where heritage craft meets modern form.',
      'btn'   => 'Discover The Archive',
      'url'   => route('search'),
    ],
    4 => [
      'badge' => 'Festive Elegance',
      'title' => "Crimson Whisper\nCapsule",
      'desc'  => 'Weightless tulle drenched in ceremonial crimson, edged with delicate antique gota patti work.',
      'btn'   => 'Explore Capsule',
      'url'   => route('search'),
    ],
    5 => [
      'badge' => 'Bespoke Craft',
      'title' => "Pure Heirloom\nComfort",
      'desc'  => 'Soft uncrushable silk satin crafted to drape seamlessly under royal lehengas and festive silhouettes.',
      'btn'   => 'Shop Collection',
      'url'   => route('search'),
    ],
  ];

  for ($s = 1; $s <= 5; $s++) {
    $def = $saayaDefaultSlides[$s];
    $customImg = \App\Models\Setting::get("saaya_slide_{$s}_image");
    if ($s === 1 && empty($customImg)) {
      $customImg = \App\Models\Setting::get('saaya_hero_image') ?: \App\Models\Setting::get('hero_banner_image');
    }

    $saayaSlides[] = [
      'image'    => $customImg ? \App\Models\Setting::imageUrl($customImg) : null,
      'badge'    => \App\Models\Setting::get("saaya_slide_{$s}_badge", $s === 1 ? \App\Models\Setting::get('saaya_hero_badge', $def['badge']) : $def['badge']),
      'title'    => \App\Models\Setting::get("saaya_slide_{$s}_title", $s === 1 ? \App\Models\Setting::get('saaya_hero_title', $def['title']) : $def['title']),
      'subtitle' => \App\Models\Setting::get("saaya_slide_{$s}_subtitle", $s === 1 ? \App\Models\Setting::get('saaya_hero_subtitle', $def['desc']) : $def['desc']),
      'btn_text' => \App\Models\Setting::get("saaya_slide_{$s}_btn_text", $def['btn']),
      'btn_url'  => \App\Models\Setting::get("saaya_slide_{$s}_btn_url", $def['url']),
    ];
  }
@endphp

<section class="relative bg-maroon overflow-hidden min-h-[580px] sm:min-h-[660px] lg:min-h-[760px] flex items-center select-none"
         x-data="{
           currentSlide: 0,
           totalSlides: {{ count($saayaSlides) }},
           timer: null,
           intervalMs: 4000,
           startAutoplay() {
             this.stopAutoplay();
             this.timer = setInterval(() => {
               this.nextSlide();
             }, this.intervalMs);
           },
           stopAutoplay() {
             if (this.timer) {
               clearInterval(this.timer);
               this.timer = null;
             }
           },
           nextSlide() {
             this.currentSlide = (this.currentSlide + 1) % this.totalSlides;
           },
           prevSlide() {
             this.currentSlide = (this.currentSlide - 1 + this.totalSlides) % this.totalSlides;
           },
           goToSlide(index) {
             this.currentSlide = index;
             this.startAutoplay();
           }
         }"
         x-init="startAutoplay()"
         @mouseenter="stopAutoplay()"
         @mouseleave="startAutoplay()"
         @keydown.arrow-right.window="nextSlide(); startAutoplay()"
         @keydown.arrow-left.window="prevSlide(); startAutoplay()"
         aria-roledescription="carousel"
         aria-label="Saaya Hero Slider">

  {{-- 5 FULL BACKGROUND IMAGE / TEXTURE LAYERS --}}
  @foreach($saayaSlides as $index => $slide)
    <div class="absolute inset-0 w-full h-full bg-cover bg-center bg-no-repeat transition-all duration-1000 ease-in-out transform"
         :class="currentSlide === {{ $index }} ? 'opacity-100 scale-100 z-10' : 'opacity-0 scale-105 pointer-events-none z-0'"
         @if($slide['image'])
           style="background-image: url('{{ $slide['image'] }}');"
         @else
           style="background-image: radial-gradient(circle at 75% 40%, rgba(201,162,75,0.18) 0%, rgba(61,12,17,0.98) 70%);"
         @endif
         role="group"
         aria-roledescription="slide"
         aria-label="Slide {{ $index + 1 }} of {{ count($saayaSlides) }}"
         :aria-hidden="currentSlide !== {{ $index }}">
      @if(!$slide['image'])
        {{-- Luxury default gold watermark pattern for slides without custom image --}}
        <div class="absolute inset-0 flex items-center justify-end pr-12 lg:pr-24 opacity-20 pointer-events-none">
          <svg class="w-72 h-88 lg:w-96 lg:h-[480px] text-gold" fill="currentColor" viewBox="0 0 100 130">
            <ellipse cx="50" cy="30" rx="18" ry="22"/>
            <path d="M20 55 Q35 45 50 48 Q65 45 80 55 L85 115 Q65 125 50 122 Q35 125 15 115 Z"/>
          </svg>
        </div>
      @endif
    </div>
  @endforeach

  {{-- REGAL GOLD PATTERN & MAROON VIGNETTE OVERLAY (ENSURES CRISP TEXT READABILITY) --}}
  <div class="absolute inset-0 z-20 pointer-events-none opacity-[.08]"
       style="background-image:radial-gradient(circle,#C9A24B 1px,transparent 1px);background-size:28px 28px;"></div>
  <div class="absolute inset-0 z-20 pointer-events-none bg-gradient-to-r from-maroon/95 via-maroon/75 to-maroon/40 lg:to-transparent"></div>
  <div class="absolute inset-0 z-20 pointer-events-none bg-radial-gradient from-transparent via-transparent to-black/30"></div>

  {{-- SLIDER CONTENT (HEADLINE, WRITEUP & CUSTOM LINK) --}}
  <div class="relative z-30 max-w-7xl mx-auto px-6 lg:px-12 py-20 w-full">
    <div class="max-w-xl">
      @foreach($saayaSlides as $index => $slide)
        <div x-show="currentSlide === {{ $index }}"
             x-transition:enter="transition ease-out duration-700"
             x-transition:enter-start="opacity-0 translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-300 absolute"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2">
          
          <p class="font-display italic text-gold text-xl lg:text-2xl mb-4 flex items-center gap-2 drop-shadow-sm">
            <span class="w-2 h-2 rounded-full bg-gold inline-block animate-pulse"></span>
            <span>{{ $slide['badge'] }}</span>
          </p>

          <h1 class="font-display text-5xl lg:text-7xl text-ivory leading-[1.08] mb-6 drop-shadow-[0_4px_16px_rgba(0,0,0,0.6)]">
            {!! nl2br(e($slide['title'])) !!}
          </h1>

          <p class="font-body text-ivory/85 text-base lg:text-lg leading-relaxed max-w-md mb-10 drop-shadow-sm">
            {{ $slide['subtitle'] }}
          </p>

          <div class="flex flex-wrap gap-4 items-center">
            <a href="{{ $slide['btn_url'] }}" 
               class="gold-btn py-4 px-10 text-xs font-body font-semibold tracking-widest uppercase shadow-lg transition-transform hover:scale-105 active:scale-95 inline-flex items-center gap-2">
              <span>{{ $slide['btn_text'] }}</span>
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
          </div>

        </div>
      @endforeach
    </div>
  </div>

  {{-- BOTTOM SLIDER NAVIGATION PILLS & COUNTER (Antique Gold) --}}
  <div class="absolute bottom-6 sm:bottom-8 left-6 sm:left-12 z-40 flex items-center gap-2 bg-black/40 backdrop-blur-md px-4 py-2 rounded-full border border-gold/30 shadow-md">
    @foreach($saayaSlides as $index => $slide)
      <button type="button"
              @click="goToSlide({{ $index }})"
              class="h-2.5 rounded-full transition-all duration-500 cursor-pointer focus:outline-none"
              :class="currentSlide === {{ $index }} ? 'w-8 bg-gold shadow-sm' : 'w-2.5 bg-ivory/30 hover:bg-gold/60'"
              aria-label="Go to slide {{ $index + 1 }}">
      </button>
    @endforeach
    <span class="ml-2 font-mono text-[11px] font-semibold text-gold select-none">
      <span x-text="currentSlide + 1"></span> / {{ count($saayaSlides) }}
    </span>
  </div>

  {{-- NAVIGATION ARROWS (PREV / NEXT) --}}
  <div class="absolute bottom-6 sm:bottom-8 right-6 sm:right-12 z-40 flex items-center gap-2.5">
    <button type="button"
            @click="prevSlide(); startAutoplay()"
            class="w-10 h-10 rounded-full bg-black/40 hover:bg-gold text-gold hover:text-maroon border border-gold/40 shadow-sm backdrop-blur-xs flex items-center justify-center transition-all duration-200 hover:scale-105 active:scale-95 focus:outline-none cursor-pointer"
            aria-label="Previous slide">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
    </button>
    <button type="button"
            @click="nextSlide(); startAutoplay()"
            class="w-10 h-10 rounded-full bg-black/40 hover:bg-gold text-gold hover:text-maroon border border-gold/40 shadow-sm backdrop-blur-xs flex items-center justify-center transition-all duration-200 hover:scale-105 active:scale-95 focus:outline-none cursor-pointer"
            aria-label="Next slide">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
    </button>
  </div>

</section>

{{-- Scrolling Announcement Band --}}
@include('storefront.common.scrolling-band')

{{-- Petal divider --}}
<div class="bg-ivory py-3 flex justify-center">
  <div class="petal-divider w-48 opacity-50"></div>
</div>


{{-- ═══════════════════════════════════════════════
     CATEGORY STRIP (Slideable Square Boxes)
═══════════════════════════════════════════════ --}}
@php
  $parentCategories = $categories->filter(fn($c) => is_null($c->parent_id));
@endphp
<section class="bg-ivory pt-10 pb-4 lg:pt-12 lg:pb-6"
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
             const gap = parseFloat(window.getComputedStyle(el).gap) || 16;
             const step = itemWidth + gap;

             if (step <= 0) return;

             // Calculate how many items fit in the visible viewport
             const visibleItems = Math.max(1, Math.floor((el.clientWidth + gap) / step));
             const scrollCount = visibleItems > 1 ? Math.min(visibleItems - 1, 3) : 1;

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
  <div class="max-w-7xl mx-auto px-6 lg:px-12">

    {{-- Header: Category Title + View All + Prev/Next Arrow Buttons --}}
    <div class="flex items-center justify-between mb-6">
      <div>
        <p class="font-display italic text-gold text-base lg:text-lg mb-1">Discover</p>
        <h2 class="font-display text-3xl sm:text-4xl lg:text-5xl text-maroon">Shop by Category</h2>
      </div>

      <div class="flex items-center gap-2">
        <a href="{{ route('featured.products') }}" 
           class="gold-btn py-2 px-4 sm:px-5 text-xs font-body font-semibold tracking-widest uppercase transition">
          View All
        </a>
        <div class="flex items-center gap-1.5">
          <button type="button" 
                  @click="scrollTrack(-1)" 
                  class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-white hover:bg-gold/15 text-maroon border border-gold/40 flex items-center justify-center transition text-xs font-bold focus:outline-none disabled:opacity-30 disabled:cursor-not-allowed shadow-2xs" 
                  :disabled="!canScrollLeft"
                  aria-label="Previous categories">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
          </button>
          <button type="button" 
                  @click="scrollTrack(1)" 
                  class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-maroon hover:bg-maroon/90 text-gold border border-gold/50 flex items-center justify-center transition text-xs font-bold shadow-2xs focus:outline-none disabled:opacity-30 disabled:cursor-not-allowed" 
                  :disabled="!canScrollRight"
                  aria-label="Next categories">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
          </button>
        </div>
      </div>
    </div>

    {{-- Scrollable Category Track with Square/Portrait Boxes --}}
    <div x-ref="catTrack" 
         @scroll.passive="checkScroll()"
         class="flex items-stretch gap-3 sm:gap-3.5 lg:gap-4 overflow-x-auto scroll-smooth scrollbar-none pb-2 pt-1"
         style="scrollbar-width: none; -ms-overflow-style: none; scroll-snap-type: x mandatory;">
      @forelse($parentCategories as $cat)
        <a href="{{ route('category.show', $cat->slug) }}"
           class="category-item group relative overflow-hidden shrink-0 w-[170px] sm:w-[200px] md:w-[220px] lg:w-[230px] xl:w-[245px] transition-transform duration-300 shadow-sm hover:shadow-xl"
           style="aspect-ratio: 3/4; scroll-snap-align: start; scroll-snap-stop: normal;">
          {{-- Category image or gradient --}}
          <div class="card-img-wrap absolute inset-0">
            @if($cat->image_url)
              <img src="{{ $cat->image_url }}" alt="{{ $cat->name }}"
                   class="card-img-zoom w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                   loading="lazy">
            @else
              <div class="w-full h-full bg-gradient-to-b from-blush/40 to-maroon/70"></div>
            @endif
          </div>
          {{-- Overlay --}}
          <div class="absolute inset-0 bg-gradient-to-t from-maroon/90 via-maroon/30 to-transparent"></div>
          {{-- Label --}}
          <div class="absolute bottom-0 left-0 right-0 p-4 lg:p-5">
            <p class="font-display text-ivory text-lg sm:text-xl lg:text-2xl leading-tight line-clamp-2">{{ $cat->name }}</p>
            <p class="font-body text-gold text-[10px] sm:text-xs tracking-widest uppercase mt-1 flex items-center gap-1 font-semibold">
              Shop Now
              <svg class="w-3 h-3 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/></svg>
            </p>
          </div>
          {{-- Foil corner accent --}}
          <div class="absolute top-3 right-3 w-6 h-6 border-t border-r border-gold/60 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
          <div class="absolute bottom-3 left-3 w-6 h-6 border-b border-l border-gold/60 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
        </a>
      @empty
        @php
          $defaultSaayaCats = [
            'Bralettes',
            'Lingerie Sets',
            'Bodysuits',
            'Nightwear & Robes',
            'Bridal Edit',
            'Everyday Essentials',
          ];
        @endphp
        @foreach($defaultSaayaCats as $fallbackName)
          <a href="{{ route('featured.products') }}"
             class="category-item group relative overflow-hidden shrink-0 w-[170px] sm:w-[200px] md:w-[220px] lg:w-[230px] xl:w-[245px] transition-transform duration-300 shadow-sm hover:shadow-xl"
             style="aspect-ratio: 3/4; scroll-snap-align: start; scroll-snap-stop: normal;">
            <div class="card-img-wrap absolute inset-0">
              <div class="w-full h-full bg-gradient-to-b from-blush/40 to-maroon/70"></div>
            </div>
            <div class="absolute inset-0 bg-gradient-to-t from-maroon/90 via-maroon/30 to-transparent"></div>
            <div class="absolute bottom-0 left-0 right-0 p-4 lg:p-5">
              <p class="font-display text-ivory text-lg sm:text-xl lg:text-2xl leading-tight line-clamp-2">{{ $fallbackName }}</p>
              <p class="font-body text-gold text-[10px] sm:text-xs tracking-widest uppercase mt-1 flex items-center gap-1 font-semibold">
                Shop Now
                <svg class="w-3 h-3 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/></svg>
              </p>
            </div>
            <div class="absolute top-3 right-3 w-6 h-6 border-t border-r border-gold/60 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
            <div class="absolute bottom-3 left-3 w-6 h-6 border-b border-l border-gold/60 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
          </a>
        @endforeach
      @endforelse
    </div>

  </div>
</section>


{{-- ═══════════════════════════════════════════════
     CURATED FEATURED PICKS  —  warm ivory grid
═══════════════════════════════════════════════ --}}
@php
  $curatedProducts = ($featured && $featured->count()) ? $featured : $featuredProducts;
@endphp
@if($curatedProducts && $curatedProducts->count())
<section class="bg-ivory pt-8 pb-14 lg:pt-10 lg:pb-16 border-t border-gold/15" id="curatedPicks">
  <div class="max-w-7xl mx-auto px-6 lg:px-12">

    {{-- Section Heading --}}
    <div class="text-center mb-8 lg:mb-10 reveal">
      <p class="font-display italic text-gold text-lg lg:text-xl mb-1">
        {{ \App\Models\Setting::get('saaya_curated_badge', 'Curated') }}
      </p>
      <h2 class="font-display text-4xl lg:text-5xl text-maroon">
        {{ \App\Models\Setting::get('saaya_curated_title', 'Curated Featured Picks') }}
      </h2>
      <p class="font-body text-maroon/70 text-sm max-w-xl mx-auto mt-2 leading-relaxed">
        {{ \App\Models\Setting::get('saaya_curated_subtitle', 'Handcrafted heirlooms shaped with pure mulberry silk, delicate jaali embroidery, and timeless silhouettes.') }}
      </p>
      <div class="flex justify-center mt-3">
        <div class="petal-divider w-24 opacity-60"></div>
      </div>
    </div>

    {{-- Products grid --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 lg:gap-6">
      @foreach($curatedProducts->take(8) as $product)
        <div>
          @include('storefront.saaya.partials.product-card', ['product' => $product])
        </div>
      @endforeach
    </div>

    {{-- View All CTA --}}
    <div class="text-center mt-8 sm:mt-10 reveal">
      <a href="{{ route('featured.products') }}" class="gold-btn py-4 px-10 text-xs font-body font-semibold tracking-widest uppercase inline-block">
        Explore Curated Edit
      </a>
    </div>

  </div>
</section>
@endif


{{-- ═══════════════════════════════════════════════
     FLASH SALE / SPECIAL OFFERS SECTION (REF IMAGE DESIGN)
═══════════════════════════════════════════════ --}}
@if(isset($saleProducts) && $saleProducts->count())
<section class="bg-ivory pt-8 pb-8 sm:pt-10 sm:pb-10 lg:pt-10 lg:pb-12 border-t border-gold/15" id="saleSection">
  <div class="max-w-7xl mx-auto px-6 lg:px-12">

    {{-- Heading matching Reference Design --}}
    <div class="text-center mb-6 sm:mb-8 reveal">
      <p class="font-display italic text-gold text-lg lg:text-xl mb-1">{{ \App\Models\Setting::get('saaya_sale_kicker', 'Limited Time Deals') }}</p>
      <h2 class="font-display text-4xl lg:text-5xl text-maroon">{{ \App\Models\Setting::get('saaya_sale_title', 'Special Sale Offers') }}</h2>
      <p class="font-body text-maroon/60 text-xs md:text-sm max-w-lg mx-auto mt-2">{{ \App\Models\Setting::get('saaya_sale_subtitle', 'Exclusive heirlooms and seasonal lingerie edits at celebratory promotional prices.') }}</p>
    </div>

    {{-- Category filter tabs matching Reference Design --}}
    <div class="flex flex-wrap justify-center gap-2 mb-6 sm:mb-8 reveal" id="saleFilterWrap">
      <button class="sale-tab active font-body text-xs tracking-widest uppercase px-5 py-2.5 border border-gold text-maroon bg-gold/20 transition-colors rounded-full" data-filter="all">All</button>
      @foreach($categories->take(6) as $cat)
        <button class="sale-tab font-body text-xs tracking-widest uppercase px-5 py-2.5 border border-gold/30 text-maroon/70 hover:border-gold hover:text-gold transition-colors rounded-full"
                data-filter="{{ $cat->slug }}">{{ $cat->name }}</button>
      @endforeach
    </div>

    {{-- Products grid --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 lg:gap-6" id="saleGrid">
      @foreach($saleProducts->take(8) as $product)
        <div class="sale-card" data-category="{{ $product->category_slugs_string }}">
          @include('storefront.saaya.partials.sale-card', ['product' => $product])
        </div>
      @endforeach
    </div>

    {{-- View All CTA --}}
    <div class="text-center mt-8 sm:mt-10 reveal">
      <a href="{{ route('collections.sale') }}" class="gold-btn py-4 px-10 text-xs font-body font-semibold tracking-widest uppercase inline-block">
        Explore All Deals
      </a>
    </div>

  </div>
</section>
@endif


{{-- ═══════════════════════════════════════════════
     BEST SELLERS  —  dark maroon, tab filter, grid
═══════════════════════════════════════════════ --}}
@php
  $bestSellerItems = ($bestsellers && $bestsellers->count()) ? $bestsellers : $featuredProducts;
@endphp
@if($bestSellerItems && $bestSellerItems->count())
<section class="bg-maroon-dark pt-8 pb-8 sm:pt-10 sm:pb-10 lg:pt-10 lg:pb-12" id="bestSellers">
  <div class="max-w-7xl mx-auto px-6 lg:px-12">

    {{-- Heading --}}
    <div class="text-center mb-6 sm:mb-8 reveal">
      <p class="font-display italic text-gold text-lg mb-1">Most Loved</p>
      <h2 class="font-display text-4xl lg:text-5xl text-ivory">Best Sellers</h2>
    </div>

    {{-- Category filter tabs --}}
    <div class="flex flex-wrap justify-center gap-2 mb-6 sm:mb-8 reveal" id="bsFilterWrap">
      <button class="bs-tab active font-body text-xs tracking-widest uppercase px-5 py-2.5 border border-gold/40 text-gold hover:border-gold transition-colors rounded-full" data-filter="all">All</button>
      @foreach($categories->take(5) as $cat)
        <button class="bs-tab font-body text-xs tracking-widest uppercase px-5 py-2.5 border border-ivory/20 text-ivory/60 hover:border-gold hover:text-gold transition-colors rounded-full"
                data-filter="{{ $cat->slug }}">{{ $cat->name }}</button>
      @endforeach
    </div>

    {{-- Products grid --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 lg:gap-6" id="bsGrid">
      @foreach($bestSellerItems as $product)
        <div class="bs-card" data-category="{{ $product->category_slugs_string }}">
          @include('storefront.saaya.partials.product-card', ['product' => $product])
        </div>
      @endforeach
    </div>

    {{-- View All CTA --}}
    <div class="text-center mt-8 sm:mt-10 reveal">
      <a href="{{ route('search') }}" class="gold-btn py-4 px-10 text-xs font-body font-semibold tracking-widest uppercase inline-block">
        View Full Collection
      </a>
    </div>

  </div>
</section>
@endif


{{-- ═══════════════════════════════════════════════
     ABOUT / STORY  —  ivory, foil-frame image, stats
═══════════════════════════════════════════════ --}}
<section class="bg-ivory pt-8 pb-10 sm:pt-10 sm:pb-12 lg:pt-12 lg:pb-14">
  <div class="max-w-7xl mx-auto px-6 lg:px-12 grid lg:grid-cols-2 gap-12 lg:gap-20 items-center">

    {{-- Left: foil-frame image --}}
    <div class="reveal flex justify-center lg:justify-start">
      <div class="foil-frame w-72 h-96 lg:w-96 lg:h-[480px] relative overflow-hidden">
        <svg class="frame-line" viewBox="0 0 100 100" preserveAspectRatio="none">
          <rect x="2" y="2" width="96" height="96" vector-effect="non-scaling-stroke"/>
        </svg>
        @php
          $saayaStoryImg = \App\Models\Setting::get('saaya_story_image');
        @endphp
        @if($saayaStoryImg)
          <img src="{{ \App\Models\Setting::imageUrl($saayaStoryImg) }}" alt="Our Story" class="w-full h-full object-cover">
        @else
          <div class="w-full h-full bg-gradient-to-b from-blush/30 to-maroon/20 flex items-center justify-center">
            <svg class="w-24 h-32 text-maroon/20" fill="currentColor" viewBox="0 0 80 110">
              <ellipse cx="40" cy="22" rx="14" ry="18"/>
              <path d="M10 45 Q25 35 40 38 Q55 35 70 45 L72 95 Q55 105 40 102 Q25 105 8 95 Z"/>
            </svg>
          </div>
        @endif
      </div>
    </div>

    {{-- Right: text + stats --}}
    <div class="reveal">
      <p class="font-display italic text-gold text-lg mb-2">{{ \App\Models\Setting::get('saaya_story_badge', 'Our Story') }}</p>
      <h2 class="font-display text-4xl lg:text-5xl text-maroon mb-6 leading-tight">
        {!! nl2br(e(\App\Models\Setting::get('saaya_story_title', "A Love Letter\nto Indian Femininity"))) !!}
      </h2>
      <p class="font-body text-maroon/70 leading-relaxed mb-4">
        {{ \App\Models\Setting::get('saaya_story_p1', 'Saaya was born from a belief that every Indian woman deserves lingerie that honours her form and her heritage. We weave together age-old motifs — jaali embroidery, zari threadwork, lotus prints — with modern fits engineered for real bodies.') }}
      </p>
      <p class="font-body text-maroon/70 leading-relaxed mb-10">
        {{ \App\Models\Setting::get('saaya_story_p2', 'Every piece leaves our atelier only after passing through the hands of our master karigars, who carry forward skills passed down through generations.') }}
      </p>

      {{-- Stats --}}
      <div class="grid grid-cols-3 gap-4 border-t border-gold/20 pt-8">
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
            <p class="font-body text-xs text-maroon/60 mt-1 leading-snug">{{ $label }}</p>
          </div>
        @endforeach
      </div>
    </div>

  </div>
</section>


{{-- ═══════════════════════════════════════════════
     CUSTOMER REVIEWS & PRAISE
═══════════════════════════════════════════════ --}}
@php
  $displayReviews = (isset($reviews) && $reviews->count()) 
      ? $reviews 
      : \App\Models\ProductReview::where('is_approved', true)->with(['product', 'user.addresses'])->latest()->take(3)->get();
@endphp
@if($displayReviews && $displayReviews->count())
<section class="bg-ivory pt-8 pb-10 sm:pt-10 sm:pb-12 lg:pt-10 lg:pb-14 border-t border-gold/15" id="reviewsSection">
  <div class="max-w-7xl mx-auto px-6 lg:px-12">
    <div class="text-center mb-8 reveal">
      <p class="font-display italic text-gold text-base lg:text-lg mb-1">Patron Praise</p>
      <h2 class="font-display text-3xl sm:text-4xl text-maroon">What Our Patrons Say</h2>
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
        <div class="bg-white/80 rounded-2xl p-6 sm:p-8 border border-gold/25 shadow-xs flex flex-col justify-between hover:border-gold/60 transition">
          <div>
            <div class="flex items-center gap-1 text-gold mb-3 text-sm">
              @for($i = 1; $i <= 5; $i++)
                <span>{{ $i <= ($rev->rating ?? 5) ? '★' : '☆' }}</span>
              @endfor
            </div>
            @if($rev->title)
              <h3 class="font-display font-bold text-maroon text-base mb-1.5">{{ $rev->title }}</h3>
            @endif
            <p class="font-body text-xs text-maroon/80 italic leading-relaxed">
              "{{ $rev->comment }}"
            </p>
          </div>
          <div class="mt-6 pt-4 border-t border-gold/15 flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-gold/15 text-maroon font-bold flex items-center justify-center text-xs shrink-0 border border-gold/30">
              {{ strtoupper($initials ?: 'VB') }}
            </div>
            <div>
              <h4 class="font-display text-sm font-bold text-maroon">{{ $rev->customer_name }}</h4>
              <span class="font-body text-[10px] text-gold tracking-wider uppercase font-semibold">
                Verified Patron @if($location) • {{ $location }} @endif
              </span>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</section>
@endif


{{-- ═══════════════════════════════════════════════
     CONTACT  —  maroon bg, centered form
═══════════════════════════════════════════════ --}}
<section class="bg-maroon pt-10 pb-12 sm:pt-12 sm:pb-16">
  <div class="max-w-xl mx-auto px-6 lg:px-12">

    <div class="text-center mb-10 reveal">
      <p class="font-display italic text-gold text-lg mb-1">Get in Touch</p>
      <h2 class="font-display text-4xl text-ivory">Write to Us</h2>
      <p class="font-body text-ivory/60 text-sm mt-3">We reply within one business day.</p>
    </div>

    <form class="flex flex-col gap-4 reveal" action="{{ route('contact.submit') }}" method="POST">
      @csrf
      <input type="text" name="name" required placeholder="Your Name"
             class="border border-gold/30 bg-transparent px-4 py-3.5 text-sm text-ivory placeholder-ivory/40 focus:border-gold focus:outline-none transition-colors font-body">
      <input type="email" name="email" required placeholder="Email Address"
             class="border border-gold/30 bg-transparent px-4 py-3.5 text-sm text-ivory placeholder-ivory/40 focus:border-gold focus:outline-none transition-colors font-body">
      <textarea name="message" rows="4" required placeholder="Your message…"
                class="border border-gold/30 bg-transparent px-4 py-3.5 text-sm text-ivory placeholder-ivory/40 focus:border-gold focus:outline-none transition-colors font-body resize-none"></textarea>
      <button type="submit" class="gold-btn py-4 text-xs font-body font-semibold tracking-widest uppercase mt-1">
        Send Message
      </button>
    </form>

  </div>
</section>

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
        t.classList.remove('active', 'border-gold', 'text-maroon', 'bg-gold/20');
        t.classList.add('border-gold/30', 'text-maroon/70');
      });
      tab.classList.add('active', 'border-gold', 'text-maroon', 'bg-gold/20');
      tab.classList.remove('border-gold/30', 'text-maroon/70');

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
          emptyMsg.className = 'col-span-full py-16 text-center text-ivory/60 font-body';
          emptyMsg.innerHTML = '<p class="text-base font-semibold text-gold uppercase tracking-wider mb-1">No items found</p><p class="text-xs text-ivory/50">No promotional drops currently available in this collection.</p>';
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
        t.classList.remove('active','border-gold','text-gold');
        t.classList.add('border-ivory/20','text-ivory/60');
      });
      tab.classList.add('active','border-gold','text-gold');
      tab.classList.remove('border-ivory/20','text-ivory/60');

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
          emptyMsg.className = 'col-span-full py-16 text-center text-ivory/60 font-body';
          emptyMsg.innerHTML = '<p class="text-base font-semibold text-gold uppercase tracking-wider mb-1">No items found</p><p class="text-xs text-ivory/50">No best-selling items currently available in this collection.</p>';
          bsGrid.appendChild(emptyMsg);
        } else if (emptyMsg) {
          emptyMsg.style.display = '';
        }
      } else if (emptyMsg) {
        emptyMsg.style.display = 'none';
      }
    });
  });

  /* Ensure active tab keeps gold border on load */
  document.querySelector('.bs-tab.active')?.classList.add('border-gold','text-gold');
  document.querySelector('.bs-tab.active')?.classList.remove('border-ivory/20','text-ivory/60');

  /* ── Reveal on scroll ── */
  function revealElements() {
    document.querySelectorAll('.reveal:not(.in-view)').forEach(el => {
      el.classList.add('in-view', 'visible');
    });
  }

  if ('IntersectionObserver' in window) {
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('in-view', 'visible');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.05, rootMargin: '0px 0px 60px 0px' });

    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));

    // Fallback: automatically reveal all elements after load so content is never stuck hidden
    setTimeout(revealElements, 600);
    window.addEventListener('load', () => setTimeout(revealElements, 300));
  } else {
    revealElements();
  }
})();
</script>
@endpush

</x-storefront-layout>
