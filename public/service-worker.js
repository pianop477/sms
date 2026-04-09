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
const TOKEN_CACHE_NAME = 'gatepass-tokens-v1';

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
                    if (key !== CACHE_NAME && key !== TOKEN_CACHE_NAME) {
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

    // 🔥 CRITICAL: NEVER CACHE TOKEN VERIFICATION API
    if (
        url.pathname.startsWith('/api/') ||
        url.pathname.includes('login') ||
        url.pathname.includes('logout') ||
        url.pathname.includes('verify') ||
        url.pathname.includes('otp') ||
        url.pathname.includes('session') ||
        url.pathname === '/tokens/verify'
    ) {
        // Handle token verification specially for offline
        if (url.pathname === '/tokens/verify' && event.request.method === 'POST') {
            event.respondWith(handleTokenVerification(event.request));
            return;
        }

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

// ========== OFFLINE TOKEN VERIFICATION ==========

// Handle token verification (online/offline)
async function handleTokenVerification(request) {
    try {
        // Try network first
        const response = await fetch(request.clone());

        // If online and successful, update cache
        if (response.ok) {
            const data = await response.clone().json();
            if (data.success) {
                await storeTokenInCache(data.data);
            }
            return response;
        }
        throw new Error('Network failed');
    } catch (error) {
        // Offline mode - check cache
        console.log('[SW] Offline mode - checking cached tokens');
        const requestData = await request.clone().json();
        const tokenCode = requestData.token;

        // Check token cache
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
                message: 'Token si sahihi. Tafadhali wasiliana na ofisi ya shule au hakikisha mtandao umewashwa.'
            }), {
                status: 400,
                headers: { 'Content-Type': 'application/json' }
            });
        }
    }
}

// Store token in cache
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

        // Clean old tokens
        await cleanOldTokens();
    } catch (error) {
        console.error('[SW] Failed to store token:', error);
    }
}

// Get token from cache
async function getTokenFromCache(tokenCode) {
    try {
        const cache = await caches.open(TOKEN_CACHE_NAME);
        const tokenKey = `token_${tokenCode}`;
        const response = await cache.match(tokenKey);

        if (response) {
            const data = await response.json();
            // Check if token is still valid
            const expiresAt = new Date(data.token.expires_at);
            if (expiresAt > new Date()) {
                return {
                    token: data.token,
                    student: data.student,
                    installment: data.installment
                };
            } else {
                // Remove expired token
                await cache.delete(tokenKey);
                return null;
            }
        }
        return null;
    } catch (error) {
        console.error('[SW] Failed to get token:', error);
        return null;
    }
}

// Clean old/expired tokens from cache
async function cleanOldTokens() {
    try {
        const cache = await caches.open(TOKEN_CACHE_NAME);
        const requests = await cache.keys();
        const now = new Date();

        for (const request of requests) {
            const response = await cache.match(request);
            if (response) {
                const data = await response.json();
                const expiresAt = new Date(data.token.expires_at);
                if (expiresAt < now) {
                    await cache.delete(request);
                    console.log('[SW] Removed expired token:', request.url);
                }
            }
        }
    } catch (error) {
        console.error('[SW] Failed to clean tokens:', error);
    }
}

// Sync tokens when back online
self.addEventListener('sync', (event) => {
    if (event.tag === 'sync-tokens') {
        event.waitUntil(syncTokens());
    }
});

async function syncTokens() {
    console.log('[SW] Syncing tokens with server...');
    // Implement if needed
}
