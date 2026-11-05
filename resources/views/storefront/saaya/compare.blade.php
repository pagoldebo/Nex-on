{{-- resources/views/storefront/saaya/compare.blade.php
     Saaya compare page — up to 4 products side-by-side.
     Controller passes: $products (Collection, max 4, toCardArray-ready),
                        $categories (layout) --}}

<x-storefront-layout>

<style>
  /* Sticky label column */
  .cmp-label-col {
    position: sticky;
    left: 0;
    background: #F7F1E6; /* ivory */
    z-index: 10;
    min-width: 130px;
  }
  .cmp-table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
  .cmp-table { min-width: 600px; width: 100%; border-collapse: collapse; }
  .cmp-table td, .cmp-table th { vertical-align: top; }
</style>

<div class="bg-ivory min-h-screen">

{{-- Header --}}
<div class="bg-maroon py-12 lg:py-16 relative overflow-hidden">
  <div class="absolute inset-0 opacity-[.05] pointer-events-none"
       style="background-image:radial-gradient(circle,#C9A24B 1px,transparent 1px);background-size:24px 24px;"></div>
  <div class="relative max-w-7xl mx-auto px-6 lg:px-12 text-center">
    <p class="font-display italic text-gold text-lg mb-1">Side by Side</p>
    <h1 class="font-display text-5xl text-ivory">Compare</h1>
    @if($products->count())
      <p class="font-body text-ivory/50 text-sm mt-3">Comparing {{ $products->count() }} {{ Str::plural('piece', $products->count()) }}</p>
    @endif
  </div>
</div>


<div class="max-w-7xl mx-auto px-6 lg:px-12 py-10 lg:py-14">

  @if($products->count() < 2)
    {{-- Not enough products --}}
    <div class="text-center py-20">
      <svg class="w-12 h-12 text-gold/30 mx-auto mb-4" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7"/>
      </svg>
      <p class="font-display text-3xl text-maroon mb-2">Nothing to compare</p>
      <p class="font-body text-sm text-maroon/50 mb-8">Add at least 2 products to compare them side by side.</p>
      <a href="{{ route('search') }}" class="gold-btn py-3.5 px-8 text-xs font-body font-semibold tracking-widest uppercase inline-block">Browse Collection</a>
    </div>

  @else

    @php
      $attrs = [
        'Image'         => fn($p) => null,            // handled specially
        'Price'         => fn($p) => '₹' . number_format($p->startingPrice ?? optional($p->colors->first()?->variants->first())->price ?? 0, 0),
        'Category'      => fn($p) => $p->category?->name ?? '—',
        'Available In'  => fn($p) => $p->colors->pluck('name')->join(', ') ?: '—',
        'Sizes'         => fn($p) => $p->colors->first()?->variants->pluck('size')->unique()->join(', ') ?: '—',
        'In Stock'      => fn($p) => $p->colors->flatMap->variants->sum('stock') > 0 ? 'Yes' : 'Out of stock',
      ];
    @endphp

    <div class="cmp-table-wrap">
      <table class="cmp-table">

        {{-- Product headers --}}
        <thead>
          <tr>
            <th class="cmp-label-col w-36 pb-4 pr-4"></th>
            @foreach($products as $p)
              @php $card = $p->toCardArray(); @endphp
              <th class="pb-4 px-4 text-left" style="width:{{ round(100 / ($products->count() + 1)) }}%">
                <div class="relative group">
                  {{-- Remove button --}}
                  <a href="{{ route('compare') }}?ids={{ $products->pluck('id')->reject(fn($id) => $id == $p->id)->join(',') }}"
                     class="absolute -top-1 -right-1 z-10 w-6 h-6 bg-ivory border border-gold/30 flex items-center justify-center text-maroon/40 hover:text-maroon hover:border-gold transition-colors"
                     aria-label="Remove {{ $p->name }}">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                  </a>
                  {{-- Image --}}
                  <a href="{{ route('product.show', $p->slug) }}">
                    <div class="aspect-[3/4] overflow-hidden bg-blush/30 mb-3">
                      <img src="{{ $card['coverImage'] }}" alt="{{ $p->name }}"
                           class="w-full h-full object-cover hover:scale-105 transition-transform duration-500">
                    </div>
                    <p class="font-display text-base text-maroon leading-snug">{{ $p->name }}</p>
                  </a>
                  @if($card['badge'])
                    <span class="inline-block mt-1 bg-gold text-maroon text-[9px] px-2 py-0.5 tracking-widest uppercase font-body">{{ $card['badge'] }}</span>
                  @endif
                </div>
              </th>
            @endforeach

            {{-- Add another slot (if < 4) --}}
            @if($products->count() < 4)
              <th class="pb-4 px-4" style="width:{{ round(100 / ($products->count() + 1)) }}%">
                <a href="{{ route('search') }}?compare_ids={{ $products->pluck('id')->join(',') }}"
                   class="flex flex-col items-center justify-center aspect-[3/4] border-2 border-dashed border-gold/30 text-maroon/40 hover:border-gold hover:text-gold transition-colors group">
                  <svg class="w-8 h-8 mb-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                  <span class="font-body text-xs">Add piece</span>
                </a>
              </th>
            @endif
          </tr>
        </thead>

        {{-- Attribute rows --}}
        <tbody class="divide-y divide-gold/15">

          @foreach($attrs as $label => $fn)
            @if($label === 'Image') @continue @endif
            <tr>
              <td class="cmp-label-col py-4 pr-4">
                <p class="text-[10px] font-body font-semibold uppercase tracking-widest text-gold-dark">{{ $label }}</p>
              </td>
              @foreach($products as $p)
                <td class="py-4 px-4">
                  <p class="font-body text-sm text-maroon">{{ $fn($p) }}</p>
                </td>
              @endforeach
              @if($products->count() < 4)<td></td>@endif
            </tr>
          @endforeach

          {{-- CTA row --}}
          <tr>
            <td class="cmp-label-col py-5 pr-4"></td>
            @foreach($products as $p)
              <td class="py-5 px-4">
                <a href="{{ route('product.show', $p->slug) }}"
                   class="gold-btn block text-center py-3 text-[10px] font-body font-semibold tracking-widest uppercase">
                  View &amp; Buy
                </a>
              </td>
            @endforeach
            @if($products->count() < 4)<td></td>@endif
          </tr>

        </tbody>
      </table>
    </div>

    {{-- Clear all --}}
    <div class="text-center mt-8">
      <a href="{{ route('compare') }}" class="font-body text-xs text-maroon/40 hover:text-gold transition-colors underline-grow">
        Clear comparison
      </a>
    </div>

  @endif

</div>
</div>

</x-storefront-layout>
