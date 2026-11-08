{{--
    ======================================================================
    THEME: LUMIÈRE — FOOTER COMPONENT
    ======================================================================
    File: resources/views/storefront/lumiere/components/footer.blade.php
    Features:
      - Vibrant silk pink (#DB2777) luxury backdrop
      - Brand bio & custom trust badges (footer_badge1, footer_badge2)
      - Experience directory: Story, Concierge, FAQs, Size Guide, Hygiene Policy
      - AJAX newsletter subscription endpoint with real-time feedback
--}}
@php
    $siteLogo = $siteLogo ?? \App\Models\Setting::get('site_logo');
    $storeName = $storeName ?? \App\Models\Setting::get('store_name', 'Lumière');
@endphp
<footer class="bg-pink-600 text-white/80 py-16 px-6 lg:px-10">
    <div class="max-w-7xl mx-auto grid sm:grid-cols-2 lg:grid-cols-4 gap-10">
        <div>
            @if(!empty($siteLogo))
                <div class="mb-4">
                    <img src="{{ asset('storage/' . $siteLogo) }}" alt="{{ $storeName }}" class="h-9 w-auto max-w-[180px] object-contain bg-white/20 backdrop-blur-xs p-1.5 rounded-xl">
                </div>
            @else
                <p class="font-display italic text-2xl font-black text-white mb-4 tracking-tighter">Lumière.</p>
            @endif
            <p class="text-sm leading-relaxed">
                {{ \App\Models\Setting::get('footer_bio', 'Redefining everyday elegance through sustainable silk lingerie, made in India.') }}
            </p>
            @php
              $fBadge1 = \App\Models\Setting::get('footer_badge1');
              $fBadge2 = \App\Models\Setting::get('footer_badge2');
            @endphp
            @if(!empty($fBadge1) || !empty($fBadge2))
              <div class="flex items-center gap-2 pt-3 flex-wrap">
                @if(!empty($fBadge1))
                  <span class="inline-flex items-center gap-1 text-[11px] font-bold text-white bg-white/15 px-2.5 py-1 rounded-md border border-white/20">
                    ✓ {{ $fBadge1 }}
                  </span>
                @endif
                @if(!empty($fBadge2))
                  <span class="inline-flex items-center gap-1 text-[11px] font-bold text-white bg-white/15 px-2.5 py-1 rounded-md border border-white/20">
                    ✓ {{ $fBadge2 }}
                  </span>
                @endif
              </div>
            @endif
        </div>
        <div>
            <p class="text-pink-200 uppercase text-[10px] font-black tracking-widest mb-4">Shop</p>
            <ul class="space-y-3 text-sm">
                <li><a href="{{ route('featured.products') }}" class="hover:text-white transition-colors">Featured Edit</a></li>
                <li><a href="{{ route('new.arrivals') }}" class="hover:text-white transition-colors">New Arrivals</a></li>
                <li><a href="{{ route('collections.sale') }}" class="hover:text-white transition-colors text-pink-300 font-bold">Sale &amp; Offers ✨</a></li>
                @foreach (collect($categories ?? [])->take(5) as $cat)
                    <li><a href="{{ route('category.show', $cat->slug) }}" class="hover:text-white transition-colors">{{ $cat->name }}</a></li>
                @endforeach
            </ul>
        </div>
        <div>
            <p class="text-pink-200 uppercase text-[10px] font-black tracking-widest mb-4">Experience</p>
            <ul class="space-y-3 text-sm">
                <li><a href="{{ route('about') }}" class="hover:text-white transition-colors">Our Story</a></li>
                <li><a href="{{ route('contact') }}" class="hover:text-white transition-colors">Contact Us</a></li>
                <li><a href="{{ route('faq') }}" class="hover:text-white transition-colors">Help &amp; FAQs</a></li>
                <li><a href="{{ route('sizeGuide') }}" class="hover:text-white transition-colors">Size Guide</a></li>
                <li><a href="{{ route('shippingPolicy') }}" class="hover:text-white transition-colors">Shipping Info</a></li>
                <li><a href="{{ route('returnPolicy') }}" class="hover:text-white transition-colors">Hygiene &amp; Returns</a></li>
                <li><a href="{{ route('compare') }}" class="hover:text-white transition-colors">Compare Products</a></li>
            </ul>
        </div>
        <div>
            <p class="text-pink-200 uppercase text-[10px] font-black tracking-widest mb-4">Newsletter</p>
            <form onsubmit="event.preventDefault(); submitLumiereNewsletter(this);" class="space-y-2">
                @csrf
                <div class="relative">
                    <input type="email" name="email" required placeholder="Your Email" class="w-full bg-white/20 border border-white/30 rounded-xl py-3 px-4 text-xs text-white placeholder:text-pink-100 outline-none pr-14">
                    <button type="submit" class="absolute right-4 top-1/2 -translate-y-1/2 text-white font-bold text-[10px] uppercase">Join</button>
                </div>
                <p class="lumiere-nl-msg text-xs text-pink-100 hidden font-semibold"></p>
            </form>
            <script>
            async function submitLumiereNewsletter(form) {
                const input = form.querySelector('input[name="email"]');
                const msg = form.querySelector('.lumiere-nl-msg');
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
                    msg.textContent = 'Error subscribing.';
                    msg.classList.remove('hidden');
                } finally {
                    btn.disabled = false;
                }
            }
            </script>
        </div>
    </div>
    <p class="text-center text-xs text-white/40 mt-12">© {{ date('Y') }} {{ config('app.name', 'Nex-on') }}. All rights reserved. <span class="platform-branding-attribution inline-block" data-nexon-platform-branding="verified">{{ $platformBranding ?? '• Powered by Nex-on' }}</span></p>
</footer>
