<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Template;
use App\Services\Auth\TotpService;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile and security form.
     */
    public function edit(Request $request, TotpService $totp): View
    {
        $user = $request->user();
        $theme = Template::active();

        $qrCodeSvgUrl = null;
        $secretKey    = null;
        $recoveryCodes = [];

        if ($user->two_factor_secret) {
            try {
                $secretKey = decrypt($user->two_factor_secret);
                $qrCodeSvgUrl = $totp->getQrCodeSvgUrl(config('app.name', 'Project Nexon'), $user->email, $secretKey);
                $recoveryCodes = $user->getRecoveryCodes();
            } catch (\Throwable $e) {
                // If decryption fails, safely leave null
            }
        }

        return view('profile.edit', compact('user', 'theme', 'qrCodeSvgUrl', 'secretKey', 'recoveryCodes'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        $user->fill([
            'name'  => strip_tags(trim($request->validated('name'))),
            'email' => strtolower(trim($request->validated('email'))),
            'phone' => $request->validated('phone') ? trim($request->validated('phone')) : $user->phone,
        ]);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;

            if ($user instanceof MustVerifyEmail) {
                $user->sendEmailVerificationNotification();
            }
        }

        $user->save();

        return redirect()->route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Update the user's password securely with current password verification.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'confirmed', Password::defaults()],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('profile.edit')->with('status', 'password-updated');
    }

    /**
     * Delete the user's account with password confirmation.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::guard('web')->logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}

