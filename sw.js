//Install service worker and cache important files into the cache storage
self.addEventListener("install", e => {
    e.waitUntil(
        caches.open("pwa-assets")
        .then(cache => {
           return cache.addAll(["./", "./styles/style.css", "./scripts/jquery.min.js", "./scripts/site_scripts.js", "./scripts/clipboard.min.js", "./images/thetecsup_logo_icon.png", "./images/profile.jpg"]);
        })
    );
});

//check if reposnse hae been cached and if not use the network to load resources
self.addEventListener("fetch", e => {
    e.respondWith(
        caches.match(e.request).then(response => {
            return response || fetch(e.request); 
        })
    );
});