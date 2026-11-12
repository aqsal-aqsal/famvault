# 06. Implementation Roadmap & Checklist

Use this document as a milestone progress guide within Antigravity IDE.

## Milestone Checklist

### [x] Week 1: Initialization & PWA Foundation
- [x] Scaffold Laravel 11 & install Filament v3/v4.
- [x] Create `public/manifest.json` and generate app icons (192px & 512px).
- [x] Implement `public/sw.js` (Network-First Service Worker).
- [x] Create `pwa-tags.blade.php` view and register via `PanelsRenderHook::HEAD_START`.
- [x] Test PWA installation on Desktop browsers and iOS Safari (Add to Home Screen).

### [x] Week 2: Database Schema & Pedigree Management
- [x] Create migrations for `family_members`, `vault_documents`, and `utility_records`.
- [x] Set up Eloquent model relationships (`father`, `mother`, `spouse`, `children`).
- [x] Build `FamilyMemberResource` in Filament (CRUD operations for family members).
- [x] Build `FamilyTree` Custom Page and integrate an interactive canvas diagram.
- [x] Verify inter-generational linkages (children, parents, spouses).

### [x] Week 3: Document Vault Security (Client-Side Encryption)
- [x] Implement `vault-crypto.js` utility module (Web Crypto API AES-GCM 256).
- [x] Build `VaultDocumentResource` in Filament.
- [x] Customize the upload form so files are encrypted in the browser prior to server transmission.
- [x] Build document view modal with *in-memory* decryption using passphrase.
- [x] Build `UtilityRecordResource` for managing utility/meter/policy accounts.

### [x] Week 4: Notification Automation & Final Testing
- [x] Add remaining-days badge column to the Filament documents table.
- [x] Implement `vault:check-expiry` Artisan command and register daily cron schedule.
- [x] Integrate notification webhook bot (Telegram/WhatsApp) for expiry alerts.
- [x] Test mobile viewport responsiveness, touch gestures, and safe-area insets on Android & iOS.
