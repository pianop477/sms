const CACHE_NAME = 'ShuleApp-cache-v3.0'; // Change version when updating
const ASSETS_TO_CACHE = [
    '/',
    '/index.php',
    '/manifest.json?v=3.0', // Ensure manifest.json is always updated
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
    self.skipWaiting(); // Force immediate activation
});

// Activate event: Clear outdated caches and force refresh
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

    // Force update on all active clients
    self.clients.claim().then(() => {
        self.clients.matchAll({ type: 'window' }).then(clients => {
            clients.forEach(client => client.navigate(client.url));
        });
    });

    // Fetch new manifest.json to update theme color immediately
    event.waitUntil(
        fetch('/manifest.json?v=3.0').then(response => response.json()).then(updatedManifest => {
            console.log('Manifest updated:', updatedManifest);
        }).catch(err => {
            console.error('Manifest update failed:', err);
        })
    );
});

// Fetch event: Smart caching strategy
self.addEventListener('fetch', (event) => {
    const requestUrl = new URL(event.request.url);

    // Avoid caching session-related requests
    if (requestUrl.pathname === '/check-session') {
        event.respondWith(fetch(event.request));
        return;
    }

    // Use cache but update assets in the background
    event.respondWith(
        caches.match(event.request).then((cachedResponse) => {
            const fetchPromise = fetch(event.request).then((networkResponse) => {
                if (networkResponse && networkResponse.status === 200) {
                    caches.open(CACHE_NAME).then((cache) => {
                        cache.put(event.request, networkResponse.clone()); // Update cache
                    });
                }
                return networkResponse;
            }).catch(() => cachedResponse || caches.match('/offline.html'));

            return cachedResponse || fetchPromise; // Return cache first, then update
        })
    );
});
