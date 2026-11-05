{{-- resources/views/storefront/organic/login.blade.php
     Organic Market Dedicated Customer Sign In Page --}}
<x-storefront-layout :theme="$theme" title="Customer Sign In — {{ config('app.name', 'Organic Market') }}" metaDesc="Sign in to your {{ config('app.name', 'Organic Market') }} patron account to view harvest orders, track doorstep deliveries, and manage your fresh produce wishlist.">

  <style>
    body, #mainContent {
      background-color: #F9F8F3 !important;
    }
  </style>

  <div class="py-10 sm:py-16 min-h-[calc(100vh-140px)]" style="background-color: #F9F8F3 !important;">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

      {{-- Breadcrumbs --}}
      <nav aria-label="Breadcrumb" class="mb-8 flex items-center gap-2 text-xs text-stone-500 font-medium">
        <a href="{{ route('home') }}" class="hover:text-organic-600 transition">Home</a>
        <span class="text-stone-300">/</span>
        <span class="text-stone-400">Patron Access</span>
        <span class="text-stone-300">/</span>
        <span class="text-organic-700 font-bold" aria-current="page">Sign In</span>
      </nav>

      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
        
        {{-- LEFT COLUMN: Farm Fresh Perks & Story --}}
        <div class="lg:col-span-5 space-y-6">
          <div class="text-white rounded-3xl p-8 sm:p-10 shadow-2xl border border-stone-800/80 relative overflow-hidden" style="background-color: #000000 !important;">

            <div class="relative z-10 space-y-6">
              <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-[10px] font-bold tracking-widest bg-organic-700/80 text-organic-200 border border-organic-500/40 uppercase">
                <svg class="w-3.5 h-3.5 text-organic-300" viewBox="0 0 24 24" fill="currentColor"><use href="#organic-leaf"/></svg>
                <span>Patron Circle</span>
              </div>

              <div>
                <h2 class="text-2xl sm:text-3xl font-black font-display text-white leading-tight">
                  Welcome to Pure, Clean Living.
                </h2>
                <p class="text-xs sm:text-sm text-organic-100/90 mt-2.5 leading-relaxed font-light">
                  Sign in to your member account to access subscription harvests, track fresh dispatches, and re-order farm favorites in one click.
                </p>
              </div>

              <div class="space-y-4 pt-4 border-t border-organic-700/60 text-xs">
                <div class="flex items-start gap-3">
                  <div class="w-8 h-8 rounded-xl bg-organic-700 text-organic-200 flex items-center justify-center shrink-0 border border-organic-600/50">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><use href="#organic-delivery"/></svg>
                  </div>
                  <div>
                    <h3 class="font-bold text-white text-xs">Priority Cold-Chain Dispatch</h3>
                    <p class="text-organic-200/80 text-[11px] mt-0.5 leading-relaxed">Harvested at dawn, packed in insulated crates, and delivered fresh to your kitchen.</p>
                  </div>
                </div>

                <div class="flex items-start gap-3">
                  <div class="w-8 h-8 rounded-xl bg-organic-700 text-organic-200 flex items-center justify-center shrink-0 border border-organic-600/50">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><use href="#organic-seal"/></svg>
                  </div>
                  <div>
                    <h3 class="font-bold text-white text-xs">100% Certified Organic Guarantee</h3>
                    <p class="text-organic-200/80 text-[11px] mt-0.5 leading-relaxed">Zero synthetic pesticides, non-GMO heirloom seeds, and regenerative soil practices.</p>
                  </div>
                </div>

                <div class="flex items-start gap-3">
                  <div class="w-8 h-8 rounded-xl bg-organic-700 text-organic-200 flex items-center justify-center shrink-0 border border-organic-600/50">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><use href="#organic-heart"/></svg>
                  </div>
                  <div>
                    <h3 class="font-bold text-white text-xs">Saved Harvest Wishlists</h3>
                    <p class="text-organic-200/80 text-[11px] mt-0.5 leading-relaxed">Keep your curated seasonal grocery edits ready for instant weekly checkout.</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        {{-- RIGHT COLUMN: Sign In Form --}}
        <div class="lg:col-span-7">
          <div class="bg-white rounded-3xl p-8 sm:p-10 shadow-lg border border-stone-200/70">
            <div class="mb-6">
              <span class="text-xs font-bold uppercase tracking-wider text-organic-600 block mb-1">Member Portal</span>
              <h1 class="text-2xl sm:text-3xl font-black text-stone-900 font-display">Sign In to Your Account</h1>
              <p class="text-xs sm:text-sm text-stone-500 mt-1.5">Enter your email and password below to continue.</p>
            </div>

            @if(session('status'))
              <div class="mb-6 p-4 rounded-2xl bg-organic-50 border border-organic-200 text-organic-800 text-xs font-semibold">
                {{ session('status') }}
              </div>
            @endif

            @if($errors->any())
              <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs space-y-1">
                @foreach($errors->all() as $error)
                  <p>• {{ $error }}</p>
                @endforeach
              </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
              @csrf

              <div>
                <label for="email" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">Email Address</label>
                <input id="email" 
                       type="email" 
                       name="email" 
                       value="{{ old('email') }}" 
                       required 
                       autofocus 
                       placeholder="patron@example.com"
                       class="w-full px-4 py-3 rounded-xl border border-stone-200 bg-stone-50 focus:bg-white text-stone-900 text-sm focus:outline-none focus:ring-2 focus:ring-organic-500/30 focus:border-organic-500 transition">
              </div>

              <div>
                <div class="flex items-center justify-between mb-1.5">
                  <label for="password" class="block text-xs font-bold text-stone-700 uppercase tracking-wider">Password</label>
                  @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-xs font-semibold text-organic-600 hover:text-organic-700 transition">Forgot password?</a>
                  @endif
                </div>
                <input id="password" 
                       type="password" 
                       name="password" 
                       required 
                       autocomplete="current-password"
                       placeholder="••••••••"
                       class="w-full px-4 py-3 rounded-xl border border-stone-200 bg-stone-50 focus:bg-white text-stone-900 text-sm focus:outline-none focus:ring-2 focus:ring-organic-500/30 focus:border-organic-500 transition">
              </div>

              <div class="flex items-center justify-between pt-1">
                <label class="flex items-center gap-2 cursor-pointer">
                  <input type="checkbox" name="remember" class="w-4 h-4 rounded border-stone-300 text-organic-600 focus:ring-organic-500/30">
                  <span class="text-xs text-stone-600 font-medium select-none">Remember this device</span>
                </label>
              </div>

              <div class="pt-2">
                <button type="submit" 
                        class="w-full py-3.5 px-6 rounded-full bg-organic-500 hover:bg-organic-600 text-white font-black text-sm tracking-wide shadow-md hover:shadow-lg transition flex items-center justify-center gap-2">
                  <span>Sign In to Account</span>
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </button>
              </div>

              <div class="pt-4 text-center border-t border-stone-100">
                <p class="text-xs text-stone-500">
                  Don't have a member account yet? 
                  <a href="{{ route('register') }}" class="font-bold text-organic-600 hover:text-organic-700 hover:underline transition ml-1">Create an Account</a>
                </p>
              </div>
            </form>
          </div>
        </div>

      </div>
    </div>
  </div>

</x-storefront-layout>

