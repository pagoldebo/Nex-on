@extends('admin.layout')

@section('title', 'Cookies & Social Analytics Tag Manager')

@section('content')
<div class="space-y-6 pb-12">

    <!-- Header & Action Bar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 text-[10px] font-bold rounded-md bg-indigo-50 text-indigo-600 border border-indigo-200/50 uppercase font-mono tracking-wider">Privacy &amp; Analytics</span>
                <span class="text-xs text-slate-400 font-medium">Phase 2 &bull; Tag Manager &amp; Cookie Consent</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 mt-1">Cookies &amp; Social Analytics Tag Manager</h1>
            <p class="text-xs text-slate-500 mt-0.5">Centralized tracking script configuration for GA4, Meta Pixel, and GTM with built-in GDPR/ePrivacy compliant cookie consent.</p>
        </div>

        <button type="submit" form="analyticsForm"
                class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-md shadow-indigo-600/20 flex items-center gap-2 transition self-start md:self-auto">
            <svg class="w-4 h-4 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>Save Tag Configurations</span>
        </button>
    </div>

    <!-- Main Configuration Form -->
    <form id="analyticsForm" method="POST" action="{{ route('admin.settings.analytics.update') }}" class="space-y-6 text-xs">
        @csrf

        <!-- Cookie Consent Banner Settings -->
        <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">GDPR &amp; ePrivacy Cookie Consent Banner</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Displays a floating consent banner on storefront allowing visitors to accept, decline, or customize tracking preferences.</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="cookie_banner_enabled" value="1" class="sr-only peer" {{ $settings['cookie_banner_enabled'] ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                </label>
            </div>

            <div class="pt-3 border-t border-slate-100 max-w-md">
                <label class="block font-bold text-slate-700 mb-1">Privacy &amp; Cookie Policy URL</label>
                <input type="text" name="cookie_policy_url" value="{{ $settings['cookie_policy_url'] }}"
                       class="w-full px-3.5 py-2 rounded-xl border border-slate-200 font-mono text-slate-900 focus:outline-none focus:border-indigo-500">
                <span class="text-[10px] text-slate-400">Linked within the storefront cookie preference modal</span>
            </div>
        </div>

        <!-- Analytics & Marketing Tracking Tags Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Google Tag Manager (GTM) -->
            <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold">G</div>
                        <h3 class="font-bold text-slate-900">Google Tag Manager (GTM)</h3>
                    </div>
                    <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-slate-100 text-slate-600 font-bold">Recommended</span>
                </div>
                <p class="text-[11px] text-slate-500">Deploy custom tags, triggers, and remarketing pixels via GTM container container snippet.</p>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">GTM Container ID</label>
                    <input type="text" name="analytics_gtm_id" value="{{ $settings['analytics_gtm_id'] }}" placeholder="GTM-XXXXXXX"
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-200 font-mono text-slate-900 focus:outline-none focus:border-indigo-500">
                </div>
            </div>

            <!-- Google Analytics 4 (GA4) -->
            <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center font-bold">📊</div>
                        <h3 class="font-bold text-slate-900">Google Analytics 4 (GA4)</h3>
                    </div>
                    <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-slate-100 text-slate-600 font-bold">e-Commerce</span>
                </div>
                <p class="text-[11px] text-slate-500">Track page views, product impressions, add-to-cart events, and purchase conversions.</p>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">GA4 Measurement ID</label>
                    <input type="text" name="analytics_ga4_id" value="{{ $settings['analytics_ga4_id'] }}" placeholder="G-XXXXXXXXXX"
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-200 font-mono text-slate-900 focus:outline-none focus:border-indigo-500">
                </div>
            </div>

            <!-- Meta Pixel (Facebook & Instagram) -->
            <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">∞</div>
                        <h3 class="font-bold text-slate-900">Meta Pixel (Facebook &amp; Instagram)</h3>
                    </div>
                    <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-slate-100 text-slate-600 font-bold">Ads &amp; ROAS</span>
                </div>
                <p class="text-[11px] text-slate-500">Measure advertising performance and build high-intent retargeting audiences.</p>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Meta Pixel ID</label>
                    <input type="text" name="analytics_meta_pixel_id" value="{{ $settings['analytics_meta_pixel_id'] }}" placeholder="e.g. 123456789012345"
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-200 font-mono text-slate-900 focus:outline-none focus:border-indigo-500">
                </div>
            </div>

            <!-- TikTok Pixel & Pinterest Tag -->
            <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center font-bold">🎵</div>
                        <h3 class="font-bold text-slate-900">TikTok &amp; Pinterest Ads</h3>
                    </div>
                    <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-slate-100 text-slate-600 font-bold">Social</span>
                </div>
                <p class="text-[11px] text-slate-500">Track short-form video shopping and visual inspiration conversions.</p>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">TikTok Pixel ID</label>
                        <input type="text" name="analytics_tiktok_pixel_id" value="{{ $settings['analytics_tiktok_pixel_id'] }}" placeholder="e.g. C123456789"
                               class="w-full px-3 py-2 rounded-xl border border-slate-200 font-mono text-slate-900 focus:outline-none focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Pinterest Tag ID</label>
                        <input type="text" name="analytics_pinterest_tag_id" value="{{ $settings['analytics_pinterest_tag_id'] }}" placeholder="e.g. 2612345678"
                               class="w-full px-3 py-2 rounded-xl border border-slate-200 font-mono text-slate-900 focus:outline-none focus:border-indigo-500">
                    </div>
                </div>
            </div>

        </div>
    </form>

</div>
@endsection
