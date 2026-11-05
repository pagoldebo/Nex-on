<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Template;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * AdminThemeController
 *
 * Manages which storefront theme is active.
 * Only one template row may have is_active = true at a time.
 *
 * Routes (web, auth:admin middleware assumed):
 *   GET    /admin/themes          → index
 *   POST   /admin/themes/{template}/activate  → activate
 */
class AdminThemeController extends Controller
{
    // ── Index ─────────────────────────────────────────────────────────────────

    public function index(): View
    {
        $templates = Template::orderBy('name')->get();

        return view('admin.themes', compact('templates'));
    }

    // ── Activate ──────────────────────────────────────────────────────────────

    public function activate(Request $request, Template $template): RedirectResponse
    {
        // Deactivate all, then activate the chosen one — single transaction
        \DB::transaction(function () use ($template) {
            Template::query()->update(['is_active' => false]);
            $template->update(['is_active' => true]);
        });

        Template::clearActiveCache();

        return redirect()
            ->route('admin.themes.index')
            ->with('success', "\"{$template->name}\" is now the active storefront theme.");
    }

    // ── Admin Panel Theme Toggle ──────────────────────────────────────────────

    public function toggleAdminTheme(Request $request)
    {
        $current = $request->cookie('admin_theme', 'light');
        $new = ($current === 'dark') ? 'light' : 'dark';

        if ($request->filled('theme')) {
            $requested = (string) $request->input('theme');
            $new = in_array($requested, ['light', 'dark'], true) ? $requested : 'light';
        }

        $cookie = cookie('admin_theme', $new, 60 * 24 * 365, '/', null, false, false);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'theme'   => $new,
                'is_dark' => ($new === 'dark'),
            ])->cookie($cookie);
        }

        return back()
            ->withCookie($cookie)
            ->with('success', 'Admin theme switched to ' . ($new === 'dark' ? 'Black Theme' : 'White Theme') . '.');
    }
}
