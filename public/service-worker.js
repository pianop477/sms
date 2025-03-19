const CACHE_NAME = 'ShuleApp-cache-v1.5'; // Cache versioning
const ASSETS_TO_CACHE = [
  '/',
  '/index.php',
  '/assets/css/styles.css',
  '/assets/js/scripts.js',
  '/icons/icon.png',
  '/icons/icon_2.png',
  '/icons/icon_3.png',
  '/offline.html' // Fallback page for offline use
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
  self.skipWaiting(); // Immediately activate the new service worker
});

// Activate event: Clear outdated caches
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
  self.clients.claim(); // Take control of all clients immediately
});

// Fetch event: Handle both network requests and offline scenarios
self.addEventListener('fetch', (event) => {
  // For navigation requests (page load)
  if (event.request.mode === 'navigate') {
    event.respondWith(
      fetch(event.request)
        .then((response) => response)
        .catch(() => {
          // Notify the user when offline
          self.registration.showNotification('You are offline', {
            body: 'We will bring you back online shortly...',
            icon: '/icons/icon_2.png',
            badge: '/icons/icon.png'
          });
          return caches.match('/offline.html'); // Serve offline fallback page
        })
    );
  } else {
    // For other assets (images, CSS, JS, etc.), try fetching from cache first
    event.respondWith(
      caches.match(event.request).then((cachedResponse) => {
        if (cachedResponse) {
          console.log(`Cache hit: ${event.request.url}`);
          return cachedResponse; // Return cached version if exists
        }
        return fetch(event.request).then((networkResponse) => {
          // Clone the response before consuming it
          const clonedResponse = networkResponse.clone();
          // Cache non-cached responses for future use
          if (networkResponse && networkResponse.status === 200) {
            caches.open(CACHE_NAME).then((cache) => {
              cache.put(event.request, clonedResponse); // Use the cloned response
            });
          }
          return networkResponse;
        }).catch((err) => {
          console.error(`Error fetching resource: ${event.request.url}`, err);
          throw err; // Propagate the error if unable to fetch
        });
      })
    );
  }
});
