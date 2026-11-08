{{--
    ======================================================================
    ADMIN MODULE: SETTINGS SMS
    ======================================================================
    File: resources/views/admin/settings/sms.blade.php
    Responsibility: Back-office administration for SETTINGS SMS
    Features:
      - Standard administration UI following the Nex-on design guidelines
      - Accessible controls, Tailwind CSS styling, and reactive Alpine.js components
    Security & Access Control:
      - Guarded by Laravel authentication and RBAC permission middleware
    Operations & Maintenance:
      - Integrates with master administration shell (@extends('admin.layout'))
--}}
@extends('admin.layout', [
    'title' => 'SMS Gateways & OTP Center',
    'breadcrumbs' => [
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Settings', 'url' => route('admin.settings.index')],
        ['label' => 'SMS Gateways'],
    ]
])

@section('content')
<div class="max-w-5xl mx-auto space-y-6" x-data="{ activeProvider: '{{ $settings['active_driver'] }}' }">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">SMS Gateways &amp; OTP Configuration</h1>
            <p class="text-xs text-slate-500 mt-1">Configure Twilio, MSG91 &amp; Fast2SMS for order notifications &amp; customer OTP verification</p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('admin.settings.index') }}" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition">
                &larr; Store Settings
            </a>
            <button type="submit" form="smsForm" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-md shadow-indigo-600/20 flex items-center gap-1.5 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>Save SMS Settings</span>
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2 shadow-xs">
            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-center gap-2 shadow-xs">
            <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- 3 Metrics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total SMS Sent</span>
                <div class="text-2xl font-black text-indigo-600 mt-1 font-mono">{{ $stats['total_sent'] }}</div>
                <span class="text-[10px] text-slate-400">Lifetime notifications</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Sent Today</span>
                <div class="text-2xl font-black text-emerald-600 mt-1 font-mono">{{ $stats['today_sent'] }}</div>
                <span class="text-[10px] text-slate-400">Dispatched in last 24h</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                ✓
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Active Gateway</span>
                <div class="text-lg font-black text-slate-900 mt-1 font-mono uppercase">{{ $settings['active_driver'] }}</div>
                <span class="text-[10px] text-slate-400">Live SMS dispatch provider</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
        </div>
    </div>

    <!-- Main Configuration Form -->
    <form id="smsForm" method="POST" action="{{ route('admin.settings.sms.update') }}" class="space-y-6">
        @csrf

        <!-- Provider Selection Box -->
        <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs space-y-4">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">1. Select Default SMS Provider</h2>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                <label class="p-4 rounded-2xl border cursor-pointer flex flex-col justify-between transition"
                       :class="activeProvider === 'twilio' ? 'border-indigo-600 bg-indigo-50/50 shadow-sm' : 'border-slate-200 bg-white hover:bg-slate-50'">
                    <div class="flex items-center justify-between">
                        <span class="font-extrabold text-slate-900">Twilio</span>
                        <input type="radio" name="sms_active_driver" value="twilio" x-model="activeProvider" class="text-indigo-600">
                    </div>
                    <span class="text-[10px] text-slate-400 mt-2">Global SMS &bull; High Reliability</span>
                </label>

                <label class="p-4 rounded-2xl border cursor-pointer flex flex-col justify-between transition"
                       :class="activeProvider === 'msg91' ? 'border-indigo-600 bg-indigo-50/50 shadow-sm' : 'border-slate-200 bg-white hover:bg-slate-50'">
                    <div class="flex items-center justify-between">
                        <span class="font-extrabold text-slate-900">MSG91</span>
                        <input type="radio" name="sms_active_driver" value="msg91" x-model="activeProvider" class="text-indigo-600">
                    </div>
                    <span class="text-[10px] text-slate-400 mt-2">India DLT Flows &bull; Fast Delivery</span>
                </label>

                <label class="p-4 rounded-2xl border cursor-pointer flex flex-col justify-between transition"
                       :class="activeProvider === 'fast2sms' ? 'border-indigo-600 bg-indigo-50/50 shadow-sm' : 'border-slate-200 bg-white hover:bg-slate-50'">
                    <div class="flex items-center justify-between">
                        <span class="font-extrabold text-slate-900">Fast2SMS</span>
                        <input type="radio" name="sms_active_driver" value="fast2sms" x-model="activeProvider" class="text-indigo-600">
                    </div>
                    <span class="text-[10px] text-slate-400 mt-2">Instant Indian Quick SMS Route</span>
                </label>

                <label class="p-4 rounded-2xl border cursor-pointer flex flex-col justify-between transition"
                       :class="activeProvider === 'log' ? 'border-indigo-600 bg-indigo-50/50 shadow-sm' : 'border-slate-200 bg-white hover:bg-slate-50'">
                    <div class="flex items-center justify-between">
                        <span class="font-extrabold text-slate-900">Log Driver</span>
                        <input type="radio" name="sms_active_driver" value="log" x-model="activeProvider" class="text-indigo-600">
                    </div>
                    <span class="text-[10px] text-slate-400 mt-2">Local Simulated Testing</span>
                </label>
            </div>
        </div>

        <!-- Provider Specific Credentials -->
        <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs space-y-5">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">2. Provider API Credentials</h2>

            <!-- Twilio Credentials -->
            <div x-show="activeProvider === 'twilio'" class="space-y-4" x-cloak>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Twilio Account SID *</label>
                        <input type="text" name="sms_twilio_sid" value="{{ $settings['twilio_sid'] }}" placeholder="ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
                               class="w-full px-3 py-2 rounded-xl border border-slate-200 font-mono text-slate-900 focus:outline-none focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Twilio Auth Token *</label>
                        <input type="password" name="sms_twilio_token" value="{{ $settings['twilio_token'] }}" placeholder="••••••••••••••••"
                               class="w-full px-3 py-2 rounded-xl border border-slate-200 font-mono text-slate-900 focus:outline-none focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Twilio Phone Number (From) *</label>
                        <input type="text" name="sms_twilio_from" value="{{ $settings['twilio_from'] }}" placeholder="+1234567890"
                               class="w-full px-3 py-2 rounded-xl border border-slate-200 font-mono text-slate-900 focus:outline-none focus:border-indigo-500">
                    </div>
                </div>
            </div>

            <!-- MSG91 Credentials -->
            <div x-show="activeProvider === 'msg91'" class="space-y-4" x-cloak>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">MSG91 Auth Key *</label>
                        <input type="password" name="sms_msg91_authkey" value="{{ $settings['msg91_authkey'] }}" placeholder="AuthKey..."
                               class="w-full px-3 py-2 rounded-xl border border-slate-200 font-mono text-slate-900 focus:outline-none focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Registered DLT Sender ID *</label>
                        <input type="text" name="sms_msg91_sender_id" value="{{ $settings['msg91_sender_id'] }}" placeholder="NEXON"
                               class="w-full px-3 py-2 rounded-xl border border-slate-200 font-mono text-slate-900 focus:outline-none focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Order Confirmed Template ID (Flow)</label>
                        <input type="text" name="sms_msg91_order_confirmed_template_id" value="{{ $settings['msg91_order_template'] }}" placeholder="61a8...FlowID"
                               class="w-full px-3 py-2 rounded-xl border border-slate-200 font-mono text-slate-900 focus:outline-none focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">OTP Verification Template ID (Flow)</label>
                        <input type="text" name="sms_msg91_otp_login_template_id" value="{{ $settings['msg91_otp_template'] }}" placeholder="61b2...FlowID"
                               class="w-full px-3 py-2 rounded-xl border border-slate-200 font-mono text-slate-900 focus:outline-none focus:border-indigo-500">
                    </div>
                </div>
            </div>

            <!-- Fast2SMS Credentials -->
            <div x-show="activeProvider === 'fast2sms'" class="space-y-4" x-cloak>
                <div class="text-xs">
                    <label class="block font-semibold text-slate-700 mb-1">Fast2SMS Authorization API Key *</label>
                    <input type="password" name="sms_fast2sms_api_key" value="{{ $settings['fast2sms_api_key'] }}" placeholder="Fast2SMS API Key"
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 font-mono text-slate-900 focus:outline-none focus:border-indigo-500">
                </div>
            </div>

            <!-- Log Driver Info -->
            <div x-show="activeProvider === 'log'" class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/60 text-xs text-slate-600" x-cloak>
                <span class="font-bold text-slate-900 block mb-0.5">Development Simulation Mode Active</span>
                <p>All outgoing order SMS &amp; OTP codes will be recorded in the SMS Audit Log below and written to Laravel logs without invoking live telecom networks.</p>
            </div>
        </div>

        <!-- 3. Automated Notification Triggers -->
        <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs space-y-4">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">3. Automated Notification Triggers</h2>
            <div class="space-y-3 text-xs">
                <div class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-50 border border-slate-200/60">
                    <div>
                        <span class="font-bold text-slate-900 block">Order Confirmation SMS</span>
                        <span class="text-slate-500 text-[11px]">Dispatches SMS with order reference and tracking link when order is placed</span>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="enable_order_confirmed_sms" value="1" class="sr-only peer" {{ $settings['enable_order_confirmed_sms'] ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                    </label>
                </div>

                <div class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-50 border border-slate-200/60">
                    <div>
                        <span class="font-bold text-slate-900 block">Order Dispatched SMS</span>
                        <span class="text-slate-500 text-[11px]">Dispatches SMS with courier name and AWB tracking code upon marking order as Shipped</span>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="enable_order_dispatched_sms" value="1" class="sr-only peer" {{ $settings['enable_order_dispatched_sms'] ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                    </label>
                </div>

                <div class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-50 border border-slate-200/60">
                    <div>
                        <span class="font-bold text-slate-900 block">Phone OTP Verification SMS</span>
                        <span class="text-slate-500 text-[11px]">Enables 6-digit one-time password verification for customer mobile logins</span>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="enable_otp_login_sms" value="1" class="sr-only peer" {{ $settings['enable_otp_login_sms'] ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                    </label>
                </div>
            </div>
        </div>
    </form>

    <!-- Test SMS Dispatch Console -->
    <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs space-y-4">
        <h2 class="text-sm font-bold text-slate-900">Live Gateway Test Tool</h2>
        <p class="text-xs text-slate-500">Send an immediate test message using the active provider to confirm telecom connectivity.</p>

        <form method="POST" action="{{ route('admin.settings.sms.test') }}" class="flex flex-col sm:flex-row gap-3 text-xs">
            @csrf
            <input type="text" name="test_phone" required placeholder="Mobile Number (e.g. 9876543210)"
                   class="w-full sm:w-64 px-3.5 py-2 rounded-xl border border-slate-200 font-mono text-slate-900 focus:outline-none focus:border-indigo-500">
            <input type="text" name="test_message" value="Hello! This is a test SMS from Nexon CMS Store via {{ strtoupper($settings['active_driver']) }}."
                   class="flex-1 px-3.5 py-2 rounded-xl border border-slate-200 text-slate-900 focus:outline-none focus:border-indigo-500">
            <button type="submit" class="px-5 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl font-bold transition whitespace-nowrap">
                Dispatch Test SMS
            </button>
        </form>
    </div>

    <!-- SMS Audit Logs Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-900">SMS Outbox &amp; Audit Logs</h2>
            <span class="text-xs text-slate-400 font-mono">{{ $smsLogs->total() }} total dispatches</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-left text-xs">
                <thead class="bg-slate-50/70 text-slate-500 uppercase font-mono text-[10px]">
                    <tr>
                        <th class="px-6 py-3">Timestamp</th>
                        <th class="px-6 py-3">Phone</th>
                        <th class="px-6 py-3">Provider</th>
                        <th class="px-6 py-3">Template</th>
                        <th class="px-6 py-3">Message Snippet</th>
                        <th class="px-6 py-3 text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($smsLogs as $log)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-6 py-3 font-mono text-slate-500">
                                {{ $log->created_at->format('M d, Y · H:i:s') }}
                            </td>
                            <td class="px-6 py-3 font-mono font-bold text-slate-900">
                                {{ $log->phone }}
                            </td>
                            <td class="px-6 py-3 font-mono uppercase text-[11px] text-slate-700">
                                {{ $log->driver }}
                            </td>
                            <td class="px-6 py-3 font-semibold text-slate-800">
                                {{ ucfirst(str_replace('_', ' ', $log->template_type)) }}
                            </td>
                            <td class="px-6 py-3 text-slate-600 truncate max-w-xs" title="{{ $log->message }}">
                                {{ $log->message }}
                            </td>
                            <td class="px-6 py-3 text-right">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $log->status === 'sent' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                    {{ strtoupper($log->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-400">No SMS dispatches logged yet. Use the test sender above.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($smsLogs->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $smsLogs->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
