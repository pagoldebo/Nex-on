{{-- resources/views/storefront/lumiere/search.blade.php
     Data from StorefrontController@search: $q (string), $results (LengthAwarePaginator or empty Collection) --}}
<x-storefront-layout :theme="$theme" title="{{ $q ? 'Search — '.$q : 'Search' }}">

<style>
  .animate-fade { animation: pageFadeIn .5s ease both; }
  .pill-row { scrollbar-width:none; }
  .pill-row::-webkit-scrollbar { display:none; }
</style>

<div class="max-w-7xl mx-auto px-6 md:px-12 py-10">

  {{-- Search bar --}}
  <form method="GET" action="{{ route('search') }}" role="search" class="mb-10 reveal">
    <label for="srchInput" class="sr-only">Search products</label>
    <div class="flex gap-3">
      <div class="relative flex-1">
        <svg class="absolute left-5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="M21 21l-4.3-4.3"/></svg>
        <input id="srchInput" name="q" type="search" value="{{ $q }}" placeholder="Search for silk sets, bralettes, sleepwear…" autofocus
               class="w-full rounded-2xl border border-pink-100 bg-white pl-12 pr-5 py-4 text-sm text-gray-800 outline-none focus:border-pink-400 transition-colors">
      </div>
      <button type="submit" class="px-7 py-4 rounded-2xl text-xs font-bold uppercase tracking-widest bg-[#405549] text-white hover:bg-pink-600 transition-colors flex-shrink-0">Search</button>
      @if ($q)
        <a href="{{ route('search') }}" class="px-5 py-4 rounded-2xl text-xs font-bold uppercase tracking-widest border border-pink-100 text-gray-400 hover:text-gray-700 hover:border-pink-300 transition-colors flex-shrink-0" aria-label="Clear search">✕</a>
      @endif
    </div>
  </form>

  @if ($q)
    {{-- Result count --}}
    @php $total = method_exists($results, 'total') ? $results->total() : $results->count(); @endphp
    <p class="text-sm text-gray-400 mb-8">
      @if ($total)
        <span class="font-semibold text-gray-900">{{ number_format($total) }}</span> {{ Str::plural('result', $total) }} for &ldquo;<span class="text-pink-600">{{ $q }}</span>&rdquo;
      @else
        No results for &ldquo;<span class="text-pink-600">{{ $q }}</span>&rdquo;
      @endif
    </p>

    @if ($total)
      {{-- Results grid --}}
      <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-x-5 gap-y-10 mb-14">
        @foreach ($results as $product)
          @include('storefront.lumiere.partials.lustre-card', ['product' => $product])
        @endforeach
      </div>
      @if (method_exists($results, 'links'))
        <div class="flex justify-center">{{ $results->appends(['q' => $q])->links() }}</div>
      @endif
    @else
      {{-- No results: show suggestions --}}
      <div class="text-center py-10 mb-10">
        <svg class="w-12 h-12 mx-auto text-pink-100 mb-4" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="M21 21l-4.3-4.3"/></svg>
        <p class="font-display italic text-xl text-gray-300 mb-2">Nothing found</p>
        <p class="text-sm text-gray-400 mb-8">Try a broader term or browse by category below.</p>
      </div>
      {{-- Category shortcuts --}}
      @if ($categories->count())
        <div class="mb-10">
          <p class="text-[10px] uppercase tracking-widest font-bold text-gray-400 mb-4">Browse by Category</p>
          <div class="pill-row flex gap-2 overflow-x-auto -mx-1 px-1">
            @foreach ($categories as $cat)
              <a href="{{ route('category.show', $cat->slug) }}"
                 class="flex-shrink-0 px-5 py-2.5 rounded-full text-xs font-bold uppercase tracking-widest border border-pink-100 text-gray-500 hover:border-pink-400 hover:text-pink-600 transition-colors">
                {{ $cat->name }}
              </a>
            @endforeach
          </div>
        </div>
      @endif
    @endif

  @else
    {{-- No query: popular searches + category pills --}}
    <div class="mb-10">
      <p class="text-[10px] uppercase tracking-widest font-bold text-gray-400 mb-4">Popular Searches</p>
      <div class="flex flex-wrap gap-2">
        @foreach (['Silk Slip', 'Lustre Bralette', 'Lace Set', 'Sleepwear', 'Bridal Edit', 'Wireless Bra', 'High-Waist Brief'] as $term)
          <a href="{{ route('search') }}?q={{ urlencode($term) }}"
             class="px-5 py-2.5 rounded-full text-xs font-semibold border border-pink-100 text-gray-600 hover:border-pink-400 hover:text-pink-600 transition-colors">{{ $term }}</a>
        @endforeach
      </div>
    </div>

    @if ($categories->count())
      <div>
        <p class="text-[10px] uppercase tracking-widest font-bold text-gray-400 mb-4">Browse by Category</p>
        <div class="pill-row flex gap-2 overflow-x-auto -mx-1 px-1">
          @foreach ($categories as $cat)
            <a href="{{ route('category.show', $cat->slug) }}"
               class="flex-shrink-0 px-5 py-2.5 rounded-full text-xs font-bold uppercase tracking-widest border border-pink-100 text-gray-500 hover:border-pink-400 hover:text-pink-600 transition-colors">
              {{ $cat->name }}
            </a>
          @endforeach
        </div>
      </div>
    @endif
  @endif
</div>

{{-- Quick-view modal for the lustre cards --}}
@include('storefront.lumiere.partials.quick-view')

</x-storefront-layout>
