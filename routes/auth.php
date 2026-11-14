<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password as PasswordBroker;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
| Provides lightweight native authentication routes so login, registration,
| and logout work out of the box with storefront modal and dedicated pages.
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        $theme = \App\Models\Template::active();
        if (view()->exists("storefront.{$theme->key}.login")) {
            return view("storefront.{$theme->key}.login", compact('theme'));
        }
        return view('auth.login', compact('theme'));
    })->name('login');

    Route::get('/admin/login', function () {
        return redirect()->route('login');
    });

    Route::post('/login', function (Request $request) {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        $throttleKey = Str::transliterate(Str::lower($credentials['email']) . '|' . $request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $message = 'Too many login attempts. Please try again in ' . $seconds . ' seconds.';
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'message' => $message,
                    'errors'  => ['email' => [$message]],
                ], 429);
            }
            return back()->withErrors([
                'email' => $message,
            ])->onlyInput('email');
        }

        $user = User::where('email', $credentials['email'])->first();

        if ($user && $user->isDeactivated()) {
            return back()->withErrors([
                'email' => 'Your account has been deactivated. Please contact support.',
            ])->onlyInput('email');
        }

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            RateLimiter::hit($throttleKey, 60);
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        }

        RateLimiter::clear($throttleKey);

        // Transparent cryptographic rehash if work factor or algorithm has upgraded
        if (Hash::needsRehash($user->password)) {
            $user->password = Hash::make($credentials['password']);
            $user->save();
        }

        // Branch 1: If user has enabled RFC 6238 TOTP App (Google Authenticator / Authy)
        if ($user->hasTwoFactorEnabled()) {
            session([
                'login.id'       => $user->id,
                'login.remember' => $request->boolean('remember'),
            ]);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success'  => true,
                    'redirect' => route('two-factor.login'),
                    'message'  => 'Please enter the 6-digit code from your authenticator app.',
                ]);
            }

            return redirect()->route('two-factor.login');
        }

        // Branch 2: Standard Email OTP (default for patrons)
        $otp = $user->generateOtp(10);

        session([
            'pending_2fa_user_id'  => $user->id,
            'pending_2fa_remember' => $request->boolean('remember'),
            'pending_2fa_email'    => $user->email,
        ]);

        try {
            \Illuminate\Support\Facades\Notification::route('mail', $user->email)
                ->notify(new \App\Notifications\TwoFactorOTP($otp, $user->name));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send login OTP: ' . $e->getMessage());
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success'  => true,
                'redirect' => route('verification.2fa.form'),
                'message'  => 'A verification code has been sent to your email.',
            ]);
        }

        return redirect()->route('verification.2fa.form')
            ->with('status', 'A verification code has been sent to your email.');
    })->middleware('throttle:login');

    // Two-Step Email OTP Verification Routes
    $showVerifyForm = function () {
        $pendingUserId = session('pending_2fa_user_id');
        if (!$pendingUserId) {
            return redirect()->route('login')->withErrors(['email' => 'Session expired. Please sign in again.']);
        }

        $user = User::find($pendingUserId);
        if (!$user) {
            session()->forget(['pending_2fa_user_id', 'pending_2fa_remember', 'pending_2fa_email']);
            return redirect()->route('login')->withErrors(['email' => 'User not found. Please sign in again.']);
        }

        $theme = \App\Models\Template::active();
        return view('auth.verify-otp', [
            'email' => $user->email,
            'theme' => $theme,
        ]);
    };

    Route::get('/verify-otp', $showVerifyForm)->name('verification.otp.form');
    Route::get('/verify-2fa', $showVerifyForm)->name('verification.2fa.form');

    $verifyOtpHandler = function (Request $request) {
        $request->validate([
            'otp' => ['required', 'string', 'digits_between:4,6'],
        ]);

        $pendingUserId = session('pending_2fa_user_id');
        $remember      = session('pending_2fa_remember', false);

        if (!$pendingUserId) {
            return redirect()->route('login')->withErrors(['email' => 'Session expired. Please sign in again.']);
        }

        $user = User::find($pendingUserId);
        if (!$user) {
            session()->forget(['pending_2fa_user_id', 'pending_2fa_remember', 'pending_2fa_email']);
            return redirect()->route('login')->withErrors(['email' => 'User not found. Please sign in again.']);
        }

        $attemptKey = 'otp_attempts:' . $user->id;
        if (RateLimiter::tooManyAttempts($attemptKey, 5)) {
            $user->clearOtp();
            session()->forget(['pending_2fa_user_id', 'pending_2fa_remember', 'pending_2fa_email']);
            $msg = 'Too many failed verification attempts. Your code has been invalidated for security. Please sign in again.';
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 429);
            }
            return redirect()->route('login')->withErrors(['email' => $msg]);
        }

        if ($user->otp_expires_at && now()->isAfter($user->otp_expires_at)) {
            $user->clearOtp();
            session()->forget(['pending_2fa_user_id', 'pending_2fa_remember', 'pending_2fa_email']);
            $msg = 'The verification code has expired. Please sign in again to receive a fresh code.';
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return back()->withErrors(['otp' => $msg]);
        }

        if ($user->otp_code !== $request->otp) {
            RateLimiter::hit($attemptKey, 300);
            $remaining = 5 - RateLimiter::attempts($attemptKey);
            if ($remaining <= 0) {
                $user->clearOtp();
                session()->forget(['pending_2fa_user_id', 'pending_2fa_remember', 'pending_2fa_email']);
                $msg = 'Too many failed verification attempts. Your code has been invalidated. Please sign in again.';
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['success' => false, 'message' => $msg], 429);
                }
                return redirect()->route('login')->withErrors(['email' => $msg]);
            }

            $msg = "The verification code is incorrect. {$remaining} attempts remaining.";
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return back()->withErrors(['otp' => $msg])->withInput();
        }

        RateLimiter::clear($attemptKey);

        // Clear OTP & Session
        $user->clearOtp();
        session()->forget(['pending_2fa_user_id', 'pending_2fa_remember', 'pending_2fa_email']);

        Auth::login($user, $remember);
        $request->session()->regenerate();

        if ($user->role === 'admin') {
            return redirect()->intended(route('admin.dashboard'));
        }

        return redirect()->intended(route('home'))->with('success', 'Welcome back, ' . $user->name . '!');
    };

    Route::post('/verify-otp', $verifyOtpHandler)->name('verification.otp')->middleware('throttle:otp_verify');
    Route::post('/verify-2fa', $verifyOtpHandler)->name('verification.2fa')->middleware('throttle:otp_verify');

    $resendOtpHandler = function (Request $request) {
        $pendingUserId = session('pending_2fa_user_id');
        if (!$pendingUserId) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Session expired. Please sign in again.'], 400);
            }
            return redirect()->route('login')->withErrors(['email' => 'Session expired. Please sign in again.']);
        }

        $user = User::find($pendingUserId);
        if (!$user) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'User not found.'], 404);
            }
            return redirect()->route('login')->withErrors(['email' => 'User not found. Please sign in again.']);
        }

        $resendThrottleKey = 'resend_otp:' . $user->id;
        if (RateLimiter::tooManyAttempts($resendThrottleKey, 3)) {
            $seconds = RateLimiter::availableIn($resendThrottleKey);
            $msg = 'Please wait ' . $seconds . ' seconds before requesting another code.';
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 429);
            }
            return back()->withErrors(['otp' => $msg]);
        }
        RateLimiter::hit($resendThrottleKey, 300);

        $otp = $user->generateOtp(10);

        try {
            \Illuminate\Support\Facades\Notification::route('mail', $user->email)
                ->notify(new \App\Notifications\TwoFactorOTP($otp, $user->name));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Failed to resend login OTP: ' . $e->getMessage());
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'A new verification code has been sent to your email.',
            ]);
        }

        return back()->with('status', 'A new verification code has been sent to your email.');
    };

    Route::post('/resend-otp', $resendOtpHandler)->name('verification.store')->middleware('throttle:otp_resend');
    Route::post('/resend-2fa', $resendOtpHandler)->name('verification.2fa.resend')->middleware('throttle:otp_resend');

    // Mail OTP Theme Preview Route (for local testing & visual review only)
    Route::get('/preview-mail/otp', function () {
        if (! app()->environment('local', 'testing')) {
            abort(404);
        }

        $otp = request('otp', '4829');
        $user = (object) ['name' => request('name', 'Lady Eleanor')];
        $appName = \App\Models\Setting::appName();
        $storeLogo = \App\Models\Setting::get('site_logo');

        return view('emails.two-factor-otp', [
            'otp'             => $otp,
            'user'            => $user,
            'appName'         => $appName,
            'storeLogo'       => $storeLogo,
            'supportEmail'    => \App\Models\Setting::supportEmail(),
            'supportPhone'    => \App\Models\Setting::supportPhone(),
            'supportWhatsapp' => \App\Models\Setting::get('support_whatsapp', \App\Models\Setting::supportPhone()),
            'storeAddress'    => \App\Models\Setting::storeAddress(),
        ]);
    })->name('email.preview.otp');

    Route::get('/register', function () {
        $theme = \App\Models\Template::active();
        return view('auth.register', compact('theme'));
    })->name('register');

    Route::post('/register', function (Request $request) {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
            'phone'    => ['nullable', 'string', 'max:50'],
        ]);

        $user = User::forceCreate([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'phone'    => $request->input('phone'),
            'password' => Hash::make($validated['password']),
            'role'     => 'customer',
            'is_staff' => false,
        ]);

        // Auto-enroll into baseline Loyalty Tier if available
        try {
            if (class_exists(\App\Models\LoyaltyTier::class) && class_exists(\App\Models\LoyaltyAccount::class)) {
                $baseTier = \App\Models\LoyaltyTier::orderBy('min_spend', 'asc')->first();
                if ($baseTier) {
                    \App\Models\LoyaltyAccount::firstOrCreate(
                        ['user_id' => $user->id],
                        [
                            'loyalty_tier_id'        => $baseTier->id,
                            'points_balance'         => 100, // 100 Welcome Points
                            'points_earned_lifetime' => 100,
                        ]
                    );
                }
            }
        } catch (\Throwable $e) {}

        // Auto-subscribe to newsletter if selected
        if ($request->boolean('newsletter', true) && class_exists(\App\Models\Subscriber::class)) {
            try {
                \App\Models\Subscriber::firstOrCreate(
                    ['email' => $user->email],
                    ['is_active' => true]
                );
            } catch (\Throwable $e) {}
        }

        Auth::login($user);

        return redirect()->intended(route('home'))->with('success', 'Welcome to ' . config('app.name', 'Nex-on') . '! Your account has been created.');
    })->middleware('throttle:register');

    Route::get('/forgot-password', function () {
        return view('auth.login', ['forgot' => true]);
    })->name('password.request');

    Route::post('/forgot-password', function (Request $request) {
        $request->validate(['email' => ['required', 'email']]);

        // Send reset link using secure hashed tokens in password_reset_tokens
        PasswordBroker::sendResetLink(
            $request->only('email')
        );

        // Always return generic success message to prevent user account enumeration (M-02)
        return back()->with('status', 'If an account exists with that email address, we have emailed your password reset link.');
    })->name('password.email')->middleware('throttle:password_reset');

    Route::get('/reset-password/{token}', function (string $token) {
        return view('auth.login', ['token' => $token, 'email' => request('email')]);
    })->name('password.reset');

    Route::post('/reset-password', function (Request $request) {
        $request->validate([
            'token'    => ['required'],
            'email'    => ['required', 'email'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $status = PasswordBroker::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, string $password) {
                $user->forceFill([
                    'password'       => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                Auth::login($user);
            }
        );

        return $status === PasswordBroker::PASSWORD_RESET
            ? redirect()->route('home')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    })->name('password.store')->middleware('throttle:password_reset');

    // Two-Factor Authenticator Challenge (TOTP RFC 6238)
    Route::get('/two-factor-challenge', [\App\Http\Controllers\Auth\TwoFactorAuthenticatedSessionController::class, 'create'])->name('two-factor.login');
    Route::post('/two-factor-challenge', [\App\Http\Controllers\Auth\TwoFactorAuthenticatedSessionController::class, 'store'])->middleware('throttle:6,1');
});

Route::middleware('auth')->group(function () {
    // User Profile Management (/profile)
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('password.update');
    Route::delete('/profile', [\App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');

    // Two-Factor Authenticator App (TOTP) Management
    Route::post('/user/two-factor-authentication', [\App\Http\Controllers\Auth\TwoFactorAuthenticationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('two-factor.enable');
    Route::post('/user/confirmed-two-factor-authentication', [\App\Http\Controllers\Auth\TwoFactorAuthenticationController::class, 'confirm'])
        ->middleware('throttle:6,1')
        ->name('two-factor.confirm');
    Route::delete('/user/two-factor-authentication', [\App\Http\Controllers\Auth\TwoFactorAuthenticationController::class, 'destroy'])
        ->middleware('throttle:6,1')
        ->name('two-factor.disable');
    Route::post('/user/two-factor-recovery-codes', [\App\Http\Controllers\Auth\TwoFactorAuthenticationController::class, 'regenerateRecoveryCodes'])
        ->middleware('throttle:6,1')
        ->name('two-factor.regenerate-recovery-codes');

    Route::post('/logout', function (Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    })->name('logout');
});
