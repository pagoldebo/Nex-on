{{--
    ======================================================================
    VIEW TEMPLATE:  STOREFRONT COMMON TERMS 
    ======================================================================
    File: resources/views/storefront/common/terms.blade.php
    Responsibility: View template supporting the Nex-on platform
    Features:
      - Standard Blade template syntax with Tailwind CSS styling
      - Integrates with application controllers and view model data
--}}
<x-storefront-layout :theme="$theme" title="Terms of Service &amp; Purchase Agreement" metaDesc="Review the terms and conditions governing the purchase and delivery of our intimate apparel.">
    <div class="py-12 lg:py-20 bg-stone-50/50">
        <div class="max-w-4xl mx-auto px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <span class="text-[11px] font-bold tracking-[0.25em] text-rose-600 uppercase">Legal Agreement</span>
                <h1 class="text-3xl lg:text-5xl font-serif text-slate-900 mt-3 font-normal tracking-tight">Terms of Service</h1>
                <p class="text-sm lg:text-base text-slate-600 mt-4 leading-relaxed font-light">
                    The terms, conditions, and standards governing your use of this atelier storefront and purchase of goods.
                </p>
            </div>

            <div class="bg-white rounded-3xl p-8 sm:p-10 border border-slate-200/80 shadow-xs space-y-8 text-xs text-slate-600 leading-relaxed">
                <div class="space-y-3">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900">1. Acceptance of Terms</h3>
                    <p>
                        By accessing this website, placing an order, or interacting with our personal concierge, you agree to be bound by these Terms of Service and all applicable laws and regulations of India.
                    </p>
                </div>

                <div class="space-y-3 pt-4 border-t border-slate-100">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900">2. Pricing, Inaccuracies &amp; Currency</h3>
                    <p>
                        All prices are listed in Indian Rupees (₹ INR) and include all applicable statutory Goods and Services Tax (GST). We reserve the right to correct any typographical pricing errors or discontinue any limited edition collection without prior notice.
                    </p>
                </div>

                <div class="space-y-3 pt-4 border-t border-slate-100">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900">3. Orders, Dispatch &amp; Title</h3>
                    <p>
                        An order confirmation email constitutes receipt of your request, not a binding contract of sale. We reserve the right to decline or cancel any order suspected of fraudulent activity or unauthorized bulk reselling.
                    </p>
                </div>

                <div class="space-y-3 pt-4 border-t border-slate-100">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900">4. Intellectual Property</h3>
                    <p>
                        All imagery, brand photography, silhouettes, copy, and logo typography are the exclusive intellectual property of our company. Unauthorized reproduction, scraping, or commercial exploitation is strictly prohibited.
                    </p>
                </div>

                <div class="space-y-3 pt-4 border-t border-slate-100">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900">5. Governing Law &amp; Jurisdiction</h3>
                    <p>
                        These Terms shall be governed by and construed in accordance with the laws of India. Any disputes arising hereunder shall be subject to the exclusive jurisdiction of the competent courts in India.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-storefront-layout>
