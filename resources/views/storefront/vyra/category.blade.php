{{--
    ======================================================================
    THEME: VYRA — Modern Noir & High-Contrast Streetwear
    PAGE: CATEGORY COLLECTION CATALOG
    ======================================================================
    File: resources/views/storefront/vyra/category.blade.php
    Controller: App\Http\Controllers\StorefrontController@category
    Route Context: GET /category/{slug}
    ViewModel Injections:
      - $theme: Active Template model ('vyra')
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
<div class="max-w-7xl mx-auto px-6 lg:px-10 py-10">

  <div class="mb-10 reveal">
    <nav class="font-body text-xs flex items-center gap-2 mb-4 opacity-50 text-[#F2F0EC] uppercase">
      <a href="{{ route('home') }}" class="hover:opacity-100">Home</a>
      @foreach($breadcrumbs ?? $category->getBreadcrumbs() as $crumb)
        <span>/</span>
        @if(!$loop->last)
          <a href="{{ route('category.show', $crumb->slug) }}" class="hover:opacity-100">{{ $crumb->name }}</a>
        @else
          <span style="color:#FF3D5A">{{ $crumb->name }}</span>
        @endif
      @endforeach
    </nav>
    <h1 class="font-display uppercase text-4xl md:text-5xl text-[#F2F0EC]">{{ $category->name }}</h1>
    <p class="font-body text-sm opacity-50 mt-2 text-[#F2F0EC]">{{ $products->total() }} products</p>

    @if(($subcategories ?? $category->children)->count() > 0)
      <div class="flex items-center gap-2 flex-wrap mt-5">
        <span class="text-xs font-bold uppercase tracking-wider opacity-60 text-[#F2F0EC]">Subcategories:</span>
        @foreach($subcategories ?? $category->children as $sub)
          <a href="{{ route('category.show', $sub->slug) }}" class="px-4 py-1.5 text-xs font-bold uppercase tracking-wider transition-all border border-[#FF3D5A] text-[#F2F0EC] hover:bg-[#FF3D5A] hover:text-[#0E0E10]">
            {{ $sub->name }}
          </a>
        @endforeach
      </div>
    @endif
  </div>

  <div class="flex flex-wrap items-center justify-between gap-4 mb-8 pb-4 border-b" style="border-color:#FF3D5A22">
    <div class="flex gap-2 flex-wrap">
      @foreach ($categories as $cat)
        <a href="{{ route('category.show', $cat->slug) }}"
           class="px-4 py-1.5 text-xs font-bold uppercase tracking-widest transition-all"
           style="{{ $cat->id === $category->id ? 'background:#FF3D5A;color:#0E0E10;' : 'border:1px solid #FF3D5A44;color:#F2F0EC;' }}">
          {{ $cat->name }}
        </a>
      @endforeach
    </div>
    <form method="GET" action="{{ url()->current() }}">
      <select name="sort" onchange="this.form.submit()" class="border text-xs px-3 py-2 outline-none" style="border-color:#FF3D5A44;color:#F2F0EC;background:#0E0E10">
        <option value="latest" {{ request('sort','latest')==='latest'?'selected':'' }}>Latest</option>
        <option value="price_asc" {{ request('sort')==='price_asc'?'selected':'' }}>Price: Low → High</option>
        <option value="price_desc" {{ request('sort')==='price_desc'?'selected':'' }}>Price: High → Low</option>
      </select>
    </form>
  </div>

  @if ($products->count())
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5 mb-12">
      @foreach ($products as $product)
        @include('storefront.vyra.partials.product-card', ['product' => $product])
      @endforeach
    </div>
    {{ $products->links() }}
  @else
    <div class="text-center py-20 opacity-40 text-[#F2F0EC]">
      <p class="font-display uppercase text-2xl">No products found</p>
    </div>
  @endif
</div>
</x-storefront-layout>
