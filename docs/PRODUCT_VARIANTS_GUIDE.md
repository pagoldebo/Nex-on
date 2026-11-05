# Controlling Price, Image, Size & Color Across All 4 Themes
### Blade (server-rendered) approach — companion to LARAVEL_BACKEND_GUIDE.md

## First, the honest state of your 4 files today

| | Saaya / Buttercup / Vyra | Lumière |
|---|---|---|
| Price | Hardcoded text in HTML | Hardcoded in JS arrays |
| Image | SVG placeholder, hardcoded | Unsplash URL, hardcoded in JS |
| **Size selector** | **Doesn't exist** | Exists (quick-view modal) |
| **Color selector** | **Doesn't exist** | Exists (quick-view modal), but shared globally — every product shows the same 3 colors |

This matters because "control size and color" isn't just a data-wiring problem for 3 of your 4 themes — there's no UI for it yet. Below I'll show you (1) the schema that supports real per-product variants, (2) how to add a lightweight size/color picker to Saaya/Buttercup/Vyra's product cards, and (3) the Blade loop that drives all of it from MySQL.

---

## 1. Schema additions (on top of LARAVEL_BACKEND_GUIDE.md)

Your original `products` table had `sizes` and `colors` as JSON columns — fine for *display*, but not enough if different sizes/colors should have different stock or images. Two ways to go:

### Simple version (good enough for most stores)
Keep `sizes` and `colors` as JSON arrays on `products`, just actually use them:

```php
// products table (already in your migration)
$table->json('sizes')->nullable();   // ["S","M","L","XL"]
$table->json('colors')->nullable();  // [{"name":"Maroon","hex":"#3D0C11"},{"name":"Gold","hex":"#C9A24B"}]
```

This is enough if every size/color of a product shares the same price and stock count.

### Proper version (real variants — recommended)
If a bridal set in size XL should be trackable separately from size S, or "Gold" and "Blush" need different photos, you need a `product_variants` table:

```php
Schema::create('product_variants', function (Blueprint $table) {
    $table->id();
    $table->foreignId('product_id')->constrained()->cascadeOnDelete();
    $table->string('size')->nullable();        // "S", "M", "L", "XL"
    $table->string('color_name')->nullable();  // "Maroon"
    $table->string('color_hex')->nullable();   // "#3D0C11"
    $table->string('image_path')->nullable();  // variant-specific image, falls back to product image if null
    $table->decimal('price_override', 10, 2)->nullable(); // null = use product.price
    $table->unsignedInteger('stock')->default(0);
    $table->timestamps();
});
```

```php
// app/Models/ProductVariant.php
class ProductVariant extends Model
{
    protected $fillable = ['product_id','size','color_name','color_hex','image_path','price_override','stock'];

    public function product() { return $this->belongsTo(Product::class); }

    public function getEffectivePriceAttribute() {
        return $this->price_override ?? $this->product->price;
    }
}
```

```php
// app/Models/Product.php — add this relationship
public function variants() {
    return $this->hasMany(ProductVariant::class);
}

public function colors() {
    return $this->variants->unique('color_name')->values();
}

public function sizes() {
    return $this->variants->pluck('size')->unique()->values();
}
```

**This guide uses the Simple version below** (JSON columns) since it's enough for most lingerie SKUs and is far less admin-panel work to build forms for. I'll flag where you'd swap in variants if you need them later.

---

## 2. The admin form fields you actually fill in

In `admin/products/create.blade.php` (or edit), the relevant inputs:

```blade
<label>Price (₹)</label>
<input type="number" name="price" step="0.01" required>

<label>Compare-at Price (₹, optional — shows as strikethrough)</label>
<input type="number" name="compare_at_price" step="0.01">

<label>Main Image</label>
<input type="file" name="image" accept="image/*">

<label>Sizes (comma-separated)</label>
<input type="text" name="sizes_input" placeholder="S, M, L, XL">

<label>Colors</label>
<div id="colorRepeater">
  <!-- repeatable: name + hex picker -->
  <input type="text" name="color_names[]" placeholder="Maroon">
  <input type="color" name="color_hexes[]" value="#3D0C11">
</div>
<button type="button" onclick="addColorRow()">+ Add Color</button>
```

Controller side — turn the comma list into JSON before saving:

```php
// ProductController@store
$data['sizes'] = array_map('trim', explode(',', $request->sizes_input));

$colors = [];
foreach ($request->color_names as $i => $name) {
    if ($name) {
        $colors[] = ['name' => $name, 'hex' => $request->color_hexes[$i]];
    }
}
$data['colors'] = $colors;

Product::create($data);
```

That's it — one product record now carries price, compare-at price, image, an array of sizes, and an array of `{name, hex}` color objects.

---

## 3. Saaya — full Blade conversion (the reference pattern)

This is the part that actually answers your question. Saaya's current product card (static HTML):

```html
<article class="reveal bg-ivory group" style="transition-delay:.05s">
  <div class="card-img-wrap relative aspect-[3/4] overflow-hidden bg-blush/30">
    <span class="absolute top-3 left-3 z-10 bg-gold text-maroon text-[10px] px-2 py-1 tracking-widest">BRIDAL</span>
    <div class="card-img-zoom w-full h-full flex items-center justify-center">
      <svg viewBox="0 0 160 200" class="w-3/5">...</svg>
    </div>
  </div>
  <div class="p-4">
    <p class="text-[11px] tracking-widest text-gold-dark uppercase">Bra</p>
    <h3 class="font-display text-lg text-maroon mt-1">Zari Bloom Bridal Set</h3>
    <p class="text-maroon font-medium mt-2">₹2,499 <span class="text-maroon/40 line-through text-sm ml-1">₹3,599</span></p>
  </div>
</article>
```

Converted to a Blade loop, **with a size/color picker added** (since Saaya has none today):

```blade
{{-- resources/views/storefront/saaya/partials/product-card.blade.php --}}
<article class="reveal bg-ivory group" data-product-id="{{ $product->id }}">
  <div class="card-img-wrap relative aspect-[3/4] overflow-hidden bg-blush/30">
    @if($product->badge)
      <span class="absolute top-3 left-3 z-10 bg-gold text-maroon text-[10px] px-2 py-1 tracking-widest">
        {{ strtoupper($product->badge) }}
      </span>
    @endif

    {{-- Real product photo replaces the SVG placeholder --}}
    <img src="{{ $product->image_path ? asset('storage/'.$product->image_path) : asset('images/placeholder.jpg') }}"
         alt="{{ $product->name }}"
         class="card-img-zoom w-full h-full object-cover">
  </div>

  <div class="p-4">
    <p class="text-[11px] tracking-widest text-gold-dark uppercase">{{ $product->category->name }}</p>
    <h3 class="font-display text-lg text-maroon mt-1">{{ $product->name }}</h3>

    <p class="text-maroon font-medium mt-2">
      ₹{{ number_format($product->price) }}
      @if($product->compare_at_price)
        <span class="text-maroon/40 line-through text-sm ml-1">₹{{ number_format($product->compare_at_price) }}</span>
      @endif
    </p>

    {{-- NEW: color swatches, only rendered if the product has any --}}
    @if($product->colors && count($product->colors))
      <div class="flex gap-1.5 mt-2">
        @foreach ($product->colors as $color)
          <button type="button"
                  class="color-swatch w-4 h-4 rounded-full border border-gold/30"
                  style="background-color: {{ $color['hex'] }};"
                  title="{{ $color['name'] }}"
                  onclick="selectProductColor(this, {{ $product->id }})"></button>
        @endforeach
      </div>
    @endif

    {{-- NEW: size pills, only rendered if the product has any --}}
    @if($product->sizes && count($product->sizes))
      <div class="flex gap-1.5 mt-2">
        @foreach ($product->sizes as $size)
          <button type="button"
                  class="size-pill text-[10px] w-6 h-6 rounded-full border border-gold/30 text-maroon/70 hover:border-gold hover:text-maroon transition-colors"
                  onclick="selectProductSize(this, {{ $product->id }})">{{ $size }}</button>
        @endforeach
      </div>
    @endif
  </div>
</article>
```

And the section that loops over it:

```blade
{{-- resources/views/storefront/saaya/home.blade.php --}}
<section id="products" class="bg-maroon-dark py-24 px-6 lg:px-10">
  <div class="max-w-7xl mx-auto">
    ...
    <div class="hidden sm:grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
      @foreach ($featured as $product)
        @include('storefront.saaya.partials.product-card', ['product' => $product])
      @endforeach
    </div>

    {{-- Mobile swipe carousel — same partial, same data, different wrapper --}}
    <div class="sm:hidden">
      <div class="swipe-track" id="productSwipe">
        @foreach ($featured as $product)
          <div class="swipe-item px-1">
            @include('storefront.saaya.partials.product-card', ['product' => $product])
          </div>
        @endforeach
      </div>
    </div>
  </div>
</section>
```

**One partial, two contexts (grid + carousel) — no duplicated markup, and both update the instant you change a product in admin.**

A tiny bit of JS to track the selection (add near your other scripts):

```js
function selectProductColor(btn, productId) {
  document.querySelectorAll(`[data-product-id="${productId}"] .color-swatch`)
    .forEach(s => s.classList.remove('ring-2','ring-maroon'));
  btn.classList.add('ring-2','ring-maroon');
}
function selectProductSize(btn, productId) {
  document.querySelectorAll(`[data-product-id="${productId}"] .size-pill`)
    .forEach(s => s.classList.remove('bg-maroon','text-ivory'));
  btn.classList.add('bg-maroon','text-ivory');
}
```

This gives Saaya real size/color selection without touching its visual language — swatches use `gold`/`maroon` tokens already in its palette.

---

## 4. Applying the same pattern to Buttercup and Vyra

Identical structure, just swap the class tokens to match each theme's design system. The only things that change are colors/shapes — the Blade logic (`@if($product->colors)`, `@foreach`) is copy-paste identical.

**Buttercup** (rounded, pastel) — size pills become full circles with `rounded-full` and `bg-rose`:
```blade
<button class="size-pill text-[10px] w-7 h-7 rounded-full border border-rose/40 text-ink/60 hover:bg-rose hover:text-cream transition-colors"
        onclick="selectProductSize(this, {{ $product->id }})">{{ $size }}</button>
```

**Vyra** (sharp, Gen-Z) — size pills become square-edged with the coral/lime palette:
```blade
<button class="size-pill text-[10px] w-7 h-7 border border-off/20 text-off/60 hover:border-lime hover:text-lime transition-colors"
        onclick="selectProductSize(this, {{ $product->id }})">{{ $size }}</button>
```

Same `<img>` swap-in replaces every theme's SVG placeholder with `{{ asset('storage/'.$product->image_path) }}`.

---

## 5. Lumière — adapting its existing JS-array pattern to Blade

Lumière is the interesting case: it already has a size/color UI (the quick-view modal), so instead of building new UI, you **feed Blade-rendered data into the existing JS**.

Replace the hardcoded JS arrays at the top of its `<script>` block:

```js
// BEFORE — hardcoded
const lustreCollection = [
  { name: 'Odette Ribbon Bra', price: 1499, stock: 2, baseImg: '...', hoverImg: '...' }
];
```

with a Blade-injected version:

```blade
{{-- In storefront/lumiere/home.blade.php, right before the closing </script> tag's data section --}}
<script>
  const lustreCollection = @json($featuredProducts->map(fn($p) => [
      'id' => $p->id,
      'name' => $p->name,
      'price' => $p->price,
      'stock' => $p->stock,
      'baseImg' => $p->image_path ? asset('storage/'.$p->image_path) : asset('images/placeholder.jpg'),
      'hoverImg' => $p->gallery_paths[0] ?? null ? asset('storage/'.$p->gallery_paths[0]) : asset('images/placeholder.jpg'),
      'colors' => $p->colors ?? [],
      'sizes' => $p->sizes ?? [],
  ]));

  const bestSellers = @json($bestsellerProducts->map(fn($p) => [
      'id' => $p->id, 'name' => $p->name, 'price' => $p->price, 'stock' => $p->stock,
      'desc' => $p->description,
      'baseImg' => $p->image_path ? asset('storage/'.$p->image_path) : asset('images/placeholder.jpg'),
      'hoverImg' => $p->gallery_paths[0] ?? null ? asset('storage/'.$p->gallery_paths[0]) : asset('images/placeholder.jpg'),
  ]));
</script>
```

`@json()` is Laravel's Blade helper — it safely converts a PHP array/collection straight into a JS array literal, escaped correctly. **Everything else in Lumière's existing JS (`openQuickView`, `renderColors`, `renderSizes`, the swipe carousels) keeps working exactly as-is**, because it was already built to consume arrays shaped like this — you're just changing where the array comes from.

The one real change needed: `renderColors()` currently uses a single global `shades` array for every product. To make colors per-product (so a coral bra doesn't show maroon as an option), update:

```js
// BEFORE
function renderColors() {
  document.getElementById('colorOptions').innerHTML = shades.map(...).join('');
}

// AFTER — reads colors from the specific product
let currentProductColors = [];
function renderColors() {
  document.getElementById('colorOptions').innerHTML = currentProductColors.map((s, idx) =>
    `<button onclick="event.stopPropagation(); selectShade(${idx})" class="w-8 h-8 rounded-full border-2 ${selectedColor.hex === s.hex ? 'border-gray-900 scale-110' : 'border-transparent'}" style="background-color: ${s.hex}"></button>`
  ).join('');
}
function openQuickView(n, p, img, colors) {
  currentProductColors = colors && colors.length ? colors : [{name:'Default', hex:'#FDF8F9'}];
  selectedColor = currentProductColors[0];
  // ...rest stays the same
}
```

And update the `onclick` in the product card templates to pass `${JSON.stringify(item.colors)}` as a 4th argument to `openQuickView(...)`.

---

## 6. The controller that supplies all of this

```php
// app/Http/Controllers/StorefrontController.php
public function home()
{
    $template = Template::active();
    $categories = Category::with('products')->orderBy('sort_order')->get();
    $featured = Product::where('is_active', true)->where('is_featured', true)->with('category')->get();
    $bestsellers = Product::where('is_active', true)->where('is_bestseller', true)->with('category')->get();

    return view("storefront.{$template->key}.home", compact('categories', 'featured', 'bestsellers'));
}
```

(Add an `is_bestseller` boolean column to `products` if you want Lumière's two separate rails — Lustre Edit vs Bestsellers — to pull from genuinely different product sets rather than the same `is_featured` flag.)

---

## 7. What you get once this is done

| Admin does this... | ...and it updates here automatically |
|---|---|
| Changes a product's price in `/admin/products` | Every theme's product card, cart total, quick-view price |
| Uploads a new product image | Card image, hover-swap image (Lumière), quick-view image |
| Adds "XL" to a product's sizes | Size pill appears in Saaya/Buttercup/Vyra cards + Lumière's quick-view |
| Adds a new color `{name: "Rose", hex: "#FFB6C1"}` | Color swatch appears everywhere that product renders |
| Switches the active template | A completely different theme renders, same underlying product data |

This is the payoff of Option A — one source of truth in MySQL, four visual skins reading from it identically.

---

## 8. Suggested order of work

1. Add the `colors`/`sizes` JSON usage to your `Product` model and admin form (section 2).
2. Convert Saaya fully first (section 3) — it's the reference implementation.
3. Copy the pattern to Buttercup and Vyra, swapping only Tailwind classes (section 4).
4. Wire Lumière's existing JS to Blade-supplied `@json()` data (section 5) and make `renderColors()` per-product.
5. Test by editing one product's price/image/size/color in admin and confirming it changes across all 4 themes when you switch the active template.
