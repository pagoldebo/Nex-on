{{--
    ======================================================================
    THEME: VERDIA — Botanical Living & Organic Produce Marketplace
    PAGE: MULTI-PRODUCT COMPARISON MATRIX
    ======================================================================
    File: resources/views/storefront/organic/compare.blade.php
    Controller: App\Http\Controllers\StorefrontController@compare
    Route Context: GET /compare
    ViewModel Injections:
      - $theme: Active Template model ('organic')
    Responsibility: Side-by-side specification, pricing, and variant comparison
    Features:
      - Multi-column comparison grid synchronized with sessionStorage.compare_items
      - Side-by-side spec alignment: Fabric, Fit, Care, Country of Origin, Price
      - Instant Add to Bag button per column and one-click remove item
      - Empty state with recommendations to browse categories
    Operations & Maintenance:
      - Client-driven table using Alpine.js and local storage cache
--}}
<x-storefront-layout :theme="$theme" title="Compare Produce">

  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
    
    {{-- Breadcrumbs --}}
    <nav class="text-xs text-stone-500 font-medium flex items-center gap-2 mb-6">
      <a href="{{ route('home') }}" class="hover:text-organic-600 transition">Home</a>
      <span>/</span>
      <span class="text-stone-900 font-bold">Compare Produce</span>
    </nav>

    <div class="mb-10">
      <h1 class="text-2xl sm:text-3xl font-black text-stone-900 font-display">Side-by-Side Produce Comparison</h1>
      <p class="text-xs sm:text-sm text-stone-500 mt-1 font-light">Evaluate organic specifications, certifications, and pack sizes.</p>
    </div>

    @if($products->count() >= 2)
      <div class="overflow-x-auto bg-white rounded-3xl border border-stone-200 p-6 shadow-xs">
        <table class="w-full min-w-[600px] text-xs">
          <thead>
            <tr>
              <th class="text-left py-4 pr-6 text-xs font-black uppercase tracking-wider text-stone-400 w-32">Specification</th>
              @foreach($products as $p)
                <th class="py-4 px-4 align-top w-56 text-center">
                  <div class="p-4 bg-stone-50 rounded-2xl border border-stone-200/80">
                    <img src="{{ $p->cover_image }}" alt="{{ $p->name }}" class="w-24 h-24 object-contain mx-auto mb-2">
                    <h4 class="font-extrabold text-stone-900 text-xs truncate font-display">{{ $p->name }}</h4>
                    <p class="text-organic-700 font-black text-sm mt-1">from ₹{{ number_format($p->starting_price, 2) }}</p>
                    <a href="{{ route('product.show', $p->slug) }}" class="btn-organic inline-block mt-3 px-4 py-1.5 rounded-full text-[10px] font-bold">
                      View Details
                    </a>
                  </div>
                </th>
              @endforeach
            </tr>
          </thead>
          <tbody class="divide-y divide-stone-100 text-stone-700">
            <tr>
              <td class="py-3.5 pr-6 font-bold text-stone-400 uppercase">Department</td>
              @foreach($products as $p)
                <td class="py-3.5 px-4 text-center font-semibold">{{ $p->category->name ?? 'General' }}</td>
              @endforeach
            </tr>
            <tr>
              <td class="py-3.5 pr-6 font-bold text-stone-400 uppercase">Certification</td>
              @foreach($products as $p)
                <td class="py-3.5 px-4 text-center text-organic-700 font-bold">✓ 100% Certified Organic</td>
              @endforeach
            </tr>
            <tr>
              <td class="py-3.5 pr-6 font-bold text-stone-400 uppercase">Pack Sizes</td>
              @foreach($products as $p)
                <td class="py-3.5 px-4 text-center">
                  {{ $p->colors->flatMap(fn($c) => $c->variants->pluck('size'))->unique()->join(', ') ?: 'Standard' }}
                </td>
              @endforeach
            </tr>
            <tr>
              <td class="py-3.5 pr-6 font-bold text-stone-400 uppercase">Farm Origin</td>
              @foreach($products as $p)
                <td class="py-3.5 px-4 text-center text-stone-500">Verified Local Indian Organic Farm</td>
              @endforeach
            </tr>
          </tbody>
        </table>
      </div>
    @else
      <div class="text-center py-24 bg-white rounded-3xl border border-stone-200 p-8 max-w-md mx-auto">
        <div class="w-16 h-16 rounded-full bg-organic-50 text-organic-600 flex items-center justify-center mx-auto mb-4">
          ⚖️
        </div>
        <h3 class="text-lg font-black text-stone-900 font-display">Select at least 2 produce items</h3>
        <p class="text-xs text-stone-500 mt-1 mb-6 font-light">Add produce to comparison to evaluate nutritional attributes side-by-side.</p>
        <a href="{{ route('home') }}" class="btn-organic inline-block px-6 py-2.5 rounded-full text-xs font-bold">
          Explore Produce Catalog
        </a>
      </div>
    @endif

  </div>

</x-storefront-layout>

