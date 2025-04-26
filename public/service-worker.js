const CACHE_NAME = 'ShuleApp-cache-v3.0.1'; // Update version as needed
const ASSETS_TO_CACHE = [
    '/manifest.json?v=3.1.0',  // Only manifest should be cached
    '/assets/css/styles.css', // CSS should be cached
    '/assets/js/scripts.js',  // JS should be cached
    '/icons/icon.png',       // Icon should be cached
    '/icons/icon_2.png',     // Icon should be cached
    '/icons/icon_3.png',     // Icon should be cached
    '/offline.html'          // Offline page should be cached
];

// Install Event - Cache only necessary assets (manifest, logos, css, js, offline page)
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(ASSETS_TO_CACHE);
        })
    );
    self.skipWaiting();
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
        })
    );
    self.clients.claim();
});

// Fetch Event - Directly fetch from the network, with fallback to offline page if offline
self.addEventListener('fetch', (event) => {
    const requestUrl = new URL(event.request.url);

    // Check if the request is for one of the cached static files
    if (
        ['/manifest.json', '/assets/css/styles.css', '/assets/js/scripts.js', '/icons/icon.png', '/icons/icon_2.png', '/icons/icon_3.png'].includes(requestUrl.pathname)
    ) {
        event.respondWith(
            caches.match(event.request).then((cachedResponse) => {
                // If the request is cached, return the cached version; otherwise, fetch from network
                return cachedResponse || fetch(event.request);
            }).catch(() => {
                // If the network fails, serve the offline page
                return caches.match('/offline.html');
            })
        );
        return;
    }

    // For all other requests, fetch directly from the network, no caching
    event.respondWith(
        fetch(event.request).catch(() => {
            // If the network is not available, serve the offline page
            return caches.match('/offline.html');
        })
    );
});
