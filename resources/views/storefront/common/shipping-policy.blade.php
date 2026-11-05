<x-storefront-layout :theme="$theme" :title="\App\Models\Setting::get('shipping_policy_title', 'Shipping & Discreet Delivery')" metaDesc="Learn about our 100% discreet packaging promise, courier partners, dispatch timelines, and free shipping across India.">
    <div class="py-12 lg:py-20 bg-stone-50/50">
        <div class="max-w-4xl mx-auto px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <span class="text-[11px] font-bold tracking-[0.25em] text-rose-600 uppercase">{{ \App\Models\Setting::get('shipping_policy_badge', 'Logistics & Privacy') }}</span>
                <h1 class="text-3xl lg:text-5xl font-serif text-slate-900 mt-3 font-normal tracking-tight">{{ \App\Models\Setting::get('shipping_policy_title', 'Shipping & Discreet Delivery') }}</h1>
                <p class="text-sm lg:text-base text-slate-600 mt-4 leading-relaxed font-light">
                    {{ \App\Models\Setting::get('shipping_policy_subtitle', 'Our gold standard delivery experience: fast, trackable, and completely private from our atelier to your doorstep.') }}
                </p>
            </div>

            <!-- Discreet Packaging Spotlight Banner -->
            <div class="bg-slate-950 text-white rounded-3xl p-8 sm:p-10 mb-12 relative overflow-hidden shadow-md">
                <div class="max-w-xl space-y-4">
                    <span class="text-[10px] font-mono uppercase tracking-widest text-rose-400">{{ \App\Models\Setting::get('shipping_banner_badge', 'Our Privacy Promise') }}</span>
                    <h2 class="text-2xl font-serif">{{ \App\Models\Setting::get('shipping_banner_title', '100% Confidential Outer Packaging') }}</h2>
                    <p class="text-xs text-slate-300 leading-relaxed font-light">
                        {!! nl2br(e(\App\Models\Setting::get('shipping_banner_desc', 'Every order is dispatched in an unmarked, tamper-evident exterior box or opaque courier flyer. There are zero mentions of lingerie, brassieres, or intimate apparel anywhere on the exterior mailing label. The sender is listed simply as "LS Logistics".'))) !!}
                    </p>
                </div>
            </div>

            <div class="bg-white rounded-3xl p-8 sm:p-10 border border-slate-200/80 shadow-xs space-y-8 text-xs text-slate-600 leading-relaxed">
                <div class="space-y-3">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900">{{ \App\Models\Setting::get('shipping_sec1_title', '1. Processing & Dispatch Timelines') }}</h3>
                    <p>
                        {!! nl2br(e(\App\Models\Setting::get('shipping_sec1_desc', 'All in-stock orders are processed within 24 business hours (Monday through Saturday, excluding national holidays). Once dispatched, an SMS and email notification with your live tracking AWB link will be automatically sent.'))) !!}
                    </p>
                </div>

                <div class="space-y-3 pt-4 border-t border-slate-100">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900">{{ \App\Models\Setting::get('shipping_sec2_title', '2. Estimated Delivery Transit Times') }}</h3>
                    @if(\App\Models\Setting::get('shipping_sec2_desc'))
                        <div class="space-y-1.5 font-light">
                            {!! nl2br(e(\App\Models\Setting::get('shipping_sec2_desc'))) !!}
                        </div>
                    @else
                        <ul class="list-disc pl-5 space-y-1.5 font-light">
                            <li><strong>Metro Cities (Mumbai, Delhi NCR, Bengaluru, Hyderabad, Chennai, Kolkata):</strong> 1 to 2 business days via Express Air.</li>
                            <li><strong>Tier 2 Cities &amp; State Capitals:</strong> 2 to 3 business days.</li>
                            <li><strong>Rest of India &amp; Remote Pincodes:</strong> 3 to 5 business days.</li>
                        </ul>
                    @endif
                </div>

                <div class="space-y-3 pt-4 border-t border-slate-100">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900">{{ \App\Models\Setting::get('shipping_sec3_title', '3. Shipping Rates & Free Delivery') }}</h3>
                    <p>
                        {!! nl2br(e(\App\Models\Setting::get('shipping_sec3_desc', 'We offer Complimentary Express Shipping on all prepaid orders exceeding ₹1,499 across India. For orders below ₹1,499, a nominal flat express shipping fee of ₹99 is applied at checkout.'))) !!}
                    </p>
                </div>

                <div class="space-y-3 pt-4 border-t border-slate-100">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900">{{ \App\Models\Setting::get('shipping_sec4_title', '4. Cash on Delivery (COD)') }}</h3>
                    <p>
                        {!! nl2br(e(\App\Models\Setting::get('shipping_sec4_desc', 'Cash on Delivery is available for serviceable pincodes with a flat ₹50 handling charge. An OTP or telephonic confirmation may be requested prior to dispatch for high-value COD shipments.'))) !!}
                    </p>
                </div>

                <div class="space-y-3 pt-4 border-t border-slate-100">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900">{{ \App\Models\Setting::get('shipping_sec5_title', '5. Courier Partners') }}</h3>
                    <p>
                        {!! nl2br(e(\App\Models\Setting::get('shipping_sec5_desc', 'We partner exclusively with top-tier courier networks including Bluedart, Delhivery, Shadowfax, and Xpressbees to ensure safe transit and zero tampering.'))) !!}
                    </p>
                </div>

                @if(\App\Models\Setting::get('shipping_custom_content'))
                    <div class="space-y-3 pt-4 border-t border-slate-100">
                        {!! \App\Models\Setting::get('shipping_custom_content') !!}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-storefront-layout>
