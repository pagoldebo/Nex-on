{{-- resources/views/storefront/organic/components/category-sheet.blade.php
     Mobile category offcanvas drawer. --}}
<div id="categorySheet" class="invisible fixed inset-0 z-50 pointer-events-none transition-all duration-300 opacity-0 bg-stone-900/60 backdrop-blur-xs">
  <div id="categorySheetPanel" class="fixed inset-y-0 left-0 max-w-xs w-full bg-white shadow-2xl p-6 transform -translate-x-full transition-transform duration-300 flex flex-col justify-between overflow-y-auto pointer-events-auto">
    <div>
      <div class="flex items-center justify-between pb-4 border-b border-stone-200">
        <h3 class="text-base font-extrabold text-stone-900 font-display flex items-center gap-2">
          <svg class="w-5 h-5 text-organic-500" viewBox="0 0 24 24"><use xlink:href="#organic-leaf"></use></svg>
          Organic Departments
        </h3>
        <button type="button" onclick="closeCategorySheet()" class="p-1.5 text-stone-400 hover:text-stone-700 rounded-full" aria-label="Close categories">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
      </div>

      <div class="mt-4 space-y-1">
        <a href="{{ route('featured.products') }}" class="flex items-center justify-between p-2.5 rounded-xl hover:bg-organic-50 text-organic-700 font-bold text-xs">
          <span>🌟 Featured Products</span>
          <span class="text-[10px] text-organic-600 bg-organic-100 px-2 py-0.5 rounded-full">Top Picks</span>
        </a>
        <a href="{{ route('new.arrivals') }}" class="flex items-center justify-between p-2.5 rounded-xl hover:bg-organic-50 text-stone-800 font-bold text-xs">
          <span>🌱 Fresh Arrivals</span>
          <span class="text-[10px] text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">New</span>
        </a>
        <a href="{{ route('sale.products') }}" class="flex items-center justify-between p-2.5 rounded-xl hover:bg-organic-50 text-citrus font-bold text-xs">
          <span>🏷️ Farm Deals &amp; Discounts</span>
          <span class="text-[10px] text-white bg-citrus px-2 py-0.5 rounded-full">Hot</span>
        </a>
      </div>

      <div class="mt-6 pt-4 border-t border-stone-200">
        <h4 class="text-[11px] font-bold text-stone-400 uppercase tracking-wider mb-2 px-1">All Categories</h4>
        <div class="space-y-2">
          @forelse($categories as $cat)
            <div class="rounded-xl border border-stone-100 bg-stone-50/50 p-2.5">
              <a href="{{ route('category.show', $cat->slug) }}" class="flex items-center gap-2 text-xs font-bold text-stone-800 hover:text-organic-600 transition">
                <span class="w-2 h-2 rounded-full bg-organic-500"></span>
                <span>{{ $cat->name }}</span>
              </a>
              @if($cat->children && $cat->children->count() > 0)
                <div class="mt-2 pl-3 pt-2 border-t border-stone-200/60 space-y-1.5">
                  @foreach($cat->children as $sub)
                    <a href="{{ route('category.show', $sub->slug) }}" class="block text-[11px] text-stone-600 hover:text-organic-600 transition pl-1">
                      • {{ $sub->name }}
                    </a>
                  @endforeach
                </div>
              @endif
            </div>
          @empty
            <p class="text-xs text-stone-400 p-2">No categories available.</p>
          @endforelse
        </div>
      </div>
    </div>

    <div class="pt-6 border-t border-stone-200">
      <a href="{{ route('home') }}" class="block w-full py-2.5 text-center bg-organic-50 hover:bg-organic-100 text-organic-700 font-bold text-xs rounded-xl transition">
        Return to Home Page
      </a>
    </div>
  </div>
</div>

<script>
  function openCategorySheet() {
    const sheet = document.getElementById('categorySheet');
    const panel = document.getElementById('categorySheetPanel');
    if (!sheet || !panel) return;
    sheet.classList.remove('invisible', 'pointer-events-none', 'opacity-0');
    sheet.classList.add('opacity-100');
    panel.classList.remove('-translate-x-full');
  }

  function closeCategorySheet() {
    const sheet = document.getElementById('categorySheet');
    const panel = document.getElementById('categorySheetPanel');
    if (!sheet || !panel) return;
    panel.classList.add('-translate-x-full');
    sheet.classList.remove('opacity-100');
    sheet.classList.add('opacity-0', 'pointer-events-none');
    setTimeout(() => {
      sheet.classList.add('invisible');
    }, 300);
  }

  document.getElementById('categorySheet')?.addEventListener('click', function(e) {
    if (e.target === this) closeCategorySheet();
  });
</script>

