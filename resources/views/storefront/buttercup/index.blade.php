{{--
    ======================================================================
    THEME: BUTTERCUP — HOMEPAGE TEMPLATE
    ======================================================================
    File: resources/views/storefront/buttercup/index.blade.php
    Controller: App\Http\Controllers\StorefrontController@index
    ViewModel Injections:
      - $theme:            Active Template model ('buttercup')
      - $categories:       Parent categories with circular pill swatches
      - $featured:         Curated cozy comfort collections
      - $bestsellers:      Top rated essentials
      - $newArrivals:      Fresh seasonal pastel drops
    Section Architecture:
      1. HERO SLIDER:     5-slide full-width background image carousel with 3s autoplay
      2. SCROLLING BAND:  Global delicate brand motto ticker
      3. SHOP BY CAT:     Circular pastel bubbles with organic hover scaling
      4. COZY EDIT:       Featured editorial rail with quick-view modal triggers
      5. BEST SELLERS:    Tabbed product grid with instant color swatch preview
      6. VALUE PILLARS:   Cloud-soft comfort, eco-modal, non-wire body promises
      7. REVIEWS:         Verified client testimonials & star impressions
      8. NEWSLETTER:      Soft pastel subscription CTA
--}}
<x-storefront-layout :theme="$theme" title="Home">

{{-- ===== HERO SLIDER (5 FULL BACKGROUND IMAGES, 3-SECOND AUTOPLAY) ===== --}}
@php
  $heroSlides = [];
  $buttercupDefs = [
    1 => [
      'badge'    => 'New Season Edit',
      'title'    => "Comfort that\nFeels Like Home",
      'subtitle' => 'Buttery-soft everyday lingerie, made for real bodies and real days — because comfort should never be a compromise.',
      'btn1_text'=> 'Shop Now',
      'btn1_url' => route('home') . '#products',
      'btn2_text'=> 'Explore',
      'btn2_url' => route('search'),
    ],
    2 => [
      'badge'    => 'Cloud-Soft Intimates',
      'title'    => "Second-Skin Ease,\nAll Day Long",
      'subtitle' => 'Breathable modal and organic cotton with zero pinch. Gentle, weightless support made to move with you.',
      'btn1_text'=> 'Shop Intimates',
      'btn1_url' => route('home') . '#products',
      'btn2_text'=> 'View Collection',
      'btn2_url' => route('featured.products'),
    ],
    3 => [
      'badge'    => 'Pure Indulgence',
      'title'    => "Soft as a Whisper,\nCrafted for You",
      'subtitle' => 'Delicate lace accents meet featherlight silhouettes. Experience the sublime touch of effortless elegance every day.',
      'btn1_text'=> 'Explore Bestsellers',
      'btn1_url' => route('home') . '#products',
      'btn2_text'=> 'Discover More',
      'btn2_url' => route('search'),
    ],
    4 => [
      'badge'    => 'Wire-Free Freedom',
      'title'    => "Zero Pinching,\n100% Pure Comfort",
      'subtitle' => 'Say goodbye to painful underwires forever. Our contour bands adapt harmoniously to your natural curves.',
      'btn1_text'=> 'Shop Wire-Free',
      'btn1_url' => route('home') . '#products',
      'btn2_text'=> 'Our Fit Guide',
      'btn2_url' => route('search'),
    ],
    5 => [
      'badge'    => 'Lounge & Sleep',
      'title'    => "Dreamy Loungewear\nfor Cozy Evenings",
      'subtitle' => 'From quiet morning coffee to tranquil midnight slumber — wrap yourself in the soothing bliss of buttery loungewear.',
      'btn1_text'=> 'Shop Loungewear',
      'btn1_url' => route('home') . '#products',
      'btn2_text'=> 'Explore All',
      'btn2_url' => route('featured.products'),
    ],
  ];

  for ($s = 1; $s <= 5; $s++) {
    $custom = \App\Models\Setting::get("buttercup_slide_{$s}_image");
    if ($s === 1 && empty($custom)) {
      $custom = \App\Models\Setting::get('buttercup_hero_image');
    }
    $imgUrl = \App\Models\Setting::imageUrl($custom);
    if (!$imgUrl && file_exists(public_path("images/slider/slide-{$s}.jpg"))) {
      $imgUrl = asset("images/slider/slide-{$s}.jpg");
    }

    $d = $buttercupDefs[$s];
    $heroSlides[] = [
      'image'     => $imgUrl,
      'kicker'    => \App\Models\Setting::get("buttercup_slide_{$s}_badge", $s === 1 ? \App\Models\Setting::get('buttercup_hero_badge', $d['badge']) : $d['badge']),
      'title'     => \App\Models\Setting::get("buttercup_slide_{$s}_title", $s === 1 ? \App\Models\Setting::get('buttercup_hero_title', $d['title']) : $d['title']),
      'subtitle'  => \App\Models\Setting::get("buttercup_slide_{$s}_subtitle", $s === 1 ? \App\Models\Setting::get('buttercup_hero_subtitle', $d['subtitle']) : $d['subtitle']),
      'btn1_text' => \App\Models\Setting::get("buttercup_slide_{$s}_btn1_text", $d['btn1_text']),
      'btn1_url'  => \App\Models\Setting::get("buttercup_slide_{$s}_btn1_url", $d['btn1_url']),
      'btn2_text' => \App\Models\Setting::get("buttercup_slide_{$s}_btn2_text", $d['btn2_text']),
      'btn2_url'  => \App\Models\Setting::get("buttercup_slide_{$s}_btn2_url", $d['btn2_url']),
    ];
  }
@endphp
      'btn2_url'  => \App\Models\Setting::get('buttercup_slide_5_btn2_url', route('featured.products')),
    ],
  ];
@endphp

<section class="relative bg-[#FBE7E4] overflow-hidden min-h-[580px] sm:min-h-[640px] lg:min-h-[720px] flex items-center select-none"
         x-data="{
           currentSlide: 0,
           totalSlides: {{ count($heroSlides) }},
           timer: null,
           intervalMs: 3000,
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
         aria-label="Buttercup Hero Slider">

  {{-- 5 FULL BACKGROUND IMAGE LAYERS --}}
  @foreach($heroSlides as $index => $slide)
    <div class="absolute inset-0 w-full h-full bg-cover bg-center bg-no-repeat transition-all duration-1000 ease-in-out transform"
         :class="currentSlide === {{ $index }} ? 'opacity-100 scale-100 z-10' : 'opacity-0 scale-105 pointer-events-none z-0'"
         @if(!empty($slide['image']))
           style="background-image: url('{{ $slide['image'] }}');"
         @else
           style="background-image: radial-gradient(circle at 70% 30%, rgba(216,154,160,0.2) 0%, rgba(251,231,228,0.95) 70%);"
         @endif
         role="group"
         aria-roledescription="slide"
         aria-label="Slide {{ $index + 1 }} of {{ count($heroSlides) }}"
         :aria-hidden="currentSlide !== {{ $index }}">
    </div>
  @endforeach

  {{-- BLUSH GRADIENT / SCRIM OVERLAY (ASSURES TEXT READABILITY) --}}
  <div class="absolute inset-0 z-20 pointer-events-none bg-gradient-to-r from-[#FBE7E4]/95 via-[#FBE7E4]/85 to-[#FBE7E4]/40 md:to-transparent"></div>
  <div class="absolute inset-0 z-20 pointer-events-none bg-radial-gradient from-transparent via-transparent to-[#FBE7E4]/30"></div>

  {{-- SLIDER CONTENT (HEADLINES, COPY & CALL-TO-ACTIONS) --}}
  <div class="relative z-30 max-w-7xl mx-auto px-6 lg:px-10 py-16 sm:py-24 w-full">
    <div class="max-w-xl">
      @foreach($heroSlides as $index => $slide)
        <div x-show="currentSlide === {{ $index }}"
             x-transition:enter="transition ease-out duration-700"
             x-transition:enter-start="opacity-0 translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-300 absolute"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2">
          
          <p class="text-[#7F9272] text-xs font-bold uppercase tracking-widest mb-3 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-[#7F9272] inline-block animate-pulse"></span>
            <span>{{ $slide['kicker'] }}</span>
          </p>

          @php
            $lines = explode("\n", str_replace(["\r\n", "\r"], "\n", $slide['title']));
          @endphp
          <h1 class="font-display text-4xl sm:text-5xl md:text-6xl text-[#4A3B3A] leading-[1.1] mb-6">
            @if(count($lines) > 1)
              {{ $lines[0] }}<br><span class="text-[#B97680]">{{ implode(' ', array_slice($lines, 1)) }}</span>
            @else
              {{ $slide['title'] }}
            @endif
          </h1>

          <p class="font-body text-[#4A3B3A]/85 text-sm sm:text-base max-w-md mb-8 leading-relaxed font-medium">
            {{ $slide['subtitle'] }}
          </p>

          <div class="flex flex-wrap gap-4 items-center">
            <a href="{{ $slide['btn1_url'] }}" 
               class="px-8 py-3.5 text-xs font-bold uppercase tracking-widest transition-all bg-[#D89AA0] text-[#FFFAF5] hover:bg-[#B97680] hover:shadow-lg rounded-full shadow-sm">
              {{ $slide['btn1_text'] }}
            </a>
            <a href="{{ $slide['btn2_url'] }}" 
               class="px-8 py-3.5 text-xs font-bold uppercase tracking-widest transition-all border border-[#D89AA0] text-[#4A3B3A] hover:bg-white/80 rounded-full bg-white/50 backdrop-blur-xs font-semibold">
              {{ $slide['btn2_text'] }}
            </a>
          </div>

        </div>
      @endforeach
    </div>
  </div>

  {{-- BOTTOM SLIDER NAVIGATION PILLS & COUNTER --}}
  <div class="absolute bottom-6 sm:bottom-8 left-6 sm:left-10 z-40 flex items-center gap-2 bg-white/60 backdrop-blur-md px-4 py-2 rounded-full border border-[#D89AA0]/20 shadow-xs">
    @foreach($heroSlides as $index => $slide)
      <button type="button"
              @click="goToSlide({{ $index }})"
              class="h-2.5 rounded-full transition-all duration-500 cursor-pointer focus:outline-none"
              :class="currentSlide === {{ $index }} ? 'w-8 bg-[#B97680] shadow-sm' : 'w-2.5 bg-[#4A3B3A]/30 hover:bg-[#B97680]/60'"
              aria-label="Go to slide {{ $index + 1 }}">
      </button>
    @endforeach
    <span class="ml-2 font-mono text-[11px] font-semibold text-[#4A3B3A]/70 select-none">
      <span x-text="currentSlide + 1"></span> / {{ count($heroSlides) }}
    </span>
  </div>

  {{-- NAVIGATION ARROWS (PREV / NEXT) --}}
  <div class="absolute bottom-6 sm:bottom-8 right-6 sm:right-10 z-40 flex items-center gap-2.5">
    <button type="button"
            @click="prevSlide(); startAutoplay()"
            class="w-10 h-10 rounded-full bg-white/80 hover:bg-white text-[#4A3B3A] border border-[#D89AA0]/30 shadow-sm backdrop-blur-xs flex items-center justify-center transition-all duration-200 hover:scale-105 active:scale-95 focus:outline-none"
            aria-label="Previous slide">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
    </button>
    <button type="button"
            @click="nextSlide(); startAutoplay()"
            class="w-10 h-10 rounded-full bg-white/80 hover:bg-white text-[#4A3B3A] border border-[#D89AA0]/30 shadow-sm backdrop-blur-xs flex items-center justify-center transition-all duration-200 hover:scale-105 active:scale-95 focus:outline-none"
            aria-label="Next slide">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
    </button>
  </div>
</section>

{{-- Scrolling Announcement Band --}}
@include('storefront.common.scrolling-band')

{{-- Soft floral divider --}}
<div class="bg-[#FFFAF5] py-3 flex justify-center">
  <div class="w-48 h-0.5 bg-gradient-to-r from-transparent via-[#D89AA0]/40 to-transparent"></div>
</div>

{{-- ===== CATEGORIES ===== --}}
<section class="py-12 lg:py-16 px-6 lg:px-10" 
         style="background:#FFFAF5"
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
    {{-- Header: Category Title + View All + Prev/Next Buttons --}}
    <div class="flex items-center justify-between mb-8">
      <div>
        <p class="text-[#7F9272] text-[11px] font-bold uppercase tracking-widest mb-1">Discover</p>
        <h2 class="font-display text-2xl sm:text-3xl md:text-4xl text-[#4A3B3A]">Shop by Category</h2>
      </div>

      <div class="flex items-center gap-2">
        <a href="{{ route('featured.products') }}" 
           class="text-xs sm:text-sm font-bold uppercase tracking-wider px-4 py-2 rounded-full transition shadow-xs bg-[#D89AA0] text-[#FFFAF5] hover:bg-[#B97680]">
          View All
        </a>
        <div class="flex items-center gap-1.5">
          <button type="button" 
                  @click="scrollTrack(-1)" 
                  class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-[#FBE7E4] hover:bg-[#F5D5D0] text-[#4A3B3A] flex items-center justify-center transition text-xs font-bold focus:outline-none disabled:opacity-35 disabled:cursor-not-allowed shadow-2xs border border-[#D89AA0]/20" 
                  :disabled="!canScrollLeft"
                  aria-label="Previous categories">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
          </button>
          <button type="button" 
                  @click="scrollTrack(1)" 
                  class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-[#D89AA0] hover:bg-[#B97680] text-white flex items-center justify-center transition text-xs font-bold shadow-2xs focus:outline-none disabled:opacity-35 disabled:cursor-not-allowed" 
                  :disabled="!canScrollRight"
                  aria-label="Next categories">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
          </button>
        </div>
      </div>
    </div>

    {{-- Category Items Track --}}
    <div x-ref="catTrack" 
         @scroll.passive="checkScroll()"
         class="flex items-start gap-4 sm:gap-5 lg:gap-6 overflow-x-auto scroll-smooth scrollbar-none pb-2 pt-1"
         style="scrollbar-width: none; -ms-overflow-style: none; scroll-snap-type: x mandatory;">
      @php
        $parentCats = $categories->filter(fn($c) => is_null($c->parent_id));
      @endphp
      @forelse($parentCats as $cat)
        <a href="{{ route('category.show', $cat->slug) }}" 
           class="category-item group shrink-0 lg:shrink lg:flex-1 min-w-[96px] sm:min-w-[105px] lg:min-w-0 flex flex-col items-center text-center transition"
           style="scroll-snap-align: start; scroll-snap-stop: normal;">
          <div class="w-20 h-20 sm:w-24 sm:h-24 md:w-28 md:h-28 lg:w-24 lg:h-24 xl:w-28 xl:h-28 rounded-full overflow-hidden mb-3 bg-[#D89AA0]/15 border-2 border-[#D89AA0]/30 shadow-2xs group-hover:scale-105 group-hover:border-[#D89AA0] group-hover:shadow-md transition-all duration-300 flex items-center justify-center">
            @if($cat->image_url)
              <img src="{{ $cat->image_url }}" 
                   alt="{{ $cat->name }}" 
                   class="w-full h-full object-cover rounded-full" 
                   loading="lazy">
            @else
              <svg viewBox="0 0 60 60" class="w-10 h-10"><circle cx="30" cy="30" r="20" fill="#D89AA0" opacity="0.4"/></svg>
            @endif
          </div>
          <h3 class="font-body font-semibold text-xs sm:text-sm text-[#4A3B3A] group-hover:text-[#B97680] transition leading-snug px-1 line-clamp-2 max-w-[125px]">
            {{ $cat->name }}
          </h3>
        </a>
      @empty
        @foreach(['Lingerie Sets', 'Bralettes', 'Bodysuits', 'Nightwear', 'Bridal Edit', 'Everyday Essentials'] as $fallbackName)
          <a href="{{ route('featured.products') }}" 
             class="category-item group shrink-0 lg:shrink lg:flex-1 min-w-[96px] sm:min-w-[105px] lg:min-w-0 flex flex-col items-center text-center transition"
             style="scroll-snap-align: start; scroll-snap-stop: normal;">
            <div class="w-20 h-20 sm:w-24 sm:h-24 md:w-28 md:h-28 lg:w-24 lg:h-24 xl:w-28 xl:h-28 rounded-full overflow-hidden mb-3 bg-[#D89AA0]/15 border-2 border-[#D89AA0]/30 shadow-2xs group-hover:scale-105 group-hover:border-[#D89AA0] group-hover:shadow-md transition-all duration-300 flex items-center justify-center">
              <svg viewBox="0 0 60 60" class="w-10 h-10"><circle cx="30" cy="30" r="20" fill="#D89AA0" opacity="0.4"/></svg>
            </div>
            <h3 class="font-body font-semibold text-xs sm:text-sm text-[#4A3B3A] group-hover:text-[#B97680] transition leading-snug px-1 line-clamp-2 max-w-[125px]">
              {{ $fallbackName }}
            </h3>
          </a>
        @endforeach
      @endforelse
    </div>
  </div>
</section>

{{-- Divider --}}
<div class="bg-[#FBE7E4]/50 py-3 flex justify-center">
  <div class="w-48 h-0.5 bg-gradient-to-r from-transparent via-[#7F9272]/30 to-transparent"></div>
</div>

{{-- ===== CURATED FEATURED PICKS ===== --}}
@php
  $curatedProducts = ($featured && $featured->count()) ? $featured : $featuredProducts;
@endphp
@if($curatedProducts && $curatedProducts->count())
<section id="curatedPicks" class="py-16 lg:py-24 px-6 lg:px-10 bg-[#FBE7E4]/50">
  <div class="max-w-7xl mx-auto">
    <div class="text-center mb-12 reveal">
      <p class="text-[#7F9272] text-xs font-bold uppercase tracking-widest mb-1">
        {{ \App\Models\Setting::get('buttercup_curated_badge', 'Curated') }}
      </p>
      <h2 class="font-display text-3xl md:text-5xl text-center text-[#4A3B3A]">
        {{ \App\Models\Setting::get('buttercup_curated_title', 'Curated Featured Picks') }}
      </h2>
      <p class="font-body text-[#4A3B3A]/70 text-xs md:text-sm max-w-xl mx-auto mt-2 leading-relaxed">
        {{ \App\Models\Setting::get('buttercup_curated_subtitle', 'Everyday cloud-soft intimates, wire-free comfort, and breathable cotton modal sets made for real bodies.') }}
      </p>
      <div class="flex justify-center mt-3">
        <div class="w-24 h-0.5 bg-gradient-to-r from-transparent via-[#D89AA0]/50 to-transparent"></div>
      </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
      @foreach($curatedProducts->take(8) as $product)
        <div class="reveal">
          @include('storefront.buttercup.partials.product-card', ['product' => $product])
        </div>
      @endforeach
    </div>

    <div class="text-center mt-12 reveal">
      <a href="{{ route('featured.products') }}" class="px-8 py-3.5 text-xs font-bold uppercase tracking-widest transition-all bg-[#D89AA0] text-[#FFFAF5] hover:bg-[#B97680] rounded-full inline-block shadow-sm">
        Explore Curated Edit 🌸
      </a>
    </div>
  </div>
</section>
@endif

{{-- Divider --}}
<div class="bg-[#FFFAF5] py-3 flex justify-center">
  <div class="w-48 h-0.5 bg-gradient-to-r from-transparent via-[#D89AA0]/30 to-transparent"></div>
</div>

{{-- ═══════════════════════════════════════════════
     FLASH SALE / SPECIAL OFFERS SECTION (REF IMAGE DESIGN)
═══════════════════════════════════════════════ --}}
@if(isset($saleProducts) && $saleProducts->count())
<section class="py-16 lg:py-24 px-6 lg:px-10 bg-[#FFFAF5]" id="saleSection">
  <div class="max-w-7xl mx-auto">
    <div class="text-center mb-10 reveal">
      <p class="text-[#7F9272] text-xs font-bold uppercase tracking-widest mb-1">{{ \App\Models\Setting::get('buttercup_sale_kicker', 'Limited Time Deals') }}</p>
      <h2 class="font-display text-3xl md:text-5xl text-center text-[#4A3B3A]">{{ \App\Models\Setting::get('buttercup_sale_title', 'Special Sale Offers') }}</h2>
      <p class="font-body text-[#4A3B3A]/70 text-xs md:text-sm max-w-xl mx-auto mt-2 leading-relaxed">{{ \App\Models\Setting::get('buttercup_sale_subtitle', 'Explore cloud-soft comfort sets and everyday silhouettes at celebratory sale prices.') }}</p>
    </div>

    {{-- Category filter tabs matching Reference Design --}}
    <div class="flex flex-wrap justify-center gap-2 mb-10 reveal" id="saleFilterWrap">
      <button class="sale-tab active font-body text-xs tracking-widest uppercase px-5 py-2.5 border border-[#D89AA0] text-[#B97680] bg-[#D89AA0]/15 hover:border-[#B97680] transition-colors rounded-full" data-filter="all">All</button>
      @foreach($categories->take(6) as $cat)
        <button class="sale-tab font-body text-xs tracking-widest uppercase px-5 py-2.5 border border-[#D89AA0]/30 text-[#4A3B3A]/60 hover:border-[#D89AA0] hover:text-[#B97680] transition-colors rounded-full"
                data-filter="{{ $cat->slug }}">{{ $cat->name }}</button>
      @endforeach
    </div>

    {{-- Products grid --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5" id="saleGrid">
      @foreach($saleProducts->take(8) as $product)
        <div class="sale-card reveal" data-category="{{ $product->category_slugs_string }}">
          @include('storefront.buttercup.partials.sale-card', ['product' => $product])
        </div>
      @endforeach
    </div>

    <div class="text-center mt-12 reveal">
      <a href="{{ route('collections.sale') }}" class="px-8 py-3.5 text-xs font-bold uppercase tracking-widest transition-all bg-[#D89AA0] text-[#FFFAF5] hover:bg-[#B97680] rounded-full inline-block shadow-sm">
        Explore All Deals 🌸
      </a>
    </div>
  </div>
</section>

{{-- Divider --}}
<div class="bg-[#FFFAF5] py-3 flex justify-center">
  <div class="w-48 h-0.5 bg-gradient-to-r from-transparent via-[#D89AA0]/30 to-transparent"></div>
</div>
@endif

{{-- ===== BESTSELLERS (WITH CATEGORY FILTER TABS) ===== --}}
@php
  $bestSellerItems = ($bestsellers && $bestsellers->count()) ? $bestsellers : $featuredProducts;
@endphp
@if($bestSellerItems && $bestSellerItems->count())
<section class="py-16 lg:py-24 px-6 lg:px-10" style="background:#FFFAF5" id="bestSellers">
  <div class="max-w-7xl mx-auto">
    <div class="text-center mb-10 reveal">
      <p class="text-[#7F9272] text-xs font-bold uppercase tracking-widest mb-1">Most Loved</p>
      <h2 class="font-display text-3xl md:text-5xl text-center text-[#4A3B3A]">Best Sellers</h2>
    </div>

    {{-- Category filter tabs --}}
    <div class="flex flex-wrap justify-center gap-2 mb-10 reveal" id="bsFilterWrap">
      <button class="bs-tab active font-body text-xs tracking-widest uppercase px-5 py-2.5 border border-[#D89AA0] text-[#B97680] hover:border-[#B97680] transition-colors rounded-full" data-filter="all">All</button>
      @foreach($categories->take(5) as $cat)
        <button class="bs-tab font-body text-xs tracking-widest uppercase px-5 py-2.5 border border-[#D89AA0]/30 text-[#4A3B3A]/60 hover:border-[#D89AA0] hover:text-[#B97680] transition-colors rounded-full"
                data-filter="{{ $cat->slug }}">{{ $cat->name }}</button>
      @endforeach
    </div>

    {{-- Products grid --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5" id="bsGrid">
      @foreach($bestSellerItems as $product)
        <div class="bs-card reveal" data-category="{{ $product->category_slugs_string }}">
          @include('storefront.buttercup.partials.product-card', ['product' => $product])
        </div>
      @endforeach
    </div>

    <div class="text-center mt-12 reveal">
      <a href="{{ route('search') }}" class="px-8 py-3.5 text-xs font-bold uppercase tracking-widest transition-all border border-[#D89AA0] text-[#4A3B3A] hover:bg-[#D89AA0] hover:text-white rounded-full inline-block">
        View Full Collection
      </a>
    </div>
  </div>
</section>
@endif

{{-- Divider --}}
<div class="bg-[#FBE7E4]/50 py-3 flex justify-center">
  <div class="w-48 h-0.5 bg-gradient-to-r from-transparent via-[#7F9272]/30 to-transparent"></div>
</div>

{{-- ===== ABOUT / STORY (2-COLUMN MATCHING SAAYA) ===== --}}
<section id="about" class="py-16 lg:py-24 px-6 lg:px-10 bg-[#FBE7E4]/50">
  <div class="max-w-7xl mx-auto grid lg:grid-cols-2 gap-12 lg:gap-20 items-center">
    
    {{-- Left: Story Image Container --}}
    <div class="reveal flex justify-center lg:justify-start">
      <div class="aspect-[3/4] w-72 lg:w-96 rounded-[32px] overflow-hidden border-2 border-[#D89AA0]/40 shadow-md relative bg-[#FFFAF5]">
        @php
          $bcStoryImg = \App\Models\Setting::get('buttercup_story_image');
        @endphp
        @if($bcStoryImg)
          <img src="{{ \App\Models\Setting::imageUrl($bcStoryImg) }}" alt="Our Story" class="w-full h-full object-cover">
        @else
          <div class="w-full h-full bg-gradient-to-b from-[#FBE7E4] to-[#FFFAF5] flex flex-col items-center justify-center p-8 text-center border border-[#D89AA0]/20">
            <svg viewBox="0 0 100 120" class="w-24 h-28 text-[#D89AA0]/40 mb-3" fill="currentColor">
              <ellipse cx="50" cy="25" rx="16" ry="20"/>
              <path d="M15 48 Q30 38 50 42 Q70 38 85 48 L88 105 Q70 115 50 112 Q30 115 12 105 Z"/>
            </svg>
            <p class="font-display text-sm font-bold text-[#B97680]">Buttercup Studio</p>
            <p class="font-body text-xs text-[#4A3B3A]/60 mt-1">Comfort that feels like home</p>
          </div>
        @endif
      </div>
    </div>

    {{-- Right: Text + Stats --}}
    <div class="reveal">
      <p class="text-[#7F9272] text-xs font-bold uppercase tracking-widest mb-2">
        {{ \App\Models\Setting::get('buttercup_story_badge', 'Our Story') }}
      </p>
      <h2 class="font-display text-3xl md:text-5xl mb-6 text-[#4A3B3A] leading-tight">
        {!! nl2br(e(\App\Models\Setting::get('buttercup_story_title', "Designed for Real Bodies,\nReal Days"))) !!}
      </h2>
      <p class="font-body text-[#4A3B3A]/70 leading-relaxed mb-4">
        {{ \App\Models\Setting::get('buttercup_story_p1', 'Buttercup started with one simple question: why should comfort and style be a trade-off? We design every piece with breathable, skin-friendly fabric — soft enough for everyday, pretty enough to feel special.') }}
      </p>
      <p class="font-body text-[#4A3B3A]/70 leading-relaxed mb-10">
        {{ \App\Models\Setting::get('buttercup_story_p2', 'Zero painful underwires, zero pinch. Just butter-soft modal, organic cotton, and gentle stretch that moves with you from dawn to dusk.') }}
      </p>

      {{-- Stats --}}
      <div class="grid grid-cols-3 gap-4 border-t border-[#D89AA0]/30 pt-8">
        @php
          $bcStat1Num = \App\Models\Setting::get('buttercup_stat1_num', '5+');
          $bcStat1Lbl = \App\Models\Setting::get('buttercup_stat1_label', 'Years of Comfort');
          $bcStat2Num = \App\Models\Setting::get('buttercup_stat2_num', '2L+');
          $bcStat2Lbl = \App\Models\Setting::get('buttercup_stat2_label', 'Happy Customers');
          $bcStat3Num = \App\Models\Setting::get('buttercup_stat3_num', '100%');
          $bcStat3Lbl = \App\Models\Setting::get('buttercup_stat3_label', 'Skin-Friendly');
        @endphp
        @foreach([[$bcStat1Num, $bcStat1Lbl], [$bcStat2Num, $bcStat2Lbl], [$bcStat3Num, $bcStat3Lbl]] as [$num, $label])
          <div class="text-center">
            <p class="font-display text-3xl lg:text-4xl text-[#B97680]">{{ $num }}</p>
            <p class="font-body text-xs text-[#4A3B3A]/60 uppercase tracking-widest mt-1">{{ $label }}</p>
          </div>
        @endforeach
      </div>
    </div>

  </div>
</section>

{{-- ===== CUSTOMER REVIEWS ===== --}}
@php
  $displayReviews = (isset($reviews) && $reviews->count()) 
      ? $reviews 
      : \App\Models\ProductReview::where('is_approved', true)->with(['product', 'user.addresses'])->latest()->take(3)->get();
@endphp
@if($displayReviews && $displayReviews->count())
<section class="py-12 lg:py-16 px-6 lg:px-10 bg-[#FFFAF5]">
  <div class="max-w-7xl mx-auto">
    <div class="text-center mb-10 reveal">
      <p class="text-[#7F9272] text-xs font-bold uppercase tracking-widest mb-1">Kind Words</p>
      <h2 class="font-display text-3xl md:text-4xl text-[#4A3B3A]">Loved by Real Bodies</h2>
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
        <div class="bg-[#FFFDFB] rounded-[28px] p-6 sm:p-8 border border-[#D89AA0]/20 shadow-xs flex flex-col justify-between hover:shadow-md transition">
          <div>
            <div class="flex items-center gap-1 text-amber-400 mb-3 text-sm">
              @for($i = 1; $i <= 5; $i++)
                <span>{{ $i <= ($rev->rating ?? 5) ? '★' : '☆' }}</span>
              @endfor
            </div>
            @if($rev->title)
              <h3 class="font-body font-bold text-[#4A3B3A] text-sm mb-1.5">{{ $rev->title }}</h3>
            @endif
            <p class="font-body text-xs text-[#4A3B3A]/80 italic leading-relaxed">
              "{{ $rev->comment }}"
            </p>
          </div>
          <div class="mt-6 pt-4 border-t border-[#D89AA0]/15 flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-[#FBE7E4] text-[#B97680] font-bold flex items-center justify-center text-xs shrink-0">
              {{ strtoupper($initials ?: 'VB') }}
            </div>
            <div>
              <h4 class="font-body text-xs font-bold text-[#4A3B3A]">{{ $rev->customer_name }}</h4>
              <span class="text-[10px] text-[#B97680]">
                Verified Buyer @if($location) • {{ $location }} @endif
              </span>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</section>

{{-- Divider --}}
<div class="bg-[#FFFAF5] py-2 flex justify-center">
  <div class="w-48 h-0.5 bg-gradient-to-r from-transparent via-[#D89AA0]/30 to-transparent"></div>
</div>
@endif

{{-- ===== CONTACT ===== --}}
<section id="contact" class="py-16 lg:py-24 px-6 lg:px-10" style="background:#FFFAF5">
  <div class="max-w-xl mx-auto text-center reveal">
    <p class="text-[#7F9272] text-xs font-bold uppercase tracking-widest mb-2">Get in Touch</p>
    <h2 class="font-display text-3xl md:text-4xl mb-4 text-[#4A3B3A]">We're Here to Help</h2>
    <p class="font-body text-[#4A3B3A]/60 text-sm mb-10">We reply within one business day.</p>
    
    <form action="{{ route('contact.submit') }}" method="POST" class="grid sm:grid-cols-2 gap-4 text-left">
      @csrf
      <input type="text"  name="name"    placeholder="Full Name"      required class="rounded-2xl border px-4 py-3.5 text-sm outline-none w-full bg-[#FFFAF5] font-body" style="border-color:#D89AA044;color:#4A3B3A">
      <input type="email" name="email"   placeholder="Email Address"  required class="rounded-2xl border px-4 py-3.5 text-sm outline-none w-full bg-[#FFFAF5] font-body" style="border-color:#D89AA044;color:#4A3B3A">
      <textarea name="message" placeholder="Your Message…" rows="4" required class="sm:col-span-2 rounded-2xl border px-4 py-3.5 text-sm outline-none w-full bg-[#FFFAF5] font-body resize-none" style="border-color:#D89AA044;color:#4A3B3A"></textarea>
      <button type="submit" class="sm:col-span-2 py-3.5 text-xs font-bold uppercase tracking-widest transition-all bg-[#D89AA0] text-[#FFFAF5] hover:bg-[#B97680] rounded-full">Send Message 🌸</button>
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
        t.classList.remove('active', 'border-[#D89AA0]', 'text-[#B97680]', 'bg-[#D89AA0]/15');
        t.classList.add('border-[#D89AA0]/30', 'text-[#4A3B3A]/60');
      });
      tab.classList.add('active', 'border-[#D89AA0]', 'text-[#B97680]', 'bg-[#D89AA0]/15');
      tab.classList.remove('border-[#D89AA0]/30', 'text-[#4A3B3A]/60');

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
          emptyMsg.className = 'col-span-full py-16 text-center text-[#4A3B3A]/60 font-body';
          emptyMsg.innerHTML = '<p class="text-base font-semibold text-[#4A3B3A]/80 mb-1">No items found</p><p class="text-xs text-[#4A3B3A]/50">No promotional deals currently available in this collection.</p>';
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
        t.classList.remove('active','border-[#D89AA0]','text-[#B97680]');
        t.classList.add('border-[#D89AA0]/30','text-[#4A3B3A]/60');
      });
      tab.classList.add('active','border-[#D89AA0]','text-[#B97680]');
      tab.classList.remove('border-[#D89AA0]/30','text-[#4A3B3A]/60');

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
          emptyMsg.className = 'col-span-full py-16 text-center text-[#4A3B3A]/60 font-body';
          emptyMsg.innerHTML = '<p class="text-base font-semibold text-[#4A3B3A]/80 mb-1">No items found</p><p class="text-xs text-[#4A3B3A]/50">No best-selling items currently available in this collection.</p>';
          bsGrid.appendChild(emptyMsg);
        } else if (emptyMsg) {
          emptyMsg.style.display = '';
        }
      } else if (emptyMsg) {
        emptyMsg.style.display = 'none';
      }
    });
  });

  document.querySelector('.bs-tab.active')?.classList.add('border-[#D89AA0]','text-[#B97680]');
  document.querySelector('.bs-tab.active')?.classList.remove('border-[#D89AA0]/30','text-[#4A3B3A]/60');
})();
</script>
@endpush

</x-storefront-layout>
