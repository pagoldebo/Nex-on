{{--
    ======================================================================
    THEME: SAAYA — Royal Indian Trousseau & Heritage Silk
    PAGE: ORDER RECEIPT & FULFILLMENT TRACKER
    ======================================================================
    File: resources/views/storefront/saaya/order-confirmation.blade.php
    Controller: App\Http\Controllers\StorefrontController@orderConfirmation
    Route Context: GET /order-confirmation/{order}
    ViewModel Injections:
      - $theme: Active Template model ('saaya')
      - $order: Confirmed Order model with items and customer relation
    Responsibility: Post-purchase order summary, payment confirmation, and dispatch timeline
    Features:
      - Confirmed order number, receipt date, and payment status badge
      - Itemized product table with unit prices, discounts, taxes, and final total
      - Shipping address and delivery method details
      - Multi-stage fulfillment timeline (Placed -> Packed -> Out for Delivery -> Delivered)
    Operations & Maintenance:
      - Strict BOLA/RLS security: Accessible only by order owner or verified guest session
--}}
<x-storefront-layout>

<div class="bg-ivory min-h-screen">

{{-- Maroon header --}}
<div class="bg-maroon py-14 lg:py-20 relative overflow-hidden">
  <div class="absolute inset-0 opacity-[.05] pointer-events-none"
       style="background-image:radial-gradient(circle,#C9A24B 1px,transparent 1px);background-size:24px 24px;"></div>
  <div class="relative max-w-7xl mx-auto px-6 lg:px-12 text-center">
    <p class="font-display italic text-gold text-xl mb-2">Thank You</p>
    <h1 class="font-display text-5xl lg:text-6xl text-ivory" id="ocHeading">Your Order is Placed</h1>
  </div>
</div>

<div class="max-w-3xl mx-auto px-6 lg:px-12 py-12 lg:py-16">

  {{-- Order details (shown when order matches) --}}
  <div id="ocDetails" class="hidden">

    {{-- Foil-frame confirmation card --}}
    <div class="foil-frame bg-ivory p-8 lg:p-10 relative text-center mb-10">
      <svg class="frame-line" viewBox="0 0 100 100" preserveAspectRatio="none">
        <rect x="2" y="2" width="96" height="96" vector-effect="non-scaling-stroke"/>
      </svg>

      {{-- Gold checkmark --}}
      <div class="w-16 h-16 rounded-full border-2 border-gold flex items-center justify-center mx-auto mb-5">
        <svg class="w-8 h-8 text-gold" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
        </svg>
      </div>

      <h2 class="font-display text-3xl text-maroon mb-1" id="ocThankYou">Thank you!</h2>
      <p class="font-body text-maroon/60 text-sm mb-4" id="ocConfirmLine">Your order has been confirmed.</p>
      <div class="inline-block bg-gold/10 border border-gold/30 px-6 py-2 mb-2">
        <p class="font-body text-[10px] uppercase tracking-widest text-gold-dark">Order ID</p>
        <p class="font-display text-xl text-maroon" id="ocOrderId">—</p>
      </div>
      <p class="font-body text-xs text-maroon/40 mt-2" id="ocPlacedAt"></p>
    </div>

    {{-- Two-column: items + shipping --}}
    <div class="grid md:grid-cols-2 gap-8 mb-10">

      {{-- Items --}}
      <div>
        <h3 class="font-display text-xl text-maroon mb-4">Items Ordered</h3>
        <div id="ocItems" class="divide-y divide-gold/15"></div>
        <div class="border-t border-gold/20 mt-4 pt-4 space-y-1.5 font-body text-sm">
          <div class="flex justify-between text-maroon/60"><span>Subtotal</span><span id="ocSubtotal">—</span></div>
          <div class="flex justify-between text-maroon/60"><span>Shipping</span><span id="ocShipping">—</span></div>
          <div class="flex justify-between font-semibold text-maroon text-base mt-1"><span>Total</span><span id="ocTotal">—</span></div>
        </div>
      </div>

      {{-- Shipping address --}}
      <div>
        <h3 class="font-display text-xl text-maroon mb-4">Shipping To</h3>
        <div id="ocAddress" class="font-body text-sm text-maroon/70 space-y-1 leading-relaxed"></div>

        <div class="mt-6">
          <h3 class="font-display text-xl text-maroon mb-2">Payment</h3>
          <p id="ocPayment" class="font-body text-sm text-maroon/70"></p>
        </div>
      </div>
    </div>

    {{-- Petal divider --}}
    <div class="flex justify-center mb-10">
      <div class="petal-divider w-48 opacity-40"></div>
    </div>

    {{-- CTA --}}
    <div class="text-center space-y-4">
      <a href="{{ route('search') }}" class="gold-btn py-4 px-10 text-xs font-body font-semibold tracking-widest uppercase inline-block">
        Continue Shopping
      </a>
      @auth
        <p class="font-body text-xs text-maroon/40">
          <a href="{{ route('account') }}" class="hover:text-gold transition-colors underline-grow">View your account</a> for order history.
        </p>
      @else
        <p class="font-body text-xs text-maroon/40">
          <button onclick="window.openLoginModal?.()" class="hover:text-gold transition-colors underline-grow">Sign in</button>
          or <a href="{{ route('register') }}" class="hover:text-gold transition-colors underline-grow">create an account</a> to track orders.
        </p>
      @endauth
    </div>

  </div>

  {{-- Fallback: order not found --}}
  <div id="ocMissing" class="hidden text-center py-16">
    <svg class="w-12 h-12 text-gold/30 mx-auto mb-4" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
    </svg>
    <p class="font-display text-3xl text-maroon mb-2">Order not found</p>
    <p class="font-body text-sm text-maroon/50 mb-8">We couldn't locate this order in your session.</p>
    <a href="{{ route('home') }}" class="gold-btn py-3.5 px-8 text-xs font-body font-semibold tracking-widest uppercase inline-block">Go Home</a>
  </div>

</div>
</div>

@push('scripts')
<script>
(function () {

  function esc(s) {
    return String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
  }

  const PAY_LABELS = { upi: 'UPI / GPay / PhonePe', card: 'Credit / Debit Card', cod: 'Cash on Delivery' };

  @php
    $serverOrderPayload = (isset($orderModel) && $orderModel) ? [
      'id'       => $orderModel->reference,
      'order_id' => $orderModel->id,
      'subtotal' => $orderModel->subtotal,
      'shipping' => [
        'name'     => $orderModel->shipping_name,
        'phone'    => $orderModel->shipping_phone,
        'email'    => $orderModel->shipping_email,
        'address1' => $orderModel->shipping_address1,
        'address2' => $orderModel->shipping_address2,
        'city'     => $orderModel->shipping_city,
        'state'    => $orderModel->shipping_state,
        'pincode'  => $orderModel->shipping_pincode,
      ],
      'payment'  => $orderModel->payment_method,
      'total'    => $orderModel->total,
      'placedAt' => $orderModel->created_at?->toISOString(),
      'items'    => $orderModel->items->map(fn ($i) => [
        'name'      => $i->product_name,
        'colorName' => $i->color_name,
        'size'      => $i->size,
        'qty'       => $i->qty,
        'price'     => $i->price,
        'image'     => $i->cover_image,
      ])->values()->all(),
    ] : null;
  @endphp
  const serverOrder = @json($serverOrderPayload);

  const params    = new URLSearchParams(window.location.search);
  const orderId   = params.get('order') || '{{ $order ?? '' }}';
  const detailsEl = document.getElementById('ocDetails');
  const missingEl = document.getElementById('ocMissing');

  let stored = serverOrder;
  if (!stored) {
    try { stored = JSON.parse(sessionStorage.getItem('lc_last_order') || 'null'); } catch { stored = null; }
  }

  if (!stored) {
    detailsEl?.classList.add('hidden');
    missingEl?.classList.remove('hidden');
    return;
  }

  // Show details
  detailsEl?.classList.remove('hidden');
  missingEl?.classList.add('hidden');

  // Personalise heading
  const firstName = (stored.shipping?.name || '').split(' ')[0];
  if (firstName) {
    const h = document.getElementById('ocThankYou');
    if (h) h.textContent = 'Thank you, ' + firstName + '!';
  }

  // Order ID + date
  const idEl = document.getElementById('ocOrderId');
  if (idEl) idEl.textContent = stored.id;
  const dateEl = document.getElementById('ocPlacedAt');
  if (dateEl && stored.placedAt) {
    dateEl.textContent = 'Placed on ' + new Date(stored.placedAt).toLocaleDateString('en-IN', { day:'numeric', month:'long', year:'numeric' });
  }

  // Items
  const itemsEl = document.getElementById('ocItems');
  if (itemsEl && stored.items?.length) {
    itemsEl.innerHTML = stored.items.map(i => `
      <div class="flex gap-3 py-3">
        <img src="${esc(i.image)}" alt="" class="w-12 h-16 object-cover flex-shrink-0 bg-blush/30">
        <div class="flex-1 min-w-0">
          <p class="font-display text-sm text-maroon truncate">${esc(i.name)}</p>
          <p class="font-body text-[11px] text-maroon/50">${esc(i.colorName)} · ${esc(i.size)} · ×${i.qty}</p>
          <p class="font-body text-xs text-gold-dark font-semibold">₹${Number(i.price * i.qty).toLocaleString('en-IN')}</p>
        </div>
      </div>`).join('');
  }

  // Totals
  const sub     = stored.subtotal ?? stored.items?.reduce((s, i) => s + i.price * i.qty, 0) ?? 0;
  const total   = stored.total ?? sub;
  const shipAmt = total - sub - (stored.payment === 'cod' ? 49 : 0);
  document.getElementById('ocSubtotal').textContent = '₹' + sub.toLocaleString('en-IN');
  document.getElementById('ocShipping').textContent = shipAmt <= 0 ? 'Free' : '₹' + shipAmt.toLocaleString('en-IN');
  document.getElementById('ocTotal').textContent    = '₹' + total.toLocaleString('en-IN');

  // Address
  const addrEl = document.getElementById('ocAddress');
  if (addrEl && stored.shipping) {
    const s = stored.shipping;
    addrEl.innerHTML = [
      `<strong class="text-maroon">${esc(s.name)}</strong>`,
      esc(s.phone),
      esc(s.address1),
      s.address2 ? esc(s.address2) : null,
      `${esc(s.city)}, ${esc(s.state)} – ${esc(s.pincode)}`,
      `<a href="mailto:${esc(s.email)}" class="text-gold hover:underline">${esc(s.email)}</a>`,
    ].filter(Boolean).map(l => `<p>${l}</p>`).join('');
  }

  // Payment
  const payEl = document.getElementById('ocPayment');
  if (payEl) payEl.textContent = PAY_LABELS[stored.payment] || stored.payment || '—';

})();
</script>
@endpush

</x-storefront-layout>
