<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\InventoryLog;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductColorImage;
use App\Models\ProductVariant;
use App\Services\ProductImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminInventoryController extends Controller
{
    /**
     * Display the main inventory control center.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('q', ''));
        $status = $request->input('status', 'all');
        $categoryId = $request->input('category_id');

        // Query variants with parent product, color, and category
        $query = ProductVariant::with(['product.category', 'color', 'product.colors.images'])
            ->whereHas('product');

        // Search by SKU (Variant SKU or Base SKU) or Product Name
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('sku', 'like', "%{$search}%")
                  ->orWhere('size', 'like', "%{$search}%")
                  ->orWhereHas('product', function ($pq) use ($search) {
                      $pq->where('name', 'like', "%{$search}%")
                         ->orWhere('sku', 'like', "%{$search}%")
                         ->orWhere('slug', 'like', "%{$search}%");
                  });
            });
        }

        // Category filter
        if (!empty($categoryId)) {
            $query->whereHas('product', function ($pq) use ($categoryId) {
                $pq->where('category_id', $categoryId);
            });
        }

        // Stock status filter
        if ($status === 'out_of_stock') {
            $query->where('stock', 0);
        } elseif ($status === 'low_stock') {
            $query->where('stock', '>', 0)->where('stock', '<=', 5);
        } elseif ($status === 'in_stock') {
            $query->where('stock', '>', 5);
        }

        $variants = $query->orderBy('stock', 'asc')
            ->paginate(25)
            ->withQueryString();

        // High-level KPI metrics
        $totalSkus        = ProductVariant::count();
        $totalUnits       = (int) ProductVariant::sum('stock');
        $lowStockCount    = ProductVariant::where('stock', '>', 0)->where('stock', '<=', 5)->count();
        $outOfStockCount  = ProductVariant::where('stock', 0)->count();
        $totalValuation   = (float) ProductVariant::selectRaw('SUM(stock * price) as val')->value('val') ?? 0;

        $categories = Category::orderBy('sort_order')->get();

        return view('admin.inventory.index', compact(
            'variants',
            'categories',
            'search',
            'status',
            'categoryId',
            'totalSkus',
            'totalUnits',
            'lowStockCount',
            'outOfStockCount',
            'totalValuation'
        ));
    }

    /**
     * Update stock for a specific SKU variant (supports AJAX & standard form).
     */
    public function updateStock(Request $request, ProductVariant $variant): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'stock'      => 'nullable|integer|min:0',
            'change_qty' => 'nullable|integer',
            'type'       => 'nullable|string|in:manual_adjustment,restock',
            'notes'      => 'nullable|string|max:255',
        ]);

        $type  = $validated['type'] ?? 'manual_adjustment';
        $notes = $validated['notes'] ?? 'Admin Inventory Center update';

        if (isset($validated['stock'])) {
            $variant->setStock((int) $validated['stock'], $type, $notes, Auth::id());
        } elseif (isset($validated['change_qty'])) {
            $variant->adjustStock((int) $validated['change_qty'], $type, $notes, Auth::id());
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success'    => true,
                'new_stock'  => $variant->stock,
                'in_stock'   => $variant->in_stock,
                'is_low'     => $variant->is_low_stock,
                'message'    => "Stock updated for SKU {$variant->sku}. Current stock: {$variant->stock} units.",
            ]);
        }

        return back()->with('success', "Stock updated for SKU {$variant->sku}. Current stock: {$variant->stock} units.");
    }

    /**
     * Add a brand new product directly from the Inventory Center via SKU,
     * and automatically publish it to the storefront site.
     */
    public function quickStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'sku'              => 'nullable|string|max:60|unique:products,sku',
            'name'             => 'required|string|max:255',
            'category_id'      => 'required|exists:categories,id',
            'price'            => 'required|numeric|min:0',
            'compare_at_price' => 'nullable|numeric|min:0',
            'stock'            => 'required|integer|min:0',
            'color_name'       => 'nullable|string|max:50',
            'color_hex'        => 'nullable|string|max:9',
            'size'             => 'nullable|string|max:10',
            'variant_sku'      => 'nullable|string|max:60|unique:product_variants,sku',
            'is_active'        => 'nullable|boolean',
            'image'            => 'nullable|file|mimes:jpeg,png,jpg,webp,avif,gif|mimetypes:image/jpeg,image/png,image/webp,image/avif,image/gif|max:4096',
            'description'      => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $colorName = !empty($validated['color_name']) ? trim($validated['color_name']) : 'Classic Black';
            $colorHex  = !empty($validated['color_hex']) ? trim($validated['color_hex']) : '#18181B';
            $size      = !empty($validated['size']) ? strtoupper(trim($validated['size'])) : 'M';

            // 1. Create Base Product
            $baseSku = !empty($validated['sku'])
                ? strtoupper(trim($validated['sku']))
                : 'LS-' . strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $validated['name']), 0, 3)) . '-' . strtoupper(Str::random(4));

            $product = Product::create([
                'name'          => $validated['name'],
                'sku'           => $baseSku,
                'slug'          => Str::slug($validated['name']) . '-' . Str::random(4),
                'category_id'   => $validated['category_id'],
                'description'   => $validated['description'] ?? 'Luxurious intimate piece with superior fit and crafted detail.',
                'is_active'     => $request->boolean('is_active', true),
                'is_featured'   => false,
                'is_bestseller' => false,
            ]);

            // 2. Create Initial Color
            $color = ProductColor::create([
                'product_id' => $product->id,
                'name'       => $colorName,
                'hex'        => $colorHex,
                'sort_order' => 0,
            ]);

            // Optional Image Upload
            if ($request->hasFile('image')) {
                $path = ProductImageService::store($request->file('image'), $product);
                ProductColorImage::create([
                    'product_color_id' => $color->id,
                    'image_path'       => $path,
                    'sort_order'       => 0,
                ]);
            }

            // 3. Create Variant with Variant SKU
            $variantSku = !empty($validated['variant_sku'])
                ? strtoupper(trim($validated['variant_sku']))
                : ($baseSku . '-' . strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $colorName), 0, 3)) . '-' . $size);

            $variant = ProductVariant::create([
                'product_id'       => $product->id,
                'product_color_id' => $color->id,
                'size'             => $size,
                'sku'              => $variantSku,
                'price'            => $validated['price'],
                'compare_at_price' => $validated['compare_at_price'] ?? null,
                'stock'            => $validated['stock'],
            ]);

            // 4. Log Initial Inventory Stock Movement
            InventoryLog::create([
                'product_variant_id' => $variant->id,
                'sku'                => $variant->sku,
                'change_qty'         => (int) $validated['stock'],
                'new_stock'          => (int) $validated['stock'],
                'type'               => 'initial',
                'notes'              => "Initial inventory creation for '{$product->name}'",
                'created_by'         => Auth::id(),
            ]);
        });

        $statusMsg = $request->boolean('is_active', true)
            ? 'and is now LIVE on your storefront!'
            : 'and saved as a draft in inventory.';

        return redirect()->route('admin.inventory.index')->with('success', "New product SKU successfully added to inventory {$statusMsg}");
    }

    /**
     * Get stock movement audit logs for a specific variant SKU.
     */
    public function logs(ProductVariant $variant): JsonResponse
    {
        $logs = $variant->inventoryLogs()
            ->with('user:id,name,email')
            ->get()
            ->map(fn ($log) => [
                'id'         => $log->id,
                'sku'        => $log->sku,
                'change_qty' => ($log->change_qty > 0 ? '+' : '') . $log->change_qty,
                'new_stock'  => $log->new_stock,
                'type_label' => $log->type_label,
                'notes'      => $log->notes,
                'user'       => $log->user ? $log->user->name : 'System / Customer',
                'date'       => $log->created_at->format('d M Y, h:i A'),
            ]);

        return response()->json([
            'success' => true,
            'sku'     => $variant->sku,
            'product' => $variant->product->name ?? 'N/A',
            'size'    => $variant->size,
            'stock'   => $variant->stock,
            'logs'    => $logs,
        ]);
    }

    /**
     * Export complete inventory spreadsheet as CSV.
     */
    public function exportCsv(): Response
    {
        $variants = ProductVariant::with(['product.category', 'color'])
            ->orderBy('sku', 'asc')
            ->get();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="inventory-report-' . date('Y-m-d') . '.csv"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $handle = fopen('php://output', 'w');
        ob_start();

        // UTF-8 BOM for Excel compatibility
        fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

        // Header row
        fputcsv($handle, [
            'Variant SKU',
            'Base Product SKU',
            'Product Name',
            'Category',
            'Color',
            'Size',
            'Current Stock',
            'Unit Price (INR)',
            'MRP (INR)',
            'Stock Valuation (INR)',
            'Stock Status',
            'Storefront Status',
        ]);

        foreach ($variants as $v) {
            $stockStatus = 'In Stock';
            if ($v->stock === 0) {
                $stockStatus = 'Out of Stock';
            } elseif ($v->stock <= 5) {
                $stockStatus = 'Low Stock';
            }

            fputcsv($handle, [
                $v->sku ?? 'N/A',
                $v->product->sku ?? 'N/A',
                $v->product->name ?? 'N/A',
                $v->product->category->name ?? 'Uncategorized',
                $v->color->name ?? 'N/A',
                $v->size ?? 'N/A',
                $v->stock,
                $v->price,
                $v->compare_at_price ?? $v->price,
                $v->stock * $v->price,
                $stockStatus,
                ($v->product && $v->product->is_active) ? 'Live' : 'Draft',
            ]);
        }

        fclose($handle);
        $content = ob_get_clean();

        return response($content, 200, $headers);
    }
}
