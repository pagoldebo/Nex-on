{{--
    ======================================================================
    STOREFRONT COMMON COMPONENT: INTERACTIVE SIZE & FIT GUIDE
    ======================================================================
    File: resources/views/storefront/common/size-guide.blade.php
    Component Injection: storefront.common.size-guide
    Responsibility: Apparel size measurement tables and international conversions
    Features:
      - Tabbed views for Inches (in) and Centimeters (cm)
      - International size conversion matrix (US, UK, EU, India)
      - How-to-measure illustration guide (Bust, Waist, Hips, Inseam)
    Operations & Maintenance:
      - Content can be customized via Admin -> Settings -> Size Guide
--}}
<x-storefront-layout :theme="$theme" :title="\App\Models\Setting::get('sizeguide_title', 'The Precision Size Guide')" metaDesc="Find your perfect lingerie fit with our interactive bra size calculator and international sister-sizing chart.">
    <div class="py-12 lg:py-20 bg-stone-50/50">
        <div class="max-w-5xl mx-auto px-6 lg:px-8">
            <!-- Header -->
            <div class="text-center max-w-2xl mx-auto mb-16">
                <span class="text-[11px] font-bold tracking-[0.25em] text-rose-600 uppercase">{{ \App\Models\Setting::get('sizeguide_badge', 'Atelier Fit Perfection') }}</span>
                <h1 class="text-3xl lg:text-5xl font-serif text-slate-900 mt-3 font-normal tracking-tight">{{ \App\Models\Setting::get('sizeguide_title', 'The Precision Size Guide') }}</h1>
                <p class="text-sm lg:text-base text-slate-600 mt-4 leading-relaxed font-light">
                    {{ \App\Models\Setting::get('sizeguide_subtitle', 'Over 80% of women wear the incorrect bra size. Use our interactive measurement calculator to find your exact band, cup, and sister size.') }}
                </p>
            </div>

            <!-- Interactive Bra Calculator Card -->
            <div class="bg-white rounded-3xl p-8 sm:p-10 border border-slate-200/80 shadow-md mb-16">
                <div class="max-w-2xl mx-auto text-center space-y-6">
                    <span class="text-xs font-mono text-slate-400 uppercase tracking-widest">{{ \App\Models\Setting::get('sizeguide_calc_badge', 'Interactive Calculator') }}</span>
                    <h2 class="text-2xl font-serif text-slate-900 font-medium">{{ \App\Models\Setting::get('sizeguide_calc_title', 'Find Your Bespoke Size') }}</h2>
                    <p class="text-xs text-slate-500 font-light">
                        {{ \App\Models\Setting::get('sizeguide_calc_desc', 'Grab a flexible measuring tape while standing relaxed in an unpadded, non-pushup bra.') }}
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-4 text-left">
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                1. Underbust (Inches)
                            </label>
                            <input type="number" id="calcUnderbust" placeholder="e.g. 29.5" step="0.5" min="24" max="50" 
                                   class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-slate-900/10 focus:border-slate-900 outline-none transition">
                            <span class="text-[11px] text-slate-400 block font-light">{{ \App\Models\Setting::get('sizeguide_underbust_hint', 'Measure snugly directly underneath your breasts.') }}</span>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                2. Bust Peak (Inches)
                            </label>
                            <input type="number" id="calcBust" placeholder="e.g. 33.5" step="0.5" min="26" max="60" 
                                   class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-slate-900/10 focus:border-slate-900 outline-none transition">
                            <span class="text-[11px] text-slate-400 block font-light">{{ \App\Models\Setting::get('sizeguide_bust_hint', 'Measure loosely across the fullest part of your bust.') }}</span>
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="button" onclick="calculateBraSize()" class="px-8 py-3 bg-slate-900 hover:bg-rose-950 text-white rounded-full text-xs font-bold uppercase tracking-widest transition shadow-sm">
                            Calculate My Size
                        </button>
                    </div>

                    <!-- Calculator Result Box -->
                    <div id="calcResult" class="hidden mt-6 p-6 rounded-2xl bg-rose-50/70 border border-rose-200/60 animate-fade-in">
                        <div class="text-[11px] uppercase tracking-wider font-bold text-rose-700">Recommended Size</div>
                        <div id="calculatedSize" class="text-3xl font-serif font-bold text-slate-900 my-1">32C</div>
                        <div id="sisterSizeText" class="text-xs text-slate-600 font-light">Sister sizes: 30D (tighter band) or 34B (looser band).</div>
                        <div class="mt-4">
                            <a href="{{ route('search') }}" class="inline-block text-xs font-bold text-rose-700 underline hover:text-rose-900">Browse Products in This Size →</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sister Sizing Explanation Table -->
            <div class="space-y-12">
                <div class="bg-white rounded-3xl p-8 border border-slate-200/80 shadow-xs space-y-6">
                    <h3 class="text-xl font-serif text-slate-900">{{ \App\Models\Setting::get('sizeguide_sister_title', 'Sister Sizing Matrix') }}</h3>
                    <p class="text-xs text-slate-500 leading-relaxed font-light">
                        {{ \App\Models\Setting::get('sizeguide_sister_desc', 'Sister sizes share identical cup volume despite having different band measurements. If your band feels too loose or too snug, navigate horizontally along your sister size group:') }}
                    </p>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border border-slate-100 rounded-xl overflow-hidden">
                            <thead class="bg-slate-50 font-bold uppercase tracking-wider text-slate-500 text-[10px]">
                                <tr>
                                    <th class="p-3.5 border-b border-slate-200">Sister Group</th>
                                    <th class="p-3.5 border-b border-slate-200">Tight Band (Snug Fit)</th>
                                    <th class="p-3.5 border-b border-slate-200">Standard Fit</th>
                                    <th class="p-3.5 border-b border-slate-200">Relaxed Band (Comfort Fit)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-mono">
                                <tr>
                                    <td class="p-3.5 font-sans font-semibold text-slate-900">Group 1</td>
                                    <td class="p-3.5 text-slate-600">30B</td>
                                    <td class="p-3.5 font-bold text-indigo-600">32A</td>
                                    <td class="p-3.5 text-slate-600">—</td>
                                </tr>
                                <tr class="bg-slate-50/40">
                                    <td class="p-3.5 font-sans font-semibold text-slate-900">Group 2</td>
                                    <td class="p-3.5 text-slate-600">30C</td>
                                    <td class="p-3.5 font-bold text-indigo-600">32B</td>
                                    <td class="p-3.5 text-slate-600">34A</td>
                                </tr>
                                <tr>
                                    <td class="p-3.5 font-sans font-semibold text-slate-900">Group 3</td>
                                    <td class="p-3.5 text-slate-600">30D</td>
                                    <td class="p-3.5 font-bold text-indigo-600">32C</td>
                                    <td class="p-3.5 text-slate-600">34B</td>
                                </tr>
                                <tr class="bg-slate-50/40">
                                    <td class="p-3.5 font-sans font-semibold text-slate-900">Group 4</td>
                                    <td class="p-3.5 text-slate-600">32D</td>
                                    <td class="p-3.5 font-bold text-indigo-600">34C</td>
                                    <td class="p-3.5 text-slate-600">36B</td>
                                </tr>
                                <tr>
                                    <td class="p-3.5 font-sans font-semibold text-slate-900">Group 5</td>
                                    <td class="p-3.5 text-slate-600">32DD / 32E</td>
                                    <td class="p-3.5 font-bold text-indigo-600">34D</td>
                                    <td class="p-3.5 text-slate-600">36C</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Panties & Sleepwear Sizing -->
                <div class="bg-white rounded-3xl p-8 border border-slate-200/80 shadow-xs space-y-6">
                    <h3 class="text-xl font-serif text-slate-900">{{ \App\Models\Setting::get('sizeguide_bottoms_title', 'Panties, Chemises &amp; Robes') }}</h3>
                    <p class="text-xs text-slate-500 leading-relaxed font-light">
                        {{ \App\Models\Setting::get('sizeguide_bottoms_desc', 'For bottoms and sleepwear, measure your waist at the narrowest point and your hips around the fullest curve.') }}
                    </p>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border border-slate-100 rounded-xl overflow-hidden">
                            <thead class="bg-slate-50 font-bold uppercase tracking-wider text-slate-500 text-[10px]">
                                <tr>
                                    <th class="p-3.5 border-b border-slate-200">Size</th>
                                    <th class="p-3.5 border-b border-slate-200">Waist (Inches)</th>
                                    <th class="p-3.5 border-b border-slate-200">Hips (Inches)</th>
                                    <th class="p-3.5 border-b border-slate-200">Indian / UK Size</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-mono">
                                <tr>
                                    <td class="p-3.5 font-bold text-slate-900">XS</td>
                                    <td class="p-3.5 text-slate-600">24 – 26"</td>
                                    <td class="p-3.5 text-slate-600">34 – 36"</td>
                                    <td class="p-3.5 text-slate-600 font-sans">UK 6–8 (XS)</td>
                                </tr>
                                <tr class="bg-slate-50/40">
                                    <td class="p-3.5 font-bold text-slate-900">S</td>
                                    <td class="p-3.5 text-slate-600">26 – 28"</td>
                                    <td class="p-3.5 text-slate-600">36 – 38"</td>
                                    <td class="p-3.5 text-slate-600 font-sans">UK 8–10 (S)</td>
                                </tr>
                                <tr>
                                    <td class="p-3.5 font-bold text-slate-900">M</td>
                                    <td class="p-3.5 text-slate-600">28 – 30"</td>
                                    <td class="p-3.5 text-slate-600">38 – 40"</td>
                                    <td class="p-3.5 text-slate-600 font-sans">UK 10–12 (M)</td>
                                </tr>
                                <tr class="bg-slate-50/40">
                                    <td class="p-3.5 font-bold text-slate-900">L</td>
                                    <td class="p-3.5 text-slate-600">30 – 33"</td>
                                    <td class="p-3.5 text-slate-600">40 – 43"</td>
                                    <td class="p-3.5 text-slate-600 font-sans">UK 12–14 (L)</td>
                                </tr>
                                <tr>
                                    <td class="p-3.5 font-bold text-slate-900">XL</td>
                                    <td class="p-3.5 text-slate-600">33 – 36"</td>
                                    <td class="p-3.5 text-slate-600">43 – 46"</td>
                                    <td class="p-3.5 text-slate-600 font-sans">UK 14–16 (XL)</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                @if(\App\Models\Setting::get('sizeguide_additional_info'))
                    <div class="bg-white rounded-3xl p-8 border border-slate-200/80 shadow-xs space-y-4 text-xs text-slate-600 leading-relaxed">
                        <h3 class="text-base font-bold uppercase tracking-wider text-slate-900">Custom Fitting Notes &amp; Guidelines</h3>
                        <div>
                            {!! nl2br(e(\App\Models\Setting::get('sizeguide_additional_info'))) !!}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        function calculateBraSize() {
            const underbust = parseFloat(document.getElementById('calcUnderbust').value);
            const bust = parseFloat(document.getElementById('calcBust').value);

            if (isNaN(underbust) || isNaN(bust) || bust <= underbust) {
                alert('Please enter valid measurements. Bust measurement must be larger than underbust.');
                return;
            }

            // Band calculation (standard underbust rounding to even number)
            let band = Math.round(underbust);
            if (band % 2 !== 0) band += 1;
            if (underbust < 28) band = 28;
            if (underbust > 44) band = 44;

            // Cup difference
            const diff = bust - underbust;
            let cup = 'A';
            if (diff >= 0.5 && diff < 1.5) cup = 'A';
            else if (diff >= 1.5 && diff < 2.5) cup = 'B';
            else if (diff >= 2.5 && diff < 3.5) cup = 'C';
            else if (diff >= 3.5 && diff < 4.5) cup = 'D';
            else if (diff >= 4.5 && diff < 5.5) cup = 'DD';
            else if (diff >= 5.5) cup = 'E';

            const size = `${band}${cup}`;
            document.getElementById('calculatedSize').textContent = size;

            // Calculate sister sizes
            const tighterBand = band - 2;
            const looserBand = band + 2;
            const cupChars = ['A', 'B', 'C', 'D', 'DD', 'E', 'F'];
            const cupIdx = cupChars.indexOf(cup);

            let sisterMsg = '';
            if (cupIdx !== -1) {
                const largerCup = cupChars[cupIdx + 1] || 'E';
                const smallerCup = cupChars[cupIdx - 1] || 'A';
                sisterMsg = `Sister sizes: ${tighterBand}${largerCup} (firmer band) or ${looserBand}${smallerCup} (relaxed band).`;
            }

            document.getElementById('sisterSizeText').textContent = sisterMsg;
            document.getElementById('calcResult').classList.remove('hidden');
        }
    </script>
</x-storefront-layout>
