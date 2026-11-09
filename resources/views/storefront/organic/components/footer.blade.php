{{--
    ======================================================================
    THEME: VERDIA (ORGANIC) — FOOTER COMPONENT
    ======================================================================
    File: resources/views/storefront/organic/components/footer.blade.php
    Features:
      - Farm-fresh newsletter card with leaf background accents
      - 4-column directory: Categories, Farm Trust, Customer Care, Verified Certifications
      - Certified organic guarantee seals & secure payment icons
      - Platform attribution & legal compliance notices
--}}
<footer class="bg-white border-t border-stone-200 mt-16 pt-12 text-stone-700 text-xs">

  {{-- 1. Farm-Fresh Newsletter Card --}}
  <div class="max-w-7xl mx-auto px-4 sm:px-6 mb-12">
    <div class="rounded-3xl bg-gradient-to-r from-organic-900 via-organic-800 to-organic-900 p-8 sm:p-12 text-white relative overflow-hidden shadow-xl">
      {{-- Decorative leaf background accents --}}
      <div class="absolute -right-10 -bottom-10 w-60 h-60 opacity-10 pointer-events-none text-white">
        <svg viewBox="0 0 24 24" class="w-full h-full fill-current"><use xlink:href="#organic-leaf"></use></svg>
      </div>

      <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
        <div class="lg:col-span-7 space-y-2">
          <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold tracking-wider uppercase bg-organic-500/30 text-organic-200 border border-organic-400/30">
            {{ \App\Models\Setting::get('organic_news_badge', '🌱 Join the Organic Family') }}
          </span>
          <h3 class="text-2xl sm:text-3xl font-extrabold font-display tracking-tight text-white">
            {{ \App\Models\Setting::get('organic_news_title', 'Get ₹200 Off Your First Fresh Harvest') }}
          </h3>
          <p class="text-xs sm:text-sm text-organic-100/90 font-light max-w-xl">
            {{ \App\Models\Setting::get('organic_news_subtitle', 'Subscribe to our weekly seasonal harvest alerts, farm updates, and exclusive patron discounts. No spam, just pure organic goodness.') }}
          </p>
        </div>

        <div class="lg:col-span-5" x-data="{ email: '', status: '', message: '', loading: false }">
          <form @submit.prevent="
            loading = true;
            fetch('{{ route('newsletter.subscribe') }}', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').content },
              body: JSON.stringify({ email })
            })
            .then(r => r.json())
            .then(data => {
              loading = false;
              if (data.success) {
                status = 'success';
                message = data.message;
                email = '';
              } else {
                status = 'error';
                message = data.message || 'Subscription failed. Please try again.';
              }
            })
            .catch(() => {
              loading = false;
              status = 'error';
              message = 'An unexpected error occurred.';
            });
          " class="flex flex-col gap-2">
            <div class="flex items-center rounded-full bg-white/10 backdrop-blur-md p-1.5 border border-white/20 focus-within:border-organic-400 transition">
              <input type="email" 
                     x-model="email" 
                     placeholder="Enter your email address..." 
                     required 
                     class="w-full bg-transparent px-4 py-2.5 text-xs sm:text-sm text-white placeholder-organic-200/70 focus:outline-none">
              <button type="submit" 
                      :disabled="loading" 
                      class="shrink-0 bg-organic-500 hover:bg-organic-400 text-white font-bold text-xs px-5 py-2.5 rounded-full transition shadow-md flex items-center gap-1">
                <span x-show="!loading">Subscribe</span>
                <span x-show="loading" class="animate-spin text-xs">⟳</span>
              </button>
            </div>
            <p x-show="message" x-text="message" class="text-xs px-2" :class="status === 'success' ? 'text-organic-300' : 'text-rose-300'"></p>
          </form>
        </div>
      </div>
    </div>
  </div>

  {{-- 2. Four Main Directory Columns --}}
  <div class="max-w-7xl mx-auto px-4 sm:px-6 py-10 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">

    {{-- Col 1: About Organic --}}
    <div class="space-y-4">
      <div class="flex items-center gap-2">
        @if(!empty($siteLogo))
          <img src="{{ asset('images/' . ltrim(str_replace('storage/', '', $siteLogo), '/')) }}" alt="{{ $storeName }}" class="h-8 md:h-10 w-auto max-w-[180px] object-contain">
        @else
          <div class="w-8 h-8 rounded-lg bg-organic-500 text-white flex items-center justify-center">
            <svg class="w-5 h-5" viewBox="0 0 24 24"><use xlink:href="#organic-leaf"></use></svg>
          </div>
          <span class="text-xl font-black text-organic-900 font-display">{{ $storeName }}<span class="text-organic-500">.</span></span>
        @endif
      </div>
      <p class="text-xs text-stone-500 leading-relaxed font-light">
        {{ \App\Models\Setting::get('footer_bio', 'Nexon Organic is your trusted farm-to-door boutique. We partner directly with verified Indian organic farmers to deliver chemical-free fruits, vegetables, and pantry staples within 24 hours of harvest.') }}
      </p>
      @php
        $fBadge1 = \App\Models\Setting::get('footer_badge1', 'FSSAI Certified');
        $fBadge2 = \App\Models\Setting::get('footer_badge2', '100% Non-GMO');
      @endphp
      @if(!empty($fBadge1) || !empty($fBadge2))
        <div class="flex items-center gap-2 text-stone-400 pt-2 flex-wrap">
          @if(!empty($fBadge1))
            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-organic-700 bg-organic-50 px-2.5 py-1 rounded-md border border-organic-200">
              ✓ {{ $fBadge1 }}
            </span>
          @endif
          @if(!empty($fBadge2))
            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-organic-700 bg-organic-50 px-2.5 py-1 rounded-md border border-organic-200">
              ✓ {{ $fBadge2 }}
            </span>
          @endif
        </div>
      @endif
    </div>

    {{-- Col 2: Shop & Categories --}}
    <div>
      <h4 class="text-xs font-black uppercase tracking-wider text-stone-900 mb-4 font-display">Fresh Harvests</h4>
      <ul class="space-y-2 text-xs text-stone-600">
        <li><a href="{{ route('featured.products') }}" class="hover:text-organic-600 transition">Featured Organic Products</a></li>
        <li><a href="{{ route('new.arrivals') }}" class="hover:text-organic-600 transition">New Season Arrivals</a></li>
        <li><a href="{{ route('sale.products') }}" class="hover:text-citrus transition">Special Farm Discounts</a></li>
        @foreach($categories->take(5) as $cat)
          <li><a href="{{ route('category.show', $cat->slug) }}" class="hover:text-organic-600 transition">{{ $cat->name }}</a></li>
        @endforeach
      </ul>
    </div>

    {{-- Col 3: Customer Care --}}
    <div>
      <h4 class="text-xs font-black uppercase tracking-wider text-stone-900 mb-4 font-display">Customer Care</h4>
      <ul class="space-y-2 text-xs text-stone-600">
        <li><a href="{{ route('contact') }}" class="hover:text-organic-600 transition">Contact Farm Support</a></li>
        <li><a href="{{ route('faq') }}" class="hover:text-organic-600 transition">Frequently Asked Questions</a></li>
        <li><a href="{{ route('shippingPolicy') }}" class="hover:text-organic-600 transition">Delivery &amp; Shipping Rates</a></li>
        <li><a href="{{ route('returnPolicy') }}" class="hover:text-organic-600 transition">Freshness Guarantee &amp; Returns</a></li>
        <li><a href="{{ route('terms') }}" class="hover:text-organic-600 transition">Terms of Service</a></li>
        <li><a href="{{ route('privacy') }}" class="hover:text-organic-600 transition">Privacy Policy</a></li>
        @auth
          <li><a href="{{ route('account') }}" class="hover:text-organic-600 transition">Track Active Orders</a></li>
        @endauth
      </ul>
    </div>

    {{-- Col 4: Store & Dispatch Hub --}}
    <div class="space-y-3">
      <h4 class="text-xs font-black uppercase tracking-wider text-stone-900 mb-4 font-display">Farm Dispatch Hub</h4>
      <p class="text-xs text-stone-600 leading-relaxed">
        <strong class="text-stone-800">Address:</strong><br>
        Nexon Organic Dispatch Center, Phase 2 Agro Hub, Mumbai, Maharashtra 400050
      </p>
      @php
        $footerCarePhone = $organicCustomerCare ?? \App\Models\Setting::get('organic_customer_care', \App\Models\Setting::supportPhone());
        $footerTelHref = preg_replace('/[^0-9+]/', '', $footerCarePhone);
        $footerCareEmail = $organicSupportEmail ?? \App\Models\Setting::get('organic_support_email', \App\Models\Setting::supportEmail());
      @endphp
      <p class="text-xs text-stone-600">
        <strong class="text-stone-800">Helpline:</strong> <a href="tel:{{ $footerTelHref }}" class="hover:text-organic-600 hover:underline">{{ $footerCarePhone }}</a> (9 AM – 8 PM)
      </p>
      <p class="text-xs text-stone-600">
        <strong class="text-stone-800">Email:</strong> <a href="mailto:{{ $footerCareEmail }}" class="hover:text-organic-600 hover:underline">{{ $footerCareEmail }}</a>
      </p>
      <div class="pt-2">
        <span class="text-[10px] uppercase font-bold text-stone-400 block mb-1">Supported Payments:</span>
        <div class="flex items-center gap-2 text-stone-500 text-lg">
          <i class="fa-brands fa-cc-visa" title="Visa"></i>
          <i class="fa-brands fa-cc-mastercard" title="Mastercard"></i>
          <i class="fa-solid fa-money-bill-wave" title="Cash on Delivery"></i>
          <span class="text-[10px] font-bold bg-stone-100 text-stone-700 px-1.5 py-0.5 rounded border border-stone-200">UPI Instant</span>
        </div>
      </div>
    </div>

  </div>

  {{-- 3. Bottom Bar & Attribution --}}
  <div class="border-t border-stone-200 bg-stone-50 py-5">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 flex flex-col sm:flex-row items-center justify-between gap-3 text-center sm:text-left text-[11px] text-stone-500">
      <div>
        &copy; {{ date('Y') }} {{ config('app.name', 'Nex-on') }}. All rights reserved.
      </div>
      <div>
        <span class="platform-branding-attribution inline-block" data-nexon-platform-branding="verified">{{ $platformBranding ?? '• Powered by Nex-on' }}</span>
      </div>
    </div>
  </div>

</footer>

