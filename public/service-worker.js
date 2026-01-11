const CACHE_NAME = 'ShuleApp-cache-v2026.01'; // Increase/change version number
const APP_VERSION = '2026.01'; // ⬅️ ADD SEPARATE VERSION VARIABLE
const ASSETS_TO_CACHE = [
    '/manifest.json',
    '/assets/css/styles.css',
    '/assets/js/scripts.js',
    '/icons/new_icon1.png',
    '/icons/new_icon2.png',
    '/icons/new_icon3.png',
    // '/icons/icon_4.png',
    '/offline.html'
].map(url => `${url}?v=${APP_VERSION}`); // Auto-add version parameter

// Install Event
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => cache.addAll(ASSETS_TO_CACHE))
            .then(() => self.skipWaiting())
    );
});

self.addEventListener('message', (event) => {
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
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

    // Skip non-GET requests and external URLs
    if (event.request.method !== 'GET' ||
        requestUrl.origin !== self.location.origin) return;

    // ⚡ 3. SMART CACHE MATCHING - FIXED!
    const requestWithoutQuery = requestUrl.pathname;
    const isCacheableAsset = ASSETS_TO_CACHE.some(cachedAsset => {
        const assetUrl = new URL(cachedAsset, self.location.origin);
        return assetUrl.pathname === requestWithoutQuery;
    });

    if (isCacheableAsset) {
        event.respondWith(
            // ⚡ 4. NETWORK FIRST WITH STALE-WHILE-REVALIDATE
            (async () => {
                try {
                    // Try network first
                    const networkResponse = await fetch(event.request);

                    // Update cache in background
                    const cache = await caches.open(CACHE_NAME);
                    await cache.put(event.request, networkResponse.clone());

                    return networkResponse;
                } catch (error) {
                    // Network failed - try cache
                    const cachedResponse = await caches.match(event.request);

                    if (cachedResponse) {
                        // ⚡ 5. BACKGROUND UPDATE IF CACHE IS OLD
                        setTimeout(async () => {
                            try {
                                const freshResponse = await fetch(event.request);
                                const cache = await caches.open(CACHE_NAME);
                                await cache.put(event.request, freshResponse);
                            } catch (e) {
                                // Silent fail - we have cached version
                            }
                        }, 0);

                        return cachedResponse;
                    }

                    // No cache - show offline page
                    return caches.match(`/offline.html?v=${APP_VERSION}`);
                }
            })()
        );
        return;
    }

    // ⚡ 6. FOR OTHER REQUESTS: NETWORK FIRST
    event.respondWith(
        fetch(event.request)
            .catch(() => caches.match(`/offline.html?v=${APP_VERSION}`))
    );
});
