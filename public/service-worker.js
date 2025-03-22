const CACHE_NAME = 'ShuleApp-cache-v3.1'; // Sasisha toleo
const ASSETS_TO_CACHE = [
    '/',
    '/index.php',
    '/manifest.json?v=3.1', // Sasisha ili kuhakikisha inaboreshwa
    '/assets/css/styles.css',
    '/assets/js/scripts.js',
    '/icons/icon.png',
    '/icons/icon_2.png',
    '/icons/icon_3.png',
    '/offline.html'
];

// Install event: Cache static assets
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            console.log('Caching essential assets...');
            return cache.addAll(ASSETS_TO_CACHE);
        }).catch((err) => {
            console.error('Error caching assets during install:', err);
        })
    );
    self.skipWaiting();
});

// Activate event: Clear outdated caches and notify clients
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cache) => {
                    if (cache !== CACHE_NAME) {
                        console.log(`Deleting old cache: ${cache}`);
                        return caches.delete(cache);
                    }
                })
            );
        }).catch((err) => {
            console.error('Error during cache cleanup:', err);
        })
    );

    // Notify clients about the update
    self.clients.claim().then(() => {
        self.clients.matchAll({ type: 'window' }).then(clients => {
            clients.forEach(client => client.postMessage({ type: 'SW_UPDATED' }));
        });
    });
});

// Fetch event: Smart caching strategy
self.addEventListener('fetch', (event) => {
    const requestUrl = new URL(event.request.url);

    if (requestUrl.pathname === '/check-session') {
        event.respondWith(fetch(event.request));
        return;
    }

    event.respondWith(
        caches.match(event.request).then((cachedResponse) => {
            return cachedResponse || fetch(event.request).then((networkResponse) => {
                return caches.open(CACHE_NAME).then((cache) => {
                    cache.put(event.request, networkResponse.clone());
                    return networkResponse;
                });
            }).catch(() => caches.match('/offline.html'));
        })
    );
});
