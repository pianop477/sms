const APP_VERSION = '2026.04.21';
const CACHE_NAME = `shuleapp-cache-${APP_VERSION}`;
const TOKEN_CACHE_NAME = 'gatepass-tokens-v1';

/* =============================
   STATIC ASSETS
============================= */
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

/* =============================
   INSTALL
============================= */
self.addEventListener('install', event => {
    console.log('[SW] Installing new version:', APP_VERSION);
    self.skipWaiting();
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => {
            return cache.addAll(STATIC_ASSETS);
        })
    );
});

/* =============================
   ACTIVATE
============================= */
self.addEventListener('activate', event => {
    console.log('[SW] Activating new version:', APP_VERSION);
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
        }).then(() => {
            console.log('[SW] Claiming clients');
            return self.clients.claim();
        })
    );
});

/* =============================
   SKIP WAITING
============================= */
self.addEventListener('message', event => {
    if (event.data && event.data.type === 'SKIP_WAITING') {
        console.log('[SW] Skipping waiting');
        self.skipWaiting();
    }
});

/* =============================
   FETCH HANDLER
============================= */
self.addEventListener('fetch', event => {
    const url = new URL(event.request.url);

    /* API REQUESTS - NEVER CACHE */
    if (
        url.pathname.startsWith('/api/') ||
        url.pathname.includes('login') ||
        url.pathname.includes('logout') ||
        url.pathname.includes('otp') ||
        url.pathname.includes('session')
    ) {
        event.respondWith(fetch(event.request));
        return;
    }

    /* MANIFEST.JSON */
    if (url.pathname === '/manifest.json') {
        event.respondWith(
            fetch(event.request)
                .then(res => {
                    const clone = res.clone();
                    caches.open(CACHE_NAME).then(cache => {
                        cache.put(event.request, clone);
                    });
                    return res;
                })
                .catch(() => caches.match(event.request))
        );
        return;
    }

    /* STATIC ASSETS */
    if (
        url.pathname.startsWith('/assets/') ||
        url.pathname.startsWith('/icons/') ||
        url.pathname.match(/\.(css|js|png|jpg|jpeg|svg|ico)$/i)
    ) {
        event.respondWith(
            caches.match(event.request).then(cached => {
                const fetchPromise = fetch(event.request)
                    .then(res => {
                        if (res && res.status === 200) {
                            const clone = res.clone();
                            caches.open(CACHE_NAME).then(cache => {
                                cache.put(event.request, clone);
                            });
                        }
                        return res;
                    })
                    .catch(() => cached);
                return cached || fetchPromise;
            })
        );
        return;
    }

    /* TOKEN VERIFICATION */
    if (url.pathname === '/tokens/verify') {
        if (event.request.method === 'POST') {
            event.respondWith(handleTokenVerification(event.request));
            return;
        }
    }

    /* HTML PAGES - Network first with offline fallback */
    event.respondWith(
        fetch(event.request)
            .then(res => {
                if (res && res.status === 200) {
                    const clone = res.clone();
                    caches.open(CACHE_NAME).then(cache => {
                        cache.put(event.request, clone);
                    });
                }
                return res;
            })
            .catch(() => {
                return caches.match('/offline.html');
            })
    );
});

/* =============================
   TOKEN VERIFY LOGIC
============================= */
async function handleTokenVerification(request) {
    try {
        const response = await fetch(request.clone());
        if (response.ok) {
            const data = await response.clone().json();
            if (data.success) {
                await storeTokenInCache(data.data);
            }
            return response;
        }
        throw new Error('Network failed');
    } catch (error) {
        const requestData = await request.clone().json();
        const tokenCode = requestData.token;
        const cachedToken = await getTokenFromCache(tokenCode);

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
            message: 'Token invalid or expired'
        }), {
            status: 400,
            headers: { 'Content-Type': 'application/json' }
        });
    }
}

async function storeTokenInCache(tokenData) {
    const cache = await caches.open(TOKEN_CACHE_NAME);
    const key = `token_${tokenData.token.token}`;
    const payload = {
        token: tokenData.token,
        student: tokenData.student,
        installment: tokenData.installment,
        cached_at: new Date().toISOString()
    };
    await cache.put(key, new Response(JSON.stringify(payload)));
    await cleanOldTokens();
}

async function getTokenFromCache(code) {
    const cache = await caches.open(TOKEN_CACHE_NAME);
    const res = await cache.match(`token_${code}`);
    if (!res) return null;
    const data = await res.json();
    const expires = new Date(data.token.expires_at);
    if (expires > new Date()) {
        return data;
    }
    await cache.delete(`token_${code}`);
    return null;
}

async function cleanOldTokens() {
    const cache = await caches.open(TOKEN_CACHE_NAME);
    const keys = await cache.keys();
    const now = new Date();
    for (const req of keys) {
        const res = await cache.match(req);
        const data = await res.json();
        if (new Date(data.token.expires_at) < now) {
            await cache.delete(req);
        }
    }
}

/* =============================
   BACKGROUND SYNC
============================= */
self.addEventListener('sync', event => {
    if (event.tag === 'sync-tokens') {
        console.log('[SW] Background sync triggered');
        event.waitUntil(syncTokens());
    }
});

async function syncTokens() {
    try {
        const cache = await caches.open(TOKEN_CACHE_NAME);
        const keys = await cache.keys();

        for (const req of keys) {
            const res = await cache.match(req);
            const data = await res.json();

            const response = await fetch('/tokens/verify', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ token: data.token.token })
            });

            const result = await response.json();
            if (!result.success) {
                await cache.delete(req);
            }
        }
        console.log('[SW] Tokens synced successfully');
    } catch (e) {
        console.error('[SW] Sync failed:', e);
    }
}
