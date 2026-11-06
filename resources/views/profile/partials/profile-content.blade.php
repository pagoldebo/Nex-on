<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-stone-200/80 shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-indigo-600 uppercase tracking-wider mb-1">
                <span>Account Security</span>
                <span>•</span>
                <span class="text-stone-500 font-medium">OWASP-Hardened Profile</span>
            </div>
            <h1 class="text-2xl font-bold text-stone-900 tracking-tight">Security &amp; Profile Hub</h1>
            <p class="text-xs sm:text-sm text-stone-500 mt-0.5">Manage personal credentials, update password, and configure RFC 6238 Two-Factor Authenticator (TOTP).</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-stone-100 text-stone-700 border border-stone-200">
                Role: {{ ucfirst($user->role ?? 'User') }}
            </span>
        </div>
    </div>

    <!-- Status Alerts -->
    @if (session('status') === 'profile-updated')
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2">
            <span>✓</span> Profile information has been updated successfully.
        </div>
    @elseif (session('status') === 'password-updated')
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2">
            <span>✓</span> Your password has been updated securely.
        </div>
    @elseif (session('status') === 'two-factor-authentication-enabled')
        <div class="p-4 rounded-2xl bg-indigo-50 border border-indigo-200 text-indigo-800 text-xs font-semibold flex items-center gap-2">
            <span>ℹ️</span> Two-factor authentication setup initialized. Scan the QR code below and provide the 6-digit confirmation PIN.
        </div>
    @elseif (session('status') === 'two-factor-authentication-confirmed')
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2">
            <span>🛡️</span> Two-factor authentication confirmed and activated. Your account is now fortified with rolling TOTP codes.
        </div>
    @elseif (session('status') === 'two-factor-authentication-disabled')
        <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 text-xs font-semibold flex items-center gap-2">
            <span>⚠️</span> Two-factor authentication has been disabled.
        </div>
    @elseif (session('status') === 'recovery-codes-generated')
        <div class="p-4 rounded-2xl bg-indigo-50 border border-indigo-200 text-indigo-800 text-xs font-semibold flex items-center gap-2">
            <span>🔑</span> New emergency recovery codes generated. Please copy and store them safely.
        </div>
    @endif

    @if ($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-xs text-rose-700 space-y-1">
            <div class="font-bold">Please correct the following:</div>
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- 1. Profile Information -->
    <div class="bg-white p-6 sm:p-8 rounded-2xl border border-stone-200/80 shadow-xs space-y-6">
        <div class="border-b border-stone-100 pb-4">
            <h2 class="text-base font-bold text-stone-900">Personal Information</h2>
            <p class="text-xs text-stone-400 mt-0.5">Update your display name, contact phone, and primary email address.</p>
        </div>

        <form method="POST" action="{{ route('profile.update') }}" class="space-y-4 max-w-xl">
            @csrf
            @method('PATCH')

            <div>
                <label for="name" class="block text-xs font-semibold text-stone-700 mb-1">Full Name</label>
                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                       class="w-full px-3.5 py-2.5 bg-stone-50 border border-stone-200 rounded-xl text-sm font-medium text-stone-900 focus:bg-white focus:border-stone-900 focus:ring-2 focus:ring-stone-900/10">
            </div>

            <div>
                <label for="email" class="block text-xs font-semibold text-stone-700 mb-1">Email Address</label>
                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                       class="w-full px-3.5 py-2.5 bg-stone-50 border border-stone-200 rounded-xl text-sm font-medium text-stone-900 focus:bg-white focus:border-stone-900 focus:ring-2 focus:ring-stone-900/10">
            </div>

            <div>
                <label for="phone" class="block text-xs font-semibold text-stone-700 mb-1">Contact Phone</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" placeholder="+91 98765 43210"
                       class="w-full px-3.5 py-2.5 bg-stone-50 border border-stone-200 rounded-xl text-sm font-medium text-stone-900 focus:bg-white focus:border-stone-900 focus:ring-2 focus:ring-stone-900/10">
            </div>

            <div class="pt-2">
                <button type="submit" class="px-5 py-2.5 bg-stone-900 text-white rounded-xl text-xs font-bold hover:bg-stone-800 transition shadow-xs">
                    Save Changes
                </button>
            </div>
        </form>
    </div>

    <!-- 2. Update Password -->
    <div class="bg-white p-6 sm:p-8 rounded-2xl border border-stone-200/80 shadow-xs space-y-6">
        <div class="border-b border-stone-100 pb-4">
            <h2 class="text-base font-bold text-stone-900">Update Password</h2>
            <p class="text-xs text-stone-400 mt-0.5">Ensure your account uses a secure, random password to protect your data and purchases.</p>
        </div>

        <form method="POST" action="{{ route('password.update') }}" class="space-y-4 max-w-xl">
            @csrf
            @method('PUT')

            <div>
                <label for="current_password" class="block text-xs font-semibold text-stone-700 mb-1">Current Password</label>
                <input type="password" name="current_password" id="current_password" required autocomplete="current-password"
                       class="w-full px-3.5 py-2.5 bg-stone-50 border border-stone-200 rounded-xl text-sm font-medium text-stone-900 focus:bg-white focus:border-stone-900 focus:ring-2 focus:ring-stone-900/10">
            </div>

            <div>
                <label for="password" class="block text-xs font-semibold text-stone-700 mb-1">New Password</label>
                <input type="password" name="password" id="password" required autocomplete="new-password" placeholder="Minimum 8 characters"
                       class="w-full px-3.5 py-2.5 bg-stone-50 border border-stone-200 rounded-xl text-sm font-medium text-stone-900 focus:bg-white focus:border-stone-900 focus:ring-2 focus:ring-stone-900/10">
            </div>

            <div>
                <label for="password_confirmation" class="block text-xs font-semibold text-stone-700 mb-1">Confirm New Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required autocomplete="new-password"
                       class="w-full px-3.5 py-2.5 bg-stone-50 border border-stone-200 rounded-xl text-sm font-medium text-stone-900 focus:bg-white focus:border-stone-900 focus:ring-2 focus:ring-stone-900/10">
            </div>

            <div class="pt-2">
                <button type="submit" class="px-5 py-2.5 bg-stone-900 text-white rounded-xl text-xs font-bold hover:bg-stone-800 transition shadow-xs">
                    Update Password
                </button>
            </div>
        </form>
    </div>

    <!-- 3. Two-Factor Authentication (RFC 6238 TOTP App) -->
    <div id="two-factor" class="bg-white p-6 sm:p-8 rounded-2xl border border-stone-200/80 shadow-xs space-y-6">
        <div class="border-b border-stone-100 pb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-lg">📱</span>
                    <h2 class="text-base font-bold text-stone-900">Two-Factor Authenticator (Google Authenticator / Authy)</h2>
                </div>
                <p class="text-xs text-stone-400 mt-0.5">Fortify your account using standard RFC 6238 time-based one-time passcodes.</p>
            </div>
            <div>
                @if ($user->hasTwoFactorEnabled())
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Active &amp; Fortified
                    </span>
                @elseif ($user->two_factor_secret)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                        Pending 6-Digit Confirmation
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-stone-100 text-stone-500 border border-stone-200">
                        Disabled (Using Email OTP)
                    </span>
                @endif
            </div>
        </div>

        @if (! $user->two_factor_secret)
            <!-- TOTP Disabled: Setup Prompt -->
            <div class="space-y-4">
                <p class="text-xs text-stone-600 leading-relaxed">
                    When enabled, sign-in attempts will require a rolling 6-digit passcode generated by your mobile authenticator app (Google Authenticator, Authy, Microsoft Authenticator, or 1Password) instead of waiting for an email.
                </p>
                <form method="POST" action="{{ route('two-factor.enable') }}">
                    @csrf
                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white rounded-xl text-xs font-bold hover:bg-indigo-700 transition shadow-xs flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                        Set Up Authenticator App
                    </button>
                </form>
            </div>
        @elseif (! $user->two_factor_confirmed_at)
            <!-- TOTP Setup in Progress: Scan QR & Confirm -->
            <div class="space-y-6">
                <p class="text-xs text-stone-600 leading-relaxed">
                    To activate Two-Factor Authentication, scan the QR code using your authenticator application or enter the manual secret key, then verify with the generated 6-digit code.
                </p>

                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6 p-5 rounded-2xl bg-stone-50 border border-stone-200">
                    @if ($qrCodeSvgUrl)
                        <div class="p-2.5 bg-white rounded-xl shadow-xs border border-stone-200 shrink-0">
                            <img src="{{ $qrCodeSvgUrl }}" alt="2FA QR Code" class="w-36 h-36">
                        </div>
                    @endif
                    <div class="space-y-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-stone-500">Manual Setup Secret:</span>
                        <div class="font-mono text-xs font-bold text-indigo-700 bg-white px-3 py-2 rounded-lg border border-stone-200 select-all break-all">
                            {{ $secretKey }}
                        </div>
                        <p class="text-[11px] text-stone-400">Works with Google Authenticator, Authy, Apple Passwords &amp; 1Password.</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('two-factor.confirm') }}" class="space-y-4 max-w-sm">
                    @csrf
                    <div>
                        <label for="confirm_totp_code" class="block text-xs font-semibold text-stone-700 mb-1">
                            Enter 6-Digit Code from App
                        </label>
                        <input type="text" name="code" id="confirm_totp_code" maxlength="6" inputmode="numeric" placeholder="123456" required autofocus
                               class="w-full px-3.5 py-2.5 bg-stone-50 border border-stone-200 rounded-xl text-center font-mono text-lg font-bold tracking-widest text-stone-900 focus:bg-white focus:border-stone-900 focus:ring-2 focus:ring-stone-900/10">
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="submit" class="px-5 py-2.5 bg-emerald-600 text-white rounded-xl text-xs font-bold hover:bg-emerald-700 transition shadow-xs">
                            Confirm &amp; Activate 2FA
                        </button>
                    </div>
                </form>

                <form method="POST" action="{{ route('two-factor.disable') }}">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="password" value="cancelling">
                    <button type="submit" class="text-xs text-stone-400 hover:text-stone-700 underline">
                        Cancel Setup
                    </button>
                </form>
            </div>
        @else
            <!-- TOTP Active -->
            <div class="space-y-6">
                <p class="text-xs text-stone-600 leading-relaxed">
                    Your account is fortified with Time-based One-Time Passwords. Whenever you sign in, you will be prompted for a 6-digit rolling code generated by your mobile authenticator app.
                </p>

                <!-- Emergency Recovery Codes -->
                <div class="p-5 rounded-2xl bg-stone-50 border border-stone-200 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-stone-800">Emergency Recovery Codes</span>
                        <span class="text-[11px] text-stone-400">One-time use each</span>
                    </div>
                    <p class="text-[11px] text-stone-500 leading-relaxed">
                        Store these backup codes in a password manager. If you lose access to your phone or authenticator app, each code can be used once to access your account.
                    </p>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 font-mono text-xs font-bold text-stone-700 bg-white p-3.5 rounded-xl border border-stone-200 select-all">
                        @foreach ($recoveryCodes as $code)
                            <div>{{ $code }}</div>
                        @endforeach
                    </div>

                    <form method="POST" action="{{ route('two-factor.regenerate-recovery-codes') }}" class="pt-2">
                        @csrf
                        <button type="submit" class="text-xs text-indigo-600 hover:text-indigo-800 font-semibold underline">
                            Regenerate Fresh Recovery Codes
                        </button>
                    </form>
                </div>

                <!-- Disable 2FA Form -->
                <form method="POST" action="{{ route('two-factor.disable') }}" class="space-y-3 max-w-md pt-2">
                    @csrf
                    @method('DELETE')

                    <div>
                        <label for="disable_password" class="block text-xs font-semibold text-stone-700 mb-1">
                            Current Password to Disable 2FA
                        </label>
                        <input type="password" name="password" id="disable_password" required placeholder="Enter current password"
                               class="w-full px-3.5 py-2.5 bg-stone-50 border border-stone-200 rounded-xl text-sm font-medium text-stone-900 focus:bg-white focus:border-rose-600 focus:ring-2 focus:ring-rose-600/10">
                    </div>

                    <button type="submit" class="px-5 py-2.5 bg-rose-50 text-rose-700 border border-rose-200 rounded-xl text-xs font-bold hover:bg-rose-100 transition">
                        Disable Authenticator 2FA
                    </button>
                </form>
            </div>
        @endif
    </div>

    <!-- 4. Danger Zone: Delete Account -->
    <div class="bg-rose-50/50 p-6 sm:p-8 rounded-2xl border border-rose-200/80 shadow-xs space-y-4">
        <div>
            <h2 class="text-base font-bold text-rose-900">Delete Account</h2>
            <p class="text-xs text-rose-600/80 mt-0.5">Permanently remove your account and all associated personal credentials.</p>
        </div>

        <form method="POST" action="{{ route('profile.destroy') }}" onsubmit="return confirm('Are you sure you want to permanently delete your account? This action cannot be reversed.');" class="space-y-4 max-w-xl">
            @csrf
            @method('DELETE')

            <div>
                <label for="delete_acc_password" class="block text-xs font-semibold text-rose-900 mb-1">Confirm Password</label>
                <input type="password" name="password" id="delete_acc_password" required placeholder="Enter current password to confirm deletion"
                       class="w-full px-3.5 py-2.5 bg-white border border-rose-200 rounded-xl text-sm font-medium text-stone-900 focus:border-rose-600 focus:ring-2 focus:ring-rose-600/10">
            </div>

            <button type="submit" class="px-5 py-2.5 bg-rose-600 text-white rounded-xl text-xs font-bold hover:bg-rose-700 transition shadow-xs">
                Permanently Delete Account
            </button>
        </form>
    </div>

</div>
