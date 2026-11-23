# Nex-on — Multi-Theme E-Commerce & Retail ERP

[![Laravel 12](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP 8.2+](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind-CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![Alpine.js](https://img.shields.io/badge/Alpine.js-3.x-8BC0D0?style=for-the-badge&logo=alpine.js&logoColor=white)](https://alpinejs.dev)
[![Tests Passing](https://img.shields.io/badge/Tests-479%20Passed-brightgreen?style=for-the-badge)](tests)
[![Release](https://img.shields.io/badge/Release-v0.11-blue?style=for-the-badge)](https://github.com/pagoldebo/Nex-on/releases)

**Nex-on** is an enterprise-grade, multi-theme e-commerce and retail ERP platform built on **Laravel 12**. It pairs five distinct, high-fashion storefront design systems with a full-suite omnichannel back-office (inventory, POS, warehouse batch tracking, worldwide tax & GST accounting, HRM, and patron security).

---

## 🌟 Key Features

### 1. Five Interchangeable Storefront Themes
Switch between five fully realized lifestyle brand aesthetics in one click without altering database records or business logic:
* **Vyra**: High-impact modern noir & streetwear aesthetic (dark mode `#0E0E10`, electric coral `#FF3D5A`, lime `#D4FF3D`, video hero sliders).
* **Buttercup**: Cloud-soft, pastel comfort intimates (blush `#FBE7E4`, rose `#B97680`, sage `#A8B89A`).
* **Saaya**: Royal Indian trousseau & heritage silk (deep burgundy `#4A0E17`, antique gold `#D4AF37`, silk cream `#fdfbf7`).
* **Lumière**: Minimalist, French-inspired pure silk atelier (`#FAFAFA`, deep pine `#405549`, editorial typography).
* **Organic**: Farm-to-fork raw living, artisanal wellness & cold-pressed pantry (`#F9F8F3`, forest sage `#2D6A4F`, sunlit amber `#F1B927`).

### 2. Universal Product Quick View
* Interactive quick view modal integrated seamlessly across all 5 themes.
* Customers can preview product image galleries, switch color swatches, select sizes, view real-time inventory stock indicators, and add items directly to their shopping cart without leaving catalog or category feeds.

### 3. Worldwide Tax Configuration & Invoicing
* **Configurable Global Tax Settings**: Set custom tax name (e.g. VAT, GST, Sales Tax, IVA), tax percentage rate, and tax registration number directly in Admin Settings, with full toggle control.
* **Customer Invoices**: Detailed tax break-up (Subtotal, Tax Rate %, Shipping, and Grand Total) displayed on customer order summaries, receipts, and downloadable PDF invoices.
* **Over-the-Counter POS**: Real-time tax computation on cash/card terminal checkouts and thermal receipts.
* **B2B Commercial Invoices**: Interactive line-item repeater with automated tax breakdowns (CGST/SGST/IGST or single global VAT/sales tax percentage) and one-click PDF printing.
* **Supplier Procurement**: Supplier invoices and purchase orders support per-line tax rates and discount inputs.

### 4. 100% Next-Gen AVIF Image Pipeline & Direct Public Media
* **Automated AVIF Storage**: Raster uploads (JPG, PNG, GIF) are automatically converted into next-generation **`.avif`** format via native PHP 8.5 `imageavif()`, delivering up to 50% smaller file sizes than WebP and 80% smaller than JPEG with pristine alpha channel preservation.
* **Client-Side Pre-Conversion (`public/js/avif-converter.js`)**: Converts images directly in the user's browser before upload using HTML5 Canvas, displaying an interactive overlay progress spinner (*"Converting Image to AVIF..."*) for ultra-fast uploads.
* **Zero Legacy WebP Dependencies**: Fully purged legacy WebP converter scripts and listeners across admin and storefront forms.
* **Direct Media Storage**: Media assets (`banners/`, `branding/`, `categories/`, `products/`, `slider/`) are stored directly inside `public/images/`, removing any symlink dependencies (`php artisan storage:link` is obsolete) and protected by Apache `.htaccess` script execution prevention.
* **Brand Logo Fidelity**: Primary header and dedicated footer logos are preserved in their native lossless format (`branding/logo.png`), preventing unwanted lossy compression.

### 5. Patron Security & Centered Two-Factor Authentication
* **Centered Modal Dialogs**: Universal horizontal and vertical centering for login modals, register forms, and account dialogues across all 5 themes and mobile viewports.
* **Email Verification OTP**: Secure 4-digit numeric code dispatched on every sign-in attempt.
* **Auto-Submit & Resend Cooldown**: Auto-submits on 4 digits typed/pasted, paired with a 60-second AJAX resend timer.
* **Responsive HTML Mail Theme**: Polished email template with prominent code block, expiration countdown, and security warnings. Preview available at `/preview-mail/otp`.

### 6. Hierarchical Category Management
* Multi-level parent-child tree navigation with accordion auto-collapse in the admin dashboard.
* Storefront filter tabs automatically include ancestor and subcategory products for seamless browsing.

### 7. Omnichannel Retail & Point of Sale (POS)
* Shift management, cash float tracking, drawer opening/closing.
* Pure-PHP Code128 thermal barcode label printing (vector SVG generation).

### 8. Multi-Outlet Warehousing & Batch Tracking
* Outlets and inter-branch stock transfers.
* FIFO batch allocation, expiration date tracking, and recall traceability.

### 9. Procurement & Commercial Invoicing
* Supplier directory, Purchase Orders (PO), and 3-way invoice matching.
* B2B Commercial Invoices with automatic tax splits and printable PDF format.
* One-click Tally ERP XML accounting voucher export.

### 10. HRM, Staff Performance & Payroll
* Employee directory, department allocation, and shift management.
* Daily clock-in/out attendance tracking and monthly salary slip generation.
* 360-degree peer feedback wall and quarterly OKR / appraisal scoring matrices (30/50/20 composite).

### 11. Customer Support & Growth Marketing
* Support ticket helpdesk with SLA tracking, private agent notes, and customer replies.
* Knowledge Base article publisher and storefront FAQ builder.
* Multi-SMS gateway drivers (Twilio, MSG91, Fast2SMS, Log driver).
* Abandoned cart automated recovery drip campaigns.
* Tiered VIP Loyalty Rewards program (welcome points, point accrual, and tier upgrades).

### 12. Multi-Currency Engine & Global Forex Conversion
* **10 Global Currencies Supported**: Native out-of-the-box support for INR (`₹`), USD (`$`), EUR (`€`), GBP (`£`), AED (`AED `), CAD (`CA$`), AUD (`AU$`), SGD (`SG$`), SAR (`SAR `), and JPY (`¥`).
* **Universal Storefront Currency Switcher**: Accessible `<x-currency-switcher />` dropdown seamlessly integrated into desktop headers and mobile navigation drawers across all 5 themes.
* **Instant Reactive Switching**: Switches instantly via session (`session('currency')`), cookie, and AJAX (`POST /currency/switch`), updating both Blade directives (`@currency($amount)`) and clientside JavaScript (`window.formatPrice()`).
* **Order Transactional Snapshotting**: At checkout, each order freezes the patron's active currency and exact exchange rate directly onto the `orders` record (`currency` and `exchange_rate`), guaranteeing permanent historical accuracy for international book-keeping and customer receipts.
* **Admin Rate & Base Currency Manager**: Configure base currency, toggle multi-currency on/off, and customize exact exchange rates with real-time sample previews in `/admin/settings`.

### 13. Modular Asset Separation & Zero Embedded Scripts
* **Enterprise Asset Separation**: All procedural `<script>` and `<style>` blocks across all 14 admin domains (Finance, Fleet, HRM, Integrations, Inventory, Invoices, Marketing, Orders, POS, Products, Purchases, Reports, Settings, Staff) are extracted into dedicated versioned static files under `public/js/admin/` and `public/css/admin/`.
* **Clean Blade Templates**: Blade views contain pure declarative markup, eliminating CSP script-injection vectors and enabling aggressive HTTP browser caching.

---

## 🛠 Tech Stack

* **Backend Framework**: Laravel 12 (PHP 8.2+)
* **Database**: MySQL 8.x / MariaDB
* **Frontend**: Blade Components, Tailwind CSS, Alpine.js, HTML5 Canvas
* **Image Engine**: GD / native `imageavif` (PHP 8.5) & Client-side HTML5 Canvas AVIF
* **Testing**: PHPUnit / Pest (479 tests, 2,734 assertions)
* **Auth**: Custom Patron Auth with Two-Factor Email OTP
* **PWA**: Web App Manifest & service worker readiness

---

## 🚀 Getting Started

### Prerequisites
* PHP >= 8.2 with `pdo_mysql`, `mbstring`, `openssl`, `gd` (with AVIF support).
* Composer 2.x
* MySQL 8.0+ or MariaDB 10.4+ (XAMPP / Laragon / Native)

### Installation Steps

1. **Clone or Navigate to the Repository**:
   ```bash
   cd c:\xampp\htdocs\public\Nexon
   ```

2. **Install Dependencies**:
   ```bash
   composer install
   ```

3. **Configure Environment**:
   Copy `.env.example` to `.env` if not present and configure database & mail settings:
   ```env
   APP_NAME="Nex-on"
   APP_ENV=local
   APP_KEY=
   APP_DEBUG=true
   APP_URL=http://localhost/Nexon/public

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=nexon_db
   DB_USERNAME=root
   DB_PASSWORD=

   MAIL_MAILER=log
   MAIL_FROM_ADDRESS="support@nexon.app"
   MAIL_FROM_NAME="${APP_NAME}"
   ```

4. **Generate Application Key**:
   ```bash
   php artisan key:generate
   ```

5. **Run Migrations & Seeders (or import fresh production database)**:
   ```bash
   php artisan migrate --seed
   ```
   *Alternatively, import `database/nexon_fresh_production.sql` directly into MySQL for a production-ready store with 0 test orders.*

6. **Direct Media Assets (No Symlink Needed)**:
   Media assets (`banners/`, `branding/`, `categories/`, `products/`, `slider/`) are stored directly inside `public/images/`. No symbolic link (`php artisan storage:link`) is needed.

7. **Start Development Server** (if not using Apache/XAMPP):
   ```bash
   php artisan serve
   ```

---

## 💾 Production Database Snapshots

Pre-packaged database snapshots are available inside the `database/` directory:
* **`database/nexon_fresh_production.sql`**: Production launch DB with **0 test orders**, active themes, default worldwide tax configurations, and super admin accounts.
* **`database/nexon_demo_production.sql`**: Comprehensive demo database loaded with sample apparel products, categories, tags, and reviews across all themes.

Production release zip: **[nexon-production.zip (GitHub Release v0.10)](https://github.com/pagoldebo/Nex-on/releases/download/v0.10/nexon-production.zip)**

---

## 🔑 Default Credentials

| Role | Email / Username | Password | Access URL | Privileges / Notes |
| :--- | :--- | :--- | :--- | :--- |
| **Super Administrator** | `admin@nexon.app` | `admin123` | `/login` → `/admin` | Full unrestricted access to store operations, settings, ERP, and finances |
| **Store Manager** | `manager@nexon.app` | `manager123` | `/login` → `/admin` | Store item catalog, sales orders, billing, invoices, POS checkout & inventory (Settings & Staff restricted) |
| **Demo Customer / Patron** | `priya.sharma@example.com` | `password123` | `/login` | Storefront browsing, shopping cart, customer checkout & orders |

> [!NOTE]
> **Two-Step Verification (OTP):**
> Upon submitting your email and password, a secure 4-digit verification code is dispatched. In local development (`MAIL_MAILER=log`), the OTP is written directly to **`storage/logs/laravel.log`** and saved in the **`otp_code`** column on the `users` table.
> You can also fetch or generate a fresh OTP anytime via the dedicated Artisan CLI command:
> ```bash
> php artisan admin:otp
> ```

---

## 🧪 Testing

Nex-on includes a comprehensive automated test suite covering all storefront themes, authentication, ERP workflows, tax calculation, and APIs:

```bash
php artisan test
```

To run only the Email OTP authentication tests:
```bash
php artisan test --filter=LoginEmailOtpTest
```

To run the Worldwide Tax tests:
```bash
php artisan test --filter=WorldwideTaxSettingsTest
```

To run the Storefront Login Modal tests:
```bash
php artisan test --filter=StorefrontLoginModalTest
```

---

## 📁 Key Directory Structure

```
Nexon/
├── app/
│   ├── Http/Controllers/     # Storefront, Admin, POS, HRM, Invoicing Controllers
│   ├── Models/               # Eloquent Models (Product, Order, Batch, User, etc.)
│   ├── Notifications/        # TwoFactorOTP and System Notifications
│   └── Services/             # ProductImageService, BarcodeService, SmsService
├── database/
│   ├── migrations/           # Database Schema Migrations (52 migrations)
│   ├── seeders/              # Initial Seeders (Admin, Categories, Products, Themes)
│   ├── nexon_fresh_production.sql  # Clean DB with 0 test orders
│   └── nexon_demo_production.sql   # Full demo catalog DB
├── resources/
│   └── views/
│       ├── auth/             # Login, Register, Two-Factor OTP views
│       ├── admin/            # Admin Back-Office & ERP modules
│       ├── emails/           # HTML Email Templates (TwoFactorOTP, Invoices)
│       ├── components/       # Global Storefront Layout and Shared UI
│       └── storefront/       # 5 Theme Folders (vyra, buttercup, saaya, lumiere, organic)
├── routes/
│   ├── web.php               # Storefront & Admin ERP Routes
│   └── auth.php              # Login, Registration & OTP Verification Routes
└── tests/
    └── Feature/              # 470 Automated Feature & Integration Tests (2,677 assertions)
```

---

## 📚 Engineering & Operations Documentation
* **[Storefront Themes Architecture Guide](docs/THEMES_ARCHITECTURE_GUIDE.md)**: Full design tokens, palette hexes, typography, and section specifications for all 5 themes.
* **[Developer & Operations Technical Manual](docs/DEVELOPER_OPERATIONS_GUIDE.md)**: System architecture, RBAC roles, security domains 1–11, operational runbooks, and coding standards.
* **[Theme Development & Settings Guide](docs/HOW_TO_ADD_A_NEW_THEME.md)**: Blueprint for designing new storefront themes and adding custom controls to the Admin Settings page.

---

## 📄 License
This project is open-source software licensed under the [MIT License](LICENSE).
