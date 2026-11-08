{{--
    ======================================================================
    THEME: VERDIA — Botanical Living & Organic Produce Marketplace
    PAGE: CATEGORY COLLECTION CATALOG
    ======================================================================
    File: resources/views/storefront/organic/category.blade.php
    Controller: App\Http\Controllers\StorefrontController@category
    Route Context: GET /category/{slug}
    ViewModel Injections:
      - $theme: Active Template model ('organic')
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
<x-storefront-layout :theme="$theme" :title="$category->name">
  
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
    
    {{-- Header Banner & Breadcrumbs --}}
    <div class="mb-10 reveal">
      <nav class="text-xs text-stone-500 font-medium flex items-center gap-2 mb-3">
        <a href="{{ route('home') }}" class="hover:text-organic-600 transition">Home</a>
        @foreach($breadcrumbs ?? ($category->exists ? $category->getBreadcrumbs() : []) as $crumb)
          <span>/</span>
          @if(!$loop->last)
            <a href="{{ route('category.show', $crumb->slug) }}" class="hover:text-organic-600 transition">{{ $crumb->name }}</a>
          @else
            <span class="text-organic-700 font-bold">{{ $crumb->name }}</span>
          @endif
        @endforeach
      </nav>

      <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
          <span class="text-xs font-bold uppercase tracking-wider text-organic-600 block mb-1">Organic Catalog</span>
          <h1 class="text-3xl sm:text-4xl font-black text-stone-900 font-display">{{ $category->name }}</h1>
          @if(!empty($category->description))
            <p class="text-xs sm:text-sm text-stone-600 mt-2 max-w-2xl font-light">{{ $category->description }}</p>
          @endif
        </div>
        <div class="shrink-0">
          <span class="text-xs font-bold text-stone-700 bg-organic-50 px-3.5 py-1.5 rounded-full border border-organic-200">
            {{ $products->total() }} Certified Produce Available
          </span>
        </div>
      </div>

      {{-- Subcategories Pills if any --}}
      @if(isset($subcategories) && $subcategories->count() > 0)
        <div class="flex items-center gap-2 flex-wrap mt-5 pt-4 border-t border-stone-200/70">
          <span class="text-xs font-bold text-stone-400 uppercase tracking-wider mr-1">Sub-Depts:</span>
          @foreach($subcategories as $sub)
            <a href="{{ route('category.show', $sub->slug) }}" class="px-3.5 py-1.5 rounded-full text-xs font-semibold bg-white hover:bg-organic-50 text-stone-700 hover:text-organic-700 border border-stone-200 hover:border-organic-400 transition shadow-2xs">
              {{ $sub->name }}
            </a>
          @endforeach
        </div>
      @endif
    </div>

    {{-- Filter Toolbar: Department tabs & Sorting --}}
    <div class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4 mb-8 pb-5 border-b border-stone-200">
      
      {{-- Horizontal Department Pills --}}
      <div class="flex items-center gap-2 overflow-x-auto pb-1 max-w-full">
        <a href="{{ route('home') }}#products" 
           class="px-4 py-2 rounded-full text-xs font-bold transition whitespace-nowrap {{ $category->id == 0 ? 'bg-organic-500 text-white shadow-xs' : 'bg-white border border-stone-200 text-stone-700 hover:bg-stone-50' }}">
          All Departments
        </a>
        @foreach($categories->take(8) as $cat)
          <a href="{{ route('category.show', $cat->slug) }}" 
             class="px-4 py-2 rounded-full text-xs font-bold transition whitespace-nowrap {{ $cat->id === $category->id ? 'bg-organic-500 text-white shadow-xs' : 'bg-white border border-stone-200 text-stone-700 hover:bg-stone-50' }}">
            {{ $cat->name }}
          </a>
        @endforeach
      </div>

      {{-- Sorting Selector --}}
      <form method="GET" action="{{ url()->current() }}" class="shrink-0 flex items-center gap-2">
        <label for="sortSelector" class="text-xs font-bold text-stone-500 uppercase">Sort:</label>
        <select id="sortSelector" 
                name="sort" 
                onchange="this.form.submit()" 
                class="bg-white border border-stone-200 text-xs font-bold text-stone-800 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-organic-500/30">
          <option value="latest" {{ request('sort', 'latest') === 'latest' ? 'selected' : '' }}>Latest Harvests</option>
          <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
          <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
        </select>
      </form>

    </div>

    {{-- Product Grid --}}
    @if($products->count())
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 mb-12">
        @foreach($products as $product)
          @include('storefront.organic.partials.product-card', ['product' => $product])
        @endforeach
      </div>

      {{-- Pagination --}}
      <div class="mt-8 flex justify-center">
        {{ $products->links() }}
      </div>
    @else
      <div class="text-center py-24 bg-white rounded-3xl border border-stone-200 p-8 max-w-md mx-auto">
        <div class="w-16 h-16 rounded-full bg-organic-50 text-organic-600 flex items-center justify-center mx-auto mb-4">
          <svg class="w-8 h-8 fill-current" viewBox="0 0 24 24"><use xlink:href="#organic-leaf"></use></svg>
        </div>
        <h3 class="text-lg font-black text-stone-900 font-display">No produce found in this department</h3>
        <p class="text-xs text-stone-500 mt-1 mb-6 font-light">Fresh seasonal items will be restocked following upcoming farmer harvests.</p>
        <a href="{{ route('home') }}" class="btn-organic inline-block px-6 py-2.5 rounded-full text-xs font-bold">
          Explore Other Produce
        </a>
      </div>
    @endif

  </div>

</x-storefront-layout>

