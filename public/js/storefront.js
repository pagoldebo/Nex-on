/**
 * ======================================================================
 * NEXON STOREFRONT: COMMON CORE JAVASCRIPT
 * Shared cart, drawer, modal, quick-view and UI behaviors across themes
 * ======================================================================
 */

// ── Multi-currency helpers ─────────────────────────────────────────
window.formatPrice = function(amount) {
    const num = Number(amount || 0);
    const cfg = window.currencyConfig || { code: 'INR', symbol: '₹', rate: 1.0, decimals: 0, locale: 'en-IN' };
    const rate = typeof cfg.rate === 'number' && cfg.rate > 0 ? cfg.rate : 1.0;
    const decimals = typeof cfg.decimals === 'number' ? cfg.decimals : 0;
    const converted = num * rate;
    const formatted = converted.toLocaleString(cfg.locale || 'en-US', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals
    });
    return (cfg.symbol || '₹') + formatted;
};

window.switchCurrency = async function(code) {
    if (!code) return;
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const res = await fetch('/currency/switch', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken || ''
            },
            body: JSON.stringify({ currency: code })
        });
        const data = await res.json();
        if (data.success) {
            window.location.reload();
        } else {
            console.error('Failed to switch currency:', data.message);
            window.location.href = '/currency/' + encodeURIComponent(code);
        }
    } catch (e) {
        window.location.href = '/currency/' + encodeURIComponent(code);
    }
};

// ── Cart state (shared across all pages via sessionStorage) ────────
let cart = JSON.parse(sessionStorage.getItem('lc_cart') || '[]');

function addToCart(productId, name, colorName, size, price, image, variantId, sku) {
    let item;
    if (typeof productId === 'object' && productId !== null) {
        item = productId;
    } else {
        item = { productId, name, colorName, size, price, image, variantId: variantId || null, sku: sku || null, qty: 1 };
    }
    const existing = cart.find(i => i.productId === item.productId && i.colorName === item.colorName && i.size === item.size);
    if (existing) {
        existing.qty = (existing.qty || 1) + (item.qty || 1);
    } else {
        cart.push({
            productId: item.productId,
            variantId: item.variantId || null,
            sku: item.sku || null,
            name: item.name,
            colorName: item.colorName,
            size: item.size,
            price: item.price,
            image: item.image,
            qty: item.qty || 1
        });
    }
    persistCart();
    updateCartUI();
    openCartDrawer();
}

function removeFromCart(productId, colorName, size) {
    cart = cart.filter(i => !(i.productId === productId && i.colorName === colorName && i.size === size));
    persistCart();
    updateCartUI();
}

function persistCart() {
    sessionStorage.setItem('lc_cart', JSON.stringify(cart));
}

function cartTotal() {
    return cart.reduce((sum, i) => sum + (i.price * i.qty), 0);
}

function updateCartUI() {
    const count = cart.reduce((s, i) => s + i.qty, 0);
    document.querySelectorAll('.cart-count').forEach(el => el.textContent = count);
    document.querySelectorAll('.cart-count-badge').forEach(el => el.textContent = count);
    const totalEl = document.getElementById('cartTotal');
    if (totalEl) totalEl.textContent = window.formatPrice(cartTotal());
    document.querySelectorAll('.cart-total-amount, #cartDrawerSubtotal').forEach(el => el.textContent = window.formatPrice(cartTotal()));
    renderCartItems();
}

const escHtml = s => String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));

function renderCartItems() {
    const container = document.getElementById('cartItems');
    if (!container) return;
    if (cart.length === 0) {
        container.innerHTML = '<p class="text-center text-sm opacity-40 mt-10 px-4">Your bag is empty.</p>';
        return;
    }
    container.innerHTML = cart.map((item, idx) => `
        <div class="flex gap-3 py-3 border-b border-current/10">
            <img src="${escHtml(item.image)}" class="w-14 h-14 object-cover rounded-lg flex-shrink-0 bg-gray-100" alt="${escHtml(item.name)}">
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-sm truncate">${escHtml(item.name)}</p>
                <p class="text-xs opacity-50">${escHtml(item.colorName)} / ${escHtml(item.size)} × ${Number(item.qty) || 1}</p>
                <p class="font-bold text-sm mt-0.5">${window.formatPrice((Number(item.price) || 0) * (Number(item.qty) || 1))}</p>
            </div>
            <button type="button" onclick="removeFromCartByIndex(${idx})" class="opacity-40 hover:opacity-100 text-lg leading-none" aria-label="Remove item">&times;</button>
        </div>
    `).join('');
}

function removeFromCartByIndex(idx) {
    if (cart[idx]) {
        cart.splice(idx, 1);
        persistCart();
        updateCartUI();
    }
}

function openCartDrawer() {
    if (typeof updateCartUI === 'function') updateCartUI();
    document.getElementById('cartDrawer')?.classList.remove('translate-x-full');
    document.getElementById('cartOverlay')?.classList.remove('hidden');
}

function closeCartDrawer() {
    document.getElementById('cartDrawer')?.classList.add('translate-x-full');
    document.getElementById('cartOverlay')?.classList.add('hidden');
}

// ── Intersection Observer for Scroll Reveals ──
const revealIO = new IntersectionObserver(entries => {
    entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('in-view'); });
}, { threshold: 0.12 });

function setActiveTab(name) {
    document.querySelectorAll('.tab-item').forEach(t => t.classList.toggle('active', t.dataset.tab === name));
}

// ── Login Modal Controls ──
function openLoginModal() {
    const m = document.getElementById('loginModal');
    if (!m) return;
    if (typeof m.showModal === 'function' && m.tagName === 'DIALOG') {
        try { m.showModal(); return; } catch(e) {}
    }
    m.classList.remove('hidden');
    m.classList.add('flex');
    m.style.setProperty('display', 'flex', 'important');
    document.body.style.overflow = 'hidden';
    setTimeout(() => m.querySelector('input[type="email"], input[type="text"]')?.focus(), 50);
}

function closeLoginModal() {
    const m = document.getElementById('loginModal');
    if (!m) return;
    if (typeof m.close === 'function' && m.tagName === 'DIALOG' && m.open) {
        try { m.close(); return; } catch(e) {}
    }
    m.classList.add('hidden');
    m.classList.remove('flex');
    m.style.setProperty('display', 'none', 'important');
    document.body.style.overflow = '';
}
window.openLoginModal = openLoginModal;
window.closeLoginModal = closeLoginModal;

// ── Category Sheet Controls ──
window.openCategorySheet = function() {
    const overlay = document.getElementById('sheetOverlay');
    if (!overlay) return;
    overlay.style.background = 'rgba(0,0,0,0.5)';
    overlay.style.visibility = 'visible';
    overlay.style.pointerEvents = 'auto';
    overlay.querySelector('.bottom-sheet')?.style.setProperty('transform', 'translateY(0)');
    document.body.style.overflow = 'hidden';
};

window.closeCategorySheet = function() {
    const overlay = document.getElementById('sheetOverlay');
    if (!overlay) return;
    overlay.style.background = 'rgba(0,0,0,0)';
    overlay.style.pointerEvents = 'none';
    overlay.querySelector('.bottom-sheet')?.style.setProperty('transform', 'translateY(100%)');
    document.body.style.overflow = '';
    setTimeout(() => { overlay.style.visibility = 'hidden'; }, 350);
};

// ── Search Overlay Controls ──
function openSearch() {
    document.getElementById('searchOverlay')?.classList.remove('hidden');
    document.getElementById('searchOverlay')?.classList.add('flex');
    setTimeout(() => document.getElementById('searchInput')?.focus(), 50);
}

function closeSearch() {
    document.getElementById('searchOverlay')?.classList.add('hidden');
    document.getElementById('searchOverlay')?.classList.remove('flex');
}
window.openSearch = openSearch;
window.closeSearch = closeSearch;

// ── Product-rail renderer used by index pages ────────────────
function renderProductRail(products, desktopGridId, mobileTrackId, dotsId) {
    const desktopGrid = document.getElementById(desktopGridId);
    const mobileTrack = document.getElementById(mobileTrackId);
    const dotsContainer = document.getElementById(dotsId);
    if (!desktopGrid || !window.renderRailCard) return;

    desktopGrid.innerHTML = products.map(p => window.renderRailCard(p)).join('');
    if (mobileTrack) {
        mobileTrack.innerHTML = products.map(p => `<div class="swipe-item">${window.renderRailCard(p)}</div>`).join('');
    }
    if (dotsContainer) {
        dotsContainer.innerHTML = products.map((_, i) => `<span class="swipe-dot ${i === 0 ? 'active' : ''}"></span>`).join('');
        mobileTrack?.addEventListener('scroll', () => {
            const idx = Math.round(mobileTrack.scrollLeft / mobileTrack.clientWidth);
            dotsContainer.querySelectorAll('.swipe-dot').forEach((d, i) => d.classList.toggle('active', i === idx));
        }, { passive: true });
    }
    document.querySelectorAll('.product-card').forEach(card => {
        if (window.initProductCardPicker) window.initProductCardPicker(card);
        revealIO.observe(card);
    });
}
window.renderProductRail = renderProductRail;

// ── Pull-to-refresh Indicator ──
(function () {
    const indicator = document.getElementById('pullIndicator');
    if (!indicator) return;
    let startY = 0, pulling = false;
    window.addEventListener('touchstart', e => {
        if (window.scrollY === 0) { startY = e.touches[0].clientY; pulling = true; }
    }, { passive: true });
    window.addEventListener('touchmove', e => {
        if (pulling && e.touches[0].clientY - startY > 60) indicator.classList.add('opacity-100');
    }, { passive: true });
    window.addEventListener('touchend', () => {
        setTimeout(() => indicator.classList.remove('opacity-100'), 600);
        pulling = false;
    });
})();

// ── Scrolling Announcement Band Auto-placement ──
function ensureScrollingBand() {
    if (!document.querySelector('.sld-ticker-container')) {
        const hero = document.querySelector('main > section:first-of-type, main > div:first-of-type, .hero-section, #heroSection');
        const template = document.getElementById('globalScrollingBandTemplate');
        if (hero && template) {
            hero.insertAdjacentHTML('afterend', template.innerHTML);
        }
    }
}

// ── Site Header Shadow on Scroll ──
window.addEventListener('scroll', () => {
    document.getElementById('siteHeader')?.classList.toggle('shadow-md', window.scrollY > 40);
}, { passive: true });

// ── Document Ready Listeners ──
document.addEventListener('DOMContentLoaded', () => {
    updateCartUI();
    document.querySelectorAll('.reveal:not(.in-view)').forEach(el => revealIO.observe(el));
    ensureScrollingBand();

    // Auto-open login modal if server validation failed (flagged via data attribute)
    const loginModal = document.getElementById('loginModal');
    if (loginModal && loginModal.dataset.autoOpen === 'true') {
        openLoginModal();
    }

    // AJAX Login Form interceptor
    if (loginModal) {
        const loginForm = loginModal.querySelector('form[action*="login"]');
        if (loginForm) {
            loginForm.addEventListener('submit', async function(e) {
                e.preventDefault();

                let alertEl = loginModal.querySelector('#loginModalAlert');
                let alertText = loginModal.querySelector('#loginModalAlertText');

                if (!alertEl) {
                    alertEl = document.createElement('div');
                    alertEl.id = 'loginModalAlert';
                    alertEl.className = 'p-3 rounded-xl mb-4 text-xs font-semibold bg-rose-50 border border-rose-200 text-rose-700';
                    loginForm.prepend(alertEl);
                    alertText = alertEl;
                }

                alertEl.classList.add('hidden');
                if (alertText) alertText.textContent = '';

                const submitBtn = loginForm.querySelector('button[type="submit"]');
                const origBtnHtml = submitBtn ? submitBtn.innerHTML : '';
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
                }

                try {
                    const formData = new FormData(loginForm);
                    const response = await fetch(loginForm.action, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: formData
                    });

                    const data = await response.json().catch(() => null);

                    if (response.ok && data) {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                            return;
                        }
                        window.location.reload();
                        return;
                    }

                    let errorMsg = 'The provided credentials do not match our records.';
                    if (data) {
                        if (data.errors) {
                            const firstKey = Object.keys(data.errors)[0];
                            if (firstKey && data.errors[firstKey]?.[0]) {
                                errorMsg = data.errors[firstKey][0];
                            }
                        } else if (data.message) {
                            errorMsg = data.message;
                        }
                    }

                    if (alertText) {
                        alertText.textContent = errorMsg;
                    } else {
                        alertEl.textContent = errorMsg;
                    }
                    alertEl.classList.remove('hidden');

                    const pwdInput = loginForm.querySelector('input[name="password"]');
                    if (pwdInput) {
                        pwdInput.value = '';
                        pwdInput.focus();
                    }
                } catch (err) {
                    loginForm.submit();
                    return;
                } finally {
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
                        submitBtn.innerHTML = origBtnHtml;
                    }
                }
            });
        }
    }

    // Live search input listener
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const q = this.value.trim();
            const res = document.getElementById('liveResults');
            if (!res) return;
            if (q.length < 2) { res.classList.add('hidden'); return; }
            fetch(`/search?q=${encodeURIComponent(q)}&ajax=1`)
                .then(r => r.json())
                .then(data => {
                    res.classList.remove('hidden');
                    res.innerHTML = data.length
                        ? data.map(p => `
                            <a href="${escHtml(p.url || ('/products/' + p.slug))}" class="flex items-center gap-3 p-2.5 rounded-xl transition-all hover:bg-black/5">
                                <img src="${escHtml(p.cover_image || p.image || '/images/placeholder.jpg')}" class="w-10 h-10 object-cover rounded-lg" alt="">
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-bold truncate">${escHtml(p.name)}</p>
                                    <p class="text-[11px] font-bold" style="color:var(--color-accent,#DB2777)">${window.formatPrice(p.price)}</p>
                                </div>
                            </a>
                        `).join('')
                        : `<p class="text-xs opacity-40 col-span-2 text-center py-4">No results found.</p>`;
                })
                .catch(() => {});
        });
    }
});

// Modal click outside & ESC key handling
document.addEventListener('click', function(e) {
    const m = document.getElementById('loginModal');
    if (m && e.target === m) {
        closeLoginModal();
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeLoginModal();
        closeSearch();
        closeCartDrawer();
        if (typeof closeQuickView === 'function') closeQuickView();
        if (typeof closeCategorySheet === 'function') closeCategorySheet();
    }
});

// Global Quick View Event Delegation (Works across all themes and cards)
document.addEventListener('click', function(e) {
    const btn = e.target.closest('[data-quick-view], .quick-view-btn, .btn-quick-view, [data-action="quick-view"]');
    if (!btn) return;
    e.preventDefault();
    e.stopPropagation();

    let product = null;
    if (btn.hasAttribute('data-product')) {
        try {
            const raw = btn.getAttribute('data-product');
            product = typeof raw === 'string' ? JSON.parse(raw) : raw;
        } catch(err) {}
    }
    if (!product) {
        const card = btn.closest('[data-product]');
        if (card) {
            try {
                const raw = card.getAttribute('data-product');
                product = typeof raw === 'string' ? JSON.parse(raw) : raw;
            } catch(err) {}
        }
    }
    if (product && typeof window.openQuickView === 'function') {
        window.openQuickView(product);
    }
});
