<x-storefront-layout :theme="$theme ?? \App\Models\Template::active()" title="Verify Sign In — {{ config('app.name', 'Project Nexon') }}" metaDesc="Verify your two-step login code to securely access your {{ config('app.name', 'Project Nexon') }} patron account.">

    <style>
        body, #mainContent {
            background-color: #F9F8F3 !important;
        }

        .otp-input-field {
            letter-spacing: 0.8rem;
            font-size: 2rem;
            font-weight: 800;
            text-align: center;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
        }
        @media (max-width: 640px) {
            .otp-input-field {
                letter-spacing: 0.5rem;
                font-size: 1.6rem;
            }
        }
    </style>

    <div class="py-10 lg:py-16 min-h-[calc(100vh-140px)]" style="background-color: #F9F8F3 !important;">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Breadcrumbs -->
            <nav aria-label="Breadcrumb" class="mb-8 flex items-center gap-2 text-xs text-stone-500 font-medium">
                <a href="{{ route('home') }}" class="hover:text-stone-900 transition">Home</a>
                <span class="text-stone-300">/</span>
                <a href="{{ route('login') }}" class="hover:text-stone-900 transition">Patron Access</a>
                <span class="text-stone-300">/</span>
                <span class="text-stone-800 font-semibold" aria-current="page">Two-Step Verification</span>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-start">

                <!-- LEFT COLUMN: Maison Privilege Club & VIP Highlights -->
                <div class="lg:col-span-5 space-y-6">
                    <div class="text-white rounded-3xl p-8 sm:p-10 shadow-2xl border border-stone-800/80 relative overflow-hidden" style="background-color: #000000 !important;">

                        <div class="relative z-10 space-y-6">
                            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-[10px] font-bold tracking-[0.2em] bg-stone-900 text-amber-300 border border-amber-300/30 uppercase">
                                <span>✦</span>
                                <span>Two-Step Verification</span>
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

                <!-- RIGHT COLUMN: OTP Verification Form -->
                <div class="lg:col-span-7">
                    <div class="bg-white rounded-3xl border border-stone-200/90 shadow-xl shadow-stone-900/5 p-8 sm:p-12 relative">

                        <div class="mb-6 text-center sm:text-left">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold tracking-[0.2em] bg-stone-100 text-stone-800 border border-stone-200 uppercase font-mono mb-3">
                                <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                <span>Two-Factor Authentication</span>
                            </span>
                            <h1 class="text-2xl sm:text-3xl font-serif text-stone-900 font-normal tracking-tight">
                                Verify Your Sign In
                            </h1>
                            <p class="text-xs sm:text-sm text-stone-500 mt-2 font-light">
                                Enter the 4-digit verification code sent to your registered email address.
                            </p>
                        </div>

                        <!-- Email Notice Box -->
                        <div class="mb-6 p-4 rounded-2xl bg-stone-50 border border-stone-200 flex items-center justify-between text-xs">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-stone-200 text-stone-700 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </div>
                                <div>
                                    <span class="text-[11px] text-stone-400 block font-mono uppercase">Code Sent To</span>
                                    <strong class="text-stone-900 font-semibold text-xs sm:text-sm">{{ $email ?? session('pending_2fa_email', 'your email') }}</strong>
                                </div>
                            </div>
                            <span class="text-[11px] text-stone-500 hidden sm:inline">Valid for 10 min</span>
                        </div>

                        <!-- Flash Messages -->
                        <div id="flashAlertContainer">
                            @if(session('status'))
                                <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-800 font-semibold flex items-center gap-2 shadow-xs">
                                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span>{{ session('status') }}</span>
                                </div>
                            @endif

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
                        </div>

                        <!-- OTP Form -->
                        <form id="otpForm" method="POST" action="{{ route('verification.2fa') }}" class="space-y-6">
                            @csrf

                            <div>
                                <label for="otp" class="block font-bold text-stone-700 uppercase tracking-wider text-[11px] mb-2 text-center sm:text-left">
                                    4-Digit Verification Code <span class="text-rose-600">*</span>
                                </label>
                                <div class="relative">
                                    <input id="otp"
                                           type="text"
                                           name="otp"
                                           inputmode="numeric"
                                           pattern="[0-9]{4}"
                                           maxlength="4"
                                           autocomplete="one-time-code"
                                           required
                                           autofocus
                                           placeholder="••••"
                                           value="{{ old('otp') }}"
                                           class="otp-input-field w-full py-4 px-4 rounded-2xl border-2 {{ $errors->has('otp') ? 'border-rose-400 bg-rose-50/40 text-rose-900' : 'border-stone-300 focus:border-stone-900 bg-stone-50/50 text-stone-900' }} focus:ring-4 focus:ring-stone-900/10 outline-none transition placeholder:text-stone-300 placeholder:opacity-50">
                                </div>
                                <p class="text-[11px] text-stone-400 text-center mt-2">
                                    Code automatically submits once all 4 digits are entered.
                                </p>
                            </div>

                            <!-- Resend Code Section with Countdown Timer -->
                            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-2 text-xs text-stone-500 border-t border-stone-100">
                                <span>Didn't receive the email code?</span>
                                <div class="flex items-center gap-2">
                                    <button type="button" 
                                            id="resendBtn" 
                                            onclick="triggerResendOtp()"
                                            class="text-xs font-bold text-stone-900 hover:text-stone-600 hover:underline disabled:opacity-40 disabled:cursor-not-allowed disabled:no-underline transition">
                                        Resend Code
                                    </button>
                                    <span id="countdownText" class="text-[11px] text-stone-400 font-mono"></span>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="pt-2 space-y-3">
                                <button type="submit" 
                                        id="submitOtpBtn"
                                        class="w-full py-3.5 px-6 rounded-xl bg-stone-900 hover:bg-stone-800 active:bg-black text-white font-bold text-xs sm:text-sm tracking-wider uppercase transition shadow-lg shadow-stone-900/10 flex items-center justify-center gap-2 group">
                                    <span id="submitOtpBtnText">Verify &amp; Sign In</span>
                                    <svg id="submitOtpSpinner" class="w-4 h-4 animate-spin hidden" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                    </svg>
                                    <svg id="submitOtpArrow" class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </button>

                                <div class="text-center pt-2">
                                    <a href="{{ route('login') }}" class="text-xs text-stone-500 hover:text-stone-900 transition font-medium">
                                        &larr; Not you? Go back to sign in
                                    </a>
                                </div>
                            </div>
                        </form>

                        <!-- Hidden Form fallback for Resend if JS fetch fails -->
                        <form id="resendForm" method="POST" action="{{ route('verification.2fa.resend') }}" class="hidden">
                            @csrf
                        </form>

                    </div>
                </div>

            </div>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const otpInput = document.getElementById('otp');
            const otpForm = document.getElementById('otpForm');
            const submitBtn = document.getElementById('submitOtpBtn');
            const submitBtnText = document.getElementById('submitOtpBtnText');
            const submitSpinner = document.getElementById('submitOtpSpinner');
            const submitArrow = document.getElementById('submitOtpArrow');
            const resendBtn = document.getElementById('resendBtn');
            const countdownText = document.getElementById('countdownText');

            let isSubmitting = false;

            // Auto-focus OTP input
            if (otpInput) {
                otpInput.focus();

                // Format and Auto-submit on 4 digits
                otpInput.addEventListener('input', function () {
                    // Only numbers
                    this.value = this.value.replace(/[^0-9]/g, '').slice(0, 4);

                    if (this.value.length === 4 && !isSubmitting) {
                        isSubmitting = true;
                        submitBtnText.textContent = 'Verifying Code...';
                        submitSpinner.classList.remove('hidden');
                        submitArrow.classList.add('hidden');
                        submitBtn.disabled = true;

                        setTimeout(() => {
                            otpForm.submit();
                        }, 300);
                    }
                });

                // Paste support
                otpInput.addEventListener('paste', function (e) {
                    e.preventDefault();
                    const pastedData = (e.clipboardData || window.clipboardData).getData('text');
                    const cleanDigits = pastedData.replace(/[^0-9]/g, '').slice(0, 4);
                    this.value = cleanDigits;

                    if (cleanDigits.length === 4 && !isSubmitting) {
                        isSubmitting = true;
                        submitBtnText.textContent = 'Verifying Code...';
                        submitSpinner.classList.remove('hidden');
                        submitArrow.classList.add('hidden');
                        submitBtn.disabled = true;

                        setTimeout(() => {
                            otpForm.submit();
                        }, 300);
                    }
                });
            }

            // Normal form submit state
            if (otpForm) {
                otpForm.addEventListener('submit', function () {
                    if (!isSubmitting) {
                        isSubmitting = true;
                        submitBtnText.textContent = 'Verifying Code...';
                        submitSpinner.classList.remove('hidden');
                        submitArrow.classList.add('hidden');
                        submitBtn.disabled = true;
                    }
                });
            }

            // Countdown Timer for Resend Button (60 seconds)
            let countdown = 60;
            let timerInterval = null;

            function startCountdown() {
                resendBtn.disabled = true;
                countdown = 60;
                updateCountdownUI();

                if (timerInterval) clearInterval(timerInterval);
                timerInterval = setInterval(() => {
                    countdown--;
                    updateCountdownUI();
                    if (countdown <= 0) {
                        clearInterval(timerInterval);
                        resendBtn.disabled = false;
                        countdownText.textContent = '';
                    }
                }, 1000);
            }

            function updateCountdownUI() {
                if (countdown > 0) {
                    countdownText.textContent = `(${countdown}s)`;
                } else {
                    countdownText.textContent = '';
                }
            }

            // Start countdown on page load
            startCountdown();

            // Trigger Resend OTP via AJAX
            window.triggerResendOtp = function () {
                if (resendBtn.disabled) return;

                resendBtn.disabled = true;
                const originalText = resendBtn.textContent;
                resendBtn.textContent = 'Sending...';

                fetch("{{ route('verification.2fa.resend') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({})
                })
                .then(response => response.json())
                .then(data => {
                    resendBtn.textContent = originalText;
                    startCountdown();

                    // Show success alert
                    const container = document.getElementById('flashAlertContainer');
                    if (container) {
                        container.innerHTML = `
                            <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-800 font-semibold flex items-center gap-2 shadow-xs">
                                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>${data.message || 'A new verification code has been sent to your email.'}</span>
                            </div>
                        `;
                    }
                    if (otpInput) {
                        otpInput.value = '';
                        otpInput.focus();
                    }
                })
                .catch(() => {
                    // Fallback to form submission
                    document.getElementById('resendForm').submit();
                });
            };
        });
    </script>

</x-storefront-layout>

