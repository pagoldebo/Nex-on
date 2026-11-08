{{--
    ======================================================================
    THEME: VERDIA — Botanical Living & Organic Produce Marketplace
    PAGE: CURATED FEATURED PRODUCTS / SPOTLIGHT EDIT
    ======================================================================
    File: resources/views/storefront/organic/featured-products.blade.php
    Controller: App\Http\Controllers\StorefrontController@featuredProducts
    Route Context: GET /featured-products
    ViewModel Injections:
      - $theme: Active Template model ('organic')
      - $featuredProducts: Collection of spotlight products (is_featured = 1)
    Responsibility: Curated editorial spotlight showcasing hero items
    Features:
      - Editorial layout highlighting flagship apparel / organic harvest items
      - Badges for limited editions, bestsellers, and atelier exclusives
      - Instant quick-view modal triggers and direct Add to Bag action
    Operations & Maintenance:
      - Items managed in Admin Products -> 'Is Featured' toggle
--}}
@include('storefront.organic.category')

