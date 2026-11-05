{{-- resources/views/storefront/lumiere/category.blade.php
     Data from StorefrontController@category: $category, $products (paginator), $categories --}}
<x-storefront-layout :theme="$theme" :title="$category->name">

<style>
  .animate-fade { animation: pageFadeIn .5s ease both; }
  .pill-row { scrollbar-width:none; }
  .pill-row::-webkit-scrollbar { display:none; }
</style>

<div class="max-w-7xl mx-auto px-6 md:px-12 py-10">

  {{-- Header --}}
  <div class="mb-10 reveal">
    <nav class="text-xs flex items-center gap-2 mb-4 text-gray-400">
      <a href="{{ route('home') }}" class="hover:text-pink-600 transition-colors">Home</a>
      @foreach($breadcrumbs ?? $category->getBreadcrumbs() as $crumb)
        <span>/</span>
        @if(!$loop->last)
          <a href="{{ route('category.show', $crumb->slug) }}" class="hover:text-pink-600 transition-colors">{{ $crumb->name }}</a>
        @else
          <span class="text-pink-600 font-medium">{{ $crumb->name }}</span>
        @endif
      @endforeach
    </nav>
    <h1 class="font-display italic text-4xl md:text-5xl text-gray-900">{{ $category->name }}</h1>
    <p class="text-sm text-gray-400 mt-2">{{ $products->total() }} {{ \Illuminate\Support\Str::plural('piece', $products->total()) }}</p>

    @if(($subcategories ?? $category->children)->count() > 0)
      <div class="flex items-center gap-2 flex-wrap mt-5">
        <span class="text-xs uppercase tracking-wider text-gray-400 font-bold">Subcategories:</span>
        @foreach($subcategories ?? $category->children as $sub)
          <a href="{{ route('category.show', $sub->slug) }}" class="px-4 py-1.5 rounded-full text-xs font-semibold border border-pink-200 text-gray-600 hover:border-pink-500 hover:text-pink-600 transition-colors">
            {{ $sub->name }}
          </a>
        @endforeach
      </div>
    @endif
  </div>

  {{-- Filter pills + sort --}}
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-10 pb-5 border-b border-pink-50">
    <div class="pill-row flex gap-2 overflow-x-auto -mx-1 px-1">
      @foreach ($categories as $cat)
        <a href="{{ route('category.show', $cat->slug) }}"
           class="flex-shrink-0 px-5 py-2 rounded-full text-[11px] font-bold uppercase tracking-widest transition-colors
                  {{ $cat->id === $category->id ? 'bg-pink-600 text-white' : 'border border-pink-100 text-gray-500 hover:border-pink-400 hover:text-pink-600' }}">
          {{ $cat->name }}
        </a>
      @endforeach
    </div>
    <form method="GET" action="{{ url()->current() }}" class="flex-shrink-0">
      <label for="sortSelect" class="sr-only">Sort products</label>
      <select id="sortSelect" name="sort" onchange="this.form.submit()"
              class="rounded-full border border-pink-100 bg-white text-xs font-semibold text-gray-700 px-4 py-2.5 outline-none focus:border-pink-400">
        <option value="latest"     {{ request('sort', 'latest') === 'latest' ? 'selected' : '' }}>Newest</option>
        <option value="price_asc"  {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Price: Low → High</option>
        <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Price: High → Low</option>
      </select>
    </form>
  </div>

  {{-- Grid --}}
  @if ($products->count())
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-x-5 gap-y-10 mb-14">
      @foreach ($products as $product)
        @include('storefront.lumiere.partials.lustre-card', ['product' => $product])
      @endforeach
    </div>
    <div class="flex justify-center">{{ $products->links() }}</div>
  @else
    <div class="text-center py-24">
      <p class="font-display italic text-2xl text-gray-300 mb-4">Nothing here yet</p>
      <a href="{{ route('home') }}" class="inline-block px-6 py-3 rounded-2xl text-xs font-bold uppercase tracking-widest bg-gray-900 text-white hover:bg-pink-600 transition-colors">Back to Home</a>
    </div>
  @endif
</div>

{{-- Quick-view modal for the cards --}}
@include('storefront.lumiere.partials.quick-view')

</x-storefront-layout>
