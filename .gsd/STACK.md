# Technology Stack — Nex-on

> Initialized on 2026-11-13 for GSD Antigravity

## Runtime

| Technology | Version | Purpose |
|------------|---------|---------|
| **PHP** | 8.5.10 | Backend scripting language |
| **Laravel** | 12.72.0 | Core full-stack web framework & ORM |
| **MySQL** | 8.4.3 | Relational database (InnoDB, UTF8mb4) |
| **Node.js** | 22+ | Frontend build runtime & Vite |

## Core Architecture & Libraries

| Feature | System / Location | Purpose |
|---------|-------------------|---------|
| **Multi-Theme Storefront** | `resources/views/storefront/{saaya, buttercup, vyra, lumiere, verdia}` | 5 distinct brand themes with live preview & dynamic theme switcher |
| **Admin ERP Suite** | `app/Http/Controllers/Admin/*` | POS, Inventory, Batch tracking, Payroll, B2B Invoices, Barcode generator, Analytics, Settings |
| **PDF Generation** | `barryvdh/laravel-dompdf` v3.1.2 + `dompdf/dompdf` v3.1.6 | Thermal receipts, GST tax invoices, and packing slips |
| **HTTP Client** | `guzzlehttp/guzzle` v7.16.1 | External APIs (payment gateways, courier integrations, SMS) |
| **Markdown / Content** | `league/commonmark` v2.10.7 | Secure Markdown parsing for Knowledge Base & Helpdesk |
| **Cryptography / Security** | `phpseclib/phpseclib` v3.0.58 + `HtmlSanitizerService` | Rich-text XSS prevention, SSRF protection, TOTP 2FA |
| **Frontend Styling** | Tailwind CSS 4 + Alpine.js | Modern utility styling & lightweight reactive UI components |

## Security & Reliability Baseline

- **`composer audit`**: 0 security advisories (all 69 previous CVEs patched)
- **`npm audit`**: 0 vulnerabilities
- **PHPUnit Test Suite**: 429 feature & unit tests passing (2,406 assertions)
- **Session Security**: AES-256 encrypted sessions (`SESSION_ENCRYPT=true`), HTTP-only, SameSite=lax, Secure cookie enabled
- **Defensive Headers**: CSP, HSTS, X-Content-Type-Options, X-Frame-Options, Permissions-Policy

---

*Last updated: 2026-11-13*
