<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustHosts(at: fn () => array_values(array_filter([
            config('app.url') ? parse_url(config('app.url'), PHP_URL_HOST) : null,
            'localhost',
            '127.0.0.1',
            '*.test',
        ])));
        $middleware->encryptCookies();
        $middleware->validateCsrfTokens(except: [
            'webhook/*',
        ]);
        $middleware->web(append: [
            \App\Http\Middleware\SecurityHeadersMiddleware::class,
            \App\Http\Middleware\SetLocale::class,
            \App\Http\Middleware\EnsurePlatformBranding::class,
        ]);
        $middleware->alias([
            'permission' => \App\Http\Middleware\EnsurePermission::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
