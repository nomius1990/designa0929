<?php

namespace App\ProgressiveWebApp\Observer;

use Braintree\Exception;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

class UpdateManifest implements ObserverInterface
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
        if ($this->_pwaHelper->isEnabled() && $this->_pwaHelper->isModuleOutputEnabled('App_ProgressiveWebApp') == '1') {
            $manifestData = [
                'name' => $this->_pwaHelper->getManifestName(),
                'short_name' => $this->_pwaHelper->getManifestShortName(),
                'description' => $this->_pwaHelper->getManifestDescription(),
                'start_url' => $this->_pwaHelper->getManifestStartUrl(),
                'display' => $this->_pwaHelper->getManifestDisplayType(),
                'orientation' => $this->_pwaHelper->getManifestOrientation(),
                'theme_color' => $this->_pwaHelper->getManifestThemeColor(),
                'background_color' => $this->_pwaHelper->getManifestBgColor(),
                "gcm_sender_id" => '103953800507'
            ];

            if ($icon = $this->_pwaHelper->getManifestIcon()) {
                $url = implode("", [
                    $this->urlBuilder->getBaseUrl(["_type" => UrlInterface::URL_TYPE_MEDIA]),
                    "app/manifest/icons/",
                    $icon,
                ]);
                $iconSizes = explode(',', $this->_pwaHelper->getManifestIconSizes());
                $i = 0;
                foreach ($iconSizes as $size) {
                    $manifestData['icons'][$i] = ["src" => $url, "sizes" => $size];
                    $i++;
                }
            }

            $manifestData = json_encode($manifestData);
            $this->updateManifestStatic($manifestData);
            $this->updateManifestApp($manifestData);
            $this->copyServiceWorkerJs();

        }
    }

    public function updateManifestStatic($manifestData)
    {
        $ioAdapter = $this->file;
        $staticPath = $this->directoryList->getPath(DirectoryList::STATIC_VIEW) .
            \DIRECTORY_SEPARATOR .
            $this->assetRepo->getStaticViewFileContext()->getPath() .
            \DIRECTORY_SEPARATOR .
            'App_ProgressiveWebApp' . \DIRECTORY_SEPARATOR . 'js' . \DIRECTORY_SEPARATOR;

        try {
            if ($ioAdapter->checkAndCreateFolder($staticPath, 0777)) {
                $fileName = "manifest.json";
                $ioAdapter->open(array('path' => $staticPath));
                $ioAdapter->write($fileName, $manifestData, 0777);
            }
        } catch (\Exception $exception) {
            $this->_pwaHelper->logger()->error($exception);
        }
    }

    public function updateManifestApp($manifestData)
    {
        $ioAdapter = $this->file;
        $appPath = $this->directoryList->getPath(DirectoryList::APP) .
            \DIRECTORY_SEPARATOR . 'code' .
            \DIRECTORY_SEPARATOR . 'App' .
            \DIRECTORY_SEPARATOR . 'ProgressiveWebApp' .
            \DIRECTORY_SEPARATOR . 'view' .
            \DIRECTORY_SEPARATOR . 'frontend' .
            \DIRECTORY_SEPARATOR . 'web' .
            \DIRECTORY_SEPARATOR . 'js' . \DIRECTORY_SEPARATOR;

        try {
            if ($ioAdapter->checkAndCreateFolder($appPath, 0777)) {
                $ioAdapter->mkdir($appPath, 0777);
                $fileName = "manifest.json";
                $ioAdapter->open(array('path' => $appPath));
                $ioAdapter->write($fileName, $manifestData, 0777);
            }
        } catch (\Exception $exception) {
            $this->_pwaHelper->logger()->error($exception);
        }
    }

    public function copyServiceWorkerJs()
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
