{{--
    ======================================================================
    TRANSACTIONAL EMAIL: NEW ORDER ADMIN
    ======================================================================
    File: resources/views/emails/new-order-admin.blade.php
    Mailable / Notification Context: App\Mail
    Responsibility: Formatted HTML transactional email dispatched to customers or staff
    Features:
      - Responsive email layout compatible with major email clients (Gmail, Outlook, Apple Mail)
      - Organization branding, order specifics, and secure direct action links
    Operations & Maintenance:
      - Dispatched asynchronously via Laravel Queues for optimal web throughput
--}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>New Order Alert: #{{ $order->reference }}</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f8fafc; margin: 0; padding: 20px; color: #0f172a;">
    <div style="max-w: 560px; margin: 0 auto; background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 24px;">
        <h2 style="margin: 0 0 12px 0; color: #e11d48; font-size: 20px;">🛒 New Order Received!</h2>
        <p style="font-size: 14px; color: #475569; margin: 0 0 20px 0;">
            A new order has been placed on the store. Reference: <strong>#{{ $order->reference }}</strong>
        </p>

        <div style="background: #f1f5f9; border-radius: 8px; padding: 16px; margin-bottom: 20px; font-size: 13px;">
            <p style="margin: 0 0 6px 0;"><strong>Customer:</strong> {{ $order->shipping_name }} ({{ $order->shipping_email }})</p>
            <p style="margin: 0 0 6px 0;"><strong>Phone:</strong> {{ $order->shipping_phone }}</p>
            <p style="margin: 0 0 6px 0;"><strong>Total Value:</strong> ₹{{ number_format($order->total, 2) }}</p>
            <p style="margin: 0 0 6px 0;"><strong>Payment Method:</strong> {{ strtoupper($order->payment_method) }}</p>
            <p style="margin: 0;"><strong>Destination:</strong> {{ $order->shipping_city }}, {{ $order->shipping_state }} ({{ $order->shipping_pincode }})</p>
        </div>

        <h4 style="margin: 0 0 8px 0; font-size: 13px; text-transform: uppercase; color: #64748b;">Items Summary:</h4>
        <ul style="font-size: 13px; color: #334155; padding-left: 20px; margin: 0 0 24px 0;">
            @foreach($order->items as $item)
                <li>{{ $item->product_name }} — {{ $item->color_name }} / {{ $item->size }} &times; {{ $item->qty }} (₹{{ number_format($item->price, 2) }})</li>
            @endforeach
        </ul>

        <a href="{{ route('admin.orders.show', $order) }}" style="display: inline-block; background: #e11d48; color: #ffffff; text-decoration: none; padding: 10px 20px; border-radius: 8px; font-weight: bold; font-size: 13px;">
            Open Order in Admin Panel &rarr;
        </a>
    </div>
</body>
</html>
