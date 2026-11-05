{{-- resources/views/storefront/saaya/checkout.blade.php
     Saaya checkout — live database order persistence with coupons, address book & dynamic settings. --}}

<x-storefront-layout>

<style>
  input[name="payment"]:checked + div .pay-dot { opacity: 1; transform: scale(1); }
</style>

<div class="bg-ivory min-h-screen">

{{-- Compact header --}}
<div class="bg-maroon py-10 lg:py-14 relative overflow-hidden">
  <div class="absolute inset-0 opacity-[.05] pointer-events-none"
       style="background-image:radial-gradient(circle,#C9A24B 1px,transparent 1px);background-size:24px 24px;"></div>
  <div class="relative max-w-7xl mx-auto px-6 lg:px-12">
    <p class="font-display italic text-gold text-lg mb-1">Almost There</p>
    <h1 class="font-display text-4xl lg:text-5xl text-ivory">Checkout</h1>
  </div>
</div>

{{-- Empty-cart guard --}}
<div id="ckEmpty" class="hidden max-w-7xl mx-auto px-6 lg:px-12 py-20 text-center">
  <p class="font-display text-3xl text-maroon mb-4">Your bag is empty</p>
  <a href="{{ route('search') }}" class="gold-btn py-3.5 px-8 text-xs font-body font-semibold tracking-widest uppercase inline-block">Shop Now</a>
</div>

<div id="ckMain" class="hidden max-w-7xl mx-auto px-6 lg:px-12 py-10 lg:py-14">
  <div class="grid lg:grid-cols-[1fr_380px] gap-10 lg:gap-16 items-start">

    {{-- ── LEFT: shipping + payment form ── --}}
    <form id="ckForm" novalidate class="space-y-8">
      @csrf

      {{-- Shipping details --}}
      <fieldset>
        <legend class="font-display text-2xl text-maroon mb-5">Shipping Details</legend>

        {{-- Address Book Autofill if logged in and has addresses --}}
        @if(isset($addresses) && $addresses->isNotEmpty())
          <div class="mb-5 p-4 rounded-xl border border-gold/30 bg-gold/5">
            <label class="ck-label mb-1.5 text-gold-dark">Choose From Saved Addresses</label>
            <select id="savedAddressPicker" class="ck-input bg-white text-xs">
              <option value="">-- Or enter a new address below --</option>
              @foreach($addresses as $addr)
                <option value="{{ $addr->id }}"
                        data-name="{{ $addr->name }}"
                        data-phone="{{ $addr->phone }}"
                        data-address1="{{ $addr->address1 }}"
                        data-address2="{{ $addr->address2 }}"
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

        <div class="space-y-4">

          <div class="grid sm:grid-cols-2 gap-4">
            <div>
              <label class="ck-label">Full Name</label>
              <input type="text" name="name" id="ckName" required autocomplete="name"
                     value="{{ $user->name ?? '' }}"
                     class="ck-input" placeholder="Priya Sharma">
              <p class="ck-err hidden">Please enter your full name.</p>
            </div>
            <div>
              <label class="ck-label">Mobile Number</label>
              <input type="tel" name="phone" id="ckPhone" required autocomplete="tel"
                     pattern="[6-9][0-9]{9}" maxlength="10"
                     class="ck-input" placeholder="9876543210">
              <p class="ck-err hidden">Enter a valid 10-digit Indian mobile number.</p>
            </div>
          </div>

          <div>
            <label class="ck-label">Email Address</label>
            <input type="email" name="email" id="ckEmail" required autocomplete="email"
                   value="{{ $user->email ?? '' }}"
                   class="ck-input" placeholder="priya@example.com">
            <p class="ck-err hidden">Please enter a valid email address.</p>
          </div>

          <div>
            <label class="ck-label">Address Line 1</label>
            <input type="text" name="address1" id="ckAddress1" required autocomplete="address-line1"
                   class="ck-input" placeholder="Flat / House no, Building, Street">
            <p class="ck-err hidden">Please enter your address.</p>
          </div>

          <div>
            <label class="ck-label">Address Line 2 <span class="font-normal text-maroon/40">(optional)</span></label>
            <input type="text" name="address2" id="ckAddress2" autocomplete="address-line2"
                   class="ck-input" placeholder="Area, Colony, Landmark">
          </div>

          <div class="grid sm:grid-cols-3 gap-4">
            <div>
              <label class="ck-label">City</label>
              <input type="text" name="city" id="ckCity" required autocomplete="address-level2"
                     class="ck-input" placeholder="Mumbai">
              <p class="ck-err hidden">Please enter your city.</p>
            </div>
            <div>
              <label class="ck-label">State</label>
              <input type="text" name="state" id="ckState" required autocomplete="address-level1"
                     class="ck-input" placeholder="Maharashtra">
              <p class="ck-err hidden">Please enter your state.</p>
            </div>
            <div>
              <label class="ck-label">PIN Code</label>
              <input type="text" name="pincode" id="ckPincode" required autocomplete="postal-code"
                     pattern="[0-9]{6}" maxlength="6"
                     class="ck-input" placeholder="400001">
              <p class="ck-err hidden">Enter a valid 6-digit PIN code.</p>
            </div>
          </div>

          @auth
            <label class="flex items-center gap-2.5 pt-2 text-xs font-body text-maroon/70 cursor-pointer">
              <input type="checkbox" name="save_address" value="1" checked class="accent-gold">
              <span>Save this address to my account for faster checkouts</span>
            </label>
          @endauth

        </div>
      </fieldset>

      {{-- Payment method --}}
      <fieldset>
        <legend class="font-display text-2xl text-maroon mb-5">Payment Method</legend>
        <div class="space-y-3">

          <label class="block cursor-pointer">
            <input type="radio" name="payment" value="upi" checked class="sr-only">
            <div class="border border-gold/30 bg-ivory p-4 flex items-center justify-between transition-colors">
              <div class="flex items-center gap-3">
                <span class="w-4 h-4 rounded-full border border-gold flex items-center justify-center">
                  <span class="pay-dot w-2 h-2 rounded-full bg-maroon opacity-0 transition-opacity"></span>
                </span>
                <div>
                  <p class="font-display text-base text-maroon">UPI / QR</p>
                  <p class="font-body text-xs text-maroon/50">GPay, PhonePe, Paytm, BHIM</p>
                </div>
              </div>
              <span class="font-body text-[10px] uppercase tracking-widest text-gold-dark font-semibold">Fastest</span>
            </div>
          </label>

          <label class="block cursor-pointer">
            <input type="radio" name="payment" value="card" class="sr-only">
            <div class="border border-gold/30 bg-ivory p-4 flex items-center justify-between transition-colors">
              <div class="flex items-center gap-3">
                <span class="w-4 h-4 rounded-full border border-gold flex items-center justify-center">
                  <span class="pay-dot w-2 h-2 rounded-full bg-maroon opacity-0 transition-opacity"></span>
                </span>
                <div>
                  <p class="font-display text-base text-maroon">Credit / Debit Card</p>
                  <p class="font-body text-xs text-maroon/50">Visa, Mastercard, RuPay</p>
                </div>
              </div>
            </div>
          </label>

          @if($enableCod ?? true)
            <label class="block cursor-pointer">
              <input type="radio" name="payment" value="cod" class="sr-only">
              <div class="border border-gold/30 bg-ivory p-4 flex items-center justify-between transition-colors">
                <div class="flex items-center gap-3">
                  <span class="w-4 h-4 rounded-full border border-gold flex items-center justify-center">
                    <span class="pay-dot w-2 h-2 rounded-full bg-maroon opacity-0 transition-opacity"></span>
                  </span>
                  <div>
                    <p class="font-display text-base text-maroon">Cash on Delivery</p>
                    <p class="font-body text-xs text-maroon/50">Pay at doorstep (+₹{{ $codCharge ?? 40 }} convenience charge)</p>
                  </div>
                </div>
                <span class="font-body text-xs text-maroon/60">+₹{{ $codCharge ?? 40 }}</span>
              </div>
            </label>
          @endif

        </div>
      </fieldset>

      <div id="ckErrorBox" class="hidden p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-medium"></div>

      <button type="submit" id="ckSubmitBtn"
              class="gold-btn w-full py-4 text-xs font-body font-semibold tracking-widest uppercase transition">
        Place Order
      </button>

    </form>

    {{-- ── RIGHT: order summary ── --}}
    <div class="lg:sticky lg:top-28">
      <div class="foil-frame bg-ivory p-7 relative">
        <svg class="frame-line" viewBox="0 0 100 100" preserveAspectRatio="none">
          <rect x="2" y="2" width="96" height="96" vector-effect="non-scaling-stroke"/>
        </svg>

        <h2 class="font-display text-2xl text-maroon mb-5">Your Bag</h2>

        <div id="ckItems" class="divide-y divide-gold/15 mb-5 max-h-64 overflow-y-auto pr-1"></div>

        {{-- Coupon promo code form --}}
        <div class="mb-4 pt-3 border-t border-gold/20">
          <label class="block text-[11px] font-body font-semibold text-maroon/70 uppercase tracking-wider mb-1.5">Apply Promo Code</label>
          <div class="flex gap-2">
            <input type="text" id="couponCodeInput" placeholder="e.g. WELCOME10" 
                   class="ck-input py-2 text-xs uppercase font-mono tracking-wider flex-1">
            <button type="button" id="applyCouponBtn" class="px-4 py-2 bg-maroon text-gold text-xs font-semibold uppercase tracking-wider hover:bg-maroon/90 transition">
              Apply
            </button>
          </div>
          <p id="couponMsg" class="text-xs mt-1.5 hidden"></p>
        </div>

        <div class="space-y-2 font-body text-sm">
          <div class="flex justify-between text-maroon/60">
            <span>Subtotal</span>
            <span id="ckSubtotal">₹0</span>
          </div>
          <div class="flex justify-between text-maroon/60">
            <span>Shipping</span>
            <span id="ckShipping">Free</span>
          </div>
          <div class="flex justify-between text-maroon/60" id="ckCodRow" style="display:none!important">
            <span>COD charge</span>
            <span id="ckCodAmt">₹{{ $codCharge ?? 40 }}</span>
          </div>
          <div class="flex justify-between text-emerald-700 hidden" id="ckDiscountRow">
            <span>Discount (<span id="ckDiscountCode"></span>)</span>
            <span id="ckDiscountAmt">-₹0</span>
          </div>
          <div class="petal-divider opacity-30 my-1"></div>
          <div class="flex justify-between font-semibold text-maroon text-base">
            <span>Total</span>
            <span id="ckTotal">₹0</span>
          </div>
        </div>
      </div>

      <p class="mt-4 text-[10px] font-body text-maroon/40 text-center leading-relaxed">
        Complimentary delivery over ₹{{ number_format($freeShippingThreshold ?? 1499, 0) }} · 100% Discreet Packaging
      </p>
    </div>

  </div>
</div>

</div>

{{-- Tailwind shorthand classes used above --}}
<style>
  .ck-label  { display:block; font-size:.6875rem; font-family:var(--font-body,inherit); font-weight:600; letter-spacing:.1em; text-transform:uppercase; color:rgba(61,12,17,.6); margin-bottom:.375rem; }
  .ck-input  { width:100%; padding:.75rem 1rem; font-size:.875rem; font-family:var(--font-body,inherit); background:#FAF7F2; border:1px solid rgba(201,162,75,.35); color:#3D0C11; outline:none; transition:border-color .15s; }
  .ck-input:focus { border-color:#C9A24B; background:#fff; }
  .ck-input.invalid { border-color:#ef4444; }
  .ck-err    { font-size:.75rem; color:#ef4444; margin-top:.25rem; }
</style>

@push('scripts')
<script>
(function () {

  function esc(s) {
    return String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
  }

  const FREE_THRESHOLD = {{ $freeShippingThreshold ?? 1499 }};
  const COD_CHARGE     = {{ $codCharge ?? 40 }};
  const SHIPPING_COST  = {{ $shippingCost ?? 99 }};

  const emptyEl      = document.getElementById('ckEmpty');
  const mainEl       = document.getElementById('ckMain');
  const itemsEl      = document.getElementById('ckItems');
  const subtotalEl   = document.getElementById('ckSubtotal');
  const shippingEl   = document.getElementById('ckShipping');
  const totalEl      = document.getElementById('ckTotal');
  const codRow       = document.getElementById('ckCodRow');
  const discountRow  = document.getElementById('ckDiscountRow');
  const discountCode = document.getElementById('ckDiscountCode');
  const discountAmt  = document.getElementById('ckDiscountAmt');
  const submitBtn    = document.getElementById('ckSubmitBtn');
  const form         = document.getElementById('ckForm');
  const errorBox     = document.getElementById('ckErrorBox');
  const couponInput  = document.getElementById('couponCodeInput');
  const applyBtn     = document.getElementById('applyCouponBtn');
  const couponMsg    = document.getElementById('couponMsg');
  const addressPicker= document.getElementById('savedAddressPicker');

  let bag = [];
  let appliedCoupon = null; // { code, discount, type, value }

  function getBag() {
    try { return JSON.parse(sessionStorage.getItem('lc_cart') || '[]'); } catch { return []; }
  }

  function subTotal(b) { return b.reduce((s, i) => s + i.price * i.qty, 0); }

  function renderSummary() {
    bag = getBag();
    if (!bag.length) {
      emptyEl?.classList.remove('hidden');
      mainEl?.classList.add('hidden');
      return;
    }
    emptyEl?.classList.add('hidden');
    mainEl?.classList.remove('hidden');

    if (itemsEl) {
      itemsEl.innerHTML = bag.map(i => `
        <div class="flex gap-3 py-3">
          <img src="${esc(i.image)}" alt="" class="w-14 h-18 object-cover flex-shrink-0 bg-blush/30" style="height:4.5rem">
          <div class="flex-1 min-w-0">
            <p class="font-display text-sm text-maroon truncate">${esc(i.name)}</p>
            <p class="font-body text-[11px] text-maroon/50">${esc(i.colorName)} · ${esc(i.size)} · ×${i.qty}</p>
            <p class="font-body text-xs text-gold-dark font-semibold mt-0.5">₹${Number(i.price * i.qty).toLocaleString('en-IN')}</p>
          </div>
        </div>`).join('');
    }

    updateTotals();
  }

  function updateTotals() {
    const sub = subTotal(bag);
    const isCod = document.querySelector('input[name="payment"]:checked')?.value === 'cod';
    const shipping = sub >= FREE_THRESHOLD ? 0 : SHIPPING_COST;
    const cod = isCod ? COD_CHARGE : 0;
    
    let discount = 0;
    if (appliedCoupon) {
      discount = appliedCoupon.discount || 0;
      discountRow?.classList.remove('hidden');
      if (discountCode) discountCode.textContent = appliedCoupon.code;
      if (discountAmt) discountAmt.textContent = '-₹' + Number(discount).toLocaleString('en-IN');
    } else {
      discountRow?.classList.add('hidden');
    }

    const total = Math.max(0, sub + shipping + cod - discount);

    if (subtotalEl) subtotalEl.textContent = '₹' + sub.toLocaleString('en-IN');
    if (shippingEl) shippingEl.textContent = shipping === 0 ? 'Free' : '₹' + shipping;
    if (totalEl)    totalEl.textContent    = '₹' + total.toLocaleString('en-IN');
    if (codRow)     codRow.style.display   = isCod ? 'flex' : 'none';
  }

  document.querySelectorAll('input[name="payment"]').forEach(r => {
    r.addEventListener('change', updateTotals);
  });

  // Saved address picker autocompletion
  addressPicker?.addEventListener('change', e => {
    const opt = e.target.selectedOptions[0];
    if (!opt || !opt.value) return;
    document.getElementById('ckName').value     = opt.dataset.name || '';
    document.getElementById('ckPhone').value    = opt.dataset.phone || '';
    document.getElementById('ckAddress1').value = opt.dataset.address1 || '';
    document.getElementById('ckAddress2').value = opt.dataset.address2 || '';
    document.getElementById('ckCity').value     = opt.dataset.city || '';
    document.getElementById('ckState').value    = opt.dataset.state || '';
    document.getElementById('ckPincode').value  = opt.dataset.pincode || '';
  });

  // Trigger default address populate on load
  if (addressPicker && addressPicker.value) {
    addressPicker.dispatchEvent(new Event('change'));
  }

  // Coupon apply handler
  applyBtn?.addEventListener('click', async () => {
    const code = couponInput.value.trim();
    if (!code) return;
    const sub = subTotal(bag);

    applyBtn.disabled = true;
    applyBtn.textContent = '...';
    couponMsg.classList.add('hidden');

    try {
      const res = await fetch("{{ route('coupon.apply') }}", {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ code, subtotal: sub })
      });
      const data = await res.json();
      if (data.success) {
        appliedCoupon = data;
        couponMsg.textContent = data.message;
        couponMsg.className = 'text-xs mt-1.5 font-medium text-emerald-600 block';
        updateTotals();
      } else {
        appliedCoupon = null;
        couponMsg.textContent = data.message || 'Invalid coupon code';
        couponMsg.className = 'text-xs mt-1.5 font-medium text-rose-600 block';
        updateTotals();
      }
    } catch {
      couponMsg.textContent = 'Error checking coupon.';
      couponMsg.className = 'text-xs mt-1.5 font-medium text-rose-600 block';
    } finally {
      applyBtn.disabled = false;
      applyBtn.textContent = 'Apply';
    }
  });

  /* ── Validation ── */
  function validateForm() {
    let valid = true;
    form.querySelectorAll('[required]').forEach(input => {
      const err = input.nextElementSibling;
      const ok  = input.checkValidity() && input.value.trim() !== '';
      input.classList.toggle('invalid', !ok);
      if (err?.classList.contains('ck-err')) err.classList.toggle('hidden', ok);
      if (!ok) valid = false;
    });
    return valid;
  }

  form?.querySelectorAll('input').forEach(input => {
    input.addEventListener('blur', () => {
      if (!input.required) return;
      const err = input.nextElementSibling;
      const ok  = input.checkValidity() && input.value.trim() !== '';
      input.classList.toggle('invalid', !ok);
      if (err?.classList.contains('ck-err')) err.classList.toggle('hidden', ok);
    });
  });

  /* ── Submit Order to Database ── */
  form?.addEventListener('submit', async e => {
    e.preventDefault();
    if (!validateForm()) return;

    submitBtn.disabled    = true;
    submitBtn.textContent = 'Securing Your Order…';
    if (errorBox) errorBox.classList.add('hidden');

    const fd = new FormData(form);
    const shipping = {
      name:    fd.get('name'),
      phone:   fd.get('phone'),
      email:   fd.get('email'),
      address1:fd.get('address1'),
      address2:fd.get('address2'),
      city:    fd.get('city'),
      state:   fd.get('state'),
      pincode: fd.get('pincode'),
    };
    const payment  = fd.get('payment');
    const sub      = subTotal(bag);
    const isCod    = payment === 'cod';
    const shipCost = sub >= FREE_THRESHOLD ? 0 : SHIPPING_COST;
    const discount = appliedCoupon ? appliedCoupon.discount : 0;
    const total    = Math.max(0, sub + shipCost + (isCod ? COD_CHARGE : 0) - discount);

    const payload = {
      shipping,
      payment,
      subtotal: sub,
      shipping_charge: shipCost,
      cod_charge: isCod ? COD_CHARGE : 0,
      discount: discount,
      coupon_code: appliedCoupon ? appliedCoupon.code : null,
      total: total,
      items: bag,
      save_address: fd.get('save_address') ? true : false,
    };

    try {
      const res = await fetch("{{ route('checkout.placeOrder') }}", {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(payload)
      });

      const data = await res.json();

      if (data.success) {
        // Save to client storage so confirmation renders seamlessly
        try { sessionStorage.setItem('lc_last_order', JSON.stringify(data.order)); } catch {}
        try { sessionStorage.setItem('lc_cart', '[]'); } catch {}
        if (typeof updateCartUI === 'function') updateCartUI();

        window.location.href = data.redirect_url;
      } else {
        throw new Error(data.message || 'Unable to place order. Please review your details.');
      }
    } catch (err) {
      if (errorBox) {
        errorBox.textContent = err.message || 'Network error placing order. Please try again.';
        errorBox.classList.remove('hidden');
      }
      submitBtn.disabled = false;
      submitBtn.textContent = 'Place Order';
    }
  });

  renderSummary();

})();
</script>
@endpush

</x-storefront-layout>
