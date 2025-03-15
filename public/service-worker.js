const CACHE_NAME = 'ShuleApp-dynamic-cache-v1';

// Usihifadhi chochote kwa muda mrefu, kila kitu kitachukuliwa moja kwa moja kutoka server
self.addEventListener('install', (event) => {
    console.log('Service Worker installing...');
    self.skipWaiting(); // Force update immediately
});

self.addEventListener('activate', (event) => {
    console.log('Service Worker activated');
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
    self.clients.claim(); // Ensure all tabs use new service worker immediately
});

// Fetch kila kitu moja kwa moja kutoka server
self.addEventListener('fetch', (event) => {
    event.respondWith(
        fetch(event.request).catch(() => caches.match(event.request))
    );
});

// Tuma ujumbe kwa watumiaji wa standalone app kuhusu update mpya
self.addEventListener('message', (event) => {
    if (event.data === 'checkForUpdate') {
        self.skipWaiting();
        console.log('Forcing service worker update...');
    }
});
