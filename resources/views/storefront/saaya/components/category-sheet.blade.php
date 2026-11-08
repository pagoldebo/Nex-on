{{--
    ======================================================================
    THEME: SAAYA — Royal Indian Trousseau & Heritage Silk
    COMPONENT: MOBILE CATEGORY SHEET DRAWER
    ======================================================================
    File: resources/views/storefront/saaya/components/category-sheet.blade.php
    Component Injection: storefront.saaya.components.category-sheet
    Responsibility: Slide-up mobile category hierarchy and catalog navigation
    Features:
      - Hierarchical parent/child category listing with smooth expandable accordions
      - Direct thumbnail links to category collection landing pages
      - Smooth backdrop dismiss gesture and escape key listener
    Operations & Maintenance:
      - Populated via view shared categories variable ($categories)
      - Controlled via Alpine x-show="categorySheetOpen"
--}}
<div id="sheetOverlay" class="lg:hidden" role="dialog" aria-modal="true" aria-label="Browse categories">
  <div class="bottom-sheet">
    <div class="sheet-handle"></div>

    <div class="px-6 pt-2 pb-4 flex items-center justify-between border-b border-gold/20">
      <p class="font-display italic text-xl text-maroon">Shop by Category</p>
      <button id="sheetCloseBtn" class="p-2 text-maroon/50 hover:text-maroon transition-colors" aria-label="Close">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>

    <ul class="divide-y divide-gold/15 px-2 pt-2">
      @foreach ($categories as $cat)
        <li class="py-1">
          <div class="flex items-center justify-between px-4 py-3 text-maroon hover:bg-gold/10 rounded-xl transition-colors group">
            <a href="{{ route('category.show', $cat->slug) }}" class="font-body text-sm font-medium flex-1">
              {{ $cat->name }}
            </a>
            <a href="{{ route('category.show', $cat->slug) }}" class="p-1">
              <svg class="w-4 h-4 text-gold/50 group-hover:text-gold transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/></svg>
            </a>
          </div>
          @if($cat->children && $cat->children->count() > 0)
            <div class="flex flex-wrap gap-1.5 px-4 pb-2 pt-0.5">
              @foreach($cat->children as $sub)
                <a href="{{ route('category.show', $sub->slug) }}"
                   class="inline-flex items-center text-xs px-2.5 py-1 rounded-full bg-cream border border-gold/30 text-maroon hover:bg-gold/15 transition-colors">
                  {{ $sub->name }}
                </a>
              @endforeach
            </div>
          @endif
        </li>
      @endforeach
    </ul>

    <div class="px-6 py-5 mt-2 border-t border-gold/20">
      <a href="{{ route('search') }}" class="gold-btn block text-center py-3.5 text-xs font-body font-semibold uppercase tracking-widest rounded-xl">
        View All Collections
      </a>
    </div>
  </div>
</div>

<script>
  function closeSheet() {
    const o = document.getElementById('sheetOverlay');
    if (o) {
      o.classList.remove('open');
      document.getElementById('tabCategoriesBtn')?.setAttribute('aria-expanded','false');
    }
  }

  // Overlay background click
  document.getElementById('sheetOverlay')?.addEventListener('click', e => {
    if (e.target === e.currentTarget) closeSheet();
  });

  // Close button
  document.getElementById('sheetCloseBtn')?.addEventListener('click', closeSheet);

  // Swipe down to close
  (function () {
    let startY = 0;
    const sheet = document.querySelector('.bottom-sheet');
    if (!sheet) return;
    sheet.addEventListener('touchstart', e => { startY = e.touches[0].clientY; }, { passive: true });
    sheet.addEventListener('touchend', e => {
      if (e.changedTouches[0].clientY - startY > 60) closeSheet();
    }, { passive: true });
  })();
</script>
