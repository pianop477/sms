const CACHE_NAME = 'ShuleApp-cache-v4'; // Update version ili kulazimisha update

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
            return Promise.all(
                ASSETS_TO_CACHE.map((asset) => {
                    return cache.add(asset).catch((error) => {
                        console.error(`Failed to cache: ${asset}`, error);
                    });
                })
            );
        })
    );
    self.skipWaiting();
});


// Activate event - Futa cache za zamani
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames
                    .filter((cache) => cache !== CACHE_NAME)
                    .map((cache) => caches.delete(cache))
            );
        })
    );
    self.clients.claim(); // Hakikisha service worker mpya inachukua control ya pages zote
});

// Fetch event - Cache-first strategy kwa static files, network-first kwa dynamic content
self.addEventListener('fetch', (event) => {
    const url = new URL(event.request.url);

    if (ASSETS_TO_CACHE.includes(url.pathname)) {
        // Cache-first strategy kwa static assets
        event.respondWith(
            caches.match(event.request).then((cachedResponse) => {
                return cachedResponse || fetch(event.request).then((networkResponse) => {
                    return caches.open(CACHE_NAME).then((cache) => {
                        cache.put(event.request, networkResponse.clone());
                        return networkResponse;
                    });
                });
            })
        );
    } else {
        // Network-first strategy kwa dynamic content
        event.respondWith(
            fetch(event.request)
                .then((response) => {
                    if (!response || response.status !== 200 || response.type !== 'basic') {
                        return response;
                    }

                    let responseClone = response.clone();
                    caches.open(CACHE_NAME).then((cache) => {
                        cache.put(event.request, responseClone);
                    });

                    return response;
                })
                .catch(() => caches.match(event.request))
        );
    }
});

// Badala ya kulazimisha page reload, toa notification ya update
self.addEventListener('controllerchange', () => {
    self.clients.matchAll().then((clients) => {
        clients.forEach(client => client.postMessage({ action: "refresh" }));
    });
});
