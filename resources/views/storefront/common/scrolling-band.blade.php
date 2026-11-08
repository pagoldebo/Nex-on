{{--
    ======================================================================
    STOREFRONT COMMON COMPONENT: GLOBAL SCROLLING ANNOUNCEMENT BAND
    ======================================================================
    File: resources/views/storefront/common/scrolling-band.blade.php
    Component Injection: storefront.common.scrolling-band
    Responsibility: Infinitely scrolling promotional announcement ticker below hero
    Features:
      - CSS hardware-accelerated marquee animation (keyframes marquee)
      - Dynamic text injection from admin settings ('scrolling_band_text')
      - Pause-on-hover accessibility behavior
    Operations & Maintenance:
      - Text editable in Admin -> Settings -> General -> Announcement Marquee
--}}
@php
    $freeShipping = \App\Models\Setting::get('free_shipping_threshold', '1499');
    $supportPhone = \App\Models\Setting::get('support_phone', '+91 98765 43210');
    $tickerEnabled = \App\Models\Setting::get('ticker_enabled', '1');

    $cleanTicker = fn($val, $fallback) => ($val !== null && trim($val) !== '')
        ? \App\Services\HtmlSanitizerService::clean($val)
        : $fallback;

    $item1 = \App\Models\Setting::get('ticker_item_1');
    $item1Html = $cleanTicker($item1, 'Free Delivery on orders over <strong>₹' . e($freeShipping) . '</strong>');

    $item2 = \App\Models\Setting::get('ticker_item_2');
    $item2Html = $cleanTicker($item2, 'Upto 500/- off for selected styles.');

    $item3 = \App\Models\Setting::get('ticker_item_3');
    $item3Html = $cleanTicker($item3, 'Use code <strong>WELCOME5</strong> for 5% OFF');

    $item4 = \App\Models\Setting::get('ticker_item_4');
    $item4Html = $cleanTicker($item4, 'Accepting UPI payments');

    $item5 = \App\Models\Setting::get('ticker_item_5');
    $item5Html = $cleanTicker($item5, 'Call us: <strong>' . e($supportPhone) . '</strong>');

    $tickerItems = [
        ['icon' => 'fa-solid fa-truck sld-icon-amber', 'html' => $item1Html],
        ['icon' => '',                                 'html' => $item2Html],
        ['icon' => 'fa-solid fa-tags sld-icon-amber',  'html' => $item3Html],
        ['icon' => '',                                 'html' => $item4Html],
        ['icon' => 'fa-solid fa-phone sld-icon-pink',  'html' => $item5Html],
    ];

    $activeTickerItems = array_filter($tickerItems, fn($it) => !empty(trim(strip_tags($it['html']))));
    if (empty($activeTickerItems)) {
        $activeTickerItems = $tickerItems;
    }
@endphp

@if($tickerEnabled !== '0')
<style>
    .sld-ticker-container {
        width: 100%;
        max-width: 100vw;
        height: 46px;
        background-color: #0b1120;
        overflow: hidden;
        position: relative;
        z-index: 20;
        border-top: 1px solid rgba(255, 255, 255, 0.08);
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        font-family: inherit;
    }

    .sld-ticker-track {
        display: flex;
        width: max-content;
        height: 100%;
        align-items: center;
        animation: sld-ticker-scroll 32s linear infinite;
        will-change: transform;
    }

    .sld-ticker-container:hover .sld-ticker-track {
        animation-play-state: paused;
    }

    .sld-ticker-group {
        display: flex;
        align-items: center;
        flex-shrink: 0;
    }

    .sld-ticker-item {
        display: inline-flex;
        align-items: center;
        white-space: nowrap;
        padding: 0 1.75rem;
        color: #ffffff;
        font-size: 0.85rem;
        font-weight: 500;
        letter-spacing: 0.01em;
        text-decoration: none;
    }

    .sld-ticker-item strong {
        font-weight: 700;
        color: #ffffff;
        margin: 0 2px;
    }

    .sld-separator {
        width: 5px;
        height: 5px;
        background-color: #f59e0b;
        border-radius: 50%;
        margin-right: 14px;
        flex-shrink: 0;
        opacity: 0.85;
    }

    .sld-icon-amber {
        color: #f59e0b;
        margin-right: 8px;
        font-size: 0.85rem;
    }

    .sld-icon-pink {
        color: #ec4899;
        margin-right: 8px;
        font-size: 0.85rem;
    }

    @keyframes sld-ticker-scroll {
        0% {
            transform: translateX(0);
        }
        100% {
            transform: translateX(-50%);
        }
    }

    @media (max-width: 768px) {
        .sld-ticker-container {
            height: 42px;
        }
        .sld-ticker-track {
            animation-duration: 22s;
        }
        .sld-ticker-item {
            font-size: 0.78rem;
            padding: 0 1.25rem;
        }
        .sld-separator {
            width: 4px;
            height: 4px;
            margin-right: 10px;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .sld-ticker-track {
            animation: none;
            overflow-x: auto;
        }
    }
</style>

<div class="sld-ticker-container" role="region" aria-label="Store highlights announcement ticker">
    <div class="sld-ticker-track">
        {{-- Section 1 --}}
        <div class="sld-ticker-group">
            @foreach($activeTickerItems as $item)
                <div class="sld-ticker-item">
                    <span class="sld-separator"></span>
                    <span>
                        @if(!empty($item['icon']))
                            <i class="{{ $item['icon'] }}"></i>
                        @endif
                        {!! $item['html'] !!}
                    </span>
                </div>
            @endforeach
        </div>

        {{-- Section 2 (Duplicate for seamless continuous loop) --}}
        <div class="sld-ticker-group" aria-hidden="true">
            @foreach($activeTickerItems as $item)
                <div class="sld-ticker-item">
                    <span class="sld-separator"></span>
                    <span>
                        @if(!empty($item['icon']))
                            <i class="{{ $item['icon'] }}"></i>
                        @endif
                        {!! $item['html'] !!}
                    </span>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endif
