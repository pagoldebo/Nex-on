<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Driver Delivery Web App — {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-slate-100 text-slate-900 min-h-screen font-sans antialiased pb-16">

    <!-- Top Navigation Bar -->
    <header class="sticky top-0 z-40 bg-slate-900 text-white px-4 py-3.5 shadow-md flex items-center justify-between">
        <div class="flex items-center gap-2">
            <span class="text-xl">🛵</span>
            <div>
                <h1 class="text-sm font-black tracking-wide">Driver Delivery Portal</h1>
                <div class="text-[10px] text-slate-400">Haute Couture Boutique Express</div>
            </div>
        </div>

        @if($driver)
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    {{ $driver->name }}
                </span>
            </div>
        @endif
    </header>

    <!-- Main Container -->
    <main class="max-w-md mx-auto p-4 space-y-4">

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold shadow-xs">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold shadow-xs">
                {{ session('error') }}
            </div>
        @endif

        @if(!$driver)
            <!-- Driver Authentication Form -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200 space-y-4 text-center mt-8">
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center mx-auto text-2xl font-bold">
                    📦
                </div>
                <div>
                    <h2 class="text-base font-black text-slate-900">Rider Login</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Enter your registered mobile phone number to view your assigned drop-offs.</p>
                </div>

                <form method="GET" action="{{ route('driver.portal') }}" class="space-y-3 pt-2 text-xs">
                    <input type="text" name="phone" required placeholder="e.g. +91 98765 00000"
                           class="w-full px-4 py-3 rounded-xl border border-slate-200 text-center font-mono font-bold text-sm focus:outline-none focus:border-indigo-500">
                    <button type="submit" class="w-full py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold transition shadow-md shadow-indigo-600/20">
                        Access My Deliveries
                    </button>
                </form>
            </div>
        @else

            <!-- Active Runs Overview Header -->
            <div class="flex items-center justify-between px-1">
                <h2 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Assigned Deliveries</h2>
                <span class="text-xs font-bold font-mono px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-700">
                    {{ $orders->count() }} Pending
                </span>
            </div>

            <!-- Orders Cards List -->
            @forelse($orders as $order)
                <div x-data="{ showModal: false }" class="bg-white rounded-3xl border border-slate-200 p-5 shadow-xs space-y-3.5">
                    
                    <div class="flex items-center justify-between pb-2.5 border-b border-slate-100">
                        <div>
                            <span class="font-mono font-black text-indigo-600 text-xs">#{{ $order->reference }}</span>
                            <span class="text-[10px] text-slate-400 block">{{ $order->created_at->format('d M, H:i') }}</span>
                        </div>

                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $order->delivery_status === 'out_for_delivery' ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                            {{ str_replace('_', ' ', $order->delivery_status) }}
                        </span>
                    </div>

                    <!-- Customer & Address -->
                    <div class="space-y-1.5 text-xs">
                        <div class="font-bold text-slate-900 text-sm flex items-center justify-between">
                            <span>{{ $order->shipping_name }}</span>
                            <a href="tel:{{ $order->shipping_phone }}" class="p-1.5 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 transition inline-flex items-center gap-1 font-bold text-[11px]">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                <span>Call</span>
                            </a>
                        </div>
                        <p class="text-slate-600 text-[11px] leading-relaxed">
                            {{ $order->shipping_address1 }} {{ $order->shipping_address2 }}, {{ $order->shipping_city }} - {{ $order->shipping_pincode }}
                        </p>
                    </div>

                    <!-- Payment Mode -->
                    <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between text-xs">
                        <div>
                            <span class="text-[10px] uppercase font-bold text-slate-400 block">Payment Mode</span>
                            <span class="font-bold {{ $order->payment_method === 'cod' ? 'text-amber-600' : 'text-emerald-600' }}">
                                {{ $order->payment_method === 'cod' ? 'Cash on Delivery (COD)' : 'Prepaid Online' }}
                            </span>
                        </div>
                        <div class="text-right">
                            <span class="text-[10px] uppercase font-bold text-slate-400 block">Total Due</span>
                            <span class="font-black text-slate-900 text-sm">₹{{ number_format($order->total) }}</span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="pt-1">
                        @if($order->delivery_status === 'assigned')
                            <form method="POST" action="{{ route('driver.order.status', $order) }}">
                                @csrf
                                <input type="hidden" name="action" value="start">
                                <button type="submit" class="w-full py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-600/20 transition flex items-center justify-center gap-1.5">
                                    <span>Out for Delivery</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </button>
                            </form>
                        @elseif($order->delivery_status === 'out_for_delivery')
                            <button @click="showModal = true" type="button"
                                    class="w-full py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-600/20 transition flex items-center justify-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Confirm Delivery (Collect Proof)</span>
                            </button>
                        @endif
                    </div>

                    <!-- Proof of Delivery Modal -->
                    <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs" style="display: none;">
                        <div @click.away="showModal = false" class="bg-white rounded-3xl max-w-sm w-full p-5 text-slate-900 shadow-2xl space-y-4">
                            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                                <h3 class="text-sm font-black">Proof of Delivery</h3>
                                <button @click="showModal = false" type="button" class="text-slate-400 text-lg">&times;</button>
                            </div>

                            <form method="POST" action="{{ route('driver.order.status', $order) }}" class="space-y-3 text-xs">
                                @csrf
                                <input type="hidden" name="action" value="complete">

                                <div>
                                    <label class="block font-bold text-slate-700 mb-1">Customer / Receiver Name *</label>
                                    <input type="text" name="proof_signature" value="{{ $order->shipping_name }}" required
                                           class="w-full px-3 py-2 rounded-xl border border-slate-200 font-bold focus:outline-none focus:border-emerald-500">
                                </div>

                                <div>
                                    <label class="block font-bold text-slate-700 mb-1">Delivery Notes / Handover Details</label>
                                    <textarea name="proof_notes" rows="2" placeholder="e.g. Handed to security guard / customer at doorstep"
                                              class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:border-emerald-500"></textarea>
                                </div>

                                <div class="pt-2 flex items-center justify-end gap-2">
                                    <button @click="showModal = false" type="button" class="px-3.5 py-2 rounded-xl text-slate-600 font-bold">Cancel</button>
                                    <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 text-white font-bold transition shadow-sm">
                                        Mark as Delivered
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            @empty
                <div class="bg-white rounded-3xl border border-slate-200 p-8 text-center text-slate-400 space-y-2">
                    <span class="text-3xl">✨</span>
                    <div class="font-bold text-slate-800 text-sm">All Deliveries Complete</div>
                    <p class="text-xs text-slate-500">You currently have no pending orders assigned to you.</p>
                </div>
            @endforelse

        @endif

    </main>

</body>
</html>
