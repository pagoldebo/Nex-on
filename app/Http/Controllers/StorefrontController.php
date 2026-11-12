<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Coupon;
use App\Models\CustomerAddress;
use App\Models\Order;
use App\Models\OrderRequest;
use App\Models\PincodeZone;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\Setting;
use App\Models\Subscriber;
use App\Models\Template;
use App\Models\Wishlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;

/**
 * Class StorefrontController
 *
 * Primary controller for all customer-facing storefront routes.
 * Dynamically resolves the active lifestyle theme (vyra, buttercup, saaya, lumiere, organic/verdia),
 * eager-loads product matrices to prevent N+1 queries, and handles cart, wishlist,
 * reviews, customer orders, and checkout interactions.
 *
 * @package App\Http\Controllers
 */
class StorefrontController extends Controller
{
    /**
     * Resolves the active template instance.
     * Views resolve strictly as: storefront.{theme->key}.{page}
     *
     * @return \App\Models\Template
     */
    private function theme(): Template
    {
        return Template::active();
    }

    /**
     * Base query for active products with eager-loaded relationships.
     * Prevents N+1 queries when calling $product->toCardArray() across collection cards.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function productQuery()
    {
        return Product::with(['category.parent.parent.parent', 'brand', 'colors.images', 'colors.variants'])
            ->where('is_active', true);
    }

    /**
     * Sanitizes comma-separated integer IDs from query string parameters.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return int[]
     */
    private function idsFromRequest(Request $request): array
    {
        return array_values(array_filter(array_map('intval', explode(',', (string) $request->get('ids', '')))));
    }

    /**
     * Renders the storefront homepage for the active theme.
     * Passes categories, featured collections, bestsellers, new arrivals, and client reviews.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $theme            = $this->theme();
        $categories       = Category::whereNull('parent_id')->with(['children.children', 'products'])->orderBy('sort_order')->get();
        $featured         = $this->productQuery()->where('is_featured', true)->latest()->take(8)->get();
        $featuredProducts = $featured;
        $bestsellers      = $this->productQuery()->where('is_bestseller', true)->latest()->take(8)->get();
        $newArrivals      = $this->productQuery()->latest()->take(8)->get();
        $saleProducts     = $this->productQuery()
            ->where(function ($q) {
                $q->whereHas('variants', function ($v) {
                    $v->whereNotNull('compare_at_price')
                      ->whereColumn('compare_at_price', '>', 'price');
                })->orWhere('badge', 'like', '%SALE%')
                  ->orWhere('badge', 'like', '%OFF%')
                  ->orWhere('badge', 'like', '%DEAL%');
            })
            ->latest()
            ->take(12)
            ->get();

        if ($saleProducts->isEmpty()) {
            $saleProducts = $this->productQuery()->latest()->take(8)->get();
        }

        $reviews = ProductReview::where('is_approved', true)
            ->with(['product', 'user.addresses'])
            ->latest()
            ->take(6)
            ->get();

        return view("storefront.{$theme->key}.index", compact('theme', 'categories', 'featured', 'featuredProducts', 'bestsellers', 'newArrivals', 'saleProducts', 'reviews'));
    }

    public function product(Product $product)
    {
        $theme = $this->theme();
        $product->load(['category', 'brand', 'addons', 'colors.images', 'colors.variants', 'approvedReviews.user']);

        $related = $this->productQuery()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(4)->get();
        $relatedProducts = $related;
        $categories      = Category::orderBy('sort_order')->get();

        return view("storefront.{$theme->key}.product", compact('theme', 'product', 'related', 'relatedProducts', 'categories'));
    }

    public function category(Request $request, Category $category)
    {
        $theme = $this->theme();

        $currentSort = $request->get('sort', 'latest');
        $sortOptions = [
            'latest'     => 'Latest Arrivals',
            'price_asc'  => 'Price: Low to High',
            'price_desc' => 'Price: High to Low',
        ];

        $category->load(['parent.parent', 'children.children']);
        $descendantIds = $category->getAllDescendantIds();

        // Price lives on variants, so sort by each product's cheapest variant.
        $query = $this->productQuery()
            ->whereIn('category_id', $descendantIds)
            ->withMin('variants', 'price');

        match ($currentSort) {
            'price_asc'  => $query->orderBy('variants_min_price')->orderBy('id'),
            'price_desc' => $query->orderByDesc('variants_min_price')->orderByDesc('id'),
            default      => $query->latest(),
        };

        $products      = $query->paginate(16)->withQueryString();
        $categories    = Category::whereNull('parent_id')->with('children.children')->orderBy('sort_order')->get();
        $subcategories = $category->children;
        $breadcrumbs   = $category->getBreadcrumbs();

        return view("storefront.{$theme->key}.category", compact('theme', 'category', 'products', 'categories', 'subcategories', 'breadcrumbs', 'currentSort', 'sortOptions'));
    }

    /**
     * Dedicated listing for Featured Products (/featured-products, /collections/featured-products).
     */
    public function featuredProducts(Request $request)
    {
        $hasFeatured = $this->productQuery()->where('is_featured', true)->exists();
        $query = $hasFeatured
            ? $this->productQuery()->where('is_featured', true)
            : $this->productQuery();

        return $this->renderCollectionPage(
            $request,
            $query,
            'Featured Products',
            'featured-products',
            'Explore our handcrafted selection of iconic pieces and signature essentials.'
        );
    }

    /**
     * Dedicated listing for Sale & Promotional Offers (/collections/sale, /sale).
     */
    public function saleProductsList(Request $request)
    {
        $query = $this->productQuery()->where(function ($q) {
            $q->whereHas('variants', function ($v) {
                $v->whereNotNull('compare_at_price')
                  ->whereColumn('compare_at_price', '>', 'price');
            })->orWhere('badge', 'like', '%SALE%')
              ->orWhere('badge', 'like', '%OFF%')
              ->orWhere('badge', 'like', '%DEAL%');
        });

        if (! (clone $query)->exists()) {
            $query = $this->productQuery();
        }

        return $this->renderCollectionPage(
            $request,
            $query,
            'Sale & Special Offers',
            'collections/sale',
            'Exclusive seasonal discounts and limited-time promotional offers on fine lingerie.'
        );
    }

    /**
     * Dedicated listing for New Launches & Arrivals (/new-arrivals, /collections/new-arrivals, /new-launches).
     */
    public function newArrivalsList(Request $request)
    {
        $query = $this->productQuery()->latest();

        return $this->renderCollectionPage(
            $request,
            $query,
            'New Arrivals & Launches',
            'new-arrivals',
            'The freshest additions to our collection. Modern aesthetics, exquisite craftsmanship.'
        );
    }

    /**
     * Generic collection resolver (/collections/{slug}).
     */
    public function collectionBySlug(Request $request, string $slug)
    {
        return match (strtolower($slug)) {
            'sale', 'deals', 'special-offers' => $this->saleProductsList($request),
            'featured-products', 'featured'   => $this->featuredProducts($request),
            'new-arrivals', 'new-launches', 'new' => $this->newArrivalsList($request),
            'all'                             => $this->renderCollectionPage(
                $request,
                $this->productQuery(),
                'All Products',
                'collections/all',
                'Explore our complete catalog of signature pieces.'
            ),
            default                           => Category::where('slug', $slug)->first()
                                                    ? $this->category($request, Category::where('slug', $slug)->firstOrFail())
                                                    : abort(404),
        };
    }

    /**
     * Helper to render theme-faithful collection catalog with sorting and pagination.
     */
    protected function renderCollectionPage(Request $request, $query, string $title, string $slug, string $description = '')
    {
        $theme = $this->theme();

        $currentSort = $request->get('sort', 'latest');
        $sortOptions = [
            'latest'     => 'Latest Arrivals',
            'price_asc'  => 'Price: Low to High',
            'price_desc' => 'Price: High to Low',
        ];

        // Price lives on variants, so sort by each product's cheapest variant.
        $query->withMin('variants', 'price');

        match ($currentSort) {
            'price_asc'  => $query->orderBy('variants_min_price')->orderBy('id'),
            'price_desc' => $query->orderByDesc('variants_min_price')->orderByDesc('id'),
            default      => $query->latest(),
        };

        $products      = $query->paginate(16)->withQueryString();
        $categories    = Category::whereNull('parent_id')->with('children.children')->orderBy('sort_order')->get();
        $category      = new Category([
            'name' => $title,
            'slug' => $slug,
        ]);
        $category->id = 0;
        $category->description = $description;
        $category->setRelation('children', collect([]));

        $subcategories = collect([]);
        $breadcrumbs   = [
            (object) ['name' => $title, 'slug' => $slug],
        ];

        return view("storefront.{$theme->key}.category", compact(
            'theme',
            'category',
            'products',
            'categories',
            'subcategories',
            'breadcrumbs',
            'currentSort',
            'sortOptions'
        ));
    }

    public function cart()
    {
        $theme = $this->theme();
        $freeShippingThreshold = (int) Setting::get('free_shipping_threshold', 1499);
        $shippingCost          = (int) Setting::get('shipping_cost', 99);
        $codCharge             = (int) Setting::get('cod_charge', 40);

        return view("storefront.{$theme->key}.cart", compact('theme', 'freeShippingThreshold', 'shippingCost', 'codCharge'));
    }

    public function account()
    {
        $theme = $this->theme();
        $user  = Auth::user();

        if (! $user) {
            return redirect()->route('login');
        }

        $orders    = $user->orders()->with(['items', 'requests'])->latest()->take(20)->get();
        $addresses = $user->addresses()->orderByDesc('is_default')->get();

        return view("storefront.{$theme->key}.account", compact('theme', 'user', 'orders', 'addresses'));
    }

    public function wishlist(Request $request)
    {
        $theme = $this->theme();
        $ids   = $this->idsFromRequest($request);

        if (Auth::check() && method_exists(Auth::user(), 'wishlists')) {
            $ids = array_unique(array_merge($ids, Auth::user()->wishlists()->pluck('product_id')->all()));
        }

        $wishlistProducts = $ids ? $this->productQuery()->whereIn('id', $ids)->get() : collect();
        $products         = $wishlistProducts;
        $categories       = Category::orderBy('sort_order')->get();

        return view("storefront.{$theme->key}.wishlist", compact('theme', 'wishlistProducts', 'products', 'categories'));
    }

    public function checkout()
    {
        $theme                 = $this->theme();
        $user                  = Auth::user();
        $addresses             = $user ? $user->addresses()->orderByDesc('is_default')->get() : collect();
        $freeShippingThreshold = (int) Setting::get('free_shipping_threshold', 1499);
        $shippingCost          = (int) Setting::get('shipping_cost', 99);
        $codCharge             = (int) Setting::get('cod_charge', 40);
        $enableCod             = Setting::get('enable_cod', '1') === '1';
        $customerGroup         = $user ? $user->customerGroups()->where('is_active', true)->first() : null;

        return view("storefront.{$theme->key}.checkout", compact(
            'theme',
            'user',
            'addresses',
            'freeShippingThreshold',
            'shippingCost',
            'codCharge',
            'enableCod',
            'customerGroup'
        ));
    }

    /**
     * Storefront Checkout to Database Bridge (POST /checkout/place-order).
     */
    public function placeOrder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'shipping.name'     => 'required|string|max:120',
            'shipping.phone'    => 'required|string|max:15',
            'shipping.email'    => 'required|email|max:120',
            'shipping.address1' => 'required|string|max:255',
            'shipping.address2' => 'nullable|string|max:255',
            'shipping.city'     => 'required|string|max:100',
            'shipping.state'    => 'required|string|max:100',
            'shipping.pincode'  => 'required|string|max:10',
            'payment'           => 'required|string|in:cod,upi,card',
            'items'             => 'required|array|min:1',
            'subtotal'          => 'required|numeric|min:0',
            'discount'          => 'nullable|numeric|min:0',
            'coupon_code'       => 'nullable|string|max:50',
            'save_address'      => 'nullable|boolean',
        ]);

        $userId = Auth::id();
        $order  = Order::createFromCheckout($request->all(), $userId);

        // Save address to user address book if requested or if user has no saved addresses
        if ($userId) {
            $shipping = $request->input('shipping', []);
            $hasAddress = CustomerAddress::where('user_id', $userId)->exists();
            if ($request->boolean('save_address') || ! $hasAddress) {
                CustomerAddress::firstOrCreate([
                    'user_id'   => $userId,
                    'address1'  => $shipping['address1'] ?? '',
                    'pincode'   => $shipping['pincode'] ?? '',
                ], [
                    'name'       => $shipping['name'] ?? '',
                    'phone'      => $shipping['phone'] ?? '',
                    'address2'   => $shipping['address2'] ?? null,
                    'city'       => $shipping['city'] ?? '',
                    'state'      => $shipping['state'] ?? '',
                    'is_default' => ! $hasAddress,
                ]);
            }
        }

        // Dispatch transactional emails
        try {
            Mail::send('emails.order-confirmation', ['order' => $order], function ($m) use ($order) {
                $m->to($order->shipping_email, $order->shipping_name)
                  ->subject('Order Confirmed: #' . $order->reference . ' — Saaya');
            });

            $adminEmail = Setting::get('support_email', 'support@saaya.in');
            Mail::send('emails.new-order-admin', ['order' => $order], function ($m) use ($adminEmail, $order) {
                $m->to($adminEmail)
                  ->subject('New Order Placed: #' . $order->reference . ' (₹' . number_format($order->total, 2) . ')');
            });
        } catch (\Exception $e) {
            Log::warning('Order email dispatch error: ' . $e->getMessage());
        }

        // Dispatch instant push alerts to webhooks (Discord / Slack / Harold)
        try {
            \App\Services\WebhookService::sendOrderNotification($order);
        } catch (\Exception $e) {
            Log::warning('Order webhook dispatch error: ' . $e->getMessage());
        }

        // Store placed order credentials in session for guest order confirmation access control
        session([
            'recent_order_id'  => $order->id,
            'recent_order_ref' => $order->reference,
        ]);

        return response()->json([
            'success'      => true,
            'order_id'     => $order->id,
            'reference'    => $order->reference,
            'redirect_url' => route('order.confirmation', $order->id),
            'order'        => [
                'id'        => $order->id,
                'reference' => $order->reference,
                'total'     => $order->total,
                'subtotal'  => $order->subtotal,
                'shipping'  => [
                    'name'     => $order->shipping_name,
                    'phone'    => $order->shipping_phone,
                    'email'    => $order->shipping_email,
                    'address1' => $order->shipping_address1,
                    'address2' => $order->shipping_address2,
                    'city'     => $order->shipping_city,
                    'state'    => $order->shipping_state,
                    'pincode'  => $order->shipping_pincode,
                ],
                'items'     => $order->items->map(fn ($i) => [
                    'name'      => $i->product_name,
                    'colorName' => $i->color_name,
                    'size'      => $i->size,
                    'qty'       => $i->qty,
                    'price'     => $i->price,
                    'image'     => $i->cover_image,
                ]),
                'payment'   => $order->payment_method,
            ],
        ]);
    }

    public function orderConfirmation($order)
    {
        $theme      = $this->theme();
        $orderModel = Order::with(['items.product', 'items.productColor', 'requests'])
            ->where('id', $order)
            ->orWhere('reference', $order)
            ->firstOrFail();

        // Broken Object Level Authorization (BOLA) Check:
        // Only the order owner, an administrator, or the guest session that placed this order may access it.
        $isOwner = Auth::check() && $orderModel->user_id && $orderModel->user_id === Auth::id();
        $isAdmin = Auth::check() && Auth::user()->isAdmin();
        $isSessionGuest = session('recent_order_id') == $orderModel->id || session('recent_order_ref') === $orderModel->reference;

        if (! $isOwner && ! $isAdmin && ! $isSessionGuest) {
            abort(403, 'Unauthorized access to order confirmation.');
        }

        return view("storefront.{$theme->key}.order-confirmation", compact('theme', 'order', 'orderModel'));
    }

    /**
     * Apply coupon endpoint (POST /coupon/apply).
     */
    public function applyCoupon(Request $request): JsonResponse
    {
        $couponThrottleKey = 'coupon_guess:' . $request->ip();
        if (RateLimiter::tooManyAttempts($couponThrottleKey, 10)) {
            $seconds = RateLimiter::availableIn($couponThrottleKey);
            return response()->json([
                'success' => false,
                'message' => "Too many invalid coupon attempts. Please try again in {$seconds} seconds.",
            ], 429);
        }

        $code     = strtoupper(trim((string) $request->input('code', '')));
        $subtotal = (float) $request->input('subtotal', 0);

        if ($code === '') {
            return response()->json(['success' => false, 'message' => 'Please enter a coupon code.'], 422);
        }

        $coupon = Coupon::where('code', $code)->first();
        if (! $coupon) {
            RateLimiter::hit($couponThrottleKey, 60);
            return response()->json(['success' => false, 'message' => 'Invalid promotional code.'], 404);
        }

        RateLimiter::clear($couponThrottleKey);

        $check = $coupon->isValidForAmount($subtotal);
        if (! $check['valid']) {
            return response()->json(['success' => false, 'message' => $check['message']], 422);
        }

        $discount = $coupon->calculateDiscount($subtotal);
        $newTotal = max(0, $subtotal - $discount);

        return response()->json([
            'success'   => true,
            'code'      => $coupon->code,
            'type'      => $coupon->type,
            'value'     => (float) $coupon->value,
            'discount'  => $discount,
            'new_total' => $newTotal,
            'message'   => $check['message'],
        ]);
    }

    /**
     * Remove coupon endpoint (POST /coupon/remove).
     */
    public function removeCoupon(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Coupon has been removed.',
        ]);
    }

    /**
     * Check active customer group tier & discount for authenticated customer.
     */
    public function checkCustomerGroup(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['has_group' => false, 'discount' => 0]);
        }

        $group = $user->customerGroups()->where('is_active', true)->first();
        if (! $group) {
            return response()->json(['has_group' => false, 'discount' => 0]);
        }

        $subtotal = (float) $request->input('subtotal', 0);
        $discount = $group->calculateDiscount($subtotal);

        return response()->json([
            'has_group'      => true,
            'group_id'       => $group->id,
            'group_name'     => $group->name,
            'discount_type'  => $group->discount_type,
            'discount_value' => (float) $group->discount_value,
            'min_spend'      => (float) $group->min_spend,
            'discount'       => $discount,
            'qualifies'      => $subtotal >= (float) $group->min_spend,
        ]);
    }

    /**
     * Pincode serviceability & ETA checker (POST /pincode/check).
     */
    public function checkPincode(Request $request): JsonResponse
    {
        $pincode = trim((string) $request->input('pincode', ''));
        if (strlen($pincode) !== 6 || ! ctype_digit($pincode)) {
            return response()->json([
                'success' => false,
                'message' => 'Please enter a valid 6-digit Indian PIN code.',
            ], 422);
        }

        $info = PincodeZone::checkPincode($pincode);

        return response()->json([
            'success' => true,
            'data'    => $info,
        ]);
    }

    /**
     * Submit a product review (POST /products/{product}/review).
     */
    public function submitReview(Request $request, Product $product): JsonResponse
    {
        $validated = $request->validate([
            'rating'        => 'required|integer|between:1,5',
            'customer_name' => 'required|string|max:100',
            'city'          => 'nullable|string|max:100',
            'title'         => 'nullable|string|max:150',
            'comment'       => 'required|string|max:1000',
        ]);

        $review = ProductReview::create([
            'product_id'    => $product->id,
            'user_id'       => Auth::id(),
            'customer_name' => $validated['customer_name'],
            'city'          => $validated['city'] ?? null,
            'rating'        => $validated['rating'],
            'title'         => $validated['title'] ?? null,
            'comment'       => $validated['comment'],
            'is_approved'   => false, // Pending admin moderation
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Thank you for your review! It will be published upon moderation.',
            'review'  => $review,
        ]);
    }

    /**
     * Subscribe to newsletter (POST /newsletter/subscribe).
     */
    public function subscribeNewsletter(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255',
        ]);

        Subscriber::firstOrCreate(
            ['email' => strtolower(trim($validated['email']))],
            ['is_active' => true]
        );

        return response()->json([
            'success' => true,
            'message' => 'Thank you for subscribing to our private newsletter!',
        ]);
    }

    /**
     * Customer cancel or return order request (POST /orders/{order}/request).
     */
    public function submitOrderRequest(Request $request, Order $order): JsonResponse
    {
        $validated = $request->validate([
            'type'     => 'required|string|in:cancel,return',
            'reason'   => 'required|string|max:255',
            'comments' => 'nullable|string|max:1000',
        ]);

        // Broken Object Level Authorization (BOLA) Check:
        $isOwner = Auth::check() && $order->user_id && $order->user_id === Auth::id();
        $isAdmin = Auth::check() && Auth::user()->isAdmin();
        $isSessionGuest = session('recent_order_id') == $order->id || session('recent_order_ref') === $order->reference;

        if (! $isOwner && ! $isAdmin && ! $isSessionGuest) {
            return response()->json(['success' => false, 'message' => 'Unauthorized. You do not have permission to modify this order.'], 403);
        }

        if ($validated['type'] === 'cancel' && ! $order->isCancellable()) {
            return response()->json(['success' => false, 'message' => 'This order can no longer be cancelled as it is already being processed or dispatched.'], 422);
        }

        $existing = OrderRequest::where('order_id', $order->id)
            ->where('type', $validated['type'])
            ->where('status', OrderRequest::STATUS_PENDING)
            ->first();

        if ($existing) {
            return response()->json(['success' => false, 'message' => 'You already have an active request pending for this order.'], 422);
        }

        $req = OrderRequest::create([
            'order_id' => $order->id,
            'user_id'  => Auth::id() ?? $order->user_id,
            'type'     => $validated['type'],
            'reason'   => $validated['reason'],
            'comments' => $validated['comments'] ?? null,
            'status'   => OrderRequest::STATUS_PENDING,
        ]);

        // Auto-cancel if pending
        if ($validated['type'] === 'cancel' && $order->status === Order::STATUS_PENDING) {
            $order->update(['status' => Order::STATUS_CANCELLED]);
            $req->update(['status' => OrderRequest::STATUS_APPROVED, 'admin_notes' => 'Auto-approved on request.']);
            // Restock items
            foreach ($order->items as $item) {
                if ($item->product_variant_id) {
                    $variant = \App\Models\ProductVariant::find($item->product_variant_id);
                    if ($variant) {
                        $variant->adjustStock($item->qty, 'order_cancellation_restock', "Order #{$order->reference} cancelled");
                    }
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => $validated['type'] === 'cancel'
                ? 'Your cancellation request has been submitted.'
                : 'Your return request has been submitted. Our team will contact you shortly.',
        ]);
    }

    /**
     * Address Book Management: Store / Update address.
     */
    public function saveAddress(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['success' => false, 'message' => 'Login required.'], 401);
        }

        $validated = $request->validate([
            'id'         => 'nullable|integer',
            'name'       => 'required|string|max:100',
            'phone'      => 'required|string|max:15',
            'address1'   => 'required|string|max:255',
            'address2'   => 'nullable|string|max:255',
            'city'       => 'required|string|max:100',
            'state'      => 'required|string|max:100',
            'pincode'    => 'required|string|max:10',
            'type'       => 'nullable|string|in:home,work,other',
            'is_default' => 'nullable|boolean',
        ]);

        if (! empty($validated['is_default'])) {
            CustomerAddress::where('user_id', $user->id)->update(['is_default' => false]);
        }

        $address = CustomerAddress::updateOrCreate(
            ['id' => $validated['id'] ?? null, 'user_id' => $user->id],
            [
                'name'       => $validated['name'],
                'phone'      => $validated['phone'],
                'address1'   => $validated['address1'],
                'address2'   => $validated['address2'] ?? null,
                'city'       => $validated['city'],
                'state'      => $validated['state'],
                'pincode'    => $validated['pincode'],
                'type'       => $validated['type'] ?? 'home',
                'is_default' => $validated['is_default'] ?? false,
            ]
        );

        return response()->json(['success' => true, 'address' => $address, 'message' => 'Address saved successfully.']);
    }

    /**
     * Address Book Management: Delete address.
     */
    public function deleteAddress(CustomerAddress $address): JsonResponse
    {
        if ($address->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $address->delete();

        return response()->json(['success' => true, 'message' => 'Address removed.']);
    }

    public function search(Request $request)
    {
        $theme       = $this->theme();
        $q           = trim((string) $request->get('q', ''));
        $currentSort = $request->get('sort', 'default');
        $sortOptions = [
            'default'    => 'Featured',
            'price_asc'  => 'Price: Low to High',
            'price_desc' => 'Price: High to Low',
            'newest'     => 'Newest Arrivals',
        ];

        $base = fn () => $this->productQuery()->where(function ($w) use ($q) {
            $w->where('name', 'like', "%{$q}%")
              ->orWhere('description', 'like', "%{$q}%")
              ->orWhereHas('category', fn ($c) => $c->where('name', 'like', "%{$q}%"));
        });

        // Live search from the overlay (search-overlay component expects [{name,url}]).
        if ($request->boolean('ajax')) {
            return response()->json(
                $q === '' ? [] : $base()->take(6)->get()->map(fn ($p) => [
                    'name' => $p->name,
                    'url'  => route('product.show', $p->slug),
                ])->values()
            );
        }

        $products   = $q !== '' ? $base()->paginate(16)->withQueryString() : collect();
        $results    = $products;
        $categories = Category::whereNull('parent_id')->with(['children.children'])->orderBy('sort_order')->get();

        return view("storefront.{$theme->key}.search", compact('theme', 'q', 'results', 'products', 'categories', 'currentSort', 'sortOptions'));
    }

    public function compare(Request $request)
    {
        $theme    = $this->theme();
        $ids      = $this->idsFromRequest($request);
        $products = $ids ? $this->productQuery()->whereIn('id', $ids)->get() : collect();

        return view("storefront.{$theme->key}.compare", compact('theme', 'products'));
    }

    /**
     * Wishlist Toggle API (POST /wishlist/toggle).
     */
    public function toggleWishlist(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $productId = (int) $request->input('product_id');
        $user      = Auth::user();

        if (! $user) {
            return response()->json([
                'success'       => true,
                'authenticated' => false,
                'in_wishlist'   => true,
                'message'       => 'Item added to local wishlist. Sign in to sync across devices.',
            ]);
        }

        $existing = Wishlist::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->first();

        if ($existing) {
            $existing->delete();
            $inWishlist = false;
            $message    = 'Item removed from wishlist';
        } else {
            Wishlist::create([
                'user_id'    => $user->id,
                'product_id' => $productId,
            ]);
            $inWishlist = true;
            $message    = 'Item saved to your wishlist';
        }

        $count = Wishlist::where('user_id', $user->id)->count();

        return response()->json([
            'success'        => true,
            'authenticated'  => true,
            'in_wishlist'    => $inWishlist,
            'wishlist_count' => $count,
            'message'        => $message,
        ]);
    }

    /**
     * Dedicated Customer Tax Invoice View.
     */
    public function customerInvoice(Order $order)
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login');
        }

        if ($order->user_id !== $user->id && $user->role !== 'admin') {
            abort(403, 'Unauthorized access to this invoice.');
        }

        $order->load(['items.product', 'items.productColor', 'user']);

        return view('admin.orders.invoice', compact('order'));
    }

    /**
     * Static Policy & Information Pages.
     */
    public function about()
    {
        $theme      = $this->theme();
        $categories = Category::orderBy('sort_order')->get();
        if (view()->exists("storefront.{$theme->key}.about")) {
            return view("storefront.{$theme->key}.about", compact('theme', 'categories'));
        }
        return view('storefront.common.about', compact('theme', 'categories'));
    }

    public function contact()
    {
        $theme      = $this->theme();
        $categories = Category::orderBy('sort_order')->get();
        if (view()->exists("storefront.{$theme->key}.contact")) {
            return view("storefront.{$theme->key}.contact", compact('theme', 'categories'));
        }
        return view('storefront.common.contact', compact('theme', 'categories'));
    }

    public function faq()
    {
        $theme      = $this->theme();
        $categories = Category::orderBy('sort_order')->get();
        $faqs       = \App\Models\Faq::active()->get()->groupBy('category');
        return view('storefront.common.faq', compact('theme', 'categories', 'faqs'));
    }

    public function terms()
    {
        $theme      = $this->theme();
        $categories = Category::orderBy('sort_order')->get();
        return view('storefront.common.terms', compact('theme', 'categories'));
    }

    public function privacy()
    {
        $theme      = $this->theme();
        $categories = Category::orderBy('sort_order')->get();
        return view('storefront.common.privacy', compact('theme', 'categories'));
    }

    public function shippingPolicy()
    {
        $theme      = $this->theme();
        $categories = Category::orderBy('sort_order')->get();
        return view('storefront.common.shipping-policy', compact('theme', 'categories'));
    }

    public function returnPolicy()
    {
        $theme      = $this->theme();
        $categories = Category::orderBy('sort_order')->get();
        return view('storefront.common.return-policy', compact('theme', 'categories'));
    }

    public function sizeGuide()
    {
        $theme      = $this->theme();
        $categories = Category::orderBy('sort_order')->get();
        return view('storefront.common.size-guide', compact('theme', 'categories'));
    }

    /**
     * Switch active application storefront language.
     */
    public function switchLocale(Request $request, string $locale)
    {
        $supported = array_keys(\App\Http\Middleware\SetLocale::SUPPORTED_LOCALES);
        if (!in_array($locale, $supported, true)) {
            $locale = config('app.fallback_locale', 'en');
        }

        session(['locale' => $locale]);
        \Illuminate\Support\Facades\App::setLocale($locale);
        cookie()->queue('locale', $locale, 60 * 24 * 365);

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'locale'  => $locale,
                'meta'    => \App\Http\Middleware\SetLocale::SUPPORTED_LOCALES[$locale] ?? null,
            ]);
        }

        return back();
    }

    /**
     * Render dynamic custom visual block page.
     */
    public function customPage(string $slug)
    {
        $theme = $this->theme();
        $page  = \App\Models\CustomPage::where('slug', $slug)->published()->firstOrFail();

        return view('storefront.common.custom-page', compact('theme', 'page'));
    }

    /**
     * Generate dynamic XML Sitemap compliant with sitemaps.org and Google Image Search.
     *
     * @return \Illuminate\Http\Response
     */
    public function sitemap()
    {
        $products = Product::where('is_active', true)
            ->with(['colors.images'])
            ->select('id', 'name', 'slug', 'updated_at')
            ->get();

        $categories = Category::select('id', 'name', 'slug', 'updated_at')->get();

        $curatedCollections = [
            'featured-products' => 0.8,
            'sale'              => 0.8,
            'new-arrivals'      => 0.8,
            'new-launches'      => 0.8,
        ];

        $staticPages = [
            'about'           => ['changefreq' => 'monthly', 'priority' => 0.7],
            'contact'         => ['changefreq' => 'monthly', 'priority' => 0.7],
            'faq'             => ['changefreq' => 'weekly',  'priority' => 0.6],
            'terms'           => ['changefreq' => 'monthly', 'priority' => 0.5],
            'privacy'         => ['changefreq' => 'monthly', 'priority' => 0.5],
            'shipping-policy' => ['changefreq' => 'monthly', 'priority' => 0.5],
            'return-policy'   => ['changefreq' => 'monthly', 'priority' => 0.5],
            'size-guide'      => ['changefreq' => 'monthly', 'priority' => 0.6],
            'help'            => ['changefreq' => 'weekly',  'priority' => 0.6],
        ];

        $xml = view('storefront.common.sitemap', compact(
            'products',
            'categories',
            'curatedCollections',
            'staticPages'
        ))->render();

        return response($xml, 200)
            ->header('Content-Type', 'application/xml; charset=utf-8')
            ->header('Cache-Control', 'public, max-age=3600');
    }

    /**
     * Generate dynamic robots.txt pointing to the canonical sitemap.
     *
     * @return \Illuminate\Http\Response
     */
    public function robots()
    {
        $sitemapUrl = url('/sitemap.xml');
        $content = "User-agent: *\n"
            . "Allow: /\n"
            . "Disallow: /admin/\n"
            . "Disallow: /admin/*\n"
            . "Disallow: /account/\n"
            . "Disallow: /account/*\n"
            . "Disallow: /checkout\n"
            . "Disallow: /checkout/*\n"
            . "Disallow: /cart\n"
            . "Disallow: /wishlist\n"
            . "Disallow: /compare\n"
            . "Disallow: /search\n\n"
            . "Sitemap: {$sitemapUrl}\n";

        return response($content, 200)
            ->header('Content-Type', 'text/plain; charset=utf-8');
    }
}
