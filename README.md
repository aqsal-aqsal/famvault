# Family Vault & Pedigree Hub

A secure, internal family web application built on **Laravel 11** and **Filament PHP** designed for:
- 🔐 **Zero-Knowledge Document Vault:** Client-side Web Crypto API encryption (AES-GCM 256) ensuring only encrypted data is stored on the server.
- 🌳 **Interactive Family Tree / Pedigree:** Multi-generational hierarchy visualization with responsive canvas.
- ⚡ **Utility Records Ledger:** Centralized tracking for household meter numbers, utility accounts, and insurance policies.
- ⏰ **Automated Expiry Reminders:** Daily scheduler and alert notifications for expiring identity cards, licenses, and passports.
- 📱 **Progressive Web App (PWA):** Standalone app experience for Android, iOS, and Desktop.

## Quick Start Guide

### 1. Requirements
- PHP 8.2+ with `pdo_sqlite`, `sqlite3`, `zip`, and `gd` extensions enabled.
- Composer 2.x

### 2. Setup
```bash
# Copy environment file and generate application key
cp .env.example .env
php artisan key:generate

# Run database migrations and seed sample pedigree & utility data
php artisan migrate --seed

# Run local development server
php artisan serve
```

Access the application at `http://127.0.0.1:8000/admin`.

**Default Admin Credentials:**
- Email: `admin@famvault.local`
- Password: `password`

### 3. Expiry Check Scheduler
Run manually or let the automated daily cron task handle document expiry alerts:
```bash
php artisan vault:check-expiry
```

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
- **Admin & UI:** Filament PHP, Livewire 3, Alpine.js, Tailwind CSS
- **Cryptography:** Web Crypto API (`window.crypto.subtle`)
- **Platform:** Progressive Web App (PWA) Standalone Mode
