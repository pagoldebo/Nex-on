{{--
    ======================================================================
    THEME: BUTTERCUP — FOOTER COMPONENT
    ======================================================================
    File: resources/views/storefront/buttercup/components/footer.blade.php
    Features:
      - Warm rose background with organic cotton cream typography
      - Brand bio, customer care links, and size guides
      - AJAX newsletter subscription endpoint
      - Safe-area bottom padding for mobile sticky tab-bar
--}}
@php
    $siteLogo = $siteLogo ?? \App\Models\Setting::get('site_logo');
    $storeName = $storeName ?? \App\Models\Setting::get('store_name', 'Buttercup');
@endphp
<footer class="bg-[#B97680] text-[#FFFAF5]/80 py-16 px-6 lg:px-10">
    <div class="max-w-7xl mx-auto grid sm:grid-cols-2 lg:grid-cols-4 gap-10">
        <div>
            @if(!empty($siteLogo))
                <div class="mb-4">
                    <img src="{{ asset('images/' . ltrim(str_replace('storage/', '', $siteLogo), '/')) }}" alt="{{ $storeName }}" class="h-9 w-auto max-w-[180px] object-contain bg-white/20 backdrop-blur-xs p-1.5 rounded-xl">
                </div>
            @else
                <p class="font-display text-2xl text-[#FFFAF5] mb-4">Buttercup 🌼</p>
            @endif
            <p class="text-sm leading-relaxed">
                {{ \App\Models\Setting::get('footer_bio', 'Soft, breathable, everyday lingerie made for real comfort.') }}
            </p>
            @php
              $fBadge1 = \App\Models\Setting::get('footer_badge1');
              $fBadge2 = \App\Models\Setting::get('footer_badge2');
            @endphp
            @if(!empty($fBadge1) || !empty($fBadge2))
              <div class="flex items-center gap-2 pt-3 flex-wrap">
                @if(!empty($fBadge1))
                  <span class="inline-flex items-center gap-1 text-[11px] font-bold text-[#FFFAF5] bg-white/15 px-2.5 py-1 rounded-md border border-white/20">
                    ✓ {{ $fBadge1 }}
                  </span>
                @endif
                @if(!empty($fBadge2))
                  <span class="inline-flex items-center gap-1 text-[11px] font-bold text-[#FFFAF5] bg-white/15 px-2.5 py-1 rounded-md border border-white/20">
                    ✓ {{ $fBadge2 }}
                  </span>
                @endif
              </div>
            @endif
        </div>
        <div>
            <p class="text-[#FFFAF5] uppercase text-xs font-bold tracking-widest mb-4">Shop</p>
            <ul class="space-y-2 text-sm">
                <li><a href="{{ route('featured.products') }}" class="hover:text-white transition-colors">Featured Pieces</a></li>
                <li><a href="{{ route('new.arrivals') }}" class="hover:text-white transition-colors">New Arrivals</a></li>
                <li><a href="{{ route('collections.sale') }}" class="hover:text-white transition-colors text-[#F3D2CD] font-medium">Sale &amp; Offers 🌸</a></li>
                @foreach (collect($categories ?? [])->take(5) as $cat)
                    <li><a href="{{ route('category.show', $cat->slug) }}" class="hover:text-white transition-colors">{{ $cat->name }}</a></li>
                @endforeach
            </ul>
        </div>
        <div>
            <p class="text-[#FFFAF5] uppercase text-xs font-bold tracking-widest mb-4">Help</p>
            <ul class="space-y-2 text-sm">
                <li><a href="{{ route('about') }}" class="hover:text-white transition-colors">About Us</a></li>
                <li><a href="{{ route('contact') }}" class="hover:text-white transition-colors">Contact</a></li>
                <li><a href="{{ route('faq') }}" class="hover:text-white transition-colors">FAQs</a></li>
                <li><a href="{{ route('sizeGuide') }}" class="hover:text-white transition-colors">Size Guide</a></li>
                <li><a href="{{ route('shippingPolicy') }}" class="hover:text-white transition-colors">Shipping &amp; Delivery</a></li>
                <li><a href="{{ route('returnPolicy') }}" class="hover:text-white transition-colors">Returns &amp; Hygiene</a></li>
                <li><a href="{{ route('compare') }}" class="hover:text-white transition-colors">Compare</a></li>
            </ul>
        </div>
        <div>
            <p class="text-[#FFFAF5] uppercase text-xs font-bold tracking-widest mb-4">Newsletter</p>
            <form onsubmit="event.preventDefault(); submitButtercupNewsletter(this);" class="space-y-2">
                @csrf
                <div class="flex bg-white/10 rounded-full px-4 py-2">
                    <input type="email" name="email" required placeholder="Your email" class="bg-transparent text-sm flex-1 placeholder-[#FFFAF5]/50 focus:outline-none text-[#FFFAF5]">
                    <button type="submit" class="text-[#FFFAF5] text-sm font-bold">→</button>
                </div>
                <p class="buttercup-nl-msg text-xs text-[#FFFAF5] hidden font-semibold"></p>
            </form>
            <script>
            async function submitButtercupNewsletter(form) {
                const input = form.querySelector('input[name="email"]');
                const msg = form.querySelector('.buttercup-nl-msg');
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
                    msg.textContent = data.message || 'Subscribed!';
                    msg.classList.remove('hidden');
                    input.value = '';
                } catch {
                    msg.textContent = 'Error subscribing. Please try again.';
                    msg.classList.remove('hidden');
                } finally {
                    btn.disabled = false;
                }
            }
            </script>
        </div>
    </div>
    <p class="text-center text-xs text-[#FFFAF5]/40 mt-12">© {{ date('Y') }} {{ config('app.name', 'Nex-on') }}. All rights reserved. <span class="platform-branding-attribution inline-block" data-nexon-platform-branding="verified">{{ $platformBranding ?? '• Powered by Nex-on' }}</span></p>
</footer>
