{{--
    ======================================================================
    THEME: SAAYA — Royal Indian Trousseau & Heritage Silk
    PAGE: CATEGORY COLLECTION CATALOG
    ======================================================================
    File: resources/views/storefront/saaya/category.blade.php
    Controller: App\Http\Controllers\StorefrontController@category
    Route Context: GET /category/{slug}
    ViewModel Injections:
      - $theme: Active Template model ('saaya')
      - $category: Selected Category model
      - $products: Paginated collection of category products
      - $subcategories: Child categories for faceted navigation
    Responsibility: Category product showcase with faceted filters and sorting
    Features:
      - Filter rail: Price range, size/variant pills, availability, rating
      - Sorting options: Newest, Price (Low-High, High-Low), Most Popular
      - Responsive product grid with quick-view modal triggers
      - Accessible pagination links
    Operations & Maintenance:
      - Only displays active products (is_active = 1)
--}}
<x-storefront-layout>

<div class="bg-ivory min-h-screen">

{{-- ═══════════════════════════════════════════════
     CATEGORY HERO  —  compact maroon banner
═══════════════════════════════════════════════ --}}
<div class="relative bg-maroon overflow-hidden py-14 lg:py-20">
  <div class="absolute inset-0 opacity-[.06] pointer-events-none"
       style="background-image:radial-gradient(circle,#C9A24B 1px,transparent 1px);background-size:24px 24px;"></div>
  <div class="relative max-w-7xl mx-auto px-6 lg:px-12 text-center">
    <p class="font-display italic text-gold text-lg mb-2">Collection</p>
    <h1 class="font-display text-5xl lg:text-6xl text-ivory">{{ $category->name }}</h1>
    @if($category->description)
      <p class="font-body text-ivory/60 text-sm mt-4 max-w-md mx-auto">{{ $category->description }}</p>
    @endif
  </div>
</div>


{{-- ═══════════════════════════════════════════════
     BREADCRUMB + SORT BAR
═══════════════════════════════════════════════ --}}
<div class="max-w-7xl mx-auto px-6 lg:px-12 py-4 flex flex-wrap items-center justify-between gap-3 border-b border-gold/15">

  {{-- Breadcrumb --}}
  <ol class="flex items-center gap-2 text-xs font-body text-maroon/50">
    <li><a href="{{ route('home') }}" class="hover:text-gold transition-colors">Home</a></li>
    @foreach($breadcrumbs ?? $category->getBreadcrumbs() as $crumb)
      <li><svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/></svg></li>
      @if(!$loop->last)
        <li><a href="{{ route('category.show', $crumb->slug) }}" class="hover:text-gold transition-colors">{{ $crumb->name }}</a></li>
      @else
        <li class="text-maroon font-semibold">{{ $crumb->name }}</li>
      @endif
    @endforeach
  </ol>

  {{-- Sort + count --}}
  <div class="flex items-center gap-4">
    <span class="text-xs font-body text-maroon/40">{{ $products->total() }} pieces</span>
    <div class="relative">
      <select id="sortSelect"
              class="appearance-none border border-gold/30 bg-transparent text-xs font-body text-maroon pl-3 pr-8 py-2 focus:outline-none focus:border-gold cursor-pointer hover:border-gold transition-colors">
        @foreach($sortOptions as $val => $label)
          <option value="{{ $val }}" {{ $currentSort === $val ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
      </select>
      <svg class="pointer-events-none absolute right-2 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gold/60" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
    </div>
  </div>

</div>

@if(($subcategories ?? $category->children)->count() > 0)
  <div class="max-w-7xl mx-auto px-6 lg:px-12 pt-6 flex items-center gap-2 flex-wrap">
    <span class="text-xs uppercase font-body tracking-widest text-maroon/60 font-semibold">Subcategories:</span>
    @foreach($subcategories ?? $category->children as $sub)
      <a href="{{ route('category.show', $sub->slug) }}"
         class="px-4 py-1.5 text-xs font-body font-semibold tracking-wider border border-gold/30 text-maroon hover:border-gold hover:text-gold transition-colors">
        {{ $sub->name }}
      </a>
    @endforeach
  </div>
@endif


{{-- ═══════════════════════════════════════════════
     PRODUCT GRID
═══════════════════════════════════════════════ --}}
<section class="max-w-7xl mx-auto px-6 lg:px-12 py-8 lg:py-10">

  @if($products->count())
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 lg:gap-6">
      @foreach($products as $product)
        <div class="reveal">
          @include('storefront.saaya.partials.product-card', ['product' => $product])
        </div>
      @endforeach
    </div>

    {{-- Pagination --}}
    @if($products->hasPages())
      <div class="mt-12 flex justify-center items-center gap-1 font-body text-sm">
        {{-- Previous --}}
        @if($products->onFirstPage())
          <span class="w-9 h-9 flex items-center justify-center border border-gold/20 text-maroon/25 cursor-not-allowed">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
          </span>
        @else
          <a href="{{ $products->previousPageUrl() }}&sort={{ $currentSort }}"
             class="w-9 h-9 flex items-center justify-center border border-gold/30 text-maroon hover:border-gold hover:text-gold transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
          </a>
        @endif

        {{-- Pages --}}
        @foreach($products->getUrlRange(1, $products->lastPage()) as $page => $url)
          @if($page == $products->currentPage())
            <span class="w-9 h-9 flex items-center justify-center bg-maroon text-ivory text-xs">{{ $page }}</span>
          @else
            <a href="{{ $url }}&sort={{ $currentSort }}"
               class="w-9 h-9 flex items-center justify-center border border-gold/30 text-maroon hover:border-gold hover:text-gold transition-colors text-xs">{{ $page }}</a>
          @endif
        @endforeach

        {{-- Next --}}
        @if($products->hasMorePages())
          <a href="{{ $products->nextPageUrl() }}&sort={{ $currentSort }}"
             class="w-9 h-9 flex items-center justify-center border border-gold/30 text-maroon hover:border-gold hover:text-gold transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
          </a>
        @else
          <span class="w-9 h-9 flex items-center justify-center border border-gold/20 text-maroon/25 cursor-not-allowed">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
          </span>
        @endif
      </div>
    @endif

  @else
    {{-- Empty state --}}
    <div class="text-center py-24">
      <svg class="w-12 h-12 text-gold/30 mx-auto mb-4" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/></svg>
      <p class="font-display text-2xl text-maroon mb-2">No pieces here yet</p>
      <p class="font-body text-sm text-maroon/50 mb-8">Check back soon or explore our other collections.</p>
      <a href="{{ route('search') }}" class="gold-btn py-3.5 px-8 text-xs font-body font-semibold tracking-widest uppercase inline-block">Browse All</a>
    </div>
  @endif

</section>

</div>

@push('scripts')
<script>
  document.getElementById('sortSelect')?.addEventListener('change', function () {
    const url = new URL(window.location.href);
    url.searchParams.set('sort', this.value);
    url.searchParams.delete('page');
    window.location.href = url.toString();
  });

  if ('IntersectionObserver' in window) {
    const obs = new IntersectionObserver(entries => {
      entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('visible'); obs.unobserve(e.target); } });
    }, { threshold: 0.1 });
    document.querySelectorAll('.reveal').forEach(el => obs.observe(el));
  } else {
    document.querySelectorAll('.reveal').forEach(el => el.classList.add('visible'));
  }
</script>
@endpush

</x-storefront-layout>
