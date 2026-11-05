# Product Variants — Database-Driven Color/Size/Price/Image

This implements exactly what you described: **price, image, size, and color are
all linked to the product via `product_id`**, with the specific rule that:

- **Images belong to the COLOR** (up to 8 per color — switching color swaps the gallery)
- **Price + stock belong to the SIZE-WITHIN-COLOR** (Red+S = ₹499, Red+L = ₹599, Blue+S = ₹349 — each is independent)
- **Combinations are sparse** — a color only has the sizes an admin actually added

## The data model, in plain terms

```
Product            ("Zari Bloom Bra")
  └── ProductColor  ("Red", hex #DC2626)
        ├── ProductColorImage × up to 8   (the gallery — same for every size of Red)
        └── ProductVariant                (THIS is the real SKU)
              ├── size: "S"   price: 499   stock: 12
              ├── size: "L"   price: 599   stock: 4
              └── size: "XL"  price: 649   stock: 0   (sold out, but still listed)
  └── ProductColor  ("Blue", hex #2563EB)
        ├── ProductColorImage × up to 8   (different photos than Red)
        └── ProductVariant
              └── size: "S"   price: 349   stock: 8
              (no L or XL for Blue — sparse by design, nothing to fill in)
```

`product_id` is the root key tying everything together, exactly as you asked —
every color and every variant has a `product_id` foreign key, so you can always
trace price/image/size/color back to one product.

## Files in this folder

```
migrations/
  2026_01_01_000000_create_categories_table.php
  2026_01_01_000001_create_products_table.php       (no price/image here — see below)
  2026_01_01_000002_create_product_colors_table.php (colors + their images)
  2026_01_01_000003_create_product_variants_table.php (the real SKU: color+size+price+stock)
  2026_01_01_000004_create_templates_table.php      (the 4-theme switcher, from earlier guide)

models/
  Category.php
  Product.php          (toCardArray() — the single method every theme calls)
  ProductColor.php      (toPickerArray() — feeds the JS color/size picker)
  ProductColorImage.php
  ProductVariant.php
  Template.php

controllers/
  ProductController.php  (admin CRUD — handles the full nested color/size form)

views/
  admin-product-create.blade.php  (repeatable color blocks, each with image upload + size rows)
  saaya-product-card.blade.php    (storefront card — reference implementation)
  product-card.js                 (drives color/size selection + live price swap)
```

## Why price/image aren't on the `products` table

This is the part that directly answers "red S = 499, red L = 599" — if price
lived on `products`, every variant would be forced to share one price. Putting
price and stock on `product_variants` (one row per color+size combo) is what
makes per-combination pricing possible at all.

Same logic for images: if images lived on `products`, color couldn't change
them. Images live on `product_colors` instead, so switching from Red to Blue
swaps the whole gallery, but staying on Red and changing S→L keeps the same
photos (since size doesn't own images — color does, matching what you said).

## How the admin actually fills this in

1. Create a product → name, category, description.
2. Add a color ("Red") → upload up to 8 images for it.
3. Inside that color, add size rows — each row is its own price, compare-at
   price, and stock count. Add as many or as few sizes as exist for that color.
4. Add another color ("Blue") → different 8 images, different size rows.
5. Save. One submit creates the product + both colors + all their images +
   all their size variants, in a single database transaction (see
   `ProductController::syncColors()` — if anything fails, nothing partial gets saved).

## How the storefront uses it (the part that drives all 4 themes)

`Product::toCardArray()` flattens a product (with its colors, images, and
variants already eager-loaded) into one JSON-friendly array:

```php
[
  'id' => 12,
  'name' => 'Zari Bloom Bra',
  'startingPrice' => 349,        // cheapest variant, shown before any selection
  'coverImage' => '...',         // first image of the first color
  'colors' => [
    [
      'name' => 'Red', 'hex' => '#DC2626',
      'images' => ['...jpg', '...jpg', ...],     // up to 8
      'sizes' => [
        ['size' => 'S', 'price' => 499, 'stock' => 12, 'inStock' => true],
        ['size' => 'L', 'price' => 599, 'stock' => 4,  'inStock' => true],
      ],
    ],
    [
      'name' => 'Blue', 'hex' => '#2563EB',
      'images' => [...],
      'sizes' => [
        ['size' => 'S', 'price' => 349, 'stock' => 8, 'inStock' => true],
      ],
    ],
  ],
]
```

This array gets dropped into the product card's `data-product` attribute as
JSON. `product-card.js` reads it and handles everything client-side — clicking
a color swatch swaps the image and rebuilds the size pills (since sizes are
sparse per color); clicking a size pill updates the price to that exact
variant's price. No page reload, no extra API call — the data for one product
card is small enough to embed directly.

## Applying this to Buttercup, Vyra, and Lumière

**Buttercup & Vyra** — copy `saaya-product-card.blade.php`, rename the file
into each theme's `partials/` folder, and change only the Tailwind classes
(rounded pills + rose palette for Buttercup, sharp edges + coral/lime for
Vyra). The Blade logic (`@foreach`, `$cardData`, `data-product`) and the JS
file are reused **completely unchanged** — `product-card.js` doesn't know or
care which theme it's running in, it only reads class names already present
in the markup it's attached to.

**Lumière** — slightly different because it already has a quick-view modal
with its own color/size rendering functions (`renderColors()`, `renderSizes()`
from the earlier guide). Instead of using `product-card.js`, feed
`toCardArray()` output into Lumière's existing `lustreCollection` /
`bestSellers` arrays via `@json($products->map->toCardArray())`, then update
its `renderColors()`/`renderSizes()` to read `color.sizes` (with their own
price/stock) instead of the old shared global `shades` array. The shape is
compatible — `toCardArray()` was designed to match what Lumière already expects.

## Next steps

1. Drop these files into a real Laravel project (`php artisan migrate` the
   migrations, place the models/controllers in their namespaced folders).
2. Wire `routes/web.php`: `Route::resource('admin/products', ProductController::class)`.
3. Build `admin/products/edit.blade.php` as the update-form twin of
   `create.blade.php` (same template-cloning JS, just pre-populated with the
   product's existing colors/sizes/images on load).
4. Copy `saaya-product-card.blade.php` into Buttercup/Vyra/Lumière, restyled.
5. Test: create one product with 2 colors, each with different sizes/prices,
   confirm switching color swaps images and switching size swaps price —
   across all 4 themes once each has the partial.
