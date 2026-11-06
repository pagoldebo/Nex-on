<?php

namespace App\Services;

use App\Models\Order;
use App\Models\PhoneOtp;
use App\Models\Setting;
use App\Models\SmsLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * Send SMS via the active configured driver.
     */
    public function send(string $phone, string $message, string $templateType = 'custom', ?int $orderId = null): array
    {
        $driver = Setting::get('sms_active_driver', 'log'); // twilio, msg91, fast2sms, log
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Normalize 10-digit Indian numbers to E.164 (+91) if needed
        if (strlen($phone) === 10) {
            $formattedPhone = '+91' . $phone;
        } elseif (str_starts_with($phone, '91') && strlen($phone) === 12) {
            $formattedPhone = '+' . $phone;
        } else {
            $formattedPhone = '+' . $phone;
        }

        $result = match ($driver) {
            'twilio'   => $this->sendViaTwilio($formattedPhone, $message),
            'msg91'    => $this->sendViaMsg91($phone, $message, $templateType),
            'fast2sms' => $this->sendViaFast2Sms($phone, $message),
            default    => $this->sendViaLog($formattedPhone, $message),
        };

        // Record in audit log
        $log = SmsLog::create([
            'phone'               => $formattedPhone,
            'driver'              => $driver,
            'template_type'       => $templateType,
            'order_id'            => $orderId,
            'message'             => $message,
            'status'              => $result['success'] ? 'sent' : 'failed',
            'gateway_response_id' => $result['response_id'] ?? null,
            'error_message'       => $result['error'] ?? null,
        ]);

        return array_merge($result, ['log_id' => $log->id]);
    }

    /**
     * Automated Order Confirmation SMS trigger.
     */
    public function sendOrderConfirmation(Order $order): array
    {
        $storeName = Setting::get('store_name', config('app.name', 'NEXON STORE'));
        $trackUrl = url("/order-confirmation/{$order->reference}");
        
        $message = "Dear {$order->shipping_name}, your order #{$order->reference} for {$order->formatted_total} has been confirmed with {$storeName}! Track your order here: {$trackUrl}";

        return $this->send($order->shipping_phone, $message, 'order_confirmed', $order->id);
    }

    /**
     * Automated Order Dispatched SMS trigger.
     */
    public function sendOrderDispatched(Order $order): array
    {
        $storeName = Setting::get('store_name', config('app.name', 'NEXON STORE'));
        $courier = $order->courier ?: 'Express Courier';
        $awb = $order->tracking_number ?: 'AWB-LIVE';

        $message = "Great news! Your order #{$order->reference} has been dispatched via {$courier} (Tracking: {$awb}). Thank you for shopping with {$storeName}!";

        return $this->send($order->shipping_phone, $message, 'order_dispatched', $order->id);
    }

    /**
     * Generate & send a 6-digit phone OTP.
     */
    public function generateAndSendOtp(string $phone): array
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        $otp = (string) rand(100000, 999999);

        // Store in phone_otps table valid for 10 minutes
        PhoneOtp::create([
            'phone'       => $phone,
            'otp'         => $otp,
            'expires_at'  => now()->addMinutes(10),
            'is_verified' => false,
        ]);

        $storeName = Setting::get('store_name', config('app.name', 'NEXON STORE'));
        $message = "Your {$storeName} verification code is {$otp}. Valid for 10 minutes. Do not share this OTP with anyone.";

        $result = $this->send($phone, $message, 'otp_login');
        return array_merge($result, ['otp' => $otp]); // Return otp for test environments
    }

    /**
     * Verify submitted phone OTP.
     */
    public function verifyOtp(string $phone, string $otp): bool
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        $record = PhoneOtp::where('phone', $phone)
            ->where('otp', trim($otp))
            ->where('is_verified', false)
            ->latest()
            ->first();

        if ($record && !$record->isExpired()) {
            $record->update(['is_verified' => true]);
            return true;
        }

        return false;
    }

    /**
     * Twilio REST API Driver.
     */
    protected function sendViaTwilio(string $formattedPhone, string $message): array
    {
        $sid = Setting::get('sms_twilio_sid', '');
        $token = Setting::get('sms_twilio_token', '');
        $from = Setting::get('sms_twilio_from', '');

        if (empty($sid) || empty($token) || empty($from)) {
            return $this->sendViaLog($formattedPhone, $message, 'Twilio credentials incomplete; fallen back to log.');
        }

        try {
            $response = Http::withBasicAuth($sid, $token)
                ->asForm()
                ->post("https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json", [
                    'To'   => $formattedPhone,
                    'From' => $from,
                    'Body' => $message,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success'     => true,
                    'response_id' => $data['sid'] ?? 'TWILIO-SENT',
                ];
            }

            return [
                'success' => false,
                'error'   => $response->body(),
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'error'   => $e->getMessage(),
            ];
        }
    }

    /**
     * MSG91 Driver.
     */
    protected function sendViaMsg91(string $phone, string $message, string $templateType): array
    {
        $authKey = Setting::get('sms_msg91_authkey', '');
        $senderId = Setting::get('sms_msg91_sender_id', 'NEXON');

        if (empty($authKey)) {
            return $this->sendViaLog($phone, $message, 'MSG91 credentials missing; fallen back to log.');
        }

        try {
            $response = Http::withHeaders([
                'authkey'      => $authKey,
                'content-type' => 'application/json',
            ])->post('https://control.msg91.com/api/v5/flow/', [
                'template_id' => Setting::get("sms_msg91_{$templateType}_template_id", 'default_flow_id'),
                'short_url'   => '0',
                'recipients'  => [
                    [
                        'mobiles' => '91' . substr($phone, -10),
                        'message' => $message,
                    ],
                ],
            ]);

            if ($response->successful()) {
                return [
                    'success'     => true,
                    'response_id' => 'MSG91-' . time(),
                ];
            }

            return ['success' => false, 'error' => $response->body()];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Fast2SMS Driver.
     */
    protected function sendViaFast2Sms(string $phone, string $message): array
    {
        $apiKey = Setting::get('sms_fast2sms_api_key', '');

        if (empty($apiKey)) {
            return $this->sendViaLog($phone, $message, 'Fast2SMS API key missing; fallen back to log.');
        }

        try {
            $response = Http::withHeaders([
                'authorization' => $apiKey,
            ])->asForm()->post('https://www.fast2sms.com/dev/bulkV2', [
                'route'   => 'v3',
                'sender_id' => 'TXTIND',
                'message' => $message,
                'language'=> 'english',
                'numbers' => substr($phone, -10),
            ]);

            if ($response->successful()) {
                return ['success' => true, 'response_id' => 'F2S-' . time()];
            }

            return ['success' => false, 'error' => $response->body()];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Fallback Log Driver for local development & testing.
     */
    protected function sendViaLog(string $phone, string $message, ?string $note = null): array
    {
        Log::info("[SMS SIMULATION] To: {$phone} | Msg: {$message}" . ($note ? " | Note: {$note}" : ''));
        return [
            'success'     => true,
            'response_id' => 'LOG-' . uniqid(),
            'note'        => $note,
        ];
    }
}
