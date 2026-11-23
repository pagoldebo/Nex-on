/**
 * ======================================================================
 * THEME: BUTTERCUP — THEME CONFIG & CLIENT BEHAVIORS
 * Tailwind runtime tokens + Buttercup interactive widgets
 * ======================================================================
 */

// ── 1. Tailwind Runtime Configuration ──
tailwind.config = {
    theme: {
        extend: {
            colors: {
                blush: { DEFAULT: '#FBE7E4', deep: '#F3D2CD' },
                rose:  { DEFAULT: '#D89AA0', dark: '#B97680' },
                cream: '#FFFAF5',
                sage:  { DEFAULT: '#A8B89A', dark: '#7F9272' },
                ink:   '#4A3B3A',
            },
            fontFamily: {
                display: ['Fraunces', 'serif'],
                body: ['Quicksand', 'sans-serif'],
            },
            borderRadius: {
                '4xl': '2.5rem',
            },
        }
    }
};

// ── 2. Newsletter AJAX Handler ──
async function submitButtercupNewsletter(form) {
    const input = form.querySelector('input[name="email"]');
    const msg = form.querySelector('.buttercup-nl-msg');
    const btn = form.querySelector('button');
    if (!input || !input.value) return;
    btn.disabled = true;
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const res = await fetch('/newsletter/subscribe', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ email: input.value })
        });
        const data = await res.json();
        msg.textContent = data.message || 'Subscribed!';
        msg.classList.remove('hidden');
        input.value = '';
    } catch {
        msg.textContent = 'Error subscribing. Please try again.';
        msg.classList.remove('hidden');
    } finally {
        btn.disabled = false;
    }
}
window.submitButtercupNewsletter = submitButtercupNewsletter;

// ── 3. Account Tab & Order Management ──
function showTab(tab) {
    document.querySelectorAll('.acc-panel').forEach(p => p.classList.add('hidden'));
    document.getElementById('tab-' + tab)?.classList.remove('hidden');

    document.querySelectorAll('.acc-tab-btn').forEach(b => {
        b.classList.remove('bg-[#D89AA0]', 'text-[#FFFAF5]');
        b.classList.add('text-[#4A3B3A]', 'opacity-70');
    });

    const activeBtn = document.getElementById('tabBtn-' + tab);
    if (activeBtn) {
        activeBtn.classList.add('bg-[#D89AA0]', 'text-[#FFFAF5]');
        activeBtn.classList.remove('text-[#4A3B3A]', 'opacity-70');
    }
}
window.showTab = showTab;

async function submitCancel(orderId) {
    const reason = prompt('Please specify a reason for cancellation:');
    if (!reason) return;
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const res = await fetch(`/orders/${orderId}/request`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ type: 'cancel', reason: reason })
        });
        const data = await res.json();
        alert(data.message || 'Cancellation request processed.');
        window.location.reload();
    } catch {
        alert('Error submitting cancellation request.');
    }
}
window.submitCancel = submitCancel;

async function submitReturn(orderId) {
    const reason = prompt('Please specify reason for return / exchange (e.g. Size fit issue, fabric preference, damaged):');
    if (!reason) return;
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const res = await fetch(`/orders/${orderId}/request`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ type: 'return', reason: reason })
        });
        const data = await res.json();
        alert(data.message || 'Return request submitted.');
        window.location.reload();
    } catch {
        alert('Error submitting return request.');
    }
}
window.submitReturn = submitReturn;

async function saveAddress() {
    const btn = document.getElementById('saveAddrBtn');
    if (btn) {
        btn.disabled = true;
        btn.textContent = 'Saving...';
    }
    const payload = {
        name: document.getElementById('newAddrName')?.value,
        phone: document.getElementById('newAddrPhone')?.value,
        address1: document.getElementById('newAddr1')?.value,
        address2: document.getElementById('newAddr2')?.value || null,
        city: document.getElementById('newAddrCity')?.value,
        state: document.getElementById('newAddrState')?.value,
        pincode: document.getElementById('newAddrPin')?.value,
        is_default: document.getElementById('newAddrDefault')?.checked ? 1 : 0,
    };
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const res = await fetch('/account/addresses', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(payload)
        });
        const data = await res.json();
        if (data.success) {
            alert('Address saved successfully.');
            window.location.reload();
        } else {
            alert(data.message || 'Error saving address.');
        }
    } catch {
        alert('Network error saving address.');
    } finally {
        if (btn) {
            btn.disabled = false;
            btn.textContent = 'Save Address';
        }
    }
}
window.saveAddress = saveAddress;

async function removeAddress(id) {
    if (!confirm('Are you sure you want to remove this saved address?')) return;
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const res = await fetch(`/account/addresses/${id}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });
        const data = await res.json();
        if (data.success) {
            window.location.reload();
        } else {
            alert(data.message || 'Error deleting address.');
        }
    } catch {
        alert('Error deleting address.');
    }
}
window.removeAddress = removeAddress;

// ── 4. Wishlist Item Removal ──
function rmWish(id, btn) {
    let wl = JSON.parse(localStorage.getItem('lc_wishlist') || '[]');
    wl = wl.filter(x => x !== id);
    localStorage.setItem('lc_wishlist', JSON.stringify(wl));
    btn.closest('.relative')?.remove();
}
window.rmWish = rmWish;

// ── 5. Cart Page Adjust Quantity ──
function adjustQty(pid, cn, sz, d) {
    if (!window.cart) return;
    const i = window.cart.find(x => x.productId === pid && x.colorName === cn && x.size === sz);
    if (!i) return;
    i.qty = Math.max(1, i.qty + d);
    if (typeof persistCart === 'function') persistCart();
    location.reload();
}
window.adjustQty = adjustQty;

// ── 6. Buttercup Homepage Tab Filters & Widgets ──
document.addEventListener('DOMContentLoaded', () => {
    /* ── Sale Section tab filter ── */
    const saleTabs  = document.querySelectorAll('#saleFilterWrap .sale-tab');
    const saleCards = document.querySelectorAll('#saleGrid .sale-card');
    const saleGrid  = document.getElementById('saleGrid');

    if (saleTabs.length && saleCards.length) {
        saleTabs.forEach(tab => {
            tab.addEventListener('click', () => {
                saleTabs.forEach(t => {
                    t.classList.remove('active', 'border-[#D89AA0]', 'text-[#B97680]', 'bg-[#D89AA0]/15');
                    t.classList.add('border-[#D89AA0]/30', 'text-[#4A3B3A]/60');
                });
                tab.classList.add('active', 'border-[#D89AA0]', 'text-[#B97680]', 'bg-[#D89AA0]/15');
                tab.classList.remove('border-[#D89AA0]/30', 'text-[#4A3B3A]/60');

                const filter = tab.dataset.filter;
                let visibleCount = 0;
                saleCards.forEach(card => {
                    const cats = (card.dataset.category || '').split(/\s+/).filter(Boolean);
                    const match = filter === 'all' || cats.includes(filter);
                    card.style.display = match ? '' : 'none';
                    if (match) visibleCount++;
                });

                let emptyMsg = document.getElementById('saleEmptyMsg');
                if (visibleCount === 0) {
                    if (!emptyMsg && saleGrid) {
                        emptyMsg = document.createElement('div');
                        emptyMsg.id = 'saleEmptyMsg';
                        emptyMsg.className = 'col-span-full py-16 text-center text-[#4A3B3A]/60 font-body';
                        emptyMsg.innerHTML = '<p class="text-base font-semibold text-[#4A3B3A]/80 mb-1">No items found</p><p class="text-xs text-[#4A3B3A]/50">No promotional deals currently available in this collection.</p>';
                        saleGrid.appendChild(emptyMsg);
                    } else if (emptyMsg) {
                        emptyMsg.style.display = '';
                    }
                } else if (emptyMsg) {
                    emptyMsg.style.display = 'none';
                }
            });
        });
    }

    /* ── Best Sellers tab filter ── */
    const tabs   = document.querySelectorAll('.bs-tab');
    const cards  = document.querySelectorAll('.bs-card');
    const bsGrid = document.getElementById('bsGrid');

    if (tabs.length && cards.length) {
        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                tabs.forEach(t => {
                    t.classList.remove('active', 'border-[#D89AA0]', 'text-[#B97680]');
                    t.classList.add('border-[#D89AA0]/30', 'text-[#4A3B3A]/60');
                });
                tab.classList.add('active', 'border-[#D89AA0]', 'text-[#B97680]');
                tab.classList.remove('border-[#D89AA0]/30', 'text-[#4A3B3A]/60');

                const filter = tab.dataset.filter;
                let visibleCount = 0;
                cards.forEach(card => {
                    const cats = (card.dataset.category || '').split(/\s+/).filter(Boolean);
                    const match = filter === 'all' || cats.includes(filter);
                    card.style.display = match ? '' : 'none';
                    if (match) visibleCount++;
                });

                let emptyMsg = document.getElementById('bsEmptyMsg');
                if (visibleCount === 0) {
                    if (!emptyMsg && bsGrid) {
                        emptyMsg = document.createElement('div');
                        emptyMsg.id = 'bsEmptyMsg';
                        emptyMsg.className = 'col-span-full py-16 text-center text-[#4A3B3A]/60 font-body';
                        emptyMsg.innerHTML = '<p class="text-base font-semibold text-[#4A3B3A]/80 mb-1">No items found</p><p class="text-xs text-[#4A3B3A]/50">No best-selling items currently available in this collection.</p>';
                        bsGrid.appendChild(emptyMsg);
                    } else if (emptyMsg) {
                        emptyMsg.style.display = '';
                    }
                } else if (emptyMsg) {
                    emptyMsg.style.display = 'none';
                }
            });
        });

        document.querySelector('.bs-tab.active')?.classList.add('border-[#D89AA0]', 'text-[#B97680]');
        document.querySelector('.bs-tab.active')?.classList.remove('border-[#D89AA0]/30', 'text-[#4A3B3A]/60');
    }

    /* ── Cart Page Items Renderer ── */
    const cartPageEl = document.getElementById('cartPageItems');
    if (cartPageEl && window.cart && window.cart.length) {
        cartPageEl.innerHTML = '<div class="divide-y" style="border-color:#D89AA022">' + window.cart.map(i => `
            <div class="flex gap-4 py-4">
                <img src="${i.image}" class="w-20 h-20 object-cover rounded-2xl flex-shrink-0 bg-gray-100" alt="${i.name}">
                <div class="flex-1 min-w-0">
                    <p class="font-body font-semibold text-sm text-[#4A3B3A]">${i.name}</p>
                    <p class="font-body text-xs opacity-50 mt-0.5 text-[#4A3B3A]">${i.colorName} / ${i.size}</p>
                    <div class="flex items-center gap-3 mt-2">
                        <button onclick="adjustQty(${i.productId},'${i.colorName}','${i.size}',-1)" class="w-6 h-6 border rounded-full text-sm flex items-center justify-center" style="border-color:#D89AA044;color:#4A3B3A">−</button>
                        <span class="font-body text-sm text-[#4A3B3A]">${i.qty}</span>
                        <button onclick="adjustQty(${i.productId},'${i.colorName}','${i.size}',1)" class="w-6 h-6 border rounded-full text-sm flex items-center justify-center" style="border-color:#D89AA044;color:#4A3B3A">+</button>
                    </div>
                </div>
                <div class="text-right">
                    <p class="font-display font-bold" style="color:#D89AA0">₹${((Number(i.price) || 0) * (Number(i.qty) || 1)).toLocaleString('en-IN')}</p>
                    <button onclick="removeFromCart(${i.productId},'${i.colorName}','${i.size}');location.reload()" class="text-xs opacity-40 hover:opacity-80 mt-1 font-body text-[#4A3B3A]">Remove</button>
                </div>
            </div>`).join('') + '</div>';

        if (typeof cartTotal === 'function') {
            const tot = cartTotal();
            const sub = document.getElementById('cartPageSubtotal');
            const total = document.getElementById('cartPageTotal');
            if (sub) sub.textContent = '₹' + tot.toLocaleString('en-IN');
            if (total) total.textContent = '₹' + tot.toLocaleString('en-IN');
        }
    }

    /* ── Client-side Order Confirmation Hydration ── */
    const orderIdEl = document.getElementById('orderId');
    if (orderIdEl && (!orderIdEl.textContent || orderIdEl.textContent.trim() === '—' || orderIdEl.textContent.trim() === '')) {
        const o = JSON.parse(sessionStorage.getItem('lc_last_order') || 'null');
        if (o) {
            orderIdEl.textContent = o.id || o.reference || '—';
            const totalEl = document.getElementById('orderTotal');
            if (totalEl) totalEl.textContent = '₹' + Number(o.total || 0).toLocaleString('en-IN');
            const itemsEl = document.getElementById('orderItems');
            if (itemsEl && o.items?.length) {
                itemsEl.innerHTML = o.items.map(i =>
                    `<div class="flex justify-between opacity-80 py-1"><span>${i.name} (${i.colorName}/${i.size}) ×${i.qty}</span><span class="font-bold">₹${((Number(i.price) || 0) * (Number(i.qty) || 1)).toLocaleString('en-IN')}</span></div>`
                ).join('');
            }
        }
    }
});
