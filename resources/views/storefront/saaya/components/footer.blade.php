{{-- resources/views/storefront/saaya/components/footer.blade.php
     Dark maroon background, ivory/gold text, 4-column grid. Adds bottom padding on mobile for the tab bar. --}}

{{-- Petal dotted divider --}}
<div class="petal-divider mx-6 lg:mx-10 mb-0 opacity-40"></div>

<footer class="bg-maroon-dark text-ivory/70 pt-16 pb-32 lg:pb-16 px-6 lg:px-10">
  <div class="max-w-7xl mx-auto grid sm:grid-cols-2 lg:grid-cols-4 gap-10">

    {{-- Brand --}}
    @php
        $siteLogo = $siteLogo ?? \App\Models\Setting::get('site_logo');
        $storeName = $storeName ?? \App\Models\Setting::get('store_name', 'Saaya');
    @endphp
    <div>
      @if(!empty($siteLogo))
        <div class="mb-4">
          <img src="{{ asset('storage/' . $siteLogo) }}" alt="{{ $storeName }}" class="h-9 w-auto max-w-[180px] object-contain bg-white/10 backdrop-blur-xs p-1.5 rounded-xl">
        </div>
      @else
        <p class="font-display text-2xl text-ivory">Sa<span class="text-gold">a</span>ya</p>
      @endif
      <p class="text-sm mt-4 leading-relaxed">
        {{ \App\Models\Setting::get('footer_bio', 'Bridal & premium lingerie, hand-finished in India for every celebrated moment.') }}
      </p>
      @php
        $fBadge1 = \App\Models\Setting::get('footer_badge1');
        $fBadge2 = \App\Models\Setting::get('footer_badge2');
      @endphp
      @if(!empty($fBadge1) || !empty($fBadge2))
        <div class="flex items-center gap-2 pt-3 flex-wrap">
          @if(!empty($fBadge1))
            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-gold bg-gold/10 px-2.5 py-1 rounded-md border border-gold/30">
              ✓ {{ $fBadge1 }}
            </span>
          @endif
          @if(!empty($fBadge2))
            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-gold bg-gold/10 px-2.5 py-1 rounded-md border border-gold/30">
              ✓ {{ $fBadge2 }}
            </span>
          @endif
        </div>
      @endif
      <div class="flex gap-4 mt-6">
        <a href="#" aria-label="Instagram" class="text-ivory/40 hover:text-gold transition-colors">
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
        </a>
        <a href="#" aria-label="Pinterest" class="text-ivory/40 hover:text-gold transition-colors">
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C5.373 0 0 5.373 0 12c0 5.084 3.163 9.426 7.627 11.174-.105-.949-.2-2.405.042-3.441.218-.937 1.407-5.965 1.407-5.965s-.359-.719-.359-1.782c0-1.668.967-2.914 2.171-2.914 1.023 0 1.518.769 1.518 1.69 0 1.029-.655 2.568-.994 3.995-.283 1.194.599 2.169 1.777 2.169 2.133 0 3.772-2.249 3.772-5.495 0-2.873-2.064-4.882-5.012-4.882-3.414 0-5.418 2.561-5.418 5.207 0 1.031.397 2.138.893 2.738a.36.36 0 01.083.345l-.333 1.36c-.053.22-.174.267-.402.161-1.499-.698-2.436-2.889-2.436-4.649 0-3.785 2.75-7.262 7.929-7.262 4.163 0 7.398 2.967 7.398 6.931 0 4.136-2.607 7.464-6.227 7.464-1.216 0-2.359-.632-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0z"/></svg>
        </a>
      </div>
    </div>

    {{-- Shop --}}
    <div>
      <p class="text-gold uppercase text-xs tracking-widest font-body mb-4">Shop</p>
      <ul class="space-y-2 text-sm">
        <li><a href="{{ route('featured.products') }}" class="hover:text-gold transition-colors">Featured Pieces</a></li>
        <li><a href="{{ route('new.arrivals') }}" class="hover:text-gold transition-colors">New Arrivals</a></li>
        <li><a href="{{ route('collections.sale') }}" class="hover:text-gold transition-colors text-gold">Sale &amp; Offers</a></li>
        @foreach ($categories->take(5) as $cat)
          <li><a href="{{ route('category.show', $cat->slug) }}" class="hover:text-gold transition-colors">{{ $cat->name }}</a></li>
        @endforeach
      </ul>
    </div>

    {{-- Company --}}
    <div>
      <p class="text-gold uppercase text-xs tracking-widest font-body mb-4">Company</p>
      <ul class="space-y-2 text-sm">
        <li><a href="{{ route('about') }}" class="hover:text-gold transition-colors">About Saaya</a></li>
        <li><a href="{{ route('contact') }}" class="hover:text-gold transition-colors">Contact Concierge</a></li>
        <li><a href="{{ route('faq') }}" class="hover:text-gold transition-colors">Client FAQs</a></li>
        <li><a href="{{ route('sizeGuide') }}" class="hover:text-gold transition-colors">Size Guide &amp; Matrix</a></li>
        <li><a href="{{ route('shippingPolicy') }}" class="hover:text-gold transition-colors">Discreet Shipping</a></li>
        <li><a href="{{ route('returnPolicy') }}" class="hover:text-gold transition-colors">Returns &amp; Hygiene</a></li>
      </ul>
    </div>

    {{-- Newsletter --}}
    <div>
      <p class="text-gold uppercase text-xs tracking-widest font-body mb-4">Newsletter</p>
      <p class="text-sm leading-relaxed mb-4">Invitations to private edits &amp; new arrivals — nothing more.</p>
      <form onsubmit="event.preventDefault(); submitNewsletter(this);" class="space-y-2">
        @csrf
        <div class="flex border-b border-gold/40 pb-2">
          <input type="email" name="email" required placeholder="Your email address" aria-label="Email for newsletter"
                 class="bg-transparent text-sm flex-1 placeholder-ivory/40 focus:outline-none focus:placeholder-ivory/60 transition-colors font-body text-ivory">
          <button type="submit" class="text-gold text-base px-2 hover:scale-110 transition-transform" aria-label="Subscribe">→</button>
        </div>
        <p class="newsletter-msg text-[11px] font-body text-gold hidden"></p>
      </form>
      <script>
      async function submitNewsletter(form) {
        const input = form.querySelector('input[name="email"]');
        const msg = form.querySelector('.newsletter-msg');
        const btn = form.querySelector('button');
        if (!input || !input.value) return;
        btn.disabled = true;
        try {
          const res = await fetch('{{ route('newsletter.subscribe') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ email: input.value })
          });
          const data = await res.json();
          msg.textContent = data.message || 'Subscribed successfully.';
          msg.classList.remove('hidden');
          input.value = '';
        } catch {
          msg.textContent = 'Subscription error. Please try again.';
          msg.classList.remove('hidden');
        } finally {
          btn.disabled = false;
        }
      }
      </script>
    </div>
  </div>

  <div class="petal-divider max-w-7xl mx-auto mt-12 mb-8 opacity-20"></div>

  <p class="text-center text-xs text-ivory/40 font-body">© {{ date('Y') }} {{ config('app.name', 'Nex-on') }}. All rights reserved. &nbsp;·&nbsp; Handcrafted in India. &nbsp;<span class="platform-branding-attribution inline-block" data-nexon-platform-branding="verified">{{ $platformBranding ?? '• Powered by Nex-on' }}</span></p>
</footer>
