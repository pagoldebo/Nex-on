<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Supported application locales and native metadata.
     */
    public const SUPPORTED_LOCALES = [
        'en' => ['name' => 'English',  'native' => 'English', 'dir' => 'ltr', 'flag' => '🇬🇧'],
        'hi' => ['name' => 'Hindi',    'native' => 'हिन्दी',  'dir' => 'ltr', 'flag' => '🇮🇳'],
        'es' => ['name' => 'Spanish',  'native' => 'Español', 'dir' => 'ltr', 'flag' => '🇪🇸'],
        'fr' => ['name' => 'French',   'native' => 'Français','dir' => 'ltr', 'flag' => '🇫🇷'],
        'ar' => ['name' => 'Arabic',   'native' => 'العربية', 'dir' => 'rtl', 'flag' => '🇦🇪'],
    ];

    /**
     * Handle an incoming request and initialize the active locale.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->query('locale')
            ?? session('locale')
            ?? $request->cookie('locale')
            ?? config('app.locale', 'en');

        if (!array_key_exists($locale, self::SUPPORTED_LOCALES)) {
            $locale = config('app.fallback_locale', 'en');
        }

        App::setLocale($locale);

        // Store selected locale in session if not already stored
        if (!session()->has('locale') || session('locale') !== $locale) {
            session(['locale' => $locale]);
        }

        return $next($request);
    }
}
