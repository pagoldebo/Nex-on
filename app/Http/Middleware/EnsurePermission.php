<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePermission
{
    /**
     * Handle an incoming request, strictly verifying that the authenticated user
     * possesses at least one of the required permission slugs.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$permissions
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        $user = $request->user();

        if (! $user) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->guest(route('login'));
        }

        // Super-admin has unrestricted server-side access to all administrative domains
        if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
            return $next($request);
        }

        // Parse and normalize permission slugs (supports commas, variadics, and pipe delimiters)
        $requiredPermissions = [];
        foreach ($permissions as $permGroup) {
            foreach (explode('|', $permGroup) as $slug) {
                $slug = trim($slug);
                if ($slug !== '') {
                    $requiredPermissions[] = $slug;
                }
            }
        }

        // If no specific permissions were defined, allow request
        if (empty($requiredPermissions)) {
            return $next($request);
        }

        // Enforce server-side authorization: user must have at least one of the requested permissions
        foreach ($requiredPermissions as $permission) {
            if (method_exists($user, 'hasPermission') && $user->hasPermission($permission)) {
                return $next($request);
            }
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message'              => 'Forbidden. You do not have the required permissions to perform this action.',
                'required_permissions' => $requiredPermissions,
            ], 403);
        }

        abort(403, 'Forbidden. You do not have the required permissions to perform this action.');
    }
}
