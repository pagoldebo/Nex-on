{{--
    resources/views/components/storefront-layout.blade.php
    The root layout for ALL four themes, ALL ten pages.

    Usage:
        <x-storefront-layout :theme="$theme" title="Page Title">
            ... page content ...
        </x-storefront-layout>

    Props:
      $theme    — the active Template model (->key: 'saaya'|'buttercup'|'vyra'|'lumiere')
      $title    — <title> tag content
      $metaDesc — optional meta description
--}}
@props([
    'theme'    => null,
    'title'    => config('app.name', 'Project Nexon'),
    'metaDesc' => config('app.name', 'Project Nexon') . ' - Online Store.',
])

@php
    $theme = $theme ?? \App\Models\Template::active();
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
    <meta name="description" content="{{ $metaDesc }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} — {{ config('app.name') }}</title>

    {{-- PWA Web App Manifest & Mobile Meta Tags --}}
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#7f1d1d">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="{{ \App\Models\Setting::get('store_name', config('app.name', 'Saaya')) }}">
    <link rel="apple-touch-icon" href="{{ asset('icons/icon-192x192.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,500&family=Jost:wght@300;400;500;600&family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,600;1,9..144,500&family=Quicksand:wght@400;500;600;700&family=Archivo+Black&family=Space+Grotesk:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@300;400;600;800&family=Playfair+Display:ital,wght@0,700;1,700&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" referrerpolicy="no-referrer">

    @includeIf("storefront.{$theme->key}.components.tailwind-config")

    <style>
        *, *::before, *::after { box-sizing: border-box; }
        html {
            scroll-behavior: smooth;
            overflow-x: hidden;
            width: 100%;
            max-width: 100vw;
        }
        body {
            overflow-x: hidden;
            width: 100%;
            max-width: 100vw;
            -webkit-font-smoothing: antialiased;
        }
        ::selection { background: var(--color-accent, #DB2777); color: #fff; }

        .reveal { opacity: 0; transform: translateY(24px); transition: opacity .7s ease, transform .7s ease; }
        .reveal.in-view { opacity: 1; transform: translateY(0); }

        .swipe-track { display:flex; overflow-x:auto; scroll-snap-type:x mandatory; -webkit-overflow-scrolling:touch; scrollbar-width:none; gap:1rem; }
        .swipe-track::-webkit-scrollbar { display:none; }
        .swipe-item { flex:0 0 82%; scroll-snap-align:center; }
        .swipe-dots { display:flex; gap:6px; justify-content:center; margin-top:12px; }
        .swipe-dot { width:7px; height:7px; border-radius:99px; opacity:.3; transition:opacity .25s ease, width .25s ease; background:var(--color-accent, #DB2777); }
        .swipe-dot.active { opacity:1; width:18px; }

        @media (max-width:1023px) { body { padding-bottom: 72px; } }

        .page-fade { animation: pageFadeIn .25s ease-out; }
        @keyframes pageFadeIn { from { opacity:0; } to { opacity:1; } }

        @media (prefers-reduced-motion:reduce) {
            .reveal, .swipe-dot, .page-fade { transition:none!important; animation:none!important; opacity:1!important; }
        }

        /* Product Rich Text, Charts, Tables, Lists & Typography */
        .product-rich-text table {
            width: 100% !important;
            border-collapse: collapse !important;
            margin: 1rem 0 !important;
            font-size: 0.875rem !important;
        }
        .product-rich-text th,
        .product-rich-text td {
            border: 1px solid rgba(140, 140, 140, 0.25) !important;
            padding: 0.55rem 0.8rem !important;
            text-align: left !important;
        }
        .product-rich-text th {
            background-color: rgba(140, 140, 140, 0.1) !important;
            font-weight: 700 !important;
        }
        .product-rich-text ul {
            list-style-type: disc !important;
            margin-left: 1.5rem !important;
            margin-top: 0.5rem !important;
            margin-bottom: 0.5rem !important;
        }
        .product-rich-text ol {
            list-style-type: decimal !important;
            margin-left: 1.5rem !important;
            margin-top: 0.5rem !important;
            margin-bottom: 0.5rem !important;
        }
        .product-rich-text li {
            margin-bottom: 0.25rem !important;
        }
        .product-rich-text b,
        .product-rich-text strong {
            font-weight: 700 !important;
        }
        .product-rich-text h2,
        .product-rich-text h3,
        .product-rich-text h4 {
            font-weight: 700 !important;
            margin-top: 0.75rem !important;
            margin-bottom: 0.35rem !important;
        }
    </style>

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
        <script>
            (function () {
                function ensureScrollingBand() {
                    if (!document.querySelector('.sld-ticker-container')) {
                        const hero = document.querySelector('main > section:first-of-type, main > div:first-of-type, .hero-section, #heroSection');
                        const template = document.getElementById('globalScrollingBandTemplate');
                        if (hero && template) {
                            hero.insertAdjacentHTML('afterend', template.innerHTML);
                        }
                    }
                }
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', ensureScrollingBand);
                } else {
                    ensureScrollingBand();
                }
            })();
        </script>
    @endif

    @include("storefront.{$theme->key}.components.footer")
    @include("storefront.{$theme->key}.components.tab-bar")
    @include("storefront.{$theme->key}.components.category-sheet")
    @include("storefront.{$theme->key}.components.cart-drawer")
    @include("storefront.{$theme->key}.components.login-modal")
    @include("storefront.{$theme->key}.components.search-overlay")

    <script>
        // ── Cart state (shared across all pages via sessionStorage) ────────
        let cart = JSON.parse(sessionStorage.getItem('lc_cart') || '[]');

        function addToCart(productId, name, colorName, size, price, image, variantId, sku) {
            let item;
            if (typeof productId === 'object' && productId !== null) {
                item = productId;
            } else {
                item = { productId, name, colorName, size, price, image, variantId: variantId || null, sku: sku || null, qty: 1 };
            }
            const existing = cart.find(i => i.productId === item.productId && i.colorName === item.colorName && i.size === item.size);
            if (existing) {
                existing.qty = (existing.qty || 1) + (item.qty || 1);
            } else {
                cart.push({
                    productId: item.productId,
                    variantId: item.variantId || null,
                    sku: item.sku || null,
                    name: item.name,
                    colorName: item.colorName,
                    size: item.size,
                    price: item.price,
                    image: item.image,
                    qty: item.qty || 1
                });
            }
            persistCart();
            updateCartUI();
            openCartDrawer();
        }

        function removeFromCart(productId, colorName, size) {
            cart = cart.filter(i => !(i.productId === productId && i.colorName === colorName && i.size === size));
            persistCart();
            updateCartUI();
        }

        function persistCart() { sessionStorage.setItem('lc_cart', JSON.stringify(cart)); }
        function cartTotal() { return cart.reduce((sum, i) => sum + (i.price * i.qty), 0); }

        function updateCartUI() {
            const count = cart.reduce((s, i) => s + i.qty, 0);
            document.querySelectorAll('.cart-count').forEach(el => el.textContent = count);
            document.querySelectorAll('.cart-count-badge').forEach(el => el.textContent = count);
            const totalEl = document.getElementById('cartTotal');
            if (totalEl) totalEl.textContent = '₹' + cartTotal().toLocaleString('en-IN');
            document.querySelectorAll('.cart-total-amount, #cartDrawerSubtotal').forEach(el => el.textContent = '₹' + cartTotal().toLocaleString('en-IN'));
            renderCartItems();
        }

        function renderCartItems() {
            const container = document.getElementById('cartItems');
            if (!container) return;
            if (cart.length === 0) {
                container.innerHTML = '<p class="text-center text-sm opacity-40 mt-10 px-4">Your bag is empty.</p>';
                return;
            }
            container.innerHTML = cart.map(item => `
                <div class="flex gap-3 py-3 border-b border-current/10">
                    <img src="${item.image}" class="w-14 h-14 object-cover rounded-lg flex-shrink-0 bg-gray-100">
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-sm truncate">${item.name}</p>
                        <p class="text-xs opacity-50">${item.colorName} / ${item.size} × ${item.qty}</p>
                        <p class="font-bold text-sm mt-0.5">₹${(item.price * item.qty).toLocaleString('en-IN')}</p>
                    </div>
                    <button onclick="removeFromCart(${item.productId},'${item.colorName}','${item.size}')" class="opacity-40 hover:opacity-100 text-lg leading-none">&times;</button>
                </div>
            `).join('');
        }

        function openCartDrawer() {
            if (typeof updateCartUI === 'function') updateCartUI();
            document.getElementById('cartDrawer')?.classList.remove('translate-x-full');
            document.getElementById('cartOverlay')?.classList.remove('hidden');
        }
        function closeCartDrawer() {
            document.getElementById('cartDrawer')?.classList.add('translate-x-full');
            document.getElementById('cartOverlay')?.classList.add('hidden');
        }

        const revealIO = new IntersectionObserver(entries => {
            entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('in-view'); });
        }, { threshold: 0.12 });
        document.querySelectorAll('.reveal').forEach(el => revealIO.observe(el));

        function setActiveTab(name) {
            document.querySelectorAll('.tab-item').forEach(t => t.classList.toggle('active', t.dataset.tab === name));
        }

        function openLoginModal() {
            document.getElementById('loginModal')?.classList.remove('hidden');
            document.getElementById('loginModal')?.classList.add('flex');
        }
        function closeLoginModal() {
            document.getElementById('loginModal')?.classList.add('hidden');
            document.getElementById('loginModal')?.classList.remove('flex');
        }
        document.getElementById('loginModal')?.addEventListener('click', e => {
            if (e.target.id === 'loginModal') closeLoginModal();
        });

        function openSearch() {
            document.getElementById('searchOverlay')?.classList.remove('hidden');
            document.getElementById('searchOverlay')?.classList.add('flex');
            setTimeout(() => document.getElementById('searchInput')?.focus(), 50);
        }
        function closeSearch() {
            document.getElementById('searchOverlay')?.classList.add('hidden');
            document.getElementById('searchOverlay')?.classList.remove('flex');
        }

        // ── Simple product-rail renderer used by index pages ────────────────
        // Renders the same card markup into a desktop grid AND a mobile
        // swipe track from one product array (output of toCardArray()).
        function renderProductRail(products, desktopGridId, mobileTrackId, dotsId) {
            const desktopGrid = document.getElementById(desktopGridId);
            const mobileTrack = document.getElementById(mobileTrackId);
            const dotsContainer = document.getElementById(dotsId);
            if (!desktopGrid || !window.renderRailCard) return;

            desktopGrid.innerHTML = products.map(p => window.renderRailCard(p)).join('');
            if (mobileTrack) {
                mobileTrack.innerHTML = products.map(p => `<div class="swipe-item">${window.renderRailCard(p)}</div>`).join('');
            }
            if (dotsContainer) {
                dotsContainer.innerHTML = products.map((_, i) => `<span class="swipe-dot ${i === 0 ? 'active' : ''}"></span>`).join('');
                mobileTrack?.addEventListener('scroll', () => {
                    const idx = Math.round(mobileTrack.scrollLeft / mobileTrack.clientWidth);
                    dotsContainer.querySelectorAll('.swipe-dot').forEach((d, i) => d.classList.toggle('active', i === idx));
                }, { passive: true });
            }
            document.querySelectorAll('.product-card').forEach(card => {
                if (window.initProductCardPicker) window.initProductCardPicker(card);
                revealIO.observe(card);
            });
        }

        (function () {
            const indicator = document.getElementById('pullIndicator');
            let startY = 0, pulling = false;
            window.addEventListener('touchstart', e => {
                if (window.scrollY === 0) { startY = e.touches[0].clientY; pulling = true; }
            }, { passive: true });
            window.addEventListener('touchmove', e => {
                if (pulling && e.touches[0].clientY - startY > 60) indicator.classList.add('opacity-100');
            }, { passive: true });
            window.addEventListener('touchend', () => {
                setTimeout(() => indicator.classList.remove('opacity-100'), 600);
                pulling = false;
            });
        })();

        window.addEventListener('scroll', () => {
            document.getElementById('siteHeader')?.classList.toggle('shadow-md', window.scrollY > 40);
        });

        document.addEventListener('DOMContentLoaded', () => {
            updateCartUI();
            document.querySelectorAll('.reveal:not(.in-view)').forEach(el => revealIO.observe(el));
        });
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
    <script src="{{ asset('js/pwa-install.js') }}?v=1"></script>
    <script src="{{ asset('js/product-card.js') }}"></script>
    {{-- Client-Side WebP Converter: Converts JPG/PNG to WebP on uploader machine --}}
    <script src="{{ asset('js/webp-converter.js') }}"></script>

    @stack('scripts')
    {{ $scripts ?? '' }}
</body>
</html>
