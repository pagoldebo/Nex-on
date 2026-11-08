{{-- resources/views/storefront/organic/product.blade.php
     Organic Market Single Product Details Page: Gallery, Nutritional/Farm Specs, Variant Stepper, Add to Basket, Pincode ETA, and Reviews. --}}
<x-storefront-layout :theme="$theme" :title="$product->name">
  
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
    
    {{-- Breadcrumbs --}}
    <nav class="text-xs text-stone-500 font-medium flex items-center gap-2 mb-8">
      <a href="{{ route('home') }}" class="hover:text-organic-600 transition">Home</a>
      <span>/</span>
      <a href="{{ route('category.show', $product->category->slug) }}" class="hover:text-organic-600 transition">{{ $product->category->name }}</a>
      <span>/</span>
      <span class="text-stone-900 font-bold truncate max-w-xs">{{ $product->name }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-start">
      
      {{-- LEFT: Image Gallery --}}
      <div class="lg:col-span-6 space-y-4">
        <div class="aspect-[3/4] bg-white rounded-3xl border border-stone-200 p-6 flex items-center justify-center relative overflow-hidden shadow-sm" style="aspect-ratio: 3/4;">
          
          {{-- Badge --}}
          @if($product->badge)
            <span class="absolute top-4 left-4 bg-citrus text-white text-xs font-black uppercase px-3 py-1 rounded-full shadow-xs">
              {{ $product->badge }}
            </span>
          @else
            <span class="absolute top-4 left-4 bg-organic-500 text-white text-[11px] font-bold uppercase px-3 py-1 rounded-full shadow-xs flex items-center gap-1">
              <svg class="w-3.5 h-3.5" viewBox="0 0 24 24"><use xlink:href="#organic-leaf"></use></svg>
              100% Organic
            </span>
          @endif

          <img id="mainProductImg" 
               src="{{ $product->cover_image }}" 
               alt="{{ $product->name }}" 
               class="max-h-full max-w-full object-contain transition-all duration-300">
        </div>

        {{-- Thumbnails --}}
        <div class="flex items-center gap-3 overflow-x-auto pb-2">
          @foreach($product->colors as $color)
            @foreach($color->images as $img)
              <button type="button" 
                      onclick="document.getElementById('mainProductImg').src='{{ asset('storage/' . $img->image_path) }}'"
                      class="w-16 aspect-[3/4] rounded-2xl bg-white border-2 border-stone-200 hover:border-organic-500 p-1 shrink-0 transition overflow-hidden"
                      style="aspect-ratio: 3/4;">
                <img src="{{ asset('storage/' . $img->image_path) }}" alt="{{ $color->name }}" class="w-full h-full object-contain">
              </button>
            @endforeach
          @endforeach
        </div>
      </div>

      {{-- RIGHT: Product Info & Order Box --}}
      <div class="lg:col-span-6 space-y-6">
        <div>
          <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider mb-1">
            @if($product->brand)
              <span class="text-stone-900">{{ $product->brand->name }}</span>
              <span class="text-stone-300">·</span>
            @endif
            <span class="text-organic-600">{{ $product->category->name }}</span>
          </div>
          <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black text-stone-900 font-display leading-tight">
            {{ $product->name }}
          </h1>

          {{-- Rating --}}
          <div class="flex items-center gap-2 mt-2">
            <div class="flex items-center text-citrus-gold text-sm">
              @for($i = 0; $i < 5; $i++)
                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><use xlink:href="#organic-star"></use></svg>
              @endfor
            </div>
            <span class="text-xs text-stone-500 font-semibold">(4.9 • 128 Verified Farm Patrons)</span>
          </div>
        </div>

        {{-- Price Box --}}
        <div class="p-4 bg-white rounded-2xl border border-stone-200 shadow-2xs flex items-baseline gap-3">
          <span id="currentDisplayPrice" class="text-3xl font-black text-organic-900 font-display">
            ₹{{ number_format($product->starting_price, 2) }}
          </span>
          @if($product->compare_at_price && $product->compare_at_price > $product->starting_price)
            <span id="compareDisplayPrice" class="text-sm text-stone-400 line-through">
              ₹{{ number_format($product->compare_at_price, 2) }}
            </span>
            <span class="text-xs font-black text-citrus bg-citrus-light px-2.5 py-0.5 rounded-full">
              Save ₹{{ number_format($product->compare_at_price - $product->starting_price, 2) }}
            </span>
          @endif
        </div>

        {{-- Description snippet --}}
        @if($product->description)
          <div class="text-xs sm:text-sm text-stone-600 leading-relaxed font-light product-rich-text">
            {!! $product->description !!}
          </div>
        @endif

        {{-- Variant & Color Selectors --}}
        @php
          $colors = $product->colors()->with('variants')->get();
          $defaultColor = $colors->first();
          $variants = $defaultColor?->variants ?? collect();
        @endphp

        <div class="space-y-4 pt-2 border-t border-stone-200">
          
          {{-- Variety / Color Swatches if present --}}
          @if($colors->count() > 1)
            <div>
              <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-2">
                Harvest Variety: <span id="activeVarietyName" class="text-organic-700 font-extrabold">{{ $defaultColor?->name }}</span>
              </label>
              <div class="flex items-center gap-2 flex-wrap">
                @foreach($colors as $ci => $color)
                  <button type="button" 
                          onclick="selectProductColor({{ $ci }}, @js($color->name))"
                          class="variety-btn px-3 py-1.5 rounded-xl border text-xs font-semibold flex items-center gap-2 transition {{ $ci === 0 ? 'border-organic-500 bg-organic-50 text-organic-800' : 'border-stone-200 bg-white text-stone-700' }}"
                          data-color-idx="{{ $ci }}">
                    <span class="w-3.5 h-3.5 rounded-full border border-stone-300" style="background-color: {{ $color->hex ?? '#699E4B' }}"></span>
                    <span>{{ $color->name }}</span>
                  </button>
                @endforeach
              </div>
            </div>
          @endif

          {{-- Pack Size / Weight Variant Pills --}}
          <div>
            <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-2">
              Select Pack / Net Weight:
            </label>
            <div class="flex items-center gap-2.5 flex-wrap" id="sizePillsWrapper">
              @forelse($variants as $vi => $v)
                <button type="button" 
                        onclick="selectProductVariant({{ $v->id }}, '{{ $v->size }}', {{ $v->price }}, {{ $v->stock }})"
                        class="size-variant-btn px-4 py-2 rounded-xl border-2 text-xs font-bold transition {{ $vi === 0 ? 'border-organic-500 bg-organic-50 text-organic-900 shadow-xs' : 'border-stone-200 bg-white text-stone-700 hover:border-stone-300' }}"
                        data-variant-id="{{ $v->id }}"
                        data-price="{{ $v->price }}"
                        data-size="{{ $v->size }}">
                  {{ $v->size }}
                </button>
              @empty
                <span class="text-xs text-stone-400">Standard Pack (1 Unit)</span>
              @endforelse
            </div>
          </div>

          {{-- Quantity Selector & Add to Cart --}}
          <div class="pt-2 flex flex-col sm:flex-row items-stretch sm:items-center gap-3" x-data="{ qty: 1 }">
            <div class="flex items-center justify-between border-2 border-stone-200 rounded-2xl bg-white px-3 py-2 w-full sm:w-36">
              <button type="button" @click="if (qty > 1) qty--" class="w-8 h-8 rounded-lg hover:bg-stone-100 flex items-center justify-center text-stone-600 font-bold text-lg" aria-label="Decrease quantity">−</button>
              <span x-text="qty" class="font-extrabold text-sm text-stone-900">1</span>
              <button type="button" @click="qty++" class="w-8 h-8 rounded-lg hover:bg-stone-100 flex items-center justify-center text-stone-600 font-bold text-lg" aria-label="Increase quantity">+</button>
            </div>

            <button type="button" 
                    id="addToBagPrimaryBtn"
                    onclick="triggerDirectAddToCart()"
                    class="btn-organic flex-1 py-3.5 px-6 rounded-2xl font-bold text-xs uppercase tracking-wider shadow-lg flex items-center justify-center gap-2">
              <svg class="w-5 h-5" viewBox="0 0 24 24"><use xlink:href="#organic-cart"></use></svg>
              <span>Add to Fresh Basket</span>
            </button>
          </div>

        </div>

        {{-- Indian PIN Code Delivery Checker --}}
        <div class="p-4 bg-stone-50 rounded-2xl border border-stone-200/80 text-xs" x-data="{ 
            pincode: '', 
            status: '', 
            message: '', 
            checking: false,
            check() {
              if (this.pincode.length !== 6 || !/^\d{6}$/.test(this.pincode)) { 
                this.message = 'Please enter a valid 6-digit Indian PIN code.'; 
                this.status = 'error'; 
                return; 
              }
              this.checking = true;
              fetch('{{ route('pincode.check') }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ pincode: this.pincode })
              })
              .then(r => r.json())
              .then(res => {
                this.checking = false;
                if (res.success && res.data) {
                  const d = res.data;
                  this.status = 'success';
                  this.message = `✓ Deliverable to ${d.city ? d.city + ', ' + d.state : 'your location'} (${d.pincode}) • Delivery in ${d.estimated_days} days (${d.estimated_date}) • ${d.cod_available ? 'COD Available' : 'Prepaid Only'}`;
                } else {
                  this.status = 'error';
                  this.message = res.message || 'Service unavailable in this pincode.';
                }
              })
              .catch(() => { 
                this.checking = false; 
                this.status = 'error'; 
                this.message = 'Unable to check delivery at this moment.'; 
              });
            }
          }">
          <label class="block font-bold text-stone-800 mb-1.5 flex items-center gap-1.5">
            <svg class="w-4 h-4 text-organic-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Check Delivery Availability
          </label>
          <div class="flex items-center gap-2">
            <input type="text" 
                   x-model="pincode" 
                   maxlength="6"
                   placeholder="Enter 6-digit PIN code" 
                   @keydown.enter.prevent="check()"
                   class="flex-1 bg-white border border-stone-300 rounded-xl px-3 py-2 text-xs text-stone-800 focus:outline-none focus:border-organic-500">
            <button type="button" 
                    @click="check()"
                    class="bg-stone-800 hover:bg-stone-900 text-white font-bold px-4 py-2 rounded-xl text-xs transition">
              <span x-show="!checking">Check</span>
              <span x-show="checking">...</span>
            </button>
          </div>
          <p x-show="message" x-text="message" class="mt-2 text-xs font-semibold" :class="status === 'success' ? 'text-organic-700' : 'text-rose-600'"></p>
        </div>

        {{-- Available Add-ons --}}
        @if($product->addons->isNotEmpty())
          <div class="border-t border-stone-200 pt-3">
            <p class="text-xs font-bold uppercase tracking-wider text-stone-700 mb-2 flex items-center gap-1.5">
              <svg class="w-3.5 h-3.5 text-organic-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
              Available Add-ons
            </p>
            <div class="space-y-1.5">
              @foreach($product->addons as $addon)
                <label class="flex items-center justify-between text-xs text-stone-700 bg-stone-50 hover:bg-stone-100/80 border border-stone-200 px-3.5 py-2.5 rounded-xl cursor-pointer transition select-none">
                  <div class="flex items-center gap-2.5">
                    <input type="checkbox"
                           class="pdp-addon-checkbox rounded border-stone-300 text-organic-600 focus:ring-organic-500 w-4 h-4 cursor-pointer accent-[#699E4B]"
                           data-price="{{ $addon->price }}"
                           data-name="{{ $addon->name }}"
                           data-id="{{ $addon->id }}"
                           onchange="updateProductAddonPricing()">
                    <span class="font-medium text-stone-800">{{ $addon->name }}</span>
                  </div>
                  <span class="font-bold text-organic-700 font-mono">+₹{{ number_format($addon->price, 0) }}</span>
                </label>
              @endforeach
            </div>
          </div>
        @endif

        {{-- Organic Assurance List --}}
        <div class="grid grid-cols-2 gap-3 pt-2 text-xs text-stone-600">
          <div class="flex items-center gap-2">
            <span class="w-5 h-5 rounded-full bg-organic-100 text-organic-700 flex items-center justify-center font-bold text-[10px]">✓</span>
            <span>Zero chemical preservatives</span>
          </div>
          <div class="flex items-center gap-2">
            <span class="w-5 h-5 rounded-full bg-organic-100 text-organic-700 flex items-center justify-center font-bold text-[10px]">✓</span>
            <span>Temperature-controlled courier</span>
          </div>
          <div class="flex items-center gap-2">
            <span class="w-5 h-5 rounded-full bg-organic-100 text-organic-700 flex items-center justify-center font-bold text-[10px]">✓</span>
            <span>Traceable farm origins</span>
          </div>
          <div class="flex items-center gap-2">
            <span class="w-5 h-5 rounded-full bg-organic-100 text-organic-700 flex items-center justify-center font-bold text-[10px]">✓</span>
            <span>100% money-back freshness guarantee</span>
          </div>
        </div>

      </div>

    </div>

    {{-- ═══════════════════════════════════════════════
         PRODUCT HIGHLIGHTS & ACCORDION (Dedicated Division)
    ═══════════════════════════════════════════════ --}}
    <div class="mt-16 pt-12 border-t border-stone-200 space-y-8">
      <div class="space-y-4 max-w-4xl text-xs sm:text-sm text-stone-600 leading-relaxed font-light product-rich-text">
        <div class="flex items-center gap-2.5 mb-2 not-prose">
          <span class="w-1 h-5 rounded-full bg-gradient-to-b from-organic-500 to-emerald-600 inline-block shrink-0"></span>
          <h3 class="font-bold text-stone-900 text-lg sm:text-xl tracking-wide uppercase">Product Highlights</h3>
        </div>
        {!! $product->long_description ?: $product->description ?: 'Cultivated organically following ancient Vedic farming practices without urea, DAP, or chemical weedicides. Hand-harvested at peak dawn and packed in ventilated organic craft sleeves.' !!}
        @if(!$product->long_description)
          <h4 class="font-bold text-stone-900 mt-4">Storage Guidelines:</h4>
          <ul class="list-disc pl-5 space-y-1">
            <li>Keep in a cool, dry place away from direct sunlight.</li>
            <li>For leafy greens, store in the crisper drawer of your refrigerator wrapped in a damp muslin cloth.</li>
            <li>Consume within 4–6 days of delivery for highest vitamin retention.</li>
          </ul>
        @endif
      </div>

      {{-- Delivery & Returns Accordion --}}
      <div class="border-t border-stone-200 pt-4 divide-y divide-stone-200 not-prose">
        {{-- Delivery --}}
        <div x-data="{ open: false }" class="py-4">
          <button type="button" @click="open = !open" class="flex items-center justify-between w-full text-left font-bold text-sm sm:text-base text-stone-900 hover:text-organic-600 transition">
            <span class="flex items-center gap-2">
              <svg class="w-4 h-4 text-organic-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
              Delivery Information
            </span>
            <svg class="w-4 h-4 text-stone-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
          </button>
          <div x-show="open" x-transition class="mt-3 text-xs sm:text-sm text-stone-600 leading-relaxed pl-6">
            {{ \App\Models\Setting::get('product_delivery_info') ?: \App\Models\Setting::get('organic_delivery_info', 'Freshly packed and dispatched within 24–48 hours of harvest in temperature-monitored vehicles. Free delivery on orders above ₹799.') }}
          </div>
        </div>
        {{-- Returns & Exchange --}}
        <div x-data="{ open: false }" class="py-4">
          <button type="button" @click="open = !open" class="flex items-center justify-between w-full text-left font-bold text-sm sm:text-base text-stone-900 hover:text-organic-600 transition">
            <span class="flex items-center gap-2">
              <svg class="w-4 h-4 text-organic-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
              Returns &amp; Exchange
            </span>
            <svg class="w-4 h-4 text-stone-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
          </button>
          <div x-show="open" x-transition class="mt-3 text-xs sm:text-sm text-stone-600 leading-relaxed pl-6">
            {{ \App\Models\Setting::get('product_returns_info') ?: \App\Models\Setting::get('organic_returns_info', '100% freshness guarantee. If any produce arrives damaged or unsatisfactory, report within 24 hours for instant refund or replacement.') }}
          </div>
        </div>
      </div>
    </div>

    {{-- Patron Reviews --}}
    <div class="mt-16 pt-12 border-t border-stone-200 space-y-6">
      <div class="flex items-center justify-between">
        <div>
          <span class="text-xs font-bold uppercase tracking-wider text-organic-600 block mb-1">Feedback</span>
          <h3 class="text-xl sm:text-2xl font-black text-stone-900 font-display">Patron Reviews ({{ $product->approvedReviews->count() }})</h3>
        </div>
      </div>
      <div class="space-y-4 max-w-4xl">
        @forelse($product->approvedReviews as $rev)
          <div class="p-4 bg-stone-50 rounded-2xl border border-stone-200">
            <div class="flex items-center justify-between mb-1">
              <h5 class="text-xs font-bold text-stone-900">{{ $rev->customer_name }}</h5>
              <span class="text-[10px] text-stone-400">{{ $rev->created_at->format('M d, Y') }}</span>
            </div>
            <div class="flex items-center text-citrus-gold text-xs mb-2">
              @for($r = 0; $r < $rev->rating; $r++) ★ @endfor
            </div>
            <p class="text-xs text-stone-600">{{ $rev->comment }}</p>
          </div>
        @empty
          <p class="text-xs text-stone-400">No patron reviews yet. Be the first to share your thoughts!</p>
        @endforelse
      </div>
    </div>

    {{-- Related Products --}}
    @if(isset($relatedProducts) && $relatedProducts->isNotEmpty())
      <div class="mt-16 pt-12 border-t border-stone-200">
        <h3 class="text-2xl font-black text-stone-900 font-display mb-8">Related Products</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          @foreach($relatedProducts as $rel)
            @include('storefront.organic.partials.product-card', ['product' => $rel])
          @endforeach
        </div>
      </div>
    @endif

  </div>

  {{-- Script for interactive variants and addToCart bridging --}}
  <script>
    let activeColorName = @js($defaultColor?->name ?? "Natural");
    let activeVariantId = {{ (int) ($variants->first()?->id ?? 0) }};
    let activeSize = @js($variants->first()?->size ?? "Standard");
    let activePrice = {{ (float) ($variants->first()?->price ?? $product->starting_price) }};
    const productId = {{ (int) $product->id }};
    const productName = @js($product->name);
    const productImage = @js($product->cover_image);
    const productSku = @js($variants->first()?->sku ?? $product->sku ?? "SKU-ORG");

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
      const finalPrice = activePrice + addonTotal;
      const priceEl = document.getElementById('currentDisplayPrice');
      if (priceEl) {
        priceEl.textContent = '₹' + finalPrice.toLocaleString('en-IN', { minimumFractionDigits: 2 });
      }
    }

    function selectProductColor(idx, colorName) {
      activeColorName = colorName;
      const varietyEl = document.getElementById('activeVarietyName');
      if (varietyEl) varietyEl.textContent = colorName;

      document.querySelectorAll('.variety-btn').forEach((btn, i) => {
        if (i === idx) {
          btn.classList.add('border-organic-500', 'bg-organic-50', 'text-organic-800');
          btn.classList.remove('border-stone-200', 'bg-white', 'text-stone-700');
        } else {
          btn.classList.remove('border-organic-500', 'bg-organic-50', 'text-organic-800');
          btn.classList.add('border-stone-200', 'bg-white', 'text-stone-700');
        }
      });
    }

    function selectProductVariant(varId, size, price, stock) {
      activeVariantId = varId;
      activeSize = size;
      activePrice = price;

      updateProductAddonPricing();

      document.querySelectorAll('.size-variant-btn').forEach(btn => {
        if (parseInt(btn.getAttribute('data-variant-id')) === varId) {
          btn.classList.add('border-organic-500', 'bg-organic-50', 'text-organic-900', 'shadow-xs');
          btn.classList.remove('border-stone-200', 'bg-white', 'text-stone-700');
        } else {
          btn.classList.remove('border-organic-500', 'bg-organic-50', 'text-organic-900', 'shadow-xs');
          btn.classList.add('border-stone-200', 'bg-white', 'text-stone-700');
        }
      });
    }

    function triggerDirectAddToCart() {
      if (typeof addToCart === 'function') {
        const addonTotal = getSelectedAddonsTotal();
        const finalPrice = activePrice + addonTotal;
        const addonNames = getSelectedAddonNames();
        const extraDesc  = addonNames.length ? ' (Add-ons: ' + addonNames.join(', ') + ')' : '';
        addToCart(productId, productName + extraDesc, activeColorName, activeSize, finalPrice, productImage, activeVariantId, productSku);
      }
    }
  </script>

</x-storefront-layout>

