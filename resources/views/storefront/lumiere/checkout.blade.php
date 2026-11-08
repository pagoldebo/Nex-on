{{-- resources/views/storefront/lumiere/checkout.blade.php
     Lumière checkout — live database order persistence with coupons, address book & dynamic settings. --}}
<x-storefront-layout :theme="$theme" title="Checkout">

<style>
  input:checked + div .co-dot { opacity: 1; }
</style>

<div class="max-w-6xl mx-auto px-6 md:px-12 py-10">
  <h1 class="font-display italic text-3xl md:text-4xl text-gray-900 mb-10 reveal">Checkout</h1>

  <div class="grid lg:grid-cols-5 gap-10">

    {{-- Shipping + payment (3/5) --}}
    <form id="checkoutForm" class="lg:col-span-3 space-y-5" autocomplete="on">
      @csrf
      <h2 class="font-display italic text-xl text-gray-900">Shipping Details</h2>

      @if(isset($addresses) && $addresses->isNotEmpty())
        <div class="mb-4 p-4 rounded-2xl border border-pink-100 bg-pink-50/30">
          <label class="block text-xs font-bold uppercase tracking-wider mb-2 text-gray-700">Choose From Saved Addresses</label>
          <select id="lmAddressPicker" class="w-full rounded-xl border border-pink-200 p-2.5 text-xs outline-none bg-white text-gray-800">
            <option value="">-- Or enter new delivery address --</option>
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
        <input name="name" id="lmName" type="text" required autocomplete="name" placeholder="Full Name" value="{{ $user->name ?? '' }}"
               class="rounded-2xl border border-pink-100 bg-white px-5 py-3.5 text-sm text-gray-800 outline-none focus:border-pink-400 transition-colors">
        <input name="phone" id="lmPhone" type="tel" required inputmode="numeric" autocomplete="tel-national" placeholder="Mobile Number"
               pattern="[6-9][0-9]{9}" title="10-digit Indian mobile number" maxlength="10"
               class="rounded-2xl border border-pink-100 bg-white px-5 py-3.5 text-sm text-gray-800 outline-none focus:border-pink-400 transition-colors">
      </div>
      <input name="email" id="lmEmail" type="email" required autocomplete="email" placeholder="Email Address" value="{{ $user->email ?? '' }}"
             class="w-full rounded-2xl border border-pink-100 bg-white px-5 py-3.5 text-sm text-gray-800 outline-none focus:border-pink-400 transition-colors">
      <input name="address" id="lmAddress" type="text" required autocomplete="street-address" placeholder="Street Address"
             class="w-full rounded-2xl border border-pink-100 bg-white px-5 py-3.5 text-sm text-gray-800 outline-none focus:border-pink-400 transition-colors">
      <div class="grid sm:grid-cols-3 gap-4">
        <input name="city" id="lmCity" type="text" required autocomplete="address-level2" placeholder="City"
               class="rounded-2xl border border-pink-100 bg-white px-5 py-3.5 text-sm text-gray-800 outline-none focus:border-pink-400 transition-colors">
        <input name="state" id="lmState" type="text" required autocomplete="address-level1" placeholder="State"
               class="rounded-2xl border border-pink-100 bg-white px-5 py-3.5 text-sm text-gray-800 outline-none focus:border-pink-400 transition-colors">
        <input name="pincode" id="lmPincode" type="text" required inputmode="numeric" autocomplete="postal-code" placeholder="Pincode"
               pattern="[1-9][0-9]{5}" title="6-digit pincode" maxlength="6"
               class="rounded-2xl border border-pink-100 bg-white px-5 py-3.5 text-sm text-gray-800 outline-none focus:border-pink-400 transition-colors">
      </div>

      <h2 class="font-display italic text-xl text-gray-900 pt-4">Payment Method</h2>
      <div class="space-y-3">
        <label class="relative block cursor-pointer">
          <input type="radio" name="payment" value="upi" checked class="peer sr-only" onchange="coUpdateTotals()">
          <div class="flex items-center gap-4 rounded-2xl border border-pink-100 bg-white p-4 transition-colors peer-checked:border-pink-500 peer-checked:bg-pink-50">
            <span class="w-4 h-4 rounded-full border-2 border-pink-200 flex items-center justify-center flex-shrink-0">
              <span class="co-dot w-2 h-2 rounded-full bg-pink-600 opacity-0 transition-opacity"></span>
            </span>
            <div>
              <span class="text-sm font-semibold text-gray-800 block">UPI / QR (Instant)</span>
              <span class="text-xs text-gray-400">Google Pay, PhonePe, Paytm</span>
            </div>
          </div>
        </label>
        <label class="relative block cursor-pointer">
          <input type="radio" name="payment" value="card" class="peer sr-only" onchange="coUpdateTotals()">
          <div class="flex items-center gap-4 rounded-2xl border border-pink-100 bg-white p-4 transition-colors peer-checked:border-pink-500 peer-checked:bg-pink-50">
            <span class="w-4 h-4 rounded-full border-2 border-pink-200 flex items-center justify-center flex-shrink-0">
              <span class="co-dot w-2 h-2 rounded-full bg-pink-600 opacity-0 transition-opacity"></span>
            </span>
            <div>
              <span class="text-sm font-semibold text-gray-800 block">Credit / Debit Card</span>
              <span class="text-xs text-gray-400">Visa, Mastercard, RuPay</span>
            </div>
          </div>
        </label>
        @if($enableCod ?? true)
          <label class="relative block cursor-pointer">
            <input type="radio" name="payment" value="cod" class="peer sr-only" onchange="coUpdateTotals()">
            <div class="flex items-center justify-between rounded-2xl border border-pink-100 bg-white p-4 transition-colors peer-checked:border-pink-500 peer-checked:bg-pink-50">
              <div class="flex items-center gap-4">
                <span class="w-4 h-4 rounded-full border-2 border-pink-200 flex items-center justify-center flex-shrink-0">
                  <span class="co-dot w-2 h-2 rounded-full bg-pink-600 opacity-0 transition-opacity"></span>
                </span>
                <div>
                  <span class="text-sm font-semibold text-gray-800 block">Cash on Delivery</span>
                  <span class="text-xs text-gray-400">Doorstep settlement</span>
                </div>
              </div>
              <span class="text-xs font-bold text-pink-600">+₹{{ $codCharge ?? 40 }}</span>
            </div>
          </label>
        @endif
      </div>
    </form>

    {{-- Order summary (2/5) --}}
    <div class="lg:col-span-2">
      <div class="bg-white rounded-[28px] border border-pink-50 p-6 lg:sticky lg:top-28 shadow-sm shadow-pink-50">
        <h2 class="font-display italic text-xl text-gray-900 mb-5">Order Summary</h2>
        <div id="coItems" class="divide-y divide-pink-50 text-sm text-gray-700 mb-4 max-h-56 overflow-y-auto"></div>
        
        {{-- Coupon Promo Code --}}
        <div class="mb-4 pt-3 border-t border-pink-50">
          <div class="flex gap-2">
            <input type="text" id="lmCoupon" placeholder="PROMO CODE" class="rounded-xl border border-pink-100 px-3 py-2 text-xs uppercase font-mono outline-none flex-1 focus:border-pink-400">
            <button type="button" onclick="lmApplyCoupon()" class="px-4 py-2 rounded-xl text-xs font-bold bg-pink-600 text-white hover:bg-pink-700 transition">Apply</button>
          </div>
          <p id="lmCouponMsg" class="text-xs mt-1.5 hidden font-medium"></p>
        </div>

        <div class="border-t border-pink-50 pt-4 space-y-2 text-sm text-gray-600">
          <div class="flex justify-between"><span>Subtotal</span><span id="coSubtotal" class="font-semibold text-gray-900">₹0</span></div>
          <div class="flex justify-between"><span>Shipping</span><span id="coShipping" class="text-gray-400">Free</span></div>
          <div class="flex justify-between text-gray-600 hidden" id="coCodRow"><span>COD Charge</span><span>₹{{ $codCharge ?? 40 }}</span></div>
          <div class="flex justify-between text-pink-600 hidden font-medium" id="coDiscountRow">
            <span>Discount (<span id="coDiscountCode"></span>)</span>
            <span id="coDiscountAmt">-₹0</span>
          </div>
          <div class="flex justify-between pt-3 border-t border-pink-50 text-base font-bold text-gray-900">
            <span>Total</span><span id="coTotal" class="text-pink-600">₹0</span>
          </div>
        </div>
        <button id="coPlaceBtn" type="submit" form="checkoutForm"
                class="mt-6 w-full py-4 rounded-2xl text-xs font-bold uppercase tracking-widest bg-[#405549] text-white hover:bg-pink-600 transition-colors disabled:opacity-40 disabled:cursor-not-allowed shadow-md">
          Place Order
        </button>
        <p id="coEmptyNote" class="hidden text-center text-xs text-gray-400 mt-3">
          Your bag is empty. <a href="{{ route('home') }}" class="text-pink-600 underline">Continue shopping</a>
        </p>
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

function coEsc(s) {
    return String(s).replace(/[&<>"']/g, ch => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[ch]));
}

function coRenderSummary() {
    const rupees = n => '₹' + Number(n).toLocaleString('en-IN');
    document.getElementById('coItems').innerHTML = cart.map(i => `
        <div class="flex gap-3 py-3">
            <img src="${coEsc(i.image)}" alt="" class="w-14 h-16 object-cover rounded-xl bg-pink-50 flex-shrink-0">
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-gray-900 truncate">${coEsc(i.name)}</p>
                <p class="text-xs text-gray-400">${coEsc(i.colorName)} / ${coEsc(i.size)} × ${i.qty}</p>
            </div>
            <p class="font-semibold text-gray-900">${rupees(i.price * i.qty)}</p>
        </div>`).join('');
    
    coUpdateTotals();

    const empty = cart.length === 0;
    document.getElementById('coPlaceBtn').disabled = empty;
    document.getElementById('coEmptyNote').classList.toggle('hidden', !empty);
}

function coUpdateTotals() {
    const rupees = n => '₹' + Number(n).toLocaleString('en-IN');
    const sub = cartTotal();
    const isCod = document.querySelector('input[name="payment"]:checked')?.value === 'cod';
    const shipCost = sub >= FREE_THRESHOLD ? 0 : SHIPPING_COST;
    const codFee = isCod ? COD_CHARGE : 0;
    const disc = appliedCoupon ? appliedCoupon.discount : 0;
    const total = Math.max(0, sub + shipCost + codFee - disc);

    document.getElementById('coSubtotal').textContent = rupees(sub);
    document.getElementById('coShipping').textContent = shipCost === 0 ? 'Free' : rupees(shipCost);
    
    const codRow = document.getElementById('coCodRow');
    if (codRow) codRow.classList.toggle('hidden', !isCod);

    const discRow = document.getElementById('coDiscountRow');
    if (discRow) {
      discRow.classList.toggle('hidden', !appliedCoupon);
      if (appliedCoupon) {
        document.getElementById('coDiscountCode').textContent = appliedCoupon.code;
        document.getElementById('coDiscountAmt').textContent = '-' + rupees(disc);
      }
    }

    document.getElementById('coTotal').textContent = rupees(total);
}

async function lmApplyCoupon() {
  const code = document.getElementById('lmCoupon').value.trim();
  const msg = document.getElementById('lmCouponMsg');
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
      msg.className = 'text-xs mt-1.5 font-medium text-emerald-600 block';
    } else {
      appliedCoupon = null;
      msg.textContent = data.message || 'Invalid coupon';
      msg.className = 'text-xs mt-1.5 font-medium text-pink-600 block';
    }
  } catch {
    msg.textContent = 'Error verifying coupon';
    msg.className = 'text-xs mt-1.5 font-medium text-pink-600 block';
  }
  coUpdateTotals();
}

document.getElementById('checkoutForm').addEventListener('submit', async e => {
    e.preventDefault();
    if (!cart.length) return;

    const btn = document.getElementById('coPlaceBtn');
    btn.disabled = true;
    btn.textContent = 'Securing Order…';

    const f = new FormData(e.target);
    const sub = cartTotal();
    const payment = f.get('payment');
    const isCod = payment === 'cod';
    const shipCost = sub >= FREE_THRESHOLD ? 0 : SHIPPING_COST;
    const disc = appliedCoupon ? appliedCoupon.discount : 0;
    const total = Math.max(0, sub + shipCost + (isCod ? COD_CHARGE : 0) - disc);

    const payload = {
        shipping: {
            name: f.get('name'),
            phone: f.get('phone'),
            email: f.get('email'),
            address1: f.get('address'),
            city: f.get('city'),
            state: f.get('state'),
            pincode: f.get('pincode'),
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
            alert(data.message || 'Error processing order.');
            btn.disabled = false;
            btn.textContent = 'Place Order';
        }
    } catch {
        alert('Network connection error.');
        btn.disabled = false;
        btn.textContent = 'Place Order';
    }
});

document.addEventListener('DOMContentLoaded', () => {
    coRenderSummary();
    const picker = document.getElementById('lmAddressPicker');
    picker?.addEventListener('change', e => {
      const opt = e.target.selectedOptions[0];
      if (!opt || !opt.value) return;
      document.getElementById('lmName').value    = opt.dataset.name || '';
      document.getElementById('lmPhone').value   = opt.dataset.phone || '';
      document.getElementById('lmAddress').value = opt.dataset.address1 || '';
      document.getElementById('lmCity').value    = opt.dataset.city || '';
      document.getElementById('lmState').value   = opt.dataset.state || '';
      document.getElementById('lmPincode').value = opt.dataset.pincode || '';
    });
    if (picker && picker.value) picker.dispatchEvent(new Event('change'));
});
</script>
@endpush

</x-storefront-layout>
