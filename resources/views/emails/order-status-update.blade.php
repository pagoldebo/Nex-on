<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Status Update: #{{ $order->reference }}</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #fdfbf7; margin: 0; padding: 24px; color: #2d1e2f;">
    <div style="max-w: 580px; margin: 0 auto; background: #ffffff; border-radius: 16px; border: 1px solid #ebd9c8; overflow: hidden;">
        
        <div style="background: #4A0E17; padding: 24px; text-align: center;">
            <h2 style="color: #F8F4EE; font-family: Georgia, serif; font-size: 22px; margin: 0;">Order Status Update</h2>
            <p style="color: #D4AF37; font-size: 11px; text-transform: uppercase; letter-spacing: 2px; margin: 4px 0 0 0;">Reference: #{{ $order->reference }}</p>
        </div>

        <div style="padding: 28px 24px;">
            <p style="font-size: 15px; margin: 0 0 16px 0;">Dear <strong>{{ $order->shipping_name }}</strong>,</p>
            <p style="font-size: 14px; line-height: 1.6; color: #665053; margin: 0 0 20px 0;">
                The status of your order <strong>#{{ $order->reference }}</strong> has been updated to:
            </p>

            <div style="background: #faf5ee; border-radius: 12px; border: 1px solid #ebd9c8; padding: 20px; text-align: center; margin-bottom: 24px;">
                <span style="font-size: 18px; font-weight: bold; color: #4A0E17; text-transform: uppercase; letter-spacing: 1px;">
                    {{ ucfirst($order->status) }}
                </span>
            </div>

            @if($order->status === 'shipped' && $order->tracking_number)
            <div style="background: #fdf3f4; border: 1px solid #fecdd3; border-radius: 12px; padding: 16px; margin-bottom: 24px; font-size: 13px;">
                <strong style="color: #9f1239; display: block; margin-bottom: 6px; text-transform: uppercase; font-size: 11px;">Courier Tracking Information</strong>
                <p style="margin: 0 0 4px 0;"><strong>Courier:</strong> {{ $order->courier ?? 'Express Delivery' }}</p>
                <p style="margin: 0;"><strong>Tracking AWB / Number:</strong> <span style="font-family: monospace; font-weight: bold;">{{ $order->tracking_number }}</span></p>
            </div>
            @endif

            <p style="font-size: 13px; line-height: 1.6; color: #8c7373; margin: 0 0 24px 0;">
                You can view complete order history, track status updates, and download your tax invoice anytime by signing into your account.
            </p>

            <div style="text-align: center;">
                <a href="{{ route('account') }}" style="display: inline-block; background: #4A0E17; color: #ffffff; text-decoration: none; padding: 12px 28px; border-radius: 9999px; font-weight: bold; font-size: 13px; letter-spacing: 1px;">
                    View My Account &rarr;
                </a>
            </div>
        </div>

        <div style="background: #faf5ee; padding: 16px; text-align: center; border-top: 1px solid #ebd9c8; font-size: 11px; color: #8c7373;">
            <p style="margin: 0;">Thank you for shopping with us. {{ config('app.name', 'Project Nexon') }}.</p>
        </div>
    </div>
</body>
</html>
