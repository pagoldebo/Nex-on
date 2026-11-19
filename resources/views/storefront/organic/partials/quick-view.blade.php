{{--
    ======================================================================
    THEME: VERDIA (ORGANIC) — Botanical Living & Organic Produce Marketplace
    PARTIAL: PRODUCT QUICK-VIEW MODAL
    ======================================================================
    File: resources/views/storefront/organic/partials/quick-view.blade.php
    Responsibility: Interactive botanical modal popup for rapid fresh produce/product inspection and cart add
    Features:
      - Crisp stone/white canvas, fresh organic green (#699E4B), citrus badges & clean typography
      - Live color/variety selector with image gallery thumbnails
      - Size/pack option selector with live stock indicators
      - Direct reactive Add to Basket handler synced with storefront cart store
      - Full product details link redirecting to standalone product page
--}}
<div id="quickViewModal" style="display: none;" class="fixed inset-0 z-[220] bg-stone-900/60 backdrop-blur-sm items-end sm:items-center justify-center sm:p-4 transition-all duration-300">
    <div class="bg-white text-stone-900 w-full sm:max-w-3xl rounded-t-[32px] sm:rounded-[28px] overflow-hidden shadow-2xl border border-stone-200 max-h-[92vh] overflow-y-auto relative animate-fade">
        <button type="button" data-qv-close="true" onclick="closeQuickView()" class="absolute top-4 right-5 z-20 w-9 h-9 rounded-full bg-stone-100 hover:bg-organic-500 hover:text-white text-stone-500 text-2xl font-light flex items-center justify-center transition-all cursor-pointer leading-none shadow-xs" aria-label="Close">&times;</button>
        
        <div class="grid sm:grid-cols-2">
            {{-- Left column: Image & Gallery --}}
            <div class="relative bg-stone-50 p-4 flex flex-col justify-between border-b sm:border-b-0 sm:border-r border-stone-100">
                <div class="relative aspect-[4/5] rounded-2xl overflow-hidden bg-white shadow-xs flex items-center justify-center">
                    <img id="qvImage" src="" alt="" class="max-h-full max-w-full object-contain transition-transform duration-500">
                    <span id="qvBadge" class="absolute top-3 left-3 z-10 bg-organic-500 text-white text-[9px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider shadow-xs hidden">
                    </span>
                </div>
                {{-- Thumbnails strip --}}
                <div id="qvThumbnails" class="flex gap-2 mt-3 overflow-x-auto pb-1 empty:hidden"></div>
            </div>

            {{-- Right column: Product Details --}}
            <div class="p-6 sm:p-8 flex flex-col justify-between gap-4">
                <div>
                    <span id="qvCategory" class="text-[10px] uppercase tracking-wider font-bold text-organic-600 block mb-1"></span>
                    <h3 id="qvName" class="font-display text-2xl sm:text-3xl text-stone-900 font-extrabold tracking-tight leading-tight"></h3>
                    <div id="qvDescription" class="text-xs text-stone-600 leading-relaxed mt-2 line-clamp-3 product-rich-text [&_p]:inline [&_p]:m-0"></div>
                </div>

                <div class="flex items-baseline gap-2 pt-2 border-t border-stone-100">
                    <span class="text-xs text-stone-400 uppercase tracking-wider font-semibold">Price:</span>
                    <p id="qvPrice" class="text-2xl font-display font-black text-organic-900">
                        <span id="qvPriceCurrent"></span>
                        <span id="qvPriceCompare" class="text-sm font-normal text-stone-400 line-through ml-2 hidden"></span>
                    </p>
                </div>

                {{-- Color / Variety Swatches --}}
                <div id="qvColorSection">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] uppercase tracking-wider font-bold text-stone-500">
                            Variety / Shade: <span id="qvColorName" class="text-organic-600 font-semibold"></span>
                        </span>
                    </div>
                    <div id="colorOptions" class="flex gap-2.5 flex-wrap"></div>
                </div>

                {{-- Size / Pack Options --}}
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] uppercase tracking-wider font-bold text-stone-500">Select Pack / Size</span>
                        <span id="qvStockWarning" class="text-[10px] font-bold text-citrus hidden font-mono"></span>
                    </div>
                    <div id="sizeOptions" class="flex gap-2 flex-wrap"></div>
                </div>

                {{-- Action Buttons --}}
                <div class="pt-2 space-y-2">
                    <button id="qvAddBtn" onclick="qvAddToBag()" disabled
                            class="w-full py-3.5 px-6 bg-organic-500 hover:bg-organic-600 text-white font-bold text-xs uppercase tracking-wider rounded-xl transition-all duration-300 shadow-md hover:shadow-organic disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        <span id="qvAddBtnText">SELECT PACK / SIZE</span>
                    </button>

                    <div class="text-center pt-1 border-t border-stone-100">
                        <a id="qvViewDetails" href="#" class="inline-flex items-center gap-1.5 text-xs font-semibold text-stone-500 hover:text-organic-600 transition-colors py-1">
                            <span>View Full Harvest Dossier &amp; Nutritional Facts</span>
                            <span class="text-organic-500">→</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script nonce="{{ $cspNonce ?? '' }}">
(function() {
    let qvProduct = null, qvColorIdx = 0, qvSize = null;

    window.openQuickView = function(product) {
        if (!product) return;
        qvProduct = product;
        qvColorIdx = 0;
        qvSize = null;

        const nameEl = document.getElementById('qvName');
        if (nameEl) nameEl.textContent = product.name || '';

        const catEl = document.getElementById('qvCategory');
        if (catEl) catEl.textContent = product.category || 'Fresh Organic Collection';

        const descEl = document.getElementById('qvDescription');
        if (descEl) descEl.innerHTML = product.description || product.shortDescription || '';

        const badgeEl = document.getElementById('qvBadge');
        if (badgeEl) {
            if (product.badge) {
                badgeEl.textContent = product.badge;
                badgeEl.classList.remove('hidden');
            } else {
                badgeEl.classList.add('hidden');
            }
        }

        const viewDetailsEl = document.getElementById('qvViewDetails');
        if (viewDetailsEl) viewDetailsEl.href = '{{ url('/products') }}/' + encodeURIComponent(product.slug);

        const colorsContainer = document.getElementById('colorOptions');
        const colorSection = document.getElementById('qvColorSection');
        if (colorsContainer && product.colors && product.colors.length) {
            if (colorSection) colorSection.classList.remove('hidden');
            colorsContainer.innerHTML = '';
            product.colors.forEach((c, i) => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.title = c.name || '';
                btn.className = `qv-swatch w-7 h-7 rounded-full border-2 transition-all cursor-pointer ${i === 0 ? 'border-organic-600 ring-2 ring-organic-300 scale-110 shadow-xs' : 'border-stone-200 hover:border-organic-400'}`;
                const safeHex = (c.hex && /^#[0-9a-fA-F]{3,8}$/.test(c.hex)) ? c.hex : '#699E4B';
                btn.style.background = safeHex;
                btn.onclick = function() { qvSelectColor(i); };
                colorsContainer.appendChild(btn);
            });
        } else if (colorSection) {
            colorSection.classList.add('hidden');
        }

        qvSelectColor(0);

        const m = document.getElementById('quickViewModal');
        if (m) {
            m.style.setProperty('display', 'flex', 'important');
            m.classList.remove('hidden');
            m.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }
    };

    window.closeQuickView = function() {
        const m = document.getElementById('quickViewModal');
        if (m) {
            m.style.setProperty('display', 'none', 'important');
            m.classList.add('hidden');
            m.classList.remove('flex');
            document.body.style.overflow = '';
        }
    };

    window.qvSelectColor = function(idx) {
        if (!qvProduct) return;
        qvColorIdx = idx;
        qvSize = null;
        const colors = qvProduct.colors || [];
        const c = colors[idx] || null;

        const colorNameEl = document.getElementById('qvColorName');
        if (colorNameEl) colorNameEl.textContent = c ? (c.name || '') : '';

        const initialImg = (c && c.images && c.images[0]) || qvProduct.coverImage || '';
        const imgEl = document.getElementById('qvImage');
        if (imgEl) imgEl.src = initialImg;

        // Render thumbnails if multiple images exist
        const thumbContainer = document.getElementById('qvThumbnails');
        if (thumbContainer) {
            thumbContainer.innerHTML = '';
            const images = (c && c.images && c.images.length) ? c.images : (qvProduct.coverImage ? [qvProduct.coverImage] : []);
            if (images.length > 1) {
                images.forEach((imgUrl, imgIdx) => {
                    const thumb = document.createElement('button');
                    thumb.type = 'button';
                    thumb.className = `w-10 h-12 rounded-lg overflow-hidden border-2 transition-all cursor-pointer flex-shrink-0 ${imgIdx === 0 ? 'border-organic-600 scale-105' : 'border-stone-200 opacity-70 hover:opacity-100'}`;
                    thumb.innerHTML = `<img src="${imgUrl}" class="w-full h-full object-cover">`;
                    thumb.onclick = function() {
                        if (imgEl) imgEl.src = imgUrl;
                        thumbContainer.querySelectorAll('button').forEach((b, k) => {
                            b.className = `w-10 h-12 rounded-lg overflow-hidden border-2 transition-all cursor-pointer flex-shrink-0 ${k === imgIdx ? 'border-organic-600 scale-105' : 'border-stone-200 opacity-70 hover:opacity-100'}`;
                        });
                    };
                    thumbContainer.appendChild(thumb);
                });
            }
        }

        document.querySelectorAll('.qv-swatch').forEach((b, i) => {
            const active = i === idx;
            b.classList.toggle('border-organic-600', active);
            b.classList.toggle('ring-2', active);
            b.classList.toggle('ring-organic-300', active);
            b.classList.toggle('scale-110', active);
            b.classList.toggle('shadow-xs', active);
            b.classList.toggle('border-stone-200', !active);
        });

        const sizeContainer = document.getElementById('sizeOptions');
        if (sizeContainer) {
            sizeContainer.innerHTML = '';
            const sizes = (c && c.sizes) ? c.sizes : [];
            if (sizes.length) {
                sizes.forEach(s => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.dataset.size = s.size || '';
                    btn.disabled = !s.inStock;
                    btn.className = `qv-size px-3.5 py-1.5 rounded-lg text-xs font-bold border transition-all ${s.inStock ? 'border-stone-200 text-stone-700 hover:border-organic-500 hover:bg-organic-50 cursor-pointer' : 'border-stone-100 text-stone-300 cursor-not-allowed'}`;
                    btn.textContent = (s.size || '') + (s.inStock ? '' : ' (Out)');
                    btn.onclick = function() { qvSelectSize(s.size); };
                    sizeContainer.appendChild(btn);
                });

                // Auto select first in-stock variant
                const firstInStock = sizes.find(s => s.inStock);
                if (firstInStock) {
                    qvSelectSize(firstInStock.size);
                }
            } else {
                qvShowPrice(qvProduct.startingPrice, qvProduct.startingCompareAt);
                qvUpdateBtn();
            }
        }

        qvShowPrice(qvProduct.startingPrice, qvProduct.startingCompareAt);
        const stockWarning = document.getElementById('qvStockWarning');
        if (stockWarning) stockWarning.classList.add('hidden');
        qvUpdateBtn();
    };

    window.qvSelectSize = function(size) {
        if (!qvProduct) return;
        qvSize = size;
        const colors = qvProduct.colors || [];
        const c = colors[qvColorIdx] || null;
        const v = (c && c.sizes) ? c.sizes.find(s => s.size === size) : null;

        document.querySelectorAll('.qv-size').forEach(b => {
            const on = b.dataset.size === size;
            b.classList.toggle('bg-organic-500', on);
            b.classList.toggle('text-white', on);
            b.classList.toggle('border-organic-500', on);
            b.classList.toggle('shadow-xs', on);
        });

        if (v) {
            qvShowPrice(v.price, v.compareAtPrice);
            const w = document.getElementById('qvStockWarning');
            if (w) {
                if (v.stock > 0 && v.stock <= 3) {
                    w.textContent = 'Only ' + v.stock + ' packs remaining!';
                    w.classList.remove('hidden');
                } else {
                    w.classList.add('hidden');
                }
            }
        }

        qvUpdateBtn();
    };

    function qvShowPrice(price, compareAt) {
        const curEl = document.getElementById('qvPriceCurrent');
        if (curEl) curEl.textContent = '₹' + Number(price || 0).toLocaleString('en-IN', { minimumFractionDigits: 2 });

        const cpEl = document.getElementById('qvPriceCompare');
        if (cpEl) {
            if (compareAt && Number(compareAt) > Number(price)) {
                cpEl.textContent = '₹' + Number(compareAt).toLocaleString('en-IN', { minimumFractionDigits: 2 });
                cpEl.classList.remove('hidden');
            } else {
                cpEl.classList.add('hidden');
            }
        }
    }

    function qvUpdateBtn() {
        const b = document.getElementById('qvAddBtn');
        const t = document.getElementById('qvAddBtnText');
        if (!b) return;
        const hasSizes = qvProduct && qvProduct.colors && qvProduct.colors[qvColorIdx] && qvProduct.colors[qvColorIdx].sizes && qvProduct.colors[qvColorIdx].sizes.length > 0;
        b.disabled = hasSizes ? !qvSize : false;
        if (t) t.textContent = (!hasSizes || qvSize) ? 'ADD TO BASKET' : 'SELECT PACK / SIZE';
    }

    window.qvAddToBag = function() {
        if (!qvProduct) return;
        const colors = qvProduct.colors || [];
        const c = colors[qvColorIdx] || null;
        const v = (c && c.sizes && qvSize) ? c.sizes.find(s => s.size === qvSize) : (c && c.sizes ? c.sizes[0] : null);
        
        if (typeof addToCart === 'function') {
            addToCart(
                qvProduct.id,
                qvProduct.name,
                c ? c.name : 'Default',
                qvSize || 'Standard',
                v ? v.price : (qvProduct.startingPrice || 0),
                (c && c.images && c.images[0]) || qvProduct.coverImage || '',
                v ? (v.variantId || v.id) : null,
                v ? v.sku : null
            );
        }
        closeQuickView();
    };

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeQuickView();
    });

    const modal = document.getElementById('quickViewModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal || e.target.closest('[data-qv-close]')) {
                closeQuickView();
            }
        });
    }

    const addBtn = document.getElementById('qvAddBtn');
    if (addBtn) {
        addBtn.addEventListener('click', function() {
            qvAddToBag();
        });
    }
})();
</script>
