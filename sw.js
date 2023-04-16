//Install service worker and cache important files into the cache storage
self.addEventListener("install", e => {
    e.waitUntil(
        caches.open("pwa-assets")
        .then(cache => {
        return cache.addAll(["./", "./index", "./styles/style.css", "./images/thetecsup_logo_icon.png", "./scripts/jquery.min.js", "./scripts/site_scripts.js", "./scripts/clipboard.min.js", "./offline.html"]);
        })
    );
});

//check if reposnse hae been cached and if not use the network to load resources
self.addEventListener("fetch", e => {
    e.respondWith(
        caches.match(e.request).then(response => {
            // serve cached url or serve request from network if url isn't cached
            return response || fetch(e.request); 
        }).catch(() => {
            //fall back url for when request fails
            return caches.match('./offline.html');
         }
        )
    );
});