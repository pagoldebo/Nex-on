{{--
    ======================================================================
    REUSABLE BLADE COMPONENT: STOREFRONT LAYOUT
    ======================================================================
    File: resources/views/components/storefront-layout.blade.php
    Component Tag: <x-storefront-layout>
    Responsibility: Reusable UI component across the Nex-on application
    Features:
      - Encapsulated component logic, props validation, and slot injection
      - Accessible Tailwind CSS styling and smooth transition animations
--}}
@props([
    'theme'     => null,
    'title'     => null,
    'metaDesc'  => null,
    'canonical' => null,
    'ogImage'   => null,
    'ogType'    => 'website',
    'jsonLd'    => null,
    'noindex'   => false,
    'product'   => null,
    'category'  => null,
])

@php
    $theme = $theme ?? \App\Models\Template::active();
    $seoService = app(\App\Services\SeoService::class);
    $storeName = \App\Models\Setting::get('store_name', config('app.name', 'Saaya'));
    $storeTagline = \App\Models\Setting::get('store_tagline', 'Heritage Luxury Bridalwear & Handcrafted Sarees');
    $defaultMeta = \App\Models\Setting::get('store_meta_description', "Explore handcrafted bridalwear, luxury sarees, designer lehengas, and bespoke couture at {$storeName}. Exclusive handcrafted creations delivered worldwide.");

    // Resolve Product Context if passed
    if ($product && $product instanceof \App\Models\Product) {
        $title = $title ?: ($product->name . ' — ' . ($product->category?->name ?? 'Heritage Collection'));
        $metaDesc = $metaDesc ?: \Illuminate\Support\Str::limit(strip_tags($product->short_description ?: $product->description ?: $product->name), 155);
        $canonical = $canonical ?: route('product.show', $product->slug);
        $ogImage = $ogImage ?: ($product->cover_image ? asset($product->cover_image) : null);
        $ogType = 'product';
        if ($jsonLd === null) {
            $jsonLd = $seoService->generateProductJsonLd($product);
        }
    }

    // Resolve Category Context if passed
    if ($category && $category instanceof \App\Models\Category && !$product) {
        $title = $title ?: ($category->name . ' Collection');
        $metaDesc = $metaDesc ?: "Explore the {$category->name} collection at {$storeName}. Handcrafted designer fashion and bespoke luxury trousseau.";
        $canonical = $canonical ?: route('category.show', $category->slug);
        $ogImage = $ogImage ?: ($category->image_url ?: null);
        if ($jsonLd === null) {
            $jsonLd = $seoService->generateCategoryJsonLd($category);
        }
    }

    // Title Formula
    if (empty($title) || $title === 'Home' || $title === config('app.name')) {
        $resolvedTitle = $storeName . ' — ' . $storeTagline;
    } elseif (str_contains($title, $storeName) || str_contains($title, config('app.name'))) {
        $resolvedTitle = $title;
    } else {
        $resolvedTitle = $title . ' | ' . $storeName;
    }

    // Automatically protect transactional and dynamic search pages from indexing
    if (!$noindex) {
        $noindex = request()->is('cart*', 'checkout*', 'account*', 'wishlist*', 'compare*', 'order-confirmation*', 'search*');
    }

    $resolvedMetaDesc = $metaDesc ?: $defaultMeta;
    $resolvedCanonical = $canonical ?: url()->current();
    $resolvedOgImage = $ogImage ? $seoService->ensureAbsoluteUrl($ogImage) : $seoService->ensureAbsoluteUrl('images/branding/logo.png');

    // Default Site-wide Schemas (Organization & WebSite)
    $siteSchemas = [
        $seoService->generateOrganizationJsonLd(),
        $seoService->generateWebSiteJsonLd(),
    ];

    if (!empty($jsonLd)) {
        if (is_array($jsonLd) && isset($jsonLd['@context'])) {
            $allSchemas = array_merge($siteSchemas, [$jsonLd]);
        } elseif (is_array($jsonLd)) {
            $allSchemas = array_merge($siteSchemas, $jsonLd);
        } else {
            $allSchemas = $siteSchemas;
        }
    } else {
        $allSchemas = $siteSchemas;
    }

    // Header, footer, tab bar and category sheet need root categories with hierarchical subcategories
    $categories = \App\Models\Category::with(['children.children', 'products:id,category_id,name,slug'])
        ->whereNull('parent_id')
        ->orderBy('sort_order')
        ->get();
    $allCategories = \App\Models\Category::with(['parent', 'children'])->orderBy('sort_order')->get();
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" class="{{ $theme->key }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- SEO Canonical URL --}}
    <link rel="canonical" href="{{ $resolvedCanonical }}">

    {{-- Robots Meta Directive --}}
    <meta name="robots" content="{{ $noindex ? 'noindex, nofollow' : 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1' }}">

    {{-- Primary Meta Tags --}}
    <title>{{ $resolvedTitle }}</title>
    <meta name="title" content="{{ $resolvedTitle }}">
    <meta name="description" content="{{ $resolvedMetaDesc }}">

    {{-- Open Graph / Facebook / WhatsApp --}}
    <meta property="og:type" content="{{ $ogType }}">
    <meta property="og:site_name" content="{{ $storeName }}">
    <meta property="og:url" content="{{ $resolvedCanonical }}">
    <meta property="og:title" content="{{ $resolvedTitle }}">
    <meta property="og:description" content="{{ $resolvedMetaDesc }}">
    <meta property="og:image" content="{{ $resolvedOgImage }}">
    <meta property="og:locale" content="{{ str_replace('-', '_', app()->getLocale()) }}">
    @if($ogType === 'product' && $product)
    <meta property="product:price:amount" content="{{ $product->starting_price ?? $product->price }}">
    <meta property="product:price:currency" content="{{ app(\App\Services\CurrencyService::class)->getUserCurrency() }}">
    @endif

    {{-- Global Currency Runtime Configuration --}}
    <script>
        window.currencyConfig = @json(app(\App\Services\CurrencyService::class)->getJsConfig());
    </script>

    {{-- Twitter Cards --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ $resolvedCanonical }}">
    <meta name="twitter:title" content="{{ $resolvedTitle }}">
    <meta name="twitter:description" content="{{ $resolvedMetaDesc }}">
    <meta name="twitter:image" content="{{ $resolvedOgImage }}">

    {{-- Structured Data (Schema.org JSON-LD) --}}
    @foreach($allSchemas as $schema)
    <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}</script>
    @endforeach

    {{-- PWA Web App Manifest & Mobile Meta Tags --}}
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#7f1d1d">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="{{ $storeName }}">
    <link rel="apple-touch-icon" href="/icons/icon-192x192.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/icons/icon-192x192.png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,500&family=Jost:wght@300;400;500;600&family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,600;1,9..144,500&family=Quicksand:wght@400;500;600;700&family=Archivo+Black&family=Space+Grotesk:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@300;400;600;800&family=Playfair+Display:ital,wght@0,700;1,700&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    @includeIf("storefront.{$theme->key}.components.tailwind-config")
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer">

    {{-- Storefront Common Core Stylesheet --}}
    <link rel="stylesheet" href="/css/storefront.css">

    {{-- Active Theme Specific Stylesheet --}}
    @if(file_exists(public_path("css/themes/{$theme->key}.css")))
        <link rel="stylesheet" href="/css/themes/{{ $theme->key }}.css">
    @endif
    @includeIf("storefront.{$theme->key}.components.theme-css")

    {{ $head ?? '' }}
</head>
<body class="page-fade">

    <div id="pullIndicator" class="fixed top-0 left-0 right-0 z-[80] flex justify-center pt-2.5 opacity-0 pointer-events-none transition-opacity duration-200">
        <div class="pull-spinner w-5 h-5 rounded-full border-[2.5px] border-transparent animate-spin" style="border-top-color:var(--color-accent,#DB2777)"></div>
    </div>

    @include("storefront.{$theme->key}.components.header")

    <main id="mainContent" role="main">
        {{ $slot }}
    </main>

    {{-- Auto-ensure Scrolling Announcement Band for all current and future themes --}}
    @if(request()->routeIs('home') && \App\Models\Setting::get('ticker_enabled', '1') !== '0')
        <template id="globalScrollingBandTemplate">
            @include('storefront.common.scrolling-band')
        </template>
    @endif

    @include("storefront.{$theme->key}.components.footer")
    @include("storefront.{$theme->key}.components.tab-bar")
    @include("storefront.{$theme->key}.components.category-sheet")
    @include("storefront.{$theme->key}.components.cart-drawer")
    @include("storefront.{$theme->key}.components.login-modal")
    @include("storefront.{$theme->key}.components.search-overlay")
    @if(view()->exists("storefront.{$theme->key}.partials.quick-view"))
        @include("storefront.{$theme->key}.partials.quick-view")
    @else
        @include("storefront.common.quick-view")
    @endif

    <script src="/js/storefront.js"></script>
    @if(file_exists(public_path("js/themes/{$theme->key}.js")))
        <script src="/js/themes/{{ $theme->key }}.js"></script>
    @endif
    <script>
        function openLoginModal() {
            const m = document.getElementById('loginModal');
            if (!m) return;
            if (typeof m.showModal === 'function' && m.tagName === 'DIALOG') {
                try { m.showModal(); return; } catch(e) {}
            }
            m.classList.remove('hidden');
            m.classList.add('flex');
            m.style.setProperty('display', 'flex', 'important');
            document.body.style.overflow = 'hidden';
            setTimeout(() => m.querySelector('input[type="email"], input[type="text"]')?.focus(), 50);
        }
        function closeLoginModal() {
            const m = document.getElementById('loginModal');
            if (!m) return;
            if (typeof m.close === 'function' && m.tagName === 'DIALOG' && m.open) {
                try { m.close(); return; } catch(e) {}
            }
            m.classList.add('hidden');
            m.classList.remove('flex');
            m.style.setProperty('display', 'none', 'important');
            document.body.style.overflow = '';
        }
        window.openLoginModal = openLoginModal;
        window.closeLoginModal = closeLoginModal;
    </script>

    @include('storefront.common.cookie-consent')
    @include('storefront.common.pwa-prompt')

    {{-- Service Worker Registration & PWA Mobile App Scripts --}}
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(reg => {
                        console.log('PWA ServiceWorker registered with scope:', reg.scope);
                    })
                    .catch(err => {
                        console.warn('PWA ServiceWorker registration failed:', err);
                    });
            });
        }
    </script>
    <script src="/js/pwa-install.js?v=1"></script>
    <script src="/js/product-card.js"></script>
    {{-- Client-Side AVIF Converter: Converts JPG/PNG to ultra-compressed AVIF on uploader machine --}}
    <script src="{{ asset('js/avif-converter.js') }}"></script>

    @stack('scripts')
    {{ $scripts ?? '' }}
</body>
</html>
