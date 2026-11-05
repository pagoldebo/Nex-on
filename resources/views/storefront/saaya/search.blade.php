{{-- resources/views/storefront/saaya/search.blade.php
     Saaya search results page.
     Controller passes: $q (string), $products (paginated|empty Collection),
                        $categories (layout + suggestions), $sortOptions, $currentSort
     Ajax branch: returns JSON when ?ajax=1 --}}

<x-storefront-layout>

<div class="bg-ivory min-h-screen">

{{-- ═══════════════════════════════════════════════
     SEARCH HEADER
═══════════════════════════════════════════════ --}}
<div class="bg-maroon py-12 lg:py-16 relative overflow-hidden">
  <div class="absolute inset-0 opacity-[.05] pointer-events-none"
       style="background-image:radial-gradient(circle,#C9A24B 1px,transparent 1px);background-size:24px 24px;"></div>
  <div class="relative max-w-7xl mx-auto px-6 lg:px-12">

    {{-- Search input --}}
    <form method="GET" action="{{ route('search') }}" class="max-w-2xl mx-auto">
      <div class="flex items-center gap-3 border-b-2 border-gold/50 pb-3 focus-within:border-gold transition-colors">
        <svg class="w-5 h-5 text-gold/60 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="M21 21l-4.3-4.3"/></svg>
        <input id="srchInput"
               type="search"
               name="q"
               value="{{ $q }}"
               autocomplete="off"
               placeholder="Search bras, panties, nightwear…"
               class="flex-1 bg-transparent text-ivory text-xl lg:text-2xl font-display placeholder-ivory/30 focus:outline-none">
        @if($q)
          <a href="{{ route('search') }}" class="text-ivory/40 hover:text-ivory transition-colors" aria-label="Clear search">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
          </a>
        @endif
      </div>
      @if($currentSort !== 'default')
        <input type="hidden" name="sort" value="{{ $currentSort }}">
      @endif
    </form>

    @if($q)
      <p class="font-body text-ivory/50 text-sm text-center mt-4">
        @if($products->total())
          {{ number_format($products->total()) }} {{ Str::plural('result', $products->total()) }} for
          <span class="text-gold">"{{ $q }}"</span>
        @else
          No results for <span class="text-gold">"{{ $q }}"</span>
        @endif
      </p>
    @endif

  </div>
</div>


{{-- ═══════════════════════════════════════════════
     STATE A — No query: popular pills + categories
═══════════════════════════════════════════════ --}}
@if(!$q)
<section class="max-w-7xl mx-auto px-6 lg:px-12 py-12 lg:py-16">

  <div class="mb-12">
    <p class="text-[10px] uppercase tracking-widest font-semibold text-gold-dark font-body mb-4">Popular Searches</p>
    <div class="flex flex-wrap gap-2">
      @foreach(['Silk Bra Set','Bridal Trousseau','Lace Brief','Wireless Bra','Nightwear','Camisole','Cotton Panty','Bralette'] as $term)
        <a href="{{ route('search') }}?q={{ urlencode($term) }}"
           class="border border-gold/40 text-maroon text-xs px-5 py-2.5 rounded-full hover:border-gold hover:bg-gold/10 transition-colors font-body">
          {{ $term }}
        </a>
      @endforeach
    </div>
  </div>

  @php
    $parentCategories = $categories->filter(fn($c) => is_null($c->parent_id));
  @endphp
  @if($parentCategories->count())
    <p class="text-[10px] uppercase tracking-widest font-semibold text-gold-dark font-body mb-4">Shop by Category</p>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
      @foreach($parentCategories as $cat)
        <a href="{{ route('category.show', $cat->slug) }}"
           class="group relative overflow-hidden reveal" style="aspect-ratio:3/4;">
          <div class="absolute inset-0 bg-gradient-to-b from-blush/30 to-maroon/70"></div>
          <div class="absolute inset-0 flex items-end p-4">
            <p class="font-display text-ivory text-lg group-hover:text-gold transition-colors">{{ $cat->name }}</p>
          </div>
        </a>
      @endforeach
    </div>
  @endif

</section>
@endif


{{-- ═══════════════════════════════════════════════
     STATE B — Has query, has results
═══════════════════════════════════════════════ --}}
@if($q && $products->count())
<section class="max-w-7xl mx-auto px-6 lg:px-12 py-8 lg:py-10">

  {{-- Sort bar --}}
  <div class="flex justify-end mb-6">
    <div class="relative">
      <select id="srchSortSelect"
              class="appearance-none border border-gold/30 bg-transparent text-xs font-body text-maroon pl-3 pr-8 py-2 focus:outline-none focus:border-gold cursor-pointer hover:border-gold transition-colors">
        @foreach($sortOptions as $val => $label)
          <option value="{{ $val }}" {{ $currentSort === $val ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
      </select>
      <svg class="pointer-events-none absolute right-2 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gold/60" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
    </div>
  </div>

  {{-- Grid --}}
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
      @if($products->onFirstPage())
        <span class="w-9 h-9 flex items-center justify-center border border-gold/20 text-maroon/25 cursor-not-allowed">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        </span>
      @else
        <a href="{{ $products->previousPageUrl() }}&q={{ urlencode($q) }}&sort={{ $currentSort }}"
           class="w-9 h-9 flex items-center justify-center border border-gold/30 text-maroon hover:border-gold hover:text-gold transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        </a>
      @endif

      @foreach($products->getUrlRange(1, $products->lastPage()) as $page => $url)
        @if($page == $products->currentPage())
          <span class="w-9 h-9 flex items-center justify-center bg-maroon text-ivory text-xs">{{ $page }}</span>
        @else
          <a href="{{ $url }}&q={{ urlencode($q) }}&sort={{ $currentSort }}"
             class="w-9 h-9 flex items-center justify-center border border-gold/30 text-maroon hover:border-gold hover:text-gold transition-colors text-xs">{{ $page }}</a>
        @endif
      @endforeach

      @if($products->hasMorePages())
        <a href="{{ $products->nextPageUrl() }}&q={{ urlencode($q) }}&sort={{ $currentSort }}"
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

</section>
@endif


{{-- ═══════════════════════════════════════════════
     STATE C — Has query, no results
═══════════════════════════════════════════════ --}}
@if($q && !$products->count())
<section class="max-w-7xl mx-auto px-6 lg:px-12 py-16 lg:py-20">

  <div class="text-center mb-14">
    <svg class="w-12 h-12 text-gold/30 mx-auto mb-4" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="M21 21l-4.3-4.3"/></svg>
    <p class="font-display text-3xl text-maroon mb-2">No results found</p>
    <p class="font-body text-sm text-maroon/50">Try a different term or browse our categories below.</p>
  </div>

  <p class="text-[10px] uppercase tracking-widest font-semibold text-gold-dark font-body mb-4">Try These</p>
  <div class="flex flex-wrap gap-2 mb-12">
    @foreach(['Silk Bra Set','Bridal Trousseau','Lace Brief','Wireless Bra','Nightwear','Camisole'] as $term)
      <a href="{{ route('search') }}?q={{ urlencode($term) }}"
         class="border border-gold/40 text-maroon text-xs px-5 py-2.5 rounded-full hover:border-gold hover:bg-gold/10 transition-colors font-body">
        {{ $term }}
      </a>
    @endforeach
  </div>

  @if($categories->count())
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
      @foreach($categories as $cat)
        <a href="{{ route('category.show', $cat->slug) }}"
           class="border border-gold/30 text-maroon text-center px-4 py-5 hover:border-gold hover:bg-gold/5 transition-colors font-body text-sm">
          {{ $cat->name }}
        </a>
      @endforeach
    </div>
  @endif

</section>
@endif

</div>

@push('scripts')
<script>
  document.getElementById('srchSortSelect')?.addEventListener('change', function () {
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
