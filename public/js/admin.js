/**
 * ==========================================================================
 * NEXON ADMIN PANEL — CORE JAVASCRIPT SYSTEM
 * Theme Engine, SPA PJAX Navigation, Scroll Persistence & Link Prefetching
 * ==========================================================================
 */

// 1. INSTANT THEME ENGINE
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
        var toggleUrl = window.adminThemeToggleUrl || document.body?.dataset?.themeToggleUrl || '/admin/themes/admin-toggle';
        if (token) {
            fetch(toggleUrl, {
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

// 2. SIDEBAR, PROGRESS BAR & PJAX NAVIGATION
(function() {
    var nav = document.getElementById('adminSidebarNav');
    var workspace = document.getElementById('adminWorkspace');
    var pjaxBar = document.getElementById('adminPjaxBar');

    // ROCK-SOLID SCROLL PERSISTENCE (Zero Bouncing, Single Pass)
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

    // PROGRESS BAR CONTROLS
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

    // SEAMLESS SPA NAVIGATION (Keeps Sidebar Loaded & Scroll Frozen)
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

            // Re-init rich editors & dispatch pjax completion event
            if (typeof window.initRichEditors === 'function') {
                try { window.initRichEditors(); } catch (e) {}
            }
            window.dispatchEvent(new CustomEvent('pjax:complete', { detail: { url: url } }));

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

    // INTERCEPT INTERNAL ADMIN CLICKS
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

    // BROWSER BACK / FORWARD POPSTATE
    window.addEventListener('popstate', function(e) {
        if (window.location.pathname.includes('/admin')) {
            navigateTo(window.location.href, false);
        }
    });

    // INSTANT HOVER PREFETCHING
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
