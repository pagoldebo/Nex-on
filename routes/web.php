<?php

use App\Http\Controllers\Admin\AdminAbandonedCartController;
use App\Http\Controllers\Admin\AdminAddonController;
use App\Http\Controllers\Admin\AdminAnalyticsController;
use App\Http\Controllers\Admin\AdminAttendanceController;
use App\Http\Controllers\Admin\AdminAttributeController;
use App\Http\Controllers\Admin\AdminBarcodeController;
use App\Http\Controllers\Admin\AdminBatchController;
use App\Http\Controllers\Admin\AdminCashFlowController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminCommercialInvoiceController;
use App\Http\Controllers\Admin\AdminCouponController;
use App\Http\Controllers\Admin\AdminCustomerController;
use App\Http\Controllers\Admin\AdminCustomerGroupController;
use App\Http\Controllers\Admin\AdminCustomerSegmentController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminDeliveryPersonnelController;
use App\Http\Controllers\Admin\AdminDepartmentController;
use App\Http\Controllers\Admin\AdminExpenseController;
use App\Http\Controllers\Admin\AdminForecastingController;
use App\Http\Controllers\Admin\AdminInventoryController;
use App\Http\Controllers\Admin\AdminLoyaltyController;
use App\Http\Controllers\Admin\AdminMarketingController;
use App\Http\Controllers\Admin\AdminNewsletterController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminOrderRequestController;
use App\Http\Controllers\Admin\AdminOutletController;
use App\Http\Controllers\Admin\AdminPageBuilderController;
use App\Http\Controllers\Admin\AdminPaymentGatewayController;
use App\Http\Controllers\Admin\AdminPayrollController;
use App\Http\Controllers\Admin\AdminPerformanceController;
use App\Http\Controllers\Admin\AdminPosController;
use App\Http\Controllers\Admin\AdminPosShiftController;
use App\Http\Controllers\Admin\AdminProfitLossController;
use App\Http\Controllers\Admin\AdminPurchaseOrderController;
use App\Http\Controllers\Admin\AdminPurchaseReturnController;
use App\Http\Controllers\Admin\AdminPushNotificationController;
use App\Http\Controllers\Admin\AdminReportController;
use App\Http\Controllers\Admin\AdminReviewController;
use App\Http\Controllers\Admin\AdminRoleController;
use App\Http\Controllers\Admin\AdminStaffController;
use App\Http\Controllers\Admin\AdminSmsController;
use App\Http\Controllers\Admin\AdminSupplierInvoiceController;
use App\Http\Controllers\Admin\AdminSupportTicketController;
use App\Http\Controllers\Admin\AdminKnowledgeBaseController;
use App\Http\Controllers\Admin\AdminFaqController;
use App\Http\Controllers\PaymentWebhookController;
use App\Http\Controllers\Admin\AdminSettingController;
use App\Http\Controllers\Admin\AdminTallyController;
use App\Http\Controllers\Admin\AdminThemeController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\StorefrontController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Storefront Routes
|--------------------------------------------------------------------------
| URLs are theme-agnostic: StorefrontController picks the active theme and
| renders storefront.{theme}.{page}, so switching theme never changes a URL.
| Route names below are referenced by every theme's Blade files.
*/
Route::get('/',                           [StorefrontController::class, 'index'])->name('home');
Route::get('/products/{product:slug}',    [StorefrontController::class, 'product'])->name('product.show');
Route::get('/category/{category:slug}',   [StorefrontController::class, 'category'])->name('category.show');

// Curated Collections & Discovery Links (Shopify patterns)
Route::get('/featured-products',               [StorefrontController::class, 'featuredProducts'])->name('featured.products');
Route::get('/collections/featured-products',   [StorefrontController::class, 'featuredProducts'])->name('collections.featured');
Route::get('/collections/sale',                [StorefrontController::class, 'saleProductsList'])->name('collections.sale');
Route::get('/sale',                            [StorefrontController::class, 'saleProductsList'])->name('sale.products');
Route::get('/collections/new-arrivals',        [StorefrontController::class, 'newArrivalsList'])->name('collections.new');
Route::get('/new-arrivals',                    [StorefrontController::class, 'newArrivalsList'])->name('new.arrivals');
Route::get('/collections/new-launches',        [StorefrontController::class, 'newArrivalsList'])->name('collections.launches');
Route::get('/new-launches',                    [StorefrontController::class, 'newArrivalsList'])->name('new.launches');
Route::get('/collections/{slug}',              [StorefrontController::class, 'collectionBySlug'])->name('collections.show');

// Google SEO Engine & Crawl Discovery
Route::get('/sitemap.xml',                      [StorefrontController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt',                       [StorefrontController::class, 'robots'])->name('robots');

// PWA Mobile App Manifest
Route::get('/manifest.json', function () {
    $storeName = \App\Models\Setting::get('store_name');
    if (empty($storeName) || $storeName === 'Nexon') {
        $storeName = config('app.name', 'Nex-on');
    }
    return response()->json([
        'name' => $storeName . ' – Online Store',
        'short_name' => $storeName,
        'description' => 'Shop apparel, bridal trousseau, and signature edits at ' . $storeName,
        'start_url' => '/',
        'scope' => '/',
        'display' => 'standalone',
        'background_color' => '#fdfbf7',
        'theme_color' => '#7f1d1d',
        'orientation' => 'portrait-primary',
        'icons' => [
            [
                'src' => '/icons/icon-192x192.png',
                'sizes' => '192x192',
                'type' => 'image/png',
                'purpose' => 'any maskable',
            ],
            [
                'src' => '/icons/icon-512x512.png',
                'sizes' => '512x512',
                'type' => 'image/png',
                'purpose' => 'any maskable',
            ],
        ],
        'categories' => ['shopping', 'lifestyle'],
        'prefer_related_applications' => false,
    ], 200, ['Content-Type' => 'application/manifest+json']);
})->name('pwa.manifest');

Route::get('/cart',                       [StorefrontController::class, 'cart'])->name('cart');
Route::get('/account',                    [StorefrontController::class, 'account'])->name('account');
Route::put('/account', function (\Illuminate\Http\Request $request) {
    $user = \Illuminate\Support\Facades\Auth::user();
    if (! $user) return redirect()->route('login');
    $validated = $request->validate([
        'name'             => ['required', 'string', 'max:255'],
        'email'            => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
        'current_password' => ['nullable', 'required_with:password', 'current_password'],
        'password'         => ['nullable', 'string', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
    ]);
    $user->name  = $validated['name'];
    $user->email = $validated['email'];
    if (! empty($validated['password'])) {
        $user->password = \Illuminate\Support\Facades\Hash::make($validated['password']);
    }
    $user->save();
    return back()->with('profile_success', 'Profile updated successfully.');
})->middleware('auth')->name('account.update');

Route::get('/account/orders/{order}/invoice', [StorefrontController::class, 'customerInvoice'])->middleware('auth')->name('account.orders.invoice');
Route::get('/wishlist',                   [StorefrontController::class, 'wishlist'])->name('wishlist');
Route::post('/wishlist/toggle',           [StorefrontController::class, 'toggleWishlist'])->middleware('throttle:60,1')->name('wishlist.toggle');
Route::get('/checkout',                   [StorefrontController::class, 'checkout'])->name('checkout');
Route::post('/checkout/place-order',      [StorefrontController::class, 'placeOrder'])->middleware('throttle:checkout')->name('checkout.placeOrder');
Route::get('/order-confirmation/{order}', [StorefrontController::class, 'orderConfirmation'])->name('order.confirmation');
Route::get('/search',                     [StorefrontController::class, 'search'])->middleware('throttle:search')->name('search');
Route::get('/compare',                    [StorefrontController::class, 'compare'])->name('compare');

// Static Policy & Brand Pages
Route::get('/about',                      [StorefrontController::class, 'about'])->name('about');
Route::get('/contact',                    [StorefrontController::class, 'contact'])->name('contact');
Route::post('/contact',                   [ContactController::class, 'submit'])->middleware('throttle:contact')->name('contact.submit');
Route::get('/faq',                        [StorefrontController::class, 'faq'])->name('faq');
Route::get('/terms',                      [StorefrontController::class, 'terms'])->name('terms');
Route::get('/privacy',                    [StorefrontController::class, 'privacy'])->name('privacy');
Route::get('/shipping-policy',            [StorefrontController::class, 'shippingPolicy'])->name('shippingPolicy');
Route::get('/return-policy',              [StorefrontController::class, 'returnPolicy'])->name('returnPolicy');
Route::get('/size-guide',                 [StorefrontController::class, 'sizeGuide'])->name('sizeGuide');
Route::get('/help',                       [AdminKnowledgeBaseController::class, 'storefrontHelp'])->name('help.index');
Route::get('/help/{slug}',                [AdminKnowledgeBaseController::class, 'storefrontArticle'])->name('help.show');

// Aliases for dot-notation
Route::get('/policy/shipping',            [StorefrontController::class, 'shippingPolicy'])->name('shipping.policy');
Route::get('/policy/return',              [StorefrontController::class, 'returnPolicy'])->name('return.policy');
Route::get('/guide/size',                 [StorefrontController::class, 'sizeGuide'])->name('size.guide');

// Promotional & Tools APIs
Route::post('/coupon/apply',              [StorefrontController::class, 'applyCoupon'])->middleware('throttle:coupon_apply')->name('coupon.apply');
Route::post('/coupon/remove',             [StorefrontController::class, 'removeCoupon'])->name('coupon.remove');
Route::post('/pincode/check',             [StorefrontController::class, 'checkPincode'])->middleware('throttle:pincode_check')->name('pincode.check');
Route::post('/products/{product:slug}/review', [StorefrontController::class, 'submitReview'])->middleware('throttle:reviews')->name('products.review');
Route::post('/orders/{order}/request',    [StorefrontController::class, 'submitOrderRequest'])->middleware('throttle:order_request')->name('orders.request');
Route::post('/account/addresses',         [StorefrontController::class, 'saveAddress'])->name('account.addresses.store');
Route::delete('/account/addresses/{address}', [StorefrontController::class, 'deleteAddress'])->name('account.addresses.destroy');
Route::post('/newsletter/subscribe',      [StorefrontController::class, 'subscribeNewsletter'])->middleware('throttle:newsletter')->name('newsletter.subscribe');
Route::post('/contact/store',                 [ContactController::class, 'submit'])->middleware('throttle:contact')->name('contact.store');
Route::get('/cart/recover/{hash}',             [AdminAbandonedCartController::class, 'restore'])->name('cart.recover');
Route::match(['GET', 'POST'], '/locale/{locale}', [StorefrontController::class, 'switchLocale'])->name('locale.switch');
Route::get('/p/{slug}',                   [StorefrontController::class, 'customPage'])->name('custom-page.show');

// Driver Delivery Web App Portal
Route::get('/driver/portal',              [AdminDeliveryPersonnelController::class, 'driverPortal'])->middleware('throttle:driver_status')->name('driver.portal');
Route::post('/driver/login',               [AdminDeliveryPersonnelController::class, 'driverLogin'])->middleware('throttle:login')->name('driver.login');
Route::post('/driver/orders/{order}/status', [AdminDeliveryPersonnelController::class, 'driverUpdateStatus'])->middleware('throttle:driver_status')->name('driver.order.status');

// Live Payment Gateways Webhooks & Client APIs
Route::post('/webhook/razorpay',          [PaymentWebhookController::class, 'handleRazorpay'])->middleware('throttle:webhooks')->name('webhook.razorpay');
Route::post('/webhook/stripe',            [PaymentWebhookController::class, 'handleStripe'])->middleware('throttle:webhooks')->name('webhook.stripe');
Route::post('/webhook/phonepe',           [PaymentWebhookController::class, 'handlePhonePe'])->middleware('throttle:webhooks')->name('webhook.phonepe');
Route::post('/api/payment/razorpay/verify', [PaymentWebhookController::class, 'verifyRazorpayClientPayment'])->middleware('throttle:payment_verify')->name('api.payment.razorpay.verify');
Route::get('/api/payment/gateways',       [PaymentWebhookController::class, 'getActiveGateways'])->name('api.payment.gateways');

// SMS & Customer Phone OTP Verification APIs
Route::post('/api/sms/send-otp',             [AdminSmsController::class, 'apiSendOtp'])->middleware('throttle:sms_send')->name('api.sms.sendOtp');
Route::post('/api/sms/verify-otp',           [AdminSmsController::class, 'apiVerifyOtp'])->middleware('throttle:sms_verify')->name('api.sms.verifyOtp');

// Web & Mobile Push Notification Subscription APIs (authenticated to prevent anonymous DB growth - L-01)
Route::middleware(['auth', 'throttle:push_subscription'])->group(function () {
    Route::post('/api/push/subscribe',           [AdminPushNotificationController::class, 'apiSubscribe'])->name('api.push.subscribe');
    Route::post('/api/push/unsubscribe',         [AdminPushNotificationController::class, 'apiUnsubscribe'])->name('api.push.unsubscribe');
});

// Customer Wholesale & VIP Group Pricing API
Route::post('/api/customer-group/check',      [StorefrontController::class, 'checkCustomerGroup'])->middleware('throttle:30,1')->name('api.customer-group.check');

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
| Comprehensive administration panel for dashboard, products, categories,
| orders management, invoices, themes, settings, coupons, customers,
| return requests, add-ons, newsletter, and reviews.
*/
Route::prefix('admin')->name('admin.')->middleware(['auth', \App\Http\Middleware\EnsureUserIsAdmin::class])->group(function () {
    // Dashboard & System Status
    Route::get('/', [AdminDashboardController::class, 'index'])
        ->middleware('permission:dashboard.view')
        ->name('dashboard');
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])
        ->middleware('permission:dashboard.view')
        ->name('dashboard.index');

    // Administrative Cache Flush
    Route::post('/optimize/clear', [AdminSettingController::class, 'clearCache'])
        ->middleware('permission:settings.manage')
        ->name('optimize.clear');

    // Advance Point of Sale (POS Terminal & Shift Auditing)
    Route::middleware('permission:pos.access')->group(function () {
        Route::get('/pos', [AdminPosController::class, 'index'])->name('pos.index');
        Route::get('/pos/products/search', [AdminPosController::class, 'searchProducts'])->name('pos.products.search');
        Route::post('/pos/customers/quick-store', [AdminPosController::class, 'quickStoreCustomer'])->name('pos.customers.quickStore');
        Route::post('/pos/checkout', [AdminPosController::class, 'checkout'])->name('pos.checkout');
        Route::get('/pos/orders/{order}/receipt', [AdminPosController::class, 'receipt'])->name('pos.receipt');

        // POS Shift Reconciliation & Tender Analytics
        Route::get('/pos/shifts', [AdminPosShiftController::class, 'index'])->name('pos.shifts.index');
        Route::post('/pos/shifts/open', [AdminPosShiftController::class, 'openShift'])->name('pos.shifts.open');
        Route::get('/pos/shifts/{shift}', [AdminPosShiftController::class, 'show'])->name('pos.shifts.show');
        Route::post('/pos/shifts/{shift}/movement', [AdminPosShiftController::class, 'recordMovement'])->name('pos.shifts.movement');
        Route::post('/pos/shifts/{shift}/close', [AdminPosShiftController::class, 'closeShift'])->name('pos.shifts.close');
        Route::get('/pos/shifts/{shift}/print', [AdminPosShiftController::class, 'printReport'])->name('pos.shifts.print');
    });

    // Products Catalog & Merchandising
    Route::get('/products', [ProductController::class, 'index'])
        ->middleware('permission:products.view|products.manage')
        ->name('products.index');

    Route::middleware('permission:products.manage')->group(function () {
        Route::post('/products/bulk-delete', [ProductController::class, 'bulkDelete'])->name('products.bulkDelete');
        Route::post('/products/{product}/duplicate', [ProductController::class, 'duplicate'])->name('products.duplicate');
        Route::patch('/products/{product}/toggle', [ProductController::class, 'toggleStatus'])->name('products.toggle');
        Route::resource('products', ProductController::class)->except(['index']);

        // Product Attributes & Specifications Engine
        Route::get('/attributes', [AdminAttributeController::class, 'index'])->name('attributes.index');
        Route::post('/attributes', [AdminAttributeController::class, 'store'])->name('attributes.store');
        Route::put('/attributes/{attribute}', [AdminAttributeController::class, 'update'])->name('attributes.update');
        Route::delete('/attributes/{attribute}', [AdminAttributeController::class, 'destroy'])->name('attributes.destroy');
        Route::post('/attributes/groups', [AdminAttributeController::class, 'storeGroup'])->name('attributes.groups.store');
        Route::put('/attributes/groups/{group}', [AdminAttributeController::class, 'updateGroup'])->name('attributes.groups.update');
        Route::delete('/attributes/groups/{group}', [AdminAttributeController::class, 'destroyGroup'])->name('attributes.groups.destroy');
        Route::post('/products/{product}/attributes', [AdminAttributeController::class, 'saveProductAttributes'])->name('products.attributes.save');

        // Categories
        Route::resource('categories', AdminCategoryController::class)->except(['create', 'show', 'edit']);
    });

    // Automated Barcode Label Printing Suite
    Route::middleware('permission:products.view|products.manage|inventory.view')->group(function () {
        Route::get('/barcode', [AdminBarcodeController::class, 'index'])->name('barcode.index');
        Route::post('/barcode/print', [AdminBarcodeController::class, 'print'])->name('barcode.print');
        Route::get('/barcode/preview', [AdminBarcodeController::class, 'preview'])->name('barcode.preview');
    });

    // Inventory & SKU Management (Read)
    Route::middleware('permission:inventory.view|inventory.manage')->group(function () {
        Route::get('/inventory/export',                 [AdminInventoryController::class, 'exportCsv'])->name('inventory.export');
        Route::get('/inventory',                        [AdminInventoryController::class, 'index'])->name('inventory.index');
        Route::get('/inventory/variants/{variant}/logs', [AdminInventoryController::class, 'logs'])->name('inventory.logs');
    });

    // Inventory Management (Write)
    Route::middleware('permission:inventory.manage')->group(function () {
        Route::post('/inventory/quick-store',           [AdminInventoryController::class, 'quickStore'])->name('inventory.quickStore');
        Route::patch('/inventory/variants/{variant}',   [AdminInventoryController::class, 'updateStock'])->name('inventory.updateStock');
    });

    // Inventory Forecasting
    Route::middleware('permission:inventory.forecasting|inventory.manage')->group(function () {
        Route::get('/inventory/forecasting',            [AdminForecastingController::class, 'index'])->name('inventory.forecasting');
        Route::get('/inventory/forecasting/export',     [AdminForecastingController::class, 'exportCsv'])->name('inventory.forecasting.export');
    });

    // Batch Tracking & FIFO Expiry
    Route::middleware('permission:inventory.batches|inventory.manage')->group(function () {
        Route::get('/inventory/batches',                [AdminBatchController::class, 'index'])->name('inventory.batches.index');
        Route::post('/inventory/batches',               [AdminBatchController::class, 'store'])->name('inventory.batches.store');
        Route::get('/inventory/batches/{batch}/trace',  [AdminBatchController::class, 'trace'])->name('inventory.batches.trace');
    });

    // Outlets, Transfers, Purchase Orders & Supplier Relations
    Route::middleware('permission:inventory.manage')->group(function () {
        Route::get('/outlets',                             [AdminOutletController::class, 'index'])->name('outlets.index');
        Route::post('/outlets',                            [AdminOutletController::class, 'store'])->name('outlets.store');
        Route::get('/outlets/{outlet}',                    [AdminOutletController::class, 'show'])->name('outlets.show');
        Route::post('/outlets/{outlet}/stock',             [AdminOutletController::class, 'adjustStock'])->name('outlets.adjustStock');
        Route::get('/transfers',                           [AdminOutletController::class, 'transfersIndex'])->name('transfers.index');
        Route::get('/transfers/create',                    [AdminOutletController::class, 'transfersCreate'])->name('transfers.create');
        Route::post('/transfers',                          [AdminOutletController::class, 'transfersStore'])->name('transfers.store');
        Route::get('/transfers/{transfer}',                [AdminOutletController::class, 'transfersShow'])->name('transfers.show');
        Route::post('/transfers/{transfer}/dispatch',      [AdminOutletController::class, 'transfersDispatch'])->name('transfers.dispatch');
        Route::post('/transfers/{transfer}/receive',       [AdminOutletController::class, 'transfersReceive'])->name('transfers.receive');

        // Supplier Purchase Orders (PO) & Reordering
        Route::get('/purchase-orders',                      [AdminPurchaseOrderController::class, 'index'])->name('purchase-orders.index');
        Route::get('/purchase-orders/create',               [AdminPurchaseOrderController::class, 'create'])->name('purchase-orders.create');
        Route::post('/purchase-orders',                     [AdminPurchaseOrderController::class, 'store'])->name('purchase-orders.store');
        Route::get('/purchase-orders/{purchaseOrder}',       [AdminPurchaseOrderController::class, 'show'])->name('purchase-orders.show');
        Route::post('/purchase-orders/{purchaseOrder}/receive', [AdminPurchaseOrderController::class, 'receiveGoods'])->name('purchase-orders.receiveGoods');
        Route::patch('/purchase-orders/{purchaseOrder}/status',  [AdminPurchaseOrderController::class, 'updateStatus'])->name('purchase-orders.updateStatus');
        Route::post('/purchase-orders/auto-generate',        [AdminPurchaseOrderController::class, 'autoGenerate'])->name('purchase-orders.autoGenerate');
        Route::get('/purchase-orders/{purchaseOrder}/print',  [AdminPurchaseOrderController::class, 'printSlip'])->name('purchase-orders.print');

        // Supplier Invoices & 3-Way Matching
        Route::get('/supplier-invoices', [AdminSupplierInvoiceController::class, 'index'])->name('supplier-invoices.index');
        Route::post('/supplier-invoices', [AdminSupplierInvoiceController::class, 'store'])->name('supplier-invoices.store');
        Route::post('/supplier-invoices/{supplierInvoice}/payment', [AdminSupplierInvoiceController::class, 'recordPayment'])->name('supplier-invoices.payment');
        Route::delete('/supplier-invoices/{supplierInvoice}', [AdminSupplierInvoiceController::class, 'destroy'])->name('supplier-invoices.destroy');

        // Purchase Returns & Debit Notes
        Route::get('/purchase-returns', [AdminPurchaseReturnController::class, 'index'])->name('purchase-returns.index');
        Route::post('/purchase-returns', [AdminPurchaseReturnController::class, 'store'])->name('purchase-returns.store');
        Route::get('/purchase-returns/{purchaseReturn}/debit-note', [AdminPurchaseReturnController::class, 'printDebitNote'])->name('purchase-returns.print');
        Route::patch('/purchase-returns/{purchaseReturn}/status', [AdminPurchaseReturnController::class, 'updateStatus'])->name('purchase-returns.status');

        // Suppliers Directory
        Route::get('/suppliers',                            [AdminPurchaseOrderController::class, 'suppliersIndex'])->name('suppliers.index');
        Route::post('/suppliers',                           [AdminPurchaseOrderController::class, 'storeSupplier'])->name('suppliers.store');
    });

    // Orders Management (View)
    Route::middleware('permission:orders.view|orders.manage')->group(function () {
        Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::get('/orders/{order}/invoice', [AdminOrderController::class, 'invoice'])->name('orders.invoice');
    });

    // Orders Management (Mutations & Fleet)
    Route::middleware('permission:orders.manage')->group(function () {
        Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.updateStatus');
        Route::patch('/orders/{order}/tracking', [AdminOrderController::class, 'updateTracking'])->name('orders.updateTracking');
        Route::post('/orders/{order}/assign-driver', [AdminDeliveryPersonnelController::class, 'assignOrder'])->name('orders.assignDriver');

        // Delivery Personnel Fleet Management
        Route::get('/delivery-personnel',          [AdminDeliveryPersonnelController::class, 'index'])->name('fleet.index');
        Route::post('/delivery-personnel',         [AdminDeliveryPersonnelController::class, 'store'])->name('fleet.store');
        Route::put('/delivery-personnel/{personnel}', [AdminDeliveryPersonnelController::class, 'update'])->name('fleet.update');

        // Order Cancellation & Return Requests
        Route::get('/order-requests', [AdminOrderRequestController::class, 'index'])->name('order-requests.index');
        Route::patch('/order-requests/{orderRequest}', [AdminOrderRequestController::class, 'update'])->name('order-requests.update');
    });

    // Orders Export
    Route::get('/orders/export', [AdminOrderController::class, 'exportCsv'])
        ->middleware('permission:orders.export|orders.manage')
        ->name('orders.export');

    // Financial Statements, Outflows & Commercial Invoices
    Route::middleware('permission:reports.view')->group(function () {
        Route::get('/finance/expenses', [AdminExpenseController::class, 'index'])->name('finance.expenses');
        Route::post('/finance/expenses', [AdminExpenseController::class, 'store'])->name('finance.expenses.store');
        Route::post('/finance/expenses/categories', [AdminExpenseController::class, 'storeCategory'])->name('finance.expenses.categories.store');
        Route::delete('/finance/expenses/{expense}', [AdminExpenseController::class, 'destroy'])->name('finance.expenses.destroy');

        // Finance: Real-Time Cash Flow Statement
        Route::get('/finance/cash-flow', [AdminCashFlowController::class, 'index'])->name('finance.cash-flow');
        Route::get('/finance/cash-flow/export', [AdminCashFlowController::class, 'exportCsv'])->name('finance.cash-flow.export');

        // Finance: Profit & Loss Statement
        Route::get('/finance/profit-loss', [AdminProfitLossController::class, 'index'])->name('finance.pl');
        Route::get('/finance/profit-loss/export', [AdminProfitLossController::class, 'exportCsv'])->name('finance.pl.export');
        Route::get('/finance/profit-loss/print', [AdminProfitLossController::class, 'print'])->name('finance.pl.print');

        // Finance / Invoices: B2B Commercial Invoices & Credit Notes
        Route::get('/invoices', [AdminCommercialInvoiceController::class, 'index'])->name('invoices.index');
        Route::get('/invoices/create', [AdminCommercialInvoiceController::class, 'create'])->name('invoices.create');
        Route::post('/invoices', [AdminCommercialInvoiceController::class, 'store'])->name('invoices.store');
        Route::get('/invoices/{invoice}', [AdminCommercialInvoiceController::class, 'show'])->name('invoices.show');
        Route::patch('/invoices/{invoice}/status', [AdminCommercialInvoiceController::class, 'updateStatus'])->name('invoices.status');
        Route::get('/invoices/{invoice}/print', [AdminCommercialInvoiceController::class, 'print'])->name('invoices.print');

        // Financial & Operational Analytics Reports Suite
        Route::get('/reports',                  [AdminReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export/sales',     [AdminReportController::class, 'exportSalesCsv'])->name('reports.export.sales');
        Route::get('/reports/export/products',  [AdminReportController::class, 'exportProductsCsv'])->name('reports.export.products');
        Route::get('/reports/export/inventory', [AdminReportController::class, 'exportInventoryCsv'])->name('reports.export.inventory');
        Route::get('/reports/export/gst',       [AdminReportController::class, 'exportGstCsv'])->name('reports.export.gst');
    });

    // Store Settings, Themes, ERP Integrations & Marketing
    Route::middleware('permission:settings.manage')->group(function () {
        // Themes
        Route::get('/themes', [AdminThemeController::class, 'index'])->name('themes.index');
        Route::post('/themes/{template}/activate', [AdminThemeController::class, 'activate'])->name('themes.activate');
        Route::post('/themes/admin-toggle', [AdminThemeController::class, 'toggleAdminTheme'])->name('themes.admin-toggle');

        // General Settings
        Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [AdminSettingController::class, 'update'])->name('settings.update');
        Route::post('/settings/adjust-bulk-prices', [AdminSettingController::class, 'adjustBulkPrices'])->name('settings.adjustBulkPrices');
        Route::get('/settings/analytics', [AdminAnalyticsController::class, 'index'])->name('settings.analytics');
        Route::post('/settings/analytics',[AdminAnalyticsController::class, 'update'])->name('settings.analytics.update');

        // ERP & Accounting Integration (Tally Prime)
        Route::get('/integrations/tally',                   [AdminTallyController::class, 'index'])->name('integrations.tally');
        Route::get('/integrations/tally/index',             [AdminTallyController::class, 'index'])->name('integrations.tally.index');
        Route::get('/integrations/tally/export-sales',       [AdminTallyController::class, 'exportSalesXml'])->name('integrations.tally.exportSales');
        Route::get('/integrations/tally/export-receipts',    [AdminTallyController::class, 'exportReceiptsXml'])->name('integrations.tally.exportReceipts');
        Route::get('/integrations/tally/export-stock',       [AdminTallyController::class, 'exportStockXml'])->name('integrations.tally.exportStock');
        Route::post('/integrations/tally/orders/{order}/sync', [AdminTallyController::class, 'syncSingleOrder'])->name('integrations.tally.syncOrder');

        // Add-ons & Brands Management
        Route::patch('/addons/{addon}/toggle', [AdminAddonController::class, 'toggle'])->name('addons.toggle');
        Route::resource('addons', AdminAddonController::class)->except(['create', 'show', 'edit']);
        Route::post('/brands', [AdminAddonController::class, 'storeBrand'])->name('brands.store');
        Route::put('/brands/{brand}', [AdminAddonController::class, 'updateBrand'])->name('brands.update');
        Route::delete('/brands/{brand}', [AdminAddonController::class, 'destroyBrand'])->name('brands.destroy');
        Route::patch('/brands/{brand}/toggle', [AdminAddonController::class, 'toggleBrand'])->name('brands.toggle');

        // Newsletter & Broadcast Campaigns
        Route::get('/newsletter', [AdminNewsletterController::class, 'index'])->name('newsletter.index');
        Route::post('/newsletter/send', [AdminNewsletterController::class, 'send'])->name('newsletter.send');
        Route::post('/newsletter/preview', [AdminNewsletterController::class, 'preview'])->name('newsletter.preview');
        Route::delete('/newsletter/{subscriber}', [AdminNewsletterController::class, 'destroy'])->name('newsletter.destroy');

        // Web & Mobile Push Notification Broadcast Campaigns
        Route::get('/push-notifications',               [AdminPushNotificationController::class, 'index'])->name('push.index');
        Route::post('/push-notifications/send',         [AdminPushNotificationController::class, 'send'])->name('push.send');

        // Coupons
        Route::patch('/coupons/{coupon}/toggle', [AdminCouponController::class, 'toggle'])->name('coupons.toggle');
        Route::resource('coupons', AdminCouponController::class)->except(['create', 'show', 'edit']);

        // Growth / Marketing Suite
        Route::get('/marketing/dashboard', [AdminMarketingController::class, 'dashboard'])->name('marketing.dashboard');
        Route::get('/marketing/abandoned-carts', [AdminAbandonedCartController::class, 'index'])->name('marketing.abandoned-carts');
        Route::post('/marketing/abandoned-carts/{cart}/drip', [AdminAbandonedCartController::class, 'triggerDrip'])->name('marketing.abandoned-carts.drip');
        Route::post('/marketing/abandoned-carts/{cart}/recovered', [AdminAbandonedCartController::class, 'markRecovered'])->name('marketing.abandoned-carts.recovered');
        Route::delete('/marketing/abandoned-carts/{cart}', [AdminAbandonedCartController::class, 'destroy'])->name('marketing.abandoned-carts.destroy');
        Route::post('/marketing/abandoned-carts/simulate', [AdminAbandonedCartController::class, 'simulate'])->name('marketing.abandoned-carts.simulate');

        // VIP Points & Loyalty Program
        Route::get('/marketing/loyalty', [AdminLoyaltyController::class, 'index'])->name('marketing.loyalty');
        Route::put('/marketing/loyalty/tiers/{tier}', [AdminLoyaltyController::class, 'updateTier'])->name('marketing.loyalty.tier.update');
        Route::post('/marketing/loyalty/accounts/{account}/adjust', [AdminLoyaltyController::class, 'adjustPoints'])->name('marketing.loyalty.account.adjust');

        // Customers
        Route::get('/customers', [AdminCustomerController::class, 'index'])->name('customers.index');
        Route::get('/customers/segments', [AdminCustomerSegmentController::class, 'index'])->name('customers.segments');
        Route::get('/customers/segments/export', [AdminCustomerSegmentController::class, 'export'])->name('customers.segments.export');
        Route::get('/customers/{customer}', [AdminCustomerController::class, 'show'])->name('customers.show');
        Route::patch('/customers/{customer}/toggle', [AdminCustomerController::class, 'toggleStatus'])->name('customers.toggle');
        Route::post('/customers/{customer}/password', [AdminCustomerController::class, 'updatePassword'])->name('customers.password');

        // Customer Groups & Wholesale Pricing Tiers
        Route::get('/customer-groups', [AdminCustomerGroupController::class, 'index'])->name('customer-groups.index');
        Route::post('/customer-groups', [AdminCustomerGroupController::class, 'store'])->name('customer-groups.store');
        Route::put('/customer-groups/{customerGroup}', [AdminCustomerGroupController::class, 'update'])->name('customer-groups.update');
        Route::delete('/customer-groups/{customerGroup}', [AdminCustomerGroupController::class, 'destroy'])->name('customer-groups.destroy');
        Route::post('/customer-groups/{customerGroup}/assign', [AdminCustomerGroupController::class, 'assignCustomers'])->name('customer-groups.assign');
        Route::delete('/customer-groups/{customerGroup}/customers/{user}', [AdminCustomerGroupController::class, 'removeCustomer'])->name('customer-groups.remove-customer');

        // Product Reviews Moderation
        Route::get('/reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
        Route::post('/reviews', [AdminReviewController::class, 'store'])->name('reviews.store');
        Route::patch('/reviews/{review}/toggle', [AdminReviewController::class, 'toggle'])->name('reviews.toggle');
        Route::delete('/reviews/{review}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');

        // Visual Block Page Builder
        Route::patch('/pages/{page}/toggle', [AdminPageBuilderController::class, 'togglePublish'])->name('pages.toggle');
        Route::resource('pages', AdminPageBuilderController::class);
    });

    // Payment Gateways
    Route::middleware('permission:settings.gateways|settings.manage')->group(function () {
        Route::get('/settings/gateways', [AdminPaymentGatewayController::class, 'index'])->name('settings.gateways');
        Route::post('/settings/gateways', [AdminPaymentGatewayController::class, 'update'])->name('settings.gateways.update');
    });

    // SMS & OTP Gateways
    Route::middleware('permission:settings.sms|settings.manage')->group(function () {
        Route::get('/settings/sms',      [AdminSmsController::class, 'index'])->name('settings.sms');
        Route::post('/settings/sms',     [AdminSmsController::class, 'update'])->name('settings.sms.update');
        Route::post('/settings/sms/test',[AdminSmsController::class, 'sendTest'])->name('settings.sms.test');
    });

    // Privileged Staff & RBAC Administration
    Route::middleware('permission:roles.manage')->group(function () {
        // Dynamic Role & Permission Management (RBAC)
        Route::get('/roles',                            [AdminRoleController::class, 'index'])->name('roles.index');
        Route::post('/roles',                           [AdminRoleController::class, 'store'])->name('roles.store');
        Route::put('/roles/{role}',                     [AdminRoleController::class, 'update'])->name('roles.update');
        Route::delete('/roles/{role}',                  [AdminRoleController::class, 'destroy'])->name('roles.destroy');
        Route::post('/roles/assign-user',               [AdminRoleController::class, 'assignUser'])->name('roles.assignUser');

        // HRM: Payroll Management
        Route::prefix('hrm/payroll')->as('hrm.payroll.')->group(function () {
            Route::get('/overview', [AdminPayrollController::class, 'overview'])->name('overview');
            Route::get('/run', [AdminPayrollController::class, 'run'])->name('run');
            Route::post('/run', [AdminPayrollController::class, 'processRun'])->name('run.process');
            Route::get('/review', [AdminPayrollController::class, 'reviewLatest'])->name('review.latest');
            Route::get('/payslips', [AdminPayrollController::class, 'payslips'])->name('payslips');
            Route::get('/payslip/{item}/print', [AdminPayrollController::class, 'printPayslip'])->name('payslip.print');
            Route::get('/{payroll}/review', [AdminPayrollController::class, 'review'])->name('review');
            Route::post('/{payroll}/status', [AdminPayrollController::class, 'updateStatus'])->name('status');
            Route::get('/{payroll}/bank-file', [AdminPayrollController::class, 'exportBankFile'])->name('bank-file');
        });

        // HRM: Performance Management & Appraisals
        Route::prefix('hrm/performance')->as('hrm.performance.')->group(function () {
            Route::get('/goals', [AdminPerformanceController::class, 'goals'])->name('goals');
            Route::get('/goals/create', [AdminPerformanceController::class, 'createGoal'])->name('goals.create');
            Route::post('/goals', [AdminPerformanceController::class, 'storeGoal'])->name('goals.store');
            Route::post('/goals/{goal}/progress', [AdminPerformanceController::class, 'updateGoalProgress'])->name('goals.update-progress');
            Route::get('/appraisals', [AdminPerformanceController::class, 'appraisals'])->name('appraisals');
            Route::get('/appraisals/create', [AdminPerformanceController::class, 'createAppraisal'])->name('appraisals.create');
            Route::post('/appraisals', [AdminPerformanceController::class, 'storeAppraisal'])->name('appraisals.store');
            Route::get('/cycles', [AdminPerformanceController::class, 'cycles'])->name('cycles');
            Route::post('/cycles', [AdminPerformanceController::class, 'storeCycle'])->name('cycles.store');
            Route::get('/feedback', [AdminPerformanceController::class, 'feedback'])->name('feedback');
            Route::post('/feedback', [AdminPerformanceController::class, 'storeFeedback'])->name('feedback.store');
        });

        // Organization & Staff Management
        Route::prefix('staff')->as('staff.')->group(function () {
            Route::get('/departments', [AdminDepartmentController::class, 'index'])->name('departments');
            Route::post('/departments', [AdminDepartmentController::class, 'store'])->name('departments.store');
            Route::put('/departments/{department}', [AdminDepartmentController::class, 'update'])->name('departments.update');
            Route::post('/departments/{department}/assign', [AdminDepartmentController::class, 'assignStaff'])->name('departments.assign');
            Route::post('/departments/shifts', [AdminDepartmentController::class, 'scheduleShift'])->name('departments.shifts');
            Route::delete('/departments/shifts/{shift}', [AdminDepartmentController::class, 'deleteShift'])->name('departments.shifts.destroy');

            Route::get('/attendance', [AdminAttendanceController::class, 'index'])->name('attendance');
            Route::post('/attendance/punch', [AdminAttendanceController::class, 'punch'])->name('attendance.punch');
            Route::put('/attendance/{attendance}/adjust', [AdminAttendanceController::class, 'adjust'])->name('attendance.adjust');
            Route::get('/attendance/export', [AdminAttendanceController::class, 'export'])->name('attendance.export');

            Route::get('/', [AdminStaffController::class, 'index'])->name('index');
            Route::post('/', [AdminStaffController::class, 'store'])->name('store');
            Route::post('/bulk', [AdminStaffController::class, 'bulkAction'])->name('bulk');
            Route::get('/{user}', [AdminStaffController::class, 'show'])->name('show')->whereNumber('user');
            Route::put('/{user}', [AdminStaffController::class, 'update'])->name('update')->whereNumber('user');
            Route::delete('/{user}', [AdminStaffController::class, 'destroy'])->name('destroy')->whereNumber('user');
        });
    });

    // Support & Helpdesk
    Route::middleware('permission:orders.view|orders.manage')->group(function () {
        Route::prefix('support')->as('support.')->group(function () {
            // Customer Support Helpdesk & SLA Ticketing Desk
            Route::prefix('tickets')->as('tickets.')->group(function () {
                Route::get('/', [AdminSupportTicketController::class, 'index'])->name('index');
                Route::post('/', [AdminSupportTicketController::class, 'store'])->name('store');
                Route::get('/{ticket}', [AdminSupportTicketController::class, 'show'])->name('show');
                Route::post('/{ticket}/reply', [AdminSupportTicketController::class, 'reply'])->name('reply');
                Route::post('/{ticket}/status', [AdminSupportTicketController::class, 'updateStatus'])->name('update-status');
            });

            // Customer Knowledge Base Article Publisher
            Route::prefix('kb')->as('kb.')->group(function () {
                Route::get('/', [AdminKnowledgeBaseController::class, 'index'])->name('index');
                Route::get('/create', [AdminKnowledgeBaseController::class, 'create'])->name('create');
                Route::post('/', [AdminKnowledgeBaseController::class, 'store'])->name('store');
                Route::get('/{article}/edit', [AdminKnowledgeBaseController::class, 'edit'])->name('edit');
                Route::put('/{article}', [AdminKnowledgeBaseController::class, 'update'])->name('update');
                Route::delete('/{article}', [AdminKnowledgeBaseController::class, 'destroy'])->name('destroy');
                Route::post('/{article}/toggle', [AdminKnowledgeBaseController::class, 'togglePublish'])->name('toggle');

                // Categories
                Route::post('/categories', [AdminKnowledgeBaseController::class, 'storeCategory'])->name('category.store');
                Route::put('/categories/{category}', [AdminKnowledgeBaseController::class, 'updateCategory'])->name('category.update');
                Route::delete('/categories/{category}', [AdminKnowledgeBaseController::class, 'destroyCategory'])->name('category.destroy');
            });

            // Storefront FAQ Builder
            Route::prefix('faq')->as('faq.')->group(function () {
                Route::get('/', [AdminFaqController::class, 'index'])->name('index');
                Route::post('/', [AdminFaqController::class, 'store'])->name('store');
                Route::put('/{faq}', [AdminFaqController::class, 'update'])->name('update');
                Route::delete('/{faq}', [AdminFaqController::class, 'destroy'])->name('destroy');
                Route::post('/{faq}/toggle', [AdminFaqController::class, 'toggle'])->name('toggle');
            });
        });
    });
});

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
| Login / register / logout routes.
*/
require __DIR__.'/auth.php';
