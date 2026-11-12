{{--
    ======================================================================
    THEME: SAAYA — SINGLE PRODUCT DETAIL PAGE
    ======================================================================
    File: resources/views/storefront/saaya/product.blade.php
    Controller: App\Http\Controllers\StorefrontController@product
    ViewModel Injections:
      - $product:         Eloquent Product (with colors.images, colors.variants)
      - $relatedProducts: Heirloom trousseau pairings
      - $categories:      Hierarchical category tree
    Key Interactions:
      - Regal gold foil-framed bridal gallery
      - Zari silk color swatch triggers & auto-updating prices
      - Fabric craftsmanship breakdown & dry-clean hygiene guide
      - Delivery availability & pincode transit days checker
      - Patron bride testimonials & review submission
--}}

@php
  $card      = $product->toCardArray();
  $firstColor = $card['colors'][0] ?? null;
@endphp

<x-storefront-layout :theme="$theme" :product="$product">

<div class="bg-ivory min-h-screen">

{{-- ═══════════════════════════════════════════════
     BREADCRUMB
═══════════════════════════════════════════════ --}}
<nav class="max-w-7xl mx-auto px-6 lg:px-12 pt-6 pb-2" aria-label="Breadcrumb">
  <ol class="flex items-center gap-2 text-xs font-body text-maroon/50">
    <li><a href="{{ route('home') }}" class="hover:text-gold transition-colors">Home</a></li>
    <li><svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/></svg></li>
    @if($product->category)
      <li><a href="{{ route('category.show', $product->category->slug) }}" class="hover:text-gold transition-colors">{{ $product->category->name }}</a></li>
      <li><svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/></svg></li>
    @endif
    <li class="text-maroon truncate max-w-[180px]">{{ $product->name }}</li>
  </ol>
</nav>


{{-- ═══════════════════════════════════════════════
     MAIN  —  gallery left / picker right
═══════════════════════════════════════════════ --}}
<section class="max-w-7xl mx-auto px-6 lg:px-12 py-8 lg:py-12">
  <div class="grid lg:grid-cols-[1fr_480px] gap-8 lg:gap-16 items-start">

    {{-- ── GALLERY (left) ─────────────────────────── --}}
    <div class="pdp-gallery">

      {{-- Main image — foil-frame --}}
      <div class="foil-frame relative aspect-[3/4] overflow-hidden bg-blush/20 group select-none" style="aspect-ratio: 3/4;">
        <svg class="frame-line" viewBox="0 0 100 100" preserveAspectRatio="none">
          <rect x="2" y="2" width="96" height="96" vector-effect="non-scaling-stroke"/>
        </svg>
        @if($card['badge'])
          <span class="absolute top-4 left-4 z-10 bg-gold text-maroon text-[10px] px-3 py-1 tracking-widest uppercase font-body font-semibold shadow-xs">
            {{ $card['badge'] }}
          </span>
        @endif

        {{-- Slide Counter Badge --}}
        <div id="pdpSlideBadge" class="absolute bottom-3 right-3 z-10 bg-maroon/75 backdrop-blur-md text-gold text-[10px] font-mono font-semibold px-2.5 py-1 rounded-full border border-gold/30 shadow-xs pointer-events-none">
          1 / {{ count($firstColor['images'] ?? []) ?: 1 }}
        </div>

        {{-- Previous Slide Arrow --}}
        <button type="button" 
                id="pdpPrevBtn"
                class="absolute left-3 top-1/2 -translate-y-1/2 z-20 w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-ivory/90 hover:bg-gold text-maroon hover:text-maroon border border-gold/40 shadow-md flex items-center justify-center transition-all opacity-80 sm:opacity-0 sm:group-hover:opacity-100 hover:scale-110 cursor-pointer focus:outline-none"
                aria-label="Previous image">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
        </button>

        {{-- Next Slide Arrow --}}
        <button type="button" 
                id="pdpNextBtn"
                class="absolute right-3 top-1/2 -translate-y-1/2 z-20 w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-ivory/90 hover:bg-gold text-maroon hover:text-maroon border border-gold/40 shadow-md flex items-center justify-center transition-all opacity-80 sm:opacity-0 sm:group-hover:opacity-100 hover:scale-110 cursor-pointer focus:outline-none"
                aria-label="Next image">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
        </button>

        <img id="pdpMainImage"
             src="{{ $firstColor ? ($firstColor['images'][0] ?? $card['coverImage']) : $card['coverImage'] }}"
             alt="{{ $product->name }}"
             class="w-full h-full object-cover transition-opacity duration-300">
      </div>

      {{-- Thumbnail strip --}}
      <div id="pdpThumbs" class="flex gap-2 mt-3 overflow-x-auto pb-1 scrollbar-hide">
        @if($firstColor)
          @foreach($firstColor['images'] as $i => $img)
            <button type="button"
                    class="pdp-thumb flex-shrink-0 w-16 aspect-[3/4] border-2 overflow-hidden transition-all duration-200 cursor-pointer {{ $i === 0 ? 'border-gold scale-105 shadow-xs opacity-100' : 'border-gold/20 opacity-60 hover:opacity-100' }}"
                    style="aspect-ratio: 3/4;"
                    data-idx="{{ $i }}"
                    data-src="{{ $img }}"
                    title="View image {{ $i + 1 }}">
              <img src="{{ $img }}" alt="{{ $product->name }} view {{ $i + 1 }}" class="w-full h-full object-cover pointer-events-none">
            </button>
          @endforeach
        @endif
      </div>

    </div>

    {{-- ── PICKER (right) ─────────────────────────── --}}
    <div class="pdp-picker lg:sticky lg:top-28 space-y-6"
         id="pdpPicker"
         data-product='@json($card)'>

      {{-- Category & Brand tag --}}
      <div class="flex items-center gap-2 text-[10px] tracking-widest uppercase font-body text-gold-dark">
        @if($product->brand)
          <span class="font-bold text-maroon/80">{{ $product->brand->name }}</span>
          @if($product->category) <span>·</span> @endif
        @endif
        @if($product->category)
          <span>{{ $product->category->name }}</span>
        @endif
      </div>

      {{-- Name --}}
      <h1 class="font-display text-4xl lg:text-5xl text-maroon leading-tight">{{ $product->name }}</h1>

      {{-- Price --}}
      <div class="flex items-baseline gap-3">
        <span id="pdpPrice" class="font-display text-2xl text-maroon">₹{{ number_format($card['startingPrice'], 0) }}</span>
        @if($card['startingCompareAt'])
          <span id="pdpCompare" class="text-maroon/40 line-through text-base font-body">₹{{ number_format($card['startingCompareAt'], 0) }}</span>
        @endif
        <span id="pdpSavings" class="hidden text-xs font-body text-gold bg-gold/10 px-2 py-0.5 rounded-full"></span>
      </div>

      {{-- Short Description --}}
      @if($product->description)
        <div class="font-body text-maroon/80 text-sm leading-relaxed product-rich-text">{!! $product->description !!}</div>
      @endif

      {{-- Petal divider --}}
      <div class="petal-divider opacity-30"></div>

      {{-- Color swatches --}}
      @if(count($card['colors']))
        <div>
          <p class="text-xs font-body font-semibold text-maroon/60 uppercase tracking-widest mb-3">
            Colour: <span id="pdpColorName" class="text-maroon normal-case font-normal tracking-normal">{{ $card['colors'][0]['name'] ?? '' }}</span>
          </p>
          <div class="flex flex-wrap gap-2.5" id="pdpSwatches">
            @foreach($card['colors'] as $i => $color)
              <button type="button"
                      class="pdp-swatch w-8 h-8 rounded-full border-2 transition-all duration-200 hover:scale-110 {{ $i === 0 ? 'border-maroon scale-110' : 'border-gold/30' }}"
                      style="background-color:{{ $color['hex'] ?? '#ccc' }};"
                      title="{{ $color['name'] }}"
                      data-color-index="{{ $i }}"
                      aria-label="{{ $color['name'] }}">
              </button>
            @endforeach
          </div>
        </div>

        {{-- Size pills --}}
        <div>
          <div class="flex justify-between items-center mb-3">
            <p class="text-xs font-body font-semibold text-maroon/60 uppercase tracking-widest">
              Size: <span id="pdpSizeName" class="text-maroon normal-case font-normal tracking-normal">—</span>
            </p>
            <button type="button" id="pdpSizeGuide"
                    class="text-xs font-body text-gold underline-grow hover:text-gold-dark transition-colors">
              Size Guide
            </button>
          </div>
          <div class="flex flex-wrap gap-2" id="pdpSizePills">
            {{-- Populated by JS based on selected color --}}
          </div>
        </div>
      @endif

      {{-- Add to bag --}}
      <div class="space-y-3 pt-2">
        <button type="button" id="pdpAddBtn"
                class="gold-btn w-full py-4 text-xs font-body font-semibold tracking-widest uppercase disabled:opacity-40 disabled:cursor-not-allowed disabled:shadow-none disabled:translate-y-0"
                disabled>
          Select a Size
        </button>
        <button type="button" id="pdpWishBtn"
                class="w-full border border-gold/40 text-maroon py-3.5 text-xs font-body font-semibold tracking-widest uppercase hover:border-gold hover:bg-gold/5 transition-colors flex items-center justify-center gap-2">
          <svg id="pdpWishIcon" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 016.364 0L12 7.636l1.318-1.318a4.5 4.5 0 116.364 6.364L12 20.364l-7.682-7.682a4.5 4.5 0 010-6.364z"/>
          </svg>
          <span id="pdpWishLabel">Save to Wishlist</span>
        </button>
      </div>

      {{-- Pincode & Delivery Checker --}}
      <div class="pt-2 pb-2 border-t border-gold/20">
        <label for="pdpPincodeInput" class="block text-xs font-body font-semibold text-maroon/80 uppercase tracking-widest mb-2 flex items-center gap-1.5">
          <svg class="w-3.5 h-3.5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
          Check Delivery Availability
        </label>
        <div class="flex gap-2">
          <input type="text" id="pdpPincodeInput" maxlength="6" pattern="[0-9]{6}" placeholder="Enter 6-digit PIN code"
                 class="flex-1 border border-gold/30 bg-transparent px-3 py-2 text-xs font-body text-maroon placeholder-maroon/30 focus:border-gold outline-none">
          <button type="button" id="pdpPincodeBtn"
                  class="px-4 py-2 text-xs font-body font-semibold tracking-wider uppercase border border-gold text-maroon hover:bg-gold hover:text-maroon transition-colors">
            Check
          </button>
        </div>
        <div id="pdpPincodeResult" class="hidden mt-2.5 text-xs font-body"></div>
      </div>

      {{-- Available Add-ons --}}
      @if($product->addons->isNotEmpty())
        <div class="pt-2 pb-2 border-t border-gold/20">
          <p class="text-xs font-body font-semibold text-maroon/80 uppercase tracking-widest mb-2 flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            Available Add-ons
          </p>
          <div class="space-y-1.5">
            @foreach($product->addons as $addon)
              <label class="flex items-center justify-between text-xs font-body text-maroon/90 bg-gold/5 hover:bg-gold/10 border border-gold/20 px-3 py-2 rounded cursor-pointer transition select-none">
                <div class="flex items-center gap-2.5">
                  <input type="checkbox"
                         class="pdp-addon-checkbox rounded border-gold/40 text-maroon focus:ring-gold w-4 h-4 cursor-pointer accent-[#58111A]"
                         data-price="{{ $addon->price }}"
                         data-name="{{ $addon->name }}"
                         data-id="{{ $addon->id }}"
                         onchange="if(typeof updateProductAddonPricing === 'function') updateProductAddonPricing()">
                  <span class="font-medium">{{ $addon->name }}</span>
                </div>
                <span class="font-semibold text-maroon font-mono">+₹{{ number_format($addon->price, 0) }}</span>
              </label>
            @endforeach
          </div>
        </div>
      @endif

    </div>{{-- /pdp-picker --}}
  </div>
</section>


{{-- ═══════════════════════════════════════════════
     PRODUCT HIGHLIGHTS & ACCORDION (Separate Division)
═══════════════════════════════════════════════ --}}
<section class="border-t border-gold/20 py-12 lg:py-16 bg-ivory">
  <div class="max-w-7xl mx-auto px-6 lg:px-12 space-y-10">

    {{-- Product Highlights --}}
    @if($product->long_description)
      <div class="space-y-4">
        <div class="flex items-center gap-2.5">
          <span class="w-1 h-5 rounded-full bg-gradient-to-b from-gold to-gold-dark inline-block shrink-0"></span>
          <h3 class="font-display text-lg lg:text-xl font-semibold text-maroon tracking-wider uppercase">Product Highlights</h3>
        </div>
        <div class="font-body text-maroon/80 text-sm leading-relaxed product-rich-text">
          {!! $product->long_description !!}
        </div>
      </div>
    @endif

    {{-- Accordion: Care, Delivery, Returns --}}
    @php
      $saayaDelivery = \App\Models\Setting::get('product_delivery_info', 'Ships within 2–3 business days. Free delivery on orders above ₹999. Express delivery available at checkout.');
      $saayaReturns  = \App\Models\Setting::get('product_returns_info', 'Hassle-free returns within 15 days of delivery. Items must be unworn with all tags attached.');
    @endphp
    <div class="border-t border-gold/20 divide-y divide-gold/15" id="pdpAccordion">
      @foreach([
        ['Care Instructions',  'Hand wash in cold water with mild detergent. Do not bleach. Reshape and dry flat away from direct sunlight.'],
        ['Delivery',           $saayaDelivery],
        ['Returns & Exchange', $saayaReturns],
      ] as [$title, $body])
        <div class="pdp-acc-item">
          <button type="button"
                  class="pdp-acc-trigger flex items-center justify-between w-full py-4 text-left text-sm font-body font-semibold text-maroon hover:text-gold transition-colors">
            <span>{{ $title }}</span>
            <svg class="pdp-acc-icon w-4 h-4 text-gold/60 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
          </button>
          <div class="pdp-acc-body overflow-hidden max-h-0 transition-all duration-300">
            <p class="font-body text-sm text-maroon/60 leading-relaxed pb-4">{{ $body }}</p>
          </div>
        </div>
      @endforeach
    </div>

  </div>
</section>


{{-- ═══════════════════════════════════════════════
     REVIEWS & CLIENT REFLECTIONS
═══════════════════════════════════════════════ --}}
<section class="border-t border-gold/20 py-14 lg:py-20 bg-ivory">
  <div class="max-w-7xl mx-auto px-6 lg:px-12">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12">
      <div>
        <p class="font-display italic text-gold text-lg mb-1">Customer Reviews</p>
        <h2 class="font-display text-3xl lg:text-4xl text-maroon">Client Reflections</h2>
        <div class="flex items-center gap-3 mt-3">
          @php
            $revCount = $product->approvedReviews->count();
            $avgRating = $revCount ? round($product->approvedReviews->avg('rating'), 1) : 5.0;
          @endphp
          <div class="flex items-center text-gold">
            @for($i = 1; $i <= 5; $i++)
              @if($i <= round($avgRating))
                <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
              @else
                <svg class="w-4 h-4 text-gold/30 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
              @endif
            @endfor
          </div>
          <span class="font-display text-lg text-maroon">{{ $avgRating }} / 5</span>
          <span class="font-body text-xs text-maroon/50">({{ $revCount }} verified {{ Str::plural('review', $revCount) }})</span>
        </div>
      </div>
      <button type="button" id="toggleReviewFormBtn" onclick="toggleReviewForm()"
              class="border border-gold text-maroon hover:bg-gold hover:text-maroon px-6 py-3 text-xs font-body font-semibold tracking-widest uppercase transition-colors">
        Write a Review
      </button>
    </div>

    {{-- Review Form (Collapsible) --}}
    <div id="reviewFormWrap" class="hidden mb-12 p-8 border border-gold/30 bg-gold/5 relative foil-frame">
      <svg class="frame-line" viewBox="0 0 100 100" preserveAspectRatio="none">
        <rect x="2" y="2" width="96" height="96" vector-effect="non-scaling-stroke"/>
      </svg>
      <h3 class="font-display text-2xl text-maroon mb-6">Share Your Experience</h3>
      <form id="productReviewForm" onsubmit="event.preventDefault(); submitProductReview(this);" class="space-y-5">
        @csrf
        <div>
          <label class="block text-xs font-body font-semibold uppercase tracking-widest text-maroon/70 mb-2">Your Rating *</label>
          <div class="flex items-center gap-2" id="starRatingSelector">
            @for($s = 1; $s <= 5; $s++)
              <button type="button" class="star-btn text-2xl text-gold focus:outline-none cursor-pointer" data-value="{{ $s }}">★</button>
            @endfor
            <input type="hidden" name="rating" id="reviewRatingInput" value="5" required>
            <span id="starRatingLabel" class="text-xs font-body text-maroon/60 ml-2">5 Stars — Exceptional</span>
          </div>
        </div>

        <div class="grid sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-body font-semibold uppercase tracking-widest text-maroon/70 mb-1.5">Your Name *</label>
            <input type="text" name="customer_name" required value="{{ Auth::user()?->name ?? '' }}" placeholder="e.g. Priya S."
                   class="w-full border border-gold/30 bg-white px-4 py-2.5 text-sm text-maroon font-body focus:border-gold outline-none">
          </div>
          <div>
            <label class="block text-xs font-body font-semibold uppercase tracking-widest text-maroon/70 mb-1.5">Review Headline</label>
            <input type="text" name="title" placeholder="e.g. Luxurious fit & exquisite craftsmanship"
                   class="w-full border border-gold/30 bg-white px-4 py-2.5 text-sm text-maroon font-body focus:border-gold outline-none">
          </div>
        </div>

        <div>
          <label class="block text-xs font-body font-semibold uppercase tracking-widest text-maroon/70 mb-1.5">Detailed Review *</label>
          <textarea name="comment" rows="4" required placeholder="How does the fabric feel? How was the fit and finishing?"
                    class="w-full border border-gold/30 bg-white p-4 text-sm text-maroon font-body focus:border-gold outline-none leading-relaxed"></textarea>
        </div>

        <div class="flex items-center justify-between pt-2">
          <p class="text-[11px] font-body text-maroon/50">Your review will be verified by our team before publishing.</p>
          <button type="submit" id="submitReviewBtn"
                  class="gold-btn py-3 px-8 text-xs font-body font-semibold tracking-widest uppercase">
            Submit Review
          </button>
        </div>
      </form>
      <div id="reviewSuccessMsg" class="hidden text-center py-6">
        <p class="font-display text-2xl text-maroon mb-1">Thank You</p>
        <p class="font-body text-sm text-maroon/70">Your review has been submitted and will appear upon moderation.</p>
      </div>
    </div>

    {{-- Reviews List --}}
    @if($product->approvedReviews->count())
      <div class="divide-y divide-gold/15">
        @foreach($product->approvedReviews as $rev)
          <div class="py-6">
            <div class="flex items-center justify-between gap-4 mb-2">
              <div class="flex items-center gap-1 text-gold">
                @for($i = 1; $i <= 5; $i++)
                  <span class="{{ $i <= $rev->rating ? 'text-gold' : 'text-gold/25' }} text-sm">★</span>
                @endfor
              </div>
              <span class="font-body text-xs text-maroon/40">{{ $rev->created_at->format('d M Y') }}</span>
            </div>
            @if($rev->title)
              <h4 class="font-display text-lg text-maroon mb-1">{{ $rev->title }}</h4>
            @endif
            <p class="font-body text-sm text-maroon/70 leading-relaxed mb-3">{{ $rev->comment }}</p>
            <div class="flex items-center gap-2 text-xs font-body">
              <span class="font-semibold text-maroon">{{ $rev->customer_name }}</span>
              <span class="text-maroon/30">·</span>
              <span class="text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded text-[10px] uppercase font-bold tracking-wider">Verified Purchase</span>
            </div>
          </div>
        @endforeach
      </div>
    @else
      <div class="text-center py-12 border border-dashed border-gold/30 p-8">
        <p class="font-display text-xl text-maroon mb-1">No Client Reflections Yet</p>
        <p class="font-body text-xs text-maroon/50 mb-4">Be the first to share your experience with this bespoke creation.</p>
        <button type="button" onclick="toggleReviewForm()" class="text-xs font-body font-semibold text-gold-dark hover:underline uppercase tracking-wider">
          Share your review &rarr;
        </button>
      </div>
    @endif
  </div>
</section>


{{-- ═══════════════════════════════════════════════
     RELATED PRODUCTS
═══════════════════════════════════════════════ --}}
@if($relatedProducts->count())
<section class="bg-maroon-dark py-14 lg:py-20">
  <div class="max-w-7xl mx-auto px-6 lg:px-12">

    <div class="text-center mb-10 reveal">
      <p class="font-display italic text-gold text-lg mb-1">You May Also Love</p>
      <h2 class="font-display text-4xl text-ivory">Complete Your Trousseau</h2>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 lg:gap-6">
      @foreach($relatedProducts->take(4) as $related)
        @php $relCard = $related->toCardArray(); @endphp
        <div class="reveal">
          @include('storefront.saaya.partials.product-card', ['cardData' => $relCard, 'product' => $related])
        </div>
      @endforeach
    </div>

  </div>
</section>
@endif


{{-- ═══════════════════════════════════════════════
     SIZE GUIDE MODAL
═══════════════════════════════════════════════ --}}
<div id="sizeGuideModal"
     class="hidden fixed inset-0 z-[60] bg-maroon-dark/80 backdrop-blur-sm items-center justify-center p-6"
     role="dialog" aria-modal="true" aria-labelledby="sgTitle">
  <div class="bg-ivory max-w-lg w-full p-8 relative foil-frame max-h-[80vh] overflow-y-auto">
    <svg class="frame-line" viewBox="0 0 100 100" preserveAspectRatio="none">
      <rect x="2" y="2" width="96" height="96" vector-effect="non-scaling-stroke"/>
    </svg>
    <button id="sgClose" class="absolute top-4 right-4 text-maroon/40 hover:text-maroon transition-colors" aria-label="Close">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
    <h2 id="sgTitle" class="font-display text-2xl text-maroon mb-6">Size Guide</h2>
    <p class="font-body text-xs text-maroon/50 mb-4">All measurements in inches (chest circumference).</p>
    <table class="w-full text-sm font-body text-maroon/80 border-collapse">
      <thead>
        <tr class="border-b border-gold/30">
          <th class="text-left py-2 pr-4 font-semibold text-maroon">Size</th>
          <th class="text-left py-2 pr-4 font-semibold text-maroon">Chest (in)</th>
          <th class="text-left py-2 font-semibold text-maroon">Fits Cup</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gold/15">
        @foreach([['XS','30–32','A–B'],['S','32–34','A–C'],['M','34–36','B–D'],['L','36–38','C–D'],['XL','38–40','D–E'],['XXL','40–42','DD–F']] as [$s,$c,$cup])
          <tr>
            <td class="py-2.5 pr-4 font-semibold">{{ $s }}</td>
            <td class="py-2.5 pr-4">{{ $c }}</td>
            <td class="py-2.5">{{ $cup }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
    <p class="font-body text-xs text-maroon/40 mt-6">When between sizes, we recommend sizing up for comfort.</p>
  </div>
</div>

</div>{{-- /bg-ivory --}}

@push('scripts')
<script>
(function () {

  /* ────── helpers ────── */
  function esc(s) {
    return String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
  }

  /* ────── product data ────── */
  const picker      = document.getElementById('pdpPicker');
  const productData = JSON.parse(picker?.dataset.product || '{}');
  const colors      = productData.colors || [];

  let activeColorIdx = 0;
  let activeSizeIdx  = null;

  /* ────── DOM refs ────── */
  const mainImg    = document.getElementById('pdpMainImage');
  const thumbWrap  = document.getElementById('pdpThumbs');
  const priceEl    = document.getElementById('pdpPrice');
  const compareEl  = document.getElementById('pdpCompare');
  const savingsEl  = document.getElementById('pdpSavings');
  const colorName  = document.getElementById('pdpColorName');
  const sizeName   = document.getElementById('pdpSizeName');
  const sizePills  = document.getElementById('pdpSizePills');
  const addBtn     = document.getElementById('pdpAddBtn');
  const wishBtn    = document.getElementById('pdpWishBtn');
  const wishIcon   = document.getElementById('pdpWishIcon');
  const wishLabel  = document.getElementById('pdpWishLabel');

  /* ────── wishlist sync ────── */
  function getWishlist() {
    try { return JSON.parse(localStorage.getItem('lc_wishlist') || '[]'); } catch { return []; }
  }
  function saveWishlist(ids) {
    try { localStorage.setItem('lc_wishlist', JSON.stringify(ids)); } catch {}
  }
  function isWished() { return getWishlist().includes(productData.id); }
  function syncWishUI() {
    const w = isWished();
    wishIcon?.setAttribute('fill', w ? 'currentColor' : 'none');
    wishIcon?.classList.toggle('text-gold', w);
    wishIcon?.classList.toggle('text-maroon', !w);
    if (wishLabel) wishLabel.textContent = w ? 'Saved to Wishlist' : 'Save to Wishlist';
  }
  wishBtn?.addEventListener('click', () => {
    const ids = getWishlist();
    const i   = ids.indexOf(productData.id);
    if (i === -1) ids.push(productData.id); else ids.splice(i, 1);
    saveWishlist(ids);
    syncWishUI();
  });
  syncWishUI();

  /* ────── render size pills ────── */
  function renderSizePills(colorIdx) {
    if (!sizePills) return;
    activeSizeIdx = null;
    if (sizeName) sizeName.textContent = '—';
    if (addBtn)   { addBtn.disabled = true; addBtn.textContent = 'Select a Size'; }
    const sizes = colors[colorIdx]?.sizes || [];
    sizePills.innerHTML = sizes.map((s, i) => `
      <button type="button"
              class="pdp-size-pill text-[11px] w-10 h-10 rounded-full border font-body transition-all duration-150
                     ${s.inStock ? 'border-gold/40 text-maroon hover:border-maroon cursor-pointer' : 'border-gold/15 text-maroon/25 line-through cursor-not-allowed pointer-events-none'}"
              data-size-idx="${i}"
              ${!s.inStock ? 'disabled aria-disabled="true"' : ''}>
        ${esc(s.size)}
      </button>`).join('');

    sizePills.querySelectorAll('.pdp-size-pill:not([disabled])').forEach(btn => {
      btn.addEventListener('click', () => {
        const idx  = Number(btn.dataset.sizeIdx);
        const size = (colors[activeColorIdx]?.sizes || [])[idx];
        if (!size) return;
        activeSizeIdx = idx;

        sizePills.querySelectorAll('.pdp-size-pill').forEach(b => {
          b.classList.remove('bg-maroon','text-ivory','border-maroon','active');
          b.classList.add('border-gold/40','text-maroon');
          b.setAttribute('aria-selected', 'false');
        });
        btn.classList.add('bg-maroon','text-ivory','border-maroon','active');
        btn.classList.remove('border-gold/40','text-maroon');
        btn.setAttribute('aria-selected', 'true');

        if (sizeName) sizeName.textContent = size.size;
        updatePrice(size);
        if (addBtn) { addBtn.disabled = false; addBtn.textContent = 'Add to Bag'; }
      });
    });
  }

  /* ────── update price display ────── */
  let currentBasePrice = productData.startingPrice;

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

  function updatePrice(size) {
    if (size && size.price !== undefined) {
      currentBasePrice = Number(size.price);
    }
    updateProductAddonPricing(size);
  }

  function updateProductAddonPricing(size) {
    const addonTotal = getSelectedAddonsTotal();
    const p = currentBasePrice + addonTotal;
    const c = size?.compareAtPrice ?? productData.startingCompareAt;
    if (priceEl) priceEl.textContent = '₹' + Number(p).toLocaleString('en-IN');
    if (compareEl) {
      if (c && c > currentBasePrice) {
        compareEl.textContent = '₹' + Number(c).toLocaleString('en-IN');
        compareEl.classList.remove('hidden');
        const pct = Math.round((c - currentBasePrice) / c * 100);
        if (savingsEl) { savingsEl.textContent = pct + '% OFF'; savingsEl.classList.remove('hidden'); }
      } else {
        compareEl.classList.add('hidden');
        savingsEl?.classList.add('hidden');
      }
    }
  }

  /* ────── GALLERY SLIDER & THUMBNAILS ────── */
  let currentImageIdx = 0;
  let autoSlideTimer = null;
  const AUTO_SLIDE_DELAY = 4200; // 4.2 seconds auto-slide interval

  function getActiveImages() {
    return (colors[activeColorIdx]?.images && colors[activeColorIdx].images.length)
      ? colors[activeColorIdx].images
      : (productData.coverImage ? [productData.coverImage] : []);
  }

  function goToSlide(idx, userInitiated = false) {
    const imgs = getActiveImages();
    if (!imgs.length) return;

    if (idx < 0) idx = imgs.length - 1;
    if (idx >= imgs.length) idx = 0;
    currentImageIdx = idx;

    const targetSrc = imgs[currentImageIdx];

    // Smooth image transition
    if (mainImg) {
      mainImg.style.opacity = '0';
      setTimeout(() => {
        mainImg.src = targetSrc;
        mainImg.style.opacity = '1';
      }, 140);
    }

    // Update active thumbnail borders and scroll active thumb into view
    if (thumbWrap) {
      const thumbs = thumbWrap.querySelectorAll('.pdp-thumb');
      thumbs.forEach((b, i) => {
        const isActive = i === currentImageIdx;
        b.classList.toggle('border-gold', isActive);
        b.classList.toggle('scale-105', isActive);
        b.classList.toggle('shadow-xs', isActive);
        b.classList.toggle('opacity-100', isActive);
        b.classList.toggle('border-gold/20', !isActive);
        b.classList.toggle('opacity-60', !isActive);
        if (isActive) {
          b.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'nearest' });
        }
      });
    }

    // Update slide counter badge
    const badge = document.getElementById('pdpSlideBadge');
    if (badge) {
      badge.textContent = `${currentImageIdx + 1} / ${imgs.length}`;
      badge.style.display = imgs.length > 1 ? 'block' : 'none';
    }

    // Update arrow visibility
    const prevBtn = document.getElementById('pdpPrevBtn');
    const nextBtn = document.getElementById('pdpNextBtn');
    if (prevBtn) prevBtn.style.display = imgs.length > 1 ? 'flex' : 'none';
    if (nextBtn) nextBtn.style.display = imgs.length > 1 ? 'flex' : 'none';

    if (userInitiated) {
      restartAutoSlide();
    }
  }

  function nextSlide(userInitiated = false) {
    goToSlide(currentImageIdx + 1, userInitiated);
  }

  function prevSlide(userInitiated = false) {
    goToSlide(currentImageIdx - 1, userInitiated);
  }

  function startAutoSlide() {
    stopAutoSlide();
    const imgs = getActiveImages();
    if (imgs.length <= 1) return;
    autoSlideTimer = setInterval(() => {
      nextSlide(false);
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
    setTimeout(() => {
      startAutoSlide();
    }, 6000);
  }

  /* ────── render thumbnails ────── */
  function renderThumbs(colorIdx) {
    if (!thumbWrap) return;
    const imgs = (colors[colorIdx]?.images && colors[colorIdx].images.length)
      ? colors[colorIdx].images
      : (productData.coverImage ? [productData.coverImage] : []);

    currentImageIdx = 0;

    thumbWrap.innerHTML = imgs.map((src, i) => `
      <button type="button"
              class="pdp-thumb flex-shrink-0 w-16 aspect-[3/4] border-2 overflow-hidden transition-all duration-200 cursor-pointer ${i === 0 ? 'border-gold scale-105 shadow-xs opacity-100' : 'border-gold/20 opacity-60 hover:opacity-100 hover:border-gold/60'}"
              style="aspect-ratio: 3/4;"
              data-idx="${i}"
              data-src="${esc(src)}"
              title="View image ${i + 1}">
        <img src="${esc(src)}" alt="" class="w-full h-full object-cover pointer-events-none">
      </button>`).join('');

    thumbWrap.querySelectorAll('.pdp-thumb').forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        const idx = Number(btn.dataset.idx);
        goToSlide(idx, true);
      });
    });

    goToSlide(0, false);
    startAutoSlide();
  }

  // Prev / Next button listeners
  document.getElementById('pdpPrevBtn')?.addEventListener('click', (e) => {
    e.preventDefault();
    e.stopPropagation();
    prevSlide(true);
  });
  document.getElementById('pdpNextBtn')?.addEventListener('click', (e) => {
    e.preventDefault();
    e.stopPropagation();
    nextSlide(true);
  });

  // Pause auto-sliding when mouse hovers over gallery, resume on leave
  const galleryWrap = document.querySelector('.pdp-gallery');
  if (galleryWrap) {
    galleryWrap.addEventListener('mouseenter', stopAutoSlide);
    galleryWrap.addEventListener('mouseleave', startAutoSlide);
  }

  // Touch swipe support for mobile
  let touchStartX = 0;
  let touchEndX = 0;
  const mainImageContainer = mainImg?.closest('.foil-frame');
  if (mainImageContainer) {
    mainImageContainer.addEventListener('touchstart', (e) => {
      touchStartX = e.changedTouches[0].screenX;
    }, { passive: true });
    mainImageContainer.addEventListener('touchend', (e) => {
      touchEndX = e.changedTouches[0].screenX;
      const diff = touchEndX - touchStartX;
      if (Math.abs(diff) > 40) {
        if (diff < 0) {
          nextSlide(true); // Swipe left -> next image
        } else {
          prevSlide(true); // Swipe right -> prev image
        }
      }
    }, { passive: true });
  }

  /* ────── color swatch clicks ────── */
  document.querySelectorAll('.pdp-swatch').forEach(btn => {
    btn.addEventListener('click', () => {
      activeColorIdx = Number(btn.dataset.colorIndex);
      document.querySelectorAll('.pdp-swatch').forEach(b => {
        b.classList.remove('border-maroon','scale-110');
        b.classList.add('border-gold/30');
      });
      btn.classList.add('border-maroon','scale-110');
      btn.classList.remove('border-gold/30');

      if (colorName) colorName.textContent = colors[activeColorIdx]?.name ?? '';
      renderThumbs(activeColorIdx);
      renderSizePills(activeColorIdx);
    });
  });

  /* ────── add to bag ────── */
  addBtn?.addEventListener('click', () => {
    const color = colors[activeColorIdx];
    const size  = color?.sizes?.[activeSizeIdx];
    if (!color || !size) return;

    const addonTotal = getSelectedAddonsTotal();
    const finalPrice = Number(size.price) + addonTotal;
    const addonNames = getSelectedAddonNames();
    const extraDesc  = addonNames.length ? ' (Add-ons: ' + addonNames.join(', ') + ')' : '';

    if (typeof addToCart === 'function') {
      addToCart({
        productId : productData.id,
        variantId : size.variantId,
        sku       : size.sku,
        name      : productData.name + extraDesc,
        colorName : color.name,
        size      : size.size,
        price     : finalPrice,
        image     : color.images?.[0] ?? productData.coverImage,
        qty       : 1
      });
    }

    // feedback flash
    const orig = addBtn.textContent;
    addBtn.textContent = '✓ Added to Bag';
    addBtn.disabled = true;
    setTimeout(() => { addBtn.textContent = orig; addBtn.disabled = false; }, 1800);

    // open cart drawer
    if (typeof openCart === 'function') setTimeout(openCart, 200);
  });

  /* ────── accordion ────── */
  document.querySelectorAll('.pdp-acc-trigger').forEach(btn => {
    btn.addEventListener('click', () => {
      const item = btn.closest('.pdp-acc-item');
      const body = item?.querySelector('.pdp-acc-body');
      const icon = item?.querySelector('.pdp-acc-icon');
      const open = body?.style.maxHeight && body.style.maxHeight !== '0px';

      // close all
      document.querySelectorAll('.pdp-acc-body').forEach(b => { b.style.maxHeight = '0px'; });
      document.querySelectorAll('.pdp-acc-icon').forEach(i => i.classList.remove('rotate-180'));

      if (!open && body) {
        body.style.maxHeight = body.scrollHeight + 'px';
        icon?.classList.add('rotate-180');
      }
    });
  });

  /* ────── size guide modal ────── */
  const sgModal = document.getElementById('sizeGuideModal');
  document.getElementById('pdpSizeGuide')?.addEventListener('click', () => {
    sgModal?.classList.remove('hidden'); sgModal?.classList.add('flex');
  });
  document.getElementById('sgClose')?.addEventListener('click', () => {
    sgModal?.classList.add('hidden'); sgModal?.classList.remove('flex');
  });
  sgModal?.addEventListener('click', e => { if (e.target === sgModal) { sgModal.classList.add('hidden'); sgModal.classList.remove('flex'); } });
  document.addEventListener('keydown', e => { if (e.key === 'Escape') { sgModal?.classList.add('hidden'); sgModal?.classList.remove('flex'); } });

  /* ────── reveal on scroll ────── */
  if ('IntersectionObserver' in window) {
    const obs = new IntersectionObserver(entries => {
      entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('visible'); obs.unobserve(e.target); } });
    }, { threshold: 0.1 });
    document.querySelectorAll('.reveal').forEach(el => obs.observe(el));
  } else {
    document.querySelectorAll('.reveal').forEach(el => el.classList.add('visible'));
  }

  /* ────── pincode checker ────── */
  const pinInput = document.getElementById('pdpPincodeInput');
  const pinBtn   = document.getElementById('pdpPincodeBtn');
  const pinRes   = document.getElementById('pdpPincodeResult');

  async function checkPincode() {
    const pin = pinInput?.value.trim();
    if (!pin || pin.length !== 6 || !/^\d{6}$/.test(pin)) {
      if (pinRes) {
        pinRes.className = 'mt-2.5 text-xs font-body text-rose-600';
        pinRes.textContent = 'Please enter a valid 6-digit Indian PIN code.';
        pinRes.classList.remove('hidden');
      }
      return;
    }
    if (pinBtn) { pinBtn.disabled = true; pinBtn.textContent = '...'; }
    try {
      const res = await fetch('{{ route('pincode.check') }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ pincode: pin })
      });
      const data = await res.json();
      if (data.success && data.data) {
        const info = data.data;
        if (pinRes) {
          pinRes.className = 'mt-2.5 text-xs font-body p-3 bg-gold/10 border border-gold/25 rounded space-y-1 text-maroon';
          pinRes.innerHTML = `
            <div class="flex items-center gap-1.5 font-semibold text-emerald-700">
              <svg class="w-3.5 h-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
              <span>Delivery available to ${info.city ? info.city + ', ' + info.state : 'your destination'} (${info.pincode})</span>
            </div>
            <p class="text-maroon/80">Estimated delivery: <strong>${info.estimated_days} days</strong> (${info.estimated_date})</p>
            <p class="text-xs ${info.cod_available ? 'text-emerald-700' : 'text-amber-700'}">
              ${info.cod_available ? '✓ Cash on Delivery (COD) available' : 'ℹ Prepaid orders only (Card / UPI)'}
            </p>
          `;
        }
      } else {
        if (pinRes) {
          pinRes.className = 'mt-2.5 text-xs font-body text-amber-700';
          pinRes.textContent = data.message || 'Delivery service available via standard postal courier.';
        }
      }
    } catch {
      if (pinRes) {
        pinRes.className = 'mt-2.5 text-xs font-body text-rose-600';
        pinRes.textContent = 'Unable to check delivery at this moment.';
      }
    } finally {
      if (pinBtn) { pinBtn.disabled = false; pinBtn.textContent = 'Check'; }
      pinRes?.classList.remove('hidden');
    }
  }
  pinBtn?.addEventListener('click', checkPincode);
  pinInput?.addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); checkPincode(); } });

  /* ────── reviews & ratings ────── */
  window.toggleReviewForm = function () {
    const wrap = document.getElementById('reviewFormWrap');
    if (wrap) {
      wrap.classList.toggle('hidden');
      if (!wrap.classList.contains('hidden')) {
        wrap.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
    }
  };

  const starBtns = document.querySelectorAll('#starRatingSelector .star-btn');
  const starInput = document.getElementById('reviewRatingInput');
  const starLabel = document.getElementById('starRatingLabel');
  const starTexts = {
    1: '1 Star — Poor',
    2: '2 Stars — Fair',
    3: '3 Stars — Average',
    4: '4 Stars — Very Good',
    5: '5 Stars — Exceptional'
  };

  starBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      const val = Number(btn.dataset.value);
      if (starInput) starInput.value = val;
      if (starLabel) starLabel.textContent = starTexts[val] || `${val} Stars`;
      starBtns.forEach(b => {
        const bVal = Number(b.dataset.value);
        if (bVal <= val) {
          b.classList.remove('text-gold/30');
          b.classList.add('text-gold');
        } else {
          b.classList.remove('text-gold');
          b.classList.add('text-gold/30');
        }
      });
    });
  });

  window.submitProductReview = async function (form) {
    const btn = document.getElementById('submitReviewBtn');
    const successBox = document.getElementById('reviewSuccessMsg');
    if (btn) { btn.disabled = true; btn.textContent = 'Submitting...'; }

    const formData = new FormData(form);
    const payload = Object.fromEntries(formData.entries());

    try {
      const res = await fetch('{{ route('products.review', $product->slug) }}', {
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
        form.classList.add('hidden');
        if (successBox) {
          successBox.classList.remove('hidden');
        }
      } else {
        alert(data.message || 'Error submitting review.');
      }
    } catch {
      alert('An unexpected error occurred while submitting your review.');
    } finally {
      if (btn) { btn.disabled = false; btn.textContent = 'Submit Review'; }
    }
  };

  /* ────── init ────── */
  renderThumbs(0);
  renderSizePills(0);

})();
</script>
@endpush

</x-storefront-layout>
