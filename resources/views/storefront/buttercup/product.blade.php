{{--
    ======================================================================
    THEME: BUTTERCUP — SINGLE PRODUCT DETAIL PAGE
    ======================================================================
    File: resources/views/storefront/buttercup/product.blade.php
    Controller: App\Http\Controllers\StorefrontController@product
    ViewModel Injections:
      - $product:         Eloquent Product (with colors.images, colors.variants)
      - $related:         Comfort pairing recommendations
    Key Interactions:
      - Soft pastel image carousel with full zoom
      - Color swatch selector with instant image swapping
      - Non-wired size guide modal & stock warning badge
      - Delivery pincode serviceability checker
      - Verified customer comfort reviews
--}}
<x-storefront-layout :theme="$theme" :product="$product">
<div class="max-w-7xl mx-auto px-6 lg:px-10 py-10">

  <nav class="font-body text-xs flex items-center gap-2 mb-8 opacity-50 text-[#4A3B3A]">
    <a href="{{ route('home') }}" class="hover:opacity-100">Home</a>
    <span>/</span>
    <a href="{{ route('category.show', $product->category->slug) }}" class="hover:opacity-100">{{ $product->category->name }}</a>
    <span>/</span>
    <span style="color:#D89AA0">{{ $product->name }}</span>
  </nav>

  <div class="grid lg:grid-cols-2 gap-12 lg:gap-16">

    {{-- Image gallery --}}
    <div>
      <div class="aspect-[3/4] rounded-[28px] overflow-hidden mb-3" style="background:#D89AA011; aspect-ratio: 3/4;">
        <img id="mainImg" src="{{ $product->cover_image }}" alt="{{ $product->name }}" class="w-full h-full object-cover transition-all duration-500">
      </div>
      <div class="flex gap-2 overflow-x-auto pb-1">
        @foreach ($product->colors as $color)
          @foreach ($color->images->take(4) as $img)
            <button onclick="document.getElementById('mainImg').src='{{ $img->url }}'"
                    class="flex-shrink-0 w-16 aspect-[3/4] rounded-2xl overflow-hidden border-2 transition-all hover:opacity-100 opacity-60"
                    style="border-color:#D89AA044; aspect-ratio: 3/4;">
              <img src="{{ $img->url }}" class="w-full h-full object-cover">
            </button>
          @endforeach
        @endforeach
      </div>
    </div>

    {{-- Product info --}}
    <div class="flex flex-col gap-5">
      <div>
        <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-widest mb-1">
          @if($product->brand)
            <span class="text-[#4A3B3A]/80">{{ $product->brand->name }}</span>
            <span class="text-[#4A3B3A]/30">·</span>
          @endif
          <span class="text-[#7F9272]">{{ $product->category->name }}</span>
        </div>
        <h1 class="font-display text-3xl md:text-4xl text-[#4A3B3A]">{{ $product->name }}</h1>
        @if($product->badge)
          <span class="inline-block mt-2 bg-[#A8B89A] text-[#FFFAF5] text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-widest">{{ $product->badge }}</span>
        @endif
      </div>

      <p class="font-display text-3xl text-[#B97680] font-bold" id="currentPrice">
        ₹{{ number_format($product->starting_price, 0) }}
        <span id="comparePrice" class="font-body text-sm opacity-40 line-through hidden ml-2"></span>
      </p>

      @if($product->description)
        <div class="font-body text-sm leading-relaxed opacity-85 text-[#4A3B3A] product-rich-text">{!! $product->description !!}</div>
      @endif

      {{-- Color selector --}}
      <div>
        <p class="font-body text-xs font-bold uppercase tracking-widest mb-3 opacity-50 text-[#4A3B3A]">
          Shade: <span id="selectedColorName" style="color:#D89AA0">{{ $product->colors->first()?->name }}</span>
        </p>
        <div class="flex gap-3 flex-wrap" id="colorSwatches">
          @foreach ($product->colors as $i => $color)
            <button onclick="selectColor({{ $i }}, '{{ $color->name }}')"
                    class="w-9 h-9 rounded-full border-2 transition-all hover:scale-110"
                    style="background:{{ $color->hex ?? '#ccc' }};border-color:{{ $i===0 ? $color->hex : 'transparent' }}"
                    data-color-index="{{ $i }}"
                    title="{{ $color->name }}"></button>
          @endforeach
        </div>
      </div>

      {{-- Size selector --}}
      <div>
        <div class="flex items-center justify-between mb-3">
          <div class="flex items-center gap-3">
            <p class="font-body text-xs font-bold uppercase tracking-widest opacity-50 text-[#4A3B3A]">
              Size: <span id="selectedSizeName" style="color:#D89AA0">—</span>
            </p>
            <p id="stockWarning" class="font-body text-xs text-red-400 font-semibold hidden"></p>
          </div>
          <button type="button" 
                  class="pdp-size-guide-trigger text-xs font-bold text-[#D89AA0] hover:text-[#B97680] underline transition-colors cursor-pointer flex items-center gap-1">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16m-7 6h7"/></svg>
            Size Guide
          </button>
        </div>
        <div class="flex gap-2 flex-wrap" id="sizePills"></div>
      </div>

      <button id="addBtn" onclick="handleAdd()" disabled
              class="mt-2 py-4 text-sm font-bold uppercase tracking-widest transition-all disabled:opacity-40 bg-[#D89AA0] text-[#FFFAF5] hover:bg-[#B97680] rounded-full">
        Select a Size
      </button>

      <div class="flex gap-3">
        <button onclick="toggleWishlist({{ $product->id }})"
                class="flex-1 py-3 text-xs font-bold uppercase tracking-widest transition-all border border-[#D89AA0] text-[#4A3B3A] rounded-full flex items-center justify-center gap-2">
          <i class="fa-regular fa-heart text-sm"></i>
          Wishlist
        </button>
        <a href="{{ route('compare') }}?ids={{ $product->id }}"
           class="flex-1 py-3 text-xs font-bold uppercase tracking-widest transition-all border border-[#D89AA0] text-[#4A3B3A] rounded-full flex items-center justify-center">
          Compare
        </a>
      </div>

      {{-- Pincode Checker --}}
      <div class="border-t pt-5" style="border-color:#D89AA022">
        <label for="buttercupPincode" class="block font-body text-xs font-bold uppercase tracking-widest mb-2 text-[#4A3B3A] flex items-center gap-1.5">
          <svg class="w-4 h-4 text-[#D89AA0]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
          Check Delivery Availability
        </label>
        <div class="flex gap-2">
          <input type="text" id="buttercupPincode" maxlength="6" pattern="[0-9]{6}" placeholder="Enter 6-digit PIN code"
                 onkeydown="if(event.key==='Enter'){event.preventDefault();checkButtercupPincode();}"
                 class="flex-1 bg-white border rounded-full px-4 py-2.5 text-xs text-[#4A3B3A] outline-none" style="border-color:#D89AA044">
          <button type="button" onclick="checkButtercupPincode()"
                  class="px-5 py-2.5 rounded-full text-xs font-bold uppercase tracking-wider text-white transition-all hover:opacity-90"
                  style="background:#D89AA0">
            Check
          </button>
        </div>
        <div id="buttercupPinResult" class="hidden mt-3 text-xs font-body"></div>
      </div>

      {{-- Available Add-ons --}}
      @if($product->addons->isNotEmpty())
        <div class="border-t pt-3" style="border-color:#D89AA022">
          <p class="text-xs font-bold uppercase tracking-widest text-[#4A3B3A]/80 mb-2 flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5 text-[#D89AA0]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            Available Add-ons
          </p>
          <div class="space-y-1.5">
            @foreach($product->addons as $addon)
              <label class="flex items-center justify-between text-xs text-[#4A3B3A] bg-[#FFFAF5] hover:bg-[#FFF4EC] border border-[#D89AA033] hover:border-[#D89AA066] px-3.5 py-2.5 rounded-xl cursor-pointer transition select-none">
                <div class="flex items-center gap-2.5">
                  <input type="checkbox"
                         class="pdp-addon-checkbox rounded border-[#D89AA066] text-[#B97680] focus:ring-[#D89AA0] w-4 h-4 cursor-pointer accent-[#B97680]"
                         data-price="{{ $addon->price }}"
                         data-name="{{ $addon->name }}"
                         data-id="{{ $addon->id }}"
                         onchange="if(typeof updateProductAddonPricing === 'function') updateProductAddonPricing()">
                  <span class="font-medium text-[#4A3B3A]">{{ $addon->name }}</span>
                </div>
                <span class="font-bold font-mono text-[#B97680]">+₹{{ number_format($addon->price, 0) }}</span>
              </label>
            @endforeach
          </div>
        </div>
      @endif
    </div>
  </div>

  {{-- ═══════════════════════════════════════════════
       PRODUCT HIGHLIGHTS & ACCORDION (Dedicated Division)
  ═══════════════════════════════════════════════ --}}
  <div class="mt-16 pt-12 border-t space-y-8" style="border-color:#D89AA022">
    @if($product->long_description)
      <div class="space-y-4">
        <div class="flex items-center gap-2.5">
          <span class="w-1 h-5 rounded-full bg-gradient-to-b from-[#D89AA0] to-[#B97680] inline-block shrink-0"></span>
          <h3 class="font-display text-lg md:text-xl font-bold text-[#4A3B3A] uppercase tracking-wider">Product Highlights</h3>
        </div>
        <div class="font-body text-sm leading-relaxed opacity-85 text-[#4A3B3A] product-rich-text">{!! $product->long_description !!}</div>
      </div>
    @endif

    {{-- Delivery & Returns Accordion --}}
    <div class="border-t pt-4 divide-y" style="border-color:#D89AA022; --tw-divide-opacity: 0.15; divide-color: #D89AA0;">
      {{-- Delivery --}}
      <div x-data="{ open: false }" class="py-4">
        <button type="button" @click="open = !open" class="flex items-center justify-between w-full text-left font-display text-sm md:text-base font-bold text-[#4A3B3A] hover:text-[#B97680] transition">
          <span class="flex items-center gap-2">
            <svg class="w-4 h-4 text-[#D89AA0]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
            Delivery Information
          </span>
          <svg class="w-4 h-4 text-[#D89AA0] transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
        </button>
        <div x-show="open" x-transition class="mt-3 font-body text-xs md:text-sm text-[#4A3B3A]/80 leading-relaxed pl-6">
          {{ \App\Models\Setting::get('product_delivery_info', 'Ships within 2–3 business days. Free standard delivery on orders above ₹999. Express doorstep delivery available at checkout.') }}
        </div>
      </div>
      {{-- Returns & Exchange --}}
      <div x-data="{ open: false }" class="py-4">
        <button type="button" @click="open = !open" class="flex items-center justify-between w-full text-left font-display text-sm md:text-base font-bold text-[#4A3B3A] hover:text-[#B97680] transition">
          <span class="flex items-center gap-2">
            <svg class="w-4 h-4 text-[#D89AA0]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            Returns &amp; Exchange
          </span>
          <svg class="w-4 h-4 text-[#D89AA0] transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
        </button>
        <div x-show="open" x-transition class="mt-3 font-body text-xs md:text-sm text-[#4A3B3A]/80 leading-relaxed pl-6">
          {{ \App\Models\Setting::get('product_returns_info', 'Hassle-free returns within 15 days of delivery. Items must be unused, unwashed, and in original packaging with tags intact.') }}
        </div>
      </div>
    </div>
  </div>

  {{-- Customer Reviews --}}
  <div class="mt-20">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-10 pb-6 border-b" style="border-color:#D89AA022">
      <div>
        <p class="text-[#7F9272] text-xs font-bold uppercase tracking-widest mb-1">Customer Reviews</p>
        <h3 class="font-display text-2xl md:text-3xl text-[#4A3B3A]">What Customers Say</h3>
        @php
          $revCount = $product->approvedReviews->count();
          $avgRating = $revCount ? round($product->approvedReviews->avg('rating'), 1) : 5.0;
        @endphp
        <div class="flex items-center gap-2 mt-2">
          <div class="flex text-[#D89AA0]">
            @for($i = 1; $i <= 5; $i++)
              <span>{{ $i <= round($avgRating) ? '★' : '☆' }}</span>
            @endfor
          </div>
          <span class="font-bold text-sm text-[#4A3B3A]">{{ $avgRating }}</span>
          <span class="text-xs opacity-50 text-[#4A3B3A]">({{ $revCount }} {{ Str::plural('review', $revCount) }})</span>
        </div>
      </div>
      <button onclick="toggleButtercupReviewForm()"
              class="py-3 px-6 rounded-full text-xs font-bold uppercase tracking-widest border border-[#D89AA0] text-[#D89AA0] hover:bg-[#D89AA0] hover:text-white transition-all">
        Write a Review
      </button>
    </div>

    {{-- Review Form --}}
    <div id="buttercupReviewFormWrap" class="hidden mb-10 bg-[#FFFAF5] rounded-[28px] border p-6 md:p-8" style="border-color:#D89AA044">
      <h4 class="font-display text-xl mb-4 text-[#4A3B3A]">Share Your Experience</h4>
      <form id="buttercupReviewForm" onsubmit="event.preventDefault(); submitButtercupReview(this);" class="space-y-4">
        @csrf
        <div>
          <label class="block text-xs font-bold uppercase tracking-widest opacity-60 text-[#4A3B3A] mb-2">Rating</label>
          <div class="flex gap-2 text-2xl cursor-pointer" id="buttercupStarSelector">
            @for($s = 1; $s <= 5; $s++)
              <span class="buttercup-star text-[#D89AA0]" data-val="{{ $s }}">★</span>
            @endfor
          </div>
          <input type="hidden" name="rating" id="buttercupRatingInput" value="5" required>
        </div>
        <div class="grid sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-bold uppercase tracking-widest opacity-60 text-[#4A3B3A] mb-1">Your Name *</label>
            <input type="text" name="customer_name" required value="{{ Auth::user()?->name ?? '' }}" class="w-full bg-white border rounded-2xl px-4 py-2.5 text-xs text-[#4A3B3A] outline-none" style="border-color:#D89AA044">
          </div>
          <div>
            <label class="block text-xs font-bold uppercase tracking-widest opacity-60 text-[#4A3B3A] mb-1">Headline</label>
            <input type="text" name="title" placeholder="Summary of your review" class="w-full bg-white border rounded-2xl px-4 py-2.5 text-xs text-[#4A3B3A] outline-none" style="border-color:#D89AA044">
          </div>
        </div>
        <div>
          <label class="block text-xs font-bold uppercase tracking-widest opacity-60 text-[#4A3B3A] mb-1">Review *</label>
          <textarea name="comment" rows="3" required placeholder="What did you love about this item?" class="w-full bg-white border rounded-2xl p-4 text-xs text-[#4A3B3A] outline-none" style="border-color:#D89AA044"></textarea>
        </div>
        <button type="submit" class="py-3 px-8 rounded-full text-xs font-bold uppercase tracking-widest text-white transition-all" style="background:#D89AA0">
          Submit Review
        </button>
      </form>
      <div id="buttercupReviewSuccess" class="hidden text-center py-6 text-[#7F9272] font-semibold text-sm">
        ✓ Thank you! Your review has been submitted for moderation.
      </div>
    </div>

    {{-- Reviews list --}}
    @if($product->approvedReviews->count())
      <div class="grid md:grid-cols-2 gap-4">
        @foreach($product->approvedReviews as $rev)
          <div class="bg-[#FFFAF5] rounded-[24px] border p-5" style="border-color:#D89AA022">
            <div class="flex items-center justify-between mb-2">
              <span class="text-[#D89AA0] text-sm">
                @for($i=1; $i<=5; $i++) {{ $i <= $rev->rating ? '★' : '☆' }} @endfor
              </span>
              <span class="text-[10px] opacity-40 text-[#4A3B3A]">{{ $rev->created_at->format('d M Y') }}</span>
            </div>
            @if($rev->title)
              <h5 class="font-display font-semibold text-sm text-[#4A3B3A] mb-1">{{ $rev->title }}</h5>
            @endif
            <p class="font-body text-xs opacity-70 text-[#4A3B3A] leading-relaxed mb-3">{{ $rev->comment }}</p>
            <div class="flex items-center gap-2 text-xs">
              <span class="font-bold text-[#4A3B3A]">{{ $rev->customer_name }}</span>
              <span class="text-[10px] bg-[#A8B89A33] text-[#7F9272] px-2 py-0.5 rounded-full font-bold">Verified</span>
            </div>
          </div>
        @endforeach
      </div>
    @else
      <div class="bg-[#FFFAF5] rounded-[28px] border p-10 text-center" style="border-color:#D89AA022">
        <p class="text-sm opacity-50 text-[#4A3B3A] mb-2">No reviews yet for this product.</p>
        <button onclick="toggleButtercupReviewForm()" class="text-xs font-bold underline" style="color:#D89AA0">
          Be the first to leave a review →
        </button>
      </div>
    @endif
  </div>

  @if ($related->count())
    <div class="mt-20">
      <h3 class="font-display text-2xl text-center mb-10 text-[#4A3B3A]">You Might Also Like</h3>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
        @foreach ($related as $rp)
          @include('storefront.buttercup.partials.product-card', ['product' => $rp])
        @endforeach
      </div>
    </div>
  @endif
</div>

@push('scripts')
<script>
const pd = @json($product->toCardArray());
let selColor = 0, selSize = null;

function selectColor(idx, name) {
  selColor = idx; selSize = null;
  document.getElementById('selectedColorName').textContent = name;
  document.querySelectorAll('#colorSwatches button').forEach((b,i) => {
    b.style.borderColor = i===idx ? pd.colors[i].hex : 'transparent';
    b.style.transform = i===idx ? 'scale(1.15)' : 'scale(1)';
  });
  const c = pd.colors[idx];
  if (c.images.length) document.getElementById('mainImg').src = c.images[0];
  renderSizes(idx);
  updateBtn();
}

function renderSizes(ci) {
  const c = pd.colors[ci];
  document.getElementById('sizePills').innerHTML = c.sizes.map(s => `
    <button onclick="selectSize('${s.size}', ${s.price}, ${s.compareAtPrice||0}, ${s.stock})"
            class="px-4 py-2 text-xs font-bold border rounded-full transition-all ${!s.inStock ? 'opacity-30 cursor-not-allowed' : ''}"
            style="border-color:#D89AA044;color:#4A3B3A" ${!s.inStock ? 'disabled' : ''}>
      ${s.size}${!s.inStock ? ' (Out)' : ''}
    </button>`).join('');
}

let currentBasePrice = {{ (float) $product->starting_price }};

function getSelectedAddonsTotal() {
  let total = 0;
  document.querySelectorAll('.pdp-addon-checkbox:checked').forEach(cb => {
    total += Number(cb.dataset.price || 0);
  });
  return total;
}

function getSelectedAddonNames() {
  const names = [];
  document.querySelectorAll('.pdp-addon-checkbox:checked').forEach(cb => {
    names.push(cb.dataset.name);
  });
  return names;
}

function updateProductAddonPricing() {
  const addonTotal = getSelectedAddonsTotal();
  const finalPrice = currentBasePrice + addonTotal;
  const cpEl = document.getElementById('currentPrice');
  if (cpEl && cpEl.firstChild) {
    cpEl.firstChild.textContent = '₹' + finalPrice.toLocaleString('en-IN') + ' ';
  }
}

function selectSize(size, price, compareAt, stock) {
  selSize = size;
  currentBasePrice = price;
  document.getElementById('selectedSizeName').textContent = size;
  updateProductAddonPricing();
  const cp = document.getElementById('comparePrice');
  compareAt ? (cp.textContent = '₹' + compareAt.toLocaleString('en-IN'), cp.classList.remove('hidden')) : cp.classList.add('hidden');
  const sw = document.getElementById('stockWarning');
  (stock > 0 && stock <= 3) ? (sw.textContent = 'Only ' + stock + ' left!', sw.classList.remove('hidden')) : sw.classList.add('hidden');
  document.querySelectorAll('#sizePills button').forEach(b => {
    const active = b.textContent.trim().startsWith(size);
    b.style.background = active ? '#D89AA0' : '';
    b.style.color = active ? '#FFFAF5' : '#4A3B3A';
    b.style.borderColor = active ? '#D89AA0' : '#D89AA044';
  });
  updateBtn();
}

function updateBtn() {
  const b = document.getElementById('addBtn');
  b.disabled = !selSize;
  b.textContent = selSize ? 'Add to Cart' : 'Select a Size';
}

function handleAdd() {
  if (!selSize) return;
  const c = pd.colors[selColor], v = c.sizes.find(s => s.size === selSize);
  const addonTotal = getSelectedAddonsTotal();
  const finalPrice = (v ? v.price : currentBasePrice) + addonTotal;
  const addonNames = getSelectedAddonNames();
  const extraDesc  = addonNames.length ? ' (Add-ons: ' + addonNames.join(', ') + ')' : '';
  addToCart(pd.id, pd.name + extraDesc, c.name, selSize, finalPrice, c.images[0] || pd.coverImage);
}

function toggleWishlist(id) {
  let wl = JSON.parse(localStorage.getItem('lc_wishlist') || '[]');
  wl = wl.includes(id) ? wl.filter(x => x !== id) : [...wl, id];
  localStorage.setItem('lc_wishlist', JSON.stringify(wl));
}

async function checkButtercupPincode() {
  const pin = document.getElementById('buttercupPincode')?.value.trim();
  const resEl = document.getElementById('buttercupPinResult');
  if (!pin || pin.length !== 6 || !/^\d{6}$/.test(pin)) {
    if (resEl) {
      resEl.className = 'mt-3 text-xs text-rose-500 font-semibold';
      resEl.textContent = 'Please enter a valid 6-digit PIN code.';
      resEl.classList.remove('hidden');
    }
    return;
  }
  try {
    const res = await fetch('{{ route('pincode.check') }}', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
      body: JSON.stringify({ pincode: pin })
    });
    const data = await res.json();
    if (data.success && data.data) {
      const info = data.data;
      resEl.className = 'mt-3 text-xs p-3 rounded-2xl bg-white border space-y-1 text-[#4A3B3A]';
      resEl.style.borderColor = '#D89AA044';
      resEl.innerHTML = `
        <p class="font-bold text-[#7F9272]">✓ Deliverable to ${info.city ? info.city + ', ' + info.state : 'your area'} (${info.pincode})</p>
        <p class="opacity-70">Estimated Delivery: <strong>${info.estimated_days} days</strong> (${info.estimated_date})</p>
        <p class="font-semibold ${info.cod_available ? 'text-[#7F9272]' : 'text-amber-600'}">
          ${info.cod_available ? '✓ Cash on Delivery (COD) Available' : 'ℹ Online payment only'}
        </p>
      `;
    } else {
      resEl.className = 'mt-3 text-xs text-amber-700';
      resEl.textContent = data.message || 'Service available via standard courier.';
    }
  } catch {
    resEl.className = 'mt-3 text-xs text-rose-500';
    resEl.textContent = 'Error checking pincode.';
  } finally {
    resEl.classList.remove('hidden');
  }
}

function toggleButtercupReviewForm() {
  const wrap = document.getElementById('buttercupReviewFormWrap');
  if (wrap) {
    wrap.classList.toggle('hidden');
    if (!wrap.classList.contains('hidden')) wrap.scrollIntoView({ behavior: 'smooth', block: 'center' });
  }
}

document.querySelectorAll('#buttercupStarSelector .buttercup-star').forEach(star => {
  star.addEventListener('click', () => {
    const val = Number(star.dataset.val);
    document.getElementById('buttercupRatingInput').value = val;
    document.querySelectorAll('#buttercupStarSelector .buttercup-star').forEach(s => {
      s.textContent = Number(s.dataset.val) <= val ? '★' : '☆';
    });
  });
});

async function submitButtercupReview(form) {
  const btn = form.querySelector('button[type="submit"]');
  btn.disabled = true;
  btn.textContent = 'Submitting...';
  const payload = Object.fromEntries(new FormData(form).entries());
  try {
    const res = await fetch('{{ route('products.review', $product->slug) }}', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
      body: JSON.stringify(payload)
    });
    const data = await res.json();
    if (data.success) {
      form.classList.add('hidden');
      document.getElementById('buttercupReviewSuccess')?.classList.remove('hidden');
    } else {
      alert(data.message || 'Error submitting review.');
    }
  } catch {
    alert('An unexpected error occurred.');
  } finally {
    btn.disabled = false;
    btn.textContent = 'Submit Review';
  }
}

document.addEventListener('DOMContentLoaded', () => selectColor(0, pd.colors[0]?.name || ''));
</script>
@endpush

@include('storefront.common.size-guide-modal', ['product' => $product, 'theme' => $theme])

</x-storefront-layout>
