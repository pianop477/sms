// public/service-worker.js
const APP_VERSION = '2026.07.02.02';
const CACHE_NAME = `shuleapp-cache-${APP_VERSION}`;
const TOKEN_DB_NAME = 'gatepass-tokens-db';
const TOKEN_STORE_NAME = 'tokens';

// ========== INDEXEDDB HELPERS ==========
function openTokenDB() {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open(TOKEN_DB_NAME, 2);
        request.onerror = () => reject(request.error);
        request.onsuccess = () => resolve(request.result);
        request.onupgradeneeded = (event) => {
            const db = event.target.result;
            if (!db.objectStoreNames.contains(TOKEN_STORE_NAME)) {
                const store = db.createObjectStore(TOKEN_STORE_NAME, { keyPath: 'token' });
                store.createIndex('expires_at', 'expires_at', { unique: false });
                store.createIndex('student_id', 'student.id', { unique: false });
            }
        };
    });
}

async function saveTokensToIndexedDB(tokens) {
    if (!tokens || !tokens.length) return;
    const db = await openTokenDB();
    const tx = db.transaction(TOKEN_STORE_NAME, 'readwrite');
    const store = tx.objectStore(TOKEN_STORE_NAME);
    store.clear();
    for (const token of tokens) {
        if (token.token) {
            store.put({
                token: token.token,
                formatted_token: token.formatted_token || token.token,
                student: token.student || {},
                installment: token.installment || {},
                expires_at: token.expires_at || token.expires_date,
                academic_year: token.academic_year || new Date().getFullYear().toString(),
                is_valid: token.is_valid !== undefined ? token.is_valid : true,
                school_id: token.student?.school_id || null
            });
        }
    }
    return new Promise((resolve, reject) => {
        tx.oncomplete = () => {
            // Store last sync time in cache
            const lastSyncData = JSON.stringify({ lastSync: new Date().toISOString() });
            caches.open(CACHE_NAME).then(cache => {
                cache.put('/offline/last-sync', new Response(lastSyncData, {
                    headers: { 'Content-Type': 'application/json' }
                }));
            }).catch(() => {});
            resolve();
        };
        tx.onerror = () => reject(tx.error);
    });
}

async function getLastSyncTime() {
    try {
        const cache = await caches.open(CACHE_NAME);
        const response = await cache.match('/offline/last-sync');
        if (response) {
            const data = await response.json();
            return data.lastSync || null;
        }
        return null;
    } catch (e) {
        return null;
    }
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

async function getAllTokensFromIndexedDB() {
    const db = await openTokenDB();
    return new Promise((resolve, reject) => {
        const tx = db.transaction(TOKEN_STORE_NAME, 'readonly');
        const store = tx.objectStore(TOKEN_STORE_NAME);
        const request = store.getAll();
        request.onsuccess = () => {
            const tokens = request.result || [];
            const valid = tokens.filter(t => new Date(t.expires_at) > new Date());
            resolve(valid);
        };
        request.onerror = () => reject(request.error);
    });
}

async function fetchAndCacheTokens() {
    try {
        const response = await fetch('/offline/tokens');
        if (response.ok) {
            const responseForCache = response.clone();
            const data = await responseForCache.json();
            if (data.success && data.tokens && data.tokens.length) {
                await saveTokensToIndexedDB(data.tokens);
                console.log('[SW] Tokens cached offline:', data.tokens.length);

                const clients = await self.clients.matchAll();
                clients.forEach(client => {
                    client.postMessage({
                        type: 'SYNC_STATUS',
                        success: true,
                        count: data.tokens.length,
                        lastSync: new Date().toISOString()
                    });
                });
                return true;
            }
            return false;
        }
        console.error('[SW] Failed to fetch tokens:', response.status);
        return false;
    } catch (e) {
        console.error('[SW] Fetch tokens error:', e);
        return false;
    }
}

// ========== STATIC ASSETS ==========
const STATIC_ASSETS = [
    '/',
    '/offline.html',
    '/tokens/verify',
    '/manifest.json',
    '/assets/css/bootstrap.min.css',
    '/assets/css/styles.css',
    '/assets/js/scripts.js',
    '/icons/new_icon1.png',
    '/icons/new_icon2.png',
    '/icons/new_icon3.png',
    '/icons/wifi-slash.svg'
];

// ========== INSTALL ==========
self.addEventListener('install', event => {
    console.log('[SW] Installing version:', APP_VERSION);
    self.skipWaiting();
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => {
            return cache.addAll(STATIC_ASSETS);
        }).then(() => {
            return fetchAndCacheTokens();
        }).catch(err => {
            console.warn('[SW] Install warning:', err);
        })
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
        }).then(() => {
            return self.clients.claim();
        })
    );
});

// ========== MESSAGE HANDLER ==========
self.addEventListener('message', event => {
    const data = event.data;
    if (!data) return;

    if (data.type === 'SKIP_WAITING') {
        self.skipWaiting();
        return;
    }

    if (data.type === 'SYNC_TOKENS') {
        event.waitUntil(
            fetchAndCacheTokens().then(success => {
                if (event.ports && event.ports.length) {
                    event.ports[0].postMessage({
                        success: success,
                        message: success ? 'Tokens synced successfully' : 'Failed to sync tokens'
                    });
                }
            })
        );
        return;
    }

    if (data.type === 'GET_OFFLINE_TOKEN_COUNT') {
        event.waitUntil(
            Promise.all([getAllTokensFromIndexedDB(), getLastSyncTime()])
                .then(([tokens, lastSync]) => {
                    if (event.ports && event.ports.length) {
                        event.ports[0].postMessage({
                            count: tokens.length,
                            lastSync: lastSync
                        });
                    }
                })
                .catch(() => {
                    if (event.ports && event.ports.length) {
                        event.ports[0].postMessage({ count: 0, lastSync: null });
                    }
                })
        );
        return;
    }
});

// ========== FETCH HANDLER ==========
self.addEventListener('fetch', event => {
    const url = new URL(event.request.url);

    // Skip non-GET requests
    if (event.request.method !== 'GET') {
        return;
    }

    // 1. OFFLINE PAGE - cache first
    if (url.pathname === '/offline.html' || url.pathname === '/offline') {
        event.respondWith(
            caches.match(event.request).then(cached => {
                if (cached) return cached;
                return fetch(event.request).catch(() => {
                    return new Response('Offline - please check your connection', {
                        status: 503,
                        headers: { 'Content-Type': 'text/html' }
                    });
                });
            })
        );
        return;
    }

    // 2. TOKEN VERIFICATION PAGE - cache first (IMPORTANT for offline access)
    if (url.pathname === '/tokens/verify' && event.request.method === 'GET') {
        event.respondWith(
            caches.match(event.request).then(cached => {
                if (cached) {
                    console.log('[SW] Serving /tokens/verify from cache');
                    return cached;
                }
                return fetch(event.request).then(response => {
                    if (response && response.status === 200) {
                        const responseForCache = response.clone();
                        caches.open(CACHE_NAME).then(cache => {
                            cache.put(event.request, responseForCache);
                        });
                    }
                    return response;
                }).catch(() => {
                    // Fallback to offline.html if page not cached
                    return caches.match('/offline.html');
                });
            })
        );
        return;
    }

    // 3. TOKEN SYNC ENDPOINT - network first, cache for offline
    if (url.pathname === '/offline/tokens') {
        event.respondWith(
            fetch(event.request).then(response => {
                if (response.ok) {
                    const responseForCache = response.clone();
                    caches.open(CACHE_NAME).then(cache => {
                        cache.put(event.request, responseForCache);
                    });
                }
                return response;
            }).catch(async () => {
                const cachedResponse = await caches.match(event.request);
                if (cachedResponse) {
                    return cachedResponse;
                }
                return new Response(JSON.stringify({
                    success: false,
                    message: 'No offline tokens available',
                    tokens: []
                }), {
                    headers: { 'Content-Type': 'application/json' }
                });
            })
        );
        return;
    }

    // 4. TOKEN VERIFICATION POST - intercept for offline support
    if (url.pathname === '/tokens/verify' && event.request.method === 'POST') {
        event.respondWith(handleTokenVerification(event.request));
        return;
    }

    // 5. STATIC ASSETS - cache first
    if (url.pathname.startsWith('/assets/') || url.pathname.startsWith('/icons/') ||
        url.pathname.match(/\.(css|js|png|jpg|jpeg|svg|ico|webp)$/i)) {
        event.respondWith(
            caches.match(event.request).then(cached => {
                if (cached) return cached;
                return fetch(event.request).then(res => {
                    if (res && res.status === 200) {
                        const resForCache = res.clone();
                        caches.open(CACHE_NAME).then(cache => {
                            cache.put(event.request, resForCache);
                        });
                    }
                    return res;
                });
            })
        );
        return;
    }

    // 6. HTML PAGES - network first, fallback to offline.html
    event.respondWith(
        fetch(event.request).then(res => {
            if (res && res.status === 200) {
                const resForCache = res.clone();
                caches.open(CACHE_NAME).then(cache => {
                    cache.put(event.request, resForCache);
                });
            }
            return res;
        }).catch(() => {
            // For any page, try to serve cached version first, then offline.html
            return caches.match(event.request).then(cached => {
                if (cached) return cached;
                return caches.match('/offline.html');
            });
        })
    );
});

// ========== TOKEN VERIFICATION HANDLER ==========
async function handleTokenVerification(request) {
    try {
        // Clone request before reading body
        const requestClone = request.clone();

        // Try network first
        const response = await fetch(requestClone);
        if (response.ok) {
            // Clone response before reading body
            const responseClone = response.clone();
            const data = await responseClone.json();
            if (data.success && data.data) {
                const tokenData = {
                    token: data.data.token.token,
                    formatted_token: data.data.token.token,
                    student: data.data.student,
                    installment: data.data.installment,
                    expires_at: data.data.token.expires_at,
                    academic_year: new Date().getFullYear().toString(),
                    is_valid: true
                };
                // Save to IndexedDB in background
                saveTokensToIndexedDB([tokenData]).catch(() => {});
            }
            return response;
        }
        throw new Error('Network request failed with status: ' + response.status);
    } catch (error) {
        // Network failed - try offline verification
        try {
            // Clone request before reading body
            const requestClone = request.clone();
            const requestData = await requestClone.json();
            const tokenCode = requestData.token;

            if (!tokenCode) {
                throw new Error('No token provided');
            }

            const cachedToken = await getTokenFromIndexedDB(tokenCode);

            if (cachedToken && cachedToken.student) {
                const responseData = {
                    success: true,
                    message: 'Token valid (Offline Mode)',
                    offline: true,
                    data: {
                        student: cachedToken.student,
                        installment: cachedToken.installment || { name: 'School Fees', order: 1 },
                        token: {
                            token: cachedToken.token,
                            expires_at: cachedToken.expires_at
                        }
                    }
                };
                return new Response(JSON.stringify(responseData), {
                    headers: { 'Content-Type': 'application/json' }
                });
            } else {
                return new Response(JSON.stringify({
                    success: false,
                    message: 'Token invalid or expired (Offline)'
                }), {
                    status: 400,
                    headers: { 'Content-Type': 'application/json' }
                });
            }
        } catch (offlineError) {
            console.error('[SW] Offline verification error:', offlineError);
            return new Response(JSON.stringify({
                success: false,
                message: 'Offline verification failed. Please connect to the internet and try again.'
            }), {
                status: 500,
                headers: { 'Content-Type': 'application/json' }
            });
        }
    }
}

// ========== BACKGROUND SYNC ==========
self.addEventListener('sync', event => {
    if (event.tag === 'sync-tokens') {
        console.log('[SW] Background sync: syncing tokens');
        event.waitUntil(fetchAndCacheTokens());
    }
});
