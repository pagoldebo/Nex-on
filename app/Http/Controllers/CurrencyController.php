<?php

namespace App\Http\Controllers;

use App\Services\CurrencyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Class CurrencyController
 *
 * Handles switching customer storefront currency preference via AJAX or traditional GET/POST navigation.
 */
class CurrencyController extends Controller
{
    public function __construct(
        protected CurrencyService $currencyService
    ) {}

    /**
     * Switch user currency via POST request (supports both AJAX and form submission).
     */
    public function switch(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'currency' => 'required|string|size:3',
        ]);

        $code = strtoupper(trim($validated['currency']));
        $success = $this->currencyService->setUserCurrency($code);

        if (! $success) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => "Currency [{$code}] is either unsupported or disabled.",
                ], 422);
            }
            return back()->with('error', "Currency [{$code}] is currently not available.");
        }

        $cookie = cookie('currency', $code, 60 * 24 * 30); // 30 days

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success'  => true,
                'currency' => $code,
                'config'   => $this->currencyService->getJsConfig(),
            ])->withCookie($cookie);
        }

        return back()->withCookie($cookie)->with('success', "Currency switched to {$code}.");
    }

    /**
     * Switch user currency via GET route link (e.g. /currency/USD).
     */
    public function switchGet(string $code): RedirectResponse
    {
        $code = strtoupper(trim($code));
        if (! $this->currencyService->setUserCurrency($code)) {
            return back()->with('error', "Currency [{$code}] is currently not available.");
        }

        $cookie = cookie('currency', $code, 60 * 24 * 30);

        return back()->withCookie($cookie);
    }
}
