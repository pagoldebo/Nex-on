{{--
    ======================================================================
    THEME: BUTTERCUP — Pastel Comfort Intimates & Loungewear
    PAGE: SHOPPING BAG & CHECKOUT STAGING
    ======================================================================
    File: resources/views/storefront/buttercup/cart.blade.php
    Controller: App\Http\Controllers\StorefrontController@cart
    Route Context: GET /cart
    ViewModel Injections:
      - $theme: Active Template model ('buttercup')
    Responsibility: Dedicated shopping bag review, coupon redemption, and subtotal calculation
    Features:
      - Client-side reactive bag synchronized with sessionStorage.cart
      - Quantity steppers, line-item removal, and price recalculations
      - Promotional coupon input with immediate discount feedback
      - Estimated shipping, tax breakdown, and Proceed to Checkout CTA
    Operations & Maintenance:
      - Dispatches 'cart-updated' events to keep all header/drawer widgets synced
--}}
<x-storefront-layout :theme="$theme" title="Your Bag">
<div class="max-w-5xl mx-auto px-6 lg:px-10 py-10">
  <h1 class="font-display text-3xl md:text-4xl mb-10 reveal text-[#4A3B3A]">Your Bag</h1>
  <div class="grid lg:grid-cols-3 gap-10">

    <div class="lg:col-span-2" id="cartPageItems">
      <div class="text-center py-16 opacity-40">
        <p class="font-body text-sm text-[#4A3B3A]">Your bag is empty.
          <a href="{{ route('home') }}" style="color:#D89AA0" class="underline">Continue shopping →</a>
        </p>
      </div>
    </div>

    <div class="bg-[#FFFAF5] rounded-[28px] border p-6 h-fit" style="border-color:#D89AA022">
      <h3 class="font-display text-xl mb-6 text-[#4A3B3A]">Summary</h3>
      <div class="space-y-3 font-body text-sm text-[#4A3B3A]">
        <div class="flex justify-between opacity-60"><span>Subtotal</span><span id="cartPageSubtotal">₹0</span></div>
        <div class="flex justify-between opacity-60"><span>Shipping</span><span>Free over ₹1,999</span></div>
        <div class="border-t pt-3 flex justify-between font-bold text-base" style="border-color:#D89AA022">
          <span>Total</span><span id="cartPageTotal" style="color:#D89AA0">₹0</span>
        </div>
      </div>
      <a href="{{ route('checkout') }}" class="mt-6 block py-3.5 text-center text-xs font-bold uppercase tracking-widest transition-all bg-[#D89AA0] text-[#FFFAF5] hover:bg-[#B97680] rounded-full">Checkout</a>
      <a href="{{ route('home') }}" class="block py-2 text-center text-xs opacity-40 hover:opacity-70 font-body mt-2 text-[#4A3B3A]">Continue Shopping</a>
    </div>
  </div>
</div>
</x-storefront-layout>
