# Nex-on: Storefront Themes Architecture Guide

> **Authoritative Technical Specification for All 5 Interchangeable Storefront Themes**  
> **Platform Version:** Nex-on Enterprise Multi-Theme E-Commerce  
> **Directory Root:** `resources/views/storefront/`

---

## 1. Multi-Theme Comparison Matrix

Nex-on includes five fully realized storefront brand themes. Any theme can be activated with a single click in `/admin/themes` or previewed live using `?theme={key}` without altering database records, inventory, or cart business logic.

| Theme Key | Brand Name | Archetype & Market Position | Primary Palette & Accents | Typography Pairing | Signature Hero Feature |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **`vyra`** | **Vyra** | High-impact modern noir, streetwear & cyber aesthetics | Ink `#0E0E10`<br>Coral `#FF3D5A`<br>Lime `#D4FF3D`<br>Off-White `#F2F0EC` | **Display:** Archivo Black<br>**Body:** Space Grotesk | Autoplaying MP4 video background with 3-slide text carousel |
| **`buttercup`** | **Buttercup** | Cloud-soft, pastel comfort intimates & loungewear | Blush `#FBE7E4`<br>Rose `#D89AA0`<br>Cream `#FFFAF5`<br>Sage `#A8B89A` | **Display:** Fraunces (Serif)<br>**Body:** Quicksand (Sans) | 5-slide full-width background image slider with 3s autoplay |
| **`saaya`** | **Saaya** | Royal Indian trousseau, heritage silk & velvet couture | Maroon `#3D0C11`<br>Gold `#C9A24B`<br>Ivory `#F7F1E6`<br>Blush `#E8C4C0` | **Display:** Cormorant Garamond<br>**Body:** Jost | Deep velvet maroon canvas with gold foil-frame reveal |
| **`lumiere`** | **Lumière** | Minimalist French pure silk atelier & editorial luxury | Silk Pink `#DB2777`<br>Deep Pine `#405549`<br>Cream `#FFFAF5`<br>Butter `#FFE6A5` | **Display:** Playfair Display<br>**Body:** Plus Jakarta Sans | Warm sunlight gold canvas with centered category slider |
| **`organic`** | **Verdia** | Botanical luxury, clean living & farm-to-table marketplace | Forest Sage `#699E4B`<br>Sunlit Amber `#F1B927`<br>Forest Olive `#2A4B1A`<br>Cream `#F9F8F3` | **Display:** Playfair Display<br>**Body:** Nunito | Textured linen banner with certified organic badge |

---

## 2. Directory & Component Contracts

Every theme lives in its own directory under `resources/views/storefront/{key}/` and strictly conforms to this component blueprint:

```
resources/views/storefront/{theme_key}/
├── components/
│   ├── header.blade.php              # Sticky navigation, logo, announcement bar, cart button
│   ├── footer.blade.php              # Multi-column directory, newsletter form, badges
│   ├── tab-bar.blade.php             # Mobile bottom sticky navigation bar (5 quick actions)
│   ├── category-sheet.blade.php      # Mobile bottom drawer for instant category exploration
│   ├── cart-drawer.blade.php         # Slide-out mini bag / cart drawer with live subtotal
│   ├── login-modal.blade.php         # Fast customer authentication modal
│   ├── search-overlay.blade.php      # Fullscreen AJAX live search flyout with instant results
│   └── tailwind-config.blade.php     # Design tokens, custom hex values & typography styles
├── partials/
│   ├── quick-view.blade.php          # Interactive product modal with swatch & size selectors
│   └── (card partials).blade.php     # Reusable product cards (e.g. sale-card, lustre-card)
├── index.blade.php                   # Master homepage with hero, carousels & social proof
├── product.blade.php                 # Single product detail, swatches, pincode checker & reviews
├── category.blade.php                # Category product catalog with subcategory filter pills
├── cart.blade.php                    # Full shopping bag page with order summary
├── checkout.blade.php                # Shipping address form & payment gateway integration
├── account.blade.php                 # Customer portal (orders, saved addresses, password update)
├── wishlist.blade.php                # Customer saved wishlist pieces
├── compare.blade.php                 # Side-by-side product attribute comparison matrix
├── contact.blade.php                 # Concierge styling desk & contact form
├── about.blade.php                   # Atelier heritage story, craftsmanship & sustainability pillars
├── search.blade.php                  # Product search results grid
└── order-confirmation.blade.php      # Order receipt, tracking summary & print invoice
```

---

## 3. Theme-by-Theme Detailed Specifications

### 1. Vyra (`vyra`)
* **Core Philosophy**: High-energy, bold, rebellious modern fashion.
* **Master Layout**: `<x-storefront-layout :theme="$theme" title="...">`
* **Custom Config**: `resources/views/storefront/vyra/components/tailwind-config.blade.php`
* **Dynamic Settings Keys**:
  - `vyra_hero_image` / `hero_banner_image`: Poster fallback image.
  - `vyra_hero_video`: MP4 video source for hero background.
  - `vyra_hero_badge`, `vyra_hero_title`, `vyra_hero_subtitle`: Hero text overlays.
  - `vyra_story_image`, `vyra_story_badge`, `vyra_story_title`: Manifesto section.

---

### 2. Buttercup (`buttercup`)
* **Core Philosophy**: Softness, self-care, daily comfort, inclusive fits.
* **Master Layout**: `<x-storefront-layout :theme="$theme" title="...">`
* **Custom Config**: `resources/views/storefront/buttercup/components/tailwind-config.blade.php`
* **Dynamic Settings Keys**:
  - `buttercup_slide_1_image` to `buttercup_slide_5_image`: 5 background slider slides.
  - `buttercup_slide_1_title` to `buttercup_slide_5_title`: Slide headlines.
  - `buttercup_hero_badge`: New season pill text.
  - `buttercup_story_image`: Comfort atelier photo.

---

### 3. Saaya (`saaya`)
* **Core Philosophy**: Royal Indian trousseau, antique zari craftsmanship, bridal heirloom silks.
* **Master Layout**: `<x-storefront-layout :theme="$theme" title="...">`
* **Custom Config**: `resources/views/storefront/saaya/components/tailwind-config.blade.php`
* **Dynamic Settings Keys**:
  - `saaya_hero_image`: Archival bride portrait in gold foil frame.
  - `saaya_hero_badge`: Traditional collection subtitle.
  - `saaya_story_image`: Master weaver handloom workshop.
  - `announcement_bar`: Festive promotional marquee ribbon.

---

### 4. Lumière (`lumiere`)
* **Core Philosophy**: Minimalist pure mulberry silk atelier, clean lines, French elegance.
* **Master Layout**: `<x-storefront-layout :theme="$theme" title="...">`
* **Custom Config**: `resources/views/storefront/lumiere/components/tailwind-config.blade.php`
* **Dynamic Settings Keys**:
  - `lumiere_hero_image`: Minimalist silk editorial shot.
  - `lumiere_hero_badge`, `lumiere_hero_title`: Clean serif headline.
  - `lumiere_curated_title`, `lumiere_curated_subtitle`: Lustre Edit rail texts.
  - `lumiere_story_image`: Artisanal silk roots atelier photo.

---

### 5. Verdia / Organic (`organic`)
* **Core Philosophy**: Botanical luxury, regenerative agriculture, certified clean living.
* **Master Layout**: `<x-storefront-layout :theme="$theme" title="...">`
* **Custom Config**: `resources/views/storefront/organic/components/tailwind-config.blade.php`
* **Dynamic Settings Keys**:
  - `organic_hero_image`: Farm harvest linen background banner.
  - `organic_hero_badge`: 100% Certified Farm-to-Table pill.
  - `organic_story_image`: Direct farmer partnership image.
  - `organic_top_notice`: Agricultural certification top notice.

---

## 4. Developer Guidelines for Theme Maintenance

1. **Keep URLs Theme-Agnostic**:
   Never hardcode theme slugs into route URLs. All routes in `routes/web.php` (`/`, `/products/{slug}`, `/category/{slug}`, `/cart`, `/checkout`) remain constant regardless of active theme.
2. **Never Break Component Contracts**:
   Every theme must maintain all 8 components in its `components/` subfolder. If a component is omitted, `<x-storefront-layout>` will throw a missing view exception.
3. **Always Use `toCardArray()` for JSON Products**:
   Cards and modals receive `$product->toCardArray()`. This eager-loads color swatches, variant sizes, stock counts, and price tiers in a normalized structure across all 5 themes.
4. **Purge Cache After Blade Edits**:
   Whenever a Blade component or Tailwind configuration is updated, run:
   ```bash
   php artisan view:clear
   ```
5. **Verify Universal Compatibility**:
   Ensure modifications pass the automated test suite across all 5 themes:
   ```bash
   php artisan test --filter=Theme
   ```
