# Family Vault & Pedigree Hub

A secure, internal family web application built on **Laravel 11** and **Filament PHP** designed for:
- 🔐 **Zero-Knowledge Document Vault:** Client-side Web Crypto API encryption (AES-GCM 256) ensuring only encrypted data is stored on the server.
- 🌳 **Interactive Family Tree / Pedigree:** Multi-generational hierarchy visualization with responsive canvas.
- ⚡ **Utility Records Ledger:** Centralized tracking for household meter numbers, utility accounts, and insurance policies.
- ⏰ **Automated Expiry Reminders:** Daily scheduler and alert notifications for expiring identity cards, licenses, and passports.
- 📱 **Progressive Web App (PWA):** Standalone app experience for Android, iOS, and Desktop.

## Documentation

Full architectural and technical implementation guides are available in the [`docs/`](./docs) directory:

- [00. System Overview & Architecture](./docs/overview_architecture.md)
- [01. PWA Setup & Configuration](./docs/pwa_setup_configuration.md)
- [02. Database Schema & Model Relationships](./docs/database_schema_relations.md)
- [03. Client-Side Encryption (Web Crypto API)](./docs/client_side_encryption_zero_knowledge.md)
- [04. Interactive Family Tree Page](./docs/family_tree_interactive_page.md)
- [05. Document Expiry Reminders & Task Scheduler](./docs/document_expiry_reminder_scheduler.md)
- [06. Implementation Roadmap & Checklist](./docs/implementation_roadmap_checklist.md)

## Tech Stack

- **Backend:** Laravel 11, PHP 8.2+
- **Admin & UI:** Filament PHP v3/v4, Livewire 3, Alpine.js, Tailwind CSS
- **Cryptography:** Web Crypto API (`window.crypto.subtle`)
- **Platform:** Progressive Web App (PWA) Standalone Mode
