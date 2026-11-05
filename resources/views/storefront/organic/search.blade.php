{{-- resources/views/storefront/organic/search.blade.php
     Organic Market Search Page: Search Bar, Results Grid, and Popular Produce Tags. --}}
<x-storefront-layout :theme="$theme" title="Search Organic Produce">

  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
    
    {{-- Search Input Bar --}}
    <div class="max-w-2xl mx-auto mb-10">
      <form method="GET" action="{{ route('search') }}" class="flex items-center gap-2">
        <div class="relative flex-1">
          <input type="text" 
                 name="q" 
                 value="{{ $q }}" 
                 placeholder="Search" 
                 autofocus
                 class="w-full bg-white border-2 border-stone-200 focus:border-organic-500 rounded-full py-3.5 pl-5 pr-12 text-sm text-stone-800 placeholder-stone-400 focus:outline-none transition shadow-xs">
          @if($q)
            <a href="{{ route('search') }}" class="absolute right-4 top-1/2 -translate-y-1/2 text-stone-400 hover:text-stone-700 text-xs font-bold" title="Clear search">✕</a>
          @endif
        </div>
        <button type="submit" class="btn-organic px-7 py-3.5 rounded-full text-xs font-bold uppercase tracking-wider shadow-md shrink-0">
          Search
        </button>
      </form>
    </div>

    @if($q)
      @php
        $count = method_exists($results, 'total') ? $results->total() : $results->count();
      @endphp

      <div class="flex items-center justify-between pb-4 border-b border-stone-200 mb-8">
        <h1 class="text-xl sm:text-2xl font-black text-stone-900 font-display">
          Search Results for <span class="text-organic-600">&ldquo;{{ $q }}&rdquo;</span>
        </h1>
        <span class="text-xs font-bold text-stone-600 bg-organic-50 px-3 py-1 rounded-full border border-organic-200">
          {{ $count }} Produce Found
        </span>
      </div>

      @if($count > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 mb-12">
          @foreach($results as $product)
            @include('storefront.organic.partials.product-card', ['product' => $product])
          @endforeach
        </div>

        @if(method_exists($results, 'links'))
          <div class="flex justify-center mt-8">
            {{ $results->appends(['q' => $q])->links() }}
          </div>
        @endif
      @else
        <div class="text-center py-20 bg-white rounded-3xl border border-stone-200 p-8 max-w-md mx-auto">
          <div class="w-16 h-16 rounded-full bg-organic-50 text-stone-400 flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 fill-current" viewBox="0 0 24 24"><use xlink:href="#organic-search"></use></svg>
          </div>
          <h3 class="text-lg font-black text-stone-900 font-display">No organic produce found</h3>
          <p class="text-xs text-stone-500 mt-1 mb-6 font-light">Try searching for generic terms like &ldquo;spinach&rdquo;, &ldquo;oil&rdquo;, &ldquo;ghee&rdquo;, or &ldquo;mango&rdquo;.</p>
          <a href="{{ route('home') }}" class="btn-organic inline-block px-6 py-2.5 rounded-full text-xs font-bold">
            Browse All Produce
          </a>
        </div>
      @endif

    @else
      <div class="max-w-xl mx-auto text-center py-8">
        <span class="text-xs font-bold uppercase tracking-wider text-stone-400 block mb-3">Popular Farm Produce:</span>
        <div class="flex flex-wrap justify-center gap-2">
          @foreach (['Organic Spinach', 'A2 Gir Cow Ghee', 'Cold Pressed Mustard Oil', 'Wild Forest Honey', 'Alphonso Mango', 'Desi Khand Sugar', 'Organic Turmeric Powder'] as $term)
            <a href="{{ route('search', ['q' => $term]) }}" class="px-3.5 py-1.5 rounded-full bg-white hover:bg-organic-50 text-stone-700 hover:text-organic-700 border border-stone-200 text-xs font-semibold transition shadow-2xs">
              {{ $term }}
            </a>
          @endforeach
        </div>
      </div>
    @endif

  </div>

</x-storefront-layout>

