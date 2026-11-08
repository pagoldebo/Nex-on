<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\TotpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class TwoFactorAuthenticationController extends Controller
{
    public function __construct(
        protected TotpService $totp
    ) {}

    /**
     * Enable two-factor authentication for the authenticated user (generates secret & recovery codes).
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        $secret = $this->totp->generateSecretKey();
        $recoveryCodes = $this->totp->generateRecoveryCodes();

        $user->forceFill([
            'two_factor_secret'         => encrypt($secret),
            'two_factor_recovery_codes' => encrypt(json_encode($recoveryCodes)),
            'two_factor_confirmed_at'   => null, // Pending 6-digit verification
        ])->save();

        return back()->with('status', 'two-factor-authentication-enabled');
    }

    /**
     * Confirm two-factor authentication with a 6-digit code from the authenticator app.
     */
    public function confirm(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string'],
        ]);

        $user = $request->user();

        if (! $user->two_factor_secret) {
            return back();
        }

        $attemptKey = '2fa_confirm:' . $user->id;
        if (RateLimiter::tooManyAttempts($attemptKey, 5)) {
            $seconds = RateLimiter::availableIn($attemptKey);
            throw ValidationException::withMessages([
                'code' => ["Too many failed confirmation attempts. Please wait {$seconds} seconds before trying again."],
            ]);
        }

        $code = str_replace(' ', '', (string) $request->input('code'));
        $secret = decrypt($user->two_factor_secret);

        if (! $this->totp->verify($secret, $code)) {
            RateLimiter::hit($attemptKey, 300);
            throw ValidationException::withMessages([
                'code' => ['The provided two-factor code was invalid. Check your authenticator app and try again.'],
            ]);
        }

        RateLimiter::clear($attemptKey);

        $user->forceFill([
            'two_factor_confirmed_at' => now(),
        ])->save();

        return back()->with('status', 'two-factor-authentication-confirmed');
    }

    /**
     * Disable two-factor authentication for the user.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();

        // If 2FA is actively confirmed and armed, require current password verification
        if ($user->hasTwoFactorEnabled()) {
            $request->validate([
                'password' => ['required', 'string'],
            ]);

            if (! Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages([
                    'password' => ['The provided password does not match our records.'],
                ]);
            }
        }

        $user->forceFill([
            'two_factor_secret'         => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at'   => null,
        ])->save();

        return back()->with('status', 'two-factor-authentication-disabled');
    }

    /**
     * Regenerate two-factor recovery codes.
     */
    public function regenerateRecoveryCodes(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (! $user->two_factor_secret) {
            return back();
        }

        if ($user->hasTwoFactorEnabled() && $request->filled('password')) {
            if (! Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages([
                    'password' => ['The provided password does not match our records.'],
                ]);
            }
        }

        $recoveryCodes = $this->totp->generateRecoveryCodes();

        $user->forceFill([
            'two_factor_recovery_codes' => encrypt(json_encode($recoveryCodes)),
        ])->save();

        return back()->with('status', 'recovery-codes-generated');
    }
}

