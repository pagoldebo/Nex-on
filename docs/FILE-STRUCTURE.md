# Lingerie Site — Step-wise File & Folder Structure

Every file listed below maps to the step in which it was built.
Copy paths are relative to `/mnt/user-data/outputs/lingerie-templates/`.

---

## STEP 1 — Buttercup Theme: Components + Index + Product

```
laravel-pages/views/storefront/buttercup/
├── components/
│   ├── header.blade.php              ← Step 1
│   ├── footer.blade.php              ← Step 1
│   ├── tab-bar.blade.php             ← Step 1
│   ├── category-sheet.blade.php      ← Step 1
│   ├── cart-drawer.blade.php         ← Step 1
│   ├── login-modal.blade.php         ← Step 1
│   ├── search-overlay.blade.php      ← Step 1
│   └── tailwind-config.blade.php     ← Step 1
├── partials/
│   └── product-card.blade.php        ← Step 1
├── index.blade.php                   ← Step 1
└── product.blade.php                 ← Step 1
```

---

## STEP 2 — Buttercup Theme: Remaining Pages

```
laravel-pages/views/storefront/buttercup/
├── category.blade.php                ← Step 2
├── cart.blade.php                    ← Step 2
├── account.blade.php                 ← Step 2
├── wishlist.blade.php                ← Step 2
├── checkout.blade.php                ← Step 2
├── order-confirmation.blade.php      ← Step 2
├── search.blade.php                  ← Step 2
└── compare.blade.php                 ← Step 2
```

---

## STEP 3 — Vyra Theme: Components + Index + Product

```
laravel-pages/views/storefront/vyra/
├── components/
│   ├── header.blade.php              ← Step 3
│   ├── footer.blade.php              ← Step 3
│   ├── tab-bar.blade.php             ← Step 3
│   ├── category-sheet.blade.php      ← Step 3
│   ├── cart-drawer.blade.php         ← Step 3
│   ├── login-modal.blade.php         ← Step 3
│   ├── search-overlay.blade.php      ← Step 3
│   └── tailwind-config.blade.php     ← Step 3
├── partials/
│   └── product-card.blade.php        ← Step 3
├── index.blade.php                   ← Step 3
└── product.blade.php                 ← Step 3
```

---

## STEP 4 — Vyra Theme: Remaining Pages

```
laravel-pages/views/storefront/vyra/
├── category.blade.php                ← Step 4
├── cart.blade.php                    ← Step 4
├── account.blade.php                 ← Step 4
├── wishlist.blade.php                ← Step 4
├── checkout.blade.php                ← Step 4
├── order-confirmation.blade.php      ← Step 4
├── search.blade.php                  ← Step 4
└── compare.blade.php                 ← Step 4
```

---

## STEP 5 — Lumière Theme: Components + Index + Product

```
laravel-pages/views/storefront/lumiere/
├── components/
│   ├── header.blade.php              ← Step 5
│   ├── footer.blade.php              ← Step 5
│   ├── tab-bar.blade.php             ← Step 5
│   ├── category-sheet.blade.php      ← Step 5
│   ├── cart-drawer.blade.php         ← Step 5
│   ├── login-modal.blade.php         ← Step 5
│   ├── search-overlay.blade.php      ← Step 5
│   └── tailwind-config.blade.php     ← Step 5
├── partials/
│   ├── product-card.blade.php        ← Step 5  (main grid card)
│   ├── bestseller-card.blade.php     ← Step 5  (homepage best-sellers rail)
│   ├── lustre-card.blade.php         ← Step 5  (featured editorial card)
│   └── quick-view.blade.php          ← Step 5  (quick-view modal partial)
├── index.blade.php                   ← Step 5
└── product.blade.php                 ← Step 5
```

---

## STEP 6 — Lumière Theme: Remaining Pages

```
laravel-pages/views/storefront/lumiere/
├── category.blade.php                ← Step 6
├── cart.blade.php                    ← Step 6
├── account.blade.php                 ← Step 6
├── wishlist.blade.php                ← Step 6
├── checkout.blade.php                ← Step 6
├── order-confirmation.blade.php      ← Step 6
├── search.blade.php                  ← Step 6
└── compare.blade.php                 ← Step 6
```

---

## STEP 7 — Saaya Theme: Components + Index + Product

```
laravel-pages/views/storefront/saaya/
├── components/
│   ├── header.blade.php              ← Step 7a
│   ├── footer.blade.php              ← Step 7a
│   ├── tab-bar.blade.php             ← Step 7a
│   ├── category-sheet.blade.php      ← Step 7a
│   ├── cart-drawer.blade.php         ← Step 7a
│   ├── login-modal.blade.php         ← Step 7a
│   ├── search-overlay.blade.php      ← Step 7a
│   └── tailwind-config.blade.php     ← Step 7a
├── partials/
│   └── product-card.blade.php        ← Step 7b
├── index.blade.php                   ← Step 7b
└── product.blade.php                 ← Step 7c
```

---

## STEP 8 — Saaya Theme: Remaining Pages

```
laravel-pages/views/storefront/saaya/
├── category.blade.php                ← Step 8a
├── cart.blade.php                    ← Step 8b
├── account.blade.php                 ← Step 8b (delivered together)
├── wishlist.blade.php                ← Step 8c
└── checkout.blade.php                ← Step 8c (delivered together)
```

---

## STEP 9 — Shared Infrastructure + Root Layout + Routes

```
laravel-pages/
├── views/components/
│   └── storefront-layout.blade.php   ← Step 9  (root layout, ALL themes extend this)
├── controllers/
│   └── StorefrontController.php      ← Step 9
└── routes/
    └── web.php                       ← Step 9
```

---

## STEP 9 (continued) — Saaya Final Pages

```
laravel-pages/views/storefront/saaya/
├── order-confirmation.blade.php      ← Step 9f
├── search.blade.php                  ← Step 9f
└── compare.blade.php                 ← Step 9f
```

---

## STEP 10 — Backend: Database & Admin

```
laravel-files/
│
├── migrations/
│   ├── 2026_01_01_000000_create_categories_table.php     ← Prior session
│   ├── 2026_01_01_000001_create_products_table.php       ← Prior session
│   ├── 2026_01_01_000002_create_product_colors_table.php ← Prior session
│   ├── 2026_01_01_000003_create_product_variants_table.php ← Prior session
│   ├── 2026_01_01_000004_create_templates_table.php      ← Prior session
│   ├── 2024_01_01_000010_create_orders_table.php         ← Step 10a
│   └── 2024_01_01_000011_create_order_items_table.php    ← Step 10b
│
├── models/
│   ├── Category.php                  ← Prior session
│   ├── Product.php                   ← Prior session  (toCardArray lives here)
│   ├── ProductColor.php              ← Prior session
│   ├── ProductColorImage.php         ← Prior session
│   ├── ProductVariant.php            ← Prior session
│   ├── Template.php                  ← Prior session  (::active() lives here)
│   ├── Order.php                     ← Step 10c
│   └── OrderItem.php                 ← Step 10c
│
├── controllers/
│   ├── ProductController.php         ← Prior session  (admin CRUD + syncColors)
│   └── AdminThemeController.php      ← Step 10d
│
├── database/
│   └── seeders/
│       └── TemplateSeeder.php        ← Step 10f
│
├── views/
│   └── admin/
│       └── themes.blade.php          ← Step 10e
│
└── patches/
    └── wishlist-guest-redirect-backport.md  ← Step 10f
```

---

## Standalone HTML Previews (static demos, not Laravel files)

```
template-1-saaya/
└── index.html                        ← Early prototype / standalone preview

template-2-buttercup/
└── index.html

template-3-vyra/
└── index.html

template-4-lumiere/
└── index.html
```

---

## Documentation

```
DOCUMENTATION.md                      ← Full developer reference (DB schema, variables,
                                         page-by-page guide, routes, design tokens)
FILE-STRUCTURE.md                     ← This file
laravel-files/README.md               ← Backend files quick-start guide
LARAVEL_BACKEND_GUIDE.md              ← Extended backend reference
PRODUCT_VARIANTS_GUIDE.md             ← Product / color / variant data model deep-dive
```

---

## Complete Counts

| Category                       | Count |
|-------------------------------|-------|
| Storefront pages (4 × 10)     | 40    |
| Theme components (4 × 8)      | 32    |
| Product-card partials          | 7     |
| Root layout                    | 1     |
| Migrations                     | 7     |
| Eloquent models                | 8     |
| Controllers                    | 2     |
| Seeders                        | 1     |
| Admin views                    | 1     |
| Routes file                    | 1     |
| Patch / guide files            | 5     |
| **Total files**                | **105** |

---

## Where Each File Lives in Your Laravel Project

```
your-laravel-app/
│
├── app/Http/Controllers/
│   ├── StorefrontController.php          ← laravel-pages/controllers/
│   └── Admin/AdminThemeController.php    ← laravel-files/controllers/
│
├── app/Models/
│   ├── Category.php                      ← laravel-files/models/
│   ├── Product.php
│   ├── ProductColor.php
│   ├── ProductColorImage.php
│   ├── ProductVariant.php
│   ├── Template.php
│   ├── Order.php
│   └── OrderItem.php
│
├── database/migrations/                  ← laravel-files/migrations/
├── database/seeders/                     ← laravel-files/database/seeders/
│
├── resources/views/
│   ├── components/
│   │   └── storefront-layout.blade.php   ← laravel-pages/views/components/
│   ├── storefront/
│   │   ├── saaya/                        ← laravel-pages/views/storefront/saaya/
│   │   ├── buttercup/                    ← laravel-pages/views/storefront/buttercup/
│   │   ├── vyra/                         ← laravel-pages/views/storefront/vyra/
│   │   └── lumiere/                      ← laravel-pages/views/storefront/lumiere/
│   └── admin/
│       └── themes.blade.php              ← laravel-files/views/admin/
│
└── routes/
    └── web.php                           ← laravel-pages/routes/
```
