{{-- resources/views/storefront/common/pwa-prompt.blade.php
     Progressive Web App (PWA) Mobile App Installation Prompt
     Matches Jonakee luxury reference design benchmark with 24h dismissal logic and native/iOS installation support. --}}

@php
    $pwaEnabled = \App\Models\Setting::get('pwa_prompt_enabled', '1') !== '0';
    $appName    = config('app.name', 'Project Nexon');
    $customTitle = \App\Models\Setting::get('pwa_prompt_title');
    $pwaTitle   = (!empty($customTitle) && $customTitle !== 'Install Nexon Luxury App')  
                    ? $customTitle 
                    : ('Install ' . $appName . ' App');
    $pwaDesc    = \App\Models\Setting::get('pwa_prompt_description', 'Get quick access to our collection! Install our app for a faster, better shopping experience on your device.');
@endphp

@if($pwaEnabled)
<style>
    /* PWA Install Popup Reference Styles */
    .pwa-install-popup {
        position: fixed;
        bottom: -120%;
        left: 50%;
        transform: translateX(-50%);
        width: calc(100% - 32px);
        max-width: 410px;
        background: linear-gradient(135deg, #7f1d1d 0%, #991b1b 100%);
        color: #ffffff;
        padding: 24px;
        border-radius: 22px;
        box-shadow: 0 16px 45px -10px rgba(0, 0, 0, 0.45), 0 0 0 1px rgba(255, 255, 255, 0.12) inset;
        z-index: 99999;
        transition: bottom 0.45s cubic-bezier(0.16, 1, 0.3, 1), transform 0.45s cubic-bezier(0.16, 1, 0.3, 1);
        font-family: inherit;
    }

    .pwa-install-popup.show {
        bottom: 84px;
    }

    @media (min-width: 1024px) {
        .pwa-install-popup {
            left: auto;
            right: 24px;
            transform: none;
            width: 400px;
        }
        .pwa-install-popup.show {
            bottom: 24px;
        }
    }

    .pwa-popup-close {
        position: absolute;
        top: 14px;
        right: 14px;
        background: rgba(255, 255, 255, 0.22);
        border: none;
        color: #ffffff;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.2s, transform 0.2s;
    }

    .pwa-popup-close:hover {
        background: rgba(255, 255, 255, 0.35);
        transform: scale(1.05);
    }

    .pwa-popup-icon {
        width: 54px;
        height: 54px;
        background: rgba(255, 255, 255, 0.16);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 14px;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.15);
    }

    .pwa-popup-title {
        font-family: 'Playfair Display', 'Cormorant Garamond', Georgia, serif;
        font-size: 1.4rem;
        font-weight: 700;
        margin: 0 0 6px 0;
        line-height: 1.2;
        color: #ffffff;
        letter-spacing: -0.01em;
    }

    .pwa-popup-description {
        font-family: 'Jost', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        font-size: 0.85rem;
        color: rgba(255, 255, 255, 0.92);
        line-height: 1.45;
        margin: 0 0 18px 0;
    }

    .pwa-popup-buttons {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .pwa-popup-button {
        flex: 1;
        padding: 11px 16px;
        border-radius: 12px;
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        text-align: center;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        border: none;
        outline: none;
    }

    .pwa-install-button {
        background: #ffffff;
        color: #7f1d1d;
        font-weight: 700;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .pwa-install-button:hover {
        background: #fcf9f5;
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.22);
    }

    .pwa-later-button {
        background: rgba(255, 255, 255, 0.16);
        color: #ffffff;
    }

    .pwa-later-button:hover {
        background: rgba(255, 255, 255, 0.26);
    }

    .pwa-instruction-list {
        text-align: left;
        padding-left: 18px;
        list-style-type: decimal;
        margin: 4px 0 0 0;
        font-size: 0.8rem;
        line-height: 1.45;
    }

    .pwa-instruction-list li {
        margin-bottom: 5px;
    }

    .pwa-got-it {
        background: #ffffff !important;
        color: #7f1d1d !important;
        font-weight: 700 !important;
    }
</style>

<div class="pwa-install-popup" id="pwaInstallPopup" role="dialog" aria-modal="true" aria-labelledby="pwaPopupTitle">
    {{-- Close 'x' button --}}
    <button class="pwa-popup-close" id="pwaCloseBtn" aria-label="Dismiss app prompt">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>

    {{-- App smartphone icon matching reference image --}}
    <div class="pwa-popup-icon" aria-hidden="true">
        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <rect x="5" y="2" width="14" height="20" rx="3" ry="3"/>
            <line x1="12" y1="18" x2="12.01" y2="18" stroke-width="3" stroke-linecap="round"/>
        </svg>
    </div>

    {{-- Headline & Subtitle matching reference image --}}
    <h3 class="pwa-popup-title" id="pwaPopupTitle">{{ $pwaTitle }}</h3>
    <p class="pwa-popup-description">{{ $pwaDesc }}</p>

    {{-- Action buttons matching reference image --}}
    <div class="pwa-popup-buttons">
        <button class="pwa-popup-button pwa-install-button" id="pwaInstallBtn">
            <svg class="w-4 h-4 text-[#7f1d1d]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            <span>Install App</span>
        </button>
        <button class="pwa-popup-button pwa-later-button" id="pwaMaybeLaterBtn">
            Maybe Later
        </button>
    </div>
</div>
@endif
