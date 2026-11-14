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
        // Generate a cryptographically secure per-request CSP nonce (M-01)
        $nonce = base64_encode(random_bytes(16));
        $request->attributes->set('csp_nonce', $nonce);
        if (function_exists('app')) {
            app()->instance('csp_nonce', $nonce);
        }
        if (function_exists('view')) {
            view()->share('cspNonce', $nonce);
        }

        $response = $next($request);

        // Prevent MIME-type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Prevent clickjacking
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Control referrer information
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Restrict browser features
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()');

        // Prevent cross-domain policy files (Flash/PDF)
        $response->headers->set('X-Permitted-Cross-Domain-Policies', 'none');

        // Content Security Policy - Nonce-based strict CSP without unsafe-inline or unsafe-eval (M-01)
        $csp = "default-src 'self'; " .
            "script-src 'self' 'nonce-{$nonce}' 'strict-dynamic' https://cdn.jsdelivr.net https://cdn.tailwindcss.com https://cdnjs.cloudflare.com https://www.googletagmanager.com https://connect.facebook.net; " .
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; " .
            "font-src 'self' data: https://fonts.gstatic.com https://cdnjs.cloudflare.com; " .
            "img-src 'self' data: blob: https:; " .
            "connect-src 'self' https://www.google-analytics.com https://region1.google-analytics.com https://connect.facebook.net; " .
            "frame-src 'self' https://api.razorpay.com https://checkout.razorpay.com https://js.stripe.com https://www.paypal.com https://www.sandbox.paypal.com https://www.youtube.com https://www.youtube-nocookie.com https://www.google.com; " .
            "object-src 'none'; " .
            "base-uri 'self'; " .
            "form-action 'self'; " .
            "frame-ancestors 'self';";

        // Force HTTPS & Upgrade Insecure Requests (only send over TLS connections)
        if ($request->secure() || (function_exists('app') && app()->environment('production', 'staging'))) {
            $csp .= " upgrade-insecure-requests;";
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        $response->headers->set('Content-Security-Policy', $csp);

        // Inject the cryptographic nonce into HTML script tags so inline and external scripts pass CSP
        $contentType = $response->headers->get('Content-Type', '');
        if (str_contains($contentType, 'text/html')) {
            $content = $response->getContent();
            if ($content && is_string($content)) {
                $content = preg_replace_callback('/<script\b(?![^>]*\bnonce=)([^>]*)>/i', function ($matches) use ($nonce) {
                    return "<script nonce=\"{$nonce}\"{$matches[1]}>";
                }, $content);
                $response->setContent($content);
            }
        }

        return $response;
    }
}

