{{--
    ======================================================================
    THEME: VYRA — FOOTER COMPONENT
    ======================================================================
    File: resources/views/storefront/vyra/components/footer.blade.php
    Features:
      - Brand bio & custom trust badges (footer_badge1, footer_badge2)
      - Category directory & customer care navigation
      - AJAX newsletter subscription endpoint (newsletter.subscribe)
      - Platform attribution & copyright notices
--}}
@php
    $siteLogo = $siteLogo ?? \App\Models\Setting::get('site_logo');
    $storeName = $storeName ?? \App\Models\Setting::get('store_name', 'VYRA');
@endphp
<footer class="bg-[#1A1A1D] text-[#F2F0EC]/60 py-16 px-6 lg:px-10 border-t border-[#FF3D5A]/20">
    <div class="max-w-7xl mx-auto grid sm:grid-cols-2 lg:grid-cols-4 gap-10">
        <div>
            @if(!empty($siteLogo))
                <div class="mb-4">
                    <img src="{{ asset('storage/' . $siteLogo) }}" alt="{{ $storeName }}" class="h-9 w-auto max-w-[180px] object-contain bg-white/10 backdrop-blur-xs p-1.5 rounded-xl">
                </div>
            @else
                <p class="font-display text-xl text-[#F2F0EC] mb-4">VY<span class="text-[#FF3D5A]">RA</span></p>
            @endif
            <p class="text-sm leading-relaxed">
                {{ \App\Models\Setting::get('footer_bio', 'Bold lingerie for a generation that wears its power.') }}
            </p>
            @php
              $fBadge1 = \App\Models\Setting::get('footer_badge1');
              $fBadge2 = \App\Models\Setting::get('footer_badge2');
            @endphp
            @if(!empty($fBadge1) || !empty($fBadge2))
              <div class="flex items-center gap-2 pt-3 flex-wrap">
                @if(!empty($fBadge1))
                  <span class="inline-flex items-center gap-1 text-[11px] font-bold text-[#D4FF3D] bg-white/5 px-2.5 py-1 rounded-md border border-[#D4FF3D]/30">
                    ✓ {{ $fBadge1 }}
                  </span>
                @endif
                @if(!empty($fBadge2))
                  <span class="inline-flex items-center gap-1 text-[11px] font-bold text-[#D4FF3D] bg-white/5 px-2.5 py-1 rounded-md border border-[#D4FF3D]/30">
                    ✓ {{ $fBadge2 }}
                  </span>
                @endif
              </div>
            @endif
        </div>
        <div>
            <p class="text-[#FF3D5A] uppercase text-xs font-bold tracking-widest mb-4">Shop</p>
            <ul class="space-y-2 text-sm">
                <li><a href="{{ route('featured.products') }}" class="hover:text-[#D4FF3D] transition-colors">Featured Drops</a></li>
                <li><a href="{{ route('new.arrivals') }}" class="hover:text-[#D4FF3D] transition-colors">New Releases</a></li>
                <li><a href="{{ route('collections.sale') }}" class="hover:text-[#D4FF3D] transition-colors text-[#FF3D5A] font-bold">Sale &amp; Offers ⚡</a></li>
                @foreach (collect($categories ?? [])->take(5) as $cat)
                    <li><a href="{{ route('category.show', $cat->slug) }}" class="hover:text-[#D4FF3D] transition-colors">{{ $cat->name }}</a></li>
                @endforeach
            </ul>
        </div>
        <div>
            <p class="text-[#FF3D5A] uppercase text-xs font-bold tracking-widest mb-4">Help</p>
            <ul class="space-y-2 text-sm">
                <li><a href="{{ route('about') }}" class="hover:text-[#D4FF3D] transition-colors">About Us</a></li>
                <li><a href="{{ route('contact') }}" class="hover:text-[#D4FF3D] transition-colors">Contact</a></li>
                <li><a href="{{ route('faq') }}" class="hover:text-[#D4FF3D] transition-colors">FAQs</a></li>
                <li><a href="{{ route('sizeGuide') }}" class="hover:text-[#D4FF3D] transition-colors">Size Guide</a></li>
                <li><a href="{{ route('shippingPolicy') }}" class="hover:text-[#D4FF3D] transition-colors">Shipping</a></li>
                <li><a href="{{ route('returnPolicy') }}" class="hover:text-[#D4FF3D] transition-colors">Returns</a></li>
                <li><a href="{{ route('compare') }}" class="hover:text-[#D4FF3D] transition-colors">Compare</a></li>
            </ul>
        </div>
        <div>
            <p class="text-[#FF3D5A] uppercase text-xs font-bold tracking-widest mb-4">Newsletter</p>
            <form onsubmit="event.preventDefault(); submitVyraNewsletter(this);" class="space-y-2">
                @csrf
                <div class="flex border-b border-[#F2F0EC]/20 pb-2">
                    <input type="email" name="email" required placeholder="ENTER YOUR EMAIL" class="bg-transparent text-xs uppercase flex-1 placeholder-[#F2F0EC]/30 focus:outline-none text-[#F2F0EC]">
                    <button type="submit" class="text-[#D4FF3D] text-sm font-bold">→</button>
                </div>
                <p class="vyra-nl-msg text-xs text-[#D4FF3D] hidden font-bold uppercase"></p>
            </form>
            <script>
            async function submitVyraNewsletter(form) {
                const input = form.querySelector('input[name="email"]');
                const msg = form.querySelector('.vyra-nl-msg');
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
                    msg.textContent = data.message || 'SUBSCRIBED.';
                    msg.classList.remove('hidden');
                    input.value = '';
                } catch {
                    msg.textContent = 'ERROR SUBSCRIBING.';
                    msg.classList.remove('hidden');
                } finally {
                    btn.disabled = false;
                }
            }
            </script>
        </div>
    </div>
    <p class="text-center text-xs text-[#F2F0EC]/30 mt-12">© {{ date('Y') }} {{ config('app.name', 'Nex-on') }}. All rights reserved. <span class="platform-branding-attribution inline-block" data-nexon-platform-branding="verified">{{ $platformBranding ?? '• Powered by Nex-on' }}</span></p>
</footer>
