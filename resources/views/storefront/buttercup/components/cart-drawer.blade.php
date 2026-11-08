{{--
    ======================================================================
    THEME: BUTTERCUP — Pastel Comfort Intimates & Loungewear
    COMPONENT: SLIDE-OVER CART DRAWER
    ======================================================================
    File: resources/views/storefront/buttercup/components/cart-drawer.blade.php
    Component Injection: storefront.buttercup.components.cart-drawer
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
<div id="cartDrawer" class="fixed top-0 right-0 h-full w-full sm:w-[400px] z-[200] translate-x-full transition-transform duration-400 flex flex-col shadow-2xl" style="background:#FFFAF5;">
    <div class="flex items-center justify-between px-6 py-5 border-b" style="border-color:#D89AA033;">
        <h3 class="font-semibold text-lg" style="color:#4A3B3A">Your Bag</h3>
        <button onclick="closeCartDrawer()" style="color:#4A3B3A" class="text-2xl font-light hover:opacity-70 transition-opacity" aria-label="Close cart">&times;</button>
    </div>
    <div id="cartItems" class="flex-1 overflow-y-auto px-6 py-4" style="color:#4A3B3A">
        <p class="text-center text-sm opacity-40 mt-10">Your bag is empty.</p>
    </div>
    <div class="px-6 py-5 border-t" style="border-color:#D89AA033;">
        <div class="flex justify-between items-center mb-4">
            <span class="text-xs uppercase tracking-widest font-medium opacity-50" style="color:#4A3B3A">Total</span>
            <span id="cartTotal" class="text-lg font-bold" style="color:#D89AA0">₹0</span>
        </div>
        <a href="{{ route('checkout') }}" class="block w-full py-3.5 text-center text-xs font-bold uppercase tracking-widest rounded-2xl transition-colors bg-[#D89AA0] text-[#FFFAF5] hover:opacity-90">Checkout</a>
        <a href="{{ route('cart') }}" class="block w-full py-3 text-center text-xs font-medium mt-2 opacity-50 hover:opacity-80 transition-opacity" style="color:#4A3B3A">View Full Cart</a>
    </div>
</div>
<div id="cartOverlay" onclick="closeCartDrawer()" class="hidden fixed inset-0 bg-black/40 z-[190]"></div>
