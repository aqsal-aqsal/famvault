# 00. System Overview & Architecture

## 1. Project Description
**Family Vault & Pedigree Hub** is an internal family web application built on Laravel + Filament designed to serve as:
* A secure family document vault with client-side zero-knowledge encryption (*zero-knowledge storage*).
* An interactive multi-generational family tree / pedigree visualization.
* A household utility ledger (meter numbers, customer IDs, insurance policy numbers, etc.).
* An automated document expiry reminder system (driving licenses, vehicle registrations, passports, taxes).

## 2. Tech Stack
* **Framework:** Laravel 11
* **Panel Builder:** Filament PHP v3/v4
* **Reactivity & Mobile UI:** Livewire 3 + Alpine.js + Tailwind CSS
* **Frontend Cryptography:** Web Crypto API (`window.crypto.subtle`)
* **Multi-Platform Application:** Progressive Web App (PWA) Standalone Mode
* **Target Platforms:** Android (Chrome PWA), iOS (Safari Add to Home Screen), Desktop (Chrome / Edge / Brave)

## 3. Global Data Flow Diagram

```
[ User on Mobile / Desktop ]
        │
        ├──> (A) Family Tree CRUD Interaction ──> [ Livewire / Filament ] ──> [ DB: family_members ]
        │
        ├──> (B) Document Upload ──> [ Alpine.js: Web Crypto Encrypt ] 
        │                                   │
        │                                   ▼ (Send Ciphertext .enc + IV)
        │                            [ Laravel Controller / Storage ] ──> [ Storage / Disk ]
        │
        └──> (C) Open Document ────> [ Download Ciphertext ]
                                            │
                                            ▼ (Input Passphrase in RAM)
                                     [ Alpine.js: Decrypt In-Memory ] ──> [ Display Blob URL ]
```

## 4. Documentation Structure
* `pwa_setup_configuration.md`: PWA setup, Service Worker configuration, and Android/iOS meta tags.
* `database_schema_relations.md`: Migration schema definitions and Eloquent relationship mappings.
* `client_side_encryption_zero_knowledge.md`: Technical workflow and Web Crypto API implementation.
* `family_tree_interactive_page.md`: Filament custom page setup and interactive family tree canvas.
* `document_expiry_reminder_scheduler.md`: Automated reminders, cron job scheduling, and notification integrations.
* `implementation_roadmap_checklist.md`: Step-by-step implementation milestones and task checklist.
