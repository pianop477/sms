self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open('ShuleApp-cache').then((cache) => {
            return cache.addAll([
                '/',
                '/assets/css/style.css',
                '/assets/js/scripts.js',
                '/icons/icon.png',
                '/icons/icon_2.png'
            ]);
        })
    );
});

self.addEventListener('fetch', (event) => {
    event.respondWith(
        caches.match(event.request).then((response) => {
            return response || fetch(event.request);
        })
    );
});
