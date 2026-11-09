{{--
    ======================================================================
    TRANSACTIONAL EMAIL: TWO FACTOR OTP
    ======================================================================
    File: resources/views/emails/two-factor-otp.blade.php
    Mailable / Notification Context: App\Mail
    Responsibility: Formatted HTML transactional email dispatched to customers or staff
    Features:
      - Responsive email layout compatible with major email clients (Gmail, Outlook, Apple Mail)
      - Organization branding, order specifics, and secure direct action links
    Operations & Maintenance:
      - Dispatched asynchronously via Laravel Queues for optimal web throughput
--}}
@php
    $resolvedAppName = $appName ?? \App\Models\Setting::appName();
    $resolvedLogo    = $storeLogo ?? \App\Models\Setting::get('site_logo');
    $resolvedEmail   = $supportEmail ?? \App\Models\Setting::supportEmail();
    $resolvedPhone   = $supportPhone ?? \App\Models\Setting::supportPhone();
    $resolvedAddress = $storeAddress ?? \App\Models\Setting::storeAddress();
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $otp }} is your login verification code — {{ $resolvedAppName }}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            min-width: 100%;
            width: 100% !important;
            height: 100% !important;
            background-color: #F9F8F3;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.5;
            color: #1c1917;
            -webkit-font-smoothing: antialiased;
        }
        table { border-spacing: 0; border-collapse: collapse; }
        td { padding: 0; }
        img { border: 0; outline: none; text-decoration: none; }
        a { text-decoration: none; color: #1c1917; }
        @media screen and (max-width: 600px) {
            .email-container { width: 100% !important; padding: 0 12px !important; }
            .mobile-padding { padding-left: 20px !important; padding-right: 20px !important; }
            .otp-digits { font-size: 32px !important; letter-spacing: 8px !important; }
        }
    </style>
</head>
<body style="background-color: #F9F8F3; margin: 0; padding: 0;">

    <!-- Preheader preview text (visible in inbox summary) -->
    <div style="display: none; max-height: 0px; overflow: hidden; opacity: 0; font-size: 1px; line-height: 1px; color: #F9F8F3;">
        Your login verification code is {{ $otp }}. Valid for 10 minutes.
    </div>

    <center style="width: 100%; table-layout: fixed; background-color: #F9F8F3; padding-top: 36px; padding-bottom: 40px;">
        
        <!-- Header Branding -->
        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 580px;">
            <tr>
                <td align="center" style="padding-bottom: 24px;">
                    <a href="{{ config('app.url') }}" target="_blank">
                        @if(!empty($resolvedLogo))
                            <img src="{{ asset('images/' . ltrim(str_replace('storage/', '', $resolvedLogo), '/')) }}" 
                                 alt="{{ $resolvedAppName }}" 
                                 style="max-height: 44px; max-width: 200px; display: block; margin: 0 auto; object-fit: contain;">
                        @else
                            <h2 style="margin: 0; font-size: 20px; font-weight: 800; letter-spacing: 0.18em; text-transform: uppercase; color: #1c1917; font-family: Georgia, serif;">
                                {{ $resolvedAppName }}
                            </h2>
                        @endif
                    </a>
                    <p style="margin: 6px 0 0 0; font-size: 10px; font-weight: 700; letter-spacing: 0.22em; text-transform: uppercase; color: #a8a29e; font-family: monospace;">
                        MAISON PRIVILÈGE • PATRON ACCESS
                    </p>
                </td>
            </tr>
        </table>

        <!-- Main Card -->
        <table class="email-container" width="580" style="background-color: #ffffff; border-radius: 20px; border: 1px solid #e7e5e4; box-shadow: 0 4px 24px rgba(0,0,0,0.04); overflow: hidden; margin: 0 auto; max-width: 580px;">
            
            <!-- Greeting & Title -->
            <tr>
                <td align="center" class="mobile-padding" style="padding: 38px 40px 24px 40px;">
                    <table border="0" cellpadding="0" cellspacing="0" style="margin: 0 auto 16px auto;">
                        <tr>
                            <td align="center" valign="middle" style="width: 54px; height: 54px; background-color: #000000; border-radius: 16px; text-align: center; vertical-align: middle;">
                                <span style="font-size: 24px; line-height: 54px;">🔒</span>
                            </td>
                        </tr>
                    </table>
                    
                    <h1 style="margin: 0; font-size: 23px; font-weight: 700; color: #1c1917; letter-spacing: -0.4px;">
                        Login Verification
                    </h1>
                    
                    <p style="margin: 10px 0 0 0; font-size: 14px; color: #78716c; line-height: 1.5;">
                        Hello <strong style="color: #1c1917;">{{ $user->name ?? 'Patron' }}</strong>, use the verification code below to securely sign in to your {{ $resolvedAppName }} account.
                    </p>
                </td>
            </tr>

            <!-- Divider -->
            <tr>
                <td style="padding: 0 40px;">
                    <div style="border-top: 1px solid #f5f5f4; width: 100%;"></div>
                </td>
            </tr>

            <!-- Black Rounded OTP Box (Theme Black Card) -->
            <tr>
                <td class="mobile-padding" style="padding: 28px 40px;">
                    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #000000; border-radius: 18px; border: 1px solid #292524; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.25);">
                        <tr>
                            <td align="center" style="padding: 28px 20px;">
                                <div style="display: inline-block; padding: 4px 12px; background-color: #1c1917; border-radius: 20px; border: 1px solid rgba(245, 158, 11, 0.3); margin-bottom: 12px;">
                                    <span style="font-size: 10px; text-transform: uppercase; letter-spacing: 0.22em; color: #f59e0b; font-weight: 700; font-family: monospace;">
                                        ✦ YOUR 4-DIGIT VERIFICATION CODE
                                    </span>
                                </div>
                                <p class="otp-digits" style="margin: 0; font-size: 40px; font-weight: 800; letter-spacing: 14px; color: #ffffff; font-family: 'Courier New', Courier, monospace; text-shadow: 0 2px 4px rgba(0,0,0,0.5);">
                                    {{ $otp }}
                                </p>
                            </td>
                        </tr>
                    </table>

                    <!-- Expiration Notice -->
                    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-top: 16px;">
                        <tr>
                            <td align="center">
                                <p style="margin: 0; font-size: 13px; color: #78716c;">
                                    ⏱ This code will expire in <strong style="color: #1c1917;">10 minutes</strong>.
                                </p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <!-- Security Alert Box -->
            <tr>
                <td class="mobile-padding" style="padding: 0 40px 24px 40px;">
                    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #fefce8; border-left: 3px solid #f59e0b; border-radius: 8px;">
                        <tr>
                            <td style="padding: 14px 18px;">
                                <p style="margin: 0; font-size: 12px; color: #854d0e; line-height: 1.6;">
                                    <strong>⚠️ Security Notice:</strong> If you did not initiate this sign-in request, someone may be attempting to access your account. Please change your password immediately. Never share this code with anyone.
                                </p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <!-- Trust Guarantee Bar -->
            <tr>
                <td style="background-color: #fafaf9; padding: 16px 40px; border-top: 1px solid #f5f5f4;" class="mobile-padding">
                    <table width="100%" border="0" cellpadding="0" cellspacing="0">
                        <tr>
                            <td align="center">
                                <p style="margin: 0; font-size: 11px; color: #78716c; line-height: 1.6;">
                                    🔒 <strong>256-Bit SSL Encrypted Patron Access</strong><br>
                                    Our concierge team will never contact you asking for this code.
                                </p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <!-- Concierge Support -->
            <tr>
                <td style="background-color: #ffffff; padding: 20px 40px 24px 40px; border-top: 1px solid #f5f5f4;" class="mobile-padding">
                    <table width="100%" border="0" cellpadding="0" cellspacing="0">
                        <tr>
                            <td align="center">
                                <p style="margin: 0 0 6px 0; font-size: 11px; text-transform: uppercase; letter-spacing: 0.15em; font-weight: 700; color: #a8a29e;">
                                    Need Assistance?
                                </p>
                                <p style="margin: 0; font-size: 12px; color: #78716c;">
                                    Email: <a href="mailto:{{ $resolvedEmail }}" style="color: #1c1917; font-weight: 600; text-decoration: underline;">{{ $resolvedEmail }}</a>
                                    @if(!empty($resolvedPhone))
                                        &nbsp;•&nbsp; Concierge: <a href="tel:{{ preg_replace('/[^0-9+]/', '', $resolvedPhone) }}" style="color: #1c1917; font-weight: 600;">{{ $resolvedPhone }}</a>
                                    @endif
                                </p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

        </table>

        <!-- Footer -->
        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 580px;">
            <tr>
                <td align="center" style="padding: 26px 20px;">
                    @if(!empty($resolvedAddress))
                        <p style="margin: 0 0 8px 0; font-size: 11px; color: #a8a29e; line-height: 1.5;">
                            {{ $resolvedAddress }}
                        </p>
                    @endif
                    <p style="margin: 0; font-size: 11px; color: #a8a29e;">
                        &copy; {{ date('Y') }} {{ $resolvedAppName }}. All rights reserved.
                    </p>
                </td>
            </tr>
        </table>

    </center>
</body>
</html>
