{{-- resources/views/storefront/vyra/search.blade.php --}}
<x-storefront-layout :theme="$theme" title="Search">
<div class="max-w-7xl mx-auto px-6 lg:px-10 py-10">

  <form method="GET" action="{{ route('search') }}" class="flex gap-3 mb-10 reveal">
    <input type="text" name="q" value="{{ $q }}" placeholder="Search products…" autofocus
           class="flex-1 border px-5 py-3.5 text-base outline-none bg-transparent font-body"
           style="border-color:#FF3D5A44;color:#F2F0EC">
    <button type="submit" class="px-6 py-3.5 text-xs font-bold uppercase tracking-widest transition-all" style="background:#FF3D5A;color:#0E0E10;clip-path:polygon(6% 0,100% 0,94% 100%,0 100%)">Search</button>
  </form>

  @if ($q)
    <p class="font-body text-sm opacity-50 mb-8 text-[#F2F0EC]">
      {{ method_exists($results, 'total') ? $results->total() : $results->count() }} results for &ldquo;{{ $q }}&rdquo;
    </p>
    @if (method_exists($results, 'total') ? $results->total() : $results->count())
      <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5 mb-12">
        @foreach ($results as $product)
          @include('storefront.vyra.partials.product-card', ['product' => $product])
        @endforeach
      </div>
      @if (method_exists($results, 'links'))
        {{ $results->appends(['q' => $q])->links() }}
      @endif
    @else
      <div class="text-center py-20 opacity-40 text-[#F2F0EC]">
        <p class="font-display uppercase text-2xl">No results found</p>
        <p class="font-body text-sm mt-2">Try a different term or browse categories.</p>
      </div>
    @endif
  @else
    <p class="font-body text-xs font-bold uppercase tracking-widest opacity-40 mb-4 text-[#F2F0EC]">Popular Searches</p>
    <div class="flex flex-wrap gap-2">
      @foreach (['Mesh Bralette', 'High-Waist Thong', 'Slip Cami', 'Strappy Set', 'Sports Bra', 'Push-Up'] as $term)
        <a href="{{ route('search') }}?q={{ urlencode($term) }}"
           class="border px-4 py-2 text-sm font-body transition-all hover:opacity-70 uppercase"
           style="border-color:#FF3D5A44;color:#F2F0EC">{{ $term }}</a>
      @endforeach
    </div>
  @endif
</div>
</x-storefront-layout>
