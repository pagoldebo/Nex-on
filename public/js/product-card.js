// public/js/product-card.js
// Universal client-side swatch & variant picker across all storefront themes.
// Works seamlessly with markup produced by product-card.blade.php and sale-card.blade.php.

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.product-card').forEach(initProductCard);
});

function initProductCard(card) {
    if (!card || card._initialized) return;
    card._initialized = true;

    if (!card.dataset.product) return;
    let product;
    try {
        product = JSON.parse(card.dataset.product);
    } catch (e) {
        return;
    }

    let selectedColorIndex = 0;
    let selectedSize = null;

    const imgEl = card.querySelector('.product-card-img');
    const priceCurrentEl = card.querySelector('.price-current');
    const priceCompareEl = card.querySelector('.price-compare');
    const sizePillsEl = card.querySelector('.size-pills');
    const addBtn = card.querySelector('.add-to-cart-btn');
    const swatches = card.querySelectorAll('.color-swatch');

    // Theme-specific active-state classes, read from data attributes set in each theme's Blade partial
    const swatchActiveClasses   = (card.dataset.swatchActiveClass   || 'ring-2').trim().split(/\s+/).filter(Boolean);
    const swatchInactiveClasses = (card.dataset.swatchInactiveClass || 'opacity-60').trim().split(/\s+/).filter(Boolean);
    const pillActiveClasses     = (card.dataset.pillActiveClass     || 'bg-gray-900 text-white').trim().split(/\s+/).filter(Boolean);
    let   pillInactiveClasses   = (card.dataset.pillInactiveClass   || '').trim().split(/\s+/).filter(Boolean);
    const defaultPillBase       = card.dataset.pillBaseClass        || 'size-pill text-[10px] w-7 h-7 rounded-full border';

    // If no explicit inactive classes were provided, auto-extract conflicting color/border/bg classes from defaultPillBase
    if (!pillInactiveClasses.length) {
        pillInactiveClasses = defaultPillBase.split(/\s+/).filter(c => {
            if (/^text-(?!\[\d|xs|sm|base|lg|xl|2xl|3xl|left|right|center|justify)/.test(c)) return true;
            if (/^border-(?!\[\d|\d|t|b|l|r|x|y|solid|dashed|dotted|none|collapse)/.test(c)) return true;
            if (/^bg-(?!\[\d|none|transparent|auto|cover|contain)/.test(c)) return true;
            return false;
        });
    }

    // Filter base classes to ensure invariant structural classes without conflicting color/border overrides
    const basePillClasses = defaultPillBase.split(/\s+/).filter(c => !pillInactiveClasses.includes(c)).join(' ');

    function setBtnLabel(text, disabled = false) {
        if (!addBtn) return;
        addBtn.disabled = disabled;
        const span = addBtn.querySelector('span');
        if (span) {
            span.textContent = text;
        } else {
            addBtn.textContent = text;
        }
    }

    function renderSizesForColor(colorIndex) {
        const color = product.colors && product.colors[colorIndex];
        if (!color || !color.sizes || !color.sizes.length) {
            if (sizePillsEl) sizePillsEl.innerHTML = '';
            setBtnLabel(card.dataset.addToBagLabel || 'ADD TO BASKET', false);
            return;
        }

        selectedSize = null;
        setBtnLabel(card.dataset.selectSizeLabel || 'SELECT PACK / SIZE', false);

        if (sizePillsEl) {
            const initialPillClass = [basePillClasses, ...pillInactiveClasses].filter(Boolean).join(' ');
            sizePillsEl.innerHTML = color.sizes.map(s => {
                const stockClasses = s.inStock ? 'cursor-pointer' : 'opacity-30 cursor-not-allowed';
                return `
                    <button type="button"
                            class="${initialPillClass} ${stockClasses}"
                            data-size="${s.size}"
                            aria-selected="false"
                            ${s.inStock ? '' : 'disabled'}
                            title="${s.inStock ? s.size : s.size + ' (Out of stock)'}">${s.size}</button>
                `;
            }).join('');

            sizePillsEl.querySelectorAll('.size-pill').forEach(pill => {
                pill.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    selectSize(colorIndex, pill);
                });
            });

            // Auto-select the first in-stock variant so the button is active and ready to use
            const firstInStock = color.sizes.find(s => s.inStock);
            if (firstInStock) {
                const firstPill = sizePillsEl.querySelector(`[data-size="${firstInStock.size}"]`);
                if (firstPill) {
                    selectSize(colorIndex, firstPill);
                }
            }
        }
    }

    function selectColor(colorIndex) {
        selectedColorIndex = colorIndex;
        const color = product.colors && product.colors[colorIndex];
        if (!color) return;

        // Swap cover image if images exist
        if (imgEl && color.images && color.images.length) {
            imgEl.src = color.images[0];
        }

        swatches.forEach((sw, i) => {
            const isActive = i === colorIndex;
            swatchActiveClasses.forEach(c => c && sw.classList.toggle(c, isActive));
            swatchInactiveClasses.forEach(c => c && sw.classList.toggle(c, !isActive));
        });

        renderSizesForColor(colorIndex);
    }

    function selectSize(colorIndex, pillEl) {
        selectedSize = pillEl.dataset.size;
        const color = product.colors && product.colors[colorIndex];
        if (!color) return;
        const variant = color.sizes.find(s => s.size === selectedSize);
        if (!variant) return;

        if (sizePillsEl) {
            sizePillsEl.querySelectorAll('.size-pill').forEach(p => {
                const isActive = p === pillEl;
                p.classList.toggle('active', isActive);
                p.setAttribute('aria-selected', isActive ? 'true' : 'false');
                p.setAttribute('aria-pressed', isActive ? 'true' : 'false');

                if (isActive) {
                    pillInactiveClasses.forEach(c => c && p.classList.remove(c));
                    pillActiveClasses.forEach(c => c && p.classList.add(c));
                } else {
                    pillActiveClasses.forEach(c => c && p.classList.remove(c));
                    pillInactiveClasses.forEach(c => c && p.classList.add(c));
                }
            });
        }

        if (priceCurrentEl) {
            priceCurrentEl.textContent = `₹${Number(variant.price).toLocaleString('en-IN', { minimumFractionDigits: 2 })}`;
        }
        if (variant.compareAtPrice) {
            if (priceCompareEl) {
                priceCompareEl.textContent = `₹${Number(variant.compareAtPrice).toLocaleString('en-IN', { minimumFractionDigits: 2 })}`;
                priceCompareEl.classList.remove('hidden');
            }
        } else if (priceCompareEl) {
            priceCompareEl.classList.add('hidden');
        }

        const addLabel = card.dataset.addToBagLabel || 'ADD TO BASKET';
        const label = (variant.stock && variant.stock <= 3)
            ? `${addLabel} — ONLY ${variant.stock} LEFT`
            : addLabel;
        setBtnLabel(label, false);
    }

    swatches.forEach((sw, i) => {
        sw.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            selectColor(i);
        });
    });

    addBtn?.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();

        const color = product.colors && product.colors[selectedColorIndex];
        if (!color) return;

        if (!selectedSize) {
            // If user clicked while no size selected, try to select first in stock
            const inStock = color.sizes ? color.sizes.find(s => s.inStock) : null;
            if (inStock && sizePillsEl) {
                const pill = sizePillsEl.querySelector(`[data-size="${inStock.size}"]`);
                if (pill) {
                    selectSize(selectedColorIndex, pill);
                }
            } else {
                return;
            }
        }

        const variant = color.sizes.find(s => s.size === selectedSize) || color.sizes[0];
        if (!variant) return;

        if (typeof window.addToCart === 'function') {
            window.addToCart(
                product.id,
                product.name,
                color.name || 'Default',
                selectedSize || 'Standard',
                Number(variant.price || product.startingPrice || 0),
                (color.images && color.images.length) ? color.images[0] : (product.coverImage || ''),
                variant.variantId || variant.id || null,
                variant.sku || null
            );

            // Give subtle visual feedback on button
            const prevText = addBtn.innerHTML;
            addBtn.classList.add('scale-95');
            setTimeout(() => addBtn.classList.remove('scale-95'), 150);

            // Open cart drawer if available
            if (typeof window.openCartDrawer === 'function') {
                window.openCartDrawer();
            }
        }
    });

    // Initialize with first color
    if (product.colors && product.colors.length) {
        selectColor(0);
    }
}

window.initProductCard = initProductCard;
window.initProductCardPicker = initProductCard;
