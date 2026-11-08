{{--
    ======================================================================
    THEME: VERDIA — Botanical Living & Organic Produce Marketplace
    PAGE: NEW ARRIVALS & SEASONAL RELEASES
    ======================================================================
    File: resources/views/storefront/organic/new-arrivals.blade.php
    Controller: App\Http\Controllers\StorefrontController@newArrivals
    Route Context: GET /new-arrivals
    ViewModel Injections:
      - $theme: Active Template model ('organic')
      - $newArrivals: Paginated collection of newly added products
    Responsibility: Catalog of the newest seasonal inventory drops and releases
    Features:
      - Reverse-chronological product collection grid
      - 'Just In' and 'New Release' badge overlays
      - Faceted filtering by category, size, price, and color cover
    Operations & Maintenance:
      - Automatically pulls products created within recent release windows
--}}
@include('storefront.organic.category')

