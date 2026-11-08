{{--
    ======================================================================
    TRANSACTIONAL EMAIL: ORDER CONFIRMATION
    ======================================================================
    File: resources/views/emails/order-confirmation.blade.php
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
    <title>Order Confirmation #{{ $order->reference }}</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #fdfbf7; margin: 0; padding: 24px; color: #2d1e2f;">
    <div style="max-w: 600px; margin: 0 auto; background: #ffffff; border-radius: 16px; border: 1px solid #ebd9c8; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.03);">
        
        <!-- Header -->
        <div style="background: #4A0E17; padding: 32px 24px; text-align: center;">
            <h1 style="color: #F8F4EE; font-family: Georgia, serif; font-size: 28px; margin: 0; letter-spacing: 2px;">SAAYA</h1>
            <p style="color: #D4AF37; font-size: 11px; text-transform: uppercase; letter-spacing: 3px; margin: 8px 0 0 0;">Lingerie &amp; Trousseau</p>
        </div>

        <!-- Body -->
        <div style="padding: 32px 24px;">
            <p style="font-size: 16px; color: #2d1e2f; margin: 0 0 16px 0;">Dear <strong>{{ $order->shipping_name }}</strong>,</p>
            <p style="font-size: 14px; line-height: 1.6; color: #665053; margin: 0 0 24px 0;">
                Thank you for your order. We are hand-preparing your intimate pieces with utmost care and discreet packaging.
            </p>

            <!-- Order Reference Box -->
            <div style="background: #faf5ee; border-radius: 12px; border: 1px solid #ebd9c8; padding: 16px; margin-bottom: 24px;">
                <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                    <tr>
                        <td style="color: #8c7373;">Order Reference:</td>
                        <td style="font-weight: bold; text-align: right; color: #4A0E17;">#{{ $order->reference }}</td>
                    </tr>
                    <tr>
                        <td style="color: #8c7373; padding-top: 6px;">Date:</td>
                        <td style="font-weight: bold; text-align: right; padding-top: 6px;">{{ $order->created_at->format('M d, Y h:i A') }}</td>
                    </tr>
                    <tr>
                        <td style="color: #8c7373; padding-top: 6px;">Payment Method:</td>
                        <td style="font-weight: bold; text-align: right; padding-top: 6px; text-transform: uppercase;">{{ $order->payment_method }}</td>
                    </tr>
                </table>
            </div>

            <!-- Items -->
            <h3 style="font-size: 14px; text-transform: uppercase; letter-spacing: 1px; color: #4A0E17; border-bottom: 1px solid #ebd9c8; padding-bottom: 8px; margin: 0 0 16px 0;">Ordered Items</h3>
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 24px; font-size: 13px;">
                @foreach($order->items as $item)
                <tr style="border-bottom: 1px solid #f2e8dc;">
                    <td style="padding: 10px 0;">
                        <strong style="color: #2d1e2f;">{{ $item->product_name }}</strong><br>
                        <span style="color: #8c7373; font-size: 12px;">Color: {{ $item->color_name }} | Size: {{ $item->size }} | Qty: {{ $item->qty }}</span>
                    </td>
                    <td style="text-align: right; font-weight: bold; padding: 10px 0; color: #2d1e2f;">
                        ₹{{ number_format($item->price * $item->qty, 2) }}
                    </td>
                </tr>
                @endforeach
            </table>

            <!-- Financial Summary -->
            <div style="margin-left: auto; width: 240px; font-size: 13px; line-height: 1.8;">
                <div style="display: flex; justify-content: space-between; color: #665053;">
                    <span>Subtotal:</span>
                    <span>₹{{ number_format($order->subtotal, 2) }}</span>
                </div>
                @if($order->discount > 0)
                <div style="display: flex; justify-content: space-between; color: #16a34a;">
                    <span>Discount:</span>
                    <span>-₹{{ number_format($order->discount, 2) }}</span>
                </div>
                @endif
                <div style="display: flex; justify-content: space-between; color: #665053;">
                    <span>Shipping:</span>
                    <span>{{ $order->shipping_fee > 0 ? '₹' . number_format($order->shipping_fee, 2) : 'Free' }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 16px; font-weight: bold; color: #4A0E17; border-top: 1px solid #ebd9c8; padding-top: 6px; margin-top: 6px;">
                    <span>Total:</span>
                    <span>₹{{ number_format($order->total, 2) }}</span>
                </div>
            </div>

            <!-- Shipping Address -->
            <div style="margin-top: 32px; padding: 16px; background: #fdfbf7; border-radius: 12px; border: 1px solid #f2e8dc; font-size: 12px; color: #665053;">
                <strong style="color: #4A0E17; text-transform: uppercase; font-size: 11px;">Shipping Destination:</strong><br>
                {{ $order->shipping_name }}<br>
                {{ $order->shipping_address1 }} {{ $order->shipping_address2 }}<br>
                {{ $order->shipping_city }}, {{ $order->shipping_state }} — {{ $order->shipping_pincode }}<br>
                Phone: {{ $order->shipping_phone }}
            </div>
        </div>

        <!-- Footer -->
        <div style="background: #faf5ee; padding: 20px; text-align: center; border-top: 1px solid #ebd9c8; font-size: 11px; color: #8c7373;">
            <p style="margin: 0;">Discreet Delivery Guaranteed. If you have any inquiries, contact our VIP concierge at <a href="mailto:{{ \App\Models\Setting::get('support_email', 'support@nexon.app') }}" style="color: #4A0E17; text-decoration: none; font-weight: bold;">{{ \App\Models\Setting::get('support_email', 'support@nexon.app') }}</a></p>
        </div>
    </div>
</body>
</html>
