{{--
    ======================================================================
    THEME: SAAYA — Royal Indian Trousseau & Heritage Silk
    PAGE: CONCIERGE & CUSTOMER SUPPORT CONTACT
    ======================================================================
    File: resources/views/storefront/saaya/contact.blade.php
    Controller: App\Http\Controllers\StorefrontController@contact
    Route Context: GET /contact, POST /contact/send
    ViewModel Injections:
      - $theme: Active Template model ('saaya')
    Responsibility: Customer concierge inquiry portal, store locations, and helpline
    Features:
      - Interactive inquiry form (Name, Email, Phone, Order #, Message)
      - Direct WhatsApp concierge click-to-chat button
      - Physical boutique/depot address, telephone lines, and operating hours
      - Embedded interactive Google Map location
    Operations & Maintenance:
      - Form submissions protected by CSRF token and anti-spam rate limits
--}}
<x-storefront-layout :theme="$theme" title="Contact Concierge — Saaya" metaDesc="Reach out to the Saaya personal styling concierge for fit consultations, bespoke trousseau assistance, or order inquiries.">

<div class="bg-ivory py-10 lg:py-16">
  <div class="max-w-7xl mx-auto px-6 lg:px-12">

    {{-- Breadcrumb --}}
    <nav class="text-xs flex items-center gap-2 mb-8 text-maroon/50 font-body">
      <a href="{{ route('home') }}" class="hover:text-gold transition-colors">Home</a>
      <span>/</span>
      <span class="text-maroon font-medium">Contact Concierge</span>
    </nav>

    {{-- Header --}}
    <div class="text-center max-w-2xl mx-auto mb-16 reveal">
      <p class="font-display italic text-gold text-lg lg:text-xl mb-2">Personal Concierge</p>
      <h1 class="font-display text-4xl lg:text-5xl text-maroon leading-tight">How May We Assist You?</h1>
      <p class="font-body text-maroon/70 text-sm mt-3 leading-relaxed">
        Whether you require confidential sizing advice, bespoke bridal guidance, or order status assistance, our private stylists are at your service.
      </p>
      <div class="flex justify-center mt-4">
        <div class="petal-divider w-32 opacity-60"></div>
      </div>
    </div>

    @if(session('success'))
      <div class="max-w-4xl mx-auto mb-8 p-4 rounded-xl bg-gold/15 border border-gold/40 text-maroon font-body text-xs flex items-center gap-3">
        <svg class="w-5 h-5 text-gold shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        <span>{{ session('success') }}</span>
      </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start mb-16">

      {{-- Left: Concierge channels --}}
      <div class="lg:col-span-5 space-y-6 reveal">
        <div class="bg-maroon-dark text-ivory p-8 rounded-2xl border border-gold/30 shadow-xl space-y-6">
          <div>
            <p class="font-display italic text-gold text-lg mb-1">Direct Assistance</p>
            <h2 class="font-display text-2xl text-ivory">Bespoke Support</h2>
            <p class="font-body text-ivory/60 text-xs mt-2 leading-relaxed">
              Our private stylists respond to all inquiries within one business day.
            </p>
          </div>

          <div class="space-y-4 pt-4 border-t border-gold/20 font-body text-xs">
            <div class="flex items-start gap-3.5">
              <div class="w-9 h-9 rounded-full bg-gold/20 text-gold flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
              </div>
              <div>
                <div class="font-bold text-ivory">Email Inquiries</div>
                @php $supportEmail = \App\Models\Setting::supportEmail(); @endphp
                <a href="mailto:{{ $supportEmail }}" class="text-ivory/70 hover:text-gold transition">{{ $supportEmail }}</a>
              </div>
            </div>

            <div class="flex items-start gap-3.5">
              <div class="w-9 h-9 rounded-full bg-gold/20 text-gold flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
              </div>
              <div>
                <div class="font-bold text-ivory">WhatsApp &amp; Phone</div>
                @php $supportPhone = \App\Models\Setting::get('support_phone', '+91 (0) 800-SAAYA-LUX'); @endphp
                <span class="text-ivory/70">{{ $supportPhone }} (Mon–Sat, 10am–7pm IST)</span>
              </div>
            </div>

            <div class="flex items-start gap-3.5">
              <div class="w-9 h-9 rounded-full bg-gold/20 text-gold flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
              </div>
              <div>
                <div class="font-bold text-ivory">100% Confidential</div>
                <span class="text-ivory/70">Your sizing notes and correspondence are never shared.</span>
              </div>
            </div>
          </div>
        </div>

        {{-- Quick links banner --}}
        <div class="bg-maroon text-ivory p-6 rounded-2xl border border-gold/20 font-body text-xs space-y-2">
          <p class="font-display italic text-gold text-sm">Need Fast Answers?</p>
          <p class="text-ivory/70 leading-relaxed">Check our detailed fitting guides and order inquiries.</p>
          <div class="pt-2 flex gap-4">
            <a href="{{ route('faq') }}" class="text-gold underline hover:text-white font-medium">View FAQs &rarr;</a>
            <a href="{{ route('sizeGuide') }}" class="text-gold underline hover:text-white font-medium">Size Calculator &rarr;</a>
          </div>
        </div>
      </div>

      {{-- Right: Contact Form --}}
      <div class="lg:col-span-7 reveal">
        <div class="bg-white rounded-2xl p-8 lg:p-10 border border-gold/30 shadow-lg">
          <form action="{{ route('contact.submit') }}" method="POST" class="space-y-5">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-bold text-maroon uppercase tracking-wider mb-1.5 font-body">Your Name <span class="text-gold">*</span></label>
                <input type="text" name="name" required placeholder="Full Name"
                       class="w-full border border-gold/30 rounded-xl px-4 py-3 text-xs focus:ring-2 focus:ring-gold/20 focus:border-gold outline-none transition font-body text-maroon bg-ivory/30">
              </div>
              <div>
                <label class="block text-xs font-bold text-maroon uppercase tracking-wider mb-1.5 font-body">Email Address <span class="text-gold">*</span></label>
                <input type="email" name="email" required placeholder="email@domain.com"
                       class="w-full border border-gold/30 rounded-xl px-4 py-3 text-xs focus:ring-2 focus:ring-gold/20 focus:border-gold outline-none transition font-body text-maroon bg-ivory/30">
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-bold text-maroon uppercase tracking-wider mb-1.5 font-body">Phone Number</label>
                <input type="tel" name="phone" placeholder="+91 98765 43210"
                       class="w-full border border-gold/30 rounded-xl px-4 py-3 text-xs focus:ring-2 focus:ring-gold/20 focus:border-gold outline-none transition font-body text-maroon bg-ivory/30">
              </div>
              <div>
                <label class="block text-xs font-bold text-maroon uppercase tracking-wider mb-1.5 font-body">Inquiry Subject</label>
                <select name="subject" class="w-full border border-gold/30 rounded-xl px-4 py-3 text-xs focus:ring-2 focus:ring-gold/20 focus:border-gold outline-none transition font-body text-maroon bg-white">
                  <option value="Sizing & Fit Advice">Sizing &amp; Fit Advice</option>
                  <option value="Bridal Trousseau Consultation">Bridal Trousseau Consultation</option>
                  <option value="Existing Order Status">Existing Order Status</option>
                  <option value="Discreet Shipping & Delivery">Discreet Shipping &amp; Delivery</option>
                  <option value="General Inquiry">General Inquiry</option>
                </select>
              </div>
            </div>

            <div>
              <label class="block text-xs font-bold text-maroon uppercase tracking-wider mb-1.5 font-body">Your Message <span class="text-gold">*</span></label>
              <textarea name="message" rows="5" required placeholder="How may our stylists assist you?"
                        class="w-full border border-gold/30 rounded-xl px-4 py-3 text-xs focus:ring-2 focus:ring-gold/20 focus:border-gold outline-none transition font-body text-maroon bg-ivory/30 resize-none"></textarea>
            </div>

            <button type="submit" class="gold-btn w-full py-4 text-xs font-body font-semibold tracking-widest uppercase transition shadow-md">
              Send Message to Concierge
            </button>
          </form>
        </div>
      </div>

    </div>

  </div>
</div>

</x-storefront-layout>
