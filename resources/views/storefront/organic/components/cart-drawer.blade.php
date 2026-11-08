{{--
    ======================================================================
    THEME: VERDIA — Botanical Living & Organic Produce Marketplace
    COMPONENT: SLIDE-OVER CART DRAWER
    ======================================================================
    File: resources/views/storefront/organic/components/cart-drawer.blade.php
    Component Injection: storefront.organic.components.cart-drawer
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
<div id="cartDrawer" class="fixed top-0 right-0 h-full w-full sm:w-[420px] z-[200] translate-x-full transition-transform duration-300 flex flex-col justify-between shadow-2xl bg-white">
  
  {{-- Header --}}
  <div class="p-6 pb-4 border-b border-stone-200">
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-2.5">
        <div class="w-9 h-9 rounded-xl bg-organic-50 text-organic-600 flex items-center justify-center">
          <svg class="w-5 h-5" viewBox="0 0 24 24"><use xlink:href="#organic-cart"></use></svg>
        </div>
        <div>
          <h3 class="text-base font-extrabold text-stone-900 font-display">Your Fresh Basket</h3>
          <span class="text-[11px] text-stone-400 font-medium"><span class="cart-count">0</span> items selected</span>
        </div>
      </div>
      <button type="button" onclick="closeCartDrawer()" class="p-1.5 text-stone-400 hover:text-stone-700 rounded-full hover:bg-stone-100 transition cursor-pointer" aria-label="Close cart drawer">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>

    {{-- Free Shipping Meter --}}
    <div class="mt-4 p-3 bg-organic-50/70 border border-organic-200/80 rounded-2xl text-xs text-organic-900">
      <div class="flex items-center justify-between font-bold mb-1">
        <span>🌿 Free Farm Delivery</span>
        <span class="text-[10px] text-organic-600">Over ₹1,499</span>
      </div>
      <p class="text-[11px] text-organic-700 font-medium">Free express delivery applied on orders over ₹1,499.</p>
    </div>
  </div>

  {{-- Items Container --}}
  <div id="cartItems" class="flex-1 overflow-y-auto px-6 py-4 divide-y divide-stone-100">
    {{-- Populated by storefront-layout.blade.php renderCartItems() --}}
    <p class="text-center text-sm opacity-40 mt-10 px-4">Your basket is empty.</p>
  </div>

  {{-- Footer Actions --}}
  <div class="p-6 border-t border-stone-200 space-y-3 bg-white">
    <div class="flex items-center justify-between text-xs text-stone-500 font-semibold">
      <span>Delivery Guarantee</span>
      <span class="text-organic-700 font-bold">100% Chemical-Free</span>
    </div>

    <div class="flex items-center justify-between text-sm">
      <span class="font-bold text-stone-800">Subtotal:</span>
      <span id="cartDrawerSubtotal" class="font-extrabold text-base text-stone-900 cart-total-amount">₹0.00</span>
    </div>

    <div class="grid grid-cols-2 gap-3 pt-2">
      <a href="{{ route('cart') }}" class="w-full py-3 text-center rounded-xl border border-stone-300 hover:border-stone-400 text-stone-800 font-bold text-xs transition">
        View Full Basket
      </a>
      <a href="{{ route('checkout') }}" class="w-full py-3 text-center rounded-xl bg-organic-500 hover:bg-organic-600 text-white font-bold text-xs transition shadow-md shadow-organic-500/20">
        Checkout Now
      </a>
    </div>
  </div>

</div>

{{-- Backdrop Overlay --}}
<div id="cartOverlay" onclick="closeCartDrawer()" class="hidden fixed inset-0 bg-stone-900/60 backdrop-blur-xs z-[190] transition-opacity"></div>
