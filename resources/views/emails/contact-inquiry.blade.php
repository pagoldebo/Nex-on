<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Inquiry: {{ $data['subject'] ?? 'Website Contact' }}</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f8fafc; margin: 0; padding: 20px; color: #0f172a;">
    <div style="max-w: 560px; margin: 0 auto; background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 24px;">
        <h2 style="margin: 0 0 12px 0; color: #4A0E17; font-size: 20px;">📩 New Customer Inquiry</h2>
        
        <div style="background: #f1f5f9; border-radius: 8px; padding: 16px; margin-bottom: 20px; font-size: 13px;">
            <p style="margin: 0 0 6px 0;"><strong>Sender Name:</strong> {{ $data['name'] }}</p>
            <p style="margin: 0 0 6px 0;"><strong>Sender Email:</strong> <a href="mailto:{{ $data['email'] }}">{{ $data['email'] }}</a></p>
            @if(!empty($data['phone']))
            <p style="margin: 0 0 6px 0;"><strong>Phone:</strong> {{ $data['phone'] }}</p>
            @endif
            <p style="margin: 0;"><strong>Subject:</strong> {{ $data['subject'] ?? 'General Inquiry' }}</p>
        </div>

        <h4 style="margin: 0 0 8px 0; font-size: 13px; text-transform: uppercase; color: #64748b;">Message:</h4>
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px; font-size: 14px; line-height: 1.6; color: #334155; white-space: pre-wrap;">{{ $data['message'] }}</div>
    </div>
</body>
</html>
