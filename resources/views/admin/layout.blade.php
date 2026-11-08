{{--
    ======================================================================
    ADMIN CORE: MASTER ADMINISTRATIVE LAYOUT SHELL
    ======================================================================
    File: resources/views/admin/layout.blade.php
    Layout Wrapper: @extends('admin.layout')
    Responsibility: Primary administrative layout for all back-office modules
    Features:
      - Collapsible responsive sidebar with RBAC role-guarded navigation items
      - Instant theme detection (Dark Mode vs Light Mode) preventing FOUC
      - Top navigation bar with store view shortcut, notification bell, and user avatar
      - CSRF meta tag, Tailwind CDN, Alpine.js, and Chart.js integration
      - Global session flash message container and modal dialog slots
    Security & Authorization:
      - Restricted to authenticated staff accounts with admin/store_manager/clerk roles
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50 {{ (($_COOKIE['admin_theme'] ?? request()->cookie('admin_theme')) === 'dark') ? 'dark' : '' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Admin Panel' }} — {{ config('app.name', 'Nex-on') }}</title>
    
    <!-- Instant Theme Detection (Prevents FOUC / Light Flash) -->
    <script>
        (function() {
            try {
                var savedTheme = localStorage.getItem('admin_theme');
                if (savedTheme === 'dark') {
                    document.documentElement.classList.add('dark');
                } else if (savedTheme === 'light') {
                    document.documentElement.classList.remove('dark');
                } else if (document.cookie.indexOf('admin_theme=dark') !== -1) {
                    document.documentElement.classList.add('dark');
                }
            } catch (e) {}
        })();
    </script>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        dark: {
                            50: '#f8fafc',
                            100: '#f1f5f9',
                            200: '#e2e8f0',
                            300: '#cbd5e1',
                            400: '#94a3b8',
                            500: '#64748b',
                            600: '#475569',
                            700: '#334155',
                            800: '#1e293b',
                            900: '#0f172a',
                            950: '#090d16',
                        }
                    }
                }
            }
        };
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        [x-cloak] { display: none !important; }

        @media (prefers-reduced-motion: no-preference) {
            @view-transition {
                navigation: auto;
            }
        }
        
        /* Sleek clean scrollbar for light sidebar */
        .sidebar-scroll::-webkit-scrollbar {
            width: 4px;
        }
        .sidebar-scroll::-webkit-scrollbar-track {
            background: rgba(241, 245, 249, 0.6);
        }
        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9999px;
        }
        .sidebar-scroll::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        .sidebar-scroll {
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 rgba(241, 245, 249, 0.6);
        }

        /* ==========================================================================
           ADMIN BLACK / DARK THEME SYSTEM
           ========================================================================== */
        
        /* Smooth transitions when toggling theme */
        html.theme-transitioning,
        html.theme-transitioning *,
        html.theme-transitioning *:before,
        html.theme-transitioning *:after {
            transition: background-color 0.2s ease-in-out, border-color 0.2s ease-in-out, color 0.15s ease-in-out !important;
        }

        @media screen {
            /* 1. Base dark background & default text */
            html.dark {
                color-scheme: dark;
            }

            html.dark,
            html.dark body {
                background-color: #090d16 !important;
                color: #e2e8f0 !important;
            }

            /* Workspace Canvas */
            html.dark #adminWorkspace {
                background-color: #090d16 !important;
            }

            /* 2. Topbar Header & Mobile Nav */
            html.dark header.bg-white,
            html.dark div.md\:hidden.bg-white {
                background-color: #0f172a !important;
                border-color: #1e293b !important;
                color: #f8fafc !important;
            }

            /* 3. Sidebar Navigation */
            html.dark aside,
            html.dark aside > div,
            html.dark #adminSidebarNav {
                background-color: #0f172a !important;
                border-color: #1e293b !important;
                color: #cbd5e1 !important;
            }

            html.dark #adminSidebarNav a.text-slate-700,
            html.dark #adminSidebarNav a.text-slate-600,
            html.dark #adminSidebarNav button.text-slate-700 {
                color: #94a3b8 !important;
            }

            html.dark #adminSidebarNav a:hover,
            html.dark #adminSidebarNav button:hover {
                color: #f8fafc !important;
                background-color: #1e293b !important;
            }

            html.dark #adminSidebarNav a.bg-indigo-50,
            html.dark #adminSidebarNav button.bg-indigo-50\/50 {
                background-color: rgba(99, 102, 241, 0.18) !important;
                color: #818cf8 !important;
            }

            html.dark #adminSidebarNav div[x-show*="openMenus"] {
                border-color: #1e293b !important;
            }

            /* 4. Cards, Containers, Modals & Dropdowns */
            html.dark .bg-white {
                background-color: #0f172a !important;
                border-color: #1e293b !important;
                color: #e2e8f0;
            }

            /* 5. Toolbars, Submenu Bars, Filters & Subtle Tinted Backgrounds */
            /* Covers all opacity variants like bg-slate-50/50, bg-slate-50/40, bg-slate-50/75, bg-gray-50/50 */
            html.dark .bg-slate-50,
            html.dark .bg-gray-50,
            html.dark [class*="bg-slate-50"],
            html.dark [class*="bg-gray-50"] {
                background-color: #0b1120 !important;
                border-color: #1e293b !important;
            }

            /* Clean table rows - remove muddy light-slate washes on root categories and rows */
            html.dark tbody tr[class*="bg-slate-50"],
            html.dark tbody tr[class*="bg-gray-50"],
            html.dark tbody tr.bg-slate-50\/30,
            html.dark tbody tr.bg-slate-50\/20 {
                background-color: rgba(255, 255, 255, 0.02) !important;
            }

            html.dark tbody tr:hover,
            html.dark tbody tr[class*="hover:bg-slate-50"]:hover,
            html.dark .hover\:bg-slate-50:hover,
            html.dark .hover\:bg-slate-50\/70:hover,
            html.dark .hover\:bg-slate-50\/80:hover,
            html.dark .hover\:bg-slate-100:hover,
            html.dark .hover\:bg-slate-100\/70:hover,
            html.dark .hover\:bg-slate-100\/80:hover {
                background-color: rgba(30, 41, 59, 0.5) !important;
            }

            html.dark .bg-slate-100,
            html.dark .bg-gray-100,
            html.dark [class*="bg-slate-100"],
            html.dark [class*="bg-gray-100"] {
                background-color: #1e293b !important;
                border-color: #334155 !important;
                color: #cbd5e1 !important;
            }

            html.dark .bg-slate-200,
            html.dark .bg-gray-200,
            html.dark [class*="bg-slate-200"],
            html.dark [class*="bg-gray-200"] {
                background-color: #334155 !important;
                color: #f8fafc !important;
            }

            /* 6. Text Contrast & Maximum Readability Across All Submenus */
            html.dark .text-slate-900,
            html.dark .text-slate-800,
            html.dark .text-gray-900,
            html.dark .text-gray-800,
            html.dark [class*="text-slate-900"],
            html.dark [class*="text-slate-800"],
            html.dark [class*="text-gray-900"],
            html.dark [class*="text-gray-800"] {
                color: #ffffff !important;
            }

            html.dark .text-slate-700,
            html.dark .text-slate-600,
            html.dark .text-gray-700,
            html.dark .text-gray-600,
            html.dark [class*="text-slate-700"],
            html.dark [class*="text-slate-600"],
            html.dark [class*="text-gray-700"],
            html.dark [class*="text-gray-600"] {
                color: #cbd5e1 !important;
            }

            html.dark .text-slate-500,
            html.dark .text-gray-500,
            html.dark [class*="text-slate-500"],
            html.dark [class*="text-gray-500"] {
                color: #94a3b8 !important;
            }

            html.dark .text-slate-400,
            html.dark .text-gray-400,
            html.dark [class*="text-slate-400"],
            html.dark [class*="text-gray-400"] {
                color: #94a3b8 !important;
            }

            html.dark .text-slate-300,
            html.dark [class*="text-slate-300"] {
                color: #cbd5e1 !important;
            }

            /* 7. Borders & Dividers */
            html.dark .border-slate-200,
            html.dark .border-slate-100,
            html.dark .border-slate-300,
            html.dark .border-gray-200,
            html.dark .border-gray-100,
            html.dark [class*="border-slate-200"],
            html.dark [class*="border-slate-100"],
            html.dark [class*="border-slate-300"],
            html.dark [class*="border-gray-200"] {
                border-color: #1e293b !important;
            }

            html.dark .divide-slate-100 > :not([hidden]) ~ :not([hidden]),
            html.dark .divide-slate-200 > :not([hidden]) ~ :not([hidden]),
            html.dark .divide-gray-100 > :not([hidden]) ~ :not([hidden]),
            html.dark .divide-gray-200 > :not([hidden]) ~ :not([hidden]),
            html.dark [class*="divide-slate-100"] > :not([hidden]) ~ :not([hidden]),
            html.dark [class*="divide-slate-200"] > :not([hidden]) ~ :not([hidden]) {
                border-color: #1e293b !important;
            }

            /* 8. Form Controls: Inputs, selects, textareas */
            html.dark input:not([type="checkbox"]):not([type="radio"]):not([type="submit"]):not([type="button"]):not([type="range"]):not([type="color"]):not([type="file"]),
            html.dark select,
            html.dark textarea {
                background-color: #0b1120 !important;
                border: 1px solid #334155 !important;
                color: #f8fafc !important;
            }

            html.dark input::placeholder,
            html.dark textarea::placeholder {
                color: #94a3b8 !important;
            }

            html.dark select option {
                background-color: #0f172a !important;
                color: #f8fafc !important;
            }

            html.dark input[type="checkbox"],
            html.dark input[type="radio"] {
                background-color: #0b1120 !important;
                border-color: #334155 !important;
            }

            /* 9. Tables */
            html.dark table thead,
            html.dark table thead th,
            html.dark thead[class*="bg-slate-50"],
            html.dark thead[class*="bg-gray-50"] {
                background-color: #0b1120 !important;
                color: #94a3b8 !important;
                border-color: #1e293b !important;
            }

            html.dark table tbody tr {
                border-color: #1e293b !important;
            }

            /* 10. Buttons in Bars and Tables (Fixes Filter Button & Action Buttons) */
            html.dark button[class*="bg-slate-200"],
            html.dark a[class*="bg-slate-200"],
            html.dark input[type="submit"][class*="bg-slate-200"],
            html.dark button[class*="bg-slate-100"],
            html.dark a[class*="bg-slate-100"],
            html.dark input[type="submit"][class*="bg-slate-100"],
            html.dark button[class*="bg-slate-300"],
            html.dark a[class*="bg-slate-300"] {
                background-color: #1e293b !important;
                border: 1px solid #334155 !important;
                color: #f1f5f9 !important;
            }

            html.dark button[class*="bg-slate-200"]:hover,
            html.dark a[class*="bg-slate-200"]:hover,
            html.dark input[type="submit"][class*="bg-slate-200"]:hover,
            html.dark button[class*="bg-slate-100"]:hover,
            html.dark a[class*="bg-slate-100"]:hover,
            html.dark input[type="submit"][class*="bg-slate-100"]:hover,
            html.dark [class*="hover:bg-slate-200"]:hover,
            html.dark [class*="hover:bg-slate-300"]:hover {
                background-color: #334155 !important;
                border-color: #475569 !important;
                color: #ffffff !important;
            }

            /* Invert light slate-900 buttons so they contrast against dark backgrounds */
            html.dark button.bg-slate-900,
            html.dark a.bg-slate-900,
            html.dark input[type="submit"].bg-slate-900 {
                background-color: #1e293b !important;
                border: 1px solid #334155 !important;
                color: #f8fafc !important;
            }

            html.dark button.bg-slate-900:hover,
            html.dark a.bg-slate-900:hover,
            html.dark input[type="submit"].bg-slate-900:hover {
                background-color: #334155 !important;
            }

            /* 11. Status Badges, Chips & Income Statement Callouts (P&L, Categories, etc.) */
            html.dark [class*="bg-emerald-50"],
            html.dark [class*="bg-green-50"] {
                background-color: rgba(6, 78, 59, 0.4) !important;
                border-color: rgba(16, 185, 129, 0.35) !important;
                color: #34d399 !important;
            }

            html.dark [class*="text-emerald-950"],
            html.dark [class*="text-emerald-900"],
            html.dark [class*="text-emerald-800"] {
                color: #34d399 !important;
            }
            html.dark [class*="text-emerald-700"],
            html.dark [class*="text-emerald-600"] {
                color: #6ee7b7 !important;
            }

            html.dark [class*="bg-indigo-50"] {
                background-color: rgba(49, 46, 129, 0.4) !important;
                border-color: rgba(99, 102, 241, 0.35) !important;
                color: #a5b4fc !important;
            }

            html.dark [class*="text-indigo-950"],
            html.dark [class*="text-indigo-900"],
            html.dark [class*="text-indigo-800"] {
                color: #818cf8 !important;
            }
            html.dark [class*="text-indigo-700"],
            html.dark [class*="text-indigo-600"] {
                color: #a5b4fc !important;
            }

            html.dark [class*="bg-rose-50"],
            html.dark [class*="bg-red-50"] {
                background-color: rgba(136, 19, 55, 0.4) !important;
                border-color: rgba(244, 63, 94, 0.35) !important;
                color: #fb7185 !important;
            }

            html.dark [class*="text-rose-950"],
            html.dark [class*="text-rose-900"],
            html.dark [class*="text-rose-800"] {
                color: #fb7185 !important;
            }
            html.dark [class*="text-rose-700"],
            html.dark [class*="text-rose-600"] {
                color: #fda4af !important;
            }

            html.dark [class*="bg-amber-50"],
            html.dark [class*="bg-yellow-50"] {
                background-color: rgba(120, 53, 15, 0.4) !important;
                border-color: rgba(245, 158, 11, 0.35) !important;
                color: #fbbf24 !important;
            }

            html.dark [class*="text-amber-950"],
            html.dark [class*="text-amber-900"],
            html.dark [class*="text-amber-800"] {
                color: #fbbf24 !important;
            }
            html.dark [class*="text-amber-700"],
            html.dark [class*="text-amber-600"] {
                color: #fde68a !important;
            }

            html.dark [class*="bg-purple-50"] {
                background-color: rgba(88, 28, 135, 0.4) !important;
                border-color: rgba(168, 85, 247, 0.35) !important;
                color: #c084fc !important;
            }

            html.dark [class*="text-purple-950"],
            html.dark [class*="text-purple-900"],
            html.dark [class*="text-purple-800"] {
                color: #c084fc !important;
            }
            html.dark [class*="text-purple-700"],
            html.dark [class*="text-purple-600"] {
                color: #d8b4fe !important;
            }

            html.dark [class*="bg-blue-50"],
            html.dark [class*="bg-sky-50"] {
                background-color: rgba(30, 58, 138, 0.4) !important;
                border-color: rgba(59, 130, 246, 0.35) !important;
                color: #60a5fa !important;
            }

            html.dark [class*="text-blue-950"],
            html.dark [class*="text-blue-900"],
            html.dark [class*="text-blue-800"] {
                color: #60a5fa !important;
            }
            html.dark [class*="text-blue-700"],
            html.dark [class*="text-blue-600"] {
                color: #93c5fd !important;
            }

            /* 12. Subtle Shadows */
            html.dark .shadow-2xs,
            html.dark .shadow-xs,
            html.dark .shadow-sm {
                box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.5), 0 1px 2px -1px rgba(0, 0, 0, 0.5) !important;
            }

            /* 13. Dark Scrollbar */
            html.dark .sidebar-scroll::-webkit-scrollbar-track {
                background: #0f172a;
            }
            html.dark .sidebar-scroll::-webkit-scrollbar-thumb {
                background: #334155;
            }
            html.dark .sidebar-scroll::-webkit-scrollbar-thumb:hover {
                background: #475569;
            }
            html.dark .sidebar-scroll {
                scrollbar-color: #334155 #0f172a;
            }

            html.dark ::-webkit-scrollbar {
                width: 6px;
                height: 6px;
            }
            html.dark ::-webkit-scrollbar-track {
                background: #090d16;
            }
            html.dark ::-webkit-scrollbar-thumb {
                background: #1e293b;
                border-radius: 9999px;
            }
            html.dark ::-webkit-scrollbar-thumb:hover {
                background: #334155;
            }
            html.dark {
                scrollbar-color: #1e293b #090d16;
            }
        }
    </style>

    <!-- Global Instant Theme Engine -->
    <script>
        window.toggleAdminTheme = function(forceMode) {
            var html = document.documentElement;
            var currentlyDark = html.classList.contains('dark');
            var targetMode = (forceMode !== undefined) ? forceMode : (currentlyDark ? 'light' : 'dark');
            var isDark = (targetMode === 'dark');

            html.classList.add('theme-transitioning');

            if (isDark) {
                html.classList.add('dark');
            } else {
                html.classList.remove('dark');
            }

            try {
                localStorage.setItem('admin_theme', targetMode);
                document.cookie = 'admin_theme=' + targetMode + ';path=/;max-age=31536000;SameSite=Lax';
            } catch (e) {}

            // Background async sync with server
            try {
                var token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                if (token) {
                    fetch('{{ route('admin.themes.admin-toggle') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({ theme: targetMode })
                    }).catch(function() {});
                }
            } catch (e) {}

            window.dispatchEvent(new CustomEvent('admin-theme-changed', { 
                detail: { theme: targetMode, isDark: isDark } 
            }));

            setTimeout(function() {
                html.classList.remove('theme-transitioning');
            }, 250);

            return isDark;
        };
    </script>

    <!-- Alpine Plugins -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @stack('styles')
</head>
<body class="h-screen overflow-hidden text-slate-800 antialiased flex flex-col md:flex-row bg-slate-50" 
      x-data="{ 
          sidebarOpen: false,
          sidebarCollapsed: (function() {
              try {
                  return localStorage.getItem('admin_sidebar_collapsed') === 'true';
              } catch (e) {
                  return false;
              }
          })(),
          toggleSidebarCollapse() {
              this.sidebarCollapsed = !this.sidebarCollapsed;
              try {
                  localStorage.setItem('admin_sidebar_collapsed', this.sidebarCollapsed);
              } catch (e) {}
          },
          darkMode: (function() {
              try {
                  var saved = localStorage.getItem('admin_theme');
                  if (saved !== null) return saved === 'dark';
                  return document.documentElement.classList.contains('dark');
              } catch (e) {
                  return false;
              }
          })(),
          toggleTheme() {
              this.darkMode = window.toggleAdminTheme();
          }
      }"
      @admin-theme-changed.window="darkMode = $event.detail.isDark"
      @keydown.window="
          if ((event.metaKey || event.ctrlKey) && (event.key === '\\' || event.code === 'Backslash')) { 
              event.preventDefault(); 
              toggleSidebarCollapse(); 
          }
          if ((event.metaKey || event.ctrlKey) && event.shiftKey && (event.key === 'D' || event.code === 'KeyD')) {
              event.preventDefault();
              toggleTheme();
          }
      ">

    <!-- Seamless SPA Navigation Top Loading Progress Bar -->
    <div id="adminPjaxBar" class="fixed top-0 left-0 right-0 h-0.5 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 z-[99999] pointer-events-none transition-all duration-300 opacity-0" style="width: 0%;"></div>

    <!-- Mobile Top Navigation Bar -->
    <div class="md:hidden bg-white text-slate-900 border-b border-slate-200 px-4 py-3 flex items-center justify-between sticky top-0 z-50 shadow-xs shrink-0">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 font-bold tracking-tight min-w-0">
            <div class="w-8 h-8 flex items-center justify-center shrink-0 text-slate-900">
                <x-admin-logo class="w-8 h-8" />
                <span class="sr-only">{{ mb_strtoupper(mb_substr(config('app.name', 'Nex-on'), 0, 1)) }}</span>
            </div>
            <div class="flex flex-col">
                <span class="text-sm font-extrabold text-slate-900 tracking-tight leading-snug truncate">{{ $platformName ?? \App\Http\Middleware\EnsurePlatformBranding::PLATFORM_NAME }}</span>
                <span class="text-[10px] font-semibold text-slate-400 tracking-wider uppercase leading-none">{{ $platformSuite ?? \App\Http\Middleware\EnsurePlatformBranding::PLATFORM_SUITE }}</span>
            </div>
        </a>
        <div class="flex items-center gap-2"
             x-data="{ switchDark: document.documentElement.classList.contains('dark') }"
             @admin-theme-changed.window="switchDark = $event.detail.isDark">
            <!-- Mobile Black Theme Switch Button -->
            <button type="button" 
                    @click="window.toggleAdminTheme()" 
                    class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl border text-xs font-bold transition-all"
                    :class="switchDark ? 'bg-slate-900 border-slate-700 text-slate-100 shadow-inner' : 'bg-slate-100 border-slate-200 text-slate-700'"
                    :title="switchDark ? 'Black Theme is ON — Click to switch to White Theme' : 'Black Theme is OFF — Click to switch to Black Theme'">
                <svg x-show="!switchDark" class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="4"/><path stroke-linecap="round" d="M12 2v2m0 16v2M4.93 4.93l1.41 1.41m11.32 11.32l1.41 1.41M2 12h2m16 0h2M6.34 17.66l-1.41 1.41m14.14-14.14l-1.41 1.41"/></svg>
                <svg x-show="switchDark" class="w-4 h-4 text-amber-300" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/></svg>
                <span class="text-[10px] font-black uppercase px-1.5 py-0.5 rounded leading-none"
                      :class="switchDark ? 'bg-indigo-600 text-white' : 'bg-slate-200 text-slate-600'"
                      x-text="switchDark ? 'Dark: ON' : 'Dark: OFF'"></span>
            </button>
            <button @click="sidebarOpen = !sidebarOpen" class="p-2 rounded-lg bg-slate-100 text-slate-600 hover:text-slate-900 focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>
    </div>

    <!-- Backdrop for mobile sidebar -->
    <div x-show="sidebarOpen" 
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false" 
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-40 md:hidden" 
         x-cloak></div>

    @php
        $isOrdersOpen      = request()->routeIs('admin.orders*') || request()->routeIs('admin.order-requests*');
        $isProductsOpen    = request()->routeIs('admin.products*') || request()->routeIs('admin.categories*') || request()->routeIs('admin.reviews*') || request()->routeIs('admin.addons*') || request()->routeIs('admin.attributes*') || request()->routeIs('admin.barcode*');
        $isCustomersOpen   = request()->routeIs('admin.customers*') || request()->routeIs('admin.customer-groups*');
        $isInventoryOpen   = request()->routeIs('admin.inventory*');
        $isWarehouseOpen   = request()->routeIs('admin.outlets*') || request()->routeIs('admin.transfers*') || request()->routeIs('admin.fleet*');
        $isSuppliersOpen   = request()->routeIs('admin.suppliers*');
        $isPurchasesOpen   = request()->routeIs('admin.purchase-orders*') || request()->routeIs('admin.supplier-invoices*') || request()->routeIs('admin.purchase-returns*');
        $isFinanceOpen     = request()->routeIs('admin.finance*') || request()->routeIs('admin.integrations.tally*');
        $isInvoicesOpen    = request()->routeIs('admin.invoices*');
        $isMarketingOpen   = request()->routeIs('admin.marketing*') || request()->routeIs('admin.coupons*') || request()->routeIs('admin.newsletter*') || request()->routeIs('admin.push*');
        $isReportsOpen     = request()->routeIs('admin.reports*');
        $isStaffOpen       = request()->routeIs('admin.roles*') || request()->routeIs('admin.staff*');
        $isPayrollOpen     = request()->routeIs('admin.hrm.payroll*');
        $isPerformanceOpen = request()->routeIs('admin.hrm.performance*');
        $isSupportOpen     = request()->routeIs('admin.support*');
        $isSettingsOpen    = request()->routeIs('admin.settings*') || request()->routeIs('admin.themes*') || request()->routeIs('admin.pages*');
        $isDashboardsOpen  = request()->routeIs('admin.dashboard*') || request()->routeIs('admin.pos.shifts*') || (
            !$isOrdersOpen && !$isProductsOpen && !$isCustomersOpen && !$isInventoryOpen &&
            !$isWarehouseOpen && !$isSuppliersOpen && !$isPurchasesOpen && !$isFinanceOpen &&
            !$isInvoicesOpen && !$isMarketingOpen && !$isReportsOpen && !$isStaffOpen &&
            !$isPayrollOpen && !$isPerformanceOpen && !$isSupportOpen && !$isSettingsOpen
        );
    @endphp

    <!-- Sidebar Navigation -->
    <aside :class="[
               sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0',
               sidebarCollapsed ? 'md:w-20' : 'md:w-64'
           ]"
           x-data="{
               openMenus: {
                   dashboards: {{ $isDashboardsOpen ? 'true' : 'false' }},
                   orders: {{ $isOrdersOpen ? 'true' : 'false' }},
                   products: {{ $isProductsOpen ? 'true' : 'false' }},
                   customers: {{ $isCustomersOpen ? 'true' : 'false' }},
                   inventory: {{ $isInventoryOpen ? 'true' : 'false' }},
                   warehouse: {{ $isWarehouseOpen ? 'true' : 'false' }},
                   suppliers: {{ $isSuppliersOpen ? 'true' : 'false' }},
                   purchases: {{ $isPurchasesOpen ? 'true' : 'false' }},
                   finance: {{ $isFinanceOpen ? 'true' : 'false' }},
                   invoices: {{ $isInvoicesOpen ? 'true' : 'false' }},
                   marketing: {{ $isMarketingOpen ? 'true' : 'false' }},
                   reports: {{ $isReportsOpen ? 'true' : 'false' }},
                   staff: {{ $isStaffOpen ? 'true' : 'false' }},
                   payroll: {{ $isPayrollOpen ? 'true' : 'false' }},
                   performance: {{ $isPerformanceOpen ? 'true' : 'false' }},
                   support: {{ $isSupportOpen ? 'true' : 'false' }},
                   settings: {{ $isSettingsOpen ? 'true' : 'false' }}
               },
               toggle(menu) {
                   const willOpen = !this.openMenus[menu];
                   Object.keys(this.openMenus).forEach(key => {
                       this.openMenus[key] = false;
                   });
                   if (willOpen) {
                       this.openMenus[menu] = true;
                   }
               }
           }"
           class="fixed inset-y-0 left-0 z-50 w-64 bg-white text-slate-700 transition-[width,transform] duration-200 ease-in-out md:static flex flex-col shrink-0 shadow-lg md:shadow-none border-r border-slate-200/90 h-full max-h-screen overflow-x-hidden">
        
        <!-- Sidebar Header (Branding & Logo) -->
        <div class="shrink-0 bg-white border-b border-slate-200/80">
            <div class="h-16 flex items-center justify-between" :class="sidebarCollapsed ? 'md:justify-center md:px-0 px-5' : 'px-5'">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 font-bold tracking-tight min-w-0" :class="sidebarCollapsed ? 'md:justify-center' : ''">
                    <div class="w-9 h-9 flex items-center justify-center shrink-0 text-slate-900">
                        <x-admin-logo class="w-9 h-9" />
                        <span class="sr-only">{{ mb_strtoupper(mb_substr(config('app.name', 'Nex-on'), 0, 1)) }}</span>
                    </div>
                    <div class="flex flex-col min-w-0" x-show="!sidebarCollapsed" x-transition.opacity.duration.200ms>
                        <span class="text-sm font-extrabold text-slate-900 tracking-tight leading-snug truncate">{{ $platformName ?? \App\Http\Middleware\EnsurePlatformBranding::PLATFORM_NAME }}</span>
                        <span class="text-[10px] font-semibold text-slate-400 tracking-wider uppercase leading-none">{{ $platformSuite ?? \App\Http\Middleware\EnsurePlatformBranding::PLATFORM_SUITE }}</span>
                    </div>
                </a>
                <button @click="sidebarOpen = false" class="md:hidden text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        @php
            $currentTheme     = \App\Models\Template::active();
            $totalOrdersCount = \App\Models\Order::count();
            $pendingCount     = \App\Models\Order::whereIn('status', ['pending', 'confirmed'])->count();
            $pendingReqs      = \App\Models\OrderRequest::where('status', 'pending')->count();
            $pendingReviews   = \App\Models\ProductReview::where('is_approved', false)->count();
            $navLowStockCount = \App\Models\ProductVariant::lowStock()->count();
            $navOpenPosCount  = \App\Models\PurchaseOrder::whereIn('status', ['draft', 'sent', 'partial'])->count();
        @endphp

        <!-- Navigation Links (Scrollable clean organized menu matching reference) -->
        <nav id="adminSidebarNav" class="flex-1 min-h-0 overflow-y-auto overscroll-contain py-3 space-y-1 sidebar-scroll bg-white" :class="sidebarCollapsed ? 'md:px-2 px-3' : 'px-3'">

            {{-- ==================== SECTION 1: OVERVIEW ==================== --}}
            <div class="px-2 pt-1 pb-1" :class="sidebarCollapsed ? 'md:px-0 md:text-center' : 'px-2'">
                <span x-show="!sidebarCollapsed" class="text-[10px] font-bold text-slate-400 uppercase tracking-wider select-none">Overview</span>
                <div x-show="sidebarCollapsed" class="hidden md:block h-px w-6 mx-auto bg-slate-200 my-1"></div>
            </div>

            <!-- Dashboards Accordion -->
            <div>
                <button type="button" 
                        @click="sidebarCollapsed ? (toggleSidebarCollapse(), toggle('dashboards')) : toggle('dashboards')"
                        :title="sidebarCollapsed ? 'Dashboards' : ''"
                        class="w-full flex items-center rounded-xl text-xs font-semibold transition-colors {{ request()->routeIs('admin.dashboard*') || request()->routeIs('admin.pos.shifts*') ? 'text-indigo-600 font-bold bg-indigo-50/50' : 'text-slate-700 hover:text-slate-900 hover:bg-slate-100/70' }}"
                        :class="sidebarCollapsed ? 'md:justify-center md:px-0 py-2.5 px-3 justify-between' : 'justify-between px-3 py-2'">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-indigo-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Dashboards</span>
                    </div>
                    <svg x-show="!sidebarCollapsed" class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="openMenus.dashboards ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="openMenus.dashboards && !sidebarCollapsed" @if(!$isDashboardsOpen) x-cloak style="display: none;" @endif class="pl-7 pr-1 py-1 space-y-0.5 border-l-2 border-slate-100 ml-4.5 my-0.5">
                    <a href="{{ route('admin.dashboard') }}" 
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-50 text-indigo-600 font-bold shadow-xs' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">
                        eCommerce
                    </a>
                    <a href="{{ route('admin.pos.index') }}"
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium text-slate-600 hover:text-indigo-600 hover:bg-slate-50 transition-all">
                        POS Terminal
                    </a>
                    <a href="{{ route('admin.pos.shifts.index') }}"
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->routeIs('admin.pos.shifts*') ? 'bg-indigo-50 text-indigo-600 font-bold shadow-xs' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">
                        POS Shift Reconciliation (X/Z)
                    </a>
                    <a href="{{ route('admin.inventory.index') }}" 
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium text-slate-600 hover:text-indigo-600 hover:bg-slate-50 transition-all">
                        Inventory
                    </a>
                    <a href="{{ route('admin.reports.index') }}" 
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium text-slate-600 hover:text-indigo-600 hover:bg-slate-50 transition-all">
                        Sales Analytics
                    </a>
                    <a href="{{ route('admin.outlets.index') }}" 
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium text-slate-600 hover:text-indigo-600 hover:bg-slate-50 transition-all">
                        Warehouses &amp; Outlets
                    </a>
                </div>
            </div>

            <!-- POS Terminal Link (with LIVE green badge) -->
            <a href="{{ route('admin.pos.index') }}" 
               :title="sidebarCollapsed ? 'POS Cashier Terminal' : ''"
               class="flex items-center rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('admin.pos*') ? 'bg-indigo-50 text-indigo-600' : 'text-slate-700 hover:bg-slate-100/70 hover:text-slate-900' }}"
               :class="sidebarCollapsed ? 'md:justify-center md:px-0 py-2.5 px-3 justify-between' : 'justify-between px-3 py-2'">
                <div class="flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    <span x-show="!sidebarCollapsed" class="whitespace-nowrap">POS</span>
                </div>
                <span x-show="!sidebarCollapsed" class="px-2 py-0.5 text-[9px] font-bold rounded-full bg-emerald-500 text-white uppercase tracking-wider shadow-xs animate-pulse">Live</span>
            </a>

            {{-- ==================== SECTION 2: COMMERCE ==================== --}}
            <div class="px-2 pt-3 pb-1" :class="sidebarCollapsed ? 'md:px-0 md:text-center' : 'px-2'">
                <span x-show="!sidebarCollapsed" class="text-[10px] font-bold text-slate-400 uppercase tracking-wider select-none">Commerce</span>
                <div x-show="sidebarCollapsed" class="hidden md:block h-px w-6 mx-auto bg-slate-200 my-1"></div>
            </div>

            <!-- Orders Accordion -->
            <div>
                <button type="button" 
                        @click="sidebarCollapsed ? (toggleSidebarCollapse(), toggle('orders')) : toggle('orders')"
                        :title="sidebarCollapsed ? 'Orders' : ''"
                        class="w-full flex items-center rounded-xl text-xs font-semibold transition-colors {{ request()->routeIs('admin.orders*') || request()->routeIs('admin.order-requests*') ? 'text-indigo-600 font-bold bg-indigo-50/50' : 'text-slate-700 hover:text-slate-900 hover:bg-slate-100/70' }}"
                        :class="sidebarCollapsed ? 'md:justify-center md:px-0 py-2.5 px-3 justify-between' : 'justify-between px-3 py-2'">
                    <div class="flex items-center gap-2.5">
                        <div class="relative">
                            <svg class="w-4 h-4 text-indigo-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                            @if($pendingCount > 0)
                                <span x-show="sidebarCollapsed" class="absolute -top-1 -right-1 w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
                            @endif
                        </div>
                        <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Orders</span>
                    </div>
                    <div class="flex items-center gap-1.5" x-show="!sidebarCollapsed">
                        @if($totalOrdersCount > 0)
                            <span class="w-5 h-5 rounded-full bg-indigo-600 text-white text-[10px] font-bold flex items-center justify-center shrink-0 shadow-xs">{{ $totalOrdersCount > 99 ? '99+' : $totalOrdersCount }}</span>
                        @endif
                        <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="openMenus.orders ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </button>
                <div x-show="openMenus.orders && !sidebarCollapsed" @if(!$isOrdersOpen) x-cloak style="display: none;" @endif class="pl-7 pr-1 py-1 space-y-0.5 border-l-2 border-slate-100 ml-4.5 my-0.5">
                    <a href="{{ route('admin.orders.index') }}" 
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->routeIs('admin.orders.index') && !request()->has('status') ? 'bg-indigo-50 text-indigo-600 font-bold shadow-xs' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">
                        All Orders
                    </a>
                    <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" 
                       class="flex items-center justify-between px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->get('status') === 'pending' ? 'bg-indigo-50 text-indigo-600 font-bold shadow-xs' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">
                        <span>Pending</span>
                        @if($pendingCount > 0)
                            <span class="px-1.5 py-0.2 rounded-full bg-rose-500 text-white text-[9px] font-bold shrink-0">{{ $pendingCount }}</span>
                        @endif
                    </a>
                    <a href="{{ route('admin.orders.index', ['status' => 'processing']) }}" 
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->get('status') === 'processing' ? 'bg-indigo-50 text-indigo-600 font-bold shadow-xs' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">
                        Processing
                    </a>
                    <a href="{{ route('admin.orders.index', ['status' => 'shipped']) }}" 
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->get('status') === 'shipped' ? 'bg-indigo-50 text-indigo-600 font-bold shadow-xs' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">
                        Shipped
                    </a>
                    <a href="{{ route('admin.orders.index', ['status' => 'delivered']) }}" 
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->get('status') === 'delivered' ? 'bg-indigo-50 text-indigo-600 font-bold shadow-xs' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">
                        Delivered
                    </a>
                    <a href="{{ route('admin.orders.index', ['status' => 'cancelled']) }}" 
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->get('status') === 'cancelled' ? 'bg-indigo-50 text-indigo-600 font-bold shadow-xs' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">
                        Cancelled
                    </a>
                    <a href="{{ route('admin.orders.index', ['status' => 'refunded']) }}" 
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->get('status') === 'refunded' ? 'bg-indigo-50 text-indigo-600 font-bold shadow-xs' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">
                        Refunded
                    </a>
                    <a href="{{ route('admin.order-requests.index') }}" 
                       class="flex items-center justify-between px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->routeIs('admin.order-requests*') ? 'bg-indigo-50 text-indigo-600 font-bold shadow-xs' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">
                        <span>Returns</span>
                        @if($pendingReqs > 0)
                            <span class="px-1.5 py-0.2 rounded-full bg-amber-500 text-white text-[9px] font-bold shrink-0">{{ $pendingReqs }}</span>
                        @endif
                    </a>
                </div>
            </div>

            <!-- Products Accordion -->
            <div>
                <button type="button" 
                        @click="sidebarCollapsed ? (toggleSidebarCollapse(), toggle('products')) : toggle('products')"
                        :title="sidebarCollapsed ? 'Products' : ''"
                        class="w-full flex items-center rounded-xl text-xs font-semibold transition-colors {{ request()->routeIs('admin.products*') || request()->routeIs('admin.categories*') || request()->routeIs('admin.addons*') || request()->routeIs('admin.reviews*') ? 'text-indigo-600 font-bold bg-indigo-50/50' : 'text-slate-700 hover:text-slate-900 hover:bg-slate-100/70' }}"
                        :class="sidebarCollapsed ? 'md:justify-center md:px-0 py-2.5 px-3 justify-between' : 'justify-between px-3 py-2'">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-indigo-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                        <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Products</span>
                    </div>
                    <svg x-show="!sidebarCollapsed" class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="openMenus.products ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="openMenus.products && !sidebarCollapsed" @if(!$isProductsOpen) x-cloak style="display: none;" @endif class="pl-7 pr-1 py-1 space-y-0.5 border-l-2 border-slate-100 ml-4.5 my-0.5">
                    <a href="{{ route('admin.products.index') }}" 
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->routeIs('admin.products.index') ? 'bg-indigo-50 text-indigo-600 font-bold shadow-xs' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">
                        All Products
                    </a>
                    <a href="{{ route('admin.products.create') }}" 
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->routeIs('admin.products.create') ? 'bg-indigo-50 text-indigo-600 font-bold shadow-xs' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">
                        Add Product
                    </a>
                    <a href="{{ route('admin.categories.index') }}" 
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->routeIs('admin.categories*') ? 'bg-indigo-50 text-indigo-600 font-bold shadow-xs' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">
                        Categories
                    </a>
                    <a href="{{ route('admin.addons.index') }}" 
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->routeIs('admin.addons*') ? 'bg-indigo-50 text-indigo-600 font-bold shadow-xs' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">
                        Brands &amp; Add-ons
                    </a>
                    <a href="{{ route('admin.attributes.index') }}" class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->routeIs('admin.attributes*') ? 'bg-indigo-50 text-indigo-600 font-bold shadow-xs' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">Attributes</a>
                    <a href="{{ route('admin.reviews.index') }}" 
                       class="flex items-center justify-between px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->routeIs('admin.reviews*') ? 'bg-indigo-50 text-indigo-600 font-bold shadow-xs' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">
                        <span>Reviews</span>
                        @if($pendingReviews > 0)
                            <span class="px-1.5 py-0.2 rounded-full bg-amber-500 text-white text-[9px] font-bold shrink-0">{{ $pendingReviews }}</span>
                        @endif
                    </a>
                    <a href="{{ route('admin.barcode.index') }}" class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->routeIs('admin.barcode*') ? 'bg-indigo-50 text-indigo-600 font-bold shadow-xs' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">Barcode</a>
                </div>
            </div>

            <!-- Customers Accordion -->
            <div>
                <button type="button" 
                        @click="sidebarCollapsed ? (toggleSidebarCollapse(), toggle('customers')) : toggle('customers')"
                        :title="sidebarCollapsed ? 'Customers' : ''"
                        class="w-full flex items-center rounded-xl text-xs font-semibold transition-colors {{ request()->routeIs('admin.customers*') || request()->routeIs('admin.customer-groups*') ? 'text-indigo-600 font-bold bg-indigo-50/50' : 'text-slate-700 hover:text-slate-900 hover:bg-slate-100/70' }}"
                        :class="sidebarCollapsed ? 'md:justify-center md:px-0 py-2.5 px-3 justify-between' : 'justify-between px-3 py-2'">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-indigo-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Customers</span>
                    </div>
                    <svg x-show="!sidebarCollapsed" class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="openMenus.customers ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="openMenus.customers && !sidebarCollapsed" @if(!$isCustomersOpen) x-cloak style="display: none;" @endif class="pl-7 pr-1 py-1 space-y-0.5 border-l-2 border-slate-100 ml-4.5 my-0.5">
                    <a href="{{ route('admin.customers.index') }}" 
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->routeIs('admin.customers.index') || request()->routeIs('admin.customers.show') ? 'bg-indigo-50 text-indigo-600 font-bold shadow-xs' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">
                        All Customers
                    </a>
                    <a href="{{ route('admin.customer-groups.index') }}" class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->routeIs('admin.customer-groups*') ? 'bg-indigo-50 text-indigo-600 font-bold shadow-xs' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">Customer Groups</a>
                    <a href="{{ route('admin.customers.segments') }}" class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->routeIs('admin.customers.segments*') ? 'bg-indigo-50 text-indigo-600 font-bold shadow-xs' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">Segments</a>
                </div>
            </div>

            {{-- ==================== SECTION 3: OPERATIONS ==================== --}}
            <div class="px-2 pt-3 pb-1" :class="sidebarCollapsed ? 'md:px-0 md:text-center' : 'px-2'">
                <span x-show="!sidebarCollapsed" class="text-[10px] font-bold text-slate-400 uppercase tracking-wider select-none">Operations</span>
                <div x-show="sidebarCollapsed" class="hidden md:block h-px w-6 mx-auto bg-slate-200 my-1"></div>
            </div>

            <!-- Inventory Accordion -->
            <div>
                <button type="button" 
                        @click="sidebarCollapsed ? (toggleSidebarCollapse(), toggle('inventory')) : toggle('inventory')"
                        :title="sidebarCollapsed ? 'Inventory' : ''"
                        class="w-full flex items-center rounded-xl text-xs font-semibold transition-colors {{ request()->routeIs('admin.inventory*') ? 'text-indigo-600 font-bold bg-indigo-50/50' : 'text-slate-700 hover:text-slate-900 hover:bg-slate-100/70' }}"
                        :class="sidebarCollapsed ? 'md:justify-center md:px-0 py-2.5 px-3 justify-between' : 'justify-between px-3 py-2'">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-indigo-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Inventory</span>
                    </div>
                    <div class="flex items-center gap-1.5" x-show="!sidebarCollapsed">
                        <span class="w-5 h-5 rounded-full bg-indigo-600 text-white text-[10px] font-bold flex items-center justify-center shrink-0 shadow-xs">2</span>
                        <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="openMenus.inventory ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </button>
                <div x-show="openMenus.inventory && !sidebarCollapsed" @if(!$isInventoryOpen) x-cloak style="display: none;" @endif class="pl-7 pr-1 py-1 space-y-0.5 border-l-2 border-slate-100 ml-4.5 my-0.5">
                    <a href="{{ route('admin.inventory.index') }}" 
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->routeIs('admin.inventory.index') && !request()->has('status') ? 'bg-indigo-50 text-indigo-600 font-bold shadow-xs' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">
                        Stock Overview
                    </a>
                    <a href="{{ route('admin.transfers.index') }}" 
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->routeIs('admin.transfers*') ? 'bg-indigo-50 text-indigo-600 font-bold shadow-xs' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">
                        Stock Movements
                    </a>
                    <a href="{{ route('admin.inventory.index', ['status' => 'low_stock']) }}" 
                       class="flex items-center justify-between px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->get('status') === 'low_stock' ? 'bg-indigo-50 text-indigo-600 font-bold shadow-xs' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">
                        <span>Low Stock</span>
                        @if($navLowStockCount > 0)
                            <span class="px-1.5 py-0.2 rounded-full bg-rose-500 text-white text-[9px] font-bold shrink-0">{{ $navLowStockCount }}</span>
                        @endif
                    </a>
                    <a href="{{ route('admin.inventory.forecasting') }}" 
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->routeIs('admin.inventory.forecasting*') ? 'bg-indigo-50 text-indigo-600 font-bold shadow-xs' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">
                        Stock Alerts
                    </a>
                    <a href="{{ route('admin.inventory.batches.index') }}" 
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->routeIs('admin.inventory.batches*') ? 'bg-indigo-50 text-indigo-600 font-bold shadow-xs' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">
                        Batches &amp; Expiry
                    </a>
                </div>
            </div>

            <!-- Warehouses & Outlets Accordion -->
            <div>
                <button type="button" 
                        @click="sidebarCollapsed ? (toggleSidebarCollapse(), toggle('warehouse')) : toggle('warehouse')"
                        :title="sidebarCollapsed ? 'Warehouses & Outlets' : ''"
                        class="w-full flex items-center rounded-xl text-xs font-semibold transition-colors {{ request()->routeIs('admin.outlets*') || request()->routeIs('admin.transfers*') || request()->routeIs('admin.fleet*') ? 'text-indigo-600 font-bold bg-indigo-50/50' : 'text-slate-700 hover:text-slate-900 hover:bg-slate-100/70' }}"
                        :class="sidebarCollapsed ? 'md:justify-center md:px-0 py-2.5 px-3 justify-between' : 'justify-between px-3 py-2'">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-indigo-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Warehouses &amp; Outlets</span>
                    </div>
                    <svg x-show="!sidebarCollapsed" class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="openMenus.warehouse ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="openMenus.warehouse && !sidebarCollapsed" @if(!$isWarehouseOpen) x-cloak style="display: none;" @endif class="pl-7 pr-1 py-1 space-y-0.5 border-l-2 border-slate-100 ml-4.5 my-0.5">
                    <a href="{{ route('admin.outlets.index', ['type' => 'warehouse']) }}" 
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->routeIs('admin.outlets*') && request()->get('type') === 'warehouse' ? 'bg-indigo-50 text-indigo-600 font-bold shadow-xs' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">
                        Warehouses
                    </a>
                    <a href="{{ route('admin.outlets.index', ['type' => 'outlet']) }}" 
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->routeIs('admin.outlets*') && request()->get('type') === 'outlet' ? 'bg-indigo-50 text-indigo-600 font-bold shadow-xs' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">
                        Outlets
                    </a>
                    <a href="{{ route('admin.transfers.index') }}" 
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->routeIs('admin.transfers.index') ? 'bg-indigo-50 text-indigo-600 font-bold shadow-xs' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">
                        Stock Transfers
                    </a>
                    <a href="{{ route('admin.transfers.create') }}" 
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->routeIs('admin.transfers.create') ? 'bg-indigo-50 text-indigo-600 font-bold shadow-xs' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">
                        Receiving
                    </a>
                    <a href="{{ route('admin.fleet.index') }}" 
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->routeIs('admin.fleet*') ? 'bg-indigo-50 text-indigo-600 font-bold shadow-xs' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">
                        Dispatch &amp; Fleet
                    </a>
                </div>
            </div>

            <!-- Suppliers Accordion -->
            <div>
                <button type="button" 
                        @click="sidebarCollapsed ? (toggleSidebarCollapse(), toggle('suppliers')) : toggle('suppliers')"
                        :title="sidebarCollapsed ? 'Suppliers' : ''"
                        class="w-full flex items-center rounded-xl text-xs font-semibold transition-colors {{ request()->routeIs('admin.suppliers*') ? 'text-indigo-600 font-bold bg-indigo-50/50' : 'text-slate-700 hover:text-slate-900 hover:bg-slate-100/70' }}"
                        :class="sidebarCollapsed ? 'md:justify-center md:px-0 py-2.5 px-3 justify-between' : 'justify-between px-3 py-2'">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-indigo-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Suppliers</span>
                    </div>
                    <svg x-show="!sidebarCollapsed" class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="openMenus.suppliers ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="openMenus.suppliers && !sidebarCollapsed" @if(!$isSuppliersOpen) x-cloak style="display: none;" @endif class="pl-7 pr-1 py-1 space-y-0.5 border-l-2 border-slate-100 ml-4.5 my-0.5">
                    <a href="{{ route('admin.suppliers.index') }}" 
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->routeIs('admin.suppliers*') ? 'bg-indigo-50 text-indigo-600 font-bold shadow-xs' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">
                        All Suppliers
                    </a>
                    <a href="{{ route('admin.suppliers.index', ['create' => 1]) }}" 
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->routeIs('admin.suppliers*') && request()->has('create') ? 'bg-indigo-50 text-indigo-600 font-bold shadow-xs' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">
                        Add Supplier
                    </a>
                </div>
            </div>

            <!-- Purchases Accordion -->
            <div>
                <button type="button" 
                        @click="sidebarCollapsed ? (toggleSidebarCollapse(), toggle('purchases')) : toggle('purchases')"
                        :title="sidebarCollapsed ? 'Purchases' : ''"
                        class="w-full flex items-center rounded-xl text-xs font-semibold transition-colors {{ request()->routeIs('admin.purchase-orders*') || request()->routeIs('admin.supplier-invoices*') || request()->routeIs('admin.purchase-returns*') ? 'text-indigo-600 font-bold bg-indigo-50/50' : 'text-slate-700 hover:text-slate-900 hover:bg-slate-100/70' }}"
                        :class="sidebarCollapsed ? 'md:justify-center md:px-0 py-2.5 px-3 justify-between' : 'justify-between px-3 py-2'">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-indigo-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Purchases</span>
                    </div>
                    <div class="flex items-center gap-1.5" x-show="!sidebarCollapsed">
                        @if($navOpenPosCount > 0)
                            <span class="px-1.5 py-0.2 rounded-full bg-amber-500 text-white text-[9px] font-bold shrink-0">{{ $navOpenPosCount }}</span>
                        @endif
                        <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="openMenus.purchases ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </button>
                <div x-show="openMenus.purchases && !sidebarCollapsed" @if(!$isPurchasesOpen) x-cloak style="display: none;" @endif class="pl-7 pr-1 py-1 space-y-0.5 border-l-2 border-slate-100 ml-4.5 my-0.5">
                    <a href="{{ route('admin.purchase-orders.index') }}" 
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->routeIs('admin.purchase-orders.index') ? 'bg-indigo-50 text-indigo-600 font-bold shadow-xs' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">
                        Purchase Orders
                    </a>
                    <a href="{{ route('admin.purchase-orders.create') }}" 
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->routeIs('admin.purchase-orders.create') ? 'bg-indigo-50 text-indigo-600 font-bold shadow-xs' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">
                        Create PO
                    </a>
                    <a href="{{ route('admin.purchase-returns.index') }}" class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->routeIs('admin.purchase-returns*') ? 'bg-indigo-50 text-indigo-600 font-bold shadow-xs' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">Purchase Returns</a>
                    <a href="{{ route('admin.supplier-invoices.index') }}" class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->routeIs('admin.supplier-invoices*') ? 'bg-indigo-50 text-indigo-600 font-bold shadow-xs' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">Supplier Invoices</a>
                </div>
            </div>

            {{-- ==================== SECTION 4: FINANCE ==================== --}}
            <div class="px-2 pt-3 pb-1" :class="sidebarCollapsed ? 'md:px-0 md:text-center' : 'px-2'">
                <span x-show="!sidebarCollapsed" class="text-[10px] font-bold text-slate-400 uppercase tracking-wider select-none">Finance</span>
                <div x-show="sidebarCollapsed" class="hidden md:block h-px w-6 mx-auto bg-slate-200 my-1"></div>
            </div>

            <!-- Finance Accordion -->
            <div>
                <button type="button" 
                        @click="sidebarCollapsed ? (toggleSidebarCollapse(), toggle('finance')) : toggle('finance')"
                        :title="sidebarCollapsed ? 'Finance' : ''"
                        class="w-full flex items-center rounded-xl text-xs font-semibold transition-colors {{ request()->routeIs('admin.finance*') || request()->routeIs('admin.integrations.tally*') ? 'text-indigo-600 font-bold bg-indigo-50/50' : 'text-slate-700 hover:text-slate-900 hover:bg-slate-100/70' }}"
                        :class="sidebarCollapsed ? 'md:justify-center md:px-0 py-2.5 px-3 justify-between' : 'justify-between px-3 py-2'">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-indigo-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Finance</span>
                    </div>
                    <svg x-show="!sidebarCollapsed" class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="openMenus.finance ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="openMenus.finance && !sidebarCollapsed" @if(!$isFinanceOpen) x-cloak style="display: none;" @endif class="pl-7 pr-1 py-1 space-y-0.5 border-l-2 border-slate-100 ml-4.5 my-0.5">
                    <a href="{{ route('admin.reports.index') }}" 
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium text-slate-600 hover:text-indigo-600 hover:bg-slate-50 transition-all">
                        Finance Dashboard
                    </a>
                    <a href="{{ route('admin.finance.expenses') }}" 
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->routeIs('admin.finance.expenses*') ? 'bg-indigo-50 text-indigo-600 font-bold shadow-xs' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">
                        Expenses
                    </a>
                    <a href="{{ route('admin.finance.cash-flow') }}" 
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->routeIs('admin.finance.cash-flow*') ? 'bg-indigo-50 text-indigo-600 font-bold shadow-xs' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">
                        Cash Flow
                    </a>
                    <a href="{{ route('admin.finance.pl') }}" 
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->routeIs('admin.finance.pl*') ? 'bg-indigo-50 text-indigo-600 font-bold shadow-xs' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">
                        Profit &amp; Loss
                    </a>
                    <a href="{{ route('admin.integrations.tally.index') }}" 
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->routeIs('admin.integrations.tally*') ? 'bg-indigo-50 text-indigo-600 font-bold shadow-xs' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">
                        Transactions &amp; Tally
                    </a>
                    <a href="{{ route('admin.reports.index') }}" 
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium text-slate-600 hover:text-indigo-600 hover:bg-slate-50 transition-all">
                        Tax Summary
                    </a>
                </div>
            </div>

            <!-- Invoices Accordion -->
            <div>
                <button type="button" 
                        @click="sidebarCollapsed ? (toggleSidebarCollapse(), toggle('invoices')) : toggle('invoices')"
                        :title="sidebarCollapsed ? 'Invoices' : ''"
                        class="w-full flex items-center rounded-xl text-xs font-semibold transition-colors {{ request()->routeIs('admin.invoices*') ? 'text-indigo-600 font-bold bg-indigo-50/50' : 'text-slate-700 hover:text-slate-900 hover:bg-slate-100/70' }}"
                        :class="sidebarCollapsed ? 'md:justify-center md:px-0 py-2.5 px-3 justify-between' : 'justify-between px-3 py-2'">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-indigo-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Invoices</span>
                    </div>
                    <svg x-show="!sidebarCollapsed" class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="openMenus.invoices ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="openMenus.invoices && !sidebarCollapsed" @if(!$isInvoicesOpen) x-cloak style="display: none;" @endif class="pl-7 pr-1 py-1 space-y-0.5 border-l-2 border-slate-100 ml-4.5 my-0.5">
                    <a href="{{ route('admin.invoices.index') }}" 
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->routeIs('admin.invoices.index') || request()->routeIs('admin.invoices.show') ? 'bg-indigo-50 text-indigo-600 font-bold shadow-xs' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">
                        Commercial Invoices
                    </a>
                    <a href="{{ route('admin.invoices.create') }}" 
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->routeIs('admin.invoices.create') ? 'bg-indigo-50 text-indigo-600 font-bold shadow-xs' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">
                        Create B2B Invoice
                    </a>
                    <a href="{{ route('admin.orders.index') }}" 
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium text-slate-600 hover:text-indigo-600 hover:bg-slate-50 transition-all">
                        Customer Orders
                    </a>
                    <a href="{{ route('admin.pos.index') }}"
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium text-slate-600 hover:text-indigo-600 hover:bg-slate-50 transition-all">
                        POS Terminal
                    </a>
                </div>
            </div>

            {{-- ==================== SECTION 5: GROWTH ==================== --}}
            <div class="px-2 pt-3 pb-1" :class="sidebarCollapsed ? 'md:px-0 md:text-center' : 'px-2'">
                <span x-show="!sidebarCollapsed" class="text-[10px] font-bold text-slate-400 uppercase tracking-wider select-none">Growth</span>
                <div x-show="sidebarCollapsed" class="hidden md:block h-px w-6 mx-auto bg-slate-200 my-1"></div>
            </div>

            <!-- Marketing Accordion -->
            <div>
                <button type="button" 
                        @click="sidebarCollapsed ? (toggleSidebarCollapse(), toggle('marketing')) : toggle('marketing')"
                        :title="sidebarCollapsed ? 'Marketing' : ''"
                        class="w-full flex items-center rounded-xl text-xs font-semibold transition-colors {{ request()->routeIs('admin.marketing*') || request()->routeIs('admin.coupons*') || request()->routeIs('admin.newsletter*') || request()->routeIs('admin.push*') ? 'text-indigo-600 font-bold bg-indigo-50/50' : 'text-slate-700 hover:text-slate-900 hover:bg-slate-100/70' }}"
                        :class="sidebarCollapsed ? 'md:justify-center md:px-0 py-2.5 px-3 justify-between' : 'justify-between px-3 py-2'">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-indigo-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                        <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Marketing</span>
                    </div>
                    <svg x-show="!sidebarCollapsed" class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="openMenus.marketing ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="openMenus.marketing && !sidebarCollapsed" @if(!$isMarketingOpen) x-cloak style="display: none;" @endif class="pl-7 pr-1 py-1 space-y-0.5 border-l-2 border-slate-100 ml-4.5 my-0.5">
                    <a href="{{ route('admin.marketing.dashboard') }}" 
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->routeIs('admin.marketing.dashboard*') ? 'bg-indigo-50 text-indigo-600 font-bold shadow-xs' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">
                        Marketing Dashboard
                    </a>
                    <a href="{{ route('admin.newsletter.index') }}" 
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->routeIs('admin.newsletter*') ? 'bg-indigo-50 text-indigo-600 font-bold shadow-xs' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">
                        Newsletter
                    </a>
                    <a href="{{ route('admin.push.index') }}" 
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->routeIs('admin.push*') ? 'bg-indigo-50 text-indigo-600 font-bold shadow-xs' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">
                        Campaigns &amp; Push
                    </a>
                    <a href="{{ route('admin.coupons.index') }}" 
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->routeIs('admin.coupons*') ? 'bg-indigo-50 text-indigo-600 font-bold shadow-xs' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">
                        Coupons
                    </a>
                    <a href="{{ route('admin.marketing.loyalty') }}" 
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->routeIs('admin.marketing.loyalty*') ? 'bg-indigo-50 text-indigo-600 font-bold shadow-xs' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">
                        Loyalty Program
                    </a>
                    <a href="{{ route('admin.marketing.abandoned-carts') }}" 
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->routeIs('admin.marketing.abandoned-carts*') ? 'bg-indigo-50 text-indigo-600 font-bold shadow-xs' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">
                        Abandoned Carts
                    </a>
                </div>
            </div>

            <!-- Reports Accordion -->
            <div>
                <button type="button" 
                        @click="sidebarCollapsed ? (toggleSidebarCollapse(), toggle('reports')) : toggle('reports')"
                        :title="sidebarCollapsed ? 'Reports' : ''"
                        class="w-full flex items-center rounded-xl text-xs font-semibold transition-colors {{ request()->routeIs('admin.reports*') ? 'text-indigo-600 font-bold bg-indigo-50/50' : 'text-slate-700 hover:text-slate-900 hover:bg-slate-100/70' }}"
                        :class="sidebarCollapsed ? 'md:justify-center md:px-0 py-2.5 px-3 justify-between' : 'justify-between px-3 py-2'">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-indigo-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Reports</span>
                    </div>
                    <svg x-show="!sidebarCollapsed" class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="openMenus.reports ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="openMenus.reports && !sidebarCollapsed" @if(!$isReportsOpen) x-cloak style="display: none;" @endif class="pl-7 pr-1 py-1 space-y-0.5 border-l-2 border-slate-100 ml-4.5 my-0.5">
                    <a href="{{ route('admin.reports.index', ['tab' => 'sales']) }}" 
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->routeIs('admin.reports*') && request('tab', 'sales') === 'sales' ? 'bg-indigo-50 text-indigo-600 font-bold shadow-xs' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">
                        Sales Report
                    </a>
                    <a href="{{ route('admin.reports.index', ['tab' => 'products']) }}" 
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->routeIs('admin.reports*') && request('tab') === 'products' ? 'bg-indigo-50 text-indigo-600 font-bold shadow-xs' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">
                        Product Report
                    </a>
                    <a href="{{ route('admin.reports.index', ['tab' => 'inventory']) }}" 
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->routeIs('admin.reports*') && request('tab') === 'inventory' ? 'bg-indigo-50 text-indigo-600 font-bold shadow-xs' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">
                        Inventory Report
                    </a>
                    <a href="{{ route('admin.customers.segments') }}" 
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->routeIs('admin.customers.segments*') ? 'bg-indigo-50 text-indigo-600 font-bold shadow-xs' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">
                        Customer RFM Report
                    </a>
                    <a href="{{ route('admin.supplier-invoices.index') }}" 
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->routeIs('admin.supplier-invoices*') ? 'bg-indigo-50 text-indigo-600 font-bold shadow-xs' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">
                        Purchase Report
                    </a>
                    <a href="{{ route('admin.pos.shifts.index') }}" 
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->routeIs('admin.pos.shifts*') ? 'bg-indigo-50 text-indigo-600 font-bold shadow-xs' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">
                        POS Tender Report
                    </a>
                    <a href="{{ route('admin.reports.index', ['tab' => 'gst']) }}" 
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->routeIs('admin.reports*') && request('tab') === 'gst' ? 'bg-indigo-50 text-indigo-600 font-bold shadow-xs' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">
                        Tax Report (GST)
                    </a>
                </div>
            </div>

                        {{-- ==================== SECTION 6: HRM ==================== --}}
            <div class="px-2 pt-3 pb-1" :class="sidebarCollapsed ? 'md:px-0 md:text-center' : 'px-2'">
                <span x-show="!sidebarCollapsed" class="text-[10px] font-bold text-slate-400 uppercase tracking-wider select-none">HRM</span>
                <div x-show="sidebarCollapsed" class="hidden md:block h-px w-6 mx-auto bg-slate-200 my-1"></div>
            </div>

            <!-- Payroll Accordion -->
            <div>
                <button type="button" 
                        @click="sidebarCollapsed ? (toggleSidebarCollapse(), toggle('payroll')) : toggle('payroll')"
                        :title="sidebarCollapsed ? 'Payroll' : ''"
                        class="w-full flex items-center rounded-xl text-xs font-semibold text-slate-700 hover:text-slate-900 hover:bg-slate-100/70 transition-colors"
                        :class="sidebarCollapsed ? 'md:justify-center md:px-0 py-2.5 px-3 justify-between' : 'justify-between px-3 py-2'">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-indigo-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Payroll</span>
                    </div>
                    <svg x-show="!sidebarCollapsed" class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="openMenus.payroll ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="openMenus.payroll && !sidebarCollapsed" @if(!$isPayrollOpen) x-cloak style="display: none;" @endif class="pl-7 pr-1 py-1 space-y-0.5 border-l-2 border-slate-100 ml-4.5 my-0.5">
                    <a href="{{ route('admin.hrm.payroll.overview') }}"
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->routeIs('admin.hrm.payroll.overview*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-500 hover:text-indigo-600 hover:bg-slate-50' }}">
                        Payroll Overview
                    </a>
                    <a href="{{ route('admin.hrm.payroll.run') }}"
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->routeIs('admin.hrm.payroll.run*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-500 hover:text-indigo-600 hover:bg-slate-50' }}">
                        Run Payroll
                    </a>
                    <a href="{{ route('admin.hrm.payroll.review.latest') }}"
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->routeIs('admin.hrm.payroll.review*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-500 hover:text-indigo-600 hover:bg-slate-50' }}">
                        Payroll Review
                    </a>
                    <a href="{{ route('admin.hrm.payroll.payslips') }}"
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->routeIs('admin.hrm.payroll.payslips*') || request()->routeIs('admin.hrm.payroll.payslip.print') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-500 hover:text-indigo-600 hover:bg-slate-50' }}">
                        Employee Payslips
                    </a>
                </div>
            </div>

            <!-- Performance Management Accordion -->
            <div>
                <button type="button" 
                        @click="sidebarCollapsed ? (toggleSidebarCollapse(), toggle('performance')) : toggle('performance')"
                        :title="sidebarCollapsed ? 'Performance Management' : ''"
                        class="w-full flex items-center rounded-xl text-xs font-semibold text-slate-700 hover:text-slate-900 hover:bg-slate-100/70 transition-colors"
                        :class="sidebarCollapsed ? 'md:justify-center md:px-0 py-2.5 px-3 justify-between' : 'justify-between px-3 py-2'">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-indigo-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                        <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Performance Management</span>
                    </div>
                    <svg x-show="!sidebarCollapsed" class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="openMenus.performance ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="openMenus.performance && !sidebarCollapsed" @if(!$isPerformanceOpen) x-cloak style="display: none;" @endif class="pl-7 pr-1 py-1 space-y-0.5 border-l-2 border-slate-100 ml-4.5 my-0.5">
                    <a href="{{ route('admin.hrm.performance.goals') }}"
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->routeIs('admin.hrm.performance.goals') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-500 hover:text-indigo-600 hover:bg-slate-50' }}">
                        Goals & OKRs
                    </a>
                    <a href="{{ route('admin.hrm.performance.goals.create') }}"
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->routeIs('admin.hrm.performance.goals.create*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-500 hover:text-indigo-600 hover:bg-slate-50' }}">
                        New Goal
                    </a>
                    <a href="{{ route('admin.hrm.performance.appraisals') }}"
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->routeIs('admin.hrm.performance.appraisals*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-500 hover:text-indigo-600 hover:bg-slate-50' }}">
                        Appraisal List
                    </a>
                    <a href="{{ route('admin.hrm.performance.cycles') }}"
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->routeIs('admin.hrm.performance.cycles*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-500 hover:text-indigo-600 hover:bg-slate-50' }}">
                        Appraisal Cycles
                    </a>
                    <a href="{{ route('admin.hrm.performance.feedback') }}"
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->routeIs('admin.hrm.performance.feedback*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-500 hover:text-indigo-600 hover:bg-slate-50' }}">
                        Feedback & Praise
                    </a>
                </div>
            </div>

            {{-- ==================== SECTION 7: ORGANIZATION ==================== --}}
            <div class="px-2 pt-3 pb-1" :class="sidebarCollapsed ? 'md:px-0 md:text-center' : 'px-2'">
                <span x-show="!sidebarCollapsed" class="text-[10px] font-bold text-slate-400 uppercase tracking-wider select-none">Organization</span>
                <div x-show="sidebarCollapsed" class="hidden md:block h-px w-6 mx-auto bg-slate-200 my-1"></div>
            </div>

            <!-- Staff Accordion -->
            @if(auth()->user()?->isSuperAdmin() || auth()->user()?->hasPermission('roles.manage'))
            <div>
                <button type="button" 
                        @click="sidebarCollapsed ? (toggleSidebarCollapse(), toggle('staff')) : toggle('staff')"
                        :title="sidebarCollapsed ? 'Staff' : ''"
                        class="w-full flex items-center rounded-xl text-xs font-semibold transition-colors {{ request()->routeIs('admin.roles*') || request()->routeIs('admin.staff*') ? 'text-indigo-600 font-bold bg-indigo-50/50' : 'text-slate-700 hover:text-slate-900 hover:bg-slate-100/70' }}"
                        :class="sidebarCollapsed ? 'md:justify-center md:px-0 py-2.5 px-3 justify-between' : 'justify-between px-3 py-2'">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-indigo-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Staff</span>
                    </div>
                    <svg x-show="!sidebarCollapsed" class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="openMenus.staff ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="openMenus.staff && !sidebarCollapsed" @if(!$isStaffOpen) x-cloak style="display: none;" @endif class="pl-7 pr-1 py-1 space-y-0.5 border-l-2 border-slate-100 ml-4.5 my-0.5">
                    <a href="{{ route('admin.staff.index') }}" 
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->routeIs('admin.staff.index') ? 'bg-indigo-50 text-indigo-600 font-bold shadow-xs' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">
                        Staff List
                    </a>
                    <a href="{{ route('admin.roles.index') }}" 
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->routeIs('admin.roles*') ? 'bg-indigo-50 text-indigo-600 font-bold shadow-xs' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">
                        Roles &amp; Permissions
                    </a>
                    <a href="{{ route('admin.staff.departments') }}" 
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->routeIs('admin.staff.departments*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">
                        Departments &amp; Shifts
                    </a>
                    <a href="{{ route('admin.staff.attendance') }}" 
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->routeIs('admin.staff.attendance*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">
                        Attendance Time Clock
                    </a>
                </div>
            </div>
            @endif

            <!-- Support Accordion -->
            <div>
                <button type="button" 
                        @click="sidebarCollapsed ? (toggleSidebarCollapse(), toggle('support')) : toggle('support')"
                        :title="sidebarCollapsed ? 'Support' : ''"
                        class="w-full flex items-center rounded-xl text-xs font-semibold text-slate-700 hover:text-slate-900 hover:bg-slate-100/70 transition-colors"
                        :class="sidebarCollapsed ? 'md:justify-center md:px-0 py-2.5 px-3 justify-between' : 'justify-between px-3 py-2'">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-indigo-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Support</span>
                    </div>
                    <div class="flex items-center gap-1.5" x-show="!sidebarCollapsed">
                        <span class="w-5 h-5 rounded-full bg-indigo-600 text-white text-[10px] font-bold flex items-center justify-center shrink-0 shadow-xs">2</span>
                        <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="openMenus.support ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </button>
                <div x-show="openMenus.support && !sidebarCollapsed" @if(!$isSupportOpen) x-cloak style="display: none;" @endif class="pl-7 pr-1 py-1 space-y-0.5 border-l-2 border-slate-100 ml-4.5 my-0.5">
                    <a href="{{ route('admin.support.tickets.index') }}"
                       class="flex items-center justify-between px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->routeIs('admin.support.tickets*') ? 'text-indigo-600 font-semibold bg-indigo-50/80' : 'text-slate-500 hover:text-indigo-600 hover:bg-slate-50' }}">
                        <span>Tickets &amp; SLA Desk</span>
                    </a>
                    <a href="{{ route('admin.support.kb.index') }}"
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->routeIs('admin.support.kb*') ? 'text-indigo-600 font-semibold bg-indigo-50/80' : 'text-slate-500 hover:text-indigo-600 hover:bg-slate-50' }}">
                        Knowledge Base
                    </a>
                    <a href="{{ route('admin.support.faq.index') }}"
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->routeIs('admin.support.faq*') ? 'text-indigo-600 font-semibold bg-indigo-50/80' : 'text-slate-500 hover:text-indigo-600 hover:bg-slate-50' }}">
                        Storefront FAQ
                    </a>
                </div>
            </div>

            <!-- Settings Accordion -->
            @if(auth()->user()?->isSuperAdmin() || auth()->user()?->hasPermission('settings.manage'))
            <div>
                <button type="button" 
                        @click="sidebarCollapsed ? (toggleSidebarCollapse(), toggle('settings')) : toggle('settings')"
                        :title="sidebarCollapsed ? 'Settings' : ''"
                        class="w-full flex items-center rounded-xl text-xs font-semibold transition-colors {{ request()->routeIs('admin.settings*') || request()->routeIs('admin.themes*') || request()->routeIs('admin.pages*') ? 'text-indigo-600 font-bold bg-indigo-50/50' : 'text-slate-700 hover:text-slate-900 hover:bg-slate-100/70' }}"
                        :class="sidebarCollapsed ? 'md:justify-center md:px-0 py-2.5 px-3 justify-between' : 'justify-between px-3 py-2'">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-indigo-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Settings</span>
                    </div>
                    <svg x-show="!sidebarCollapsed" class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="openMenus.settings ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="openMenus.settings && !sidebarCollapsed" @if(!$isSettingsOpen) x-cloak style="display: none;" @endif class="pl-7 pr-1 py-1 space-y-0.5 border-l-2 border-slate-100 ml-4.5 my-0.5">
                    <a href="{{ route('admin.settings.index') }}" 
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->routeIs('admin.settings.index') && !request()->has('tab') ? 'bg-indigo-50 text-indigo-600 font-bold shadow-xs' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">
                        Store Settings
                    </a>
                    <a href="{{ route('admin.settings.index') }}#ticker-settings" 
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all text-slate-600 hover:text-indigo-600 hover:bg-slate-50">
                        Announcement Ticker
                    </a>
                    <a href="{{ route('admin.settings.index') }}#about-settings" 
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all text-slate-600 hover:text-indigo-600 hover:bg-slate-50">
                        About Us Page
                    </a>
                    <a href="{{ route('admin.settings.index') }}#policy-settings" 
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all text-slate-600 hover:text-indigo-600 hover:bg-slate-50">
                        Policies &amp; Size Guide
                    </a>
                    <a href="{{ route('admin.themes.index') }}" 
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->routeIs('admin.themes*') ? 'bg-indigo-50 text-indigo-600 font-bold shadow-xs' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">
                        Theme Switcher
                    </a>
                    <a href="{{ route('admin.pages.index') }}" 
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->routeIs('admin.pages*') ? 'bg-indigo-50 text-indigo-600 font-bold shadow-xs' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">
                        Visual Page Builder
                    </a>
                    <a href="{{ route('admin.settings.gateways') }}" 
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->routeIs('admin.settings.gateways*') ? 'bg-indigo-50 text-indigo-600 font-bold shadow-xs' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">
                        Payment Gateways
                    </a>
                    <a href="{{ route('admin.settings.sms') }}" 
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->routeIs('admin.settings.sms*') ? 'bg-indigo-50 text-indigo-600 font-bold shadow-xs' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">
                        SMS &amp; OTP Gateways
                    </a>
                    <a href="{{ route('admin.settings.analytics') }}" 
                       class="block px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition-all {{ request()->routeIs('admin.settings.analytics*') ? 'bg-indigo-50 text-indigo-600 font-bold shadow-xs' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">
                        Analytics &amp; Tags
                    </a>
                </div>
            </div>
            @endif

        </nav>

        <!-- Immediate Synchronous Sidebar Scroll Restoration -->
        <script>
            (function() {
                var nav = document.getElementById('adminSidebarNav');
                if (nav) {
                    var saved = sessionStorage.getItem('admin_sidebar_scroll');
                    if (saved !== null && parseInt(saved, 10) > 0) {
                        nav.scrollTop = parseInt(saved, 10);
                    }
                }
            })();
        </script>

        <!-- Sidebar Sticky Controls & Footer (Permanently pinned at bottom) -->
        <div class="shrink-0 border-t border-slate-200/80 bg-white z-10"
             :class="sidebarCollapsed ? 'md:p-2 md:space-y-2 p-3 space-y-2.5' : 'p-3 space-y-2.5'">
            
            <!-- Sticky Collapse / Expand Toggle Button -->
            <button type="button" 
                    @click="window.innerWidth < 768 ? (sidebarOpen = false) : toggleSidebarCollapse()" 
                    class="w-full flex items-center rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors group"
                    :class="sidebarCollapsed ? 'md:justify-center md:px-0 py-2.5 px-2.5 justify-between' : 'justify-between px-2.5 py-2 bg-slate-50 border border-slate-200/70 shadow-2xs'"
                    :title="sidebarCollapsed ? 'Expand Sidebar (⌘\\)' : 'Collapse Sidebar (⌘\\)'">
                <div class="flex items-center gap-2.5">
                    <svg x-show="!sidebarCollapsed" class="w-4 h-4 text-slate-400 group-hover:text-slate-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/></svg>
                    <svg x-show="sidebarCollapsed" class="w-4 h-4 text-indigo-500 group-hover:text-indigo-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/></svg>
                    <span x-show="!sidebarCollapsed" class="whitespace-nowrap font-medium text-slate-700">Collapse Sidebar</span>
                </div>
                <span x-show="!sidebarCollapsed" class="text-[10px] text-slate-400 font-mono bg-white px-1.5 py-0.5 rounded border border-slate-200/80">⌘\</span>
            </button>

            <!-- Admin Theme Status & Toggle Pill -->
            <div class="flex items-center text-xs"
                 :class="sidebarCollapsed ? 'md:justify-center md:px-0 px-1' : 'justify-between px-1'"
                 x-data="{ switchDark: document.documentElement.classList.contains('dark') }"
                 @admin-theme-changed.window="switchDark = $event.detail.isDark">
                <span x-show="!sidebarCollapsed" class="text-slate-500 font-medium text-[11px] whitespace-nowrap">Admin Theme:</span>
                <button type="button" 
                        @click="window.toggleAdminTheme()"
                        class="inline-flex items-center gap-1.5 rounded-full text-[10px] font-bold transition-all"
                        :class="[
                            switchDark ? 'bg-indigo-950/80 text-indigo-300 border border-indigo-800/60' : 'bg-slate-100 text-slate-700 border border-slate-200/90 hover:bg-slate-200/70',
                            sidebarCollapsed ? 'md:p-1.5 md:border-0 md:bg-transparent px-2.5 py-1' : 'px-2.5 py-1'
                        ]"
                        :title="switchDark ? 'Admin Theme: Black (Click to switch to White)' : 'Admin Theme: White (Click to switch to Black)'">
                    <span class="w-1.5 h-1.5 rounded-full shrink-0" :class="switchDark ? 'bg-indigo-400 shadow-[0_0_6px_rgba(129,140,248,0.9)] animate-pulse' : 'bg-amber-500'"></span>
                    <span x-show="!sidebarCollapsed" x-text="switchDark ? 'Black Theme (ON)' : 'White Theme (OFF)'"></span>
                </button>
            </div>

            <!-- Current Storefront Theme Status Pill -->
            <div class="flex items-center text-xs"
                 :class="sidebarCollapsed ? 'md:justify-center md:px-0 px-1' : 'justify-between px-1'">
                <span x-show="!sidebarCollapsed" class="text-slate-500 font-medium text-[11px] whitespace-nowrap">Store Theme:</span>
                <span class="inline-flex items-center gap-1.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200"
                      :class="sidebarCollapsed ? 'md:p-1.5 md:border-0 md:bg-transparent px-2 py-0.5' : 'px-2 py-0.5'"
                      :title="'Active Storefront: ' + '{{ ucfirst($currentTheme->name ?? $currentTheme->key ?? 'Saaya') }}'">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse shrink-0"></span>
                    <span x-show="!sidebarCollapsed">{{ ucfirst($currentTheme->name ?? $currentTheme->key ?? 'Saaya') }}</span>
                </span>
            </div>

            <!-- Quick Action Buttons -->
            <div :class="sidebarCollapsed ? 'md:flex md:flex-col md:gap-1.5 grid grid-cols-2 gap-2' : 'grid grid-cols-2 gap-2'">
                <form method="POST" action="{{ route('admin.optimize.clear') }}" onsubmit="return confirm('Clear application, route, config, and view cache?')" class="m-0 p-0 block">
                    @csrf
                    <button type="submit"
                       class="w-full flex items-center justify-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-white border border-slate-200 hover:bg-slate-100/80 text-slate-700 text-[11px] font-semibold transition shadow-2xs cursor-pointer"
                       :title="'Clear Cache'">
                        <svg class="w-3.5 h-3.5 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Clear Cache</span>
                    </button>
                </form>
                <a href="{{ route('home') }}" target="_blank" 
                   class="flex items-center justify-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-[11px] font-semibold transition shadow-xs"
                   :title="'Live Store'">
                    <svg class="w-3.5 h-3.5 text-white/90 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Live Store</span>
                </a>
            </div>

            <!-- Version Tagline & Platform Attribution -->
            <div x-show="!sidebarCollapsed" class="space-y-1 px-1 text-[10px] text-slate-400 dark:text-slate-500">
                <div class="flex items-center justify-between font-mono">
                    <span>Laravel v{{ Illuminate\Foundation\Application::VERSION }}</span>
                    <span>PHP {{ PHP_VERSION }}</span>
                </div>
                <div class="text-center font-medium">
                    <span class="platform-branding-attribution inline-block" data-nexon-platform-branding="verified">• Powered by Nex-on</span>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Workspace & Topbar (Updated dynamically on SPA navigations) -->
    <div id="adminWorkspace" class="flex-1 flex flex-col min-w-0 h-full overflow-y-auto bg-slate-50">
        <!-- Topbar -->
        <header class="bg-white border-b border-slate-200/80 sticky top-0 z-30 shadow-xs">
            <div class="px-6 sm:px-8 py-3.5 flex items-center justify-between">
                <!-- Breadcrumbs / Status -->
                <div class="flex items-center gap-2 text-xs font-medium text-slate-500">
                    <button type="button" 
                            @click="toggleSidebarCollapse()" 
                            class="hidden md:inline-flex items-center justify-center p-1.5 -ml-2 mr-1 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50/70 rounded-lg transition-colors"
                            :title="sidebarCollapsed ? 'Expand Sidebar (⌘\\)' : 'Collapse Sidebar (⌘\\)'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                        </svg>
                    </button>
                    <span class="text-slate-400">Admin</span>
                    <span>/</span>
                    <span class="text-slate-800 font-semibold">{{ $breadcrumb ?? 'Dashboard' }}</span>
                </div>

                <!-- Right Quick Actions -->
                <div class="flex items-center gap-3">
                    <!-- Black Theme Switch On/Off Button -->
                    <div class="flex items-center"
                         x-data="{ switchDark: document.documentElement.classList.contains('dark') }"
                         @admin-theme-changed.window="switchDark = $event.detail.isDark">
                        <button type="button" 
                                @click="window.toggleAdminTheme()" 
                                class="relative inline-flex items-center gap-2 px-3 py-1.5 rounded-xl border transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 select-none group"
                                :class="switchDark ? 'bg-slate-900 border-slate-700/80 text-slate-100 shadow-inner' : 'bg-slate-100 hover:bg-slate-200/70 border-slate-200 text-slate-700'"
                                :title="switchDark ? 'Black Theme is ON — Click to switch to White Theme (Ctrl+Shift+D)' : 'Black Theme is OFF — Click to switch to Black Theme (Ctrl+Shift+D)'"
                                role="switch" 
                                :aria-checked="switchDark.toString()">
                            
                            <!-- Sun / Moon Icon -->
                            <div class="relative w-4 h-4 flex items-center justify-center">
                                <svg x-show="!switchDark" class="w-4 h-4 text-amber-500 group-hover:rotate-45 transition-transform duration-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="4"/><path stroke-linecap="round" d="M12 2v2m0 16v2M4.93 4.93l1.41 1.41m11.32 11.32l1.41 1.41M2 12h2m16 0h2M6.34 17.66l-1.41 1.41m14.14-14.14l-1.41 1.41"/>
                                </svg>
                                <svg x-show="switchDark" class="w-4 h-4 text-amber-300 group-hover:-rotate-12 transition-transform duration-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/>
                                </svg>
                            </div>

                            <!-- Label -->
                            <span class="text-xs font-bold leading-none hidden xl:inline">Black Theme</span>

                            <!-- Physical Switch slider track & knob -->
                            <div class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out"
                                 :class="switchDark ? 'bg-indigo-600' : 'bg-slate-300'">
                                <span aria-hidden="true" 
                                      class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow-xs ring-0 transition duration-200 ease-in-out"
                                      :class="switchDark ? 'translate-x-4' : 'translate-x-0'"></span>
                            </div>

                            <!-- ON / OFF text pill badge -->
                            <span class="text-[10px] font-extrabold uppercase tracking-wider px-1.5 py-0.5 rounded-md leading-none"
                                  :class="switchDark ? 'bg-indigo-500/20 text-indigo-300' : 'bg-slate-200/90 text-slate-500'"
                                  x-text="switchDark ? 'ON' : 'OFF'"></span>
                        </button>
                    </div>

                    <a href="{{ route('admin.pos.index') }}" 
                       class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-bold bg-emerald-500 hover:bg-emerald-600 text-white shadow-xs transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        <span>POS Terminal</span>
                    </a>

                    <a href="{{ route('admin.orders.index') }}" 
                       class="relative p-2 rounded-lg text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition" 
                       title="Orders">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        @if($pendingCount > 0)
                            <span class="absolute top-1 right-1 w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
                        @endif
                    </a>

                    <div class="h-4 w-px bg-slate-200"></div>

                    <!-- User Pill -->
                    <div class="flex items-center gap-2">
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 hover:opacity-80 transition group" title="Account Security & Profile">
                            <div class="w-7 h-7 rounded-full bg-indigo-600 group-hover:bg-indigo-700 text-white flex items-center justify-center font-bold text-xs shadow-xs">
                                {{ mb_strtoupper(mb_substr(Auth::user()->name ?? 'A', 0, 1)) }}
                            </div>
                            <span class="hidden sm:inline text-xs font-semibold text-slate-700 group-hover:text-indigo-600">{{ Auth::user()->name ?? 'Administrator' }}</span>
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-xs text-rose-500 hover:text-rose-700 font-medium ml-1">Exit</button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Flash Notifications -->
        <div class="px-6 sm:px-8 pt-6">
            @if(session('success'))
                <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50/90 p-4 text-sm text-emerald-800 flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 rounded-xl border border-rose-200 bg-rose-50/90 p-4 text-sm text-rose-800 flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 rounded-xl border border-rose-200 bg-rose-50/90 p-4 text-sm text-rose-800 shadow-xs">
                    <div class="flex items-center gap-2 font-semibold mb-1">
                        <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>Please correct the errors below:</span>
                    </div>
                    <ul class="list-disc list-inside text-xs space-y-1 pl-2 text-rose-700">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <!-- Page Content -->
        <main class="flex-1 px-6 sm:px-8 pb-12">
            @if(isset($slot) && !empty((string)$slot))
                {{ $slot }}
            @else
                @yield('content')
            @endif
        </main>
    </div>

    <!-- Admin Seamless Navigation Engine (Zero Sidebar Reload + Frozen Scroll Persistence) -->
    <script>
        (function() {
            var nav = document.getElementById('adminSidebarNav');
            var workspace = document.getElementById('adminWorkspace');
            var pjaxBar = document.getElementById('adminPjaxBar');

            // 1. ROCK-SOLID SCROLL PERSISTENCE (Zero Bouncing, Single Pass)
            function restoreSidebarScrollOnce() {
                if (!nav) return;
                var saved = sessionStorage.getItem('admin_sidebar_scroll');
                if (saved !== null && parseInt(saved, 10) > 0) {
                    nav.scrollTop = parseInt(saved, 10);
                    return;
                }

                var activeLink = nav.querySelector('a.bg-indigo-50, a.text-indigo-600, a.text-indigo-700');
                if (!activeLink) return;

                // Check if active item is already visible in container viewport
                var navRect = nav.getBoundingClientRect();
                var linkRect = activeLink.getBoundingClientRect();
                if (linkRect.top >= navRect.top && linkRect.bottom <= navRect.bottom) {
                    return; // Already in view, do not move scroll position at all
                }

                // If offscreen, bring into view without centering or bouncing
                activeLink.scrollIntoView({ block: 'nearest', behavior: 'instant' });
                sessionStorage.setItem('admin_sidebar_scroll', nav.scrollTop);
            }

            if (nav) {
                nav.addEventListener('scroll', function() {
                    try {
                        sessionStorage.setItem('admin_sidebar_scroll', nav.scrollTop);
                    } catch (e) {}
                }, { passive: true });
            }

            // Execute strictly once on document readiness
            if (document.readyState === 'loading') {
                window.addEventListener('DOMContentLoaded', restoreSidebarScrollOnce, { once: true });
            } else {
                restoreSidebarScrollOnce();
            }

            // 2. PROGRESS BAR CONTROLS
            var progressTimer = null;
            function startProgress() {
                if (!pjaxBar) return;
                clearTimeout(progressTimer);
                pjaxBar.style.transition = 'width 0.25s ease, opacity 0.1s ease';
                pjaxBar.style.opacity = '1';
                pjaxBar.style.width = '35%';
                progressTimer = setTimeout(function() {
                    pjaxBar.style.width = '75%';
                }, 150);
            }

            function endProgress() {
                if (!pjaxBar) return;
                clearTimeout(progressTimer);
                pjaxBar.style.width = '100%';
                setTimeout(function() {
                    pjaxBar.style.opacity = '0';
                    setTimeout(function() {
                        pjaxBar.style.width = '0%';
                    }, 200);
                }, 150);
            }

            // 3. SEAMLESS SPA NAVIGATION (Keeps Sidebar Loaded & Scroll Frozen)
            var isNavigating = false;

            async function navigateTo(url, pushState) {
                if (isNavigating) return;
                isNavigating = true;
                startProgress();

                try {
                    var response = await fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-Admin-Pjax': '1'
                        }
                    });

                    // If redirected (e.g. session expired -> login) or error: fallback to full browser navigation
                    if (!response.ok || response.redirected || (response.url && !response.url.includes('/admin'))) {
                        window.location.href = response.url || url;
                        return;
                    }

                    var htmlText = await response.text();
                    var parser = new DOMParser();
                    var newDoc = parser.parseFromString(htmlText, 'text/html');

                    var newWorkspace = newDoc.getElementById('adminWorkspace');
                    var currWorkspace = document.getElementById('adminWorkspace');

                    if (!newWorkspace || !currWorkspace) {
                        window.location.href = url;
                        return;
                    }

                    // Update page title
                    if (newDoc.title) {
                        document.title = newDoc.title;
                    }

                    // FREEZE EXACT SIDEBAR SCROLL POSITION
                    var preservedScroll = nav ? nav.scrollTop : 0;

                    // Cleanly destroy and re-mount Alpine within the workspace
                    if (window.Alpine) {
                        try { window.Alpine.destroyTree(currWorkspace); } catch (e) {}
                    }

                    currWorkspace.innerHTML = newWorkspace.innerHTML;
                    currWorkspace.scrollTop = 0; // Content area scrolls to top

                    // Execute any page scripts in the swapped content
                    currWorkspace.querySelectorAll('script').forEach(function(oldScript) {
                        var newScript = document.createElement('script');
                        Array.from(oldScript.attributes).forEach(function(attr) {
                            newScript.setAttribute(attr.name, attr.value);
                        });
                        newScript.appendChild(document.createTextNode(oldScript.innerHTML));
                        oldScript.parentNode.replaceChild(newScript, oldScript);
                    });

                    if (window.Alpine) {
                        try { window.Alpine.initTree(currWorkspace); } catch (e) {}
                    }

                    // Update sidebar active highlights WITHOUT reloading or shifting the sidebar
                    updateSidebarHighlights(url);

                    // Re-enforce preserved sidebar scroll position (zero shift)
                    if (nav) {
                        nav.scrollTop = preservedScroll;
                        sessionStorage.setItem('admin_sidebar_scroll', preservedScroll);
                    }

                    // Push URL to browser history
                    if (pushState !== false) {
                        window.history.pushState({ pjax: true, url: url }, '', url);
                    }

                    // Close mobile sidebar if open
                    var bodyEl = document.querySelector('body');
                    if (bodyEl && bodyEl._x_dataStack && bodyEl._x_dataStack[0] && window.innerWidth < 768) {
                        bodyEl._x_dataStack[0].sidebarOpen = false;
                    }

                } catch (err) {
                    console.warn('[Admin PJAX] Navigation fallback:', err);
                    window.location.href = url;
                } finally {
                    endProgress();
                    isNavigating = false;
                }
            }

            function updateSidebarHighlights(targetUrl) {
                if (!nav) return;
                var currentLinks = nav.querySelectorAll('a[href]');
                var targetPath = new URL(targetUrl, window.location.origin).pathname;

                currentLinks.forEach(function(link) {
                    var linkUrl = new URL(link.getAttribute('href'), window.location.origin);
                    var isExactMatch = (linkUrl.pathname === targetPath);

                    if (isExactMatch) {
                        link.classList.remove('text-slate-600', 'text-slate-500', 'hover:bg-slate-50');
                        link.classList.add('bg-indigo-50', 'text-indigo-600', 'font-bold', 'shadow-xs');

                        // Ensure parent accordion is open and collapse others in accordion mode
                        var parentAccordion = link.closest('[x-show*="openMenus"]');
                        if (parentAccordion) {
                            parentAccordion.style.display = '';
                            var match = parentAccordion.getAttribute('x-show')?.match(/openMenus\.(\w+)/);
                            if (match && match[1]) {
                                var asideEl = document.querySelector('aside');
                                if (asideEl && asideEl._x_dataStack && asideEl._x_dataStack[0]) {
                                    var asideData = asideEl._x_dataStack[0];
                                    if (asideData.openMenus) {
                                        Object.keys(asideData.openMenus).forEach(function(k) {
                                            asideData.openMenus[k] = false;
                                        });
                                        asideData.openMenus[match[1]] = true;
                                    }
                                }
                            }
                        }
                    } else if (link.getAttribute('href').includes('/admin/')) {
                        link.classList.remove('bg-indigo-50', 'text-indigo-600', 'text-indigo-700', 'font-bold', 'shadow-xs');
                        link.classList.add('text-slate-600');
                    }
                });
            }

            // 4. INTERCEPT INTERNAL ADMIN CLICKS
            document.addEventListener('click', function(e) {
                if (e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey || e.defaultPrevented) return;

                var link = e.target.closest('a');
                if (!link || !link.href) return;

                if (link.target === '_blank' || link.hasAttribute('download') || link.hasAttribute('data-no-pjax') || link.hasAttribute('onclick')) return;

                var href = link.getAttribute('href');
                if (!href || href.startsWith('#') || href.startsWith('javascript:')) return;

                var url = new URL(link.href, window.location.origin);
                if (url.origin !== window.location.origin || !url.pathname.includes('/admin')) return;

                if (href.includes('/optimize/clear') || href.includes('/logout')) return;

                if (url.href === window.location.href) {
                    e.preventDefault();
                    if (workspace) workspace.scrollTop = 0;
                    return;
                }

                e.preventDefault();
                navigateTo(url.href, true);
            });

            // 5. BROWSER BACK / FORWARD POPSTATE
            window.addEventListener('popstate', function(e) {
                if (window.location.pathname.includes('/admin')) {
                    navigateTo(window.location.href, false);
                }
            });

            // 6. INSTANT HOVER PREFETCHING
            var prefetched = new Set();
            document.addEventListener('mouseover', function(e) {
                var link = e.target.closest('a[href*="/admin"]');
                if (!link || link.target === '_blank' || link.hasAttribute('download') || link.hasAttribute('data-no-pjax')) return;
                var href = link.href;
                if (!href || prefetched.has(href) || href.includes('#') || href.includes('/optimize/clear') || href.includes('/logout')) return;
                prefetched.add(href);
                var prefetchTag = document.createElement('link');
                prefetchTag.rel = 'prefetch';
                prefetchTag.href = href;
                document.head.appendChild(prefetchTag);
            }, { passive: true });
        })();
    </script>
    {{-- Client-Side WebP Converter: Converts JPG/PNG to WebP on uploader machine --}}
    <script src="{{ asset('js/webp-converter.js') }}"></script>
    @stack('scripts')
</body>
</html>