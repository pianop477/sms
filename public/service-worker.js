const CACHE_NAME = 'ShuleApp-cache-v4.0.1'; // Ongeza version number
const ASSETS_TO_CACHE = [
    '/manifest.json',
    '/assets/css/styles.css',
    '/assets/js/scripts.js',
    '/icons/icon.png',
    '/icons/icon_2.png',
    '/icons/icon_3.png',
    '/icons/icon_4.png',
    '/offline.html'
].map(url => `${url}?v=4.0.1`); // Auto-add version parameter

// Install Event
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => cache.addAll(ASSETS_TO_CACHE))
            .then(() => self.skipWaiting())
    );
});

// Activate Event - Cleanup old caches
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cache) => {
                    if (cache !== CACHE_NAME) {
                        return caches.delete(cache);
                    }
                })
            );
        }).then(() => self.clients.claim())
    );
});

// Fetch Event - Network first with cache fallback for static assets
self.addEventListener('fetch', (event) => {
    const requestUrl = new URL(event.request.url);

    // Skip non-GET requests
    if (event.request.method !== 'GET') return;

    // Handle static assets
    if (ASSETS_TO_CACHE.some(asset => {
        const assetUrl = new URL(asset, self.location.origin).pathname;
        return requestUrl.pathname === assetUrl.split('?')[0];
    })) {
        event.respondWith(
            fetch(event.request)
                .then(networkResponse => {
                    // Update cache with fresh response
                    return caches.open(CACHE_NAME)
                        .then(cache => {
                            cache.put(event.request, networkResponse.clone());
                            return networkResponse;
                        });
                })
                .catch(() => {
                    return caches.match(event.request)
                        .then(cachedResponse => cachedResponse || caches.match('/offline.html'));
                })
        );
        return;
    }

    // For other requests: Network first, offline page fallback
    event.respondWith(
        fetch(event.request)
            .catch(() => caches.match('/offline.html'))
    );
});
