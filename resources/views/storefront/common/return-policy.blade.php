{{--
    ======================================================================
    VIEW TEMPLATE:  STOREFRONT COMMON RETURN POLICY 
    ======================================================================
    File: resources/views/storefront/common/return-policy.blade.php
    Responsibility: View template supporting the Nex-on platform
    Features:
      - Standard Blade template syntax with Tailwind CSS styling
      - Integrates with application controllers and view model data
--}}
<x-storefront-layout :theme="$theme" :title="\App\Models\Setting::get('return_policy_title', 'Returns & Hygiene Standards')" metaDesc="Review our 7-day hassle-free return and exchange policy adhering to international intimate apparel hygiene standards.">
    <div class="py-12 lg:py-20 bg-stone-50/50">
        <div class="max-w-4xl mx-auto px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <span class="text-[11px] font-bold tracking-[0.25em] text-rose-600 uppercase">{{ \App\Models\Setting::get('return_policy_badge', 'Customer Guarantee') }}</span>
                <h1 class="text-3xl lg:text-5xl font-serif text-slate-900 mt-3 font-normal tracking-tight">{{ \App\Models\Setting::get('return_policy_title', 'Returns & Hygiene Standards') }}</h1>
                <p class="text-sm lg:text-base text-slate-600 mt-4 leading-relaxed font-light">
                    {{ \App\Models\Setting::get('return_policy_subtitle', 'Your complete confidence and hygiene are our highest priorities. Review our straightforward intimate return policies below.') }}
                </p>
            </div>

            <div class="bg-white rounded-3xl p-8 sm:p-10 border border-slate-200/80 shadow-xs space-y-8 text-xs text-slate-600 leading-relaxed">
                <!-- Hygiene banner -->
                <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200/60 text-amber-900 flex items-start gap-3">
                    <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <div>
                        <div class="font-bold text-xs uppercase tracking-wider">{{ \App\Models\Setting::get('return_banner_title', 'Intimate Hygiene Regulations') }}</div>
                        <p class="mt-1 font-light">
                            {!! nl2br(e(\App\Models\Setting::get('return_banner_desc', 'Due to strict Indian health & safety standards for second-skin intimates, items tried directly on the skin without underwear or protective hygiene stickers cannot be restocked or refunded.'))) !!}
                        </p>
                    </div>
                </div>

                <div class="space-y-3">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900">{{ \App\Models\Setting::get('return_sec1_title', '1. Eligible Items for 7-Day Return / Exchange') }}</h3>
                    @if(\App\Models\Setting::get('return_sec1_desc'))
                        <div class="space-y-1.5 font-light">
                            {!! nl2br(e(\App\Models\Setting::get('return_sec1_desc'))) !!}
                        </div>
                    @else
                        <ul class="list-disc pl-5 space-y-1.5 font-light">
                            <li><strong>Bras &amp; Bralettes:</strong> Must be unwashed, unworn, free of perfume/deodorant scents, and with original brand tags attached.</li>
                            <li><strong>Loungewear, Robes &amp; Chemises:</strong> Must be in original polybag packaging with tags intact.</li>
                            <li><strong>Incorrect or Damaged Item:</strong> If you receive a manufacturing defect or incorrect item, we arrange immediate priority replacement at zero cost to you.</li>
                        </ul>
                    @endif
                </div>

                <div class="space-y-3 pt-4 border-t border-slate-100">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900">{{ \App\Models\Setting::get('return_sec2_title', '2. Final Sale (Non-Returnable) Items') }}</h3>
                    @if(\App\Models\Setting::get('return_sec2_desc'))
                        <div class="space-y-1.5 font-light">
                            {!! nl2br(e(\App\Models\Setting::get('return_sec2_desc'))) !!}
                        </div>
                    @else
                        <ul class="list-disc pl-5 space-y-1.5 font-light">
                            <li>Panties, thongs, and brief multipacks once the outer hygiene seal is opened.</li>
                            <li>Adhesive silicone bras, pasties, and body tape.</li>
                            <li>Items purchased during clearance / archive sales explicitly tagged as "Final Sale".</li>
                        </ul>
                    @endif
                </div>

                <div class="space-y-3 pt-4 border-t border-slate-100">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900">{{ \App\Models\Setting::get('return_sec3_title', '3. How to Initiate a Return') }}</h3>
                    @if(\App\Models\Setting::get('return_sec3_desc'))
                        <p>
                            {!! nl2br(e(\App\Models\Setting::get('return_sec3_desc'))) !!}
                        </p>
                    @else
                        <p>
                            Contact our concierge at <a href="mailto:{{ \App\Models\Setting::supportEmail() }}" class="text-indigo-600 font-semibold underline">{{ \App\Models\Setting::supportEmail() }}</a> or visit our <a href="{{ route('contact') }}" class="text-indigo-600 font-semibold underline">Contact Page</a> within 7 days of package delivery. 
                            Provide your Order ID and photo of the item with tags. Our team will schedule an address reverse pickup within 48 hours.
                        </p>
                    @endif
                </div>

                <div class="space-y-3 pt-4 border-t border-slate-100">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900">{{ \App\Models\Setting::get('return_sec4_title', '4. Refund Processing') }}</h3>
                    <p>
                        {!! nl2br(e(\App\Models\Setting::get('return_sec4_desc', 'Upon receipt and quality inspection at our atelier (usually 2 business days after receipt), refunds are processed directly to your original payment method (Credit card, UPI, Net Banking) within 3 to 5 banking days. COD orders are refunded via instant UPI or NEFT transfer.'))) !!}
                    </p>
                </div>

                @if(\App\Models\Setting::get('return_custom_content'))
                    <div class="space-y-3 pt-4 border-t border-slate-100">
                        {!! \App\Services\HtmlSanitizerService::clean(\App\Models\Setting::get('return_custom_content')) !!}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-storefront-layout>
