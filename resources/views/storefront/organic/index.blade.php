{{--
    ======================================================================
    THEME: VERDIA (ORGANIC) — HOMEPAGE TEMPLATE
    ======================================================================
    File: resources/views/storefront/organic/index.blade.php
    Controller: App\Http\Controllers\StorefrontController@index
    ViewModel Injections:
      - $theme:            Active Template model ('organic' / 'Verdia')
      - $categories:       Parent organic produce & botanical categories
      - $featured:         Curated harvest & eco-living essentials
      - $bestsellers:      Popular daily staples & seasonal favourites
      - $newArrivals:      Fresh farm harvest releases
    Section Architecture:
      1. HERO BANNER:     Textured linen backdrop with gradient overlay & verified farm badge
      2. VALUE PROPS:     Certified organic, direct farm dispatch, zero-chemical guarantee
      3. SHOP BY CAT:     Clean grid of farm produce categories with visual counters
      4. FEATURED PICKS:  Botanical product cards with origin tags & weight selectors
      5. PROMO SPLIT:     Dual promotional cards for seasonal harvest baskets
      6. NEW ARRIVALS:    Freshly harvested daily stock rail
      7. GROWER STORY:    Farmer trust, fair compensation, and sustainable regenerative soil
      8. COMMUNITY:       Organic recipe & newsletter subscription box
--}}
<x-storefront-layout :theme="$theme" title="Verdia Botanical Living &amp; Farm Fresh Marketplace">

  {{-- 1. HERO BANNER SECTION --}}
  @php
    $organicHeroCustom = \App\Models\Setting::get('organic_hero_image');
    $organicHeroBg = $organicHeroCustom ? asset('images/' . ltrim(str_replace('storage/', '', $organicHeroCustom), '/')) : asset('images/themes/organic/banner-1.jpg');
  @endphp
  <section class="relative bg-cover bg-center overflow-hidden py-16 sm:py-24 lg:py-32" 
           style="background-image: linear-gradient(to right, rgba(249, 248, 243, 0.95) 0%, rgba(249, 248, 243, 0.85) 50%, rgba(249, 248, 243, 0.3) 100%), url('{{ $organicHeroBg }}');">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="max-w-2xl reveal">
        <span class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full text-xs font-bold tracking-wider uppercase bg-organic-100 text-organic-700 border border-organic-300 mb-4">
          {{ \App\Models\Setting::get('organic_hero_badge', '🌱 100% Certified Farm-to-Table') }}
        </span>
        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-stone-900 leading-[1.15] font-display tracking-tight">
          {!! nl2br(e(\App\Models\Setting::get('organic_hero_title', "Organic Foods at your Doorsteps"))) !!}
        </h1>
        <p class="mt-4 text-base sm:text-lg text-stone-600 font-light leading-relaxed">
          {{ \App\Models\Setting::get('organic_hero_subtitle', 'Pure, wholesome produce cultivated by trusted Indian organic farmers. Zero harmful pesticides, zero synthetic chemicals—just real nutrition harvested daily.') }}
        </p>

        <div class="mt-8 flex flex-wrap gap-4 items-center">
          <a href="#products" class="btn-organic px-8 py-3.5 rounded-full font-bold text-xs uppercase tracking-wider shadow-lg flex items-center gap-2">
            <span>{{ \App\Models\Setting::get('organic_hero_btn1', 'Start Shopping') }}</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
          </a>
          <a href="{{ route('featured.products') }}" class="px-8 py-3.5 rounded-full font-bold text-xs uppercase tracking-wider bg-white hover:bg-stone-100 text-stone-800 border border-stone-300 shadow-sm transition">
            {{ \App\Models\Setting::get('organic_hero_btn2', 'Explore Featured') }}
          </a>
        </div>

        {{-- Farm Metrics Counters --}}
        <div class="mt-12 pt-8 border-t border-stone-300/60 grid grid-cols-3 gap-4 sm:gap-8">
          <div>
            <p class="text-2xl sm:text-3xl font-black text-organic-900 font-display">{{ \App\Models\Setting::get('organic_metric1_num', '1,200+') }}</p>
            <p class="text-[11px] sm:text-xs text-stone-500 font-semibold uppercase tracking-wider mt-0.5">{{ \App\Models\Setting::get('organic_metric1_label', 'Organic Varieties') }}</p>
          </div>
          <div>
            <p class="text-2xl sm:text-3xl font-black text-organic-900 font-display">{{ \App\Models\Setting::get('organic_metric2_num', '50k+') }}</p>
            <p class="text-[11px] sm:text-xs text-stone-500 font-semibold uppercase tracking-wider mt-0.5">{{ \App\Models\Setting::get('organic_metric2_label', 'Happy Patrons') }}</p>
          </div>
          <div>
            <p class="text-2xl sm:text-3xl font-black text-organic-900 font-display">{{ \App\Models\Setting::get('organic_metric3_num', '100%') }}</p>
            <p class="text-[11px] sm:text-xs text-stone-500 font-semibold uppercase tracking-wider mt-0.5">{{ \App\Models\Setting::get('organic_metric3_label', 'Purity Assured') }}</p>
          </div>
        </div>

      </div>
    </div>
  </section>

  {{-- Scrolling Announcement Band --}}
  @include('storefront.common.scrolling-band')

  {{-- 2. THREE HIGHLIGHT VALUE PROPOSITIONS --}}
  <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 sm:pt-12 pb-4 sm:pb-6 relative z-10">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 lg:gap-6">
      
      {{-- Card 1: Fresh from Farm --}}
      <div class="bg-organic-500 text-white rounded-3xl p-6 shadow-xl flex items-center gap-4 hover:-translate-y-1 transition duration-300">
        <div class="w-14 h-14 rounded-2xl bg-white/20 flex items-center justify-center shrink-0">
          <svg class="w-8 h-8 fill-current" viewBox="0 0 24 24"><use xlink:href="#organic-fresh"></use></svg>
        </div>
        <div>
          <h4 class="text-base font-extrabold font-display">{{ \App\Models\Setting::get('organic_prop1_title', 'Fresh from the Farm') }}</h4>
          <p class="text-xs text-organic-100 font-light mt-0.5">{{ \App\Models\Setting::get('organic_prop1_desc', 'Harvested and dispatched within 24 hours for peak nutritional potency.') }}</p>
        </div>
      </div>

      {{-- Card 2: 100% Certified Organic --}}
      <div class="bg-organic-900 text-white rounded-3xl p-6 shadow-xl flex items-center gap-4 hover:-translate-y-1 transition duration-300">
        <div class="w-14 h-14 rounded-2xl bg-white/20 flex items-center justify-center shrink-0 text-organic-300">
          <svg class="w-8 h-8 fill-current" viewBox="0 0 24 24"><use xlink:href="#organic-seal"></use></svg>
        </div>
        <div>
          <h4 class="text-base font-extrabold font-display">{{ \App\Models\Setting::get('organic_prop2_title', '100% Certified Organic') }}</h4>
          <p class="text-xs text-organic-200 font-light mt-0.5">{{ \App\Models\Setting::get('organic_prop2_desc', 'Verified non-GMO, chemical-free crops certified under NPOP & FSSAI norms.') }}</p>
        </div>
      </div>

      {{-- Card 3: Free Express Delivery --}}
      <div class="bg-citrus text-white rounded-3xl p-6 shadow-xl flex items-center gap-4 hover:-translate-y-1 transition duration-300">
        <div class="w-14 h-14 rounded-2xl bg-white/20 flex items-center justify-center shrink-0">
          <svg class="w-8 h-8 fill-current" viewBox="0 0 32 32"><use xlink:href="#organic-delivery"></use></svg>
        </div>
        <div>
          <h4 class="text-base font-extrabold font-display">{{ \App\Models\Setting::get('organic_prop3_title', 'Free Express Delivery') }}</h4>
          <p class="text-xs text-citrus-light font-light mt-0.5">{{ \App\Models\Setting::get('organic_prop3_desc', 'Complimentary door delivery on orders over ₹1,499 with temperature control.') }}</p>
        </div>
      </div>

    </div>
  </section>

  {{-- 3. CATEGORY CAROUSEL SECTION (Matches Organic Template Reference) --}}
  <section class="py-10 sm:py-14 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8"
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
               const scrollCount = visibleItems > 1 ? Math.min(visibleItems - 1, 4) : 1;

               // Compute exact snap target to avoid any halfway cropping
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

    {{-- Header: Category Title + View All + Prev/Next Buttons --}}
    <div class="flex items-center justify-between mb-8">
      <h2 class="text-2xl sm:text-3xl font-bold text-stone-900 font-display">Category</h2>

      <div class="flex items-center gap-2">
        <a href="{{ route('featured.products') }}" 
           class="btn-organic text-xs sm:text-sm font-semibold px-4 py-2 rounded-lg transition duration-200 shadow-2xs">
          View All
        </a>
        <div class="flex items-center gap-1.5">
          <button type="button" 
                  @click="scrollTrack(-1)" 
                  class="w-8 h-8 rounded-lg bg-stone-100 hover:bg-stone-200 text-stone-700 flex items-center justify-center transition text-xs font-bold focus:outline-none disabled:opacity-35 disabled:cursor-not-allowed shadow-2xs" 
                  :disabled="!canScrollLeft"
                  aria-label="Previous categories">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
          </button>
          <button type="button" 
                  @click="scrollTrack(1)" 
                  class="w-8 h-8 rounded-lg bg-organic-500 hover:bg-organic-600 text-white flex items-center justify-center transition text-xs font-bold shadow-2xs focus:outline-none disabled:opacity-35 disabled:cursor-not-allowed" 
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
         class="flex items-start gap-3 sm:gap-4 lg:gap-5 overflow-x-auto scroll-smooth scrollbar-none pb-2 pt-1"
         style="scrollbar-width: none; -ms-overflow-style: none; scroll-snap-type: x mandatory;">
      @forelse($categories as $cat)
        @php
          $fallbackIndex = (($loop->index % 8) + 1);
          $catImg = $cat->image_url ?: asset("images/themes/organic/category-thumb-{$fallbackIndex}.jpg");
        @endphp
        <a href="{{ route('category.show', $cat->slug) }}" 
           class="category-item group shrink-0 lg:shrink lg:flex-1 min-w-[96px] sm:min-w-[105px] lg:min-w-0 flex flex-col items-center text-center transition"
           style="scroll-snap-align: start; scroll-snap-stop: normal;">
          <div class="w-20 h-20 sm:w-24 sm:h-24 md:w-28 md:h-28 lg:w-24 lg:h-24 xl:w-28 xl:h-28 rounded-full overflow-hidden mb-3 bg-stone-100 shadow-2xs group-hover:scale-105 group-hover:shadow-md transition-all duration-300">
            <img src="{{ $catImg }}" 
                 alt="{{ $cat->name }}" 
                 class="w-full h-full object-cover rounded-full" 
                 loading="lazy">
          </div>
          <h3 class="text-xs sm:text-sm font-normal sm:font-medium text-stone-800 group-hover:text-organic-600 transition leading-snug px-1 line-clamp-2 max-w-[125px]">
            {{ $cat->name }}
          </h3>
        </a>
      @empty
        @php
          $defaultCategories = [
            ['name' => 'Breads & Sweets', 'thumb' => 'category-thumb-2.jpg'],
            ['name' => 'Fruits & Veges', 'thumb' => 'category-thumb-3.jpg'],
            ['name' => 'Beverages', 'thumb' => 'category-thumb-4.jpg'],
            ['name' => 'Meat Products', 'thumb' => 'category-thumb-5.jpg'],
            ['name' => 'Breads', 'thumb' => 'category-thumb-6.jpg'],
            ['name' => 'Fruits & Veges', 'thumb' => 'category-thumb-7.jpg'],
            ['name' => 'Breads & Sweets', 'thumb' => 'category-thumb-8.jpg'],
            ['name' => 'Fruits & Veges', 'thumb' => 'category-thumb-1.jpg'],
          ];
        @endphp
        @foreach($defaultCategories as $defCat)
          <a href="{{ route('home') }}#products" 
             class="category-item group shrink-0 lg:shrink lg:flex-1 min-w-[96px] sm:min-w-[105px] lg:min-w-0 flex flex-col items-center text-center transition"
             style="scroll-snap-align: start; scroll-snap-stop: normal;">
            <div class="w-20 h-20 sm:w-24 sm:h-24 md:w-28 md:h-28 lg:w-24 lg:h-24 xl:w-28 xl:h-28 rounded-full overflow-hidden mb-3 bg-stone-100 shadow-2xs group-hover:scale-105 group-hover:shadow-md transition-all duration-300">
              <img src="{{ asset('images/themes/organic/' . $defCat['thumb']) }}" 
                   alt="{{ $defCat['name'] }}" 
                   class="w-full h-full object-cover rounded-full" 
                   loading="lazy">
            </div>
            <h3 class="text-xs sm:text-sm font-normal sm:font-medium text-stone-800 group-hover:text-organic-600 transition leading-snug px-1 line-clamp-2 max-w-[125px]">
              {{ $defCat['name'] }}
            </h3>
          </a>
        @endforeach
      @endforelse
    </div>

  </section>

  {{-- 4. FEATURED PRODUCTS SECTION --}}
  <section id="products" class="py-12 bg-white border-y border-stone-200/70">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      
      <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8">
        <div>
          <span class="text-xs font-bold uppercase tracking-wider text-organic-600 block mb-1">Handpicked Best</span>
          <h2 class="text-2xl sm:text-3xl font-black text-stone-900 font-display">Featured Organic Harvests</h2>
        </div>
        <div class="flex items-center gap-2">
          <a href="{{ route('featured.products') }}" class="btn-organic px-4 py-2 rounded-full text-xs font-bold shadow-xs">
            See All Featured
          </a>
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @forelse($featured as $product)
          @include('storefront.organic.partials.product-card', ['product' => $product])
        @empty
          @foreach($newArrivals->take(4) as $product)
            @include('storefront.organic.partials.product-card', ['product' => $product])
          @endforeach
        @endforelse
      </div>

    </div>
  </section>

  {{-- 5. TRIPLE PROMOTIONAL AD BANNERS --}}
  <section class="py-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      
      {{-- Banner 1 --}}
      <div class="rounded-3xl p-6 sm:p-8 relative overflow-hidden bg-cover bg-center shadow-md text-white min-h-[220px] flex flex-col justify-between"
           style="background-image: linear-gradient(to bottom, rgba(42, 75, 26, 0.85), rgba(42, 75, 26, 0.95)), url('{{ asset('images/themes/organic/banner-ad-1.jpg') }}');">
        <div>
          <span class="text-[10px] font-black uppercase tracking-widest text-organic-300">{{ \App\Models\Setting::get('organic_ad1_kicker', 'Limited Batch') }}</span>
          <h3 class="text-xl font-black font-display mt-1 text-white">{{ \App\Models\Setting::get('organic_ad1_title', 'Raw Wild Forest Honey') }}</h3>
          <p class="text-xs text-organic-100 font-light mt-1">{{ \App\Models\Setting::get('organic_ad1_desc', 'Unprocessed, pure nectar directly from Nilgiri biosphere reserves.') }}</p>
        </div>
        <div>
          <a href="{{ \App\Models\Setting::get('organic_ad1_url', route('search', ['q' => 'honey'])) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-citrus-gold hover:underline">
            <span>{{ \App\Models\Setting::get('organic_ad1_btn', 'Shop Wild Honey') }}</span>
            <span>&rarr;</span>
          </a>
        </div>
      </div>

      {{-- Banner 2 --}}
      <div class="rounded-3xl p-6 sm:p-8 relative overflow-hidden bg-cover bg-center shadow-md text-white min-h-[220px] flex flex-col justify-between"
           style="background-image: linear-gradient(to bottom, rgba(249, 95, 9, 0.85), rgba(249, 95, 9, 0.95)), url('{{ asset('images/themes/organic/banner-ad-2.jpg') }}');">
        <div>
          <span class="text-[10px] font-black uppercase tracking-widest text-citrus-light">{{ \App\Models\Setting::get('organic_ad2_kicker', 'Seasonal Deal') }}</span>
          <h3 class="text-xl font-black font-display mt-1 text-white">{{ \App\Models\Setting::get('organic_ad2_title', 'Cold-Pressed Wood Oils') }}</h3>
          <p class="text-xs text-white/90 font-light mt-1">{{ \App\Models\Setting::get('organic_ad2_desc', 'Traditional Kolhu pressed mustard, sesame, and groundnut oils.') }}</p>
        </div>
        <div>
          <a href="{{ \App\Models\Setting::get('organic_ad2_url', route('search', ['q' => 'oil'])) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-white hover:underline">
            <span>{{ \App\Models\Setting::get('organic_ad2_btn', 'Explore Cold-Pressed') }}</span>
            <span>&rarr;</span>
          </a>
        </div>
      </div>

      {{-- Banner 3 --}}
      <div class="rounded-3xl p-6 sm:p-8 relative overflow-hidden bg-cover bg-center shadow-md text-white min-h-[220px] flex flex-col justify-between"
           style="background-image: linear-gradient(to bottom, rgba(30, 41, 59, 0.85), rgba(30, 41, 59, 0.95)), url('{{ asset('images/themes/organic/banner-ad-3.jpg') }}');">
        <div>
          <span class="text-[10px] font-black uppercase tracking-widest text-emerald-300">{{ \App\Models\Setting::get('organic_ad3_kicker', 'Daily Essentials') }}</span>
          <h3 class="text-xl font-black font-display mt-1 text-white">{{ \App\Models\Setting::get('organic_ad3_title', 'A2 Desi Gir Cow Ghee') }}</h3>
          <p class="text-xs text-stone-200 font-light mt-1">{{ \App\Models\Setting::get('organic_ad3_desc', 'Bilona churned cultured butter from free-grazing indigenous cows.') }}</p>
        </div>
        <div>
          <a href="{{ \App\Models\Setting::get('organic_ad3_url', route('search', ['q' => 'ghee'])) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-300 hover:underline">
            <span>{{ \App\Models\Setting::get('organic_ad3_btn', 'Discover A2 Ghee') }}</span>
            <span>&rarr;</span>
          </a>
        </div>
      </div>

    </div>
  </section>

  {{-- 6. NEW SEASON ARRIVALS --}}
  <section class="py-12 bg-white border-t border-stone-200/70">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      
      <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8">
        <div>
          <span class="text-xs font-bold uppercase tracking-wider text-organic-600 block mb-1">Freshly Picked</span>
          <h2 class="text-2xl sm:text-3xl font-black text-stone-900 font-display">New Season Arrivals</h2>
        </div>
        <a href="{{ route('new.arrivals') }}" class="text-xs font-bold text-organic-700 hover:text-organic-900 flex items-center gap-1 group">
          <span>View All New Harvests</span>
          <span class="group-hover:translate-x-1 transition-transform">&rarr;</span>
        </a>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach($newArrivals->take(4) as $product)
          @include('storefront.organic.partials.product-card', ['product' => $product])
        @endforeach
      </div>

    </div>
  </section>

  {{-- 7. LIMITED HARVEST FLASH DEALS --}}
  @if($saleProducts->isNotEmpty())
  <section class="py-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8">
      <div>
        <span class="text-xs font-bold uppercase tracking-wider text-citrus block mb-1">Special Harvest Savings</span>
        <h2 class="text-2xl sm:text-3xl font-black text-stone-900 font-display">Seasonal Farm Deals &amp; Discounts</h2>
      </div>
      <a href="{{ route('sale.products') }}" class="btn-organic-gold px-5 py-2.5 rounded-full text-xs font-bold shadow-xs">
        Browse All Deals
      </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      @foreach($saleProducts->take(4) as $product)
        @include('storefront.organic.partials.sale-card', ['product' => $product])
      @endforeach
    </div>
  </section>
  @endif

  {{-- 8. SUSTAINABLE FARMING STORY --}}
  <section class="py-16 bg-organic-50/60 border-y border-organic-200/60">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
        
        <div class="space-y-6">
          <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold tracking-wider uppercase bg-organic-100 text-organic-800 border border-organic-300">
            {{ \App\Models\Setting::get('organic_story_badge', '🌱 Our Regenerative Ethos') }}
          </span>
          <h2 class="text-3xl sm:text-4xl font-black text-stone-900 font-display leading-tight">
            {{ \App\Models\Setting::get('organic_story_title', 'Nurturing Soil, Empowering Farmers, Nourishing Families') }}
          </h2>
          <p class="text-xs sm:text-sm text-stone-600 leading-relaxed font-light">
            {{ \App\Models\Setting::get('organic_story_p1', 'Every apple, leaf of spinach, and drop of oil you order supports smallholder farmers practicing holistic natural farming. We believe true peace of mind is knowing exactly who grew your food and that it was harvested with reverence for the earth.') }}
          </p>

          <div class="grid grid-cols-2 gap-4 pt-2">
            <div class="flex items-start gap-3">
              <span class="w-6 h-6 rounded-full bg-organic-500 text-white flex items-center justify-center font-bold text-xs shrink-0">✓</span>
              <div>
                <h4 class="text-xs font-extrabold text-stone-900 font-display">{{ \App\Models\Setting::get('organic_story_f1_title', 'Zero Chemical Residue') }}</h4>
                <p class="text-[11px] text-stone-500 mt-0.5">{{ \App\Models\Setting::get('organic_story_f1_desc', 'Laboratory tested batches with certified zero chemical residue.') }}</p>
              </div>
            </div>
            <div class="flex items-start gap-3">
              <span class="w-6 h-6 rounded-full bg-organic-500 text-white flex items-center justify-center font-bold text-xs shrink-0">✓</span>
              <div>
                <h4 class="text-xs font-extrabold text-stone-900 font-display">{{ \App\Models\Setting::get('organic_story_f2_title', 'Eco-Safe Packaging') }}</h4>
                <p class="text-[11px] text-stone-500 mt-0.5">{{ \App\Models\Setting::get('organic_story_f2_desc', '100% biodegradable cornstarch bags and corrugated kraft crates.') }}</p>
              </div>
            </div>
          </div>

          <div>
            <a href="{{ \App\Models\Setting::get('organic_story_btn_url', route('about')) }}" class="btn-organic inline-flex items-center gap-2 px-6 py-3 rounded-full text-xs font-bold shadow-md">
              <span>{{ \App\Models\Setting::get('organic_story_btn_text', 'Read Our Farming Manifesto') }}</span>
              <span>&rarr;</span>
            </a>
          </div>
        </div>

        <div class="relative">
          <div class="rounded-3xl overflow-hidden shadow-2xl border-4 border-white aspect-[4/3]">
            @php
              $organicStoryImg = \App\Models\Setting::get('organic_story_image');
            @endphp
            @if($organicStoryImg)
              <img src="{{ asset('images/' . ltrim(str_replace('storage/', '', $organicStoryImg), '/')) }}" alt="Sustainable organic farm fields" class="w-full h-full object-cover">
            @else
              <img src="{{ asset('images/themes/organic/sample-image.jpg') }}" alt="Sustainable organic farm fields" class="w-full h-full object-cover">
            @endif
          </div>
          <div class="absolute -bottom-6 -left-6 bg-white p-5 rounded-2xl shadow-xl border border-stone-200 hidden sm:flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-organic-100 text-organic-700 flex items-center justify-center font-black text-lg">
              🌱
            </div>
            <div>
              <p class="text-xs font-extrabold text-stone-900 font-display">{{ \App\Models\Setting::get('organic_story_badge2', '100% Traceable Harvest') }}</p>
              <p class="text-[10px] text-stone-500">{{ \App\Models\Setting::get('organic_story_badge2_desc', 'Scan QR on any crate to see farm origins') }}</p>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>

  {{-- 9. CUSTOMER REVIEWS & REPUTATION (Loaded Dynamically From Database) --}}
  @php
    $displayReviews = (isset($reviews) && $reviews->count()) 
        ? $reviews 
        : \App\Models\ProductReview::where('is_approved', true)->with(['product', 'user.addresses'])->latest()->take(3)->get();
  @endphp
  @if($displayReviews && $displayReviews->count())
  <section class="py-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center max-w-xl mx-auto mb-12">
      <span class="text-xs font-bold uppercase tracking-wider text-organic-600 block mb-1">Patron Feedback</span>
      <h2 class="text-2xl sm:text-3xl font-black text-stone-900 font-display">What Our Customers Say</h2>
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
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-stone-200 shadow-xs flex flex-col justify-between">
          <div>
            <div class="flex items-center gap-1 text-amber-400 mb-3 text-sm">
              @for($i = 1; $i <= 5; $i++)
                <span>{{ $i <= ($rev->rating ?? 5) ? '★' : '☆' }}</span>
              @endfor
            </div>
            @if($rev->title)
              <h3 class="font-bold text-stone-900 text-sm mb-1.5">{{ $rev->title }}</h3>
            @endif
            <p class="text-xs text-stone-600 italic leading-relaxed">
              "{{ $rev->comment }}"
            </p>
          </div>
          <div class="mt-6 pt-4 border-t border-stone-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-organic-100 text-organic-700 font-bold flex items-center justify-center text-xs shrink-0">
              {{ strtoupper($initials ?: 'VB') }}
            </div>
            <div>
              <h4 class="text-xs font-bold text-stone-900">{{ $rev->customer_name }}</h4>
              <span class="text-[10px] text-stone-400">
                Verified Buyer @if($location) • {{ $location }} @endif
              </span>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </section>
  @endif

</x-storefront-layout>

