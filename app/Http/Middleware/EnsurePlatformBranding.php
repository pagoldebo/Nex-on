<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class EnsurePlatformBranding
{
    /**
     * Exact branding writeup required across all theme footers.
     */
    public const BRANDING_WRITEUP = '• Powered by Nex-on';

    /**
     * Required attribution text that must never be removed or altered.
     */
    public const PLATFORM_BRANDING = 'Powered by Nex-on';

    /**
     * Exact platform name.
     */
    public const PLATFORM_NAME = 'Nex-on';

    /**
     * Platform business suite label.
     */
    public const PLATFORM_SUITE = 'E-commerce Business Suite';

    /**
     * Handle an incoming request, pass branding to views, and enforce attribution.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Pass the exact writeup and platform branding to all views and components through middleware
        View::share('platformName', self::PLATFORM_NAME);
        View::share('platformSuite', self::PLATFORM_SUITE);
        View::share('platformBranding', self::BRANDING_WRITEUP);
        View::share('platformBrandingHtml', '<span class="platform-branding-attribution inline-block" data-nexon-platform-branding="verified">' . e(self::BRANDING_WRITEUP) . '</span>');

        $response = $next($request);

        // Only process successful HTML web responses (skip API, JSON, downloads)
        if (! $response->isSuccessful() || $request->is('api/*') || $request->is('_debugbar/*')) {
            return $response;
        }

        $contentType = $response->headers->get('Content-Type', '');
        if (! empty($contentType) && ! str_contains($contentType, 'text/html')) {
            return $response;
        }

        if (! method_exists($response, 'getContent') || ! method_exists($response, 'setContent')) {
            return $response;
        }

        $content = $response->getContent();
        if (empty($content) || ! is_string($content)) {
            return $response;
        }

        // 2. Anti-tampering: Verify data-nexon-platform-branding container if present
        if (preg_match('/<([a-z0-9]+)[^>]*data-nexon-platform-branding[^>]*>(.*?)<\/\1>/is', $content, $matches)) {
            if (! str_contains($matches[0], self::PLATFORM_BRANDING)) {
                $restored = preg_replace(
                    '/(<[a-z0-9]+[^>]*data-nexon-platform-branding[^>]*>).*?(<\/[a-z0-9]+>)/is',
                    '$1' . e(self::BRANDING_WRITEUP) . '$2',
                    $matches[0]
                );
                $content = str_replace($matches[0], $restored, $content);
                $response->setContent($content);
            }
        }

        // 3. Fallback Injection: If the writeup was removed or missing completely
        if (! str_contains($content, self::PLATFORM_BRANDING)) {
            $badge = '<div class="project-nexon-badge" style="display:block !important;visibility:visible !important;opacity:0.8 !important;text-align:center !important;font-size:11px !important;padding:8px 0 !important;margin:0 auto !important;pointer-events:auto !important;color:inherit !important;"><span class="platform-branding-attribution inline-block" data-nexon-platform-branding="verified">' . e(self::BRANDING_WRITEUP) . '</span></div>';

            if (str_contains($content, '</footer>')) {
                $content = preg_replace('/(<\/footer>)/i', $badge . '$1', $content, 1);
            } elseif (str_contains($content, '</body>')) {
                $content = preg_replace('/(<\/body>)/i', $badge . '$1', $content, 1);
            } else {
                $content .= $badge;
            }

            $response->setContent($content);
        }

        return $response;
    }
}
