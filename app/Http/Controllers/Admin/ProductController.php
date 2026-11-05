<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Addon;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductColorImage;
use App\Models\ProductVariant;
use App\Services\ProductImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $search           = trim((string) $request->query('q', ''));
        $selectedCategory = $request->query('category_id');
        $selectedStatus   = $request->query('status');

        $query = Product::with(['category', 'brand', 'addons', 'colors.variants', 'colors.images'])->latest();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('badge', 'like', "%{$search}%")
                  ->orWhereHas('variants', fn ($v) => $v->where('sku', 'like', "%{$search}%"));
            });
        }

        if (!empty($selectedCategory)) {
            $cat = Category::find($selectedCategory);
            $catIds = $cat ? $cat->getAllDescendantIds() : [$selectedCategory];
            $query->whereIn('category_id', $catIds);
        }

        if ($selectedStatus === 'active') {
            $query->where('is_active', true);
        } elseif ($selectedStatus === 'draft') {
            $query->where('is_active', false);
        }

        $products   = $query->paginate(15)->withQueryString();
        $categories = Category::orderBy('sort_order')->get();

        return view('admin.products.index', compact('products', 'categories', 'search', 'selectedCategory', 'selectedStatus'));
    }

    public function toggleStatus(Product $product)
    {
        $product->is_active = !$product->is_active;
        $product->save();

        $statusLabel = $product->is_active ? 'published' : 'hidden';
        return back()->with('success', "'{$product->name}' is now {$statusLabel}.");
    }

    public function create()
    {
        $categories = Category::orderBy('sort_order')->get();
        $brands     = Brand::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();
        $addons     = Addon::where('is_active', true)->orderBy('name')->get();
        return view('admin.products.create', compact('categories', 'brands', 'addons'));
    }

    public function edit(Product $product)
    {
        $categories = Category::orderBy('sort_order')->get();
        $brands     = Brand::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();
        $addons     = Addon::where('is_active', true)->orderBy('name')->get();
        $product->load('colors.images', 'colors.variants', 'addons', 'brand');
        return view('admin.products.edit', compact('product', 'categories', 'brands', 'addons'));
    }

    /**
     * Expected request shape (see admin/products/create.blade.php for the
     * actual form fields that produce this):
     *
     * name, category_id, description, badge, is_active, is_featured, is_bestseller
     *
     * colors: [
     *   0 => [
     *     'name' => 'Red', 'hex' => '#DC2626',
     *     'images' => [UploadedFile, UploadedFile, ...],   // up to 8
     *     'sizes' => [
     *       0 => ['size' => 'S', 'price' => 499, 'compare_at_price' => null, 'stock' => 12],
     *       1 => ['size' => 'L', 'price' => 599, 'compare_at_price' => 699, 'stock' => 4],
     *     ],
     *   ],
     *   1 => [
     *     'name' => 'Blue', 'hex' => '#2563EB',
     *     'images' => [...],
     *     'sizes' => [
     *       0 => ['size' => 'S', 'price' => 349, 'compare_at_price' => null, 'stock' => 8],
     *     ],
     *   ],
     * ]
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                       => 'required|string|max:255',
            'sku'                        => 'nullable|string|max:60|unique:products,sku',
            'category_id'                => 'required|exists:categories,id',
            'brand_id'                   => 'nullable|exists:brands,id',
            'addon_ids'                  => 'nullable|array',
            'addon_ids.*'                => 'integer|exists:addons,id',
            'description'                => 'nullable|string',
            'short_description'          => 'nullable|string',
            'long_description'           => 'nullable|string',
            'badge'                      => 'nullable|string|max:30',
            'is_active'                  => 'boolean',
            'is_featured'                => 'boolean',
            'is_bestseller'              => 'boolean',

            'colors'                     => 'required|array|min:1',
            'colors.*.name'              => 'required|string|max:50',
            'colors.*.hex'               => 'nullable|string|max:9',
            'colors.*.images'            => 'nullable|array|max:8',
            'colors.*.images.*'          => 'image|max:4096',
            'colors.*.sizes'             => 'required|array|min:1',
            'colors.*.sizes.*.size'      => 'required|string|max:10',
            'colors.*.sizes.*.sku'       => 'nullable|string|max:60',
            'colors.*.sizes.*.price'     => 'required|numeric|min:0',
            'colors.*.sizes.*.compare_at_price' => 'nullable|numeric|min:0',
            'colors.*.sizes.*.stock'     => 'required|integer|min:0',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $product = Product::create([
                'name'             => $validated['name'],
                'sku'              => !empty($validated['sku']) ? strtoupper(trim($validated['sku'])) : null,
                'category_id'      => $validated['category_id'],
                'brand_id'         => $validated['brand_id'] ?? null,
                'description'      => $validated['description'] ?? $validated['short_description'] ?? null,
                'long_description' => $validated['long_description'] ?? null,
                'badge'            => $validated['badge'] ?? null,
                'is_active'        => $request->boolean('is_active', true),
                'is_featured'      => $request->boolean('is_featured'),
                'is_bestseller'    => $request->boolean('is_bestseller'),
            ]);

            if ($request->has('addon_ids')) {
                $product->addons()->sync($request->input('addon_ids', []));
            }

            $this->syncColors($product, $validated['colors'], $request);
        });

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name'                       => 'required|string|max:255',
            'sku'                        => 'nullable|string|max:60|unique:products,sku,' . $product->id,
            'category_id'                => 'required|exists:categories,id',
            'brand_id'                   => 'nullable|exists:brands,id',
            'addon_ids'                  => 'nullable|array',
            'addon_ids.*'                => 'integer|exists:addons,id',
            'description'                => 'nullable|string',
            'short_description'          => 'nullable|string',
            'long_description'           => 'nullable|string',
            'badge'                      => 'nullable|string|max:30',
            'is_active'                  => 'boolean',
            'is_featured'                => 'boolean',
            'is_bestseller'              => 'boolean',

            'colors'                     => 'required|array|min:1',
            'colors.*.id'                => 'nullable|exists:product_colors,id',
            'colors.*.name'              => 'required|string|max:50',
            'colors.*.hex'               => 'nullable|string|max:9',
            'colors.*.images'            => 'nullable|array|max:8',
            'colors.*.images.*'          => 'image|max:4096',
            'colors.*.remove_image_ids'  => 'nullable|array',
            'colors.*.remove_image_ids.*' => 'integer|exists:product_color_images,id',
            'colors.*.sizes'             => 'required|array|min:1',
            'colors.*.sizes.*.id'        => 'nullable|exists:product_variants,id',
            'colors.*.sizes.*.size'      => 'required|string|max:10',
            'colors.*.sizes.*.sku'       => 'nullable|string|max:60',
            'colors.*.sizes.*.price'     => 'required|numeric|min:0',
            'colors.*.sizes.*.compare_at_price' => 'nullable|numeric|min:0',
            'colors.*.sizes.*.stock'     => 'required|integer|min:0',
        ]);

        DB::transaction(function () use ($validated, $request, $product) {
            $product->update([
                'name'             => $validated['name'],
                'sku'              => !empty($validated['sku']) ? strtoupper(trim($validated['sku'])) : $product->sku,
                'category_id'      => $validated['category_id'],
                'brand_id'         => $validated['brand_id'] ?? null,
                'description'      => $validated['description'] ?? $validated['short_description'] ?? null,
                'long_description' => $validated['long_description'] ?? null,
                'badge'            => $validated['badge'] ?? null,
                'is_active'        => $request->boolean('is_active', true),
                'is_featured'      => $request->boolean('is_featured'),
                'is_bestseller'    => $request->boolean('is_bestseller'),
            ]);

            $product->addons()->sync($request->input('addon_ids', []));

            // Remove colors that were deleted in the form, then re-sync the rest.
            $keptColorIds = array_filter(array_column($validated['colors'], 'id'));
            $product->colors()->whereNotIn('id', $keptColorIds)->delete();

            $this->syncColors($product, $validated['colors'], $request);
        });

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return back()->with('success', 'Product removed successfully.');
    }

    /**
     * 1-Click duplicate product: clones product attributes, colors, images, and variants.
     */
    public function duplicate(Product $product)
    {
        $product->load(['colors.images', 'colors.variants']);

        $newProduct = DB::transaction(function () use ($product) {
            $baseSku = 'LS-' . strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $product->name), 0, 3)) . '-' . strtoupper(\Illuminate\Support\Str::random(4));

            $clone = Product::create([
                'category_id'      => $product->category_id,
                'sku'              => $baseSku,
                'name'             => $product->name . ' (Copy)',
                'slug'             => \Illuminate\Support\Str::slug($product->name) . '-copy-' . \Illuminate\Support\Str::random(5),
                'description'      => $product->description,
                'long_description' => $product->long_description,
                'badge'            => $product->badge,
                'is_active'        => false, // created as hidden/draft
                'is_featured'      => false,
                'is_bestseller'    => false,
            ]);

            foreach ($product->colors as $color) {
                $newColor = ProductColor::create([
                    'product_id' => $clone->id,
                    'name'       => $color->name,
                    'hex'        => $color->hex,
                    'sort_order' => $color->sort_order,
                ]);

                foreach ($color->images as $img) {
                    ProductColorImage::create([
                        'product_color_id' => $newColor->id,
                        'image_path'       => $img->image_path,
                        'sort_order'       => $img->sort_order,
                    ]);
                }

                foreach ($color->variants as $variant) {
                    $cCode = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $color->name), 0, 3));
                    $variantSku = $clone->sku . '-' . $cCode . '-' . strtoupper($variant->size);

                    ProductVariant::create([
                        'product_id'       => $clone->id,
                        'product_color_id' => $newColor->id,
                        'size'             => $variant->size,
                        'sku'              => $variantSku,
                        'price'            => $variant->price,
                        'compare_at_price' => $variant->compare_at_price,
                        'stock'            => $variant->stock,
                    ]);
                }
            }

            return $clone;
        });

        return redirect()->route('admin.products.edit', $newProduct)
            ->with('success', "'{$product->name}' duplicated as draft. Review details and publish when ready.");
    }

    /**
     * Bulk delete products.
     */
    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'integer|exists:products,id',
        ]);

        $count = Product::whereIn('id', $validated['ids'])->delete();

        return back()->with('success', "{$count} products deleted successfully.");
    }

    /**
     * Shared create/update logic: syncs colors, images, and size variants with SKUs.
     */
    private function syncColors(Product $product, array $colorsInput, Request $request): void
    {
        $position = 0;
        foreach ($colorsInput as $colorIndex => $colorData) {
            $color = ProductColor::updateOrCreate(
                ['id' => $colorData['id'] ?? null, 'product_id' => $product->id],
                [
                    'name'       => $colorData['name'],
                    'hex'        => $colorData['hex'] ?? null,
                    'sort_order' => $position,
                ]
            );
            $position++;

            // Delete images explicitly removed by admin
            $removeImageIds = $colorData['remove_image_ids'] ?? [];
            if (!empty($removeImageIds)) {
                $imagesToDelete = $color->images()->whereIn('id', $removeImageIds)->get();
                foreach ($imagesToDelete as $image) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($image->image_path);
                    $image->delete();
                }
            }

            // Upload new images up to 8 max
            $existingImageCount = $color->images()->count();
            $uploaded = $request->file("colors.$colorIndex.images") ?? [];

            foreach ($uploaded as $i => $file) {
                if ($existingImageCount + $i >= 8) {
                    break;
                }
                $path = ProductImageService::store($file, $product);
                ProductColorImage::create([
                    'product_color_id' => $color->id,
                    'image_path'       => $path,
                    'sort_order'       => $existingImageCount + $i,
                ]);
            }

            // Sync size variants with SKUs
            $keptVariantIds = [];
            foreach ($colorData['sizes'] as $sizeData) {
                $variantSku = !empty($sizeData['sku'])
                    ? strtoupper(trim($sizeData['sku']))
                    : ($product->sku . '-' . strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $color->name), 0, 3)) . '-' . strtoupper($sizeData['size']));

                $variant = ProductVariant::updateOrCreate(
                    [
                        'id'               => $sizeData['id'] ?? null,
                        'product_id'       => $product->id,
                        'product_color_id' => $color->id,
                    ],
                    [
                        'size'             => $sizeData['size'],
                        'sku'              => $variantSku,
                        'price'            => $sizeData['price'],
                        'compare_at_price' => $sizeData['compare_at_price'] ?? null,
                        'stock'            => $sizeData['stock'],
                    ]
                );
                $keptVariantIds[] = $variant->id;
            }
            $color->variants()->whereNotIn('id', $keptVariantIds)->delete();
        }
    }
}
