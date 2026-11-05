{{-- resources/views/storefront/buttercup/cart.blade.php --}}
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
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const el = document.getElementById('cartPageItems');
  if (!cart.length) return;
  el.innerHTML = '<div class="divide-y" style="border-color:#D89AA022">' + cart.map(i => `
    <div class="flex gap-4 py-4">
      <img src="${i.image}" class="w-20 h-20 object-cover rounded-2xl flex-shrink-0 bg-gray-100">
      <div class="flex-1 min-w-0">
        <p class="font-body font-semibold text-sm text-[#4A3B3A]">${i.name}</p>
        <p class="font-body text-xs opacity-50 mt-0.5 text-[#4A3B3A]">${i.colorName} / ${i.size}</p>
        <div class="flex items-center gap-3 mt-2">
          <button onclick="adjustQty(${i.productId},'${i.colorName}','${i.size}',-1)" class="w-6 h-6 border rounded-full text-sm flex items-center justify-center" style="border-color:#D89AA044;color:#4A3B3A">−</button>
          <span class="font-body text-sm text-[#4A3B3A]">${i.qty}</span>
          <button onclick="adjustQty(${i.productId},'${i.colorName}','${i.size}',1)"  class="w-6 h-6 border rounded-full text-sm flex items-center justify-center" style="border-color:#D89AA044;color:#4A3B3A">+</button>
        </div>
      </div>
      <div class="text-right">
        <p class="font-display font-bold" style="color:#D89AA0">₹${(i.price*i.qty).toLocaleString('en-IN')}</p>
        <button onclick="removeFromCart(${i.productId},'${i.colorName}','${i.size}');location.reload()" class="text-xs opacity-40 hover:opacity-80 mt-1 font-body text-[#4A3B3A]">Remove</button>
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
