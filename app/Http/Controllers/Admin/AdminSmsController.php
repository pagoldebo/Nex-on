<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\SmsLog;
use App\Services\SmsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;

class AdminSmsController extends Controller
{
    protected SmsService $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Display SMS Gateway control panel & audit logs.
     */
    public function index(): View
    {
        $settings = [
            'active_driver'              => Setting::get('sms_active_driver', 'log'),
            'enable_order_confirmed_sms' => Setting::get('enable_order_confirmed_sms', '1') === '1',
            'enable_order_dispatched_sms'=> Setting::get('enable_order_dispatched_sms', '1') === '1',
            'enable_otp_login_sms'       => Setting::get('enable_otp_login_sms', '1') === '1',

            // Twilio
            'twilio_sid'                 => Setting::get('sms_twilio_sid', ''),
            'twilio_token'               => Setting::get('sms_twilio_token', ''),
            'twilio_from'                => Setting::get('sms_twilio_from', ''),

            // MSG91
            'msg91_authkey'              => Setting::get('sms_msg91_authkey', ''),
            'msg91_sender_id'            => Setting::get('sms_msg91_sender_id', 'NEXON'),
            'msg91_order_template'       => Setting::get('sms_msg91_order_confirmed_template_id', ''),
            'msg91_otp_template'         => Setting::get('sms_msg91_otp_login_template_id', ''),

            // Fast2SMS
            'fast2sms_api_key'           => Setting::get('sms_fast2sms_api_key', ''),
        ];

        $smsLogs = SmsLog::with('order')->latest()->paginate(20);

        $stats = [
            'total_sent'   => SmsLog::where('status', 'sent')->count(),
            'total_failed' => SmsLog::where('status', 'failed')->count(),
            'today_sent'   => SmsLog::whereDate('created_at', today())->count(),
        ];

        return view('admin.settings.sms', compact('settings', 'smsLogs', 'stats'));
    }

    /**
     * Update SMS settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $fields = [
            'sms_active_driver'                  => $request->input('sms_active_driver', 'log'),
            'enable_order_confirmed_sms'         => $request->has('enable_order_confirmed_sms') ? '1' : '0',
            'enable_order_dispatched_sms'        => $request->has('enable_order_dispatched_sms') ? '1' : '0',
            'enable_otp_login_sms'               => $request->has('enable_otp_login_sms') ? '1' : '0',

            // Twilio
            'sms_twilio_sid'                     => $request->input('sms_twilio_sid', ''),
            'sms_twilio_token'                   => $request->input('sms_twilio_token', ''),
            'sms_twilio_from'                    => $request->input('sms_twilio_from', ''),

            // MSG91
            'sms_msg91_authkey'                  => $request->input('sms_msg91_authkey', ''),
            'sms_msg91_sender_id'                => $request->input('sms_msg91_sender_id', 'NEXON'),
            'sms_msg91_order_confirmed_template_id' => $request->input('sms_msg91_order_confirmed_template_id', ''),
            'sms_msg91_otp_login_template_id'       => $request->input('sms_msg91_otp_login_template_id', ''),

            // Fast2SMS
            'sms_fast2sms_api_key'               => $request->input('sms_fast2sms_api_key', ''),
        ];

        foreach ($fields as $key => $val) {
            Setting::set($key, $val);
        }

        Setting::clearCache();

        return back()->with('success', 'SMS Gateway configuration updated successfully!');
    }

    /**
     * Send test SMS.
     */
    public function sendTest(Request $request): RedirectResponse
    {
        $request->validate([
            'test_phone'   => 'required|string',
            'test_message' => 'required|string|max:160',
        ]);

        $res = $this->smsService->send(
            $request->input('test_phone'),
            $request->input('test_message'),
            'test_verification'
        );

        if ($res['success']) {
            return back()->with('success', "Test SMS successfully dispatched via {$res['response_id']}!");
        }

        return back()->with('error', "SMS Gateway error: " . ($res['error'] ?? 'Check API credentials.'));
    }

    /**
     * API: Send OTP to customer phone.
     */
    public function apiSendOtp(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => 'required|string',
        ]);

        $phone = preg_replace('/[^0-9]/', '', (string) $request->input('phone', ''));
        $throttleKey = 'sms_otp_send:' . ($phone ?: 'anon') . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return response()->json([
                'success' => false,
                'message' => "Too many OTP requests. Please wait {$seconds} seconds before trying again.",
            ], 429);
        }

        RateLimiter::hit($throttleKey, 600);

        $res = $this->smsService->generateAndSendOtp($request->input('phone'));

        return response()->json([
            'success' => true,
            'message' => 'OTP dispatched to registered mobile number.',
            'otp'     => app()->environment('local', 'testing') ? ($res['otp'] ?? null) : null,
        ]);
    }

    /**
     * API: Verify customer phone OTP.
     */
    public function apiVerifyOtp(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => 'required|string',
            'otp'   => 'required|string',
        ]);

        $phone = preg_replace('/[^0-9]/', '', (string) $request->input('phone', ''));
        $verifyThrottleKey = 'sms_otp_verify:' . ($phone ?: 'anon') . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($verifyThrottleKey, 5)) {
            $seconds = RateLimiter::availableIn($verifyThrottleKey);
            return response()->json([
                'success' => false,
                'message' => "Too many verification attempts. Please wait {$seconds} seconds before trying again.",
            ], 429);
        }

        $verified = $this->smsService->verifyOtp(
            $request->input('phone'),
            $request->input('otp')
        );

        if (!$verified) {
            RateLimiter::hit($verifyThrottleKey, 300);
            $remaining = 5 - RateLimiter::attempts($verifyThrottleKey);
            return response()->json([
                'success' => false,
                'message' => $remaining > 0
                    ? "Invalid or expired OTP code. {$remaining} attempts remaining."
                    : 'Too many failed verification attempts. Please request a new OTP.',
            ], 422);
        }

        RateLimiter::clear($verifyThrottleKey);

        return response()->json([
            'success' => true,
            'message' => 'Phone number verified successfully!',
        ]);
    }
}
