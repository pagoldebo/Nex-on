{{--
    ======================================================================
    THEME: VYRA — Modern Noir & High-Contrast Streetwear
    PAGE: CUSTOMER CHECKOUT & PAYMENT PROCESSING
    ======================================================================
    File: resources/views/storefront/vyra/checkout.blade.php
    Controller: App\Http\Controllers\StorefrontController@checkout
    Route Context: GET /checkout, POST /checkout/process
    ViewModel Injections:
      - $theme: Active Template model ('vyra')
      - $gateways: Configured payment gateway credentials & active toggles
    Responsibility: Secure customer checkout, shipping address capture, and payment
    Features:
      - Multi-step customer checkout: Shipping Address, Delivery Method, Payment Selection
      - Gateway options: Razorpay, Stripe, PhonePe, and Cash on Delivery (COD)
      - Real-time server-side price verification preventing client tampering (Domain 10)
      - Coupon validation and instant subtotal recalculation
    Operations & Maintenance:
      - Enforces strict CSRF verification and rate limiting on order submissions
--}}
<x-storefront-layout :theme="$theme" title="Checkout">
<div class="max-w-5xl mx-auto px-6 lg:px-10 py-10">
  <h1 class="font-display uppercase text-3xl md:text-4xl mb-10 reveal text-[#F2F0EC]">Checkout</h1>
  <div class="grid lg:grid-cols-5 gap-10">

    {{-- Shipping form --}}
    <form id="vyraForm" class="lg:col-span-3 space-y-5" onsubmit="event.preventDefault(); placeOrder();">
      @csrf
      <h3 class="font-display uppercase text-xl mb-2 text-[#F2F0EC]">Shipping Details</h3>

      @if(isset($addresses) && $addresses->isNotEmpty())
        <div class="mb-4 p-4 border bg-[#1A1A1D]" style="border-color:#FF3D5A44">
          <label class="block text-xs font-bold uppercase tracking-wider mb-2 text-[#F2F0EC]">Saved Addresses</label>
          <select id="vyraAddressPicker" class="w-full border p-2.5 text-xs outline-none bg-[#0E0E10] text-[#F2F0EC]" style="border-color:#FF3D5A66">
            <option value="">-- Enter new address --</option>
            @foreach($addresses as $addr)
              <option value="{{ $addr->id }}"
                      data-name="{{ $addr->name }}"
                      data-phone="{{ $addr->phone }}"
                      data-address1="{{ $addr->address1 }}"
                      data-city="{{ $addr->city }}"
                      data-state="{{ $addr->state }}"
                      data-pincode="{{ $addr->pincode }}"
                      {{ $addr->is_default ? 'selected' : '' }}>
                {{ $addr->name }} ({{ ucfirst($addr->type) }}) — {{ $addr->city }}, {{ $addr->pincode }}
              </option>
            @endforeach
          </select>
        </div>
      @endif

      <div class="grid sm:grid-cols-2 gap-4">
        <input name="name" id="vyraName" required placeholder="Full Name" value="{{ $user->name ?? '' }}" class="border px-4 py-3 text-sm outline-none w-full bg-transparent font-body" style="border-color:#FF3D5A44;color:#F2F0EC">
        <input name="phone" id="vyraPhone" required type="tel" placeholder="Phone (10 Digits)" pattern="[6-9][0-9]{9}" maxlength="10" class="border px-4 py-3 text-sm outline-none w-full bg-transparent font-body" style="border-color:#FF3D5A44;color:#F2F0EC">
      </div>
      <input name="email" id="vyraEmail" required type="email" placeholder="Email Address" value="{{ $user->email ?? '' }}" class="border px-4 py-3 text-sm outline-none w-full bg-transparent font-body" style="border-color:#FF3D5A44;color:#F2F0EC">
      <input name="address" id="vyraAddress" required placeholder="Street Address" class="border px-4 py-3 text-sm outline-none w-full bg-transparent font-body" style="border-color:#FF3D5A44;color:#F2F0EC">
      <div class="grid sm:grid-cols-3 gap-4">
        <input name="city" id="vyraCity" required placeholder="City" class="border px-4 py-3 text-sm outline-none w-full bg-transparent font-body" style="border-color:#FF3D5A44;color:#F2F0EC">
        <input name="state" id="vyraState" required placeholder="State" class="border px-4 py-3 text-sm outline-none w-full bg-transparent font-body" style="border-color:#FF3D5A44;color:#F2F0EC">
        <input name="pincode" id="vyraPincode" required placeholder="Pincode" maxlength="6" pattern="[0-9]{6}" class="border px-4 py-3 text-sm outline-none w-full bg-transparent font-body" style="border-color:#FF3D5A44;color:#F2F0EC">
      </div>

      <h3 class="font-display uppercase text-xl mt-6 mb-2 text-[#F2F0EC]">Payment Method</h3>
      <div class="space-y-3">
        <label class="bg-[#1A1A1D] border flex items-center gap-4 p-4 cursor-pointer hover:opacity-80 transition-opacity" style="border-color:#FF3D5A33">
          <input type="radio" name="payment" value="upi" checked class="accent-[#FF3D5A]" onchange="updateTotals()">
          <div>
            <span class="font-body text-sm font-bold text-[#F2F0EC] block">UPI / QR (Instant)</span>
            <span class="text-xs text-[#F2F0EC]/60 font-body">Google Pay, PhonePe, Paytm, BHIM</span>
          </div>
        </label>
        <label class="bg-[#1A1A1D] border flex items-center gap-4 p-4 cursor-pointer hover:opacity-80 transition-opacity" style="border-color:#FF3D5A33">
          <input type="radio" name="payment" value="card" class="accent-[#FF3D5A]" onchange="updateTotals()">
          <div>
            <span class="font-body text-sm font-bold text-[#F2F0EC] block">Credit / Debit Card</span>
            <span class="text-xs text-[#F2F0EC]/60 font-body">Visa, Mastercard, RuPay</span>
          </div>
        </label>
        @if($enableCod ?? true)
          <label class="bg-[#1A1A1D] border flex items-center justify-between p-4 cursor-pointer hover:opacity-80 transition-opacity" style="border-color:#FF3D5A33">
            <div class="flex items-center gap-4">
              <input type="radio" name="payment" value="cod" class="accent-[#FF3D5A]" onchange="updateTotals()">
              <div>
                <span class="font-body text-sm font-bold text-[#F2F0EC] block">Cash on Delivery</span>
                <span class="text-xs text-[#F2F0EC]/60 font-body">Pay at doorstep upon delivery</span>
              </div>
            </div>
            <span class="text-xs font-bold text-[#FF3D5A]">+₹{{ $codCharge ?? 40 }}</span>
          </label>
        @endif
      </div>
    </form>

    {{-- Order summary --}}
    <div class="lg:col-span-2">
      <div class="bg-[#1A1A1D] border p-6 sticky top-24" style="border-color:#FF3D5A22">
        <h3 class="font-display uppercase text-xl mb-5 text-[#F2F0EC]">Order Summary</h3>
        <div id="coItems" class="divide-y font-body text-sm mb-4 text-[#F2F0EC] max-h-56 overflow-y-auto" style="border-color:#FF3D5A22"></div>
        
        {{-- Coupon --}}
        <div class="mb-4 pt-3 border-t" style="border-color:#FF3D5A22">
          <div class="flex gap-2">
            <input type="text" id="vyraCoupon" placeholder="PROMO CODE" class="border px-3 py-2 text-xs uppercase font-mono outline-none flex-1 bg-transparent text-[#F2F0EC]" style="border-color:#FF3D5A44">
            <button type="button" onclick="applyCoupon()" class="px-4 py-2 text-xs font-bold uppercase tracking-wider" style="background:#FF3D5A;color:#0E0E10">Apply</button>
          </div>
          <p id="vyraCouponMsg" class="text-xs mt-1.5 hidden font-medium"></p>
        </div>

        <div class="border-t pt-4 space-y-2 font-body text-sm text-[#F2F0EC]" style="border-color:#FF3D5A22">
          <div class="flex justify-between opacity-60"><span>Subtotal</span><span id="coSubtotal">₹0</span></div>
          <div class="flex justify-between opacity-60"><span>Shipping</span><span id="coShipping">Free</span></div>
          <div class="flex justify-between opacity-60 hidden" id="coCodRow"><span>COD Charge</span><span>₹{{ $codCharge ?? 40 }}</span></div>
          <div class="flex justify-between text-[#FF3D5A] hidden" id="coDiscountRow">
            <span>Discount (<span id="coDiscountCode"></span>)</span>
            <span id="coDiscountAmt">-₹0</span>
          </div>
          <div class="flex justify-between font-bold text-base border-t pt-3" style="border-color:#FF3D5A22">
            <span>Total</span><span id="coTotal" style="color:#FF3D5A">₹0</span>
          </div>
        </div>
        <button id="vyraPlaceBtn" onclick="placeOrder()" class="mt-6 w-full py-4 text-xs font-bold uppercase tracking-widest transition-all" style="background:#FF3D5A;color:#0E0E10;clip-path:polygon(4% 0,100% 0,96% 100%,0 100%)">Place Order</button>
      </div>
    </div>
  </div>
</div>
@push('scripts')
<script>
const FREE_THRESHOLD = {{ $freeShippingThreshold ?? 1499 }};
const COD_CHARGE     = {{ $codCharge ?? 40 }};
const SHIPPING_COST  = {{ $shippingCost ?? 99 }};
let appliedCoupon    = null;

document.addEventListener('DOMContentLoaded', () => {
  renderSummary();

  const picker = document.getElementById('vyraAddressPicker');
  picker?.addEventListener('change', e => {
    const opt = e.target.selectedOptions[0];
    if (!opt || !opt.value) return;
    document.getElementById('vyraName').value    = opt.dataset.name || '';
    document.getElementById('vyraPhone').value   = opt.dataset.phone || '';
    document.getElementById('vyraAddress').value = opt.dataset.address1 || '';
    document.getElementById('vyraCity').value    = opt.dataset.city || '';
    document.getElementById('vyraState').value   = opt.dataset.state || '';
    document.getElementById('vyraPincode').value = opt.dataset.pincode || '';
  });
  if (picker && picker.value) picker.dispatchEvent(new Event('change'));
});

function renderSummary() {
  document.getElementById('coItems').innerHTML = cart.map(i =>
    `<div class="flex justify-between py-2 text-xs"><span>${i.name} (${i.colorName}/${i.size}) ×${i.qty}</span><span class="font-bold">₹${(i.price*i.qty).toLocaleString('en-IN')}</span></div>`
  ).join('') || '<p class="opacity-40 py-2">Empty</p>';
  updateTotals();
}

function updateTotals() {
  const sub = cartTotal();
  const isCod = document.querySelector('input[name="payment"]:checked')?.value === 'cod';
  const shipCost = sub >= FREE_THRESHOLD ? 0 : SHIPPING_COST;
  const codFee = isCod ? COD_CHARGE : 0;
  const disc = appliedCoupon ? appliedCoupon.discount : 0;
  const total = Math.max(0, sub + shipCost + codFee - disc);

  document.getElementById('coSubtotal').textContent = '₹' + sub.toLocaleString('en-IN');
  document.getElementById('coShipping').textContent = shipCost === 0 ? 'Free' : '₹' + shipCost;
  
  const codRow = document.getElementById('coCodRow');
  if (codRow) codRow.classList.toggle('hidden', !isCod);

  const discRow = document.getElementById('coDiscountRow');
  if (discRow) {
    discRow.classList.toggle('hidden', !appliedCoupon);
    if (appliedCoupon) {
      document.getElementById('coDiscountCode').textContent = appliedCoupon.code;
      document.getElementById('coDiscountAmt').textContent = '-₹' + disc.toLocaleString('en-IN');
    }
  }

  document.getElementById('coTotal').textContent = '₹' + total.toLocaleString('en-IN');
}

async function applyCoupon() {
  const code = document.getElementById('vyraCoupon').value.trim();
  const msg = document.getElementById('vyraCouponMsg');
  if (!code) return;
  try {
    const res = await fetch("{{ route('coupon.apply') }}", {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
      body: JSON.stringify({ code, subtotal: cartTotal() })
    });
    const data = await res.json();
    if (data.success) {
      appliedCoupon = data;
      msg.textContent = data.message;
      msg.className = 'text-xs mt-1.5 font-medium text-emerald-400 block';
    } else {
      appliedCoupon = null;
      msg.textContent = data.message || 'Invalid coupon';
      msg.className = 'text-xs mt-1.5 font-medium text-rose-400 block';
    }
  } catch {
    msg.textContent = 'Error applying coupon';
    msg.className = 'text-xs mt-1.5 font-medium text-rose-400 block';
  }
  updateTotals();
}

async function placeOrder() {
  if (!cart.length) { alert('Your bag is empty!'); return; }
  
  const form = document.getElementById('vyraForm');
  if (!form.checkValidity()) { form.reportValidity(); return; }

  const btn = document.getElementById('vyraPlaceBtn');
  btn.disabled = true;
  btn.textContent = 'PROCESSING…';

  const fd = new FormData(form);
  const sub = cartTotal();
  const payment = fd.get('payment');
  const isCod = payment === 'cod';
  const shipCost = sub >= FREE_THRESHOLD ? 0 : SHIPPING_COST;
  const disc = appliedCoupon ? appliedCoupon.discount : 0;
  const total = Math.max(0, sub + shipCost + (isCod ? COD_CHARGE : 0) - disc);

  const payload = {
    shipping: {
      name: fd.get('name'),
      phone: fd.get('phone'),
      email: fd.get('email'),
      address1: fd.get('address'),
      city: fd.get('city'),
      state: fd.get('state'),
      pincode: fd.get('pincode'),
    },
    payment,
    subtotal: sub,
    shipping_charge: shipCost,
    cod_charge: isCod ? COD_CHARGE : 0,
    discount: disc,
    coupon_code: appliedCoupon ? appliedCoupon.code : null,
    total: total,
    items: cart,
  };

  try {
    const res = await fetch("{{ route('checkout.placeOrder') }}", {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
      body: JSON.stringify(payload)
    });
    const data = await res.json();
    if (data.success) {
      sessionStorage.setItem('lc_last_order', JSON.stringify(data.order));
      cart = []; persistCart(); updateCartUI();
      window.location.href = data.redirect_url;
    } else {
      alert(data.message || 'Order failed.');
      btn.disabled = false;
      btn.textContent = 'Place Order';
    }
  } catch {
    alert('Network error placing order.');
    btn.disabled = false;
    btn.textContent = 'Place Order';
  }
}
</script>
@endpush
</x-storefront-layout>
