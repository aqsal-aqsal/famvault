# 01. PWA Configuration (Android, iOS, Desktop)

This document provides a setup guide for running the Filament panel as a standalone Progressive Web App (PWA) without a browser address bar on Android and iOS.

## 1. Web App Manifest (`public/manifest.json`)
Place this file at `public/manifest.json`:

```json
{
  "name": "Family Vault & Tree",
  "short_name": "FamilyVault",
  "start_url": "/admin",
  "scope": "/admin",
  "display": "standalone",
  "background_color": "#09090b",
  "theme_color": "#09090b",
  "orientation": "portrait-primary",
  "icons": [
    {
      "src": "/icons/icon-192x192.png",
      "sizes": "192x192",
      "type": "image/png"
    },
    {
      "src": "/icons/icon-512x512.png",
      "sizes": "512x512",
      "type": "image/png"
    },
    {
      "src": "/icons/icon-512x512-maskable.png",
      "sizes": "512x512",
      "type": "image/png",
      "purpose": "maskable"
    }
  ]
}
```

## 2. Service Worker (`public/sw.js`)
The Service Worker utilizes a **Network First** strategy to ensure Livewire calls and Laravel CSRF token state are not disrupted by local caching:

```javascript
const CACHE_NAME = 'family-vault-v1';
const ASSETS_TO_CACHE = [
  '/manifest.json',
  '/icons/icon-192x192.png',
  '/icons/icon-512x512.png'
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => cache.addAll(ASSETS_TO_CACHE))
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
  // Only process GET requests; bypass Livewire POST / PUT requests
  if (event.request.method !== 'GET') {
    return;
  }

  event.respondWith(
    fetch(event.request).catch(() => caches.match(event.request))
  );
});
```

## 3. PWA Meta Tags Component (`resources/views/filament/pwa-tags.blade.php`)
```html
<link rel="manifest" href="/manifest.json">
<meta name="theme-color" content="#09090b">

{{-- iOS Safari Configuration --}}
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="FamilyVault">
<link rel="apple-touch-icon" href="/icons/icon-192x192.png">

{{-- Viewport handling for notched displays / Dynamic Island --}}
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">

<style>
  body {
    padding-top: env(safe-area-inset-top);
    padding-bottom: env(safe-area-inset-bottom);
  }
</style>

<script>
  if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
      navigator.serviceWorker.register('/sw.js').catch((err) => {
        console.error('Service Worker registration failed:', err);
      });
    });
  }
</script>
```

## 4. Filament Registration (`AdminPanelProvider.php`)
```php
use Filament\View\PanelsRenderHook;

public function panel(Panel $panel): Panel
{
    return $panel
        ->default()
        ->id('admin')
        ->path('admin')
        ->renderHook(
            PanelsRenderHook::HEAD_START,
            fn () => view('filament.pwa-tags'),
        );
}
```
