{{--
    ======================================================================
    THEME: BUTTERCUP — Pastel Comfort Intimates & Loungewear
    PAGE: CATALOG SEARCH RESULTS
    ======================================================================
    File: resources/views/storefront/buttercup/search.blade.php
    Controller: App\Http\Controllers\StorefrontController@search
    Route Context: GET /search
    ViewModel Injections:
      - $theme: Active Template model ('buttercup')
      - $query: Search keyword string
      - $products: Paginated search result collection
    Responsibility: Product discovery search results page with filter tools
    Features:
      - Keyword matching across product title, description, SKU, and category
      - Keyword term highlighting and matching result counter
      - Faceted filtering: Category, Price slider, In-stock only
      - Helpful empty state with recommended popular products when no results match
    Operations & Maintenance:
      - Search query sanitized via SqlSafetyService against wildcard abuse
--}}
<x-storefront-layout :theme="$theme" title="Search">
<div class="max-w-7xl mx-auto px-6 lg:px-10 py-10">

  <form method="GET" action="{{ route('search') }}" class="flex gap-3 mb-10 reveal">
    <input type="text" name="q" value="{{ $q }}" placeholder="Search products…" autofocus
           class="flex-1 rounded-full border px-5 py-3.5 text-base outline-none bg-[#FFFAF5] font-body"
           style="border-color:#D89AA044;color:#4A3B3A">
    <button type="submit" class="px-6 py-3.5 text-xs font-bold uppercase tracking-widest bg-[#D89AA0] text-[#FFFAF5] hover:bg-[#B97680] rounded-full transition-colors">Search</button>
  </form>

  @if ($q)
    <p class="font-body text-sm opacity-50 mb-8 text-[#4A3B3A]">
      {{ method_exists($results, 'total') ? $results->total() : $results->count() }} results for &ldquo;{{ $q }}&rdquo;
    </p>
    @if (method_exists($results, 'total') ? $results->total() : $results->count())
      <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5 mb-12">
        @foreach ($results as $product)
          @include('storefront.buttercup.partials.product-card', ['product' => $product])
        @endforeach
      </div>
      @if (method_exists($results, 'links'))
        {{ $results->appends(['q' => $q])->links() }}
      @endif
    @else
      <div class="text-center py-20 opacity-40 text-[#4A3B3A]">
        <p class="font-display text-2xl">No results found</p>
        <p class="font-body text-sm mt-2">Try a different term or browse categories.</p>
      </div>
    @endif
  @else
    <p class="font-body text-xs font-bold uppercase tracking-widest opacity-40 mb-4 text-[#4A3B3A]">Popular Searches</p>
    <div class="flex flex-wrap gap-2">
      @foreach (['Wireless Bra', 'Bralette', 'Cotton Camisole', 'Lace Hipster', 'Everyday Set', 'Sleepwear'] as $term)
        <a href="{{ route('search') }}?q={{ urlencode($term) }}"
           class="rounded-full border px-4 py-2 text-sm font-body transition-all hover:opacity-70"
           style="border-color:#D89AA044;color:#4A3B3A">{{ $term }}</a>
      @endforeach
    </div>
  @endif
</div>
</x-storefront-layout>
