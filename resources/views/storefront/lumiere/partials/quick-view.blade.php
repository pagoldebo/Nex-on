{{--
    ======================================================================
    THEME: LUMIÈRE — French Silk Atelier & Minimalist Luxury
    PARTIAL: PRODUCT QUICK-VIEW MODAL
    ======================================================================
    File: resources/views/storefront/lumiere/partials/quick-view.blade.php
    Responsibility: Interactive luxury modal popup for rapid product inspection and cart add
    Features:
      - Clean white/pink canvas, rose lustres (#DB2777) & deep atelier pine (#405549)
      - Live color swatch selector with image gallery switcher
      - Variant option selectors (sizes, colors, finishes) with live stock feedback
      - Instant reactive Add to Bag button synced with storefront cart store
      - Full product details link redirecting to standalone product page
--}}
<div id="quickViewModal" style="display: none;" class="fixed inset-0 z-[220] bg-black/60 backdrop-blur-sm items-end sm:items-center justify-center sm:p-4 transition-all duration-300">
    <div class="bg-white text-gray-900 w-full sm:max-w-3xl rounded-t-[32px] sm:rounded-[28px] overflow-hidden shadow-2xl border border-pink-100 max-h-[92vh] overflow-y-auto relative animate-fade">
        <button type="button" data-qv-close="true" onclick="closeQuickView()" class="absolute top-4 right-5 z-20 w-9 h-9 rounded-full bg-pink-50 hover:bg-pink-600 hover:text-white text-gray-400 text-2xl font-light flex items-center justify-center transition-all cursor-pointer leading-none shadow-xs" aria-label="Close">&times;</button>
        
        <div class="grid sm:grid-cols-2">
            {{-- Left column: Image & Gallery --}}
            <div class="relative bg-pink-50/50 p-4 flex flex-col justify-between border-b sm:border-b-0 sm:border-r border-pink-100/60">
                <div class="relative aspect-[4/5] rounded-2xl overflow-hidden bg-white shadow-xs">
                    <img id="qvImage" src="" alt="" class="w-full h-full object-cover transition-transform duration-700">
                    <span id="qvBadge" class="absolute top-3 left-3 z-10 bg-white/95 backdrop-blur-md text-pink-600 text-[9px] font-black px-2.5 py-1 rounded-full uppercase tracking-widest shadow-xs hidden">
                    </span>
                </div>
                {{-- Thumbnails strip --}}
                <div id="qvThumbnails" class="flex gap-2 mt-3 overflow-x-auto pb-1 empty:hidden"></div>
            </div>

            {{-- Right column: Product Details --}}
            <div class="p-6 sm:p-8 flex flex-col justify-between gap-4">
                <div>
                    <span id="qvCategory" class="text-[10px] uppercase tracking-widest font-bold text-pink-500 block mb-1"></span>
                    <h3 id="qvName" class="font-display italic text-2xl sm:text-3xl text-gray-900 font-bold tracking-tight leading-tight"></h3>
                    <div id="qvDescription" class="text-xs text-gray-500 leading-relaxed mt-1.5 line-clamp-2 product-rich-text [&_p]:inline [&_p]:m-0"></div>
                </div>

                <div class="flex items-baseline gap-2 pt-2 border-t border-pink-100">
                    <span class="text-xs text-gray-400 uppercase tracking-widest font-semibold">Price:</span>
                    <p id="qvPrice" class="text-2xl font-bold text-pink-600">
                        <span id="qvPriceCurrent"></span>
                        <span id="qvPriceCompare" class="text-sm font-normal text-gray-300 line-through ml-2 hidden"></span>
                    </p>
                </div>

                {{-- Color Swatches --}}
                <div id="qvColorSection">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] uppercase tracking-widest font-bold text-gray-400">
                            Shade: <span id="qvColorName" class="text-pink-500 font-semibold"></span>
                        </span>
                    </div>
                    <div id="colorOptions" class="flex gap-2.5 flex-wrap"></div>
                </div>

                {{-- Size Options --}}
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] uppercase tracking-widest font-bold text-gray-400">Size</span>
                        <span id="qvStockWarning" class="text-[10px] font-bold text-rose-500 hidden font-mono"></span>
                    </div>
                    <div id="sizeOptions" class="flex gap-2 flex-wrap"></div>
                </div>

                {{-- Action Buttons --}}
                <div class="pt-2 space-y-2">
                    <button id="qvAddBtn" onclick="qvAddToBag()" disabled
                            class="w-full py-3.5 px-6 rounded-2xl text-xs font-bold uppercase tracking-widest bg-[#405549] text-white hover:bg-pink-600 transition-all duration-300 shadow-md disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        <span id="qvAddBtnText">SELECT A SIZE</span>
                    </button>

                    <div class="text-center pt-1 border-t border-pink-50">
                        <a id="qvViewDetails" href="#" class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-500 hover:text-pink-600 transition-colors py-1">
                            <span>View Full Details &amp; Specifications</span>
                            <span class="text-pink-600">→</span>
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
        if (catEl) catEl.textContent = product.category || 'Lumière Atelier';

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
                btn.className = `qv-swatch w-7 h-7 rounded-full border-2 transition-all cursor-pointer ${i === 0 ? 'border-pink-600 scale-110 shadow-xs' : 'border-gray-200 hover:border-pink-300'}`;
                const safeHex = (c.hex && /^#[0-9a-fA-F]{3,8}$/.test(c.hex)) ? c.hex : '#ccc';
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
                    thumb.className = `w-10 h-12 rounded-lg overflow-hidden border-2 transition-all cursor-pointer flex-shrink-0 ${imgIdx === 0 ? 'border-pink-600 scale-105' : 'border-pink-100 opacity-70 hover:opacity-100'}`;
                    thumb.innerHTML = `<img src="${imgUrl}" class="w-full h-full object-cover">`;
                    thumb.onclick = function() {
                        if (imgEl) imgEl.src = imgUrl;
                        thumbContainer.querySelectorAll('button').forEach((b, k) => {
                            b.className = `w-10 h-12 rounded-lg overflow-hidden border-2 transition-all cursor-pointer flex-shrink-0 ${k === imgIdx ? 'border-pink-600 scale-105' : 'border-pink-100 opacity-70 hover:opacity-100'}`;
                        });
                    };
                    thumbContainer.appendChild(thumb);
                });
            }
        }

        document.querySelectorAll('.qv-swatch').forEach((b, i) => {
            const active = i === idx;
            b.classList.toggle('border-pink-600', active);
            b.classList.toggle('scale-110', active);
            b.classList.toggle('shadow-xs', active);
            b.classList.toggle('border-gray-200', !active);
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
                    btn.className = `qv-size px-3.5 py-1.5 rounded-full text-xs font-bold border transition-all ${s.inStock ? 'border-pink-100 text-gray-700 hover:border-pink-400 hover:bg-pink-50 cursor-pointer' : 'border-gray-100 text-gray-300 cursor-not-allowed'}`;
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
            b.classList.toggle('bg-pink-600', on);
            b.classList.toggle('text-white', on);
            b.classList.toggle('text-gray-700', !on);
            b.classList.toggle('border-pink-600', on);
            b.classList.toggle('border-pink-100', !on);
            b.classList.toggle('shadow-xs', on);
            b.classList.toggle('active', on);
            b.setAttribute('aria-selected', on ? 'true' : 'false');
            if (on) {
                b.style.setProperty('background-color', '#DB2777', 'important');
                b.style.setProperty('color', '#FFFFFF', 'important');
                b.style.setProperty('border-color', '#DB2777', 'important');
            } else if (!b.disabled) {
                b.style.removeProperty('background-color');
                b.style.removeProperty('color');
                b.style.removeProperty('border-color');
            }
        });

        if (v) {
            qvShowPrice(v.price, v.compareAtPrice);
            const w = document.getElementById('qvStockWarning');
            if (w) {
                if (v.stock > 0 && v.stock <= 3) {
                    w.textContent = 'Only ' + v.stock + ' left in atelier';
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
        if (curEl) curEl.textContent = '₹' + Number(price || 0).toLocaleString('en-IN');

        const cpEl = document.getElementById('qvPriceCompare');
        if (cpEl) {
            if (compareAt && Number(compareAt) > Number(price)) {
                cpEl.textContent = '₹' + Number(compareAt).toLocaleString('en-IN');
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
        if (t) t.textContent = (!hasSizes || qvSize) ? 'ADD TO BAG' : 'SELECT A SIZE';
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
