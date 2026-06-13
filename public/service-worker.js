const APP_VERSION = '2026.06.14.02';
const CACHE_NAME = `shuleapp-cache-${APP_VERSION}`;
const TOKEN_DB_NAME = 'gatepass-tokens-db';
const TOKEN_STORE_NAME = 'tokens';

// ========== INDEXEDDB HELPERS ==========
function openTokenDB() {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open(TOKEN_DB_NAME, 1);
        request.onerror = () => reject(request.error);
        request.onsuccess = () => resolve(request.result);
        request.onupgradeneeded = (event) => {
            const db = event.target.result;
            if (!db.objectStoreNames.contains(TOKEN_STORE_NAME)) {
                const store = db.createObjectStore(TOKEN_STORE_NAME, { keyPath: 'token' });
                store.createIndex('expires_at', 'expires_at', { unique: false });
            }
        };
    });
}

async function saveTokensToIndexedDB(tokens) {
    const db = await openTokenDB();
    const tx = db.transaction(TOKEN_STORE_NAME, 'readwrite');
    const store = tx.objectStore(TOKEN_STORE_NAME);
    store.clear();
    for (const token of tokens) {
        store.put(token);
    }
    return new Promise((resolve, reject) => {
        tx.oncomplete = resolve;
        tx.onerror = () => reject(tx.error);
    });
}

async function getTokenFromIndexedDB(tokenCode) {
    const db = await openTokenDB();
    return new Promise((resolve, reject) => {
        const tx = db.transaction(TOKEN_STORE_NAME, 'readonly');
        const store = tx.objectStore(TOKEN_STORE_NAME);
        const request = store.get(tokenCode);
        request.onsuccess = () => {
            const token = request.result;
            if (token && new Date(token.expires_at) > new Date()) {
                resolve(token);
            } else {
                resolve(null);
            }
        };
        request.onerror = () => reject(request.error);
    });
}

async function fetchAndCacheTokens() {
    try {
        const response = await fetch('/api/offline/tokens');
        if (response.ok) {
            const data = await response.json();
            if (data.success && data.tokens) {
                await saveTokensToIndexedDB(data.tokens);
                console.log('[SW] Tokens cached offline:', data.tokens.length);
            }
        }
    } catch (e) {
        console.error('[SW] Failed to fetch tokens:', e);
    }
}

// ========== STATIC ASSETS ==========
const STATIC_ASSETS = [
    '/',
    '/offline.html',
    '/manifest.json',
    '/assets/css/bootstrap.min.css',
    '/assets/css/styles.css',
    '/assets/js/scripts.js',
    '/icons/new_icon1.png',
    '/icons/new_icon2.png',
    '/icons/new_icon3.png'
];

// ========== INSTALL ==========
self.addEventListener('install', event => {
    console.log('[SW] Installing version:', APP_VERSION);
    self.skipWaiting();
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => cache.addAll(STATIC_ASSETS))
            .then(() => fetchAndCacheTokens())
    );
});

// ========== ACTIVATE ==========
self.addEventListener('activate', event => {
    console.log('[SW] Activating version:', APP_VERSION);
    event.waitUntil(
        caches.keys().then(keys => {
            return Promise.all(
                keys.map(key => {
                    if (key !== CACHE_NAME) {
                        console.log('[SW] Deleting old cache:', key);
                        return caches.delete(key);
                    }
                })
            );
        }).then(() => self.clients.claim())
    );
});

// ========== MESSAGE HANDLER ==========
self.addEventListener('message', event => {
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
    if (event.data && event.data.type === 'SYNC_TOKENS') {
        event.waitUntil(fetchAndCacheTokens());
    }
});

// ========== FETCH HANDLER ==========
self.addEventListener('fetch', event => {
    const url = new URL(event.request.url);

    // API requests - don't cache other APIs
    if (url.pathname.startsWith('/api/') && url.pathname !== '/api/offline/tokens') {
        event.respondWith(fetch(event.request));
        return;
    }

    // Token verification endpoint
    if (url.pathname === '/tokens/verify' && event.request.method === 'POST') {
        event.respondWith(handleTokenVerification(event.request));
        return;
    }

    // Static assets
    if (url.pathname.startsWith('/assets/') || url.pathname.startsWith('/icons/') ||
        url.pathname.match(/\.(css|js|png|jpg|jpeg|svg|ico)$/i)) {
        event.respondWith(
            caches.match(event.request).then(cached => {
                const fetchPromise = fetch(event.request).then(res => {
                    if (res && res.status === 200) {
                        caches.open(CACHE_NAME).then(cache => cache.put(event.request, res.clone()));
                    }
                    return res;
                }).catch(() => cached);
                return cached || fetchPromise;
            })
        );
        return;
    }

    // HTML pages - network first
    event.respondWith(
        fetch(event.request).then(res => {
            if (res && res.status === 200) {
                caches.open(CACHE_NAME).then(cache => cache.put(event.request, res.clone()));
            }
            return res;
        }).catch(() => caches.match('/offline.html'))
    );
});

// ========== TOKEN VERIFICATION (OFFLINE CAPABLE) ==========
async function handleTokenVerification(request) {
    try {
        // Try network first
        const response = await fetch(request.clone());
        if (response.ok) {
            const data = await response.clone().json();
            if (data.success && data.data) {
                // Cache the token for future offline use
                await saveSingleTokenToIndexedDB(data.data);
            }
            return response;
        }
        throw new Error('Network failed');
    } catch (error) {
        // Offline mode: check IndexedDB
        const requestData = await request.clone().json();
        const tokenCode = requestData.token;
        const cachedToken = await getTokenFromIndexedDB(tokenCode);

        if (cachedToken) {
            return new Response(JSON.stringify({
                success: true,
                message: 'Token valid (Offline Mode)',
                data: cachedToken,
                offline: true
            }), {
                headers: { 'Content-Type': 'application/json' }
            });
        }

        return new Response(JSON.stringify({
            success: false,
            message: 'Token invalid or expired (Offline)'
        }), {
            status: 400,
            headers: { 'Content-Type': 'application/json' }
        });
    }
}

async function saveSingleTokenToIndexedDB(tokenData) {
    const db = await openTokenDB();
    const tx = db.transaction(TOKEN_STORE_NAME, 'readwrite');
    const store = tx.objectStore(TOKEN_STORE_NAME);
    store.put({
        token: tokenData.token.token,
        formatted_token: tokenData.token.formatted_token || tokenData.token.token,
        student: tokenData.student,
        installment: tokenData.installment,
        expires_at: tokenData.token.expires_at,
        academic_year: tokenData.token.academic_year
    });
    return new Promise(resolve => { tx.oncomplete = resolve; });
}

// ========== BACKGROUND SYNC ==========
self.addEventListener('sync', event => {
    if (event.tag === 'sync-tokens') {
        console.log('[SW] Background sync: syncing tokens');
        event.waitUntil(fetchAndCacheTokens());
    }
});
