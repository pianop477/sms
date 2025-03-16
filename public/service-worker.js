const CACHE_NAME = 'ShuleApp-cache-v1.3.4'; // Update cache version
const ASSETS_TO_CACHE = [
    '/',
    '/index.php',
    '/assets/css/styles.css',
    '/assets/js/scripts.js',
    '/icons/icon.png',
    '/icons/icon_2.png',
    '/offline.html' // Offline fallback page
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            console.log('Caching static assets...');
            return cache.addAll(ASSETS_TO_CACHE);
        })
    );
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cache) => {
                    if (cache !== CACHE_NAME) {
                        console.log('Deleting old cache:', cache);
                        return caches.delete(cache);
                    }
                })
            );
        })
    );
    self.clients.claim();
});

self.addEventListener('fetch', (event) => {
    event.respondWith(
        fetch(event.request).catch(() => {
            return caches.match(event.request).then((cachedResponse) => {
                return cachedResponse || caches.match('/offline.html'); // Tumia offline.html kama fallback
            });
        })
    );
});
