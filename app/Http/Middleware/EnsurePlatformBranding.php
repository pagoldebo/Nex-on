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

        return $next($request);
    }
}
