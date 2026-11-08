{{-- resources/views/storefront/lumiere/order-confirmation.blade.php
     Data from StorefrontController@orderConfirmation: $order (the id string from the URL).
     DEMO FLOW: details come from sessionStorage ('lc_last_order', written by checkout). It is kept for the
     tab session so a refresh still works, and only shown if its id matches the URL. With a real Order model,
     load the order server-side and render it here instead. --}}
<x-storefront-layout :theme="$theme" title="Order Confirmed">

<div class="max-w-2xl mx-auto px-6 md:px-12 py-16">

  {{-- Confirmed --}}
  <div id="ocDetails" class="hidden">
    <div class="text-center">
      <div class="w-20 h-20 rounded-full bg-pink-50 flex items-center justify-center mx-auto mb-6">
        <svg class="w-10 h-10 text-pink-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
      </div>
      <h1 id="ocThanks" class="font-display italic text-4xl md:text-5xl text-gray-900 mb-3 reveal">Thank you</h1>
      <p class="text-gray-500 mb-1">We've received your order.</p>
      <p class="text-sm text-gray-400 mb-10">Order number <span class="font-bold text-pink-600">{{ $order }}</span></p>
    </div>

    <div class="bg-white rounded-[28px] border border-pink-50 p-6 mb-5 shadow-sm shadow-pink-50">
      <h2 class="font-display italic text-lg text-gray-900 mb-4">Your pieces</h2>
      <div id="ocItems" class="divide-y divide-pink-50 text-sm text-gray-700"></div>
      <div class="border-t border-pink-50 mt-2 pt-4 flex justify-between font-bold text-gray-900">
        <span>Total</span><span id="ocTotal" class="text-pink-600">₹0</span>
      </div>
    </div>

    <div class="grid sm:grid-cols-2 gap-5 mb-10">
      <div class="bg-white rounded-[28px] border border-pink-50 p-6 shadow-sm shadow-pink-50">
        <h2 class="font-display italic text-lg text-gray-900 mb-3">Delivering to</h2>
        <address id="ocShipping" class="not-italic text-sm text-gray-600 leading-relaxed"></address>
      </div>
      <div class="bg-white rounded-[28px] border border-pink-50 p-6 shadow-sm shadow-pink-50">
        <h2 class="font-display italic text-lg text-gray-900 mb-3">Payment</h2>
        <p id="ocPayment" class="text-sm font-semibold text-gray-800"></p>
        <p id="ocPaymentNote" class="text-xs text-gray-400 mt-1"></p>
      </div>
    </div>

    <div class="flex flex-wrap justify-center gap-4">
      <a href="{{ route('home') }}" class="px-8 py-3.5 rounded-2xl text-xs font-bold uppercase tracking-widest bg-[#405549] text-white hover:bg-pink-600 transition-colors">Continue Shopping</a>
      @auth
        <a href="{{ route('account') }}" class="px-8 py-3.5 rounded-2xl text-xs font-bold uppercase tracking-widest border border-pink-200 text-gray-700 hover:border-pink-400 transition-colors">My Orders</a>
      @endauth
      <button type="button" onclick="window.print()" class="px-8 py-3.5 rounded-2xl text-xs font-bold uppercase tracking-widest border border-pink-100 text-gray-400 hover:text-gray-700 hover:border-pink-300 transition-colors">Print</button>
    </div>
  </div>

  {{-- Shown when this browser has no matching order (opened in a new tab or browser, or session ended) --}}
  <div id="ocMissing" class="text-center py-10">
    <div class="w-16 h-16 rounded-full bg-pink-50 flex items-center justify-center mx-auto mb-5">
      <svg class="w-8 h-8 text-pink-300" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
    </div>
    <h1 class="font-display italic text-3xl text-gray-900 mb-3">Order {{ $order }}</h1>
    <p class="text-sm text-gray-400 max-w-sm mx-auto mb-8">We can't show the details of this order in this browser session.</p>
    <div class="flex flex-wrap justify-center gap-4">
      <a href="{{ route('home') }}" class="px-8 py-3.5 rounded-2xl text-xs font-bold uppercase tracking-widest bg-[#405549] text-white hover:bg-pink-600 transition-colors">Continue Shopping</a>
      @auth
        <a href="{{ route('account') }}" class="px-8 py-3.5 rounded-2xl text-xs font-bold uppercase tracking-widest border border-pink-200 text-gray-700 hover:border-pink-400 transition-colors">My Orders</a>
      @endauth
    </div>
  </div>
</div>

@push('scripts')
<script>
function ocEsc(s) {
    return String(s ?? '').replace(/[&<>"']/g, ch => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[ch]));
}

document.addEventListener('DOMContentLoaded', () => {
    const serverOrder = @json(isset($orderModel) && $orderModel ? [
        'id'       => (string) $orderModel->id,
        'reference'=> $orderModel->reference,
        'items'    => $orderModel->items->map(fn($i) => [
            'name'      => $i->product_name,
            'colorName' => $i->color_name,
            'size'      => $i->size,
            'qty'       => $i->qty,
            'price'     => $i->price,
            'image'     => $i->cover_image,
        ]),
        'total'    => $orderModel->total,
        'payment'  => $orderModel->payment_method === 'cod' ? 'Cash on Delivery' : ($orderModel->payment_method === 'upi' ? 'UPI' : 'Card'),
        'shipping' => [
            'name'     => $orderModel->shipping_name,
            'phone'    => $orderModel->shipping_phone,
            'address'  => $orderModel->shipping_address1 . ($orderModel->shipping_address2 ? ', ' . $orderModel->shipping_address2 : ''),
            'city'     => $orderModel->shipping_city,
            'state'    => $orderModel->shipping_state,
            'pincode'  => $orderModel->shipping_pincode,
        ],
    ] : null);

    let o = serverOrder;
    if (!o) {
        try { o = JSON.parse(sessionStorage.getItem('lc_last_order') || 'null'); } catch (e) {}
    }

    if (!o) return;

    const rupees = n => '₹' + Number(n).toLocaleString('en-IN');
    const s = o.shipping || {};

    document.getElementById('ocThanks').textContent = s.name ? 'Thank you, ' + s.name.trim().split(/\s+/)[0] : 'Thank you';
    document.getElementById('ocItems').innerHTML = (o.items || []).map(i => `
        <div class="flex gap-3 py-3">
            <img src="${ocEsc(i.image)}" alt="" class="w-14 h-16 object-cover rounded-xl bg-pink-50 flex-shrink-0">
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-gray-900 truncate">${ocEsc(i.name)}</p>
                <p class="text-xs text-gray-400">${ocEsc(i.colorName)} / ${ocEsc(i.size)} × ${Number(i.qty)}</p>
            </div>
            <p class="font-semibold text-gray-900">${rupees(i.price * i.qty)}</p>
        </div>`).join('');
    document.getElementById('ocTotal').textContent = rupees(o.total);

    document.getElementById('ocShipping').innerHTML = [
        s.name, s.address, [s.city, s.state].filter(Boolean).join(', ') + (s.pincode ? ' – ' + s.pincode : ''), s.phone ? 'Mobile: ' + s.phone : ''
    ].filter(Boolean).map(ocEsc).join('<br>');

    document.getElementById('ocPayment').textContent = o.payment || '';
    document.getElementById('ocPaymentNote').textContent =
        o.payment === 'Cash on Delivery' ? 'Pay ' + rupees(o.total) + ' when your order arrives.' : '';

    document.getElementById('ocMissing').classList.add('hidden');
    document.getElementById('ocDetails').classList.remove('hidden');
});
</script>
@endpush

</x-storefront-layout>
