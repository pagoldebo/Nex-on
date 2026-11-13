{{--
    ======================================================================
    THEME: LUMIÈRE — French Silk Atelier & Minimalist Luxury
    PARTIAL: PRODUCT QUICK-VIEW MODAL
    ======================================================================
    File: resources/views/storefront/lumiere/partials/quick-view.blade.php
    Partial Injection: storefront.lumiere.partials.quick-view
    Responsibility: Interactive modal popup for rapid product inspection and cart add
    Features:
      - High-resolution image gallery with interactive thumbnail switcher
      - Variant option selectors (sizes, colors, finishes) with live stock feedback
      - Instant reactive Add to Bag button and quantity steppers
      - Full product details link redirecting to standalone product page
    Operations & Maintenance:
      - Loaded dynamically via client-side Alpine store or AJAX endpoint
--}}
<div id="quickViewModal" class="hidden fixed inset-0 z-[220] bg-black/50 backdrop-blur-sm items-end sm:items-center justify-center sm:p-4"
     onclick="if(event.target===this) closeQuickView()">
    <div class="bg-white w-full sm:max-w-3xl rounded-t-[32px] sm:rounded-[32px] overflow-hidden shadow-2xl max-h-[92vh] overflow-y-auto relative">
        <button onclick="closeQuickView()" class="absolute top-4 right-5 z-10 text-2xl font-light text-gray-400 hover:text-gray-700" aria-label="Close">&times;</button>
        <div class="grid sm:grid-cols-2">
            <div class="aspect-[4/5] sm:aspect-auto bg-pink-50">
                <img id="qvImage" src="" alt="" class="w-full h-full object-cover">
            </div>
            <div class="p-8 flex flex-col gap-4">
                <div>
                    <p id="qvCategory" class="text-[10px] uppercase tracking-widest font-bold text-pink-500 mb-1"></p>
                    <h3 id="qvName" class="font-display italic text-2xl text-gray-900"></h3>
                    <div id="qvDescription" class="text-xs text-gray-500 leading-relaxed mt-1.5 line-clamp-2 product-rich-text [&_p]:inline [&_p]:m-0"></div>
                </div>
                <p id="qvPrice" class="text-2xl font-bold text-pink-600">
                    <span id="qvPriceCurrent"></span>
                    <span id="qvPriceCompare" class="text-sm font-normal text-gray-300 line-through ml-2 hidden"></span>
                </p>
                <div>
                    <span class="text-[10px] uppercase tracking-widest font-bold text-gray-400 block mb-3">Shade: <span id="qvColorName" class="text-pink-500"></span></span>
                    <div id="colorOptions" class="flex gap-3 flex-wrap"></div>
                </div>
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[10px] uppercase tracking-widest font-bold text-gray-400">Size</span>
                        <span id="qvStockWarning" class="text-[10px] font-bold text-red-400 hidden"></span>
                    </div>
                    <div id="sizeOptions" class="flex gap-2 flex-wrap"></div>
                </div>
                <button id="qvAddBtn" onclick="qvAddToBag()" disabled
                        class="mt-2 py-4 rounded-2xl text-xs font-bold uppercase tracking-widest bg-[#405549] text-white hover:bg-pink-600 transition-colors disabled:opacity-40 disabled:cursor-not-allowed">
                    Select a Size
                </button>
                <a id="qvViewDetails" href="#" class="block text-center text-xs font-semibold text-gray-500 hover:text-pink-600 transition-colors py-1">
                    View Full Details &amp; Specifications →
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let qvProduct = null, qvColorIdx = 0, qvSize = null;

function openQuickView(product) {
    qvProduct = product; qvColorIdx = 0; qvSize = null;
    document.getElementById('qvName').textContent = product.name;
    document.getElementById('qvCategory').textContent = product.category || '';
    const descEl = document.getElementById('qvDescription');
    if (descEl) descEl.innerHTML = product.description || product.shortDescription || '';
    const viewDetailsEl = document.getElementById('qvViewDetails');
    if (viewDetailsEl) viewDetailsEl.href = '{{ url('/products') }}/' + encodeURIComponent(product.slug);
    document.getElementById('colorOptions').innerHTML = product.colors.map((c, i) => `
        <button type="button" onclick="qvSelectColor(${i})" title="${c.name}"
                class="qv-swatch w-8 h-8 rounded-full border-2 transition-all"
                style="background:${c.hex || '#ccc'}"></button>`).join('');
    qvSelectColor(0);
    const m = document.getElementById('quickViewModal');
    m.classList.remove('hidden'); m.classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closeQuickView() {
    const m = document.getElementById('quickViewModal');
    m.classList.add('hidden'); m.classList.remove('flex');
    document.body.style.overflow = '';
}

function qvSelectColor(idx) {
    qvColorIdx = idx; qvSize = null;
    const c = qvProduct.colors[idx];
    document.getElementById('qvColorName').textContent = c.name;
    document.getElementById('qvImage').src = (c.images && c.images[0]) || qvProduct.coverImage;
    document.querySelectorAll('.qv-swatch').forEach((b, i) => {
        b.style.borderColor = i === idx ? '#DB2777' : 'transparent';
        b.style.transform = i === idx ? 'scale(1.15)' : 'scale(1)';
    });
    document.getElementById('sizeOptions').innerHTML = c.sizes.map(s => `
        <button type="button" onclick="qvSelectSize('${s.size}')" data-size="${s.size}" ${s.inStock ? '' : 'disabled'}
                class="qv-size px-4 py-2 rounded-full text-xs font-bold border border-pink-100 text-gray-700 transition-colors ${s.inStock ? 'hover:border-pink-400' : 'opacity-30 cursor-not-allowed'}">
            ${s.size}${s.inStock ? '' : ' (Out)'}
        </button>`).join('');
    qvShowPrice(qvProduct.startingPrice, qvProduct.startingCompareAt);
    document.getElementById('qvStockWarning').classList.add('hidden');
    qvUpdateBtn();
}

function qvSelectSize(size) {
    qvSize = size;
    const v = qvProduct.colors[qvColorIdx].sizes.find(s => s.size === size);
    document.querySelectorAll('.qv-size').forEach(b => {
        const on = b.dataset.size === size;
        b.classList.toggle('bg-pink-600', on);
        b.classList.toggle('text-white', on);
        b.classList.toggle('border-pink-600', on);
    });
    qvShowPrice(v.price, v.compareAtPrice);
    const w = document.getElementById('qvStockWarning');
    if (v.stock > 0 && v.stock <= 3) { w.textContent = 'Only ' + v.stock + ' left'; w.classList.remove('hidden'); }
    else { w.classList.add('hidden'); }
    qvUpdateBtn();
}

function qvShowPrice(price, compareAt) {
    document.getElementById('qvPriceCurrent').textContent = '₹' + Number(price).toLocaleString('en-IN');
    const cp = document.getElementById('qvPriceCompare');
    if (compareAt) { cp.textContent = '₹' + Number(compareAt).toLocaleString('en-IN'); cp.classList.remove('hidden'); }
    else { cp.classList.add('hidden'); }
}

function qvUpdateBtn() {
    const b = document.getElementById('qvAddBtn');
    b.disabled = !qvSize;
    b.textContent = qvSize ? 'Add to Bag' : 'Select a Size';
}

function qvAddToBag() {
    if (!qvSize) return;
    const c = qvProduct.colors[qvColorIdx];
    const v = c.sizes.find(s => s.size === qvSize);
    addToCart(qvProduct.id, qvProduct.name, c.name, qvSize, v.price, (c.images && c.images[0]) || qvProduct.coverImage);
    closeQuickView();
}
</script>
@endpush
