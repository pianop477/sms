const CACHE_NAME = 'ShuleApp-cache-v2'; // Badilisha version ikiwa kuna update mpya

const ASSETS_TO_CACHE = [
    '/',
    '/assets/css/style.css',
    '/assets/js/scripts.js',
    '/icons/icon.png',
    '/icons/icon_2.png'
];

// Install event - Cache assets na force update
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(ASSETS_TO_CACHE);
        })
    );
    self.skipWaiting(); // Force service worker mpya kuchukua control mara moja
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

// Fetch event - Jaribu kuleta data mpya kwanza, kama haipo tumia cache
self.addEventListener('fetch', (event) => {
    event.respondWith(
        fetch(event.request)
            .then((response) => {
                // Kama request ni GET, cache response mpya
                if (!response || response.status !== 200 || response.type !== 'basic') {
                    return response;
                }

                let responseClone = response.clone();
                caches.open(CACHE_NAME).then((cache) => {
                    cache.put(event.request, responseClone);
                });

                return response;
            })
            .catch(() => caches.match(event.request)) // Kama haipo kwenye mtandao, tumia cache
    );
});
