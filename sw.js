importScripts('https://storage.googleapis.com/workbox-cdn/releases/3.0.0/workbox-sw.js');
workbox.routing.registerRoute(
    new RegExp('https://bytelover.com/css/'),
    workbox.strategies.cacheFirst()
);
workbox.routing.registerRoute(
    new RegExp('https://bytelover.com/js/'),
    workbox.strategies.cacheFirst()
);
workbox.routing.registerRoute(
    new RegExp('https://bytelover.com/img/'),
    workbox.strategies.cacheFirst()
);
workbox.routing.registerRoute(
    new RegExp('https://bytelover.com/fonts/'),
    workbox.strategies.cacheFirst()
);
workbox.precaching.precacheAndRoute([]);