const APP_VERSION = '2026.04.10';
const CACHE_NAME = `shuleapp-cache-${APP_VERSION}`;
const TOKEN_CACHE_NAME = 'gatepass-tokens-v1';

/* =============================
   STATIC ASSETS
============================= */
const STATIC_ASSETS = [
    '/',
    '/offline.html',
    '/manifest.json',
    '/tokens/verify',
    '/assets/css/styles.css',
    '/assets/js/scripts.js',
    '/icons/new_icon1.png',
    '/icons/new_icon2.png',
    '/icons/new_icon3.png'
].map(url => `${url}?v=${APP_VERSION}`);

/* =============================
   INSTALL
============================= */
self.addEventListener('install', event => {
    self.skipWaiting();
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => cache.addAll(STATIC_ASSETS))
    );
});

/* =============================
   ACTIVATE
============================= */
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(keys =>
            Promise.all(
                keys.map(key => {
                    if (key !== CACHE_NAME && key !== TOKEN_CACHE_NAME) {
                        return caches.delete(key);
                    }
                })
            )
        ).then(() => self.clients.claim())
    );
});

/* =============================
   SKIP WAITING
============================= */
self.addEventListener('message', event => {
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
});

/* =============================
   FETCH HANDLER
============================= */
self.addEventListener('fetch', event => {
    const url = new URL(event.request.url);

    /* 🔥 1. NEVER CACHE AUTH / OTP */
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

    /* 🔥 2. TOKEN VERIFY */
    if (url.pathname === '/tokens/verify') {

        // POST → verify token
        if (event.request.method === 'POST') {
            event.respondWith(handleTokenVerification(event.request));
            return;
        }

        // GET → page offline support
        if (event.request.method === 'GET') {
            event.respondWith(
                fetch(event.request)
                    .then(res => {
                        const clone = res.clone();
                        caches.open(CACHE_NAME).then(cache => {
                            cache.put('/tokens/verify?v=' + APP_VERSION, clone);
                        });
                        return res;
                    })
                    .catch(() =>
                        caches.match('/tokens/verify?v=' + APP_VERSION)
                    )
            );
            return;
        }
    }

    /* 🔥 3. STATIC FILES */
    if (
        url.pathname.startsWith('/assets/') ||
        url.pathname.startsWith('/icons/')
    ) {
        event.respondWith(
            caches.match(event.request).then(cached => {
                const fetchPromise = fetch(event.request)
                    .then(res => {
                        const clone = res.clone();
                        caches.open(CACHE_NAME).then(cache => {
                            cache.put(event.request, clone);
                        });
                        return res;
                    })
                    .catch(() => cached);

                return cached || fetchPromise;
            })
        );
        return;
    }

    /* 🔥 4. HTML */
    event.respondWith(
        fetch(event.request)
            .catch(() => caches.match('/offline.html?v=' + APP_VERSION))
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
                message: 'Token sahihi (Offline)',
                data: cachedToken,
                offline: true
            }), {
                headers: { 'Content-Type': 'application/json' }
            });
        }

        return new Response(JSON.stringify({
            success: false,
            message: 'Token si sahihi au imeexpire'
        }), {
            status: 400,
            headers: { 'Content-Type': 'application/json' }
        });
    }
}

/* =============================
   STORE TOKEN
============================= */
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

/* =============================
   GET TOKEN
============================= */
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

/* =============================
   CLEAN TOKENS
============================= */
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
   🔥 BACKGROUND SYNC
============================= */
self.addEventListener('sync', event => {
    if (event.tag === 'sync-tokens') {
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

            // 🔥 re-validate token with server
            const response = await fetch('/tokens/verify', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    token: data.token.token
                })
            });

            const result = await response.json();

            if (!result.success) {
                await cache.delete(req);
            }
        }

        console.log('[SW] Tokens synced successfully');
    } catch (e) {
        console.error('[SW] Sync failed', e);
    }
}
