{{--
    ======================================================================
    THEME: SAAYA — Royal Indian Trousseau & Heritage Silk
    PARTIAL: PRODUCT QUICK-VIEW MODAL
    ======================================================================
    File: resources/views/storefront/saaya/partials/quick-view.blade.php
    Responsibility: Interactive luxury modal popup for rapid product inspection and cart add
    Features:
      - Deep maroon velvet canvas, antique gold foil accents & Cormorant Garamond typography
      - Live color swatch selector & dynamic size availability mapping
      - Direct reactive Add to Bag handler synced with storefront cart store
      - Full bridal dossier link redirecting to standalone product page
--}}
<div id="quickViewModal" class="hidden fixed inset-0 z-[220] bg-maroon-dark/60 backdrop-blur-md items-end sm:items-center justify-center sm:p-4 transition-all duration-300"
     onclick="if(event.target===this) closeQuickView()">
    <div class="bg-[#F7F1E6] text-maroon w-full sm:max-w-3xl rounded-t-[32px] sm:rounded-[28px] overflow-hidden shadow-2xl border border-gold/40 max-h-[92vh] overflow-y-auto relative animate-fade">
        <button onclick="closeQuickView()" class="absolute top-4 right-5 z-20 text-3xl font-light text-maroon/60 hover:text-maroon hover:rotate-90 transition-all cursor-pointer leading-none" aria-label="Close">&times;</button>
        
        <div class="grid sm:grid-cols-2">
            {{-- Left column: Product Image --}}
            <div class="relative aspect-[4/5] sm:aspect-auto bg-blush/20 overflow-hidden border-b sm:border-b-0 sm:border-r border-gold/20">
                <img id="qvImage" src="" alt="" class="w-full h-full object-cover transition-transform duration-700">
                <span id="qvBadge" class="absolute top-4 left-4 z-10 bg-gold text-maroon text-[9px] font-bold px-2.5 py-1 tracking-widest uppercase shadow-sm hidden">
                </span>
            </div>

            {{-- Right column: Dossier Details --}}
            <div class="p-6 sm:p-8 flex flex-col justify-between gap-4">
                <div>
                    <span id="qvCategory" class="text-[10px] font-mono uppercase tracking-[0.2em] font-bold text-gold block mb-1"></span>
                    <h3 id="qvName" class="font-display italic text-2xl sm:text-3xl text-maroon font-semibold tracking-tight leading-tight"></h3>
                    <div id="qvDescription" class="text-xs text-maroon/70 leading-relaxed font-body mt-2 line-clamp-3 product-rich-text [&_p]:inline [&_p]:m-0"></div>
                </div>

                <div class="flex items-baseline gap-2 pt-2 border-t border-gold/20">
                    <span class="text-xs text-maroon/50 uppercase tracking-wider font-medium font-body">Price:</span>
                    <p id="qvPrice" class="text-2xl font-display font-bold text-maroon">
                        <span id="qvPriceCurrent"></span>
                        <span id="qvPriceCompare" class="text-sm font-normal text-maroon/40 line-through ml-2 hidden"></span>
                    </p>
                </div>

                {{-- Color Swatches --}}
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] uppercase tracking-widest font-bold text-maroon/60 font-body">
                            Shade: <span id="qvColorName" class="text-gold font-semibold"></span>
                        </span>
                    </div>
                    <div id="colorOptions" class="flex gap-2.5 flex-wrap"></div>
                </div>

                {{-- Size Options --}}
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] uppercase tracking-widest font-bold text-maroon/60 font-body">Select Size</span>
                        <span id="qvStockWarning" class="text-[10px] font-bold text-rose-600 hidden font-mono"></span>
                    </div>
                    <div id="sizeOptions" class="flex gap-2 flex-wrap"></div>
                </div>

                {{-- Action Buttons --}}
                <div class="pt-2 space-y-2">
                    <button id="qvAddBtn" onclick="qvAddToBag()" disabled
                            class="w-full py-3.5 px-6 bg-gold text-maroon hover:bg-maroon hover:text-gold border border-gold font-bold text-xs uppercase tracking-[0.2em] transition-all duration-300 shadow-md disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        <span id="qvAddBtnText">SELECT A SIZE</span>
                    </button>

                    <div class="text-center pt-1 border-t border-gold/10">
                        <a id="qvViewDetails" href="#" class="inline-flex items-center gap-1.5 text-xs font-semibold text-maroon/70 hover:text-gold transition-colors py-1">
                            <span>Explore Full Bridal Dossier</span>
                            <span class="text-gold">→</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
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
        if (catEl) catEl.textContent = product.category || 'Heritage Collection';

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
        if (colorsContainer && product.colors && product.colors.length) {
            colorsContainer.innerHTML = '';
            product.colors.forEach((c, i) => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.title = c.name || '';
                btn.className = `qv-swatch w-7 h-7 rounded-full border-2 transition-all cursor-pointer ${i === 0 ? 'border-maroon scale-110 shadow-xs' : 'border-gold/30 hover:border-gold'}`;
                const safeHex = (c.hex && /^#[0-9a-fA-F]{3,8}$/.test(c.hex)) ? c.hex : '#ccc';
                btn.style.background = safeHex;
                btn.onclick = function() { qvSelectColor(i); };
                colorsContainer.appendChild(btn);
            });
        }

        qvSelectColor(0);

        const m = document.getElementById('quickViewModal');
        if (m) {
            m.classList.remove('hidden');
            m.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }
    };

    window.closeQuickView = function() {
        const m = document.getElementById('quickViewModal');
        if (m) {
            m.classList.add('hidden');
            m.classList.remove('flex');
            document.body.style.overflow = '';
        }
    };

    window.qvSelectColor = function(idx) {
        if (!qvProduct || !qvProduct.colors || !qvProduct.colors[idx]) return;
        qvColorIdx = idx;
        qvSize = null;
        const c = qvProduct.colors[idx];

        const colorNameEl = document.getElementById('qvColorName');
        if (colorNameEl) colorNameEl.textContent = c.name || '';

        const imgEl = document.getElementById('qvImage');
        if (imgEl) imgEl.src = (c.images && c.images[0]) || qvProduct.coverImage || '';

        document.querySelectorAll('.qv-swatch').forEach((b, i) => {
            const active = i === idx;
            b.classList.toggle('border-maroon', active);
            b.classList.toggle('scale-110', active);
            b.classList.toggle('shadow-xs', active);
            b.classList.toggle('border-gold/30', !active);
        });

        const sizeContainer = document.getElementById('sizeOptions');
        if (sizeContainer && c.sizes) {
            sizeContainer.innerHTML = '';
            c.sizes.forEach(s => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.dataset.size = s.size || '';
                btn.disabled = !s.inStock;
                btn.className = `qv-size px-3.5 py-1.5 rounded-full text-xs font-semibold border transition-all ${s.inStock ? 'border-gold/40 text-maroon hover:border-maroon hover:bg-gold/10 cursor-pointer' : 'border-maroon/10 text-maroon/20 cursor-not-allowed'}`;
                btn.textContent = (s.size || '') + (s.inStock ? '' : ' (Out)');
                btn.onclick = function() { qvSelectSize(s.size); };
                sizeContainer.appendChild(btn);
            });
        }

        qvShowPrice(qvProduct.startingPrice, qvProduct.startingCompareAt);

        const stockWarning = document.getElementById('qvStockWarning');
        if (stockWarning) stockWarning.classList.add('hidden');

        qvUpdateBtn();
    };

    window.qvSelectSize = function(size) {
        if (!qvProduct || !qvProduct.colors || !qvProduct.colors[qvColorIdx]) return;
        qvSize = size;
        const v = qvProduct.colors[qvColorIdx].sizes.find(s => s.size === size);

        document.querySelectorAll('.qv-size').forEach(b => {
            const on = b.dataset.size === size;
            b.classList.toggle('bg-maroon', on);
            b.classList.toggle('text-ivory', on);
            b.classList.toggle('border-maroon', on);
            b.classList.toggle('shadow-xs', on);
        });

        if (v) {
            qvShowPrice(v.price, v.compareAtPrice);
            const w = document.getElementById('qvStockWarning');
            if (w) {
                if (v.stock > 0 && v.stock <= 3) {
                    w.textContent = 'Only ' + v.stock + ' handcrafted pieces left';
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
        b.disabled = !qvSize;
        if (t) t.textContent = qvSize ? 'ADD TO BAG' : 'SELECT A SIZE';
    }

    window.qvAddToBag = function() {
        if (!qvSize || !qvProduct) return;
        const c = qvProduct.colors[qvColorIdx];
        const v = c.sizes.find(s => s.size === qvSize);
        if (typeof addToCart === 'function') {
            addToCart(qvProduct.id, qvProduct.name, c.name, qvSize, v.price, (c.images && c.images[0]) || qvProduct.coverImage, v.id || null, v.sku || null);
        }
        closeQuickView();
    };

    // Close on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeQuickView();
    });
})();
</script>
@endpush
