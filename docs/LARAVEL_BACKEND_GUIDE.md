# Laravel Admin Backend — Template Switcher + Product CMS
### For the 3 lingerie frontend templates (Saaya / Buttercup / Vyra)

This is a build guide, not a copy-paste app — it gives you the exact schema, models, controllers, routes, and Blade wiring needed so a non-technical admin can:

1. Pick which of the 3 frontend templates is **live** on the storefront, from a dropdown in the admin panel.
2. Manage products (Bra / Panty / Camisole / etc.) that automatically populate into whichever template is active.
3. Manage basic site content (hero text, contact info) per template.

---

## 1. Stack & Setup

```bash
composer create-project laravel/laravel nexon-store
cd nexon-store
composer require laravel/breeze --dev   # for admin auth scaffolding
php artisan breeze:install blade
npm install && npm run build
```

`.env` database block:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nexon
DB_USERNAME=root
DB_PASSWORD=your_password
```

```bash
mysql -u root -p -e "CREATE DATABASE nexon CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

---

## 2. Database Schema (MySQL)

### 2.1 `migrations/xxxx_create_categories_table.php`

```php
Schema::create('categories', function (Blueprint $table) {
    $table->id();
    $table->string('name');           // Bra, Panty, Camisole, Bridal Set...
    $table->string('slug')->unique();
    $table->string('icon_svg')->nullable(); // optional inline svg key
    $table->unsignedInteger('sort_order')->default(0);
    $table->timestamps();
});
```

### 2.2 `migrations/xxxx_create_products_table.php`

```php
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->foreignId('category_id')->constrained()->cascadeOnDelete();
    $table->string('name');
    $table->string('slug')->unique();
    $table->text('description')->nullable();
    $table->decimal('price', 10, 2);
    $table->decimal('compare_at_price', 10, 2)->nullable(); // strike-through MRP
    $table->string('badge')->nullable();      // "NEW", "BESTSELLER", "BRIDAL"
    $table->string('image_path')->nullable();
    $table->json('gallery_paths')->nullable(); // extra images
    $table->json('sizes')->nullable();         // ["S","M","L","XL"]
    $table->json('colors')->nullable();        // ["Maroon","Gold","Blush"]
    $table->unsignedInteger('stock')->default(0);
    $table->boolean('is_active')->default(true);
    $table->boolean('is_featured')->default(false); // shows on homepage
    $table->timestamps();
});
```

### 2.3 `migrations/xxxx_create_templates_table.php`

```php
Schema::create('templates', function (Blueprint $table) {
    $table->id();
    $table->string('key')->unique();   // 'saaya', 'buttercup', 'vyra'
    $table->string('label');           // "Saaya — Bridal Atelier"
    $table->string('preview_image')->nullable();
    $table->boolean('is_active')->default(false); // only ONE row true at a time
    $table->timestamps();
});
```

### 2.4 `migrations/xxxx_create_site_settings_table.php`

Per-template editable copy (hero text, contact info, social links) without touching code:

```php
Schema::create('site_settings', function (Blueprint $table) {
    $table->id();
    $table->foreignId('template_id')->constrained()->cascadeOnDelete();
    $table->string('key');     // 'hero_title', 'hero_subtitle', 'contact_email', 'contact_phone', 'whatsapp'
    $table->text('value')->nullable();
    $table->timestamps();
    $table->unique(['template_id','key']);
});
```

### 2.5 `migrations/xxxx_create_orders_table.php` (optional, for later checkout)

```php
Schema::create('orders', function (Blueprint $table) {
    $table->id();
    $table->string('customer_name');
    $table->string('email');
    $table->string('phone');
    $table->text('address');
    $table->decimal('total', 10, 2);
    $table->enum('status', ['pending','confirmed','shipped','delivered','cancelled'])->default('pending');
    $table->timestamps();
});

Schema::create('order_items', function (Blueprint $table) {
    $table->id();
    $table->foreignId('order_id')->constrained()->cascadeOnDelete();
    $table->foreignId('product_id')->constrained();
    $table->unsignedInteger('quantity')->default(1);
    $table->string('size')->nullable();
    $table->string('color')->nullable();
    $table->decimal('price', 10, 2);
    $table->timestamps();
});
```

Run:

```bash
php artisan migrate
```

### 2.6 Seed the 3 templates

`database/seeders/TemplateSeeder.php`:

```php
public function run(): void
{
    $templates = [
        ['key' => 'saaya',     'label' => 'Saaya — Bridal Atelier',   'is_active' => true],
        ['key' => 'buttercup', 'label' => 'Buttercup — Everyday Soft','is_active' => false],
        ['key' => 'vyra',      'label' => 'Vyra — Bold Modern',       'is_active' => false],
    ];
    foreach ($templates as $t) {
        \App\Models\Template::create($t);
    }
}
```

```bash
php artisan db:seed --class=TemplateSeeder
```

---

## 3. Models

```php
// app/Models/Template.php
class Template extends Model
{
    protected $fillable = ['key','label','preview_image','is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function settings() {
        return $this->hasMany(SiteSetting::class);
    }

    public static function active(): self
    {
        return Cache::remember('active_template', 3600, fn() =>
            self::where('is_active', true)->first() ?? self::first()
        );
    }
}
```

```php
// app/Models/Category.php
class Category extends Model
{
    protected $fillable = ['name','slug','icon_svg','sort_order'];

    public function products() {
        return $this->hasMany(Product::class);
    }
}
```

```php
// app/Models/Product.php
class Product extends Model
{
    protected $fillable = [
        'category_id','name','slug','description','price','compare_at_price',
        'badge','image_path','gallery_paths','sizes','colors','stock','is_active','is_featured'
    ];
    protected $casts = [
        'gallery_paths' => 'array',
        'sizes' => 'array',
        'colors' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    public function category() {
        return $this->belongsTo(Category::class);
    }

    protected static function booted()
    {
        static::creating(fn($product) => $product->slug ??= Str::slug($product->name).'-'.Str::random(5));
    }
}
```

```php
// app/Models/SiteSetting.php
class SiteSetting extends Model
{
    protected $fillable = ['template_id','key','value'];
}
```

---

## 4. The Template-Switching Logic (the core of the request)

### 4.1 Admin Controller — switching active template

```php
// app/Http/Controllers/Admin/TemplateController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TemplateController extends Controller
{
    public function index()
    {
        $templates = Template::all();
        return view('admin.templates.index', compact('templates'));
    }

    public function activate(Request $request, Template $template)
    {
        // Deactivate all, activate the chosen one — single source of truth
        Template::query()->update(['is_active' => false]);
        $template->update(['is_active' => true]);

        Cache::forget('active_template');

        return back()->with('success', "{$template->label} is now live on the storefront.");
    }
}
```

### 4.2 Admin route

```php
// routes/web.php (inside admin middleware group)
Route::middleware(['auth', 'can:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('templates', [TemplateController::class, 'index'])->name('templates.index');
    Route::post('templates/{template}/activate', [TemplateController::class, 'activate'])->name('templates.activate');

    Route::resource('categories', CategoryController::class);
    Route::resource('products', ProductController::class);
    Route::resource('settings', SiteSettingController::class)->only(['index','update']);
});
```

### 4.3 Admin Blade — the switcher UI

```blade
{{-- resources/views/admin/templates/index.blade.php --}}
@extends('admin.layout')
@section('content')
<h1 class="text-2xl font-bold mb-6">Storefront Template</h1>

<div class="grid grid-cols-3 gap-6">
  @foreach ($templates as $template)
    <div class="border rounded-xl p-4 {{ $template->is_active ? 'ring-2 ring-green-500' : '' }}">
      <img src="{{ asset('images/'.$template->preview_image) }}" class="rounded-lg mb-3 aspect-video object-cover">
      <h3 class="font-semibold">{{ $template->label }}</h3>

      @if($template->is_active)
        <span class="inline-block mt-2 text-xs font-bold text-green-700 bg-green-100 px-2 py-1 rounded">● LIVE</span>
      @else
        <form method="POST" action="{{ route('admin.templates.activate', $template) }}" class="mt-3">
          @csrf
          <button class="bg-black text-white text-sm px-4 py-2 rounded-lg w-full">
            Make This Live
          </button>
        </form>
      @endif
    </div>
  @endforeach
</div>
@endsection
```

### 4.4 Public-facing storefront controller — serving the right template

```php
// app/Http/Controllers/StorefrontController.php
namespace App\Http\Controllers;

use App\Models\Template;
use App\Models\Category;
use App\Models\Product;

class StorefrontController extends Controller
{
    public function home()
    {
        $template   = Template::active();          // 'saaya' | 'buttercup' | 'vyra'
        $categories = Category::orderBy('sort_order')->get();
        $featured   = Product::where('is_active', true)
                              ->where('is_featured', true)
                              ->with('category')
                              ->latest()
                              ->take(8)
                              ->get();
        $settings   = $template->settings->pluck('value', 'key');

        // Each template gets its OWN blade file but shares the same data shape
        return view("storefront.{$template->key}.home", compact('categories', 'featured', 'settings'));
    }
}
```

```php
// routes/web.php
Route::get('/', [StorefrontController::class, 'home'])->name('home');
```

### 4.5 Folder structure for the 3 templates inside Laravel

```
resources/views/storefront/
├── saaya/
│   ├── home.blade.php       ← converted from template-1-saaya/index.html
│   ├── partials/
│   │   ├── header.blade.php
│   │   ├── mega-menu.blade.php
│   │   ├── footer.blade.php
│   │   └── login-modal.blade.php
├── buttercup/
│   ├── home.blade.php       ← converted from template-2-buttercup/index.html
│   └── partials/...
└── vyra/
    ├── home.blade.php       ← converted from template-3-vyra/index.html
    └── partials/...
```

**How to convert each static HTML file into Blade:**
1. Copy the `<body>` content into `home.blade.php`.
2. Replace the hardcoded product cards with a `@foreach ($featured as $product)` loop.
3. Replace the hardcoded category cards with `@foreach ($categories as $category)`.
4. Replace hero text (`<h1>Draped in Gold & Grace</h1>`) with `{{ $settings['hero_title'] ?? 'Default Title' }}`.
5. Keep all the Tailwind classes and `<style>`/`<script>` blocks exactly as-is — they're self-contained per template, so there's zero CSS bleed between templates.

Example swap inside `saaya/home.blade.php`:

```blade
<div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
  @foreach ($featured as $product)
    <article class="reveal bg-ivory group">
      <div class="card-img-wrap relative aspect-[3/4] overflow-hidden bg-blush/30">
        @if($product->badge)
          <span class="absolute top-3 left-3 z-10 bg-gold text-maroon text-[10px] px-2 py-1 tracking-widest">
            {{ strtoupper($product->badge) }}
          </span>
        @endif
        <img src="{{ asset('images/'.$product->image_path) }}" class="card-img-zoom w-full h-full object-cover">
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
      </div>
    </article>
  @endforeach
</div>
```

---

## 5. Product & Category Admin Controllers

```php
// app/Http/Controllers/Admin/ProductController.php
class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->latest()->paginate(20);
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id'      => 'required|exists:categories,id',
            'name'             => 'required|string|max:255',
            'description'      => 'nullable|string',
            'price'            => 'required|numeric|min:0',
            'compare_at_price' => 'nullable|numeric|min:0',
            'badge'            => 'nullable|string|max:30',
            'image'            => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,webp,avif|max:10240',
            'stock'            => 'required|integer|min:0',
            'is_active'        => 'boolean',
            'is_featured'      => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            // Converts to .avif format and stores directly in public/images/products/{category}/
            $data['image_path'] = app(\App\Services\ProductImageService::class)->storeUpload($request->file('image'), 'products');
        }

        Product::create($data);

        return redirect()->route('admin.products.index')->with('success', 'Product added.');
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name'        => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'image'       => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,webp,avif|max:10240',
        ]);

        if ($request->hasFile('image')) {
            $data['image_path'] = app(\App\Services\ProductImageService::class)->store($request->file('image'), $product);
        }

        $product->update($data);

        return back()->with('success', 'Product updated.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return back()->with('success', 'Product removed.');
    }
}
```

```bash
# Images are located directly inside public/images/products/... (no symlink required)
# ProductImageService automatically converts raster uploads to AVIF format with alpha preservation
```

---

## 6. Mega Menu — driven by real categories

Replace the hardcoded `Bra / Panty / Camisole` lists in each header partial with a loop, so adding a new category in admin instantly shows up in the mega menu on all 3 templates:

```blade
{{-- partials/mega-menu.blade.php (same pattern across all 3 templates, just restyle classes) --}}
<div class="menu-panel ...">
  <div class="grid grid-cols-3 gap-8 p-8">
    @foreach ($categories as $category)
      <div>
        <p class="font-display text-lg mb-3">{{ $category->name }}</p>
        <ul class="space-y-2 text-xs">
          @foreach ($category->products->take(4) as $product)
            <li><a href="{{ route('product.show', $product->slug) }}">{{ $product->name }}</a></li>
          @endforeach
        </ul>
      </div>
    @endforeach
  </div>
</div>
```

Pass `categories` with their products eager-loaded from a shared View Composer so every page (not just home) has mega-menu data:

```php
// app/Providers/AppServiceProvider.php → boot()
View::composer('storefront.*.partials.header', function ($view) {
    $view->with('categories', Category::with('products')->orderBy('sort_order')->get());
});
```

---

## 7. Login — customer-facing (Breeze handles admin auth separately)

Add a simple customer auth using Laravel's built-in `Auth` facade, reusing Breeze's `users` table:

```php
// routes/web.php
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/register', [RegisteredUserController::class, 'store']);
```

In each template's login modal form, point the `action` to these routes and keep the modal's open/close JS exactly as already written — only the form `action` and `method="POST"` + `@csrf` need adding:

```blade
<form method="POST" action="{{ route('login') }}" class="mt-8 flex flex-col gap-4">
  @csrf
  <input type="email" name="email" placeholder="Email Address" ...>
  <input type="password" name="password" placeholder="Password" ...>
  <button type="submit" class="gold-btn ...">SIGN IN</button>
</form>
```

---

## 8. Admin Panel Pages Checklist

| Page | Route | Purpose |
|---|---|---|
| Dashboard | `/admin` | Order count, low-stock alerts |
| **Templates** | `/admin/templates` | The 3-template switcher (section 4.3) |
| Categories | `/admin/categories` | Add/edit Bra, Panty, Camisole, etc. |
| Products | `/admin/products` | Full CRUD, image upload, featured toggle |
| Site Settings | `/admin/settings` | Per-template hero text, contact info |
| Orders | `/admin/orders` | View/update order status |

Use Breeze's scaffolded layout (`resources/views/layouts/app.blade.php`) as the admin shell, or swap in a free admin theme like **AdminLTE** or **Tailadmin** if you want it to look polished fast.

---

## 9. Why this approach works for your 3-template requirement

- **One database, one product catalog** — products/categories are template-agnostic. Add a product once, it appears correctly styled in whichever template is live.
- **`templates.is_active`** is the single switch. The admin clicks one button, `StorefrontController::home()` reads it, and Laravel renders a completely different Blade view (different HTML/Tailwind/JS) without touching any data.
- **Zero downtime switching** — because the cache is cleared and the next page load just resolves a different view path, there's no rebuild or redeploy step.
- **Each template stays visually isolated** — since the original Tailwind config + `<style>` blocks are scoped per Blade file (not merged into one global stylesheet), Saaya's gold/maroon tokens never leak into Vyra's lime/coral tokens.

---

## 10. Next Steps

1. Run the migrations + seeder above.
2. Convert the 3 HTML files (already built and provided) into the Blade structure in section 4.5.
3. Wire `StorefrontController` and the admin `TemplateController`.
4. Seed a few real categories/products through the admin panel, confirm they render in all 3 templates by switching between them.
5. Layer on the `orders`/`order_items` tables when you're ready for checkout (a simple Cash-on-Delivery flow is the fastest path to launch for the Indian market).
