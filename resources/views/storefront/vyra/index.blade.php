{{-- resources/views/storefront/vyra/index.blade.php --}}
<x-storefront-layout :theme="$theme" title="Home">

{{-- ===== HERO TOP SLIDER WITH GLORIA-FREE VIDEO BACKGROUND ===== --}}
@php
  $vyraHeroImg   = \App\Models\Setting::get('vyra_hero_image') ?: \App\Models\Setting::get('hero_banner_image');
  $vyraHeroVideo = \App\Models\Setting::get('vyra_hero_video');
  $vyraVideoSrc  = $vyraHeroVideo ? asset('storage/' . $vyraHeroVideo) : asset('storage/slider/vyra/banner.mp4');
  $vyraPosterSrc = $vyraHeroImg ? asset('storage/' . $vyraHeroImg) : asset('storage/slider/vyra/banner-1.jpg');

  $vyraSlides = [
    [
      'badge'    => \App\Models\Setting::get('vyra_hero_badge', 'Drop 03 / Out Now'),
      'title'    => \App\Models\Setting::get('vyra_hero_title', "Wear Your\nPower"),
      'subtitle' => \App\Models\Setting::get('vyra_hero_subtitle', "Bold cuts. Mesh, lace, and second-skin fabrics built for a generation that doesn't ask permission."),
      'btn_text' => \App\Models\Setting::get('vyra_hero_btn_text', 'Shop Drop 03'),
      'btn_url'  => \App\Models\Setting::get('vyra_hero_btn_url', route('home').'#products'),
      'btn2_text'=> 'Explore',
      'btn2_url' => route('search'),
    ],
    [
      'badge'    => \App\Models\Setting::get('vyra_slide_2_badge', 'High Voltage / New In'),
      'title'    => \App\Models\Setting::get('vyra_slide_2_title', "Raw Noir\nAllure"),
      'subtitle' => \App\Models\Setting::get('vyra_slide_2_subtitle', "Sculptural silhouettes that turn heads. Precision laser cutouts engineered for fearless statement dressing."),
      'btn_text' => \App\Models\Setting::get('vyra_slide_2_btn_text', 'Explore Noir'),
      'btn_url'  => \App\Models\Setting::get('vyra_slide_2_btn_url', route('home').'#products'),
      'btn2_text'=> 'View Lookbook',
      'btn2_url' => route('featured.products'),
    ],
    [
      'badge'    => \App\Models\Setting::get('vyra_slide_3_badge', 'Street Lingerie Edition'),
      'title'    => \App\Models\Setting::get('vyra_slide_3_title', "Second Skin\nConfidence"),
      'subtitle' => \App\Models\Setting::get('vyra_slide_3_subtitle', "Ultra-soft compression tulle and subversive tailoring. Built to feel completely invisible under every fit."),
      'btn_text' => \App\Models\Setting::get('vyra_slide_3_btn_text', 'Shop The Edit'),
      'btn_url'  => \App\Models\Setting::get('vyra_slide_3_btn_url', route('home').'#products'),
      'btn2_text'=> 'Discover More',
      'btn2_url' => route('search'),
    ],
    [
      'badge'    => \App\Models\Setting::get('vyra_slide_4_badge', 'Limited Capsule 2026'),
      'title'    => \App\Models\Setting::get('vyra_slide_4_title', "Futuristic\nEdge"),
      'subtitle' => \App\Models\Setting::get('vyra_slide_4_subtitle', "Where intimate craft meets industrial streetwear. Designed for bodies that command attention."),
      'btn_text' => \App\Models\Setting::get('vyra_slide_4_btn_text', 'Shop Capsule'),
      'btn_url'  => \App\Models\Setting::get('vyra_slide_4_btn_url', route('home').'#products'),
      'btn2_text'=> 'Explore All',
      'btn2_url' => route('featured.products'),
    ],
  ];
@endphp

<section class="relative bg-[#0E0E10] overflow-hidden min-h-[580px] sm:min-h-[680px] lg:min-h-[780px] flex items-center select-none"
         x-data="{
           currentSlide: 0,
           totalSlides: {{ count($vyraSlides) }},
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
         aria-label="Vyra Top Hero Slider">

  {{-- FULL BACKGROUND VIDEO (from gloria-free banner.mp4 stored in public/storage/slider/vyra) --}}
  <video class="hero-video absolute inset-0 w-full h-full object-cover object-center pointer-events-none z-0 opacity-100"
         autoplay
         muted
         loop
         playsinline
         preload="auto"
         poster="{{ $vyraPosterSrc }}">
    <source src="{{ $vyraVideoSrc }}" type="video/mp4">
    Your browser does not support the video tag.
  </video>

  {{-- SUBTLE SOFT OVERLAY (MINIMAL BLACK GRADIENT FOR MAXIMUM VIDEO CLARITY) --}}
  <div class="absolute inset-0 z-10 pointer-events-none bg-gradient-to-r from-black/40 via-black/15 to-transparent"></div>

  {{-- SLIDER CONTENT (CONTAINER) --}}
  <div class="relative z-20 max-w-7xl mx-auto px-6 lg:px-10 py-16 sm:py-24 w-full grid lg:grid-cols-12 gap-8 items-center">
    
    {{-- Left: Text Slides --}}
    <div class="lg:col-span-8 max-w-xl">
      @foreach($vyraSlides as $index => $slide)
        <div x-show="currentSlide === {{ $index }}"
             x-transition:enter="transition ease-out duration-700"
             x-transition:enter-start="opacity-0 translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-100 absolute"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2">
          
          <p class="text-[#D4FF3D] text-xs font-bold uppercase tracking-widest mb-3 flex items-center gap-2 drop-shadow-md">
            <span class="w-2 h-2 rounded-full bg-[#D4FF3D] inline-block animate-pulse"></span>
            <span>{{ $slide['badge'] }}</span>
          </p>

          @php
            $titleLines = explode("\n", str_replace(["\r\n", "\r"], "\n", $slide['title']));
          @endphp
          <h1 class="font-display uppercase tracking-tight text-5xl md:text-6xl text-[#F2F0EC] leading-tight mb-6 drop-shadow-[0_4px_16px_rgba(0,0,0,0.85)]">
            @if(count($titleLines) > 1)
              {{ $titleLines[0] }}<br><span class="text-[#FF3D5A]">{{ implode(' ', array_slice($titleLines, 1)) }}</span>
            @else
              {{ $slide['title'] }}
            @endif
          </h1>

          <p class="font-body text-[#F2F0EC]/90 max-w-md mb-8 leading-relaxed text-sm sm:text-base drop-shadow-md">
            {{ $slide['subtitle'] }}
          </p>

          <div class="flex flex-wrap gap-4 items-center">
            <a href="{{ $slide['btn_url'] }}" 
               class="px-8 py-3.5 text-xs font-bold uppercase tracking-widest transition-all bg-[#FF3D5A] text-[#0E0E10] hover:bg-[#D4FF3D] shadow-lg hover:shadow-[#FF3D5A]/30" 
               style="clip-path:polygon(6% 0,100% 0,94% 100%,0 100%)">
              {{ $slide['btn_text'] }}
            </a>
            <a href="{{ $slide['btn2_url'] }}" 
               class="px-8 py-3.5 text-xs font-bold uppercase tracking-widest transition-all border border-[#FF3D5A] text-[#F2F0EC] hover:bg-white/10 backdrop-blur-xs">
              {{ $slide['btn2_text'] }}
            </a>
          </div>

        </div>
      @endforeach
    </div>

    {{-- Right: Optional Custom Image Frame if uploaded by Admin --}}
    @if($vyraHeroImg)
      <div class="lg:col-span-4 hidden lg:flex justify-end">
        <div class="overflow-hidden aspect-[4/5] w-72 rounded-2xl border border-[#FF3D5A]/30 shadow-2xl bg-black/60 backdrop-blur-sm p-1.5 group">
          <img src="{{ asset('storage/' . $vyraHeroImg) }}" alt="{{ $vyraSlides[0]['title'] }}" class="w-full h-full object-cover rounded-xl transition duration-500 group-hover:scale-105">
        </div>
      </div>
    @endif

  </div>

  {{-- GLORIA-INSPIRED VERTICAL "01 02 03 04" PAGINATION INDEX (Desktop Right) --}}
  <div class="hidden md:flex flex-col items-end gap-3 absolute right-8 top-1/2 -translate-y-1/2 z-30 font-mono">
    @foreach($vyraSlides as $index => $slide)
      <button type="button"
              @click="goToSlide({{ $index }})"
              class="transition-all duration-300 font-bold focus:outline-none flex items-center gap-2 group cursor-pointer"
              :class="currentSlide === {{ $index }} ? 'text-[#D4FF3D] scale-110' : 'text-white/40 hover:text-white/80'">
        <span class="text-[10px] uppercase tracking-wider opacity-0 group-hover:opacity-100 transition-opacity"
              :class="currentSlide === {{ $index }} ? 'opacity-100 text-[#D4FF3D]' : ''">
          {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
        </span>
        <span class="w-7 h-0.5 rounded-full transition-all duration-300"
              :class="currentSlide === {{ $index }} ? 'w-10 bg-[#FF3D5A]' : 'bg-white/30 group-hover:bg-white/60'"></span>
      </button>
    @endforeach
  </div>

</section>

{{-- Scrolling Announcement Band --}}
@include('storefront.common.scrolling-band')

{{-- Petal / Neon divider --}}
<div class="bg-[#0E0E10] py-3 flex justify-center">
  <div class="w-48 h-0.5 bg-gradient-to-r from-transparent via-[#FF3D5A]/50 to-transparent"></div>
</div>

{{-- ═══════════════════════════════════════════════
     CATEGORY STRIP (Slideable Square Boxes like Saaya)
═══════════════════════════════════════════════ --}}
@php
  $parentCategories = $categories->filter(fn($c) => is_null($c->parent_id));
@endphp
<section class="pt-10 pb-6 lg:pt-12 lg:pb-8 px-6 lg:px-10" style="background:#0E0E10"
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
  <div class="max-w-7xl mx-auto">

    {{-- Header: Category Title + View All + Prev/Next Arrow Buttons --}}
    <div class="flex items-center justify-between mb-6">
      <div>
        <p class="text-[#D4FF3D] text-xs font-mono font-bold uppercase tracking-widest mb-1">Discover</p>
        <h2 class="font-display uppercase text-3xl sm:text-4xl lg:text-5xl text-[#F2F0EC]">Shop by Category</h2>
      </div>

      <div class="flex items-center gap-2">
        <a href="{{ route('featured.products') }}" 
           class="px-4 sm:px-5 py-2 text-xs font-mono font-bold uppercase tracking-wider text-[#FF3D5A] border border-[#FF3D5A]/50 hover:bg-[#FF3D5A] hover:text-[#0E0E10] transition-colors rounded-sm">
          View All
        </a>
        <div class="flex items-center gap-1.5">
          <button type="button" 
                  @click="scrollTrack(-1)" 
                  class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-[#1A1A1D] hover:bg-[#FF3D5A] hover:text-[#0E0E10] text-[#F2F0EC] border border-[#FF3D5A]/30 flex items-center justify-center transition text-xs font-bold focus:outline-none disabled:opacity-25 disabled:cursor-not-allowed shadow-2xs" 
                  :disabled="!canScrollLeft"
                  aria-label="Previous categories">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
          </button>
          <button type="button" 
                  @click="scrollTrack(1)" 
                  class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-[#FF3D5A] hover:bg-[#D4FF3D] text-[#0E0E10] border border-[#FF3D5A]/60 flex items-center justify-center transition text-xs font-bold shadow-2xs focus:outline-none disabled:opacity-25 disabled:cursor-not-allowed" 
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
           class="category-item group relative overflow-hidden shrink-0 w-[170px] sm:w-[200px] md:w-[220px] lg:w-[230px] xl:w-[245px] transition-transform duration-300 shadow-md hover:shadow-2xl border border-[#FF3D5A]/20 hover:border-[#FF3D5A]/60"
           style="aspect-ratio: 3/4; scroll-snap-align: start; scroll-snap-stop: normal;">
          {{-- Category image or gradient --}}
          <div class="card-img-wrap absolute inset-0">
            @if($cat->image_url)
              <img src="{{ $cat->image_url }}" alt="{{ $cat->name }}"
                   class="card-img-zoom w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                   loading="lazy">
            @else
              <div class="w-full h-full bg-gradient-to-b from-[#1A1A1D] via-[#242429] to-[#0E0E10] flex items-center justify-center">
                <svg viewBox="0 0 60 60" class="w-12 h-12 text-[#FF3D5A]/30" fill="currentColor">
                  <circle cx="30" cy="30" r="18"/>
                </svg>
              </div>
            @endif
          </div>
          {{-- Cyber / Dark gradient overlay --}}
          <div class="absolute inset-0 bg-gradient-to-t from-[#0E0E10] via-[#0E0E10]/40 to-transparent"></div>
          {{-- Label --}}
          <div class="absolute bottom-0 left-0 right-0 p-4 lg:p-5">
            <p class="font-display uppercase text-[#F2F0EC] text-base sm:text-lg lg:text-xl leading-tight line-clamp-2">{{ $cat->name }}</p>
            <p class="font-mono text-[#D4FF3D] text-[10px] sm:text-xs tracking-widest uppercase mt-1 flex items-center gap-1 font-bold">
              Shop Now
              <svg class="w-3 h-3 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/></svg>
            </p>
          </div>
          {{-- Neon corner accents --}}
          <div class="absolute top-2.5 right-2.5 w-4 h-4 border-t-2 border-r-2 border-[#FF3D5A] opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
          <div class="absolute bottom-2.5 left-2.5 w-4 h-4 border-b-2 border-l-2 border-[#D4FF3D] opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
        </a>
      @empty
        @php
          $defaultVyraCats = [
            'Power Bodysuits',
            'Sculpt Corsets',
            'Second-Skin Mesh',
            'Cut-Out Sets',
            'Neon Latex',
            'Statement Bras',
          ];
        @endphp
        @foreach($defaultVyraCats as $fallbackName)
          <a href="{{ route('featured.products') }}"
             class="category-item group relative overflow-hidden shrink-0 w-[170px] sm:w-[200px] md:w-[220px] lg:w-[230px] xl:w-[245px] transition-transform duration-300 shadow-md hover:shadow-2xl border border-[#FF3D5A]/20 hover:border-[#FF3D5A]/60"
             style="aspect-ratio: 3/4; scroll-snap-align: start; scroll-snap-stop: normal;">
            <div class="card-img-wrap absolute inset-0">
              <div class="w-full h-full bg-gradient-to-b from-[#1A1A1D] via-[#242429] to-[#0E0E10] flex items-center justify-center">
                <svg viewBox="0 0 60 60" class="w-12 h-12 text-[#FF3D5A]/30" fill="currentColor">
                  <circle cx="30" cy="30" r="18"/>
                </svg>
              </div>
            </div>
            <div class="absolute inset-0 bg-gradient-to-t from-[#0E0E10] via-[#0E0E10]/40 to-transparent"></div>
            <div class="absolute bottom-0 left-0 right-0 p-4 lg:p-5">
              <p class="font-display uppercase text-[#F2F0EC] text-base sm:text-lg lg:text-xl leading-tight line-clamp-2">{{ $fallbackName }}</p>
              <p class="font-mono text-[#D4FF3D] text-[10px] sm:text-xs tracking-widest uppercase mt-1 flex items-center gap-1 font-bold">
                Shop Now
                <svg class="w-3 h-3 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/></svg>
              </p>
            </div>
            <div class="absolute top-2.5 right-2.5 w-4 h-4 border-t-2 border-r-2 border-[#FF3D5A] opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
            <div class="absolute bottom-2.5 left-2.5 w-4 h-4 border-b-2 border-l-2 border-[#D4FF3D] opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
          </a>
        @endforeach
      @endforelse
    </div>

  </div>
</section>

{{-- Divider --}}
<div class="bg-[#1A1A1D] py-3 flex justify-center">
  <div class="w-48 h-0.5 bg-gradient-to-r from-transparent via-[#D4FF3D]/40 to-transparent"></div>
</div>

{{-- ===== CURATED FEATURED PICKS ===== --}}
@php
  $curatedProducts = ($featured && $featured->count()) ? $featured : $featuredProducts;
@endphp
@if($curatedProducts && $curatedProducts->count())
<section id="curatedPicks" class="py-16 lg:py-24 px-6 lg:px-10 bg-[#1A1A1D]">
  <div class="max-w-7xl mx-auto">
    <div class="text-center mb-12 reveal">
      <p class="text-[#D4FF3D] text-xs font-bold uppercase tracking-widest mb-1">
        {{ \App\Models\Setting::get('vyra_curated_badge', 'Curated') }}
      </p>
      <h2 class="font-display uppercase text-3xl md:text-5xl text-[#F2F0EC]">
        {{ \App\Models\Setting::get('vyra_curated_title', 'Curated Featured Picks') }}
      </h2>
      <p class="font-body text-[#F2F0EC]/60 text-xs md:text-sm max-w-xl mx-auto mt-2 leading-relaxed">
        {{ \App\Models\Setting::get('vyra_curated_subtitle', 'Bold cuts, second-skin mesh, and power silhouettes engineered for modern confidence.') }}
      </p>
      <div class="flex justify-center mt-3">
        <div class="w-24 h-0.5 bg-gradient-to-r from-transparent via-[#FF3D5A]/60 to-transparent"></div>
      </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 lg:gap-6">
      @foreach($curatedProducts->take(8) as $product)
        <div class="reveal">
          @include('storefront.vyra.partials.product-card', ['product' => $product])
        </div>
      @endforeach
    </div>

    <div class="text-center mt-12 reveal">
      <a href="{{ route('featured.products') }}" class="px-8 py-3.5 text-xs font-bold uppercase tracking-widest transition-all bg-[#FF3D5A] text-[#0E0E10] hover:bg-[#D4FF3D] inline-block" style="clip-path:polygon(6% 0,100% 0,94% 100%,0 100%)">
        Explore Curated Edit
      </a>
    </div>
  </div>
</section>
@endif

{{-- Divider --}}
<div class="bg-[#0E0E10] py-3 flex justify-center">
  <div class="w-48 h-0.5 bg-gradient-to-r from-transparent via-[#FF3D5A]/40 to-transparent"></div>
</div>

{{-- ═══════════════════════════════════════════════
     FLASH SALE / SPECIAL OFFERS SECTION (REF IMAGE DESIGN)
═══════════════════════════════════════════════ --}}
@if(isset($saleProducts) && $saleProducts->count())
<section class="py-16 lg:py-24 px-6 lg:px-10 bg-[#121214]" id="saleSection">
  <div class="max-w-7xl mx-auto">
    <div class="text-center mb-10 reveal">
      <p class="text-[#D4FF3D] text-xs font-mono font-bold uppercase tracking-widest mb-1">{{ \App\Models\Setting::get('vyra_sale_kicker', '⚡ Limited Drops') }}</p>
      <h2 class="font-display uppercase text-3xl md:text-5xl text-[#F2F0EC]">{{ \App\Models\Setting::get('vyra_sale_title', 'Special Sale Offers') }}</h2>
      <p class="font-body text-[#F2F0EC]/60 text-xs md:text-sm max-w-xl mx-auto mt-2 leading-relaxed">{{ \App\Models\Setting::get('vyra_sale_subtitle', 'High-impact power silhouettes, sculpting mesh, and daring cuts at exclusive promotional drops.') }}</p>
    </div>

    {{-- Category filter tabs matching Reference Design --}}
    <div class="flex flex-wrap justify-center gap-2 mb-10 reveal" id="saleFilterWrap">
      <button class="sale-tab active font-body text-xs tracking-widest uppercase px-5 py-2.5 border border-[#FF3D5A] text-[#FF3D5A] bg-[#FF3D5A]/15 hover:border-[#D4FF3D] hover:text-[#D4FF3D] transition-colors rounded-full" data-filter="all">All</button>
      @foreach($categories->take(6) as $cat)
        <button class="sale-tab font-body text-xs tracking-widest uppercase px-5 py-2.5 border border-[#F2F0EC]/20 text-[#F2F0EC]/60 hover:border-[#FF3D5A] hover:text-[#FF3D5A] transition-colors rounded-full"
                data-filter="{{ $cat->slug }}">{{ $cat->name }}</button>
      @endforeach
    </div>

    {{-- Products grid --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 lg:gap-6" id="saleGrid">
      @foreach($saleProducts->take(8) as $product)
        <div class="sale-card reveal" data-category="{{ $product->category_slugs_string }}">
          @include('storefront.vyra.partials.sale-card', ['product' => $product])
        </div>
      @endforeach
    </div>

    <div class="text-center mt-12 reveal">
      <a href="{{ route('collections.sale') }}" class="px-8 py-3.5 text-xs font-bold uppercase tracking-widest transition-all bg-[#FF3D5A] text-[#0E0E10] hover:bg-[#D4FF3D] inline-block font-mono">
        Explore All Drops ⚡
      </a>
    </div>
  </div>
</section>

{{-- Divider --}}
<div class="bg-[#0E0E10] py-3 flex justify-center">
  <div class="w-48 h-0.5 bg-gradient-to-r from-transparent via-[#FF3D5A]/40 to-transparent"></div>
</div>
@endif

{{-- ===== BESTSELLERS (WITH CATEGORY FILTER TABS) ===== --}}
@php
  $bestSellerItems = ($bestsellers && $bestsellers->count()) ? $bestsellers : $featuredProducts;
@endphp
@if($bestSellerItems && $bestSellerItems->count())
<section class="py-16 lg:py-24 px-6 lg:px-10" style="background:#0E0E10" id="bestSellers">
  <div class="max-w-7xl mx-auto">
    <div class="text-center mb-10 reveal">
      <p class="text-[#D4FF3D] text-xs font-bold uppercase tracking-widest mb-1">Most Loved</p>
      <h2 class="font-display uppercase text-3xl md:text-5xl text-[#F2F0EC]">Best Sellers</h2>
    </div>

    {{-- Category filter tabs --}}
    <div class="flex flex-wrap justify-center gap-2 mb-10 reveal" id="bsFilterWrap">
      <button class="bs-tab active font-body text-xs tracking-widest uppercase px-5 py-2.5 border border-[#FF3D5A] text-[#FF3D5A] hover:border-[#D4FF3D] hover:text-[#D4FF3D] transition-colors rounded-full" data-filter="all">All</button>
      @foreach($categories->take(5) as $cat)
        <button class="bs-tab font-body text-xs tracking-widest uppercase px-5 py-2.5 border border-[#F2F0EC]/20 text-[#F2F0EC]/60 hover:border-[#FF3D5A] hover:text-[#FF3D5A] transition-colors rounded-full"
                data-filter="{{ $cat->slug }}">{{ $cat->name }}</button>
      @endforeach
    </div>

    {{-- Products grid --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 lg:gap-6" id="bsGrid">
      @foreach($bestSellerItems as $product)
        <div class="bs-card reveal" data-category="{{ $product->category_slugs_string }}">
          @include('storefront.vyra.partials.product-card', ['product' => $product])
        </div>
      @endforeach
    </div>

    <div class="text-center mt-12 reveal">
      <a href="{{ route('search') }}" class="px-8 py-3.5 text-xs font-bold uppercase tracking-widest transition-all border border-[#FF3D5A] text-[#F2F0EC] hover:bg-[#FF3D5A] hover:text-[#0E0E10] inline-block">
        View Full Collection
      </a>
    </div>
  </div>
</section>
@endif

{{-- Divider --}}
<div class="bg-[#1A1A1D] py-3 flex justify-center">
  <div class="w-48 h-0.5 bg-gradient-to-r from-transparent via-[#D4FF3D]/40 to-transparent"></div>
</div>

{{-- ===== ABOUT / STORY (2-COLUMN MATCHING SAAYA) ===== --}}
<section id="about" class="py-16 lg:py-24 px-6 lg:px-10 bg-[#1A1A1D]">
  <div class="max-w-7xl mx-auto grid lg:grid-cols-2 gap-12 lg:gap-20 items-center">
    
    {{-- Left: Story Image Container --}}
    <div class="reveal flex justify-center lg:justify-start">
      <div class="aspect-[3/4] w-72 lg:w-96 rounded-2xl overflow-hidden border-2 border-[#FF3D5A]/40 shadow-[0_0_30px_rgba(255,61,90,0.2)] relative bg-[#0E0E10]">
        @php
          $vyraStoryImg = \App\Models\Setting::get('vyra_story_image');
        @endphp
        @if($vyraStoryImg)
          <img src="{{ asset('storage/' . $vyraStoryImg) }}" alt="Our Story" class="w-full h-full object-cover">
        @else
          <div class="w-full h-full bg-gradient-to-b from-[#1A1A1D] to-[#0E0E10] flex flex-col items-center justify-center p-8 text-center border border-[#FF3D5A]/20">
            <svg viewBox="0 0 100 120" class="w-24 h-28 text-[#FF3D5A]/30 mb-3" fill="currentColor">
              <ellipse cx="50" cy="25" rx="16" ry="20"/>
              <path d="M15 48 Q30 38 50 42 Q70 38 85 48 L88 105 Q70 115 50 112 Q30 115 12 105 Z"/>
            </svg>
            <p class="font-display uppercase text-sm font-bold text-[#FF3D5A]">Vyra Atelier</p>
            <p class="font-body text-xs text-[#F2F0EC]/50 mt-1">Built for the unapologetic</p>
          </div>
        @endif
      </div>
    </div>

    {{-- Right: Text + Stats --}}
    <div class="reveal">
      <p class="text-[#D4FF3D] text-xs font-bold uppercase tracking-widest mb-2">
        {{ \App\Models\Setting::get('vyra_story_badge', 'The Brand') }}
      </p>
      <h2 class="font-display uppercase text-3xl md:text-5xl mb-6 text-[#F2F0EC] leading-tight">
        {!! nl2br(e(\App\Models\Setting::get('vyra_story_title', "Built for the\nUnapologetic"))) !!}
      </h2>
      <p class="font-body text-[#F2F0EC]/70 leading-relaxed mb-4">
        {{ \App\Models\Setting::get('vyra_story_p1', 'VYRA launched out of a Bangalore studio, tired of lingerie that played it safe. We design for the generation raising the bar — bold colors, real fits, fabric tech that moves the way you do.') }}
      </p>
      <p class="font-body text-[#F2F0EC]/70 leading-relaxed mb-10">
        {{ \App\Models\Setting::get('vyra_story_p2', 'Engineered with second-skin breathability, ergonomic support, and cyber-luxe aesthetics made to celebrate your personal strength without compromise.') }}
      </p>

      {{-- Stats --}}
      <div class="grid grid-cols-3 gap-4 border-t border-[#FF3D5A]/20 pt-8">
        @php
          $vStat1Num = \App\Models\Setting::get('vyra_stat1_num', '3+');
          $vStat1Lbl = \App\Models\Setting::get('vyra_stat1_label', 'Years');
          $vStat2Num = \App\Models\Setting::get('vyra_stat2_num', '50K+');
          $vStat2Lbl = \App\Models\Setting::get('vyra_stat2_label', 'Community');
          $vStat3Num = \App\Models\Setting::get('vyra_stat3_num', '15+');
          $vStat3Lbl = \App\Models\Setting::get('vyra_stat3_label', 'Cities');
        @endphp
        @foreach([[$vStat1Num, $vStat1Lbl], [$vStat2Num, $vStat2Lbl], [$vStat3Num, $vStat3Lbl]] as [$num, $label])
          <div class="text-center">
            <p class="font-display text-3xl lg:text-4xl text-[#FF3D5A]">{{ $num }}</p>
            <p class="font-body text-xs text-[#F2F0EC]/60 uppercase tracking-widest mt-1">{{ $label }}</p>
          </div>
        @endforeach
      </div>
    </div>

  </div>
</section>

{{-- Divider --}}
<div class="bg-[#0E0E10] py-3 flex justify-center">
  <div class="w-48 h-0.5 bg-gradient-to-r from-transparent via-[#FF3D5A]/40 to-transparent"></div>
</div>

{{-- ===== CONTACT ===== --}}
<section id="contact" class="py-16 lg:py-24 px-6 lg:px-10" style="background:#0E0E10">
  <div class="max-w-xl mx-auto text-center reveal">
    <p class="text-[#D4FF3D] text-xs font-bold uppercase tracking-widest mb-2">Reach Out</p>
    <h2 class="font-display uppercase text-3xl md:text-4xl mb-4 text-[#F2F0EC]">Let's Talk</h2>
    <p class="font-body text-[#F2F0EC]/60 text-sm mb-10">We reply within one business day.</p>
    
    <form action="{{ route('contact.submit') }}" method="POST" class="grid sm:grid-cols-2 gap-4 text-left">
      @csrf
      <input type="text"  name="name"    placeholder="Full Name"      required class="border px-4 py-3.5 text-sm outline-none w-full bg-transparent font-body" style="border-color:#FF3D5A44;color:#F2F0EC">
      <input type="email" name="email"   placeholder="Email Address"  required class="border px-4 py-3.5 text-sm outline-none w-full bg-transparent font-body" style="border-color:#FF3D5A44;color:#F2F0EC">
      <textarea name="message" placeholder="Your Message…" rows="4" required class="sm:col-span-2 border px-4 py-3.5 text-sm outline-none w-full bg-transparent font-body resize-none" style="border-color:#FF3D5A44;color:#F2F0EC"></textarea>
      <button type="submit" class="sm:col-span-2 py-3.5 text-xs font-bold uppercase tracking-widest transition-all bg-[#FF3D5A] text-[#0E0E10] hover:bg-[#D4FF3D]">Send Message</button>
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
        t.classList.remove('active', 'border-[#FF3D5A]', 'text-[#FF3D5A]', 'bg-[#FF3D5A]/15');
        t.classList.add('border-[#F2F0EC]/20', 'text-[#F2F0EC]/60');
      });
      tab.classList.add('active', 'border-[#FF3D5A]', 'text-[#FF3D5A]', 'bg-[#FF3D5A]/15');
      tab.classList.remove('border-[#F2F0EC]/20', 'text-[#F2F0EC]/60');

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
          emptyMsg.className = 'col-span-full py-16 text-center text-[#F2F0EC]/60 font-body';
          emptyMsg.innerHTML = '<p class="text-base font-semibold text-[#F2F0EC]/80 uppercase tracking-wider mb-1">No items found</p><p class="text-xs text-[#F2F0EC]/40">No promotional drops currently available in this category.</p>';
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
        t.classList.remove('active','border-[#FF3D5A]','text-[#FF3D5A]');
        t.classList.add('border-[#F2F0EC]/20','text-[#F2F0EC]/60');
      });
      tab.classList.add('active','border-[#FF3D5A]','text-[#FF3D5A]');
      tab.classList.remove('border-[#F2F0EC]/20','text-[#F2F0EC]/60');

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
          emptyMsg.className = 'col-span-full py-16 text-center text-[#F2F0EC]/60 font-body';
          emptyMsg.innerHTML = '<p class="text-base font-semibold text-[#F2F0EC]/80 uppercase tracking-wider mb-1">No items found</p><p class="text-xs text-[#F2F0EC]/40">No best-selling items currently available in this category.</p>';
          bsGrid.appendChild(emptyMsg);
        } else if (emptyMsg) {
          emptyMsg.style.display = '';
        }
      } else if (emptyMsg) {
        emptyMsg.style.display = 'none';
      }
    });
  });

  document.querySelector('.bs-tab.active')?.classList.add('border-[#FF3D5A]','text-[#FF3D5A]');
  document.querySelector('.bs-tab.active')?.classList.remove('border-[#F2F0EC]/20','text-[#F2F0EC]/60');
})();
</script>
@endpush

</x-storefront-layout>
