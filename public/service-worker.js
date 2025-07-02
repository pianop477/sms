const CACHE_NAME = 'ShuleApp-cache-v3.3.1'; // Updated version
const OFFLINE_URL = '/offline.html'; // Path to offline page

// Static assets to cache (with versioning)
const STATIC_ASSETS = [
  '/',
  '/manifest.json',
  '/assets/css/styles.css',
  '/assets/js/scripts.js',
  '/icons/icon.png',
  '/icons/icon_2.png',
  '/icons/icon_3.png',
  '/icons/icon_4.png',
  OFFLINE_URL // IMPORTANT: Add offline.html to cache
].map(url => `${url}?v=3.3.1`); // Auto-versioning

// ===== INSTALL EVENT =====
self.addEventListener('install', (event) => {
  console.log('[Service Worker] Installing...');

  event.waitUntil(
    caches.open(CACHE_NAME)
      .then((cache) => {
        console.log('[Service Worker] Caching static assets');
        return cache.addAll(STATIC_ASSETS);
      })
      .then(() => {
        console.log('[Service Worker] Skip waiting on install');
        return self.skipWaiting();
      })
  );
});

// ===== ACTIVATE EVENT =====
self.addEventListener('activate', (event) => {
  console.log('[Service Worker] Activating...');

  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames.map((cache) => {
          if (cache !== CACHE_NAME) {
            console.log('[Service Worker] Deleting old cache:', cache);
            return caches.delete(cache);
          }
        })
      );
    }).then(() => {
      console.log('[Service Worker] Claiming clients');
      return self.clients.claim();
    })
  );
});

// ===== FETCH EVENT =====
self.addEventListener('fetch', (event) => {
  // Skip non-GET requests
  if (event.request.method !== 'GET') return;

  const requestUrl = new URL(event.request.url);

  // Check if request is for a static asset
  const isStaticAsset = STATIC_ASSETS.some(asset => {
    const assetPath = new URL(asset, self.location.origin).pathname;
    return requestUrl.pathname === assetPath.split('?')[0];
  });

  // ===== STRATEGY =====
  if (isStaticAsset) {
    // Cache-First for static assets
    event.respondWith(
      caches.match(event.request)
        .then((cachedResponse) => {
          // Return cached version if found
          if (cachedResponse) {
            console.log(`[SW] Serving from cache: ${requestUrl.pathname}`);
            return cachedResponse;
          }

          // Otherwise fetch, cache, and return
          return fetch(event.request)
            .then((networkResponse) => {
              const responseClone = networkResponse.clone();
              caches.open(CACHE_NAME)
                .then(cache => cache.put(event.request, responseClone));
              return networkResponse;
            })
            .catch(() => {
              // Special handling for root URL
              if (requestUrl.pathname === '/') {
                return caches.match(OFFLINE_URL);
              }
              return new Response('Offline (static asset not cached)');
            });
        })
    );
  } else {
    // Network-First for other requests
    event.respondWith(
      fetch(event.request)
        .then((networkResponse) => {
          // If successful, return network response
          return networkResponse;
        })
        .catch(async () => {
          console.log(`[SW] Network failed, serving offline page for: ${requestUrl.pathname}`);

          // Try to return offline.html
          const cachedOffline = await caches.match(OFFLINE_URL);
          if (cachedOffline) return cachedOffline;

          // Fallback if offline.html not cached
          return new Response(`
            <html>
              <body>
                <h1>You are offline</h1>
                <p>Try reloading when you're back online</p>
              </body>
            </html>
          `, { headers: { 'Content-Type': 'text/html' } });
        })
    );
  }
});
