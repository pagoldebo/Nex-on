<x-storefront-layout :theme="$theme ?? \App\Models\Template::active()" title="Sign In — {{ config('app.name', 'Project Nexon') }}" metaDesc="Sign in to your {{ config('app.name', 'Project Nexon') }} patron account to view order history, track bespoke shipments, and access your private wishlist.">

    <style>
        body, #mainContent {
            background-color: #F9F8F3 !important;
        }
    </style>

    <div class="py-10 lg:py-16 min-h-[calc(100vh-140px)]" style="background-color: #F9F8F3 !important;">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Breadcrumbs -->
            <nav aria-label="Breadcrumb" class="mb-8 flex items-center gap-2 text-xs text-stone-500 font-medium">
                <a href="{{ route('home') }}" class="hover:text-stone-900 transition">Home</a>
                <span class="text-stone-300">/</span>
                <span class="text-stone-400">Patron Access</span>
                <span class="text-stone-300">/</span>
                <span class="text-stone-800 font-semibold" aria-current="page">Sign In</span>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-start">

                <!-- LEFT COLUMN: VIP Privilege Club & Premium Experience -->
                <div class="lg:col-span-5 space-y-6">
                    <div class="text-white rounded-3xl p-8 sm:p-10 shadow-2xl border border-stone-800/80 relative overflow-hidden" style="background-color: #000000 !important;">

                        <div class="relative z-10 space-y-6">
                            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-[10px] font-bold tracking-[0.2em] bg-stone-900 text-amber-300 border border-amber-300/30 uppercase">
                                <span>✦</span>
                                <span>Maison Privilege Club</span>
                            </div>

                            <div>
                                <h2 class="text-2xl sm:text-3xl font-serif font-normal tracking-tight text-white leading-snug">
                                    Step into the World of {{ config('app.name', 'Project Nexon') }}
                                </h2>
                                <p class="text-xs sm:text-sm text-stone-300 mt-3 leading-relaxed font-light">
                                    Register as an esteemed patron to unlock curated seasonal drops, bespoke tailor fittings, and personal concierge services.
                                </p>
                            </div>

                            <!-- Exclusive Perks List -->
                            <div class="space-y-4 pt-4 border-t border-stone-800 text-xs">
                                <div class="flex items-start gap-3.5">
                                    <div class="w-8 h-8 rounded-xl bg-amber-400/10 text-amber-300 flex items-center justify-center shrink-0 border border-amber-400/20">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-white text-xs">100 Welcome Bonus Reward Points</h3>
                                        <p class="text-stone-400 text-[11px] mt-0.5 leading-relaxed">Instant reward points credited to your VIP wallet upon registration.</p>
                                    </div>
                                </div>

                                <div class="flex items-start gap-3.5">
                                    <div class="w-8 h-8 rounded-xl bg-rose-400/10 text-rose-300 flex items-center justify-center shrink-0 border border-rose-400/20">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-white text-xs">Private Atelier Runway Drops</h3>
                                        <p class="text-stone-400 text-[11px] mt-0.5 leading-relaxed">48-hour priority reservation access before seasonal pieces release to the public.</p>
                                    </div>
                                </div>

                                <div class="flex items-start gap-3.5">
                                    <div class="w-8 h-8 rounded-xl bg-indigo-400/10 text-indigo-300 flex items-center justify-center shrink-0 border border-indigo-400/20">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-white text-xs">White-Glove Express Delivery</h3>
                                        <p class="text-stone-400 text-[11px] mt-0.5 leading-relaxed">Insured priority dispatch with signature keepsake packaging.</p>
                                    </div>
                                </div>

                                <div class="flex items-start gap-3.5">
                                    <div class="w-8 h-8 rounded-xl bg-emerald-400/10 text-emerald-300 flex items-center justify-center shrink-0 border border-emerald-400/20">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-white text-xs">Dedicated Styling Concierge</h3>
                                        <p class="text-stone-400 text-[11px] mt-0.5 leading-relaxed">1-on-1 private assistance for bespoke sizing, bridal trousseau, and gifting.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Patron Quote -->
                            <div class="p-4 rounded-2xl border border-stone-800 text-xs text-stone-300 space-y-2" style="background-color: #0d0d0d !important;">
                                <p class="italic text-stone-200 text-[11px] leading-relaxed">
                                    &ldquo;The fabric selection and meticulous finishing exceed high couture standards. Registering gave me access to archival pieces I cherish.&rdquo;
                                </p>
                                <div class="flex items-center justify-between text-[10px] text-stone-400 font-medium">
                                    <span>&mdash; Lady Alistair M.</span>
                                    <span class="text-amber-300">★★★★★ Verified Patron</span>
                                </div>
                            </div>

                            <!-- Trust Guarantee -->
                            <div class="flex items-center justify-between pt-2 text-[10px] text-stone-400 uppercase tracking-wider font-mono">
                                <span>🔒 256-Bit SSL Security</span>
                                <span>✦ 100% Authentic</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RIGHT COLUMN: Customer Login Form -->
                <div class="lg:col-span-7">
                    <div class="bg-white rounded-3xl border border-stone-200/90 shadow-xl shadow-stone-900/5 p-8 sm:p-12 relative"
                         x-data="{ showPassword: false }">

                        <div class="mb-8">
                            <span class="text-[10px] font-bold tracking-[0.25em] text-rose-800 uppercase font-mono block mb-1">
                                Patron Authentication
                            </span>
                            <h1 class="text-2xl sm:text-3xl font-serif text-stone-900 font-normal tracking-tight">
                                Sign In to Your Account
                            </h1>
                            <p class="text-xs sm:text-sm text-stone-500 mt-2 font-light">
                                Don't have an account yet? 
                                <a href="{{ route('register') }}" class="text-rose-900 font-bold hover:underline">
                                    Create an account here &rarr;
                                </a>
                            </p>
                        </div>

                        <!-- Flash Errors -->
                        @if($errors->any())
                            <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-xs text-rose-800 space-y-1 shadow-xs">
                                @foreach($errors->all() as $error)
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                        <span>{{ $error }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @if(session('status'))
                            <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-800 font-semibold flex items-center gap-2 shadow-xs">
                                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>{{ session('status') }}</span>
                            </div>
                        @endif

                        <!-- Social Quick Buttons -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-6">
                            <button type="button" 
                                    class="w-full py-2.5 px-4 rounded-xl border border-stone-200 hover:bg-stone-50 text-stone-700 text-xs font-semibold flex items-center justify-center gap-2.5 transition shadow-2xs">
                                <svg class="w-4 h-4" viewBox="0 0 24 24">
                                    <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.8-2.4 3.66v3.05h3.88c2.27-2.09 3.665-5.17 3.665-9.15z"/>
                                    <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.41 7.34 24 12 24z"/>
                                    <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.25C.45 8.18 0 10.03 0 12s.45 3.82 1.25 5.42l4.03-3.15z"/>
                                    <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.34 0 3.26 2.59 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/>
                                </svg>
                                <span>Continue with Google</span>
                            </button>

                            <button type="button" 
                                    class="w-full py-2.5 px-4 rounded-xl border border-stone-200 hover:bg-stone-50 text-stone-700 text-xs font-semibold flex items-center justify-center gap-2.5 transition shadow-2xs">
                                <svg class="w-4 h-4 fill-current text-stone-900" viewBox="0 0 24 24">
                                    <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M15.97 6.37c.61-.75 1.04-1.8 0.92-2.85-.9.04-2 .6-2.65 1.35-.58.66-1.08 1.73-.95 2.76.99.08 2.06-.51 2.68-1.26z"/>
                                </svg>
                                <span>Continue with Apple</span>
                            </button>
                        </div>

                        <div class="relative flex items-center justify-center mb-6">
                            <div class="border-t border-stone-200 w-full"></div>
                            <span class="bg-white px-3 text-[11px] text-stone-400 uppercase tracking-widest font-mono shrink-0">
                                or sign in with email
                            </span>
                        </div>

                        <!-- Form -->
                        <form method="POST" action="{{ route('login') }}" class="space-y-5 text-xs">
                            @csrf

                            <div>
                                <label for="email" class="block font-bold text-stone-700 uppercase tracking-wider text-[11px] mb-1">
                                    Email Address <span class="text-rose-600">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-stone-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    </div>
                                    <input id="email" 
                                           type="email" 
                                           name="email" 
                                           value="{{ old('email') }}" 
                                           required 
                                           autofocus 
                                           autocomplete="email"
                                           placeholder="eleanor@example.com"
                                           class="w-full pl-10 pr-4 py-3 rounded-xl border border-stone-300 text-stone-900 text-xs sm:text-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-600 outline-none transition placeholder:text-stone-300">
                                </div>
                            </div>

                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label for="password" class="block font-bold text-stone-700 uppercase tracking-wider text-[11px]">
                                        Password <span class="text-rose-600">*</span>
                                    </label>
                                    <a href="{{ route('password.request') }}" class="text-[11px] text-rose-800 font-semibold hover:underline">
                                        Forgot Password?
                                    </a>
                                </div>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-stone-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                    </div>
                                    <input id="password" 
                                           :type="showPassword ? 'text' : 'password'" 
                                           name="password" 
                                           required 
                                           autocomplete="current-password"
                                           placeholder="••••••••"
                                           class="w-full pl-10 pr-12 py-3 rounded-xl border border-stone-300 text-stone-900 text-xs sm:text-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-600 outline-none transition placeholder:text-stone-300">
                                    <button type="button" 
                                            @click="showPassword = !showPassword"
                                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-stone-400 hover:text-stone-700 transition"
                                            :title="showPassword ? 'Hide password' : 'Show password'">
                                        <svg x-show="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <svg x-show="showPassword" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                                    </button>
                                </div>
                            </div>

                            <div class="flex items-center justify-between">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="remember" class="rounded border-stone-300 text-rose-800 focus:ring-rose-600">
                                    <span class="text-stone-600 text-xs">Keep me signed in</span>
                                </label>
                            </div>

                            <div class="pt-2">
                                <button type="submit" 
                                        class="w-full py-3.5 px-6 rounded-xl bg-stone-900 hover:bg-stone-800 active:bg-black text-white font-bold text-xs sm:text-sm tracking-wider uppercase transition shadow-lg shadow-stone-900/10 flex items-center justify-center gap-2 group">
                                    <span>Sign In to Account</span>
                                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </button>
                            </div>
                        </form>

                    </div>
                </div>

            </div>

        </div>
    </div>

</x-storefront-layout>
