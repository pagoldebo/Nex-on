{{-- resources/views/storefront/vyra/contact.blade.php --}}
<x-storefront-layout :theme="$theme" title="Contact Concierge — Vyra" metaDesc="Reach out to the Vyra styling team for sizing guidance, order status, or bespoke inquiries.">

<div class="py-12 lg:py-20" style="background:#0E0E10">
  <div class="max-w-7xl mx-auto px-6 lg:px-12">

    {{-- Breadcrumb --}}
    <nav class="text-xs flex items-center gap-2 mb-8 text-[#F2F0EC]/40 font-body">
      <a href="{{ route('home') }}" class="hover:text-[#D4FF3D] transition-colors">Home</a>
      <span>/</span>
      <span class="text-[#F2F0EC] font-medium">Contact</span>
    </nav>

    {{-- Header --}}
    <div class="text-center max-w-2xl mx-auto mb-16 reveal">
      <p class="text-[#D4FF3D] text-xs font-bold uppercase tracking-widest mb-2 font-mono">Direct Line</p>
      <h1 class="font-display uppercase text-4xl lg:text-5xl text-[#F2F0EC] leading-tight">Let's Talk</h1>
      <p class="font-body text-[#F2F0EC]/60 text-sm mt-3 leading-relaxed">
        Got questions about fit, size conversions, or your recent drop order? Reach our styling crew directly.
      </p>
      <div class="flex justify-center mt-4">
        <div class="w-32 h-0.5 bg-gradient-to-r from-transparent via-[#FF3D5A] to-transparent"></div>
      </div>
    </div>

    @if(session('success'))
      <div class="max-w-4xl mx-auto mb-8 p-4 bg-[#1A1A1D] border border-[#D4FF3D] text-[#D4FF3D] font-mono text-xs flex items-center gap-3">
        <svg class="w-5 h-5 text-[#D4FF3D] shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        <span>{{ session('success') }}</span>
      </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start mb-16">

      {{-- Left: Support channels --}}
      <div class="lg:col-span-5 space-y-6 reveal">
        <div class="bg-[#1A1A1D] p-8 border border-[#FF3D5A]/30 shadow-xl space-y-6">
          <div>
            <p class="text-[#D4FF3D] text-xs font-bold uppercase tracking-widest font-mono mb-1">Direct Assistance</p>
            <h2 class="font-display uppercase text-2xl text-[#F2F0EC]">Bespoke Support</h2>
            <p class="font-body text-[#F2F0EC]/60 text-xs mt-2 leading-relaxed">
              We respond to all styling inquiries within one business day.
            </p>
          </div>

          <div class="space-y-4 pt-4 border-t border-[#F2F0EC]/10 font-body text-xs">
            <div class="flex items-start gap-3.5">
              <div class="w-9 h-9 bg-[#0E0E10] border border-[#FF3D5A] text-[#FF3D5A] flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
              </div>
              <div>
                <div class="font-bold text-[#F2F0EC]">Email Inquiries</div>
                @php $supportEmail = \App\Models\Setting::get('support_email', 'concierge@vyra.luxury'); @endphp
                <a href="mailto:{{ $supportEmail }}" class="text-[#F2F0EC]/70 hover:text-[#D4FF3D] transition">{{ $supportEmail }}</a>
              </div>
            </div>

            <div class="flex items-start gap-3.5">
              <div class="w-9 h-9 bg-[#0E0E10] border border-[#D4FF3D] text-[#D4FF3D] flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
              </div>
              <div>
                <div class="font-bold text-[#F2F0EC]">Phone &amp; WhatsApp</div>
                @php $supportPhone = \App\Models\Setting::get('support_phone', '+91 (0) 800-VYRA-HOT'); @endphp
                <span class="text-[#F2F0EC]/70">{{ $supportPhone }} (Mon–Sat, 10am–7pm IST)</span>
              </div>
            </div>

            <div class="flex items-start gap-3.5">
              <div class="w-9 h-9 bg-[#0E0E10] border border-[#F2F0EC]/20 text-[#F2F0EC] flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
              </div>
              <div>
                <div class="font-bold text-[#F2F0EC]">Stealth &amp; Confidential</div>
                <span class="text-[#F2F0EC]/70">Your sizing notes and orders remain 100% confidential.</span>
              </div>
            </div>
          </div>
        </div>

        {{-- Quick links banner --}}
        <div class="bg-[#1A1A1D] p-6 border border-[#D4FF3D]/30 font-body text-xs space-y-2">
          <p class="font-display uppercase text-[#D4FF3D] text-sm">Need Fast Answers?</p>
          <p class="text-[#F2F0EC]/70 leading-relaxed">Check our sizing tables and delivery timeframes.</p>
          <div class="pt-2 flex gap-4">
            <a href="{{ route('faq') }}" class="text-[#FF3D5A] hover:text-[#D4FF3D] font-bold">View FAQs &rarr;</a>
            <a href="{{ route('sizeGuide') }}" class="text-[#FF3D5A] hover:text-[#D4FF3D] font-bold">Size Calculator &rarr;</a>
          </div>
        </div>
      </div>

      {{-- Right: Contact Form --}}
      <div class="lg:col-span-7 reveal">
        <div class="bg-[#1A1A1D] p-8 lg:p-10 border border-[#FF3D5A]/40 shadow-2xl">
          <form action="{{ route('contact.submit') }}" method="POST" class="space-y-5">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-bold text-[#F2F0EC] uppercase tracking-wider mb-1.5 font-body">Your Name <span class="text-[#FF3D5A]">*</span></label>
                <input type="text" name="name" required placeholder="Full Name"
                       class="w-full border border-[#FF3D5A]/30 bg-transparent px-4 py-3 text-xs focus:border-[#D4FF3D] outline-none transition font-body text-[#F2F0EC]">
              </div>
              <div>
                <label class="block text-xs font-bold text-[#F2F0EC] uppercase tracking-wider mb-1.5 font-body">Email Address <span class="text-[#FF3D5A]">*</span></label>
                <input type="email" name="email" required placeholder="email@domain.com"
                       class="w-full border border-[#FF3D5A]/30 bg-transparent px-4 py-3 text-xs focus:border-[#D4FF3D] outline-none transition font-body text-[#F2F0EC]">
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-bold text-[#F2F0EC] uppercase tracking-wider mb-1.5 font-body">Phone Number</label>
                <input type="tel" name="phone" placeholder="+91 98765 43210"
                       class="w-full border border-[#FF3D5A]/30 bg-transparent px-4 py-3 text-xs focus:border-[#D4FF3D] outline-none transition font-body text-[#F2F0EC]">
              </div>
              <div>
                <label class="block text-xs font-bold text-[#F2F0EC] uppercase tracking-wider mb-1.5 font-body">Inquiry Subject</label>
                <select name="subject" class="w-full border border-[#FF3D5A]/30 bg-[#0E0E10] px-4 py-3 text-xs focus:border-[#D4FF3D] outline-none transition font-body text-[#F2F0EC]">
                  <option value="Sizing & Fit Advice">Sizing &amp; Fit Advice</option>
                  <option value="Drop 03 Inquiries">Drop 03 Inquiries</option>
                  <option value="Existing Order Status">Existing Order Status</option>
                  <option value="Stealth Shipping & Delivery">Stealth Shipping &amp; Delivery</option>
                  <option value="General Inquiry">General Inquiry</option>
                </select>
              </div>
            </div>

            <div>
              <label class="block text-xs font-bold text-[#F2F0EC] uppercase tracking-wider mb-1.5 font-body">Your Message <span class="text-[#FF3D5A]">*</span></label>
              <textarea name="message" rows="5" required placeholder="Your message…"
                        class="w-full border border-[#FF3D5A]/30 bg-transparent px-4 py-3 text-xs focus:border-[#D4FF3D] outline-none transition font-body text-[#F2F0EC] resize-none"></textarea>
            </div>

            <button type="submit" class="w-full py-4 text-xs font-bold uppercase tracking-widest transition-all bg-[#FF3D5A] text-[#0E0E10] hover:bg-[#D4FF3D]">
              Send Message
            </button>
          </form>
        </div>
      </div>

    </div>

  </div>
</div>

</x-storefront-layout>
