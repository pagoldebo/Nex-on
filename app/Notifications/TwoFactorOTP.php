<?php

namespace App\Notifications;

use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TwoFactorOTP extends Notification
{
    use Queueable;

    public string $otp;
    public string $userName;

    public function __construct(string $otp, string $userName = 'Patron')
    {
        $this->otp = $otp;
        $this->userName = $userName;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $appName = Setting::appName();
        $storeLogo = Setting::get('site_logo');

        return (new MailMessage)
            ->subject($this->otp . ' is your verification code — ' . $appName)
            ->view('emails.two-factor-otp', [
                'otp'             => $this->otp,
                'user'            => (object) ['name' => $this->userName],
                'appName'         => $appName,
                'storeLogo'       => $storeLogo,
                'supportEmail'    => Setting::supportEmail(),
                'supportPhone'    => Setting::supportPhone(),
                'supportWhatsapp' => Setting::get('support_whatsapp', Setting::supportPhone()),
                'storeAddress'    => Setting::storeAddress(),
            ]);
    }
}
