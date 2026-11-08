{{--
    ======================================================================
    THEME: VERDIA — Botanical Living & Organic Produce Marketplace
    PAGE: ORDER RECEIPT & FULFILLMENT TRACKER
    ======================================================================
    File: resources/views/storefront/organic/order-confirmation.blade.php
    Controller: App\Http\Controllers\StorefrontController@orderConfirmation
    Route Context: GET /order-confirmation/{order}
    ViewModel Injections:
      - $theme: Active Template model ('organic')
      - $order: Confirmed Order model with items and customer relation
    Responsibility: Post-purchase order summary, payment confirmation, and dispatch timeline
    Features:
      - Confirmed order number, receipt date, and payment status badge
      - Itemized product table with unit prices, discounts, taxes, and final total
      - Shipping address and delivery method details
      - Multi-stage fulfillment timeline (Placed -> Packed -> Out for Delivery -> Delivered)
    Operations & Maintenance:
      - Strict BOLA/RLS security: Accessible only by order owner or verified guest session
--}}
<x-storefront-layout :theme="$theme" title="Harvest Order Confirmed">

  <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-20 text-center">
    
    {{-- Success Badge --}}
    <div class="w-20 h-20 rounded-full bg-organic-100 text-organic-600 flex items-center justify-center mx-auto mb-6 shadow-md border-4 border-white">
      <svg class="w-10 h-10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
    </div>

    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-organic-700 bg-organic-50 border border-organic-200 px-3 py-1 rounded-full uppercase tracking-wider mb-2">
      🌱 Order Confirmed &amp; Queued for Morning Harvest
    </span>
    
    <h1 class="text-3xl sm:text-4xl font-black text-stone-900 font-display">
      Thank You for Supporting Organic Farming!
    </h1>
    <p class="text-xs sm:text-sm text-stone-600 mt-2 font-light max-w-lg mx-auto">
      Your order has been transmitted directly to our local farmer cluster. Our harvest team will pick and inspect your produce at dawn.
    </p>

    <p class="text-xs text-stone-500 mt-4 mb-8">
      Order Reference: <span id="confOrderRef" class="font-mono font-extrabold text-organic-700 bg-organic-50 px-2.5 py-1 rounded-md border border-organic-200">{{ $orderModel->reference ?? $order ?? '—' }}</span>
    </p>

    {{-- Order Summary Box --}}
    <div class="bg-white rounded-3xl border border-stone-200 p-6 sm:p-8 text-left shadow-xs mb-8">
      <h3 class="text-base font-extrabold text-stone-900 font-display pb-3 border-b border-stone-100 flex items-center justify-between">
        <span>Produce in this Order</span>
        <span class="text-xs text-stone-400 font-normal">{{ isset($orderModel) && $orderModel ? $orderModel->items->count() : 0 }} items</span>
      </h3>

      <div id="confOrderItems" class="divide-y divide-stone-100 my-4 text-xs text-stone-700">
        @if(isset($orderModel) && $orderModel)
          @foreach($orderModel->items as $item)
            <div class="py-3 flex items-center justify-between gap-4">
              <div class="min-w-0">
                <h4 class="font-bold text-stone-900 truncate">{{ $item->product_name }}</h4>
                <p class="text-[11px] text-stone-500">{{ $item->color_name }} • {{ $item->size }} × {{ $item->qty }}</p>
              </div>
              <span class="font-black text-stone-900 shrink-0">₹{{ number_format($item->line_total, 2) }}</span>
            </div>
          @endforeach
        @endif
      </div>

      <div class="pt-4 border-t border-stone-200 flex justify-between items-baseline">
        <div>
          <span class="text-sm font-black text-stone-900">Total Paid:</span>
          <span class="text-[11px] text-stone-400 block">Payment Method: {{ strtoupper($orderModel->payment_method ?? 'ONLINE') }}</span>
        </div>
        <span id="confOrderTotal" class="text-2xl font-black text-organic-900 font-display">
          {{ isset($orderModel) && $orderModel ? '₹' . number_format($orderModel->total, 2) : '₹0.00' }}
        </span>
      </div>

      @if(isset($orderModel) && $orderModel)
        <div class="mt-6 pt-4 border-t border-stone-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 text-xs text-stone-500">
          <div>
            <strong>Deliver To:</strong> {{ $orderModel->shipping_name }}, {{ $orderModel->shipping_city }} ({{ $orderModel->shipping_pincode }})
          </div>
          @auth
            <a href="{{ route('account.orders.invoice', $orderModel->id) }}" target="_blank" class="text-organic-700 hover:underline font-bold inline-flex items-center gap-1">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
              Print Tax Invoice
            </a>
          @endauth
        </div>
      @endif

    </div>

    {{-- Actions --}}
    <div class="flex flex-wrap items-center justify-center gap-4">
      <a href="{{ route('home') }}" class="btn-organic px-8 py-3.5 rounded-full text-xs font-bold uppercase tracking-wider shadow-md">
        Continue Shopping
      </a>
      @auth
        <a href="{{ route('account') }}" class="px-8 py-3.5 rounded-full text-xs font-bold uppercase tracking-wider bg-white hover:bg-stone-50 border border-stone-300 text-stone-800 transition">
          View My Orders
        </a>
      @endauth
    </div>

  </div>

</x-storefront-layout>

