@php
    $cookieBannerEnabled = \App\Models\Setting::get('cookie_banner_enabled', '0') === '1';
    $gtmId               = \App\Models\Setting::get('analytics_gtm_id', '');
    $ga4Id               = \App\Models\Setting::get('analytics_ga4_id', '');
    $metaPixelId         = \App\Models\Setting::get('analytics_meta_pixel_id', '');
    $tiktokPixelId       = \App\Models\Setting::get('analytics_tiktok_pixel_id', '');
    $pinterestTagId      = \App\Models\Setting::get('analytics_pinterest_tag_id', '');
    $privacyUrl          = \App\Models\Setting::get('cookie_policy_url', '/privacy');
@endphp

@if($cookieBannerEnabled)
<div x-data="cookieConsentManager({
    gtmId: '{{ $gtmId }}',
    ga4Id: '{{ $ga4Id }}',
    metaPixelId: '{{ $metaPixelId }}',
    tiktokPixelId: '{{ $tiktokPixelId }}',
    pinterestTagId: '{{ $pinterestTagId }}'
})" x-cloak>

    <!-- Floating Cookie Consent Banner -->
    <div x-show="showBanner"
         x-transition:enter="transition ease-out duration-500"
         x-transition:enter-start="opacity-0 translate-y-8"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-8"
         style="position: fixed !important; z-index: 9999;"
         class="fixed bottom-20 md:bottom-6 left-4 right-4 md:left-auto md:right-6 md:max-w-md p-5 rounded-3xl bg-slate-900/95 backdrop-blur-md text-white border border-white/10 shadow-2xl">

        <div class="flex items-start gap-3.5">
            <div class="w-9 h-9 shrink-0 rounded-2xl bg-white/10 flex items-center justify-center text-lg">
                🍪
            </div>
            <div class="space-y-1.5 flex-1">
                <div class="font-bold text-sm text-white flex items-center justify-between">
                    <span>Respecting Your Privacy</span>
                    <button @click="showBanner = false" class="text-white/40 hover:text-white text-base">&times;</button>
                </div>
                <p class="text-xs text-white/70 leading-relaxed">
                    We use cookies and privacy-first tracking to tailor your boutique shopping experience, preserve your shopping cart, and optimize performance.
                    <a href="{{ $privacyUrl }}" class="underline text-white/90 hover:text-white ml-0.5">Privacy Policy</a>.
                </p>

                <!-- Actions -->
                <div class="pt-3 flex flex-wrap items-center gap-2 text-xs">
                    <button @click="acceptAll()" type="button"
                            class="px-4 py-2 rounded-xl bg-white text-slate-900 font-bold hover:bg-slate-100 transition shadow-sm">
                        Accept All
                    </button>

                    <button @click="rejectNonEssential()" type="button"
                            class="px-3.5 py-2 rounded-xl bg-white/10 text-white font-semibold hover:bg-white/20 transition">
                        Reject Non-Essential
                    </button>

                    <button @click="showModal = true" type="button"
                            class="px-3 py-2 text-white/60 hover:text-white transition font-medium underline text-[11px]">
                        Preferences
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Preferences Modal -->
    <div x-show="showModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="position: fixed !important; z-index: 10000;"
         class="fixed inset-0 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">

        <div @click.away="showModal = false"
             class="bg-white rounded-3xl max-w-md w-full p-6 text-slate-900 shadow-2xl space-y-4">

            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-base font-black">Cookie &amp; Privacy Preferences</h3>
                <button @click="showModal = false" type="button" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
            </div>

            <p class="text-xs text-slate-500 leading-relaxed">
                Customize which cookie categories you permit. Necessary cookies are required for shopping bags, currency conversions, and secure checkout.
            </p>

            <div class="space-y-3 text-xs">
                <!-- Necessary (Always Active) -->
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                    <div>
                        <div class="font-bold text-slate-900">Essential &amp; Security</div>
                        <div class="text-[11px] text-slate-500 mt-0.5">Session, cart persistence, security CSRF tokens.</div>
                    </div>
                    <span class="px-2 py-0.5 rounded-full bg-slate-200 text-slate-700 font-mono text-[10px] font-bold">Required</span>
                </div>

                <!-- Analytics -->
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                    <div>
                        <div class="font-bold text-slate-900">Analytics &amp; Performance</div>
                        <div class="text-[11px] text-slate-500 mt-0.5">Google Analytics 4 and GTM page speed metrics.</div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" x-model="prefs.analytics" class="sr-only peer">
                        <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600"></div>
                    </label>
                </div>

                <!-- Marketing & Social -->
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                    <div>
                        <div class="font-bold text-slate-900">Marketing &amp; Personalization</div>
                        <div class="text-[11px] text-slate-500 mt-0.5">Meta Pixel, TikTok, and collection recommendations.</div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" x-model="prefs.marketing" class="sr-only peer">
                        <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600"></div>
                    </label>
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 flex justify-end gap-2 text-xs">
                <button @click="showModal = false" type="button"
                        class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 font-semibold hover:bg-slate-200 transition">
                    Cancel
                </button>
                <button @click="savePreferences()" type="button"
                        class="px-5 py-2 rounded-xl bg-slate-900 text-white font-bold hover:bg-slate-800 transition">
                    Save Preferences
                </button>
            </div>
        </div>
    </div>

</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('cookieConsentManager', (config) => ({
        showBanner: false,
        showModal: false,
        prefs: {
            necessary: true,
            analytics: true,
            marketing: true
        },

        init() {
            const saved = localStorage.getItem('nexon_cookie_consent');
            if (!saved) {
                setTimeout(() => { this.showBanner = true; }, 800);
            } else {
                try {
                    const parsed = JSON.parse(saved);
                    this.prefs = parsed;
                    this.injectScripts();
                } catch (e) {
                    this.showBanner = true;
                }
            }
        },

        acceptAll() {
            this.prefs = { necessary: true, analytics: true, marketing: true };
            localStorage.setItem('nexon_cookie_consent', JSON.stringify(this.prefs));
            this.showBanner = false;
            this.injectScripts();
        },

        rejectNonEssential() {
            this.prefs = { necessary: true, analytics: false, marketing: false };
            localStorage.setItem('nexon_cookie_consent', JSON.stringify(this.prefs));
            this.showBanner = false;
        },

        savePreferences() {
            localStorage.setItem('nexon_cookie_consent', JSON.stringify(this.prefs));
            this.showModal = false;
            this.showBanner = false;
            this.injectScripts();
        },

        injectScripts() {
            // 1. Google Analytics 4 (GA4)
            if (this.prefs.analytics && config.ga4Id && !window.ga4Loaded) {
                const s = document.createElement('script');
                s.async = true;
                s.src = 'https://www.googletagmanager.com/gtag/js?id=' + config.ga4Id;
                document.head.appendChild(s);

                window.dataLayer = window.dataLayer || [];
                function gtag(){dataLayer.push(arguments);}
                gtag('js', new Date());
                gtag('config', config.ga4Id);
                window.ga4Loaded = true;
            }

            // 2. Google Tag Manager (GTM)
            if (this.prefs.analytics && config.gtmId && !window.gtmLoaded) {
                (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
                j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
                'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
                })(window,document,'script','dataLayer', config.gtmId);
                window.gtmLoaded = true;
            }

            // 3. Meta / Facebook Pixel
            if (this.prefs.marketing && config.metaPixelId && !window.fbPixelLoaded) {
                !function(f,b,e,v,n,t,s)
                {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
                n.callMethod.apply(n,arguments):n.queue.push(arguments)};
                if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
                n.queue=[];t=b.createElement(e);t.async=!0;
                t.src=v;s=b.getElementsByTagName(e)[0];
                s.parentNode.insertBefore(t,s)}(window, document,'script',
                'https://connect.facebook.net/en_US/fbevents.js');
                fbq('init', config.metaPixelId);
                fbq('track', 'PageView');
                window.fbPixelLoaded = true;
            }
        }
    }));
});
</script>
@endif
