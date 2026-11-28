<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request, ensuring the user has administrative or authorized staff privileges.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->guest(route('login'));
        }

        // Immediately terminate access if the account is deactivated or inactive staff
        if ($user->isDeactivated() || ($user->is_staff && $user->employment_status === 'inactive')) {
            Auth::logout();
            if ($request->hasSession()) {
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Account deactivated.'], 403);
            }
            return redirect()->route('login')->withErrors(['email' => 'Your account has been deactivated.']);
        }

        // Allow access only to users who are system administrators or authorized staff members with assigned RBAC roles
        $isAuthorized = (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin())
            || (method_exists($user, 'isAdmin') && $user->isAdmin())
            || ($user->is_staff && $user->roles()->exists());

        if (! $isAuthorized) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Forbidden. Administrator privileges required.'], 403);
            }
            abort(403, 'Forbidden. Administrator access required.');
        }

        return $next($request);
    }
}

