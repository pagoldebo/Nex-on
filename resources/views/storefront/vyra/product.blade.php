{{--
    ======================================================================
    THEME: VYRA — SINGLE PRODUCT DETAIL PAGE
    ======================================================================
    File: resources/views/storefront/vyra/product.blade.php
    Controller: App\Http\Controllers\StorefrontController@product
    ViewModel Injections:
      - $product:         Eloquent Product (with colors.images, colors.variants)
      - $related:         Related product suggestions for "Complete the Fit"
    Key Interactions:
      - Multi-angle gallery with thumbnail switching
      - Interactive color swatch selector (switches gallery images automatically)
      - Size variant pills with live stock & price updates
      - Universal Pincode delivery ETA checker
      - Customer impressions & moderation review submission form
--}}
<x-storefront-layout :theme="$theme" :title="$product->name">
<div class="max-w-7xl mx-auto px-6 lg:px-10 py-10">

  <nav class="font-body text-xs flex items-center gap-2 mb-8 opacity-50 text-[#F2F0EC] uppercase">
    <a href="{{ route('home') }}" class="hover:opacity-100">Home</a>
    <span>/</span>
    <a href="{{ route('category.show', $product->category->slug) }}" class="hover:opacity-100">{{ $product->category->name }}</a>
    <span>/</span>
    <span style="color:#FF3D5A">{{ $product->name }}</span>
  </nav>

  <div class="grid lg:grid-cols-2 gap-12 lg:gap-16">

    <div>
      <div class="aspect-[3/4] overflow-hidden mb-3" style="background:#FF3D5A11; aspect-ratio: 3/4;">
        <img id="mainImg" src="{{ $product->cover_image }}" alt="{{ $product->name }}" class="w-full h-full object-cover transition-all duration-500">
      </div>
      <div class="flex gap-2 overflow-x-auto pb-1">
        @foreach ($product->colors as $color)
          @foreach ($color->images->take(4) as $img)
            <button onclick="document.getElementById('mainImg').src='{{ asset('storage/'.$img->image_path) }}'"
                    class="flex-shrink-0 w-16 aspect-[3/4] overflow-hidden border-2 transition-all hover:opacity-100 opacity-60"
                    style="border-color:#FF3D5A44; aspect-ratio: 3/4;">
              <img src="{{ asset('storage/'.$img->image_path) }}" class="w-full h-full object-cover">
            </button>
          @endforeach
        @endforeach
      </div>
    </div>

    <div class="flex flex-col gap-5">
      <div>
        <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-widest mb-1">
          @if($product->brand)
            <span class="text-[#F2F0EC]">{{ $product->brand->name }}</span>
            <span class="text-white/30">·</span>
          @endif
          <span class="text-[#D4FF3D]">{{ $product->category->name }}</span>
        </div>
        <h1 class="font-display uppercase text-3xl md:text-4xl text-[#F2F0EC]">{{ $product->name }}</h1>
        @if($product->badge)
          <span class="inline-block mt-2 bg-[#D4FF3D] text-[#0E0E10] text-[10px] font-bold px-3 py-1 uppercase tracking-widest">{{ $product->badge }}</span>
        @endif
      </div>

      <p class="font-display text-3xl text-[#F2F0EC] font-bold" id="currentPrice">
        ₹{{ number_format($product->starting_price, 0) }}
        <span id="comparePrice" class="font-body text-sm opacity-40 line-through hidden ml-2"></span>
      </p>

      @if($product->description)
        <div class="font-body text-sm leading-relaxed opacity-75 text-[#F2F0EC] product-rich-text">{!! $product->description !!}</div>
      @endif

      <div>
        <p class="font-body text-xs font-bold uppercase tracking-widest mb-3 opacity-50 text-[#F2F0EC]">
          Shade: <span id="selectedColorName" style="color:#FF3D5A">{{ $product->colors->first()?->name }}</span>
        </p>
        <div class="flex gap-3 flex-wrap" id="colorSwatches">
          @foreach ($product->colors as $i => $color)
            <button onclick="selectColor({{ $i }}, '{{ $color->name }}')"
                    class="w-9 h-9 border-2 transition-all hover:scale-110"
                    style="background:{{ $color->hex ?? '#ccc' }};border-color:{{ $i===0 ? $color->hex : 'transparent' }}"
                    data-color-index="{{ $i }}"
                    title="{{ $color->name }}"></button>
          @endforeach
        </div>
      </div>

      <div>
        <div class="flex items-center justify-between mb-3">
          <p class="font-body text-xs font-bold uppercase tracking-widest opacity-50 text-[#F2F0EC]">
            Size: <span id="selectedSizeName" style="color:#FF3D5A">—</span>
          </p>
          <p id="stockWarning" class="font-body text-xs text-red-400 font-semibold hidden"></p>
        </div>
        <div class="flex gap-2 flex-wrap" id="sizePills"></div>
      </div>

      <button id="addBtn" onclick="handleAdd()" disabled
              class="mt-2 py-4 text-sm font-bold uppercase tracking-widest transition-all disabled:opacity-40 bg-[#FF3D5A] text-[#0E0E10] hover:bg-[#D4FF3D]"
              style="clip-path:polygon(4% 0,100% 0,96% 100%,0 100%)">
        Select a Size
      </button>

      <div class="flex gap-3">
        <button onclick="toggleWishlist({{ $product->id }})"
                class="flex-1 py-3 text-xs font-bold uppercase tracking-widest transition-all border border-[#FF3D5A] text-[#F2F0EC] flex items-center justify-center gap-2">
          <i class="fa-regular fa-heart text-sm"></i>
          Wishlist
        </button>
        <a href="{{ route('compare') }}?ids={{ $product->id }}"
           class="flex-1 py-3 text-xs font-bold uppercase tracking-widest transition-all border border-[#FF3D5A] text-[#F2F0EC] flex items-center justify-center">
          Compare
        </a>
      </div>

      {{-- Pincode Checker --}}
      <div class="border-t pt-5" style="border-color:#FF3D5A22">
        <label for="vyraPincode" class="block font-body text-xs font-bold uppercase tracking-widest mb-2 text-[#D4FF3D] flex items-center gap-1.5">
          <svg class="w-3.5 h-3.5 text-[#D4FF3D]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
          Check Delivery Availability
        </label>
        <div class="flex gap-2">
          <input type="text" id="vyraPincode" maxlength="6" pattern="[0-9]{6}" placeholder="ENTER 6-DIGIT PIN"
                 onkeydown="if(event.key==='Enter'){event.preventDefault();checkVyraPincode();}"
                 class="flex-1 bg-[#1A1A1D] border px-4 py-2.5 text-xs text-[#F2F0EC] uppercase outline-none focus:border-[#FF3D5A]" style="border-color:#FF3D5A44">
          <button type="button" onclick="checkVyraPincode()"
                  class="px-5 py-2.5 text-xs font-bold uppercase tracking-wider text-[#0E0E10] bg-[#D4FF3D] hover:bg-[#FF3D5A] hover:text-[#F2F0EC] transition-all"
                  style="clip-path:polygon(8% 0,100% 0,92% 100%,0 100%)">
            Check
          </button>
        </div>
        <div id="vyraPinResult" class="hidden mt-3 text-xs font-body"></div>
      </div>

      {{-- Available Add-ons --}}
      @if($product->addons->isNotEmpty())
        <div class="border-t pt-3" style="border-color:#FF3D5A22">
          <p class="text-xs font-bold uppercase tracking-widest text-[#D4FF3D] mb-2 flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5 text-[#D4FF3D]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            Available Add-ons
          </p>
          <div class="space-y-1.5">
            @foreach($product->addons as $addon)
              <label class="flex items-center justify-between text-xs text-[#F2F0EC] bg-[#1A1A1D] hover:bg-[#242428] border border-[#FF3D5A33] hover:border-[#FF3D5A66] px-3.5 py-2.5 rounded cursor-pointer transition select-none">
                <div class="flex items-center gap-2.5">
                  <input type="checkbox"
                         class="pdp-addon-checkbox rounded border-[#FF3D5A66] text-[#D4FF3D] focus:ring-[#D4FF3D] w-4 h-4 cursor-pointer accent-[#D4FF3D]"
                         data-price="{{ $addon->price }}"
                         data-name="{{ $addon->name }}"
                         data-id="{{ $addon->id }}"
                         onchange="if(typeof updateProductAddonPricing === 'function') updateProductAddonPricing()">
                  <span class="font-medium text-[#F2F0EC]">{{ $addon->name }}</span>
                </div>
                <span class="font-bold font-mono text-[#D4FF3D]">+₹{{ number_format($addon->price, 0) }}</span>
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
  <div class="mt-16 pt-12 border-t space-y-8" style="border-color:#FF3D5A22">
    @if($product->long_description)
      <div class="space-y-4">
        <div class="flex items-center gap-2.5">
          <span class="w-1 h-5 rounded-full bg-gradient-to-b from-[#D4FF3D] to-[#FF3D5A] inline-block shrink-0"></span>
          <h3 class="font-body text-base md:text-lg font-bold text-[#D4FF3D] uppercase tracking-wider">Product Highlights</h3>
        </div>
        <div class="font-body text-sm md:text-base leading-relaxed opacity-85 text-[#F2F0EC] product-rich-text">{!! $product->long_description !!}</div>
      </div>
    @endif

    {{-- Delivery & Returns Accordion --}}
    <div class="border-t pt-4 divide-y" style="border-color:#FF3D5A22; --tw-divide-opacity: 0.15; divide-color: #FF3D5A;">
      {{-- Delivery --}}
      <div x-data="{ open: false }" class="py-4">
        <button type="button" @click="open = !open" class="flex items-center justify-between w-full text-left font-body text-xs md:text-sm font-bold uppercase tracking-wider text-[#F2F0EC] hover:text-[#D4FF3D] transition">
          <span class="flex items-center gap-2">
            <svg class="w-4 h-4 text-[#D4FF3D]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
            Delivery Information
          </span>
          <svg class="w-4 h-4 text-[#FF3D5A] transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
        </button>
        <div x-show="open" x-transition class="mt-3 font-body text-xs md:text-sm text-[#F2F0EC]/75 leading-relaxed pl-6">
          {{ \App\Models\Setting::get('product_delivery_info', 'Ships within 2–3 business days. Free standard delivery on orders above ₹999. Express doorstep delivery available at checkout.') }}
        </div>
      </div>
      {{-- Returns & Exchange --}}
      <div x-data="{ open: false }" class="py-4">
        <button type="button" @click="open = !open" class="flex items-center justify-between w-full text-left font-body text-xs md:text-sm font-bold uppercase tracking-wider text-[#F2F0EC] hover:text-[#D4FF3D] transition">
          <span class="flex items-center gap-2">
            <svg class="w-4 h-4 text-[#FF3D5A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            Returns &amp; Exchange
          </span>
          <svg class="w-4 h-4 text-[#FF3D5A] transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
        </button>
        <div x-show="open" x-transition class="mt-3 font-body text-xs md:text-sm text-[#F2F0EC]/75 leading-relaxed pl-6">
          {{ \App\Models\Setting::get('product_returns_info', 'Hassle-free returns within 15 days of delivery. Items must be unworn, undamaged, and in original packaging with tags attached.') }}
        </div>
      </div>
    </div>
  </div>

  {{-- Customer Reviews --}}
  <div class="mt-20">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-10 pb-6 border-b" style="border-color:#FF3D5A22">
      <div>
        <p class="text-[#D4FF3D] text-xs font-bold uppercase tracking-widest mb-1">Customer Reviews</p>
        <h3 class="font-display uppercase text-2xl md:text-3xl text-[#F2F0EC]">Verified Buyer Reviews</h3>
        @php
          $revCount = $product->approvedReviews->count();
          $avgRating = $revCount ? round($product->approvedReviews->avg('rating'), 1) : 5.0;
        @endphp
        <div class="flex items-center gap-2 mt-2">
          <div class="flex text-[#FF3D5A]">
            @for($i = 1; $i <= 5; $i++)
              <span>{{ $i <= round($avgRating) ? '★' : '☆' }}</span>
            @endfor
          </div>
          <span class="font-bold text-sm text-[#F2F0EC]">{{ $avgRating }}</span>
          <span class="text-xs opacity-50 text-[#F2F0EC]">({{ $revCount }} {{ Str::plural('review', $revCount) }})</span>
        </div>
      </div>
      <button onclick="toggleVyraReviewForm()"
              class="py-3 px-6 text-xs font-bold uppercase tracking-widest border border-[#FF3D5A] text-[#FF3D5A] hover:bg-[#FF3D5A] hover:text-[#0E0E10] transition-all"
              style="clip-path:polygon(6% 0,100% 0,94% 100%,0 100%)">
        Write a Review
      </button>
    </div>

    {{-- Review Form --}}
    <div id="vyraReviewFormWrap" class="hidden mb-10 bg-[#1A1A1D] border p-6 md:p-8" style="border-color:#FF3D5A44">
      <h4 class="font-display uppercase text-xl mb-4 text-[#F2F0EC]">Submit Your Review</h4>
      <form id="vyraReviewForm" onsubmit="event.preventDefault(); submitVyraReview(this);" class="space-y-4">
        @csrf
        <div>
          <label class="block text-xs font-bold uppercase tracking-widest text-[#D4FF3D] mb-2">Rating</label>
          <div class="flex gap-2 text-2xl cursor-pointer" id="vyraStarSelector">
            @for($s = 1; $s <= 5; $s++)
              <span class="vyra-star text-[#FF3D5A]" data-val="{{ $s }}">★</span>
            @endfor
          </div>
          <input type="hidden" name="rating" id="vyraRatingInput" value="5" required>
        </div>
        <div class="grid sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-bold uppercase tracking-widest text-[#F2F0EC] opacity-60 mb-1">Your Name *</label>
            <input type="text" name="customer_name" required value="{{ Auth::user()?->name ?? '' }}" class="w-full bg-[#0E0E10] border px-4 py-2.5 text-xs text-[#F2F0EC] outline-none" style="border-color:#FF3D5A44">
          </div>
          <div>
            <label class="block text-xs font-bold uppercase tracking-widest text-[#F2F0EC] opacity-60 mb-1">Headline</label>
            <input type="text" name="title" placeholder="Review headline" class="w-full bg-[#0E0E10] border px-4 py-2.5 text-xs text-[#F2F0EC] outline-none" style="border-color:#FF3D5A44">
          </div>
        </div>
        <div>
          <label class="block text-xs font-bold uppercase tracking-widest text-[#F2F0EC] opacity-60 mb-1">Review *</label>
          <textarea name="comment" rows="3" required placeholder="Tell us about the fit, quality, and style..." class="w-full bg-[#0E0E10] border p-4 text-xs text-[#F2F0EC] outline-none" style="border-color:#FF3D5A44"></textarea>
        </div>
        <button type="submit" class="py-3 px-8 text-xs font-bold uppercase tracking-widest text-[#0E0E10] bg-[#FF3D5A] hover:bg-[#D4FF3D] transition-all"
                style="clip-path:polygon(4% 0,100% 0,96% 100%,0 100%)">
          Submit Review
        </button>
      </form>
      <div id="vyraReviewSuccess" class="hidden text-center py-6 text-[#D4FF3D] font-semibold text-sm">
        ✓ Thank you! Your review has been submitted for moderation.
      </div>
    </div>

    {{-- Reviews list --}}
    @if($product->approvedReviews->count())
      <div class="grid md:grid-cols-2 gap-4">
        @foreach($product->approvedReviews as $rev)
          <div class="bg-[#1A1A1D] border p-5" style="border-color:#FF3D5A22">
            <div class="flex items-center justify-between mb-2">
              <span class="text-[#FF3D5A] text-sm">
                @for($i=1; $i<=5; $i++) {{ $i <= $rev->rating ? '★' : '☆' }} @endfor
              </span>
              <span class="text-[10px] opacity-40 text-[#F2F0EC]">{{ $rev->created_at->format('d M Y') }}</span>
            </div>
            @if($rev->title)
              <h5 class="font-display uppercase font-semibold text-sm text-[#F2F0EC] mb-1">{{ $rev->title }}</h5>
            @endif
            <p class="font-body text-xs opacity-70 text-[#F2F0EC] leading-relaxed mb-3">{{ $rev->comment }}</p>
            <div class="flex items-center gap-2 text-xs">
              <span class="font-bold text-[#F2F0EC]">{{ $rev->customer_name }}</span>
              <span class="text-[10px] bg-[#D4FF3D22] text-[#D4FF3D] px-2 py-0.5 uppercase font-bold tracking-wider">Verified Buyer</span>
            </div>
          </div>
        @endforeach
      </div>
    @else
      <div class="bg-[#1A1A1D] border p-10 text-center" style="border-color:#FF3D5A22">
        <p class="text-sm opacity-50 text-[#F2F0EC] mb-2">No reviews yet for this product.</p>
        <button onclick="toggleVyraReviewForm()" class="text-xs font-bold underline text-[#FF3D5A] uppercase">
          Be the first to leave a review →
        </button>
      </div>
    @endif
  </div>

  @if ($related->count())
    <div class="mt-20">
      <h3 class="font-display uppercase text-2xl text-center mb-10 text-[#F2F0EC]">You Might Also Like</h3>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
        @foreach ($related as $rp)
          @include('storefront.vyra.partials.product-card', ['product' => $rp])
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
            class="px-4 py-2 text-xs font-bold border transition-all ${!s.inStock ? 'opacity-30 cursor-not-allowed' : ''}"
            style="border-color:#FF3D5A44;color:#F2F0EC" ${!s.inStock ? 'disabled' : ''}>
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
    b.style.background = active ? '#D4FF3D' : '';
    b.style.color = active ? '#0E0E10' : '#F2F0EC';
    b.style.borderColor = active ? '#D4FF3D' : '#FF3D5A44';
  });
  updateBtn();
}

function updateBtn() {
  const b = document.getElementById('addBtn');
  b.disabled = !selSize;
  b.textContent = selSize ? 'Add to Bag' : 'Select a Size';
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

async function checkVyraPincode() {
  const pin = document.getElementById('vyraPincode')?.value.trim();
  const resEl = document.getElementById('vyraPinResult');
  if (!pin || pin.length !== 6 || !/^\d{6}$/.test(pin)) {
    if (resEl) {
      resEl.className = 'mt-3 text-xs text-[#FF3D5A] font-semibold';
      resEl.textContent = 'Please enter a valid 6-digit Indian PIN code.';
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
      resEl.className = 'mt-3 text-xs p-3 bg-[#1A1A1D] border space-y-1 text-[#F2F0EC]';
      resEl.style.borderColor = '#FF3D5A44';
      resEl.innerHTML = `
        <p class="font-bold text-[#D4FF3D]">✓ DELIVERABLE TO ${info.city ? (info.city + ', ' + info.state).toUpperCase() : 'YOUR LOCATION'} (${info.pincode})</p>
        <p class="opacity-70">Estimated Delivery: <strong>${info.estimated_days} DAYS</strong> (${info.estimated_date})</p>
        <p class="font-semibold ${info.cod_available ? 'text-[#D4FF3D]' : 'text-amber-400'}">
          ${info.cod_available ? '✓ CASH ON DELIVERY (COD) AVAILABLE' : 'ℹ PREPAID ORDERS ONLY'}
        </p>
      `;
    } else {
      resEl.className = 'mt-3 text-xs text-amber-400';
      resEl.textContent = data.message || 'Service available via standard courier.';
    }
  } catch {
    resEl.className = 'mt-3 text-xs text-[#FF3D5A]';
    resEl.textContent = 'Error checking pincode.';
  } finally {
    resEl.classList.remove('hidden');
  }
}

function toggleVyraReviewForm() {
  const wrap = document.getElementById('vyraReviewFormWrap');
  if (wrap) {
    wrap.classList.toggle('hidden');
    if (!wrap.classList.contains('hidden')) wrap.scrollIntoView({ behavior: 'smooth', block: 'center' });
  }
}

document.querySelectorAll('#vyraStarSelector .vyra-star').forEach(star => {
  star.addEventListener('click', () => {
    const val = Number(star.dataset.val);
    document.getElementById('vyraRatingInput').value = val;
    document.querySelectorAll('#vyraStarSelector .vyra-star').forEach(s => {
      s.textContent = Number(s.dataset.val) <= val ? '★' : '☆';
    });
  });
});

async function submitVyraReview(form) {
  const btn = form.querySelector('button[type="submit"]');
  btn.disabled = true;
  btn.textContent = 'SUBMITTING...';
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
      document.getElementById('vyraReviewSuccess')?.classList.remove('hidden');
    } else {
      alert(data.message || 'Error submitting review.');
    }
  } catch {
    alert('An unexpected error occurred.');
  } finally {
    btn.disabled = false;
    btn.textContent = 'SUBMIT REVIEW';
  }
}

document.addEventListener('DOMContentLoaded', () => selectColor(0, pd.colors[0]?.name || ''));
</script>
@endpush
</x-storefront-layout>
