<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CustomerAddress;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductVariant;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminPosController extends Controller
{
    /**
     * Display the full-screen POS Cashier Terminal.
     */
    public function index(): View
    {
        $categories = Category::orderBy('sort_order', 'asc')->orderBy('name', 'asc')->get();

        $products = Product::where('is_active', true)
            ->with(['category', 'colors.images', 'colors.variants'])
            ->latest()
            ->get();

        // Map products into optimized JSON-ready catalog for fast client-side filtering & scanner lookup
        $catalog = $products->map(function ($product) {
            $variants = [];
            foreach ($product->colors as $color) {
                foreach ($color->variants as $variant) {
                    $variants[] = [
                        'id'         => $variant->id,
                        'sku'        => $variant->sku,
                        'size'       => $variant->size,
                        'color_name' => $color->name,
                        'color_hex'  => $color->hex,
                        'price'      => (float) $variant->price,
                        'stock'      => (int) $variant->stock,
                        'image'      => $color->cover_image ?: $product->cover_image,
                    ];
                }
            }

            return [
                'id'          => $product->id,
                'name'        => $product->name,
                'sku'         => $product->sku,
                'slug'        => $product->slug,
                'category_id' => $product->category_id,
                'category'    => $product->category?->name ?? 'Uncategorized',
                'base_price'  => (float) ($product->starting_price ?? 0),
                'cover_image' => $product->cover_image,
                'variants'    => $variants,
            ];
        });

        // Recent 8 POS orders for fast re-print and status glance
        $recentOrders = Order::where('channel', 'pos')
            ->with(['items', 'posCashier'])
            ->latest()
            ->take(8)
            ->get();

        // Recent retail customers for fast auto-complete
        $recentCustomers = User::where('role', 'customer')
            ->latest()
            ->take(15)
            ->get(['id', 'name', 'email']);

        $settings = [
            'store_name'    => Setting::get('store_name', config('app.name', 'LUXURY STORE')),
            'store_phone'   => Setting::get('store_phone', '+91 98765 43210'),
            'store_address' => Setting::get('store_address', '101, Luxury Galleria, Indiranagar, Bengaluru, KA - 560038'),
            'store_gstin'   => Setting::get('store_gstin', '29AAAAA0000A1Z5'),
            'currency'      => '₹',
        ];

        return view('admin.pos.index', compact('categories', 'catalog', 'recentOrders', 'recentCustomers', 'settings'));
    }

    /**
     * Fast JSON search endpoint for barcode scanner and instant SKU lookup.
     */
    public function searchProducts(Request $request): JsonResponse
    {
        $query = trim($request->get('q', ''));
        $categoryId = $request->get('category_id');

        $variantQuery = ProductVariant::with(['product.category', 'color.images', 'product.colors.images'])
            ->whereHas('product', function ($q) {
                $q->where('is_active', true);
            });

        if (!empty($categoryId)) {
            $variantQuery->whereHas('product', function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            });
        }

        if (!empty($query)) {
            $variantQuery->where(function ($q) use ($query) {
                $q->where('sku', 'LIKE', "%{$query}%")
                  ->orWhereHas('product', function ($pq) use ($query) {
                      $pq->where('name', 'LIKE', "%{$query}%")
                         ->orWhere('sku', 'LIKE', "%{$query}%");
                  });
            });
        }

        $variants = $variantQuery->take(30)->get();

        $results = $variants->map(function ($v) {
            $image = $v->color?->cover_image ?: ($v->product?->cover_image ?: asset('images/placeholder.jpg'));

            return [
                'variant_id'   => $v->id,
                'product_id'   => $v->product_id,
                'sku'          => $v->sku,
                'product_name' => $v->product->name,
                'category'     => $v->product->category?->name ?? 'General',
                'color_name'   => $v->color?->name ?? 'Standard',
                'color_hex'    => $v->color?->hex ?? '#333333',
                'size'         => $v->size,
                'price'        => (float) $v->price,
                'stock'        => (int) $v->stock,
                'image'        => $image,
            ];
        });

        return response()->json([
            'success' => true,
            'count'   => $results->count(),
            'data'    => $results,
        ]);
    }

    /**
     * Quick register a walk-in retail customer.
     */
    public function quickStoreCustomer(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:120',
            'phone'    => 'required|string|max:20',
            'email'    => 'nullable|email|max:150',
            'address'  => 'nullable|string|max:255',
            'pincode'  => 'nullable|string|max:10',
            'city'     => 'nullable|string|max:100',
            'state'    => 'nullable|string|max:100',
        ]);

        $email = !empty($validated['email'])
            ? strtolower(trim($validated['email']))
            : 'walkin_' . preg_replace('/[^0-9]/', '', $validated['phone']) . '@retail.pos';

        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name'     => $validated['name'],
                'role'     => 'customer',
                'password' => Hash::make(Str::random(16)),
            ]
        );

        if (!empty($validated['address']) || !empty($validated['city']) || !empty($validated['phone'])) {
            CustomerAddress::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'phone'   => $validated['phone'],
                ],
                [
                    'name'       => $validated['name'],
                    'address1'   => $validated['address'] ?? 'Counter Checkout',
                    'city'       => $validated['city'] ?? 'Local',
                    'state'      => $validated['state'] ?? 'KA',
                    'pincode'    => $validated['pincode'] ?? '000000',
                    'type'       => 'shipping',
                    'is_default' => true,
                ]
            );
        }

        return response()->json([
            'success'  => true,
            'message'  => 'Customer attached successfully',
            'customer' => [
                'id'      => $user->id,
                'name'    => $user->name,
                'phone'   => $validated['phone'],
                'email'   => $user->email,
                'address' => $validated['address'] ?? '',
                'city'    => $validated['city'] ?? '',
                'pincode' => $validated['pincode'] ?? '',
            ],
        ]);
    }

    /**
     * High-speed POS order completion endpoint with instant stock deduction.
     */
    public function checkout(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'items'                 => 'required|array|min:1',
            'items.*.variant_id'    => 'required|exists:product_variants,id',
            'items.*.qty'           => 'required|integer|min:1',
            'items.*.price'         => 'required|numeric|min:0',
            'customer_name'         => 'nullable|string|max:120',
            'customer_phone'        => 'nullable|string|max:20',
            'customer_email'        => 'nullable|email|max:150',
            'customer_id'           => 'nullable|exists:users,id',
            'payment_method'        => 'required|in:cash,card,upi',
            'tendered_amount'       => 'nullable|numeric|min:0',
            'discount'              => 'nullable|numeric|min:0',
            'notes'                 => 'nullable|string|max:255',
        ]);

        return DB::transaction(function () use ($validated) {
            $subtotal = 0;
            $orderItemsData = [];

            // 1. Verify and lock variants for atomic stock decrement
            foreach ($validated['items'] as $itemInput) {
                $variant = ProductVariant::with(['product', 'color'])->lockForUpdate()->findOrFail($itemInput['variant_id']);

                if ($variant->stock < $itemInput['qty']) {
                    return response()->json([
                        'success' => false,
                        'message' => "Insufficient stock for SKU [{$variant->sku}]. Available: {$variant->stock}, Requested: {$itemInput['qty']}",
                    ], 422);
                }

                $unitPrice = (int) $itemInput['price'];
                $qty = (int) $itemInput['qty'];
                $lineTotal = $unitPrice * $qty;
                $subtotal += $lineTotal;

                $orderItemsData[] = [
                    'variant'    => $variant,
                    'qty'        => $qty,
                    'price'      => $unitPrice,
                    'line_total' => $lineTotal,
                ];
            }

            $discount = (int) ($validated['discount'] ?? 0);
            $total = max(0, $subtotal - $discount);
            $paymentMethod = $validated['payment_method'];

            $tendered = (int) ($validated['tendered_amount'] ?? $total);
            if ($paymentMethod === 'cash' && $tendered < $total) {
                return response()->json([
                    'success' => false,
                    'message' => "Tendered cash (₹{$tendered}) cannot be less than total (₹{$total}).",
                ], 422);
            }
            $change = $paymentMethod === 'cash' ? max(0, $tendered - $total) : 0;

            // Generate POS sequence reference
            $date = now()->format('Ymd');
            $countToday = Order::whereDate('created_at', today())->where('channel', 'pos')->count() + 1;
            $reference = 'POS-' . $date . '-' . str_pad($countToday, 4, '0', STR_PAD_LEFT);

            $customerName  = $validated['customer_name'] ?? 'Walk-in Customer';
            $customerPhone = $validated['customer_phone'] ?? '9999999999';
            $customerEmail = $validated['customer_email'] ?? 'walkin@pos.store';

            $order = Order::create([
                'user_id'           => $validated['customer_id'] ?? null,
                'pos_cashier_id'    => Auth::id(),
                'reference'         => $reference,
                'channel'           => 'pos',
                'status'            => Order::STATUS_DELIVERED,
                'subtotal'          => $subtotal,
                'shipping_charge'   => 0,
                'cod_charge'        => 0,
                'discount'          => $discount,
                'total'             => $total,
                'tendered_amount'   => $tendered,
                'change_amount'     => $change,
                'payment_method'    => $paymentMethod,
                'payment_status'    => Order::PAYMENT_PAID,
                'payment_reference' => $paymentMethod === 'cash' ? 'CASH-TENDERED' : strtoupper($paymentMethod) . '-' . Str::random(8),
                'shipping_name'     => $customerName,
                'shipping_phone'    => $customerPhone,
                'shipping_email'    => $customerEmail,
                'shipping_address1' => 'Over The Counter POS',
                'shipping_city'     => 'Storefront',
                'shipping_state'    => 'Storefront',
                'shipping_pincode'  => '560001',
                'notes'             => $validated['notes'] ?? 'Walk-in retail POS checkout',
                'delivered_at'      => now(),
            ]);

            // Deduct inventory and write inventory logs
            foreach ($orderItemsData as $item) {
                /** @var ProductVariant $variant */
                $variant = $item['variant'];
                $qty = $item['qty'];

                $variant->adjustStock(
                    -$qty,
                    'order_deduction',
                    "POS Walk-in Sale Order #{$order->reference}",
                    Auth::id()
                );

                OrderItem::create([
                    'order_id'           => $order->id,
                    'product_id'         => $variant->product_id,
                    'product_color_id'   => $variant->product_color_id,
                    'product_variant_id' => $variant->id,
                    'sku'                => $variant->sku,
                    'product_name'       => $variant->product->name,
                    'product_slug'       => $variant->product->slug,
                    'color_name'         => $variant->color?->name ?? 'Standard',
                    'color_hex'          => $variant->color?->hex ?? '#000000',
                    'size'               => $variant->size,
                    'cover_image'        => $variant->color?->image_url ?? $variant->product->cover_image_url,
                    'price'              => $item['price'],
                    'qty'                => $qty,
                    'line_total'         => $item['line_total'],
                ]);
            }

            return response()->json([
                'success'         => true,
                'message'         => 'POS Order completed successfully!',
                'order_id'        => $order->id,
                'reference'       => $order->reference,
                'total'           => $total,
                'tendered_amount' => $tendered,
                'change_amount'   => $change,
                'receipt_url'     => route('admin.pos.receipt', $order->id),
            ]);
        });
    }

    /**
     * ESC/POS Thermal Receipt Generation (80mm & 58mm formats)
     */
    public function receipt(Order $order, Request $request): View
    {
        $order->load(['items.variant', 'posCashier']);
        $format = $request->get('format', '80mm'); // '80mm' or '58mm'

        $storeInfo = [
            'name'    => Setting::appName(),
            'phone'   => Setting::supportPhone(),
            'address' => Setting::storeAddress(),
            'gstin'   => Setting::gstin(),
            'footer'  => Setting::get('pos_receipt_footer', Setting::invoiceFooter() . ' Returns within 7 days with original tag & receipt.'),
        ];

        return view('admin.pos.receipt', compact('order', 'format', 'storeInfo'));
    }
}
