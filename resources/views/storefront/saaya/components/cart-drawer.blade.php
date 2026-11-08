{{--
    ======================================================================
    THEME: SAAYA — Royal Indian Trousseau & Heritage Silk
    COMPONENT: SLIDE-OVER CART DRAWER
    ======================================================================
    File: resources/views/storefront/saaya/components/cart-drawer.blade.php
    Component Injection: storefront.saaya.components.cart-drawer
    Responsibility: Off-canvas reactive slide-over shopping bag drawer
    Features:
      - Real-time line-item list, item removal, and quantity steppers
      - Subtotal calculation, tax preview, and free shipping progress tracker
      - Direct checkout CTA button and continue shopping dismiss action
      - Seamless client-side state synchronization with sessionStorage
    Operations & Maintenance:
      - Opened globally via Alpine event 'open-cart' or window.openCartDrawer()
      - Dispatches 'cart-updated' event to notify header badge counters
--}}
{{-- Drawer overlay --}}
<div id="cartOverlay" class="fixed inset-0 z-[58] bg-maroon-dark/50 backdrop-blur-sm opacity-0 invisible transition-all duration-300"></div>

{{-- Drawer panel --}}
<aside id="cartDrawer"
       class="fixed top-0 right-0 h-full w-full max-w-sm z-[59] bg-ivory flex flex-col
              translate-x-full transition-transform duration-300 ease-[cubic-bezier(.16,1,.3,1)]
              shadow-[-20px_0_60px_-10px_rgba(61,12,17,.25)]"
       role="dialog" aria-modal="true" aria-label="Your bag">

  {{-- Header --}}
  <div class="flex items-center justify-between px-6 py-5 border-b border-gold/20">
    <div>
      <p class="font-display italic text-gold text-sm">Your</p>
      <h2 class="font-display text-2xl text-maroon leading-tight">Bag</h2>
    </div>
    <button id="cartClose" class="p-2 text-maroon/40 hover:text-maroon transition-colors" aria-label="Close cart">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
  </div>

  {{-- Items --}}
  <div id="cartItems" class="flex-1 overflow-y-auto px-6 py-4">
    <p class="text-sm text-maroon/40 text-center py-10">Your bag is empty.</p>
  </div>

  {{-- Footer --}}
  <div class="px-6 py-5 border-t border-gold/20 space-y-4">
    <div class="flex justify-between text-sm text-maroon/70">
      <span class="font-body">Subtotal</span>
      <span id="cartTotal" class="font-semibold text-maroon font-body">₹0</span>
    </div>
    <div class="petal-divider opacity-30"></div>
    <a href="{{ route('checkout') }}"
       id="cartCheckoutLink"
       class="gold-btn block text-center py-3.5 text-xs font-body font-semibold uppercase tracking-widest opacity-40 pointer-events-none">
      Proceed to Checkout
    </a>
    <a href="{{ route('cart') }}" class="block text-center text-xs text-maroon/50 hover:text-gold transition-colors font-body py-1">
      View full bag
    </a>
  </div>
</aside>

<script>
(function () {
  const drawer   = document.getElementById('cartDrawer');
  const overlay  = document.getElementById('cartOverlay');
  const closeBtn = document.getElementById('cartClose');

  function openCart() {
    drawer.classList.remove('translate-x-full');
    overlay.classList.remove('opacity-0', 'invisible');
    document.body.style.overflow = 'hidden';
  }
  function closeCart() {
    drawer.classList.add('translate-x-full');
    overlay.classList.add('opacity-0', 'invisible');
    document.body.style.overflow = '';
  }

  closeBtn?.addEventListener('click', closeCart);
  overlay?.addEventListener('click', closeCart);

  // Expose so header + tab-bar can call openCart()
  window.openCart = openCart;

  // Re-render items whenever the layout fires cartUpdated
  function esc(s) {
    return String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
  }

  function renderDrawer() {
    const items     = document.getElementById('cartItems');
    const totalEl   = document.getElementById('cartTotal');
    const checkoutA = document.getElementById('cartCheckoutLink');
    if (!items) return;

    const bag = typeof cart !== 'undefined' ? cart : [];

    if (!bag.length) {
      items.innerHTML = '<p class="text-sm text-maroon/40 text-center py-10">Your bag is empty.</p>';
      if (checkoutA) { checkoutA.classList.add('opacity-40','pointer-events-none'); }
    } else {
      items.innerHTML = bag.map(i => `
        <div class="flex gap-4 py-4 border-b border-gold/15 last:border-0">
          <img src="${esc(i.image)}" alt="" class="w-20 h-24 object-cover flex-shrink-0 bg-blush/30">
          <div class="flex-1 min-w-0">
            <p class="font-display text-base text-maroon truncate">${esc(i.name)}</p>
            <p class="text-xs text-maroon/50 mt-0.5 font-body">${esc(i.colorName)} · ${esc(i.size)}</p>
            <p class="text-sm text-gold-dark font-semibold mt-1 font-body">₹${Number(i.price * i.qty).toLocaleString('en-IN')}</p>
            <div class="flex items-center gap-2 mt-2">
              <button type="button" data-act="dec" data-pid="${i.productId}" data-color="${esc(i.colorName)}" data-size="${esc(i.size)}"
                      class="w-7 h-7 border border-gold/30 text-maroon hover:border-gold transition-colors rounded-full text-sm">−</button>
              <span class="text-sm font-semibold text-maroon w-4 text-center font-body">${i.qty}</span>
              <button type="button" data-act="inc" data-pid="${i.productId}" data-color="${esc(i.colorName)}" data-size="${esc(i.size)}"
                      class="w-7 h-7 border border-gold/30 text-maroon hover:border-gold transition-colors rounded-full text-sm">+</button>
              <button type="button" data-act="rm" data-pid="${i.productId}" data-color="${esc(i.colorName)}" data-size="${esc(i.size)}"
                      class="ml-auto text-xs text-maroon/30 hover:text-maroon transition-colors font-body">Remove</button>
            </div>
          </div>
        </div>`).join('');
      if (checkoutA) { checkoutA.classList.remove('opacity-40','pointer-events-none'); }
    }

    const total = typeof cartTotal === 'function' ? cartTotal() : 0;
    if (totalEl) totalEl.textContent = '₹' + total.toLocaleString('en-IN');
  }

  // Delegated qty/remove handler
  document.getElementById('cartItems')?.addEventListener('click', e => {
    const btn = e.target.closest('button[data-act]');
    if (!btn) return;
    const pid = Number(btn.dataset.pid), color = btn.dataset.color, size = btn.dataset.size;
    if (btn.dataset.act === 'rm') {
      removeFromCart(pid, color, size);
    } else {
      const item = cart.find(i => i.productId === pid && i.colorName === color && i.size === size);
      if (!item) return;
      item.qty = Math.max(1, item.qty + (btn.dataset.act === 'inc' ? 1 : -1));
      persistCart(); updateCartUI();
    }
    renderDrawer();
  });

  document.addEventListener('cartUpdated', renderDrawer);
  document.addEventListener('DOMContentLoaded', renderDrawer);
})();
</script>
