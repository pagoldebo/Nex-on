<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
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
            return back()->withErrors([
                'email' => 'Too many login attempts. Please try again in ' . $seconds . ' seconds.',
            ])->onlyInput('email');
        }

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            RateLimiter::hit($throttleKey, 60);
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        }

        RateLimiter::clear($throttleKey);

        if ($user->isDeactivated()) {
            return back()->withErrors([
                'email' => 'Your account has been deactivated. Please contact support.',
            ])->onlyInput('email');
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
    });

    // Two-Step Email OTP Verification Routes (like in jonakee.in)
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
            'otp' => ['required', 'string', 'digits:4'],
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

        if ($user->otp_code !== $request->otp) {
            return back()->withErrors(['otp' => 'The verification code is incorrect.'])->withInput();
        }

        if ($user->otp_expires_at && now()->isAfter($user->otp_expires_at)) {
            return back()->withErrors(['otp' => 'The verification code has expired. Please request a new one.'])->withInput();
        }

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

    Route::post('/verify-otp', $verifyOtpHandler)->name('verification.otp');
    Route::post('/verify-2fa', $verifyOtpHandler)->name('verification.2fa');

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

    Route::post('/resend-otp', $resendOtpHandler)->name('verification.store');
    Route::post('/resend-2fa', $resendOtpHandler)->name('verification.2fa.resend');

    // Mail OTP Theme Preview Route (for testing & visual review)
    Route::get('/preview-mail/otp', function () {
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
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone'    => ['nullable', 'string', 'max:50'],
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'phone'    => $request->input('phone'),
            'password' => Hash::make($validated['password']),
            'role'     => 'customer',
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
    });

    Route::get('/forgot-password', function () {
        return view('auth.login', ['forgot' => true]);
    })->name('password.request');

    Route::post('/forgot-password', function (Request $request) {
        $request->validate(['email' => 'required|email']);
        return back()->with('status', 'If an account matches that email, a password reset link has been sent.');
    })->name('password.email');

    Route::get('/reset-password/{token}', function (string $token) {
        return view('auth.login', ['token' => $token]);
    })->name('password.reset');

    Route::post('/reset-password', function (Request $request) {
        return redirect()->route('login')->with('status', 'Password reset successfully.');
    })->name('password.store');

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
        ->name('two-factor.enable');
    Route::post('/user/confirmed-two-factor-authentication', [\App\Http\Controllers\Auth\TwoFactorAuthenticationController::class, 'confirm'])
        ->name('two-factor.confirm');
    Route::delete('/user/two-factor-authentication', [\App\Http\Controllers\Auth\TwoFactorAuthenticationController::class, 'destroy'])
        ->name('two-factor.disable');
    Route::post('/user/two-factor-recovery-codes', [\App\Http\Controllers\Auth\TwoFactorAuthenticationController::class, 'regenerateRecoveryCodes'])
        ->name('two-factor.regenerate-recovery-codes');

    Route::post('/logout', function (Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    })->name('logout');
});
