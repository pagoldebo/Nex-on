{{-- resources/views/storefront/organic/checkout.blade.php
     Organic Market Checkout Page: Shipping Form, Payment Method Selection, Coupon Application, and Order Placement Bridge. --}}
<x-storefront-layout :theme="$theme" title="Secure Checkout">

  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
    
    {{-- Breadcrumbs --}}
    <nav class="text-xs text-stone-500 font-medium flex items-center gap-2 mb-6">
      <a href="{{ route('home') }}" class="hover:text-organic-600 transition">Home</a>
      <span>/</span>
      <a href="{{ route('cart') }}" class="hover:text-organic-600 transition">Basket</a>
      <span>/</span>
      <span class="text-stone-900 font-bold">Checkout</span>
    </nav>

    <div class="mb-8">
      <h1 class="text-2xl sm:text-3xl font-black text-stone-900 font-display">Farm-to-Door Checkout</h1>
      <p class="text-xs sm:text-sm text-stone-500 mt-1 font-light">Confirm delivery address and choose your preferred payment option.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
      
      {{-- LEFT: Shipping & Payment Form --}}
      <form id="organicCheckoutForm" class="lg:col-span-7 space-y-6" onsubmit="event.preventDefault(); placeOrganicOrder();">
        @csrf

        {{-- Saved Address Picker for logged in users --}}
        @if(isset($addresses) && $addresses->isNotEmpty())
          <div class="bg-white rounded-3xl border border-stone-200 p-5 shadow-xs">
            <label for="savedAddressPicker" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-2">
              Deliver to Saved Address:
            </label>
            <select id="savedAddressPicker" class="w-full bg-stone-50 border border-stone-300 rounded-xl p-3 text-xs font-semibold text-stone-800 focus:outline-none focus:border-organic-500">
              <option value="">-- Enter a new delivery address --</option>
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
                  {{ $addr->name }} ({{ ucfirst($addr->type) }}) — {{ $addr->address1 }}, {{ $addr->city }} ({{ $addr->pincode }})
                </option>
              @endforeach
            </select>
          </div>
        @endif

        {{-- Shipping Address Fields --}}
        <div class="bg-white rounded-3xl border border-stone-200 p-6 sm:p-8 shadow-xs space-y-4">
          <h3 class="text-base font-black text-stone-900 font-display pb-3 border-b border-stone-100 flex items-center gap-2">
            <svg class="w-5 h-5 text-organic-500" viewBox="0 0 32 32"><use xlink:href="#organic-delivery"></use></svg>
            1. Delivery Address
          </h3>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label for="coName" class="block text-xs font-bold text-stone-700 uppercase mb-1">Full Name</label>
              <input type="text" id="coName" name="name" required value="{{ $user->name ?? '' }}" placeholder="e.g. Ramesh Patel" class="w-full bg-stone-50 border border-stone-200 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-stone-800 focus:outline-none focus:border-organic-500">
            </div>
            <div>
              <label for="coPhone" class="block text-xs font-bold text-stone-700 uppercase mb-1">Mobile Phone (10 Digits)</label>
              <input type="tel" id="coPhone" name="phone" required maxlength="10" pattern="[0-9]{10}" placeholder="e.g. 9876543210" class="w-full bg-stone-50 border border-stone-200 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-stone-800 focus:outline-none focus:border-organic-500">
            </div>
          </div>

          <div>
            <label for="coEmail" class="block text-xs font-bold text-stone-700 uppercase mb-1">Email for Delivery Updates</label>
            <input type="email" id="coEmail" name="email" required value="{{ $user->email ?? '' }}" placeholder="name@example.com" class="w-full bg-stone-50 border border-stone-200 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-stone-800 focus:outline-none focus:border-organic-500">
          </div>

          <div>
            <label for="coAddress" class="block text-xs font-bold text-stone-700 uppercase mb-1">Street Address / Flat No.</label>
            <input type="text" id="coAddress" name="address" required placeholder="Apartment, Street name, Landmark" class="w-full bg-stone-50 border border-stone-200 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-stone-800 focus:outline-none focus:border-organic-500">
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
              <label for="coCity" class="block text-xs font-bold text-stone-700 uppercase mb-1">City</label>
              <input type="text" id="coCity" name="city" required placeholder="Mumbai" class="w-full bg-stone-50 border border-stone-200 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-stone-800 focus:outline-none focus:border-organic-500">
            </div>
            <div>
              <label for="coState" class="block text-xs font-bold text-stone-700 uppercase mb-1">State</label>
              <input type="text" id="coState" name="state" required placeholder="Maharashtra" class="w-full bg-stone-50 border border-stone-200 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-stone-800 focus:outline-none focus:border-organic-500">
            </div>
            <div>
              <label for="coPincode" class="block text-xs font-bold text-stone-700 uppercase mb-1">Pincode (6 Digits)</label>
              <input type="text" id="coPincode" name="pincode" required maxlength="6" pattern="[0-9]{6}" placeholder="400050" class="w-full bg-stone-50 border border-stone-200 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-stone-800 focus:outline-none focus:border-organic-500">
            </div>
          </div>

          @auth
            <div class="pt-2">
              <label class="flex items-center gap-2 cursor-pointer text-xs text-stone-600 font-semibold">
                <input type="checkbox" id="coSaveAddress" name="save_address" value="1" checked class="rounded text-organic-600 focus:ring-organic-500">
                <span>Save to my address book for next harvest deliveries</span>
              </label>
            </div>
          @endauth
        </div>

        {{-- Payment Methods --}}
        <div class="bg-white rounded-3xl border border-stone-200 p-6 sm:p-8 shadow-xs space-y-4">
          <h3 class="text-base font-black text-stone-900 font-display pb-3 border-b border-stone-100 flex items-center gap-2">
            <svg class="w-5 h-5 text-organic-500" viewBox="0 0 24 24"><use xlink:href="#organic-seal"></use></svg>
            2. Payment Method
          </h3>

          <div class="space-y-3">
            {{-- UPI --}}
            <label class="flex items-center justify-between p-4 rounded-2xl border-2 border-stone-200 hover:border-organic-400 bg-stone-50/50 cursor-pointer transition">
              <div class="flex items-center gap-3">
                <input type="radio" name="payment" value="upi" checked class="text-organic-600 focus:ring-organic-500" onchange="updateOrganicTotals()">
                <div>
                  <span class="text-xs sm:text-sm font-bold text-stone-900 block">Instant UPI &amp; QR</span>
                  <span class="text-[11px] text-stone-500">Google Pay, PhonePe, Paytm, BHIM UPI</span>
                </div>
              </div>
              <span class="text-[10px] font-black text-organic-700 bg-organic-100 px-2 py-0.5 rounded-full">Recommended</span>
            </label>

            {{-- Card --}}
            <label class="flex items-center justify-between p-4 rounded-2xl border-2 border-stone-200 hover:border-organic-400 bg-stone-50/50 cursor-pointer transition">
              <div class="flex items-center gap-3">
                <input type="radio" name="payment" value="card" class="text-organic-600 focus:ring-organic-500" onchange="updateOrganicTotals()">
                <div>
                  <span class="text-xs sm:text-sm font-bold text-stone-900 block">Credit / Debit Card</span>
                  <span class="text-[11px] text-stone-500">Visa, MasterCard, RuPay, Corporate Cards</span>
                </div>
              </div>
              <span class="text-xs text-stone-400 font-bold">128-bit SSL</span>
            </label>

            {{-- COD --}}
            @if($enableCod ?? true)
              <label class="flex items-center justify-between p-4 rounded-2xl border-2 border-stone-200 hover:border-organic-400 bg-stone-50/50 cursor-pointer transition">
                <div class="flex items-center gap-3">
                  <input type="radio" name="payment" value="cod" class="text-organic-600 focus:ring-organic-500" onchange="updateOrganicTotals()">
                  <div>
                    <span class="text-xs sm:text-sm font-bold text-stone-900 block">Cash on Delivery (COD)</span>
                    <span class="text-[11px] text-stone-500">Pay cash upon doorstep delivery</span>
                  </div>
                </div>
                <span class="text-xs font-bold text-citrus">+₹{{ $codCharge ?? 40 }} fee</span>
              </label>
            @endif
          </div>

          {{-- Error Banner --}}
          <div id="checkoutErrorBox" class="hidden p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-medium"></div>
        </div>

      </form>

      {{-- RIGHT: Sticky Order Summary & Coupon --}}
      <div class="lg:col-span-5 space-y-6">
        
        <div class="bg-white rounded-3xl border border-stone-200 p-6 shadow-xs sticky top-24 space-y-4">
          <h3 class="text-base font-black text-stone-900 font-display pb-3 border-b border-stone-100">
            Order Review
          </h3>

          {{-- Selected Items Container --}}
          <div id="checkoutItemsList" class="divide-y divide-stone-100 max-h-56 overflow-y-auto pr-1"></div>

          {{-- Coupon Code Input --}}
          <div class="pt-3 border-t border-stone-100">
            <div class="flex gap-2">
              <input type="text" 
                     id="checkoutCouponCode" 
                     placeholder="PROMO CODE" 
                     class="w-full bg-stone-50 border border-stone-200 rounded-xl px-3 py-2 text-xs uppercase font-mono text-stone-800 focus:outline-none focus:border-organic-500">
              <button type="button" 
                      onclick="applyOrganicCoupon()" 
                      class="btn-organic px-4 py-2 rounded-xl text-xs font-bold shadow-xs shrink-0">
                Apply
              </button>
            </div>
            <p id="checkoutCouponMsg" class="text-xs mt-1.5 hidden font-semibold"></p>
          </div>

          {{-- Calculation Table --}}
          <div class="pt-4 border-t border-stone-100 space-y-2 text-xs text-stone-600">
            <div class="flex justify-between">
              <span>Produce Subtotal:</span>
              <span id="coSubtotal" class="font-bold text-stone-900">₹0.00</span>
            </div>
            <div class="flex justify-between">
              <span>Farm Delivery:</span>
              <span id="coShipping" class="font-bold text-organic-700">Free</span>
            </div>
            <div id="coCodRow" class="flex justify-between hidden text-stone-600">
              <span>COD Handling Charge:</span>
              <span class="font-bold text-stone-900">₹{{ $codCharge ?? 40 }}.00</span>
            </div>
            <div id="coDiscountRow" class="flex justify-between text-citrus hidden">
              <span>Discount (<span id="coDiscountLabel"></span>):</span>
              <span id="coDiscountValue" class="font-bold">-₹0.00</span>
            </div>
          </div>

          <div class="pt-4 border-t border-stone-200 flex justify-between items-baseline">
            <span class="text-sm font-black text-stone-900">Grand Total:</span>
            <span id="coGrandTotal" class="text-2xl font-black text-organic-900 font-display">₹0.00</span>
          </div>

          <button type="button" 
                  id="placeOrderSubmitBtn"
                  onclick="placeOrganicOrder()" 
                  class="btn-organic block w-full py-4 text-center rounded-2xl font-black text-xs uppercase tracking-widest shadow-lg shadow-organic-500/25">
            Place Order Now
          </button>

          <p class="text-[10px] text-center text-stone-400">
            By placing order, you agree to Nexon Organic's Freshness Warranty and Terms.
          </p>

        </div>

      </div>

    </div>

  </div>

  <script>
    const FREE_THRESHOLD = {{ $freeShippingThreshold ?? 1499 }};
    const COD_FEE = {{ $codCharge ?? 40 }};
    const SHIPPING_FEE = {{ $shippingCost ?? 99 }};
    let organicAppliedCoupon = null;

    document.addEventListener('DOMContentLoaded', () => {
      renderOrganicCheckoutSummary();

      const picker = document.getElementById('savedAddressPicker');
      picker?.addEventListener('change', e => {
        const opt = e.target.selectedOptions[0];
        if (!opt || !opt.value) return;
        document.getElementById('coName').value = opt.dataset.name || '';
        document.getElementById('coPhone').value = opt.dataset.phone || '';
        document.getElementById('coAddress').value = opt.dataset.address1 || '';
        document.getElementById('coCity').value = opt.dataset.city || '';
        document.getElementById('coState').value = opt.dataset.state || '';
        document.getElementById('coPincode').value = opt.dataset.pincode || '';
      });
    });

    function renderOrganicCheckoutSummary() {
      const itemsContainer = document.getElementById('checkoutItemsList');
      if (!itemsContainer) return;

      if (!cart || cart.length === 0) {
        itemsContainer.innerHTML = '<p class="text-xs text-stone-400 py-4 text-center">Your basket is empty. Please add items before checking out.</p>';
        return;
      }

      itemsContainer.innerHTML = cart.map(item => `
        <div class="py-2.5 flex items-center justify-between text-xs gap-3">
          <div class="flex items-center gap-2.5 min-w-0">
            <img src="${item.image}" alt="${item.name}" class="w-10 h-10 rounded-lg object-contain bg-stone-50 border border-stone-200 shrink-0">
            <div class="truncate">
              <span class="font-bold text-stone-900 block truncate">${item.name}</span>
              <span class="text-[10px] text-stone-500">${item.size} • Qty: ${item.qty}</span>
            </div>
          </div>
          <span class="font-extrabold text-stone-900 shrink-0">₹${(item.price * item.qty).toLocaleString('en-IN', { minimumFractionDigits: 2 })}</span>
        </div>
      `).join('');

      updateOrganicTotals();
    }

    function updateOrganicTotals() {
      const subtotal = cartTotal();
      const subtotalEl = document.getElementById('coSubtotal');
      const shippingEl = document.getElementById('coShipping');
      const grandTotalEl = document.getElementById('coGrandTotal');
      const codRow = document.getElementById('coCodRow');
      const discountRow = document.getElementById('coDiscountRow');

      if (subtotalEl) subtotalEl.textContent = '₹' + subtotal.toLocaleString('en-IN', { minimumFractionDigits: 2 });

      let shipping = (subtotal >= FREE_THRESHOLD || subtotal === 0) ? 0 : SHIPPING_FEE;
      if (shippingEl) shippingEl.textContent = shipping === 0 ? 'Free' : '₹' + shipping.toLocaleString('en-IN', { minimumFractionDigits: 2 });

      const isCod = document.querySelector('input[name="payment"]:checked')?.value === 'cod';
      let cod = isCod ? COD_FEE : 0;
      if (codRow) codRow.classList.toggle('hidden', !isCod);

      let discount = 0;
      if (organicAppliedCoupon) {
        discount = organicAppliedCoupon.discount;
        if (discountRow) {
          discountRow.classList.remove('hidden');
          document.getElementById('coDiscountLabel').textContent = organicAppliedCoupon.code;
          document.getElementById('coDiscountValue').textContent = '-₹' + discount.toLocaleString('en-IN', { minimumFractionDigits: 2 });
        }
      } else {
        if (discountRow) discountRow.classList.add('hidden');
      }

      const total = Math.max(0, subtotal - discount + shipping + cod);
      if (grandTotalEl) grandTotalEl.textContent = '₹' + total.toLocaleString('en-IN', { minimumFractionDigits: 2 });
    }

    function applyOrganicCoupon() {
      const code = document.getElementById('checkoutCouponCode')?.value.trim();
      const msg = document.getElementById('checkoutCouponMsg');
      if (!code) return;

      fetch('{{ route('coupon.apply') }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').content },
        body: JSON.stringify({ code: code, subtotal: cartTotal() })
      })
      .then(r => r.json())
      .then(res => {
        if (msg) {
          msg.classList.remove('hidden', 'text-rose-600', 'text-organic-700');
          if (res.success) {
            msg.classList.add('text-organic-700');
            msg.textContent = `✓ Coupon applied! Saved ₹${res.discount}`;
            organicAppliedCoupon = { code: res.code, discount: res.discount };
            updateOrganicTotals();
          } else {
            msg.classList.add('text-rose-600');
            msg.textContent = res.message || 'Invalid coupon code.';
            organicAppliedCoupon = null;
            updateOrganicTotals();
          }
        }
      })
      .catch(() => {
        if (msg) {
          msg.classList.remove('hidden');
          msg.classList.add('text-rose-600');
          msg.textContent = 'Could not validate coupon.';
        }
      });
    }

    function placeOrganicOrder() {
      const btn = document.getElementById('placeOrderSubmitBtn');
      const errBox = document.getElementById('checkoutErrorBox');
      if (!cart || cart.length === 0) {
        alert('Your basket is empty!');
        return;
      }

      const form = document.getElementById('organicCheckoutForm');
      if (!form.reportValidity()) return;

      if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="animate-spin text-sm">⟳</span> Packing your harvest...';
      }
      if (errBox) errBox.classList.add('hidden');

      const payload = {
        shipping: {
          name: document.getElementById('coName').value.trim(),
          phone: document.getElementById('coPhone').value.trim(),
          email: document.getElementById('coEmail').value.trim(),
          address1: document.getElementById('coAddress').value.trim(),
          city: document.getElementById('coCity').value.trim(),
          state: document.getElementById('coState').value.trim(),
          pincode: document.getElementById('coPincode').value.trim()
        },
        payment: document.querySelector('input[name="payment"]:checked')?.value || 'upi',
        items: cart.map(item => ({
          product_id: item.productId,
          variant_id: item.variantId,
          sku: item.sku,
          name: item.name,
          color_name: item.colorName,
          size: item.size,
          price: item.price,
          qty: item.qty,
          image: item.image
        })),
        subtotal: cartTotal(),
        discount: organicAppliedCoupon ? organicAppliedCoupon.discount : 0,
        coupon_code: organicAppliedCoupon ? organicAppliedCoupon.code : null,
        save_address: document.getElementById('coSaveAddress')?.checked ? 1 : 0
      };

      fetch('{{ route('checkout.placeOrder') }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').content },
        body: JSON.stringify(payload)
      })
      .then(r => r.json())
      .then(res => {
        if (res.success && res.redirect_url) {
          // Clear cart
          cart = [];
          persistCart();
          updateCartUI();
          window.location.href = res.redirect_url;
        } else {
          if (btn) {
            btn.disabled = false;
            btn.textContent = 'Place Order Now';
          }
          if (errBox) {
            errBox.classList.remove('hidden');
            errBox.textContent = res.message || 'Order failed. Please check form details.';
          }
        }
      })
      .catch(err => {
        if (btn) {
          btn.disabled = false;
          btn.textContent = 'Place Order Now';
        }
        if (errBox) {
          errBox.classList.remove('hidden');
          errBox.textContent = 'Server connection error. Please try again.';
        }
      });
    }
  </script>

</x-storefront-layout>

