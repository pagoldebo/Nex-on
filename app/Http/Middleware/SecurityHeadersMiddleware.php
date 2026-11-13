<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    /**
     * Handle an incoming request and add defensive HTTP security headers.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Prevent MIME-type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Prevent clickjacking
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Legacy XSS filter for older browsers
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Control referrer information
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Restrict browser features
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()');

        // Prevent cross-domain policy files (Flash/PDF)
        $response->headers->set('X-Permitted-Cross-Domain-Policies', 'none');

        // Content Security Policy
        $response->headers->set('Content-Security-Policy',
            "default-src 'self'; " .
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://cdn.tailwindcss.com https://cdnjs.cloudflare.com https://www.googletagmanager.com https://connect.facebook.net; " .
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; " .
            "font-src 'self' data: https://fonts.gstatic.com https://cdnjs.cloudflare.com; " .
            "img-src 'self' data: blob: https:; " .
            "connect-src 'self' https://www.google-analytics.com https://region1.google-analytics.com https://connect.facebook.net; " .
            "frame-src 'self' https:; " .
            "object-src 'none'; " .
            "base-uri 'self'; " .
            "form-action 'self';"
        );

        // Force HTTPS (only send over TLS connections)
        if ($request->secure() || app()->environment('production', 'staging')) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}

