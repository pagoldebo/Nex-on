{{-- resources/views/storefront/organic/contact.blade.php
     Organic Market Contact Page: Farm Depot Helpline, Address, and Interactive Contact Form. --}}
<x-storefront-layout :theme="$theme" title="Contact Our Organic Farm Support">

  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-16">
    
    {{-- Breadcrumbs --}}
    <nav class="text-xs text-stone-500 font-medium flex items-center gap-2 mb-8">
      <a href="{{ route('home') }}" class="hover:text-organic-600 transition">Home</a>
      <span>/</span>
      <span class="text-stone-900 font-bold">Contact Us</span>
    </nav>

    {{-- Header --}}
    <div class="text-center max-w-2xl mx-auto mb-16 reveal">
      <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold tracking-wider uppercase bg-organic-100 text-organic-700 border border-organic-300 mb-3">
        🌱 We're Here For You
      </span>
      <h1 class="text-3xl sm:text-4xl font-black text-stone-900 font-display">
        Get in Touch with Our Farm Team
      </h1>
      <p class="mt-3 text-xs sm:text-sm text-stone-600 font-light leading-relaxed">
        Have questions about harvest schedules, cold-chain delivery, bulk household subscriptions, or farm visits? Our team replies promptly.
      </p>
    </div>

    @if(session('success'))
      <div class="max-w-4xl mx-auto mb-8 p-4 rounded-2xl bg-organic-50 border border-organic-200 text-organic-800 text-xs font-bold flex items-center gap-2">
        <span class="text-base">✓</span>
        <span>{{ session('success') }}</span>
      </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-start">
      
      {{-- LEFT: Contact Info --}}
      <div class="lg:col-span-5 space-y-6">
        <div class="bg-white rounded-3xl border border-stone-200 p-6 sm:p-8 shadow-xs space-y-6">
          <div>
            <span class="text-xs font-bold uppercase tracking-wider text-organic-600 block mb-1">Direct Help</span>
            <h3 class="text-xl font-black text-stone-900 font-display">Customer Concierge</h3>
            <p class="text-xs text-stone-500 mt-1 font-light">
              Speak directly with our farm specialists and customer care representatives.
            </p>
          </div>

          <div class="space-y-4 pt-4 border-t border-stone-100 text-xs">
            <div class="flex items-start gap-3.5">
              <div class="w-10 h-10 rounded-xl bg-organic-50 text-organic-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
              </div>
              <div>
                @php
                  $contactPhone = $organicCustomerCare ?? \App\Models\Setting::get('organic_customer_care', \App\Models\Setting::supportPhone());
                  $contactTel = preg_replace('/[^0-9+]/', '', $contactPhone);
                  $contactEmail = $organicSupportEmail ?? \App\Models\Setting::get('organic_support_email', \App\Models\Setting::supportEmail());
                @endphp
                <p class="font-bold text-stone-900">Customer Helpline</p>
                <a href="tel:{{ $contactTel }}" class="text-organic-700 font-bold hover:underline block mt-0.5">{{ $contactPhone }}</a>
                <span class="text-[10px] text-stone-400">Monday – Saturday: 8 AM to 7 PM</span>
              </div>
            </div>

            <div class="flex items-start gap-3.5">
              <div class="w-10 h-10 rounded-xl bg-organic-50 text-organic-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
              </div>
              <div>
                <p class="font-bold text-stone-900">Email Inquiries</p>
                <a href="mailto:{{ $contactEmail }}" class="text-organic-700 font-semibold hover:underline">{{ $contactEmail }}</a>
                <span class="text-[10px] text-stone-400 block">We respond within 4 business hours</span>
              </div>
            </div>

            <div class="flex items-start gap-3.5">
              <div class="w-10 h-10 rounded-xl bg-organic-50 text-organic-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
              </div>
              <div>
                <p class="font-bold text-stone-900">Central Farm Distribution Hub</p>
                <p class="text-stone-600 mt-0.5 leading-relaxed">
                  Phase 2 Agro Cold Chain Logistics, Bandra West, Mumbai, Maharashtra 400050
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- RIGHT: Contact Form --}}
      <div class="lg:col-span-7 bg-white rounded-3xl border border-stone-200 p-6 sm:p-8 shadow-xs">
        <h3 class="text-xl font-black text-stone-900 font-display mb-6">Send Us a Message</h3>

        <form method="POST" action="{{ route('contact.submit') }}" class="space-y-4">
          @csrf

          {{-- Anti-spam honeypot hidden fields --}}
          <div style="display:none !important;" aria-hidden="true">
            <input type="text" name="website" tabindex="-1" autocomplete="off">
            <input type="text" name="phone_number" tabindex="-1" autocomplete="off">
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label for="cName" class="block text-xs font-bold text-stone-700 uppercase mb-1">Your Full Name</label>
              <input type="text" id="cName" name="name" required placeholder="e.g. Priyanshu Sharma" class="w-full bg-stone-50 border border-stone-200 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-stone-800 focus:outline-none focus:border-organic-500">
            </div>
            <div>
              <label for="cPhone" class="block text-xs font-bold text-stone-700 uppercase mb-1">Phone Number</label>
              <input type="tel" id="cPhone" name="phone" placeholder="e.g. 9876543210" class="w-full bg-stone-50 border border-stone-200 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-stone-800 focus:outline-none focus:border-organic-500">
            </div>
          </div>

          <div>
            <label for="cEmail" class="block text-xs font-bold text-stone-700 uppercase mb-1">Email Address</label>
            <input type="email" id="cEmail" name="email" required placeholder="name@example.com" class="w-full bg-stone-50 border border-stone-200 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-stone-800 focus:outline-none focus:border-organic-500">
          </div>

          <div>
            <label for="cSubject" class="block text-xs font-bold text-stone-700 uppercase mb-1">Subject</label>
            <input type="text" id="cSubject" name="subject" required placeholder="e.g. Farm Delivery Question / B2B Subscription" class="w-full bg-stone-50 border border-stone-200 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-stone-800 focus:outline-none focus:border-organic-500">
          </div>

          <div>
            <label for="cMessage" class="block text-xs font-bold text-stone-700 uppercase mb-1">Message</label>
            <textarea id="cMessage" name="message" rows="5" required placeholder="How can our harvest team help you?" class="w-full bg-stone-50 border border-stone-200 rounded-xl p-4 text-xs sm:text-sm text-stone-800 focus:outline-none focus:border-organic-500"></textarea>
          </div>

          <div class="pt-2">
            <button type="submit" class="btn-organic px-8 py-3.5 rounded-xl text-xs font-bold uppercase tracking-wider shadow-md">
              Send Message
            </button>
          </div>
        </form>
      </div>

    </div>

  </div>

</x-storefront-layout>

