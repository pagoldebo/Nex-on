<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $subject }}</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #fdfbf7; margin: 0; padding: 24px; color: #2d1e2f;">
    <div style="max-w: 600px; margin: 0 auto; background: #ffffff; border-radius: 16px; border: 1px solid #ebd9c8; overflow: hidden;">
        <div style="background: #4A0E17; padding: 32px 24px; text-align: center;">
            <h1 style="color: #F8F4EE; font-family: Georgia, serif; font-size: 26px; margin: 0; letter-spacing: 2px;">SAAYA</h1>
            <p style="color: #D4AF37; font-size: 11px; text-transform: uppercase; letter-spacing: 3px; margin: 8px 0 0 0;">Private Member's Edit</p>
        </div>

        <div style="padding: 32px 24px;">
            <h2 style="font-family: Georgia, serif; font-size: 20px; color: #4A0E17; margin: 0 0 16px 0;">{{ $subject }}</h2>
            
            <p style="font-size: 15px; margin: 0 0 16px 0; color: #665053;">Hello {{ $customerName ?? 'Friend' }},</p>
            
            <div style="font-size: 14px; line-height: 1.8; color: #3d2c2f; white-space: pre-wrap; margin-bottom: 32px;">{{ $content }}</div>

            <div style="text-align: center; margin-top: 32px;">
                <a href="{{ route('home') }}" style="display: inline-block; background: #4A0E17; color: #ffffff; text-decoration: none; padding: 14px 32px; border-radius: 9999px; font-weight: bold; font-size: 13px; letter-spacing: 2px; text-transform: uppercase;">
                    Explore The Collection &rarr;
                </a>
            </div>
        </div>

        <div style="background: #faf5ee; padding: 20px; text-align: center; border-top: 1px solid #ebd9c8; font-size: 11px; color: #8c7373;">
            <p style="margin: 0 0 6px 0;">You are receiving this exclusive invitation because you subscribed to {{ config('app.name', 'Nex-on') }} VIP announcements.</p>
            <p style="margin: 0;">Hand-finished bridal &amp; fine lingerie.</p>
        </div>
    </div>
</body>
</html>
