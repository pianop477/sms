const APP_VERSION = '2026.04.09';
const CACHE_NAME = `shuleapp-cache-${APP_VERSION}`;

// STATIC ASSETS (these are cached for offline)
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

// Token cache storage name (separate from main cache)
const TOKEN_CACHE_NAME = `gatepass-tokens-${APP_VERSION}`;

// Pages that should ALWAYS get fresh copy (no cache for navigation)
const NO_CACHE_NAVIGATION = [
    '/login',
    '/logout',
    '/register',
    '/password/reset'
];

// Critical pages that work offline but need fresh CSRF
const CRITICAL_PAGES = [
    '/',
    '/offline.html',
    '/tokens/verify',
    '/login/biometric',
    '/verify-report',
    '/contracts/verify'
];

// API routes that should NEVER be cached
const API_ROUTES_TO_SKIP = [
    '/api/',
    '/login',
    '/logout',
    '/verify',
    '/otp',
    '/session',
    '/tokens/verify/submit',
    '/biometric/send-otp',
    '/biometric/verify-otp',
    '/webauthn/login/options',
    '/webauthn/login/verify',
    '/webauthn/register/options',
    '/webauthn/register/verify',
    '/contract-gateway/api/',
    '/csrf-token'
];

// INSTALL
self.addEventListener('install', event => {
    self.skipWaiting();

    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => cache.addAll(STATIC_ASSETS))
            .catch(err => console.error('[SW] Install failed:', err))
    );
});

// ACTIVATE
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(keys => {
            return Promise.all(
                keys.map(key => {
                    if (key !== CACHE_NAME && key !== TOKEN_CACHE_NAME) {
                        console.log('[SW] Deleting old cache:', key);
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

    // SPECIAL HANDLING FOR LOGOUT REQUESTS
    if (url.pathname === '/logout' && event.request.method === 'POST') {
        event.respondWith(
            fetch(event.request).then(response => {
                // Clear all caches on successful logout
                caches.keys().then(keys => {
                    keys.forEach(key => {
                        if (key.includes('shuleapp-cache') || key.includes('gatepass-tokens')) {
                            caches.delete(key);
                            console.log('[SW] Cleared cache on logout:', key);
                        }
                    });
                });
                return response;
            }).catch(error => {
                console.error('[SW] Logout network error:', error);
                // Still try to clear caches and redirect
                caches.keys().then(keys => {
                    keys.forEach(key => {
                        if (key.includes('shuleapp-cache') || key.includes('gatepass-tokens')) {
                            caches.delete(key);
                        }
                    });
                });
                return new Response(null, {
                    status: 302,
                    headers: {
                        'Location': '/',
                        'Cache-Control': 'no-cache, no-store, must-revalidate'
                    }
                });
            })
        );
        return;
    }

    // Check if this is an API route that should NEVER be cached
    const isApiRoute = API_ROUTES_TO_SKIP.some(route => {
        if (route.includes('/api/')) {
            return url.pathname.startsWith(route);
        }
        return url.pathname.includes(route) || url.pathname === route;
    });

    // Handle API routes specially
    if (isApiRoute || url.pathname.startsWith('/api/')) {
        // Special handling for token verification POST requests
        if (url.pathname === '/tokens/verify' && event.request.method === 'POST') {
            event.respondWith(handleTokenVerification(event.request));
            return;
        }

        // For all other API routes - network only, no caching
        event.respondWith(
            fetch(event.request, {
                cache: 'no-store',
                headers: {
                    'Cache-Control': 'no-cache, no-store, must-revalidate'
                }
            }).catch(error => {
                console.log('[SW] API offline:', url.pathname);
                return new Response(JSON.stringify({
                    success: false,
                    message: 'Hakuna mtandao. Tafadhali wasiliana na ofisi ya shule.'
                }), {
                    status: 503,
                    headers: { 'Content-Type': 'application/json' }
                });
            })
        );
        return;
    }

    // ONLY HANDLE GET requests for non-API routes
    if (event.request.method !== 'GET') return;

    // Check if this page should never be cached for navigation
    const isNoCachePage = NO_CACHE_NAVIGATION.some(page => url.pathname === page);
    if (isNoCachePage && event.request.mode === 'navigate') {
        event.respondWith(
            fetch(event.request, {
                cache: 'no-store',
                headers: {
                    'Cache-Control': 'no-cache, no-store, must-revalidate'
                }
            })
        );
        return;
    }

    // Check if this is a critical page that must work offline
    const isCriticalPage = CRITICAL_PAGES.some(page => {
        if (page === '/') {
            return url.pathname === '/' || url.pathname === '';
        }
        return url.pathname === page;
    });

    // STATIC FILES STRATEGY (CACHE FIRST then NETWORK)
    if (
        url.pathname.startsWith('/assets/') ||
        url.pathname.startsWith('/icons/') ||
        url.pathname.endsWith('.css') ||
        url.pathname.endsWith('.js') ||
        url.pathname.endsWith('.png') ||
        url.pathname.endsWith('.jpg') ||
        url.pathname.endsWith('.svg') ||
        url.pathname.endsWith('.webp')
    ) {
        event.respondWith(
            caches.match(event.request).then(cached => {
                if (cached) {
                    // Update cache in background
                    fetch(event.request).then(networkResponse => {
                        if (networkResponse && networkResponse.status === 200) {
                            caches.open(CACHE_NAME).then(cache => {
                                cache.put(event.request, networkResponse.clone());
                            });
                        }
                    }).catch(() => {});
                    return cached;
                }

                return fetch(event.request).then(networkResponse => {
                    if (networkResponse && networkResponse.status === 200) {
                        caches.open(CACHE_NAME).then(cache => {
                            cache.put(event.request, networkResponse.clone());
                        });
                    }
                    return networkResponse;
                }).catch(() => {
                    if (event.request.mode === 'navigate') {
                        return caches.match('/offline.html?v=' + APP_VERSION);
                    }
                    return new Response('Resource not available offline', { status: 404 });
                });
            })
        );
        return;
    }

    // CRITICAL PAGES - NETWORK FIRST for fresh CSRF, fallback to cache
    if (isCriticalPage && event.request.mode === 'navigate') {
        event.respondWith(
            fetch(event.request, {
                cache: 'no-cache',
                headers: {
                    'Cache-Control': 'no-cache, must-revalidate'
                }
            })
            .then(response => {
                // Cache successful responses for offline use
                if (response && response.status === 200) {
                    const responseClone = response.clone();
                    caches.open(CACHE_NAME).then(cache => {
                        cache.put(event.request, responseClone);
                    });
                }
                return response;
            })
            .catch(async () => {
                // Only use cache if network fails
                const cached = await caches.match(event.request);
                if (cached) {
                    return cached;
                }
                return caches.match('/offline.html?v=' + APP_VERSION);
            })
        );
        return;
    }

    // HTML PAGES (Non-critical) - NETWORK FIRST, fallback to cache
    if (event.request.mode === 'navigate') {
        event.respondWith(
            fetch(event.request)
                .then(response => {
                    if (response && response.status === 200) {
                        const responseClone = response.clone();
                        caches.open(CACHE_NAME).then(cache => {
                            cache.put(event.request, responseClone);
                        });
                    }
                    return response;
                })
                .catch(() => {
                    return caches.match(event.request).then(cached => {
                        if (cached) return cached;
                        return caches.match('/offline.html?v=' + APP_VERSION);
                    });
                })
        );
        return;
    }

    // Default: Try cache first for other resources
    event.respondWith(
        caches.match(event.request).then(cached => {
            if (cached) return cached;
            return fetch(event.request);
        })
    );
});

// ========== OFFLINE TOKEN VERIFICATION ==========

async function handleTokenVerification(request) {
    try {
        // Try network first with timeout
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 10000);

        try {
            const response = await fetch(request.clone(), { signal: controller.signal });
            clearTimeout(timeoutId);

            if (response.ok) {
                const data = await response.clone().json();
                if (data.success && data.data) {
                    await storeTokenInCache(data.data);
                }
                return response;
            }
            throw new Error('Network response not ok');
        } catch (fetchError) {
            clearTimeout(timeoutId);
            throw fetchError;
        }
    } catch (error) {
        console.log('[SW] Offline mode - checking cached tokens');

        try {
            const requestData = await request.clone().json();
            const tokenCode = requestData.token;

            if (!tokenCode) {
                return new Response(JSON.stringify({
                    success: false,
                    message: 'Token code is required'
                }), {
                    status: 400,
                    headers: { 'Content-Type': 'application/json' }
                });
            }

            const cachedToken = await getTokenFromCache(tokenCode);

            if (cachedToken) {
                return new Response(JSON.stringify({
                    success: true,
                    message: 'Token sahihi (Mode Offline)',
                    data: cachedToken,
                    offline: true
                }), {
                    headers: { 'Content-Type': 'application/json' }
                });
            } else {
                return new Response(JSON.stringify({
                    success: false,
                    message: 'Token si sahihi au imeisha muda wake. Tafadhali wasiliana na ofisi ya shule au hakikisha mtandao umewashwa.'
                }), {
                    status: 400,
                    headers: { 'Content-Type': 'application/json' }
                });
            }
        } catch (parseError) {
            console.error('[SW] Failed to parse request:', parseError);
            return new Response(JSON.stringify({
                success: false,
                message: 'Invalid request format'
            }), {
                status: 400,
                headers: { 'Content-Type': 'application/json' }
            });
        }
    }
}

async function storeTokenInCache(tokenData) {
    try {
        const cache = await caches.open(TOKEN_CACHE_NAME);
        const tokenKey = `token_${tokenData.token.token}`;
        const cacheData = {
            token: tokenData.token,
            student: tokenData.student,
            installment: tokenData.installment,
            cached_at: new Date().toISOString()
        };

        await cache.put(tokenKey, new Response(JSON.stringify(cacheData)));
        console.log('[SW] Token stored in cache:', tokenData.token.token);
        await cleanOldTokens();
    } catch (error) {
        console.error('[SW] Failed to store token:', error);
    }
}

async function getTokenFromCache(tokenCode) {
    try {
        const cache = await caches.open(TOKEN_CACHE_NAME);
        const tokenKey = `token_${tokenCode}`;
        const response = await cache.match(tokenKey);

        if (response) {
            const data = await response.json();
            const expiresAt = new Date(data.token.expires_at);
            const now = new Date();

            if (expiresAt > now) {
                return {
                    token: data.token,
                    student: data.student,
                    installment: data.installment
                };
            } else {
                await cache.delete(tokenKey);
                console.log('[SW] Removed expired token from cache:', tokenCode);
                return null;
            }
        }
        return null;
    } catch (error) {
        console.error('[SW] Failed to get token:', error);
        return null;
    }
}

async function cleanOldTokens() {
    try {
        const cache = await caches.open(TOKEN_CACHE_NAME);
        const requests = await cache.keys();
        const now = new Date();
        let cleanedCount = 0;

        for (const request of requests) {
            const response = await cache.match(request);
            if (response) {
                const data = await response.json();
                const expiresAt = new Date(data.token.expires_at);
                if (expiresAt < now) {
                    await cache.delete(request);
                    cleanedCount++;
                }
            }
        }

        if (cleanedCount > 0) {
            console.log(`[SW] Cleaned ${cleanedCount} expired tokens`);
        }
    } catch (error) {
        console.error('[SW] Failed to clean tokens:', error);
    }
}

// Background sync for tokens when back online
self.addEventListener('sync', (event) => {
    if (event.tag === 'sync-tokens') {
        event.waitUntil(syncTokens());
    }
});

async function syncTokens() {
    console.log('[SW] Syncing tokens with server...');
}
