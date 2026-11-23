{{--
    ======================================================================
    STOREFRONT COMMON: UNIVERSAL PRODUCT QUICK-VIEW MODAL
    ======================================================================
    File: resources/views/storefront/common/quick-view.blade.php
    Responsibility: Universal fallback quick-view modal for all storefront themes
    Features:
      - Clean responsive Tailwind dialog, works across light, dark & pastel themes
      - Dynamic color, thumbnail and size variant picker
      - Real-time price and stock scarcity updates
      - Direct reactive Add to Cart handler
--}}
<div id="quickViewModal" style="display: none;" class="fixed inset-0 z-[220] bg-black/60 backdrop-blur-sm items-end sm:items-center justify-center sm:p-4 transition-all duration-300">
    <div class="bg-white text-slate-900 w-full sm:max-w-3xl rounded-t-[32px] sm:rounded-[28px] overflow-hidden shadow-2xl border border-slate-200 max-h-[92vh] overflow-y-auto relative animate-fade">
        <button type="button" data-qv-close="true" onclick="closeQuickView()" class="absolute top-4 right-5 z-20 w-9 h-9 rounded-full bg-slate-100 hover:bg-slate-900 hover:text-white text-slate-500 text-2xl font-light flex items-center justify-center transition-all cursor-pointer leading-none shadow-xs" aria-label="Close">&times;</button>
        
        <div class="grid sm:grid-cols-2">
            {{-- Left column: Image & Gallery --}}
            <div class="relative bg-slate-50 p-4 flex flex-col justify-between border-b sm:border-b-0 sm:border-r border-slate-100">
                <div class="relative aspect-[4/5] rounded-2xl overflow-hidden bg-white shadow-xs flex items-center justify-center">
                    <img id="qvImage" src="" alt="" class="max-h-full max-w-full object-contain transition-transform duration-500">
                    <span id="qvBadge" class="absolute top-3 left-3 z-10 bg-indigo-600 text-white text-[9px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider shadow-xs hidden">
                    </span>
                </div>
                {{-- Thumbnails strip --}}
                <div id="qvThumbnails" class="flex gap-2 mt-3 overflow-x-auto pb-1 empty:hidden"></div>
            </div>

            {{-- Right column: Product Details --}}
            <div class="p-6 sm:p-8 flex flex-col justify-between gap-4">
                <div>
                    <span id="qvCategory" class="text-[10px] uppercase tracking-wider font-bold text-indigo-600 block mb-1"></span>
                    <h3 id="qvName" class="font-display text-2xl sm:text-3xl text-slate-900 font-bold tracking-tight leading-tight"></h3>
                    <div id="qvDescription" class="text-xs text-slate-600 leading-relaxed mt-2 line-clamp-3 product-rich-text [&_p]:inline [&_p]:m-0"></div>
                </div>

                <div class="flex items-baseline gap-2 pt-2 border-t border-slate-100">
                    <span class="text-xs text-slate-400 uppercase tracking-wider font-semibold">Price:</span>
                    <p id="qvPrice" class="text-2xl font-display font-black text-slate-900">
                        <span id="qvPriceCurrent"></span>
                        <span id="qvPriceCompare" class="text-sm font-normal text-slate-400 line-through ml-2 hidden"></span>
                    </p>
                </div>

                {{-- Color Swatches --}}
                <div id="qvColorSection">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] uppercase tracking-wider font-bold text-slate-500">
                            Color: <span id="qvColorName" class="text-indigo-600 font-semibold"></span>
                        </span>
                    </div>
                    <div id="colorOptions" class="flex gap-2.5 flex-wrap"></div>
                </div>

                {{-- Size Options --}}
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] uppercase tracking-wider font-bold text-slate-500">Select Size</span>
                        <span id="qvStockWarning" class="text-[10px] font-bold text-rose-600 hidden font-mono"></span>
                    </div>
                    <div id="sizeOptions" class="flex gap-2 flex-wrap"></div>
                </div>

                {{-- Action Buttons --}}
                <div class="pt-2 space-y-2">
                    <button id="qvAddBtn" onclick="qvAddToBag()" disabled
                            class="w-full py-3.5 px-6 bg-slate-900 hover:bg-indigo-600 text-white font-bold text-xs uppercase tracking-wider rounded-xl transition-all duration-300 shadow-md disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        <span id="qvAddBtnText">SELECT A SIZE</span>
                    </button>

                    <div class="text-center pt-1 border-t border-slate-100">
                        <a id="qvViewDetails" href="#" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-indigo-600 transition-colors py-1">
                            <span>View Full Details &amp; Specifications</span>
                            <span class="text-indigo-600">→</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/quick-view.js') }}"></script>
@if(false)
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
        if (catEl) catEl.textContent = product.category || 'Product Collection';

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
                btn.className = `qv-swatch w-7 h-7 rounded-full border-2 transition-all cursor-pointer ${i === 0 ? 'border-indigo-600 ring-2 ring-indigo-200 scale-110 shadow-xs' : 'border-slate-200 hover:border-indigo-400'}`;
                const safeHex = (c.hex && /^#[0-9a-fA-F]{3,8}$/.test(c.hex)) ? c.hex : '#6366f1';
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
                    thumb.className = `w-10 h-12 rounded-lg overflow-hidden border-2 transition-all cursor-pointer flex-shrink-0 ${imgIdx === 0 ? 'border-indigo-600 scale-105' : 'border-slate-200 opacity-70 hover:opacity-100'}`;
                    thumb.innerHTML = `<img src="${imgUrl}" class="w-full h-full object-cover">`;
                    thumb.onclick = function() {
                        if (imgEl) imgEl.src = imgUrl;
                        thumbContainer.querySelectorAll('button').forEach((b, k) => {
                            b.className = `w-10 h-12 rounded-lg overflow-hidden border-2 transition-all cursor-pointer flex-shrink-0 ${k === imgIdx ? 'border-indigo-600 scale-105' : 'border-slate-200 opacity-70 hover:opacity-100'}`;
                        });
                    };
                    thumbContainer.appendChild(thumb);
                });
            }
        }

        document.querySelectorAll('.qv-swatch').forEach((b, i) => {
            const active = i === idx;
            b.classList.toggle('border-indigo-600', active);
            b.classList.toggle('ring-2', active);
            b.classList.toggle('ring-indigo-200', active);
            b.classList.toggle('scale-110', active);
            b.classList.toggle('shadow-xs', active);
            b.classList.toggle('border-slate-200', !active);
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
                    btn.className = `qv-size px-3.5 py-1.5 rounded-lg text-xs font-bold border transition-all ${s.inStock ? 'border-slate-200 text-slate-700 hover:border-indigo-500 hover:bg-indigo-50 cursor-pointer' : 'border-slate-100 text-slate-300 cursor-not-allowed'}`;
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
            b.classList.toggle('bg-indigo-600', on);
            b.classList.toggle('text-white', on);
            b.classList.toggle('text-slate-700', !on);
            b.classList.toggle('border-indigo-600', on);
            b.classList.toggle('border-slate-200', !on);
            b.classList.toggle('shadow-xs', on);
            b.classList.toggle('active', on);
            b.setAttribute('aria-selected', on ? 'true' : 'false');
            if (on) {
                b.style.setProperty('background-color', '#4F46E5', 'important');
                b.style.setProperty('color', '#FFFFFF', 'important');
                b.style.setProperty('border-color', '#4F46E5', 'important');
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
                    w.textContent = 'Only ' + v.stock + ' left in stock!';
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
        if (t) t.textContent = (!hasSizes || qvSize) ? 'ADD TO CART' : 'SELECT A SIZE';
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
@endif
