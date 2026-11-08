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

        // If the user has a pure customer role and has no staff/admin roles or departments assigned, block access
        if ($user->isCustomer() && ! $user->isAdmin() && ! $user->roles()->exists() && ! $user->departments()->exists()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Forbidden. Administrator privileges required.'], 403);
            }
            abort(403, 'Forbidden. Administrator access required.');
        }

        return $next($request);
    }
}

