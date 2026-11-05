{{-- resources/views/storefront/lumiere/cart.blade.php
     The cart lives in sessionStorage (global `cart` from storefront-layout); this page renders it client-side. --}}
<x-storefront-layout :theme="$theme" title="Your Bag">

<div class="max-w-6xl mx-auto px-6 md:px-12 py-10">
  <h1 class="font-display italic text-3xl md:text-4xl text-gray-900 mb-10 reveal">Your Bag</h1>

  <div class="grid lg:grid-cols-3 gap-10">

    {{-- Items (2/3) --}}
    <div class="lg:col-span-2">
      <div id="cartPageItems">
        <div class="text-center py-16">
          <p class="text-sm text-gray-400">Your bag is empty.</p>
          <a href="{{ route('home') }}" class="inline-block mt-5 px-6 py-3 rounded-2xl text-xs font-bold uppercase tracking-widest bg-gray-900 text-white hover:bg-pink-600 transition-colors">Continue Shopping</a>
        </div>
      </div>
    </div>

    {{-- Summary (1/3) --}}
    <div class="bg-white rounded-[28px] border border-pink-50 p-6 h-fit lg:sticky lg:top-28 shadow-sm shadow-pink-50">
      <h3 class="font-display italic text-xl text-gray-900 mb-6">Order Summary</h3>
      <div class="space-y-3 text-sm text-gray-600">
        <div class="flex justify-between"><span>Subtotal</span><span id="cartPageSubtotal" class="font-semibold text-gray-900">₹0</span></div>
        <div class="flex justify-between"><span>Shipping</span><span class="text-gray-400">Free over ₹1,999</span></div>
        <div class="border-t border-pink-50 pt-4 flex justify-between text-base font-bold text-gray-900">
          <span>Total</span><span id="cartPageTotal" class="text-pink-600">₹0</span>
        </div>
      </div>
      <a id="cartCheckoutBtn" href="{{ route('checkout') }}"
         class="mt-6 block py-3.5 rounded-2xl text-center text-xs font-bold uppercase tracking-widest bg-gray-900 text-white hover:bg-pink-600 transition-colors">Checkout</a>
      <a href="{{ route('home') }}" class="block text-center text-xs text-gray-400 hover:text-gray-700 transition-colors mt-3 py-2">Continue Shopping</a>
    </div>
  </div>
</div>

@push('scripts')
<script>
function cpEsc(s) {
    return String(s).replace(/[&<>"']/g, ch => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[ch]));
}

function renderCartPage() {
    const wrap = document.getElementById('cartPageItems');
    const checkout = document.getElementById('cartCheckoutBtn');
    if (!cart.length) {
        wrap.innerHTML = `
            <div class="text-center py-16">
                <p class="text-sm text-gray-400">Your bag is empty.</p>
                <a href="{{ route('home') }}" class="inline-block mt-5 px-6 py-3 rounded-2xl text-xs font-bold uppercase tracking-widest bg-gray-900 text-white hover:bg-pink-600 transition-colors">Continue Shopping</a>
            </div>`;
        checkout.classList.add('opacity-40', 'pointer-events-none');
    } else {
        checkout.classList.remove('opacity-40', 'pointer-events-none');
        wrap.innerHTML = '<div class="divide-y divide-pink-50">' + cart.map(i => `
            <div class="flex gap-4 py-5">
                <img src="${cpEsc(i.image)}" alt="" class="w-24 h-28 object-cover rounded-2xl flex-shrink-0 bg-pink-50">
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-gray-900 truncate">${cpEsc(i.name)}</p>
                    <p class="text-xs text-gray-400 mt-0.5">${cpEsc(i.colorName)} / ${cpEsc(i.size)}</p>
                    <p class="text-sm text-gray-500 mt-1">₹${Number(i.price).toLocaleString('en-IN')} each</p>
                    <div class="flex items-center gap-3 mt-3">
                        <button type="button" data-act="dec" data-pid="${i.productId}" data-color="${cpEsc(i.colorName)}" data-size="${cpEsc(i.size)}"
                                class="w-8 h-8 rounded-full border border-pink-100 text-gray-600 hover:border-pink-400 transition-colors" aria-label="Decrease quantity">−</button>
                        <span class="w-5 text-center text-sm font-semibold text-gray-900">${i.qty}</span>
                        <button type="button" data-act="inc" data-pid="${i.productId}" data-color="${cpEsc(i.colorName)}" data-size="${cpEsc(i.size)}"
                                class="w-8 h-8 rounded-full border border-pink-100 text-gray-600 hover:border-pink-400 transition-colors" aria-label="Increase quantity">+</button>
                    </div>
                </div>
                <div class="text-right flex flex-col justify-between">
                    <p class="font-bold text-pink-600">₹${(i.price * i.qty).toLocaleString('en-IN')}</p>
                    <button type="button" data-act="rm" data-pid="${i.productId}" data-color="${cpEsc(i.colorName)}" data-size="${cpEsc(i.size)}"
                            class="text-xs text-gray-300 hover:text-red-400 transition-colors">Remove</button>
                </div>
            </div>`).join('') + '</div>';
    }
    const total = cartTotal();
    document.getElementById('cartPageSubtotal').textContent = '₹' + total.toLocaleString('en-IN');
    document.getElementById('cartPageTotal').textContent = '₹' + total.toLocaleString('en-IN');
}

// One delegated handler: values come from data attributes, so quotes in names can't break anything.
document.getElementById('cartPageItems').addEventListener('click', e => {
    const btn = e.target.closest('button[data-act]');
    if (!btn) return;
    const pid = Number(btn.dataset.pid), color = btn.dataset.color, size = btn.dataset.size;
    if (btn.dataset.act === 'rm') {
        removeFromCart(pid, color, size);
    } else {
        const item = cart.find(i => i.productId === pid && i.colorName === color && i.size === size);
        if (!item) return;
        item.qty = Math.max(1, item.qty + (btn.dataset.act === 'inc' ? 1 : -1));
        persistCart();
        updateCartUI();
    }
    renderCartPage();
});

document.addEventListener('DOMContentLoaded', renderCartPage);
</script>
@endpush

</x-storefront-layout>
