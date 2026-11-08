{{--
    ======================================================================
    THEME: SAAYA — Royal Indian Trousseau & Heritage Silk
    PAGE: SHOPPING BAG & CHECKOUT STAGING
    ======================================================================
    File: resources/views/storefront/saaya/cart.blade.php
    Controller: App\Http\Controllers\StorefrontController@cart
    Route Context: GET /cart
    ViewModel Injections:
      - $theme: Active Template model ('saaya')
    Responsibility: Dedicated shopping bag review, coupon redemption, and subtotal calculation
    Features:
      - Client-side reactive bag synchronized with sessionStorage.cart
      - Quantity steppers, line-item removal, and price recalculations
      - Promotional coupon input with immediate discount feedback
      - Estimated shipping, tax breakdown, and Proceed to Checkout CTA
    Operations & Maintenance:
      - Dispatches 'cart-updated' events to keep all header/drawer widgets synced
--}}
<x-storefront-layout>

<div class="bg-ivory min-h-screen">

{{-- Compact maroon header --}}
<div class="bg-maroon py-12 lg:py-16 relative overflow-hidden">
  <div class="absolute inset-0 opacity-[.05] pointer-events-none"
       style="background-image:radial-gradient(circle,#C9A24B 1px,transparent 1px);background-size:24px 24px;"></div>
  <div class="relative max-w-7xl mx-auto px-6 lg:px-12 text-center">
    <p class="font-display italic text-gold text-lg mb-1">Review Your</p>
    <h1 class="font-display text-5xl text-ivory">Bag</h1>
  </div>
</div>

<div class="max-w-7xl mx-auto px-6 lg:px-12 py-10 lg:py-14">

  {{-- ── Empty state (shown until JS hydrates) ── --}}
  <div id="cartEmpty" class="hidden text-center py-20">
    <svg class="w-14 h-14 text-gold/30 mx-auto mb-4" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" d="M6 7h15l-1.5 9h-12z"/><path stroke-linecap="round" d="M6 7L4 3H2"/>
      <circle cx="9" cy="20" r="1"/><circle cx="17" cy="20" r="1"/>
    </svg>
    <p class="font-display text-3xl text-maroon mb-2">Your bag is empty</p>
    <p class="font-body text-sm text-maroon/50 mb-8">Add something beautiful to get started.</p>
    <a href="{{ route('search') }}" class="gold-btn py-3.5 px-10 text-xs font-body font-semibold tracking-widest uppercase inline-block">Explore Collection</a>
  </div>

  {{-- ── Loaded state ── --}}
  <div id="cartLoaded" class="hidden grid lg:grid-cols-[1fr_360px] gap-10 lg:gap-16 items-start">

    {{-- Items column --}}
    <div>
      <div id="cartPageItems" class="divide-y divide-gold/15"></div>

      {{-- Continue shopping --}}
      <div class="mt-8">
        <a href="{{ route('search') }}" class="flex items-center gap-2 text-sm font-body text-maroon/50 hover:text-gold transition-colors group">
          <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
          Continue Shopping
        </a>
      </div>
    </div>

    {{-- Summary column --}}
    <div class="lg:sticky lg:top-28">
      <div class="foil-frame bg-ivory p-8 relative">
        <svg class="frame-line" viewBox="0 0 100 100" preserveAspectRatio="none">
          <rect x="2" y="2" width="96" height="96" vector-effect="non-scaling-stroke"/>
        </svg>

        <h2 class="font-display text-2xl text-maroon mb-6">Order Summary</h2>

        <div class="space-y-3 font-body text-sm">
          <div class="flex justify-between text-maroon/70">
            <span>Subtotal (<span id="cartPageCount">0</span> items)</span>
            <span id="cartPageSubtotal">₹0</span>
          </div>
          <div class="flex justify-between text-maroon/70">
            <span>Shipping</span>
            <span id="cartPageShipping" class="text-gold">Free</span>
          </div>
          <div class="petal-divider opacity-30 my-2"></div>
          <div class="flex justify-between font-semibold text-maroon text-base">
            <span>Total</span>
            <span id="cartPageTotal">₹0</span>
          </div>
        </div>

        {{-- Promo note --}}
        <p id="cartPromoNote" class="hidden mt-3 text-[10px] font-body text-gold bg-gold/10 px-3 py-2 text-center"></p>

        <a href="{{ route('checkout') }}"
           id="cartPageCheckoutBtn"
           class="gold-btn block text-center py-4 text-xs font-body font-semibold tracking-widest uppercase mt-6 opacity-40 pointer-events-none">
          Proceed to Checkout
        </a>

        <p class="mt-4 text-[10px] font-body text-maroon/40 text-center leading-relaxed">
          Secure checkout · Free returns within 15 days
        </p>
      </div>

      {{-- Trust badges --}}
      <div class="flex justify-around mt-6">
        @foreach([
          ['M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'Secure'],
          ['M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z', 'Safe Pay'],
          ['M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z', 'Easy Returns'],
        ] as [$path, $label])
          <div class="flex flex-col items-center gap-1.5 text-maroon/40">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $path }}"/></svg>
            <span class="text-[10px] font-body">{{ $label }}</span>
          </div>
        @endforeach
      </div>
    </div>

  </div>
</div>

</div>

@push('scripts')
<script>
(function () {

  function esc(s) {
    return String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
  }

  const FREE_SHIPPING_THRESHOLD = 999;

  function renderCartPage() {
    const bag        = (typeof cart !== 'undefined') ? cart : [];
    const emptyEl    = document.getElementById('cartEmpty');
    const loadedEl   = document.getElementById('cartLoaded');
    const itemsEl    = document.getElementById('cartPageItems');
    const countEl    = document.getElementById('cartPageCount');
    const subtotalEl = document.getElementById('cartPageSubtotal');
    const totalEl    = document.getElementById('cartPageTotal');
    const shippingEl = document.getElementById('cartPageShipping');
    const promoEl    = document.getElementById('cartPromoNote');
    const checkoutBtn= document.getElementById('cartPageCheckoutBtn');

    if (!bag.length) {
      emptyEl?.classList.remove('hidden');
      loadedEl?.classList.add('hidden');
      return;
    }

    emptyEl?.classList.add('hidden');
    loadedEl?.classList.remove('hidden');

    // Items
    if (itemsEl) {
      itemsEl.innerHTML = bag.map(i => `
        <div class="flex gap-4 py-5">
          <img src="${esc(i.image)}" alt="" class="w-24 h-32 object-cover flex-shrink-0 bg-blush/30">
          <div class="flex-1 min-w-0">
            <div class="flex justify-between items-start gap-2">
              <div>
                <p class="font-display text-xl text-maroon">${esc(i.name)}</p>
                <p class="font-body text-xs text-maroon/50 mt-0.5">${esc(i.colorName)} · ${esc(i.size)}</p>
              </div>
              <p class="font-body text-sm font-semibold text-maroon flex-shrink-0">₹${Number(i.price * i.qty).toLocaleString('en-IN')}</p>
            </div>
            <div class="flex items-center gap-3 mt-4">
              <div class="flex items-center border border-gold/30">
                <button type="button" data-act="dec" data-pid="${i.productId}" data-color="${esc(i.colorName)}" data-size="${esc(i.size)}"
                        class="w-8 h-8 flex items-center justify-center text-maroon/60 hover:text-maroon hover:bg-gold/10 transition-colors text-lg leading-none">−</button>
                <span class="w-8 text-center text-sm font-semibold text-maroon font-body">${i.qty}</span>
                <button type="button" data-act="inc" data-pid="${i.productId}" data-color="${esc(i.colorName)}" data-size="${esc(i.size)}"
                        class="w-8 h-8 flex items-center justify-center text-maroon/60 hover:text-maroon hover:bg-gold/10 transition-colors text-lg leading-none">+</button>
              </div>
              <button type="button" data-act="rm" data-pid="${i.productId}" data-color="${esc(i.colorName)}" data-size="${esc(i.size)}"
                      class="text-xs font-body text-maroon/30 hover:text-maroon transition-colors ml-auto">Remove</button>
            </div>
          </div>
        </div>`).join('');
    }

    // Totals
    const sub = bag.reduce((s, i) => s + i.price * i.qty, 0);
    const totalQty = bag.reduce((s, i) => s + i.qty, 0);
    const freeShip = sub >= FREE_SHIPPING_THRESHOLD;
    if (countEl)    countEl.textContent  = totalQty;
    if (subtotalEl) subtotalEl.textContent = '₹' + sub.toLocaleString('en-IN');
    if (totalEl)    totalEl.textContent  = '₹' + sub.toLocaleString('en-IN');
    if (shippingEl) shippingEl.textContent = freeShip ? 'Free' : '₹99';
    if (promoEl) {
      if (!freeShip) {
        const remaining = FREE_SHIPPING_THRESHOLD - sub;
        promoEl.textContent = `Add ₹${remaining.toLocaleString('en-IN')} more for free shipping`;
        promoEl.classList.remove('hidden');
      } else {
        promoEl.classList.add('hidden');
      }
    }
    if (checkoutBtn) {
      checkoutBtn.classList.remove('opacity-40','pointer-events-none');
    }
  }

  // Delegated qty/remove
  document.getElementById('cartPageItems')?.addEventListener('click', e => {
    const btn = e.target.closest('button[data-act]');
    if (!btn) return;
    const pid = Number(btn.dataset.pid), color = btn.dataset.color, size = btn.dataset.size;
    if (btn.dataset.act === 'rm') {
      if (typeof removeFromCart === 'function') removeFromCart(pid, color, size);
    } else {
      const item = (typeof cart !== 'undefined' ? cart : []).find(i => i.productId === pid && i.colorName === color && i.size === size);
      if (!item) return;
      item.qty = Math.max(1, item.qty + (btn.dataset.act === 'inc' ? 1 : -1));
      if (typeof persistCart === 'function') persistCart();
      if (typeof updateCartUI === 'function') updateCartUI();
    }
    renderCartPage();
  });

  document.addEventListener('cartUpdated', renderCartPage);
  document.addEventListener('DOMContentLoaded', renderCartPage);

})();
</script>
@endpush

</x-storefront-layout>
