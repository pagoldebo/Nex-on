{{--
    ======================================================================
    ADMIN POS: HIGH-SPEED TERMINAL CHECKOUT
    ======================================================================
    File: resources/views/admin/pos/index.blade.php
    Controller: App\Http\Controllers\Admin\PosController@index
    Route Context: GET /admin/pos
    Responsibility: Point-of-Sale in-store counter checkout screen
    Features:
      - Barcode scanner hardware listener and instant SKU lookups
      - Quick item search, category grid filtering, and variant picker modal
      - Customer selection with loyalty points balance redemption
      - Multi-tender payment: Cash, Card, UPI / QR Code, Split Tender
      - Hold Cart (park transaction) and recall functionality
    Operations: Hardware keyboard shortcuts enabled for high cashier throughput
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-950">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>POS Cashier Terminal — {{ $settings['store_name'] }}</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        [x-cloak] { display: none !important; }
        
        /* Custom scrollbar for POS cart and catalog */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: #0f172a; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #475569; }
    </style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="h-full text-slate-100 antialiased overflow-hidden select-none"
      x-data="posTerminal()"
      x-init="init()"
      @keydown.window="handleGlobalKeydown($event)">

    <!-- Top POS Navigation Bar -->
    <header class="h-14 bg-slate-900 border-b border-slate-800 px-4 flex items-center justify-between shrink-0 z-20">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.dashboard') }}" class="p-2 rounded-lg bg-slate-800 text-slate-300 hover:text-white hover:bg-slate-700 transition" title="Exit to Admin Dashboard">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            
            <div class="flex items-center gap-2.5">
                <span class="w-7 h-7 rounded-lg bg-gradient-to-tr from-rose-500 to-indigo-600 flex items-center justify-center text-xs font-black text-white shadow-md shadow-rose-500/20">POS</span>
                <div>
                    <h1 class="text-sm font-extrabold tracking-wide text-white leading-none">{{ $settings['store_name'] }}</h1>
                    <span class="text-[10px] text-emerald-400 font-mono flex items-center gap-1.5 mt-0.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        TERMINAL ACTIVE &bull; CASHIER: {{ auth()->user()->name ?? 'Staff' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Center Barcode / Search Box -->
        <div class="flex-1 max-w-xl mx-4">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                </div>
                <input type="text"
                       id="barcode-input"
                       x-ref="barcodeInput"
                       x-model="searchQuery"
                       @keydown.enter.prevent="processBarcodeScan()"
                       placeholder="Scan Barcode or Search SKU / Title (Press F2 to focus)..."
                       class="w-full bg-slate-950 border border-slate-700/80 rounded-xl pl-10 pr-24 py-1.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 font-mono shadow-inner">
                <div class="absolute inset-y-0 right-0 pr-2 flex items-center gap-1.5">
                    <button @click="processBarcodeScan()" class="px-2 py-0.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded text-[11px] font-semibold transition">Scan</button>
                    <span class="text-[10px] text-slate-500 font-mono bg-slate-800 px-1.5 py-0.5 rounded border border-slate-700">F2</span>
                </div>
            </div>
        </div>

        <!-- Right Quick Actions -->
        <div class="flex items-center gap-2">
            <!-- Live Clock -->
            <div class="hidden lg:flex flex-col text-right pr-2">
                <span class="text-xs font-mono font-bold text-slate-200" x-text="currentTime"></span>
                <span class="text-[10px] text-slate-400 font-mono" x-text="currentDate"></span>
            </div>

            <!-- Recent Orders Modal Trigger -->
            <button @click="recentOrdersOpen = true" class="px-2.5 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-semibold flex items-center gap-1.5 transition border border-slate-700/60" title="Recent POS Orders">
                <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Orders</span>
            </button>

            <!-- Fullscreen Toggle -->
            <button @click="toggleFullscreen()" class="p-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition border border-slate-700/60" title="Toggle Fullscreen">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
            </button>

            <!-- Keyboard Shortcuts Helper -->
            <button @click="shortcutsModalOpen = true" class="p-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition border border-slate-700/60" title="Hotkeys (F1)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </button>
        </div>
    </header>

    <!-- Main POS Grid (Left: Catalog, Right: Cart & Tender) -->
    <main class="h-[calc(100vh-3.5rem)] flex flex-col md:flex-row overflow-hidden">
        
        <!-- ========================================== -->
        <!-- LEFT PANEL: Product Catalog & Category Tabs -->
        <!-- ========================================== -->
        <section class="flex-1 flex flex-col min-w-0 bg-slate-900/50 border-r border-slate-800">
            
            <!-- Category Tabs Slider -->
            <div class="p-3 bg-slate-900 border-b border-slate-800/80 flex items-center gap-2 overflow-x-auto shrink-0">
                <button @click="activeCategory = null"
                        :class="activeCategory === null ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'bg-slate-800 text-slate-300 hover:bg-slate-700 hover:text-white'"
                        class="px-3.5 py-1.5 rounded-xl text-xs font-semibold whitespace-nowrap transition">
                    All Products
                    <span class="ml-1 text-[10px] opacity-75 font-mono" x-text="catalog.length"></span>
                </button>
                @foreach($categories as $category)
                    <button @click="activeCategory = {{ $category->id }}"
                            :class="activeCategory === {{ $category->id }} ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'bg-slate-800 text-slate-300 hover:bg-slate-700 hover:text-white'"
                            class="px-3 py-1.5 rounded-xl text-xs font-semibold whitespace-nowrap transition">
                        {{ $category->name }}
                    </button>
                @endforeach
            </div>

            <!-- Product Grid -->
            <div class="flex-1 overflow-y-auto p-4">
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3.5">
                    <template x-for="product in filteredCatalog" :key="product.id">
                        <div @click="selectProduct(product)"
                             class="group bg-slate-900 rounded-2xl p-2.5 border border-slate-800 hover:border-indigo-500/60 hover:shadow-lg hover:shadow-indigo-500/10 cursor-pointer transition flex flex-col justify-between relative overflow-hidden">
                            
                            <!-- Product Image & Stock Badge -->
                            <div class="aspect-square w-full rounded-xl bg-slate-950 overflow-hidden relative mb-2 flex items-center justify-center">
                                <img :src="product.cover_image" 
                                     :alt="product.name" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                                     onerror="this.onerror=null; this.src='{{ asset('images/placeholder.jpg') }}'">
                                
                                <span class="absolute top-2 right-2 text-[10px] font-mono px-2 py-0.5 rounded-full font-bold shadow"
                                      :class="totalStock(product) > 5 ? 'bg-slate-900/90 text-emerald-400 border border-emerald-500/30' : (totalStock(product) > 0 ? 'bg-amber-950/90 text-amber-300 border border-amber-500/30' : 'bg-rose-950/90 text-rose-300 border border-rose-500/30')"
                                      x-text="totalStock(product) > 0 ? totalStock(product) + ' in stock' : 'Out of Stock'">
                                </span>
                            </div>

                            <!-- Details -->
                            <div>
                                <span class="text-[10px] font-mono text-slate-400 uppercase tracking-wider" x-text="product.sku || 'SKU'"></span>
                                <h3 class="text-xs font-bold text-white group-hover:text-indigo-400 line-clamp-1 mt-0.5" x-text="product.name"></h3>
                                
                                <div class="flex items-center justify-between mt-2 pt-1 border-t border-slate-800">
                                    <span class="text-xs font-extrabold text-white font-mono" x-text="'₹' + product.base_price.toLocaleString()"></span>
                                    <span class="text-[10px] font-semibold text-indigo-400 bg-indigo-500/10 px-2 py-0.5 rounded-md"
                                          x-text="product.variants.length > 1 ? product.variants.length + ' variants' : 'Quick Add'"></span>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Empty State -->
                <div x-show="filteredCatalog.length === 0" class="h-64 flex flex-col items-center justify-center text-slate-400" x-cloak>
                    <svg class="w-12 h-12 text-slate-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <p class="text-sm font-semibold">No products found matching query.</p>
                    <p class="text-xs text-slate-500 mt-1">Try another keyword or scan SKU barcode.</p>
                </div>
            </div>
        </section>

        <!-- ========================================== -->
        <!-- RIGHT PANEL: Cashier Cart & Tender Checkout -->
        <!-- ========================================== -->
        <section class="w-full md:w-[420px] lg:w-[460px] bg-slate-900 flex flex-col shrink-0 border-l border-slate-800 h-full">
            
            <!-- Customer Selector Bar -->
            <div class="p-3 bg-slate-950/80 border-b border-slate-800 flex items-center justify-between">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-8 h-8 rounded-full bg-slate-800 text-indigo-400 flex items-center justify-center text-xs font-bold shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <div class="truncate">
                        <div class="text-xs font-bold text-white truncate" x-text="customer.name"></div>
                        <div class="text-[10px] text-slate-400 font-mono truncate" x-text="customer.phone || 'Walk-in Guest'"></div>
                    </div>
                </div>

                <div class="flex items-center gap-1.5 shrink-0">
                    <button @click="customerModalOpen = true" class="px-2.5 py-1 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white rounded-lg text-[11px] font-semibold transition border border-slate-700">
                        Change / + Add
                    </button>
                    <button @click="resetCustomer()" class="p-1 text-slate-500 hover:text-rose-400" title="Reset to Walk-in Guest">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            <!-- Cart Items List Header -->
            <div class="px-4 py-2 bg-slate-900 border-b border-slate-800 flex items-center justify-between text-[11px] font-bold text-slate-400">
                <span>CART ITEMS (<span x-text="cartItemCount"></span>)</span>
                <button @click="clearCart()" x-show="cart.length > 0" class="text-rose-400 hover:text-rose-300 text-[10px] font-semibold flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Clear Cart (F8)
                </button>
            </div>

            <!-- Cart Scroll Area -->
            <div class="flex-1 overflow-y-auto p-3 space-y-2">
                <template x-for="(item, index) in cart" :key="item.variant_id">
                    <div class="p-2.5 rounded-xl bg-slate-950/70 border border-slate-800 flex items-center gap-2.5 group hover:border-slate-700 transition">
                        <!-- Tiny Thumb -->
                        <img :src="item.image" :alt="item.product_name" class="w-10 h-10 rounded-lg object-cover bg-slate-800 shrink-0" onerror="this.onerror=null; this.src='{{ asset('images/placeholder.jpg') }}'">
                        
                        <!-- Info -->
                        <div class="flex-1 min-w-0">
                            <h4 class="text-xs font-bold text-white truncate" x-text="item.product_name"></h4>
                            <div class="text-[10px] text-slate-400 font-mono flex items-center gap-1.5 mt-0.5">
                                <span class="bg-slate-800 px-1 py-0.2 rounded" x-text="item.sku"></span>
                                <span x-show="item.size" x-text="'Size: ' + item.size"></span>
                                <span x-show="item.color_name && item.color_name !== 'Standard'" x-text="'&bull; ' + item.color_name"></span>
                            </div>
                            <div class="text-xs font-bold text-slate-200 font-mono mt-1" x-text="'₹' + item.price.toLocaleString()"></div>
                        </div>

                        <!-- Stepper -->
                        <div class="flex items-center gap-1 bg-slate-900 border border-slate-700 rounded-lg p-0.5 shrink-0">
                            <button @click="updateQty(index, -1)" class="w-6 h-6 rounded flex items-center justify-center text-slate-300 hover:bg-slate-800 hover:text-white font-bold text-xs">-</button>
                            <input type="number" 
                                   x-model.number="item.qty" 
                                   @change="validateQty(index)" 
                                   class="w-8 text-center bg-transparent text-xs font-mono font-bold text-white focus:outline-none">
                            <button @click="updateQty(index, 1)" class="w-6 h-6 rounded flex items-center justify-center text-slate-300 hover:bg-slate-800 hover:text-white font-bold text-xs">+</button>
                        </div>

                        <!-- Line Total & Delete -->
                        <div class="text-right shrink-0">
                            <div class="text-xs font-bold font-mono text-emerald-400" x-text="'₹' + (item.price * item.qty).toLocaleString()"></div>
                            <button @click="removeFromCart(index)" class="text-[10px] text-slate-500 hover:text-rose-400 mt-1">Remove</button>
                        </div>
                    </div>
                </template>

                <!-- Empty Cart State -->
                <div x-show="cart.length === 0" class="h-44 flex flex-col items-center justify-center text-slate-500" x-cloak>
                    <svg class="w-10 h-10 text-slate-700 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    <p class="text-xs font-bold text-slate-400">Cart is empty</p>
                    <p class="text-[11px] text-slate-500 mt-0.5">Click product or scan barcode to add</p>
                </div>
            </div>

            <!-- Summary & Tender Checkout Footer -->
            <div class="p-4 bg-slate-950 border-t border-slate-800 shrink-0 space-y-3">
                
                <!-- Subtotal, Discount -->
                <div class="space-y-1.5 text-xs text-slate-400">
                    <div class="flex items-center justify-between">
                        <span>Subtotal:</span>
                        <span class="font-mono text-white font-bold" x-text="'₹' + subtotal.toLocaleString()"></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Discount (₹):</span>
                        <div class="flex items-center gap-1.5">
                            <input type="number" 
                                   x-model.number="discount" 
                                   min="0"
                                   placeholder="0"
                                   class="w-20 bg-slate-900 border border-slate-700 rounded px-2 py-0.5 text-right font-mono text-xs text-white focus:outline-none focus:border-indigo-500">
                        </div>
                    </div>
                    <div class="flex items-center justify-between pt-1 border-t border-slate-800 text-sm font-bold text-white">
                        <span>PAYABLE AMOUNT:</span>
                        <span class="text-lg font-black font-mono text-emerald-400" x-text="'₹' + total.toLocaleString()"></span>
                    </div>
                </div>

                <!-- Payment Modes (Cash, UPI, Card) -->
                <div>
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Payment Method</div>
                    <div class="grid grid-cols-3 gap-2">
                        <button type="button"
                                @click="paymentMethod = 'cash'; updateTendered(total)"
                                :class="paymentMethod === 'cash' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30 font-bold border-emerald-500' : 'bg-slate-900 text-slate-300 border-slate-800 hover:bg-slate-800'"
                                class="py-2 px-2 rounded-xl text-xs flex flex-col items-center justify-center gap-1 border transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            <span>Cash</span>
                        </button>

                        <button type="button"
                                @click="paymentMethod = 'upi'"
                                :class="paymentMethod === 'upi' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30 font-bold border-indigo-500' : 'bg-slate-900 text-slate-300 border-slate-800 hover:bg-slate-800'"
                                class="py-2 px-2 rounded-xl text-xs flex flex-col items-center justify-center gap-1 border transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                            <span>UPI QR</span>
                        </button>

                        <button type="button"
                                @click="paymentMethod = 'card'"
                                :class="paymentMethod === 'card' ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30 font-bold border-blue-500' : 'bg-slate-900 text-slate-300 border-slate-800 hover:bg-slate-800'"
                                class="py-2 px-2 rounded-xl text-xs flex flex-col items-center justify-center gap-1 border transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            <span>Card POS</span>
                        </button>
                    </div>
                </div>

                <!-- Cash Tender Inputs & Change Returned -->
                <div x-show="paymentMethod === 'cash'" class="p-2.5 rounded-xl bg-slate-900 border border-slate-800 space-y-2" x-cloak>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-slate-300 font-semibold">Cash Tendered:</span>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-2 flex items-center text-xs text-slate-500 font-mono">₹</span>
                            <input type="number" 
                                   x-model.number="tenderedAmount" 
                                   class="w-28 bg-slate-950 border border-slate-700 rounded-lg pl-6 pr-2 py-1 text-right font-mono text-sm font-bold text-white focus:outline-none focus:ring-1 focus:ring-emerald-500">
                        </div>
                    </div>

                    <!-- Quick Cash Chips -->
                    <div class="flex items-center gap-1.5 overflow-x-auto text-[11px] font-mono">
                        <button type="button" @click="tenderedAmount = total" class="px-2 py-0.5 rounded bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700">Exact</button>
                        <button type="button" @click="tenderedAmount = roundUp(total, 100)" class="px-2 py-0.5 rounded bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700" x-text="'₹' + roundUp(total, 100)"></button>
                        <button type="button" @click="tenderedAmount = roundUp(total, 500)" class="px-2 py-0.5 rounded bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700" x-text="'₹' + roundUp(total, 500)"></button>
                        <button type="button" @click="tenderedAmount = roundUp(total, 2000)" class="px-2 py-0.5 rounded bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700" x-text="'₹' + roundUp(total, 2000)"></button>
                    </div>

                    <!-- Change Calculation Banner -->
                    <div class="flex items-center justify-between pt-1 border-t border-slate-800 text-xs">
                        <span class="text-slate-400 font-semibold">Change to Return:</span>
                        <span class="font-mono text-base font-black" 
                              :class="changeAmount >= 0 ? 'text-emerald-400' : 'text-rose-400'"
                              x-text="'₹' + Math.max(0, changeAmount).toLocaleString()">
                        </span>
                    </div>
                </div>

                <!-- UPI QR Display Note -->
                <div x-show="paymentMethod === 'upi'" class="p-2 rounded-xl bg-indigo-950/40 border border-indigo-900/50 flex items-center gap-2.5 text-xs text-indigo-300" x-cloak>
                    <svg class="w-5 h-5 text-indigo-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Dynamic UPI QR code will popup upon completing bill for customer scan.</span>
                </div>

                <!-- Big Checkout Action Button -->
                <button type="button"
                        @click="executeCheckout()"
                        :disabled="cart.length === 0 || isCheckingOut"
                        class="w-full py-3.5 px-4 rounded-2xl bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-500 hover:from-emerald-500 hover:to-teal-500 text-white font-extrabold text-sm tracking-wide shadow-lg shadow-emerald-600/30 flex items-center justify-center gap-2 disabled:opacity-40 disabled:cursor-not-allowed transition">
                    <template x-if="!isCheckingOut">
                        <span class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            COMPLETE &amp; PRINT RECEIPT (F9)
                        </span>
                    </template>
                    <template x-if="isCheckingOut">
                        <span class="flex items-center gap-2">
                            <svg class="animate-spin w-5 h-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                            PROCESSING ORDER...
                        </span>
                    </template>
                </button>
            </div>
        </section>
    </main>

    <!-- ========================================== -->
    <!-- MODAL 1: Variant Selector Modal -->
    <!-- ========================================== -->
    <div x-show="variantModalOpen" 
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4" 
         x-cloak>
        <div @click.away="variantModalOpen = false" 
             class="bg-slate-900 border border-slate-800 rounded-3xl max-w-lg w-full p-6 shadow-2xl relative">
            
            <div class="flex items-start justify-between pb-4 border-b border-slate-800">
                <div>
                    <h3 class="text-base font-extrabold text-white" x-text="selectedProduct?.name"></h3>
                    <p class="text-xs text-slate-400 mt-0.5">Select color and size variant to add to cart</p>
                </div>
                <button @click="variantModalOpen = false" class="text-slate-400 hover:text-white p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Variant List -->
            <div class="py-4 space-y-2 max-h-80 overflow-y-auto">
                <template x-for="v in selectedProduct?.variants" :key="v.id">
                    <div @click="addVariantToCart(selectedProduct, v)"
                         class="p-3 rounded-2xl bg-slate-950 border border-slate-800/80 hover:border-indigo-500 hover:bg-slate-850 cursor-pointer transition flex items-center justify-between">
                        
                        <div class="flex items-center gap-3">
                            <img :src="v.image || selectedProduct?.cover_image" class="w-10 h-10 rounded-lg object-cover bg-slate-800 shrink-0" onerror="this.onerror=null; this.src='{{ asset('images/placeholder.jpg') }}'">
                            <div>
                                <div class="text-xs font-bold text-white flex items-center gap-1.5">
                                    <span class="w-3 h-3 rounded-full border border-slate-600 shadow inline-block shrink-0" :style="'background-color: ' + (v.color_hex || '#333')"></span>
                                    <span x-text="v.color_name || 'Standard'"></span> &bull; Size: <span class="text-indigo-400" x-text="v.size"></span>
                                </div>
                                <div class="text-[10px] text-slate-400 font-mono" x-text="'SKU: ' + v.sku"></div>
                            </div>
                        </div>

                        <div class="text-right">
                            <div class="text-xs font-bold text-white font-mono" x-text="'₹' + v.price.toLocaleString()"></div>
                            <span class="text-[10px] font-mono font-semibold"
                                  :class="v.stock > 5 ? 'text-emerald-400' : (v.stock > 0 ? 'text-amber-400' : 'text-rose-400')"
                                  x-text="v.stock + ' in stock'">
                            </span>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MODAL 2: Quick Customer Registration Modal -->
    <!-- ========================================== -->
    <div x-show="customerModalOpen" 
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4" 
         x-cloak>
        <div @click.away="customerModalOpen = false" 
             class="bg-slate-900 border border-slate-800 rounded-3xl max-w-md w-full p-6 shadow-2xl relative">
            
            <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                <h3 class="text-base font-extrabold text-white">Retail Customer</h3>
                <button @click="customerModalOpen = false" class="text-slate-400 hover:text-white p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Fast Customer Form -->
            <form @submit.prevent="saveQuickCustomer()" class="py-4 space-y-3.5 text-xs">
                <div>
                    <label class="block text-slate-300 font-semibold mb-1">Customer Name *</label>
                    <input type="text" x-model="customerForm.name" required placeholder="e.g. Rahul Sharma"
                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-slate-300 font-semibold mb-1">Phone Number *</label>
                    <input type="text" x-model="customerForm.phone" required placeholder="10-digit mobile number"
                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-white font-mono focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-slate-300 font-semibold mb-1">Email Address (Optional)</label>
                    <input type="email" x-model="customerForm.email" placeholder="customer@gmail.com"
                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-slate-300 font-semibold mb-1">City / Locality</label>
                    <input type="text" x-model="customerForm.city" placeholder="Bengaluru"
                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-indigo-500">
                </div>

                <div class="pt-2 flex items-center justify-end gap-2">
                    <button type="button" @click="customerModalOpen = false" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl font-semibold">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl font-bold shadow-md shadow-indigo-600/30">
                        Attach to Bill
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MODAL 3: Order Completed / Receipt Modal -->
    <!-- ========================================== -->
    <div x-show="receiptModalOpen" 
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/85 backdrop-blur-md p-4" 
         x-cloak>
        <div class="bg-slate-900 border border-slate-800 rounded-3xl max-w-lg w-full p-6 shadow-2xl relative text-center">
            
            <div class="w-14 h-14 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 flex items-center justify-center mx-auto mb-3">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>

            <h3 class="text-xl font-black text-white">Bill Completed Successfully!</h3>
            <p class="text-xs text-slate-400 mt-1 font-mono">Order Ref: <span class="text-indigo-400 font-bold" x-text="completedOrder?.reference"></span></p>

            <div class="my-4 p-4 rounded-2xl bg-slate-950 border border-slate-800 grid grid-cols-2 gap-4 text-left">
                <div>
                    <span class="text-[10px] text-slate-400 uppercase font-mono">Total Paid</span>
                    <div class="text-base font-bold text-white font-mono mt-0.5" x-text="'₹' + (completedOrder?.total || 0).toLocaleString()"></div>
                </div>
                <div>
                    <span class="text-[10px] text-slate-400 uppercase font-mono">Change to Return</span>
                    <div class="text-base font-bold text-emerald-400 font-mono mt-0.5" x-text="'₹' + (completedOrder?.change_amount || 0).toLocaleString()"></div>
                </div>
            </div>

            <!-- Receipt Action Buttons -->
            <div class="space-y-2">
                <div class="grid grid-cols-2 gap-2">
                    <a :href="completedOrder?.receipt_url + '?format=80mm&autoprint=1'" 
                       target="_blank"
                       class="py-2.5 px-3 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold flex items-center justify-center gap-1.5 shadow-md">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        Print 80mm Receipt
                    </a>
                    <a :href="completedOrder?.receipt_url + '?format=58mm&autoprint=1'" 
                       target="_blank"
                       class="py-2.5 px-3 bg-slate-800 hover:bg-slate-700 text-slate-200 rounded-xl text-xs font-bold flex items-center justify-center gap-1.5 border border-slate-700">
                        Print 58mm Mini
                    </a>
                </div>

                <button @click="resetForNextCustomer()" 
                        class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-extrabold tracking-wide uppercase transition shadow-md">
                    Start Next Bill (Enter)
                </button>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MODAL 4: Recent POS Orders -->
    <!-- ========================================== -->
    <div x-show="recentOrdersOpen" 
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4" 
         x-cloak>
        <div @click.away="recentOrdersOpen = false" 
             class="bg-slate-900 border border-slate-800 rounded-3xl max-w-2xl w-full p-6 shadow-2xl relative">
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <h3 class="text-base font-extrabold text-white">Recent POS Sales (Today)</h3>
                <button @click="recentOrdersOpen = false" class="text-slate-400 hover:text-white p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="py-3 max-h-96 overflow-y-auto divide-y divide-slate-800 text-xs">
                @forelse($recentOrders as $order)
                    <div class="py-2.5 flex items-center justify-between">
                        <div>
                            <div class="font-bold text-white font-mono">{{ $order->reference }}</div>
                            <div class="text-[11px] text-slate-400">
                                {{ $order->shipping_name }} &bull; {{ $order->created_at->format('H:i A') }} &bull; 
                                <span class="uppercase text-indigo-400">{{ $order->payment_method }}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="font-mono font-bold text-emerald-400">{{ $order->formatted_total }}</span>
                            <a href="{{ route('admin.pos.receipt', $order->id) }}?autoprint=1" target="_blank"
                               class="px-2.5 py-1 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white rounded-lg text-[11px] font-semibold transition border border-slate-700">
                                Re-Print
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-slate-500">No POS orders recorded yet today.</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MODAL 5: Hotkeys / Shortcuts Help -->
    <!-- ========================================== -->
    <div x-show="shortcutsModalOpen" 
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4" 
         x-cloak>
        <div @click.away="shortcutsModalOpen = false" 
             class="bg-slate-900 border border-slate-800 rounded-3xl max-w-md w-full p-6 shadow-2xl relative">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <h3 class="text-sm font-extrabold text-white">POS Keyboard Shortcuts</h3>
                <button @click="shortcutsModalOpen = false" class="text-slate-400 hover:text-white p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="py-4 space-y-2.5 text-xs text-slate-300">
                <div class="flex items-center justify-between"><span class="font-mono bg-slate-800 px-2 py-0.5 rounded text-indigo-300">F2</span> <span>Focus Barcode Scanner / SKU Search</span></div>
                <div class="flex items-center justify-between"><span class="font-mono bg-slate-800 px-2 py-0.5 rounded text-indigo-300">F4</span> <span>Open Quick Customer Registration</span></div>
                <div class="flex items-center justify-between"><span class="font-mono bg-slate-800 px-2 py-0.5 rounded text-indigo-300">F8</span> <span>Clear Active Cart</span></div>
                <div class="flex items-center justify-between"><span class="font-mono bg-slate-800 px-2 py-0.5 rounded text-indigo-300">F9 / Ctrl+Enter</span> <span>Complete Checkout &amp; Bill</span></div>
                <div class="flex items-center justify-between"><span class="font-mono bg-slate-800 px-2 py-0.5 rounded text-indigo-300">Esc</span> <span>Close any open popup modal</span></div>
            </div>
        </div>
    </div>

    <!-- Alpine.js POS State & Logic -->
    <script>
        function posTerminal() {
            return {
                catalog: @json($catalog),
                activeCategory: null,
                searchQuery: '',
                cart: [],
                discount: 0,
                paymentMethod: 'cash',
                tenderedAmount: 0,
                isCheckingOut: false,
                currentTime: '',
                currentDate: '',

                // Customer state
                customer: {
                    id: null,
                    name: 'Walk-in Retail Guest',
                    phone: '',
                    email: ''
                },
                customerForm: {
                    name: '',
                    phone: '',
                    email: '',
                    city: ''
                },

                // Modals
                variantModalOpen: false,
                customerModalOpen: false,
                receiptModalOpen: false,
                recentOrdersOpen: false,
                shortcutsModalOpen: false,
                selectedProduct: null,
                completedOrder: null,

                init() {
                    this.updateClock();
                    setInterval(() => this.updateClock(), 1000);
                    this.$nextTick(() => {
                        this.$refs.barcodeInput?.focus();
                    });
                },

                updateClock() {
                    const now = new Date();
                    this.currentTime = now.toLocaleTimeString('en-IN', { hour12: true });
                    this.currentDate = now.toLocaleDateString('en-IN', { day: '2-digit', month: 'short', year: 'numeric' });
                },

                get filteredCatalog() {
                    let list = this.catalog;
                    if (this.activeCategory !== null) {
                        list = list.filter(p => p.category_id === this.activeCategory);
                    }
                    if (this.searchQuery.trim() !== '') {
                        const q = this.searchQuery.toLowerCase().trim();
                        list = list.filter(p => 
                            p.name.toLowerCase().includes(q) ||
                            (p.sku && p.sku.toLowerCase().includes(q)) ||
                            p.variants.some(v => v.sku && v.sku.toLowerCase().includes(q))
                        );
                    }
                    return list;
                },

                totalStock(product) {
                    return product.variants.reduce((acc, v) => acc + (v.stock || 0), 0);
                },

                selectProduct(product) {
                    if (product.variants.length === 0) {
                        alert('No variant inventory found for this product.');
                        return;
                    }
                    if (product.variants.length === 1) {
                        this.addVariantToCart(product, product.variants[0]);
                    } else {
                        this.selectedProduct = product;
                        this.variantModalOpen = true;
                    }
                },

                addVariantToCart(product, variant) {
                    if (variant.stock <= 0) {
                        alert('This variant is out of stock.');
                        return;
                    }

                    const existing = this.cart.find(item => item.variant_id === variant.id);
                    if (existing) {
                        if (existing.qty + 1 > variant.stock) {
                            alert('Cannot exceed available stock of ' + variant.stock + ' units.');
                            return;
                        }
                        existing.qty++;
                    } else {
                        this.cart.push({
                            product_id: product.id,
                            variant_id: variant.id,
                            sku: variant.sku,
                            product_name: product.name,
                            color_name: variant.color_name,
                            size: variant.size,
                            price: variant.price,
                            stock: variant.stock,
                            image: variant.image || product.cover_image,
                            qty: 1
                        });
                    }

                    this.variantModalOpen = false;
                    this.tenderedAmount = this.total;
                    this.$refs.barcodeInput?.focus();
                },

                // Barcode scanner auto add
                processBarcodeScan() {
                    const code = this.searchQuery.trim();
                    if (!code) return;

                    // Scan across all variants in catalog
                    let matchedProduct = null;
                    let matchedVariant = null;

                    for (const prod of this.catalog) {
                        for (const v of prod.variants) {
                            if (v.sku && v.sku.toLowerCase() === code.toLowerCase()) {
                                matchedProduct = prod;
                                matchedVariant = v;
                                break;
                            }
                        }
                        if (matchedVariant) break;
                    }

                    if (matchedProduct && matchedVariant) {
                        this.addVariantToCart(matchedProduct, matchedVariant);
                        this.searchQuery = '';
                    } else {
                        // Keep searchQuery active in filter
                    }
                },

                updateQty(index, delta) {
                    const item = this.cart[index];
                    const next = item.qty + delta;
                    if (next <= 0) {
                        this.removeFromCart(index);
                    } else if (next > item.stock) {
                        alert('Cannot exceed on-hand stock (' + item.stock + ').');
                    } else {
                        item.qty = next;
                        this.tenderedAmount = this.total;
                    }
                },

                validateQty(index) {
                    const item = this.cart[index];
                    if (item.qty <= 0) {
                        this.removeFromCart(index);
                    } else if (item.qty > item.stock) {
                        alert('Stock limit: ' + item.stock);
                        item.qty = item.stock;
                    }
                    this.tenderedAmount = this.total;
                },

                removeFromCart(index) {
                    this.cart.splice(index, 1);
                    this.tenderedAmount = this.total;
                },

                clearCart() {
                    if (confirm('Clear all items from current cart?')) {
                        this.cart = [];
                        this.discount = 0;
                        this.tenderedAmount = 0;
                    }
                },

                get cartItemCount() {
                    return this.cart.reduce((acc, item) => acc + item.qty, 0);
                },

                get subtotal() {
                    return this.cart.reduce((acc, item) => acc + (item.price * item.qty), 0);
                },

                get total() {
                    return Math.max(0, this.subtotal - (this.discount || 0));
                },

                get changeAmount() {
                    return (this.tenderedAmount || 0) - this.total;
                },

                updateTendered(val) {
                    this.tenderedAmount = val;
                },

                roundUp(amount, unit) {
                    if (amount <= 0) return unit;
                    return Math.ceil(amount / unit) * unit;
                },

                saveQuickCustomer() {
                    fetch('{{ route("admin.pos.customers.quickStore") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(this.customerForm)
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            this.customer = data.customer;
                            this.customerModalOpen = false;
                            this.customerForm = { name: '', phone: '', email: '', city: '' };
                        } else {
                            alert(data.message || 'Error creating customer');
                        }
                    })
                    .catch(() => alert('Customer save failed. Check input data.'));
                },

                resetCustomer() {
                    this.customer = {
                        id: null,
                        name: 'Walk-in Retail Guest',
                        phone: '',
                        email: ''
                    };
                },

                executeCheckout() {
                    if (this.cart.length === 0) return;
                    if (this.paymentMethod === 'cash' && this.tenderedAmount < this.total) {
                        alert('Tendered cash is less than payable total.');
                        return;
                    }

                    this.isCheckingOut = true;

                    const payload = {
                        items: this.cart.map(i => ({
                            variant_id: i.variant_id,
                            qty: i.qty,
                            price: i.price
                        })),
                        customer_id: this.customer.id,
                        customer_name: this.customer.name,
                        customer_phone: this.customer.phone || '9999999999',
                        customer_email: this.customer.email,
                        payment_method: this.paymentMethod,
                        tendered_amount: this.paymentMethod === 'cash' ? this.tenderedAmount : this.total,
                        discount: this.discount || 0
                    };

                    fetch('{{ route("admin.pos.checkout") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(payload)
                    })
                    .then(r => r.json())
                    .then(data => {
                        this.isCheckingOut = false;
                        if (data.success) {
                            this.completedOrder = data;
                            this.receiptModalOpen = true;
                            // Update local stock in catalog
                            for (const item of this.cart) {
                                for (const p of this.catalog) {
                                    const v = p.variants.find(vr => vr.id === item.variant_id);
                                    if (v) v.stock = Math.max(0, v.stock - item.qty);
                                }
                            }
                        } else {
                            alert(data.message || 'Checkout failed.');
                        }
                    })
                    .catch(err => {
                        this.isCheckingOut = false;
                        alert('POS order execution error. Please try again.');
                    });
                },

                resetForNextCustomer() {
                    this.receiptModalOpen = false;
                    this.cart = [];
                    this.discount = 0;
                    this.tenderedAmount = 0;
                    this.completedOrder = null;
                    this.resetCustomer();
                    this.$refs.barcodeInput?.focus();
                },

                toggleFullscreen() {
                    if (!document.fullscreenElement) {
                        document.documentElement.requestFullscreen();
                    } else {
                        if (document.exitFullscreen) document.exitFullscreen();
                    }
                },

                handleGlobalKeydown(e) {
                    if (e.key === 'F2') {
                        e.preventDefault();
                        this.$refs.barcodeInput?.focus();
                    } else if (e.key === 'F4') {
                        e.preventDefault();
                        this.customerModalOpen = true;
                    } else if (e.key === 'F8') {
                        e.preventDefault();
                        this.clearCart();
                    } else if (e.key === 'F9' || (e.ctrlKey && e.key === 'Enter')) {
                        e.preventDefault();
                        if (!this.receiptModalOpen && this.cart.length > 0) {
                            this.executeCheckout();
                        }
                    } else if (e.key === 'Escape') {
                        this.variantModalOpen = false;
                        this.customerModalOpen = false;
                        this.recentOrdersOpen = false;
                        this.shortcutsModalOpen = false;
                    } else if (e.key === 'Enter' && this.receiptModalOpen) {
                        e.preventDefault();
                        this.resetForNextCustomer();
                    }
                }
            };
        }
    </script>
</body>
</html>
