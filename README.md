# Nex-on — Multi-Theme E-Commerce & Retail ERP

[![Laravel 12](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP 8.2+](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind-CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![Alpine.js](https://img.shields.io/badge/Alpine.js-3.x-8BC0D0?style=for-the-badge&logo=alpine.js&logoColor=white)](https://alpinejs.dev)
[![Tests Passing](https://img.shields.io/badge/Tests-411%20Passed-brightgreen?style=for-the-badge)](tests)

**Nex-on** is an enterprise-grade, multi-theme e-commerce and retail ERP platform built on **Laravel 12**. It pairs five distinct, high-fashion storefront design systems with a full-suite omnichannel back-office (inventory, POS, warehouse batch tracking, GST accounting, HRM, and patron security).

---

## 🌟 Key Features

### 1. Five Interchangeable Storefront Themes
Switch between five fully realized lifestyle brand aesthetics in one click without altering database records or business logic:
* **Vyra**: High-impact modern noir & streetwear aesthetic (dark mode `#0E0E10`, electric coral `#FF3D5A`, lime `#D4FF3D`, video hero sliders).
* **Buttercup**: Cloud-soft, pastel comfort intimates (blush `#FBE7E4`, rose, sage `#A8B89A`).
* **Saaya**: Royal Indian trousseau & heritage silk (deep burgundy `#4A0E17`, antique gold `#D4AF37`, silk cream `#fdfbf7`).
* **Lumière**: Minimalist, French-inspired pure silk atelier (`#FAFAFA`, deep pine `#405549`, editorial typography).
* **Verdia**: Botanical luxury & clean living marketplace (signature cream `#F9F8F3`, forest sage `#699E4B`, sunlit amber `#F1B927`).

### 2. Patron Security & Two-Factor Email OTP
* **Email Verification OTP**: Secure 4-digit numeric code dispatched on every sign-in attempt.
* **OTP Verification View**: Set against the `#F9F8F3` background with a solid jet-black (`#000000`) Maison Privilege card.
* **Auto-Submit & Resend Cooldown**: Auto-submits on 4 digits typed/pasted, paired with a 60-second AJAX resend timer.
* **Responsive HTML Mail Theme**: Polished email template with prominent code block, expiration countdown, and security warnings. Preview available at `/preview-mail/otp`.

### 3. Next-Gen AVIF Image Conversion & Direct Public Media
* Automatic conversion transforms uploaded product, category, and banner images into next-generation **`.avif`** format (PHP 8.5 native `imageavif` with alpha transparency, quality 75, speed 6), delivering up to 50% smaller payloads than WebP and 80% smaller than JPEG.
* Client-side canvas converter (`webp-converter.js`) auto-detects browser AVIF encoding support before upload to optimize client-side where possible.
* Direct `public/images/` storage architecture eliminates symlink dependencies (`php artisan storage:link` is obsolete) and features `.htaccess` execution protection.
* Strict brand logo retention: The store logo is preserved directly as `branding/logo.png` (or `.svg`), never converted or hashed. Vector SVGs remain intact with strict SVG sanitization.

### 4. Hierarchical Category Management
* Multi-level parent-child tree navigation with accordion auto-collapse in the admin dashboard.
* Storefront filter tabs automatically include ancestor and subcategory products for seamless browsing.

### 5. Omnichannel Retail & Point of Sale (POS)
* Shift management, cash float tracking, drawer opening/closing.
* Pure-PHP Code128 thermal barcode label printing (vector SVG generation).

### 6. Multi-Outlet Warehousing & Batch Tracking
* Outlets and inter-branch stock transfers.
* FIFO batch allocation, expiration date tracking, and recall traceability.

### 7. Procurement & Commercial GST Invoicing
* Supplier directory, Purchase Orders (PO), and 3-way invoice matching.
* B2B Commercial Invoices with automatic CGST, SGST, and IGST tax splits.
* One-click Tally ERP XML accounting voucher export.

### 8. HRM, Staff Performance & Payroll
* Employee directory, department allocation, and shift management.
* Daily clock-in/out attendance tracking and monthly salary slip generation.
* 360-degree peer feedback wall and quarterly OKR / appraisal scoring matrices (30/50/20 composite).

### 9. Customer Support & Growth Marketing
* Support ticket helpdesk with SLA tracking, private agent notes, and customer replies.
* Knowledge Base article publisher and storefront FAQ builder.
* Multi-SMS gateway drivers (Twilio, MSG91, Fast2SMS, Log driver).
* Abandoned cart automated recovery drip campaigns.
* Tiered VIP Loyalty Rewards program (welcome points, point accrual, and tier upgrades).

---

## 🛠 Tech Stack

* **Backend Framework**: Laravel 12 (PHP 8.2+)
* **Database**: MySQL 8.x / MariaDB
* **Frontend**: Blade Components, Tailwind CSS, Alpine.js, HTML5 Canvas
* **Testing**: PHPUnit / Pest (411 tests, 2,245 assertions)
* **Auth**: Custom Patron Auth with Two-Factor Email OTP
* **PWA**: Web App Manifest & service worker readiness

---

## 🚀 Getting Started

### Prerequisites
* PHP >= 8.2 with `pdo_mysql`, `mbstring`, `openssl`, `gd` / `imagick` extensions.
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

5. **Run Migrations & Seeders**:
   ```bash
   php artisan migrate --seed
   ```

6. **Direct Media Assets (No Symlink Needed)**:
   Media assets (`banners/`, `branding/`, `categories/`, `products/`, `slider/`, `themes/`) are stored directly inside `public/images/`. No symbolic link (`php artisan storage:link`) is needed.

7. **Start Development Server** (if not using Apache/XAMPP):
   ```bash
   php artisan serve
   ```

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

Nex-on includes a comprehensive test suite covering all storefront themes, authentication, ERP workflows, and APIs:

```bash
php artisan test
```

To run only the Email OTP authentication tests:
```bash
php artisan test --filter=LoginEmailOtpTest
```

---

## 📁 Key Directory Structure

```
Nexon/
├── app/
│   ├── Http/Controllers/     # Storefront, Admin, POS, HRM, Invoicing Controllers
│   ├── Models/               # Eloquent Models (Product, Order, Batch, User, etc.)
│   ├── Notifications/        # TwoFactorOTP and System Notifications
│   └── Services/             # BarcodeService, SmsService, RecommendationService
├── database/
│   ├── migrations/           # Database Schema Migrations (52 migrations)
│   └── seeders/              # Initial Seeders (Admin, Categories, Products, Themes)
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
    └── Feature/              # 411 Automated Feature & Integration Tests (2,245 assertions)
```

---

## 📚 Engineering & Operations Documentation
* **[Storefront Themes Architecture Guide](docs/THEMES_ARCHITECTURE_GUIDE.md)**: Full design tokens, palette hexes, typography, and section specifications for all 5 themes.
* **[Developer & Operations Technical Manual](docs/DEVELOPER_OPERATIONS_GUIDE.md)**: System architecture, RBAC roles, security domains 1–11, operational runbooks, and coding standards.
* **[Theme Development & Settings Guide](docs/HOW_TO_ADD_A_NEW_THEME.md)**: Blueprint for designing new storefront themes and adding custom controls to the Admin Settings page.

---

## 📄 License
This project is open-source software licensed under the [MIT License](LICENSE).

