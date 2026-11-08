{{--
    ======================================================================
    ADMIN MODULE: SETTINGS GATEWAYS
    ======================================================================
    File: resources/views/admin/settings/gateways.blade.php
    Responsibility: Back-office administration for SETTINGS GATEWAYS
    Features:
      - Standard administration UI following the Nex-on design guidelines
      - Accessible controls, Tailwind CSS styling, and reactive Alpine.js components
    Security & Access Control:
      - Guarded by Laravel authentication and RBAC permission middleware
    Operations & Maintenance:
      - Integrates with master administration shell (@extends('admin.layout'))
--}}
@extends('admin.layout', [
    'title' => 'Payment Gateways Hub',
    'breadcrumbs' => [
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Settings', 'url' => route('admin.settings.index')],
        ['label' => 'Payment Gateways'],
    ]
])

@section('content')
<div class="max-w-5xl mx-auto space-y-6" x-data="{ activeTab: 'razorpay' }">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Payment Gateways &amp; Merchant Hub</h1>
            <p class="text-xs text-slate-500 mt-1">Configure live &amp; test API credentials, webhooks, and toggle checkout payment options</p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('admin.settings.index') }}" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition">
                &larr; Store Settings
            </a>
            <button type="submit" form="gatewayForm" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-md shadow-indigo-600/20 flex items-center gap-1.5 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>Save All Gateways</span>
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2 shadow-xs">
            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Form -->
    <form id="gatewayForm" method="POST" action="{{ route('admin.settings.gateways.update') }}" class="space-y-6">
        @csrf

        <!-- Gateway Selector Tabs -->
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-2.5">
            <!-- Razorpay Tab -->
            <button type="button" @click="activeTab = 'razorpay'"
                    :class="activeTab === 'razorpay' ? 'border-indigo-600 bg-indigo-50/50 shadow-sm' : 'border-slate-200 bg-white hover:bg-slate-50'"
                    class="p-3.5 rounded-2xl border text-left transition flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="font-black text-xs text-slate-900">Razorpay</span>
                    <span class="w-2 h-2 rounded-full {{ $gateways['razorpay']['enabled'] ? 'bg-emerald-500' : 'bg-slate-300' }}"></span>
                </div>
                <div class="text-[10px] text-slate-400 font-mono mt-2">Cards &bull; UPI &bull; Net</div>
            </button>

            <!-- Stripe Tab -->
            <button type="button" @click="activeTab = 'stripe'"
                    :class="activeTab === 'stripe' ? 'border-indigo-600 bg-indigo-50/50 shadow-sm' : 'border-slate-200 bg-white hover:bg-slate-50'"
                    class="p-3.5 rounded-2xl border text-left transition flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="font-black text-xs text-slate-900">Stripe</span>
                    <span class="w-2 h-2 rounded-full {{ $gateways['stripe']['enabled'] ? 'bg-emerald-500' : 'bg-slate-300' }}"></span>
                </div>
                <div class="text-[10px] text-slate-400 font-mono mt-2">Global Cards &bull; ApplePay</div>
            </button>

            <!-- PhonePe Tab -->
            <button type="button" @click="activeTab = 'phonepe'"
                    :class="activeTab === 'phonepe' ? 'border-indigo-600 bg-indigo-50/50 shadow-sm' : 'border-slate-200 bg-white hover:bg-slate-50'"
                    class="p-3.5 rounded-2xl border text-left transition flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="font-black text-xs text-slate-900">PhonePe PG</span>
                    <span class="w-2 h-2 rounded-full {{ $gateways['phonepe']['enabled'] ? 'bg-emerald-500' : 'bg-slate-300' }}"></span>
                </div>
                <div class="text-[10px] text-slate-400 font-mono mt-2">Instant UPI Intent</div>
            </button>

            <!-- PayPal Tab -->
            <button type="button" @click="activeTab = 'paypal'"
                    :class="activeTab === 'paypal' ? 'border-indigo-600 bg-indigo-50/50 shadow-sm' : 'border-slate-200 bg-white hover:bg-slate-50'"
                    class="p-3.5 rounded-2xl border text-left transition flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="font-black text-xs text-slate-900">PayPal</span>
                    <span class="w-2 h-2 rounded-full {{ $gateways['paypal']['enabled'] ? 'bg-emerald-500' : 'bg-slate-300' }}"></span>
                </div>
                <div class="text-[10px] text-slate-400 font-mono mt-2">International Wallets</div>
            </button>

            <!-- Offline Tab -->
            <button type="button" @click="activeTab = 'offline'"
                    :class="activeTab === 'offline' ? 'border-indigo-600 bg-indigo-50/50 shadow-sm' : 'border-slate-200 bg-white hover:bg-slate-50'"
                    class="p-3.5 rounded-2xl border text-left transition flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="font-black text-xs text-slate-900">COD &amp; UPI QR</span>
                    <span class="w-2 h-2 rounded-full {{ $gateways['offline']['enable_cod'] ? 'bg-emerald-500' : 'bg-slate-300' }}"></span>
                </div>
                <div class="text-[10px] text-slate-400 font-mono mt-2">Cash &bull; Direct QR</div>
            </button>
        </div>

        <!-- 1. RAZORPAY CONFIGURATION PANEL -->
        <div x-show="activeTab === 'razorpay'" class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs space-y-5" x-cloak>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100">
                <div>
                    <h2 class="text-base font-extrabold text-slate-900">Razorpay Payment Gateway</h2>
                    <p class="text-xs text-slate-500 mt-0.5">{{ $gateways['razorpay']['description'] }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <select name="gateway_razorpay_mode" class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-mono font-bold bg-white">
                        <option value="test" {{ $gateways['razorpay']['mode'] === 'test' ? 'selected' : '' }}>Test / Sandbox Mode</option>
                        <option value="live" {{ $gateways['razorpay']['mode'] === 'live' ? 'selected' : '' }}>Live Production</option>
                    </select>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="gateway_razorpay_enabled" value="1" class="sr-only peer" {{ $gateways['razorpay']['enabled'] ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                        <span class="ml-2 text-xs font-bold text-slate-700">Enable</span>
                    </label>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Razorpay Key ID *</label>
                    <input type="text" name="gateway_razorpay_key_id" value="{{ $gateways['razorpay']['key_id'] }}" placeholder="rzp_test_..."
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 font-mono text-slate-900 focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Razorpay Key Secret *</label>
                    <input type="password" name="gateway_razorpay_key_secret" value="{{ $gateways['razorpay']['key_secret'] }}" placeholder="••••••••••••••••"
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 font-mono text-slate-900 focus:outline-none focus:border-indigo-500">
                </div>
                <div class="sm:col-span-2">
                    <label class="block font-semibold text-slate-700 mb-1">Webhook Secret (for signature verification)</label>
                    <input type="password" name="gateway_razorpay_webhook_secret" value="{{ $gateways['razorpay']['webhook_secret'] }}"
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 font-mono text-slate-900 focus:outline-none focus:border-indigo-500">
                </div>
            </div>

            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/60 text-xs">
                <span class="font-bold text-slate-800">Razorpay Webhook Endpoint URL:</span>
                <div class="mt-1 flex items-center justify-between bg-white px-3 py-1.5 rounded-xl border border-slate-200 font-mono text-[11px] text-slate-700">
                    <span>{{ $gateways['razorpay']['webhook_url'] }}</span>
                    <button type="button" @click="navigator.clipboard.writeText('{{ $gateways['razorpay']['webhook_url'] }}'); alert('Copied!')" class="text-indigo-600 font-bold hover:underline">Copy</button>
                </div>
                <p class="text-[10px] text-slate-400 mt-1">Configure this URL in Razorpay Dashboard &rarr; Settings &rarr; Webhooks (Events: <code>payment.captured</code>, <code>order.paid</code>).</p>
            </div>
        </div>

        <!-- 2. STRIPE CONFIGURATION PANEL -->
        <div x-show="activeTab === 'stripe'" class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs space-y-5" x-cloak>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100">
                <div>
                    <h2 class="text-base font-extrabold text-slate-900">Stripe Global Checkout</h2>
                    <p class="text-xs text-slate-500 mt-0.5">{{ $gateways['stripe']['description'] }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <select name="gateway_stripe_mode" class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-mono font-bold bg-white">
                        <option value="test" {{ $gateways['stripe']['mode'] === 'test' ? 'selected' : '' }}>Test Sandbox</option>
                        <option value="live" {{ $gateways['stripe']['mode'] === 'live' ? 'selected' : '' }}>Live Production</option>
                    </select>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="gateway_stripe_enabled" value="1" class="sr-only peer" {{ $gateways['stripe']['enabled'] ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                        <span class="ml-2 text-xs font-bold text-slate-700">Enable</span>
                    </label>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Stripe Publishable Key *</label>
                    <input type="text" name="gateway_stripe_publishable_key" value="{{ $gateways['stripe']['publishable_key'] }}" placeholder="pk_test_..."
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 font-mono text-slate-900 focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Stripe Secret Key *</label>
                    <input type="password" name="gateway_stripe_secret_key" value="{{ $gateways['stripe']['secret_key'] }}" placeholder="sk_test_..."
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 font-mono text-slate-900 focus:outline-none focus:border-indigo-500">
                </div>
                <div class="sm:col-span-2">
                    <label class="block font-semibold text-slate-700 mb-1">Stripe Webhook Signing Secret</label>
                    <input type="password" name="gateway_stripe_webhook_secret" value="{{ $gateways['stripe']['webhook_secret'] }}" placeholder="whsec_..."
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 font-mono text-slate-900 focus:outline-none focus:border-indigo-500">
                </div>
            </div>

            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/60 text-xs">
                <span class="font-bold text-slate-800">Stripe Webhook URL:</span>
                <div class="mt-1 flex items-center justify-between bg-white px-3 py-1.5 rounded-xl border border-slate-200 font-mono text-[11px] text-slate-700">
                    <span>{{ $gateways['stripe']['webhook_url'] }}</span>
                    <button type="button" @click="navigator.clipboard.writeText('{{ $gateways['stripe']['webhook_url'] }}'); alert('Copied!')" class="text-indigo-600 font-bold hover:underline">Copy</button>
                </div>
            </div>
        </div>

        <!-- 3. PHONEPE CONFIGURATION PANEL -->
        <div x-show="activeTab === 'phonepe'" class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs space-y-5" x-cloak>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100">
                <div>
                    <h2 class="text-base font-extrabold text-slate-900">PhonePe Payment Gateway</h2>
                    <p class="text-xs text-slate-500 mt-0.5">{{ $gateways['phonepe']['description'] }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <select name="gateway_phonepe_mode" class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-mono font-bold bg-white">
                        <option value="sandbox" {{ $gateways['phonepe']['mode'] === 'sandbox' ? 'selected' : '' }}>UAT / Sandbox</option>
                        <option value="production" {{ $gateways['phonepe']['mode'] === 'production' ? 'selected' : '' }}>Production Live</option>
                    </select>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="gateway_phonepe_enabled" value="1" class="sr-only peer" {{ $gateways['phonepe']['enabled'] ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                        <span class="ml-2 text-xs font-bold text-slate-700">Enable</span>
                    </label>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Merchant ID (MID) *</label>
                    <input type="text" name="gateway_phonepe_merchant_id" value="{{ $gateways['phonepe']['merchant_id'] }}"
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 font-mono text-slate-900 focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Salt Key *</label>
                    <input type="password" name="gateway_phonepe_salt_key" value="{{ $gateways['phonepe']['salt_key'] }}"
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 font-mono text-slate-900 focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Salt Key Index *</label>
                    <input type="text" name="gateway_phonepe_salt_index" value="{{ $gateways['phonepe']['salt_index'] }}"
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 font-mono text-slate-900 focus:outline-none focus:border-indigo-500">
                </div>
            </div>
        </div>

        <!-- 4. PAYPAL CONFIGURATION PANEL -->
        <div x-show="activeTab === 'paypal'" class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs space-y-5" x-cloak>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100">
                <div>
                    <h2 class="text-base font-extrabold text-slate-900">PayPal International Gateway</h2>
                    <p class="text-xs text-slate-500 mt-0.5">{{ $gateways['paypal']['description'] }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <select name="gateway_paypal_mode" class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-mono font-bold bg-white">
                        <option value="sandbox" {{ $gateways['paypal']['mode'] === 'sandbox' ? 'selected' : '' }}>Sandbox</option>
                        <option value="live" {{ $gateways['paypal']['mode'] === 'live' ? 'selected' : '' }}>Live</option>
                    </select>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="gateway_paypal_enabled" value="1" class="sr-only peer" {{ $gateways['paypal']['enabled'] ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                        <span class="ml-2 text-xs font-bold text-slate-700">Enable</span>
                    </label>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">PayPal Client ID *</label>
                    <input type="text" name="gateway_paypal_client_id" value="{{ $gateways['paypal']['client_id'] }}"
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 font-mono text-slate-900 focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">PayPal Client Secret *</label>
                    <input type="password" name="gateway_paypal_secret" value="{{ $gateways['paypal']['secret'] }}"
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 font-mono text-slate-900 focus:outline-none focus:border-indigo-500">
                </div>
            </div>
        </div>

        <!-- 5. OFFLINE / COD & DIRECT UPI -->
        <div x-show="activeTab === 'offline'" class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs space-y-5" x-cloak>
            <div class="pb-3 border-b border-slate-100">
                <h2 class="text-base font-extrabold text-slate-900">Cash on Delivery &amp; Store UPI QR</h2>
                <p class="text-xs text-slate-500 mt-0.5">Manage zero-fee direct UPI collection and COD handling fees</p>
            </div>

            <div class="space-y-4 text-xs">
                <div class="flex items-center justify-between p-4 rounded-2xl bg-slate-50 border border-slate-200/60">
                    <div>
                        <span class="font-bold text-slate-900 block">Enable Cash on Delivery (COD)</span>
                        <span class="text-slate-500 text-[11px]">Allow buyers to pay cash upon doorstep delivery</span>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="enable_cod" value="1" class="sr-only peer" {{ $gateways['offline']['enable_cod'] ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                    </label>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">COD Handling Fee (₹)</label>
                    <input type="number" name="cod_charge" value="{{ $gateways['offline']['cod_charge'] }}"
                           class="w-40 px-3 py-2 rounded-xl border border-slate-200 font-mono text-slate-900 focus:outline-none focus:border-indigo-500">
                </div>

                <div class="flex items-center justify-between p-4 rounded-2xl bg-slate-50 border border-slate-200/60">
                    <div>
                        <span class="font-bold text-slate-900 block">Enable Direct Storefront UPI QR</span>
                        <span class="text-slate-500 text-[11px]">Displays instant dynamic UPI QR code on checkout screen</span>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="enable_upi_qr" value="1" class="sr-only peer" {{ $gateways['offline']['enable_upi_qr'] ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                    </label>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Store Official UPI ID (VPA) *</label>
                    <input type="text" name="store_upi_vpa" value="{{ $gateways['offline']['upi_vpa'] }}"
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 font-mono text-slate-900 focus:outline-none focus:border-indigo-500">
                </div>
            </div>
        </div>

    </form>
</div>
@endsection
