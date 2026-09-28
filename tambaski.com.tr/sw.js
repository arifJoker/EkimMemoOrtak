/**
 * TAMBASKI.COM.TR - PWA Service Worker
 */

const CACHE_NAME = 'tambaski-v1.0.1';
const ASSETS_TO_CACHE = [
  '/',
  '/index.php',
  '/category.php',
  '/cart.php',
  '/assets/css/style.css',
  '/assets/js/main.js',
  '/assets/img/logo.svg'
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll(ASSETS_TO_CACHE).catch(() => {});
    })
  );
  self.skipWaiting();
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) => {
      return Promise.all(
        keys.filter((key) => key !== CACHE_NAME).map((key) => caches.delete(key))
      );
    })
  );
  self.clients.claim();
});

self.addEventListener('fetch', (event) => {
  if (event.request.method !== 'GET') return;
  
  event.respondWith(
    fetch(event.request).catch(() => {
      return caches.match(event.request);
    })
  );
});
