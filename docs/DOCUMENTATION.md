# Lingerie Site — Full Developer Documentation

> Indian multi-theme lingerie e-commerce platform  
> Stack: **Laravel 8.74+ · MySQL · Blade · Tailwind CSS · Alpine.js · Vanilla JS**

---

## Table of Contents

1. [Project Overview](#1-project-overview)
2. [Repository Structure](#2-repository-structure)
3. [Database Schema](#3-database-schema)
4. [Theme System](#4-theme-system)
5. [Blade Architecture](#5-blade-architecture)
6. [Product Data Model & toCardArray()](#6-product-data-model--tocardarray)
7. [Client-Side State (Cart · Wishlist · Compare)](#7-client-side-state)
8. [Page-by-Page Reference](#8-page-by-page-reference)
9. [Backend Files Reference](#9-backend-files-reference)
10. [Routes Cheatsheet](#10-routes-cheatsheet)
11. [Design Tokens per Theme](#11-design-tokens-per-theme)
12. [How to Add a New Theme](#12-how-to-add-a-new-theme)
13. [Deployment Checklist](#13-deployment-checklist)

---

## 1. Project Overview

A single Laravel application that serves **four completely independent storefront themes** from one codebase. Switching themes in production requires **no code change and no redeployment** — one database row controls which theme is live.

### The Four Themes

| Key        | Name      | Personality                        | Primary Font          | Palette               |
|------------|-----------|------------------------------------|-----------------------|-----------------------|
| `saaya`    | Saaya     | Bridal / artisanal        | Cormorant Garamond    | Maroon · Gold · Ivory |
| `buttercup`| Buttercup | Soft pastels / everyday wear       | Fraunces              | Rose · Sage · Cream   |
| `vyra`     | Vyra      | Bold Gen-Z / streetwear            | Archivo Black         | Coral · Lime · Noir   |
| `lumiere`  | Lumière   | Premium boutique / French-inspired | Playfair Display      | Pink · Cream · Gold   |

### Each theme ships with

- 10 storefront pages (index, product, category, cart, account, wishlist, checkout, order-confirmation, search, compare)
- 8 shared components (header, footer, tab-bar, category-sheet, cart-drawer, login-modal, search-overlay, token-injector)
- 1 product-card partial

---

## 2. Repository Structure

```
app/
├── Http/
│   └── Controllers/
│       ├── StorefrontController.php       # Routes all public pages → active theme view
│       ├── Admin/
│       │   └── AdminThemeController.php   # GET/POST /admin/themes
│       └── WishlistController.php         # Handles ?ids= param for guest wishlists
├── Models/
│   ├── Template.php                       # Themes table; ::active() returns active row
│   ├── Product.php                        # toCardArray() lives here
│   ├── ProductColor.php                   # One row per color variant of a product
│   ├── ProductVariant.php                 # One row per size within a color
│   ├── Category.php
│   ├── Order.php                          # createFromCheckout(), status constants
│   └── OrderItem.php                      # Snapshot columns + accessors

database/
├── migrations/
│   ├── ..._create_templates_table.php
│   ├── ..._create_categories_table.php
│   ├── ..._create_products_table.php
│   ├── ..._create_product_colors_table.php
│   ├── ..._create_product_variants_table.php
│   ├── 2024_01_01_000010_create_orders_table.php
│   └── 2024_01_01_000011_create_order_items_table.php
└── seeders/
    └── TemplateSeeder.php                 # Seeds 4 theme rows; saaya is_active=true

resources/views/
├── layouts/
│   └── storefront-layout.blade.php        # Root shell used by ALL themes
├── storefront/
│   ├── saaya/
│   │   ├── components/                    # x-saaya-* Blade components
│   │   ├── partials/
│   │   │   └── product-card.blade.php
│   │   ├── index.blade.php
│   │   ├── product.blade.php
│   │   ├── category.blade.php
│   │   ├── cart.blade.php
│   │   ├── account.blade.php
│   │   ├── wishlist.blade.php
│   │   ├── checkout.blade.php
│   │   ├── order-confirmation.blade.php
│   │   ├── search.blade.php
│   │   └── compare.blade.php
│   ├── buttercup/   (same structure)
│   ├── vyra/        (same structure)
│   └── lumiere/     (same structure)
└── admin/
    └── themes.blade.php                   # Admin theme switcher UI
```

---

## 3. Database Schema

### `templates`

| Column      | Type         | Notes                                      |
|-------------|--------------|--------------------------------------------|
| `id`        | bigint PK    |                                            |
| `key`       | varchar(32)  | Unique. Matches view folder name (`saaya`) |
| `name`      | varchar(100) | Display name (`Saaya`)                     |
| `is_active` | boolean      | **Only one row can be true at a time**     |
| timestamps  |              |                                            |

### `categories`

| Column       | Type         | Notes                              |
|--------------|--------------|------------------------------------|
| `id`         | bigint PK    |                                    |
| `name`       | varchar      | e.g. "Bralettes"                   |
| `slug`       | varchar      | Unique. Used in route model binding|
| `image`      | varchar null | Cover image URL                    |
| timestamps   |              |                                    |

### `products`

| Column           | Type         | Notes                                         |
|------------------|--------------|-----------------------------------------------|
| `id`             | bigint PK    |                                               |
| `category_id`    | FK           | → categories                                  |
| `name`           | varchar      |                                               |
| `slug`           | varchar      | Unique. Route model binding                   |
| `description`    | text null    |                                               |
| `care`           | text null    | Wash care instructions                        |
| `fabric`         | text null    | Material description                          |
| `is_published`   | boolean      | false = draft, not shown on storefront        |
| `is_featured`    | boolean      | Shown in "Best Sellers" / featured sections   |
| timestamps       |              |                                               |

### `product_colors`

| Column       | Type         | Notes                                      |
|--------------|--------------|--------------------------------------------|
| `id`         | bigint PK    |                                            |
| `product_id` | FK           | → products                                 |
| `name`       | varchar      | e.g. "Blush Pink"                          |
| `hex`        | char(7) null | e.g. "#E8C4C0" — shown as swatch           |
| `images`     | json         | Array of up to 8 image URLs                |
| `sort_order` | int          | Display order within product               |
| timestamps   |              |                                            |

### `product_variants`

| Column              | Type            | Notes                                    |
|---------------------|-----------------|------------------------------------------|
| `id`                | bigint PK       |                                          |
| `product_color_id`  | FK              | → product_colors                         |
| `size`              | varchar         | e.g. "S", "M", "34B", "XL"              |
| `price`             | unsignedInteger | Selling price in **paise** (₹1 = 100p)  |
| `compare_at_price`  | unsignedInt null| Original price if on sale               |
| `stock`             | unsignedSmallInt| Current inventory count                 |
| timestamps          |                 |                                          |

> **Key insight:** Red+S=₹499 and Red+L=₹599 are two separate `product_variants` rows under the same `product_color`. There is no flat variant model.

### `orders`

| Column               | Type         | Notes                                              |
|----------------------|--------------|----------------------------------------------------|
| `id`                 | bigint PK    |                                                    |
| `user_id`            | FK null      | Null = guest order                                 |
| `reference`          | varchar(32)  | Unique. Auto-generated: `SAA-YYYYMMDD-0001`        |
| `status`             | varchar(32)  | `pending\|confirmed\|processing\|shipped\|delivered\|cancelled\|refund_requested\|refunded` |
| `subtotal`           | unsignedInt  | Sum of line totals (paise)                         |
| `shipping_charge`    | unsignedInt  | 0 if free (≥₹999), else 99                        |
| `cod_charge`         | unsignedInt  | 0 or 49                                            |
| `discount`           | unsignedInt  | Coupon savings (future use)                        |
| `total`              | unsignedInt  | subtotal + shipping + cod − discount               |
| `payment_method`     | varchar(32)  | `upi\|card\|cod`                                   |
| `payment_status`     | varchar(32)  | `pending\|paid\|failed\|refunded`                  |
| `payment_reference`  | varchar null | Gateway transaction ID                             |
| `shipping_name`      | varchar      | Snapshot of shipping address at order time         |
| `shipping_phone`     | varchar(15)  |                                                    |
| `shipping_email`     | varchar      |                                                    |
| `shipping_address1`  | varchar      |                                                    |
| `shipping_address2`  | varchar null |                                                    |
| `shipping_city`      | varchar      |                                                    |
| `shipping_state`     | varchar      |                                                    |
| `shipping_pincode`   | varchar(10)  |                                                    |
| `tracking_number`    | varchar null |                                                    |
| `courier`            | varchar null |                                                    |
| `shipped_at`         | timestamp null|                                                   |
| `delivered_at`       | timestamp null|                                                   |
| `notes`              | text null    |                                                    |
| timestamps           |              |                                                    |

### `order_items`

| Column               | Type            | Notes                                        |
|----------------------|-----------------|----------------------------------------------|
| `id`                 | bigint PK       |                                              |
| `order_id`           | FK              | → orders (cascade delete)                    |
| `product_id`         | FK null         | Null if product deleted from catalogue       |
| `product_color_id`   | FK null         | Null if color deleted                        |
| `product_variant_id` | FK null         | Null if variant deleted                      |
| `product_name`       | varchar         | **Snapshot** — never changes post-order      |
| `product_slug`       | varchar         | For order-history deep-links                 |
| `color_name`         | varchar         | Snapshot                                     |
| `color_hex`          | char(7) null    | Snapshot                                     |
| `size`               | varchar         | Snapshot                                     |
| `cover_image`        | varchar null    | Snapshot                                     |
| `price`              | unsignedInt     | Unit selling price at purchase (paise)       |
| `compare_at_price`   | unsignedInt null| Original price if on sale (paise)            |
| `qty`                | unsignedSmallInt|                                              |
| `line_total`         | unsignedInt     | price × qty (paise)                          |
| timestamps           |                 |                                              |

> **Why snapshot columns?** If a product is edited or deleted after an order, the order history still shows exactly what the customer bought, at what price, in what colour and size.

---

## 4. Theme System

### How switching works

```
templates table
┌──────────────┬───────────┐
│ key          │ is_active │
├──────────────┼───────────┤
│ saaya        │   TRUE    │  ← active
│ buttercup    │   false   │
│ vyra         │   false   │
│ lumiere      │   false   │
└──────────────┴───────────┘
        ↓
Template::active()          returns the active Template model (cached)
        ↓
StorefrontController        resolves view path:
                            "storefront.{template->key}.{page}"
                            e.g. "storefront.saaya.product"
        ↓
resources/views/storefront/saaya/product.blade.php  ← rendered
```

### `Template::active()`

```php
// Called by StorefrontController on every request
$template = Template::where('is_active', true)->firstOrFail();
// $template->key === 'saaya'
```

### Switching themes (admin)

```
POST /admin/themes/{template}/activate
→ DB::transaction:
    UPDATE templates SET is_active = false   (all rows)
    UPDATE templates SET is_active = true WHERE id = {id}
→ redirect with flash success
```

---

## 5. Blade Architecture

### Root layout: `storefront-layout.blade.php`

All 40 storefront pages (4 themes × 10 pages) extend this single layout via:

```blade
<x-storefront-layout>
  ...page content...
</x-storefront-layout>
```

The layout provides:

| Feature                 | How it works                                               |
|-------------------------|------------------------------------------------------------|
| `<head>` with fonts     | Google Fonts loaded per theme via token injection          |
| Tailwind config         | CSS variables injected as `--color-gold`, `--color-maroon` etc. |
| Global cart state       | `sessionStorage('lc_cart')` parsed on DOMContentLoaded    |
| Cart drawer             | `#cartDrawer` — toggled by `openCart()` / `closeCart()`   |
| Login modal             | `#loginModal` — toggled by `openLoginModal()`             |
| Search overlay          | `#searchOverlay` — toggled by search icon                 |
| Category bottom sheet   | `#categorySheet` — slide-up on mobile                     |
| Bottom tab bar          | Fixed mobile nav with Home / Category / Search / Bag / Account |
| IntersectionObserver    | `.reveal` elements → `.visible` class (fade-up animation) |
| `@stack('scripts')`     | Each page pushes page-specific JS via `@push('scripts')`  |

### Page-specific scripts

Every page that needs custom JS uses:

```blade
@push('scripts')
<script>
(function () {
  // Page JS here — always wrapped in IIFE to avoid global scope pollution
})();
</script>
@endpush
```

### `esc()` helper (all pages with JS templates)

```js
function esc(s) {
  return String(s ?? '').replace(/[&<>"']/g, c =>
    ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c])
  );
}
```

Used in every `innerHTML` template string to prevent XSS.

---

## 6. Product Data Model & toCardArray()

`Product::toCardArray()` is the single normalisation point that converts the 3-level DB hierarchy into a flat JS-friendly shape. **Every storefront page that displays products uses this.**

### Return shape

```js
{
  id:               123,
  name:             "Lotus Silk Bra Set",
  slug:             "lotus-silk-bra-set",
  category:         "Bralettes",
  badge:            "New",        // or "Sale", "Trending", null
  startingPrice:    799,          // lowest price across all variants (paise)
  startingCompareAt:999,          // corresponding compare-at price, or null
  coverImage:       "https://…",  // first image of first color
  totalStock:       42,           // sum of all variant stock
  colors: [
    {
      name:   "Blush Pink",
      hex:    "#E8C4C0",
      images: ["url1", "url2", "url3"],   // up to 8
      sizes: [
        {
          size:           "S",
          price:          799,
          compareAtPrice: 999,
          stock:          5,
          inStock:        true
        },
        { size: "M", price: 799, compareAtPrice: 999, stock: 0, inStock: false },
        { size: "L", price: 849, compareAtPrice: 999, stock: 3, inStock: true  }
      ]
    },
    {
      name:   "Midnight Blue",
      hex:    "#1A237E",
      images: ["url4"],
      sizes:  [ … ]
    }
  ]
}
```

### Where it is used

| Page              | How used                                                            |
|-------------------|---------------------------------------------------------------------|
| `product.blade.php` | `data-product='@json($card)'` on `#pdpPicker` — JS reads it to drive gallery, size pills, price display, Add to Bag |
| `product-card.blade.php` | Passed as `$cardData` — renders cover image, badge, price |
| `category.blade.php` | Each product in the grid includes the product-card partial      |
| `search.blade.php` | Same grid pattern                                                   |
| `wishlist.blade.php` | Same grid pattern                                                 |
| `compare.blade.php` | `$products->each->toCardArray()` for images and badges           |

---

## 7. Client-Side State

### Cart — `sessionStorage('lc_cart')`

**Lives in:** `sessionStorage` (cleared when browser tab closes)

**Shape:**
```js
[
  {
    productId:      123,
    productColorId: 45,
    productVariantId: 78,
    name:           "Lotus Silk Bra Set",
    slug:           "lotus-silk-bra-set",
    colorName:      "Blush Pink",
    colorHex:       "#E8C4C0",
    size:           "M",
    image:          "https://…",
    price:          799,
    compareAtPrice: 999,
    qty:            2
  }
]
```

**Global functions** (defined in storefront-layout):

| Function          | What it does                                    |
|-------------------|-------------------------------------------------|
| `updateCartUI()`  | Re-reads `lc_cart`, updates badge counts, re-renders cart drawer |
| `openCart()`      | Slides open `#cartDrawer`                       |
| `closeCart()`     | Slides closed `#cartDrawer`                     |

**Add to bag flow (product page):**
1. User picks color swatch → JS swaps images, updates `currentColor`
2. User picks size pill → JS sets `currentVariant`, updates price display
3. User clicks "Add to Bag" → item pushed to `lc_cart` array → `updateCartUI()` → `openCart()`

**Cart page hydration:**
```js
bag = JSON.parse(sessionStorage.getItem('lc_cart') || '[]');
// renders items, calculates totals, shows #cartLoaded or #cartEmpty
```

**Delegated event listeners on cart page:**
```js
document.addEventListener('click', e => {
  if (e.target.closest('[data-action="inc"]'))  { /* qty++ */ }
  if (e.target.closest('[data-action="dec"]'))  { /* qty-- or remove if 0 */ }
  if (e.target.closest('[data-action="remove"]')){ /* remove item */ }
});
```

---

### Wishlist — `localStorage('lc_wishlist')`

**Lives in:** `localStorage` (persists across sessions)

**Shape:** `[123, 456, 789]` — array of product IDs (integers)

**Heart button toggle (product card):**
```js
let ids = JSON.parse(localStorage.getItem('lc_wishlist') || '[]');
if (ids.includes(productId)) {
  ids = ids.filter(i => i !== productId);  // remove
} else {
  ids.push(productId);                     // add
}
localStorage.setItem('lc_wishlist', JSON.stringify(ids));
// toggle heart icon fill
```

**Wishlist page — guest redirect:**
Because the wishlist page is server-rendered (needs DB lookups), there's a script at the top of `wishlist.blade.php`:
```js
// Fires ONCE on load if ?ids= is absent
if (!window.location.search.includes('ids=')) {
  var ids = JSON.parse(localStorage.getItem('lc_wishlist') || '[]');
  if (ids.length) {
    window.location.replace('/wishlist?ids=' + ids.join(','));
  }
}
```
The server then does: `Product::whereIn('id', $ids)->get()` and renders the page with real product data.

---

### Compare — `localStorage('lc_compare')`

**Lives in:** `localStorage`

**Shape:** `[123, 456]` — array of product IDs, **max 4**

**Add/remove logic (product card):**
```js
let ids = JSON.parse(localStorage.getItem('lc_compare') || '[]');
if (ids.includes(productId)) {
  ids = ids.filter(i => i !== productId);
} else if (ids.length < 4) {
  ids.push(productId);
} else {
  alert('You can compare up to 4 products.');
  return;
}
localStorage.setItem('lc_compare', JSON.stringify(ids));
```

**Compare page URL:** `/compare?ids=123,456,789`  
Server loads products, renders side-by-side table.

---

### Order — `sessionStorage('lc_last_order')`

**Written by:** checkout page on successful form submission  
**Read by:** order-confirmation page  
**Deleted:** never (survives refresh; user can bookmark confirmation URL)

**Shape:**
```js
{
  id:        "SAA1697183400000",   // 'SAA' + Date.now()
  items:     [ /* same shape as lc_cart items */ ],
  shipping:  {
    name, phone, email, address1, address2, city, state, pincode
  },
  payment:   "upi",               // upi | card | cod
  subtotal:  1598,
  total:     1697,                // subtotal + shipping (99 if <₹999) + cod (49 if cod)
  placedAt:  "2024-01-15T07:30:00.000Z"
}
```

**Order confirmation matching:**
```js
const orderId = new URLSearchParams(location.search).get('order');
const stored  = JSON.parse(sessionStorage.getItem('lc_last_order'));
if (!stored || String(stored.id) !== orderId) {
  // show #ocMissing
} else {
  // show #ocDetails, populate with stored data
}
```

---

## 8. Page-by-Page Reference

### `index.blade.php` — Homepage

**Controller variables:**
| Variable      | Type                  | Description                              |
|---------------|-----------------------|------------------------------------------|
| `$categories` | `Collection<Category>`| All categories — nav + category strip    |
| `$featured`   | `Collection<Product>` | `is_featured=true` products for best-sellers tab |

**Key sections:**
- **Hero** — Full-width maroon header with foil-frame decorative card, CTA
- **Category strip** — 3:4 aspect-ratio cards with overlay gradient
- **Best sellers** — Tab filter by category (`data-filter` attribute) + product grid
- **About** — Brand story with 3 stats
- **Contact form** — Basic HTML form (no server action wired yet)

---

### `product.blade.php` — Product Detail Page (PDP)

**Controller variables:**
| Variable      | Type       | Description                                           |
|---------------|------------|-------------------------------------------------------|
| `$product`    | `Product`  | Eager-loaded with `colors.variants`, `category`       |
| `$card`       | `array`    | `$product->toCardArray()` — consumed by JS picker     |
| `$related`    | `Collection<Product>` | Same category, excluding current product |
| `$categories` | `Collection<Category>` | Layout nav                              |

**Key element IDs:**
| ID              | Role                                          |
|-----------------|-----------------------------------------------|
| `#pdpPicker`    | `data-product='@json($card)'` — JS reads this |
| `#pdpGallery`   | Main image display                            |
| `#pdpThumbs`    | Thumbnail strip                               |
| `#pdpColorWrap` | Color swatch buttons                          |
| `#pdpSizes`     | Size pill buttons                             |
| `#pdpPrice`     | Live price display                            |
| `#pdpSavings`   | Savings badge (hidden when no discount)       |
| `#pdpAddBtn`    | Add to Bag button                             |
| `#pdpWishBtn`   | Save to Wishlist button (heart)               |
| `#sizeGuideModal` | Modal with size chart table                 |

---

### `category.blade.php` — Category Listing

**Controller variables:**
| Variable        | Type                    | Description                       |
|-----------------|-------------------------|-----------------------------------|
| `$category`     | `Category`              | Current category (route model binding on slug) |
| `$products`     | `LengthAwarePaginator`  | Paginated products in this category |
| `$categories`   | `Collection<Category>`  | Layout nav                        |
| `$sortOptions`  | `array`                 | `['default'=>'Featured', 'price_asc'=>'Price: Low to High', …]` |
| `$currentSort`  | `string`                | Currently selected sort key       |

**Sort mechanism:** JS catches `<select>` change → updates `?sort=` in URL → full page reload (no Ajax).

---

### `cart.blade.php` — Cart Page

**Controller variables:**
| Variable      | Type                   | Description  |
|---------------|------------------------|--------------|
| `$categories` | `Collection<Category>` | Layout nav   |

**No `$products` variable** — cart is 100% client-side, hydrated from `sessionStorage('lc_cart')`.

**Element IDs:**
| ID              | Role                                               |
|-----------------|----------------------------------------------------|
| `#cartEmpty`    | Shown when `lc_cart` is empty                      |
| `#cartLoaded`   | Shown when cart has items                          |
| `#cartItems`    | Container where item rows are injected             |
| `#cartSubtotal` | Subtotal display                                   |
| `#cartShipping` | "Free" or "₹99" shipping display                  |
| `#cartNudge`    | "Spend ₹X more for free shipping" banner          |
| `#cartTotal`    | Final total display                                |

**Thresholds:**
- Free shipping: `subtotal >= 999` (₹9.99 in paise, stored as integer ₹)
- COD charge: ₹49 (added at checkout, not on cart page)

---

### `account.blade.php` — My Account

**Controller variables:**
| Variable      | Type                   | Description                 |
|---------------|------------------------|-----------------------------|
| `$user`       | `User`                 | `Auth::user()`              |
| `$orders`     | `Collection<Order>`    | User's order history        |
| `$categories` | `Collection<Category>` | Layout nav                  |

**Tabs:** Orders · Wishlist · Profile · Addresses  
**Tab persistence:** `sessionStorage('saa_acc_tab')` — survives page reload within session  
**Wishlist tab:** Makes a `fetch()` to `route('wishlist')+'?ids=<ids>'` and renders product cards inline

**Forms:**
- Profile → `POST route('account.update')` with `@csrf`
- Addresses → `POST route('account.address.update')` with `@csrf`
- Logout → `POST route('logout')` with `@csrf @method('POST')`

---

### `wishlist.blade.php` — Wishlist

**Controller variables:**
| Variable      | Type                    | Description                                  |
|---------------|-------------------------|----------------------------------------------|
| `$products`   | `Collection<Product>`   | Products matching `?ids=` param; empty if none |
| `$categories` | `Collection<Category>`  | Layout nav                                   |

**Guest redirect script** runs before any HTML — see [Section 7](#wishlist----localstoragelc_wishlist).

**Remove button:** `.wish-remove[data-product-id]` → updates `localStorage` + `history.replaceState` → removes card from DOM without page reload. Reloads only when last item removed (to show empty state).

---

### `checkout.blade.php` — Checkout

**Controller variables:**
| Variable      | Type                   | Description |
|---------------|------------------------|-------------|
| `$categories` | `Collection<Category>` | Layout nav  |

**Form fields and validation:**

| Field name  | Type  | Validation                              |
|-------------|-------|-----------------------------------------|
| `name`      | text  | Required                                |
| `phone`     | tel   | Required, pattern `[6-9][0-9]{9}` (Indian mobile) |
| `email`     | email | Required                                |
| `address1`  | text  | Required                                |
| `address2`  | text  | Optional                                |
| `city`      | text  | Required                                |
| `state`     | text  | Required                                |
| `pincode`   | text  | Required, pattern `[0-9]{6}`            |
| `payment`   | radio | `upi` \| `card` \| `cod`; default `upi` |

**CSS radio dot trick:**
```css
input[name="payment"]:checked + div .pay-dot {
  opacity: 1;
  transform: scale(1);
}
```

**On submit:**
1. Client-side validation (all required fields + patterns)
2. Builds `order` object → saves to `lc_last_order` in sessionStorage
3. Clears `lc_cart` (sets to `[]`)
4. Calls `updateCartUI()` to reset badge count
5. Redirects to `route('order.confirmation')+'?order='+order.id`

**Free shipping threshold:** ₹999  
**COD charge:** ₹49 (shown as separate line when selected)

---

### `order-confirmation.blade.php` — Order Confirmation

**Controller variables:**
| Variable      | Type                   | Description |
|---------------|------------------------|-------------|
| `$categories` | `Collection<Category>` | Layout nav  |

**No server-side order data** — reads `lc_last_order` from `sessionStorage`.

**Match logic:**
```js
const orderId = new URLSearchParams(location.search).get('order');
const stored  = JSON.parse(sessionStorage.getItem('lc_last_order') || 'null');
// Show #ocDetails if IDs match, else show #ocMissing
```

**Displayed data:** order ID, placed date, items list, subtotal/shipping/total, shipping address, payment method, personalised "Thank you, {firstName}!" heading.

---

### `search.blade.php` — Search Results

**Controller variables:**
| Variable       | Type                    | Description                                     |
|----------------|-------------------------|-------------------------------------------------|
| `$q`           | `string`                | Search query (empty string if no query)         |
| `$products`    | `LengthAwarePaginator`  | Results; empty paginator if no query            |
| `$categories`  | `Collection<Category>`  | Layout nav + category suggestions               |
| `$sortOptions` | `array`                 | Same as category page                           |
| `$currentSort` | `string`                | Current sort key                                |

**Three render states:**

| State   | Condition             | Shows                                    |
|---------|-----------------------|------------------------------------------|
| No query | `!$q`                | Popular search pills + category grid     |
| Results  | `$q && $products->count()` | Sort bar + product grid + pagination |
| No results | `$q && !$products->count()` | Suggestion pills + category tiles |

**Important:** Search input uses `id="srchInput"` (not `id="searchInput"`) to avoid DOM collision with the global search overlay, which already uses `id="searchInput"`.

---

### `compare.blade.php` — Product Comparison

**Controller variables:**
| Variable      | Type                   | Description                                        |
|---------------|------------------------|----------------------------------------------------|
| `$products`   | `Collection<Product>`  | Up to 4 products from `?ids=` param               |
| `$categories` | `Collection<Category>` | Layout nav                                         |

**Minimum 2 products** required — shows empty state if `$products->count() < 2`.

**Sticky label column:**
```css
.cmp-label-col {
  position: sticky;
  left: 0;
  background: #F7F1E6; /* must match page background */
  z-index: 10;
  min-width: 130px;
}
```

**Attribute rows** are defined as PHP closures:
```php
$attrs = [
  'Price'      => fn($p) => '₹' . number_format($p->startingPrice, 0),
  'Category'   => fn($p) => $p->category?->name ?? '—',
  'Available In' => fn($p) => $p->colors->pluck('name')->join(', '),
  'Sizes'      => fn($p) => $p->colors->first()?->variants->pluck('size')->unique()->join(', '),
  'In Stock'   => fn($p) => $p->colors->flatMap->variants->sum('stock') > 0 ? 'Yes' : 'Out of stock',
];
```

**Remove product link** rebuilds `?ids=` without that product:
```blade
route('compare') . '?ids=' . $products->pluck('id')->reject(fn($id) => $id == $p->id)->join(',')
```

---

## 9. Backend Files Reference

### `Order.php` model

| Method / Property      | Description                                                  |
|------------------------|--------------------------------------------------------------|
| `STATUS_*` constants   | `PENDING`, `CONFIRMED`, `PROCESSING`, `SHIPPED`, `DELIVERED`, `CANCELLED`, `REFUND_REQ`, `REFUNDED` |
| `PAYMENT_*` constants  | `PENDING`, `PAID`, `FAILED`, `REFUNDED`                     |
| `booted()`             | Auto-generates `reference` on create (`SAA-YYYYMMDD-0001`)  |
| `generateReference()`  | Static. Counts today's orders + 1, zero-pads to 4 digits    |
| `user()`               | BelongsTo User                                               |
| `items()`              | HasMany OrderItem                                            |
| `formatted_subtotal`   | Accessor. Returns `"₹1,499"`                                 |
| `formatted_total`      | Accessor. Returns `"₹1,648"`                                 |
| `status_label`         | Accessor. Human-readable status string                       |
| `status_color`         | Accessor. Tailwind class pair for badge (e.g. `"bg-green-50 text-green-700"`) |
| `shipping_address`     | Accessor. Single-line address string                         |
| `isCod()`              | `payment_method === 'cod'`                                   |
| `isPaid()`             | `payment_status === 'paid'`                                  |
| `isDelivered()`        | `status === 'delivered'`                                     |
| `isCancellable()`      | `status` is `pending` or `confirmed`                         |
| `createFromCheckout(array $data, ?int $userId)` | Creates Order + OrderItems from JS checkout payload in one call |

### `OrderItem.php` model

| Method / Property    | Description                                         |
|----------------------|-----------------------------------------------------|
| `order()`            | BelongsTo Order                                     |
| `product()`          | BelongsTo Product (nullable)                        |
| `productColor()`     | BelongsTo ProductColor (nullable)                   |
| `productVariant()`   | BelongsTo ProductVariant (nullable)                 |
| `formatted_price`    | Accessor. `"₹799"`                                  |
| `formatted_line_total` | Accessor. `"₹1,598"`                              |
| `saving`             | Accessor. Per-unit saving vs `compare_at_price`; null if no discount |
| `isOnSale()`         | `saving !== null`                                   |
| `summary`            | Accessor. `"Lotus Silk Bra — Blush Pink, M × 2"`   |

### `AdminThemeController.php`

| Method       | Route                              | Description                               |
|--------------|------------------------------------|-------------------------------------------|
| `index()`    | `GET /admin/themes`                | Lists all templates, renders `admin.themes` |
| `activate()` | `POST /admin/themes/{template}/activate` | Deactivates all, activates chosen one via DB transaction |

### `TemplateSeeder.php`

Seeds 4 rows using `upsert` on `key` — safe to re-run.  
Default active theme: **saaya**.

```bash
php artisan db:seed --class=TemplateSeeder
```

---

## 10. Routes Cheatsheet

```php
// Storefront (public)
Route::get('/',                          [StorefrontController::class, 'index'])->name('home');
Route::get('/products/{product:slug}',   [StorefrontController::class, 'product'])->name('product.show');
Route::get('/category/{category:slug}',  [StorefrontController::class, 'category'])->name('category.show');
Route::get('/cart',                      [StorefrontController::class, 'cart'])->name('cart');
Route::get('/checkout',                  [StorefrontController::class, 'checkout'])->name('checkout');
Route::get('/order-confirmation',        [StorefrontController::class, 'orderConfirmation'])->name('order.confirmation');
Route::get('/search',                    [StorefrontController::class, 'search'])->name('search');
Route::get('/compare',                   [StorefrontController::class, 'compare'])->name('compare');
Route::get('/wishlist',                  [StorefrontController::class, 'wishlist'])->name('wishlist');

// Auth (public)
Route::get('/account',                   [AccountController::class, 'show'])->name('account')->middleware('auth');
Route::post('/account/update',           [AccountController::class, 'update'])->name('account.update')->middleware('auth');
Route::get('/register',                  [AuthController::class, 'register'])->name('register');
Route::post('/logout',                   [AuthController::class, 'logout'])->name('logout');

// Admin (auth:admin middleware)
Route::prefix('admin')->middleware('auth:admin')->group(function () {
    Route::get('/themes',                         [AdminThemeController::class, 'index'])->name('admin.themes.index');
    Route::post('/themes/{template}/activate',    [AdminThemeController::class, 'activate'])->name('admin.themes.activate');
});
```

---

## 11. Design Tokens per Theme

### Saaya

```css
--color-maroon:    #3D0C11;
--color-gold:      #C9A24B;
--color-gold-dark: #A07830;
--color-ivory:     #F7F1E6;
--color-blush:     #E8C4C0;
font-display: 'Cormorant Garamond', serif;
font-body:    'Jost', sans-serif;
```

### Buttercup

```css
--color-charcoal:   #2C2C2C;
--color-buttercup:  #F5C518;
--color-rose:       #F4A5A5;
--color-sage:       #8FAF8A;
--color-cream:      #FAF6F0;
font-display: 'Fraunces', serif;
font-body:    'Inter', sans-serif;
```

### Vyra

```css
--color-navy:   #1A1A2E;
--color-crimson:#E94560;
--color-lime:   #B5E550;
--color-ivory:  #F5F0E8;
font-display: 'Archivo Black', sans-serif;
font-body:    'Archivo', sans-serif;
```

### Lumière

```css
--color-noir:     #0F0F0F;
--color-gold:     #D4AF37;
--color-champagne:#F7E7CE;
--color-pink:     #F2C4CE;
font-display: 'IM Fell English', serif;
font-body:    'Cormorant Garamond', serif;
```

---

## 12. How to Add a New Theme

1. **Add a DB row** — insert into `templates` (`key='mytheme'`, `name='My Theme'`, `is_active=false`)
2. **Create view folder** — `resources/views/storefront/mytheme/`
3. **Copy page stubs** from any existing theme, then restyle
4. **Add design tokens** — update `storefront-layout.blade.php` token-injector for the new key
5. **Update `AdminThemeController` preview map** (optional — just for the admin swatch card)
6. **Activate** — via `/admin/themes` UI or:
   ```sql
   UPDATE templates SET is_active = 0;
   UPDATE templates SET is_active = 1 WHERE key = 'mytheme';
   ```

No route changes, no controller changes, no deployment required.

---

## 13. Deployment Checklist

```bash
# 1. Run migrations
php artisan migrate

# 2. Seed themes (safe to re-run)
php artisan db:seed --class=TemplateSeeder

# 3. Clear caches
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 4. Verify active theme
php artisan tinker --execute="echo App\Models\Template::where('is_active',true)->first()->key;"

# 5. Media assets (banners, branding, categories, products, slider)
# Stored directly in public/images/ — no storage symlink needed
```

**Environment variables to set:**
```
APP_ENV=production
APP_KEY=         # php artisan key:generate
DB_HOST=
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=
```

---

## 14. Media Storage Architecture & AVIF Image Pipeline

### Direct Media Storage (`public/images/`)
All public static media assets are consolidated directly under `public/images/`. The framework does not rely on symbolic links (`php artisan storage:link` is obsolete).
The public disk in `config/filesystems.php` points to `public_path('images')`:
```php
'public' => [
    'driver' => 'local',
    'root' => public_path('images'),
    'url' => env('APP_URL').'/images',
    'visibility' => 'public',
    'throw' => false,
],
'links' => [],
```

#### Directory Layout
```
public/images/
├── .htaccess                     # Script execution protection (disallows .php, allows media MIME types)
├── banners/                      # Promotional & category hero banners ({hash}.avif)
├── branding/                     # Store branding assets (fixed logo: branding/logo.png or logo.svg)
├── categories/                   # Category thumbnails ({hash}.avif)
├── products/
│   ├── {main_category}/          # Clean SEO hierarchy ({name}-{sku}-{random6}.avif)
│   └── ...                       # Top-level demo images
├── slider/
│   ├── buttercup/                # Buttercup carousel slides
│   └── vyra/                     # Vyra neon hero video/poster
└── themes/
    └── organic/                  # Organic storefront theme assets (banner-*.jpg, icons)
```

### Next-Gen AVIF Image Conversion Pipeline
The system automatically converts raster image uploads (JPEG, PNG, WebP) into next-generation **`.avif`** format via `App\Services\ProductImageService`:
* **Encoding Engine:** Uses PHP 8.5 native `imageavif($image, null, 75, 6)` (Quality 75, Speed 6) with full alpha channel transparency (`imagesavealpha`).
* **Magic Byte Validation:** AVIF files are verified via bytes 4–12 (`ftypavif` or `ftypavis`).
* **Vector File Safety:** Vector SVGs (`.svg`) are never converted to raster images. They are sanitized via `HtmlSanitizerService` to eliminate embedded scripts and saved as `.svg`.
* **Client-Side Optimization (`public/js/webp-converter.js`):** Feature-detects browser canvas AVIF encoding support (`detectAvifSupport()`). Converts images client-side before form submission to eliminate server CPU compression latency.

### Dedicated Brand Logo Handling
Store logo uploads (`AdminSettingController@update`) are exempt from random hashing and AVIF conversion:
* Always saved strictly as `branding/logo.png` (or `branding/logo.svg` for SVGs).
* Storefront headers and invoices render the logo via `Setting::siteLogoUrl()`, resolving `asset('images/branding/logo.png')` with automatic fallback.

### Terminal Commands
* **Fetch or Generate Admin 2FA OTP:**
  ```bash
  php artisan admin:otp
  # Or for a specific email:
  php artisan admin:otp admin@nexon.app
  ```

---

*Generated for the Lingerie Site project — multi-theme Indian e-commerce storefront.*
