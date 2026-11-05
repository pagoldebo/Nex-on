# Wishlist Guest-Redirect Backport
## Buttercup · Vyra · Lumière

Saaya's wishlist page has a guest-redirect script that reads `localStorage('lc_wishlist')`
and redirects once to `?ids=<ids>` so the server can look up products.
The other three themes need the same addition.

---

### What to add

Paste this block **immediately after the opening `<x-storefront-layout>` tag**
(before any `<div>` or other markup) in each theme's `wishlist.blade.php`:

```blade
{{-- Guest redirect — fires only when ?ids is absent and localStorage has items --}}
<script>
(function () {
  if (window.location.search.includes('ids=')) return; // already redirected
  var ids;
  try { ids = JSON.parse(localStorage.getItem('lc_wishlist') || '[]'); } catch { ids = []; }
  if (ids.length) {
    window.location.replace(window.location.pathname + '?ids=' + ids.join(','));
  }
})();
</script>
```

---

### Files to edit

| Theme      | File path                                                        |
|------------|------------------------------------------------------------------|
| Buttercup  | `resources/views/storefront/buttercup/wishlist.blade.php`        |
| Vyra       | `resources/views/storefront/vyra/wishlist.blade.php`             |
| Lumière    | `resources/views/storefront/lumiere/wishlist.blade.php`          |

---

### Why it works

1. Script runs before any HTML renders.
2. If `?ids=` is already in the URL → already redirected, do nothing (prevents loop).
3. If localStorage is empty → do nothing (empty state rendered server-side).
4. Otherwise → `window.location.replace(…?ids=<ids>)` — controller receives IDs,
   queries `Product::whereIn('id', …)->get()`, passes `$products` to the view.

---

### Controller requirement (all three themes must have this)

The `WishlistController@show` (or equivalent) must accept an `ids` query param:

```php
public function show(Request $request): View
{
    $ids      = array_filter(explode(',', $request->query('ids', '')));
    $products = $ids
        ? Product::with('colors.variants', 'category')
                 ->whereIn('id', $ids)
                 ->get()
        : collect();

    return view('storefront.' . Template::active()->key . '.wishlist', [
        'products'   => $products,
        'categories' => Category::all(),
    ]);
}
```

The Saaya `StorefrontController` already handles this pattern.
