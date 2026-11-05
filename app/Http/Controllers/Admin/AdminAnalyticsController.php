<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminAnalyticsController extends Controller
{
    /**
     * Display Social Analytics & Cookie Consent Settings panel.
     */
    public function index(): View
    {
        $settings = [
            'cookie_banner_enabled'    => Setting::get('cookie_banner_enabled', '1') === '1',
            'analytics_gtm_id'         => Setting::get('analytics_gtm_id', ''),
            'analytics_ga4_id'         => Setting::get('analytics_ga4_id', ''),
            'analytics_meta_pixel_id'  => Setting::get('analytics_meta_pixel_id', ''),
            'analytics_tiktok_pixel_id'=> Setting::get('analytics_tiktok_pixel_id', ''),
            'analytics_pinterest_tag_id'=> Setting::get('analytics_pinterest_tag_id', ''),
            'cookie_policy_url'        => Setting::get('cookie_policy_url', '/privacy'),
        ];

        return view('admin.settings.analytics', compact('settings'));
    }

    /**
     * Update Analytics & Tag Manager settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $fields = [
            'cookie_banner_enabled'     => $request->has('cookie_banner_enabled') ? '1' : '0',
            'analytics_gtm_id'          => trim($request->input('analytics_gtm_id', '')),
            'analytics_ga4_id'          => trim($request->input('analytics_ga4_id', '')),
            'analytics_meta_pixel_id'   => trim($request->input('analytics_meta_pixel_id', '')),
            'analytics_tiktok_pixel_id' => trim($request->input('analytics_tiktok_pixel_id', '')),
            'analytics_pinterest_tag_id'=> trim($request->input('analytics_pinterest_tag_id', '')),
            'cookie_policy_url'         => trim($request->input('cookie_policy_url', '/privacy')),
        ];

        foreach ($fields as $key => $val) {
            Setting::set($key, $val);
        }

        Setting::clearCache();

        return back()->with('success', 'Marketing tracking tags & cookie privacy settings updated successfully!');
    }
}
