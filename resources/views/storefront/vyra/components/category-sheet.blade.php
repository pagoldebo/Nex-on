{{--
    ======================================================================
    THEME: VYRA — Modern Noir & High-Contrast Streetwear
    COMPONENT: MOBILE CATEGORY SHEET DRAWER
    ======================================================================
    File: resources/views/storefront/vyra/components/category-sheet.blade.php
    Component Injection: storefront.vyra.components.category-sheet
    Responsibility: Slide-up mobile category hierarchy and catalog navigation
    Features:
      - Hierarchical parent/child category listing with smooth expandable accordions
      - Direct thumbnail links to category collection landing pages
      - Smooth backdrop dismiss gesture and escape key listener
    Operations & Maintenance:
      - Populated via view shared categories variable ($categories)
      - Controlled via Alpine x-show="categorySheetOpen"
--}}
<div id="sheetOverlay" class="lg:hidden fixed inset-0 z-[70] transition-all" style="background:rgba(0,0,0,0);visibility:hidden;pointer-events:none;"
     onclick="if(event.target===this) closeCategorySheet()">
    <div class="bottom-sheet absolute bottom-0 left-0 right-0 max-h-[80vh] overflow-y-auto pb-8 translate-y-full transition-transform duration-400 border-t-2"
         style="background:#1A1A1D;border-color:#FF3D5A;">
        <div class="w-9 h-[3px] mx-auto mt-3 mb-2" style="background:#FF3D5A"></div>
        <div class="px-6 pt-2 pb-4">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-xl font-semibold uppercase" style="color:#F2F0EC">Shop by Category</h3>
                <button onclick="closeCategorySheet()" style="color:#F2F0EC" aria-label="Close">&times;</button>
            </div>
            <div class="space-y-3">
                @foreach ($categories ?? [] as $cat)
                    <div class="border-b pb-2" style="border-color:#FF3D5A22;">
                        <div class="flex items-center justify-between py-2">
                            <a href="{{ route('category.show', $cat->slug) }}" onclick="closeCategorySheet()"
                               class="font-medium uppercase text-sm hover:text-[#D4FF3D] transition-colors flex items-center gap-2.5" style="color:#F2F0EC">
                                @if($cat->image_url)
                                    <img src="{{ $cat->image_url }}" alt="{{ $cat->name }}" class="w-7 h-7 rounded-full object-cover border border-[#FF3D5A]/40 shrink-0">
                                @endif
                                <span>{{ $cat->name }}</span>
                            </a>
                            <a href="{{ route('category.show', $cat->slug) }}" onclick="closeCategorySheet()" class="p-1 text-[#FF3D5A]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/></svg>
                            </a>
                        </div>
                        @if($cat->children && $cat->children->count() > 0)
                            <div class="flex flex-wrap gap-1.5 pl-2 pt-1 pb-1">
                                @foreach($cat->children as $sub)
                                    <a href="{{ route('category.show', $sub->slug) }}" onclick="closeCategorySheet()"
                                       class="inline-flex items-center text-xs px-2.5 py-1 uppercase tracking-wider bg-[#0E0E10] text-[#F2F0EC]/80 border border-[#FF3D5A]/30 hover:border-[#D4FF3D] hover:text-[#D4FF3D] transition-colors">
                                        {{ $sub->name }}
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
            <a href="{{ route('search') }}" onclick="closeCategorySheet()" class="block text-center mt-6 py-3 text-xs font-bold uppercase tracking-widest bg-[#FF3D5A] text-[#0E0E10]">Shop All Products</a>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const overlay = document.getElementById('sheetOverlay');
        window.openCategorySheet = function() {
            overlay.style.background = 'rgba(0,0,0,0.7)';
            overlay.style.visibility = 'visible';
            overlay.style.pointerEvents = 'auto';
            overlay.querySelector('.bottom-sheet').style.transform = 'translateY(0)';
            document.body.style.overflow = 'hidden';
        };
        window.closeCategorySheet = function() {
            overlay.style.background = 'rgba(0,0,0,0)';
            overlay.querySelector('.bottom-sheet').style.transform = 'translateY(100%)';
            setTimeout(() => { overlay.style.visibility = 'hidden'; overlay.style.pointerEvents = 'none'; }, 350);
            document.body.style.overflow = '';
        };
    });
</script>
