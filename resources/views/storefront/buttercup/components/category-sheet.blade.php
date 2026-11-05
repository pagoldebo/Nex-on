{{-- resources/views/storefront/buttercup/components/category-sheet.blade.php --}}
<div id="sheetOverlay" class="lg:hidden fixed inset-0 z-[70] transition-all" style="background:rgba(0,0,0,0);visibility:hidden;pointer-events:none;"
     onclick="if(event.target===this) closeCategorySheet()">
    <div class="bottom-sheet absolute bottom-0 left-0 right-0 rounded-t-[32px] max-h-[80vh] overflow-y-auto pb-8 translate-y-full transition-transform duration-400"
         style="background:#FFFAF5;">
        <div class="w-10 h-1 rounded-full mx-auto mt-3 mb-2" style="background:#D89AA0"></div>
        <div class="px-6 pt-2 pb-4">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-xl font-semibold" style="color:#4A3B3A">Shop by Category</h3>
                <button onclick="closeCategorySheet()" style="color:#4A3B3A" aria-label="Close">&times;</button>
            </div>
            <div class="space-y-3">
                @foreach ($categories ?? [] as $cat)
                    <div class="border-b pb-2" style="border-color:#D89AA022;">
                        <div class="flex items-center justify-between py-2">
                            <a href="{{ route('category.show', $cat->slug) }}" onclick="closeCategorySheet()"
                               class="font-medium text-sm hover:underline flex items-center gap-2.5" style="color:#4A3B3A">
                                @if($cat->image_url)
                                    <img src="{{ $cat->image_url }}" alt="{{ $cat->name }}" class="w-7 h-7 rounded-full object-cover border border-[#D89AA0]/30 shrink-0">
                                @endif
                                <span>{{ $cat->name }}</span>
                            </a>
                            <a href="{{ route('category.show', $cat->slug) }}" onclick="closeCategorySheet()" style="color:#D89AA0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/></svg>
                            </a>
                        </div>
                        @if($cat->children && $cat->children->count() > 0)
                            <div class="flex flex-wrap gap-1.5 pl-2 pt-1 pb-1">
                                @foreach($cat->children as $sub)
                                    <a href="{{ route('category.show', $sub->slug) }}" onclick="closeCategorySheet()"
                                       class="inline-flex items-center text-xs px-2.5 py-1 rounded-full bg-white text-[#4A3B3A] border border-[#D89AA0]/30 hover:border-[#B97680] transition-colors">
                                        {{ $sub->name }}
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
            <a href="{{ route('search') }}" onclick="closeCategorySheet()" class="block text-center mt-6 py-3 rounded-2xl text-xs font-bold uppercase tracking-widest bg-[#D89AA0] text-[#FFFAF5]">View All Products</a>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const overlay = document.getElementById('sheetOverlay');
        window.openCategorySheet = function() {
            overlay.style.background = 'rgba(0,0,0,0.5)';
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
