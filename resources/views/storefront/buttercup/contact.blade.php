{{-- resources/views/storefront/buttercup/contact.blade.php --}}
<x-storefront-layout :theme="$theme" title="Contact Us — Buttercup Care" metaDesc="Reach out to Buttercup customer care for sizing questions, returns, or order inquiries.">

<div class="py-12 lg:py-20" style="background:#FFFAF5">
  <div class="max-w-7xl mx-auto px-6 lg:px-12">

    {{-- Breadcrumb --}}
    <nav class="text-xs flex items-center gap-2 mb-8 text-[#4A3B3A]/40 font-body">
      <a href="{{ route('home') }}" class="hover:text-[#B97680] transition-colors">Home</a>
      <span>/</span>
      <span class="text-[#4A3B3A] font-medium">Contact Us</span>
    </nav>

    {{-- Header --}}
    <div class="text-center max-w-2xl mx-auto mb-16 reveal">
      <p class="text-[#7F9272] text-xs font-bold uppercase tracking-widest mb-2 font-body">Customer Care</p>
      <h1 class="font-display text-4xl lg:text-5xl text-[#4A3B3A] leading-tight">We're Here to Help</h1>
      <p class="font-body text-[#4A3B3A]/60 text-sm mt-3 leading-relaxed">
        Have questions about sizing, care instructions, or your order? We reply with warmth within one business day.
      </p>
      <div class="flex justify-center mt-4">
        <div class="w-32 h-0.5 bg-gradient-to-r from-transparent via-[#D89AA0] to-transparent"></div>
      </div>
    </div>

    @if(session('success'))
      <div class="max-w-4xl mx-auto mb-8 p-4 rounded-2xl bg-[#FBE7E4] border border-[#D89AA0] text-[#4A3B3A] font-body text-xs flex items-center gap-3">
        <svg class="w-5 h-5 text-[#B97680] shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        <span>{{ session('success') }}</span>
      </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start mb-16">

      {{-- Left: Customer care channels --}}
      <div class="lg:col-span-5 space-y-6 reveal">
        <div class="bg-[#FBE7E4]/60 p-8 rounded-[28px] border border-[#D89AA0]/30 shadow-sm space-y-6">
          <div>
            <p class="text-[#7F9272] text-xs font-bold uppercase tracking-widest font-body mb-1">Friendly Support</p>
            <h2 class="font-display text-2xl text-[#4A3B3A]">Buttercup Concierge</h2>
            <p class="font-body text-[#4A3B3A]/70 text-xs mt-2 leading-relaxed">
              Our team of fit specialists is dedicated to making you feel comfortable every step of the way.
            </p>
          </div>

          <div class="space-y-4 pt-4 border-t border-[#D89AA0]/20 font-body text-xs">
            <div class="flex items-start gap-3.5">
              <div class="w-9 h-9 rounded-full bg-[#D89AA0]/20 text-[#B97680] flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
              </div>
              <div>
                <div class="font-bold text-[#4A3B3A]">Email Us</div>
                @php $supportEmail = \App\Models\Setting::get('support_email', 'care@buttercup.com'); @endphp
                <a href="mailto:{{ $supportEmail }}" class="text-[#4A3B3A]/70 hover:text-[#B97680] transition">{{ $supportEmail }}</a>
              </div>
            </div>

            <div class="flex items-start gap-3.5">
              <div class="w-9 h-9 rounded-full bg-[#7F9272]/20 text-[#7F9272] flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
              </div>
              <div>
                <div class="font-bold text-[#4A3B3A]">Call &amp; WhatsApp</div>
                @php $supportPhone = \App\Models\Setting::get('support_phone', '+91 (0) 800-BUTTER-CUP'); @endphp
                <span class="text-[#4A3B3A]/70">{{ $supportPhone }} (Mon–Fri, 9am–6pm IST)</span>
              </div>
            </div>

            <div class="flex items-start gap-3.5">
              <div class="w-9 h-9 rounded-full bg-[#D89AA0]/20 text-[#B97680] flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
              </div>
              <div>
                <div class="font-bold text-[#4A3B3A]">Private &amp; Safe</div>
                <span class="text-[#4A3B3A]/70">All inquiries and purchases arrive in 100% discrete plain boxes.</span>
              </div>
            </div>
          </div>
        </div>

        {{-- Quick links banner --}}
        <div class="bg-[#FBE7E4]/40 p-6 rounded-[24px] border border-[#D89AA0]/30 font-body text-xs space-y-2">
          <p class="font-display text-[#B97680] text-sm font-bold">Frequently Asked Questions</p>
          <p class="text-[#4A3B3A]/70 leading-relaxed">Need instant help with bra size conversions or shipping?</p>
          <div class="pt-2 flex gap-4">
            <a href="{{ route('faq') }}" class="text-[#B97680] hover:text-[#4A3B3A] font-bold">View FAQs &rarr;</a>
            <a href="{{ route('sizeGuide') }}" class="text-[#B97680] hover:text-[#4A3B3A] font-bold">Size Guide &rarr;</a>
          </div>
        </div>
      </div>

      {{-- Right: Contact Form --}}
      <div class="lg:col-span-7 reveal">
        <div class="bg-white rounded-[28px] p-8 lg:p-10 border border-[#D89AA0]/30 shadow-md">
          <form action="{{ route('contact.submit') }}" method="POST" class="space-y-5">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-bold text-[#4A3B3A] uppercase tracking-wider mb-1.5 font-body">Your Name <span class="text-[#B97680]">*</span></label>
                <input type="text" name="name" required placeholder="Full Name"
                       class="w-full border border-[#D89AA0]/40 rounded-2xl px-4 py-3 text-xs focus:ring-2 focus:ring-[#D89AA0]/20 focus:border-[#D89AA0] outline-none transition font-body text-[#4A3B3A] bg-[#FFFAF5]">
              </div>
              <div>
                <label class="block text-xs font-bold text-[#4A3B3A] uppercase tracking-wider mb-1.5 font-body">Email Address <span class="text-[#B97680]">*</span></label>
                <input type="email" name="email" required placeholder="email@domain.com"
                       class="w-full border border-[#D89AA0]/40 rounded-2xl px-4 py-3 text-xs focus:ring-2 focus:ring-[#D89AA0]/20 focus:border-[#D89AA0] outline-none transition font-body text-[#4A3B3A] bg-[#FFFAF5]">
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-bold text-[#4A3B3A] uppercase tracking-wider mb-1.5 font-body">Phone Number</label>
                <input type="tel" name="phone" placeholder="+91 98765 43210"
                       class="w-full border border-[#D89AA0]/40 rounded-2xl px-4 py-3 text-xs focus:ring-2 focus:ring-[#D89AA0]/20 focus:border-[#D89AA0] outline-none transition font-body text-[#4A3B3A] bg-[#FFFAF5]">
              </div>
              <div>
                <label class="block text-xs font-bold text-[#4A3B3A] uppercase tracking-wider mb-1.5 font-body">Inquiry Subject</label>
                <select name="subject" class="w-full border border-[#D89AA0]/40 rounded-2xl px-4 py-3 text-xs focus:ring-2 focus:ring-[#D89AA0]/20 focus:border-[#D89AA0] outline-none transition font-body text-[#4A3B3A] bg-white">
                  <option value="Sizing & Fit Advice">Sizing &amp; Fit Advice</option>
                  <option value="Existing Order Status">Existing Order Status</option>
                  <option value="Returns & Exchanges">Returns &amp; Exchanges</option>
                  <option value="Fabric Care Tips">Fabric Care Tips</option>
                  <option value="General Inquiry">General Inquiry</option>
                </select>
              </div>
            </div>

            <div>
              <label class="block text-xs font-bold text-[#4A3B3A] uppercase tracking-wider mb-1.5 font-body">Your Message <span class="text-[#B97680]">*</span></label>
              <textarea name="message" rows="5" required placeholder="How can we help make you more comfortable?"
                        class="w-full border border-[#D89AA0]/40 rounded-2xl px-4 py-3 text-xs focus:ring-2 focus:ring-[#D89AA0]/20 focus:border-[#D89AA0] outline-none transition font-body text-[#4A3B3A] bg-[#FFFAF5] resize-none"></textarea>
            </div>

            <button type="submit" class="w-full py-4 text-xs font-bold uppercase tracking-widest transition-all bg-[#D89AA0] text-[#FFFAF5] hover:bg-[#B97680] rounded-full shadow-sm">
              Send Message 🌸
            </button>
          </form>
        </div>
      </div>

    </div>

  </div>
</div>

</x-storefront-layout>
