/**
 * ======================================================================
 * NEXON STOREFRONT: UNIVERSAL PRODUCT QUICK-VIEW MODAL JAVASCRIPT
 * Works seamlessly across all themes and quick-view modal partials
 * ======================================================================
 */
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
        if (catEl) catEl.textContent = product.category || 'Collection';

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
        if (viewDetailsEl) {
            viewDetailsEl.href = product.slug ? ('/products/' + encodeURIComponent(product.slug)) : '#';
        }

        const colorsContainer = document.getElementById('colorOptions');
        const colorSection = document.getElementById('qvColorSection');
        if (colorsContainer && product.colors && product.colors.length) {
            if (colorSection) colorSection.classList.remove('hidden');
            colorsContainer.innerHTML = '';
            product.colors.forEach((c, i) => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.title = c.name || '';
                btn.className = `qv-swatch w-7 h-7 rounded-full border-2 transition-all cursor-pointer ${i === 0 ? 'border-rose-dark scale-110 shadow-xs active' : 'border-rose/30 hover:border-rose'}`;
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
                    thumb.className = `w-10 h-12 rounded-lg overflow-hidden border-2 transition-all cursor-pointer flex-shrink-0 ${imgIdx === 0 ? 'border-rose-dark scale-105 active' : 'border-rose/20 opacity-70 hover:opacity-100'}`;
                    thumb.innerHTML = `<img src="${imgUrl}" class="w-full h-full object-cover" alt="">`;
                    thumb.onclick = function() {
                        if (imgEl) imgEl.src = imgUrl;
                        thumbContainer.querySelectorAll('button').forEach((b, k) => {
                            b.className = `w-10 h-12 rounded-lg overflow-hidden border-2 transition-all cursor-pointer flex-shrink-0 ${k === imgIdx ? 'border-rose-dark scale-105 active' : 'border-rose/20 opacity-70 hover:opacity-100'}`;
                        });
                    };
                    thumbContainer.appendChild(thumb);
                });
            }
        }

        document.querySelectorAll('.qv-swatch').forEach((b, i) => {
            const active = i === idx;
            b.classList.toggle('border-rose-dark', active);
            b.classList.toggle('scale-110', active);
            b.classList.toggle('shadow-xs', active);
            b.classList.toggle('border-rose/30', !active);
            b.classList.toggle('active', active);
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
                    btn.className = `qv-size px-3.5 py-1.5 rounded-full text-xs font-semibold border transition-all ${s.inStock ? 'border-rose/40 text-ink hover:border-rose hover:bg-rose/10 cursor-pointer' : 'border-rose/10 text-ink/20 cursor-not-allowed'}`;
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
            b.classList.toggle('bg-[#B97680]', on);
            b.classList.toggle('text-cream', on);
            b.classList.toggle('text-ink', !on);
            b.classList.toggle('border-[#B97680]', on);
            b.classList.toggle('border-rose/40', !on);
            b.classList.toggle('shadow-xs', on);
            b.classList.toggle('active', on);
            b.setAttribute('aria-selected', on ? 'true' : 'false');
            if (on) {
                b.style.setProperty('background-color', '#D89AA0', 'important');
                b.style.setProperty('color', '#FFFAF5', 'important');
                b.style.setProperty('border-color', '#D89AA0', 'important');
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

    document.addEventListener('DOMContentLoaded', function() {
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
    });
})();
