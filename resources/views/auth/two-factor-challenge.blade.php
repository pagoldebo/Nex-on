{{--
    ======================================================================
    AUTHENTICATION: TWO FACTOR CHALLENGE
    ======================================================================
    File: resources/views/auth/two-factor-challenge.blade.php
    Route Context: /two-factor-challenge
    Responsibility: Secure customer and staff authentication interface
    Features:
      - High-security credentials input with CSRF protection
      - Patron rate limiting enforcement (prevents brute-force attacks)
      - Two-Factor OTP challenge handling when active
    Operations & Maintenance:
      - Controlled via App\Http\Controllers\Auth controllers
--}}
<x-storefront-layout :theme="$theme ?? \App\Models\Template::active()" title="Two-Factor Authenticator Challenge — {{ config('app.name', 'Nex-on') }}" metaDesc="Verify your two-factor authenticator app code to securely access your {{ config('app.name', 'Nex-on') }} account.">

    <style>
        body, #mainContent {
            background-color: #F9F8F3 !important;
        }

        .totp-input-field {
            letter-spacing: 0.6rem;
            font-size: 2rem;
            font-weight: 800;
            text-align: center;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
        }
        @media (max-width: 640px) {
            .totp-input-field {
                letter-spacing: 0.35rem;
                font-size: 1.5rem;
            }
        }
    </style>

    <div class="py-10 lg:py-16 min-h-[calc(100vh-140px)]" style="background-color: #F9F8F3 !important;">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Breadcrumbs -->
            <nav aria-label="Breadcrumb" class="mb-8 flex items-center gap-2 text-xs text-stone-500 font-medium">
                <a href="{{ route('home') }}" class="hover:text-stone-900 transition">Home</a>
                <span class="text-stone-300">/</span>
                <a href="{{ route('login') }}" class="hover:text-stone-900 transition">Sign In</a>
                <span class="text-stone-300">/</span>
                <span class="text-stone-800 font-semibold" aria-current="page">Two-Factor Authentication</span>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-start" x-data="{ recovery: false }">

                <!-- LEFT COLUMN: Maison Privilege Security Highlights -->
                <div class="lg:col-span-5 space-y-6">
                    <div class="text-white rounded-3xl p-8 sm:p-10 shadow-2xl border border-stone-800/80 relative overflow-hidden" style="background-color: #000000 !important;">

                        <div class="relative z-10 space-y-6">
                            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-[10px] font-bold tracking-[0.2em] bg-stone-900 text-amber-300 border border-amber-300/30 uppercase">
                                <span>🔐</span>
                                <span>OWASP-Hardened TOTP</span>
                            </div>

                            <div>
                                <h2 class="text-2xl sm:text-3xl font-serif font-normal tracking-tight text-white leading-snug">
                                    Authenticator App Challenge
                                </h2>
                                <p class="text-xs sm:text-sm text-stone-300 mt-3 leading-relaxed font-light">
                                    Your account is fortified with RFC 6238 time-based two-factor authentication.
                                </p>
                            </div>

                            <!-- Security Notes -->
                            <div class="space-y-4 pt-4 border-t border-stone-800 text-xs">
                                <div class="flex items-start gap-3.5">
                                    <div class="w-8 h-8 rounded-xl bg-amber-400/10 text-amber-300 flex items-center justify-center shrink-0 border border-amber-400/20">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-white">Google Authenticator / Authy</div>
                                        <p class="text-[11px] text-stone-400 mt-0.5">Open your authenticator app to retrieve the current 6-digit rolling code.</p>
                                    </div>
                                </div>

                                <div class="flex items-start gap-3.5">
                                    <div class="w-8 h-8 rounded-xl bg-emerald-400/10 text-emerald-300 flex items-center justify-center shrink-0 border border-emerald-400/20">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-white">Anti-Brute Force Throttling</div>
                                        <p class="text-[11px] text-stone-400 mt-0.5">Protected with exponential backoff delays and constant-time token verification.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RIGHT COLUMN: TOTP Code Input Form -->
                <div class="lg:col-span-7">
                    <div class="bg-white rounded-3xl p-8 sm:p-12 shadow-xl border border-stone-200/80">
                        <div class="max-w-md mx-auto space-y-6">

                            <div class="text-center space-y-2">
                                <div class="w-14 h-14 mx-auto rounded-2xl bg-stone-900 text-amber-300 flex items-center justify-center shadow-lg border border-amber-300/20">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                </div>
                                <h1 class="text-2xl sm:text-3xl font-serif font-bold text-stone-900">
                                    <span x-show="!recovery">Enter Authenticator PIN</span>
                                    <span x-show="recovery" x-cloak>Enter Recovery Code</span>
                                </h1>
                                <p class="text-xs sm:text-sm text-stone-500">
                                    <span x-show="!recovery">Type the 6-digit code shown in your authenticator application.</span>
                                    <span x-show="recovery" x-cloak>Type one of the 8 emergency backup codes generated for your account.</span>
                                </p>
                            </div>

                            @if ($errors->any())
                                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-xs text-rose-700 space-y-1">
                                    <div class="font-bold flex items-center gap-1.5">
                                        <span>⚠️</span>
                                        <span>Authentication Failed</span>
                                    </div>
                                    <ul class="list-disc list-inside">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('two-factor.login') }}" class="space-y-6">
                                @csrf

                                <!-- 6-Digit TOTP Mode -->
                                <div x-show="!recovery" class="space-y-2">
                                    <label for="totp_code" class="block text-xs font-bold uppercase tracking-wider text-stone-600 text-center">
                                        6-Digit Security Code
                                    </label>
                                    <input
                                        type="text"
                                        name="code"
                                        id="totp_code"
                                        maxlength="6"
                                        inputmode="numeric"
                                        autocomplete="one-time-code"
                                        placeholder="······"
                                        autofocus
                                        class="totp-input-field w-full px-4 py-3 bg-stone-50 border border-stone-300 rounded-2xl text-stone-900 focus:bg-white focus:border-stone-900 focus:ring-2 focus:ring-stone-900/10 transition-all placeholder:text-stone-300"
                                    >
                                </div>

                                <!-- Emergency Recovery Code Mode -->
                                <div x-show="recovery" x-cloak class="space-y-2">
                                    <label for="recovery_code" class="block text-xs font-bold uppercase tracking-wider text-stone-600 text-center">
                                        Emergency Recovery Code
                                    </label>
                                    <input
                                        type="text"
                                        name="recovery_code"
                                        id="recovery_code"
                                        autocomplete="one-time-code"
                                        placeholder="xxxxxxxxxx-xxxxxxxxxx"
                                        class="w-full px-4 py-3 bg-stone-50 border border-stone-300 rounded-2xl text-stone-900 text-center font-mono text-sm focus:bg-white focus:border-stone-900 focus:ring-2 focus:ring-stone-900/10 transition-all placeholder:text-stone-300"
                                    >
                                </div>

                                <!-- Toggle Button -->
                                <div class="text-center text-xs">
                                    <button
                                        type="button"
                                        class="text-indigo-600 hover:text-indigo-800 font-semibold underline underline-offset-4 focus:outline-none"
                                        x-show="!recovery"
                                        @click="recovery = true; $nextTick(() => document.getElementById('recovery_code')?.focus())"
                                    >
                                        Lost access? Use emergency recovery code
                                    </button>
                                    <button
                                        type="button"
                                        class="text-indigo-600 hover:text-indigo-800 font-semibold underline underline-offset-4 focus:outline-none"
                                        x-show="recovery"
                                        x-cloak
                                        @click="recovery = false; $nextTick(() => document.getElementById('totp_code')?.focus())"
                                    >
                                        Use 6-digit authenticator code instead
                                    </button>
                                </div>

                                <button
                                    type="submit"
                                    class="w-full py-4 rounded-2xl font-bold text-xs uppercase tracking-widest text-white transition-all shadow-lg hover:shadow-xl cursor-pointer"
                                    style="background-color: #000000 !important;"
                                >
                                    Verify & Proceed →
                                </button>
                            </form>

                            <div class="text-center pt-2 border-t border-stone-100">
                                <a href="{{ route('login') }}" class="text-xs font-medium text-stone-400 hover:text-stone-700 transition">
                                    ← Return to Sign In
                                </a>
                            </div>

                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

</x-storefront-layout>

