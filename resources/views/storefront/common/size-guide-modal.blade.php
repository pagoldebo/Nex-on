{{--
    ======================================================================
    STOREFRONT COMMON COMPONENT: CATEGORY-LINKED SIZE & FIT GUIDE MODAL
    ======================================================================
    File: resources/views/storefront/common/size-guide-modal.blade.php
    Option 2: Tabbed Modal linked by Product Category
    Features:
      - Automatically detects product category (Paijama/Pants, Sarees/Blouses, Shirts/Tops, Intimates, Packs/Net Weight)
      - Pre-selects the linked category tab on open
      - Seamless unit toggle: Inches (in) <-> Centimeters (cm)
      - Universal support for all storefront themes (Saaya, Organic, Buttercup, Lumiere, Vyra)
--}}
@php
    $catSlug = strtolower($product->category?->slug ?? '');
    $catName = strtolower($product->category?->name ?? '');
    $parentSlug = strtolower($product->category?->parent?->slug ?? '');
    $parentName = strtolower($product->category?->parent?->name ?? '');
    $prodName = strtolower($product->name ?? '');
    $combinedCat = "{$catSlug} {$catName} {$parentSlug} {$parentName} {$prodName}";

    if (str_contains($combinedCat, 'pant') || str_contains($combinedCat, 'pajama') || str_contains($combinedCat, 'paijama') || str_contains($combinedCat, 'trouser') || str_contains($combinedCat, 'chino') || str_contains($combinedCat, 'jeans') || str_contains($combinedCat, 'dhuti') || str_contains($combinedCat, 'bottom')) {
        $defaultTab = 'bottoms';
        $categoryLabel = 'Paijama & Bottoms';
    } elseif (str_contains($combinedCat, 'saree') || str_contains($combinedCat, 'blouse') || str_contains($combinedCat, 'ethnic') || str_contains($combinedCat, 'drape') || str_contains($combinedCat, 'pallu')) {
        $defaultTab = 'saree_blouse';
        $categoryLabel = 'Sarees & Blouses';
    } elseif (str_contains($combinedCat, 'shirt') || str_contains($combinedCat, 'kurta') || str_contains($combinedCat, 'top') || str_contains($combinedCat, 'tee') || str_contains($combinedCat, 't-shirt')) {
        $defaultTab = 'tops';
        $categoryLabel = 'Shirts & Tops';
    } elseif (str_contains($combinedCat, 'bra') || str_contains($combinedCat, 'pantie') || str_contains($combinedCat, 'lingerie') || str_contains($combinedCat, 'brief') || str_contains($combinedCat, 'trunk') || str_contains($combinedCat, 'bralette')) {
        $defaultTab = 'intimates';
        $categoryLabel = 'Intimates & Innerwear';
    } elseif (isset($theme) && $theme === 'organic' || str_contains($combinedCat, 'pack') || str_contains($combinedCat, 'weight') || str_contains($combinedCat, 'organic') || str_contains($combinedCat, 'grocery') || str_contains($combinedCat, 'food')) {
        $defaultTab = 'packs';
        $categoryLabel = 'Packs & Net Weight';
    } else {
        $defaultTab = 'bottoms';
        $categoryLabel = $product->category?->name ?? 'Apparel';
    }
@endphp

<div id="universalSizeGuideModal"
     class="hidden fixed inset-0 z-[100] bg-black/60 backdrop-blur-sm items-center justify-center p-3 sm:p-6 transition-all duration-300"
     role="dialog"
     aria-modal="true"
     aria-labelledby="usgModalTitle"
     data-default-tab="{{ $defaultTab }}">
  
  <div class="bg-white text-slate-800 rounded-3xl max-w-3xl w-full p-5 sm:p-8 relative shadow-2xl max-h-[90vh] flex flex-col overflow-hidden border border-slate-200 animate-in fade-in zoom-in-95 duration-200">
    
    {{-- Header --}}
    <div class="flex items-start justify-between pb-4 border-b border-slate-100 shrink-0">
      <div class="pr-6">
        <div class="flex items-center gap-2 flex-wrap">
          <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-indigo-50 text-indigo-700 border border-indigo-200/60">
            <span class="w-1.5 h-1.5 rounded-full bg-indigo-600 animate-pulse"></span>
            Linked Category: {{ $product->category?->name ?? $categoryLabel }}
          </span>
          <span class="text-xs text-slate-400">· Fits all {{ $product->name }} variants</span>
        </div>
        <h2 id="usgModalTitle" class="text-xl sm:text-2xl font-serif font-bold text-slate-900 mt-1">
          Precision Size &amp; Measurement Guide
        </h2>
      </div>

      {{-- Close Button --}}
      <button type="button" 
              onclick="closeUniversalSizeGuide()"
              class="w-9 h-9 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 transition flex items-center justify-center shrink-0 cursor-pointer focus:outline-none"
              aria-label="Close size guide">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>

    {{-- Controls Row: Tab Navigation & Unit Switcher --}}
    <div class="py-3 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 border-b border-slate-100 shrink-0">
      
      {{-- Tabs --}}
      <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0 scrollbar-none" id="usgTabHeaders">
        <button type="button" 
                onclick="switchUsgTab('bottoms')"
                data-tab-target="bottoms"
                class="usg-tab-btn px-3 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition cursor-pointer">
          🩳 Paijama &amp; Pants
        </button>
        <button type="button" 
                onclick="switchUsgTab('saree_blouse')"
                data-tab-target="saree_blouse"
                class="usg-tab-btn px-3 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition cursor-pointer">
          🥻 Sarees &amp; Blouses
        </button>
        <button type="button" 
                onclick="switchUsgTab('tops')"
                data-tab-target="tops"
                class="usg-tab-btn px-3 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition cursor-pointer">
          👔 Shirts &amp; Tops
        </button>
        <button type="button" 
                onclick="switchUsgTab('intimates')"
                data-tab-target="intimates"
                class="usg-tab-btn px-3 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition cursor-pointer">
          👙 Intimates
        </button>
        <button type="button" 
                onclick="switchUsgTab('packs')"
                data-tab-target="packs"
                class="usg-tab-btn px-3 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition cursor-pointer">
          📦 Pack / Net Weight
        </button>
        <button type="button" 
                onclick="switchUsgTab('measuring')"
                data-tab-target="measuring"
                class="usg-tab-btn px-3 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition cursor-pointer">
          📏 How to Measure
        </button>
      </div>

      {{-- Unit Switcher (Inches / Centimeters) --}}
      <div class="inline-flex items-center self-end sm:self-auto bg-slate-100 p-1 rounded-xl shrink-0">
        <button type="button" 
                id="usgUnitInBtn"
                onclick="setUsgUnit('in')"
                class="px-2.5 py-1 text-[11px] font-bold rounded-lg transition bg-white text-slate-900 shadow-xs cursor-pointer">
          Inches (in)
        </button>
        <button type="button" 
                id="usgUnitCmBtn"
                onclick="setUsgUnit('cm')"
                class="px-2.5 py-1 text-[11px] font-bold rounded-lg transition text-slate-500 hover:text-slate-800 cursor-pointer">
          Centimeters (cm)
        </button>
      </div>
    </div>

    {{-- Scrollable Tab Contents --}}
    <div class="overflow-y-auto py-4 space-y-6 flex-1 pr-1" id="usgTabContents">
      
      {{-- ───────────────────────────────────────────────────────────
           TAB 1: PAIJAMA & BOTTOMS (Pants, Trousers, Loungewear)
           ─────────────────────────────────────────────────────────── --}}
      <div id="usgTab-bottoms" class="usg-panel hidden space-y-4">
        <div class="flex items-center justify-between">
          <p class="text-xs text-slate-500">
            Tailored for relax-fit pajamas, loungewear trousers, and casual bottom wear with drawstring or elastic comfort.
          </p>
        </div>

        <div class="overflow-x-auto border border-slate-200 rounded-2xl">
          <table class="w-full text-left text-xs border-collapse">
            <thead class="bg-slate-50 font-bold uppercase tracking-wider text-slate-600 text-[10px] border-b border-slate-200">
              <tr>
                <th class="p-3">Size</th>
                <th class="p-3">Waist (<span class="unit-label">in</span>)</th>
                <th class="p-3">Hips (<span class="unit-label">in</span>)</th>
                <th class="p-3">Length / Outseam (<span class="unit-label">in</span>)</th>
                <th class="p-3">Standard Fit Equivalent</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 font-mono text-slate-700">
              <tr class="hover:bg-slate-50/70">
                <td class="p-3 font-sans font-bold text-slate-900">S (Small)</td>
                <td class="p-3"><span class="val-in">28 – 30"</span><span class="val-cm hidden">71 – 76 cm</span></td>
                <td class="p-3"><span class="val-in">36 – 38"</span><span class="val-cm hidden">91 – 96 cm</span></td>
                <td class="p-3"><span class="val-in">39.5"</span><span class="val-cm hidden">100 cm</span></td>
                <td class="p-3 font-sans text-slate-500">UK 28–30 · Relaxed Slim</td>
              </tr>
              <tr class="hover:bg-slate-50/70 bg-indigo-50/30">
                <td class="p-3 font-sans font-bold text-slate-900">M (Medium)</td>
                <td class="p-3"><span class="val-in">31 – 33"</span><span class="val-cm hidden">78 – 84 cm</span></td>
                <td class="p-3"><span class="val-in">39 – 41"</span><span class="val-cm hidden">99 – 104 cm</span></td>
                <td class="p-3"><span class="val-in">40.5"</span><span class="val-cm hidden">103 cm</span></td>
                <td class="p-3 font-sans text-slate-500">UK 32–34 · Standard Comfort</td>
              </tr>
              <tr class="hover:bg-slate-50/70">
                <td class="p-3 font-sans font-bold text-slate-900">L (Large)</td>
                <td class="p-3"><span class="val-in">34 – 36"</span><span class="val-cm hidden">86 – 91 cm</span></td>
                <td class="p-3"><span class="val-in">42 – 44"</span><span class="val-cm hidden">106 – 112 cm</span></td>
                <td class="p-3"><span class="val-in">41.5"</span><span class="val-cm hidden">105 cm</span></td>
                <td class="p-3 font-sans text-slate-500">UK 36–38 · Roomy Fit</td>
              </tr>
              <tr class="hover:bg-slate-50/70">
                <td class="p-3 font-sans font-bold text-slate-900">XL (Extra Large)</td>
                <td class="p-3"><span class="val-in">37 – 39"</span><span class="val-cm hidden">94 – 99 cm</span></td>
                <td class="p-3"><span class="val-in">45 – 47"</span><span class="val-cm hidden">114 – 119 cm</span></td>
                <td class="p-3"><span class="val-in">42.0"</span><span class="val-cm hidden">107 cm</span></td>
                <td class="p-3 font-sans text-slate-500">UK 40–42 · Relaxed Lounge</td>
              </tr>
              <tr class="hover:bg-slate-50/70">
                <td class="p-3 font-sans font-bold text-slate-900">XXL</td>
                <td class="p-3"><span class="val-in">40 – 43"</span><span class="val-cm hidden">101 – 109 cm</span></td>
                <td class="p-3"><span class="val-in">48 – 51"</span><span class="val-cm hidden">122 – 129 cm</span></td>
                <td class="p-3"><span class="val-in">42.5"</span><span class="val-cm hidden">108 cm</span></td>
                <td class="p-3 font-sans text-slate-500">UK 44–46 · Maximum Ease</td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="p-4 rounded-2xl bg-amber-50/70 border border-amber-200/60 text-xs text-amber-900 flex items-start gap-3">
          <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          <div>
            <strong class="font-bold">Comfort Waistband Tip:</strong> Pajama bottoms and trousers feature woven drawstrings and gentle elastic waistband granting ±2 inches flexible leeway. If you fall between sizes, select the larger size for maximum sleeping and lounging ease.
          </div>
        </div>
      </div>

      {{-- ───────────────────────────────────────────────────────────
           TAB 2: SAREES & BLOUSES (Handwoven Sarees, Drapes & Tops)
           ─────────────────────────────────────────────────────────── --}}
      <div id="usgTab-saree_blouse" class="usg-panel hidden space-y-6">
        
        {{-- Saree Standard Dimensions --}}
        <div class="p-4 rounded-2xl bg-rose-50/60 border border-rose-200/60 space-y-2">
          <div class="flex items-center gap-2">
            <span class="text-rose-700 font-bold text-xs uppercase tracking-wider">Heritage Saree Dimensions</span>
            <span class="text-[10px] bg-rose-100 text-rose-800 px-2 py-0.5 rounded-full font-bold">Universal Free Size</span>
          </div>
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs text-slate-700 pt-1">
            <div class="bg-white p-3 rounded-xl border border-rose-100">
              <span class="block text-slate-400 text-[10px] uppercase font-bold">Saree Length</span>
              <span class="text-sm font-bold text-slate-900 font-mono">5.5 Meters</span> (~6.0 Yards)
            </div>
            <div class="bg-white p-3 rounded-xl border border-rose-100">
              <span class="block text-slate-400 text-[10px] uppercase font-bold">Saree Width</span>
              <span class="text-sm font-bold text-slate-900 font-mono">45 – 48 Inches</span> (1.15m)
            </div>
            <div class="bg-white p-3 rounded-xl border border-rose-100">
              <span class="block text-slate-400 text-[10px] uppercase font-bold">Blouse Piece</span>
              <span class="text-sm font-bold text-slate-900 font-mono">0.8 – 1.0 Meter</span> (Included or Unstitched)
            </div>
          </div>
          <p class="text-[11px] text-slate-500 italic pt-1">
            Traditional sarees naturally fit every height and waist through custom pleating and drape adjustments.
          </p>
        </div>

        {{-- Blouse Size Chart --}}
        <div class="space-y-3">
          <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700">Stitched Blouse Sizing Matrix</h4>
          <div class="overflow-x-auto border border-slate-200 rounded-2xl">
            <table class="w-full text-left text-xs border-collapse">
              <thead class="bg-slate-50 font-bold uppercase tracking-wider text-slate-600 text-[10px] border-b border-slate-200">
                <tr>
                  <th class="p-3">Blouse Size</th>
                  <th class="p-3">Bust Circumference</th>
                  <th class="p-3">Underbust / Ribcage</th>
                  <th class="p-3">Blouse Length</th>
                  <th class="p-3">Armhole</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 font-mono text-slate-700">
                <tr class="hover:bg-slate-50/70">
                  <td class="p-3 font-sans font-bold text-slate-900">XS (Size 32)</td>
                  <td class="p-3"><span class="val-in">31 – 32"</span><span class="val-cm hidden">79 – 81 cm</span></td>
                  <td class="p-3"><span class="val-in">26 – 28"</span><span class="val-cm hidden">66 – 71 cm</span></td>
                  <td class="p-3"><span class="val-in">13.5"</span><span class="val-cm hidden">34 cm</span></td>
                  <td class="p-3"><span class="val-in">14.5"</span><span class="val-cm hidden">37 cm</span></td>
                </tr>
                <tr class="hover:bg-slate-50/70 bg-indigo-50/30">
                  <td class="p-3 font-sans font-bold text-slate-900">S (Size 34)</td>
                  <td class="p-3"><span class="val-in">33 – 34"</span><span class="val-cm hidden">84 – 86 cm</span></td>
                  <td class="p-3"><span class="val-in">28 – 30"</span><span class="val-cm hidden">71 – 76 cm</span></td>
                  <td class="p-3"><span class="val-in">14.0"</span><span class="val-cm hidden">35 cm</span></td>
                  <td class="p-3"><span class="val-in">15.0"</span><span class="val-cm hidden">38 cm</span></td>
                </tr>
                <tr class="hover:bg-slate-50/70">
                  <td class="p-3 font-sans font-bold text-slate-900">M (Size 36)</td>
                  <td class="p-3"><span class="val-in">35 – 36"</span><span class="val-cm hidden">89 – 91 cm</span></td>
                  <td class="p-3"><span class="val-in">30 – 32"</span><span class="val-cm hidden">76 – 81 cm</span></td>
                  <td class="p-3"><span class="val-in">14.5"</span><span class="val-cm hidden">37 cm</span></td>
                  <td class="p-3"><span class="val-in">15.5"</span><span class="val-cm hidden">39 cm</span></td>
                </tr>
                <tr class="hover:bg-slate-50/70">
                  <td class="p-3 font-sans font-bold text-slate-900">L (Size 38)</td>
                  <td class="p-3"><span class="val-in">37 – 38"</span><span class="val-cm hidden">94 – 96 cm</span></td>
                  <td class="p-3"><span class="val-in">32 – 34"</span><span class="val-cm hidden">81 – 86 cm</span></td>
                  <td class="p-3"><span class="val-in">15.0"</span><span class="val-cm hidden">38 cm</span></td>
                  <td class="p-3"><span class="val-in">16.5"</span><span class="val-cm hidden">42 cm</span></td>
                </tr>
                <tr class="hover:bg-slate-50/70">
                  <td class="p-3 font-sans font-bold text-slate-900">XL (Size 40)</td>
                  <td class="p-3"><span class="val-in">39 – 41"</span><span class="val-cm hidden">99 – 104 cm</span></td>
                  <td class="p-3"><span class="val-in">34 – 36"</span><span class="val-cm hidden">86 – 91 cm</span></td>
                  <td class="p-3"><span class="val-in">15.5"</span><span class="val-cm hidden">39 cm</span></td>
                  <td class="p-3"><span class="val-in">17.5"</span><span class="val-cm hidden">44 cm</span></td>
                </tr>
                <tr class="hover:bg-slate-50/70">
                  <td class="p-3 font-sans font-bold text-slate-900">XXL (Size 42)</td>
                  <td class="p-3"><span class="val-in">42 – 44"</span><span class="val-cm hidden">107 – 112 cm</span></td>
                  <td class="p-3"><span class="val-in">37 – 39"</span><span class="val-cm hidden">94 – 99 cm</span></td>
                  <td class="p-3"><span class="val-in">16.0"</span><span class="val-cm hidden">41 cm</span></td>
                  <td class="p-3"><span class="val-in">18.5"</span><span class="val-cm hidden">47 cm</span></td>
                </tr>
              </tbody>
            </table>
          </div>
          <p class="text-[11px] text-slate-500">
            * All stitched blouses incorporate 2 inches of internal seam margin on both sides for effortless tailoring and custom release.
          </p>
        </div>
      </div>

      {{-- ───────────────────────────────────────────────────────────
           TAB 3: SHIRTS & TOPS (Men & Women Tops, Kurtas, Tees)
           ─────────────────────────────────────────────────────────── --}}
      <div id="usgTab-tops" class="usg-panel hidden space-y-4">
        <p class="text-xs text-slate-500">
          Applicable for Men &amp; Women Formal Shirts, Casual Linen Kurtas, and Everyday Cotton Tees.
        </p>

        <div class="overflow-x-auto border border-slate-200 rounded-2xl">
          <table class="w-full text-left text-xs border-collapse">
            <thead class="bg-slate-50 font-bold uppercase tracking-wider text-slate-600 text-[10px] border-b border-slate-200">
              <tr>
                <th class="p-3">Size</th>
                <th class="p-3">Chest Circumference</th>
                <th class="p-3">Shoulder Width</th>
                <th class="p-3">Garment Length</th>
                <th class="p-3">Neck / Collar</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 font-mono text-slate-700">
              <tr class="hover:bg-slate-50/70">
                <td class="p-3 font-sans font-bold text-slate-900">S (38)</td>
                <td class="p-3"><span class="val-in">38 – 39"</span><span class="val-cm hidden">96 – 99 cm</span></td>
                <td class="p-3"><span class="val-in">17.5"</span><span class="val-cm hidden">44 cm</span></td>
                <td class="p-3"><span class="val-in">28.5"</span><span class="val-cm hidden">72 cm</span></td>
                <td class="p-3"><span class="val-in">15.0"</span><span class="val-cm hidden">38 cm</span></td>
              </tr>
              <tr class="hover:bg-slate-50/70 bg-indigo-50/30">
                <td class="p-3 font-sans font-bold text-slate-900">M (40)</td>
                <td class="p-3"><span class="val-in">40 – 41"</span><span class="val-cm hidden">101 – 104 cm</span></td>
                <td class="p-3"><span class="val-in">18.2"</span><span class="val-cm hidden">46 cm</span></td>
                <td class="p-3"><span class="val-in">29.5"</span><span class="val-cm hidden">75 cm</span></td>
                <td class="p-3"><span class="val-in">15.5"</span><span class="val-cm hidden">39 cm</span></td>
              </tr>
              <tr class="hover:bg-slate-50/70">
                <td class="p-3 font-sans font-bold text-slate-900">L (42)</td>
                <td class="p-3"><span class="val-in">42 – 43"</span><span class="val-cm hidden">106 – 109 cm</span></td>
                <td class="p-3"><span class="val-in">19.0"</span><span class="val-cm hidden">48 cm</span></td>
                <td class="p-3"><span class="val-in">30.0"</span><span class="val-cm hidden">76 cm</span></td>
                <td class="p-3"><span class="val-in">16.0"</span><span class="val-cm hidden">41 cm</span></td>
              </tr>
              <tr class="hover:bg-slate-50/70">
                <td class="p-3 font-sans font-bold text-slate-900">XL (44)</td>
                <td class="p-3"><span class="val-in">44 – 46"</span><span class="val-cm hidden">112 – 117 cm</span></td>
                <td class="p-3"><span class="val-in">19.8"</span><span class="val-cm hidden">50 cm</span></td>
                <td class="p-3"><span class="val-in">30.5"</span><span class="val-cm hidden">77 cm</span></td>
                <td class="p-3"><span class="val-in">16.5"</span><span class="val-cm hidden">42 cm</span></td>
              </tr>
              <tr class="hover:bg-slate-50/70">
                <td class="p-3 font-sans font-bold text-slate-900">XXL (46)</td>
                <td class="p-3"><span class="val-in">47 – 49"</span><span class="val-cm hidden">119 – 124 cm</span></td>
                <td class="p-3"><span class="val-in">20.5"</span><span class="val-cm hidden">52 cm</span></td>
                <td class="p-3"><span class="val-in">31.0"</span><span class="val-cm hidden">79 cm</span></td>
                <td class="p-3"><span class="val-in">17.0"</span><span class="val-cm hidden">43 cm</span></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      {{-- ───────────────────────────────────────────────────────────
           TAB 4: INTIMATES & INNERWEAR (Bras, Briefs, Panties)
           ─────────────────────────────────────────────────────────── --}}
      <div id="usgTab-intimates" class="usg-panel hidden space-y-4">
        <p class="text-xs text-slate-500">
          Precision measurement guide for bras, bralettes, briefs, and contour intimates.
        </p>

        <div class="overflow-x-auto border border-slate-200 rounded-2xl">
          <table class="w-full text-left text-xs border-collapse">
            <thead class="bg-slate-50 font-bold uppercase tracking-wider text-slate-600 text-[10px] border-b border-slate-200">
              <tr>
                <th class="p-3">Band / Size</th>
                <th class="p-3">Underbust (Snug)</th>
                <th class="p-3">Bust Peak (Fullest)</th>
                <th class="p-3">Compatible Cups</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 font-mono text-slate-700">
              <tr class="hover:bg-slate-50/70">
                <td class="p-3 font-sans font-bold text-slate-900">32 (XS/S)</td>
                <td class="p-3"><span class="val-in">27 – 29"</span><span class="val-cm hidden">68 – 74 cm</span></td>
                <td class="p-3"><span class="val-in">31 – 33"</span><span class="val-cm hidden">79 – 84 cm</span></td>
                <td class="p-3 font-sans text-slate-500">32A · 32B · 32C</td>
              </tr>
              <tr class="hover:bg-slate-50/70 bg-indigo-50/30">
                <td class="p-3 font-sans font-bold text-slate-900">34 (S/M)</td>
                <td class="p-3"><span class="val-in">29 – 31"</span><span class="val-cm hidden">74 – 79 cm</span></td>
                <td class="p-3"><span class="val-in">33 – 35"</span><span class="val-cm hidden">84 – 89 cm</span></td>
                <td class="p-3 font-sans text-slate-500">34B · 34C · 34D</td>
              </tr>
              <tr class="hover:bg-slate-50/70">
                <td class="p-3 font-sans font-bold text-slate-900">36 (M/L)</td>
                <td class="p-3"><span class="val-in">31 – 33"</span><span class="val-cm hidden">79 – 84 cm</span></td>
                <td class="p-3"><span class="val-in">35 – 37"</span><span class="val-cm hidden">89 – 94 cm</span></td>
                <td class="p-3 font-sans text-slate-500">36B · 36C · 36D</td>
              </tr>
              <tr class="hover:bg-slate-50/70">
                <td class="p-3 font-sans font-bold text-slate-900">38 (L/XL)</td>
                <td class="p-3"><span class="val-in">33 – 35"</span><span class="val-cm hidden">84 – 89 cm</span></td>
                <td class="p-3"><span class="val-in">37 – 40"</span><span class="val-cm hidden">94 – 102 cm</span></td>
                <td class="p-3 font-sans text-slate-500">38C · 38D · 38DD</td>
              </tr>
              <tr class="hover:bg-slate-50/70">
                <td class="p-3 font-sans font-bold text-slate-900">40 (XL/XXL)</td>
                <td class="p-3"><span class="val-in">35 – 37"</span><span class="val-cm hidden">89 – 94 cm</span></td>
                <td class="p-3"><span class="val-in">40 – 43"</span><span class="val-cm hidden">102 – 109 cm</span></td>
                <td class="p-3 font-sans text-slate-500">40C · 40D · 40E</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      {{-- ───────────────────────────────────────────────────────────
           TAB 5: PACK & NET WEIGHT (Organic, Pantry & Quantities)
           ─────────────────────────────────────────────────────────── --}}
      <div id="usgTab-packs" class="usg-panel hidden space-y-4">
        <p class="text-xs text-slate-500">
          Detailed net weight specifications, pack sizes, volume metrics, and recommended usage.
        </p>

        <div class="overflow-x-auto border border-slate-200 rounded-2xl">
          <table class="w-full text-left text-xs border-collapse">
            <thead class="bg-slate-50 font-bold uppercase tracking-wider text-slate-600 text-[10px] border-b border-slate-200">
              <tr>
                <th class="p-3">Pack / Variant</th>
                <th class="p-3">Net Weight</th>
                <th class="p-3">Approximate Servings</th>
                <th class="p-3">Packaging Type</th>
                <th class="p-3">Recommended For</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 font-mono text-slate-700">
              <tr class="hover:bg-slate-50/70">
                <td class="p-3 font-sans font-bold text-slate-900">Trial / Sampler</td>
                <td class="p-3">100g – 250g</td>
                <td class="p-3">4 – 8 Servings</td>
                <td class="p-3 font-sans">Aroma-seal zip pouch</td>
                <td class="p-3 font-sans text-slate-500">First-time tasting &amp; gifting</td>
              </tr>
              <tr class="hover:bg-slate-50/70 bg-indigo-50/30">
                <td class="p-3 font-sans font-bold text-slate-900">Standard Pack</td>
                <td class="p-3">500g (0.5 kg)</td>
                <td class="p-3">15 – 20 Servings</td>
                <td class="p-3 font-sans">Resealable standing barrier pouch</td>
                <td class="p-3 font-sans text-slate-500">Monthly household staple</td>
              </tr>
              <tr class="hover:bg-slate-50/70">
                <td class="p-3 font-sans font-bold text-slate-900">Family Value Pack</td>
                <td class="p-3">1,000g (1.0 kg)</td>
                <td class="p-3">35 – 40 Servings</td>
                <td class="p-3 font-sans">Heavy-gauge food-grade foil pouch</td>
                <td class="p-3 font-sans text-slate-500">Daily kitchen usage, best value</td>
              </tr>
              <tr class="hover:bg-slate-50/70">
                <td class="p-3 font-sans font-bold text-slate-900">Bulk Duo (Pack of 2)</td>
                <td class="p-3">2 × 1.0 kg (2.0 kg)</td>
                <td class="p-3">70 – 80 Servings</td>
                <td class="p-3 font-sans">Corrugated master carton</td>
                <td class="p-3 font-sans text-slate-500">Long-term pantry reserve &amp; savings</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      {{-- ───────────────────────────────────────────────────────────
           TAB 6: HOW TO MEASURE GUIDE
           ─────────────────────────────────────────────────────────── --}}
      <div id="usgTab-measuring" class="usg-panel hidden space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs text-slate-700">
          
          <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-1.5">
            <div class="flex items-center gap-2">
              <span class="w-6 h-6 rounded-full bg-slate-900 text-white font-bold text-xs flex items-center justify-center">1</span>
              <h5 class="font-bold text-slate-900 uppercase tracking-wider text-[11px]">Waist Measurement</h5>
            </div>
            <p class="text-slate-500 leading-relaxed">
              Wrap a flexible tape horizontally around your natural waistline (narrowest part above your hip bone, usually 1 inch above navel). Keep one finger between the tape and your body for natural breathing ease.
            </p>
          </div>

          <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-1.5">
            <div class="flex items-center gap-2">
              <span class="w-6 h-6 rounded-full bg-slate-900 text-white font-bold text-xs flex items-center justify-center">2</span>
              <h5 class="font-bold text-slate-900 uppercase tracking-wider text-[11px]">Hip Measurement</h5>
            </div>
            <p class="text-slate-500 leading-relaxed">
              Stand with your heels together and wrap the tape around the fullest point of your hips and buttocks. Ensure the measuring tape remains level and parallel to the floor all the way around.
            </p>
          </div>

          <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-1.5">
            <div class="flex items-center gap-2">
              <span class="w-6 h-6 rounded-full bg-slate-900 text-white font-bold text-xs flex items-center justify-center">3</span>
              <h5 class="font-bold text-slate-900 uppercase tracking-wider text-[11px]">Bust / Chest Measurement</h5>
            </div>
            <p class="text-slate-500 leading-relaxed">
              For blouses, shirts, and tops, hold the tape comfortably loose and measure horizontally across the fullest line of your chest/bust with arms resting relaxed at your sides.
            </p>
          </div>

          <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-1.5">
            <div class="flex items-center gap-2">
              <span class="w-6 h-6 rounded-full bg-slate-900 text-white font-bold text-xs flex items-center justify-center">4</span>
              <h5 class="font-bold text-slate-900 uppercase tracking-wider text-[11px]">Outseam / Length</h5>
            </div>
            <p class="text-slate-500 leading-relaxed">
              Measure along the outer seam from the top of the waistband down to the ankle bone or desired hem break.
            </p>
          </div>
        </div>
      </div>

    </div>

    {{-- Footer --}}
    <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500 shrink-0">
      <div class="flex items-center gap-2">
        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span>15-Day Hassle-Free Size Exchange Guaranteed</span>
      </div>
      <div class="flex items-center gap-4">
        <a href="{{ route('sizeGuide') }}" target="_blank" class="font-bold text-indigo-600 hover:text-indigo-800 underline transition">
          Open Full Interactive Calculator ↗
        </a>
      </div>
    </div>

  </div>
</div>

{{-- Universal Size Guide Script --}}
<script>
  let currentUsgUnit = 'in';

  function openUniversalSizeGuide(targetTab = null) {
    const modal = document.getElementById('universalSizeGuideModal');
    if (!modal) return;
    
    // Choose tab: requested target, or default attribute from category detection
    const initialTab = targetTab || modal.dataset.defaultTab || 'bottoms';
    switchUsgTab(initialTab);
    
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';
  }

  function closeUniversalSizeGuide() {
    const modal = document.getElementById('universalSizeGuideModal');
    if (!modal) return;
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = '';
  }

  function switchUsgTab(tabId) {
    // Hide all tab panels
    document.querySelectorAll('.usg-panel').forEach(panel => {
      panel.classList.add('hidden');
    });

    // Show target panel
    const targetPanel = document.getElementById('usgTab-' + tabId);
    if (targetPanel) {
      targetPanel.classList.remove('hidden');
    }

    // Update active tab buttons
    document.querySelectorAll('.usg-tab-btn').forEach(btn => {
      const isTarget = btn.dataset.tabTarget === tabId;
      if (isTarget) {
        btn.className = 'usg-tab-btn px-3 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition cursor-pointer bg-slate-900 text-white shadow-xs';
      } else {
        btn.className = 'usg-tab-btn px-3 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition cursor-pointer bg-slate-100 text-slate-600 hover:bg-slate-200 hover:text-slate-900';
      }
    });
  }

  function setUsgUnit(unit) {
    currentUsgUnit = unit;
    const inBtn = document.getElementById('usgUnitInBtn');
    const cmBtn = document.getElementById('usgUnitCmBtn');

    if (unit === 'in') {
      inBtn.className = 'px-2.5 py-1 text-[11px] font-bold rounded-lg transition bg-white text-slate-900 shadow-xs cursor-pointer';
      cmBtn.className = 'px-2.5 py-1 text-[11px] font-bold rounded-lg transition text-slate-500 hover:text-slate-800 cursor-pointer';
      document.querySelectorAll('.val-in').forEach(el => el.classList.remove('hidden'));
      document.querySelectorAll('.val-cm').forEach(el => el.classList.add('hidden'));
      document.querySelectorAll('.unit-label').forEach(el => el.textContent = 'in');
    } else {
      cmBtn.className = 'px-2.5 py-1 text-[11px] font-bold rounded-lg transition bg-white text-slate-900 shadow-xs cursor-pointer';
      inBtn.className = 'px-2.5 py-1 text-[11px] font-bold rounded-lg transition text-slate-500 hover:text-slate-800 cursor-pointer';
      document.querySelectorAll('.val-cm').forEach(el => el.classList.remove('hidden'));
      document.querySelectorAll('.val-in').forEach(el => el.classList.add('hidden'));
      document.querySelectorAll('.unit-label').forEach(el => el.textContent = 'cm');
    }
  }

  // Bind trigger buttons across all pages and themes
  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.pdp-size-guide-trigger, #pdpSizeGuide').forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        const tab = btn.dataset.sizeTab || null;
        openUniversalSizeGuide(tab);
      });
    });

    const modal = document.getElementById('universalSizeGuideModal');
    if (modal) {
      modal.addEventListener('click', (e) => {
        if (e.target === modal) closeUniversalSizeGuide();
      });
    }

    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') closeUniversalSizeGuide();
    });
  });
</script>

