const CACHE_NAME = 'ShuleApp-cache-v5'; // Badilisha version kwa kila update kubwa

const ASSETS_TO_CACHE = [
    '/',
    '/index.php', // Laravel main entry file
    '/assets/css/styles.css',
    '/assets/js/scripts.js',
    '/icons/icon.png',
    '/icons/icon_2.png'
];

// Install event - Cache static assets
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            console.log('Caching static assets...');
            return cache.addAll(ASSETS_TO_CACHE).catch((error) => {
                console.error('Failed to cache assets:', error);
            });
        })
    );
    self.skipWaiting(); // Lazimisha Service Worker kuchukua control mara moja
});

// Activate event - Futa cache za zamani
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cache) => {
                    if (cache !== CACHE_NAME) {
                        console.log('Deleting old cache:', cache);
                        return caches.delete(cache); // Futa cache za zamani
                    }
                })
            );
        })
    );
    self.clients.claim(); // Hakikisha Service Worker mpya inachukua control ya pages zote
});

// Fetch event - Cache-first strategy kwa static files, network-first kwa dynamic content
self.addEventListener('fetch', (event) => {
    const url = new URL(event.request.url);

    // Cache-first strategy kwa static assets
    if (ASSETS_TO_CACHE.includes(url.pathname)) {
        event.respondWith(
            caches.match(event.request).then((cachedResponse) => {
                if (cachedResponse) {
                    console.log('Serving from cache:', event.request.url);
                    return cachedResponse;
                }
                return fetch(event.request).then((networkResponse) => {
                    if (!networkResponse || networkResponse.status !== 200) {
                        return networkResponse;
                    }
                    console.log('Caching new asset:', event.request.url);
                    const responseClone = networkResponse.clone();
                    caches.open(CACHE_NAME).then((cache) => {
                        cache.put(event.request, responseClone);
                    });
                    return networkResponse;
                });
            })
        );
    } else {
        // Network-first strategy kwa dynamic content
        event.respondWith(
            fetch(event.request)
                .then((networkResponse) => {
                    if (!networkResponse || networkResponse.status !== 200) {
                        return networkResponse;
                    }
                    console.log('Caching dynamic content:', event.request.url);
                    const responseClone = networkResponse.clone();
                    caches.open(CACHE_NAME).then((cache) => {
                        cache.put(event.request, responseClone);
                    });
                    return networkResponse;
                })
                .catch(() => {
                    console.log('Falling back to cache for:', event.request.url);
                    return caches.match(event.request);
                })
        );
    }
});

// Badala ya kulazimisha page reload, toa notification ya update
self.addEventListener('controllerchange', () => {
    self.clients.matchAll().then((clients) => {
        clients.forEach((client) => {
            client.postMessage({ action: 'refresh' });
        });
    });
});
