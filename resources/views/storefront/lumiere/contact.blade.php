{{-- resources/views/storefront/lumiere/contact.blade.php --}}
<x-storefront-layout :theme="$theme" title="Concierge &amp; Customer Care — Lumière" metaDesc="Reach out to the Lumière concierge for personal styling consultations, sizing guidance, or order status.">

<div class="py-12 lg:py-20 bg-white">
  <div class="max-w-7xl mx-auto px-6 md:px-12">

    {{-- Breadcrumb --}}
    <nav class="text-xs flex items-center gap-2 mb-8 text-gray-400">
      <a href="{{ route('home') }}" class="hover:text-pink-600 transition-colors">Home</a>
      <span>/</span>
      <span class="text-gray-900 font-medium">Concierge</span>
    </nav>

    {{-- Header --}}
    <div class="text-center max-w-2xl mx-auto mb-16 reveal">
      <p class="text-pink-600 text-[10px] font-black uppercase tracking-widest mb-2">Concierge Care</p>
      <h1 class="font-display italic text-4xl lg:text-5xl text-gray-900 leading-tight">We'd Love to Hear From You</h1>
      <p class="text-gray-500 text-sm mt-3 leading-relaxed">
        Whether you desire bespoke silk trousseau recommendations or order assistance, our stylists reply within one business day.
      </p>
      <div class="flex justify-center mt-4">
        <div class="w-32 h-0.5 bg-gradient-to-r from-transparent via-pink-300 to-transparent"></div>
      </div>
    </div>

    @if(session('success'))
      <div class="max-w-4xl mx-auto mb-8 p-4 rounded-2xl bg-pink-50 border border-pink-200 text-pink-700 text-xs flex items-center gap-3">
        <svg class="w-5 h-5 text-pink-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        <span>{{ session('success') }}</span>
      </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start mb-16">

      {{-- Left: Concierge channels --}}
      <div class="lg:col-span-5 space-y-6 reveal">
        <div class="bg-[#FFF0F3] p-8 rounded-[32px] border border-pink-100 shadow-sm space-y-6">
          <div>
            <p class="text-pink-600 text-[10px] font-black uppercase tracking-widest mb-1">Direct Assistance</p>
            <h2 class="font-display italic text-2xl text-gray-900">Personal Styling</h2>
            <p class="text-gray-600 text-xs mt-2 leading-relaxed">
              Our atelier stylists are at your disposal for fit guidance and silk consultations.
            </p>
          </div>

          <div class="space-y-4 pt-4 border-t border-pink-200/60 text-xs text-gray-700">
            <div class="flex items-start gap-3.5">
              <div class="w-9 h-9 rounded-full bg-white text-pink-600 flex items-center justify-center shrink-0 shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
              </div>
              <div>
                <div class="font-bold text-gray-900">Email Inquiries</div>
                @php $supportEmail = \App\Models\Setting::get('support_email', 'concierge@lumiere.com'); @endphp
                <a href="mailto:{{ $supportEmail }}" class="text-gray-600 hover:text-pink-600 transition">{{ $supportEmail }}</a>
              </div>
            </div>

            <div class="flex items-start gap-3.5">
              <div class="w-9 h-9 rounded-full bg-white text-pink-600 flex items-center justify-center shrink-0 shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
              </div>
              <div>
                <div class="font-bold text-gray-900">Concierge Desk</div>
                @php $supportPhone = \App\Models\Setting::get('support_phone', '+91 (0) 800-LUMIERE'); @endphp
                <span class="text-gray-600">{{ $supportPhone }} (Mon–Sat, 10am–7pm IST)</span>
              </div>
            </div>

            <div class="flex items-start gap-3.5">
              <div class="w-9 h-9 rounded-full bg-white text-pink-600 flex items-center justify-center shrink-0 shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
              </div>
              <div>
                <div class="font-bold text-gray-900">Discreet Delivery</div>
                <span class="text-gray-600">All orders arrive in unmarked satin-finish boxes with zero outer brand markings.</span>
              </div>
            </div>
          </div>
        </div>

        {{-- Quick links banner --}}
        <div class="bg-[#FFF0F3]/60 p-6 rounded-[28px] border border-pink-100 text-xs space-y-2">
          <p class="font-display italic text-gray-900 text-sm font-bold">Frequently Asked Questions</p>
          <p class="text-gray-500 leading-relaxed">Need instant help with silk care or size charts?</p>
          <div class="pt-2 flex gap-4">
            <a href="{{ route('faq') }}" class="text-pink-600 hover:text-gray-900 font-bold">View FAQs &rarr;</a>
            <a href="{{ route('sizeGuide') }}" class="text-pink-600 hover:text-gray-900 font-bold">Size Guide &rarr;</a>
          </div>
        </div>
      </div>

      {{-- Right: Contact Form --}}
      <div class="lg:col-span-7 reveal">
        <div class="bg-white rounded-[32px] p-8 lg:p-10 border border-pink-100 shadow-lg">
          <form action="{{ route('contact.submit') }}" method="POST" class="space-y-5">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Your Name <span class="text-pink-600">*</span></label>
                <input type="text" name="name" required placeholder="Full Name"
                       class="w-full border border-pink-100 rounded-2xl px-4 py-3 text-xs focus:ring-2 focus:ring-pink-200 focus:border-pink-400 outline-none transition bg-[#FFF0F3]/30">
              </div>
              <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Email Address <span class="text-pink-600">*</span></label>
                <input type="email" name="email" required placeholder="email@domain.com"
                       class="w-full border border-pink-100 rounded-2xl px-4 py-3 text-xs focus:ring-2 focus:ring-pink-200 focus:border-pink-400 outline-none transition bg-[#FFF0F3]/30">
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Phone Number</label>
                <input type="tel" name="phone" placeholder="+91 98765 43210"
                       class="w-full border border-pink-100 rounded-2xl px-4 py-3 text-xs focus:ring-2 focus:ring-pink-200 focus:border-pink-400 outline-none transition bg-[#FFF0F3]/30">
              </div>
              <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Inquiry Subject</label>
                <select name="subject" class="w-full border border-pink-100 rounded-2xl px-4 py-3 text-xs focus:ring-2 focus:ring-pink-200 focus:border-pink-400 outline-none transition bg-white">
                  <option value="Sizing & Fit Advice">Sizing &amp; Fit Advice</option>
                  <option value="Silk Consultation">Silk Consultation</option>
                  <option value="Existing Order Status">Existing Order Status</option>
                  <option value="Discreet Shipping">Discreet Shipping</option>
                  <option value="General Inquiry">General Inquiry</option>
                </select>
              </div>
            </div>

            <div>
              <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Your Message <span class="text-pink-600">*</span></label>
              <textarea name="message" rows="5" required placeholder="How may our stylists assist you?"
                        class="w-full border border-pink-100 rounded-2xl px-4 py-3 text-xs focus:ring-2 focus:ring-pink-200 focus:border-pink-400 outline-none transition bg-[#FFF0F3]/30 resize-none"></textarea>
            </div>

            <button type="submit" class="w-full py-4 text-xs font-bold uppercase tracking-widest transition-all bg-[#405549] text-white hover:bg-pink-600 rounded-2xl shadow-md">
              Send Message ✨
            </button>
          </form>
        </div>
      </div>

    </div>

  </div>
</div>

</x-storefront-layout>
