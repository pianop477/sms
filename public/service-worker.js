const APP_VERSION = '2026.2';
const CACHE_NAME = `shuleapp-cache-${APP_VERSION}`;

const STATIC_ASSETS = [
    '/',
    '/offline.html',
    '/manifest.json',
    '/assets/css/styles.css',
    '/assets/js/scripts.js',
    '/icons/new_icon1.png',
    '/icons/new_icon2.png',
    '/icons/new_icon3.png'
].map(url => `${url}?v=${APP_VERSION}`);

// INSTALL
self.addEventListener('install', event => {
    self.skipWaiting();

    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => cache.addAll(STATIC_ASSETS))
    );
});

// ACTIVATE
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(keys => {
            return Promise.all(
                keys.map(key => {
                    if (key !== CACHE_NAME) {
                        return caches.delete(key);
                    }
                })
            );
        }).then(() => self.clients.claim())
    );
});

// FETCH
self.addEventListener('fetch', event => {

    const url = new URL(event.request.url);

    // ❗ IMPORTANT: NEVER CACHE API OR AUTH REQUESTS
    if (
        url.pathname.startsWith('/api/') ||
        url.pathname.includes('login') ||
        url.pathname.includes('logout') ||
        url.pathname.includes('verify') ||
        url.pathname.includes('otp') ||
        url.pathname.includes('session')
    ) {
        event.respondWith(fetch(event.request));
        return;
    }

    // ONLY HANDLE GET
    if (event.request.method !== 'GET') return;

    // STATIC FILES STRATEGY (STALE WHILE REVALIDATE)
    if (
        url.pathname.startsWith('/assets/') ||
        url.pathname.startsWith('/icons/') ||
        url.pathname.endsWith('.css') ||
        url.pathname.endsWith('.js') ||
        url.pathname.endsWith('.png') ||
        url.pathname.endsWith('.jpg') ||
        url.pathname.endsWith('.svg')
    ) {

        event.respondWith(
            caches.match(event.request).then(cached => {

                const fetchPromise = fetch(event.request).then(networkResponse => {

                    const responseClone = networkResponse.clone();

                    caches.open(CACHE_NAME).then(cache => {
                        cache.put(event.request, responseClone);
                    });

                    return networkResponse;

                }).catch(() => cached);

                return cached || fetchPromise;

            })
        );

        return;
    }

    // HTML PAGES → NETWORK FIRST
    event.respondWith(

        fetch(event.request)
            .then(response => response)
            .catch(() => caches.match('/offline.html?v=' + APP_VERSION))

    );

});
