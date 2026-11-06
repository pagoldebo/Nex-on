<x-storefront-layout :theme="$theme" :title="$page->meta_title ?: $page->title" :metaDesc="$page->meta_description">

    <div class="space-y-16 py-8">

        @foreach($page->content_blocks ?? [] as $block)

            {{-- 1. Hero Banner Block --}}
            @if(($block['type'] ?? '') === 'hero')
                <section class="max-w-7xl mx-auto px-6 lg:px-10">
                    <div class="relative overflow-hidden rounded-[32px] bg-slate-900 text-white p-8 md:p-16 lg:p-20 shadow-2xl flex flex-col justify-center items-center text-center">
                        <div class="relative z-10 max-w-3xl space-y-4">
                            <span class="px-3 py-1 rounded-full text-[10px] font-bold tracking-widest uppercase bg-white/10 text-white/90 border border-white/20">
                                Curated Edit
                            </span>
                            <h1 class="text-3xl md:text-5xl lg:text-6xl font-black font-display tracking-tight leading-tight">
                                {{ $block['headline'] ?? '' }}
                            </h1>
                            <p class="text-sm md:text-base text-white/80 max-w-2xl mx-auto font-body leading-relaxed">
                                {{ $block['subheadline'] ?? '' }}
                            </p>
                            @if(!empty($block['cta_text']))
                                <div class="pt-4">
                                    <a href="{{ $block['cta_link'] ?? '#' }}"
                                       class="inline-flex items-center gap-2 px-8 py-3.5 rounded-full bg-white text-slate-950 font-bold hover:bg-slate-100 transition shadow-lg text-xs tracking-wider uppercase">
                                        <span>{{ $block['cta_text'] }}</span>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </section>

            {{-- 2. Featured Products Grid Block --}}
            @elseif(($block['type'] ?? '') === 'featured_grid')
                @php
                    $gridProducts = \App\Models\Product::with(['category', 'colors.images', 'variants'])
                        ->where('is_active', true)
                        ->latest()
                        ->take($block['limit'] ?? 4)
                        ->get();
                @endphp
                <section class="max-w-7xl mx-auto px-6 lg:px-10 space-y-6">
                    <div class="text-center max-w-xl mx-auto space-y-1">
                        <h2 class="text-2xl md:text-3xl font-black font-display text-slate-900">{{ $block['title'] ?? 'Curated Collection' }}</h2>
                        <p class="text-xs text-slate-500">{{ $block['subtitle'] ?? '' }}</p>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
                        @foreach($gridProducts as $prod)
                            <div class="group bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-xs hover:shadow-lg transition">
                                <a href="{{ route('product.show', $prod->slug) }}" class="block aspect-square bg-slate-100 overflow-hidden relative">
                                    @if($prod->cover_image)
                                        <img src="{{ $prod->cover_image }}" alt="{{ $prod->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-slate-300 text-3xl font-display">✨</div>
                                    @endif
                                </a>
                                <div class="p-4 space-y-1">
                                    <div class="text-[10px] uppercase font-bold text-slate-400 font-mono">{{ $prod->category->name ?? 'Collection' }}</div>
                                    <a href="{{ route('product.show', $prod->slug) }}" class="font-bold text-xs text-slate-900 hover:text-indigo-600 truncate block">
                                        {{ $prod->name }}
                                    </a>
                                    <div class="text-xs font-black text-slate-900 pt-1">
                                        ₹{{ number_format($prod->starting_price ?? 0) }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>

            {{-- 3. Countdown Timer Block --}}
            @elseif(($block['type'] ?? '') === 'countdown')
                <section class="max-w-7xl mx-auto px-6 lg:px-10" x-data="{
                    target: new Date('{{ $block['target_date'] ?? '2026-12-31' }}').getTime(),
                    days: '00', hours: '00', minutes: '00', seconds: '00',
                    init() {
                        const update = () => {
                            const now = new Date().getTime();
                            const dist = Math.max(0, this.target - now);
                            this.days = String(Math.floor(dist / (1000 * 60 * 60 * 24))).padStart(2, '0');
                            this.hours = String(Math.floor((dist % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))).padStart(2, '0');
                            this.minutes = String(Math.floor((dist % (1000 * 60 * 60)) / (1000 * 60))).padStart(2, '0');
                            this.seconds = String(Math.floor((dist % (1000 * 60)) / 1000)).padStart(2, '0');
                        };
                        update();
                        setInterval(update, 1000);
                    }
                }">
                    <div class="p-8 md:p-12 rounded-[32px] bg-gradient-to-r from-rose-900 via-purple-900 to-slate-900 text-white text-center shadow-xl space-y-6">
                        <div class="max-w-xl mx-auto space-y-2">
                            <span class="px-3 py-1 rounded-full text-[10px] font-bold tracking-widest uppercase bg-rose-500/20 text-rose-300 border border-rose-400/30">
                                Limited Window Flash Drop
                            </span>
                            <h2 class="text-2xl md:text-4xl font-black font-display">{{ $block['title'] ?? '' }}</h2>
                            <p class="text-xs md:text-sm text-white/80">{{ $block['subtitle'] ?? '' }}</p>
                        </div>

                        <!-- Timer Digits -->
                        <div class="flex items-center justify-center gap-3 font-mono">
                            <div class="p-3 md:p-4 rounded-2xl bg-white/10 backdrop-blur border border-white/10 min-w-[70px]">
                                <div class="text-2xl md:text-4xl font-black" x-text="days">00</div>
                                <div class="text-[9px] uppercase tracking-wider text-white/60">Days</div>
                            </div>
                            <span class="text-2xl font-black text-white/40">:</span>
                            <div class="p-3 md:p-4 rounded-2xl bg-white/10 backdrop-blur border border-white/10 min-w-[70px]">
                                <div class="text-2xl md:text-4xl font-black" x-text="hours">00</div>
                                <div class="text-[9px] uppercase tracking-wider text-white/60">Hours</div>
                            </div>
                            <span class="text-2xl font-black text-white/40">:</span>
                            <div class="p-3 md:p-4 rounded-2xl bg-white/10 backdrop-blur border border-white/10 min-w-[70px]">
                                <div class="text-2xl md:text-4xl font-black" x-text="minutes">00</div>
                                <div class="text-[9px] uppercase tracking-wider text-white/60">Mins</div>
                            </div>
                            <span class="text-2xl font-black text-white/40">:</span>
                            <div class="p-3 md:p-4 rounded-2xl bg-white/10 backdrop-blur border border-white/10 min-w-[70px]">
                                <div class="text-2xl md:text-4xl font-black text-rose-400" x-text="seconds">00</div>
                                <div class="text-[9px] uppercase tracking-wider text-white/60">Secs</div>
                            </div>
                        </div>

                        @if(!empty($block['coupon_code']))
                            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/20 text-xs font-mono font-bold tracking-wider">
                                <span>Code:</span>
                                <span class="text-amber-300">{{ $block['coupon_code'] }}</span>
                            </div>
                        @endif
                    </div>
                </section>

            {{-- 4. Testimonials Block --}}
            @elseif(($block['type'] ?? '') === 'testimonials')
                <section class="max-w-4xl mx-auto px-6 text-center space-y-4">
                    <div class="text-amber-400 text-lg flex items-center justify-center gap-1">
                        @for($i = 0; $i < ($block['rating'] ?? 5); $i++)
                            <span>★</span>
                        @endfor
                    </div>
                    <blockquote class="text-xl md:text-2xl font-serif italic text-slate-800 leading-relaxed">
                        &ldquo;{{ $block['quote'] ?? '' }}&rdquo;
                    </blockquote>
                    <div class="text-xs">
                        <div class="font-bold text-slate-900">{{ $block['author'] ?? 'Verified Buyer' }}</div>
                        <div class="text-slate-400 font-medium">{{ $block['location'] ?? '' }}</div>
                    </div>
                </section>

            {{-- 5. FAQ Accordion Block --}}
            @elseif(($block['type'] ?? '') === 'faq')
                <section class="max-w-3xl mx-auto px-6 space-y-4">
                    <h2 class="text-2xl font-black font-display text-center text-slate-900">{{ $block['title'] ?? 'Frequently Asked Questions' }}</h2>
                    <div class="space-y-3 pt-2">
                        @foreach($block['items'] ?? [] as $faqIdx => $item)
                            <div x-data="{ expanded: false }" class="rounded-2xl border border-slate-200/80 bg-white p-4 shadow-xs">
                                <button @click="expanded = !expanded" class="flex items-center justify-between w-full text-left font-bold text-xs text-slate-900">
                                    <span>{{ $item['q'] ?? '' }}</span>
                                    <svg class="w-4 h-4 text-slate-400 transition-transform" :class="{ 'rotate-180': expanded }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <div x-show="expanded" x-collapse class="text-xs text-slate-600 mt-2 leading-relaxed pt-2 border-t border-slate-100" style="display: none;">
                                    {{ $item['a'] ?? '' }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>

            {{-- 6. Newsletter Block --}}
            @elseif(($block['type'] ?? '') === 'newsletter')
                <section class="max-w-4xl mx-auto px-6">
                    <div class="p-8 md:p-12 rounded-[32px] bg-slate-100 text-center space-y-4 border border-slate-200">
                        <h2 class="text-2xl md:text-3xl font-black font-display text-slate-900">{{ $block['title'] ?? 'Join The VIP Club' }}</h2>
                        <p class="text-xs text-slate-500 max-w-md mx-auto">{{ $block['subtitle'] ?? '' }}</p>
                        <form method="POST" action="{{ route('newsletter.subscribe') }}" class="flex items-center justify-center gap-2 max-w-md mx-auto pt-2">
                            @csrf
                            <input type="email" name="email" required placeholder="Enter your email address"
                                   class="px-4 py-2.5 rounded-full border border-slate-200 bg-white text-xs w-full focus:outline-none focus:border-indigo-500">
                            <button type="submit" class="px-6 py-2.5 rounded-full bg-slate-900 text-white font-bold text-xs uppercase tracking-wider hover:bg-slate-800 transition shrink-0">
                                Subscribe
                            </button>
                        </form>
                    </div>
                </section>

            @endif

        @endforeach

    </div>

</x-storefront-layout>
