{{--
    ======================================================================
    THEME: LUMIÈRE — SINGLE PRODUCT DETAIL PAGE
    ======================================================================
    File: resources/views/storefront/lumiere/product.blade.php
    Controller: App\Http\Controllers\StorefrontController@product
    ViewModel Injections:
      - $product:         Eloquent Product (with colors.images, colors.variants)
      - $related:         Curated silk pairings
    Key Interactions:
      - Silk touch editorial imagery with thumbnail switching
      - Color swatch circles (switches shade & gallery images in real time)
      - Size selection pills with live stock countdown
      - Add to Bag button styled in Deep Pine (#405549)
      - Delivery pincode serviceability checker
      - Verified customer reviews & moderation feedback
--}}
<x-storefront-layout :theme="$theme" :product="$product">

<style>
  .animate-fade { animation: pageFadeIn .5s ease both; }
  .pd-thumbs { scrollbar-width:none; }
  .pd-thumbs::-webkit-scrollbar { display:none; }
</style>

<div class="max-w-7xl mx-auto px-6 md:px-12 py-10">

  {{-- Breadcrumb --}}
  <nav class="text-xs flex items-center gap-2 mb-8 text-gray-400">
    <a href="{{ route('home') }}" class="hover:text-pink-600 transition-colors">Home</a>
    <span>/</span>
    <a href="{{ route('category.show', $product->category->slug) }}" class="hover:text-pink-600 transition-colors">{{ $product->category->name }}</a>
    <span>/</span>
    <span class="text-pink-600">{{ $product->name }}</span>
  </nav>

  <div class="grid lg:grid-cols-2 gap-10 lg:gap-16">

    {{-- Gallery: Full Auto-Slider + Clean Uncropped Thumbnails Strip --}}
    @php
      $firstColor = $product->colors->first();
      $galleryImages = collect();
      if ($firstColor) {
        $firstColorCover = $firstColor->cover_image;
        if ($firstColorCover && $firstColorCover !== asset('images/placeholder.jpg')) {
          $galleryImages->push($firstColorCover);
        }
        foreach ($firstColor->images as $cImg) {
          if (!$galleryImages->contains($cImg->url)) {
            $galleryImages->push($cImg->url);
          }
        }
      }
      if ($product->cover_image && !$galleryImages->contains($product->cover_image) && $product->cover_image !== asset('images/placeholder.jpg')) {
        $galleryImages->prepend($product->cover_image);
      }
      if ($galleryImages->isEmpty()) {
        $galleryImages->push(asset('images/placeholder.jpg'));
      }
    @endphp
    <div id="lumiereGalleryWrap" class="pdp-gallery-container flex flex-col">
      {{-- Main Hero Image Slider Container --}}
      <div class="aspect-[3/4] rounded-[32px] overflow-hidden mb-3 relative group select-none shadow-sm bg-pink-50/50" style="aspect-ratio: 3/4;">
        @if ($product->badge)
          <span class="absolute top-4 left-4 z-20 bg-white/95 backdrop-blur-md text-[9px] font-black text-pink-600 px-3 py-1.5 rounded-full uppercase tracking-widest shadow-xs pointer-events-none">
            {{ $product->badge }}
          </span>
        @endif

        {{-- Slide Counter Badge --}}
        <div id="lumiereSlideBadge" class="absolute bottom-4 right-4 z-20 bg-gray-900/75 backdrop-blur-md text-white text-[11px] font-mono font-semibold px-3 py-1 rounded-full border border-white/20 shadow-xs pointer-events-none" style="{{ $galleryImages->count() <= 1 ? 'display:none;' : '' }}">
          1 / {{ $galleryImages->count() }}
        </div>

        {{-- Previous Slide Arrow --}}
        <button type="button" 
                id="lumierePrevBtn"
                onclick="lumierePrevSlide(true)"
                class="absolute left-3 top-1/2 -translate-y-1/2 z-20 w-10 h-10 rounded-full bg-white/90 hover:bg-pink-600 text-gray-800 hover:text-white border border-pink-200/40 shadow-md flex items-center justify-center transition-all opacity-80 sm:opacity-0 sm:group-hover:opacity-100 hover:scale-110 cursor-pointer focus:outline-none"
                style="{{ $galleryImages->count() <= 1 ? 'display:none;' : '' }}"
                aria-label="Previous image">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        </button>

        {{-- Next Slide Arrow --}}
        <button type="button" 
                id="lumiereNextBtn"
                onclick="lumiereNextSlide(true)"
                class="absolute right-3 top-1/2 -translate-y-1/2 z-20 w-10 h-10 rounded-full bg-white/90 hover:bg-pink-600 text-gray-800 hover:text-white border border-pink-200/40 shadow-md flex items-center justify-center transition-all opacity-80 sm:opacity-0 sm:group-hover:opacity-100 hover:scale-110 cursor-pointer focus:outline-none"
                style="{{ $galleryImages->count() <= 1 ? 'display:none;' : '' }}"
                aria-label="Next image">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </button>

        {{-- Main Display Image --}}
        <img id="pdMainImg" 
             src="{{ $galleryImages->first() }}" 
             alt="{{ $product->name }}" 
             class="w-full h-full object-cover transition-opacity duration-300">
      </div>

      {{-- Thumbnail Strip (All Images for Active Color / Product in horizontal clean strip, uncropped) --}}
      <div id="pdThumbs" class="pd-thumbs flex gap-2.5 overflow-x-auto pb-2 pt-1 scrollbar-hide" style="{{ $galleryImages->count() <= 1 ? 'display:none;' : '' }}">
        @foreach ($galleryImages as $i => $imgUrl)
          <button type="button"
                  onclick="lumiereGoToSlide({{ $i }}, true)"
                  class="lumiere-thumb flex-shrink-0 w-16 sm:w-20 aspect-[3/4] rounded-2xl overflow-hidden border-2 transition-all duration-200 cursor-pointer {{ $i === 0 ? 'border-pink-500 ring-2 ring-pink-400/40 shadow-sm opacity-100 scale-105' : 'border-pink-200/60 opacity-60 hover:opacity-100 hover:border-pink-400' }}"
                  style="aspect-ratio: 3/4;"
                  data-idx="{{ $i }}"
                  title="View image {{ $i + 1 }}">
            <img src="{{ $imgUrl }}" alt="{{ $product->name }} view {{ $i + 1 }}" class="w-full h-full object-cover pointer-events-none">
          </button>
        @endforeach
      </div>
    </div>

    {{-- Info --}}
    <div class="flex flex-col gap-6">
      <div>
        <div class="flex items-center gap-2 text-[10px] font-black uppercase tracking-widest mb-2">
          @if($product->brand)
            <span class="text-gray-900">{{ $product->brand->name }}</span>
            <span class="text-gray-300">·</span>
          @endif
          <span class="text-pink-500">{{ $product->category->name }}</span>
        </div>
        <h1 class="font-display italic text-3xl md:text-4xl text-gray-900 leading-tight">{{ $product->name }}</h1>
      </div>

      <p class="text-3xl font-bold text-pink-600" id="pdPrice">
        <span id="pdPriceCurrent">₹{{ number_format($product->starting_price, 0) }}</span>
        <span id="pdPriceCompare" class="text-sm font-normal text-gray-300 line-through ml-2 hidden"></span>
      </p>

      @if ($product->description)
        <div class="text-sm leading-relaxed text-gray-600 product-rich-text">{!! $product->description !!}</div>
      @endif

      {{-- Colour --}}
      <div>
        <p class="text-[10px] uppercase tracking-widest font-bold text-gray-400 mb-3">
          Shade: <span id="pdColorName" class="text-pink-500">{{ $product->colors->first()?->name }}</span>
        </p>
        <div id="pdColors" class="flex gap-3 flex-wrap"></div>
      </div>

      {{-- Size --}}
      <div>
        <div class="flex items-center justify-between mb-3">
          <div class="flex items-center gap-3">
            <p class="text-[10px] uppercase tracking-widest font-bold text-gray-400">
              Size: <span id="pdSizeName" class="text-pink-500">—</span>
            </p>
            <p id="pdStockWarning" class="text-[10px] font-bold text-red-400 hidden"></p>
          </div>
          <button type="button" 
                  class="pdp-size-guide-trigger text-xs font-bold text-pink-600 hover:text-pink-700 underline transition-colors cursor-pointer flex items-center gap-1">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16m-7 6h7"/></svg>
            Size Guide
          </button>
        </div>
        <div id="pdSizes" class="flex gap-2 flex-wrap"></div>
      </div>

      {{-- Actions --}}
      <button id="pdAddBtn" onclick="pdAddToBag()" disabled
              class="py-4 rounded-2xl text-xs font-bold uppercase tracking-widest bg-[#405549] text-white hover:bg-pink-600 transition-colors disabled:opacity-40 disabled:cursor-not-allowed">
        Select a Size
      </button>

      <div class="flex gap-3">
        <button id="pdWishBtn" onclick="pdToggleWishlist()"
                class="flex-1 py-3 rounded-2xl text-xs font-bold uppercase tracking-widest border border-gray-200 text-gray-700 hover:border-pink-400 transition-colors flex items-center justify-center gap-2">
          <i id="pdWishIcon" class="fa-regular fa-heart text-sm transition-transform duration-200"></i>
          <span id="pdWishLabel">Wishlist</span>
        </button>
        <button onclick="pdAddToCompare()"
                class="flex-1 py-3 rounded-2xl text-xs font-bold uppercase tracking-widest border border-gray-200 text-gray-700 hover:border-pink-400 transition-colors">
          Compare
        </button>
      </div>

      {{-- Pincode Checker --}}
      <div class="border-t border-pink-50 pt-5">
        <label for="lumierePincode" class="block text-[10px] uppercase tracking-widest font-bold text-gray-500 mb-2 flex items-center gap-1.5">
          <svg class="w-3.5 h-3.5 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
          Check Delivery Availability
        </label>
        <div class="flex gap-2">
          <input type="text" id="lumierePincode" maxlength="6" pattern="[0-9]{6}" placeholder="Enter 6-digit PIN code"
                 onkeydown="if(event.key==='Enter'){event.preventDefault();checkLumierePincode();}"
                 class="flex-1 rounded-2xl border border-gray-200 px-4 py-2.5 text-xs text-gray-800 outline-none focus:border-pink-500">
          <button type="button" onclick="checkLumierePincode()"
                  class="px-5 py-2.5 rounded-2xl text-xs font-bold uppercase tracking-wider text-white bg-[#405549] hover:bg-pink-600 transition-colors">
            Check
          </button>
        </div>
        <div id="lumierePinResult" class="hidden mt-3 text-xs font-medium"></div>
      </div>

      {{-- Available Add-ons --}}
      @if($product->addons->isNotEmpty())
        <div class="border-t border-pink-50 pt-3">
          <p class="text-[10px] uppercase tracking-widest font-bold text-gray-500 mb-2 flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            Available Add-ons
          </p>
          <div class="space-y-1.5">
            @foreach($product->addons as $addon)
              <label class="flex items-center justify-between text-xs text-gray-700 bg-pink-50/40 hover:bg-pink-50/70 border border-pink-100 hover:border-pink-200 px-3.5 py-2.5 rounded-xl cursor-pointer transition select-none">
                <div class="flex items-center gap-2.5">
                  <input type="checkbox"
                         class="pdp-addon-checkbox rounded border-pink-300 text-pink-600 focus:ring-pink-500 w-4 h-4 cursor-pointer accent-[#db2777]"
                         data-price="{{ $addon->price }}"
                         data-name="{{ $addon->name }}"
                         data-id="{{ $addon->id }}"
                         onchange="if(typeof updateProductAddonPricing === 'function') updateProductAddonPricing()">
                  <span class="font-medium text-gray-800">{{ $addon->name }}</span>
                </div>
                <span class="font-bold text-gray-900 font-mono">+₹{{ number_format($addon->price, 0) }}</span>
              </label>
            @endforeach
          </div>
        </div>
      @endif

      <ul class="grid grid-cols-3 gap-3 text-center text-[10px] uppercase tracking-widest font-bold text-gray-400 border-t border-pink-50 pt-6">
        <li>Free shipping<br>over ₹1,999</li>
        <li>7-day easy<br>returns</li>
        <li>Pure silk,<br>hand-finished</li>
      </ul>
    </div>
  </div>

  {{-- ═══════════════════════════════════════════════
       PRODUCT HIGHLIGHTS & ACCORDION (Dedicated Division)
  ═══════════════════════════════════════════════ --}}
  <div class="mt-16 pt-12 border-t border-pink-100/60 space-y-8">
    @if ($product->long_description)
      <div class="space-y-4">
        <div class="flex items-center gap-2.5">
          <span class="w-1 h-5 rounded-full bg-gradient-to-b from-pink-500 to-rose-400 inline-block shrink-0"></span>
          <h3 class="font-display italic text-lg md:text-xl font-bold text-gray-900 tracking-wider">Product Highlights</h3>
        </div>
        <div class="text-sm md:text-base leading-relaxed text-gray-600 product-rich-text">{!! $product->long_description !!}</div>
      </div>
    @endif

    {{-- Delivery & Returns Accordion --}}
    <div class="border-t border-pink-100/60 pt-4 divide-y divide-pink-50">
      {{-- Delivery --}}
      <div x-data="{ open: false }" class="py-4">
        <button type="button" @click="open = !open" class="flex items-center justify-between w-full text-left font-display italic text-sm md:text-base font-bold text-gray-900 hover:text-pink-600 transition">
          <span class="flex items-center gap-2">
            <svg class="w-4 h-4 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
            Delivery Information
          </span>
          <svg class="w-4 h-4 text-gray-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
        </button>
        <div x-show="open" x-transition class="mt-3 text-xs md:text-sm text-gray-600 leading-relaxed pl-6">
          {{ \App\Models\Setting::get('product_delivery_info', 'Ships within 2–3 business days. Free standard delivery on orders above ₹999. Express doorstep delivery available at checkout.') }}
        </div>
      </div>
      {{-- Returns & Exchange --}}
      <div x-data="{ open: false }" class="py-4">
        <button type="button" @click="open = !open" class="flex items-center justify-between w-full text-left font-display italic text-sm md:text-base font-bold text-gray-900 hover:text-pink-600 transition">
          <span class="flex items-center gap-2">
            <svg class="w-4 h-4 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            Returns &amp; Exchange
          </span>
          <svg class="w-4 h-4 text-gray-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
        </button>
        <div x-show="open" x-transition class="mt-3 text-xs md:text-sm text-gray-600 leading-relaxed pl-6">
          {{ \App\Models\Setting::get('product_returns_info', 'Hassle-free returns within 15 days of delivery. Items must be unused, unwashed, and in original packaging with tags intact.') }}
        </div>
      </div>
    </div>
  </div>

  {{-- Customer Reviews --}}
  <div class="mt-24 border-t border-pink-50 pt-16">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-10 pb-6 border-b border-pink-50">
      <div>
        <p class="text-pink-500 text-[10px] font-black uppercase tracking-widest mb-1">Customer Reviews</p>
        <h3 class="font-display italic text-2xl md:text-3xl text-gray-900">Client Impressions</h3>
        @php
          $revCount = $product->approvedReviews->count();
          $avgRating = $revCount ? round($product->approvedReviews->avg('rating'), 1) : 5.0;
        @endphp
        <div class="flex items-center gap-2 mt-2">
          <div class="flex text-pink-500">
            @for($i = 1; $i <= 5; $i++)
              <span>{{ $i <= round($avgRating) ? '★' : '☆' }}</span>
            @endfor
          </div>
          <span class="font-bold text-sm text-gray-900">{{ $avgRating }}</span>
          <span class="text-xs text-gray-400">({{ $revCount }} {{ Str::plural('review', $revCount) }})</span>
        </div>
      </div>
      <button onclick="toggleLumiereReviewForm()"
              class="py-3 px-6 rounded-2xl text-xs font-bold uppercase tracking-widest border border-gray-200 text-gray-800 hover:border-pink-400 hover:text-pink-600 transition-colors">
        Write a Review
      </button>
    </div>

    {{-- Review Form --}}
    <div id="lumiereReviewFormWrap" class="hidden mb-12 bg-pink-50/40 rounded-3xl border border-pink-100 p-6 md:p-8">
      <h4 class="font-display italic text-xl text-gray-900 mb-4">Share Your Impression</h4>
      <form id="lumiereReviewForm" onsubmit="event.preventDefault(); submitLumiereReview(this);" class="space-y-4">
        @csrf
        <div>
          <label class="block text-[10px] uppercase tracking-widest font-bold text-gray-500 mb-2">Rating</label>
          <div class="flex gap-2 text-2xl cursor-pointer" id="lumiereStarSelector">
            @for($s = 1; $s <= 5; $s++)
              <span class="lumiere-star text-pink-500" data-val="{{ $s }}">★</span>
            @endfor
          </div>
          <input type="hidden" name="rating" id="lumiereRatingInput" value="5" required>
        </div>
        <div class="grid sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-[10px] uppercase tracking-widest font-bold text-gray-500 mb-1">Your Name *</label>
            <input type="text" name="customer_name" required value="{{ Auth::user()?->name ?? '' }}" class="w-full bg-white border border-gray-200 rounded-2xl px-4 py-2.5 text-xs text-gray-800 outline-none focus:border-pink-500">
          </div>
          <div>
            <label class="block text-[10px] uppercase tracking-widest font-bold text-gray-500 mb-1">Headline</label>
            <input type="text" name="title" placeholder="Summary of your impression" class="w-full bg-white border border-gray-200 rounded-2xl px-4 py-2.5 text-xs text-gray-800 outline-none focus:border-pink-500">
          </div>
        </div>
        <div>
          <label class="block text-[10px] uppercase tracking-widest font-bold text-gray-500 mb-1">Review *</label>
          <textarea name="comment" rows="3" required placeholder="Describe the silk touch, fit, and elegance..." class="w-full bg-white border border-gray-200 rounded-2xl p-4 text-xs text-gray-800 outline-none focus:border-pink-500 leading-relaxed"></textarea>
        </div>
        <button type="submit" class="py-3.5 px-8 rounded-2xl text-xs font-bold uppercase tracking-widest text-white bg-[#405549] hover:bg-pink-600 transition-colors">
          Submit Review
        </button>
      </form>
      <div id="lumiereReviewSuccess" class="hidden text-center py-6 text-pink-600 font-semibold text-sm">
        ✓ Thank you! Your review has been submitted for moderation.
      </div>
    </div>

    {{-- Reviews list --}}
    @if($product->approvedReviews->count())
      <div class="grid md:grid-cols-2 gap-5">
        @foreach($product->approvedReviews as $rev)
          <div class="bg-white rounded-3xl border border-pink-100 p-6 shadow-sm shadow-pink-50">
            <div class="flex items-center justify-between mb-2">
              <span class="text-pink-500 text-sm">
                @for($i=1; $i<=5; $i++) {{ $i <= $rev->rating ? '★' : '☆' }} @endfor
              </span>
              <span class="text-[10px] text-gray-400">{{ $rev->created_at->format('d M Y') }}</span>
            </div>
            @if($rev->title)
              <h5 class="font-display italic font-semibold text-sm text-gray-900 mb-1">{{ $rev->title }}</h5>
            @endif
            <p class="text-xs text-gray-600 leading-relaxed mb-3">{{ $rev->comment }}</p>
            <div class="flex items-center gap-2 text-xs">
              <span class="font-bold text-gray-900">{{ $rev->customer_name }}</span>
              <span class="text-[10px] bg-pink-50 text-pink-600 px-2.5 py-0.5 rounded-full font-bold uppercase tracking-wider">Verified</span>
            </div>
          </div>
        @endforeach
      </div>
    @else
      <div class="bg-white rounded-3xl border border-pink-100 p-12 text-center shadow-sm shadow-pink-50">
        <p class="text-sm text-gray-400 mb-2">No reviews yet for this piece.</p>
        <button onclick="toggleLumiereReviewForm()" class="text-xs font-bold text-pink-600 underline">
          Be the first to leave a review →
        </button>
      </div>
    @endif
  </div>

  {{-- Related --}}
  @if ($related->count())
    <div class="mt-24">
      <p class="text-pink-500 text-[10px] font-black uppercase tracking-widest text-center mb-2">More to love</p>
      <h3 class="font-display italic text-2xl md:text-3xl text-center text-gray-900 mb-10">You may also like</h3>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
        @foreach ($related as $rp)
          @include('storefront.lumiere.partials.lustre-card', ['product' => $rp])
        @endforeach
      </div>
    </div>
  @endif
</div>

{{-- Quick-view modal for the related cards --}}
@include('storefront.lumiere.partials.quick-view')

@push('scripts')
<script>
const pd = @json($product->toCardArray());
let pdColorIdx = 0, pdSize = null;
let currentImageIdx = 0;
let autoSlideTimer = null;
const AUTO_SLIDE_DELAY = 3500; // 3.5 seconds auto-slide
let galleryInViewport = true;

function getActiveImages() {
    const c = pd.colors && pd.colors[pdColorIdx];
    let list = [];
    if (c && c.images && c.images.length) {
        list = [...c.images];
    }
    if (pd.coverImage && !list.includes(pd.coverImage) && !pd.coverImage.includes('placeholder.jpg')) {
        list.unshift(pd.coverImage);
    }
    if (!list.length) {
        list = [pd.coverImage || '{{ asset('images/placeholder.jpg') }}'];
    }
    return list;
}

function pdRenderThumbs() {
    const imgs = getActiveImages();
    const container = document.getElementById('pdThumbs');
    if (!container) return;

    if (imgs.length <= 1) {
        container.style.display = 'none';
        return;
    }
    container.style.display = 'flex';

    container.innerHTML = imgs.map((src, i) => {
        const isActive = i === currentImageIdx;
        return `
            <button type="button" onclick="lumiereGoToSlide(${i}, true)"
                    class="lumiere-thumb flex-shrink-0 w-16 sm:w-20 aspect-[3/4] rounded-2xl overflow-hidden border-2 transition-all duration-200 cursor-pointer ${
                        isActive
                            ? 'border-pink-500 ring-2 ring-pink-400/40 shadow-sm opacity-100 scale-105'
                            : 'border-pink-200/60 opacity-60 hover:opacity-100 hover:border-pink-400'
                    }"
                    style="aspect-ratio: 3/4;"
                    data-idx="${i}"
                    title="View image ${i + 1}">
                <img src="${src}" class="w-full h-full object-cover pointer-events-none" alt="">
            </button>
        `;
    }).join('');
}

function lumiereGoToSlide(idx, userInitiated = false) {
    const imgs = getActiveImages();
    if (!imgs.length) return;

    if (idx < 0) idx = imgs.length - 1;
    if (idx >= imgs.length) idx = 0;
    currentImageIdx = idx;

    const targetSrc = imgs[currentImageIdx];
    const mainImg = document.getElementById('pdMainImg');
    if (mainImg) {
        if (mainImg.src !== targetSrc) {
            mainImg.style.opacity = '0';
            setTimeout(() => {
                mainImg.src = targetSrc;
                mainImg.style.opacity = '1';
            }, 140);
        } else {
            mainImg.style.opacity = '1';
        }
    }

    // Update thumbnails active state & scroll active thumb into view horizontally
    const thumbWrap = document.getElementById('pdThumbs');
    if (thumbWrap) {
        const thumbs = thumbWrap.querySelectorAll('.lumiere-thumb');
        thumbs.forEach((b, i) => {
            const isActive = i === currentImageIdx;
            b.classList.toggle('border-pink-500', isActive);
            b.classList.toggle('ring-2', isActive);
            b.classList.toggle('ring-pink-400/40', isActive);
            b.classList.toggle('shadow-sm', isActive);
            b.classList.toggle('opacity-100', isActive);
            b.classList.toggle('scale-105', isActive);

            b.classList.toggle('border-pink-200/60', !isActive);
            b.classList.toggle('opacity-60', !isActive);

            if (isActive) {
                const targetLeft = b.offsetLeft - (thumbWrap.clientWidth / 2) + (b.clientWidth / 2);
                thumbWrap.scrollTo({ left: Math.max(0, targetLeft), behavior: 'smooth' });
            }
        });
    }

    // Update slide counter badge
    const badge = document.getElementById('lumiereSlideBadge');
    if (badge) {
        badge.textContent = `${currentImageIdx + 1} / ${imgs.length}`;
        badge.style.display = imgs.length > 1 ? 'block' : 'none';
    }

    // Update arrow visibility
    const prevBtn = document.getElementById('lumierePrevBtn');
    const nextBtn = document.getElementById('lumiereNextBtn');
    if (prevBtn) prevBtn.style.display = imgs.length > 1 ? 'flex' : 'none';
    if (nextBtn) nextBtn.style.display = imgs.length > 1 ? 'flex' : 'none';

    if (userInitiated) {
        restartAutoSlide();
    }
}

function lumiereNextSlide(userInitiated = false) {
    lumiereGoToSlide(currentImageIdx + 1, userInitiated);
}

function lumierePrevSlide(userInitiated = false) {
    lumiereGoToSlide(currentImageIdx - 1, userInitiated);
}

function startAutoSlide() {
    stopAutoSlide();
    const imgs = getActiveImages();
    if (imgs.length <= 1) return;
    if (!galleryInViewport) return;

    autoSlideTimer = setInterval(() => {
        lumiereNextSlide(false);
    }, AUTO_SLIDE_DELAY);
}

function stopAutoSlide() {
    if (autoSlideTimer) {
        clearInterval(autoSlideTimer);
        autoSlideTimer = null;
    }
}

function restartAutoSlide() {
    stopAutoSlide();
    if (galleryInViewport) {
        startAutoSlide();
    }
}

function pdSelectColor(idx) {
    pdColorIdx = idx; 
    pdSize = null;
    currentImageIdx = 0;
    const c = pd.colors[idx];
    document.getElementById('pdColorName').textContent = c.name;
    document.querySelectorAll('.pd-swatch').forEach((b, i) => {
        b.style.borderColor = i === idx ? '#DB2777' : 'transparent';
        b.style.transform = i === idx ? 'scale(1.15)' : 'scale(1)';
    });
    pdRenderThumbs();
    lumiereGoToSlide(0, false);
    startAutoSlide();

    document.getElementById('pdSizes').innerHTML = c.sizes.map(s => `
        <button type="button" onclick="pdSelectSize('${s.size}')" data-size="${s.size}" ${s.inStock ? '' : 'disabled'}
                class="pd-size px-5 py-2.5 rounded-full text-xs font-bold border border-pink-100 text-gray-700 transition-colors ${s.inStock ? 'hover:border-pink-400' : 'opacity-30 cursor-not-allowed'}">
            ${s.size}${s.inStock ? '' : ' (Out)'}
        </button>`).join('');

    pdShowPrice(pd.startingPrice, pd.startingCompareAt);
    document.getElementById('pdSizeName').textContent = '—';
    document.getElementById('pdStockWarning').classList.add('hidden');
    pdUpdateBtn();
}

let currentBasePrice = Number(pd.startingPrice || 0);

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

function updateProductAddonPricing(compareAt) {
    const addonTotal = getSelectedAddonsTotal();
    const finalPrice = currentBasePrice + addonTotal;
    document.getElementById('pdPriceCurrent').textContent = '₹' + Number(finalPrice).toLocaleString('en-IN');
    const cp = document.getElementById('pdPriceCompare');
    if (compareAt) {
        cp.textContent = '₹' + Number(compareAt).toLocaleString('en-IN');
        cp.classList.remove('hidden');
    }
}

function pdSelectSize(size) {
    pdSize = size;
    const v = pd.colors[pdColorIdx].sizes.find(s => s.size === size);
    currentBasePrice = Number(v.price);
    document.getElementById('pdSizeName').textContent = size;
    document.querySelectorAll('.pd-size').forEach(b => {
        const on = b.dataset.size === size;
        b.classList.toggle('bg-pink-600', on);
        b.classList.toggle('text-white', on);
        b.classList.toggle('border-pink-600', on);
    });
    pdShowPrice(v.price, v.compareAtPrice);
    const w = document.getElementById('pdStockWarning');
    if (v.stock > 0 && v.stock <= 3) { w.textContent = 'Only ' + v.stock + ' left'; w.classList.remove('hidden'); }
    else { w.classList.add('hidden'); }
    pdUpdateBtn();
}

function pdShowPrice(price, compareAt) {
    currentBasePrice = Number(price);
    const addonTotal = getSelectedAddonsTotal();
    const finalPrice = currentBasePrice + addonTotal;
    document.getElementById('pdPriceCurrent').textContent = '₹' + Number(finalPrice).toLocaleString('en-IN');
    const cp = document.getElementById('pdPriceCompare');
    if (compareAt) { cp.textContent = '₹' + Number(compareAt).toLocaleString('en-IN'); cp.classList.remove('hidden'); }
    else { cp.classList.add('hidden'); }
}

function pdUpdateBtn() {
    const b = document.getElementById('pdAddBtn');
    b.disabled = !pdSize;
    b.textContent = pdSize ? 'Add to Bag' : 'Select a Size';
}

function pdAddToBag() {
    if (!pdSize) return;
    const c = pd.colors[pdColorIdx];
    const v = c.sizes.find(s => s.size === pdSize);
    const addonTotal = getSelectedAddonsTotal();
    const finalPrice = Number(v.price) + addonTotal;
    const addonNames = getSelectedAddonNames();
    const extraDesc  = addonNames.length ? ' (Add-ons: ' + addonNames.join(', ') + ')' : '';
    addToCart(pd.id, pd.name + extraDesc, c.name, pdSize, finalPrice, (c.images && c.images[0]) || pd.coverImage);
}

// ── Wishlist (localStorage, same key the wishlist page reads) ──
function pdWishlist() { return JSON.parse(localStorage.getItem('lc_wishlist') || '[]'); }
function pdRenderWish() {
    const on = pdWishlist().includes(pd.id);
    const icon = document.getElementById('pdWishIcon');
    if (icon) {
        if (icon.tagName.toLowerCase() === 'i') {
            icon.className = on ? 'fa-solid fa-heart text-sm text-pink-600 scale-110 transition-transform duration-200' : 'fa-regular fa-heart text-sm transition-transform duration-200';
        } else {
            icon.setAttribute('fill', on ? '#DB2777' : 'none');
            icon.style.color = on ? '#DB2777' : '';
        }
    }
    const label = document.getElementById('pdWishLabel');
    if (label) {
        label.textContent = on ? 'Saved' : 'Wishlist';
    }
}
function pdToggleWishlist() {
    let wl = pdWishlist();
    wl = wl.includes(pd.id) ? wl.filter(x => x !== pd.id) : [...wl, pd.id];
    localStorage.setItem('lc_wishlist', JSON.stringify(wl));
    pdRenderWish();
}

async function checkLumierePincode() {
    const pin = document.getElementById('lumierePincode')?.value.trim();
    const resEl = document.getElementById('lumierePinResult');
    if (!pin || pin.length !== 6 || !/^\d{6}$/.test(pin)) {
        if (resEl) {
            resEl.className = 'mt-3 text-xs text-rose-500 font-semibold';
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
            resEl.className = 'mt-3 text-xs p-3.5 rounded-2xl bg-white border border-pink-100 shadow-sm shadow-pink-50 space-y-1 text-gray-800';
            resEl.innerHTML = `
                <p class="font-bold text-emerald-600">✓ Deliverable to ${info.city ? info.city + ', ' + info.state : 'your address'} (${info.pincode})</p>
                <p class="text-gray-500">Estimated Delivery: <strong>${info.estimated_days} days</strong> (${info.estimated_date})</p>
                <p class="font-semibold ${info.cod_available ? 'text-emerald-600' : 'text-pink-600'}">
                    ${info.cod_available ? '✓ Cash on Delivery (COD) Available' : 'ℹ Prepaid payment required'}
                </p>
            `;
        } else {
            resEl.className = 'mt-3 text-xs text-amber-600';
            resEl.textContent = data.message || 'Service available via standard courier.';
        }
    } catch {
        resEl.className = 'mt-3 text-xs text-rose-500';
        resEl.textContent = 'Error checking delivery pincode.';
    } finally {
        resEl.classList.remove('hidden');
    }
}

function toggleLumiereReviewForm() {
    const wrap = document.getElementById('lumiereReviewFormWrap');
    if (wrap) {
        wrap.classList.toggle('hidden');
        if (!wrap.classList.contains('hidden')) wrap.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
}

document.querySelectorAll('#lumiereStarSelector .lumiere-star').forEach(star => {
    star.addEventListener('click', () => {
        const val = Number(star.dataset.val);
        document.getElementById('lumiereRatingInput').value = val;
        document.querySelectorAll('#lumiereStarSelector .lumiere-star').forEach(s => {
            s.textContent = Number(s.dataset.val) <= val ? '★' : '☆';
        });
    });
});

async function submitLumiereReview(form) {
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
            document.getElementById('lumiereReviewSuccess')?.classList.remove('hidden');
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

document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('pdColors').innerHTML = pd.colors.map((c, i) => `
        <button type="button" onclick="pdSelectColor(${i})" title="${c.name}"
                class="pd-swatch w-9 h-9 rounded-full border-2 transition-all"
                style="background:${c.hex || '#ccc'}"></button>`).join('');
    if (pd.colors.length) pdSelectColor(0);
    pdRenderWish();

    // Pause on hover
    const galleryWrap = document.getElementById('lumiereGalleryWrap');
    if (galleryWrap) {
        galleryWrap.addEventListener('mouseenter', stopAutoSlide);
        galleryWrap.addEventListener('mouseleave', () => {
            if (galleryInViewport) startAutoSlide();
        });

        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    galleryInViewport = entry.isIntersecting;
                    if (!galleryInViewport) {
                        stopAutoSlide();
                    } else {
                        startAutoSlide();
                    }
                });
            }, { threshold: 0.2 });
            observer.observe(galleryWrap);
        }
    }

    // Pause on tab/window hide
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            stopAutoSlide();
        } else if (galleryInViewport) {
            startAutoSlide();
        }
    });
});
</script>
@endpush

@include('storefront.common.size-guide-modal', ['product' => $product, 'theme' => $theme])

</x-storefront-layout>
