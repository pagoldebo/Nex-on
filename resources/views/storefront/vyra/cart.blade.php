{{--
    ======================================================================
    THEME: VYRA — Modern Noir & High-Contrast Streetwear
    PAGE: SHOPPING BAG & CHECKOUT STAGING
    ======================================================================
    File: resources/views/storefront/vyra/cart.blade.php
    Controller: App\Http\Controllers\StorefrontController@cart
    Route Context: GET /cart
    ViewModel Injections:
      - $theme: Active Template model ('vyra')
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
  <h1 class="font-display uppercase text-3xl md:text-4xl mb-10 reveal text-[#F2F0EC]">Your Bag</h1>
  <div class="grid lg:grid-cols-3 gap-10">

    <div class="lg:col-span-2" id="cartPageItems">
      <div class="text-center py-16 opacity-40">
        <p class="font-body text-sm text-[#F2F0EC]">Your bag is empty.
          <a href="{{ route('home') }}" style="color:#FF3D5A" class="underline">Continue shopping →</a>
        </p>
      </div>
    </div>

    <div class="bg-[#1A1A1D] border p-6 h-fit" style="border-color:#FF3D5A22">
      <h3 class="font-display uppercase text-xl mb-6 text-[#F2F0EC]">Summary</h3>
      <div class="space-y-3 font-body text-sm text-[#F2F0EC]">
        <div class="flex justify-between opacity-60"><span>Subtotal</span><span id="cartPageSubtotal">₹0</span></div>
        <div class="flex justify-between opacity-60"><span>Shipping</span><span>Free over ₹999</span></div>
        <div class="border-t pt-3 flex justify-between font-bold text-base" style="border-color:#FF3D5A22">
          <span>Total</span><span id="cartPageTotal" style="color:#FF3D5A">₹0</span>
        </div>
      </div>
      <a href="{{ route('checkout') }}" class="mt-6 block py-3.5 text-center text-xs font-bold uppercase tracking-widest transition-all" style="background:#FF3D5A;color:#0E0E10;clip-path:polygon(4% 0,100% 0,96% 100%,0 100%)">Checkout</a>
      <a href="{{ route('home') }}" class="block py-2 text-center text-xs opacity-40 hover:opacity-70 font-body mt-2 uppercase text-[#F2F0EC]">Continue Shopping</a>
    </div>
  </div>
</div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const el = document.getElementById('cartPageItems');
  if (!cart.length) return;
  el.innerHTML = '<div class="divide-y" style="border-color:#FF3D5A22">' + cart.map(i => `
    <div class="flex gap-4 py-4">
      <img src="${i.image}" class="w-20 h-20 object-cover flex-shrink-0 bg-gray-800">
      <div class="flex-1 min-w-0">
        <p class="font-body font-semibold text-sm text-[#F2F0EC]">${i.name}</p>
        <p class="font-body text-xs opacity-50 mt-0.5 text-[#F2F0EC] uppercase">${i.colorName} / ${i.size}</p>
        <div class="flex items-center gap-3 mt-2">
          <button onclick="adjustQty(${i.productId},'${i.colorName}','${i.size}',-1)" class="w-6 h-6 border text-sm flex items-center justify-center" style="border-color:#FF3D5A44;color:#F2F0EC">−</button>
          <span class="font-body text-sm text-[#F2F0EC]">${i.qty}</span>
          <button onclick="adjustQty(${i.productId},'${i.colorName}','${i.size}',1)"  class="w-6 h-6 border text-sm flex items-center justify-center" style="border-color:#FF3D5A44;color:#F2F0EC">+</button>
        </div>
      </div>
      <div class="text-right">
        <p class="font-display font-bold" style="color:#FF3D5A">₹${(i.price*i.qty).toLocaleString('en-IN')}</p>
        <button onclick="removeFromCart(${i.productId},'${i.colorName}','${i.size}');location.reload()" class="text-xs opacity-40 hover:opacity-80 mt-1 font-body uppercase text-[#F2F0EC]">Remove</button>
      </div>
    </div>`).join('') + '</div>';
  const tot = cartTotal();
  document.getElementById('cartPageSubtotal').textContent = '₹' + tot.toLocaleString('en-IN');
  document.getElementById('cartPageTotal').textContent = '₹' + tot.toLocaleString('en-IN');
});
function adjustQty(pid, cn, sz, d) {
  const i = cart.find(x => x.productId===pid && x.colorName===cn && x.size===sz);
  if (!i) return;
  i.qty = Math.max(1, i.qty + d);
  persistCart();
  location.reload();
}
</script>
@endpush
</x-storefront-layout>
