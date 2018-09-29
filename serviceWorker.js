/**
 * @copyright Copyright
 */
importScripts('https://www.gstatic.com/firebasejs/5.3.1/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/5.3.1/firebase-messaging.js');

firebase.initializeApp({
    'messagingSenderId': '266675675605'
});

const messaging = firebase.messaging();
var CACHE_NAME = 'pwa-app-cache-v1';
var SWversion = '84';

self.addEventListener('install', function (event) {
    // Perform install steps
    event.waitUntil(
        self.skipWaiting()
    );
});

self.addEventListener('activate', function (event) {
    console.log('[ServiceWorker] Activate');
    event.waitUntil(
        caches.keys().then(function (cacheNames) {
            return Promise.all(
                cacheNames.map(function (cacheName) {
                    return caches.delete(cacheName);

                })
            );
        })
    );
    return self.clients.claim()
});

self.addEventListener('fetch', function (event) {
    if (event.request.method !== 'POST' && event.request.url.toString() &&
        event.request.url.toString().indexOf('/admin/') === -1 &&
        event.request.url.toString().indexOf('/checkout/') === -1 &&
        event.request.url.toString().indexOf('/cart/') === -1 &&
        event.request.url.toString().indexOf('/key/') === -1 &&
        event.request.url.toString().indexOf('/adminhtml/') === -1 &&
        event.request.url.toString().indexOf('/serviceWorker/') === -1) {
        event.respondWith(
            caches.match(event.request)
                .then(function (response) {
                    // Cache hit - return response
                    if (response) {
                        return response;
                    }

                    // IMPORTANT: Clone the request. A request is a stream and
                    // can only be consumed once. Since we are consuming this
                    // once by cache and once by the browser for fetch, we need
                    // to clone the response.
                    var fetchRequest = event.request.clone();

                    return fetch(fetchRequest).then(
                        function (response) {
                            // Check if we received a valid response
                            if (!response || response.status !== 200 || response.type !== 'basic') {
                                return response;
                            }

                            // IMPORTANT: Clone the response. A response is a stream
                            // and because we want the browser to consume the response
                            // as well as the cache consuming the response, we need
                            // to clone it so we have two streams.

                            var responseToCache = response.clone();
                            caches.open(CACHE_NAME)
                                .then(function (cache) {
                                    cache.put(event.request, responseToCache);

                                });

                            return response;
                        }
                    );
                })
        );
    }
});

messaging.setBackgroundMessageHandler(function (payload) {
    console.log('[serviceWorker.js] Received background message ', payload);
});

self.addEventListener('notificationclick', function (event) {
    event.notification.close();
});