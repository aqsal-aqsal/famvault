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
