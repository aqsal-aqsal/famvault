# 06. Implementation Roadmap & Checklist

Use this document as a milestone progress guide within Antigravity IDE.

## Milestone Checklist

### [ ] Week 1: Initialization & PWA Foundation
- [ ] Scaffold Laravel 11 & install Filament v3/v4.
- [ ] Create `public/manifest.json` and generate app icons (192px & 512px).
- [ ] Implement `public/sw.js` (Network-First Service Worker).
- [ ] Create `pwa-tags.blade.php` view and register via `PanelsRenderHook::HEAD_START`.
- [ ] Test PWA installation on Desktop browsers and iOS Safari (Add to Home Screen).

### [ ] Week 2: Database Schema & Pedigree Management
- [ ] Create migrations for `family_members`, `vault_documents`, and `utility_records`.
- [ ] Set up Eloquent model relationships (`father`, `mother`, `spouse`, `children`).
- [ ] Build `FamilyMemberResource` in Filament (CRUD operations for family members).
- [ ] Build `FamilyTree` Custom Page and integrate an interactive canvas diagram.
- [ ] Verify inter-generational linkages (children, parents, spouses).

### [ ] Week 3: Document Vault Security (Client-Side Encryption)
- [ ] Implement `vault-crypto.js` utility module (Web Crypto API AES-GCM 256).
- [ ] Build `VaultDocumentResource` in Filament.
- [ ] Customize the upload form so files are encrypted in the browser prior to server transmission.
- [ ] Build document view modal with *in-memory* decryption using passphrase.
- [ ] Build `UtilityRecordResource` for managing utility/meter/policy accounts.

### [ ] Week 4: Notification Automation & Final Testing
- [ ] Add remaining-days badge column to the Filament documents table.
- [ ] Implement `vault:check-expiry` Artisan command and register daily cron schedule.
- [ ] Integrate notification webhook bot (Telegram/WhatsApp) for expiry alerts.
- [ ] Test mobile viewport responsiveness, touch gestures, and safe-area insets on Android & iOS.
