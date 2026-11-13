{{--
    ======================================================================
    VIEW TEMPLATE:  STOREFRONT COMMON CONTACT 
    ======================================================================
    File: resources/views/storefront/common/contact.blade.php
    Responsibility: View template supporting the Nex-on platform
    Features:
      - Standard Blade template syntax with Tailwind CSS styling
      - Integrates with application controllers and view model data
--}}
<x-storefront-layout :theme="$theme" title="Contact Concierge &amp; Customer Care" metaDesc="Reach out to our personal styling concierge for fit assistance, custom orders, or order status inquiries.">
    <div class="py-12 lg:py-20 bg-stone-50/50">
        <div class="max-w-5xl mx-auto px-6 lg:px-8">
            <!-- Header -->
            <div class="text-center max-w-2xl mx-auto mb-16">
                <span class="text-[11px] font-bold tracking-[0.25em] text-rose-600 uppercase">Personal Concierge</span>
                <h1 class="text-3xl lg:text-5xl font-serif text-slate-900 mt-3 font-normal tracking-tight">How May We Assist You?</h1>
                <p class="text-sm lg:text-base text-slate-600 mt-4 leading-relaxed font-light">
                    Whether you require confidential sizing advice, bespoke bridal guidance, or tracking assistance, our team is at your complete disposal.
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
                <!-- Concierge Info -->
                <div class="lg:col-span-5 space-y-8">
                    <div class="bg-white rounded-3xl p-8 border border-slate-200/80 shadow-xs space-y-6">
                        <div>
                            <span class="text-[10px] font-mono text-slate-400 uppercase tracking-widest block mb-1">Direct Assistance</span>
                            <h2 class="text-xl font-serif text-slate-900 font-medium">Bespoke Support</h2>
                            <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                                Our private stylists respond to all inquiries within 2 to 4 business hours.
                            </p>
                        </div>

                        <div class="space-y-4 pt-4 border-t border-slate-100 text-xs">
                            <div class="flex items-start gap-3.5">
                                <div class="w-8 h-8 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </div>
                                <div>
                                    <div class="font-bold text-slate-800">Email Inquiries</div>
                                    @php $supEmail = \App\Models\Setting::supportEmail(); @endphp
                                    <a href="mailto:{{ $supEmail }}" class="text-slate-500 hover:text-rose-600 transition">{{ $supEmail }}</a>
                                </div>
                            </div>

                            <div class="flex items-start gap-3.5">
                                <div class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                </div>
                                <div>
                                    <div class="font-bold text-slate-800">WhatsApp &amp; Phone</div>
                                    @php $supPhone = \App\Models\Setting::get('support_phone', '+91 (0) 800-LUX-LACE'); @endphp
                                    <span class="text-slate-500">{{ $supPhone }} (Mon–Sat, 10am–7pm IST)</span>
                                </div>
                            </div>

                            <div class="flex items-start gap-3.5">
                                <div class="w-8 h-8 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                </div>
                                <div>
                                    <div class="font-bold text-slate-800">100% Confidential</div>
                                    <span class="text-slate-500">Your sizing notes and correspondence are never shared.</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- FAQ quick prompt -->
                    <div class="bg-rose-950 text-white rounded-3xl p-6 text-xs space-y-2">
                        <div class="font-bold uppercase tracking-wider text-rose-300">Quick Answers</div>
                        <p class="text-rose-100 font-light leading-relaxed">
                            Looking for immediate help regarding discreet packaging or bra size conversions?
                        </p>
                        <div class="pt-2 flex gap-4">
                            <a href="{{ route('faq') }}" class="text-white underline font-semibold hover:text-rose-200">View FAQs ↗</a>
                            <a href="{{ route('sizeGuide') }}" class="text-white underline font-semibold hover:text-rose-200">Size Calculator ↗</a>
                        </div>
                    </div>
                </div>

                <!-- Interactive Form -->
                <div class="lg:col-span-7">
                    <div class="bg-white rounded-3xl p-8 sm:p-10 border border-slate-200/80 shadow-xs relative">
                        <form id="contactForm" onsubmit="handleContactSubmit(event)" class="space-y-5">
                            @csrf
                            <!-- Bot Honeypot (must stay blank) -->
                            <input type="text" name="hp_field" style="display:none !important;" tabindex="-1" autocomplete="off">
                            <input type="hidden" name="form_time" value="{{ time() }}">

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Your Name <span class="text-rose-500">*</span></label>
                                    <input type="text" name="name" required placeholder="Elena Vance" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-xs focus:ring-2 focus:ring-slate-900/10 focus:border-slate-900 outline-none transition">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Email Address <span class="text-rose-500">*</span></label>
                                    <input type="email" name="email" required placeholder="elena@example.com" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-xs focus:ring-2 focus:ring-slate-900/10 focus:border-slate-900 outline-none transition">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Phone Number</label>
                                    <input type="tel" name="phone" placeholder="+91 98765 43210" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-xs focus:ring-2 focus:ring-slate-900/10 focus:border-slate-900 outline-none transition">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Inquiry Topic</label>
                                    <select name="subject" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-xs focus:ring-2 focus:ring-slate-900/10 focus:border-slate-900 outline-none transition bg-white">
                                        <option value="Sizing & Fit Advice">Sizing &amp; Fit Advice</option>
                                        <option value="Existing Order Status">Existing Order Status</option>
                                        <option value="Discreet Shipping & Delivery">Discreet Shipping &amp; Delivery</option>
                                        <option value="Bridal Trousseau Consultation">Bridal Trousseau Consultation</option>
                                        <option value="Return or Exchange">Return or Exchange</option>
                                        <option value="General Inquiry">General Inquiry</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Your Message <span class="text-rose-500">*</span></label>
                                <textarea name="message" rows="5" required placeholder="Please describe your sizing inquiry or question in detail..." class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-xs focus:ring-2 focus:ring-slate-900/10 focus:border-slate-900 outline-none transition"></textarea>
                            </div>

                            <div id="contactFormStatus" class="hidden p-3 rounded-xl text-xs font-medium"></div>

                            <button type="submit" id="contactSubmitBtn" class="w-full py-3.5 bg-slate-900 hover:bg-rose-950 text-white rounded-xl text-xs font-bold uppercase tracking-widest transition shadow-sm flex items-center justify-center gap-2">
                                <span>Send Confidential Message</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        async function handleContactSubmit(e) {
            e.preventDefault();
            const form = document.getElementById('contactForm');
            const btn = document.getElementById('contactSubmitBtn');
            const statusDiv = document.getElementById('contactFormStatus');
            const formData = new FormData(form);

            btn.disabled = true;
            btn.innerHTML = '<span>Transmitting...</span>';
            statusDiv.className = 'hidden';

            try {
                const res = await fetch("{{ route('contact.submit') }}", {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await res.json();

                if (res.ok && data.success) {
                    statusDiv.className = 'p-4 rounded-xl text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200';
                    statusDiv.innerHTML = '✓ ' + (data.message || 'Thank you. Your message has been safely received by our concierge.');
                    form.reset();
                } else {
                    statusDiv.className = 'p-4 rounded-xl text-xs font-semibold bg-rose-50 text-rose-800 border border-rose-200';
                    statusDiv.innerHTML = '✕ ' + (data.message || 'Please check your information and try again.');
                }
            } catch (err) {
                statusDiv.className = 'p-4 rounded-xl text-xs font-semibold bg-rose-50 text-rose-800 border border-rose-200';
                statusDiv.innerHTML = '✕ Network error. Please try again or email us directly.';
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<span>Send Confidential Message</span>';
            }
        }
    </script>
</x-storefront-layout>
