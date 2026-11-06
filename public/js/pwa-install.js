/**
 * Progressive Web App (PWA) Mobile App Installation Controller
 * PWA installation prompt with 24h dismissal logic and native iOS/Android handlers.
 */

(function () {
    'use strict';

    let deferredPrompt = null;
    const STORAGE_KEY = 'nexon-pwa-prompt-dismissed';
    const ONE_DAY_MS = 24 * 60 * 60 * 1000;

    // Detect if running as installed standalone app
    function isAppInstalled() {
        return window.matchMedia('(display-mode: standalone)').matches ||
            window.navigator.standalone === true ||
            document.referrer.includes('android-app://');
    }

    // Detect iOS devices (iPhone, iPad, iPod)
    function isIOS() {
        return /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
    }

    // Check if install prompt should automatically show
    function shouldShowPopup() {
        if (isAppInstalled()) {
            return false;
        }

        const lastDismissed = localStorage.getItem(STORAGE_KEY);
        if (!lastDismissed) {
            return true;
        }

        const timeSince = Date.now() - parseInt(lastDismissed, 10);
        return timeSince >= ONE_DAY_MS;
    }

    function markPopupDismissed() {
        localStorage.setItem(STORAGE_KEY, Date.now().toString());
    }

    function showInstallPopup() {
        const popup = document.getElementById('pwaInstallPopup');
        if (popup) {
            popup.classList.add('show');
        }
    }

    function hideInstallPopup() {
        const popup = document.getElementById('pwaInstallPopup');
        if (popup) {
            popup.classList.remove('show');
        }
    }

    // Fallback instructions for iOS Safari and browsers without beforeinstallprompt
    function showManualInstructions() {
        const popup = document.getElementById('pwaInstallPopup');
        if (!popup) return;

        const title = popup.querySelector('.pwa-popup-title');
        const desc = popup.querySelector('.pwa-popup-description');
        const installBtn = document.getElementById('pwaInstallBtn');
        const laterBtn = document.getElementById('pwaMaybeLaterBtn');

        if (isIOS()) {
            if (title) title.textContent = 'Install on iOS';
            if (desc) {
                desc.innerHTML = `
                    <ol class="pwa-instruction-list">
                        <li>Tap the <strong>Share</strong> button <svg class="inline w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg> in Safari.</li>
                        <li>Scroll down and tap <strong>Add to Home Screen</strong>.</li>
                        <li>Tap <strong>Add</strong> in the top-right corner.</li>
                    </ol>
                `;
            }
        } else {
            if (title) title.textContent = 'Install App';
            if (desc) {
                desc.innerHTML = `
                    <ol class="pwa-instruction-list">
                        <li>Tap browser menu (<strong>⋮</strong> or <strong>⋯</strong>).</li>
                        <li>Select <strong>Install app</strong> or <strong>Add to Home Screen</strong>.</li>
                        <li>Confirm installation to add to your device.</li>
                    </ol>
                `;
            }
        }

        if (installBtn) installBtn.style.display = 'none';
        if (laterBtn) {
            laterBtn.textContent = 'Got It';
            laterBtn.classList.add('pwa-got-it');
        }
    }

    async function handleInstallClick() {
        if (!deferredPrompt) {
            showManualInstructions();
            return;
        }

        try {
            deferredPrompt.prompt();
            const choiceResult = await deferredPrompt.userChoice;
            if (choiceResult && choiceResult.outcome === 'accepted') {
                markPopupDismissed();
            } else {
                markPopupDismissed();
            }
        } catch (err) {
            console.error('PWA install prompt error:', err);
            showManualInstructions();
        }

        deferredPrompt = null;
        hideInstallPopup();
    }

    function handleMaybeLaterClick() {
        markPopupDismissed();
        hideInstallPopup();
    }

    function handleCloseClick() {
        markPopupDismissed();
        hideInstallPopup();
    }

    // Listen for beforeinstallprompt event from Chromium browsers
    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredPrompt = e;

        if (shouldShowPopup()) {
            setTimeout(() => {
                showInstallPopup();
            }, 2000);
        }
    });

    // Listen for successful installation
    window.addEventListener('appinstalled', () => {
        hideInstallPopup();
        markPopupDismissed();
        deferredPrompt = null;
    });

    // Public method to trigger manually (e.g. from footer button or admin test)
    window.showPwaInstallPrompt = function (force = false) {
        if (force || shouldShowPopup()) {
            showInstallPopup();
        }
    };

    document.addEventListener('DOMContentLoaded', () => {
        const installBtn = document.getElementById('pwaInstallBtn');
        const maybeLaterBtn = document.getElementById('pwaMaybeLaterBtn');
        const closeBtn = document.getElementById('pwaCloseBtn');

        if (installBtn) installBtn.addEventListener('click', handleInstallClick);
        if (maybeLaterBtn) maybeLaterBtn.addEventListener('click', handleMaybeLaterClick);
        if (closeBtn) closeBtn.addEventListener('click', handleCloseClick);

        // Fallback for browsers without beforeinstallprompt (e.g. iOS Safari)
        setTimeout(() => {
            if (shouldShowPopup() && !deferredPrompt && !isAppInstalled()) {
                showInstallPopup();
            }
        }, 3000);
    });
})();
