{{--
    ======================================================================
    VIEW TEMPLATE:  STOREFRONT COMMON FAQ 
    ======================================================================
    File: resources/views/storefront/common/faq.blade.php
    Responsibility: View template supporting the Nex-on platform
    Features:
      - Standard Blade template syntax with Tailwind CSS styling
      - Integrates with application controllers and view model data
--}}
<x-storefront-layout :theme="$theme" title="Frequently Asked Questions (FAQ)" metaDesc="Find answers to common questions about discreet shipping, sizing, order tracking, and intimate returns.">
    <div class="py-12 lg:py-20 bg-stone-50/50">
        <div class="max-w-4xl mx-auto px-6 lg:px-8">
            <!-- Header -->
            <div class="text-center max-w-2xl mx-auto mb-16">
                <span class="text-[11px] font-bold tracking-[0.25em] text-rose-600 uppercase">Client Assistance</span>
                <h1 class="text-3xl lg:text-5xl font-serif text-slate-900 mt-3 font-normal tracking-tight">Frequently Asked Questions</h1>
                <p class="text-sm lg:text-base text-slate-600 mt-4 leading-relaxed font-light">
                    Everything you need to know about our atelier orders, confidential dispatch, sizing assistance, and care instructions.
                </p>
            </div>

            <div class="space-y-10">
                @if(isset($faqs) && $faqs->isNotEmpty())
                    @foreach($faqs as $catTitle => $items)
                        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-4">
                            <div class="flex items-center gap-2.5 text-xs font-bold uppercase tracking-wider text-slate-900 border-b border-slate-100 pb-3">
                                <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>{{ $catTitle }}</span>
                            </div>
                            <div class="space-y-3 divide-y divide-slate-100">
                                @foreach($items as $faq)
                                    <details class="group pt-2 cursor-pointer">
                                        <summary class="flex items-center justify-between font-semibold text-slate-900 text-sm list-none py-2">
                                            <span>{{ $faq->question }}</span>
                                            <span class="transition group-open:rotate-180 text-slate-400">▾</span>
                                        </summary>
                                        <p class="text-xs text-slate-600 leading-relaxed pt-2 pb-3 font-light">
                                            {!! nl2br(e($faq->answer)) !!}
                                        </p>
                                    </details>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                @else
                <!-- Category 1: Discreet Shipping -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-4">
                    <div class="flex items-center gap-2.5 text-xs font-bold uppercase tracking-wider text-slate-900 border-b border-slate-100 pb-3">
                        <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        <span>1. Discreet Shipping &amp; Packaging</span>
                    </div>

                    <div class="space-y-3 divide-y divide-slate-100">
                        <details class="group pt-2 cursor-pointer">
                            <summary class="flex items-center justify-between font-semibold text-slate-900 text-sm list-none py-2">
                                <span>Will anyone know what is inside the package?</span>
                                <span class="transition group-open:rotate-180 text-slate-400">▾</span>
                            </summary>
                            <p class="text-xs text-slate-600 leading-relaxed pt-2 pb-3 font-light">
                                Absolutely not. All orders are packed in 100% plain, tamper-proof exterior brown or matte black boxes with no brand logos, no product names, and no lingerie references on the shipping label. The sender name appears discreetly as "LS Logistics".
                            </p>
                        </details>

                        <details class="group pt-2 cursor-pointer">
                            <summary class="flex items-center justify-between font-semibold text-slate-900 text-sm list-none py-2">
                                <span>How long will delivery take to my city?</span>
                                <span class="transition group-open:rotate-180 text-slate-400">▾</span>
                            </summary>
                            <p class="text-xs text-slate-600 leading-relaxed pt-2 pb-3 font-light">
                                Metro cities (Mumbai, Delhi NCR, Bangalore, Chennai, Hyderabad, Kolkata) generally receive deliveries within 24 to 48 hours. Tier 2 and Tier 3 cities across India take 2 to 4 business days via express air couriers (Bluedart, Delhivery, Xpressbees).
                            </p>
                        </details>
                    </div>
                </div>

                <!-- Category 2: Sizing & Fit -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-4">
                    <div class="flex items-center gap-2.5 text-xs font-bold uppercase tracking-wider text-slate-900 border-b border-slate-100 pb-3">
                        <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.879 2.879M12 12L9.121 9.121m0 5.758a3 3 0 10-4.243-4.243 3 3 0 004.243 4.243z"/></svg>
                        <span>2. Sizing &amp; Fit Assistance</span>
                    </div>

                    <div class="space-y-3 divide-y divide-slate-100">
                        <details class="group pt-2 cursor-pointer">
                            <summary class="flex items-center justify-between font-semibold text-slate-900 text-sm list-none py-2">
                                <span>How do I determine my accurate bra size?</span>
                                <span class="transition group-open:rotate-180 text-slate-400">▾</span>
                            </summary>
                            <p class="text-xs text-slate-600 leading-relaxed pt-2 pb-3 font-light">
                                Take a snug measurement directly under your bust for your band size, and a loose measurement across the fullest part of your bust for cup sizing. Refer to our interactive <a href="{{ route('sizeGuide') }}" class="text-rose-600 font-semibold underline">Size Guide &amp; Sister Size Chart</a> or contact our concierge for one-on-one virtual consultation.
                            </p>
                        </details>

                        <details class="group pt-2 cursor-pointer">
                            <summary class="flex items-center justify-between font-semibold text-slate-900 text-sm list-none py-2">
                                <span>What if my usual cup size feels too tight in a balconette?</span>
                                <span class="transition group-open:rotate-180 text-slate-400">▾</span>
                            </summary>
                            <p class="text-xs text-slate-600 leading-relaxed pt-2 pb-3 font-light">
                                Balconette and unlined plunge cuts can fit slightly shallower. In structured lace, many clients prefer moving up half a cup or trying their sister size (e.g. from 34B to 32C).
                            </p>
                        </details>
                    </div>
                </div>

                <!-- Category 3: Orders, Payments & Returns -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-4">
                    <div class="flex items-center gap-2.5 text-xs font-bold uppercase tracking-wider text-slate-900 border-b border-slate-100 pb-3">
                        <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        <span>3. Payments, Returns &amp; Hygiene Standards</span>
                    </div>

                    <div class="space-y-3 divide-y divide-slate-100">
                        <details class="group pt-2 cursor-pointer">
                            <summary class="flex items-center justify-between font-semibold text-slate-900 text-sm list-none py-2">
                                <span>What payment methods are supported?</span>
                                <span class="transition group-open:rotate-180 text-slate-400">▾</span>
                            </summary>
                            <p class="text-xs text-slate-600 leading-relaxed pt-2 pb-3 font-light">
                                We accept all Indian Credit &amp; Debit Cards (Visa, Mastercard, RuPay, Amex), UPI (Google Pay, PhonePe, Paytm), Net Banking across 50+ banks, and Cash on Delivery (COD) for eligible pincodes.
                            </p>
                        </details>

                        <details class="group pt-2 cursor-pointer">
                            <summary class="flex items-center justify-between font-semibold text-slate-900 text-sm list-none py-2">
                                <span>What is your return policy for intimate lingerie?</span>
                                <span class="transition group-open:rotate-180 text-slate-400">▾</span>
                            </summary>
                            <p class="text-xs text-slate-600 leading-relaxed pt-2 pb-3 font-light">
                                For hygiene and safety standards, bras, sleepwear, and loungewear with intact security tags and original packaging may be exchanged or returned within 7 days. Panties, thongs, adhesive bras, and silicone accessories are final sale once the tamper-evident hygiene seal is broken.
                            </p>
                        </details>
                    </div>
                </div>
                @endif
            </div>

            <!-- Still have questions footer -->
            <div class="text-center mt-16 p-8 rounded-3xl bg-white border border-slate-200/80 shadow-xs">
                <h3 class="font-serif text-lg text-slate-900">Still have a question?</h3>
                <p class="text-xs text-slate-500 mt-1">Our confidential customer care specialists are happy to assist.</p>
                <div class="mt-4 flex items-center justify-center gap-4">
                    <a href="{{ route('contact') }}" class="px-6 py-2.5 bg-slate-900 hover:bg-rose-950 text-white rounded-full text-xs font-bold uppercase tracking-wider transition">
                        Contact Concierge
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-storefront-layout>
