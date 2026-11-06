<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\TotpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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

        $code = str_replace(' ', '', (string) $request->input('code'));
        $secret = decrypt($user->two_factor_secret);

        if (! $this->totp->verify($secret, $code)) {
            throw ValidationException::withMessages([
                'code' => ['The provided two-factor code was invalid. Check your authenticator app and try again.'],
            ]);
        }

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
        $request->validate([
            'password' => ['required', 'string'],
        ]);

        if (! Hash::check($request->password, $request->user()->password)) {
            throw ValidationException::withMessages([
                'password' => ['The provided password does not match our records.'],
            ]);
        }

        $user = $request->user();
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

        $recoveryCodes = $this->totp->generateRecoveryCodes();

        $user->forceFill([
            'two_factor_recovery_codes' => encrypt(json_encode($recoveryCodes)),
        ])->save();

        return back()->with('status', 'recovery-codes-generated');
    }
}
