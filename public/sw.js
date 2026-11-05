/**
 * Service Worker for Progressive Web App (PWA)
 * Enables offline shell & assets caching while ensuring fresh live catalog/orders.
 */

const CACHE_NAME = 'luxury-store-pwa-v1';
const urlsToCache = [
    '/icons/icon-192x192.png',
    '/icons/icon-512x512.png'
];

self.addEventListener('install', event => {
    self.skipWaiting();
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => {
            return cache.addAll(urlsToCache);
        })
    );
});

self.addEventListener('fetch', event => {
    const request = event.request;

    // For HTML navigate requests, ALWAYS network first (fresh catalog, inventory, cart)
    if (request.mode === 'navigate' || request.headers.get('accept')?.includes('text/html')) {
        event.respondWith(
            fetch(request).catch(() => caches.match(request))
        );
        return;
    }

    // For static images/assets, try cache first, fallback to network
    event.respondWith(
        caches.match(request).then(response => {
            return response || fetch(request);
        })
    );
});

self.addEventListener('activate', event => {
    const cacheWhitelist = [CACHE_NAME];
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cacheName => {
                    if (!cacheWhitelist.includes(cacheName)) {
                        return caches.delete(cacheName);
                    }
                })
            );
        }).then(() => self.clients.claim())
    );
});
