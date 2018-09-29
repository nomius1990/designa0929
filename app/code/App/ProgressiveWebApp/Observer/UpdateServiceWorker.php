<?php

namespace App\ProgressiveWebApp\Observer;

use Braintree\Exception;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

class UpdateServiceWorker implements ObserverInterface
{
    protected $_pwaHelper;
    protected $urlBuilder;
    protected $directoryList;
    private $assetRepo;
    protected $file;

    public function __construct(
        \App\ProgressiveWebApp\Helper\Data $pwaHelper,
        UrlInterface $urlBuilder,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Framework\Filesystem\Io\File $file
    ) {
        $this->_pwaHelper = $pwaHelper;
        $this->urlBuilder = $urlBuilder;
        $this->directoryList = $directoryList;
        $this->assetRepo = $assetRepo;
        $this->file = $file;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $randomNumber = rand(10, 100);
        $senderId = $this->_pwaHelper->getMessageSenderId();
        $fcmVersion = $this->_pwaHelper->getFcmVersion();
        if ($this->_pwaHelper->isEnabled() && $this->_pwaHelper->isModuleOutputEnabled('App_ProgressiveWebApp') == '1') {
            $data = "/**
 * @copyright Copyright
 */
importScripts('https://www.gstatic.com/firebasejs/$fcmVersion/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/$fcmVersion/firebase-messaging.js');

firebase.initializeApp({
    'messagingSenderId': '$senderId'
});

const messaging = firebase.messaging();
var CACHE_NAME = 'pwa-app-cache-v1';
var SWversion = '$randomNumber';

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
});";
            $ioAdapter = $this->file;
            $rootPath = $this->directoryList->getPath(DirectoryList::ROOT) . \DIRECTORY_SEPARATOR;

            try {
                if ($ioAdapter->fileExists($rootPath . 'serviceWorker.js')) {
                    $ioAdapter->rm($rootPath . 'serviceWorker.js');
                }
                $fileName = "serviceWorker.js";
                $ioAdapter->open(array('path' => $rootPath));
                $ioAdapter->write($fileName, $data, 0777);
            } catch (\Exception $exception) {
                $this->_pwaHelper->logger()->error($exception);
            }
        }
    }

}
