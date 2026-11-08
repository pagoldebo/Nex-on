{{--
    ======================================================================
    THEME: VERDIA — Botanical Living & Organic Produce Marketplace
    PAGE: SHOPPING BAG & CHECKOUT STAGING
    ======================================================================
    File: resources/views/storefront/organic/cart.blade.php
    Controller: App\Http\Controllers\StorefrontController@cart
    Route Context: GET /cart
    ViewModel Injections:
      - $theme: Active Template model ('organic')
    Responsibility: Dedicated shopping bag review, coupon redemption, and subtotal calculation
    Features:
      - Client-side reactive bag synchronized with sessionStorage.cart
      - Quantity steppers, line-item removal, and price recalculations
      - Promotional coupon input with immediate discount feedback
      - Estimated shipping, tax breakdown, and Proceed to Checkout CTA
    Operations & Maintenance:
      - Dispatches 'cart-updated' events to keep all header/drawer widgets synced
--}}
<x-storefront-layout :theme="$theme" title="Your Fresh Produce Basket">

  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
    
    {{-- Breadcrumbs --}}
    <nav class="text-xs text-stone-500 font-medium flex items-center gap-2 mb-6">
      <a href="{{ route('home') }}" class="hover:text-organic-600 transition">Home</a>
      <span>/</span>
      <span class="text-stone-900 font-bold">Shopping Basket</span>
    </nav>

    <div class="flex items-center justify-between pb-6 border-b border-stone-200 mb-8">
      <div>
        <h1 class="text-2xl sm:text-3xl font-black text-stone-900 font-display">Your Fresh Produce Basket</h1>
        <p class="text-xs sm:text-sm text-stone-500 mt-1 font-light">Carefully packed in eco-friendly biodegradable crates.</p>
      </div>
      <a href="{{ route('home') }}" class="hidden sm:inline-flex items-center gap-1.5 text-xs font-bold text-organic-700 hover:text-organic-900">
        <span>&larr; Continue Shopping</span>
      </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
      
      {{-- LEFT: Cart Items List --}}
      <div class="lg:col-span-8 space-y-6">
        
        {{-- Free Delivery Meter --}}
        <div class="p-4 bg-organic-50 rounded-2xl border border-organic-200/80 flex items-center justify-between text-xs text-organic-900">
          <div class="flex items-center gap-2.5">
            <span class="w-7 h-7 rounded-full bg-organic-500 text-white flex items-center justify-center font-bold text-xs shrink-0">🌿</span>
            <div>
              <p class="font-bold">Free Farm Delivery over ₹{{ number_format($freeShippingThreshold, 0) }}</p>
              <p class="text-[11px] text-organic-700 font-medium">Standard cold-chain delivery is applied otherwise.</p>
            </div>
          </div>
          <span class="font-black text-xs text-organic-800 hidden sm:inline">Fresh Harvest Guarantee</span>
        </div>

        {{-- Dynamic Cart Items Container --}}
        <div id="cartPageItems" class="bg-white rounded-3xl border border-stone-200 p-6 shadow-xs">
          <div class="text-center py-16 text-stone-400">
            <svg class="w-12 h-12 mx-auto mb-3 opacity-30" viewBox="0 0 24 24"><use xlink:href="#organic-cart"></use></svg>
            <p class="text-sm font-semibold text-stone-600">Your fresh produce basket is empty.</p>
            <p class="text-xs text-stone-400 mt-1">Explore our farm harvest and add wholesome vegetables, fruits, and staples.</p>
            <a href="{{ route('home') }}" class="btn-organic inline-block mt-4 px-6 py-2.5 rounded-full text-xs font-bold">
              Explore Farm Harvest
            </a>
          </div>
        </div>

      </div>

      {{-- RIGHT: Summary & Coupon Box --}}
      <div class="lg:col-span-4 space-y-6">
        
        {{-- Order Summary Card --}}
        <div class="bg-white rounded-3xl border border-stone-200 p-6 shadow-xs space-y-4">
          <h3 class="text-base font-black text-stone-900 font-display pb-3 border-b border-stone-100">
            Basket Summary
          </h3>

          <div class="space-y-2.5 text-xs text-stone-600">
            <div class="flex justify-between">
              <span>Items Subtotal:</span>
              <span id="cartPageSubtotal" class="font-bold text-stone-900">₹0.00</span>
            </div>
            <div class="flex justify-between">
              <span>Estimated Delivery:</span>
              <span id="cartPageShipping" class="font-bold text-organic-700">Calculated at Checkout</span>
            </div>
            <div id="discountRow" class="flex justify-between text-citrus hidden">
              <span>Coupon Discount:</span>
              <span id="cartPageDiscount" class="font-bold">-₹0.00</span>
            </div>
          </div>

          <div class="pt-3 border-t border-stone-100 flex justify-between items-baseline">
            <span class="text-sm font-black text-stone-900">Estimated Total:</span>
            <span id="cartPageTotal" class="text-2xl font-black text-organic-900 font-display">₹0.00</span>
          </div>

          <div class="pt-2">
            <a href="{{ route('checkout') }}" class="btn-organic block w-full py-3.5 text-center rounded-2xl font-bold text-xs uppercase tracking-wider shadow-md shadow-organic-500/20">
              Proceed to Secure Checkout
            </a>
          </div>

          <p class="text-[10px] text-center text-stone-400 pt-1">
            Tax included. Eco-friendly biodegradable packaging included.
          </p>
        </div>

        {{-- Farm Guarantees --}}
        <div class="p-5 bg-stone-50 rounded-2xl border border-stone-200/80 space-y-2.5 text-xs text-stone-600">
          <div class="flex items-center gap-2">
            <span class="text-organic-600 font-bold">✓</span>
            <span>Temperature monitored transit</span>
          </div>
          <div class="flex items-center gap-2">
            <span class="text-organic-600 font-bold">✓</span>
            <span>100% money-back freshness guarantee</span>
          </div>
          <div class="flex items-center gap-2">
            <span class="text-organic-600 font-bold">✓</span>
            <span>Safe online UPI, Cards &amp; Cash on Delivery</span>
          </div>
        </div>

      </div>

    </div>

  </div>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      renderFullCartPage();
    });

    function renderFullCartPage() {
      const container = document.getElementById('cartPageItems');
      if (!container) return;

      if (!cart || cart.length === 0) {
        return; // Empty state already rendered
      }

      container.innerHTML = `
        <div class="divide-y divide-stone-100">
          ${cart.map((item, idx) => `
            <div class="py-4 sm:py-5 flex items-center gap-4">
              <div class="w-18 h-18 sm:w-20 sm:h-20 rounded-2xl bg-stone-50 border border-stone-200 p-2 shrink-0 flex items-center justify-center overflow-hidden">
                <img src="${item.image}" alt="${item.name}" class="max-h-full max-w-full object-contain">
              </div>
              <div class="flex-1 min-w-0">
                <h4 class="text-xs sm:text-sm font-extrabold text-stone-900 truncate font-display">${item.name}</h4>
                <p class="text-[11px] text-stone-500 mt-0.5">Pack / Variety: <span class="font-semibold text-stone-700">${item.size} • ${item.colorName}</span></p>
                <p class="text-xs font-bold text-organic-700 mt-1">₹${item.price.toLocaleString('en-IN', { minimumFractionDigits: 2 })} each</p>
              </div>
              <div class="flex items-center gap-2">
                <div class="flex items-center border border-stone-200 rounded-xl bg-stone-50 px-2 py-1">
                  <button type="button" onclick="adjustPageQty(${item.productId}, '${item.colorName}', '${item.size}', -1)" class="w-6 h-6 hover:bg-stone-200 rounded text-stone-600 font-bold text-xs flex items-center justify-center">−</button>
                  <span class="w-8 text-center text-xs font-bold text-stone-800">${item.qty}</span>
                  <button type="button" onclick="adjustPageQty(${item.productId}, '${item.colorName}', '${item.size}', 1)" class="w-6 h-6 hover:bg-stone-200 rounded text-stone-600 font-bold text-xs flex items-center justify-center">+</button>
                </div>
              </div>
              <div class="text-right shrink-0 min-w-[70px]">
                <p class="text-sm font-black text-stone-900 font-display">₹${(item.price * item.qty).toLocaleString('en-IN', { minimumFractionDigits: 2 })}</p>
                <button type="button" onclick="removePageItem(${item.productId}, '${item.colorName}', '${item.size}')" class="text-[11px] text-rose-500 hover:text-rose-700 font-semibold mt-1">Remove</button>
              </div>
            </div>
          `).join('')}
        </div>
      `;

      const tot = cartTotal();
      const subtotalEl = document.getElementById('cartPageSubtotal');
      const totalEl = document.getElementById('cartPageTotal');
      if (subtotalEl) subtotalEl.textContent = '₹' + tot.toLocaleString('en-IN', { minimumFractionDigits: 2 });
      if (totalEl) totalEl.textContent = '₹' + tot.toLocaleString('en-IN', { minimumFractionDigits: 2 });
    }

    function adjustPageQty(pid, cn, sz, delta) {
      const item = cart.find(x => x.productId === pid && x.colorName === cn && x.size === sz);
      if (!item) return;
      item.qty = Math.max(1, item.qty + delta);
      persistCart();
      updateCartUI();
      renderFullCartPage();
    }

    function removePageItem(pid, cn, sz) {
      removeFromCart(pid, cn, sz);
      updateCartUI();
      renderFullCartPage();
      if (cart.length === 0) location.reload();
    }
  </script>

</x-storefront-layout>

