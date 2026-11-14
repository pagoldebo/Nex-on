<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebhookService
{
    /**
     * Send instant order push notification to configured webhook endpoints.
     * Compatible with Discord webhooks, Slack incoming webhooks, Harold App, Zapier, WhatsApp bots.
     */
    public static function sendOrderNotification(Order $order): void
    {
        $webhookUrls = self::getWebhookUrls();

        if (empty($webhookUrls)) {
            Log::info('WebhookService: No order webhook URLs configured.');
            return;
        }

        $order->loadMissing(['user', 'items']);
        $payload = self::buildPayload($order);

        foreach ($webhookUrls as $url) {
            self::sendWebhook($url, $payload);
        }
    }

    /**
     * Retrieve webhook URLs from config, environment, or settings,
     * securely filtering out prohibited internal/SSRF addresses.
     */
    public static function getWebhookUrls(): array
    {
        $urlsString = config('services.webhooks.order_urls', '');
        
        if (empty($urlsString)) {
            $urlsString = Setting::get('order_webhook_urls', '');
        }

        if (empty($urlsString)) {
            return [];
        }

        $urls = array_map('trim', explode(',', $urlsString));
        $validUrls = array_filter($urls, fn ($u) => !empty($u) && filter_var($u, FILTER_VALIDATE_URL));

        // Enforce SSRF protection
        return SsrfProtectionService::filterSafeUrls($validUrls);
    }

    /**
     * Build rich webhook payload with support for standard HTTP & Discord formats.
     */
    private static function buildPayload(Order $order): array
    {
        $customerName = $order->shipping_name ?: ($order->user->name ?? 'Guest Customer');
        $totalFormatted = number_format((float) $order->total, 2);
        $itemsCount = $order->items->count();

        $itemDetails = $order->items->map(function ($item) {
            return ($item->product_name ?? 'Product') . ' [' . ($item->sku ?? 'No SKU') . '] x ' . $item->qty;
        })->implode(', ');

        return [
            // Standard notification format (Herald / Push / Custom webhook)
            'title'          => '🛍️ New Order: #' . $order->reference,
            'body'           => "{$customerName} placed an order for ₹{$totalFormatted} ({$itemsCount} items)",
            'order_id'       => $order->id,
            'reference'      => $order->reference,
            'customer_name'  => $customerName,
            'customer_email' => $order->shipping_email,
            'customer_phone' => $order->shipping_phone,
            'total'          => $totalFormatted,
            'subtotal'       => number_format((float) $order->subtotal, 2),
            'payment_method' => strtoupper($order->payment_method),
            'items_count'    => $itemsCount,
            'items'          => $itemDetails,
            'shipping_city'  => $order->shipping_city,
            'shipping_pincode' => $order->shipping_pincode,

            // Discord webhook embed format compatibility
            'content'        => "🛍️ **New Order Placed: #{$order->reference}**\n**Customer:** {$customerName}\n**Amount:** ₹{$totalFormatted}\n**Payment:** " . strtoupper($order->payment_method) . "\n**Items:** {$itemDetails}",
        ];
    }

    /**
     * Post JSON payload to target webhook URL using SSRF-safe client.
     */
    private static function sendWebhook(string $url, array $payload): void
    {
        // Enforce SSRF protection before dispatch
        if (!SsrfProtectionService::isSafeUrl($url)) {
            Log::warning("WebhookService: Blocked SSRF attempt to dispatch webhook to '{$url}'.");
            return;
        }

        try {
            // Using safe client with redirects disabled to prevent SSRF via 30x redirection
            $response = SsrfProtectionService::createSafeClient(8)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post($url, $payload);

            if ($response->successful()) {
                Log::info("Webhook successfully dispatched to {$url}", [
                    'order'  => $payload['reference'],
                    'status' => $response->status(),
                ]);
            } else {
                Log::warning("Webhook dispatch failed to {$url}", [
                    'order'  => $payload['reference'],
                    'status' => $response->status(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error("Webhook dispatch error for {$url}: " . $e->getMessage());
        }
    }
}
