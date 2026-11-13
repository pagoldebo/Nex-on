{{--
    ======================================================================
    THEME: LUMIÈRE — French Silk Atelier & Minimalist Luxury
    PAGE: MULTI-PRODUCT COMPARISON MATRIX
    ======================================================================
    File: resources/views/storefront/lumiere/compare.blade.php
    Controller: App\Http\Controllers\StorefrontController@compare
    Route Context: GET /compare
    ViewModel Injections:
      - $theme: Active Template model ('lumiere')
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

<style>
  .animate-fade { animation: pageFadeIn .5s ease both; }
  /* The attribute label column stays fixed while the product columns scroll horizontally on mobile */
  .cmp-table { border-collapse: separate; border-spacing: 0; }
  .cmp-table td, .cmp-table th { vertical-align: top; }
  .cmp-label-col { position: sticky; left: 0; background: #FFFAF5; z-index: 10; }
</style>

<div class="max-w-7xl mx-auto px-6 md:px-12 py-10">

  <div class="mb-10 reveal">
    <h1 class="font-display italic text-3xl md:text-4xl text-gray-900 mb-2">Compare</h1>
    <p class="text-sm text-gray-400">Side-by-side product comparison</p>
  </div>

  @if ($products->count() >= 2)

    {{-- Quick-remove bar above the table --}}
    <div class="flex flex-wrap gap-3 mb-8">
      @foreach ($products as $p)
        <div class="flex items-center gap-2 bg-white border border-pink-100 rounded-full px-4 py-2 shadow-sm">
          <span class="text-xs font-semibold text-gray-700 truncate max-w-[140px]">{{ $p->name }}</span>
          <a href="{{ route('compare') }}?ids={{ $products->where('id','!=',$p->id)->pluck('id')->join(',') }}"
             class="text-gray-300 hover:text-red-400 transition-colors text-base leading-none"
             aria-label="Remove {{ $p->name }} from compare">&times;</a>
        </div>
      @endforeach
      <a href="{{ route('compare') }}" class="flex items-center gap-2 border border-dashed border-pink-200 rounded-full px-4 py-2 text-xs font-semibold text-gray-400 hover:text-pink-600 hover:border-pink-400 transition-colors">
        Clear all &times;
      </a>
    </div>

    {{-- Comparison table --}}
    <div class="overflow-x-auto rounded-[28px] border border-pink-50 bg-white shadow-sm shadow-pink-50 mb-10">
      <table class="cmp-table w-full min-w-[560px]">

        {{-- Product headers --}}
        <thead>
          <tr class="border-b border-pink-50">
            <th class="cmp-label-col w-36 py-5 pl-6 pr-4 text-left border-r border-pink-50"></th>
            @foreach ($products as $p)
              <th class="py-5 px-5 text-center align-top border-r border-pink-50 last:border-r-0">
                <div class="flex flex-col items-center gap-3">
                  <a href="{{ route('product.show', $p->slug) }}" class="block w-full max-w-[160px]">
                    <img src="{{ $p->cover_image }}" alt="{{ $p->name }}"
                         class="w-full aspect-square object-cover rounded-2xl bg-pink-50 hover:opacity-80 transition-opacity">
                  </a>
                  <p class="font-display italic text-sm text-gray-900 leading-tight">{{ $p->name }}</p>
                  <p class="text-pink-600 font-bold text-sm">from ₹{{ number_format($p->starting_price, 0) }}</p>
                  <button onclick="openQuickView({{ \Illuminate\Support\Js::from($p->toCardArray()) }})"
                          class="w-full px-3 py-2 rounded-full text-[10px] font-bold uppercase tracking-widest bg-pink-600 text-white hover:bg-pink-700 transition-colors">
                    Quick View
                  </button>
                  <a href="{{ route('product.show', $p->slug) }}"
                     class="w-full px-3 py-2 rounded-full text-[10px] font-bold uppercase tracking-widest border border-pink-200 text-gray-600 hover:border-pink-400 hover:text-pink-600 transition-colors text-center">
                    Full Details
                  </a>
                </div>
              </th>
            @endforeach
          </tr>
        </thead>

        {{-- Attribute rows --}}
        <tbody class="text-sm text-gray-700">
          @php
            $rows = [
              ['Category',   fn($p) => $p->category->name],
              ['Badge',      fn($p) => $p->badge ?? '—'],
              ['Colors',     fn($p) => $p->colors->pluck('name')->join(', ') ?: '—'],
              ['Sizes',      fn($p) => $p->colors->flatMap(fn($c) => $c->variants->pluck('size'))->unique()->sort()->join(', ') ?: '—'],
              ['Min Price',  fn($p) => '₹'.number_format($p->starting_price, 0)],
              ['In Stock',   fn($p) => $p->total_stock > 0 ? $p->total_stock.' units' : 'Out of stock'],
            ];
          @endphp

          @foreach ($rows as [$label, $fn])
            <tr class="border-t border-pink-50 {{ $loop->odd ? 'bg-white' : 'bg-[#FFFAF5]' }}">
              <td class="cmp-label-col py-4 pl-6 pr-4 border-r border-pink-50">
                <span class="text-[10px] uppercase tracking-widest font-bold text-gray-400">{{ $label }}</span>
              </td>
              @foreach ($products as $p)
                <td class="py-4 px-5 text-center border-r border-pink-50 last:border-r-0">
                  {{ $fn($p) }}
                </td>
              @endforeach
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    {{-- Add another product CTA --}}
    @if ($products->count() < 4)
      <div class="text-center py-6 border-2 border-dashed border-pink-100 rounded-[28px] mb-10">
        <p class="text-sm text-gray-400 mb-3">Add {{ 4 - $products->count() }} more {{ Str::plural('product', 4 - $products->count()) }} to compare</p>
        <a href="{{ route('search') }}" class="inline-block px-6 py-3 rounded-2xl text-xs font-bold uppercase tracking-widest border border-pink-200 text-gray-600 hover:border-pink-400 hover:text-pink-600 transition-colors">Browse Products</a>
      </div>
    @endif

  @else
    {{-- Empty / not enough products --}}
    <div class="text-center py-24">
      <svg class="w-14 h-14 mx-auto text-pink-100 mb-5" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
      <p class="font-display italic text-2xl text-gray-300 mb-2">Nothing to compare yet</p>
      <p class="text-sm text-gray-400 mb-6 max-w-xs mx-auto">Select pieces from our collections to evaluate specifications side-by-side.</p>
      <a href="{{ route('home') }}" class="inline-block px-6 py-3 rounded-2xl text-xs font-bold uppercase tracking-widest bg-[#405549] text-white hover:bg-pink-600 transition-colors">Discover Pieces</a>
    </div>
  @endif
</div>

{{-- Quick-view modal (used by the Quick View button in each product header) --}}
@include('storefront.lumiere.partials.quick-view')

</x-storefront-layout>
