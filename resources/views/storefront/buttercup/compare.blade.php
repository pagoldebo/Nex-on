{{--
    ======================================================================
    THEME: BUTTERCUP — Pastel Comfort Intimates & Loungewear
    PAGE: MULTI-PRODUCT COMPARISON MATRIX
    ======================================================================
    File: resources/views/storefront/buttercup/compare.blade.php
    Controller: App\Http\Controllers\StorefrontController@compare
    Route Context: GET /compare
    ViewModel Injections:
      - $theme: Active Template model ('buttercup')
    Responsibility: Side-by-side specification, pricing, and variant comparison
    Features:
      - Multi-column comparison grid synchronized with sessionStorage.compare_items
      - Side-by-side spec alignment: Fabric, Fit, Care, Country of Origin, Price
      - Instant Add to Bag button per column and one-click remove item
      - Empty state with recommendations to browse categories
    Operations & Maintenance:
      - Client-driven table using Alpine.js and local storage cache
--}}
<x-storefront-layout :theme="$theme" title="Compare Products">
<div class="max-w-7xl mx-auto px-6 lg:px-10 py-10">
  <h1 class="font-display text-3xl md:text-4xl mb-2 reveal text-[#4A3B3A]">Compare</h1>
  <p class="font-body text-sm opacity-50 mb-10 text-[#4A3B3A]">Side-by-side comparison</p>

  @if ($products->count() >= 2)
    <div class="overflow-x-auto">
      <table class="w-full min-w-[560px]">
        <thead>
          <tr>
            <th class="text-left py-4 pr-6 text-xs font-bold uppercase tracking-widest opacity-40 font-body w-28 text-[#4A3B3A]">Attribute</th>
            @foreach ($products as $p)
              <th class="py-4 px-3 align-top">
                <div class="bg-[#FFFAF5] rounded-[28px] border p-4 text-center" style="border-color:#D89AA022">
                  <img src="{{ $p->cover_image }}" class="w-full aspect-square object-cover rounded-2xl mb-3">
                  <p class="font-display text-sm text-[#4A3B3A]">{{ $p->name }}</p>
                  <p class="text-[#B97680] text-xs font-bold mt-1">from ₹{{ number_format($p->starting_price, 0) }}</p>
                  <a href="{{ route('product.show', $p->slug) }}" class="inline-block mt-3 px-4 py-1.5 text-[10px] font-bold uppercase tracking-widest bg-[#D89AA0] text-[#FFFAF5] rounded-full">View</a>
                </div>
              </th>
            @endforeach
          </tr>
        </thead>
        <tbody class="font-body text-sm text-[#4A3B3A]">
          @foreach ([
            ['Category', fn($p) => $p->category->name],
            ['Badge',    fn($p) => $p->badge ?? '—'],
            ['Colors',   fn($p) => $p->colors->pluck('name')->join(', ')],
            ['Sizes',    fn($p) => $p->colors->flatMap(fn($c) => $c->variants->pluck('size'))->unique()->join(', ')],
            ['Min Price',fn($p) => '₹'.number_format($p->starting_price, 0)],
          ] as [$label, $fn])
            <tr class="border-t" style="border-color:#D89AA011">
              <td class="py-4 pr-6 text-xs font-bold uppercase tracking-widest opacity-40">{{ $label }}</td>
              @foreach ($products as $p)
                <td class="py-4 px-3 text-center">{{ $fn($p) }}</td>
              @endforeach
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @else
    <div class="text-center py-20 opacity-40 text-[#4A3B3A]">
      <p class="font-display text-2xl mb-2">Select at least 2 products</p>
      <p class="font-body text-sm mb-6">Explore our curated collections and select products.</p>
      <a href="{{ route('home') }}" class="inline-block px-6 py-3 text-xs font-bold uppercase tracking-widest bg-[#D89AA0] text-[#FFFAF5] hover:bg-[#B97680] rounded-full transition-colors">Browse Products</a>
    </div>
  @endif
</div>
</x-storefront-layout>
