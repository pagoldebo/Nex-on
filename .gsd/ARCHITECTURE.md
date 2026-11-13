# System Architecture — Nex-on Luxury E-Commerce & ERP

> Initialized on 2026-11-13 for GSD Antigravity

## System Overview

Nex-on is a full-stack Laravel 12 multi-theme luxury e-commerce engine and enterprise ERP backend designed for high-end retail, custom apparel, and omnichannel operations.

```
                  ┌──────────────────────────────────────────────┐
                  │          Storefront Themes (5 Themes)        │
                  │   [Saaya] [Buttercup] [Vyra] [Lumière] [Verdia]│
                  └──────────────────────┬───────────────────────┘
                                         │
┌───────────────────────────────┐        │
│       Admin ERP Platform      │        │  Blade Views + Tailwind 4 + Alpine.js
│  - POS & Thermal Billing      │        │
│  - B2B GST Invoicing & Tally  │        ▼
│  - Inventory & FIFO Batches   │  ┌──────────────────────────────┐
│  - Staff Attendance & Payroll │  │     HTTP Middleware Layer    │
│  - Settings & Theme Engine    ├──┤  - SecurityHeadersMiddleware │
│  - Loyalty & Patron 2FA       │  │  - EnsureUserIsAdmin         │
└───────────────┬───────────────┘  │  - RateLimiter (Throttles)   │
                │                  └──────────────┬───────────────┘
                │                                 │
                ▼                                 ▼
┌─────────────────────────────────────────────────────────────────┐
│                    Eloquent Domain Models & Services             │
│  - Product, Category (Recursive Rollups), Color, Size, Variant   │
│  - Order, OrderItem, AbandonedCart, Coupon, Customer, Loyalty    │
│  - Services: HtmlSanitizerService, SsrfProtection, ProductImage │
└────────────────────────────────┬────────────────────────────────┘
                                 │
                                 ▼
┌─────────────────────────────────────────────────────────────────┐
│                    MySQL Database (nexon_db)                    │
│      Backup: database/nexon_db_backup.sql (Schema + Seeds)       │
└─────────────────────────────────────────────────────────────────┘
```

## Architectural Highlights

### 1. Multi-Theme Switching Architecture
- 5 independent storefront views reside under `resources/views/storefront/{theme_key}/`.
- Active theme dynamically controlled via `Template::active()` and Admin Settings.
- Zero hardcoded assets: All slider banners, ad graphics, and CTAs dynamically stream from database settings (`Setting::get(...)`) or fall back to native CSS gradients.

### 2. Recursive Category Hierarchy
- Products attached to deep subcategories (e.g. `Men -> Shirt -> Formal Shirt`) automatically roll up recursive piece counts to all parent categories.
- Handled cleanly via recursive Eloquent relations and optimized tree traversal.

### 3. Dynamic Environment Configuration
- Support and notification email addresses resolve dynamically via `Setting::supportEmail()`, prioritizing `.env` variables (`SUPPORT_EMAIL` or `MAIL_FROM_ADDRESS`) without hardcoded domain fallbacks.

### 4. Enterprise Hardening & Evidence Discipline
- Strict separation of guest vs admin operations (`auth` + `EnsureUserIsAdmin`).
- Every input mutating rich-text product copy passes through `HtmlSanitizerService` mutators to stop XSS before persistence.
- Complete regression suite: 429 tests verify inventory, auth, OTP, B2B tax math, and frontend theme integrity.

---

*Last updated: 2026-11-13*
