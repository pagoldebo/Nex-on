<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentWebhookController extends Controller
{
    /**
     * Razorpay Webhook Listener (POST /webhook/razorpay).
     */
    public function handleRazorpay(Request $request): JsonResponse
    {
        $signature = $request->header('X-Razorpay-Signature');
        $webhookSecret = Setting::get('gateway_razorpay_webhook_secret', '');
        $payload = $request->getContent();

        // Signature verification if secret configured
        if (!empty($webhookSecret)) {
            if (empty($signature)) {
                Log::warning('Razorpay webhook signature missing.');
                return response()->json(['error' => 'Signature missing'], 400);
            }

            $expectedSignature = hash_hmac('sha256', $payload, $webhookSecret);
            if (!hash_equals($expectedSignature, $signature)) {
                Log::warning('Razorpay webhook signature verification failed.');
                return response()->json(['error' => 'Invalid signature'], 400);
            }
        }

        $event = $request->input('event');
        $payloadData = $request->input('payload.payment.entity', []);

        if (in_array($event, ['payment.captured', 'order.paid'])) {
            $notes = $payloadData['notes'] ?? [];
            $orderRef = $notes['order_reference'] ?? $payloadData['description'] ?? null;
            $paymentId = $payloadData['id'] ?? 'RZP-' . time();

            if ($orderRef) {
                $order = Order::where('reference', $orderRef)->first();
                if ($order && $order->payment_status !== Order::PAYMENT_PAID) {
                    $order->update([
                        'payment_status'    => Order::PAYMENT_PAID,
                        'payment_reference' => $paymentId,
                        'status'            => Order::STATUS_CONFIRMED,
                    ]);
                    Log::info("Order #{$order->reference} marked PAID via Razorpay Webhook [{$paymentId}].");
                }
            }
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Stripe Webhook Listener (POST /webhook/stripe).
     */
    public function handleStripe(Request $request): JsonResponse
    {
        $webhookSecret = Setting::get('gateway_stripe_webhook_secret', '');
        if (!empty($webhookSecret)) {
            $signatureHeader = $request->header('Stripe-Signature');
            if (empty($signatureHeader)) {
                Log::warning('Stripe webhook signature header missing.');
                return response()->json(['error' => 'Signature header missing'], 400);
            }

            $timestamp = null;
            $signature = null;
            foreach (explode(',', $signatureHeader) as $part) {
                $pair = explode('=', trim($part), 2);
                if (count($pair) === 2) {
                    if ($pair[0] === 't') {
                        $timestamp = $pair[1];
                    } elseif ($pair[0] === 'v1') {
                        $signature = $pair[1];
                    }
                }
            }

            if (!$timestamp || !$signature) {
                Log::warning('Stripe webhook signature header format invalid.');
                return response()->json(['error' => 'Invalid signature format'], 400);
            }

            $signedPayload = $timestamp . '.' . $request->getContent();
            $expectedSignature = hash_hmac('sha256', $signedPayload, $webhookSecret);
            if (!hash_equals($expectedSignature, $signature)) {
                Log::warning('Stripe webhook signature verification failed.');
                return response()->json(['error' => 'Invalid signature'], 400);
            }
        }

        $event = $request->input('type');
        $dataObject = $request->input('data.object', []);

        if (in_array($event, ['payment_intent.succeeded', 'checkout.session.completed'])) {
            $orderRef = $dataObject['metadata']['order_reference'] ?? null;
            $paymentId = $dataObject['id'] ?? 'STRIPE-' . time();

            if ($orderRef) {
                $order = Order::where('reference', $orderRef)->first();
                if ($order && $order->payment_status !== Order::PAYMENT_PAID) {
                    $order->update([
                        'payment_status'    => Order::PAYMENT_PAID,
                        'payment_reference' => $paymentId,
                        'status'            => Order::STATUS_CONFIRMED,
                    ]);
                    Log::info("Order #{$order->reference} marked PAID via Stripe Webhook [{$paymentId}].");
                }
            }
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * PhonePe Webhook Listener (POST /webhook/phonepe).
     */
    public function handlePhonePe(Request $request): JsonResponse
    {
        $saltKey = Setting::get('gateway_phonepe_salt_key', '');
        $saltIndex = Setting::get('gateway_phonepe_salt_index', '1');
        $responseBase64 = $request->input('response');

        if (!empty($saltKey) && !empty($responseBase64)) {
            $verifyHeader = $request->header('X-VERIFY');
            if (empty($verifyHeader)) {
                Log::warning('PhonePe webhook X-VERIFY header missing.');
                return response()->json(['error' => 'Verification header missing'], 400);
            }

            $expectedChecksum1 = hash('sha256', $responseBase64 . $saltKey) . '###' . $saltIndex;
            $expectedChecksum2 = hash('sha256', $responseBase64 . '/v3/transaction/callback' . $saltKey) . '###' . $saltIndex;

            if (!hash_equals($expectedChecksum1, $verifyHeader) && !hash_equals($expectedChecksum2, $verifyHeader)) {
                Log::warning('PhonePe webhook checksum verification failed.');
                return response()->json(['error' => 'Checksum verification failed'], 400);
            }
        }

        if (!empty($responseBase64)) {
            $decoded = json_decode(base64_decode($responseBase64), true);
            if (($decoded['code'] ?? '') === 'PAYMENT_SUCCESS') {
                $txnId = $decoded['data']['merchantTransactionId'] ?? null;
                if ($txnId) {
                    $order = Order::where('reference', $txnId)->orWhere('payment_reference', $txnId)->first();
                    if ($order) {
                        $order->update([
                            'payment_status' => Order::PAYMENT_PAID,
                            'status'         => Order::STATUS_CONFIRMED,
                        ]);
                    }
                }
            }
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Storefront Client API: Verify Razorpay Client Signature upon frontend modal success.
     */
    public function verifyRazorpayClientPayment(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order_reference'     => 'required|exists:orders,reference',
            'razorpay_payment_id' => 'required|string',
            'razorpay_order_id'   => 'nullable|string',
            'razorpay_signature'  => 'nullable|string',
        ]);

        $order = Order::where('reference', $validated['order_reference'])->firstOrFail();

        $keySecret = Setting::get('gateway_razorpay_key_secret', '');
        if (!empty($keySecret) && !empty($validated['razorpay_order_id']) && !empty($validated['razorpay_signature'])) {
            $expectedSignature = hash_hmac('sha256', $validated['razorpay_order_id'] . '|' . $validated['razorpay_payment_id'], $keySecret);
            if (!hash_equals($expectedSignature, $validated['razorpay_signature'])) {
                Log::warning('Client Razorpay payment verification signature mismatch.');
                return response()->json(['error' => 'Invalid payment signature'], 400);
            }
        }

        $order->update([
            'payment_status'    => Order::PAYMENT_PAID,
            'payment_reference' => $validated['razorpay_payment_id'],
            'status'            => Order::STATUS_CONFIRMED,
        ]);

        return response()->json([
            'success'   => true,
            'message'   => 'Payment verified successfully',
            'reference' => $order->reference,
        ]);
    }

    /**
     * Storefront API: Get Active Gateway Configuration for Client Checkout.
     */
    public function getActiveGateways(): JsonResponse
    {
        return response()->json([
            'razorpay' => [
                'enabled' => Setting::get('gateway_razorpay_enabled', '0') === '1',
                'key_id'  => Setting::get('gateway_razorpay_key_id', ''),
            ],
            'stripe' => [
                'enabled'         => Setting::get('gateway_stripe_enabled', '0') === '1',
                'publishable_key' => Setting::get('gateway_stripe_publishable_key', ''),
            ],
            'phonepe' => [
                'enabled' => Setting::get('gateway_phonepe_enabled', '0') === '1',
            ],
            'cod' => [
                'enabled' => Setting::get('enable_cod', '1') === '1',
                'charge'  => (int) Setting::get('cod_charge', 40),
            ],
            'upi_qr' => [
                'enabled' => Setting::get('enable_upi_qr', '1') === '1',
                'vpa'     => Setting::get('store_upi_vpa', 'nexonstore@okhdfcbank'),
            ],
        ]);
    }
}
