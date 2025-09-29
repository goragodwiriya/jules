const CACHE_NAME = 'appointment-scheduler-v1';
const urlsToCache = [
    '/',
    '/index.html',
    '/css/style.css',
    '/js/script.js',
    '/manifest.json',
    '/images/icon-192x192.png',
    '/images/icon-512x512.png'
];

self.addEventListener('install', event => {
    // Perform install steps
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => {
                console.log('Opened cache');
                return cache.addAll(urlsToCache);
            })
    );
});

self.addEventListener('fetch', event => {
    event.respondWith(
        caches.match(event.request)
            .then(response => {
                // Cache hit - return response
                if (response) {
                    return response;
                }
                return fetch(event.request);
            }
        )
    );
});

self.addEventListener('activate', event => {
    const cacheWhitelist = [CACHE_NAME];
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cacheName => {
                    if (cacheWhitelist.indexOf(cacheName) === -1) {
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
});

// Handle notification clicks
self.addEventListener('notificationclick', event => {
    event.notification.close(); // Close the notification

    // This opens the app to the root page. A more complex app might
    // navigate to the specific appointment.
    event.waitUntil(
        clients.matchAll({ type: 'window' }).then(clientsArr => {
            // If a window is already open, focus it.
            const hadWindowToFocus = clientsArr.some(windowClient => windowClient.url === self.location.origin + '/index.html' ? (windowClient.focus(), true) : false);
            // Otherwise, open a new window.
            if (!hadWindowToFocus) clients.openWindow('index.html');
        })
    );
});