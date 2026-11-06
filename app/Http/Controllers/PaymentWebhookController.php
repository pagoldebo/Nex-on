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
        if (!empty($webhookSecret) && !empty($signature)) {
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
        $responseBase64 = $request->input('response');
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
