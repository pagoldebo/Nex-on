{{--
    ======================================================================
    THEME: BUTTERCUP — Pastel Comfort Intimates & Loungewear
    PARTIAL: PRODUCT QUICK-VIEW MODAL
    ======================================================================
    File: resources/views/storefront/buttercup/partials/quick-view.blade.php
    Responsibility: Interactive comfort modal popup for rapid product inspection and cart add
    Features:
      - Warm pastel cream (#FAF6F0) canvas, rose blush (#B97680) accents & Playfair / Plus Jakarta typography
      - Live color swatch selector with image gallery switcher
      - Variant size selector with live inventory scarcity alerts
      - Direct reactive Add to Cart handler synced with storefront cart store
      - Full product details link redirecting to standalone product page
--}}
<div id="quickViewModal" style="display: none;" class="fixed inset-0 z-[220] bg-ink/60 backdrop-blur-sm items-end sm:items-center justify-center sm:p-4 transition-all duration-300">
    <div class="bg-[#FAF6F0] text-ink w-full sm:max-w-3xl rounded-t-[36px] sm:rounded-[32px] overflow-hidden shadow-2xl border border-rose/30 max-h-[92vh] overflow-y-auto relative animate-fade">
        <button type="button" data-qv-close="true" onclick="closeQuickView()" class="absolute top-4 right-5 z-20 w-9 h-9 rounded-full bg-cream/80 hover:bg-rose hover:text-cream text-ink/60 text-2xl font-light flex items-center justify-center transition-all cursor-pointer leading-none shadow-xs" aria-label="Close">&times;</button>
        
        <div class="grid sm:grid-cols-2">
            {{-- Left column: Image & Gallery --}}
            <div class="relative bg-blush-deep/40 p-4 flex flex-col justify-between border-b sm:border-b-0 sm:border-r border-rose/20">
                <div class="relative aspect-[4/5] rounded-2xl overflow-hidden bg-white/60 shadow-xs">
                    <img id="qvImage" src="" alt="" class="w-full h-full object-cover transition-transform duration-500">
                    <span id="qvBadge" class="absolute top-3 left-3 z-10 bg-sage text-cream text-[9px] font-bold px-2.5 py-1 rounded-full uppercase tracking-widest shadow-xs hidden">
                    </span>
                </div>
                {{-- Thumbnails strip --}}
                <div id="qvThumbnails" class="flex gap-2 mt-3 overflow-x-auto pb-1 empty:hidden"></div>
            </div>

            {{-- Right column: Product Details --}}
            <div class="p-6 sm:p-8 flex flex-col justify-between gap-4">
                <div>
                    <span id="qvCategory" class="text-[10px] uppercase tracking-wider font-bold text-sage-dark block mb-1"></span>
                    <h3 id="qvName" class="font-display text-2xl sm:text-3xl text-ink font-bold tracking-tight leading-tight"></h3>
                    <div id="qvDescription" class="text-xs text-ink/70 leading-relaxed mt-2 line-clamp-3 product-rich-text [&_p]:inline [&_p]:m-0"></div>
                </div>

                <div class="flex items-baseline gap-2 pt-2 border-t border-rose/20">
                    <span class="text-xs text-ink/50 uppercase tracking-wider font-semibold">Price:</span>
                    <p id="qvPrice" class="text-2xl font-display font-bold text-rose-dark">
                        <span id="qvPriceCurrent"></span>
                        <span id="qvPriceCompare" class="text-sm font-normal text-ink/30 line-through ml-2 hidden"></span>
                    </p>
                </div>

                {{-- Color Swatches --}}
                <div id="qvColorSection">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] uppercase tracking-wider font-bold text-ink/60">
                            Color: <span id="qvColorName" class="text-[#B97680] font-semibold"></span>
                        </span>
                    </div>
                    <div id="colorOptions" class="flex gap-2.5 flex-wrap"></div>
                </div>

                {{-- Size Options --}}
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] uppercase tracking-wider font-bold text-ink/60">Select Size</span>
                        <span id="qvStockWarning" class="text-[10px] font-bold text-rose-dark hidden font-mono"></span>
                    </div>
                    <div id="sizeOptions" class="flex gap-2 flex-wrap"></div>
                </div>

                {{-- Action Buttons --}}
                <div class="pt-2 space-y-2">
                    <button id="qvAddBtn" onclick="qvAddToBag()" disabled
                            class="w-full py-3.5 px-6 bg-[#B97680] hover:bg-[#A3626C] text-cream font-bold text-xs uppercase tracking-wider rounded-2xl transition-all duration-300 shadow-md disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        <span id="qvAddBtnText">SELECT A SIZE</span>
                    </button>

                    <div class="text-center pt-1 border-t border-rose/10">
                        <a id="qvViewDetails" href="#" class="inline-flex items-center gap-1.5 text-xs font-semibold text-ink/70 hover:text-[#B97680] transition-colors py-1">
                            <span>View Full Details &amp; Sizing Guide</span>
                            <span class="text-[#B97680]">→</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/js/quick-view.js"></script>
