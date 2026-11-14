<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
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

