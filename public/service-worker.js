// SERVICE WORKER FIX
const CACHE_NAME = 'ShuleApp-cache-v3.2'; // Sasisha toleo
const ASSETS_TO_CACHE = [
    '/',
    '/index.php',
    '/manifest.json?v=3.2',
    '/assets/css/styles.css',
    '/assets/js/scripts.js',
    '/icons/icon.png',
    '/icons/icon_2.png',
    '/icons/icon_3.png',
    '/offline.html'
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
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
                        return caches.delete(cache);
                    }
                })
            );
        })
    );
    self.clients.claim();
});

self.addEventListener('fetch', (event) => {
    const requestUrl = new URL(event.request.url);

    // Bypass caching for authentication, session check, and CSRF requests
    if (['/login', '/logout', '/sanctum/csrf-cookie', '/session/check', '/session/extend'].includes(requestUrl.pathname)) {
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
            });
        }).catch(() => caches.match('/offline.html'))
    );
});
